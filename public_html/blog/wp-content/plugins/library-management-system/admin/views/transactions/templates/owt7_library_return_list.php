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

    if(!empty($params['returns']) && is_array($params['returns'])){
        foreach($params['returns'] as $return){
            ?>
    <tr>
        <td><?php echo esc_html($return->id); ?></td>
        <td>
            <strong><?php esc_html_e('Branch', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->branch_name); ?><br>
            <strong><?php esc_html_e('Name', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->user_name); ?><br>
        </td>
        <td>
            <strong><?php esc_html_e('Category', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->category_name); ?><br>
            <strong><?php esc_html_e('Name', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->book_name); ?><br>
        </td>
        <td>
            <strong><?php esc_html_e('Borrow ID', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->borrow_id); ?><br>
            <strong><?php esc_html_e('Total Days', 'library-management-system'); ?>:</strong> <?php echo esc_html($return->total_days); ?> <?php esc_html_e('Days', 'library-management-system'); ?><br>
            <strong><?php esc_html_e('Issued', 'library-management-system'); ?>:</strong> <?php echo esc_html(date("Y-m-d", strtotime($return->issued_on))); ?><br>
            <strong><?php esc_html_e('Returned', 'library-management-system'); ?>:</strong> <?php echo esc_html(date("Y-m-d", strtotime($return->created_at))); ?><br>
        </td>
        <td>
            <?php 
            if($return->status && $return->has_paid == 1){ 
            ?>
            <span class="owt7_late_fine_text"><?php esc_html_e("User has to <u>Pay Fine</u>", "library-management-system") ?></span>
            <span class="owt7_late_fine_text"><?php esc_html_e("Total Extra", "library-management-system") ?>: <u><?php echo esc_html($return->extra_days . " " . __("days", 'library-management-system')); ?></u></span>
            <span class="owt7_late_fine_text"><?php esc_html_e("Total Fine", "library-management-system") ?>: <u><?php echo esc_html($return->fine_amount) ." ". esc_html(get_option( 'owt7_lms_currency' )); ?></u></span>
            <?php }else{ ?>
                <span class="action-btn view-btn owt7_label_text">
                    <?php esc_html_e('No fine', 'library-management-system'); ?>
                </span>
            <?php } ?>
        </td>
        <td>
            <?php if($return->status && $return->has_paid == 1){ ?> 
                <a href="javascript:void(0);" title='<?php esc_attr_e("Pay Fine", "library-management-system"); ?>'
                    class="action-btn delete-btn owt7_pay_late_fine" data-id="<?php echo esc_attr(base64_encode($return->id)) ?>">
                    <span class="dashicons dashicons-info"></span> <?php esc_html_e('Pay Fine', 'library-management-system'); ?>
                </a>
            <?php }else{ ?>
                <a href="javascript:void(0);" title='<?php esc_attr_e("Delete", "library-management-system"); ?>'
                    class="action-btn delete-btn action-btn-delete" data-id="<?php echo esc_attr(base64_encode($return->id)) ?>"
                    data-module="<?php echo esc_attr(base64_encode('book_return')); ?>">
                    
                </a>
            <?php } ?>
        </td>
    </tr>
    <?php
        }
    } 
?>
