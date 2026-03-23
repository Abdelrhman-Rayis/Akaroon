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

if(!empty($params['users']) && is_array($params['users'])){
    foreach($params['users'] as $user){
        ?>
        <tr> 
            <td><?php echo esc_html($user->u_id); ?></td>
            <td><?php echo esc_html(ucwords($user->name)); ?></td>
            <td><?php echo !empty($user->email) ? esc_html($user->email) : esc_html__("N/A", "library-management-system"); ?></td>
            <td><?php echo !empty($user->branch_name) ? esc_html($user->branch_name) : esc_html__("N/A", "library-management-system"); ?></td>
            <td>
                <?php if($user->status){ ?>
                <a href="javascript:void(0);" class="action-btn view-btn">
                    <?php esc_html_e("Active", "library-management-system"); ?>
                </a>
                <?php }else{ ?>
                <a href="javascript:void(0);" class="action-btn delete-btn">
                    <?php esc_html_e("Inactive", "library-management-system"); ?>
                </a>
                <?php } ?>
            </td>
            <td><?php echo esc_html($user->created_at); ?></td>
            <td>
                <?php 
                    // Generate the nonce for the actions
                    $page_nonce = wp_create_nonce('owt7_manage_users_page_nonce');
                ?>

                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&opt=view&id=<?php echo esc_attr(base64_encode($user->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>" 
                title="<?php esc_attr_e("View", "library-management-system"); ?>" 
                class="action-btn view-btn">
                    
                </a>

                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&opt=edit&id=<?php echo esc_attr(base64_encode($user->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>" 
                title="<?php esc_attr_e("Edit", "library-management-system"); ?>" 
                class="action-btn edit-btn">
                    
                </a>

                <a href="javascript:void(0);" title="<?php esc_attr_e("Delete", "library-management-system"); ?>" class="action-btn delete-btn action-btn-delete" data-id="<?php echo esc_attr(base64_encode($user->id)); ?>" data-module="<?php echo esc_attr(base64_encode('user')); ?>">
                    
                </a>
            </td>
        </tr>
        <?php
    }
}
?>
