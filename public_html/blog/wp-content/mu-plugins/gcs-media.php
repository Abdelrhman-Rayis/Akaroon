<?php
/**
 * Plugin Name: GCS Media Rewrite
 * Description: Rewrites WordPress media attachment URLs to Google Cloud Storage
 *              when running on Cloud Run (MEDIA_BASE_URL env var is set).
 *              Falls back to default behaviour in local dev (env var absent).
 */

defined( 'ABSPATH' ) || exit;

$_gcs_media_base = rtrim( getenv('MEDIA_BASE_URL') ?: '', '/' );
if ( ! $_gcs_media_base ) {
    return; // Local dev — do nothing
}

$_gcs_uploads_url = $_gcs_media_base . '/wp-uploads';

/**
 * Replace any local uploads URL with the GCS equivalent.
 * Handles both localhost:8082 (imported from local) and the Cloud Run URL.
 */
function akaroon_rewrite_to_gcs( $url ) {
    global $_gcs_uploads_url;

    // Patterns that need replacing
    $patterns = [
        // Cloud Run URL pattern
        'https://akaroon-git-844063198632.europe-west1.run.app/blog/wp-content/uploads',
        // Local dev pattern (imported DB)
        'http://localhost:8082/blog/wp-content/uploads',
        'http://localhost/blog/wp-content/uploads',
        // Any other host — catch-all using site URL
        rtrim( get_option('siteurl'), '/' ) . '/wp-content/uploads',
    ];

    foreach ( $patterns as $pattern ) {
        if ( strpos( $url, $pattern ) !== false ) {
            return str_replace( $pattern, $_gcs_uploads_url, $url );
        }
    }
    return $url;
}

// Rewrite single attachment URLs
add_filter( 'wp_get_attachment_url', 'akaroon_rewrite_to_gcs', 99 );

// Rewrite srcset URLs (responsive images)
add_filter( 'wp_calculate_image_srcset', function( $sources ) {
    foreach ( $sources as &$source ) {
        $source['url'] = akaroon_rewrite_to_gcs( $source['url'] );
    }
    return $sources;
}, 99 );

// Rewrite URLs inside post content (gallery shortcodes store absolute URLs)
add_filter( 'the_content', function( $content ) {
    global $_gcs_uploads_url;

    // 1. Rewrite wp-content/uploads → GCS
    $content = str_replace(
        [
            'https://akaroon-git-844063198632.europe-west1.run.app/blog/wp-content/uploads',
            'http://localhost:8082/blog/wp-content/uploads',
            'http://localhost/blog/wp-content/uploads',
        ],
        $_gcs_uploads_url,
        $content
    );

    // 2. Rewrite any remaining localhost:8082 origin → relative path
    //    e.g. http://localhost:8082/files/التأصيل/search.php → /files/التأصيل/search.php
    $content = str_replace(
        [ 'http://localhost:8082', 'http://localhost' ],
        '',
        $content
    );

    return $content;
}, 99 );
