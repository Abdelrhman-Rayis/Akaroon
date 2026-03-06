<?php
/**
 * One-time startup fix: update WordPress nav menu items that still point
 * to old akaroon.com or localhost URLs.  Runs via PHP CLI from start.sh
 * before Apache starts.  Idempotent — safe to run on every container start.
 */

$socket   = getenv('DB_SOCKET')       ?: '';
$host     = getenv('WP_DB_HOST')      ?: (getenv('DB_HOST') ?: 'mysql');
$user     = getenv('DB_USER')         ?: 'root';
$pass     = getenv('DB_PASSWORD')     ?: '';
$blog_db  = getenv('WP_BLOG_DB_NAME') ?: 'akaroon_a-wordp-1gu';
$site_url = rtrim(getenv('WP_URL') ?: 'https://akaroon-git-844063198632.europe-west1.run.app', '/');

$dsn = $socket
    ? "mysql:unix_socket={$socket};dbname={$blog_db};charset=utf8mb4"
    : "mysql:host={$host};dbname={$blog_db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    ]);

    // Fix menu items pointing to old akaroon.com or localhost (non-blog links)
    $stmt = $pdo->prepare(
        "UPDATE wp_postmeta
         SET    meta_value = :url
         WHERE  meta_key   = '_menu_item_url'
           AND  meta_value != ''
           AND  meta_value != :url
           AND  (meta_value LIKE '%akaroon.com%' OR meta_value LIKE '%localhost%')
           AND  meta_value NOT LIKE '%/blog%'
           AND  meta_value NOT LIKE '%/library%'"
    );
    $stmt->execute([':url' => $site_url . '/']);
    $fixed_home = $stmt->rowCount();

    // Fix blog menu items pointing to old akaroon.com/blog or localhost/blog
    $stmt2 = $pdo->prepare(
        "UPDATE wp_postmeta
         SET    meta_value = :url
         WHERE  meta_key   = '_menu_item_url'
           AND  meta_value != ''
           AND  meta_value != :url
           AND  (meta_value LIKE '%akaroon.com/blog%' OR meta_value LIKE '%localhost%/blog%')"
    );
    $stmt2->execute([':url' => $site_url . '/blog/']);
    $fixed_blog = $stmt2->rowCount();

    fwrite(STDERR, "[fix-menu] Updated {$fixed_home} home + {$fixed_blog} blog menu items → {$site_url}\n");

} catch (Exception $e) {
    fwrite(STDERR, "[fix-menu] WARNING: Could not fix menu URLs: " . $e->getMessage() . "\n");
    // Non-fatal — let Apache start anyway
}
