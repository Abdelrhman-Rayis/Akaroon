<div class="container">
    <div class="row owt-inner-row owt-lib-breadcrumb-top">
        <div class="col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage">Library Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="admin.php?page=owt-lib-manage-books">Book List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?php
                        if (isset($params['action']) && $params['action'] == "edit") {
                            echo 'Edit Book';
                        } elseif (isset($params['action']) && $params['action'] == "view") {
                            echo 'View Book';
                        } else {
                            echo 'Create Book';
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
                Create Book
                <a href="admin.php?page=owt-lib-manage-books" class="btn btn-danger pull-right owt-btn-right owt-btn-top wpowt-lib-btn"><i class="mdi mdi-arrow-left-bold-circle-outline"></i> Back</a>
            </div>
            <div class="panel-body">

                <?php
                if ($params['total_books'] >= 5  && isset($params['action']) && $params['action'] == "add") {
                    ?>
                    <div class="alert alert-danger">
                        <b>You have no more credits left to Add Book</b>
                    </div>
                    <?php
                } else {
                    ?>
                    <form action="javascript:void(0)" id="wpowt-lib-frm-create-new-book" method="post">
                        <div class="col-sm-5">
                            <div class="form-group">

                                <label for="dd-category">Category*</label>
                                <select class="form-control" id="dd-category" name="txt_category">
                                    <option value="-1">Choose category</option>
                                    <?php
                                    if (isset($params['categories']) && count($params['categories']) > 0) {

                                        $saved_id = isset($params['book_data']['category_id']) ? intval($params['book_data']['category_id']) : '';

                                        foreach ($params['categories'] as $category) {

                                            $selected = '';
                                            if ($saved_id == $category->id) {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option value="<?php echo $category->id; ?>" <?php echo $selected; ?>><?php echo $category->name; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="txt-book-id">Book ID*</label>
                                <input type="text" value="<?php echo isset($params['book_data']['book_id']) ? esc_attr($params['book_data']['book_id']) : ''; ?>" <?php
                                if (isset($params['action']) && $params['action'] == "edit") {
                                    echo 'readonly';
                                }
                                ?> required="" class="form-control" id="txt-book-id" name="txt_book_id" placeholder="Enter ID">
                            </div>
                            <div class="form-group">
                                <label for="txtname">Name*</label>
                                <input type="text" required="" value="<?php echo isset($params['book_data']['name']) ? esc_attr($params['book_data']['name']) : ''; ?>" class="form-control" id="txt-name" name="txt_name" placeholder="Enter name">
                            </div>
                            <div class="form-group">
                                <label for="txtauthor">Author*</label>
                                <input type="text" value="<?php echo isset($params['book_data']['author_info']) ? esc_attr($params['book_data']['author_info']) : ''; ?>" required="" class="form-control" id="txt-author" name="txt_author" placeholder="Enter author">
                            </div>
                            <div class="form-group">
                                <label for="txtpublication">Publication*</label>
                                <input type="text" required="" value="<?php echo isset($params['book_data']['publisher_info']) ? esc_attr($params['book_data']['publisher_info']) : ''; ?>" class="form-control" id="txt-publication" name="txt_publication" placeholder="Enter publication">
                            </div>
                            <div class="form-group">
                                <label for="txtamount">Amount (<?php echo OWT_LIBRARY_PLUGIN_BOOK_CURRENCY; ?>)*</label>
                                <input type="number" min="1" required="" class="form-control" value="<?php echo isset($params['book_data']['amount']) ? esc_attr($params['book_data']['amount']) : ''; ?>"  id="txtamount" name="txtamount" placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="txt-description-info">Description Info</label>
                                <textarea class="form-control" rows="5" id="txt-description-info" name='txt_description_info' placeholder="Description"><?php echo isset($params['book_data']['description']) ? esc_attr($params['book_data']['description']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="txtisbn">ISBN</label>
                                <input type="text" class="form-control" value="<?php echo isset($params['book_data']['isbn']) ? esc_attr($params['book_data']['isbn']) : ''; ?>" id="txtisbn" name="txtisbn" placeholder="Enter ISBN">
                            </div>
                            <div class="form-group">
                                <label for="txtimage">Book Cover Image</label>
                                <?php
                                if (isset($params['action']) && $params['action'] != "view") {
                                    ?>
                                    <button id="btnUploadImage" type="button" class="btn btn-primary button-large">Upload Image</button>
                                    <?php
                                }
                                ?>
                                <img src="<?php echo isset($params['book_data']['cover_image']) ? esc_attr($params['book_data']['cover_image']) : OWT_LIBRARY_PLUGIN_URL . 'assets/images/blank-book.jpg'; ?>" class="wpowt-lib-img-prev"/>
                                <input type="hidden" value="<?php echo isset($params['book_data']['cover_image']) ? esc_attr($params['book_data']['cover_image']) : OWT_LIBRARY_PLUGIN_URL . 'assets/images/blank-book.jpg'; ?>" name="book_cover_image" id="stu_profile_image"/>
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
