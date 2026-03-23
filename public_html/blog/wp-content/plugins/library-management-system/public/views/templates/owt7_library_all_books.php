<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="book-list-container" id="owt7_lib_book_container">
    <?php
    if(is_array($params['books']) && count($params['books']) > 0){
        foreach($params['books'] as $book){
    ?>
        <div class="book-card">
            <div class="book-cover">
                <?php if(!empty($book->cover_image)){ ?>
                    <img src="<?php echo $book->cover_image; ?>" alt="<?php echo ucwords($book->name); ?>">
                <?php }else{ ?>
                    <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>" alt="<?php echo ucwords($book->name); ?>">
                <?php } ?> 
            </div>
            <div class="book-details">
                <h3 class="book-name"><strong><?php echo ucwords($book->name); ?></strong></h3>
                <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo ucwords($book->category_name); ?></p>
                <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo ucwords($book->author_name); ?></p>
                <p class="book-quantity"><strong><?php esc_html_e('Publication Name:', 'library-management-system'); ?></strong>
                    <?php echo ucwords($book->publication_name); ?></p>
                <p class="book-status">
                    <strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong>
                    <?php if($book->status && $book->stock_quantity > 0){ ?>
                    <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_available"><?php esc_html_e('Available', 'library-management-system'); ?></a>
                    <?php } else{ ?>
                    <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_not_available"><?php esc_html_e('Not Available', 'library-management-system'); ?></a>
                    <?php } ?>
                </p>
            </div>
            <div class="book-footer">
                <a title="<?php esc_attr_e('View', 'library-management-system'); ?>" href="<?php echo home_url('wp-library-books/?bid='.base64_encode($book->id)); ?>"
                    class="view-book-btn">
                    <?php esc_html_e('View', 'library-management-system'); ?>
                </a>
            </div>
        </div>
        <?php
        }
    }
    ?>
</div>
<div class="pagination">
    <?php
    if ($params['total_pages'] > 1) {
        $current_page = $params['current_page'];
        $total_pages  = $params['total_pages'];
        $base_url     = home_url('wp-library-books/');

        // Number of pages to show before/after current
        $range = 2;

        // First page link
        if ($current_page > 1) {
            echo '<a href="' . esc_url($base_url . '?p_no=1') . '">' . esc_html__('First', 'library-management-system') . '</a>';
            echo '<a href="' . esc_url($base_url . '?p_no=' . ($current_page - 1)) . '">&laquo; ' . esc_html__('Prev', 'library-management-system') . '</a>';
        }

        // Page numbers
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                if ($i == $current_page) {
                    echo '<span class="current-page">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url($base_url . '?p_no=' . $i) . '">' . $i . '</a>';
                }
            } elseif ($i == 2 && $current_page - $range > 3) {
                echo '<span class="dots">...</span>';
            } elseif ($i == $total_pages - 1 && $current_page + $range < $total_pages - 2) {
                echo '<span class="dots">...</span>';
            }
        }

        // Next & Last page link
        if ($current_page < $total_pages) {
            echo '<a href="' . esc_url($base_url . '?p_no=' . ($current_page + 1)) . '">' . esc_html__('Next', 'library-management-system') . ' &raquo;</a>';
            echo '<a href="' . esc_url($base_url . '?p_no=' . $total_pages) . '">' . esc_html__('Last', 'library-management-system') . '</a>';
        }
    }
    ?>
</div>
