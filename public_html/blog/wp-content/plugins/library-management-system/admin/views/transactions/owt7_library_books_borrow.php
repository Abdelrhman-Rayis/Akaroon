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

    <div class="lms-borrow-books">

        <div class="page-header">
            <div class="breadcrumb"> <?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("Borrow a Book", "library-management-system"); ?></span> </div>
            <div class="page-actions">
            <?php 
                // Generate the nonce for the actions
                $page_nonce = wp_create_nonce('owt7_manage_transactions_page_nonce');
            ?>
                <a href="admin.php?page=owt7_library_transactions" class="btn"><span class="dashicons dashicons-clock"></span> <?php esc_html_e("Book(s) Borrow History", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-undo"></span> <?php esc_html_e("Book(s) Return", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return-history&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-backup"></span> <?php esc_html_e("Book(s) Return History", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php esc_html_e("Borrow a Book", "library-management-system"); ?></h2>
            </div>

            <form class="owt7_lms_borrow_book" id="owt7_lms_borrow_book" action="javascript:void(0);" method="post">

                <?php wp_nonce_field('owt7_library_actions', 'owt7_lms_nonce'); ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone"><?php esc_html_e("Borrow Date", "library-management-system"); ?></label>
                        <input type="text" id="owt7_txt_borrow_date" name="owt7_txt_borrow_date"
                            value="<?php echo esc_attr(date('Y-m-d')); ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="owt7_dd_branch_id"><?php esc_html_e("Branch", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_branch_id" name="owt7_dd_branch_id">
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
                    </div>
                    <div class="form-group">
                        <label for="owt7_dd_u_id"><?php esc_html_e("User", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_u_id" name="owt7_dd_u_id">
                            <option value=""><?php esc_html_e("-- Select User --", "library-management-system"); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="owt7_dd_category_id"><?php esc_html_e("Category", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_category_id" name="owt7_dd_category_id">
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
                    <div class="form-group">
                        <label for="owt7_dd_book_id"><?php esc_html_e("Book", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_book_id" name="owt7_dd_book_id">
                            <option value=""><?php esc_html_e("-- Select Book --", "library-management-system"); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="owt7_dd_days"><?php esc_html_e("Days", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_days" name="owt7_dd_days">
                            <option value=""><?php esc_html_e("-- Select Days --", "library-management-system"); ?></option>
                            <option value="7">7 <?php esc_html_e("Days", "library-management-system"); ?></option>
                            <option value="15">15 <?php esc_html_e("Days", "library-management-system"); ?></option>
                            <option value="30">30 <?php esc_html_e("Days", "library-management-system"); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-row buttons-group">
                    <button class="btn submit-save-btn" type="submit"><?php esc_html_e("Submit & Save", "library-management-system"); ?></button>
                </div>
            </form>

        </div>
    </div>

</div>
