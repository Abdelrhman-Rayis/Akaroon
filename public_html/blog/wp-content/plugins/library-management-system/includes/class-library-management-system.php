<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://onlinewebtutorhub.blogspot.in/
 * @since      1.0.0
 *
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @author     Online Web Tutor <onlinewebtutorhub@gmail.com>
 */
class Library_Management_System {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Library_Management_System_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'library-management-system';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Library_Management_System_Loader. Orchestrates the hooks of the plugin.
     * - Library_Management_System_i18n. Defines internationalization functionality.
     * - Library_Management_System_Admin. Defines all hooks for the admin area.
     * - Library_Management_System_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-library-management-system-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-library-management-system-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-library-management-system-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-library-management-system-public.php';

        $this->loader = new Library_Management_System_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Library_Management_System_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Library_Management_System_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Library_Management_System_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'owt_library_enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'owt_library_enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'owt_library_admin_menus');
        $this->loader->add_action('admin_notices', $plugin_admin, 'owt_library_free_version_rules');
        $this->loader->add_action('wp_ajax_owt_lib_handler', $plugin_admin, 'owt_library_ajax_handler');
        $this->loader->add_filter("plugin_action_links_" . OWT_LIBRARY_PLUGIN_BASEPATH, $plugin_admin, 'wpowt_library_settings_link');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Library_Management_System_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        //add_shortcode("books_listing_page", array($plugin_public, "owt_library_frontend_books_listing"));
        //add_shortcode("user_registration_page", array($plugin_public, "owt_library_frontend_user_registration"));
        add_shortcode("owt_library_tabs", array($plugin_public, "owt_library_frontend_tabs"));
        $this->loader->add_action('wp_ajax_owt_lib_frontend_handler', $plugin_public, 'owt_library_frontend_ajax_handler');
        $this->loader->add_action('wp_ajax_nopriv_owt_lib_frontend_handler', $plugin_public, 'owt_library_frontend_ajax_handler');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
        self::owt_library_default_plugin_values();
    }

    /*
     * plugin custom settings
     */

    public function owt_library_default_plugin_values() {
        $late_fine = get_option('owt_lib_book_late_fine');
        if (empty($late_fine)) {
            update_option("owt_lib_book_late_fine", 1);
        }
        $currency_code = get_option('owt_lib_currency_code');
        if (empty($currency_code)) {
            update_option("owt_lib_currency_code", "INR");
        }
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Library_Management_System_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
