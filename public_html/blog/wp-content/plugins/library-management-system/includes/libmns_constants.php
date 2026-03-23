<?php
/**
 * Library Management System – Constants
 *
 * @package    Library_Management_System
 * @since      3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_plugin_main_file = dirname( __DIR__ ) . '/library-management-system.php';

define( 'LIBMNS_VERSION', '3.4' );
define( 'LIBMNS_PREFIX', 'owt7' );
define( 'LIBMNS_BUY_PRO_VERSION_LINK', 'https://onlinewebtutorblog.com/library-management-system-wordpress-plugin/' );
define( 'LIBMNS_FREE_VERSION_DOC_LINK', 'https://onlinewebtutorblog.com/doc/lms-free-version/' );
define( 'LIBMNS_PLUGIN_DIR_PATH', plugin_dir_path( $lms_plugin_main_file ) );
define( 'LIBMNS_PLUGIN_URL', plugin_dir_url( $lms_plugin_main_file ) );
define( 'LIBMNS_BASE_NAME', plugin_basename( $lms_plugin_main_file ) );
define( 'LIBMNS_DEFAULT_SHOW_BOOKS', 10 );
define( 'LIBMNS_DEFAULT_PAGE_NUMBER', 1 );
define( 'LIBMNS_FREE_VERSION_LIMIT', 'NDA=' );
