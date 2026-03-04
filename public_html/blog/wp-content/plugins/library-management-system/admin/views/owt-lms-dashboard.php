<div class="container">

    <div class="row">
        <div class="col-sm-12">
            <div class="jumbotron jumbotron-fluid owt-jumbotron-margin">
                <div class="container">
                    <h2 class="display-4">Library System Dashboard</h2>
                    <p class="lead owt-jumb-p">Hey, you can manage everything of your library here. Try me once I will throw your stress.<br/>Please find the details of the Plugin here <a href="Javascript:;" data-toggle="modal" data-target="#wpowt-features-modal">Click here</a></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 owt-left-panel-body">
            <div class="col-md-3 owt-col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading owt-panel-head wpowt-lib-bg">
                        <img class="owt-image-dash" src="<?php echo OWT_LIBRARY_PLUGIN_URL ?>assets/images/student.png" alt="Manage Student">
                    </div>
                    <div class="panel-body">
                        <h5 class="wpowt-label-bold">Manage Student</h5>
                        <p>Total Students: <?php echo intval($params['students']); ?></p>
                        <p><a href="admin.php?page=owt-lib-manage-students" class="btn btn-primary wpowt-lib-btn">Click here to go <i class="mdi mdi-arrow-right-bold-circle-outline"></i></a></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 owt-col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading owt-panel-head wpowt-lib-bg">
                        <img class="owt-image-dash" src="<?php echo OWT_LIBRARY_PLUGIN_URL ?>assets/images/teacher.png" alt="Manage Staff">
                    </div>
                    <div class="panel-body">
                        <h5 class="wpowt-label-bold">Manage Teachers/Staff</h5>
                        <p>Total Management Staffs: <?php echo intval($params['staffs']); ?></p>
                        <p class=""><a href="admin.php?page=owt-lib-manage-staffs" class="btn btn-primary wpowt-lib-btn">Click here to go <i class="mdi mdi-arrow-right-bold-circle-outline"></i></a></p>
                    </div>
                </div>

            </div>
            <div class="col-md-3 owt-col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading owt-panel-head wpowt-lib-bg">
                        <img class="owt-image-dash" src="<?php echo OWT_LIBRARY_PLUGIN_URL ?>assets/images/book.svg" alt="Manage Book">
                    </div>
                    <div class="panel-body">
                        <h5 class="wpowt-label-bold">Manage Books</h5>
                        <p>Total Books: <?php echo intval($params['books']); ?></p>
                        <p class="card-text"><a href="admin.php?page=owt-lib-manage-books" class="btn btn-primary wpowt-lib-btn">Click here to go <i class="mdi mdi-arrow-right-bold-circle-outline"></i></a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 owt-col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading owt-panel-head wpowt-lib-bg">
                        <img class="owt-image-dash" src="<?php echo OWT_LIBRARY_PLUGIN_URL ?>assets/images/issue-return.png" alt="Card image cap">
                    </div>
                    <div class="panel-body">
                        <h5 class="wpowt-label-bold">Student Issue List</h5>
                        <p>Issued to Student: <?php echo intval($params['issue_to_students']); ?></p>
                        <p class="card-text"><a href="admin.php?page=owt-lib-book-issue-list" class="btn btn-primary wpowt-lib-btn">Click here to go <i class="mdi mdi-arrow-right-bold-circle-outline"></i></a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 owt-col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading owt-panel-head wpowt-lib-bg">
                        <img class="owt-image-dash" src="<?php echo OWT_LIBRARY_PLUGIN_URL ?>assets/images/issue-return.png" alt="Card image cap">
                    </div>
                    <div class="panel-body">
                        <h5 class="wpowt-label-bold">Staff Issue List</h5>
                        <p>Issued to Staff: <?php echo intval($params['issue_to_staffs']); ?></p>
                        <p class="card-text"><a href="admin.php?page=owt-lib-staff-book-issue" class="btn btn-primary wpowt-lib-btn">Click here to go <i class="mdi mdi-arrow-right-bold-circle-outline"></i></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="wpowt-features-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">How can we know its features !</h4>
            </div>
            <div class="modal-body">
                <?php
                ob_start();
                include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/report/owt-lib-features-list.php';
                $template = ob_get_contents();
                ob_end_clean();
                echo $template;
                ?>
            </div>
        </div>

    </div>
</div>