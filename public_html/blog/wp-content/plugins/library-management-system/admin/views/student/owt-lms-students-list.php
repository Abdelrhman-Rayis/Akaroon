<div class="container">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Student</li>
                </ol>
            </nav>
        </div>
    </div>
    <?php include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/common.php'; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                Students List
                <a href="admin.php?page=owt-lib-create-student" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">Add Student</a>
                <a href="Javascript:;" data-toggle="modal" data-target="#wpowt-branch-modal" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">Add Branch</a>
                <a href="Javascript:;" data-toggle="modal" data-target="#wpowt-view-all-branch-modal" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">View All Branch</a>
            </div>
            <div class="panel-body">
                <table id="owt-tbl-book-list" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Reg ID</th>
                            <th>Reg Type</th>
                            <th>Name</th>
                            <th>Class/Branch</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($params['students'])) {

                            if (count($params['students']) > 0) {

                                $count = 1;
                                foreach ($params['students'] as $student) {

                                    if ($count > 2) {
                                        continue;
                                    }
                                    if (!empty($student->phone_no)) {
                                        $phone = $student->phone_no;
                                    } elseif (!empty($student->parent_phone_no)) {
                                        $phone = $student->parent_phone_no . " (parent)";
                                    } else {
                                        $phone = '<i>-- No phone --</i>';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $student->student_id; ?></td>
                                        <td><?php echo ucfirst($student->registration_type); ?></td>
                                        <td><?php echo $student->name; ?></td>
                                        <td><?php echo $this->owt_library_get_branch_info($student->branch_id); ?></td>
                                        <td><?php echo!empty($student->email) ? $student->email : '<i>-- No email --</i>'; ?></td>
                                        <td><?php echo $phone; ?></td>
                                        <td>
                                            <a href="admin.php?page=owt-lib-create-student&action=edit&stid=<?php echo $student->id; ?>" class='btn btn-info wpowt-lib-btn' title="Edit"><i class="mdi mdi-pencil"></i></a>
                                            <a href="admin.php?page=owt-lib-create-student&action=view&stid=<?php echo $student->id; ?>" class='btn btn-info wpowt-lib-btn' title="View"><i class="mdi mdi-eye"></i></a>
                                            <a href="javascript:void(0)" class='btn btn-danger wpowt-lib-del-student wpowt-lib-btn' data-id="<?php echo $student->id; ?>" title="Delete"><i class="mdi mdi-trash-can-outline"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>

                    </tbody>
                </table> 
            </div>
        </div>

    </div>
</div>

<div id="wpowt-branch-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Adding Class/Branch...</h4>
            </div>
            <div class="modal-body">
                <?php
                if (count($params['branches']) >= 2) {
                    ?>
                    <div class="alert alert-danger">
                        <b>You have no more credits left to Add Branch/Class</b>
                    </div>
                    <?php
                } else {
                    ?>
                    <form action="Javascript:Void(0)" id="wpowt-frm-add-frm" method="post">
                        <div class="form-group">
                            <label for="txtbranch">Class/Branch Title</label>
                            <input type="text" required="" class="form-control" placeholder="Enter Branch Title" id="wpowt-branch" name="wpowt_branch">
                        </div>
                        <button type="submit" class="btn btn-primary wpowt-lib-btn"><i class="mdi mdi-check-outline"></i> Submit</button>
                    </form>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>

<div id="wpowt-view-all-branch-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Showing all Branches...</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($params['branches']) && count($params['branches']) > 0) {
                            $branch_count = 1;
                            foreach ($params['branches'] as $index => $branch) {

                                if ($branch_count > 2) {
                                    continue;
                                }
                                ?>
                                <tr class="warning wpowt-branch-tr-row">
                                    <td><?php echo $branch_count++; ?></td>
                                    <td class="wpowt-branch-edit-name"><?php echo $branch->name; ?></td>
                                    <td><?php echo $branch->status == 1 ? '<i>Active</i>' : '<i>Inactive</i>'; ?></td>
                                    <td class="wpowt-branch-btns">
                                        <div class="wpowt-branch-action-btns">
                                            <a href="Javascript:;" class="btn btn-info wpowt-edit-name wpowt-lib-btn"><i class="mdi mdi-pencil"></i></a>
                                            <a href="Javascript:;" class="btn btn-danger wpowt-delete-branch wpowt-lib-btn" data-id="<?php echo $branch->id; ?>"><i class="mdi mdi-trash-can-outline"></i></a>
                                        </div>
                                        <div class="wpowt-branch-save-btn" style="display: none;">
                                            <a href="Javascript:;" class="btn btn-info wpowt-save-branch wpowt-lib-btn" data-branch="<?php echo $branch->name; ?>" data-id="<?php echo $branch->id; ?>"><i class="mdi mdi-check-outline"></i> Save</a>
                                            <a href="Javascript:;" class="btn btn-danger wpowt-cancel-update wpowt-lib-btn"><i class="mdi mdi-close"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>