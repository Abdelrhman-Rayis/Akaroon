# What is Akaroon?
Akaroon is an online Sudanese heritage library primarily contributed to by volunteers and led by Prof. Ibrahim A. Omar. Its mission is to preserve Sudanese heritage knowledge for the next generation of Sudanese leaders. Since its launch in 2020, the website has undergone many changes to improve its services.
This repository is created to better manage the future development of the services. If you have any inquiries, you can contact me on my GitHub account or through the Akaroon website's contact form.

# Live Website
https://www.akaroon.com/

# Development Version
https://development.akaroon.com/

# Library Resources
The Akaroon Library contains over 2,000 documents and books organized into seven main themes: education, philosophy, politics, society, the state, organizations, and foundational studies.

## How to Access
Dear visitor, you can explore all the books and documents using the search feature on the website or by visiting the categories page. We welcome any comments or suggestions. You can also reach out to us through the "Contact Us" page or check out our blog, which features many articles and content related to the site that you can comment on or share on social media platforms.

# System Architecture

## Overview

Akaroon runs on **Google Cloud Run** (serverless, auto-scaling) backed by **Google Cloud SQL** for databases and **Google Cloud Storage** for all media. A GitHub push to `main` automatically triggers **Cloud Build**, which builds the Docker image and deploys it — zero manual steps required.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                            USERS (Browser)                                  │
└───────────────────────────────────┬─────────────────────────────────────────┘
                                    │ HTTPS
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                    Google Cloud Run  (europe-west1)                         │
│              akaroon-git-844063198632.europe-west1.run.app                  │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │                   Docker Container (PHP 8.2 + Apache)                │   │
│  │                                                                      │   │
│  │  /                   → public_html/index.php  (homepage)             │   │
│  │  /blog/              → WordPress Blog         (Nightingale theme)    │   │
│  │  /library/           → WordPress Library      (catalog front-end)    │   │
│  │  /blog/ibrahimfinalsearch.php → Global Search (UNION REGEXP + arquery) │ │
│  │  /files/{category}/  → Category Filter Pages  (×7, AJAX card grids) │   │
│  │  /img/               → Site UI images         (static assets)        │   │
│  │                                                                      │   │
│  │  Startup: docker/start.sh                                            │   │
│  │    ├── Configures Apache to listen on $PORT                          │   │
│  │    └── Runs fix-menu.php  (fixes WordPress nav menu URLs in DB)      │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
└────────────┬──────────────────────────────────┬────────────────────────────┘
             │ PDO / mysqli (TCP)                │ HTTP (media requests)
             ▼                                  ▼
┌────────────────────────┐        ┌─────────────────────────────────────────┐
│   Google Cloud SQL     │        │      Google Cloud Storage               │
│   MySQL 8.0            │        │      gs://akaroon-media (public)        │
│   europe-west1         │        │      europe-west1                       │
│                        │        │                                         │
│  ┌──────────────────┐  │        │  /files/{category}/files/  → PDFs       │
│  │ akaroon_akaroondb│  │        │  /files/{category}/image/  → Covers     │
│  │  7 category tables│  │        │  /wp-uploads/              → WP media   │
│  │  ~2,094 records  │  │        │                                         │
│  └──────────────────┘  │        │  Served via:                            │
│  ┌──────────────────┐  │        │  storage.googleapis.com/akaroon-media/  │
│  │ akaroon_a-wordp  │  │        │  Controlled by: MEDIA_BASE_URL env var  │
│  │ (WordPress Blog  │  │        └─────────────────────────────────────────┘
│  │  DB)             │  │
│  └──────────────────┘  │
│  ┌──────────────────┐  │
│  │ akaroon_library  │  │
│  │ (WordPress Lib.  │  │
│  │  DB)             │  │
│  └──────────────────┘  │
└────────────────────────┘
```

---

## CI/CD Pipeline

```
Developer
    │
    │  git push origin main
    ▼
┌──────────────┐     webhook      ┌──────────────────────────────┐
│   GitHub     │ ───────────────► │   Google Cloud Build         │
│   (main)     │                  │                              │
└──────────────┘                  │  1. docker build -t gcr.io/  │
                                  │     akaroon-project/akaroon  │
                                  │  2. docker push → GCR        │
                                  │  3. gcloud run deploy        │
                                  │     akaroon-git              │
                                  └──────────────┬───────────────┘
                                                 │
                                                 ▼
                                  ┌──────────────────────────────┐
                                  │   Cloud Run (new revision)   │
                                  │   Zero-downtime deploy       │
                                  └──────────────────────────────┘
```

---

## Local Development

```
Developer Machine
  │
  │  docker compose up --build
  ▼
┌─────────────────────────────────────────────┐
│  Docker Compose  (localhost:8082)           │
│                                             │
│  ┌─────────────────┐  ┌─────────────────┐  │
│  │  php container  │  │  mysql container│  │
│  │  PHP 8.2+Apache │  │  MySQL 8.0      │  │
│  │  port 8082      │◄─┤  port 3306      │  │
│  └─────────────────┘  └─────────────────┘  │
│                                             │
│  ┌─────────────────┐                        │
│  │  phpmyadmin     │                        │
│  │  port 8083      │                        │
│  └─────────────────┘                        │
│                                             │
│  Media: served locally from                 │
│  public_html/files/*/  and                  │
│  public_html/blog/wp-content/uploads/       │
│  (MEDIA_BASE_URL not set → local paths)     │
└─────────────────────────────────────────────┘
```

---

## Application Components

| Component | Path | Technology | Description |
|---|---|---|---|
| **Homepage** | `/` | PHP + HTML/CSS/JS | Main landing page with search entry point |
| **WordPress Blog** | `/blog/` | WordPress 6.x + Nightingale theme | Editorial hub, articles, photo gallery, contact |
| **WordPress Library** | `/library/` | WordPress 6.x | Catalog front-end (second WP instance) |
| **Global Search** | `/blog/ibrahimfinalsearch.php` | Standalone PHP + PDO | Full-text UNION REGEXP across all 7 category tables with Arabic normalization (`arquery()`) |
| **Category Filters** | `/files/{category}/search.php` ×7 | PHP + PDO + AJAX | Per-category search: التأصيل, التعليم, الفلسفة, السياسة, المجتمع, الدولة, المنظمات |
| **Content DB** | `akaroon_akaroondb` | MySQL 8.0 | ~2,094 Arabic academic records, 7 tables |
| **Blog DB** | `akaroon_a-wordp-*` | MySQL 8.0 | WordPress posts, menus, media metadata |
| **Library DB** | `akaroon_library` | MySQL 8.0 | WordPress Library instance database |
| **GCS Media** | `gs://akaroon-media` | Google Cloud Storage | 2,351 PDFs + 2,273 cover images + WordPress uploads (~38 GB total) |

---

## WordPress Customisations

| File | Purpose |
|---|---|
| `docker/wp-config-cloud.php` | Production WP config — Cloud SQL IP, HTTPS proxy fix, GCS env vars |
| `docker/fix-menu.php` | Startup script — rewrites nav menu URLs in DB to match the Cloud Run hostname |
| `wp-content/mu-plugins/gcs-media.php` | Must-Use plugin — rewrites all WordPress media URLs from localhost to GCS at render time |

---

## Environment Variables (Cloud Run)

| Variable | Value | Used By |
|---|---|---|
| `WP_DB_HOST` | `34.76.91.107` | WordPress Blog & Library DB connection |
| `DB_HOST` | `34.76.91.107` | PHP category filter pages |
| `DB_USER` | `akaroon` | All DB connections |
| `DB_PASSWORD` | _(secret)_ | All DB connections |
| `WP_URL` | `https://akaroon-git-844063198632.europe-west1.run.app` | fix-menu.php nav URL rewrite |
| `MEDIA_BASE_URL` | `https://storage.googleapis.com/akaroon-media` | GCS media routing for PDFs, images, WP uploads |
| `PORT` | set by Cloud Run | Apache listen port |

---

## Known Issues & Notes

### Elementor Plugin — Disabled (March 2026)

**Status:** Elementor is **permanently disabled** (`wp-content/plugins/elementor.disabled/`).

**Reason:** Updating Elementor caused a PHP 8 fatal crash — it passed `null` to a method now enforcing a strict `string` type. The blog renders correctly without it using the Nightingale theme alone.

**To re-enable:** Verify PHP 8.2 compatibility → rename `elementor.disabled` → `elementor` → test locally at `localhost:8082/blog/` before deploying.
