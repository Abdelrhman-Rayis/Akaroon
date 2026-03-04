<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Bookshelves_Settings {

	private static $instance = null;
	public $parent = null;
	public $base = '';
	public $settings = array();

	public function __construct( $parent ) {
		$this->parent = $parent;
		$this->base = 'lbs_';

		// Initialize settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add Settings page link.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to the plugin menu.
		add_filter( 'plugin_action_links_' . plugin_basename( $parent->file ), array( $this, 'add_settings_link' ) );
	}

	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	public function add_menu_item() {
		$page = add_submenu_page(
			'edit.php?post_type=bookshelves',
			'Bookshelf Settings',
			'Settings',
			'manage_options',
			'bookshelf-settings-page',
			array( $this, 'settings_page' )
		);
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="edit.php?post_type=bookshelves&page=bookshelf-settings-page">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	//==================================================
	// Save plugin settings
	//==================================================
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {
				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->token . '_settings' );

				foreach ( $data['fields'] as $field ) {
					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						'lbs_display_setting_field',
						$this->parent->token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}
				if ( ! $current_section ) { break; }
			}
		}
	}

	//==================================================
	// Delete plugin settings
	//==================================================
	public function unregister_settings() {
		foreach ( $this->settings as $section => $data ) {
			foreach ( $data['fields'] as $field ) {
				// Unregister field.
				$option_name = $this->base . $field['id'];
				unregister_setting( $this->parent->token . '_settings', $option_name, $validation );
			}
		}
	}

	//==================================================
	// Write settings section title and description
	//==================================================
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	//==================================================
	// Assemble settings page
	//==================================================
	public function settings_page() {
		$html = '<div class="wrap" id="' . $this->parent->token . '_settings">' . "\n";
		$html .= '<h2>Library Bookshelves Plugin Settings</h2>' . "\n";
		$html .= '<h3><b>We want to know where our plugin is being used and how you’re using it! <a href="mailto:lorangj@guilderlandlibrary.org?subject=We are using Library Bookshelves!" target="_blank">Say “Hi!”</a> and let us know where you are from.</b></h3>' . "\n";
		$tab = '';

		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}

		// Assemble page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";
			$c = 0;

			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
					if ( 0 === $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section === $_GET['tab'] ) {
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Write tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}
		
		// Assemble settings form.
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		// Assemble settings fields.
		ob_start();
		settings_fields( $this->parent->token . '_settings' );
		do_settings_sections( $this->parent->token . '_settings' );
		$html .= ob_get_clean();
		if ( $tab == 'slick' ) {
			$html .= '<p class="reset">' . "\n";
			$html .= '<input name="ResetSlick" type="button" class="button-primary" value="Reset Slick Defaults" onclick="resetSlickDefaults()" />' . "\n";
			$html .= '</p>' . "\n";
		}
		$html .= '<p class="submit">' . "\n";
		$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="Save Settings" />' . "\n";
		$html .= '</p>' . "\n";
		$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";
		echo $html;

		// Modify settings options based on user selections
		?>
		<script>
		function resetSlickDefaults() {
			jQuery( "#slick_accessibility, #slick_autoplay, #slick_draggable, #slick_infinite, #slick_pauseOnFocus, #slick_swipe, #slick_swipeToSlide, #slick_touchMove, #slick_useCSS, #slick_waitForAnimate" ).prop( "checked", true );
			jQuery( "#slick_adaptiveHeight, #slick_rtl, #slick_arrows, #slick_centerMode, #slick_dots, #slick_fade, #slick_focusOnChange, #slick_focusOnSelect, #slick_mobileFirst, #slick_pauseOnHover, #slick_pauseOnDotsHover, #slick_useTransform, #slick_variableWidth, #slick_vertical, #slick_verticalSwiping, #slick_rtl" ).prop( "checked", false );
			jQuery( "#slick_autoplaySpeed" ).val( "3000" );
			jQuery( "#slick_centerPadding" ).val( "50px" );
			jQuery( "#slick_lazyLoad" ).val( "ondemand" );
			jQuery( "#slick_respondTo" ).val( "slider" );
			jQuery( "#slick_responsive" ).val( '[ { breakpoint: 1025, settings: { slidesToShow: 6, slidesToScroll: 1 } }, { breakpoint: 769, settings: { slidesToShow: 4, slidesToScroll: 1 } }, { breakpoint: 481, settings: { slidesToShow: 2, slidesToScroll: 2 } } ]' );
			jQuery( "#slick_rows, #slick_slidesPerRow, #slick_slidesToScroll" ).val( "1" );
			jQuery( "#slick_slidesToShow" ).val( "6" );
			jQuery( "#slick_speed" ).val( "300" );
			jQuery( "#slick_touchThreshold" ).val( "5" );
			jQuery( "#slick_zIndex" ).val( "1000" );
		}

		jQuery( "#cat_System" ).attr( "onchange", "service(this.value)" );
		jQuery( "#cat_CDN" ).attr( "onchange", "cdn(this.value)" );
		cat_sys = jQuery( "#cat_System" ).val();
		cat_CDN = jQuery( "#cat_CDN" ).val();
		service( cat_sys );
		cdn( cat_CDN );

		function service( cat_sys ) {
			switch ( cat_sys ) {
				case 'bibliocommons':
				case 'dbtextworks':
				case 'encore':
				case 'koha':
				case 'pika':
				case 'primo':
				case 'sirsi_horizon':
				case 'spydus':
				case 'tlc':
				case 'worldcatds':
					enableCatDomain();
					disableCatProfile();
					enableEbooks();
					break;
				case 'calibre':
					enableCatDomain();
					enableCatProfile();
					jQuery( "#cat_Profile + label" ).html( "Enter your Calibre library ID." );
					disableEbooks();
					break;
				case 'cops':
					enableCatDomain();
					enableCatProfile();
					jQuery( "#cat_Profile + label" ).html( "If your COPS installation is located in a subdirectory of your domain, enter the directory name here." );
					disableEbooks();
					break;
				case 'ebsco_eds':
					enableCatDomain();
					enableCatProfile();
					jQuery( "#cat_Profile + label" ).html( "Enter your EBSCOHost customer ID." );
					disableEbooks();
					break;
				case 'evergreen-record':
					enableCatDomain();
					enableCatProfile();
					jQuery( "#cat_Profile + label" ).html( "Enter your library location ID for your Bookshelf links to show catalog customizations for your library. Leave blank for the default catalog skin." );
					disableEbooks();
					break;
				case 'evergreen':
				case 'polaris':
				case 'polaris63':
				case 'sirsi_ent':
				case 'tlc_ls1':
				case 'webpac':
					enableCatDomain();
					enableCatProfile();
					jQuery( "#cat_Profile + label" ).html( "Enter your library location ID for your Bookshelf links to show catalog customizations for your library. Leave blank for the default catalog skin." );
					enableEbooks();
					break;
				case 'hoopla':
				case 'openlibrary':
				case 'worldcat':
					disableCatDomain();
					disableCatProfile();
					enableEbooks();
					break;
				case 'cloudlibrary':
				case 'overdrive':
					disableCatDomain();
					disableCatProfile();
					disableEbooks();
					break;
			}
		}
		
		function disableCatDomain() {
			jQuery( "#cat_Protocol" ).attr( "disabled", "true" );
			jQuery( "#cat_DomainName" ).attr( "disabled", "true" );
		}

		function enableCatDomain() {
			jQuery( "#cat_Protocol" ).removeAttr( "disabled" );
			jQuery( "#cat_DomainName" ).removeAttr( "disabled" );
		}
		
		function disableCatProfile() {
			jQuery( "#cat_Profile" ).attr( "disabled", "true" );
			jQuery( "#cat_Profile + label" ).html( "" );
		}
		
		function enableCatProfile() {
			jQuery( "#cat_Profile" ).removeAttr( "disabled" );
		}
		
		function disableEbooks() {
			jQuery( "#cat_cloudlibrary" ).attr( "disabled", "true" );
			jQuery( "#cat_overdrive" ).attr( "disabled", "true" );
		}

		function enableEbooks() {
			jQuery( "#cat_cloudlibrary" ).removeAttr( "disabled" );
			jQuery( "#cat_overdrive" ).removeAttr( "disabled" );
		}

		function cdn( cat_CDN ) {
			switch ( cat_CDN ) {
				case 'amazon':
				case 'calibre':
				case 'cops':
				case 'chilifresh':
				case 'ebsco':
				case 'encore':
				case 'evergreen':
				case 'evergreen-record':
				case 'openlibrary':
				case 'pika':
					jQuery( "#cat_CDN_id" ).attr( "disabled", "true" );
					jQuery( "#cat_CDN_id + label" ).html( "" );
					jQuery( "#cat_CDN_pass" ).attr( "disabled", "true" );
					break;
				case 'contentcafe':
					jQuery( "#cat_CDN_id + label" ).html( "Baker & Taylor ContentCafe may require a username and password." );
					jQuery( "#cat_CDN_id" ).removeAttr( "disabled" );
					jQuery( "#cat_CDN_pass" ).removeAttr( "disabled" );
					break;
				case 'syndetics':
					jQuery( "#cat_CDN_id + label" ).html( "Syndetics may require a customer ID." );
					jQuery( "#cat_CDN_id" ).removeAttr( "disabled" );
					break;
				case 'tlc':
					jQuery( "#cat_CDN_id + label" ).html( "TLC requires a customer ID." );
					jQuery( "#cat_CDN_id" ).removeAttr( "disabled" );
					jQuery( "#cat_CDN_pass" ).attr( "disabled", "true" );
					break;
			}
		}
		</script>
		<?php
	}

	//==================================================
	// Build settings object
	//==================================================
	private function settings_fields() {
		$settings = lbs_settings_obj();
		return $settings;
	}

	public static function instance( $parent ) {
		null === self::$instance && self::$instance = new self( $parent );
		return self::$instance;
	}
}
