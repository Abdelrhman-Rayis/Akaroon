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

    <div class="lms-add-branch">

        <div class="page-header">
            <div class="breadcrumb"> <?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("Add New Branch", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <?php 
                    // Generate the nonce for the actions
                    $page_nonce = wp_create_nonce('owt7_manage_users_page_nonce');
                ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=list&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("List Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("Add New User", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users" class="btn"><?php esc_html_e("List User", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <?php if(isset($params['action'])){ ?> <h2><?php echo esc_html(ucfirst($params['action'])); ?> <?php esc_html_e("Branch", "library-management-system"); ?></h2> <?php }else{ ?> <h2><?php esc_html_e("Add Branch", "library-management-system"); ?></h2> <?php } ?> 
            </div>

            <form class="owt7_lms_branch_form" id="owt7_lms_branch_form" action="javascript:void(0);" method="post">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type" value="<?php echo esc_attr(isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'); ?>">
                <?php 
                if(isset($params['action']) && $params['action'] == 'edit'){ 
                    ?>
                    <div class="form-row buttons-group">
                    <input type="hidden" name="edit_id" value="<?php echo esc_attr(isset($params['branch']['id']) ? $params['branch']['id'] : ''); ?>">
                    </div>
                    <?php
                } 
                ?>

                <div class="form-row">
                    <!-- Branch name -->
                    <div class="form-group">
                        <label for="owt7_txt_branch_name"><?php esc_html_e("Branch Name", "library-management-system"); ?> <span class="required">*</span></label>
                        <input value="<?php echo esc_attr(isset($params['branch']['name']) ? $params['branch']['name'] : ''); ?>" <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> type="text" id="owt7_txt_branch_name" required name="owt7_txt_branch_name"
                        placeholder="...">
                    </div>
                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_branch_status"><?php esc_html_e("Status", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> required id="owt7_dd_branch_status" name="owt7_dd_branch_status" required>
                            <option value="">-- <?php esc_html_e("Select Status", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['branch']['status']) && $params['branch']['status'] == $key){
                                        $selected = "selected";
                                    }
                                    ?>
                                        <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_html(ucfirst($status)); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                </div>

                <?php if(isset($params['action']) && $params['action'] == 'view'){ }else{ ?>                
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php esc_html_e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                <?php } ?>

            </form>

        </div>
    </div>

</div>
