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
    <div class="page-header">
        <div class="breadcrumb">
            <?php esc_html_e('Library System', 'library-management-system'); ?> >>
            <span class="active">
                <?php esc_html_e('Settings', 'library-management-system'); ?>
            </span>
        </div>
        <div class="page-actions">
            <?php
            $alreadyAvailable = get_option('owt7_lms_test_data');
            if (!empty($alreadyAvailable)) {
                ?>
                <a href="javascript:void(0)" id="owt7_lms_run_data_importer" class="btn input-mask-opacity">
                    <span class="dashicons dashicons-backup"></span>
                    <?php esc_html_e("Run Test Data Importer", "library-management-system"); ?>
                </a>
                <a href="javascript:void(0)" id="owt7_lms_refresh_test_data"><?php esc_html_e("Refresh it?", "library-management-system"); ?></a>
                <a href="javascript:void(0)" id="owt7_lms_remove_test_data"><?php esc_html_e("Remove Test Data?", "library-management-system"); ?></a>
                <?php
            } else {
                ?>
                <a href="javascript:void(0)" id="owt7_lms_run_data_importer" class="btn">
                    <span class="dashicons dashicons-backup"></span>
                    <?php esc_html_e("Run Test Data Importer", "library-management-system"); ?>
                </a>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="page-container lms-settings">

        <div class="lms-dashboard card-container">
            <!-- Card 1 -->
            <div class="card">
                <span class="dashicons dashicons-admin-site"></span>
                <h2><?php esc_html_e('Set Country & Currency', 'library-management-system'); ?></h2>
                <a href="admin.php?page=owt7_library_settings&mod=country" class="owt7-lms-anc-settings"><?php esc_html_e('Configure Settings', 'library-management-system'); ?></a>
            </div>
            <!-- Card 2 -->
            <div class="card">
                <span class="dashicons dashicons-tag"></span>
                <h2><?php esc_html_e('Set Late Fine', 'library-management-system'); ?></h2>
                <a href="admin.php?page=owt7_library_settings&mod=late_fine" class="owt7-lms-anc-settings"><?php esc_html_e('View Settings', 'library-management-system'); ?></a>
            </div>
            <!-- Card 3 -->
            <div class="card">
                <span class="dashicons dashicons-shortcode"></span>
                <h2><?php _e('Shortcode(s)', 'library-management-system'); ?></h2>
                <a href="admin.php?page=owt7_library_settings&mod=shortcodes"
                    class="owt7-lms-anc-settings"><?php _e('View Settings', 'library-management-system'); ?></a>
            </div>
        </div>
    </div>
</div>
