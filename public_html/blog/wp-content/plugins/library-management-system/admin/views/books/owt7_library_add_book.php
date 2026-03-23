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

    <div class="owt7_library_add_book">

        <div class="page-header">
            <div class="breadcrumb"> <?php esc_html_e("Library System", "library-management-system"); ?> >> <span class="active"><?php esc_html_e("Add New Book", "library-management-system"); ?></span> </div>
            <div class="page-actions">
            <?php 
                // Generate the nonce for the actions
                $page_nonce = wp_create_nonce('owt7_manage_books_page_nonce');
            ?>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=add&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-plus-alt"></span> <?php esc_html_e("Add New Category", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=list&_wpnonce=<?php echo esc_attr($page_nonce); ?>" class="btn"><span class="dashicons dashicons-list-view"></span> <?php esc_html_e("List Category", "library-management-system"); ?></a>
                <a href="admin.php?page=owt7_library_books" class="btn"><span class="dashicons dashicons-book-alt"></span> <?php esc_html_e("List Book", "library-management-system"); ?></a>
                <?php 
                // Show Preview button only when editing
                if (isset($_GET['opt']) && $_GET['opt'] === 'edit' && !empty($_GET['id'])) {
                    $book_id = base64_decode(sanitize_text_field($_GET['id']));
                    if (!empty($book_id)) {
                        $preview_url = home_url('wp-library-books/?bid=' . base64_encode($book_id));
                        ?>
                        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="btn btn-preview">
                             <?php esc_html_e("Preview Book", "library-management-system"); ?>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <?php if(isset($params['action'])){ ?> <h2><?php echo esc_attr(ucfirst($params['action'])." Book", "library-management-system"); ?></h2>
                <?php } else{ ?> <h2><?php esc_html_e("Add Book", "library-management-system"); ?></h2> <?php } ?>
            </div>

            <form class="owt7_lms_book_form" id="owt7_lms_book_form" action="javascript:void(0);" method="post">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type"
                    value="<?php echo isset($params['action']) && !empty($params['action']) ? esc_attr($params['action']) : 'add'; ?>">
                <?php 
                if(isset($params['action']) && $params['action'] == 'edit'){ 
                    ?>
                <input type="hidden" name="edit_id" value="<?php echo isset($params['book']['id']) ? esc_attr($params['book']['id']) : ''; ?>">
                <?php
                } 
                ?>

                <div class="form-row">
                    <!-- Book ID -->
                    <div class="form-group">
                        <label for="owt7_txt_book_id"><?php esc_html_e("Book ID", "library-management-system"); ?> <span class="required">*</span> 
                            <?php if(isset($params['action']) && in_array($params['action'], ["view", "edit"])){}else{ ?>
                                <a href="javascript:void(0);" data-module="book" id="owt7_btn_ids_auto_generate"><u><?php esc_html_e('Auto-generate', 'library-management-system') ?></u>?</a>
                            <?php } ?>
                         </label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_id']) ? esc_attr($params['book']['book_id']) : ''; ?>" required type="text" id="owt7_txt_book_id" <?php if(isset($params['action']) && in_array($params['action'], ["view", "edit"])){ ?>readonly<?php }else{} ?> name="owt7_txt_book_id" placeholder="...">
                    </div>
                    <!-- Category -->
                    <div class="form-group">
                        <label for="owt7_dd_category_id"><?php esc_html_e("Category", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> required id="owt7_dd_category_id" name="owt7_dd_category_id">
                            <option value=""><?php esc_html_e("-- Select Category --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['categories']) && is_array($params['categories'])){
                                    foreach($params['categories'] as $category){
                                        $selected = "";
                                        if(isset($params['book']['category_id']) && $params['book']['category_id'] == $category->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($category->id); ?>">
                                    <?php echo esc_html(ucfirst($category->name)); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Bookcase -->
                    <div class="form-group">
                        <label for="owt7_dd_bookcase_id"><?php esc_html_e("Bookcase", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> required id="owt7_dd_bookcase_id" name="owt7_dd_bookcase_id">
                            <option value=""><?php esc_html_e("-- Select Bookcase --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['bookcases']) && is_array($params['bookcases'])){
                                    foreach($params['bookcases'] as $bookcase){
                                        $selected = "";
                                        if(isset($params['book']['bookcase_id']) && $params['book']['bookcase_id'] == $bookcase->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($bookcase->id); ?>">
                                    <?php echo esc_html(ucfirst($bookcase->name)); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <!-- Section -->
                    <div class="form-group">
                        <label for="owt7_dd_section_id"><?php esc_html_e("Section", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> required id="owt7_dd_section_id" name="owt7_dd_section_id">
                            <option value=""><?php esc_html_e("-- Select Section --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['sections']) && is_array($params['sections'])){
                                    foreach($params['sections'] as $section){
                                        $selected = "";
                                        if(isset($params['book']['bookcase_section_id']) && $params['book']['bookcase_section_id'] == $section->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($section->id); ?>">
                                    <?php echo esc_html(ucfirst($section->name)); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Book Name -->
                    <div class="form-group">
                        <label for="owt7_txt_book_name"><?php esc_html_e("Book Name", "library-management-system"); ?> <span class="required">*</span></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['name']) ? esc_attr($params['book']['name']) : ''; ?>" required type="text" id="owt7_txt_book_name" name="owt7_txt_book_name" placeholder="...">
                    </div>
                    <!-- Author Name -->
                    <div class="form-group">
                        <label for="owt7_txt_author_name"><?php esc_html_e("Author(s) Name", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['author_name']) ? esc_attr($params['book']['author_name']) : ''; ?>" type="text" id="owt7_txt_author_name" name="owt7_txt_author_name" placeholder="...">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Publication Name -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_name"><?php esc_html_e("Publication Name", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_name']) ? esc_attr($params['book']['publication_name']) : ''; ?>" type="text" id="owt7_txt_publication_name" name="owt7_txt_publication_name" placeholder="...">
                    </div>
                    <!-- Publication Year -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_year"><?php esc_html_e("Publication Year", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_year']) ? esc_attr($params['book']['publication_year']) : ''; ?>" type="text" id="owt7_txt_publication_year" name="owt7_txt_publication_year" placeholder="...">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Publication Location -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_location"><?php esc_html_e("Publication Location", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_location']) ? esc_attr($params['book']['publication_location']) : ''; ?>" type="text" id="owt7_txt_publication_location" name="owt7_txt_publication_location" placeholder="...">
                    </div>
                    <div class="form-group">
                        <!-- Cost -->
                        <label for="owt7_txt_cost"><?php esc_html_e("Cost", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['amount']) ? esc_attr($params['book']['amount']) : ''; ?>" type="number" min="0" max="100000" id="owt7_txt_cost" name="owt7_txt_cost" placeholder="...">
                    </div>
                </div>

                <div class="form-row">
                    <!-- ISBN -->
                    <div class="form-group">
                        <label for="owt7_txt_isbn"><?php esc_html_e("ISBN", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['isbn']) ? esc_attr($params['book']['isbn']) : ''; ?>" type="text" id="owt7_txt_isbn" name="owt7_txt_isbn" placeholder="...">
                    </div>
                    <!-- Book URL -->
                    <div class="form-group">
                        <label for="owt7_txt_book_url"><?php esc_html_e("Book URL", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_url']) ? esc_attr($params['book']['book_url']) : ''; ?>" type="text" id="owt7_txt_book_url" name="owt7_txt_book_url" placeholder="...">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Stock Quantity -->
                    <div class="form-group">
                        <label for="owt7_txt_quantity"><?php esc_html_e("Stock Quantity", "library-management-system"); ?> <span class="required">*</span></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['stock_quantity']) ? esc_attr($params['book']['stock_quantity']) : ''; ?>" required type="number" min="1" max="10000" id="owt7_txt_quantity" name="owt7_txt_quantity" placeholder="...">
                    </div>
                    <!-- Book Language -->
                    <div class="form-group">
                        <label for="owt7_txt_book_language"><?php esc_html_e("Book Language", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_language']) ? esc_attr($params['book']['book_language']) : ''; ?>" type="text" id="owt7_txt_book_language" name="owt7_txt_book_language" placeholder="...">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Number of Pages -->
                    <div class="form-group">
                        <label for="owt7_txt_total_pages"><?php esc_html_e("Number of Page(s)", "library-management-system"); ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_pages']) ? esc_attr($params['book']['book_pages']) : ''; ?>" type="text" id="owt7_txt_total_pages" name="owt7_txt_total_pages" placeholder="...">
                    </div>
                    <!-- Description -->
                    <div class="form-group">
                        <label for="owt7_txt_description"><?php esc_html_e("Description", "library-management-system"); ?></label>
                        <?php
                            $content   = isset($params['book']['description']) ? wp_kses_post($params['book']['description']) : '';
                            $editor_id = 'owt7_txt_description';
                            $settings  = array(
                                'textarea_name' => 'owt7_txt_description',
                                'textarea_rows' => 6,
                                'media_buttons' => false,
                                'teeny'         => true,
                                'quicktags'     => false,
                                'tinymce'       => array(
                                    'toolbar1' => 'bold,italic,underline,bullist,numlist,link,unlink,undo,redo',
                                    'toolbar2' => '',
                                ),
                                'readonly' => (isset($params['action']) && $params['action'] == 'view'),
                            );

                            wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Cover Image -->
                    <div class="form-group">
                        <label for="owt7_cover_image"><?php esc_html_e("Cover Image", "library-management-system"); ?></label>
                        <?php if(isset($params['action']) && $params['action'] == 'view'){ }else{ ?>
                        <button id="owt7_upload_image" type="button" class="btn btn-primary button-large">
                            <?php esc_html_e("Upload Cover Image", "library-management-system"); ?>
                        </button>
                        <?php } 
                                if(!empty($params['book']['cover_image'])){
                                    ?> <img src="<?php echo esc_attr($params['book']['cover_image']); ?>"
                            id="owt7_library_image_preview" /> <?php
                                }else{
                                    ?> <img src="<?php echo esc_url(LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'); ?>" id="owt7_library_image_preview" /> <?php
                                } 
                        ?>
                        <input type="hidden" value="<?php echo isset($params['book']['cover_image']) ? esc_attr($params['book']['cover_image']) : esc_url(LIBMNS_PLUGIN_URL) . 'admin/images/default-cover-image.png'; ?>" name="owt7_cover_image" id="owt7_image_url" />
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_book_status"><?php esc_html_e("Status", "library-management-system"); ?> <span class="required">*</span></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_book_status" required name="owt7_dd_book_status">
                            <option value=""><?php esc_html_e("-- Select Status --", "library-management-system"); ?></option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['book']['status']) && $params['book']['status'] == $key){
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