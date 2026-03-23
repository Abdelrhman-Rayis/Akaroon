<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
class Library_Management_System_Activator {

    public function activate() {
        $this->owt7_library_generate_plugin_tables();
        $this->owt7_library_update_tables_for_multilingual();
        $this->owt7_library_insert_default_data();
        $this->owt7_library_options();
        $this->owt7_library_shortcodes();
    }

    private function owt7_library_generate_plugin_tables() {

        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $tables = [
            'users' => [
                'name' => $this->owt7_library_tbl_users(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    register_from ENUM('web', 'admin') DEFAULT 'admin',
                    u_id VARCHAR(20) DEFAULT NULL,
                    name VARCHAR(255) DEFAULT NULL,
                    email VARCHAR(80) DEFAULT NULL,
                    gender ENUM('male', 'female', 'other') DEFAULT NULL,
                    branch_id INT(5) DEFAULT NULL,
                    phone_no VARCHAR(20) DEFAULT NULL,
                    profile_image VARCHAR(220) DEFAULT NULL,
                    address_info TEXT,
                    status INT NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'books' => [
                'name' => $this->owt7_library_tbl_books(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    book_id VARCHAR(20) DEFAULT NULL,
                    bookcase_id INT(5) DEFAULT NULL,
                    bookcase_section_id INT(5) DEFAULT NULL,
                    category_id INT(5) DEFAULT NULL,
                    name VARCHAR(255) DEFAULT NULL,
                    author_name VARCHAR(255) DEFAULT NULL,
                    publication_name VARCHAR(255) DEFAULT NULL,
                    publication_year VARCHAR(10) DEFAULT NULL,
                    publication_location VARCHAR(255) DEFAULT NULL,
                    amount VARCHAR(10) DEFAULT NULL,
                    cover_image VARCHAR(200) DEFAULT NULL,
                    isbn VARCHAR(20) DEFAULT NULL,
                    book_url VARCHAR(220) DEFAULT NULL,
                    stock_quantity INT(5) DEFAULT NULL,
                    book_language VARCHAR(50) DEFAULT NULL,
                    book_pages INT(5) DEFAULT NULL,
                    description TEXT,
                    status INT NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'bookcase' => [
                'name' => $this->owt7_library_tbl_bookcase(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) DEFAULT NULL,
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'bookcase_sections' => [
                'name' => $this->owt7_library_tbl_bookcase_sections(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) DEFAULT NULL,
                    bookcase_id INT(5) DEFAULT NULL,
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'branch' => [
                'name' => $this->owt7_library_tbl_branch(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) DEFAULT NULL,
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'category' => [
                'name' => $this->owt7_library_tbl_category(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) DEFAULT NULL,
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'book_borrow' => [
                'name' => $this->owt7_library_tbl_book_borrow(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    borrow_id VARCHAR(11) DEFAULT NULL,
                    category_id INT(5) DEFAULT NULL,
                    book_id INT(5) DEFAULT NULL,
                    branch_id INT(5) DEFAULT NULL,
                    u_id INT(5) DEFAULT NULL,
                    borrows_days INT(5) DEFAULT NULL,
                    return_date VARCHAR(20) DEFAULT NULL,
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'book_return' => [
                'name' => $this->owt7_library_tbl_book_return(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    borrow_id VARCHAR(11) DEFAULT NULL,
                    category_id INT(5) DEFAULT NULL,
                    book_id INT(5) DEFAULT NULL,
                    branch_id INT(5) DEFAULT NULL,
                    u_id INT(5) DEFAULT NULL,
                    has_fine_status ENUM('1', '0') NOT NULL DEFAULT '0',
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
            'book_late_fine' => [
                'name' => $this->owt7_library_tbl_book_late_fine(),
                'sql' => "CREATE TABLE %s (
                    id INT NOT NULL AUTO_INCREMENT,
                    return_id INT(5) DEFAULT NULL,
                    book_id INT(5) DEFAULT NULL,
                    u_id INT(5) DEFAULT NULL,
                    extra_days INT(5) DEFAULT NULL,
                    fine_amount INT(5) DEFAULT NULL,
                    has_paid ENUM('1', '2') NOT NULL DEFAULT '1' COMMENT '1 - Not Paid, 2 - Paid',
                    status ENUM('1', '0') NOT NULL DEFAULT '1',
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) %s;"
            ],
        ];

        foreach ($tables as $table) {
            $cache_key = 'table_exists_' . md5($table['name']);
            $table_exists = wp_cache_get($cache_key);
            if (false === $table_exists) {
                $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table['name'])) == $table['name'];
                wp_cache_set($cache_key, $table_exists);
            }

            if (!$table_exists) {
                dbDelta(sprintf($table['sql'], $table['name'], $wpdb->get_charset_collate()));
                wp_cache_delete($cache_key);
            }
        }

    }

    /**
     * Update existing tables for multilingual support
     *
     * @since 3.4
     */
    private function owt7_library_update_tables_for_multilingual() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        
        // If charset_collate is empty, default to utf8mb4
        if (empty($charset_collate)) {
            $charset_collate = 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
        }
        
        // Define column updates for each table
        $table_updates = [
            $this->owt7_library_tbl_users() => [
                'name' => 'VARCHAR(255)',
            ],
            $this->owt7_library_tbl_books() => [
                'name' => 'VARCHAR(255)',
                'author_name' => 'VARCHAR(255)',
                'publication_name' => 'VARCHAR(255)',
                'publication_location' => 'VARCHAR(255)',
            ],
            $this->owt7_library_tbl_bookcase() => [
                'name' => 'VARCHAR(255)',
            ],
            $this->owt7_library_tbl_bookcase_sections() => [
                'name' => 'VARCHAR(255)',
            ],
            $this->owt7_library_tbl_branch() => [
                'name' => 'VARCHAR(255)',
            ],
            $this->owt7_library_tbl_category() => [
                'name' => 'VARCHAR(255)',
            ],
        ];

        foreach ($table_updates as $table_name => $columns) {
            // Check if table exists
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) == $table_name;
            
            if ($table_exists) {
                // Update table charset/collation to utf8mb4 if not already set
                $table_info = $wpdb->get_row($wpdb->prepare(
                    "SELECT table_collation FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
                    $wpdb->dbname,
                    $table_name
                ));
                
                if ($table_info && strpos($table_info->table_collation, 'utf8mb4') === false) {
                    $wpdb->query("ALTER TABLE `{$table_name}` CONVERT TO {$charset_collate}");
                }
                
                // Update each column
                foreach ($columns as $column_name => $column_definition) {
                    // Check if column exists and get current definition
                    $current_col = $wpdb->get_row(
                        $wpdb->prepare(
                            "SHOW COLUMNS FROM `{$table_name}` WHERE Field = %s",
                            $column_name
                        )
                    );
                    
                    if ($current_col) {
                        // Check if column needs updating (size is smaller than 255)
                        $needs_update = false;
                        if (preg_match('/VARCHAR\((\d+)\)/', $current_col->Type, $matches)) {
                            $current_size = (int)$matches[1];
                            if ($current_size < 255) {
                                $needs_update = true;
                            }
                        } elseif (strpos($current_col->Type, 'VARCHAR') !== false && strpos($current_col->Type, '255') === false) {
                            $needs_update = true;
                        }
                        
                        if ($needs_update) {
                            // Build ALTER statement preserving NULL, DEFAULT, etc.
                            $null_attr = $current_col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                            $default_attr = '';
                            if ($current_col->Default !== null) {
                                $default_attr = "DEFAULT " . ($current_col->Default === 'CURRENT_TIMESTAMP' 
                                    ? 'CURRENT_TIMESTAMP' 
                                    : "'" . esc_sql($current_col->Default) . "'");
                            } elseif ($current_col->Null === 'YES') {
                                $default_attr = 'DEFAULT NULL';
                            }
                            
                            $alter_sql = sprintf(
                                "ALTER TABLE `%s` MODIFY `%s` %s %s %s",
                                $table_name,
                                $column_name,
                                $column_definition,
                                $null_attr,
                                $default_attr
                            );
                            
                            $wpdb->query($alter_sql);
                        }
                    }
                }
            }
        }
    }

    /**
     * Insert default data.
     *
     * @since 3.0
     */
    private function owt7_library_insert_default_data() {
        // Implementation to insert default data
    }

    private function owt7_library_shortcodes() {
        global $wpdb;

        // Array of pages to create
        $pages = [
            [
                'title'   => "Library Books",
                'content' => "[owt7_library_books]"
            ]
        ];

        // Create pages
        foreach ($pages as $page) {

            // Generate a consistent slug
            $slug = "wp-" . sanitize_title($page['title']);

            // Check if the page already exists by slug
            $is_page_exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->prefix}posts WHERE post_name = %s AND post_type = 'page' AND post_status IN ('publish', 'draft', 'pending')",
                    $slug
                )
            );

            // Only create page if it doesn't exist
            if (empty($is_page_exists)) {
                wp_insert_post([
                    'post_title'     => $page['title'],
                    'post_content'   => $page['content'],
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'post_name'      => $slug,
                    'post_author'    => 1, // Admin user ID
                    'post_date'      => current_time('mysql'),
                    'post_date_gmt'  => current_time('mysql', true)
                ]);
            }
        }
    }

    /**
     * Add plugin options.
     *
     * @since 3.0
     */
    private function owt7_library_options() {
        update_option('owt7_library_version', '3.4');
        update_option('owt7_library_system', serialize(['lms' => 'free']));
        update_option('owt7_library_db_tables', serialize([
            $this->owt7_library_tbl_branch(),
            $this->owt7_library_tbl_users(),
            $this->owt7_library_tbl_bookcase(),
            $this->owt7_library_tbl_bookcase_sections(),
            $this->owt7_library_tbl_category(),
            $this->owt7_library_tbl_books(),
            $this->owt7_library_tbl_book_borrow(),
            $this->owt7_library_tbl_book_return(),
            $this->owt7_library_tbl_book_late_fine()
        ]));
        update_option('owt7_lms_late_fine_currency', '1');
        update_option('owt7_lms_country', 'India');
        update_option('owt7_lms_currency', 'INR');
    }

    /**
     * Return the users table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_users() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_users';
    }

    /**
     * Return the books table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_books() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_books';
    }

    /**
     * Return the bookcase table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_bookcase() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_bookcase';
    }

    /**
     * Return the bookcase sections table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_bookcase_sections() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_bookcase_sections';
    }

    /**
     * Return the branch table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_branch() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_branch';
    }

    /**
     * Return the category table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_category() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_category';
    }

    /**
     * Return the book borrow table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_book_borrow() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_book_borrow';
    }

    /**
     * Return the book return table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_book_return() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_book_return';
    }

    /**
     * Return the book late fine table name.
     *
     * @since 3.0
     */
    public function owt7_library_tbl_book_late_fine() {
        global $wpdb;
        return $wpdb->prefix . 'owt7_lib_book_late_fine';
    }
}
?>
