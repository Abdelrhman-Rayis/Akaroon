<?php
/**
 * LOCAL DEVELOPMENT wp-config for: /library
 * Mounted by Docker over the original wp-config.php
 * Points to the MySQL Docker container instead of the live server.
 */

// --- Database (local Docker MySQL) ---
define( 'DB_NAME',     'akaroon_a-wordp-qxn' );
define( 'DB_USER',     'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST',     'mysql' );   // Docker service name
define( 'DB_CHARSET',  'utf8' );
define( 'DB_COLLATE',  '' );

// --- Local URLs ---
define( 'WP_HOME',    'http://localhost:8082/library' );
define( 'WP_SITEURL', 'http://localhost:8082/library' );

// --- Auth keys (local dev only — not secure, do not reuse on production) ---
define( 'AUTH_KEY',         'local-auth-key-library' );
define( 'SECURE_AUTH_KEY',  'local-secure-auth-key-library' );
define( 'LOGGED_IN_KEY',    'local-logged-in-key-library' );
define( 'NONCE_KEY',        'local-nonce-key-library' );
define( 'AUTH_SALT',        'local-auth-salt-library' );
define( 'SECURE_AUTH_SALT', 'local-secure-auth-salt-library' );
define( 'LOGGED_IN_SALT',   'local-logged-in-salt-library' );
define( 'NONCE_SALT',       'local-nonce-salt-library' );

// --- Table prefix ---
$table_prefix = 'wp_';

// --- Debug ---
define( 'WP_DEBUG',         true );
define( 'WP_DEBUG_LOG',     true );
define( 'WP_DEBUG_DISPLAY', false );

// --- Disable caching / SSL enforcement for local HTTP ---
define( 'WP_CACHE', false );
define( 'FORCE_SSL_ADMIN', false );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
