<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms">

    <div class="jumbotron">
        <h1><?php esc_html_e("Welcome to Library Management System", "library-management-system"); ?><sup class="premium"><?php esc_html_e("Free", "library-management-system"); ?></sup><sup>v<?php echo esc_html(LIBMNS_VERSION); ?></sup></h1>
    </div>

    <div class="lms-dashboard card-container">
        <!-- Card 1 -->
        <div class="card">
            <span class="dashicons dashicons-book"></span>
            <h2><?php esc_html_e("Manage Books", "library-management-system"); ?></h2>
            <a href="admin.php?page=owt7_library_books"><?php esc_html_e("View Books", "library-management-system"); ?></a>
        </div>
        <!-- Card 2 -->
        <div class="card">
            <span class="dashicons dashicons-admin-users"></span>
            <h2><?php esc_html_e("Manage Users", "library-management-system"); ?></h2>
            <a href="admin.php?page=owt7_library_users"><?php esc_html_e("View Users", "library-management-system"); ?></a>
        </div>
        <!-- Card 3 -->
        <div class="card">
            <span class="dashicons dashicons-archive"></span>
            <h2><?php esc_html_e("Manage Bookcase", "library-management-system"); ?></h2>
            <a href="admin.php?page=owt7_library_bookcases"><?php esc_html_e("View Bookcases", "library-management-system"); ?></a>
        </div>
        <!-- Card 4 -->
        <div class="card">
            <span class="dashicons dashicons-chart-bar"></span>
            <h2><?php esc_html_e("Transaction Reports", "library-management-system"); ?></h2>
            <a href="admin.php?page=owt7_library_transactions"><?php esc_html_e("View Reports", "library-management-system"); ?></a>
        </div>
        <!-- Card 5 -->
        <div class="card">
            <span class="dashicons dashicons-admin-tools"></span>
            <h2><?php esc_html_e("Settings", "library-management-system"); ?></h2>
            <a href="admin.php?page=owt7_library_settings"><?php esc_html_e("Configure Settings", "library-management-system"); ?></a>
        </div>
    </div>
</div>
