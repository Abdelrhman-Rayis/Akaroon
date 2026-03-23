<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.3
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if ( ! function_exists( 'WP_Filesystem' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

$creds = request_filesystem_credentials( '', '', false, false, null );
if ( ! WP_Filesystem( $creds ) ) {
	return false;
}
class Library_Management_System_Admin {

    private $plugin_name;
    private $table_activator;
    private $version;
	private $usersTable;
	private $branchTable;
	private $bookcaseTable;
	private $bookcaseSectionTable;
	private $categoryTable;
	private $booksTable;
	private $booksBorrowTable;
	private $booksReturnTable;
	private $booksLateFineTable;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        require_once LIBMNS_PLUGIN_DIR_PATH . 'includes/class-library-management-system-activator.php';
        $this->table_activator = new Library_Management_System_Activator();

		$this->usersTable = $this->table_activator->owt7_library_tbl_users();
		$this->branchTable = $this->table_activator->owt7_library_tbl_branch();
		$this->bookcaseTable = $this->table_activator->owt7_library_tbl_bookcase();
		$this->bookcaseSectionTable = $this->table_activator->owt7_library_tbl_bookcase_sections();
		$this->categoryTable = $this->table_activator->owt7_library_tbl_category();
		$this->booksTable = $this->table_activator->owt7_library_tbl_books();
		$this->booksBorrowTable = $this->table_activator->owt7_library_tbl_book_borrow();
		$this->booksReturnTable = $this->table_activator->owt7_library_tbl_book_return();
		$this->booksLateFineTable = $this->table_activator->owt7_library_tbl_book_late_fine();
    }

    public function enqueue_styles() {
        wp_enqueue_style( "owt7-lms-table-css", plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( "owt7-lms-table-buttons-css", plugin_dir_url( __FILE__ ) . 'css/buttons.dataTables.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( "owt7-lms-toastr-css", plugin_dir_url( __FILE__ ) . 'css/toastr.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/library-management-system-admin.css', array(), time(), 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( "owt7-lms-validate", plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-toastr", plugin_dir_url( __FILE__ ) . 'js/toastr.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-btns", plugin_dir_url( __FILE__ ) . 'js/dataTables.buttons.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-excel-btn", plugin_dir_url( __FILE__ ) . 'js/jszip.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-pdf-btn", plugin_dir_url( __FILE__ ) . 'js/pdfmake.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-vfs-fonts", plugin_dir_url( __FILE__ ) . 'js/vfs_fonts.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-btns-plugin", plugin_dir_url( __FILE__ ) . 'js/buttons.html5.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( "owt7-lms-datatable-copy-btn", plugin_dir_url( __FILE__ ) . 'js/buttons.print.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/library-management-system-admin.js', array( 'jquery' ), time(), false );

        wp_localize_script($this->plugin_name, "owt7_library", array(
            "ajaxurl" => admin_url("admin-ajax.php"),
            "active" => 1,
            "ajax_nonce" => wp_create_nonce('owt7_library_actions'),
            "messages" => array(
                "message_1" => __('Submitted, please wait', 'library-management-system'),
                "message_2" => __('Submit', 'library-management-system'),
                "message_3" => __('Success', 'library-management-system'),
                "message_4" => __('Error', 'library-management-system'),
                "message_5" => __('Upload Image', 'library-management-system'),
                "message_6" => __('Select Section', 'library-management-system'),
                "message_7" => __('Select User', 'library-management-system'),
                "message_8" => __('Select Book', 'library-management-system'),
                "message_9" => __('Run Data Importer remove existing data from Categories, Books, Bookcases, Sections, Branched and Users Table and then install new data. Do you want to Run Test Data Importer?', 'library-management-system'),
                "message_10" => __('Are you sure want to remove LMS Test Data?', 'library-management-system'),
                "message_11" => __('Are you sure want to pay the fine?', 'library-management-system'),
                "message_12" => __('Are you sure want to delete?', 'library-management-system'),
                "message_13" => __('Are you sure want to return this book?', 'library-management-system')
            )
        ));
    }

    public function owt7_library_management_menus() {
        // Main menu
        add_menu_page(__('Library Management', 'library-management-system'), __('Library Management', 'library-management-system'), 'manage_options', 'library_management_system', array($this, 'owt7_library_management_dashboard_page'), 'dashicons-book-alt', 67);

        // Submenus
        add_submenu_page('library_management_system', __('Dashboard', 'library-management-system'), __('Dashboard', 'library-management-system'), 'manage_options', 'library_management_system', array($this, 'owt7_library_management_dashboard_page'));
        add_submenu_page('library_management_system', __('Manage Users', 'library-management-system'), __('Manage Users', 'library-management-system'), 'manage_options', 'owt7_library_users', array($this, 'owt7_library_management_manage_users_page'));
        add_submenu_page('library_management_system', __('Manage Bookcase & Section', 'library-management-system'), __('Manage Bookcase & Section', 'library-management-system'), 'manage_options', 'owt7_library_bookcases', array($this, 'owt7_library_management_manage_bookcase_page'));
        add_submenu_page('library_management_system', __('Manage Books', 'library-management-system'), __('Manage Books', 'library-management-system'), 'manage_options', 'owt7_library_books', array($this, 'owt7_library_management_manage_books_page'));
        add_submenu_page('library_management_system', __('Book Transactions', 'library-management-system'), __('Book Transactions', 'library-management-system'), 'manage_options', 'owt7_library_transactions', array($this, 'owt7_library_management_transactions_page'));
        add_submenu_page('library_management_system', __('Settings', 'library-management-system'), __('Settings', 'library-management-system'), 'manage_options', 'owt7_library_settings', array($this, 'owt7_library_management_settings_page'));
        add_submenu_page('library_management_system', __('Free Vs Pro LMS', 'library-management-system'), __('Free Vs Pro LMS', 'library-management-system'), 'manage_options', 'owt7_library_free_vs_pro', array($this, 'owt7_library_management_free_vs_pro_page'));
        add_submenu_page('library_management_system', __('Upgrade to Pro', 'library-management-system'), __('Upgrade to Pro', 'library-management-system'), 'manage_options', 'owt7_library_addons', array($this, 'owt7_library_management_addons_page'));
    }

    public function owt7_add_plugin_action_links($links) {
        $settings_link = '<a href="admin.php?page=owt7_library_settings">' . esc_html__('Settings', 'library-management-system') . '</a>';
        $links[] = $settings_link;
        $doc_link = '<a href="' . LIBMNS_FREE_VERSION_DOC_LINK . '" target="_blank">Documentation</a>';
        $links[] = $doc_link;
        return $links;
    }

	function owt7_clean_text($value) {
		$value = sanitize_text_field(trim($value));
		$value = stripslashes_deep($value);
		// Remove double-escaped quotes like L\\\'amico → L'amico
		$value = preg_replace("/\\\\+'/", "'", $value);
		return $value;
	}

    public function owt7_library_management_dashboard_page() {
        $this->owt7_library_include_template_file("", "owt7_library_dashboard");
    }

	public function owt7_library_management_manage_users_page() {
		global $wpdb;

		$mod = isset($_GET['mod']) ? strtolower(sanitize_text_field(wp_unslash($_GET['mod']))) : "";
		$fn = isset($_GET['fn']) ? strtolower(sanitize_text_field(wp_unslash($_GET['fn']))) : "";
		$page_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : "";

		// Verify nonce
		if ( isset($_GET['mod']) && (!isset($page_nonce) || !wp_verify_nonce($page_nonce, 'owt7_manage_users_page_nonce')) ) {
			wp_die(__('Nonce verification failed', 'library-management-system'));
		}

		$allowed_pages = [
			"user" => [
				"add",
				"list"
			],
			"branch" => [
				"add",
				"list"
			]
		];

		// Query String Params
		$id = isset($_GET['id']) ? intval(base64_decode(sanitize_text_field(wp_unslash($_GET['id'])))) : "";
		$opt = isset($_GET['opt']) ? sanitize_key(wp_unslash($_GET['opt'])) : "";

		// Add Media Library Files
		wp_enqueue_media();

		$statuses = [
			"1" => "active",
			"0" => "inactive"
		];

		if (!empty($fn) && isset($allowed_pages[$mod]) && in_array($fn, $allowed_pages[$mod])) {
			if ($mod == "branch") { // [add, edit, view]

				// All branches
				$branches = $wpdb->get_results(
					"SELECT branch.*, (SELECT count(*) FROM {$this->usersTable} as user WHERE branch.id = user.branch_id LIMIT 1) as total_users FROM {$this->branchTable} as branch"
				);

				$branch = array();

				if (!empty($id)) {
					$branch = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->branchTable} WHERE id = %d",
							$id
						),
						ARRAY_A
					);
				}

				if (!empty($branch)) { // [edit, view]

					$this->owt7_library_include_template_file(
						"users",
						"owt7_library_{$fn}_{$mod}",
						[
							"branch" => $branch,
							"statuses" => $statuses,
							"action" => $opt
						]
					);
				} else { // [list, add]

					$this->owt7_library_include_template_file(
						"users",
						"owt7_library_{$fn}_{$mod}",
						[
							"branches" => $branches,
							"statuses" => $statuses
						]
					);
				}
			} elseif ($mod == "user") { // [add, edit, view]

				// All "Active" branches
				$branches = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$this->branchTable} WHERE status = %d",
						1
					)
				);

				$genders = ["male", "female", "other"];

				$user = array();

				if (!empty($id)) {
					$user = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->usersTable} WHERE id = %d",
							$id
						),
						ARRAY_A
					);
				}

				$this->owt7_library_include_template_file(
					"users",
					"owt7_library_{$fn}_{$mod}",
					[
						"branches" => $branches,
						"user" => $user,
						"genders" => $genders,
						"statuses" => $statuses,
						"action" => $opt
					]
				);
			}
		} else { // [list]

			// All Users
			$users = $wpdb->get_results(
				"SELECT user.*, (SELECT name FROM {$this->branchTable} as branch WHERE branch.id = user.branch_id LIMIT 1) as branch_name FROM {$this->usersTable} as user"
			);
			
			// All "Active" branches
			$branches = $wpdb->get_results("SELECT * from {$this->branchTable} WHERE status = 1");

			$this->owt7_library_include_template_file(
				"users",
				"owt7_library_users",
				[
					"users" => $users,
					"branches" => $branches,
				]
			);
		}
	}


	public function owt7_library_management_manage_bookcase_page() {

		global $wpdb;

		$mod = isset($_GET['mod']) ? strtolower(sanitize_text_field(wp_unslash($_GET['mod']))) : "";
		$fn = isset($_GET['fn']) ? strtolower(sanitize_text_field(wp_unslash($_GET['fn']))) : "";
		$page_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : "";

		// Verify nonce
		if ( isset($_GET['mod']) && (!isset($page_nonce) || !wp_verify_nonce($page_nonce, 'owt7_manage_bookcase_page_nonce')) ) {
			wp_die(__('Nonce verification failed', 'library-management-system'));
		}

		$allowed_pages = [
			'bookcase' => [
				'add',
				'list',
			],
			'section' => [
				'add',
				'list',
			],
		];

		// Query String Params
		$id = isset($_GET['id']) ? intval(base64_decode(sanitize_text_field(wp_unslash($_GET['id'])))) : "";
		$opt = isset($_GET['opt']) ? sanitize_key(wp_unslash($_GET['opt'])) : "";

		$statuses = [
			'1' => 'active',
			'0' => 'inactive',
		];

		if ( ! empty( $fn ) && in_array( $fn, $allowed_pages[ $mod ] ) ) {

			if ( 'section' === $mod ) { // [add, edit, view]

				// All "Active" bookcases
				$bookcases = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$this->bookcaseTable} WHERE status = %d", 
						1
					)
				);

				$section = [];

				if ( ! empty( $id ) ) {
					$section = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->bookcaseSectionTable} WHERE id = %d", 
							$id
						),
						ARRAY_A
					);
				}

				if ( ! empty( $section ) ) { // [edit, view]
					$this->owt7_library_include_template_file(
						'bookcases',
						'owt7_library_' . $fn . '_section',
						[
							'section'   => $section,
							'bookcases' => $bookcases,
							'statuses'  => $statuses,
							'action'    => $opt,
						]
					);
				} else { // [list, add]
					$sections = $wpdb->get_results(
						"SELECT sec.*, bkcase.name AS bookcase_name FROM {$this->bookcaseSectionTable} sec INNER JOIN {$this->bookcaseTable} bkcase ON sec.bookcase_id = bkcase.id"
					);

					$this->owt7_library_include_template_file(
						'bookcases',
						'owt7_library_' . $fn . '_section',
						[
							'sections'  => $sections,
							'bookcases' => $bookcases,
							'statuses'  => $statuses,
						]
					);
				}

			} elseif ( 'bookcase' === $mod ) { // [add, edit, view]

				$bookcase = [];

				if ( ! empty( $id ) ) {
					$bookcase = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->bookcaseTable} WHERE id = %d", $id
						),
						ARRAY_A
					);
				}

				$this->owt7_library_include_template_file(
					'bookcases',
					'owt7_library_' . $fn . '_bookcase',
					[
						'bookcase' => $bookcase,
						'statuses' => $statuses,
						'action'   => $opt,
					]
				);
			}

		} else {

			// All bookcases
			$bookcases = $wpdb->get_results(
				"SELECT bkcase.*, (SELECT count(*) FROM {$this->bookcaseSectionTable} AS section WHERE section.bookcase_id = bkcase.id LIMIT 1) AS total_sections FROM {$this->bookcaseTable} AS bkcase"
			);

			$this->owt7_library_include_template_file(
				'bookcases',
				'owt7_library_bookcases',
				[
					'bookcases' => $bookcases,
				]
			);
		}
	}

	// Callback: "Books and Categories"
	public function owt7_library_management_manage_books_page() {

		global $wpdb;

		$mod = isset($_GET['mod']) ? strtolower(sanitize_text_field(wp_unslash($_GET['mod']))) : "";
		$fn = isset($_GET['fn']) ? strtolower(sanitize_text_field(wp_unslash($_GET['fn']))) : "";
		$page_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : "";

		// Verify nonce
		if ( isset($_GET['mod']) && (!isset($page_nonce) || !wp_verify_nonce($page_nonce, 'owt7_manage_books_page_nonce')) ) {
			wp_die(__('Nonce verification failed', 'library-management-system'));
		}

		$allowed_pages = [
			'book'     => [
				'add',
				'list',
			],
			'category' => [
				'add',
				'list',
			],
		];

		// Query String Params
		$id = isset($_GET['id']) ? intval(base64_decode(sanitize_text_field(wp_unslash($_GET['id'])))) : "";
		$opt = isset($_GET['opt']) ? sanitize_key(wp_unslash($_GET['opt'])) : "";

		// Add Media Library Files
		wp_enqueue_media();

		$statuses = [
			'1' => 'active',
			'0' => 'inactive',
		];

		if ( ! empty( $fn ) && in_array( $fn, $allowed_pages[ $mod ] ) ) {

			if ( 'category' === $mod ) { // [add, edit, view]

				$category = [];

				if ( ! empty( $id ) ) {
					$category = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->categoryTable} WHERE id = %d", 
							$id
						),
						ARRAY_A
					);
				}

				if ( ! empty( $category ) ) { // [edit, view]
					$this->owt7_library_include_template_file(
						'books',
						'owt7_library_' . $fn . '_category',
						[
							'category' => $category,
							'statuses' => $statuses,
							'action'   => $opt,
						]
					);
				} else { // [list, add]

					// All categories
					$categories = $wpdb->get_results(
						"SELECT category.*, (SELECT count(*) FROM {$this->booksTable} AS book WHERE book.category_id = category.id LIMIT 1) AS total_books FROM {$this->categoryTable} AS category"
					);

					$this->owt7_library_include_template_file(
						'books',
						'owt7_library_' . $fn . '_category',
						[
							'categories' => $categories,
							'statuses'   => $statuses,
						]
					);
				}

			} elseif ( 'book' === $mod ) {

				$book     = [];
				$sections = [];

				// All categories
				$categories = $wpdb->get_results(
					"SELECT * FROM {$this->categoryTable} WHERE status = 1"
				);

				$bookcases = $wpdb->get_results(
					"SELECT * FROM {$this->bookcaseTable} WHERE status = 1"
				);

				if ( ! empty( $id ) ) {
					$book = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->booksTable} WHERE id = %d", 
							$id
						),
						ARRAY_A
					);

					if ( ! empty( $book['bookcase_id'] ) ) {
						$sections = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT * FROM {$this->bookcaseSectionTable} WHERE bookcase_id = %d AND status = %d", $book['bookcase_id'], 1
							)
						);
					}
				}

				if ( ! empty( $book ) ) { // [edit, view]
					$this->owt7_library_include_template_file(
						'books',
						'owt7_library_' . $fn . '_book',
						[
							'book'      => $book,
							'statuses'  => $statuses,
							'sections'  => $sections,
							'bookcases' => $bookcases,
							'action'    => $opt,
							'categories' => $categories,
						]
					);
				} else { // [add]
					$this->owt7_library_include_template_file(
						'books',
						'owt7_library_' . $fn . '_book',
						[
							'statuses'  => $statuses,
							'bookcases' => $bookcases,
							'categories' => $categories,
						]
					);
				}
			}

		} else {

			// All books
			$books = $wpdb->get_results(
				"SELECT book.id, book.book_id, book.name, book.stock_quantity, book.status, book.created_at, (SELECT category.name FROM {$this->categoryTable} AS category WHERE category.id = book.category_id LIMIT 1) AS category_name, (SELECT bkcase.name FROM {$this->bookcaseTable} AS bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name, (SELECT section.name FROM {$this->bookcaseSectionTable} AS section WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name FROM {$this->booksTable} AS book"
			);

			// All categories
			$categories = $wpdb->get_results(
				"SELECT * FROM {$this->categoryTable} WHERE status = 1"
			);


			$this->owt7_library_include_template_file(
				'books',
				'owt7_library_books',
				[
					'books'     => $books,
					'categories' => $categories,
				]
			);
		}
	}

	// Callback: "Books Borrow and Return Transactions"
	public function owt7_library_management_transactions_page() {

		global $wpdb;

		$mod = isset($_GET['mod']) ? strtolower(sanitize_text_field(wp_unslash($_GET['mod']))) : "";
		$fn = isset($_GET['fn']) ? strtolower(sanitize_text_field(wp_unslash($_GET['fn']))) : "";
		$page_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : "";

		// Verify nonce
		if ( isset($_GET['mod']) && (!isset($page_nonce) || !wp_verify_nonce($page_nonce, 'owt7_manage_transactions_page_nonce')) ) {
			wp_die(__('Nonce verification failed', 'library-management-system'));
		}

		$allowed_pages = [
			'books' => [
				'books',
				'borrow',
				'return-history',
				'return',
				'history',
			],
		];

		if ( ! empty( $fn ) && in_array( $fn, $allowed_pages[ $mod ] ) ) {

			$fn = str_replace( '-', '_', $fn );

			if ( 'books' === $mod ) {

				$returns = [];

				$branches = $wpdb->get_results(
					"SELECT id, name FROM {$this->branchTable} WHERE status = 1"
				);

				$categories = $wpdb->get_results(
					"SELECT id, name FROM {$this->categoryTable} WHERE status = 1"
				);

				// Return History
				if ( 'return_history' === $fn ) {

					$returns = $wpdb->get_results(
						"SELECT rt.id, rt.borrow_id, rt.status, rt.created_at,(SELECT category.name FROM {$this->categoryTable} AS category WHERE category.id = rt.category_id LIMIT 1) AS category_name, (SELECT book.name FROM {$this->booksTable} AS book WHERE book.id = rt.book_id LIMIT 1) AS book_name, (SELECT branch.name FROM {$this->branchTable} AS branch WHERE branch.id = rt.branch_id LIMIT 1) AS branch_name, (SELECT user.name FROM {$this->usersTable} AS user WHERE user.id = rt.u_id LIMIT 1) AS user_name, (SELECT borrow.borrows_days FROM {$this->booksBorrowTable} AS borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS total_days, (SELECT borrow.created_at FROM {$this->booksBorrowTable} AS borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS issued_on, (SELECT fine.has_paid FROM {$this->booksLateFineTable} AS fine WHERE fine.return_id = rt.id LIMIT 1) AS has_paid, (SELECT fine.fine_amount FROM {$this->booksLateFineTable} AS fine WHERE fine.return_id = rt.id LIMIT 1) AS fine_amount, (SELECT fine.extra_days FROM {$this->booksLateFineTable} AS fine WHERE fine.return_id = rt.id LIMIT 1) AS extra_days FROM {$this->booksReturnTable} AS rt"
					);					
				}

				$this->owt7_library_include_template_file(
					"transactions", 
					"owt7_library_{$mod}_{$fn}",
					[
						"branches" => $branches,
						"categories" => $categories,
						"returns" => $returns
					]
				);
			} else{

				$this->owt7_library_include_template_file(
					"transactions", 
					"owt7_library_books_{$mod}_{$fn}"
				);
			}
		} else {

			$borrows = $wpdb->get_results(
				"SELECT borrow.id, borrow.borrow_id, borrow.borrows_days, borrow.return_date, borrow.status, borrow.created_at, (SELECT category.name FROM {$this->categoryTable} category WHERE category.id = borrow.category_id LIMIT 1) as category_name, (SELECT book.name FROM {$this->booksTable} book WHERE book.id = borrow.book_id LIMIT 1) as book_name, (SELECT branch.name FROM {$this->branchTable} branch WHERE branch.id = borrow.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM {$this->usersTable} user WHERE user.id = borrow.u_id LIMIT 1) as user_name FROM {$this->booksBorrowTable} borrow ORDER by borrow.id DESC"
			);

			$branches = $wpdb->get_results(
				"SELECT id, name from {$this->branchTable} WHERE status = 1"
			);

			$categories = $wpdb->get_results(
				"SELECT id, name from {$this->categoryTable} WHERE status = 1"
			);

			$this->owt7_library_include_template_file(
				"transactions", 
				"owt7_library_books_transactions",
				[
					"borrows" => $borrows,
					"branches" => $branches,
					"categories" => $categories
				]
			);
		}
	}

	// Callback: "Settings Page"
	public function owt7_library_management_settings_page() {

		global $wpdb;

		// [late_fine, country]
		$mod = isset($_GET['mod']) ? strtolower(sanitize_text_field(wp_unslash($_GET['mod']))) : "";

		if ( ! empty( $mod ) ) {
			$this->owt7_library_include_template_file(
				'settings',
				"owt7_library_{$mod}_settings"
			);
		} else {
			$this->owt7_library_include_template_file(
				'settings',
				'owt7_library_settings'
			);
		}
	}

	// Callback: "Free Vs Pro LMS"
	public function owt7_library_management_free_vs_pro_page() {
		$this->owt7_library_include_template_file( 'lms', 'owt7_library_free_vs_pro' );
	}

	// Callback: "Addons"
	public function owt7_library_management_addons_page() {
		$this->owt7_library_include_template_file( 'lms', 'owt7_library_addons' );
	}

	// Helper function: Include template file
	private function owt7_library_include_template_file( $mod, $template, $lib_params = [] ) {

		ob_start();
		$params = $lib_params;

		if ( ! empty( $mod ) ) {
			include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/{$mod}/" . $template . '.php';
		} else {
			include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/' . $template . '.php';
		}

		$template = ob_get_contents();
		ob_end_clean();

		echo $template;
	}

	// Generate Unique Identifier
	private function owt7_library_generate_unique_identifier( $prefix = 'LMS', $length = 6 ) {
		if ( $length <= 0 ) {
			return $prefix;
		}

		$characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$randomString .= $characters[ wp_rand( 0, $charactersLength - 1 ) ];
		}

		return $prefix . $randomString;
	}

	// Send Response in JSON
	private function json( $response = [] ) {
		
		$data = isset( $response[2] ) ? $response[2] : [];
		$ar   = [
			'sts' => $response[0],
			'msg' => $response[1],
			'arr' => $data,
		];
	
		// Use wp_json_encode() instead of json_encode()
		echo wp_json_encode($ar);
		die;
	}

	// LMS Free Version Credit
	private function owt7_library_free_version_credit( $table, $type = '' ) {

		global $wpdb;

		$data = $wpdb->get_results( "SELECT * from {$table}" );

		$limited_credits = [ 'categories', 'bookcases', 'branches' ];

		if ( ! empty( $type ) && in_array( $type, $limited_credits ) ) {
			$credit = base64_decode( LIBMNS_FREE_VERSION_LIMIT );
			$credit = intval( $credit - 10 );
			if ( count( $data ) < $credit ) {
				return true;
			} else {
				return false;
			}
		} else {
			if ( count( $data ) < base64_decode( LIBMNS_FREE_VERSION_LIMIT ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	// Manage Stock
	private function owt7_library_manage_books_stock( $book_id, $action ) {
		global $wpdb;

		$book_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->booksTable} WHERE id = %d",
				$book_id
			)
		);

		if ( ! empty( $book_data ) ) {
			$stock_quantity = $book_data->stock_quantity;

			if ( 'plus' === $action ) {
				$stock_quantity = $stock_quantity + 1;
				$wpdb->update(
					$this->booksTable,
					[ 'stock_quantity' => $stock_quantity ],
					[ 'id' => $book_id ]
				);
				return true;
			} elseif ( 'minus' === $action ) {
				if ( $stock_quantity > 0 ) {
					$stock_quantity = $stock_quantity - 1;
					$wpdb->update(
						$this->booksTable,
						[ 'stock_quantity' => $stock_quantity ],
						[ 'id' => $book_id ]
					);
					return true;
				} else {
					return false;
				}
			}
		}

		return false;
	}

	// Ajax Handler
	public function owt7_library_management_ajax_handler(){

		// Verify nonce (dies with proper WP response on failure)
		check_ajax_referer( 'owt7_library_actions', 'owt7_lms_nonce', true );

		// Ensure user is logged in and has admin capability
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'msg' => 'Insufficient permissions.' ), 403 );
		}

		global $wpdb;

		$param = isset( $_REQUEST['param'] ) ? sanitize_text_field(wp_unslash(trim( $_REQUEST['param'] ) ) ) : "";

		if ( !empty( $param ) ) {

			if ( $param === "owt7_lms_branch_form" ) {

				// Action Type
				$action_type = isset($_REQUEST['action_type']) ? sanitize_text_field(wp_unslash(trim($_REQUEST['action_type']))) : "";
				// Form Data
				$branch  = isset($_REQUEST['owt7_txt_branch_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_branch_name']) : "";
				$status  = isset($_REQUEST['owt7_dd_branch_status']) ? absint($_REQUEST['owt7_dd_branch_status']) : 1;
				$edit_id = isset($_REQUEST['edit_id']) ? absint($_REQUEST['edit_id']) : 0;

			
				if ( ! empty( $action_type ) ) { // [add, edit]
			
					if ( $action_type === "add" ) {
			
						if ( ! empty( $branch ) ) {
			
							if ( $this->owt7_library_free_version_credit( $this->branchTable, "branches" ) ) {
			
								$is_branch_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->branchTable} WHERE LOWER(TRIM(name)) = %s",
										strtolower(trim($branch))
									)
								);
			
								if ( ! empty( $is_branch_exists ) ) {
									$response = [
										0,
										__( "Branch name already taken", "library-management-system" )
									];
								} else {
									$wpdb->insert(
										$this->branchTable,
										[
											'name'   => $branch,
											'status' => $status
										]
									);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [
											1,
											__( "Successfully, Branch added to LMS", "library-management-system" )
										];
									} else {
										$response = [
											0,
											__( "Failed to add Branch", "library-management-system" )
										];
									}
								}
			
							} else {
								$response = [
									0,
									__( "Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system" )
								];
							}
			
						} else {
							$response = [
								0,
								__( "Branch value required", "library-management-system" )
							];
						}
			
					} elseif ( $action_type === "edit" ) {
			
						if ( ! empty( $branch ) ) {
			
							$branch_have_same_id = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->branchTable} WHERE LOWER(TRIM(name)) = %s AND id <> %d",
									strtolower(trim($branch)),
									$edit_id
								)
							);
			
							if ( ! empty( $branch_have_same_id ) ) {
								$response = [
									0,
									__( "Branch name already taken", "library-management-system" )
								];
							} else {
			
								$is_branch_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->branchTable} WHERE id = %d",
										$edit_id
									)
								);
			
								if ( ! empty( $is_branch_exists ) ) {
			
									$wpdb->update(
										$this->branchTable,
										[
											'name'   => $branch,
											'status' => $status
										],
										[
											'id' => $edit_id
										]
									);
			
									$response = [
										1,
										__( "Successfully, Branch data updated", "library-management-system" )
									];
			
								} else {
									$response = [
										0,
										__( "Branch not found", "library-management-system" )
									];
								}
							}
			
						} else {
							$response = [
								0,
								__( "Branch value required", "library-management-system" )
							];
						}
			
					}
			
				} else {
					$response = [
						0,
						__( "Invalid Operation", "library-management-system" )
					];
				}
			} elseif ( $param === "owt7_lms_user_form" ) {

				// Action Type
				$action_type = isset($_REQUEST['action_type']) ? sanitize_text_field(trim(wp_unslash($_REQUEST['action_type']))) : "";
				// Form Data
				$userId       = isset($_REQUEST['owt7_txt_u_id']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_u_id']) : "";
				$branchId     = isset($_REQUEST['owt7_dd_branch_id']) ? absint(wp_unslash($_REQUEST['owt7_dd_branch_id'])) : 1;
				$name         = isset($_REQUEST['owt7_txt_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_name']) : "";
				$email        = isset($_REQUEST['owt7_txt_email']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_email']) : "";
				$phone        = isset($_REQUEST['owt7_txt_phone']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_phone']) : "";
				$gender       = isset($_REQUEST['owt7_dd_gender']) ? $this->owt7_clean_text($_REQUEST['owt7_dd_gender']) : "";
				$address      = isset($_REQUEST['owt7_txt_address']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_address']) : "";
				$profileImage = isset($_REQUEST['owt7_profile_image']) ? $this->owt7_clean_text($_REQUEST['owt7_profile_image']) : "";
				$status       = isset($_REQUEST['owt7_dd_user_status']) ? absint(wp_unslash($_REQUEST['owt7_dd_user_status'])) : 1;
				$edit_id      = isset($_REQUEST['edit_id']) ? absint(wp_unslash($_REQUEST['edit_id'])) : 0;

			
				if ( ! empty( $action_type ) ) { // [add, edit, view]
			
					if ( $action_type === "add" ) {
			
						if ( ! empty( $userId ) || ! empty( $branchId ) || ! empty( $name ) ) {
			
							if ( $this->owt7_library_free_version_credit( $this->usersTable ) ) {
			
								$is_user_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->usersTable} WHERE LOWER(TRIM(u_id)) = %s",
										strtolower( trim( $userId ) )
									)
								);
			
								if ( ! empty( $is_user_exists ) ) {
									$response = [
										0,
										__( "User already exists", "library-management-system" )
									];
								} else {
			
									$wpdb->insert(
										$this->usersTable,
										[
											'register_from'   => 'admin',
											'u_id'            => $userId,
											'name'            => $name,
											'email'           => $email,
											'gender'          => $gender,
											'branch_id'       => $branchId,
											'phone_no'        => $phone,
											'profile_image'   => $profileImage,
											'address_info'    => $address,
											'status'          => $status
										]
									);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [
											1,
											__( "Successfully, User added to LMS", "library-management-system" )
										];
									} else {
										$response = [
											0,
											__( "Failed to add User", "library-management-system" )
										];
									}
								}
			
							} else {
								$response = [
									0,
									__( "Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system" )
								];
							}
			
						} else {
							$response = [
								0,
								__( "Required fields are missing", "library-management-system" )
							];
						}
			
					} elseif ( $action_type === "edit" ) {
			
						if ( ! empty( $userId ) || ! empty( $branchId ) || ! empty( $name ) ) {
			
							$user_have_same_id = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->usersTable} WHERE LOWER(TRIM(u_id)) = %s AND id <> %d",
									strtolower( trim( $userId ) ),
									$edit_id
								)
							);
			
							if ( ! empty( $user_have_same_id ) ) {
								$response = [
									0,
									__( "User ID already taken", "library-management-system" )
								];
							} else {
			
								$is_user_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->usersTable} WHERE id = %d",
										$edit_id
									)
								);
			
								if ( ! empty( $is_user_exists ) ) {
			
									$wpdb->update(
										$this->usersTable,
										[
											'u_id'            => $userId,
											'name'            => $name,
											'email'           => $email,
											'gender'          => $gender,
											'branch_id'       => $branchId,
											'phone_no'        => $phone,
											'profile_image'   => $profileImage,
											'address_info'    => $address,
											'status'          => $status
										],
										[
											'id' => $edit_id
										]
									);
			
									$response = [
										1,
										__( "Successfully, User data updated", "library-management-system" )
									];
			
								} else {
									$response = [
										0,
										__( "User not found", "library-management-system" )
									];
								}
							}
			
						} else {
							$response = [
								0,
								__( "Required fields are missing", "library-management-system" )
							];
						}
			
					}
			
				} else {
					$response = [
						0,
						__( "Invalid Operation", "library-management-system" )
					];
				}
			
			} elseif ( $param === "owt7_lms_delete_function" ) {

				$deleteId      = isset( $_REQUEST['id'] ) ? intval( base64_decode( $_REQUEST['id'] ) ) : '';
				$deleteModule  = isset( $_REQUEST['module'] ) ? base64_decode( $_REQUEST['module'] ) : ''; 
			
				// Module-Association Mapping
				$associatedModules = [
					'branch'    => 'user',
					'bookcase'  => 'section',
					'category'  => 'book'
				];
			
				$tableName     = '';
				$deleteStatus  = false;
			
				// Determine the table and check conditions based on module
				switch ( $deleteModule ) {
					case 'user':
						$tableName = $this->usersTable;
						break;
					
					case 'branch':
						$tableName = $this->branchTable;
						// Check if any users are associated with the branch
						$has_data = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT count(*) as total_rows FROM {$this->usersTable} WHERE branch_id = %d",
								$deleteId
							)
						);
						if ( isset( $has_data->total_rows ) && $has_data->total_rows > 0 ) {
							$deleteStatus = true;
						}
						break;
					
					case 'bookcase':
						$tableName = $this->bookcaseTable;
						// Check if any sections are associated with the bookcase
						$has_data = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT count(*) as total_rows FROM {$this->bookcaseSectionTable} WHERE bookcase_id = %d",
								$deleteId
							)
						);
						if ( isset( $has_data->total_rows ) && $has_data->total_rows > 0 ) {
							$deleteStatus = true;
						}
						break;
					
					case 'section':
						$tableName = $this->bookcaseSectionTable;
						break;
					
					case 'category':
						$tableName = $this->categoryTable;
						// Check if any books are associated with the category
						$has_data = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT count(*) as total_rows FROM {$this->booksTable} WHERE category_id = %d",
								$deleteId
							)
						);
						if ( isset( $has_data->total_rows ) && $has_data->total_rows > 0 ) {
							$deleteStatus = true;
						}
						break;
					
					case 'book':
						$tableName = $this->booksTable;
						break;
					
					case 'book_borrow':
						$tableName = $this->booksBorrowTable;
						$deleteModule = str_replace( '_', ' ', $deleteModule );
						break;
					
					case 'book_return':
						$tableName = $this->booksReturnTable;
						$deleteModule = str_replace( '_', ' ', $deleteModule );
						break;
					
					default:
						$response = [
							0,
							__( 'Data Module not found', 'library-management-system' )
						];
						break;
				}
			
				if ( ! empty( $tableName ) ) {
			
					if ( ! $deleteStatus ) {
			
						$is_data_exists = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * FROM {$tableName} WHERE id = %d",
								$deleteId
							)
						);
			
						if ( ! empty( $is_data_exists ) ) {
		
							$wpdb->delete( $tableName, [ 'id' => $is_data_exists->id ] );
		
							$response = [
								1,
								sprintf( __( 'Successfully, %s deleted', 'library-management-system' ), ucfirst( $deleteModule ) )
							];
							
						} else {
							$response = [
								0,
								__( ucfirst( $deleteModule ) . " not found", "library-management-system" )
							];
						}
			
					} else {
						// If deletion is blocked due to associated data
						$response = [
							0,
							__( "Failed to delete. There are associated rows of " . ucfirst( $associatedModules[ $deleteModule ] ) . " with this " . ucfirst( $deleteModule ) . ". Please delete them first.", "library-management-system" )
						];
					}
			
				} else {
					$response = [
						0,
						__( "Data Module not found", "library-management-system" )
					];
				}
			} elseif ( $param == "owt7_lms_bookcase_form" ) {

				// Action Type
				$action_type = isset($_REQUEST['action_type']) ? $this->owt7_clean_text($_REQUEST['action_type']) : "";
				// Form Data
				$bookcase = isset($_REQUEST['owt7_txt_bookcase_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_bookcase_name']) : "";
				$status   = isset($_REQUEST['owt7_dd_bookcase_status']) ? absint(wp_unslash($_REQUEST['owt7_dd_bookcase_status'])) : 1;
				$edit_id  = isset($_REQUEST['edit_id']) ? absint(wp_unslash($_REQUEST['edit_id'])) : 0;
			
				if ( !empty( $action_type ) ) { // [add, edit]
			
					// Handle "add" action
					if ( $action_type == "add" ) {
						if ( !empty( $bookcase ) ) {
							if ( $this->owt7_library_free_version_credit( $this->bookcaseTable, "bookcases" ) ) {
			
								$is_bookcase_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->bookcaseTable} WHERE LOWER(TRIM(name)) = %s",
										strtolower(trim($bookcase))
									)
								);
			
								if ( !empty( $is_bookcase_exists ) ) {
									$response = [ 0, __("Bookcase name already taken", "library-management-system") ];
								} else {
									$wpdb->insert( $this->bookcaseTable, [
										"name" => $bookcase,
										"status" => $status
									]);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [ 1, __("Successfully, Bookcase added to LMS", "library-management-system") ];
									} else {
										$response = [ 0, __("Failed to add Bookcase", "library-management-system") ];
									}
								}
			
							} else {
								$response = [ 0, __("Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system") ];
							}
						} else {
							$response = [ 0, __("Bookcase value required", "library-management-system") ];
						}
			
					// Handle "edit" action
					} elseif ( $action_type == "edit" ) {
						if ( !empty( $bookcase ) ) {
			
							$bookcase_have_same_id = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->bookcaseTable} WHERE LOWER(TRIM(name)) = %s AND id <> %d",
									strtolower(trim($bookcase)), $edit_id
								)
							);
			
							if ( !empty( $bookcase_have_same_id ) ) {
								$response = [ 0, __("Bookcase name already taken", "library-management-system") ];
							} else {
			
								$is_bookcase_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->bookcaseTable} WHERE id = %d",
										$edit_id
									)
								);
			
								if ( !empty( $is_bookcase_exists ) ) {
									$wpdb->update(
										$this->bookcaseTable,
										[ "name" => $bookcase, "status" => $status ],
										[ "id" => $edit_id ]
									);
			
									$response = [ 1, __("Successfully, Bookcase data updated", "library-management-system") ];
								} else {
									$response = [ 0, __("Bookcase not found", "library-management-system") ];
								}
							}
			
						} else {
							$response = [ 0, __("Bookcase value required", "library-management-system") ];
						}
			
					}
			
				} else {
					$response = [ 0, __("Invalid Operation", "library-management-system") ];
				}
			} elseif ( $param == "owt7_lms_section_form" ) {

				// Action Type
				$action_type = isset($_REQUEST['action_type']) ? $this->owt7_clean_text($_REQUEST['action_type']) : "";
				// Form Data
				$bookcase_id = isset($_REQUEST['owt7_dd_bookcase_id']) ? absint(wp_unslash($_REQUEST['owt7_dd_bookcase_id'])) : 0;
				$section     = isset($_REQUEST['owt7_txt_section_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_section_name']) : "";
				$status      = isset($_REQUEST['owt7_dd_section_status']) ? absint(wp_unslash($_REQUEST['owt7_dd_section_status'])) : 1;
				$edit_id     = isset($_REQUEST['edit_id']) ? absint(wp_unslash($_REQUEST['edit_id'])) : 0;

			
				if ( !empty( $action_type ) ) { // [add, edit]
			
					// Handle "add" action
					if ( $action_type == "add" ) {
						if ( !empty( $section ) ) {
			
							// Check for credits
							if ( $this->owt7_library_free_version_credit( $this->bookcaseSectionTable ) ) {
			
								// Check if section already exists
								$is_section_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->bookcaseSectionTable} WHERE LOWER(TRIM(name)) = %s AND bookcase_id = %d",
										strtolower(trim($section)), $bookcase_id
									)
								);
			
								if ( !empty( $is_section_exists ) ) {
									$response = [ 0, __("Section name already taken", "library-management-system") ];
								} else {
									// Insert new section
									$wpdb->insert( $this->bookcaseSectionTable, [
										"name" => $section,
										"bookcase_id" => $bookcase_id,
										"status" => $status
									]);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [ 1, __("Successfully, Section added to LMS", "library-management-system") ];
									} else {
										$response = [ 0, __("Failed to add Section", "library-management-system") ];
									}
								}
			
							} else {
								$response = [ 0, __("Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system") ];
							}
			
						} else {
							$response = [ 0, __("Section value required", "library-management-system") ];
						}
			
					// Handle "edit" action
					} elseif ( $action_type == "edit" ) {
						if ( !empty( $section ) ) {
			
							// Check if section name is already taken (excluding current section)
							$section_have_same_id = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->bookcaseSectionTable} WHERE LOWER(TRIM(name)) = %s AND bookcase_id = %d AND id <> %d",
									strtolower(trim($section)), $bookcase_id, $edit_id
								)
							);
			
							if ( !empty( $section_have_same_id ) ) {
								$response = [ 0, __("Section name already taken", "library-management-system") ];
							} else {
								// Check if section exists
								$is_section_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->bookcaseSectionTable} WHERE id = %d",
										$edit_id
									)
								);
			
								if ( !empty( $is_section_exists ) ) {
									// Update section data
									$wpdb->update(
										$this->bookcaseSectionTable,
										[ "name" => $section, "bookcase_id" => $bookcase_id, "status" => $status ],
										[ "id" => $edit_id ]
									);
			
									$response = [ 1, __("Successfully, Section data updated", "library-management-system") ];
								} else {
									$response = [ 0, __("Section not found", "library-management-system") ];
								}
							}
			
						} else {
							$response = [ 0, __("Section value required", "library-management-system") ];
						}
			
					}
			
				} else {
					$response = [ 0, __("Invalid Operation", "library-management-system") ];
				}
			} elseif ( $param == "owt7_lms_category_form" ) {

				// Action Type
				$action_type = isset($_REQUEST['action_type']) ? $this->owt7_clean_text($_REQUEST['action_type']) : "";
				// Form Data
				$category  = isset($_REQUEST['owt7_txt_category_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_category_name']) : "";
				$status    = isset($_REQUEST['owt7_dd_category_status']) ? absint(wp_unslash($_REQUEST['owt7_dd_category_status'])) : 1;
				$edit_id   = isset($_REQUEST['edit_id']) ? absint(wp_unslash($_REQUEST['edit_id'])) : 0;

			
				// Check if action_type is not empty
				if ( !empty( $action_type ) ) { 
			
					// Handle "add" action
					if ( $action_type == "add" ) {
			
						if ( !empty( $category ) ) {
			
							// Check for available credits
							if ( $this->owt7_library_free_version_credit( $this->categoryTable, "categories" ) ) {
			
								// Check if category already exists
								$is_category_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->categoryTable} WHERE LOWER(TRIM(name)) = %s",
										strtolower(trim($category))
									)
								);
			
								if ( !empty( $is_category_exists ) ) {
									$response = [ 0, __("Category name already taken", "library-management-system") ];
								} else {
									// Insert the new category
									$wpdb->insert( $this->categoryTable, [
										"name" => $category,
										"status" => $status
									]);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [ 1, __("Successfully, Category added to LMS", "library-management-system") ];
									} else {
										$response = [ 0, __("Failed to add Category", "library-management-system") ];
									}
								}
			
							} else {
								$response = [ 0, __("Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system") ];
							}
			
						} else {
							$response = [ 0, __("Category value required", "library-management-system") ];
						}
			
					// Handle "edit" action
					} elseif ( $action_type == "edit" ) {
			
						if ( !empty( $category ) ) {
			
							// Check if category name is already taken (excluding current category)
							$category_have_same_id = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->categoryTable} WHERE LOWER(TRIM(name)) = %s AND id <> %d",
									strtolower(trim($category)), $edit_id
								)
							);
			
							if ( !empty( $category_have_same_id ) ) {
								$response = [ 0, __("Category name already taken", "library-management-system") ];
							} else {
								// Check if the category exists
								$is_category_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->categoryTable} WHERE id = %d",
										$edit_id
									)
								);
			
								if ( !empty( $is_category_exists ) ) {
									// Update category data
									$wpdb->update(
										$this->categoryTable,
										[ "name" => $category, "status" => $status ],
										[ "id" => $edit_id ]
									);
			
									$response = [ 1, __("Successfully, Category data updated", "library-management-system") ];
								} else {
									$response = [ 0, __("Category not found", "library-management-system") ];
								}
							}
			
						} else {
							$response = [ 0, __("Category value required", "library-management-system") ];
						}
			
					}
			
				} else {
					$response = [ 0, __("Invalid Operation", "library-management-system") ];
				}
			} elseif ( $param == "owt7_lms_book_form" ) {

				// Sanitize and prepare inputs
				$action_type          = isset($_REQUEST['action_type']) ? $this->owt7_clean_text($_REQUEST['action_type']) : '';
				$book_id              = isset($_REQUEST['owt7_txt_book_id']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_book_id']) : '';
				$category_id          = isset($_REQUEST['owt7_dd_category_id']) ? absint($_REQUEST['owt7_dd_category_id']) : 0;
				$bookcase_id          = isset($_REQUEST['owt7_dd_bookcase_id']) ? absint($_REQUEST['owt7_dd_bookcase_id']) : 0;
				$section_id           = isset($_REQUEST['owt7_dd_section_id']) ? absint($_REQUEST['owt7_dd_section_id']) : 0;
				$status               = isset($_REQUEST['owt7_dd_book_status']) ? absint($_REQUEST['owt7_dd_book_status']) : 1;
				$book_name            = isset($_REQUEST['owt7_txt_book_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_book_name']) : '';
				$author_name          = isset($_REQUEST['owt7_txt_author_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_author_name']) : '';
				$publication_name     = isset($_REQUEST['owt7_txt_publication_name']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_publication_name']) : '';
				$publication_year     = isset($_REQUEST['owt7_txt_publication_year']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_publication_year']) : '';
				$publication_location = isset($_REQUEST['owt7_txt_publication_location']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_publication_location']) : '';
				$cost                 = isset($_REQUEST['owt7_txt_cost']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_cost']) : '';
				$isbn                 = isset($_REQUEST['owt7_txt_isbn']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_isbn']) : '';
				$book_url             = isset($_REQUEST['owt7_txt_book_url']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_book_url']) : '';
				$quantity             = isset($_REQUEST['owt7_txt_quantity']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_quantity']) : '';
				$book_language        = isset($_REQUEST['owt7_txt_book_language']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_book_language']) : '';
				$total_pages          = isset($_REQUEST['owt7_txt_total_pages']) ? $this->owt7_clean_text($_REQUEST['owt7_txt_total_pages']) : '';
				$description = isset($_REQUEST['owt7_txt_description']) ? wp_kses_post(wp_unslash($_REQUEST['owt7_txt_description'])) : '';
				$cover_image          = isset($_REQUEST['owt7_cover_image']) ? $this->owt7_clean_text($_REQUEST['owt7_cover_image']) : '';
				$edit_id              = isset($_REQUEST['edit_id']) ? absint($_REQUEST['edit_id']) : 0;
			
				// Check for valid action type
				if ( !empty( $action_type ) ) {
			
					// Handle "add" action
					if ( $action_type == "add" ) {
			
						if ( !empty( $book_id ) || !empty( $category_id ) || !empty( $bookcase_id ) || !empty( $section_id ) || !empty( $book_name ) ) {
			
							// Check if credits are available
							if ( $this->owt7_library_free_version_credit( $this->booksTable ) ) {
			
								// Check if book already exists
								$is_book_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->booksTable} WHERE LOWER(TRIM(name)) = %s AND book_id = %s AND category_id = %d", strtolower( trim( $book_name ) ), $book_id, $category_id
									)
								);
			
								if ( !empty( $is_book_exists ) ) {
									$response = [ 0, __("Book name already taken", "library-management-system") ];
								} else {
									// Insert new book into the database
									$wpdb->insert( $this->booksTable, [
										"book_id" => $book_id,
										"bookcase_id" => $bookcase_id,
										"bookcase_section_id" => $section_id,
										"category_id" => $category_id,
										"author_name" => $author_name,
										"name" => $book_name,
										"publication_name" => $publication_name,
										"publication_year" => $publication_year,
										"publication_location" => $publication_location,
										"amount" => $cost,
										"cover_image" => $cover_image,
										"isbn" => $isbn,
										"book_url" => $book_url,
										"stock_quantity" => $quantity,
										"book_language" => $book_language,
										"book_pages" => $total_pages,
										"description" => $description,
										"status" => $status
									]);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [ 1, __("Successfully, Book added to LMS", "library-management-system") ];
									} else {
										$response = [ 0, __("Failed to add Book", "library-management-system") ];
									}
								}
			
							} else {
								$response = [ 0, __("Sorry, No Credits Left.<br/>Upgrade to Pro Version of LMS.", "library-management-system") ];
							}
			
						} else {
							$response = [ 0, __("Please fill required values", "library-management-system") ];
						}
			
					// Handle "edit" action
					} elseif ( $action_type == "edit" ) {
			
						if ( !empty( $book_id ) || !empty( $category_id ) || !empty( $bookcase_id ) || !empty( $section_id ) || !empty( $book_name ) ) {
			
							// Check if book with same data exists
							$book_have_same_data = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->booksTable} WHERE LOWER(TRIM(name)) = %s AND book_id = %s AND category_id = %d AND id <> %d", strtolower( trim( $book_name ) ), $book_id, $category_id, $edit_id
								)
							);
			
							if ( !empty( $book_have_same_data ) ) {
								$response = [ 0, __("Book name already taken", "library-management-system") ];
							} else {
								// Check if book exists by ID
								$is_book_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * FROM {$this->booksTable} WHERE id = %d", $edit_id
									)
								);
			
								if ( !empty( $is_book_exists ) ) {
									// Update book data in the database
									$wpdb->update( $this->booksTable, [
										"book_id" => $book_id,
										"bookcase_id" => $bookcase_id,
										"bookcase_section_id" => $section_id,
										"category_id" => $category_id,
										"author_name" => $author_name,
										"name" => $book_name,
										"publication_name" => $publication_name,
										"publication_year" => $publication_year,
										"publication_location" => $publication_location,
										"amount" => $cost,
										"cover_image" => $cover_image,
										"isbn" => $isbn,
										"book_url" => $book_url,
										"stock_quantity" => $quantity,
										"book_language" => $book_language,
										"book_pages" => $total_pages,
										"description" => $description,
										"status" => $status
									], [ "id" => $edit_id ]);
			
									$response = [ 1, __("Successfully, Book data updated", "library-management-system") ];
								} else {
									$response = [ 0, __("Book not found", "library-management-system") ];
								}
							}
			
						} else {
							$response = [ 0, __("Please fill required values", "library-management-system") ];
						}
					}
			
				} else {
					$response = [ 0, __("Invalid Operation", "library-management-system") ];
				}
			} elseif ( $param == "owt7_lms_filter_section" ) {
				// Get the Bookcase ID from request and sanitize it
				$bookcase_id = isset( $_REQUEST['bkcase_id'] ) ? absint( $_REQUEST['bkcase_id'] ) : 0;
			
				// Fetch sections based on the Bookcase ID and active status
				$sections = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, name FROM {$this->bookcaseSectionTable} WHERE bookcase_id = %d AND status = %d",
						$bookcase_id, 1
					)
				);
			
				// Check if any sections are found
				if ( !empty( $sections ) ) {
					// Return success response with sections data
					$response = [
						1, 
						__("Sections", "library-management-system"),
						[ "sections" => $sections ]
					];
				} else {
					// Return failure response if no sections found
					$response = [
						0, 
						__("No Section Found", "library-management-system")
					];
				}
			} elseif ( $param == "owt7_lms_filter_user" ) {
				// Get Branch ID from request and sanitize it
				$branch_id = isset( $_REQUEST['branch_id'] ) ? absint( $_REQUEST['branch_id'] ) : 0;
			
				// Fetch users based on Branch ID and active status
				$users = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, name FROM {$this->usersTable} WHERE branch_id = %d AND status = %d",
						$branch_id, 1
					)
				);
			
				// Check if any users are found
				if ( !empty( $users ) ) {
					$response = [
						1, 
						__("Users", "library-management-system"),
						[ "users" => $users ]
					];
				} else {
					$response = [
						0, 
						__("No User Found", "library-management-system")
					];
				}
			} elseif ( $param == "owt7_lms_filter_book" ) {
				// Get Category ID from request and sanitize it
				$category_id = isset( $_REQUEST['category_id'] ) ? absint( $_REQUEST['category_id'] ) : 0;
			
				// Fetch books based on Category ID and active status
				$books = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, name FROM {$this->booksTable} WHERE category_id = %d AND status = %d",
						$category_id, 1
					)
				);
			
				// Check if any books are found
				if ( !empty( $books ) ) {
					$response = [
						1, 
						__("Books", "library-management-system"),
						[ "books" => $books ]
					];
				} else {
					$response = [
						0, 
						__("No book found", "library-management-system")
					];
				}
			} elseif ( $param == "owt7_lms_borrow_book" ) {
				// Sanitize and assign required fields
				$branch_id = isset( $_REQUEST['owt7_dd_branch_id'] ) ? absint( $_REQUEST['owt7_dd_branch_id'] ) : 0;
				$u_id = isset( $_REQUEST['owt7_dd_u_id'] ) ? absint( $_REQUEST['owt7_dd_u_id'] ) : 0;
				$category_id = isset( $_REQUEST['owt7_dd_category_id'] ) ? absint( $_REQUEST['owt7_dd_category_id'] ) : 0;
				$book_id = isset( $_REQUEST['owt7_dd_book_id'] ) ? absint( $_REQUEST['owt7_dd_book_id'] ) : 0;
				$days_count = isset( $_REQUEST['owt7_dd_days'] ) ? absint( $_REQUEST['owt7_dd_days'] ) : 0;
			
				// Ensure all required fields are provided
				if ( !empty($branch_id) && !empty($u_id) && !empty($category_id) && !empty($book_id) && !empty($days_count) ) {
					// Check if the user has already borrowed the same book
					$has_book_borrowed = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM {$this->booksBorrowTable} WHERE u_id = %d AND book_id = %d AND status = 1",
							$u_id, $book_id
						)
					);
			
					if ( !empty( $has_book_borrowed ) ) {
						$response = [
							0, 
							__("Failed, This Book is already borrowed by the User.", "library-management-system")
						];
					} else {
						// Check if the user has already borrowed a book
						$borrowed_books = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * FROM {$this->booksBorrowTable} WHERE u_id = %d AND status = 1",
								$u_id
							)
						);
			
						if ( !empty( $borrowed_books ) ) {
							$response = [
								0, 
								__("Want to Borrow More than 1 Book? Upgrade to Pro Version", "library-management-system")
							];
						} else {
							// Check if there are any late fines for the user
							$book_fine_details = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM {$this->booksLateFineTable} WHERE u_id = %d AND has_paid = 1 AND status = 1",
									$u_id
								)
							);
			
							if ( !empty( $book_fine_details ) ) {
								$response = [
									0, 
									__("Failed to Borrow Book. User has a late fine.", "library-management-system")
								];
							} else {
								// Manage book stock and proceed with borrowing
								if ( $this->owt7_library_manage_books_stock( $book_id, "minus" ) ) {
									// Generate unique borrow ID
									$borrow_id = $this->owt7_library_generate_unique_identifier( 8 );
									$currentDate = new DateTime();
									$currentDate->modify( "+" . $days_count . " days" );
									$newDate = $currentDate->format( 'Y-m-d' );
			
									// Insert borrow record
									$wpdb->insert( $this->booksBorrowTable, [
										"borrow_id" => $borrow_id,
										"category_id" => $category_id,
										"book_id" => $book_id,
										"branch_id" => $branch_id,
										"u_id" => $u_id,
										"borrows_days" => $days_count,
										"return_date" => $newDate,
										"status" => 1
									]);
			
									if ( $wpdb->insert_id > 0 ) {
										$response = [
											1, 
											__("Successfully, Book borrowed", "library-management-system")
										];
									} else {
										$response = [
											0, 
											__("Failed to borrow book", "library-management-system")
										];
									}
								} else {
									$response = [
										0, 
										__("Failed, Book is Out of Stock.", "library-management-system")
									];
								}
							}
						}
					}
				} else {
					$response = [
						0, 
						__("All fields are required", "library-management-system")
					];
				}
			} elseif ( $param == 'owt7_lms_filter_borrow_book' ) {

				$u_id = isset( $_REQUEST['u_id'] ) ? absint( $_REQUEST['u_id'] ) : '';
			
				$borrowed_books = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT borrow.id, 
							(SELECT book.name FROM {$this->booksTable} AS book WHERE book.id = borrow.book_id LIMIT 1) AS book_name 
						FROM {$this->booksBorrowTable} AS borrow 
						WHERE borrow.status = %d 
						AND borrow.u_id = %d", 1, $u_id
					)
				);
			
				if ( ! empty( $borrowed_books ) ) {
					$response = [
						1,
						'Books',
						[
							'books' => $borrowed_books
						]
					];
				} else {
					$response = [
						0,
						__( 'No book found', 'library-management-system' )
					];
				}
			} elseif ( $param == 'owt7_lms_return_book' ) {
			
				$borrow_ids = isset( $_REQUEST['owt7_borrow_books_id'] ) ? $_REQUEST['owt7_borrow_books_id'] : '';
			
				if ( ! empty( $borrow_ids ) && is_array( $borrow_ids ) ) {
			
					foreach ( $borrow_ids as $borrow_id ) {
			
						$book_borrow_details = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * FROM {$this->booksBorrowTable} 
								WHERE id = %d AND status = %d", $borrow_id, 1
							)
						);
			
						if ( ! empty( $book_borrow_details ) ) {
			
							// Late Fine Calculation
							$borrow_days = intval( $book_borrow_details->borrows_days );
							$return_date = gmdate( 'Y-m-d' );
							$borrow_date = gmdate( 'Y-m-d', strtotime( $book_borrow_details->created_at ) );

							$borrow_date_obj = new DateTime( $borrow_date );
							$return_date_obj = new DateTime( $return_date );
			
							// Calculate the difference in days
							$date_diff = $borrow_date_obj->diff( $return_date_obj )->days;
							$total_late_fine = 0;
							$extra_days = 0;
			
							// Check if the difference in days exceeds the borrow_days
							if ( $date_diff > $borrow_days ) {
								$extra_days = $date_diff - $borrow_days;
								$per_day_late_fine = get_option( 'owt7_lms_late_fine_currency' );
								$total_late_fine = $extra_days * intval( $per_day_late_fine );
							}
			
							if ( $this->owt7_library_manage_books_stock( $book_borrow_details->book_id, 'plus' ) ) {
								// Insert into Return History
								$wpdb->insert( 
									$this->booksReturnTable, 
									[
										'borrow_id'    => $book_borrow_details->borrow_id,
										'category_id'  => $book_borrow_details->category_id,
										'book_id'      => $book_borrow_details->book_id,
										'branch_id'    => $book_borrow_details->branch_id,
										'u_id'         => $book_borrow_details->u_id,
										'has_fine_status' => $extra_days > 0 ? 1 : 0,
										'status'       => 1
									]
								);
			
								// Manage Late Fine History
								if ( $extra_days > 0 && $total_late_fine > 0 ) {
									$wpdb->insert( 
										$this->booksLateFineTable, 
										[
											'return_id'   => $wpdb->insert_id,
											'book_id'     => $book_borrow_details->book_id,
											'u_id'        => $book_borrow_details->u_id,
											'extra_days'  => $extra_days,
											'fine_amount' => $total_late_fine,
											'status'      => 1,
											'has_paid'    => 1 // 1 - Not paid, 2 - Paid
										]
									);
								}
			
								// Update Borrow History
								$wpdb->update(
									$this->booksBorrowTable, 
									[ 'status' => 0 ], 
									[ 'id' => $borrow_id ]
								);
							}
						}
					}
			
					$response = [
						1,
						__( 'Successfully, Book(s) Returned', 'library-management-system' )
					];
			
				} else {
					$response = [
						0,
						__( 'Please Select Book(s) to be Return', 'library-management-system' )
					];
				}
			} elseif ( $param == 'owt7_lms_data_filters' ) {
			
				$filterby    = isset( $_REQUEST['filterby'] ) ? sanitize_text_field( trim( $_REQUEST['filterby'] ) ) : '';
				$list        = isset( $_REQUEST['list'] ) ? sanitize_text_field( trim( $_REQUEST['list'] ) ) : '';
				$filterbyId  = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;
			
				if ( ! empty( $filterby ) ) {
			
					$borrows = [];
			
					if ( $filterby == 'category' && intval( $filterbyId ) > 0 ) {
			
						if ( ! empty( $list ) && $list == 'borrow_history' ) {
			
							$borrows = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT borrow.id, borrow.borrow_id, borrow.borrows_days, borrow.return_date, borrow.status, borrow.created_at, 
										(SELECT category.name 
											FROM {$this->categoryTable} AS category 
											WHERE category.id = borrow.category_id LIMIT 1) AS category_name,
										(SELECT book.name 
											FROM {$this->booksTable} AS book 
											WHERE book.id = borrow.book_id LIMIT 1) AS book_name,
										(SELECT branch.name 
											FROM {$this->branchTable} AS branch 
											WHERE branch.id = borrow.branch_id LIMIT 1) AS branch_name,
										(SELECT user.name 
											FROM {$this->usersTable} AS user 
											WHERE user.id = borrow.u_id LIMIT 1) AS user_name
									FROM {$this->booksBorrowTable} AS borrow 
									WHERE borrow.category_id = %d 
									ORDER BY borrow.id DESC",
									$filterbyId
								)
							);								
						} elseif ( ! empty( $list ) && $list == 'return_history' ) {
			
							$returns = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT rt.id, rt.borrow_id, rt.status, rt.created_at, (SELECT category.name FROM {$this->categoryTable} AS category 
											WHERE category.id = rt.category_id LIMIT 1) AS category_name,
										(SELECT book.name 
											FROM {$this->booksTable} AS book 
											WHERE book.id = rt.book_id LIMIT 1) AS book_name,
										(SELECT branch.name 
											FROM {$this->branchTable} AS branch 
											WHERE branch.id = rt.branch_id LIMIT 1) AS branch_name,
										(SELECT user.name 
											FROM {$this->usersTable} AS user 
											WHERE user.id = rt.u_id LIMIT 1) AS user_name,
										(SELECT borrow.borrows_days 
											FROM {$this->booksBorrowTable} AS borrow 
											WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS total_days,
										(SELECT borrow.created_at 
											FROM {$this->booksBorrowTable} AS borrow 
											WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS issued_on
									FROM {$this->booksReturnTable} AS rt 
									WHERE rt.category_id = %d 
									ORDER BY rt.id DESC",
									$filterbyId
								)
							);								
						}
					} elseif ( $filterby == 'branch' && intval( $filterbyId ) > 0 ) {
			
						if ( ! empty( $list ) && $list == 'borrow_history' ) {
			
							$borrows = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT borrow.id, borrow.borrow_id, borrow.borrows_days, borrow.return_date, borrow.status, borrow.created_at, 
										(SELECT category.name 
											FROM {$this->categoryTable} AS category 
											WHERE category.id = borrow.category_id LIMIT 1) AS category_name,
										(SELECT book.name 
											FROM {$this->booksTable} AS book 
											WHERE book.id = borrow.book_id LIMIT 1) AS book_name,
										(SELECT branch.name 
											FROM {$this->branchTable} AS branch 
											WHERE branch.id = borrow.branch_id LIMIT 1) AS branch_name,
										(SELECT user.name 
											FROM {$this->usersTable} AS user 
											WHERE user.id = borrow.u_id LIMIT 1) AS user_name
									FROM {$this->booksBorrowTable} AS borrow 
									WHERE borrow.branch_id = %d 
									ORDER BY borrow.id DESC",
									$filterbyId
								)
							);								
						} elseif ( ! empty( $list ) && $list == 'return_history' ) {
			
							$returns = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT rt.id, rt.borrow_id, rt.status, rt.created_at, 
											(SELECT category.name 
												FROM {$this->categoryTable} AS category 
												WHERE category.id = rt.category_id LIMIT 1) AS category_name,
											(SELECT book.name 
												FROM {$this->booksTable} AS book 
												WHERE book.id = rt.book_id LIMIT 1) AS book_name,
											(SELECT branch.name 
												FROM {$this->branchTable} AS branch 
												WHERE branch.id = rt.branch_id LIMIT 1) AS branch_name,
											(SELECT user.name 
												FROM {$this->usersTable} AS user 
												WHERE user.id = rt.u_id LIMIT 1) AS user_name,
											(SELECT borrow.borrows_days 
												FROM {$this->booksBorrowTable} AS borrow 
												WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS total_days,
											(SELECT borrow.created_at 
												FROM {$this->booksBorrowTable} AS borrow 
												WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) AS issued_on
										FROM {$this->booksReturnTable} AS rt 
										WHERE rt.branch_id = %d 
										ORDER BY rt.id DESC",
									$filterbyId
								)
							);								
						}
					}
			
					if ( ! empty( $list ) && $list == 'borrow_history' ) {
			
						if ( ! empty( $borrows ) ) {
							ob_start();
							// Template Variables
							$params['borrows'] = $borrows;
							include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_borrow_list.php';
							$template = ob_get_contents();
							ob_end_clean();
							// Output
							$response = [
								1,
								'Book(s) Borrow List',
								[
									'template' => $template
								]
							];
						} else {
							$response = [
								0,
								__( 'No data found', 'library-management-system' )
							];
						}
					} elseif ( ! empty( $list ) && $list == 'return_history' ) {
			
						if ( ! empty( $returns ) ) {
							ob_start();
							// Template Variables
							$params['returns'] = $returns;
							include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_list.php';
							$template = ob_get_contents();
							ob_end_clean();
							// Output
							$response = [
								1,
								'Book(s) Return List',
								[
									'template' => $template
								]
							];
						} else {
							$response = [
								0,
								__( 'No data found', 'library-management-system' )
							];
						}
					}
				} else {
					$response = [
						0,
						__( 'Invalid LMS operation', 'library-management-system' )
					];
				}
			} elseif ( $param == 'owt7_lms_data_settings' ) {

				$type = isset( $_REQUEST['owt7_lms_settings_type'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_settings_type'] ) ) : '';
			
				if ( ! empty( $type ) && $type == 'late_fine' ) {
			
					$amount = isset( $_REQUEST['owt7_lms_fine_amount'] ) ? absint( $_REQUEST['owt7_lms_fine_amount'] ) : 0;
					update_option( 'owt7_lms_late_fine_currency', intval( $amount ) );
			
					$response = [
						1,
						__( 'Successfully, LMS Settings updated', 'library-management-system' )
					];
			
				} elseif ( ! empty( $type ) && $type == 'country_currency' ) {
			
					$country = isset( $_REQUEST['owt7_lms_country'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_country'] ) ) : '';
					$currency = isset( $_REQUEST['owt7_lms_currency'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_currency'] ) ) : '';
					update_option( 'owt7_lms_country', $country );
					update_option( 'owt7_lms_currency', $currency );
			
					$response = [
						1,
						__( 'Successfully, LMS Settings updated', 'library-management-system' )
					];
				}
			} elseif ( $param == 'owt7_lms_data_option_filters' ) {
			
				$filter_value = isset( $_REQUEST['value'] ) ? sanitize_text_field( $_REQUEST['value'] ) : 'all';
				$filter_by   = isset( $_REQUEST['filterBy'] ) ? sanitize_text_field( trim( $_REQUEST['filterBy'] ) ) : '';
				$module      = isset( $_REQUEST['module'] ) ? sanitize_text_field( trim( $_REQUEST['module'] ) ) : '';
			
				if ( ! empty( $module ) ) {
			
					// Books
					if ( $module == 'books' && $filter_by == 'category' ) {
			
						if ( $filter_value == 'all' ) {
			
							// All Books
							$books = $wpdb->get_results(
								"SELECT book.id, book.book_id, book.name, book.stock_quantity, book.status, book.created_at, 
										(SELECT category.name 
											FROM {$this->categoryTable} AS category 
											WHERE category.id = book.category_id LIMIT 1) AS category_name, 
										(SELECT bkcase.name 
											FROM {$this->bookcaseTable} AS bkcase 
											WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name, 
										(SELECT section.name 
											FROM {$this->bookcaseSectionTable} AS section 
											WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name
									FROM {$this->booksTable} AS book"
							);								
						} elseif ( $filter_value > 0 ) {
			
							// Filtered Books
							$books = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT book.id, book.book_id, book.name, book.stock_quantity, book.status, book.created_at, 
										(SELECT category.name 
											FROM {$this->categoryTable} AS category 
											WHERE category.id = book.category_id LIMIT 1) AS category_name, 
										(SELECT bkcase.name 
											FROM {$this->bookcaseTable} AS bkcase 
											WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name, 
										(SELECT section.name 
											FROM {$this->bookcaseSectionTable} AS section 
											WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name
									FROM {$this->booksTable} AS book WHERE book.category_id = %d",
									$filter_value
								)	
							);	
							//echo $wpdb->last_query;							
						}
			
						$module_folder = 'books';
					} elseif ( $module == 'sections' && $filter_by == 'bookcase' ) { // Bookcases
			
						if ( $filter_value == 'all' ) {
			
							// All Sections
							$sections = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT sec.*, bkcase.name AS bookcase_name 
										FROM {$this->bookcaseSectionTable} sec 
										INNER JOIN {$this->bookcaseTable} bkcase 
										ON sec.bookcase_id = bkcase.id"
								)
							);				
						} elseif ( $filter_value > 0 ) {
			
							// Filtered Sections
							$sections = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT sec.*, bkcase.name AS bookcase_name 
										FROM {$this->bookcaseSectionTable} sec 
										INNER JOIN {$this->bookcaseTable} bkcase 
										ON sec.bookcase_id = bkcase.id 
										WHERE sec.bookcase_id = %d",
									$filter_value
								)
							);								
						}
			
						$module_folder = 'bookcases';
					} elseif ( $module == 'users' && $filter_by == 'branch' ) { // Users
			
						if ( $filter_value == 'all' ) {
			
							// All Users
							$users = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT user.*, (SELECT name FROM {$this->branchTable} AS branch 
											WHERE branch.id = user.branch_id LIMIT 1) AS branch_name 
										FROM {$this->usersTable} AS user"
								)
							);				
						} elseif ( $filter_value > 0 ) {
			
							// Filtered Users
							$users = $wpdb->get_results(
								$wpdb->prepare(
									"SELECT user.*, 
										(SELECT name FROM {$this->branchTable} AS branch WHERE branch.id = user.branch_id LIMIT 1) AS branch_name FROM $this->usersTable AS user WHERE user.branch_id = %d",
									$filter_value
								)
							);								
						}
			
						$module_folder = 'users';
					}
			
					$params[$module] = ${$module};
			
					ob_start();
					include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/{$module_folder}/templates/owt7_library_{$module}_list.php";
					$template = ob_get_contents();
					ob_end_clean();
			
					$response = [
						1,
						ucfirst( $module ),
						[
							'template' => $template
						]
					];
			
				} else {
			
					$response = [
						0,
						__( 'Invalid LMS Module', 'library-management-system' )
					];
				}
			} elseif ( $param == 'owt7_lms_import_test_data' ) {

				global $wp_filesystem;
			
				// Remove existing data
				$truncate_table_names = [
					$this->categoryTable,
					$this->booksTable,
					$this->bookcaseTable,
					$this->bookcaseSectionTable,
					$this->branchTable,
					$this->usersTable
				];
				foreach ( $truncate_table_names as $table ) {
					$wpdb->query( "TRUNCATE TABLE {$table}" );
				}
			
				$directory = LIBMNS_PLUGIN_DIR_PATH . 'admin/sample-data';
				$files = array_diff( scandir( $directory ), [ '..', '.' ] );
			
				$import_status = false;
			
				foreach ( $files as $file ) {
			
					$csv_file_path = $directory . '/' . $file;
			
					if ( $wp_filesystem->exists( $csv_file_path ) ) {
			
						if ( pathinfo( $file, PATHINFO_EXTENSION ) == 'csv' ) {
			
							$file_name = pathinfo( $file, PATHINFO_FILENAME );
							$csv_content = $wp_filesystem->get_contents( $csv_file_path );
			
							// Parse CSV content
							$csv_lines = explode( "\n", $csv_content );
							$data_columns = str_getcsv( array_shift( $csv_lines ) );
			
							$csv_data = [];
							foreach ( $csv_lines as $line ) {
								if ( ! empty( $line ) ) {
									$csv_data[] = str_getcsv( $line );
								}
							}
			
							if ( ! empty( $csv_data ) ) {
			
								$table_name = '';
								$credit = base64_decode( LIBMNS_FREE_VERSION_LIMIT );
			
								if ( $file_name == 'categories' ) {
									$csv_data = array_slice( $csv_data, 0, ( $credit - 10 ) );
									$table_name = $this->categoryTable;
								} elseif ( $file_name == 'books' ) {
									$csv_data = array_slice( $csv_data, 0, $credit );
									$table_name = $this->booksTable;
								} elseif ( $file_name == 'bookcases' ) {
									$csv_data = array_slice( $csv_data, 0, ( $credit - 10 ) );
									$table_name = $this->bookcaseTable;
								} elseif ( $file_name == 'sections' ) {
									$csv_data = array_slice( $csv_data, 0, $credit );
									$table_name = $this->bookcaseSectionTable;
								} elseif ( $file_name == 'branches' ) {
									$csv_data = array_slice( $csv_data, 0, ( $credit - 10 ) );
									$table_name = $this->branchTable;
								} elseif ( $file_name == 'users' ) {
									$csv_data = array_slice( $csv_data, 0, $credit );
									$table_name = $this->usersTable;
								}
			
								$each_row = [];
								// Attach column with Data
								foreach ( $csv_data as $data ) {
									for ( $row_count = 0; $row_count < count( $data_columns ); $row_count++ ) {
										$each_row[ $data_columns[ $row_count ] ] = $data[ $row_count ];
									}
									$wpdb->insert( $table_name, $each_row );
								}
			
								$import_status = true;
							}
						}
					}
				}
			
				if ( $import_status ) {
					// Test Data Flag
					update_option( 'owt7_lms_test_data', 1 );
			
					$response = [
						1,
						__( 'Successfully, Test Data Imported to LMS', 'library-management-system' )
					];
				} else {
					$response = [
						0,
						__( 'Failed to Import Test data', 'library-management-system' )
					];
				}
			} elseif ( $param === 'owt7_lms_remove_test_data' ) {

				// Remove existing data
				$truncate_table_names = [
					$this->categoryTable,
					$this->booksTable,
					$this->bookcaseTable,
					$this->bookcaseSectionTable,
					$this->branchTable,
					$this->usersTable,
				];
			
				foreach ( $truncate_table_names as $table ) {
					$wpdb->query( "TRUNCATE TABLE {$table}" );
				}
			
				delete_option( 'owt7_lms_test_data' );
			
				$response = [
					1,
					__( 'Successfully, Test Data Removed', 'library-management-system' ),
				];
			
			} elseif ( $param === 'owt7_pay_late_fine' ) {
			
				$return_id = isset( $_REQUEST['return_id'] ) ? absint( base64_decode( $_REQUEST['return_id'] ) ) : 0;
			
				$book_fine_details = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM {$this->booksLateFineTable} WHERE return_id = %d AND has_paid = %d",
						$return_id, 1
					)
				);					
			
				if ( ! empty( $book_fine_details ) ) {
					$wpdb->update(
						$this->booksLateFineTable,
						[
							'has_paid' => 2,
							'status'   => 0,
						],
						[
							'return_id' => $return_id,
						]
					);
			
					$wpdb->update(
						$this->booksReturnTable,
						[
							'has_fine_status' => 0,
						],
						[
							'id' => $return_id,
						]
					);
			
					$response = [
						1,
						__( 'Successfully, Late Fine Paid.', 'library-management-system' ),
					];
				} else {
					$response = [
						0,
						__( 'Fine already paid', 'library-management-system' ),
					];
				}
			
			} else {
			
				$response = [
					0,
					__( 'Invalid LMS Operation', 'library-management-system' ),
				];
			
			}																																																
		} else{

			$response = [
				0, 
				__("Invalid LMS operation", "library-management-system")
			];
		}

		wp_send_json($this->json($response));

		wp_die();
	}
}