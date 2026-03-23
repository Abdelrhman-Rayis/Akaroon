<?php
/**
 * DRAFT MIGRATION SCRIPT
 * This script demonstrates how to migrate the existing 7 database tables
 * into the proposed unified `categories` and `documents` schema.
 * 
 * Usage Context: Reusable Smart Library Framework
 */

// Safety exit removed for execution

$_db_host   = getenv('WP_DB_HOST')     ?: 'mysql';
$_db_name   = getenv('WP_DB_NAME')     ?: 'akaroon_akaroondb';
$_db_user   = getenv('DB_USER')        ?: 'root';
$_db_pass   = getenv('DB_PASSWORD')    ?: 'root';

$pdo = new PDO("mysql:host=$_db_host;dbname=$_db_name;charset=utf8mb4", $_db_user, $_db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Starting migration to unified schema...\n";

// 1. Create the new Categories table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `slug` varchar(255) NOT NULL UNIQUE,
        `name_ar` varchar(255) NOT NULL,
        `name_en` varchar(255) DEFAULT NULL,
        `status` tinyint(1) DEFAULT '0',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 2. Create the new Documents table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `documents` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `category_id` int(11) NOT NULL,
        `original_id` varchar(255) DEFAULT NULL, /* To keep track of old row ID for media linking */
        `title` varchar(1000) NOT NULL,
        `author` varchar(1000) DEFAULT NULL,
        `year_of_issue` varchar(255) DEFAULT NULL,
        `field_of_research` varchar(1000) DEFAULT NULL,
        `place_of_issue` varchar(1000) DEFAULT NULL,
        `keywords` text DEFAULT NULL,
        `file_cover` varchar(500) DEFAULT NULL,
        `file_pdf` varchar(500) DEFAULT NULL,
        `metadata` json DEFAULT NULL,
        `status` tinyint(1) DEFAULT '0',
        PRIMARY KEY (`id`),
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 3. Define the categories mapping
$categories = [
    'tas'   => ['slug' => 'tasweq', 'name_ar' => 'التأصيل'],
    'edu'   => ['slug' => 'education', 'name_ar' => 'التعليم'],
    'soc'   => ['slug' => 'society', 'name_ar' => 'المجتمع'],
    'pol'   => ['slug' => 'politics', 'name_ar' => 'السياسة'],
    'org'   => ['slug' => 'organizations', 'name_ar' => 'منظمات'],
    'state' => ['slug' => 'state', 'name_ar' => 'الدولة'],
    'philo' => ['slug' => 'philosophy', 'name_ar' => 'الفلسفة'],
];

// 4. Migrate Data
foreach ($categories as $old_table => $cat_data) {
    try {
        // Insert category
        $stmtCat = $pdo->prepare("INSERT IGNORE INTO `categories` (`slug`, `name_ar`) VALUES (?, ?)");
        $stmtCat->execute([$cat_data['slug'], $cat_data['name_ar']]);
        
        $stmtGetCat = $pdo->prepare("SELECT id FROM `categories` WHERE `slug` = ?");
        $stmtGetCat->execute([$cat_data['slug']]);
        $categoryId = $stmtGetCat->fetchColumn();

        // Check if old table exists
        $result = $pdo->query("SHOW TABLES LIKE '{$old_table}'");
        if ($result->rowCount() > 0) {
            echo "Migrating data from table: $old_table\n";
            $rows = $pdo->query("SELECT * FROM `$old_table`")->fetchAll(PDO::FETCH_ASSOC);

            $stmtDoc = $pdo->prepare("
                INSERT INTO `documents` (
                    `category_id`, `original_id`, `title`, `author`, `year_of_issue`, 
                    `field_of_research`, `place_of_issue`, `keywords`, `file_cover`, `file_pdf`, `status`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($rows as $row) {
                // Ensure the exact original ID is maintained as the filename
                $pdf_filename = $row['id'] . ".pdf"; 
                $stmtDoc->execute([
                    $categoryId,
                    $row['id'],
                    $row['The_Title_of_Paper_Book'],
                    $row['The_number_of_the_Author'], // the strangely named author col
                    $row['Year_of_issue'],
                    $row['Field_of_research'],
                    $row['Place_of_issue'],
                    $row['Key_words'],
                    $row['image'], // cover image
                    $pdf_filename,
                    $row['status']
                ]);
            }
            echo "Successfully migrated " . count($rows) . " rows from $old_table.\n";
        } else {
            echo "Table $old_table does not exist. Skipping.\n";
        }

    } catch (Exception $e) {
        echo "Error migrating $old_table: " . $e->getMessage() . "\n";
    }
}

echo "Migration completed successfully!\n";
?>
