<?php
/**
 * WordPress config for Cloud Run + Cloud SQL
 * All credentials come from environment variables set in Cloud Run.
 */

// ── Database ───────────────────────────────────────────────────────────────
define( 'DB_NAME',    getenv('WP_BLOG_DB_NAME') ?: 'akaroon_wpblog' );
define( 'DB_USER',    getenv('DB_USER')         ?: '' );
define( 'DB_PASSWORD',getenv('DB_PASSWORD')     ?: '' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

// Cloud SQL uses a Unix socket: /cloudsql/PROJECT:REGION:INSTANCE
// Set DB_SOCKET env var in Cloud Run; leave unset for local Docker (uses DB_HOST).
$_socket = getenv('DB_SOCKET');
if ( $_socket ) {
    // WordPress socket format: "127.0.0.1:/path/to/socket"
    define( 'DB_HOST', '127.0.0.1:' . $_socket );
} else {
    define( 'DB_HOST', getenv('DB_HOST') ?: 'mysql' );
}
unset( $_socket );

// ── URLs ───────────────────────────────────────────────────────────────────
$_base = rtrim( getenv('WP_URL') ?: 'http://localhost:8082', '/' );
define( 'WP_HOME',    $_base . '/blog' );
define( 'WP_SITEURL', $_base . '/blog' );
unset( $_base );

// ── Auth keys (set unique values in Cloud Run env vars) ───────────────────
define( 'AUTH_KEY',         getenv('WP_AUTH_KEY')         ?: 'change-me-auth-key' );
define( 'SECURE_AUTH_KEY',  getenv('WP_SECURE_AUTH_KEY')  ?: 'change-me-secure-auth-key' );
define( 'LOGGED_IN_KEY',    getenv('WP_LOGGED_IN_KEY')    ?: 'change-me-logged-in-key' );
define( 'NONCE_KEY',        getenv('WP_NONCE_KEY')        ?: 'change-me-nonce-key' );
define( 'AUTH_SALT',        getenv('WP_AUTH_SALT')        ?: 'change-me-auth-salt' );
define( 'SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT') ?: 'change-me-secure-auth-salt' );
define( 'LOGGED_IN_SALT',   getenv('WP_LOGGED_IN_SALT')   ?: 'change-me-logged-in-salt' );
define( 'NONCE_SALT',       getenv('WP_NONCE_SALT')       ?: 'change-me-nonce-salt' );

// ── Misc ───────────────────────────────────────────────────────────────────
$table_prefix = 'wp_';
define( 'WP_DEBUG',         false );
define( 'WP_DEBUG_LOG',     false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_CACHE',         false );
define( 'FORCE_SSL_ADMIN',  false );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
