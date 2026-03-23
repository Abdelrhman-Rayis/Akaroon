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

    <div class="owt7_library_list_books">

        <div class="page-header">
            <div class="breadcrumb">
                <?php esc_html_e("Library System", "library-management-system"); ?> >>
                <span class="active"><?php esc_html_e("List Book", "library-management-system"); ?></span>
            </div>
            <div class="page-actions">
            <?php 
                // Generate the nonce for the actions
                $page_nonce = wp_create_nonce('owt7_manage_books_page_nonce');
            ?>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-plus-alt"></span> <?php esc_html_e("Add New Category", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=list&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-list-view"></span> <?php esc_html_e("List Category", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-book-alt"></span> <?php esc_html_e("Add New Book", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php esc_html_e("Book List", "library-management-system"); ?></h2>
            </div>

            <div class="filter-container">
                <label for="owt7_lms_category_filter"><?php esc_html_e("Filter by:", "library-management-system"); ?></label>
                <select data-module="books" data-filter-by="category" id="owt7_lms_data_filter" class="owt7_lms_data_filter">
                    <option value="all"><?php esc_html_e("All", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['categories']) && is_array($params['categories'])){
                        foreach($params['categories'] as $category){
                            ?>
                            <option value="<?php echo esc_attr($category->id); ?>"><?php echo esc_html(ucfirst($category->name)); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <table class="owt7-lms-table" id="tbl_books_list">
                <thead>
                    <tr>
                        <th><?php esc_html_e("Book ID", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Basic Details", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Name", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Stock Quantity", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Status", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/books/templates/owt7_library_books_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
