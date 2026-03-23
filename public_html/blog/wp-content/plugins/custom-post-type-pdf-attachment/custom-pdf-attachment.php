<?php
/*
Plugin Name: Custom Post Type Attachment
Plugin URI: https://wordpress.org/plugins/custom-post-type-pdf-attachment/
Description: This plugin will allow you to upload files to your post or pages or any other custom post types. You can either use shortcodes or functions to display attachments.
Version: 3.4.6
Text Domain: custom-post-type-pdf-attachment
Author: aviplugins.com
Author URI: https://www.aviplugins.com/
 */

/**
 *      |||||
 *    <(`0_0`)>
 *    ()(afo)()
 *      ()-()
 **/

define('CPTA_PLUGIN_DIR', 'custom-post-type-pdf-attachment');
define('CPTA_PLUGIN_PATH', dirname(__FILE__));

function plug_install_custom_post_type_attachment()
{
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (is_plugin_active('custom-post-type-attachment-pro/custom-pdf-attachment.php')) {
        wp_die('It seems you have <strong>Custom Post Type Attachment PRO</strong> plugin activated. Please deactivate to continue.');
        exit;
    }

    include_once CPTA_PLUGIN_PATH . '/config/config-attachment-file-types.php';
    include_once CPTA_PLUGIN_PATH . '/config/config-attachment-icons.php';

    include_once CPTA_PLUGIN_PATH . '/includes/class-form.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-settings.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-scripts.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-attachment.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-attachment-list.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-latest-attachment.php';
    include_once CPTA_PLUGIN_PATH . '/includes/class-filesize.php';
    include_once CPTA_PLUGIN_PATH . '/pdf-attachment-widget.php';
    include_once CPTA_PLUGIN_PATH . '/shortcode-functions.php';
    include_once CPTA_PLUGIN_PATH . '/functions.php';

    new CPTA_Settings;
    new CPTA_Scripts;
    new CPTA_Attachments_Init;
    new CPTA_Attachments_List_Init;
}

class WP_Custom_Post_Type_Attachment_Pre_Checking
{
    public function __construct()
    {
        plug_install_custom_post_type_attachment();
    }
}

new WP_Custom_Post_Type_Attachment_Pre_Checking;

add_action('widgets_init', function () {register_widget('PDF_Attachment_Widget');});

add_shortcode('pdf_attachment', 'custom_pdf_attachment_shortcode');
add_shortcode('pdf_all_attachments', 'custom_pdf_all_attachments_shortcode');

add_action('plugins_loaded', 'cpta_load_plugin_textdomain');

register_activation_hook(__FILE__, 'cpta_setup_init');

function cpta_setup_init()
{
    global $wpdb;

    // older versions compatibility //
    $upload_dir = wp_upload_dir();
    $query = "UPDATE `" . $wpdb->base_prefix . "postmeta` set meta_value = replace(meta_value,'" . $upload_dir['baseurl'] . "','') WHERE `meta_key` LIKE '%cpt_pdf_attachment%'";
    $wpdb->query($query);
    // older versions compatibility //
}
