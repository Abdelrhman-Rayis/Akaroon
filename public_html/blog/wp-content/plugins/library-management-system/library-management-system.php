<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://onlinewebtutorhub.blogspot.in/
 * @since             1.0.0
 * @package           Library_Management_System
 *
 * @wordpress-plugin
 * Plugin Name:       Library Management System
 * Plugin URI:        https://onlinewebtutorhub.blogspot.com/p/library-management-system.html
 * Description:       Library Management System plugin gives you the flexibility to manage students, staffs, books etc. By the help of which you can issue book to users. All the feature details you can find at Plugin main dashboard.
 * Version:           1.0.0
 * Author:            Online Web Tutor
 * Author URI:        http://onlinewebtutorhub.blogspot.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       library-management-system
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('FS_METHOD')) {
    define("FS_METHOD", "direct");
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('OWT_LIBRARY_PLUGIN_VERSION', '1.0.0');
define('OWT_LIBRARY_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('OWT_LIBRARY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('OWT_LIBRARY_PLUGIN_PREFIX', "wpowt-pl");
define('OWT_LIBRARY_PLUGIN_BASEPATH', plugin_basename(__FILE__));
define('OWT_LIBRARY_PLUGIN_BOOK_LATE_FINE', 2);
define('OWT_LIBRARY_PLUGIN_BOOK_CURRENCY', get_option("owt_lib_currency_code"));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-library-management-system-activator.php
 */
function activate_library_management_system() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-library-management-system-activator.php';
    $table_activator = new Library_Management_System_Activator();
    $table_activator->owt_library_tables_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-library-management-system-deactivator.php
 */
function deactivate_library_management_system() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-library-management-system-deactivator.php';
    $table_deactivator = new Library_Management_System_Deactivator();
    $table_deactivator->deactivate();
}

register_activation_hook(__FILE__, 'activate_library_management_system');
register_deactivation_hook(__FILE__, 'deactivate_library_management_system');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-library-management-system.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_library_management_system() {

    $plugin = new Library_Management_System();
    $plugin->run();
}

run_library_management_system();
