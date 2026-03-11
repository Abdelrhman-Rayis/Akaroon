-- ============================================================
-- Akaroon OCR Migration
-- Adds OCR columns to all 7 category tables in akaroon_akaroondb
-- ============================================================
--
-- HOW TO RUN (from your Mac):
--   mysql -h 34.76.91.107 -u akaroon -p akaroon_akaroondb < tools/ocr_migration.sql
--
-- OR inside Docker (no direct Cloud SQL TCP from Mac):
--   docker exec -i akaroon_php mysql -h 34.76.91.107 -u akaroon -p akaroon_akaroondb < tools/ocr_migration.sql
--
-- SAFE TO RE-RUN? No — ALTER TABLE fails if column already exists.
-- Check state first:
--   SHOW COLUMNS FROM tas LIKE 'ocr_text';
--
-- NEW COLUMNS (same 5 added to all 7 tables):
--   ocr_text           LONGTEXT  — metadata header + full OCR body (what FULLTEXT indexes)
--   ocr_page_count     SMALLINT  — number of pages Mistral parsed
--   ocr_quality_score  FLOAT     — 0.0–1.0: fraction of DB keywords found in OCR output
--                                   < 0.30 = likely poor scan, flag for review
--   ocr_processed_at   DATETIME  — set on success; NULL = not yet processed (pipeline skip flag)
--   embedding          JSON      — Mistral embedding vector (1024 floats) for semantic search
-- ============================================================

USE akaroon_akaroondb;

-- ── tas (التأصيل) ─────────────────────────────────────────

ALTER TABLE tas
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE tas
    ADD FULLTEXT INDEX ft_ocr_tas (ocr_text);

-- ── edu (التعليم) ─────────────────────────────────────────

ALTER TABLE edu
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE edu
    ADD FULLTEXT INDEX ft_ocr_edu (ocr_text);

-- ── philo (الفلسفة) ───────────────────────────────────────

ALTER TABLE philo
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE philo
    ADD FULLTEXT INDEX ft_ocr_philo (ocr_text);

-- ── pol (السياسة) ─────────────────────────────────────────

ALTER TABLE pol
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE pol
    ADD FULLTEXT INDEX ft_ocr_pol (ocr_text);

-- ── soc (المجتمع) ─────────────────────────────────────────

ALTER TABLE soc
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE soc
    ADD FULLTEXT INDEX ft_ocr_soc (ocr_text);

-- ── state (الدولة) ────────────────────────────────────────

ALTER TABLE state
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE state
    ADD FULLTEXT INDEX ft_ocr_state (ocr_text);

-- ── org (المنظمات) ────────────────────────────────────────

ALTER TABLE org
    ADD COLUMN ocr_text          LONGTEXT NULL,
    ADD COLUMN ocr_page_count    SMALLINT NULL,
    ADD COLUMN ocr_quality_score FLOAT    NULL,
    ADD COLUMN ocr_processed_at  DATETIME NULL,
    ADD COLUMN embedding         JSON     NULL;

ALTER TABLE org
    ADD FULLTEXT INDEX ft_ocr_org (ocr_text);

-- ── Verify: should show 5 rows per table (35 total) ──────

SELECT
    TABLE_NAME                   AS `table`,
    COUNT(*)                     AS `ocr_columns_added`
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME   IN ('tas','edu','philo','pol','soc','state','org')
  AND COLUMN_NAME  IN ('ocr_text','ocr_page_count','ocr_quality_score','ocr_processed_at','embedding')
GROUP BY TABLE_NAME
ORDER BY TABLE_NAME;
