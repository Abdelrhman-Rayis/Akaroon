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
<!-- The Modal -->
<div id="owt7_lms_mdl_settings" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>
            <?php if (!empty(get_option('owt7_lms_late_fine_currency'))) { ?>
                <?php esc_html_e('Update Late Fine', 'library-management-system'); ?>
            <?php } else { ?>
                <?php esc_html_e('Set Late Fine', 'library-management-system'); ?>
            <?php } ?>
        </h2>
        <form class="owt7_lms_form_settings" id="owt7_lms_data_settings" method="post" action="javascript:void(0);">
            <?php wp_nonce_field('owt7_library_actions', 'owt7_lms_nonce'); ?>
            <input type="hidden" name="owt7_lms_settings_type" value="late_fine">
            <div class="form-group">
                <label for="owt7_lms_fine_amount"><?php esc_html_e('Fine Amount:', 'library-management-system'); ?> <span class="required">*</span></label>
                <input value="<?php echo esc_attr(get_option('owt7_lms_late_fine_currency')); ?>" type="text" id="owt7_lms_fine_amount" name="owt7_lms_fine_amount" class="form-control" placeholder="..." required>
            </div>
            <button type="submit" class="btn"><?php esc_html_e('Submit & Save', 'library-management-system'); ?></button>
        </form>
    </div>
</div>
