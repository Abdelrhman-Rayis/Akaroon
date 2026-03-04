<?php
if (count($books) > 0) {

    foreach ($books as $index => $book) {
        ?>
        <div class="single-book-item">
            <div class="book-image"><img src="<?php echo $book->cover_image; ?>" style="height: 100px;width: 100px;"/></div>
            <div class="book-title">Title: <?php echo $book->name; ?></div>
            <div class="book-author">Author: <?php echo $book->author_info; ?></div>
            <div class="book-category">Category: <?php echo $book->category_name; ?></div>
            <div class="book-isbn">ISBN: <?php echo $book->isbn; ?></div>
        </div>
        <?php
    }
}
?>