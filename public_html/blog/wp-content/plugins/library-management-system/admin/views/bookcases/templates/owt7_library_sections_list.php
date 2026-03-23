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

if (!empty($params['sections']) && is_array($params['sections'])) {
    foreach ($params['sections'] as $section) {

        // Clean up slashes from text fields
        $section_name     = esc_html(preg_replace("/\\\\+'/", "'", $section->name));
        $bookcase_name    = esc_html(preg_replace("/\\\\+'/", "'", $section->bookcase_name));
        $created_at       = esc_html($section->created_at);
        $encoded_id       = esc_attr(base64_encode($section->id));
        ?>
        <tr>
            <td>
                <input type="checkbox" name="owt7_lms_chk_btn[]" 
                       data-id="<?php echo esc_attr($section->id); ?>" 
                       class="owt7_lms_chkbox">
            </td>
            <td><?php echo ucwords($bookcase_name); ?></td>
            <td><?php echo ucwords($section_name); ?></td>
            <td>
                <?php if ($section->status) { ?>
                    <a href="javascript:void(0);" class="action-btn view-btn">
                        <?php _e("Active", "library-management-system"); ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0);" class="action-btn delete-btn">
                        <?php _e("Inactive", "library-management-system"); ?>
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $created_at; ?></td>
            <td>
                <a href="javascript:void(0)" 
                   data-module="sections" 
                   data-id="<?php echo $encoded_id; ?>"
                   title="<?php _e('Clone', 'library-management-system'); ?>" 
                   class="action-btn clone-btn owt7_lms_clone_data">
                    <span class="dashicons dashicons-image-rotate-right"></span>
                </a>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=add&opt=view&id=<?php echo $encoded_id; ?>"
                   title="<?php _e('View', 'library-management-system'); ?>" 
                   class="action-btn view-btn">
                    
                </a>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=add&opt=edit&id=<?php echo $encoded_id; ?>"
                   title="<?php _e('Edit', 'library-management-system'); ?>" 
                   class="action-btn edit-btn">
                    
                </a>
                <a href="javascript:void(0);" 
                   title="<?php _e('Delete', 'library-management-system'); ?>" 
                   class="action-btn delete-btn action-btn-delete"
                   data-id="<?php echo $encoded_id; ?>"
                   data-module="<?php echo esc_attr(base64_encode('section')); ?>">
                    
                </a>
            </td>
        </tr>
        <?php
    }
}
?>
