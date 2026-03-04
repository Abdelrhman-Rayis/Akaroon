<?php
/**
 * LOCAL DEVELOPMENT wp-config for: /blog
 * Mounted by Docker over the original wp-config.php
 * Points to the MySQL Docker container instead of the live server.
 */

// --- Database (local Docker MySQL) ---
define( 'DB_NAME',     'akaroon_a-wordp-1gu' );
define( 'DB_USER',     'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST',     'mysql' );   // Docker service name
define( 'DB_CHARSET',  'utf8' );
define( 'DB_COLLATE',  '' );

// --- Local URLs ---
define( 'WP_HOME',    'http://localhost:8082/blog' );
define( 'WP_SITEURL', 'http://localhost:8082/blog' );

// --- Auth keys (local dev only — not secure, do not reuse on production) ---
define( 'AUTH_KEY',         'local-auth-key-blog' );
define( 'SECURE_AUTH_KEY',  'local-secure-auth-key-blog' );
define( 'LOGGED_IN_KEY',    'local-logged-in-key-blog' );
define( 'NONCE_KEY',        'local-nonce-key-blog' );
define( 'AUTH_SALT',        'local-auth-salt-blog' );
define( 'SECURE_AUTH_SALT', 'local-secure-auth-salt-blog' );
define( 'LOGGED_IN_SALT',   'local-logged-in-salt-blog' );
define( 'NONCE_SALT',       'local-nonce-salt-blog' );

// --- Table prefix ---
$table_prefix = 'wp_';

// --- Debug (helpful for local dev) ---
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );

// --- Disable caching plugins that need LiteSpeed/server-side infra ---
define( 'WP_CACHE', false );

// --- Disable SSL enforcement (we're on plain HTTP locally) ---
define( 'FORCE_SSL_ADMIN', false );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
