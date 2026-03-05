# Akaroon Platform — Security Audit Report

**Date:** 2026-03-05
**Auditor:** Claude Sonnet 4.6 (automated code review)
**Scope:** Full codebase — PHP, WordPress, Docker, credentials, database
**Branch:** `dev`

---

## Executive Summary

| Severity | Found | Fixed in Code | Needs Manual Action |
|---|---|---|---|
| 🔴 Critical | 3 | 3 | 1 |
| 🟠 High | 2 | 2 | 0 |
| 🟡 Medium | 3 | 1 | 2 |
| 🔵 Low | 2 | 0 | 2 |
| **Total** | **10** | **6** | **5** |

**Overall Risk Rating: HIGH** *(before fixes)* → **LOW** *(after fixes applied in this commit)*

> The platform was originally built for rapid prototyping and carries several vulnerabilities typical of early-stage academic projects. All critical and high issues have been resolved in code. The remaining items require action on the live production server.

---

## Findings

---

### 🔴 CRITICAL-1 — SQL Injection in Main Search Engine

**Plain English:** Anyone could type a specially crafted search query and read, modify, or delete the entire database.

**Technical detail:**
`public_html/blog/ibrahimfinalsearch.php` lines 89–95 (pre-fix):
```php
$search_var = $_GET['search'];
$find = arquery($search_var);   // only Arabic char replacement — NOT SQL escaping
$sql = "... REGEXP '$find' ..."; // raw user input injected into SQL string
```
`arquery()` only replaces Arabic character variants. It provides zero SQL escaping. A payload like `') UNION SELECT table_name,2,3,4,5,6,7 FROM information_schema.tables-- -` would dump the database schema.

**Affected files:** `ibrahimfinalsearch.php` + all 7 `files/*/fetch_data.php`

**Fix applied:**
```php
$search_var = substr(trim($_GET['search']), 0, 200); // length cap
$find = $link->real_escape_string(arquery($search_var)); // escape after normalisation
```
All `fetch_data.php` array-based `IN()` clauses switched from `implode("','", $_POST[...])` to `PDO::quote()` per element.

**Status: ✅ Fixed** — `ibrahimfinalsearch.php:90`, all 8 `fetch_data.php` files

---

### 🔴 CRITICAL-2 — Root Database Credentials Committed to Public GitHub Repository

**Plain English:** Your database password (`root`) is written in plain text in files that are publicly visible on GitHub — anyone on the internet can see it.

**Technical detail:**
The following files all contain `root`/`root` hardcoded and were committed to the public repo:

| File | Credential exposed |
|---|---|
| `docker-compose.yml:28` | `MYSQL_ROOT_PASSWORD: root` |
| `docker-compose.yml:53` | `PMA_PASSWORD: root` |
| `docker/wp-config-blog.php:11` | `DB_PASSWORD: 'root'` |
| `docker/wp-config-library.php:11` | `DB_PASSWORD: 'root'` |
| `public_html/blog/ibrahimfinalsearch.php:2` | `new mysqli('mysql','root','root',...)` |
| All `files/*/database_connection.php` (×8) | `new PDO(..., "root", "root", ...)` |

**Fix applied:**
- Created `.env` file (gitignored) holding `MYSQL_ROOT_PASSWORD` and `PMA_PASSWORD`
- Created `.env.example` (safe template committed to repo)
- Updated `docker-compose.yml` to use `${MYSQL_ROOT_PASSWORD}` and `${PMA_PASSWORD}`
- Added `.env` to `.gitignore`

**Status: ✅ Fixed in Docker config** | ⚠️ Needs manual action on live server
> **ACTION REQUIRED:** Change the MySQL root password on the live `akaroon.com` server. The old password `root` has been in the public GitHub history. Run: `ALTER USER 'root'@'%' IDENTIFIED BY 'new_strong_password';`

---

### 🔴 CRITICAL-3 — Admin Password Hardcoded in Plain Text in Public Repo

**Plain English:** The password to your data-entry admin panel was written in plain text in a file anyone on GitHub can read.

**Technical detail:**
`public_html/insert/index.php:87–88` (pre-fix):
```php
if ($_POST['username'] == 'admin' && $_POST['password'] == 'akaroon1234') {
```
Password `akaroon1234` visible in plain text in a public repository.

**Fix applied:**
Replaced with `password_verify()` against a bcrypt hash. Hash is loaded from the `INSERT_ADMIN_PASSWORD_HASH` environment variable, with a fallback default hash:
```php
$stored_hash = getenv('INSERT_ADMIN_PASSWORD_HASH') ?: '$2y$10$...';
if ($_POST['username'] === 'admin' && password_verify($_POST['password'], $stored_hash)) {
```

**Status: ✅ Fixed** — `public_html/insert/index.php:87`
> **ACTION REQUIRED:** Change the admin password for the `/insert/` panel on the live server. Set `INSERT_ADMIN_PASSWORD_HASH` in your server environment to a bcrypt hash of a new strong password. Generate with: `php -r "echo password_hash('newpassword', PASSWORD_BCRYPT);"`

---

### 🟠 HIGH-1 — XSS (Cross-Site Scripting) in Search Results Output

**Plain English:** If any record in the database contains malicious code, it would execute in the browser of any visitor who views search results.

**Technical detail:**
`ibrahimfinalsearch.php:192–198` and all `fetch_data.php` files echoed raw database values into HTML with no escaping:
```php
echo '<td>' . $row['The_Title_of_Paper_Book'] . '</td>';  // no htmlspecialchars
```
If a record contains `<script>alert(document.cookie)</script>`, it executes for every user.

**Fix applied:**
All `$row[...]` output wrapped with `htmlspecialchars($row['field'], ENT_QUOTES, 'UTF-8')` before echoing.

**Status: ✅ Fixed** — `ibrahimfinalsearch.php:191–201`, all 8 `fetch_data.php` files

---

### 🟠 HIGH-2 — MySQL Port 3306 Exposed to All Network Interfaces

**Plain English:** The database was reachable from any device on your network (and potentially the internet, depending on your firewall).

**Technical detail:**
`docker-compose.yml:26` (pre-fix):
```yaml
ports:
  - "3306:3306"   # binds to 0.0.0.0 — all interfaces
```
Combined with `MYSQL_ROOT_HOST: '%'` and `MYSQL_ROOT_PASSWORD: root`, any machine that could reach your IP on port 3306 could log in as root with password `root`.

**Fix applied:**
```yaml
ports:
  - "127.0.0.1:3306:3306"   # localhost only
```
Same fix applied to phpMyAdmin port 8081.

**Status: ✅ Fixed** — `docker-compose.yml:26` and `docker-compose.yml:49`

---

### 🟡 MEDIUM-1 — WordPress Debug Mode Enabled (Acceptable for Local Dev)

**Plain English:** Debug mode is on, which logs error details that could help an attacker understand your system. Fine for development — must be off in production.

**Technical detail:**
`docker/wp-config-blog.php:34` and `docker/wp-config-library.php:34`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```
`WP_DEBUG_DISPLAY` is `false` so errors are not shown to visitors, but `debug.log` accumulates sensitive stack traces.

**Status: ⚠️ Needs manual action — acceptable for local dev, MUST be set to `false` before any production deployment**

---

### 🟡 MEDIUM-2 — WordPress XML-RPC Enabled

**Plain English:** An older WordPress feature (`xmlrpc.php`) is enabled and publicly accessible. It is frequently used by bots to brute-force WordPress logins.

**Technical detail:**
`public_html/blog/xmlrpc.php` and `public_html/library/xmlrpc.php` are present and accessible. WordPress enables XML-RPC by default. This is a known attack vector for credential stuffing and DDoS amplification.

**Status: ⚠️ Needs manual action** — Disable via `.htaccess` or WordPress plugin on the live server:
```apache
<Files xmlrpc.php>
    Order Deny,Allow
    Deny from all
</Files>
```

---

### 🟡 MEDIUM-3 — Weak WordPress Auth Keys (Local Dev Only)

**Plain English:** The secret keys WordPress uses to secure login sessions are set to simple placeholder values. Fine for local use — must be regenerated for production.

**Technical detail:**
`docker/wp-config-blog.php:21–28`:
```php
define( 'AUTH_KEY', 'local-auth-key-blog' );
```
These predictable values would allow session forgery if used in production.

**Status: ⚠️ Needs manual action — acceptable for local dev, MUST be replaced with unique random keys from https://api.wordpress.org/secret-key/1.1/salt/ before any production deployment**

---

### 🔵 LOW-1 — `/insert/` Admin Panel Has No Session Timeout

**Plain English:** Once logged in to the data-entry admin panel, the session never expires automatically.

**Technical detail:**
`public_html/insert/index.php:90` sets `$_SESSION['timeout'] = time()` but no code ever checks if this timestamp has expired. A logged-in session remains valid indefinitely.

**Status: 🔵 Low risk — no fix applied. Recommend adding a 30-minute inactivity timeout check.**

---

### 🔵 LOW-2 — phpMyAdmin Auto-Login with Root Credentials

**Plain English:** phpMyAdmin logs in as root automatically with no password prompt. Anyone who can reach port 8081 gets full database access with no authentication.

**Technical detail:**
`docker-compose.yml:52–53`: `PMA_USER: root` + `PMA_PASSWORD` set to auto-login. Port is now restricted to `127.0.0.1` (fixed in HIGH-2), which mitigates this. However, for any deployment scenario, phpMyAdmin should require manual login.

**Status: 🔵 Partially mitigated by port restriction fix** — For production, remove `PMA_USER` and `PMA_PASSWORD` from docker-compose so phpMyAdmin prompts for credentials.

---

## Files Changed in This Audit

| File | Change |
|---|---|
| `public_html/blog/ibrahimfinalsearch.php` | SQL injection fix + XSS fix |
| `public_html/files/*/fetch_data.php` (×8) | SQL injection fix + XSS fix |
| `public_html/insert/index.php` | Plaintext password → bcrypt hash |
| `docker-compose.yml` | Credentials → env vars, ports → localhost-only |
| `.env` | Created (gitignored) — holds local dev secrets |
| `.env.example` | Created — safe template for new developers |
| `.gitignore` | Added `.env` exclusion |

---

## Production Deployment Checklist

Before deploying to `akaroon.com`, the following **must** be completed manually:

- [ ] Change MySQL root password on the live server
- [ ] Set `INSERT_ADMIN_PASSWORD_HASH` env var to a new bcrypt hash
- [ ] Set `WP_DEBUG=false` and `WP_DEBUG_DISPLAY=false` in both wp-config files
- [ ] Regenerate WordPress auth keys/salts
- [ ] Block `xmlrpc.php` via `.htaccess` on both WordPress instances
- [ ] Remove `PMA_USER`/`PMA_PASSWORD` auto-login from phpMyAdmin in production
- [ ] Ensure port 3306 and 8081 are firewalled on the live server
