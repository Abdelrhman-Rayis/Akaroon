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
class Library_Management_System_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    3.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'library-management-system',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
