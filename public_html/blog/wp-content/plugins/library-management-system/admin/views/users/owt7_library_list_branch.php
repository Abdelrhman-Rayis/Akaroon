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
    <div class="lms-list-branch">
        <div class="page-header">
            <div class="breadcrumb"> 
                <?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("List Branch", "library-management-system"); ?></span> 
            </div>
            <div class="page-actions">
                <?php 
                    // Generate the nonce for the actions
                    $page_nonce = wp_create_nonce('owt7_manage_users_page_nonce');
                ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("Add User Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("Add New User", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users" class="btn"><?php esc_html_e("List User", "library-management-system"); ?></a>
            </div>
        </div>
        <div class="page-container">
            <div class="page-title">
                <h2><?php esc_html_e("Branch List", "library-management-system"); ?></h2>
            </div>
            <table class="owt7-lms-table" id="tbl_branches_list">
                <thead>
                    <tr>
                        <th><?php esc_html_e("S No", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Name", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Total User(s)", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Status", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Created at", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(!empty($params['branches']) && is_array($params['branches'])){
                            foreach($params['branches'] as $branch){
                                ?>
                                <tr>
                                    <td><?php echo esc_html($branch->id); ?></td>
                                    <td><?php echo esc_html(ucwords($branch->name)); ?></td>
                                    <td><?php echo esc_html($branch->total_users); ?></td>
                                    <td>
                                        <?php if($branch->status){ ?>
                                            <a href="javascript:void(0);" class="action-btn view-btn">
                                                <?php esc_html_e("Active", "library-management-system"); ?>
                                            </a>
                                        <?php } else { ?>
                                            <a href="javascript:void(0);" class="action-btn delete-btn">
                                                <?php esc_html_e("Inactive", "library-management-system"); ?>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo esc_html($branch->created_at); ?></td>
                                    <td>
                                        <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&opt=view&id=<?php echo esc_attr(base64_encode($branch->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>"
                                            title="<?php esc_html_e("View", "library-management-system"); ?>" class="action-btn view-btn">
                                            
                                        </a>
                                        <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&opt=edit&id=<?php echo esc_attr(base64_encode($branch->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>"
                                            title="<?php esc_html_e("Edit", "library-management-system"); ?>" class="action-btn edit-btn">
                                            
                                        </a>
                                        <a href="javascript:void(0);" title="<?php esc_html_e("Delete", "library-management-system"); ?>" class="action-btn delete-btn action-btn-delete"
                                            data-id="<?php echo esc_attr(base64_encode($branch->id)); ?>"
                                            data-module="<?php echo esc_attr(base64_encode('branch')); ?>">
                                            
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
