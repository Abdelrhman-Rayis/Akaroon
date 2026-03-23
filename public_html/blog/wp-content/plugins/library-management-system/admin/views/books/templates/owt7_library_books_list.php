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

if (!empty($params['books']) && is_array($params['books'])) {
    foreach ($params['books'] as $book) {
        $book_name = esc_html(preg_replace("/\\\\+'/", "'", $book->name ?? ''));
        $category_name = esc_html(preg_replace("/\\\\+'/", "'", $book->category_name ?? ''));
        $bookcase_name = esc_html(preg_replace("/\\\\+'/", "'", $book->bookcase_name ?? ''));
        $section_name = esc_html(preg_replace("/\\\\+'/", "'", $book->section_name ?? ''));
        ?>
        <tr>
            <td><?php echo esc_html($book->book_id); ?></td>
            <td>
                <strong><?php esc_html_e("Category", "library-management-system"); ?>:</strong> <span><?php echo $category_name; ?></span><br>
                <strong><?php esc_html_e("Bookcase", "library-management-system"); ?>:</strong> <span><?php echo $bookcase_name; ?></span><br>
                <strong><?php esc_html_e("Section", "library-management-system"); ?>:</strong> <span><?php echo $section_name; ?></span>
            </td>
            <td><?php echo ucwords($book_name); ?></td>
            <td><?php echo esc_html(intval($book->stock_quantity)); ?></td>
            <td>
                <?php if ($book->status) { ?>
                    <a href="javascript:void(0);" class="action-btn view-btn">
                        <?php esc_html_e("Active", "library-management-system"); ?>
                    </a>
                <?php } else { ?>
                    <a href="javascript:void(0);" class="action-btn delete-btn">
                        <?php esc_html_e("Inactive", "library-management-system"); ?>
                    </a>
                <?php } ?>
            </td>
            <?php 
                // Generate the nonce for the actions
                $page_nonce = wp_create_nonce('owt7_manage_books_page_nonce');
            ?>
            <td>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add&opt=view&id=<?php echo esc_attr(base64_encode($book->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>"
                   title="<?php esc_attr_e('View', 'library-management-system'); ?>" class="action-btn view-btn">
                    
                </a>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add&opt=edit&id=<?php echo esc_attr(base64_encode($book->id)); ?>&_wpnonce=<?php echo esc_attr($page_nonce); ?>"
                   title="<?php esc_attr_e('Edit', 'library-management-system'); ?>" class="action-btn edit-btn">
                    
                </a>
                <a href="javascript:void(0);" title="<?php esc_attr_e('Delete', 'library-management-system'); ?>"
                   class="action-btn delete-btn action-btn-delete" data-id="<?php echo esc_attr(base64_encode($book->id)); ?>"
                   data-module="<?php echo esc_attr(base64_encode('book')); ?>">
                    
                </a>
            </td>
        </tr>
        <?php
    }
}
?>
