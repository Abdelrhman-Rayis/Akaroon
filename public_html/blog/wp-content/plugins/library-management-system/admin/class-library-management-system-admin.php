<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://onlinewebtutorhub.blogspot.in/
 * @since      1.0.0
 *
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin
 * @author     Online Web Tutor <onlinewebtutorhub@gmail.com>
 */
class Library_Management_System_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    private $table_activator;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        require_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'includes/class-library-management-system-activator.php';
        $this->table_activator = new Library_Management_System_Activator();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function owt_library_enqueue_styles() {

        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : "";

        $plugin_valid_pages = array(
            "owt-lib-manage",
            "owt-lib-manage-students",
            "owt-lib-manage-staffs",
            "owt-lib-manage-books",
            "owt-lib-book-issue-list",
            "owt-lib-return-book-list",
            "owt-lib-manage-settings",
            "owt-lib-create-student",
            "owt-lib-create-staff",
            "owt-lib-create-book",
            "owt-lib-create-book-issue",
            "owt-lib-create-book-return",
            "owt-lib-staff-book-return",
            "owt-lib-staff-book-issue"
        );
        if (in_array($page, $plugin_valid_pages)) {
            //stylesheet files
            wp_enqueue_style("owt-lib-bootstrap", OWT_LIBRARY_PLUGIN_URL . 'assets/css/bootstrap.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-font-icons", OWT_LIBRARY_PLUGIN_URL . 'assets/fonts/material-icons.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-materialsdesignicons", OWT_LIBRARY_PLUGIN_URL . 'assets/css/materialdesignicons.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-datatable", OWT_LIBRARY_PLUGIN_URL . 'assets/css/jquery.dataTables.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-sweetalert", OWT_LIBRARY_PLUGIN_URL . 'assets/css/sweetalert.css', array(), $this->version, 'all');
            wp_enqueue_style("buttons.dataTables", OWT_LIBRARY_PLUGIN_URL . 'assets/css/buttons.dataTables.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-toastr", OWT_LIBRARY_PLUGIN_URL . 'assets/css/toastr.min.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-custom-css", OWT_LIBRARY_PLUGIN_URL . 'admin/css/library-management-system-admin.css', array(), $this->version, 'all');
            wp_enqueue_style("owt-lib-global", OWT_LIBRARY_PLUGIN_URL . 'assets/css/owt-lib-global.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function owt_library_enqueue_scripts() {
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : "";

        $plugin_valid_pages = array(
            "owt-lib-manage",
            "owt-lib-manage-students",
            "owt-lib-manage-staffs",
            "owt-lib-manage-books",
            "owt-lib-book-issue-list",
            "owt-lib-return-book-list",
            "owt-lib-manage-settings",
            "owt-lib-create-student",
            "owt-lib-create-staff",
            "owt-lib-create-book",
            "owt-lib-create-book-issue",
            "owt-lib-create-book-return",
            "owt-lib-staff-book-return",
            "owt-lib-staff-book-issue"
        );
        if (in_array($page, $plugin_valid_pages)) {
            // javascript files
            wp_enqueue_script("bootstrap", OWT_LIBRARY_PLUGIN_URL . 'assets/js/bootstrap.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("datatable", OWT_LIBRARY_PLUGIN_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("validate", OWT_LIBRARY_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("sweatalert", OWT_LIBRARY_PLUGIN_URL . 'assets/js/sweetalert.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("buttons.html5", OWT_LIBRARY_PLUGIN_URL . 'assets/js/buttons.html5.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("dataTables.buttons", OWT_LIBRARY_PLUGIN_URL . 'assets/js/dataTables.buttons.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("pdfmake", OWT_LIBRARY_PLUGIN_URL . 'assets/js/pdfmake.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script("vfs_fonts", OWT_LIBRARY_PLUGIN_URL . 'assets/js/vfs_fonts.js', array('jquery'), $this->version, true);
            wp_enqueue_script("toastr", OWT_LIBRARY_PLUGIN_URL . 'assets/js/toastr.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name, OWT_LIBRARY_PLUGIN_URL . 'admin/js/library-management-system-admin.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name, "owt_lib", array(
                "ajaxurl" => admin_url("admin-ajax.php"),
                "owt_lib_prefix" => OWT_LIBRARY_PLUGIN_PREFIX
            ));
        }
    }

    public function owt_library_admin_menus() {

        add_menu_page("Library Management", "Library Management", "manage_options", "owt-lib-manage", array($this, "owt_library_management_dashbaord"), "dashicons-book-alt", 58);
        add_submenu_page("owt-lib-manage", "Dashboard", "Dashboard", "manage_options", "owt-lib-manage", array($this, "owt_library_management_dashbaord"));
        add_submenu_page("owt-lib-manage", "Students", "Students", "manage_options", "owt-lib-manage-students", array($this, "owt_library_students_management"));
        add_submenu_page("owt-lib-manage", "Staffs", "Staffs", "manage_options", "owt-lib-manage-staffs", array($this, "owt_library_staffs_management"));
        add_submenu_page("owt-lib-manage", "Books", "Books", "manage_options", "owt-lib-manage-books", array($this, "owt_library_books_management"));
        add_submenu_page("owt-lib-manage", "Issue Book", "Issue Book", "manage_options", "owt-lib-create-book-issue", array($this, "owt_library_create_new_issue"));
        add_submenu_page("owt-lib-manage", "Student Issued", "Student Issued", "manage_options", "owt-lib-book-issue-list", array($this, "owt_library_book_issue_list"));
        add_submenu_page("owt-lib-manage", "Faculty Issued", "Faculty Issued", "manage_options", "owt-lib-staff-book-issue", array($this, "owt_library_staff_book_issue"));
        add_submenu_page("owt-lib-manage", "Return Book", "Return Book", "manage_options", "owt-lib-create-book-return", array($this, "owt_library_create_new_return"));
        add_submenu_page("owt-lib-manage", "Student Returned", "Student Returned", "manage_options", "owt-lib-return-book-list", array($this, "owt_library_return_book_list"));
        add_submenu_page("owt-lib-manage", "Faculty Returned", "Faculty Returned", "manage_options", "owt-lib-staff-book-return", array($this, "owt_library_staff_book_return"));
        add_submenu_page("owt-lib-manage", "Settings Panel", "Settings Panel", "manage_options", "owt-lib-manage-settings", array($this, "owt_library_settings_panel"));
        add_submenu_page("owt-lib-manage", "", "", "manage_options", "owt-lib-create-book", array($this, "owt_library_create_book"));
        add_submenu_page("owt-lib-manage", "", "", "manage_options", "owt-lib-create-staff", array($this, "owt_library_create_staff"));
        add_submenu_page("owt-lib-manage", "", "", "manage_options", "owt-lib-create-student", array($this, "owt_library_create_student"));
    }

    public function owt_library_staff_book_return() {

        global $wpdb;

        $staff_book_returns = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT rt.id as return_id,issue.created_at as issued_date, staff.staff_id, staff.name, staff.email, staff.phone_no, book.name as book_name, category.name as cat_name ,rt.has_fine_status,rt.created_at as returned_date from " . $this->table_activator->owt_library_tbl_book_return() . " rt INNER JOIN " . $this->table_activator->owt_library_tbl_book_issue() . " issue ON rt.book_issue_id = issue.issue_id INNER JOIN " . $this->table_activator->owt_library_tbl_user_type() . " utype ON issue.user_type_id = utype.id INNER JOIN " . $this->table_activator->owt_library_tbl_teachers_staff() . " staff ON staff.id = issue.user_id INNER JOIN " . $this->table_activator->owt_library_tbl_books() . " book ON book.id = issue.book_id INNER JOIN " . $this->table_activator->owt_library_tbl_category() . " category ON category.id = book.category_id WHERE utype.type != %s AND staff.status = %d", 'student', 1
                )
        );

        $book_returns = array();
        if (!empty($staff_book_returns) && count($staff_book_returns) > 0) {

            foreach ($staff_book_returns as $index => $return) {

                $book_returns[] = array(
                    "return_id" => $return->return_id,
                    "id" => $return->staff_id,
                    "name" => $return->name,
                    "email" => $return->email,
                    "phone_no" => $return->phone_no,
                    "book_name" => $return->book_name,
                    "cat_name" => $return->cat_name,
                    "issued_date" => $return->issued_date,
                    "returned_date" => $return->returned_date,
                    "fine_status" => $return->has_fine_status
                );
            }
        }
        $this->owt_library_include_template_file("report/owt-lms-staff-book-return-list", array("book_returns" => $book_returns));
    }

    public function owt_library_staff_book_issue() {
        global $wpdb;

        $book_issues_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE status = %d ORDER BY id desc", 1
                )
        );
        $issue_list = array();
        if (!empty($book_issues_list)) {

            foreach ($book_issues_list as $index => $issue) {

                $utype = $this->owt_library_get_usertype_info($issue->user_type_id);
                $name = '';
                $email = '';
                $u_id = '';
                if ($utype != "student") {
                    $staff_info = $this->owt_library_get_staff_info($issue->user_id);
                    $name = $staff_info->name;
                    $email = $staff_info->email;
                    $u_id = $staff_info->staff_id;

                    $issue_list[] = array(
                        "issue_id" => $issue->issue_id,
                        "category" => $this->owt_library_get_category_info($issue->category_id),
                        "book" => $this->owt_library_get_book_info($issue->book_id),
                        "user_type" => $this->owt_library_get_usertype_info($issue->user_type_id),
                        "name" => $name,
                        "email" => $email,
                        "created" => $issue->created_at,
                        "count_days" => $issue->duration_days,
                        "user_id" => $u_id
                    );
                }
            }
        }

        $this->owt_library_include_template_file("report/owt-lms-staff-book-issue-list", array("issues" => $issue_list));
    }

    public function owt_library_create_new_return() {
        global $wpdb;

        $user_types = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,type from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE status = %d", 1
                )
        );

        $student_branch = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,name from " . $this->table_activator->owt_library_tbl_branch() . " WHERE status = %d", 1
                )
        );

        $this->owt_library_include_template_file("report/owt-lms-entry-return-book", array("data" => $user_types, "branches" => $student_branch));
    }

    public function owt_library_create_new_issue() {

        global $wpdb;

        $categories = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,name from " . $this->table_activator->owt_library_tbl_category() . " WHERE status = %d", 1
                )
        );

        $student_branch = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,name from " . $this->table_activator->owt_library_tbl_branch() . " WHERE status = %d", 1
                )
        );

        $staff_types = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,type from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE status = %d", 1
                )
        );

        $this->owt_library_include_template_file("report/owt-lms-create-issue-book", array("categories" => $categories, "staff_types" => $staff_types, "branches" => $student_branch));
    }

    public function owt_library_return_book_list() {

        global $wpdb;

        $student_book_returns = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT rt.id as return_id,issue.created_at as issued_date, student.student_id, student.name, student.email, student.phone_no, book.name as book_name, category.name as cat_name ,rt.has_fine_status,rt.created_at as returned_date from " . $this->table_activator->owt_library_tbl_book_return() . " rt INNER JOIN " . $this->table_activator->owt_library_tbl_book_issue() . " issue ON rt.book_issue_id = issue.issue_id INNER JOIN " . $this->table_activator->owt_library_tbl_user_type() . " utype ON issue.user_type_id = utype.id INNER JOIN " . $this->table_activator->owt_library_tbl_students() . " student ON student.id = issue.user_id INNER JOIN " . $this->table_activator->owt_library_tbl_books() . " book ON book.id = issue.book_id INNER JOIN " . $this->table_activator->owt_library_tbl_category() . " category ON category.id = book.category_id WHERE utype.type = %s AND student.status = %d", 'student', 1
                )
        );
        $book_returns = array();
        if (!empty($student_book_returns) && count($student_book_returns) > 0) {

            foreach ($student_book_returns as $index => $return) {

                $book_returns[] = array(
                    "return_id" => $return->return_id,
                    "id" => $return->student_id,
                    "name" => $return->name,
                    "email" => $return->email,
                    "phone_no" => $return->phone_no,
                    "book_name" => $return->book_name,
                    "cat_name" => $return->cat_name,
                    "issued_date" => $return->issued_date,
                    "returned_date" => $return->returned_date,
                    "fine_status" => $return->has_fine_status
                );
            }
        }
        $this->owt_library_include_template_file("report/owt-lms-book-return-list", array("return_books" => $student_book_returns, "book_returns" => $book_returns));
    }

    public function owt_library_book_issue_list() {
        global $wpdb;

        $book_issues_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE status = %d ORDER BY id desc", 1
                )
        );
        $issue_list = array();
        if (!empty($book_issues_list)) {

            foreach ($book_issues_list as $index => $issue) {

                $utype = $this->owt_library_get_usertype_info($issue->user_type_id);
                $name = '';
                $email = '';
                $u_id = '';
                if ($utype == "student") {
                    $student_info = $this->owt_library_get_student_info($issue->user_id);
                    $name = isset($student_info->name) ? $student_info->name : "";
                    $email = isset($student_info->email) ? $student_info->email : "";
                    $u_id = isset($student_info->student_id) ? $student_info->student_id : "";
                    $branch_name = $this->owt_library_get_branch_info($issue->branch_id);
                    $issue_list[] = array(
                        "issue_id" => $issue->issue_id,
                        "category" => $this->owt_library_get_category_info($issue->category_id),
                        "book" => $this->owt_library_get_book_info($issue->book_id),
                        "user_type" => $this->owt_library_get_usertype_info($issue->user_type_id),
                        "name" => $name,
                        "email" => $email,
                        "branch" => $branch_name,
                        "created" => $issue->created_at,
                        "count_days" => $issue->duration_days,
                        "user_id" => $u_id
                    );
                }
            }
        }

        $this->owt_library_include_template_file("report/owt-lms-student-book-issue-list", array("issues" => $issue_list));
    }

    public function owt_library_create_book() {

        global $wpdb;

        $categories = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT id,name from " . $this->table_activator->owt_library_tbl_category() . " WHERE status = %d", 1
                )
        );

        $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : "add";
        $find_book = array();
        $valid_opt_fn = array("edit", "delete", "view");
        if (!empty($action) && in_array($action, $valid_opt_fn)) {

            $bkid = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : 0;

            if ($bkid > 0) {

                $find_book = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE id = %d", $bkid
                        ), ARRAY_A
                );
            }
        }

        wp_enqueue_media();

        $count_books = $wpdb->get_results(
                "SELECT * from " . $this->table_activator->owt_library_tbl_books()
        );

        $this->owt_library_include_template_file("book/owt-lms-create-book", array("categories" => $categories, "book_data" => $find_book, "action" => $action, "total_books" => count($count_books)));
    }

    public function owt_library_management_dashbaord() {

        global $wpdb;

        $students_count = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->owt_library_tbl_students() . " WHERE status = %d", 1
                )
        );

        $staffs_count = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE status = %d", 1
                )
        );

        $book_count = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->owt_library_tbl_books() . " WHERE status = %d", 1
                )
        );

        $issued_to_students = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(issue.id) from " . $this->table_activator->owt_library_tbl_book_issue() . " issue INNER JOIN " . $this->table_activator->owt_library_tbl_user_type() . " type ON issue.user_type_id = type.id WHERE issue.status = %d AND type.type = %s", 1, 'student'
                )
        );

        $issued_to_staffs = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(issue.id) from " . $this->table_activator->owt_library_tbl_book_issue() . " issue INNER JOIN " . $this->table_activator->owt_library_tbl_user_type() . " type ON issue.user_type_id = type.id WHERE issue.status = %d AND type.type != %s", 1, 'student'
                )
        );

        $this->owt_library_include_template_file("owt-lms-dashboard", array("students" => $students_count, "staffs" => $staffs_count, "books" => $book_count, "issue_to_students" => $issued_to_students, "issue_to_staffs" => $issued_to_staffs));
    }

    public function owt_library_students_management() {

        global $wpdb;

        $students_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE status = %d ORDER by id DESC limit 2", 1
                )
        );

        $all_branches = $wpdb->get_results(
                "SELECT * from " . $this->table_activator->owt_library_tbl_branch()
        );

        $this->owt_library_include_template_file("student/owt-lms-students-list", array("students" => $students_list, "branches" => $all_branches));
    }

    public function owt_library_staffs_management() {

        global $wpdb;

        $staff_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE status = %d ORDER by id DESC limit 2", 1
                )
        );

        $all_staff_types = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE type != %s", 'student'
                )
        );

        $this->owt_library_include_template_file("staff/owt-lms-staffs-list", array("staffs" => $staff_list, "staff_types" => $all_staff_types));
    }

    public function owt_library_books_management() {

        global $wpdb;

        $books_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE status = %d ORDER by id DESC limit 5", 1
                )
        );

        $all_categories = $wpdb->get_results(
                "SELECT * from " . $this->table_activator->owt_library_tbl_category()
        );

        $this->owt_library_include_template_file("book/owt-lms-books-list", array("books" => $books_list, "categories" => $all_categories));
    }

    public function owt_library_settings_panel() {

        global $wpdb;

        $courties_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_country() . " WHERE status = %d", 1
                )
        );

        $currencies_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_country_currency() . " WHERE status = %d", 1
                )
        );

        $this->owt_library_include_template_file("report/owt-lms-settings-panel", array("countries" => $courties_list, "currencies" => $currencies_list));
    }

    public function owt_library_create_student() {

        global $wpdb;

        $get_branch = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE status = %d", 1
                )
        );

        $courties_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_country() . " WHERE status = %d", 1
                )
        );

        wp_enqueue_media();

        $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : "add";
        $find_student = array();
        $valid_opt_fn = array("edit", "delete", "view");
        if (!empty($action) && in_array($action, $valid_opt_fn)) {

            $stid = isset($_REQUEST['stid']) ? intval($_REQUEST['stid']) : 0;

            if ($stid > 0) {

                $find_student = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE id = %d", $stid
                        ), ARRAY_A
                );
            }
        }

        $total_count_student = $wpdb->get_results(
                "SELECT * from " . $this->table_activator->owt_library_tbl_students()
        );

        $this->owt_library_include_template_file("student/owt-lms-create-student", array("data" => $get_branch, "countries" => $courties_list, "student_data" => $find_student, "action" => $action, "total_students" => count($total_count_student)));
    }

    public function owt_library_create_staff() {

        global $wpdb;

        $courties_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_country() . " WHERE status = %d", 1
                )
        );

        $staff_types = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE type != %s", 'student'
                )
        );

        $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : "add";
        $find_staff = array();
        $valid_opt_fn = array("edit", "delete", "view");
        if (!empty($action) && in_array($action, $valid_opt_fn)) {

            $stfid = isset($_REQUEST['stfid']) ? intval($_REQUEST['stfid']) : 0;

            if ($stfid > 0) {

                $find_staff = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE id = %d", $stfid
                        ), ARRAY_A
                );
            }
        }

        $total_count_staffs = $wpdb->get_results(
                "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff()
        );

        wp_enqueue_media();
        $this->owt_library_include_template_file("staff/owt-lms-create-staff", array("countries" => $courties_list, "staff_types" => $staff_types, "staff_data" => $find_staff, "action" => $action, "total_staffs" => count($total_count_staffs)));
    }

    public function owt_library_admin_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('OWT Library Management System has successfully installed! Enjoy the system. Click here to visit Online Web Tutor <a href="http://onlinewebtutorhub.blogspot.in/" target="_blank" class="button button-primary button-large">Click here</a>', 'sample-text-domain'); ?></p>
        </div>
        <?php
    }

    public function owt_library_ajax_handler() {

        global $wpdb;

        $param = isset($_REQUEST['param']) ? trim($_REQUEST['param']) : "";

        if (!empty($param)) {

            if ($param == "owt_lib_create_student") {

                $total_count_student = $wpdb->get_results(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_students()
                );

                $reg_id = isset($_REQUEST['txt_reg_id']) ? sanitize_text_field($_REQUEST['txt_reg_id']) : "";
                $txt_name = isset($_REQUEST['txt_name']) ? sanitize_text_field($_REQUEST['txt_name']) : "";
                $txt_branch = isset($_REQUEST['txt_branch']) ? sanitize_text_field($_REQUEST['txt_branch']) : "";
                $txt_email = isset($_REQUEST['txt_email']) ? sanitize_text_field($_REQUEST['txt_email']) : "";
                $txt_phone = isset($_REQUEST['txt_phone']) ? sanitize_text_field($_REQUEST['txt_phone']) : "";
                $txt_address_info = isset($_REQUEST['txt_address_info']) ? sanitize_text_field($_REQUEST['txt_address_info']) : "";
                $txt_city = isset($_REQUEST['txt_city']) ? sanitize_text_field($_REQUEST['txt_city']) : "";
                $txt_st_state = isset($_REQUEST['txt_st_state']) ? sanitize_text_field($_REQUEST['txt_st_state']) : "";
                $txt_country = isset($_REQUEST['txt_country']) ? sanitize_text_field($_REQUEST['txt_country']) : "";
                $txt_father_name = isset($_REQUEST['txt_father_name']) ? sanitize_text_field($_REQUEST['txt_father_name']) : "";
                $txt_mother_name = isset($_REQUEST['txt_mother_name']) ? sanitize_text_field($_REQUEST['txt_mother_name']) : "";
                $txt_parent_phone = isset($_REQUEST['txt_parent_phone']) ? sanitize_text_field($_REQUEST['txt_parent_phone']) : "";
                $stu_profile_image = isset($_REQUEST['stu_profile_image']) ? sanitize_text_field($_REQUEST['stu_profile_image']) : "";
                $action = isset($_REQUEST['opt_action']) ? trim($_REQUEST['opt_action']) : "add"; // or it will be edit

                if ($txt_branch < 0) {
                    $this->json(0, "Please select student branch");
                }

                if ($txt_country < 0) {
                    $this->json(0, "Please select country");
                }

                if ($action == "add") {

                    if (count($total_count_student) >= 2) {
                        $this->json(0, "No more credits left to add student at this free version");
                    }

                    if (!empty($txt_email)) { // checking email existance 
                        $find_email = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE email = %s", $txt_email
                                )
                        );
                        if (!empty($find_email)) {
                            $this->json(0, "Student already taken this email, try another one");
                        }
                    }

                    if (!empty($reg_id)) { // checking registration id existance 
                        $find_reg_id = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE student_id = %s", $reg_id
                                )
                        );
                        if (!empty($find_reg_id)) {
                            $this->json(0, "Registration ID already registered");
                        }
                    }

                    $wpdb->insert($this->table_activator->owt_library_tbl_students(), array(
                        "registration_type" => "admin",
                        "name" => $txt_name,
                        "student_id" => $reg_id,
                        "email" => $txt_email,
                        "branch_id" => $txt_branch,
                        "phone_no" => $txt_phone,
                        "profile_image" => $stu_profile_image,
                        "father_name" => $txt_father_name,
                        "mother_name" => $txt_mother_name,
                        "parent_phone_no" => $txt_parent_phone,
                        "address_info" => $txt_address_info,
                        "city" => $txt_city,
                        "state" => $txt_st_state,
                        "country_id" => $txt_country
                    ));

                    if ($wpdb->insert_id > 0) {
                        $this->json(1, "Student created successfully, reloading page...");
                    } else {
                        $this->json(0, "Failed to create student");
                    }
                } elseif ($action == "edit") {

                    $find_student = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE student_id = %s", $reg_id
                            )
                    );

                    if (!empty($find_student)) {

                        $wpdb->update(
                                $this->table_activator->owt_library_tbl_students(), array(
                            "name" => $txt_name,
                            "email" => $txt_email,
                            "branch_id" => $txt_branch,
                            "phone_no" => $txt_phone,
                            "profile_image" => $stu_profile_image,
                            "father_name" => $txt_father_name,
                            "mother_name" => $txt_mother_name,
                            "parent_phone_no" => $txt_parent_phone,
                            "address_info" => $txt_address_info,
                            "city" => $txt_city,
                            "state" => $txt_st_state,
                            "country_id" => $txt_country
                                ), array(
                            "id" => $find_student->id
                                )
                        );

                        $this->json(1, "Student details updated, reloading page...");
                    } else {

                        $this->json(0, "Invalid Student ID");
                    }
                }
            } elseif ($param == "owt_lib_delete_return") {

                $return_id = isset($_REQUEST['return_id']) ? $_REQUEST['return_id'] : "";

                if ($return_id > 0) {

                    $wpdb->delete($this->table_activator->owt_library_tbl_book_return(), array(
                        "id" => $return_id
                    ));

                    $this->json(1, "Record deleted successfully, reloadig page...");
                } else {
                    $this->json(0, "Invalid record");
                }
            } elseif ($param == "owt_lib_delete_student") {

                $student_id = isset($_REQUEST['st']) ? intval($_REQUEST['st']) : 0;

                if ($student_id > 0) {

                    $find_student = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE id = %d", $student_id
                            )
                    );

                    if (!empty($find_student)) {

                        $wpdb->delete($this->table_activator->owt_library_tbl_book_issue(), array(
                            "user_type_id" => 3,
                            "user_id" => $student_id
                        ));

                        $wpdb->delete($this->table_activator->owt_library_tbl_students(), array(
                            "id" => $student_id
                        ));
                        $this->json(1, "Student deleted successfully");
                    } else {
                        $this->json(0, "Invalid ID, Student not found");
                    }
                } else {
                    $this->json(0, "Invalid ID, Student not found");
                }
            } elseif ($param == "owt_lib_add_branch") {

                $branch = isset($_REQUEST['wpowt_branch']) ? sanitize_text_field($_REQUEST['wpowt_branch']) : "";
                if (!empty($branch)) {

                    $is_branch_exists = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE LOWER(TRIM(name)) = %s", strtolower(trim($branch))
                            )
                    );

                    if (!empty($is_branch_exists)) {

                        $this->json(0, "Branch/Class already created");
                    } else {

                        $wpdb->insert($this->table_activator->owt_library_tbl_branch(), array(
                            "name" => $branch
                        ));

                        if ($wpdb->insert_id > 0) {

                            $this->json(1, "New Branch/Class created successfully, reloading page...");
                        } else {
                            $this->json(0, "Failed to create Branch/Class");
                        }
                    }
                } else {
                    $this->json(0, "Invalid Branch value");
                }
            } elseif ($param == "owt_lib_create_staff") {

                $total_count_staffs = $wpdb->get_results(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff()
                );

                $reg_id = isset($_REQUEST['txt_reg_id']) ? sanitize_text_field($_REQUEST['txt_reg_id']) : "";
                $dd_staff_type_id = isset($_REQUEST['dd_staff_type_id']) ? intval($_REQUEST['dd_staff_type_id']) : "";
                $txt_name = isset($_REQUEST['txt_name']) ? sanitize_text_field($_REQUEST['txt_name']) : "";
                $txt_email = isset($_REQUEST['txt_email']) ? sanitize_text_field($_REQUEST['txt_email']) : "";
                $txt_phone = isset($_REQUEST['txt_phone']) ? sanitize_text_field($_REQUEST['txt_phone']) : "";
                $txt_address_info = isset($_REQUEST['txt_address_info']) ? sanitize_text_field($_REQUEST['txt_address_info']) : "";
                $txt_city = isset($_REQUEST['txt_city']) ? sanitize_text_field($_REQUEST['txt_city']) : "";
                $txt_st_state = isset($_REQUEST['txt_state']) ? sanitize_text_field($_REQUEST['txt_state']) : "";
                $txt_country = isset($_REQUEST['txt_country']) ? sanitize_text_field($_REQUEST['txt_country']) : "";
                $stu_profile_image = isset($_REQUEST['staff_profile_image']) ? sanitize_text_field($_REQUEST['staff_profile_image']) : "";
                $action = isset($_REQUEST['opt_action']) ? trim($_REQUEST['opt_action']) : "add"; // or it will be edit

                if ($dd_staff_type_id < 0) {
                    $this->json(0, "Please select staff type");
                }

                if ($txt_country < 0) {
                    $this->json(0, "Please select country");
                }

                if ($action == "add") {

                    if (count($total_count_staffs) > 2) {
                        $this->json(0, "No more credits left to add staff at this free version");
                    }

                    if (!empty($txt_email)) { // checking email existance 
                        $find_email = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE email = %s", $txt_email
                                )
                        );
                        if (!empty($find_email)) {
                            $this->json(0, "Staff already taken this email, try another one");
                        }
                    }

                    if (!empty($reg_id)) { // checking ID existance 
                        $find_reg_id = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE staff_id = %s", $reg_id
                                )
                        );
                        if (!empty($find_reg_id)) {
                            $this->json(0, "Staff ID already registered");
                        }
                    }

                    $wpdb->insert($this->table_activator->owt_library_tbl_teachers_staff(), array(
                        "name" => $txt_name,
                        "staff_id" => $reg_id,
                        "staff_type_id" => $dd_staff_type_id,
                        "email" => $txt_email,
                        "phone_no" => $txt_phone,
                        "profile_image" => $stu_profile_image,
                        "address_info" => $txt_address_info,
                        "city" => $txt_city,
                        "state" => $txt_st_state,
                        "country_id" => $txt_country
                    ));

                    if ($wpdb->insert_id > 0) {
                        $this->json(1, "Staff created successfully, reloading page...");
                    } else {
                        $this->json(0, "Failed to create staff");
                    }
                } elseif ($action == "edit") {

                    $find_reg_id = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE staff_id = %s", $reg_id
                            )
                    );

                    if (!empty($find_reg_id)) {

                        $wpdb->update(
                                $this->table_activator->owt_library_tbl_teachers_staff(), array(
                            "name" => $txt_name,
                            "staff_type_id" => $dd_staff_type_id,
                            "email" => $txt_email,
                            "phone_no" => $txt_phone,
                            "profile_image" => $stu_profile_image,
                            "address_info" => $txt_address_info,
                            "city" => $txt_city,
                            "state" => $txt_st_state,
                            "country_id" => $txt_country
                                ), array(
                            "id" => $find_reg_id->id
                                )
                        );

                        $this->json(1, "Staff details updated, reloading page...");
                    } else {

                        $this->json(0, "Invalid Staff ID");
                    }
                }
            } elseif ($param == "owt_lib_delete_staff") {

                $staff_id = isset($_REQUEST['st']) ? intval($_REQUEST['st']) : 0;

                if ($staff_id > 0) {

                    $find_staff = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE id = %d", $staff_id
                            )
                    );

                    if (!empty($find_staff)) {

                        $staff_type_id = isset($find_staff->staff_type_id) ? $find_staff->staff_type_id : 0;

                        $wpdb->delete($this->table_activator->owt_library_tbl_book_issue(), array(
                            "user_type_id" => $staff_type_id,
                            "user_id" => $staff_id
                        ));

                        $wpdb->delete($this->table_activator->owt_library_tbl_teachers_staff(), array(
                            "id" => $staff_id
                        ));
                        $this->json(1, "Staff deleted successfully");
                    } else {
                        $this->json(0, "Invalid ID, Staff not found");
                    }
                } else {
                    $this->json(0, "Invalid ID, Staff not found");
                }
            } elseif ($param == "owt_lib_add_staff_type") {

                $type = isset($_REQUEST['wpowt_type']) ? sanitize_text_field($_REQUEST['wpowt_type']) : "";
                if (!empty($type)) {

                    $is_type_exists = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE LOWER(TRIM(type)) = %s", strtolower(trim($type))
                            )
                    );

                    if (!empty($is_type_exists)) {

                        $this->json(0, "Staff Type already created");
                    } else {

                        $wpdb->insert($this->table_activator->owt_library_tbl_user_type(), array(
                            "type" => $type
                        ));

                        if ($wpdb->insert_id > 0) {

                            $this->json(1, "New Staff type created successfully, reloading page...");
                        } else {
                            $this->json(0, "Failed to create Staff type");
                        }
                    }
                } else {
                    $this->json(0, "Invalid Staff type value");
                }
            } elseif ($param == "owt_lib_create_book") {

                $books_list = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE status = %d", 1
                        )
                );

                $txt_category = isset($_REQUEST['txt_category']) ? sanitize_text_field($_REQUEST['txt_category']) : "";
                $txt_book_id = isset($_REQUEST['txt_book_id']) ? sanitize_text_field($_REQUEST['txt_book_id']) : "";
                $txt_name = isset($_REQUEST['txt_name']) ? sanitize_text_field($_REQUEST['txt_name']) : "";
                $txt_author = isset($_REQUEST['txt_author']) ? sanitize_text_field($_REQUEST['txt_author']) : "";
                $txt_publication = isset($_REQUEST['txt_publication']) ? sanitize_text_field($_REQUEST['txt_publication']) : "";
                $txtamount = isset($_REQUEST['txtamount']) ? sanitize_text_field($_REQUEST['txtamount']) : "";
                $txt_description_info = isset($_REQUEST['txt_description_info']) ? sanitize_text_field($_REQUEST['txt_description_info']) : "";
                $txtisbn = isset($_REQUEST['txtisbn']) ? sanitize_text_field($_REQUEST['txtisbn']) : "";
                $book_cover_image = isset($_REQUEST['book_cover_image']) ? sanitize_text_field($_REQUEST['book_cover_image']) : "";
                $action = isset($_REQUEST['opt_action']) ? trim($_REQUEST['opt_action']) : "add"; // or it will be edit

                if ($txt_category < 0) {
                    $this->json(0, "Please select a category");
                }

                if ($action == "add") {

                    if (count($books_list) >= 5) {
                        $this->json(0, "No more credits left to add book at this free version");
                    }

                    if (!empty($txt_book_id)) { // checking email existance 
                        $find_Book = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE book_id = %s", $txt_book_id
                                )
                        );
                        if (!empty($find_Book)) {
                            $this->json(0, "Book ID already exists, try another one");
                        }
                    }

                    $wpdb->insert($this->table_activator->owt_library_tbl_books(), array(
                        "book_id" => $txt_book_id,
                        "name" => $txt_name,
                        "author_info" => $txt_author,
                        "publisher_info" => $txt_publication,
                        "category_id" => $txt_category,
                        "amount" => $txtamount,
                        "cover_image" => $book_cover_image,
                        "isbn" => $txtisbn,
                        "description" => $txt_description_info
                    ));

                    if ($wpdb->insert_id > 0) {
                        $this->json(1, "Book created successfully, reloading page...");
                    } else {
                        $this->json(0, "Failed to create book");
                    }
                } elseif ($action == "edit") {

                    $find_bk_id = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE book_id = %s", $txt_book_id
                            )
                    );

                    if (!empty($find_bk_id)) {

                        $wpdb->update(
                                $this->table_activator->owt_library_tbl_books(), array(
                            "name" => $txt_name,
                            "author_info" => $txt_author,
                            "publisher_info" => $txt_publication,
                            "category_id" => $txt_category,
                            "amount" => $txtamount,
                            "cover_image" => $book_cover_image,
                            "isbn" => $txtisbn,
                            "description" => $txt_description_info
                                ), array(
                            "id" => $find_bk_id->id
                                )
                        );

                        $this->json(1, "Book details updated, reloading page...");
                    } else {

                        $this->json(0, "Invalid Book ID");
                    }
                }
            } elseif ($param == "owt_lib_add_book_category") {

                $category_title = isset($_REQUEST['txt_title']) ? sanitize_text_field($_REQUEST['txt_title']) : "";

                if (!empty($category_title)) {

                    $is_exists = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_category() . " WHERE LOWER(TRIM(name)) = %s", strtolower(trim($category_title))
                            )
                    );

                    if (!empty($is_exists)) {

                        $this->json(0, "Category already created");
                    } else {

                        $wpdb->insert($this->table_activator->owt_library_tbl_category(), array(
                            "name" => $category_title
                        ));

                        if ($wpdb->insert_id > 0) {

                            $this->json(1, "New Category created successfully, reloading page...");
                        } else {
                            $this->json(0, "Failed to create category");
                        }
                    }
                } else {
                    $this->json(0, "Invalid Category value");
                }
            } elseif ($param == "owt_lib_delete_book") {

                $book_id = isset($_REQUEST['st']) ? intval($_REQUEST['st']) : 0;

                if ($book_id > 0) {

                    $find_book = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE id = %d", $book_id
                            )
                    );

                    if (!empty($find_book)) {

                        $wpdb->delete($this->table_activator->owt_library_tbl_books(), array(
                            "id" => $book_id
                        ));
                        $this->json(1, "Book deleted successfully");
                    } else {
                        $this->json(0, "Invalid ID, Book not found");
                    }
                } else {
                    $this->json(0, "Invalid ID, Book not found");
                }
            } elseif ($param == "owt_lib_filter_books") {

                $category_id = isset($_REQUEST['ct']) ? intval($_REQUEST['ct']) : "";
                if (!empty($category_id)) {

                    $books = $wpdb->get_results(
                            $wpdb->prepare(
                                    "SELECT id,name from " . $this->table_activator->owt_library_tbl_books() . " WHERE category_id = %d", $category_id
                            ), ARRAY_A
                    );
                    if (count($books) > 0) {
                        $this->json(1, "Book found", $books);
                    } else {
                        $this->json(0, "No Books found for this category");
                    }
                } else {
                    $this->json(0, "Invalid ID");
                }
            } elseif ($param == "owt_lib_filter_users") {

                $user_type_id = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : "";
                if (!empty($user_type_id)) {

                    $user_type = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE id = %d", $user_type_id
                            )
                    );
                    if (!empty($user_type)) {

                        if ($user_type->type == "student") {

                            $this->json(1, "Student row", array("show" => "student"));
                        } else {

                            $staffs = $wpdb->get_results(
                                    $wpdb->prepare(
                                            "SELECT id,name,staff_id from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE staff_type_id = %d AND status = %d", $user_type_id, 1
                                    ), ARRAY_A
                            );

                            $this->json(1, "Staff data", array("show" => "staff", "data" => $staffs));
                        }
                    }
                } else {
                    $this->json(0, "Invalid user type value");
                }
            } elseif ($param == "owt_lib_filter_branch") {

                $branch_id = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : "";
                if (!empty($branch_id)) {

                    $students_list = $wpdb->get_results(
                            $wpdb->prepare(
                                    "SELECT id,name,student_id from " . $this->table_activator->owt_library_tbl_students() . " WHERE branch_id = %d", $branch_id
                            ), ARRAY_A
                    );

                    if (count($students_list) > 0) {

                        $this->json(1, "Students list", array("students" => $students_list));
                    } else {

                        $this->json(0, "No students found");
                    }
                } else {
                    $this->json(0, "Invalid branch, please select a valid branch");
                }
            } elseif ($param == "owt_lib_issue_book") {

                $u_type = isset($_REQUEST['wpowt_hidden_type']) ? trim($_REQUEST['wpowt_hidden_type']) : '';

                $txt_category = isset($_REQUEST['txt_category']) ? intval($_REQUEST['txt_category']) : "";
                if (!empty($txt_category) && $txt_category == -1) {
                    $this->json(0, "Please select book category");
                }

                $dd_book = isset($_REQUEST['dd_book']) ? intval($_REQUEST['dd_book']) : "";
                if (!empty($dd_book) && $dd_book == -1) {
                    $this->json(0, "Please select book");
                }

                $txt_days_count = isset($_REQUEST['txt_days_count']) ? intval($_REQUEST['txt_days_count']) : 0;
                if ($txt_days_count < 0) {
                    $this->json(0, "Please select valid issue days");
                }

                $dd_user_type_id = isset($_REQUEST['dd_user_type_id']) ? intval($_REQUEST['dd_user_type_id']) : '';
                if (!empty($dd_user_type_id) && $dd_user_type_id == -1) {
                    $this->json(0, "Please select User type");
                }

                $issue_id = substr(str_shuffle("123456789ABCDEFGHIJKLMNOPQRSTU"), 6, 5);

                if ($u_type == "student") {

                    $dd_st_branch_id = isset($_REQUEST['dd_st_branch_id']) ? intval($_REQUEST['dd_st_branch_id']) : 0;
                    if (!empty($dd_st_branch_id) && $dd_st_branch_id == -1) {
                        $this->json(0, "Please select branch");
                    }

                    if ($dd_st_branch_id > 0) {

                        $dd_student_id = isset($_REQUEST['dd_student_id']) ? intval($_REQUEST['dd_student_id']) : 0;
                        if (!empty($dd_student_id) && $dd_student_id == -1) {
                            $this->json(0, "Please select student");
                        } elseif ($dd_student_id > 0) {
                            //student area
                            //checking if student has already book
                            $has_issued = 0;
                            $late_fine = 0;

                            $has_student_already_book = $wpdb->get_row(
                                    $wpdb->prepare(
                                            "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE user_id = %d AND user_type_id = %d ORDER BY id DESC limit 1", $dd_student_id, $dd_user_type_id
                                    )
                            );

                            if (!empty($has_student_already_book) && $has_student_already_book->status) {
                                $has_issued = 1;
                            }

                            $book_issue_id = isset($has_student_already_book->issue_id) ? $has_student_already_book->issue_id : "";

                            // checking for the fine
                            $has_fine = $wpdb->get_row(
                                    $wpdb->prepare(
                                            "SELECT * from " . $this->table_activator->owt_library_tbl_book_return() . " WHERE book_issue_id = %s", $book_issue_id
                                    )
                            );

                            if (!empty($has_fine) && $has_fine->has_fine_status) {
                                $late_fine = 1;
                                $this->json(0, "Student has late fine of last book issued, needs to be submit first.");
                            }

                            if (!$has_issued && !$late_fine) {

                                $wpdb->insert($this->table_activator->owt_library_tbl_book_issue(), array(
                                    "issue_id" => $issue_id,
                                    "category_id" => $txt_category,
                                    "book_id" => $dd_book,
                                    "user_type_id" => $dd_user_type_id,
                                    "user_id" => $dd_student_id,
                                    "branch_id" => $dd_st_branch_id,
                                    "duration_days" => $txt_days_count,
                                    "status" => 1
                                ));

                                if ($wpdb->insert_id > 0) {

                                    $this->json(1, "Book issued successfully, reloading page...");
                                } else {

                                    $this->json(0, "Failed to issue book");
                                }
                            } else {
                                $this->json(0, "Student has already a book issued");
                            }
                        }
                    }
                } else {

                    $dd_staff_id = isset($_REQUEST['dd_staff_id']) ? intval($_REQUEST['dd_staff_id']) : 0;

                    if (!empty($dd_staff_id) && $dd_staff_id == -1) {

                        $this->json(0, "Please select staff");
                    } elseif ($dd_staff_id > 0) {
                        //student area
                        //checking if student has already book
                        $has_issued = 0;
                        $late_fine = 0;

                        $has_staff_already_book = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE user_id = %d AND user_type_id = %d ORDER BY id DESC limit 1", $dd_staff_id, $dd_user_type_id
                                )
                        );

                        if (!empty($has_staff_already_book) && $has_staff_already_book->status) {
                            $has_issued = 1;
                        }

                        $book_issue_id = isset($has_staff_already_book->issue_id) ? $has_staff_already_book->issue_id : "";

                        // checking for the fine
                        $has_fine = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_book_return() . " WHERE book_issue_id = %s", $book_issue_id
                                )
                        );

                        if (!empty($has_fine) && $has_fine->has_fine_status) {
                            $late_fine = 1;
                            $this->json(0, "Staff has late fine of last book issued, needs to be submit first.");
                        }

                        if (!$has_issued && !$late_fine) {

                            $wpdb->insert($this->table_activator->owt_library_tbl_book_issue(), array(
                                "issue_id" => $issue_id,
                                "category_id" => $txt_category,
                                "book_id" => $dd_book,
                                "user_type_id" => $dd_user_type_id,
                                "user_id" => $dd_staff_id,
                                "duration_days" => $txt_days_count,
                                "status" => 1
                            ));

                            if ($wpdb->insert_id > 0) {

                                $this->json(1, "Book issued successfully, reloading page...");
                            } else {

                                $this->json(0, "Failed to issue book");
                            }
                        } else {
                            $this->json(0, "Staff has already a book issued");
                        }
                    }
                }
            } elseif ($param == "owt_lib_student_issued_books") {

                $user_id = isset($_REQUEST['stid']) ? intval($_REQUEST['stid']) : 0;
                if ($user_id == -1) {
                    $this->json(0, "Please select student");
                }

                $issues_books = $wpdb->get_results(
                        $wpdb->prepare(
                                "Select issue.*, book.name as book_name from " . $this->table_activator->owt_library_tbl_book_issue() . " issue INNER JOIN " . $this->table_activator->owt_library_tbl_books() . " book ON issue.book_id = book.id WHERE issue.user_id = %d AND issue.status = %d AND issue.user_type_id = %d", $user_id, 1, 3
                        ), ARRAY_A
                );

                if (!empty($issues_books)) {

                    $this->json(1, "Books found", array("books" => $issues_books));
                } else {
                    $this->json(0, "No books issued");
                }
            } elseif ($param == "owt_lib_staff_issued_books") {

                $user_id = isset($_REQUEST['stfid']) ? intval($_REQUEST['stfid']) : 0;
                if ($user_id == -1) {
                    $this->json(0, "Please select staff");
                }

                $issues_books = $wpdb->get_results(
                        $wpdb->prepare(
                                "Select issue.*, book.name as book_name from " . $this->table_activator->owt_library_tbl_book_issue() . " issue INNER JOIN " . $this->table_activator->owt_library_tbl_books() . " book ON issue.book_id = book.id WHERE issue.user_id = %d AND issue.status= %d AND issue.user_type_id != %d", $user_id, 1, 3
                        ), ARRAY_A
                );

                if (!empty($issues_books)) {

                    $this->json(1, "Books found", array("books" => $issues_books));
                } else {
                    $this->json(0, "No books issued");
                }
            } elseif ($param == "return_book_by_issue_id") {

                $issue_id = isset($_REQUEST['issue_id']) ? $_REQUEST['issue_id'] : "";

                $book_details = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE issue_id = %s", $issue_id
                        )
                );

                if (!empty($book_details)) {

                    $count_days = $book_details->duration_days;
                    $issued_date = $book_details->created_at;
                    $current_date = date("Y-m-d");
                    $issued = new DateTime(date("Y-m-d", strtotime($issued_date)));
                    $return = new DateTime($current_date);
                    $days = $issued->diff($return);
                    $book_kept_days = $days->format('%a');
                    $extra_days = 0;
                    $late_fine = 0;
                    $user_id = $book_details->user_id;

                    if ($count_days >= $book_kept_days) {
                        // ok its fine to keep
                    } else {
                        $extra_days = $book_kept_days - $count_days;
                        $late_fine = $extra_days * OWT_LIBRARY_PLUGIN_BOOK_LATE_FINE;
                    }
                    $late_fine_status = $late_fine > 0 ? 1 : 0;
                    // book return entry
                    $wpdb->insert($this->table_activator->owt_library_tbl_book_return(), array(
                        "user_id" => $user_id,
                        "book_issue_id" => $issue_id,
                        "has_fine_status" => $late_fine_status
                    ));

                    if ($late_fine_status) {

                        $return_id = $wpdb->insert_id;
                        // late fine insert
                        $wpdb->insert($this->table_activator->owt_library_tbl_book_late_fine(), array(
                            "book_return_id" => $return_id,
                            "extra_days" => $extra_days,
                            "fine_amount" => $late_fine,
                            "fine_pay_status" => 0
                        ));
                    }

                    //update return status
                    $wpdb->update(
                            $this->table_activator->owt_library_tbl_book_issue(), array(
                        "status" => 0
                            ), array(
                        "issue_id" => $issue_id
                            )
                    );

                    if ($late_fine_status) {
                        $this->json(1, "Book(s) returned successfully with fine");
                    } else {
                        $this->json(1, "Book(s) returned successfully");
                    }
                } else {
                    $this->json(0, "No Book found");
                }
            } elseif ($param == "owt_lib_student_return_book") {

                $book_return_list = isset($_REQUEST['book_return_list']) ? $_REQUEST['book_return_list'] : array();

                $late_fine_status = 0;

                if (!empty($book_return_list) && count($book_return_list) > 0) {

                    foreach ($book_return_list as $issue) {

                        $book_details = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_book_issue() . " WHERE issue_id = %s", $issue
                                )
                        );

                        if (!empty($book_details)) {

                            $count_days = $book_details->duration_days;
                            $issued_date = $book_details->created_at;
                            $current_date = date("Y-m-d");
                            $issued = new DateTime(date("Y-m-d", strtotime($issued_date)));
                            $return = new DateTime($current_date);
                            $days = $issued->diff($return);
                            $book_kept_days = $days->format('%a');
                            $extra_days = 0;
                            $late_fine = 0;
                            $user_id = $book_details->user_id;

                            if ($count_days >= $book_kept_days) {
                                // ok its fine to keep
                            } else {
                                $extra_days = $book_kept_days - $count_days;
                                $late_fine = $extra_days * OWT_LIBRARY_PLUGIN_BOOK_LATE_FINE;
                            }
                            $late_fine_status = $late_fine > 0 ? 1 : 0;
                            // book return entry
                            $wpdb->insert($this->table_activator->owt_library_tbl_book_return(), array(
                                "user_id" => $user_id,
                                "book_issue_id" => $issue,
                                "has_fine_status" => $late_fine_status
                            ));

                            if ($late_fine_status) {

                                $return_id = $wpdb->insert_id;
                                // late fine insert
                                $wpdb->insert($this->table_activator->owt_library_tbl_book_late_fine(), array(
                                    "book_return_id" => $return_id,
                                    "extra_days" => $extra_days,
                                    "fine_amount" => $late_fine,
                                    "fine_pay_status" => 0
                                ));
                            }

                            //update return status
                            $wpdb->update(
                                    $this->table_activator->owt_library_tbl_book_issue(), array(
                                "status" => 0
                                    ), array(
                                "issue_id" => $issue
                                    )
                            );
                        }
                    }

                    if ($late_fine_status) {
                        $this->json(1, "Book(s) returned successfully with fine");
                    } else {
                        $this->json(1, "Book(s) returned successfully");
                    }
                } else {

                    $this->json(0, "No Books found to return");
                }
            } elseif ($param == "owt_lib_student_fine_details") {
                $return_id = isset($_REQUEST['return_id']) ? intval($_REQUEST['return_id']) : '';

                $return_details = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * from " . $this->table_activator->owt_library_tbl_book_late_fine() . " WHERE book_return_id = %d", $return_id
                        ), ARRAY_A
                );

                $this->json(1, "fine details found", $return_details);
            } elseif ($param == "owt_lib_pay_late_fine") {

                $return_id = isset($_REQUEST['wpowt_lib_return_id']) ? intval($_REQUEST['wpowt_lib_return_id']) : "";
                if (!empty($return_id)) {

                    $late_fine = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_book_late_fine() . " WHERE book_return_id = %d", $return_id
                            )
                    );
                    if (!empty($late_fine)) {
                        // update fine pay status
                        $wpdb->update($this->table_activator->owt_library_tbl_book_late_fine(), array(
                            "fine_pay_status" => 1
                                ), array(
                            "book_return_id" => $return_id
                        ));
                        // update return book
                        $wpdb->update($this->table_activator->owt_library_tbl_book_return(), array(
                            "has_fine_status" => 0,
                                ), array(
                            "id" => $return_id
                        ));

                        $this->json(1, "Fine paid successfully");
                    } else {
                        $this->json(0, "No late fine found with this return");
                    }
                } else {
                    $this->json(0, "Invalid Book Return");
                }
            } elseif ($param == "owt_lib_update_branch") {

                $branch_name = isset($_REQUEST['branch_name']) ? trim($_REQUEST['branch_name']) : "";
                $branch_id = isset($_REQUEST['branch_id']) ? intval($_REQUEST['branch_id']) : "";

                if (!empty($branch_name)) {

                    $branch_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE LOWER(TRIM(name)) = %s AND status = %d AND id != %d", strtolower($branch_name), 1, $branch_id
                            )
                    );

                    if (!empty($branch_data)) {
                        $this->json(0, "Branch name already exists");
                    } else {
                        $wpdb->update($this->table_activator->owt_library_tbl_branch(), array(
                            "name" => $branch_name
                                ), array(
                            "id" => $branch_id
                        ));

                        $this->json(1, "Branch name updated successfully");
                    }
                } else {
                    $this->json(0, "Please provide branch name");
                }
            } elseif ($param == "owt_lib_delete_branch") {
                $branch_id = isset($_REQUEST['branch_id']) ? intval($_REQUEST['branch_id']) : "";

                if ($branch_id > 0) {

                    $branch_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE id = %d", $branch_id
                            )
                    );

                    if (!empty($branch_data)) {

                        $find_student = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE branch_id = %d", $branch_id
                                )
                        );

                        if (!empty($find_student)) {
                            $this->json(0, "Student(s) exists in this branch");
                        } else {
                            $wpdb->delete($this->table_activator->owt_library_tbl_branch(), array(
                                "id" => $branch_id
                            ));

                            $this->json(1, "Branch deleted successfully");
                        }
                    } else {
                        $this->json(0, "Branch not found");
                    }
                } else {
                    $this->json(0, "Invalid branch to delete");
                }
            } elseif ($param == "owt_lib_update_stafftype") {

                $staff_name = isset($_REQUEST['staff_name']) ? trim($_REQUEST['staff_name']) : "";
                $staff_id = isset($_REQUEST['staff_id']) ? intval($_REQUEST['staff_id']) : "";

                if (!empty($staff_name)) {

                    $staff_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE LOWER(TRIM(type)) = %s AND status = %d AND id != %d", strtolower($staff_name), 1, $staff_id
                            )
                    );

                    if (!empty($staff_data)) {
                        $this->json(0, "Staff type name already exists");
                    } else {
                        $wpdb->update($this->table_activator->owt_library_tbl_user_type(), array(
                            "type" => $staff_name
                                ), array(
                            "id" => $staff_id
                        ));

                        $this->json(1, "Staff type name updated successfully");
                    }
                } else {
                    $this->json(0, "Please provide staff type name");
                }
            } elseif ($param == "owt_lib_delete_stafftype") {

                $staff_id = isset($_REQUEST['staff_id']) ? intval($_REQUEST['staff_id']) : "";

                if ($staff_id > 0) {

                    $staff_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE id = %d", $staff_id
                            )
                    );

                    if (!empty($staff_data)) {

                        $find_staff = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE staff_type_id = %d", $staff_id
                                )
                        );

                        if (!empty($find_staff)) {
                            $this->json(0, "Staff(s) exists of this staff type");
                        } else {
                            $wpdb->delete($this->table_activator->owt_library_tbl_user_type(), array(
                                "id" => $staff_id
                            ));

                            $this->json(1, "Staff type deleted successfully");
                        }
                    } else {
                        $this->json(0, "Staff type not found");
                    }
                } else {
                    $this->json(0, "Invalid staff type to delete");
                }
            } elseif ($param == "owt_lib_update_category_title") {

                $category_name = isset($_REQUEST['category_name']) ? trim($_REQUEST['category_name']) : "";
                $category_id = isset($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : "";

                if (!empty($category_name)) {

                    $category_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_category() . " WHERE LOWER(TRIM(name)) = %s AND status = %d AND id != %d", strtolower($category_name), 1, $category_id
                            )
                    );

                    if (!empty($category_data)) {
                        $this->json(0, "Category name already exists");
                    } else {
                        $wpdb->update($this->table_activator->owt_library_tbl_category(), array(
                            "name" => $category_name
                                ), array(
                            "id" => $category_id
                        ));

                        $this->json(1, "Category name updated successfully");
                    }
                } else {
                    $this->json(0, "Please provide category name");
                }
            } elseif ($param == "owt_lib_delete_category") {

                $category_id = isset($_REQUEST['category_id']) ? intval($_REQUEST['category_id']) : "";

                if ($category_id > 0) {

                    $category_data = $wpdb->get_row(
                            $wpdb->prepare(
                                    "SELECT * from " . $this->table_activator->owt_library_tbl_category() . " WHERE id = %d", $category_id
                            )
                    );

                    if (!empty($category_data)) {

                        $find_category = $wpdb->get_row(
                                $wpdb->prepare(
                                        "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE category_id = %d", $category_id
                                )
                        );

                        if (!empty($find_category)) {
                            $this->json(0, "Book(s) exists of this category");
                        } else {
                            $wpdb->delete($this->table_activator->owt_library_tbl_category(), array(
                                "id" => $category_id
                            ));

                            $this->json(1, "Category deleted successfully");
                        }
                    } else {
                        $this->json(0, "Category not found");
                    }
                } else {
                    $this->json(0, "Invalid staff type to delete");
                }
            } elseif ($param == "owt_lib_settings_panel") {

                $currency_code = isset($_REQUEST['txt_currency']) ? sanitize_text_field($_REQUEST['txt_currency']) : -1;

                if ($currency_code < 0) {
                    $this->json(0, "Please select currency code");
                }

                update_option("owt_lib_currency_code", $currency_code);

                $this->json(1, "Settings saved successfully");
            }
        }

        wp_die();
    }

    public function json($sts, $msg, $arr = array()) {
        $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
        print_r(json_encode($ar));
        die;
    }

    public function owt_library_include_template_file($template, $lib_params = array()) {

        ob_start();
        $params = $lib_params;
        include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'admin/views/' . $template . ".php";
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function owt_library_get_category_info($category_id) {

        global $wpdb;
        $category_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_category() . " WHERE id = %d AND status = %d", $category_id, 1
                )
        );
        if (!empty($category_info)) {
            return $category_info->name;
        }
        return '';
    }

    public function owt_library_get_branch_info($branch_id) {

        global $wpdb;
        $branch_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE id = %d AND status = %d", $branch_id, 1
                )
        );
        if (!empty($branch_info)) {
            return $branch_info->name;
        }
        return '';
    }

    public function owt_library_get_book_info($book_id) {

        global $wpdb;
        $book_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_books() . " WHERE id = %d AND status = %d", $book_id, 1
                )
        );
        if (!empty($book_info)) {
            return $book_info->name;
        }
        return '';
    }

    public function owt_library_get_usertype_info($user_type_id) {

        global $wpdb;
        $user_type_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_user_type() . " WHERE id = %d AND status = %d", $user_type_id, 1
                )
        );
        if (!empty($user_type_info)) {
            return $user_type_info->type;
        }
        return '';
    }

    public function owt_library_get_staff_info($user_id) {

        global $wpdb;
        $user_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_teachers_staff() . " WHERE id = %d AND status = %d", $user_id, 1
                )
        );
        if (!empty($user_info)) {
            return $user_info;
        }
        return '';
    }

    /*
     * Checking book count
     */

    public function owt_library_free_version_rules() {
        global $wpdb;
        $count_books = $wpdb->get_var(
                $wpdb->prepare(
                        "SELECT count(id) from " . $this->table_activator->owt_library_tbl_books() . " WHERE status = %d", 1
                )
        );

        if ($count_books > 4) {
            ?>
            <div class="notice notice-warning">
                <p><b><u>Library Management System (Plugin warning):</u> Books exceeded, please contact Plugin Admin for paid version. For free only 5 books you can create & manage.</b></p>
            </div>
            <?php
        }
    }

    public function owt_library_get_student_info($student_id) {

        global $wpdb;
        $student_info = $wpdb->get_row(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_students() . " WHERE id = %d AND status = %d", $student_id, 1
                )
        );
        if (!empty($student_info)) {
            return $student_info;
        }
        return '';
    }

    public function wpowt_library_settings_link($links) {
        $lib_settings_link = admin_url('admin.php?page=owt-lib-manage-settings');
        $settings_link = '<a href="' . $lib_settings_link . '">' . __('Settings') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

}
