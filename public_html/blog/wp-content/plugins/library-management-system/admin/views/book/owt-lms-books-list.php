<div class="container">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Book</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/common.php'; ?>

    <div class="row owt-inner-row">
        <div class="panel panel-primary">
            <div class="panel-heading wpowt-lib-bg">
                Book List
                <a href="admin.php?page=owt-lib-create-book" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">Add Book</a>
                <a href="javscript:;" id='btn-create-category' data-toggle="modal" data-target="#frm-create-category" class="btn btn-warning pull-right owt-btn-right owt-btn-top wpowt-lib-btn">Create Category</a>
                <a href="Javascript:;" data-toggle="modal" data-target="#wpowt-view-all-category-modal" class="btn btn-info pull-right owt-btn-right owt-btn-top wpowt-lib-btn">View All Categories</a>
            </div>
            <div class="panel-body">
                <table id="owt-tbl-book-list" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Name</th>
                            <th>Book ID</th>
                            <th>Author</th>
                            <th>Publication</th>
                            <th>Amount (<?php echo OWT_LIBRARY_PLUGIN_BOOK_CURRENCY; ?>)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($params['books']) && count($params['books']) > 0) {
                            $book_count = 1;
                            foreach ($params['books'] as $index => $book) {
                                ?>
                                <tr>
                                    <td><?php echo $book_count++; ?></td>
                                    <td><?php echo $book->name; ?></td>
                                    <td><?php echo $book->book_id; ?></td>
                                    <td><?php echo $book->author_info; ?></td>
                                    <td><?php echo $book->publisher_info; ?></td>
                                    <td><?php echo $book->amount; ?></td>
                                    <td>
                                        <a href="admin.php?page=owt-lib-create-book&action=edit&bid=<?php echo $book->id ?>" class='btn btn-info wpowt-lib-btn' title="Edit"><i class="mdi mdi-pencil"></i></a>
                                        <a href="admin.php?page=owt-lib-create-book&action=view&bid=<?php echo $book->id ?>" class='btn btn-info wpowt-lib-btn' title="View"><i class="mdi mdi-eye"></i></a>
                                        <a href="javascript:void(0)" class='btn btn-danger wpowt-lib-del-book wpowt-lib-btn' data-id="<?php echo $book->id; ?>" title="Delete"><i class="mdi mdi-trash-can-outline"></i></a>
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

<div id="frm-create-category" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Book Category</h4>
            </div>
            <div class="modal-body">
                <?php
                if (count($params['categories']) >= 2) {
                    ?>
                    <div class="alert alert-danger">
                        <b>You have no more credits left to Add category</b>
                    </div>
                    <?php
                } else {
                    ?>
                    <form action="javascript:void(0)" id='wpowt-frm-book-category'>
                        <div class="form-group">
                            <label for="txt-title">Category Title:</label>
                            <input type="text" required="" class="form-control" id="txt-title" name='txt_title' placeholder="Category Title">
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

<div id="wpowt-view-all-category-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Showing all Categories...</h4>
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
                        if (isset($params['categories']) && count($params['categories']) > 0) {
                            $categories_count = 1;
                            foreach ($params['categories'] as $index => $type) {
                                ?>
                                <tr class="warning wpowt-category-tr-row">
                                    <td><?php echo $categories_count++; ?></td>
                                    <td class="wpowt-category-edit-name"><?php echo $type->name; ?></td>
                                    <td><?php echo $type->status == 1 ? '<i>Active</i>' : '<i>Inactive</i>'; ?></td>
                                    <td class="wpowt-category-btns">
                                        <div class="wpowt-category-action-btns">
                                            <a href="Javascript:;" class="btn btn-info wpowt-edit-category-name wpowt-lib-btn"><i class="mdi mdi-pencil"></i></a>
                                            <a href="Javascript:;" class="btn btn-danger wpowt-delete-category wpowt-lib-btn" data-id="<?php echo $type->id; ?>"><i class="mdi mdi-trash-can-outline"></i></a>
                                        </div>
                                        <div class="wpowt-category-save-btn" style="display: none;">
                                            <a href="Javascript:;" class="btn btn-info wpowt-save-category wpowt-lib-btn" data-type="<?php echo $type->name; ?>" data-id="<?php echo $type->id; ?>"><i class="mdi mdi-check-outline"></i> Save</a>
                                            <a href="Javascript:;" class="btn btn-danger wpowt-cancel-category-update wpowt-lib-btn"><i class="mdi mdi-close"></i></a>
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