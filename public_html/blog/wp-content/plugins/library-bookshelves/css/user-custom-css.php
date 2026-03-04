<?php
require_once '../../../../wp-load.php';

header( 'Content-type: text/css' );

$table = $wpdb->prefix . 'options';
$css_like   = $wpdb->esc_like( 'lbs_css_' ) . '%';
$css_query = $wpdb->prepare( 'SELECT * FROM %1s WHERE option_name like %s', array( $table, $css_like ) );
$css_result = $wpdb->get_results( $css_query, ARRAY_A );

if ( $css_result ) {
	foreach ( $css_result as $c ) {
		$css_options[ $c['option_name'] ] = $c['option_value'];
	}
} else {
	$css_options = lbs_set_defaults( 'css' );
}

?>
/*
	User-editable styles
*/

.bookshelf {
<?php
	echo "\tmargin-top: " . $css_options['lbs_css_bookshelf_margin_top'] . ";\n";
	$margin = $css_options['lbs_css_bookshelf_margin_side'];
	echo "\tmargin-left: " . $margin . ";\n";
	echo "\tmargin-right: " . $margin . ";\n";
	echo "\tmargin-bottom: " . $css_options['lbs_css_bookshelf_margin_bottom'] . ";\n";
?>
}

.bookshelf .slick-arrow::before {
<?php echo "\tcolor: " . $css_options['lbs_css_arrow_color'] . ";\n"; ?>
<?php echo "\tfont-size: " . $css_options['lbs_css_arrow_size'] . ";\n"; ?>
}

.bookshelf .slick-prev {
<?php echo "\tleft: " . $css_options['lbs_css_arrow_distance'] . " !important;\n"; ?>
}

.bookshelf .slick-next {
<?php echo "\tright: " . $css_options['lbs_css_arrow_distance'] . " !important;\n"; ?>
}

.bookshelf .slick-dots {
<?php echo "\tbottom: " . $css_options['lbs_css_dots_bottom_offset'] . " !important;\n"; ?>
}

.bookshelf .slick-dots li button::before, .bookshelf .slick-dots li.slick-active button::before {
<?php echo "\tcolor: " . $css_options['lbs_css_dot_color'] . ";\n"; ?>
}

.Bookshelves_Widget .ui-tabs-active, .Bookshelves_Widget .ui-tabs-nav li:hover {
<?php echo "\tbackground-color: " . $css_options['lbs_css_widget_active_bg_color'] . ";\n"; ?>
}

.Bookshelves_Widget .ui-tabs-active .ui-tabs-anchor, .Bookshelves_Widget .ui-tabs-nav li a:hover {
<?php echo "\tcolor: " . $css_options['lbs_css_widget_active_link_color'] . ";\n"; ?>
}

.Bookshelves_Widget .ui-tabs-nav li {
<?php echo "\tborder-width: " . $css_options['lbs_css_widget_tab_border_width'] . ";\n"; ?>
<?php echo "\tborder-color: " . $css_options['lbs_css_widget_tab_border_color'] . ";\n"; ?>
}
