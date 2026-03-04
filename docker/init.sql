-- Local development database initialization
-- Runs before the WordPress SQL dumps

-- WordPress Blog database
CREATE DATABASE IF NOT EXISTS `wordpress_blog`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- WordPress Library database
CREATE DATABASE IF NOT EXISTS `wordpress_library`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Main app database (search index used by ibrahimfinalsearch.php etc.)
CREATE DATABASE IF NOT EXISTS `akaroon_main`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Test/dev databases referenced by legacy scripts (search.php, search5.php, FC.php)
CREATE DATABASE IF NOT EXISTS `test_db`  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `test`     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS `testing`  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Ensure root can connect from any host (phpMyAdmin / TablePlus / host tools)
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
