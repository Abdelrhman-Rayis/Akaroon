<?php

if (!function_exists('cpta_load_plugin_textdomain')) {
    function cpta_load_plugin_textdomain()
    {
        load_plugin_textdomain('custom-post-type-pdf-attachment', false, basename(dirname(__FILE__)) . '/languages/');
    }
}

if (!function_exists('cpta_get_file_url')) {
    function cpta_get_file_url($post_id = '', $file = 1)
    {
        $file_path = esc_html(get_post_meta($post_id, 'cpt_pdf_attachment' . $file, true));
        if ($file_path) {
            $upload_dir = wp_upload_dir();
            return $upload_dir['baseurl'] . $file_path;
        }
        return false;
    }
}

if (!function_exists('cpta_use_media_library')) {
    function cpta_use_media_library()
    {
        $use_default_media_library = get_option('use_default_media_library');
        return ($use_default_media_library == 'Yes' ? true : false);
    }
}
