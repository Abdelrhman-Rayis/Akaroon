<?php
function bookshelves_create_taxonomy() {
	register_taxonomy(
		'location',
		'bookshelves',
		array(
			'labels' 			=> array(
				'name'          => 'Location',
				'singular_name' => 'Location',
				'search_items'  => 'Search Locations',
				'all_items'     => 'All Locations',
				'edit_item'     => 'Edit Location',
				'update_item'   => 'Update Location',
				'add_new_item'  => 'Add New Location',
				'new_item_name' => 'New Location Name',
				'not_found'     => 'No locations found.',
				'menu_name'     => 'Locations',
				'view_item'     => 'View Locations',
			),
			'description'       => 'Used to assign Bookshelf posts to Bookshelf Widgets.',
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'rewrite'           => false,
			'query_var'         => true,
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'bookshelves_create_taxonomy' );
