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
class Library_Management_System_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $table_activator;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once LIBMNS_PLUGIN_DIR_PATH . 'includes/class-library-management-system-activator.php';
        $this->table_activator = new Library_Management_System_Activator();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * @since    3.0
	 */
	public function owt7_library_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/library-management-system-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( "owt7-lms-toastr-css", plugin_dir_url( __FILE__ ) . 'css/toastr.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @since    3.0
	 */
	public function owt7_library_enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/library-management-system-public.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-toastr", plugin_dir_url( __FILE__ ) . 'js/toastr.min.js', array( 'jquery' ), $this->version, false );

		wp_localize_script($this->plugin_name, "owt7_library", array(
			"ajaxurl" => admin_url("admin-ajax.php"),
			"ajax_nonce" => wp_create_nonce('owt7_library_actions'),
			"page_no" => 1
		));
	}

	// All books Shortcode Handler
	public function owt7_library_all_books_shortcode($template) {
		global $wpdb;

		$raw_bid = isset( $_REQUEST['bid'] ) ? trim( wp_unslash( $_REQUEST['bid'] ) ) : '';
		$book_id = 0;

		if ( $raw_bid !== '' ) {

			$decoded = base64_decode( $raw_bid, true );
			if ( $decoded !== false && $decoded !== '' ) {
				$decoded = trim( $decoded );
				if ( ctype_digit( $decoded ) ) {
					$book_id = intval( $decoded );
				} else {
					$book_id = 0;
				}
			}
		}

		if ( $book_id > 0 ) {
			$sql = "
				SELECT book.*,
					(SELECT category.name FROM " . $this->table_activator->owt7_library_tbl_category() . " AS category WHERE category.id = book.category_id LIMIT 1) AS category_name,
					(SELECT bkcase.name FROM " . $this->table_activator->owt7_library_tbl_bookcase() . " AS bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name,
					(SELECT section.name FROM " . $this->table_activator->owt7_library_tbl_bookcase_sections() . " AS section WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name
				FROM " . $this->table_activator->owt7_library_tbl_books() . " AS book
				WHERE book.id = %d
				LIMIT 1
			";

			$prepared = $wpdb->prepare( $sql, $book_id );
			$book = $wpdb->get_row( $prepared );

			if ( ! empty( $book ) ) {
				return $this->owt7_library_include_template_file( "owt7_library_single_book", compact( "book_id", "book" ) );
			} else {
				return $this->owt7_library_include_template_file( "errors/owt7_library_404_page" );
			}
		} else {
			$books_per_page = (int) LIBMNS_DEFAULT_SHOW_BOOKS;
			if ( $books_per_page <= 0 ) {
				$books_per_page = 10;
			}

			$current_page = isset( $_GET['p_no'] ) ? intval( $_GET['p_no'] ) : (int) LIBMNS_DEFAULT_PAGE_NUMBER;
			if ( $current_page < 1 ) {
				$current_page = 1;
			}

			$offset = ( $current_page - 1 ) * $books_per_page;
			if ( $offset < 0 ) {
				$offset = 0;
			}

			$categories_sql = "
				SELECT category.*,
					(SELECT count(*) FROM " . $this->table_activator->owt7_library_tbl_books() . " AS book WHERE book.category_id = category.id LIMIT 1) AS total_books
				FROM " . $this->table_activator->owt7_library_tbl_category() . " AS category
				WHERE status = 1
			";
			$categories = $wpdb->get_results( $categories_sql );

			$all_books = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . $this->table_activator->owt7_library_tbl_books() . " WHERE status = 1" );

			$books_sql = "
				SELECT book.*,
					(SELECT category.name FROM " . $this->table_activator->owt7_library_tbl_category() . " AS category WHERE category.id = book.category_id LIMIT 1) AS category_name,
					(SELECT bkcase.name FROM " . $this->table_activator->owt7_library_tbl_bookcase() . " AS bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name,
					(SELECT section.name FROM " . $this->table_activator->owt7_library_tbl_bookcase_sections() . " AS section WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name
				FROM " . $this->table_activator->owt7_library_tbl_books() . " AS book
				WHERE status = 1
				LIMIT %d OFFSET %d
			";
			$prepared_books_sql = $wpdb->prepare( $books_sql, $books_per_page, $offset );
			$books = $wpdb->get_results( $prepared_books_sql );

			$total_pages = 0;
			if ( $books_per_page > 0 ) {
				$total_pages = (int) ceil( $all_books / $books_per_page );
			}

			return $this->owt7_library_include_template_file( "owt7_library_books", compact( "books", "categories", "total_pages", "current_page" ) );
		}
	}

	// Helper function
	public function owt7_library_include_template_file($template, $lib_params = array()){

		ob_start();
		$params = $lib_params;
		include_once LIBMNS_PLUGIN_DIR_PATH . 'public/views/' . $template . ".php";
		$template = ob_get_contents();
		ob_end_clean();

		return $template;
	}
}