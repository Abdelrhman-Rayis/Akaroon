<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'widgets_init', 'bookshelves_register_widget' );

function bookshelves_register_widget() {
	register_widget( 'Bookshelves_Widget' );
}

class Bookshelves_Widget extends WP_Widget {

	private static $instance = null;

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'Bookshelves_Widget',
			'description' => 'Widget that displays Bookshelves',
		);
		parent::__construct( 'Bookshelves_Widget', 'Bookshelf Widget', $widget_ops );
	}

	//==================================================
	// Admin side of the widget
	//==================================================
	public function form( $instance ) {
		$defaults = array(
			'title'    => '',
			'location' => '',
			'tabbed'   => false,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$location = $instance['location'];
		$tabbed = $instance['tabbed'];
		$locations = get_terms( array( 'taxonomy' => 'location' ) );

		// Look for Location taxonomies before rendering widget settings
		if ( $locations ) {
			$widget_title_id = esc_attr( $this->get_field_id( 'title' ) );
			$widget_title_name = esc_attr( $this->get_field_name( 'title' ) );

			$widget_location_id = esc_attr( $this->get_field_id( 'location' ) );
			$widget_location_name = esc_attr( $this->get_field_name( 'location' ) );

			echo "<p><label for='" . $widget_title_id . "'>Title: </label>\n
					 <input id='" . $widget_title_id . "' name='" . $widget_title_name . "' type='text' size='25' value='" . esc_attr( $title ) . "'></input> <i>(optional)</i></p>";
			echo "<p><label for='" . $widget_location_id . "'>Show Bookshelves with this Location tag:</label>\n
				  <select id='" . $widget_location_id . "' name='" . $widget_location_name . "'>";

			foreach ( $locations  as $loc ) {
				if ( $loc->name === $location ) {
					echo '<option selected value="' . esc_attr( $loc->name ) . '">' . esc_html( $loc->name ) . '</option>';
				} else {
					echo '<option value="' . esc_attr( $loc->name ) . '">' . esc_html( $loc->name ) . '</option>';
				}
			}
			echo "</select></p>\n
				  <p><input type='checkbox' id='" . esc_attr( $this->get_field_id( 'tabbed' ) ) . "' name='" . esc_attr( $this->get_field_name( 'tabbed' ) ) . "' " . ( ( ! $tabbed ) ? '' : 'checked' ) . '>Tabbed Shelves</p>';
		} else {
			echo '<p>To use this widget, assign a Location tag to a Bookshelf post.</p>';
		}
	}

	//==================================================
	// Public side of the widget
	//==================================================
	public function widget( $args, $instance ) {
		$location = ( empty( $instance['location'] ) ) ? '&nbsp;' : $instance['location'];
		$shelves = new WP_Query(
			array(
				'post_type'   => 'bookshelves',
				'post_status' => 'publish',
				'tax_query'   => array(
					array(
						'taxonomy' => 'location',
						'field'    => 'slug',
						'terms'    => $location,
					),
				),
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			)
		);

		if ( $shelves->post_count > 0 ) {
			$widget_title = trim( apply_filters( 'widget_title', $instance['title'] ) );
			$tabbed = $instance['tabbed'];

			if ( $tabbed ) {
				wp_enqueue_script( 'jquery-ui-tabs' );
				echo "<script>
						jQuery(function(){ jQuery('#tabs').tabs(); });
					</script>";
			}

			echo $args['before_widget'];

			if ( isset( $widget_title ) ) {
				echo $args['before_title'];
				echo esc_html( $widget_title );
				echo $args['after_title'];
			}

			// If Tabbed option is selected create jQuery UI tabs
			if ( $tabbed ) {
				$num_shelves = $shelves->post_count;
				$tab_width_percent = round( 100 / $num_shelves, 2 );

				echo "<div id='tabs'><ul>";
				while ( $shelves->have_posts() ) {
					$shelves->the_post();
					$id = get_the_ID();
					the_title( "<li style='width:" . $tab_width_percent . "%'><a href=#bookshelf-" . $id . '>', '</a></li>' );
				}
				echo '</ul>';
				wp_reset_postdata();
			}

			// Roll through and render Bookshelves
			while ( $shelves->have_posts() ) {
				$shelves->the_post();
				$id = get_the_ID();

				if ( ! $tabbed ) {
					the_title( '<h3>', '</h3>' );
				}

				$shelf_class = 'shelf-widget-' . $id;

				$html = lbs_shelveBooks( $id, true );
				echo $html;
			}

			// jQuery UI Tabs initialization script
			if ( $tabbed ) {
				wp_enqueue_script( 'jquery-ui-tabs' );

				echo "<script type='text/javascript'>
					jQuery(function(){ jQuery('#tabs').tabs(); });
					jQuery(document).ready(function(){
						jQuery('#tabs').tabs({
							activate: function(e,ui){}
						});

						jQuery('#tabs').on('tabsactivate',function(e,ui){
							var active = '#' + jQuery('#tabs div:visible').attr('id'); 
							jQuery(active).slick('setPosition');
						});
					});
					</script>";
			}
			echo $args['after_widget'];
		}

		wp_reset_postdata();
	}

	//==================================================
	// Update widget settings
	//==================================================
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = $new_instance;

		if ( isset( $new_instance['tabbed'] ) ) {
			$instance['tabbed'] = $new_instance['tabbed'];
		} else {
			$instance['tabbed'] = false;
		}

		return $instance;
	}

	public static function instance() {
		null === self::$instance && self::$instance = new self();
		return self::$instance;
	}
}
