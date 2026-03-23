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

    <div class="lms-borrow-history">

        <div class="page-header">
            <div class="breadcrumb"> 
                <?php esc_html_e("Library System", "library-management-system"); ?>  >> 
                <span class="active"><?php esc_html_e("Book(s) Borrow History", "library-management-system"); ?></span> 
            </div>
            <?php 
                // Generate the nonce for the actions
                $page_nonce = wp_create_nonce('owt7_manage_transactions_page_nonce');
            ?>
            <div class="page-actions">
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=borrow&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-book-alt"></span> <?php esc_html_e("Borrow a Book", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-undo"></span> <?php esc_html_e("Book(s) Return", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return-history&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-backup"></span> <?php esc_html_e("Book(s) Return History", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php esc_html_e("Book(s) Borrow History", "library-management-system"); ?></h2>
            </div>

            <div class="filter-container">

                <label for="owt7_lms_filter"><?php esc_html_e("Filter by:", "library-management-system"); ?></label>

                <select data-list="borrow_history" data-table="owt7_lms_tbl_borrow_list" data-option="branch"
                    id="owt7_lms_dd_branch_filter" class="owt7_lms_dd_data_filter">
                    <option value=""><?php esc_html_e("-- Select Branch --", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['branches']) && is_array($params['branches'])){
                        foreach($params['branches'] as $key => $branch){
                            ?>
                    <option value="<?php echo esc_attr($branch->id); ?>"><?php echo esc_html(ucfirst($branch->name)); ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>

                <select data-list="borrow_history" data-table="owt7_lms_tbl_borrow_list" data-option="category"
                    id="owt7_lms_dd_category_filter" class="owt7_lms_dd_data_filter">
                    <option value=""><?php esc_html_e("-- Select Category --", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['categories']) && is_array($params['categories'])){
                        foreach($params['categories'] as $key => $category){
                            ?>
                    <option value="<?php echo esc_attr($category->id); ?>"><?php echo esc_html(ucfirst($category->name)); ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>

            </div>

            <table class="owt7-lms-table" id="tbl_books_borrow_history">
                <thead>
                    <tr>
                        <th><?php esc_html_e("Borrow ID", "library-management-system"); ?></th>
                        <th><?php esc_html_e("User Details", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Book Details", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Borrow Details (Y-m-d)", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Status", "library-management-system"); ?></th>
                        <th><?php esc_html_e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody id="owt7_lms_tbl_borrow_list">
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_borrow_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
