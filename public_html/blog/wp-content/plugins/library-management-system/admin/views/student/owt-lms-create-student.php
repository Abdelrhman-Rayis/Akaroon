<div class="container wpowt-lib-cont">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage-students">Student List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php
                        if (isset($params['action']) && $params['action'] == "edit") {
                            echo 'Edit Student';
                        } elseif (isset($params['action']) && $params['action'] == "view") {
                            echo 'View Student';
                        } else {
                            echo 'Create Stduent';
                        }
                        ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/common.php'; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                Create Student
                <a href="admin.php?page=owt-lib-manage-students" class="btn btn-danger pull-right owt-btn-right owt-btn-top wpowt-lib-btn"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a>
            </div>
            <div class="panel-body">
                <?php
                if ($params['total_students'] >= 2 && isset($params['action']) && $params['action'] == "add") {
                    ?>
                    <div class="alert alert-danger">
                        <b>You have no more credits left to Add Student</b>
                    </div>
                    <?php
                } else {
                    ?>
                    <form action="javascript:void(0)" id="wpowt-lib-frm-create-new-student" method="post">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="txtregid">Registration ID*</label>
                                <input type="text" required="" <?php
                                if (isset($params['action']) && $params['action'] == "edit") {
                                    echo 'readonly';
                                } else {
                                    
                                }
                                ?>  value="<?php echo isset($params['student_data']['student_id']) ? esc_attr($params['student_data']['student_id']) : ""; ?>" class="form-control" id="txt-reg-id" name="txt_reg_id" placeholder="Enter registration ID">
                            </div>
                            <div class="form-group">
                                <label for="txtname">Name*</label>
                                <input type="text" required class="form-control" id="txt-name" value="<?php echo isset($params['student_data']['name']) ? esc_attr($params['student_data']['name']) : ""; ?>" name="txt_name" placeholder="Enter name">
                            </div>
                            <div class="form-group">
                                <label for="dd-branch">Class/Branch*</label>
                                <select class="form-control" id="dd-branch" name="txt_branch">
                                    <option value="-1">Choose Branch</option>
                                    <?php
                                    if (isset($params['data'])) {

                                        if (count($params['data']) > 0) {

                                            $saved_branch = isset($params['student_data']['branch_id']) ? esc_attr($params['student_data']['branch_id']) : "";

                                            foreach ($params['data'] as $branch) {

                                                $selected = '';
                                                if ($branch->id == $saved_branch) {
                                                    $selected = 'selected="selected"';
                                                }
                                                ?>
                                                <option value="<?php echo $branch->id ?>" <?php echo $selected; ?>><?php echo $branch->name ?></option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="txtemail">Email</label>
                                <input type="email" class="form-control" value="<?php echo isset($params['student_data']['email']) ? esc_attr($params['student_data']['email']) : ""; ?>" id="txt-email" name="txt_email" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="txtphone">Phone*</label>
                                <input type="text" required="" class="form-control" id="txt-phone" value="<?php echo isset($params['student_data']['phone_no']) ? esc_attr($params['student_data']['phone_no']) : ""; ?>" name="txt_phone" placeholder="Enter phone">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="txt-address-info">Address Info*</label>
                                <textarea class="form-control" required rows="5" id="txt-address-info" name='txt_address_info' placeholder="Address information"><?php echo isset($params['student_data']['address_info']) ? esc_attr($params['student_data']['address_info']) : ""; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="txtcity">City</label>
                                <input type="text" class="form-control" value="<?php echo isset($params['student_data']['city']) ? esc_attr($params['student_data']['city']) : ""; ?>" id="txt-city" name="txt_city" placeholder="Enter city">
                            </div>

                            <div class="form-group">
                                <label for="dd-state">State</label>
                                <input type="text" name="txt_st_state" value="<?php echo isset($params['student_data']['state']) ? esc_attr($params['student_data']['state']) : ""; ?>" placeholder="Enter State" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label for="dd-counrty">Country*</label>
                                <select class="form-control" id="dd-counrty" name="txt_country">
                                    <option value="-1">Select country</option>
                                    <?php
                                    if (isset($params['countries'])) {

                                        if (count($params['countries']) > 0) {

                                            $saved_country = isset($params['student_data']['country_id']) ? esc_attr($params['student_data']['country_id']) : "";

                                            foreach ($params['countries'] as $country) {
                                                $selected = '';
                                                if ($country->id == $saved_country) {
                                                    $selected = 'selected="selected"';
                                                }
                                                ?>
                                                <option value="<?php echo $country->id ?>" <?php echo $selected; ?>><?php echo $country->name ?></option>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="txtfathername">Father's name*</label>
                                <input type="text" class="form-control" required="" value="<?php echo isset($params['student_data']['father_name']) ? esc_attr($params['student_data']['father_name']) : ""; ?>" id="txt-father-name" name="txt_father_name" placeholder="Enter father name">
                            </div>
                            <div class="form-group">
                                <label for="txtmothername">Mother's name*</label>
                                <input type="text" class="form-control" required id="txt-mother-name" value="<?php echo isset($params['student_data']['mother_name']) ? esc_attr($params['student_data']['mother_name']) : ""; ?>" name="txt_mother_name" placeholder="Enter mother name">
                            </div>
                            <div class="form-group">
                                <label for="txtphone">Parent's Phone</label>
                                <input type="number" min="1" class="form-control" id="txt-parent-phone" name="txt_parent_phone" value="<?php echo isset($params['student_data']['parent_phone_no']) ? esc_attr($params['student_data']['parent_phone_no']) : ""; ?>" placeholder="Enter parent phone">
                            </div>
                            <div class="form-group">
                                <label for="txtimage">Student Profile Image</label>
                                <?php if (isset($params['action']) && $params['action'] != "view") { ?>
                                    <button id="btnUploadImage" type="button" class="btn btn-primary button-large">Upload Image</button>
                                <?php } ?>

                                <img src="<?php echo!empty($params['student_data']['profile_image']) ? esc_attr($params['student_data']['profile_image']) : OWT_LIBRARY_PLUGIN_URL . 'assets/images/blank-user.png'; ?>" class="wpowt-lib-img-prev"/>
                                <input type="hidden" name="stu_profile_image" value="<?php echo isset($params['student_data']['profile_image']) ? esc_attr($params['student_data']['profile_image']) : ""; ?>" id="stu_profile_image"/>
                            </div>
                        </div>
                        <?php if (isset($params['action']) && $params['action'] != "view") { ?>
                            <div class="form-group"> 
                                <input type="hidden" name="opt_action" value="<?php echo $params['action']; ?>"/>
                                <div class="col-sm-12 owt-text-align">
                                    <button type="submit" class="btn btn-success wpowt-lib-btn"><i class="mdi mdi-check-outline"></i> Submit</button>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                    <?php
                }
                ?>


            </div>

        </div>

    </div>
</div>
