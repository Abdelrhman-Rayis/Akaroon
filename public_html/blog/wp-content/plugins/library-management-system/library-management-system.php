<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.4
 * @package    Library_Management_System
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 * @wordpress-plugin
 * Plugin Name:       Library Management System
 * Plugin URI:        https://onlinewebtutorblog.com/library-management-system-wordpress-plugin/
 * Description:       Library Management System plugin gives the flexibility to manage users, branches, bookcases, sections, categories, books, etc. By using this LMS plugin you can <strong>Manage the Library System of Users</strong>. Plugin manage reports, late fine system, filters, etc. Using: <strong>LMS Free Version</strong>
 * Version:           3.4
 * Author:            Online Web Tutor
 * Author URI:        https://onlinewebtutorblog.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       library-management-system
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/libmns_constants.php';

function activate_library_management_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-management-system-activator.php';
	$table_activator = new Library_Management_System_Activator();
    $table_activator->activate();
}

function deactivate_library_management_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-management-system-activator.php';
	$table_activator = new Library_Management_System_Activator();
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-management-system-deactivator.php';
	$table_deactivator = new Library_Management_System_Deactivator($table_activator);
    $table_deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_library_management_system' );
register_deactivation_hook( __FILE__, 'deactivate_library_management_system' );

require plugin_dir_path( __FILE__ ) . 'includes/class-library-management-system.php';

function run_library_management_system() {
	$plugin = new Library_Management_System();
	$plugin->run();
}
run_library_management_system();
