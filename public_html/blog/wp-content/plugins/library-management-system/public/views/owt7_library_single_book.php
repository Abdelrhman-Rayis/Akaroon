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
    <div class="owt7-lms-single-book">
        <a href="<?php echo home_url('wp-library-books'); ?>" class="owt7_lms_back_button"><< <?php esc_html_e('Back', 'library-management-system'); ?></a>
        <div class="book-details-container owt7_lms_single_book">
            <?php if(isset($params['book']->cover_image) && !empty($params['book']->cover_image)){ ?>
            <div class="book-cover">
                <img src="<?php echo $params['book']->cover_image; ?>" alt="<?php echo $params['book']->name; ?>">
            </div>
            <?php }else{ ?>
            <div class="book-cover">
                <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>" alt="<?php echo $params['book']->name; ?>">
            </div>
            <?php } ?>
            <div class="book-info">
                <h2 class="book-title"><?php echo $params['book']->name; ?></h2>
                <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo $params['book']->author_name; ?></p>
                <p class="book-status"><strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong> <?php echo $params['book']->status; ?></p>
                <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo $params['book']->category_name; ?></p>
                <p class="book-description">
                    <strong><?php esc_html_e('Description:', 'library-management-system'); ?></strong> <?php echo $params['book']->description; ?>
                </p>
            </div>
            
        </div>
    </div>
</div>