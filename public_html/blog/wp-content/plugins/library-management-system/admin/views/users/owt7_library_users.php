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
    <div class="lms-list-user">
        <div class="page-header">
            <div class="breadcrumb"> 
                <?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("List User", "library-management-system"); ?></span> 
            </div>
            <div class="page-actions">
                <?php 
                    // Generate the nonce for the actions
                    $page_nonce = wp_create_nonce('owt7_manage_users_page_nonce');
                ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-building"></span> <?php esc_html_e("Add User Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=list&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-list-view"></span> <?php esc_html_e("List Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-admin-users"></span> <?php esc_html_e("Add New User", "library-management-system"); ?></a>
            </div>
        </div>
        <div class="page-container">
            <div class="page-title">
                <h2><?php esc_html_e("User(s) List", "library-management-system"); ?></h2>
            </div>
            <div class="filter-container">
                <label for="owt7_lms_branch_filter"><?php esc_html_e("Filter by:", "library-management-system"); ?></label>
                <select data-module="users" data-filter-by="branch" id="owt7_lms_data_filter" class="owt7_lms_data_filter">
                    <option value=""><?php esc_html_e("-- Select Branch --", "library-management-system"); ?></option>
                    <option value="all"><?php esc_html_e("All", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['branches']) && is_array($params['branches'])){
                        foreach($params['branches'] as $branch){
                            ?>
                            <option value="<?php echo esc_attr($branch->id); ?>"><?php echo esc_html(ucfirst($branch->name)); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <table class="owt7-lms-table" id="tbl_users_list">
                <thead>
                    <tr>
                        <th><?php esc_html_e("User ID", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Name", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Email", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Branch", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Status", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Created at", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/users/templates/owt7_library_users_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
