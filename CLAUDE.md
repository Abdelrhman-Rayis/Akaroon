# Akaroon — AI Agent Context File

> **For AI agents and future sessions:** This file is the single source of truth for the Akaroon project. Read it completely before making any changes. Update the **Session Log** and **Current State** sections at the end of every work session.

---

## 1. What Is Akaroon?

Akaroon is an online **Sudanese heritage digital library** led by **Ibrahim Ahmed Omer**. It preserves Arabic academic documents — books, papers, and articles — across 7 thematic categories. The library contains ~2,094 records, 2,351 PDFs, and 2,273 cover images (~38 GB total).

- **Live site:** https://www.akaroon.com/ (Softwex hosting — separate system, do not touch)
- **Dev/staging:** https://development.akaroon.com/ (→ 301 → Cloud Run)
- **GitHub:** https://github.com/Abdelrhman-Rayis/Akaroon
- **Cloud Run URL:** https://akaroon-git-844063198632.europe-west1.run.app/

---

## 2. Two Completely Separate Production Systems

**CRITICAL — understand this before touching anything:**

| | Live Production | Development / Staging |
|---|---|---|
| Domain | `www.akaroon.com` | `development.akaroon.com` |
| Hosting | **Softwex** LiteSpeed (91.204.209.26) | **Google Cloud Run** (europe-west1) |
| Deployed by | Manual / FTP | GitHub push → Cloud Build (automatic) |
| Connection | **No connection to Cloud Run** | 301 redirect from Softwex → Cloud Run |
| Database | Separate MySQL on Softwex | Google Cloud SQL (34.76.91.107) |

The GitHub repo and everything in this context file relates **only to the Cloud Run system**. Never attempt to modify the live Softwex site from this repo.

---

## 3. Repository Structure

```
akaroon/
├── CLAUDE.md                          ← this file — update after every session
├── README.md                          ← GitHub README with diagrams and docs
├── Dockerfile                         ← builds the Cloud Run container image
├── docker-compose.yml                 ← local dev (localhost:8082)
├── cloudbuild.yaml                    ← CI/CD: build → push GCR → deploy Cloud Run
├── .dockerignore                      ← excludes 38GB media from Docker image
├── .gitignore                         ← excludes JPGs/PNGs (with !public_html/img/ exception)
│
├── docker/
│   ├── start.sh                       ← container entrypoint: sets $PORT, runs fix-menu.php
│   ├── php.ini                        ← display_errors=Off, log_errors=On
│   ├── fix-menu.php                   ← PHP CLI: fixes WP nav menu URLs in DB at startup
│   ├── wp-config-cloud.php            ← WordPress Blog production config
│   └── wp-config-library-cloud.php   ← WordPress Library production config
│
├── public_html/
│   ├── index.php                      ← main homepage
│   ├── img/                           ← site UI images (professor.jpg, etc.) — tracked in git
│   ├── blog/                          ← WordPress Blog instance
│   │   ├── wp-config.php              ← gitignored (overridden by wp-config-cloud.php in Docker)
│   │   └── wp-content/
│   │       ├── mu-plugins/
│   │       │   └── gcs-media.php      ← CRITICAL: rewrites media URLs to GCS
│   │       ├── plugins/
│   │       │   └── elementor.disabled/ ← Elementor DISABLED (PHP 8 crash — do not rename)
│   │       └── uploads/               ← gitignored + dockerignored (on GCS)
│   ├── library/                       ← WordPress Library instance
│   │   └── wp-content/uploads/        ← gitignored + dockerignored (on GCS)
│   └── files/
│       ├── التأصيل/
│       │   ├── search.php             ← category filter page
│       │   └── fetch_data.php         ← AJAX endpoint — uses MEDIA_BASE_URL for GCS
│       ├── التعليم/fetch_data.php
│       ├── الفلسفة/fetch_data.php
│       ├── السياسة/fetch_data.php
│       ├── المجتمع/fetch_data.php
│       ├── الدولة/fetch_data.php
│       └── المنظمات/fetch_data.php
│
└── docs/
    ├── evolution-diagram.png          ← "The Evolution of Akaroon" story diagram
    ├── architecture-cloud-run.png     ← Cloud Run architecture diagram
    ├── architecture-domains.png       ← domain routing diagram
    ├── architecture-full.png          ← full system architecture diagram
    └── screenshots/                   ← UI screenshots for README
```

---

## 4. Cloud Infrastructure

### Google Cloud Project
- **Project:** `akaroon-project`
- **Region:** `europe-west1` (Belgium) for all services

### Google Cloud Run
- **Service name:** `akaroon-git`
- **URL:** `https://akaroon-git-844063198632.europe-west1.run.app`
- **Container:** PHP 8.2 + Apache
- **Scaling:** 0 → N instances (serverless, scales to zero when idle)
- **Port:** injected via `$PORT` env var (Apache configured at startup)

### Google Cloud SQL
- **Instance:** MySQL 8.0, `db-f1-micro`
- **Public IP:** `34.76.91.107`
- **Region:** `europe-west1`
- **Connection from Cloud Run:** TCP over public IP (no Cloud SQL Auth Proxy)
- **Three databases:**
  - `akaroon_akaroondb` — 7 category tables, ~2,094 Arabic academic records
  - `akaroon_a-wordp-1gu` (exact name may vary) — WordPress Blog database
  - `akaroon_library` — WordPress Library database

### Google Cloud Storage
- **Bucket:** `gs://akaroon-media`
- **Access:** Public (`allUsers:objectViewer`)
- **Region:** `europe-west1`
- **Public URL base:** `https://storage.googleapis.com/akaroon-media`
- **Structure:**
  ```
  gs://akaroon-media/
  ├── files/
  │   ├── التأصيل/
  │   │   ├── files/     ← PDFs
  │   │   └── image/     ← cover JPGs
  │   ├── التعليم/...
  │   ├── الفلسفة/...
  │   ├── السياسة/...
  │   ├── المجتمع/...
  │   ├── الدولة/...
  │   └── المنظمات/...
  └── wp-uploads/        ← WordPress media library (mirrors wp-content/uploads/)
  ```
- **Total size:** ~38 GB (5,039 files)

### Google Container Registry
- **Image:** `gcr.io/akaroon-project/akaroon`

### Google Cloud Build
- **Trigger:** push to `main` branch on GitHub
- **Steps:** docker build → docker push GCR → gcloud run deploy
- **Config file:** `cloudbuild.yaml`

---

## 5. Environment Variables (Cloud Run)

Set these in Cloud Run service configuration. **Never hardcode them.**

| Variable | Value | Purpose |
|---|---|---|
| `WP_DB_HOST` | `34.76.91.107` | WordPress Blog & Library DB host |
| `DB_HOST` | `34.76.91.107` | PHP category filter pages DB host |
| `DB_USER` | `akaroon` | All DB connections |
| `DB_PASSWORD` | *(secret — check Cloud Run console)* | All DB connections |
| `WP_BLOG_DB_NAME` | `akaroon_a-wordp-1gu` | WordPress Blog DB name |
| `WP_URL` | `https://akaroon-git-844063198632.europe-west1.run.app` | Used by fix-menu.php |
| `MEDIA_BASE_URL` | `https://storage.googleapis.com/akaroon-media` | Switches PHP + WP to GCS URLs |
| `PORT` | *(set automatically by Cloud Run)* | Apache listen port |

**Local dev:** `MEDIA_BASE_URL` is NOT set → PHP falls back to local relative paths → `gcs-media.php` is a no-op.

---

## 6. Key Custom Files — What They Do

### `docker/start.sh`
Container entrypoint. Runs before Apache:
1. Reads `$PORT` from Cloud Run environment
2. Patches Apache config to listen on that port
3. Runs `php /docker/fix-menu.php || true` (DB fix, failure is non-fatal)
4. Launches `apache2-foreground`

### `docker/fix-menu.php`
PHP CLI script that runs at every container boot. Connects to Cloud SQL and:
- Logs all current `_menu_item_url` postmeta values to stderr (visible in Cloud Run logs)
- Forces post IDs 15 and 97 (الرئيسية nav items) to `$WP_URL/` (site root)
- Fixes any menu items still pointing to `akaroon.com` or `localhost` (non-blog) → site root
- Fixes any menu items pointing to `akaroon.com/blog` or `localhost/blog` → `$WP_URL/blog/`
- **Why needed:** WordPress stores absolute URLs in the database; the Cloud Run URL changes between service revisions, so menus break after deploy. This script corrects them at boot.

### `docker/wp-config-cloud.php`
WordPress production config (copied into the container, overrides the gitignored `wp-config.php`):
- Reads all DB credentials from environment variables
- Uses `WP_DB_HOST` (public IP `34.76.91.107`) — **not socket format**
- Fixes HTTPS detection: sets `$_SERVER['HTTPS'] = 'on'` when `X-Forwarded-Proto: https` header present (Cloud Run is behind a load balancer)
- Sets `WP_HOME` and `WP_SITEURL` from `WP_URL` env var

### `public_html/blog/wp-content/mu-plugins/gcs-media.php`
WordPress Must-Use Plugin (auto-loaded, cannot be disabled from admin). Only activates when `MEDIA_BASE_URL` is set. Does three things:
1. **`wp_get_attachment_url` filter** — rewrites individual attachment URLs to GCS
2. **`wp_calculate_image_srcset` filter** — rewrites responsive image srcset URLs to GCS
3. **`the_content` filter** — two replacements in post HTML:
   - Replaces `localhost:8082/blog/wp-content/uploads` and Cloud Run URL `/.../uploads` → GCS `wp-uploads/`
   - Strips any absolute hostname from `/files/` links using regex: `https?://[a-z0-9.:-]+(/files/)` → `$1` (makes them relative, works on any host)

### `public_html/files/*/fetch_data.php` (×7)
AJAX endpoint for each category filter page. Reads `MEDIA_BASE_URL`:
```php
$_media_base = rtrim(getenv('MEDIA_BASE_URL') ?: '', '/');
$_category   = basename(__DIR__);
$_img_base   = $_media_base ? "{$_media_base}/files/{$_category}/image" : 'image';
$_pdf_base   = $_media_base ? "{$_media_base}/files/{$_category}/files" : 'files';
```

---

## 7. CI/CD Workflow

```
Developer → git push origin main
         → GitHub webhook → Cloud Build triggered
         → Step 1: docker build -t gcr.io/akaroon-project/akaroon .
           (.dockerignore excludes 38GB media → image ~2.5GB)
         → Step 2: docker push gcr.io/akaroon-project/akaroon
         → Step 3: gcloud run deploy akaroon-git --region europe-west1
         → Cloud Run: new revision created → health check → traffic swap
         → Zero-downtime deploy complete (~8-10 minutes total)
```

**Check build status:**
```bash
gcloud builds list --limit=5 --format="table(id,status,createTime)"
```

**Check Cloud Run logs:**
```bash
gcloud logging read 'resource.type="cloud_run_revision"' --limit=50 --format="table(timestamp,textPayload)"
```

---

## 8. Local Development

### Start local stack
```bash
cd /Users/rayis/Documents/akaroon
docker compose up --build
```
- Site: http://localhost:8082
- Blog: http://localhost:8082/blog/
- phpMyAdmin: http://localhost:8083
- `MEDIA_BASE_URL` not set → media served from local files

### Stop a stuck container
```bash
docker stop akaroon_php   # or whatever name is shown in docker ps
docker compose down
```

### `.claude/launch.json`
```json
{
  "name": "Akaroon (full stack)",
  "runtimeExecutable": "docker",
  "runtimeArgs": ["compose", "up", "--build"],
  "port": 8082,
  "autoPort": false   ← MUST stay false (WP URLs hardcoded to :8082)
}
```

---

## 9. .gitignore Key Rules

- `*.jpg`, `*.png`, `*.pdf` — ignored globally (media files are huge)
- `!public_html/img/` — **exception**: site UI images (professor.jpg etc.) ARE committed
- `!docs/*.png`, `!docs/*.jpg` — **exception**: README/docs images ARE committed
- `!docs/screenshots/*.png` — **exception**: UI screenshots ARE committed
- `public_html/blog/wp-content/uploads/` — ignored (on GCS)
- `public_html/blog/wp-config.php` — ignored (overridden by docker/wp-config-cloud.php)

**To force-add an image that's ignored:**
```bash
git add -f public_html/img/newimage.jpg
```

---

## 10. Known Issues & Permanent Decisions

### Elementor — Permanently Disabled
- Folder: `public_html/blog/wp-content/plugins/elementor.disabled/`
- **Do NOT rename back to `elementor`** — causes PHP 8 fatal crash
- The blog renders fine with Nightingale theme alone
- Only re-enable after verifying PHP 8.2 compatibility

### WordPress URLs in Database
- WordPress stores absolute URLs in the DB (menus, post content, media)
- The DB on Cloud SQL has `http://akaroon.com/...` and old `localhost:8082/...` URLs
- **Menus:** fixed at boot by `fix-menu.php`
- **Post content links to /files/:** fixed at render time by `gcs-media.php` regex
- **Media URLs:** fixed at render time by `gcs-media.php` filters
- **Never run WordPress search-replace on the Cloud SQL DB** — it will corrupt serialized PHP data

### Cloud SQL Connection
- Uses **public IP over TCP** (`34.76.91.107:3306`), not Cloud SQL Auth Proxy
- Direct TCP from local Mac often times out — use PHP CLI via Docker container instead
- Authorized networks: Cloud Run IPs are whitelisted

### Professor Photo
- `public_html/img/professor.jpg` — was previously excluded by `*.jpg` gitignore rule
- Fixed with `!public_html/img/` exception — all 11 images in `/img/` are now tracked

### GCS Upload History
- Uploaded with: `nohup gsutil -m rsync -r public_html/files/ gs://akaroon-media/files/ &`
- WordPress uploads: `nohup gsutil -m rsync -r public_html/blog/wp-content/uploads/ gs://akaroon-media/wp-uploads/ &`
- gsutil rsync is resumable — safe to re-run if interrupted

---

## 11. Application Routes & Pages

| URL | File | Notes |
|---|---|---|
| `/` | `public_html/index.php` | Homepage with search |
| `/blog/` | WordPress | Nightingale theme |
| `/blog/?page_id=103` | WordPress page | التصنيفات — category links (localhost URLs fixed by gcs-media.php) |
| `/blog/?page_id=207` | WordPress page | معرض الصور — photo gallery (WP uploads from GCS) |
| `/blog/ibrahimfinalsearch.php` | Standalone PHP | Global search across all 7 tables |
| `/files/التأصيل/search.php` | PHP | Category filter — الدراسات التأصيلية |
| `/files/التعليم/search.php` | PHP | Category filter — التعليم |
| `/files/الفلسفة/search.php` | PHP | Category filter — الفلسفة |
| `/files/السياسة/search.php` | PHP | Category filter — السياسة |
| `/files/المجتمع/search.php` | PHP | Category filter — المجتمع |
| `/files/الدولة/search.php` | PHP | Category filter — الدولة |
| `/files/المنظمات/search.php` | PHP | Category filter — المنظمات |
| `/img/professor.jpg` | Static | Professor portrait (tracked in git) |

---

## 12. Health Check Commands

Quick verification after any deployment:

```bash
# All key pages return 200
for url in \
  "https://akaroon-git-844063198632.europe-west1.run.app/" \
  "https://akaroon-git-844063198632.europe-west1.run.app/blog/" \
  "https://akaroon-git-844063198632.europe-west1.run.app/img/professor.jpg" \
  "https://akaroon-git-844063198632.europe-west1.run.app/blog/?page_id=207"; do
  echo "$(curl -o /dev/null -s -w '%{http_code}') $url"
done

# Gallery images point to GCS (not localhost)
curl -s "https://akaroon-git-844063198632.europe-west1.run.app/blog/?page_id=207" | \
  grep -o 'src="[^"]*storage\.googleapis\.com[^"]*"' | head -5

# التصنيفات links are relative (not akaroon.com)
curl -s "https://akaroon-git-844063198632.europe-west1.run.app/blog/?page_id=103" | \
  grep -o 'href="[^"]*files[^"]*search\.php"' | head -7

# fix-menu.php ran successfully at last boot
gcloud logging read 'resource.type="cloud_run_revision" AND textPayload:"fix-menu"' \
  --limit=5 --format="table(timestamp,textPayload)"
```

---

## 13. Session Log

> **Instruction for AI agents:** After completing any work session, append a new entry to this log. Keep entries concise — one line per significant change. This log is how future sessions know what was recently done and why.

---

### Session: March 2026 (Context Migration Session)

**Fixes deployed to Cloud Run:**
- `docker/php.ini` — `display_errors=Off` to hide PHP warnings in browser
- `docker/start.sh` — added `fix-menu.php` call at container startup
- `docker/fix-menu.php` — NEW: fixes WordPress nav menu URLs at boot; forces post IDs 15+97 (الرئيسية) to site root `/`
- `docker/wp-config-cloud.php` — committed correct version (uses `WP_DB_HOST` public IP, not broken socket format)
- `Dockerfile` — added `COPY docker/fix-menu.php`

**GCS migration:**
- Created `gs://akaroon-media` bucket (europe-west1, public)
- Uploaded all 5,039 files (~38 GB) using `gsutil -m rsync`
- Updated all 7 `fetch_data.php` files to use `MEDIA_BASE_URL` env var
- Updated `ibrahimfinalsearch.php` for GCS URLs
- Added `MEDIA_BASE_URL` env var to Cloud Run service
- Added `.dockerignore` rules to exclude media from image (saves 38 GB)

**Three Cloud Run UI fixes:**
- `public_html/img/` — force-added 11 images (professor.jpg was excluded by `*.jpg` gitignore rule); added `!public_html/img/` exception to `.gitignore`
- `wp-content/mu-plugins/gcs-media.php` — NEW: WordPress MU plugin rewrites media URLs to GCS and strips localhost/akaroon.com origins from `/files/` links in post content
- `docker/fix-menu.php` — updated to fix الرئيسية (post IDs 15, 97) pointing to `/blog/` instead of `/`

**Docs added to GitHub:**
- `docs/evolution-diagram.png` — 5-chapter evolution story diagram
- `docs/architecture-cloud-run.png` — Cloud Run architecture diagram
- `docs/architecture-domains.png` — domain routing diagram
- `docs/architecture-full.png` — full system architecture diagram
- `README.md` — complete rewrite with Evolution section, System Architecture, CI/CD, Local Dev, components table, env vars table, design decisions

**Current verified state (all passing):**
- `/` → HTTP 200
- `/blog/` → HTTP 200
- `/img/professor.jpg` → HTTP 200
- `/blog/?page_id=207` (gallery) → images from `storage.googleapis.com`
- `/blog/?page_id=103` (التصنيفات) → links are relative `/files/...`
- الرئيسية menu → goes to site root `/`

---

### Session: March 2026 (Semantic Search + Admin Upload + WP Plugin Repo)

**Search UI fixes:**
- `public_html/css/akaroon-theme.css` — Fixed iOS-style semantic toggle switch (3 bugs):
  - `.ak-switch-track` needed `display: inline-block` (span ignores width/height when inline)
  - `.ak-switch-input` replaced `opacity:0; width:0; height:0` with proper visually-hidden pattern (`clip:rect(0,0,0,0); appearance:none; width:1px; height:1px; margin:-1px`)
  - `.ak-search-box-row` changed `flex:1` (collapses to 0px when toggle takes space) → `flex-basis:100%`
- Added PHP `filemtime()` CSS cache-busting to all 9 pages (`index.php`, `ibrahimfinalsearch.php`, 7× `search.php`)

**Qabas integration (CC-BY-ND-4.0, Layer 3 — root siblings):**
- `tools/build_qabas_lookup.php` — NEW: converts Qabas-dataset.csv → `lib/qabas_lookup.php`
  - 58,465 rows → 14,784 roots, 49,004 lemmas
  - Normalises Arabic, removes spaces from roots, indexes multiple spellings
- `Dockerfile` — added Qabas build step (copy CSV + script → run → delete)
- `.gitignore` — added `lib/qabas_lookup.php` (CC-BY-ND: derivative not distributable)

**Lisan Sudanese Corpus integration (CC-BY-4.0, Layer 1 — dialect bridging):**
- `sudanese/` folder — NEW: committed Lisan Sudanese Dialect Corpus CSV (CC-BY-4.0 allows this)
  - `Lisan-Sudanese-dataset.csv` (6.3MB, 52,616 rows)
  - `Lisan-Sudanese RowText_sentences.csv`
  - `ReadMe.pdf`, `license.pdf`, `tagset_translation.xlsx`
- `tools/build_lisan_sudanese_lookup.php` — NEW: converts CSV → `lib/lisan_sudanese_lookup.php`
  - 15,036 dialect→MSA entries + 9,697 English→MSA gloss entries
  - Vote-based deduplication (most frequent MSA lemma wins per token)
  - Skips identity mappings (dialect = MSA)
- `Dockerfile` — added Lisan build step
- `.gitignore` — added `lib/lisan_sudanese_lookup.php`

**search_expand.php rewrite (all 3 layers):**
- `public_html/lib/search_expand.php` — REWRITTEN with 3-layer expansion:
  - Layer 1 (Lisan Sudanese, runs FIRST): dialect→MSA + English gloss→MSA
  - Layer 2 (Arabic Ontology): synonym expansion (cap 10 terms)
  - Layer 3 (Qabas root siblings): root-sibling expansion (cap 16 terms)
  - MSA from Layer 1 is added to `$words[]` so Layers 2+3 expand it further
  - All loaders use `static $data = null` OPcache-friendly lazy pattern

**Admin upload portal:**
- `public_html/admin/upload.php` — COMPLETE REWRITE:
  - WordPress auth: `require_once wp-load.php` + `auth_redirect()` for non-admins
  - Reads DB credentials from env vars (`DB_HOST`, `DB_USER`, `DB_PASSWORD`)
  - Category map with 7 real table names: `['tas'=>'التأصيل', ...]`
  - `nextId()` via `SELECT COALESCE(MAX(id), 0) + 1`
  - GCS upload via Cloud Run metadata server token (no key files)
  - Ghostscript cover auto-generation from PDF page 1
  - Production mode: uploads PDF + JPG to GCS `files/{catFolder}/files/`
  - Local dev mode: saves to `public_html/files/{catFolder}/files/`
  - INSERT uses real column names from DB schema
  - Bootstrap 5 RTL, earthy palette, bilingual Arabic/English UI
  - Live next-ID badge via JS on category change
  - Double-submit prevention

**Ontology fact-check:**
- Arabic Ontology actual size: 637 words, 2,285 synonym pairs (NOT ~50,000 as previously stated)

**WordPress-based open-source version:**
- New repo created: https://github.com/Abdelrhman-Rayis/akaroon-wp
- Scaffolded with: CPT `akaroon_document`, taxonomy `akaroon_category`, ported 3-layer engine,
  admin upload page, GCS adapter, [akaroon_search] + [akaroon_browse] shortcodes, build tools,
  complete CLAUDE.md for new session context

---

## 14. What To Work On Next (Backlog)

- [ ] Replace professor avatar photo — user wants to update `public_html/img/professor.jpg` with a new photo (ask them to save it to a local path first)
- [ ] Verify `development.akaroon.com` → 301 → Cloud Run flow works end-to-end in browser
- [ ] Consider syncing Cloud Run DB with Softwex live DB so content stays in sync
- [ ] The WordPress Library (`/library/`) has never been fully tested on Cloud Run
- [ ] `akaroonproject/.DS_Store` keeps showing as modified — should probably add to `.gitignore`
- [ ] Add Qabas + Lisan attribution text on the Akaroon site (CC-BY license requirement)
- [ ] Commit and deploy everything from this session to Cloud Run (semantic search + upload portal)
- [ ] **akaroon-wp repo**: WP-CLI migration command to import 2,094 records from original 7 MySQL tables
