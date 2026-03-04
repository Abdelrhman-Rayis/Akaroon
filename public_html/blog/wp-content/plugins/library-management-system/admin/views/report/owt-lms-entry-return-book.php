<div class="container <?php echo OWT_LIBRARY_PLUGIN_PREFIX; ?>_cont">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-return-book-list">Book Return List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Entry Return Book</li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/common.php'; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                Return a Book
                <a href="admin.php?page=owt-lib-return-book-list" class="btn btn-danger pull-right owt-btn-right owt-btn-top wpowt-lib-btn"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a>
            </div>
            <div class="panel-body">
                <form action="javascript:void(0)" id="wpowt-frm-book-return" method="post">

                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="wpowt-dd-user-type-id">User Type*</label>
                            <select class="form-control" id="wpowt-dd-types" name="dd_user_type_id">
                                <option value="-1">Select User type</option> 
                                <?php
                                if (isset($params['data'])) {
                                    $user_types = $params['data'];
                                    if (count($user_types) > 0) {
                                        foreach ($user_types as $type) {
                                            ?>
                                            <option value='<?php echo $type->id; ?>'><?php echo ucfirst($type->type) ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="wpowt-lib-student-section" style="display: none;">
                            <div class="form-group wpowt-lib-dd-branch">
                                <label for="dd-student-branch-id">Select Branch*</label>
                                <select class="form-control" id="wpowt-student-branch-dd" name="dd_st_branch_id">
                                    <option value="-1">Choose branch</option>
                                    <?php
                                    foreach ($params['branches'] as $index => $branch) {
                                        ?>
                                        <option value="<?php echo $branch->id ?>"><?php echo ucfirst($branch->name); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group wpowt-lib-dd-student-list" style="display: none;">
                                <label for="dd-student-id">Select Student*</label>
                                <select class="form-control" id="wpowt-students-dd-list" name="wpowt_return_dd_student_id">
                                    <option value="-1">Choose student</option>
                                </select>
                            </div>
                        </div>

                        <div class="wpowt-lib-staff-section" style="display: none;">
                            <div class="form-group">
                                <label for="dd-staff-id">Select Staff*</label>
                                <select class="form-control" id="wpowt-lib-stafflist" name="wpowt_return_dd_staff_id">
                                    <option>1</option>
                                </select>
                            </div>
                        </div>
                    </div>  

                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="dd-category">Book Issue List*</label>
                            <div class="wpowt-lib-books-issued-area">
                                <i>-- No Books Issued --</i>
                            </div>
                        </div>
                    </div>


                    <div class="form-group"> 
                        <div class="col-sm-12 owt-text-align">
                            <button type="submit" class="btn btn-success wpowt-lib-btn"><i class="mdi mdi-check-outline"></i> Submit</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>



