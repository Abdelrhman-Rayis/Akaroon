<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms">
    <div class="owt7_lms_front_books">
        <div class="filter-bar">
            <h2 class="book-list-heading"><?php _e('Book List', 'library-management-system') ?></h2>
        </div>
        <div id="owt7_lms_books">
            <?php
            ob_start();
            // Template Variables
            $template_file = "owt7_library_all_books";
            include_once LIBMNS_PLUGIN_DIR_PATH . "public/views/templates/{$template_file}.php";
            $template = ob_get_contents();
            ob_end_clean();
            echo $template;
            ?>
        </div>
    </div>
</div>