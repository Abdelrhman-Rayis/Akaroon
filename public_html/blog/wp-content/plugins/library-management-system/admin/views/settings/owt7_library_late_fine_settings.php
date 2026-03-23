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

    <div class="owt7_lms_settings">

        <div class="page-header">
            <div class="breadcrumb">
                <?php esc_html_e('Library System', 'library-management-system'); ?> >> 
                <span class="active"><?php esc_html_e('Late Fine', 'library-management-system'); ?></span>
            </div>
            <div class="page-actions">
                <?php if (!empty(get_option('owt7_lms_late_fine_currency'))) { ?>
                    <a href="javascript:void(0)" id="owt7_lms_fine_modal" class="btn">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php esc_html_e('Update Late Fine', 'library-management-system'); ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0)" id="owt7_lms_fine_modal" class="btn">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php esc_html_e('Set Late Fine', 'library-management-system'); ?>
                    </a>
                <?php } ?>
                <a href="admin.php?page=owt7_library_settings" class="btn">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e('All Settings', 'library-management-system'); ?>
                </a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php esc_html_e('Late Fine', 'library-management-system'); ?></h2>
            </div>

            <table class="owt7-lms-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Fine Amount', 'library-management-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span class="owt7_lms_settings_values">
                                <?php 
                                    $late_fine = esc_html(get_option('owt7_lms_late_fine_currency'));
                                    echo !empty($late_fine) ? esc_html($late_fine) : '--'; 
                                ?>
                                <strong>
                                    <?php 
                                        $currency = esc_html(get_option('owt7_lms_currency'));
                                        if (!empty($currency)) {
                                            echo esc_html($currency);
                                        } else {
                                            echo !empty($late_fine) ? esc_html__('(Currency Unknown)', 'library-management-system') : '--'; 
                                        }
                                    ?>
                                </strong>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>


<div class="owt7_lms_modal_section">
    <?php
    ob_start();
    $fileName = "owt7_mdl_late_fine_settings";
    include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/settings/modals/{$fileName}.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
    ?>
</div>
