# Akaroon Website — Full Test Report

**Environment:** `http://localhost:8082` · Docker (PHP 8.2 + MySQL 8) · March 2026

---

## 1. HTTP Status — All Pages

| Page | Status | Notes |
|---|---|---|
| `/` | ✅ 200 | Serves WordPress blog homepage |
| `/blog/` | ✅ 200 | WordPress blog |
| `/library/` | ⚠️ 200* | Loads but renders 404 inside WP |
| `/blog/ibrahimfinalsearch.php` | ✅ 200 | Main search |
| `/filter/search.php` | ✅ 200 | الدولة category filter (empty filters) |
| `/files/التعليم/search.php` | ✅ 200 | |
| `/files/التأصيل/search.php` | ✅ 200 | |
| `/files/المجتمع/search.php` | ✅ 200 | |
| `/files/السياسة/search.php` | ✅ 200 | |
| `/files/الدولة/search.php` | ✅ 200 | |
| `/files/الفلسفة/search.php` | ✅ 200 | |
| `/files/منظمات/search.php` | ✅ 200 | |
| `/Live-Search/ibrahimfinalsearch.php` | ✅ 200 | Form works, no results (empty DB) |
| `/search.php` | ❌ 200 | Fatal PHP error shown |
| `/search5.php` | ⚠️ 200 | Stub form, no backend |
| `/Categories.php` | ⚠️ 200 | Dead anchor links |
| `/FC.php` | ❌ 200 | Fatal PHP error shown |
| `localhost:8081` (phpMyAdmin) | ✅ 200 | phpMyAdmin 5.2.3 |

---

## 2. Main Search — `ibrahimfinalsearch.php` ✅ WORKING

Located at `/blog/ibrahimfinalsearch.php`. Searches across all 7 tables simultaneously using Arabic regex normalization (`arquery()` — normalizes alef variants, ta marbuta, etc.).

| Query | Results |
|---|---|
| تعليم (education) | **454 rows** |
| مجتمع (society) | **239 rows** |
| سياسة (politics) | **200 rows** |
| فلسفة (philosophy) | **186 rows** |

Cover images (scanned document thumbnails) display correctly. Title and author links are generated. No `akaroon.com` redirects detected.

---

## 3. Category Filter Pages — `files/*/search.php` ✅ WORKING

All 7 category pages load with live data from `akaroon_akaroondb`. Author and Field of Research checkboxes are populated from the database. Book cards with cover images and PDF links are rendered via AJAX (`fetch_data.php`).

| Category | Records |
|---|---|
| التعليم (Education) | **409** |
| منظمات (Organizations) | **511** |
| الدولة (State) | **346** |
| التأصيل (Foundations) | **308** |
| السياسة (Politics) | **181** |
| الفلسفة (Philosophy) | **173** |
| المجتمع (Society) | **166** |
| **Total** | **2,094** |

---

## 4. WordPress Blog — `/blog/` ✅ WORKING

- **Title:** Akaroon || عكارون — by Ibrahim Ahmed Omer
- Arabic RTL content loads correctly
- Search bar in header functional
- Navigation menu present
- Social share buttons (Facebook, Twitter, WhatsApp) visible
- No `akaroon.com` links remaining in content (275+ replaced via WP-CLI)
- `wp-admin` accessible (redirects to login as expected)

---

## 5. WordPress Library — `/library/` ⚠️ BROKEN HOMEPAGE

WP instance loads (HTTP 200) but displays **"404 - Page not found"** because `show_on_front` is set to `posts` with no posts published. The theme, menus, and search bar all render correctly — only the front page content is missing.

**Fix:** WP Admin → Settings → Reading → set a static front page.

---

## 6. phpMyAdmin — `localhost:8081` ✅ WORKING

phpMyAdmin 5.2.3 accessible. All databases visible:

| Database | Purpose |
|---|---|
| `akaroon_akaroondb` | Main content (7 category tables, 2,094 records) |
| `akaroon_a-wordp-1gu` | WordPress blog |
| `akaroon_a-wordp-qxn` | WordPress library |
| `form-wizard` | Legacy filter tables (mostly empty) |

---

## 7. Issues Found

### 🔴 HIGH — `search.php` Fatal Error

**URL:** `/search.php`
**Error:** `Fatal error: Table 'test_db.test_db' doesn't exist`
Leftover test/development file querying a non-existent table. Not part of the production site — safe to delete or ignore.

---

### 🔴 HIGH — `FC.php` Fatal Error

**URL:** `/FC.php`
**Error:** `Fatal error: Table 'testing.tbl_customer' doesn't exist`
Another leftover development file with a hardcoded test database reference. Not part of the production site — safe to delete or ignore.

---

### 🟡 MEDIUM — Live-Search Returns No Results

**URL:** `/Live-Search/ibrahimfinalsearch.php`
The page and AJAX endpoint (`call_ajax.php?n=<query>`) function correctly, but the `form-wizard.meta4` table has **0 rows**. The live-search feature is fully wired up but has no data.
The production search at `/blog/ibrahimfinalsearch.php` works correctly and is the one in active use.

---

### 🟡 MEDIUM — Library WordPress 404 Homepage

**URL:** `/library/`
WordPress is running but no front page is assigned. The database has published pages (e.g. `التصنيفات`, `الرئيسية`, `Home`) but none is set as the front page in WP options.
**Fix:** WP Admin → Settings → Reading → set "A static page" and select the desired front page.

---

### 🟡 MEDIUM — `filter/search.php` Empty Filters

**URL:** `/filter/search.php`
The Author and Field of Research checkboxes are empty because `form-wizard.state` has 0 rows (placeholder table only). The per-category pages at `/files/*/search.php` work correctly as they query `akaroon_akaroondb`.

---

### 🟠 LOW — `search5.php` Stub

Renders an empty search form with no backend logic. Unused development file.

---

### 🟠 LOW — `Categories.php` Dead Links

All category links are `href="#Education"` style anchors pointing nowhere. Not connected to real navigation.

---

### 🟠 LOW — PDF & Image Files Not Available Locally

The 2,094 records have cover images and PDF links that resolve to local paths (e.g. `/files/التعليم/files/1.pdf`). These files were excluded from git (36 GB of content). Clicking them results in 404 locally — this is expected behaviour for the dev environment. The files exist on the live server at `akaroon.com`.

---

## 8. Summary

| Component | Status | Details |
|---|---|---|
| WordPress blog (`/blog/`) | ✅ Working | Full Arabic content, navigation, search |
| Main search (`ibrahimfinalsearch.php`) | ✅ Working | 2,094 records searchable across 7 tables |
| 7 category filter pages | ✅ Working | All returning live data with AJAX cards |
| phpMyAdmin | ✅ Working | All databases accessible |
| WordPress library (`/library/`) | ⚠️ Partial | Loads but needs front page configured |
| Live-Search (`/Live-Search/`) | ⚠️ Partial | Code works, `meta4` table is empty |
| `filter/search.php` | ⚠️ Partial | Loads, but filter lists empty (no data in `form-wizard`) |
| `search.php` | ❌ Broken | Fatal error — test file, not production |
| `FC.php` | ❌ Broken | Fatal error — test file, not production |
| PDF/image files | ⚠️ N/A locally | 36 GB excluded — exist on live server only |
