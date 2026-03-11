#!/usr/bin/env python3
"""
Akaroon OCR Pipeline
====================
Reads every published PDF from the Akaroon MySQL DB → fetches from GCS →
sends to Mistral OCR → injects DB metadata → saves enriched Markdown to
GCS + updates MySQL with searchable text, quality score, and embedding vector.

USAGE
-----
  # Test 3 docs from one category (no DB writes, no API calls):
  python ocr_pipeline.py --category pol --limit 3 --dry-run

  # Process one full category:
  python ocr_pipeline.py --category pol

  # Process everything (all 7 categories, ~2,351 docs):
  python ocr_pipeline.py

  # OCR only — skip embedding step (faster, cheaper; embed later):
  python ocr_pipeline.py --skip-embedding

  # Re-process specific doc IDs (e.g., to fix failures):
  python ocr_pipeline.py --category pol --ids 45,102,307

SETUP
-----
  python3 -m venv .venv && source .venv/bin/activate
  pip install -r tools/requirements-ocr.txt
  cp tools/.env.example tools/.env   # then fill in values

GCS AUTH (choose one):
  gcloud auth application-default login
  OR: export GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json

WHAT GETS STORED
----------------
For each document (id=123, category=السياسة, table=pol):

  GCS:   gs://akaroon-media/ocr/السياسة/123.md
         ┌─ YAML frontmatter: id, title, author, keywords, year, field, pages
         ├─ Arabic metadata header (Arabic labels, human-readable)
         └─ Full OCR body (page-by-page Markdown from Mistral)

  MySQL: pol.ocr_text          = metadata header + "---" + OCR body
         pol.ocr_page_count    = number of pages
         pol.ocr_quality_score = 0.0–1.0 (keyword coverage; <0.30 = suspect)
         pol.ocr_processed_at  = timestamp (NULL = not yet done; pipeline skip flag)
         pol.embedding         = JSON array of 1024 floats (Mistral mistral-embed)

METADATA INJECTION
------------------
The metadata header prepended to every document looks like:

  العنوان: التحول الديمقراطي في السودان
  المؤلف: إبراهيم أحمد عمر
  التصنيف: السياسة
  مجال البحث: الدراسات السياسية
  الكلمات المفتاحية: الديمقراطية، السودان، الحوكمة
  سنة النشر: 2018
  مكان النشر: الخرطوم
  عدد الصفحات: 74

This ensures that FULLTEXT search and embedding models see the curated DB
metadata, not just potentially-imperfect OCR text.

SAFETY
------
  - Idempotent: skips rows where ocr_processed_at IS NOT NULL
  - Failures are logged but never crash the whole run
  - --dry-run shows exactly what would happen without any API calls
  - ocr_pipeline.log is written alongside any run
"""

import argparse
import json
import logging
import os
import sys
import time
from datetime import datetime, timezone
from pathlib import Path
from typing import Optional

import pymysql
import pymysql.cursors
from dotenv import load_dotenv
from google.cloud import storage as gcs
from mistralai import Mistral

# ─────────────────────────────────────────────────────────────
# Config
# ─────────────────────────────────────────────────────────────

load_dotenv(Path(__file__).parent / '.env')

# Table name → GCS/filesystem category folder name (Arabic)
CATEGORIES: dict[str, str] = {
    'tas':   'التأصيل',
    'edu':   'التعليم',
    'philo': 'الفلسفة',
    'pol':   'السياسة',
    'soc':   'المجتمع',
    'state': 'الدولة',
    'org':   'منظمات',
}

GCS_BUCKET      = 'akaroon-media'
GCS_BASE_URL    = 'https://storage.googleapis.com/akaroon-media'
OCR_MODEL       = 'mistral-ocr-latest'
EMBED_MODEL     = 'mistral-embed'

# Seconds to sleep between Mistral API calls (respect rate limits)
RATE_LIMIT_S    = 1.0

# OCR quality warning threshold (fraction of DB keywords found in OCR body)
QUALITY_WARN    = 0.30

DB_CONFIG: dict = {
    'host':        os.getenv('DB_HOST',     '127.0.0.1'),
    'port':        int(os.getenv('DB_PORT', '3306')),
    'user':        os.getenv('DB_USER'),
    'password':    os.getenv('DB_PASSWORD'),
    'database':    os.getenv('DB_NAME',     'akaroon_akaroondb'),
    'charset':     'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor,
    'connect_timeout': 10,
}

# ─────────────────────────────────────────────────────────────
# Logging — stdout + ocr_pipeline.log
# ─────────────────────────────────────────────────────────────

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s  %(levelname)-8s  %(message)s',
    datefmt='%H:%M:%S',
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler('ocr_pipeline.log', encoding='utf-8'),
    ],
)
log = logging.getLogger(__name__)

# ─────────────────────────────────────────────────────────────
# Text builders
# ─────────────────────────────────────────────────────────────

def _s(row: dict, key: str) -> str:
    """Safe string from DB row — strips whitespace, never None."""
    return str(row.get(key) or '').strip()


def build_metadata_header(row: dict, page_count: int) -> str:
    """
    Arabic-label metadata block injected at the top of every OCR document.
    Becomes part of the FULLTEXT-indexed ocr_text and embedding input.
    """
    return (
        f"العنوان: {_s(row, 'The_Title_of_Paper_Book')}\n"
        f"المؤلف: {_s(row, 'The_number_of_the_Author')}\n"
        f"التصنيف: {_s(row, 'Category')}\n"
        f"مجال البحث: {_s(row, 'Field_of_research')}\n"
        f"الكلمات المفتاحية: {_s(row, 'Key_words')}\n"
        f"سنة النشر: {_s(row, 'Year_of_issue')}\n"
        f"مكان النشر: {_s(row, 'Place_of_issue')}\n"
        f"عدد الصفحات: {page_count}"
    )


def build_yaml_frontmatter(row: dict, page_count: int) -> str:
    """
    YAML frontmatter for the GCS .md file.
    Machine-readable; used by future scripts without DB lookups.
    """
    def esc(v: str) -> str:
        return v.replace('\\', '\\\\').replace('"', '\\"')

    now = datetime.now(timezone.utc).isoformat()
    return (
        "---\n"
        f'id: {row["id"]}\n'
        f'category: "{esc(_s(row, "Category"))}"\n'
        f'title: "{esc(_s(row, "The_Title_of_Paper_Book"))}"\n'
        f'author: "{esc(_s(row, "The_number_of_the_Author"))}"\n'
        f'keywords: "{esc(_s(row, "Key_words"))}"\n'
        f'field: "{esc(_s(row, "Field_of_research"))}"\n'
        f'year: "{esc(_s(row, "Year_of_issue"))}"\n'
        f'place: "{esc(_s(row, "Place_of_issue"))}"\n'
        f'pages: {page_count}\n'
        f'ocr_processed: "{now}"\n'
        "---\n"
    )


def build_document_card(row: dict, raw_body: str) -> str:
    """
    Compact metadata-rich text sent to the embedding model.
    Title + keywords dominate the vector; first 800 chars of OCR
    provide topical grounding.

    Why not embed the full body?
      - Embedding models compress everything into one fixed-size vector.
      - 200 pages averaged into one vector loses specificity.
      - A doc card (metadata + opening) captures the document's identity
        far better for similarity search.
      - Full body is kept in GCS for chunk-level RAG retrieval later.
    """
    header = build_metadata_header(row, page_count=0)   # pages not critical here
    return f"{header}\n\n{raw_body[:800]}"


def compute_quality_score(row: dict, ocr_body: str) -> float:
    """
    Cross-checks OCR output against the curated DB keywords.
    Returns 0.0–1.0: fraction of keywords found in the OCR body.

    < 0.30 → likely scanned image PDF, garbled Arabic, or very poor scan.
    These are flagged in logs for manual review.
    """
    raw_kw = _s(row, 'Key_words')
    if not raw_kw:
        return 1.0  # no keywords → assume fine

    # Split on Arabic comma, Latin comma, or semicolon
    keywords = [
        kw.strip()
        for kw in raw_kw.replace('؛', '،').replace(';', '،').replace(',', '،').split('،')
        if kw.strip()
    ]
    if not keywords:
        return 1.0

    found = sum(1 for kw in keywords if kw in ocr_body)
    return round(found / len(keywords), 4)

# ─────────────────────────────────────────────────────────────
# GCS helpers
# ─────────────────────────────────────────────────────────────

def pdf_public_url(category_folder: str, doc_id: int) -> str:
    """Public GCS URL for the source PDF — Mistral fetches this directly."""
    return f"{GCS_BASE_URL}/files/{category_folder}/files/{doc_id}.pdf"


def md_gcs_path(category_folder: str, doc_id: int) -> str:
    """GCS object path for the enriched OCR Markdown file."""
    return f"ocr/{category_folder}/{doc_id}.md"


def upload_md_to_gcs(bucket: gcs.Bucket, gcs_path: str, content: str) -> None:
    blob = bucket.blob(gcs_path)
    blob.upload_from_string(
        content.encode('utf-8'),
        content_type='text/markdown; charset=utf-8',
    )

# ─────────────────────────────────────────────────────────────
# Core: process one document
# ─────────────────────────────────────────────────────────────

def process_document(
    row: dict,
    table: str,
    category_folder: str,
    db,
    mistral: Mistral,
    bucket: gcs.Bucket,
    skip_embedding: bool,
    dry_run: bool,
) -> bool:
    """
    Full pipeline for one document.
    Returns True on success, False on any unrecoverable error.
    """
    doc_id  = row['id']
    title   = _s(row, 'The_Title_of_Paper_Book')
    pdf_url = pdf_public_url(category_folder, doc_id)

    log.info(f"  [{table}#{doc_id}] {title[:65]}")

    if dry_run:
        log.info(f"    DRY RUN → would OCR: {pdf_url}")
        return True

    # ── Step 1: Mistral OCR ───────────────────────────────────
    try:
        ocr_resp = mistral.ocr.process(
            model=OCR_MODEL,
            document={"type": "document_url", "document_url": pdf_url},
            include_image_base64=False,   # we don't need image re-extraction
        )
    except Exception as exc:
        log.error(f"    OCR FAILED: {exc}")
        return False

    page_count = len(ocr_resp.pages)
    raw_body   = "\n\n".join(p.markdown for p in ocr_resp.pages)
    log.info(f"    OCR OK — {page_count} pages, {len(raw_body):,} chars")

    # ── Step 2: Inject metadata ───────────────────────────────
    metadata_header = build_metadata_header(row, page_count)

    # enriched_text = what goes into MySQL ocr_text column:
    #   metadata header → separator → full OCR body
    enriched_text = f"{metadata_header}\n\n---\n\n{raw_body}"

    score = compute_quality_score(row, raw_body)
    if score < QUALITY_WARN:
        log.warning(f"    LOW OCR QUALITY score={score:.2f} — check scan quality")

    # ── Step 3: Build and upload enriched .md to GCS ─────────
    yaml_front  = build_yaml_frontmatter(row, page_count)
    # GCS .md file = YAML frontmatter + enriched text (metadata header + body)
    md_content  = f"{yaml_front}\n{enriched_text}"
    gcs_obj     = md_gcs_path(category_folder, doc_id)

    try:
        upload_md_to_gcs(bucket, gcs_obj, md_content)
        log.info(f"    GCS OK — gs://akaroon-media/{gcs_obj}")
    except Exception as exc:
        log.error(f"    GCS UPLOAD FAILED: {exc}")
        return False

    # ── Step 4: Mistral Embedding (document card) ─────────────
    embedding_json: Optional[str] = None
    if not skip_embedding:
        try:
            doc_card = build_document_card(row, raw_body)
            emb_resp = mistral.embeddings.create(
                model=EMBED_MODEL,
                inputs=[doc_card],
            )
            vec             = emb_resp.data[0].embedding
            embedding_json  = json.dumps(vec)
            log.info(f"    EMBED OK — {len(vec)} dims")
        except Exception as exc:
            # Non-fatal: OCR text is still saved. Embeddings can be backfilled.
            log.warning(f"    EMBED FAILED (non-fatal, will backfill): {exc}")

    # ── Step 5: Update MySQL ──────────────────────────────────
    try:
        with db.cursor() as cur:
            cur.execute(
                f"""
                UPDATE `{table}`
                SET  ocr_text          = %s,
                     ocr_page_count    = %s,
                     ocr_quality_score = %s,
                     ocr_processed_at  = NOW(),
                     embedding         = %s
                WHERE id = %s
                """,
                (enriched_text, page_count, score, embedding_json, doc_id),
            )
        db.commit()
        log.info(f"    DB OK — ocr_text {len(enriched_text):,} chars, quality={score:.2f}")
    except Exception as exc:
        log.error(f"    DB UPDATE FAILED: {exc}")
        db.rollback()
        return False

    return True

# ─────────────────────────────────────────────────────────────
# Process one category table
# ─────────────────────────────────────────────────────────────

def process_category(
    table: str,
    category_folder: str,
    db,
    mistral: Mistral,
    bucket: gcs.Bucket,
    limit: Optional[int],
    ids: Optional[list[int]],
    skip_embedding: bool,
    dry_run: bool,
) -> tuple[int, int]:
    """
    Fetches unprocessed rows from one category table and processes them.
    Returns (ok_count, fail_count).
    """
    with db.cursor() as cur:
        if ids:
            # Re-process specific IDs (ignore ocr_processed_at)
            placeholders = ','.join(['%s'] * len(ids))
            cur.execute(
                f"""
                SELECT id, Category,
                       The_Title_of_Paper_Book, The_number_of_the_Author,
                       Field_of_research, Key_words, Year_of_issue, Place_of_issue
                FROM `{table}`
                WHERE id IN ({placeholders}) AND status = 0
                ORDER BY id
                """,
                ids,
            )
        else:
            # Normal run: only rows not yet processed
            sql = f"""
                SELECT id, Category,
                       The_Title_of_Paper_Book, The_number_of_the_Author,
                       Field_of_research, Key_words, Year_of_issue, Place_of_issue
                FROM `{table}`
                WHERE ocr_processed_at IS NULL AND status = 0
                ORDER BY id
            """
            if limit:
                sql += f" LIMIT {int(limit)}"
            cur.execute(sql)

        rows = cur.fetchall()

    total = len(rows)
    log.info(f"\n{'─' * 62}")
    log.info(f"  {category_folder}  ({table})  —  {total} document(s) to process")
    log.info(f"{'─' * 62}")

    if total == 0:
        return 0, 0

    ok_count   = 0
    fail_count = 0
    failed_ids = []

    for i, row in enumerate(rows, start=1):
        log.info(f"  [{i}/{total}]")
        ok = process_document(
            row, table, category_folder,
            db, mistral, bucket,
            skip_embedding, dry_run,
        )
        if ok:
            ok_count += 1
        else:
            fail_count += 1
            failed_ids.append(row['id'])

        if i < total and not dry_run:
            time.sleep(RATE_LIMIT_S)

    if failed_ids:
        log.warning(f"  FAILED IDs in {table}: {failed_ids}")
        log.warning(f"  Re-run with: --category {table_to_arg(table)} --ids {','.join(map(str, failed_ids))}")

    return ok_count, fail_count


def table_to_arg(table: str) -> str:
    """Return the --category argument name for a table name."""
    return table   # they're the same for Akaroon

# ─────────────────────────────────────────────────────────────
# Progress report
# ─────────────────────────────────────────────────────────────

def print_progress_report(db) -> None:
    """Show how many docs are processed vs total in each table."""
    log.info("\n" + "═" * 62)
    log.info("  CURRENT PROGRESS")
    log.info("═" * 62)
    total_done  = 0
    total_all   = 0
    with db.cursor() as cur:
        for table in CATEGORIES:
            cur.execute(f"""
                SELECT
                    COUNT(*)                                        AS total,
                    SUM(ocr_processed_at IS NOT NULL)               AS done,
                    ROUND(AVG(ocr_quality_score), 2)                AS avg_quality,
                    SUM(ocr_quality_score IS NOT NULL
                        AND ocr_quality_score < 0.30)               AS low_quality
                FROM `{table}`
                WHERE status = 0
            """)
            r = cur.fetchone()
            done  = int(r['done']  or 0)
            total = int(r['total'] or 0)
            pct   = f"{done/total*100:.0f}%" if total else "—"
            aq    = r['avg_quality'] or '—'
            lq    = int(r['low_quality'] or 0)
            log.info(
                f"  {table:<7} {CATEGORIES[table]:<10}  "
                f"{done:>4}/{total:<4} ({pct:>4})  "
                f"avg_quality={aq}  low_quality_flags={lq}"
            )
            total_done += done
            total_all  += total
    log.info("─" * 62)
    log.info(f"  TOTAL  {total_done}/{total_all}  ({total_done/total_all*100:.0f}% complete)" if total_all else "  No documents found.")
    log.info("═" * 62)

# ─────────────────────────────────────────────────────────────
# Entry point
# ─────────────────────────────────────────────────────────────

def main() -> None:
    parser = argparse.ArgumentParser(
        description='Akaroon OCR Pipeline — PDF → Mistral OCR → MySQL + GCS',
        formatter_class=argparse.RawDescriptionHelpFormatter,
    )
    parser.add_argument(
        '--category', choices=list(CATEGORIES.keys()),
        help='Process one table only (default: all 7)',
    )
    parser.add_argument(
        '--limit', type=int,
        help='Max documents per category — use for testing (e.g., --limit 5)',
    )
    parser.add_argument(
        '--ids', type=str,
        help='Comma-separated doc IDs to (re-)process, e.g. --ids 45,102,307',
    )
    parser.add_argument(
        '--dry-run', action='store_true',
        help='Show what would run — no Mistral API calls, no DB writes, no GCS uploads',
    )
    parser.add_argument(
        '--skip-embedding', action='store_true',
        help='Do OCR only; skip Mistral embedding step (faster, cheaper for first pass)',
    )
    parser.add_argument(
        '--progress', action='store_true',
        help='Show progress report for all tables then exit',
    )
    args = parser.parse_args()

    # ── Validate required env vars ────────────────────────────
    required_env = ['DB_USER', 'DB_PASSWORD', 'MISTRAL_API_KEY']
    missing = [k for k in required_env if not os.getenv(k)]
    if missing:
        log.error(f"Missing required environment variables: {', '.join(missing)}")
        log.error("Copy tools/.env.example → tools/.env and fill in the values.")
        sys.exit(1)

    # ── Connect ───────────────────────────────────────────────
    try:
        db = pymysql.connect(**DB_CONFIG)
    except Exception as exc:
        log.error(f"MySQL connection failed: {exc}")
        log.error("Tip: direct TCP from Mac to Cloud SQL often times out. "
                  "Run inside Docker: docker exec -it akaroon_php python3 /var/www/html/tools/ocr_pipeline.py")
        sys.exit(1)

    if args.progress:
        print_progress_report(db)
        db.close()
        return

    mistral    = Mistral(api_key=os.getenv('MISTRAL_API_KEY'))
    gcs_client = gcs.Client()
    bucket     = gcs_client.bucket(GCS_BUCKET)

    # Parse --ids
    target_ids: Optional[list[int]] = None
    if args.ids:
        try:
            target_ids = [int(x.strip()) for x in args.ids.split(',')]
        except ValueError:
            log.error("--ids must be comma-separated integers, e.g. --ids 45,102,307")
            sys.exit(1)

    # Which categories to run
    categories = (
        {args.category: CATEGORIES[args.category]}
        if args.category
        else CATEGORIES
    )

    log.info("=" * 62)
    log.info("Akaroon OCR Pipeline")
    log.info(f"  categories     : {list(categories.keys())}")
    log.info(f"  limit          : {args.limit or 'none (all)'}")
    log.info(f"  ids            : {target_ids or 'all unprocessed'}")
    log.info(f"  dry_run        : {args.dry_run}")
    log.info(f"  skip_embedding : {args.skip_embedding}")
    log.info(f"  OCR model      : {OCR_MODEL}")
    log.info(f"  embed model    : {EMBED_MODEL if not args.skip_embedding else 'SKIPPED'}")
    log.info("=" * 62)

    total_ok   = 0
    total_fail = 0
    started_at = time.monotonic()

    try:
        for table, folder in categories.items():
            ok, fail = process_category(
                table, folder, db, mistral, bucket,
                args.limit, target_ids,
                args.skip_embedding, args.dry_run,
            )
            total_ok   += ok
            total_fail += fail
    finally:
        db.close()

    elapsed = time.monotonic() - started_at
    mins, secs = divmod(int(elapsed), 60)

    log.info("\n" + "═" * 62)
    log.info(f"  DONE — {total_ok} succeeded  |  {total_fail} failed  |  {mins}m {secs}s")
    if total_fail:
        log.info("  Check ocr_pipeline.log for failed IDs and re-run with --ids")
    log.info("═" * 62)

    if total_fail:
        sys.exit(1)


if __name__ == '__main__':
    main()
