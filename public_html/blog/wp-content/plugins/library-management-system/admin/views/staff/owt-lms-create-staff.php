<div class="container wpowt-lib-cont">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage-staffs">Staff List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php
                        if (isset($params['action']) && $params['action'] == "edit") {
                            echo 'Edit Staff';
                        } elseif (isset($params['action']) && $params['action'] == "view") {
                            echo 'View Staff';
                        } else {
                            echo 'Create Staff';
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
                Create Staff
                <a href="admin.php?page=owt-lib-manage-staffs" class="btn btn-danger pull-right owt-btn-right owt-btn-top wpowt-lib-btn"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a>
            </div>
            <div class="panel-body">
                <?php
                if ($params['total_staffs'] >= 2 && isset($params['action']) && $params['action'] == "add") {
                    ?>
                    <div class="alert alert-danger">
                        <b>You have no more credits left to Add Staff</b>
                    </div>
                    <?php
                } else {
                    ?>
                    <form action="javascript:void(0)" id="wpowt-lib-frm-create-new-staff" method="post">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="txtstaffid">Staff ID*</label>
                                <input type="text" <?php
                                if (isset($params['action']) && $params['action'] == "edit") {
                                    echo 'readonly';
                                } else {
                                    
                                }
                                ?> value="<?php echo isset($params['staff_data']['staff_id']) ? esc_attr($params['staff_data']['staff_id']) : ""; ?>" required="" class="form-control" id="txt-reg-id" name="txt_reg_id" placeholder="Enter ID">
                            </div>
                            <div class="form-group">
                                <label for="dd-staff-type-id">Staff Type*</label>
                                <select class="form-control" id="dd-staff-type-id" name="dd_staff_type_id">
                                    <option value="-1">Choose type</option>
                                    <?php
                                    if (count($params['staff_types']) > 0) {

                                        $saved_type = isset($params['staff_data']['staff_type_id']) ? esc_attr($params['staff_data']['staff_type_id']) : "";

                                        foreach ($params['staff_types'] as $type) {

                                            $selected = '';
                                            if ($type->id == $saved_type) {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option value="<?php echo $type->id ?>" <?php echo $selected; ?>><?php echo ucfirst($type->type); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="txtname">Name*</label>
                                <input type="text" value="<?php echo isset($params['staff_data']['name']) ? esc_attr($params['staff_data']['name']) : ''; ?>" required class="form-control" id="txt-name" name="txt_name" placeholder="Enter name">
                            </div>
                            <div class="form-group">
                                <label for="txtemail">Email*</label>
                                <input type="email" required="" class="form-control" value="<?php echo isset($params['staff_data']['email']) ? esc_attr($params['staff_data']['email']) : ''; ?>" id="txt-email" name="txt_email" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="txtphone">Phone*</label>
                                <input type="text" required class="form-control" value="<?php echo isset($params['staff_data']['phone_no']) ? esc_attr($params['staff_data']['phone_no']) : ''; ?>" id="txt-phone" name="txt_phone" placeholder="Enter phone">
                            </div>
                            <div class="form-group">
                                <label for="txtimage">Staff Profile Image</label>
                                <?php
                                if (isset($params['action']) && $params['action'] != "view") {
                                    ?>
                                    <button id="btnUploadImage" type="button" class="btn btn-primary button-large">Upload Image</button>
                                <?php } ?>
                                <img src="<?php echo!empty($params['staff_data']['profile_image']) ? esc_attr($params['staff_data']['profile_image']) : OWT_LIBRARY_PLUGIN_URL . 'assets/images/blank-user.png'; ?>" class="wpowt-lib-img-prev"/>
                                <input type="hidden" value="<?php echo isset($params['staff_data']['profile_image']) ? esc_attr($params['staff_data']['profile_image']) : OWT_LIBRARY_PLUGIN_URL . 'assets/images/blank-user.png'; ?>" name="staff_profile_image" id="stu_profile_image"/>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="txt-address-info">Address Info*</label>
                                <textarea class="form-control" required rows="5" id="txt-address-info" name='txt_address_info' placeholder="Address information"><?php echo isset($params['staff_data']['address_info']) ? esc_attr($params['staff_data']['address_info']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="txtcity">City</label>
                                <input type="text" class="form-control" value="<?php echo isset($params['staff_data']['city']) ? esc_attr($params['staff_data']['city']) : ''; ?>" id="txt-city" name="txt_city" placeholder="Enter city">
                            </div>
                            <div class="form-group">
                                <label for="dd-state">State</label>
                                <input type="text" name="txt_state" value="<?php echo isset($params['staff_data']['state']) ? esc_attr($params['staff_data']['state']) : ""; ?>" placeholder="Enter State" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label for="dd-counrty">Country*</label>
                                <select class="form-control" id="dd-counrty" name="txt_country">
                                    <option value="-1">Select country</option>
                                    <?php
                                    if (isset($params['countries'])) {

                                        if (count($params['countries']) > 0) {

                                            $saved_country = isset($params['staff_data']['country_id']) ? esc_attr($params['staff_data']['country_id']) : "";

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
                        <?php
                        if (isset($params['action']) && $params['action'] != "view") {
                            ?>
                            <div class="form-group"> 
                                <input type="hidden" name="opt_action" value="<?php echo $params['action']; ?>"/>
                                <div class="col-sm-12 owt-text-align">
                                    <button type="submit" class="btn btn-success wpowt-lib-btn"> <i class="mdi mdi-check-outline"></i> Submit</button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                    </form>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>
