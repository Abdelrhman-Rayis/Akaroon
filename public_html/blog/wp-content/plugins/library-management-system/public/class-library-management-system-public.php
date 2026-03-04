<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://onlinewebtutorhub.blogspot.in/
 * @since      1.0.0
 *
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public
 * @author     Online Web Tutor <onlinewebtutorhub@gmail.com>
 */
class Library_Management_System_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        require_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'includes/class-library-management-system-activator.php';
        $this->table_activator = new Library_Management_System_Activator();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style("jquery-tabs", OWT_LIBRARY_PLUGIN_URL . 'assets/css/jquery-ui.css', array(), $this->version, 'all');
        wp_enqueue_style("owt-lib-sweetalert", OWT_LIBRARY_PLUGIN_URL . 'assets/css/sweetalert.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/library-management-system-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script("jquery");
        wp_enqueue_script('jquery-ui-core'); // enqueue jQuery UI Core
        wp_enqueue_script('jquery-ui-tabs'); // enqueue jQuery UI Tabs
        wp_enqueue_script("validate", OWT_LIBRARY_PLUGIN_URL . 'assets/js/jquery.validate.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script("sweatalert", OWT_LIBRARY_PLUGIN_URL . 'assets/js/sweetalert.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, OWT_LIBRARY_PLUGIN_URL . 'public/js/library-management-system-public.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name, "owt_lib", array(
            "ajaxurl" => admin_url("admin-ajax.php"),
            "owt_lib_prefix" => OWT_LIBRARY_PLUGIN_PREFIX
        ));
    }

    public function owt_library_frontend_books_listing() {

        global $wpdb;

        $books = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT book.*, category.name as category_name FROM " . $this->table_activator->owt_library_tbl_books() . " book INNER JOIN " . $this->table_activator->owt_library_tbl_category() . " category ON book.category_id = category.id WHERE book.status = %d", 1
                )
        );

        $categories = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * FROM " . $this->table_activator->owt_library_tbl_category() . " WHERE status = %d", 1
                )
        );

        ob_start();
        include_once OWT_LIBRARY_PLUGIN_DIR_PATH . "public/views/owt-lms-books-listing.php";
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function owt_library_frontend_user_registration() {

        global $wpdb;

        $branches = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE status = %d", 1
                )
        );

        ob_start();
        include_once OWT_LIBRARY_PLUGIN_DIR_PATH . "public/views/owt-lms-user-registration.php";
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function owt_library_frontend_ajax_handler() {

        global $wpdb;

        $param = isset($_REQUEST['param']) ? trim($_REQUEST['param']) : "";

        if (!empty($param)) {

            if ($param == "owt_lib_filter_book") {

                $category_id = isset($_REQUEST['dd_category']) ? intval($_REQUEST['dd_category']) : 0;
                $books = $wpdb->get_results(
                        $wpdb->prepare(
                                "SELECT book.*, category.name as category_name FROM " . $this->table_activator->owt_library_tbl_books() . " book INNER JOIN " . $this->table_activator->owt_library_tbl_category() . " category ON book.category_id = category.id WHERE book.status = %d AND category.id = %d", 1, $category_id
                        )
                );
                ob_start();
                include_once OWT_LIBRARY_PLUGIN_DIR_PATH . 'public/views/tmpl/owt-lms-tmpl-book.php';
                $template = ob_get_contents();
                ob_end_clean();
                if (count($books) > 0) {
                    $this->json(1, "books template loaded", array("template" => $template));
                } else {
                    $this->json(0, "<i>No book(s) found</i>");
                }
            }
        }
        wp_die();
    }

    public function owt_library_frontend_tabs() {

        global $wpdb;

        $branches = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_branch() . " WHERE status = %d", 1
                )
        );

        $books = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT book.*, category.name as category_name FROM " . $this->table_activator->owt_library_tbl_books() . " book INNER JOIN " . $this->table_activator->owt_library_tbl_category() . " category ON book.category_id = category.id WHERE book.status = %d", 1
                )
        );

        $courties_list = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * from " . $this->table_activator->owt_library_tbl_country() . " WHERE status = %d", 1
                )
        );

        $categories = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT * FROM " . $this->table_activator->owt_library_tbl_category() . " WHERE status = %d", 1
                )
        );

        ob_start();
        include_once OWT_LIBRARY_PLUGIN_DIR_PATH . "public/views/owt-lms-frontend-tabs.php";
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function json($sts, $msg, $arr = array()) {
        $ar = array('sts' => $sts, 'msg' => $msg, 'arr' => $arr);
        print_r(json_encode($ar));
        die;
    }

}

