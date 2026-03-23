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

if (!empty($params['borrows']) && is_array($params['borrows'])) {
    foreach ($params['borrows'] as $borrow) {
        ?>
        <tr>
            <td><?php echo esc_html($borrow->borrow_id); ?></td>
            <td>
                <strong><?php esc_html_e('Branch', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->branch_name); ?><br>
                <strong><?php esc_html_e('Name', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->user_name); ?><br>
            </td>
            <td>
                <strong><?php esc_html_e('Category', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->category_name); ?><br>
                <strong><?php esc_html_e('Name', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->book_name); ?><br>
            </td>
            <td>
                <strong><?php esc_html_e('Days', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->borrows_days); ?> <?php esc_html_e('days', 'library-management-system'); ?><br>
                <strong><?php esc_html_e('Issued on', 'library-management-system'); ?>:</strong> <?php echo gmdate("Y-m-d", strtotime($borrow->created_at)); ?>
                <br>
                <strong><?php esc_html_e('Return by', 'library-management-system'); ?>:</strong> <?php echo esc_html($borrow->return_date); ?><br>
            </td>
            <td>
                <?php if ($borrow->status) { ?>
                    <span class="action-btn delete-btn owt7_label_text">
                        <?php esc_html_e('Return Pending', 'library-management-system'); ?>
                    </span>
                <?php } else { ?>
                    <span class="action-btn view-btn owt7_label_text">
                        <?php esc_html_e('Returned', 'library-management-system'); ?>
                    </span>
                <?php } ?>
            </td>
            <td>
                <?php if ($borrow->status) { ?>
                    <form action="javascript:void(0)" method="post" id="owt7_lms_return_book_<?php echo esc_attr($borrow->id); ?>">
                        <?php wp_nonce_field('owt7_library_actions', 'owt7_lms_nonce'); ?>
                        <input type="hidden" name="owt7_borrow_books_id[]" value="<?php echo esc_attr($borrow->id); ?>">
                    </form>
                    <a href="javascript:void(0)" data-id="<?php echo esc_attr($borrow->id); ?>"
                        class="action-btn edit-btn owt7_lms_btn_return <?php if (!$borrow->status) { echo 'input-mask-opacity'; } ?>">
                        <span class="dashicons dashicons-book"></span>
                        <?php esc_html_e('Return', 'library-management-system'); ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0);" title="<?php esc_attr_e('Delete', 'library-management-system'); ?>"
                        class="action-btn delete-btn action-btn-delete" data-id="<?php echo esc_attr(base64_encode($borrow->id)); ?>"
                        data-module="<?php echo esc_attr(base64_encode('book_borrow')); ?>">
                        
                    </a>
                <?php } ?>
            </td>
        </tr>
        <?php
    }
} 
?>
