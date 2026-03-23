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
    <div class="error-400-container">
        <h1><?php esc_html_e('404 - Not Found', 'library-management-system'); ?></h1>
        <p><?php esc_html_e('The requested book ID was not found.', 'library-management-system'); ?></p>
        <a href="<?php echo home_url('owt7-library-books'); ?>" class="btn"><?php esc_html_e('Go to Library', 'library-management-system'); ?></a>
    </div>
</div>