<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
class Library_Management_System {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'LIBMNS_VERSION' ) ) {
			$this->version = LIBMNS_VERSION;
		} else {
			$this->version = '3.0';
		}
		$this->plugin_name = 'library-management-system';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-library-management-system-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-library-management-system-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-library-management-system-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-library-management-system-public.php';

		$this->loader = new Library_Management_System_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Library_Management_System_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Library_Management_System_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter('plugin_action_links_' . LIBMNS_BASE_NAME, $plugin_admin, 'owt7_add_plugin_action_links');
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'owt7_library_management_menus' );
		$this->loader->add_action('wp_ajax_owt_lib_handler', $plugin_admin, 'owt7_library_management_ajax_handler');
	}

	private function define_public_hooks() {
		$plugin_public = new Library_Management_System_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'owt7_library_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'owt7_library_enqueue_scripts' );
		add_shortcode( 'owt7_library_books', array($plugin_public, "owt7_library_all_books_shortcode"));
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
