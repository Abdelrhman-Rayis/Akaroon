<div id="owt-lms-tabs">
    <ul>
        <li><a href="#books-listing"><span>Book Gallery</span></a></li>
    </ul>
    <div id="books-listing">
        <div>Available Books at Library</div>

        <div id="lib-front-area">
            <form method="post" id="frm-search-book">
                <div>
                    Filter by: 
                    <select name="dd_category" id="dd_category">
                        <option value="-1">--select category--</option>
                        <?php
                        if (count($categories) > 0) {
                            foreach ($categories as $index => $category) {
                                ?>
                                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </form>
            <div id="lib-load-book">
                <?php
                ob_start();
                include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'public/views/tmpl/owt-lms-tmpl-book.php';
                $template = ob_get_contents();
                ob_end_clean();
                echo $template;
                ?>
            </div>
        </div>
    </div>
</div>
