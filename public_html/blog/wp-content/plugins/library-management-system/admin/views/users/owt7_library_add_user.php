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
    <div class="lms-add-user">
        <div class="page-header">
            <div class="breadcrumb"><?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("Add New User", "library-management-system"); ?></span></div>
            <div class="page-actions">
                <?php 
                    // Generate the nonce for the actions
                    $page_nonce = wp_create_nonce('owt7_manage_users_page_nonce');
                ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("Add New Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=list&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><?php esc_html_e("List Branch", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_users" class="btn"><?php esc_html_e("List User", "library-management-system"); ?></a>
            </div>
        </div>
        <div class="page-container">
            <div class="page-title">
                <?php if(isset($params['action'])){ ?> <h2><?php echo esc_html(ucfirst($params['action'])); ?> <?php esc_html_e("User", "library-management-system"); ?></h2> <?php }else{ ?> <h2><?php esc_html_e("Add User", "library-management-system"); ?></h2> <?php } ?> 
            </div>
            <form class="owt7_lms_user_form" id="owt7_lms_user_form" action="javascript:void(0)" method="post">
                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type" value="<?php echo esc_attr(isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'); ?>">
                <?php if(isset($params['action']) && $params['action'] == 'edit'){ ?>
                <div class="form-row buttons-group">
                    <input type="hidden" name="edit_id" value="<?php echo esc_attr(isset($params['user']['id']) ? $params['user']['id'] : ''); ?>">
                </div>
                <?php } ?>                
                <div class="form-row">
                    <!-- User ID -->
                    <div class="form-group">
                        <label for="user-id"><?php esc_html_e("User / Registration ID", "library-management-system"); ?> <span class="required">*</span>  
                            <?php if(isset($params['action']) && in_array($params['action'], ["view", "edit"])){}else{ ?>
                                <a href="javascript:void(0);" data-module="user" id="owt7_btn_ids_auto_generate"><u><?php esc_html_e('Auto-generate', 'library-management-system') ?></u>?</a>
                            <?php } ?>
                        </label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo esc_attr(isset($params['user']['u_id']) ? $params['user']['u_id'] : ''); ?>" required type="text" id="owt7_txt_u_id" name="owt7_txt_u_id" <?php if(isset($params['action']) && in_array($params['action'], ["view", "edit"])){ ?>readonly<?php }else{} ?> placeholder="...">
                    </div>
                    <!-- Branch -->
                    <div class="form-group">
                        <label for="branch"><?php esc_html_e("Select Branch", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> required id="owt7_dd_branch_id" name="owt7_dd_branch_id">
                            <option value="">-- <?php esc_html_e("Select Branch", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['branches']) && is_array($params['branches'])){
                                foreach($params['branches'] as $branch){
                                    $selected = "";
                                    if(isset($params['user']['branch_id']) && $params['user']['branch_id'] == $branch->id){
                                        $selected = "selected";
                                    }
                                    ?>
                                    <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr(ucwords($branch->id)); ?>"><?php echo esc_html(ucwords($branch->name)); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="owt7_txt_name"><?php esc_html_e("Name", "library-management-system"); ?> <span class="required">*</span></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo esc_attr(isset($params['user']['name']) ? $params['user']['name'] : ''); ?>" required type="text" id="owt7_txt_name" name="owt7_txt_name" placeholder="..." />
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label for="owt7_txt_email"><?php esc_html_e("Email", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo esc_attr(isset($params['user']['email']) ? $params['user']['email'] : ''); ?>" type="email" id="owt7_txt_email" name="owt7_txt_email" placeholder="...">
                    </div>
                </div>
                <div class="form-row">
                    <!-- Phone number -->
                    <div class="form-group">
                        <label for="owt7_txt_phone"><?php esc_html_e("Phone Number", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo esc_attr(isset($params['user']['phone_no']) ? $params['user']['phone_no'] : ''); ?>" type="text" id="owt7_txt_phone" name="owt7_txt_phone" placeholder="...">
                    </div>
                    <!-- Gender -->
                    <div class="form-group">
                        <label for="owt7_dd_gender"><?php esc_html_e("Gender", "library-management-system"); ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_gender" name="owt7_dd_gender">
                            <option value="">-- <?php esc_html_e("Select Gender", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['genders']) && is_array($params['genders'])){
                                foreach($params['genders'] as $gender){
                                    $selected = "";
                                    if(isset($params['user']['gender']) && $params['user']['gender'] == $gender){
                                        $selected = "selected";
                                    }
                                    ?>
                                        <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($gender); ?>"><?php echo esc_html(ucfirst($gender)); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Address -->
                    <div class="form-group">
                        <label for="owt7_txt_address"><?php esc_html_e("Address", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo esc_attr(isset($params['user']['address_info']) ? $params['user']['address_info'] : ''); ?>" type="text" id="owt7_txt_address" name="owt7_txt_address" placeholder="...">
                    </div>
                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_user_status"><?php esc_html_e("Status", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_user_status" required name="owt7_dd_user_status">
                            <option value="">-- <?php esc_html_e("Select Status", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['user']['status']) && $params['user']['status'] == $key){
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
                <div class="form-row">
                    <!-- Profile Image -->
                    <div class="form-group">
                        <label for="owt7_profile_image"><?php esc_html_e("Profile Image", "library-management-system"); ?></label>
                        <?php if(isset($params['action']) && $params['action'] == 'view'){ }else{ ?>
                            <button id="owt7_upload_image" type="button" class="btn btn-primary button-large">
                                <?php esc_html_e("Upload Profile Image", "library-management-system"); ?>
                            </button>
                        <?php } ?>
                        <?php if(!empty($params['user']['profile_image'])){ ?>
                            <img src="<?php echo esc_url($params['user']['profile_image']); ?>" id="owt7_library_image_preview"/>
                        <?php }else{ ?>
                            <img src="<?php echo esc_url(LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'); ?>" id="owt7_library_image_preview"/>
                        <?php } ?>
                        <input type="hidden" value="<?php echo esc_url(isset($params['user']['profile_image']) ? $params['user']['profile_image'] : LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'); ?>" name="owt7_profile_image" id="owt7_image_url" />
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
