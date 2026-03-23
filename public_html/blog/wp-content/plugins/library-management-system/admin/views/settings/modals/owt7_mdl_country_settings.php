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
        <h2><?php esc_html_e('Country & Currency Settings', 'library-management-system'); ?></h2>
        <form class="owt7_lms_form_settings" id="owt7_lms_data_settings" method="post" action="javascript:void(0);">
            <?php wp_nonce_field('owt7_library_actions', 'owt7_lms_nonce'); ?>
            <input type="hidden" name="owt7_lms_settings_type" value="country_currency">
            <div class="form-group">
                <label for="owt7_lms_country"><?php esc_html_e('Country', 'library-management-system'); ?> <span class="required">*</span></label>
                <input value="<?php echo esc_attr(get_option('owt7_lms_country')); ?>" type="text" id="owt7_lms_country" name="owt7_lms_country" class="form-control" placeholder="..." required>
            </div>
            <div class="form-group">
                <label for="owt7_lms_currency"><?php esc_html_e('Currency', 'library-management-system'); ?> <span class="required">*</span></label>
                <input value="<?php echo esc_attr(get_option('owt7_lms_currency')); ?>" type="text" id="owt7_lms_currency" name="owt7_lms_currency" class="form-control" placeholder="..." required>
            </div>
            <button type="submit" class="btn"><?php esc_html_e('Submit & Save', 'library-management-system'); ?></button>
        </form>
    </div>
</div>
