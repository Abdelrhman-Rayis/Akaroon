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
            <div class="breadcrumb"> <?php _e('Library System', 'library-management-system'); ?> >> <span
                    class="active"><?php _e('LMS Shortcode(s)', 'library-management-system'); ?></span> </div>
            <div class="page-actions">
                <a href="admin.php?page=owt7_library_settings" class="btn">
                    <span class="dashicons dashicons-arrow-left-alt"></span> 
                    <?php _e('Back', 'library-management-system'); ?>
                </a>
            </div>
        </div>

        <div class="page-header">
            <div class="breadcrumb">
                <strong>
                    <?php _e('Books Library Page', 'library-management-system'); ?>:
                </strong> 
                <a href="<?php echo home_url('wp-library-books') ?>" target="_blank">
                    <?php echo home_url('wp-library-books') ?> 
                    <span class="dashicons dashicons-external"></span>
                </a>
                <br>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e('LMS Available Shortcode(s)', 'library-management-system'); ?></h2>
            </div>

            <table class="owt7-lms-table">
                <thead>
                    <tr>
                        <th><?php _e('Shortcode', 'library-management-system'); ?></th>
                        <th><?php _e('Description', 'library-management-system'); ?></th>
                        <th><?php _e('Copy', 'library-management-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" value="[owt7_library_books]" readonly>
                        </td>
                        <td>
                            <?php _e('This shortcode shows all books fed into Library System.', 'library-management-system'); ?>
                        </td>
                        <td>
                            <a href="javascript:void(0)" title="<?php _e('Copy', 'library-management-system'); ?>"
                                class="action-btn view-btn" id="owt7_lib_shortcode_copy"
                                data-value="[owt7_library_books]">
                                <span class="dashicons dashicons-admin-page"></span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>