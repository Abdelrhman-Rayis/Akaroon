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
    <div class="owt7_library_free_vs_pro">
        <header class="header">
            <h1><?php esc_html_e("Library Management System", "library-management-system"); ?><sup class="premium"><?php esc_html_e("Free Vs Pro", "library-management-system"); ?></sup><sup>v<?php echo esc_html(LIBMNS_VERSION); ?></sup></h1>
        </header>

        <section class="introduction">
            <h2><?php esc_html_e("What is LMS Plugin?", "library-management-system"); ?></h2>
            <p>
                <?php esc_html_e("LMS is a WordPress plugin which is designed to streamline and enhance the management of libraries, offering an intuitive and powerful solution for organizing books, users, bookcases, transactions, etc. Whether you are running a small community library or a large institutional library, our plugin provides the tools you need to operate efficiently and effectively.", "library-management-system"); ?>
            </p>
        </section>

        <section class="introduction">
            <h2><?php esc_html_e("Features Comparison", "library-management-system"); ?></h2>
            <div class="comparison-table">
                <div class="comparison-column">
                    <h2 class="comparison-title"><?php esc_html_e("Free Version", "library-management-system"); ?></h2>
                    <ul class="feature-list">
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage Categories (Up to ".(base64_decode(LIBMNS_FREE_VERSION_LIMIT) - 10).")", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage Bookcases (Up to ".(base64_decode(LIBMNS_FREE_VERSION_LIMIT) - 10).")", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage Sections", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage Books (Up to ".base64_decode(LIBMNS_FREE_VERSION_LIMIT).")", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage User Branches (Up to ".(base64_decode(LIBMNS_FREE_VERSION_LIMIT) - 10).")", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage LMS Users (Up to ".base64_decode(LIBMNS_FREE_VERSION_LIMIT).")", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Borrow a Single Book", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Return Books", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Track Book Transactions and History", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Manage User Late Fines", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Test Data Import Tool", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Generate Reports (PDF / Print / CSV) with Filters", "library-management-system"); ?></li>
                        <li><span class="icon">✔️</span> <?php esc_html_e("Multilingual Support (English / Hindi)", "library-management-system"); ?></li>
                    </ul>
                </div>
                <div class="comparison-column">
                    <h2 class="comparison-title"><?php esc_html_e("Pro Version (Basic)", "library-management-system"); ?></h2>
                    <ul class="feature-list">
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited Categories", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited Bookcases", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited Sections", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited Books", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited User Branches", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage Unlimited LMS Users", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Borrow Multiple Books", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Return Multiple Books", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Track Book Transactions and History", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Manage User Late Fines", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Test Data Import Tool", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Generate Reports (PDF / Print / CSV) with Filters", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Multilingual Support (5 Languages: English, Hindi, Turkish, Vietnamese, Italian)", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Data Backup Tool (Export and Import)", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("CSV Data Import", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Book Listing with Filters (via Shortcode)", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Public LMS Page with Data Filters (Category, Book Availability)", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("List of Books with Pagination Links", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Single Book Page View", "library-management-system"); ?></li>
                        <li><span class="icon">⭐</span> <?php esc_html_e("Customizable Settings (Borrow Days, Fines, Country, Currency, Data Backup, Shortcode)", "library-management-system"); ?></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="cta">
            <h2><?php esc_html_e("Get Started with Our Basic LMS Pro Plugin", "library-management-system"); ?></h2>
            <p><?php esc_html_e("Experience the future of library management today. Download our LMS plugin and transform the way you manage your library.", "library-management-system"); ?></p>
            <a href="<?php echo esc_url(LIBMNS_BUY_PRO_VERSION_LINK); ?>" target="_blank" class="cta-btn"><?php esc_html_e("Buy Basic Premium Version", "library-management-system"); ?> <?php echo esc_html(LIBMNS_VERSION); ?></a>
        </section>
    </div>
</div>
