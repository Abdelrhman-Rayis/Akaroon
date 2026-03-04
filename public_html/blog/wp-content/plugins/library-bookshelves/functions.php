<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

//==================================================
// Read plugin options from database
//==================================================

function lbs_get_opts() {
	// Query WP options table for plugin settings
	global $wpdb;
	$table  = $wpdb->prefix . 'options';

	$cat_like   = $wpdb->esc_like( 'lbs_cat_' ) . '%';
	$cat_query  = $wpdb->prepare( 'SELECT * FROM %1s WHERE option_name like %s', array( $table, $cat_like ) );
	$cat_result = $wpdb->get_results( $cat_query, ARRAY_A );

	$slick_like   = $wpdb->esc_like( 'lbs_slick_' ) . '%';
	$slick_query  = $wpdb->prepare( 'SELECT * FROM %1s WHERE option_name like %s', array( $table, $slick_like ) );
	$slick_result = $wpdb->get_results( $slick_query, ARRAY_A );

	// Convert query results to associative arrays
	if ( $cat_result ) {
		foreach ( $cat_result as $c ) {
			$cat_options[ $c['option_name'] ] = $c['option_value'];
		}
	} else {
		$cat_options = lbs_set_defaults( 'catalog' );
	}

	if ( $slick_result ) {
		foreach ( $slick_result as $s ) {
			$slick_options[ $s['option_name'] ] = $s['option_value'];
		}
	} else {
		$slick_options = lbs_set_defaults( 'slick' );
	}

	$options = array_merge( $cat_options, $slick_options );
	return $options;
}

//==================================================
// Set default options if none are stored
//==================================================

function lbs_set_defaults( $option_set ) {
	$init_options = lbs_settings_obj();
	$init_options = $init_options[ $option_set ]['fields'];
	$options = array();

	// Prepend option names with 'lbs_'
	foreach ( $init_options as $k => $v ) {
		$id = 'lbs_' . $init_options[ $k ]['id'];
		$val = $init_options[ $k ]['default'];
		$options[ $id ] = $val;
	}

	return $options;
}

//==================================================
// Parse Slick options for initialization
//==================================================

function lbs_get_slick_opts( $options_glb, $options_post ) {
	$slick_opts = array();      // Script initialization options
	$slick_data_atts = array(); // data attribute options to override script options
	
	foreach ( $options_glb as $opt => $val ) {
		switch ( $opt ) {
			case 'lbs_slick_accessibility':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'accessibility: false';
				// If the option exists, check if it has a value
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"accessibility": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"accessibility": false' : '';
				}
				break;
			case 'lbs_slick_adaptiveHeight':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'adaptiveHeight: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"adaptiveHeight": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"adaptiveHeight": false' : '';
				}
				break;
			case 'lbs_slick_arrows':
				// defaults: slick: true, plugin: false
				( $val ) ? '' : $slick_opts[] = 'arrows: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"arrows": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"arrows": false' : '';
				}
				break;
			case 'lbs_slick_autoplay':
				// defaults: slick: false, plugin: true
				( ! $val ) ? '' : $slick_opts[] = 'autoplay: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"autoplay": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"autoplay": false' : '';
				}
				break;
			case 'lbs_slick_autoplaySpeed':
				// defaults: slick: 3000, plugin: 3000
				( '3000' === $val ) ? '' : $slick_opts[] = 'autoplaySpeed: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"autoplaySpeed": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_centerMode':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'centerMode: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"centerMode": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"centerMode": false' : '';
				}
				break;
			case 'lbs_slick_centerPadding':
				// defaults: slick: 50px, plugin: 50px
				( $val ) ? $val = strtolower( preg_replace( '/\s*/', '', $val ) ) : '';
				( '50px' === $val ) ? '' : $slick_opts[] = 'centerPadding: "' . $val . '"';
				if ( ! empty( $options_post[ $opt ] ) ) {
					$options_post[ $opt ] = strtolower( preg_replace( '/\s*/', '', $options_post[ $opt ] ) );
					( $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"centerPadding": "' . $options_post[ $opt ] . '"' : '';
				}
				break;
			case 'lbs_slick_dots':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'dots: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"dots": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"dots": false' : '';
				}
				break;
			case 'lbs_slick_draggable':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'draggable: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"draggable": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"draggable": false' : '';
				}
				break;
			case 'lbs_slick_fade':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'fade: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"fade": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"fade": false' : '';
				}
				break;
			case 'lbs_slick_focusOnChange':
				// defaults: slick: false, plugin: true
				( ! $val ) ? '' : $slick_opts[] = 'focusOnChange: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"focusOnChange": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"focusOnChange": false' : '';
				}
				break;
			case 'lbs_slick_focusOnSelect':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'focusOnSelect: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"focusOnSelect": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"focusOnSelect": false' : '';
				}
				break;
			case 'lbs_slick_infinite':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'infinite: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"infinite": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"infinite": false' : '';
				}
				break;
			case 'lbs_slick_lazyLoad':
				// defaults: slick: ondemand, plugin: ondemand
				if ( ! empty( $options_post[ $opt ] ) ) {
					( 'ondemand' === $val ) ? '' : $slick_opts[] = 'lazyLoad: "' . $val . '"';
					( $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"lazyLoad": "' . $options_post[ $opt ] . '"' : '';
				}
				break;
			case 'lbs_slick_mobileFirst':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'mobileFirst: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"mobileFirst": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"mobileFirst": false' : '';
				}
				break;
			case 'lbs_slick_pauseOnFocus':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'pauseOnFocus: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnFocus": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnFocus": false' : '';
				}
				break;
			case 'lbs_slick_pauseOnHover':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'pauseOnHover: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnHover": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnHover": false' : '';
				}
				break;
			case 'lbs_slick_pauseOnDotsHover':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'pauseOnDotsHover: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnDotsHover": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"pauseOnDotsHover": false' : '';
				}
				break;
			case 'lbs_slick_respondTo':
				// defaults: slick: window, plugin: slider
				( 'window' === $val ) ? '' : $slick_opts[] = 'respondTo: "' . $val . '"';
				if ( ! empty( $options_post[ $opt ] ) ) {
					( $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"respondTo": "' . $options_post[ $opt ] . '"' : '';
				}
				break;
			case 'lbs_slick_responsive':
				// defaults: slick: none, plugin: breakpoints defined
				if ( $val ) {
					$slick_opts[] = 'responsive: ' . $val;
				}
				if ( ! empty( $options_post[ $opt ] ) ) {
					( $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"responsive": "' . $options_post[ $opt ] . '"' : '';
				}
				break;
			case 'lbs_slick_rows':
				// defaults: slick: 1, plugin: 1
				( '1' === $val ) ? '' : $slick_opts[] = 'rows: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"rows": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_slidesPerRow':
				// defaults: slick: 1, plugin: 1
				( '1' === $val ) ? '' : $slick_opts[] = 'slidesPerRow: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"slidesPerRow": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_slidesToShow':
				// defaults: slick: 1, plugin: 6
				( '1' === $val ) ? '' : $slick_opts[] = 'slidesToShow: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"slidesToShow": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_slidesToScroll':
				// defaults: slick: 1, plugin: 1
				( '1' === $val ) ? '' : $slick_opts[] = 'slidesToScroll: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"slidesToScroll": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_speed':
				// defaults: slick: 300, plugin: 300
				( '300' === $val ) ? '' : $slick_opts[] = 'speed: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"speed": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_swipe':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'swipe: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"swipe": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"swipe": false' : '';
				}
				break;
			case 'lbs_slick_swipeToSlide':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'swipeToSlide: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"swipeToSlide": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"swipeToSlide": false' : '';
				}
				break;
			case 'lbs_slick_touchMove':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'touchMove: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"touchMove": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"touchMove": false' : '';
				}
				break;
			case 'lbs_slick_touchThreshold':
				// defaults: slick: 5, plugin: 5
				( '5' === $val ) ? '' : $slick_opts[] = 'touchThreshold: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"touchThreshold": ' . $options_post[ $opt ] : '';
				break;
			case 'lbs_slick_useCSS':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'useCSS: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"useCSS": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"useCSS": false' : '';
				}
				break;
			case 'lbs_slick_useTransform':
				// defaults: slick: true, plugin: false
				( $val ) ? '' : $slick_opts[] = 'useTransform: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"useTransform": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"useTransform": false' : '';
				}
				break;
			case 'lbs_slick_variableWidth':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'variableWidth: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"variableWidth": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"variableWidth": false' : '';
				}
				break;
			case 'lbs_slick_vertical':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'vertical: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"vertical": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"vertical": false' : '';
				}
				break;
			case 'lbs_slick_verticalSwiping':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'verticalSwiping: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"verticalSwiping": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"verticalSwiping": false' : '';
				}
				break;
			case 'lbs_slick_rtl':
				// defaults: slick: false, plugin: false
				( ! $val ) ? '' : $slick_opts[] = 'rtl: true';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"rtl": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"rtl": false' : '';
				}
				break;
			case 'lbs_slick_waitForAnimate':
				// defaults: slick: true, plugin: true
				( $val ) ? '' : $slick_opts[] = 'waitForAnimate: false';
				if ( isset( $options_post[ $opt ] ) ) {
					( ! $val && $options_post[ $opt ] ) ? $slick_data_atts[] = '"waitForAnimate": true' : '';
					( $val && ! $options_post[ $opt ] ) ? $slick_data_atts[] = '"waitForAnimate": false' : '';
				}
				break;
			case 'lbs_slick_zindex':
				// defaults: slick: 1000, plugin: 1000
				( '1000' === $val ) ? '' : $slick_opts[] = 'zIndex: ' . $val;
				( ! empty( $options_post[ $opt ] ) && $val !== $options_post[ $opt ] ) ? $slick_data_atts[] = '"zIndex": ' . $options_post[ $opt ] : '';
				break;
		}
	}

	$slick = array();

	// Convert arrays to comma-delimited strings
	$slick['opts'] = implode( ",\n", $slick_opts );
	$slick['atts'] = implode( ", ", $slick_data_atts );

	return $slick;
}

//==================================================
// Assemble Bookshelf HTML & JS
//==================================================

function lbs_shelveBooks( $post_id, $widget = false ) {
	// Get saved or default global options
	$options = lbs_get_opts();

	// Get the item ID type from the database
	$item_id_type = get_post_meta( $post_id, 'item_id_type', true );
	if ( empty( $item_id_type ) ) {
		$item_id_type = 'isbn';
	}

	// Get item meta from database
	$itemID_meta = get_post_meta( $post_id, 'isbn', true );
	$alt_meta = get_post_meta( $post_id, 'alt', true );

	// Combine item meta arrays into a multidimensional array 
	$items_meta = array_combine( array_keys( $itemID_meta ), array_map( null, $itemID_meta, $alt_meta ) );

	// Get the shuffle_items option from the database
	$shuffle_items = get_post_meta( $post_id, 'shuffle_items', true );

	// Shuffle if true
	if( true == $shuffle_items ) {
		shuffle($items_meta);
	}

	$ebooks_meta = get_post_meta( $post_id, 'ebooks', true );

	// Get the link activation status from post meta
	$disable_links = get_post_meta( $post_id, 'disable_links', true );

	// Get the post API.
	$wsapi = esc_html( get_post_meta( $post_id, 'wsapi', true ) );

	// Get post override Slick options
	$post_slick_opts = get_post_meta( $post_id, 'settings', true );

	// Parse Slick options
	$slick = lbs_get_slick_opts( $options, $post_slick_opts );

	// Extract script initialization options
	$slick_opts = $slick['opts'];

	// Extract data attribute options
	$slick_atts = $slick['atts'];

	// Get catalog settings
	$cat = lbs_get_catalog_settings();

	// If the ebook option is set in the post editor, modify the catalog settings accordingly.
	switch ( $ebooks_meta ) {
		case 'cloudlibrary':
			// Skip if the catalog system is already set to cloudLibrary
			if ( $cat['sys'] === 'cloudlibrary' ) { break; }
			$cat['sys'] = 'cloudlibrary';
			break;
		case 'overdrive':
			// Skip if the catalog system is already set to Overdrive
			if ( $cat['sys'] === 'overdrive' ) { break; }
			$cat['sys'] = 'overdrive';
			break;
		case 'hoopla':
			$cat['sys'] = 'hoopla';
			break;
	}

	// Set unique bookshelf class
	$shelf_class = 'bookshelf-' . $post_id;

	// Modify bookshelf class if rendering in a widget. Avoids error if the bookshelf is rendered in a post/page and in a widget at the same time.
	if ( $widget ) {
		$shelf_class = 'widget-' . $shelf_class;
	}

	// Assemble Bookshelf HTML
	$html = "<div class='bookshelf " . $shelf_class . "' id='bookshelf-" . $post_id . "' " . ( $slick_atts ? "data-slick='{" . $slick['atts'] . "}'" : "" ) . ">\n";

	if ( empty( $itemID_meta ) ) {
		echo 'There are no items on this Bookshelf';
	} else {
		// Run though items and fill in item numbers and alt text
		$item_count = count( $itemID_meta );

		for ( $i = 0; $i < $item_count; $i++ ) {
			$itemID = $items_meta[ $i ][0];

			// Get alt text for item if set and trim white space, otherwise make it blank
			( isset( $items_meta[ $i ][1] ) ? $alt = trim( $items_meta[ $i ][1] ) : $alt = '' );

			// Make image URL
			$cat_img_url = lbs_get_img_url( $cat, $itemID, $post_id );
			
			$cat_img = "<img src='". $cat_img_url . "' alt='" . $alt . "'>";
			
			// Make catalog link, or not
			if ( empty( $disable_links ) ) {
				switch ( $cat['sys'] ) {
					case 'bibliocommons':
						$cat_url = $cat['domain'] . "/search?t=smart&search_category=keyword&q=" . $itemID . "&commit=Search";
						break;
					case 'calibre':
						$cat_url = $cat['domain'] . "/#book_id=" . $itemID . "&library_id=" . $cat['profile'] . "&panel=book_details";
						break;
					case 'cloudlibrary':
						if ( $cat['cloudlibrary'] ) {
								$cat_url = $cat['cloudlibrary'] . "/Search/" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/Search/" . $itemID;
							}
						break;
					case 'cops':
						if ( $cat['profile'] ) {
							$cat_url = $cat['domain'] . "/" . $cat['profile'] . "/index.php?page=13&id=" . $itemID;
						} else {
							$cat_url = $cat['domain'] . "/index.php?page=13&id=" . $itemID;
						}
						break;
					case 'dbtextworks':
						$cat_url = $cat['domain'] . "/list?q=identifier_free%3A(" . $itemID . ")";
						break;
					case 'ebsco_eds':
						$cat_url = $cat['protocol'] . "search.ebscohost.com/login.aspx?direct=true&scope=site&authtype=ip,guest&custid=" . $cat['profile'] . "&profile=eds&groupid=main&AN=" . $itemID;
						break;
					case 'encore':
						$cat_url = $cat['domain'] . "/iii/encore/search/C__S" . $itemID . "?lang=eng";
						break;
					case 'evergreen':
						$cat_url = $cat['domain'] . "/eg/opac/results?query=" . $itemID . "&qtype=identifier";
						
						if ( $cat['profile'] ) {
							$cat_url .= "&locg=" .  $cat['profile'];
						}
						break;
					case 'evergreen-record':
						$cat_url = $cat['domain'] . "/eg/opac/record/" . $itemID;

						if ( $cat['profile'] ) {
							$cat_url .= "?locg=" .  $cat['profile'];
						}
						break;
					case 'hoopla':
						$cat_url = "https://hoopladigital.com/search?isbn=" . $itemID;
						break;
					case 'koha':
						$cat_url = $cat['domain'] . "/cgi-bin/koha/opac-search.pl?q=" . $itemID;
						break;
					case 'openlibrary':
						$cat_url = "https://openlibrary.org/isbn/" . $itemID;
						break;
					case 'overdrive':
						if ( $cat['overdrive'] ) {
							$cat_url = $cat['overdrive'] . "/search/title?isbn=" . $itemID;
						} else {
							$cat_url = $cat['domain'] . "/search/title?isbn=" . $itemID;
						}
						break;
					case 'pika':
						if ( $wsapi === 'pika-api' ) {
							$cat_url = $cat['domain'] . "/GroupedWork/" . $itemID;
						} else {
							$cat_url = $cat['domain'] . "/Search/Results?lookfor0[]=" . $itemID . "&type0[]=ISN";
						}
						break;
					case 'polaris':
						if ( $cat['profile'] ) {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/polaris/view.aspx?ctx=" . $cat['profile'] . "&UPC=" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/polaris/view.aspx?ctx=" . $cat['profile'] . "&ISBN=" . $itemID;
							}
						} else {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/polaris/view.aspx?UPC=" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/polaris/view.aspx?ISBN=" . $itemID;
							}
						}
						break;
					case 'polaris63':
						if ( $cat['profile'] ) {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/view.aspx?ctx=" . $cat['profile'] . "&UPC=" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/view.aspx?ctx=" . $cat['profile'] . "&ISBN=" . $itemID;
							}
						} else {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/view.aspx?UPC=" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/view.aspx?ISBN=" . $itemID;
							}
						}
						break;
					case 'primo':
						// Change URL subdirectory based on whether the catalog is hosted on exlibrisgroup.com or is self-hosted by the institution.
						if ( false === strpos( $cat['domain'], 'exlibrisgroup' ) ) {
							$cat_url = $cat['domain'] . '/discovery';
						} else {
							$cat_url = $cat['domain'] . '/primo-explore';
						}
						
						$cat_url .= '/search?query=any,contains,' . $itemID . '&tab=default_tab&search_scope=default_scope&vid=' . $cat['profile'];
						break;
					case 'sirsi_ent':
						if ( $cat['profile'] ) {
							$cat_url = $cat['domain'] . "/client/" . $cat['profile'] . "/search/results?qu=" . $itemID;
						} else {
							$cat_url = $cat['domain'] . "/client/default/search/results?qu=" . $itemID;
						}
						break;
					case 'sirsi_horizon':
						$cat_url = $cat['domain'] . "/ipac20/ipac.jsp?index=ISBNEX&term=" . $itemID . "&te=ILS&rt=ISBN|||ISBN|||false";
						break;
					case 'spydus':
						$cat_url = $cat['domain'] . "/cgi-bin/spydus.exe/ENQ/WPAC/BIBENQ?SETLVL=&SBN=" . $itemID;
						break;
					case 'tlc':
						$cat_url = $cat['domain'] . "/?section=search&term=" . $itemID;
						break;
					case 'tlc_ls1':
						if ( $cat['profile'] ) {
							$cat_url = $cat['domain'] . "/TLCScripts/interpac.dll?Search&Config=" . $cat['profile'] . "&SearchType=1&SearchField=4096&SearchData=" . $itemID;
						} else {
							$cat_url = $cat['domain'] . "/TLCScripts/interpac.dll?Search&Config=pac&SearchType=1&SearchField=4096&SearchData=" . $itemID;
						}
						break;
					case 'webpac':
						if ( $cat['profile'] ) {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/search/?searchtype=X&searcharg=" . $itemID . "+&searchscope=" . $cat['profile'];
							} else {
								$cat_url = $cat['domain'] . "/search~S" . $cat['profile'] . "/i" . $itemID;
							}
						} else {
							if ( strlen( $itemID ) === 12 ) {
								$cat_url = $cat['domain'] . "/search/?searchtype=X&searcharg=" . $itemID;
							} else {
								$cat_url = $cat['domain'] . "/search/i" . $itemID;
							}
						}
						break;
					case 'worldcat':
						$cat_url = "https://www.worldcat.org/search?q=" . $itemID;
						break;
					case 'worldcatds':
						$cat_url = $cat['domain'] . "/search?queryString=" . $itemID;
						break;
				}

				$html .= "<div><a href='" . $cat_url . "' target='_blank'>" . $cat_img . "</a></div>\n";
			} else {
				// Create item images without links
				$html .= "<div>" . $cat_img . "</div>\n";
			}
		}
	}

	// Make slick initialization script
	$html .= "</div>\n
<script type='text/javascript'>
jQuery(document).ready(function(){
	jQuery('." . $shelf_class . "').slick({" . $slick_opts . "});
});
</script>";

	return $html;
}

//==================================================
// Create Bookshelf image URLs
//==================================================

function lbs_get_img_url( $cat, $itemID, $post_id ) {
	// Get the post API.
	$wsapi = esc_html( get_post_meta( $post_id, 'wsapi', true ) );

	// Check for Koha bibliobnumbers and change image URL
	if ( $cat['sys'] === 'koha' && strlen( $itemID ) < 10 ) {
		$cat['cdn'] = 'koha';
	}

	switch ( $cat['cdn'] ) {
		case 'amazon':
			$cat_img_url = "https://images-na.ssl-images-amazon.com/images/P/" . $itemID . ".jpg";
			break;
		case 'calibre':
			$cat_img_url = $cat['domain'] . "/get/thumb/" . $itemID . "/" . $cat['profile'] . "?sz=300x400";
			break;
		case 'cops':
			if ( $cat['profile'] ) {
				$cat_img_url = $cat['domain'] . "/" . $cat['profile'] .  "/fetch.php?id=" . $itemID;
			} else {
				$cat_img_url = $cat['domain'] . "/fetch.php?id=" . $itemID;
			}
			break;
		case 'chilifresh':
			$cat_img_url = "https://content.chilifresh.com/?isbn=" . $itemID . "&size=L";
			break;
		case 'contentcafe':
			if ( $cat['CDN_id'] ) {
				$cat_img_url = "https://contentcafe2.btol.com/ContentCafe/Jacket.aspx?UserID=" . $cat['CDN_id'] . "&Password=" . $cat['CDN_pass'] . "&Return=T&Type=M&Value=" . $itemID;
			} else {
				$cat_img_url = "https://contentcafe2.btol.com/ContentCafe/Jacket.aspx?UserID=ContentCafeClient&Password=Client&Return=T&Type=M&Value=" . $itemID;
			}
			break;
		case 'ebsco':
			$cat_img_url = "https://rps2images.ebscohost.com/rpsweb/othumb?id=NL$" . $itemID . "&#36EPUB&s=l";
			break;
		case 'encore':
			if ( strlen( $itemID ) === 12 ) {
				$cat_img_url = $cat['domain'] . "/iii/encore/home?image_size=thumb&isxn=&lang=eng&service=BibImage&suite=gold&suite_code=gold+(locked)&upc=" . $itemID;
			} else {
				$cat_img_url = $cat['domain'] . "/iii/encore/home?image_size=thumb&isxn=" . $itemID . "&lang=eng&service=BibImage&suite=gold&suite_code=gold+(locked)&upc=";
			}
			break;
		case 'evergreen':
			$cat_img_url = $cat['domain'] . "/opac/extras/ac/jacket/medium/" . $itemID;
			break;
		case 'evergreen-record':
			$cat_img_url = $cat['domain'] . "/opac/extras/ac/jacket/medium/r/" . $itemID;
			break;
		case 'koha':
			$cat_img_url = $cat['domain'] . "/cgi-bin/koha/opac-image.pl?biblionumber=" . $itemID;
			break;
		case 'openlibrary':
			// Check for OpenLibrary OLID, otherwise look up ISBN
			if ( false !== stripos( $itemID, 'OL' ) ) {
				$cat_img_url = "https://covers.openlibrary.org/b/olid/" . $itemID . "-M.jpg";
			} else {
				$cat_img_url = "https://covers.openlibrary.org/b/isbn/" . $itemID . "-M.jpg";
			}
			break;
		case 'pika':
			if ( $wsapi === 'pika-api' ) {
				$cat_img_url = $cat['domain'] . "/bookcover.php?id=" . $itemID . "&size=medium&type=grouped_work";
			} else {
				$cat_img_url = $cat['domain'] . "/bookcover.php?size=large&isn=" . $itemID . "&upc=" . $itemID;
			}
			break;
		case 'syndetics':
			if ( strlen( $itemID ) === 12 ) {
				$item_id_type = 'upc';
			} else {
				$item_id_type = 'isbn';
			}
			
			$cat_img_url = "https://syndetics.com/index.aspx?" . $item_id_type . "=" . $itemID . "/LC.GIF";
			
			if ( $cat['CDN_id'] ) {
				$cat_img_url = $cat_img_url . "&client=" . $cat['CDN_id'];
			}
			break;
		case 'tlc':
			$cat_img_url = "https://ls2content.tlcdelivers.com/tlccontent?customerid=" . $cat['CDN_id'] . "&appid=ls2pac&requesttype=BOOKJACKET-MD&Isbn=" . $itemID;
	}
	
	return $cat_img_url;
}

//==================================================
// Update Bookshelf items from API
//==================================================

function lbs_update_items_from_api( $post_id ) {
	$meta = get_post_meta( $post_id );

	foreach ( $meta as $k => $v ) {
		$meta[ $k ] = $v[0];
	}

	lbs_get_items_from_api( $post_id, $meta );
}

//==================================================
// Get items from API and (re)schedule post updates
//==================================================

function lbs_get_items_from_api( $post_id, $api_meta ) {
	$cat = lbs_get_catalog_settings();
	update_post_meta( $post_id, 'wsapi', $api_meta['wsapi'] );
	update_post_meta( $post_id, 'ws-key', $api_meta['ws-key'] );
	update_post_meta( $post_id, 'ws-secret', $api_meta['ws-secret'] );
	update_post_meta( $post_id, 'ws-token-url', $api_meta['ws-token-url'] );
	update_post_meta( $post_id, 'ws-request', $api_meta['ws-request'] );
	update_post_meta( $post_id, 'ws-json', $api_meta['ws-json'] );
	update_post_meta( $post_id, 'item_id_type', $api_meta['item_id_type'] );
	update_post_meta( $post_id, 'schedule', $api_meta['schedule'] );
	
	// wp_remote_get args
	$wprg_args = array( 'timeout' => 30 );

	$itemIDs = array();
	$alts = array();

	// Process web service request result into meta
	switch ( $api_meta['wsapi'] ) {
		case 'cops-api':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );
			$response = json_decode( $response['body'], true );

			// Stop on 0 results
			if ( ! $response['entries'] || $response['containsBook'] === 0 ) {
				break;
			}

			foreach ( $response['entries'] as $title ) {
				$itemIDs[] = $title['book']['id'];
				$alt = $title['title'];
				
				if ( ! empty ( $title['book']['authorsName'] ) ) {
					$alt .= ' by ' . $title['book']['authorsName'];
				}
				
				$alts[] = $alt;
			}

			if ( $response['multipleDatabase'] ) {
				foreach ( $itemIDs as &$itemID ) {
					$itemID .= "&db=" . $response['databaseId'];
				}
				unset( $itemID );
			}
			break;

		case 'eg-supercat':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );

			// Stop on error
			if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
				break;
			}

			// Convert XML into SimpleXML object
			$xml = simplexml_load_string( $response['body'] );
			
			foreach ( $xml->mods as $title ) {
				// Get item title
				$alt = lbs_trim_item_title( (string)$title->titleInfo->title );

				// Prepend a nonsorted title word (e.g. "The") if it exists
				if ( isset( $title->titleInfo->nonSort ) ) {
					$alt = (string)$title->titleInfo->nonSort . $alt;
				}

				// Append the author/producer from the first note field if it exists
				if ( ! empty ( $title->note[0] ) ) {
					$alt .= ' by ' . (string)$title->note[0];
				}

				// Get record numbers if catalog is set to evergreen-record, otherwise get ISBNs
				if ( $cat['sys'] === 'evergreen-record' ) {
					$itemIDs[] = lbs_trim_item_id( (string)$title->recordInfo->recordIdentifier[0] );
				} else {
					// Look for a UPC#, which is preferable for items with UPCs, e.g. DVDs
					$identifiers = (array)$title->identifier;

					// Throw out the @attributes element. We only want identifiers.
					unset( $identifiers['@attributes'] );

					// Find a 12-digit UPC number among the identifiers
					$item_id = preg_grep( "/^\d{12}$/", $identifiers );
					if( ! $item_id ) {
						// If no UPC find the 1st ISBN10 or ISBN13 that's not all 0s
						$item_id = preg_grep( "/^(\d{10}|\d{13})$(?<!0000000000)/", $identifiers );
					}

					// Reindex array
					$item_id = array_merge( $item_id );

					if( $item_id ) {
						$itemIDs[] = (string)$item_id[0];
					} else {
						$itemIDs[] = '';
					}
				}
				$alts[] = $alt;
			}
			break;

		case 'json':
		case 'koha-rws':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );

			// Stop on 0/bad results
			if ( is_wp_error( $response ) ) {
				break;
			}

			$response = json_decode( $response['body'], true );

			foreach ( $response as $title ) {
				$itemID = explode ( " | ", $title[0] );
				$itemIDs[] = $itemID[0];

				if ( isset ( $title[1] ) ) {
					$alt = $title[1];

					if ( isset ( $title[2] ) ) {
						$alt .= ' by ' . $title[2];
					}	
				} else {
					$alt = '';
				}

				$alts[] = $alt;
			}

			break;

		case 'koha-rss':
			// Build SimplePie object from RSS feeed
			$feedURL = urldecode( $api_meta['ws-request'] );
			$feed = fetch_feed( $feedURL );

			// Stop on 0/bad results
			if ( is_wp_error( $feed ) ) {
				break;
			}

			$items = $feed->get_items();

			foreach( $items as $item ) {
				// Get the description field from the feed item
				$description = $item->get_description();

				// Get the dc:identifier tag for the feed item
				$dcidentifier = $item->get_item_tags( 'http://purl.org/dc/elements/1.1/', 'identifier' );

				$item_id = array();

				// Look for item ID in image URL
				switch( $cat['cdn'] ) {
					case 'amazon':
						preg_match( "/(?<=[pP]\/)(\d+)/", $description, $item_id );
						break;
					case 'contentcafe':
						preg_match( "/(?<=[vV]alue=)(\d+[xX]?)/", $description, $item_id );
						break;
					case 'syndetics':
						preg_match( "/(?<=isbn=)(\d+[xX]?)/", $description, $item_id );

						if ( empty( $item_id ) ) {
							preg_match( "/(?<=upc=)(\d+)/", $description, $item_id );
						}
						break;
				}

				// If no item ID is found in a feed item image URL...
				if ( empty( lbs_trim_item_id( $item_id ) ) ) {
					// ...look in the dc:identifier XML tag
					if ( $dcidentifier ) {
						$item_id = explode( '|', $dcidentifier[0]['data'] );
						// Trim each identifier
						foreach( $item_id as &$id ) {
							$id = lbs_trim_item_id( $id );
						}
					}

					// If no identifier is in the dc:identifier XML tag, get the biblionumber
					if ( empty( $item_id[0] ) ) {
						$item_link = $item->get_link();
						preg_match( "/(?<=biblionumber=)\d+/i", $item_link, $item_id );
					}
				}

				$itemIDs[] = $item_id[0];
				$alts[] = lbs_trim_item_title( $item->get_title() );
			}
			break;

		case 'nytbooks':
			$response = wp_remote_get( $api_meta['ws-request'] . '?api-key=' . $api_meta['ws-key'], $wprg_args );
			$response = json_decode( $response['body'], true );

			// Stop on error status
			if ( ! $response['status'] === "OK" ) {
				break;
			}

			foreach ( $response['results']['books'] as $title ) {
				if ( ! empty ( $title['primary_isbn13'] ) ) {
					$itemIDs[] = $title['primary_isbn13'];
				} else {
					$itemIDs[] = $title['primary_isbn10'];
				}

				$alt = $title['title'];

				if ( ! empty ( $title['author'] ) ) {
					$alt .= ' by ' . $title['author'];
				}

				$alts[] = $alt;
			}
			break;

		case 'openlibrary':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );
				
			// Stop if list is invalid
			if ( is_wp_error( $response ) ) {
				break;
			}
			
			$response = json_decode( $response['body'], true );

			// Check if returned list has no items
			if ( empty( $response['work_count'] ) && empty( $response['size'] ) && empty( $response['numFound'] ) ) {
				break;
			}

			// Look for works (subject lists), entries (user-created lists), or docs (author.
			if ( isset( $response['works'] ) ) {
				$items = $response['works'];
			} else if ( isset( $response['entries'] ) ) {
				$items = $response['entries'];
			} else if ( isset( $response['docs'] ) ) {
				$items = $response['docs'];
			}

			foreach ( $items as $item ) {
				// If the work has a cover edition key, use that instead of the work key.
				if ( isset( $item['cover_edition_key'] ) ) {
					$OLID = $item['cover_edition_key'];
				} else {
					if ( isset( $item['key'] ) ) {
						$OLID = ltrim( $item['key'], '/works/' );
					} else {
						// If no key, use the url
						$OLID = ltrim( $item['url'], '/books/' );
					}
				}

				$itemIDs[] = $OLID;
				$alt = $item['title'];

				if ( isset( $item['authors'] ) ) {
					$alt .= ' by ' . $item['authors'][0]['name'];
				} else if ( isset( $item['author_name'] ) ) {
					$alt .= ' by ' . $item['author_name'][0];
				}

				$alts[] = $alt;
			}
			break;

		case 'pika-api':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );
			$response = json_decode( $response['body'], true );

			// Stop if list ID is invalid
			if ( ! $response['result']['success'] ) {
				break;
			}

			foreach ( $response['result']['titles'] as $title ) {
				$itemIDs[] = $title['id'];
				$alt = $title['title'];

				if ( ! empty ( $title['author'] ) ) {
					$alt .= ' by ' . $title['author'];
				}

				$alts[] = $alt;
			}
			break;

		case 'symws':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );

			// Stop on error
			if ( is_wp_error( $response ) ) {
				break;
			}

			$response = json_decode( $response['body'], true );

			switch ( $api_meta['item_id_type'] ) {
				case 'upc':
					foreach ( $response['HitlistTitleInfo'] as $item ) {
						if ( strlen( $item['UPC'][ 0 ] ) !== 0 ) {
							$itemIDs[] = $item['UPC'][ 0 ];
							$alt = $item['title'];

							if ( ! empty( $item['author'] ) ) {
								$alt .= ' by ' . $item['author'];
							}

							$alts[] = $alt;
						}
					}
					break;
				case 'isbn':
				default:
					foreach ( $response['HitlistTitleInfo'] as $item ) {
						if ( strlen( $item['ISBN'][ 0 ] ) !== 0 ) {
							$itemIDs[] = $item['ISBN'][ 0 ];
							$alt = $item['title'];
							if ( ! empty( $item['author'] ) ) {
								$alt .= ' by ' . $item['author'];
							}
							$alts[] = $alt;
						}
					}
					break;
			}
			break;

		case 'sierra-api':
			$items = lbs_get_sierra_api_data( $api_meta['ws-token-url'], $api_meta['ws-request'], $api_meta['ws-json'], $api_meta['ws-key'], $api_meta['ws-secret'] );

			foreach ( $items as $item ) {
				$itemIDs[] = $item['id'];
				$alts[] = $item['alt'];
			}
			break;

		case 'tlcls2pac':
			$response = wp_remote_get( $api_meta['ws-request'], $wprg_args );

			// Stop on error
			if ( is_wp_error( $response ) ) {
				break;
			}

			$response = json_decode( $response['body'], true );

			foreach ( $response['resources'] as $item ) {
				$standardNumber = ( isset( $item['standardNumbers'][0]['data'] ) ? $item['standardNumbers'][0]['data'] : '');

				// Skip item if first standard number is a UPC or blank
				if ( strlen( $standardNumber ) !== 12 || ! empty( $standardNumber ) ) {
					$itemIDs[] = $standardNumber;
					$alts[] = $item['shortTitle'];
				}
			}
			break;
	}

	if ( $itemIDs ) {
		// Remove duplicates
		// Find uniques
		$items_unique = array_unique( $itemIDs );
		$alts_unique = array_unique( $alts );

		// Find duplicates
		$item_dupes = array_diff_assoc( $itemIDs, $items_unique );
		$alt_dupes = array_diff_assoc( $alts, $alts_unique );

		// Get duplicates keys
		$item_dupe_keys = array_keys( $item_dupes );
		$alt_dupe_keys = array_keys( $alt_dupes );

		// Merge duplicate key arrays
		$dupe_keys = array_merge( $item_dupe_keys, $alt_dupe_keys );

		// If there are duplicate keys, remove those keys from item and alt arrays
		if ( ! empty( $dupe_keys ) ) {
			foreach( $dupe_keys as $key ) {
				unset( $itemIDs[$key] );
				unset( $alts[$key] );
			}
		}

		// Escape alt text for use in HTML
		$alts = array_map( 'esc_attr', $alts );

		// Set image size threshold and file types for automatic item removal
		switch ( $cat['cdn'] ) {
			case 'contentcafe':
				// Baker & Taylor ContentCafe default placeholder images are no longer smaller than cover art.
				// Cover art images are JPG, placeholders are PNG or 80px wide JPGs.
				$x_dim = 80;
				$exclude_type = 'png';
				break;
			case 'syndetics':
				$x_dim = 24;
				$exclude_type = 'none';
				break;
			default:
				$x_dim = 1;
				$exclude_type = 'none';
				break;
		}

		// Remove item ID if it returns an image with x-dimension <= $min_x
		$items = lbs_weed_invalid_images( $cat, $post_id, $itemIDs, $alts, $x_dim, $exclude_type );
		$itemIDs = $items['ID'];
		$alts = $items['alt'];
	}
	
	// Process items into post meta
	update_post_meta( $post_id, 'isbn', $itemIDs );
	update_post_meta( $post_id, 'alt', $alts );

	// Schedule Bookshelf updates
	// If update is scheduled, unschedule
	lbs_unschedule_event( $post_id );

	// Schedule if user selection is not 'none'
	$schedule = $api_meta['schedule'];

	if ( 'none' !== $schedule ) {
		$hook = 'update_bookshelf';
		$args = array( strval( $post_id ) );
		$schedules = wp_get_schedules();
		$next_run = time() + $schedules[ $schedule ]['interval'];
		wp_schedule_event( $next_run, $schedule, $hook, $args );
	}
}

//==================================================
// Process Sierra API OAuth 2.0 requests
//==================================================

function lbs_get_sierra_api_data( $token_url, $request_url, $json_query, $key, $secret ) {
	// Generate authorization key
	$authkey = base64_encode( "$key:$secret" );

	// Request access token
	$header = array( 'Authorization' => 'Basic ' . $authkey );
	$body = array( 'grant_type' => 'client_credentials' );
	$response = wp_remote_post(
		$token_url,
		array(
			'headers' => $header,
			'body'    => $body,
		)
	);

	$items = array();

	if ( ! is_wp_error( $response ) && $response['response']['code'] == '200' ) {
		$access_token = json_decode( $response['body'] )->access_token;

		// Send initial query to get bibids
		$header = array(
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type'  => 'application/json;charset=UTF-8',
		);

		$body = stripslashes( $json_query );
		$response = wp_remote_post(
			$request_url,
			array(
				'headers' => $header,
				'body'    => $body,
			)
		);

		if ( ! is_wp_error( $response ) ) {
			$json = json_decode( $response['body'] );

			// Request MARC data for each item returned
			$header = array(
				'Authorization' => 'Bearer ' . $access_token,
				'Accept'        => 'application/marc-in-json',
			);

			//Roll through each item in the query response.
			foreach ( $json->entries as $item ) {
				$item_url = $item->link . '/marc';

				$response = wp_remote_get(
					$item_url,
					array( 'headers' => $header )
				);

				// Decode JSON as array.
				$json = json_decode( $response['body'], true );

				if ( isset( $json['fields'] ) ) {
					// Get ISBNs in record.
					$marc_isbn = array_column( $json['fields'], '020' );

					// Get UPCs in record.
					$marc_upc = array_column( $json['fields'], '024' );
				}

				$itemID = '';

				//Get the first ISBN in the record, stop when found
				foreach ( $marc_isbn as $isbn ) {
					if ( ! empty( $isbn['subfields'][0]['a'] ) ) {
						$itemID = lbs_trim_item_id( $isbn['subfields'][0]['a'] );
						break;
					}
				}

				// If no ISBN found in record get the first UPC
				if ( empty( $itemID ) ) {
					foreach ( $marc_upc as $upc ) {
						if ( ! empty( $upc['subfields'][0]['a'] ) ) {
							$itemID = lbs_trim_item_id( $upc['subfields'][0]['a'] );
							break;
						}
					}
				}

				// Skip items with no ISBN or UPC.
				if ( isset( $itemID ) ) {
					// Use the MARC title for the cover image alt attribute.
					$alt = '';

					if ( isset( $json['fields'] ) ) {
						$marc_title = array_column( $json['fields'], '245' );

						if ( ! empty( $marc_title ) ) {
							$alt = lbs_trim_item_title( $marc_title[0]['subfields'][0]['a'] );
						}
					}

					// Check for duplicate IDs and skip them.
					if ( ! in_array( $itemID, array_column( $items, 'id' ), true ) ) {
						$items[] = array(
							'id'  => $itemID,
							'alt' => $alt,
						);
					}
				}
			}
		}
	} else {
		// Create empty items array
		$items[] = array(
			'id'  => '',
			'alt' => '',
		);
	}

	return $items;
}

//==================================================
// Trim item titles at : or / or line feed
//==================================================

function lbs_trim_item_title( $title ) {
	$title = preg_split( '/\r\n|\r|\n/', $title );
	$trimmed = rtrim( $title[0], ' \/\:' );
	return $trimmed;
}

//==================================================
// Trim item identifiers
//==================================================

function lbs_trim_item_id( $item_id, $type = 'isbn' ) {
	switch( $type ) {
		case 'isbn':
			// Catch all leading non-ISBN characters
			$regex = '/[^0-9Xx]/';
			break;		
		default:
			// Catch all leading non-numeric characters
			$regex = '/[^0-9]/';
			break;
	}

	$item_id = preg_replace( $regex, '', $item_id );
	return $item_id;
}

//=========================================================
// Remove itemIDs with invalid images from API response
//=========================================================

function lbs_weed_invalid_images( $cat, $post_id, $itemIDs, $alts, $x_dim, $exclude_type) {
	foreach ( $itemIDs as $itemID ) {
		// Create image object from url
		$cat_img_url = lbs_get_img_url( $cat, $itemID, $post_id );
		$img = wp_get_image_editor( $cat_img_url );
		$mime_type = wp_remote_retrieve_header( wp_remote_get( $cat_img_url ), 'content-type' );
		
		$key = array_search( $itemID, $itemIDs );

		// Clear the item ID and alt text if the image URL returns an error, is too small, or is the wrong image type
		if ( is_wp_error( $img ) ) {
			unset( $itemIDs[$key] );
			unset( $alts[$key] );
		} else {
			$img_size = $img->get_size();
			// Compare image width to the too-small dimension
			// Check if item ID is equivalent to 0 (SuperCat will do this if identifier does not exist
			// Check image mime type against blank placeholder image mime type
			if( $img_size['width'] <= $x_dim || $itemID === 0 || false !== strpos( $mime_type, $exclude_type ) ) {
				unset( $itemIDs[$key] );
				unset( $alts[$key] );
			}
		}
	}

	// Remove empty indices and reindex array
	$items = array();
	$items['ID'] =  array_merge( $itemIDs );
	$items['alt'] = array_merge( $alts );
	return $items;
}

//==================================================
// Unschedule 'update_bookshelf' cron job for a post
//==================================================

function lbs_unschedule_event( $post_id ) {
	$hook = 'update_bookshelf';
	$args = array( strval( $post_id ) );
	$timestamp = wp_next_scheduled( $hook, $args );

	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, $hook, $args );
	}
}

//==================================================
// Add cron intervals
//==================================================

function lbs_add_cron_intervals( $schedules ) {
	$schedules['weekly'] = array(
		'interval' => 7 * DAY_IN_SECONDS,
		'display'  => esc_html__( 'Once Weekly' ),
	);

	$schedules['monthly'] = array(
		'interval' => 30 * DAY_IN_SECONDS,
		'display'  => esc_html__( 'Once Monthly' ),
	);

	return $schedules;
}

//==================================================
// Return catalog settings array
//==================================================
	
function lbs_get_catalog_settings() {
	$options = lbs_get_opts();
	$cat = array();

	// Get catalog protocol
	if ( isset( $options['lbs_cat_Protocol'] ) ) {
		$cat['protocol'] = $options['lbs_cat_Protocol'];
	} else {
		$cat['protocol'] = 'https://';
	}

	// Get catalog protocol and domain name
	$cat['domain'] = $cat['protocol'] . $options['lbs_cat_DomainName'];

	// Get catalog system
	$cat['sys'] = $options['lbs_cat_System'];

	// Get catalog profile
	$cat['profile'] = $options['lbs_cat_Profile'];

	// Get catalog image CDN
	$cat['cdn'] = $options['lbs_cat_CDN'];

	// Get customer ID for image CDN
	if ( isset( $options['lbs_cat_CDN_id'] ) ) {
		$cat['CDN_id'] = $options['lbs_cat_CDN_id'];
	} else {
		$cat['CDN_id'] = '';
	}

	// Get customer password for image CDN
	if ( isset( $options['lbs_cat_CDN_pass'] ) ) {
		$cat['CDN_pass'] = $options['lbs_cat_CDN_pass'];
	} else {
		$cat['CDN_pass'] = '';
	}
	
	// Get cloudLibrary catalog domain
	if ( isset( $options['lbs_cat_cloudlibrary'] ) ) {
		$cat['cloudlibrary'] = 'https://' . $options['lbs_cat_cloudlibrary'];
	} else {
		$cat['cloudlibrary'] = '';
	}

	// Get Overdrive catalog domain
	if ( isset( $options['lbs_cat_overdrive'] ) ) {
		$cat['overdrive'] = 'https://' . $options['lbs_cat_overdrive'];
	} else {
		$cat['overdrive'] = '';
	}

	return $cat;
}

//==================================================
// Build settings object
//==================================================

function lbs_settings_obj() {
	$settings['catalog'] = array(
		'title'       => 'Catalog Settings',
		'description' => 'Want this plugin to support your catalog system? Email the <a href="mailto:lorangj@guilderlandlibrary.org" target="_blank">author</a>.<br><br><b>Note for EBSCOHost users:</b> Leave Catalog Domain blank and enter your Customer ID in the Catalog Profile field.<br><b>Note for Evergreen users:</b> You have the option to use ISBN, UPC, or record numbers to display items. Make the corresponding selections for Catalog System and Image CDN.',
		'fields'      => array(
			array(
				'id'          => 'cat_Protocol',
				'label'       => 'Catalog Protocol',
				'description' => '',
				'type'        => 'select',
				'options'     => array(
					'https://' => 'HTTPS',
					'http://'  => 'HTTP',
				),
				'default'     => 'https://',
			),
			array(
				'id'          => 'cat_System',
				'label'       => 'Catalog System',
				'description' => 'Select your ILS.<br><b>If you are using Polaris 6.3 or higher you may need to choose Polaris 6.3+ for your item links to work.</b>',
				'type'        => 'select',
				'options'     => array(
					'bibliocommons'    => 'BiblioCommons',
					'calibre'          => 'Calibre',
					'cloudlibrary'     => 'cloudLibrary',
					'cops'             => 'COPS: Calibre OPDS (and HTML) PHP Server',
					'dbtextworks'      => 'DB/Textworks',
					'ebsco_eds'        => 'EBSCOHost Discovery Service',
					'encore'           => 'Encore',
					'evergreen'        => 'Evergreen (ISBN/UPC)',
					'evergreen-record' => 'Evergreen (Record)',
					'primo'            => 'Ex Libris Primo',
					'hoopla'           => 'Hoopla',
					'koha'             => 'Koha',
					'openlibrary'      => 'OpenLibrary.org',
					'overdrive'        => 'Overdrive',
					'pika'             => 'Pika',
					'polaris'          => 'Polaris',
					'polaris63'        => 'Polaris 6.3+',
					'sirsi_ent'        => 'SirsiDynix Enterprise',
					'sirsi_horizon'    => 'SirsiDynix Horizon',
					'spydus'           => 'Spydus',
					'tlc_ls1'          => 'TLC Library System',
					'tlc'              => 'TLC LS2',
					'webpac'           => 'WebPAC PRO',
					'worldcat'         => 'WorldCat.org',
					'worldcatds'       => 'WorldCat Discovery Service',
				),
				'default'     => 'openlibrary',
			),
			array(
				'id'          => 'cat_DomainName',
				'label'       => 'Catalog Domain Name',
				'description' => '',
				'type'        => 'url',
				'default'     => 'openlibrary.org',
				'placeholder' => '',
				'callback'    => 'lbs_trim_url',
				'size'        => '40',
			),
			array(
				'id'          => 'cat_Profile',
				'label'       => 'Catalog Profile/Config',
				'description' => '<br>Enter your profile ID.<br>If your catalog is in a subdirectory of your catalog domain, enter the directory name here (e.g. library.system.domain/your_library).<br>If your catalog uses an OPAC config value or location ID to customize the interface, enter it here.<br>This is also where to enter your Calibre library ID.',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'cat_CDN',
				'label'       => 'Image CDN',
				'description' => 'Select the image CDN used by your catalog.',
				'type'        => 'select',
				'options'     => array(
					'amazon'           => 'Amazon',
					'contentcafe'      => 'Baker & Taylor',
					'calibre'          => 'Calibre',
					'cops'             => 'COPS: Calibre OPDS (and HTML) PHP Server',
					'chilifresh'       => 'ChiliFresh',
					'ebsco'            => 'EBSCO',
					'encore'           => 'Encore',
					'evergreen'        => 'Evergreen (ISBN/UPC)',
					'evergreen-record' => 'Evergreen (Record)',
					'openlibrary'      => 'OpenLibrary.org',
					'pika'             => 'Pika',
					'syndetics'        => 'Syndetics',
					'tlc'              => 'TLC',
				),
				'default'     => 'openlibrary',
			),
			array(
				'id'          => 'cat_CDN_id',
				'label'       => 'CDN ID/Username',
				'description' => '<br>Enter the credentials for your image CDN, if required.<br>Syndetics and TLC may require a customer ID.<br>Baker & Taylor may require username and password.',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'cat_CDN_pass',
				'label'       => 'CDN Password',
				'description' => '',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => '',
			),
			array(
				'id'          => 'cat_overdrive',
				'label'       => 'Overdrive Catalog',
				'description' => 'Enter your Overdrive catalog URL If you want the option to make some Bookshelves link directly to Overdrive instead of your main catalog.',
				'type'        => 'url',
				'default'     => '',
				'callback'    => 'lbs_trim_url',
				'placeholder' => '',
			),
			array(
				'id'          => 'cat_cloudlibrary',
				'label'       => 'cloudLibrary Catalog',
				'description' => 'Enter your cloudLibrary catalog URL If you want the option to make some Bookshelves link directly to cloudLibrary instead of your main catalog.',
				'type'        => 'url',
				'default'     => '',
				'callback'    => 'lbs_trim_url',
				'placeholder' => '',
			),
		),
	);
	$settings['slick'] = array(
		'title'       => 'Slider Settings',
		'description' => 'This plugin uses <a href="https://kenwheeler.github.io/slick/" target="_blank">slick carousel</a>.',
		'fields'      => array(
			array(
				'id'          => 'slick_accessibility',
				'label'       => 'Accessibility',
				'description' => 'Enables tabbing and arrow key navigation. Unless autoplay: true, sets browser focus to current slide (or first of current slide set, if multiple slidesToShow) after slide change. For full a11y compliance enable focusOnChange in addition to this.',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_adaptiveHeight',
				'label'       => 'Adaptive Height',
				'description' => 'Adapts slider height to the current slide',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_autoplay',
				'label'       => 'Autoplay',
				'description' => 'Enables auto play of slides',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_autoplaySpeed',
				'label'       => 'Autoplay Speed',
				'description' => 'Auto play change interval in milliseconds',
				'type'        => 'number',
				'default'     => '3000',
				'placeholder' => '3000',
				'min'         => '1',
				'size'        => '5',
			),
			array(
				'id'          => 'slick_arrows',
				'label'       => 'Arrows',
				'description' => 'Enable Next/Prev Arrows',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_centerMode',
				'label'       => 'Center Mode',
				'description' => 'Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_centerPadding',
				'label'       => 'Center Padding',
				'description' => 'Side padding when in center mode (px or %)',
				'type'        => 'text',
				'default'     => '50px',
				'placeholder' => '50px',
				'size'        => '4',
				'callback'    => 'sanitize_text_field',
			),
			array(
				'id'          => 'slick_dots',
				'label'       => 'Dots',
				'description' => 'Current slide indicator dots',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_draggable',
				'label'       => 'Draggable',
				'description' => 'Enables desktop dragging',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_fade',
				'label'       => 'Fade',
				'description' => 'Enables fade',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_focusOnChange',
				'label'       => 'Focus on Change',
				'description' => 'Puts focus on slide after change',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_focusOnSelect',
				'label'       => 'Focus on Select',
				'description' => 'Enable focus on selected element (click)',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_infinite',
				'label'       => 'Infinite',
				'description' => 'Infinite looping',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_lazyLoad',
				'label'       => 'Lazy Load',
				'description' => 'Accepts "ondemand" or "progressive" for lazy load technique. "ondemand" will load the image as soon as you slide to it, "progressive" loads one image after the other when the page loads.',
				'type'        => 'select',
				'options'     => array(
					'ondemand'    => 'On demand',
					'progressive' => 'Progressive',
				),
				'default'     => 'ondemand',
			),
			array(
				'id'          => 'slick_mobileFirst',
				'label'       => 'Mobile First',
				'description' => 'Responsive settings use mobile first calculation',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_pauseOnFocus',
				'label'       => 'Pause on Focus',
				'description' => 'Pauses autoplay when slider is focused',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_pauseOnHover',
				'label'       => 'Pause on Hover',
				'description' => 'Pauses autoplay on hover',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_pauseOnDotsHover',
				'label'       => 'Pause on Dots Hover',
				'description' => 'Pauses autoplay when a dot is hovered',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_respondTo',
				'label'       => 'Respond To',
				'description' => 'Width that responsive object responds to. Can be "window", "slider" or "min" (the smaller of the two)',
				'type'        => 'select',
				'options'     => array(
					'window' => 'Window',
					'slider' => 'Slider',
					'min'    => 'Min',
				),
				'default'     => 'slider',
			),
			array(
				'id'          => 'slick_responsive',
				'label'       => 'Responsive',
				'description' => 'Array of objects containing breakpoints and settings objects. Enables settings at given breakpoint. Set settings to "unslick" instead of an object to disable slick at a given breakpoint.',
				'type'        => 'textarea',
				'callback'    => 'sanitize_textarea_field',
				'default'     => '[ { breakpoint: 1025, settings: { slidesToShow: 6, slidesToScroll: 1 } }, { breakpoint: 769, settings: { slidesToShow: 4, slidesToScroll: 1 } }, { breakpoint: 481, settings: { slidesToShow: 2, slidesToScroll: 2 } } ]',
				'placeholder' => '[ { breakpoint: , settings: { } } ]',
			),
			array(
				'id'          => 'slick_rows',
				'label'       => 'Rows',
				'description' => 'Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row.',
				'type'        => 'number',
				'default'     => '1',
				'placeholder' => '1',
				'min'         => '1',
				'size'        => '4',
			),
			array(
				'id'          => 'slick_slidesPerRow',
				'label'       => 'Slides per Row',
				'description' => 'With grid mode initialized via the rows option, this sets how many slides are in each grid row.',
				'type'        => 'number',
				'default'     => '1',
				'placeholder' => '1',
				'min'         => '1',
				'size'        => '4',
			),
			array(
				'id'          => 'slick_slidesToShow',
				'label'       => 'Slides to Show',
				'description' => '# of slides to show / # of pages of slides to show with grid mode enabled',
				'type'        => 'number',
				'default'     => '6',
				'placeholder' => '6',
				'min'         => '1',
				'size'        => '4',
			),
			array(
				'id'          => 'slick_slidesToScroll',
				'label'       => 'Slides to Scroll',
				'description' => '# of slides to scroll (Has no effect if Swipe to Slide is enabled).',
				'type'        => 'number',
				'default'     => '1',
				'placeholder' => '1',
				'min'         => '1',
				'size'        => '4',
			),
			array(
				'id'          => 'slick_speed',
				'label'       => 'Speed',
				'description' => 'Transition speed',
				'type'        => 'number',
				'default'     => '300',
				'placeholder' => '300',
				'min'         => '1',
				'size'        => '5',
			),
			array(
				'id'          => 'slick_swipe',
				'label'       => 'Swipe',
				'description' => 'Enables touch swipe',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_swipeToSlide',
				'label'       => 'Swipe to Slide',
				'description' => 'Swipe to slide irrespective of slidesToScroll. (If Autoplay is on this setting will behave as if Slides to Scroll = 1).',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_touchMove',
				'label'       => 'Touch Move',
				'description' => 'Enables slide moving with touch',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_touchThreshold',
				'label'       => 'Touch Threshold',
				'description' => 'To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider.',
				'type'        => 'number',
				'default'     => '5',
				'placeholder' => '5',
				'min'         => '1',
				'size'        => '4',
			),
			array(
				'id'          => 'slick_useCSS',
				'label'       => 'Use CSS',
				'description' => 'Enable/Disable CSS Transitions',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_useTransform',
				'label'       => 'Use Transform',
				'description' => 'Enable/Disable CSS Transforms',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_variableWidth',
				'label'       => 'Variable Width',
				'description' => 'Automatic slide width calculation',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_vertical',
				'label'       => 'Vertical',
				'description' => 'Vertical slide mode',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_verticalSwiping',
				'label'       => 'Vertical Swiping',
				'description' => 'Vertical swipe mode',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_rtl',
				'label'       => 'RTL',
				'description' => 'Change the slider\'s direction to become right-to-left',
				'type'        => 'checkbox',
				'default'     => '',
			),
			array(
				'id'          => 'slick_waitForAnimate',
				'label'       => 'Wait for Animate',
				'description' => 'Ignores requests to advance the slide while animating',
				'type'        => 'checkbox',
				'default'     => 'on',
			),
			array(
				'id'          => 'slick_zIndex',
				'label'       => 'Z-Index',
				'description' => 'Set the zIndex values for slides, useful for IE9 and lower',
				'type'        => 'number',
				'default'     => '1000',
				'placeholder' => '1000',
				'size'        => '5',
			),
		),
	);

	$settings['css'] = array(
		'title'       => 'Customize CSS',
		'description' => 'Edit CSS Styles for plugin elements.',
		'fields'      => array(
			array(
				'id'          => 'css_bookshelf_margin_top',
				'label'       => 'Bookshelf Top Margin',
				'description' => 'Bookshelf container top margin (px, %, auto)',
				'type'        => 'text',
				'placeholder' => '0px',
				'size'        => '2',
				'default'     => '0px',
			),
			array(
				'id'          => 'css_bookshelf_margin_side',
				'label'       => 'Bookshelf Side Margin',
				'description' => 'Bookshelf container left and right margins (px, %, auto)',
				'type'        => 'text',
				'placeholder' => 'auto',
				'size'        => '2',
				'default'     => 'auto',
			),
			array(
				'id'          => 'css_bookshelf_margin_bottom',
				'label'       => 'Bookshelf Bottom Margin',
				'description' => 'Bookshelf container bottom margin (px, %, auto)',
				'type'        => 'text',
				'placeholder' => '30px',
				'size'        => '2',
				'default'     => '30px',
			),
			array(
				'id'          => 'css_arrow_color',
				'label'       => 'Navigation Arrow Color',
				'description' => 'Slick navigation arrow color',
				'type'        => 'color',
				'default'     => '#000000',
			),
			array(
				'id'          => 'css_arrow_size',
				'label'       => 'Navigation Arrow Size',
				'description' => 'Slick navigation arrow size',
				'type'        => 'text',
				'placeholder' => '20px',
				'size'        => '2',
				'default'     => '20px',
			),
			array(
				'id'          => 'css_arrow_distance',
				'label'       => 'Navigation Arrow Distance',
				'description' => 'Slick navigation arrow distance from left and right edges of the Bookshelf',
				'type'        => 'text',
				'placeholder' => '-25px',
				'size'        => '2',
				'default'     => '-25px',
			),
			array(
				'id'          => 'css_dots_bottom_offset',
				'label'       => 'Navigation Dots Bottom Offset',
				'description' => 'Slick navigation dots offset from the bottom of the Bookshelf container (px, %, auto)',
				'type'        => 'text',
				'placeholder' => '-25px',
				'size'        => '2',
				'default'     => '-25px',
			),
			array(
				'id'          => 'css_dot_color',
				'label'       => 'Navigation Dots Color',
				'description' => 'Slick navigation dots color',
				'type'        => 'color',
				'default'     => '#000000',
			),
			array(
				'id'          => 'css_widget_active_bg_color',
				'label'       => 'Widget Active Tab Color',
				'description' => 'Bookshelf Widget active tab background color. Also applies to inactive tabs on hover.',
				'type'        => 'color',
				'default'     => '#ffffff',
			),
			array(
				'id'          => 'css_widget_active_link_color',
				'label'       => 'Widget Active Tab Link Color',
				'description' => 'Bookshelf Widget active tab link color. Also applies to inactive tabs on hover.',
				'type'        => 'color',
				'default'     => '#000000',
			),
			array(
				'id'          => 'css_widget_tab_border_width',
				'label'       => 'Widget Tab Border Width',
				'description' => 'Bookshelf Widget tab border width.',
				'type'        => 'text',
				'placeholder' => '1px',
				'size'        => '2',
				'default'     => '1px',
			),
			array(
				'id'          => 'css_widget_tab_border_color',
				'label'       => 'Widget Tab Border Color',
				'description' => 'Bookshelf Widget tab border color.',
				'type'        => 'color',
				'default'     => '#000000',
			),
		),
	);
	return $settings;
}

//==================================================
// Generate HTML for settings fields
//==================================================

function lbs_display_setting_field( $data = array(), $post = false ) {
	// Get field info from plugin settings page or from post editor.
	isset( $data['field'] ) ? $field = $data['field'] : $field = $data;

	// Check for and add prefix to option name.
	isset( $data['prefix'] ) ? $option_name = $data['prefix'] : $option_name = 'lbs_';

	// Get saved field data.
	$option_name .= $field['id'];

	// Get saved settings data, if any, else write defaults.
	if ( $post ) {
		// Get saved settings from post meta.
		$settings = get_post_meta( $post, 'settings', true );

		// If no settings exist in post meta, use the global setting.
		$settings ? $data = $settings[ $option_name ] : $data = get_option( $option_name, $field['default'] );

		// Layout post settings like the plugin settings page
		echo "<tr><th scope='row'>".esc_html( $field['label'] ) . "</th><td>";
	} else {
		// Get saved option
		$data = get_option( $option_name );
		if ( false === $data && isset( $field['default'] ) ) {
			$data = $field['default'];
		}
	}

	// Clear html
	$html = '';

	// Assemble setting fields
	switch ( $field['type'] ) {
		case 'text':
			$size = '';
			if ( isset( $field['size'] ) ) {
				$size = ' size="' . esc_attr( $field['size'] ) . '"';
			}
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $size . ' />' . "\n";
			break;

		case 'url':
			$size = '';
			if ( isset( $field['size'] ) ) {
				$size = ' size="' . esc_attr( $field['size'] ) . '"';
			}
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $size . ' />' . "\n";
			break;

		case 'number':
		case 'hidden':
			$min = '';
			if ( isset( $field['min'] ) ) {
				$min = ' min="' . esc_attr( $field['min'] ) . '"';
			}

			$max = '';
			if ( isset( $field['max'] ) ) {
				$max = ' max="' . esc_attr( $field['max'] ) . '"';
			}
			$size = '';
			if ( isset( $field['size'] ) ) {
				$size = ' style="width: ' . esc_attr( $field['size'] ) . 'em;"';
			}
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '' . $size . '/>' . "\n";
			break;

		case 'textarea':
			$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="25" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>' . "\n";
			break;

		case 'checkbox':
			$checked = '';
			if ( $data && 'on' === $data ) {
				$checked = 'checked="checked"';
			}
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

		case 'select':
			$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
			foreach ( $field['options'] as $k => $v ) {
				$selected = false;
				if ( $k === $data ) {
					$selected = true;
				}
				$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
			}
			$html .= '</select> ';
			break;

		case 'color':
			$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $data ) . '" onchange="javascript: jQuery(&quot;#' . esc_attr( $field['id'] ) . '2&quot;).val(this.value);" />' . "\n";
			$html .= '<input id="' . esc_attr( $field['id'] ) . '2" name="' . esc_attr( $option_name ) . '" type="text" maxlength="7" size="6" value="' . esc_attr( $data ) . '" onchange="javascript: jQuery(&quot;#' . esc_attr( $field['id'] ) . '&quot;).val(this.value);" />';
			break;
	}

	// Assemble setting field descriptions
	switch ( $field['type'] ) {
		default:
			if ( ! $post ) {
				$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
			}

			$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

			if ( ! $post ) {
				$html .= '</label>' . "\n";
			}
			break;
	}

	echo $html;

	// Close post settings table rows
	if ( $post ) {
		echo "</td></tr>";
	}
}

//==================================================
// Trim "http(s)://" and trailing slash from catalog URLs
//==================================================

function lbs_trim_url( $data ) {
	$data = sanitize_text_field( $data );
	$regex = '/^(http:\/\/|https:\/\/)|\/$/';
	$replacement = '';
	$data = preg_replace( $regex, $replacement, $data );
	return $data;
}

//==================================================
// Support PHP 5.3 which does not include array_column()
//==================================================

if ( ! function_exists( 'array_column' ) ) {
	function array_column( $array, $column_name ) {
		return array_map( function( $element ) use( $column_name ){ return $element[ $column_name ]; }, $array );
	}
}
