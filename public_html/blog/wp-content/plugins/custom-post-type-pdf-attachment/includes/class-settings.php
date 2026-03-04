<?php
class CPTA_Settings {

	public function __construct() {
		$this->load_settings();
	}
	
	public function custom_pdf_attachment_post_data(){
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "save_custom_pdf_attachment_settings"){
			
			if ( ! isset( $_POST['save_custom_pdf_attachment_action_field'] ) || ! wp_verify_nonce( $_POST['save_custom_pdf_attachment_action_field'], 'save_custom_pdf_attachment_action' ) ) {
			   wp_die( 'Sorry, your nonce did not verify.');
			} 
			
			if( isset($_POST['attachment_post_types']) and is_array($_POST['attachment_post_types']) ) {
				$saved_post_types_for_pdf_attachment = array_map('sanitize_text_field',$_POST['attachment_post_types']);
				update_option( 'saved_post_types_for_pdf_attachment', $saved_post_types_for_pdf_attachment );
			} else {
				delete_option( 'saved_post_types_for_pdf_attachment' );
			}
			
			if(isset($_POST['no_of_pdf_attachment'])){
				update_option( 'saved_no_of_pdf_attachment', sanitize_text_field($_POST['no_of_pdf_attachment']) );
			} else {
				delete_option( 'saved_no_of_pdf_attachment' );
			}
			
			if(isset($_POST['pdf_open_in'])){
				update_option( 'pdf_open_in', sanitize_text_field($_POST['pdf_open_in']) );
			} else {
				delete_option( 'pdf_open_in' );
			}

			if(isset($_POST['use_default_media_library'])){
				update_option( 'use_default_media_library', sanitize_text_field($_POST['use_default_media_library']) );
			} else {
				delete_option( 'use_default_media_library' );
			}
			
			if(isset($_POST['display_file_size'])){
				update_option( 'display_file_size', sanitize_text_field($_POST['display_file_size']) );
			} else {
				delete_option( 'display_file_size' );
			}
			
			$GLOBALS['msg'] = __('Data updated successfully','custom-post-type-pdf-attachment');
		}
	}
	
	public function post_types_selected( $saved_types = '' ){
		$args = array(
		'public'   => true,
		);
		$post_types = get_post_types( $args, 'names' ); 
		$post_types = array_diff($post_types, array('attachment'));
		foreach ( $post_types as $post_type ) {
			if(is_array($saved_types) and in_array($post_type,$saved_types)){
				echo '<p><label>' . Form_Class::form_checkbox('attachment_post_types[]','attachment_post_types',$post_type,'','','',true,'','',false) . ' ' . $post_type . '</label></p>';
			} else{
				echo '<p><label>' . Form_Class::form_checkbox('attachment_post_types[]','attachment_post_types',$post_type,'','','',false,'','',false) . ' ' . $post_type . '</label></p>';
			}
		}
	}
	
	public function get_no_of_pdf_files_selected( $saved_no_of_pdf_attachment = '' ){
		$ret = '';
		$ret .= '<option value="">-</option>';
		for($i=1; $i<=10; $i++){
			if($saved_no_of_pdf_attachment == $i){
				$ret .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			} else {
				$ret .= '<option value="'.$i.'">'.$i.'</option>';
			}
		}
		return $ret;
	}
	
	public function get_pdf_open_in_selected( $pdf_open_in = '' ){
		$open_in_array = array( '_self' => 'Same Window', '_blank' => 'New Window' );
		$ret = '';
		
		if(is_array($open_in_array)){
			foreach( $open_in_array as $key => $value ){
				if($key == $pdf_open_in){
					$ret .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
				} else {
					$ret .= '<option value="'.$key.'">'.$value.'</option>';
				}
			}
		}
		return $ret;
	}
	
	public function show_message(){
		if(isset($GLOBALS['msg'])){
			echo '<div class="updated"><p>'.$GLOBALS['msg'].'</p></div>';
			$GLOBALS['msg'] = '';
		}
	}
	
	public function help_info($saved_no_of_pdf_attachment=''){
		include( CPTA_PLUGIN_PATH . '/view/admin/help-info.php');
	}

	public function pro_add(){
		include( CPTA_PLUGIN_PATH . '/view/admin/pro-add.php');
	}
	
	public function  custom_pdf_attachment_options () {
		global $wpdb;
		$saved_types 					= get_option('saved_post_types_for_pdf_attachment');
		$saved_no_of_pdf_attachment 	= (int)get_option('saved_no_of_pdf_attachment');
		$pdf_open_in 					= get_option('pdf_open_in');
		$use_default_media_library 		= get_option('use_default_media_library');
		$display_file_size 				= get_option('display_file_size');

		echo '<div class="wrap">';
		$this->show_message();
		$this->help_support();
		$this->pro_add();
		Form_Class::form_open();
		wp_nonce_field( 'save_custom_pdf_attachment_action', 'save_custom_pdf_attachment_action_field' );
		Form_Class::form_input('hidden','option','','save_custom_pdf_attachment_settings');
		include( CPTA_PLUGIN_PATH . '/view/admin/settings.php');
		Form_Class::form_close();
		$this->info();
		$this->donate();
		echo '</div>';
	}
	
	public function help_support(){
		include( CPTA_PLUGIN_PATH . '/view/admin/help.php');
	}

	public function info(){
		include( CPTA_PLUGIN_PATH . '/view/admin/info.php');
	}

	public function custom_pdf_attachment_plugin_menu () {
		add_options_page( 'Custom Post Type Attachment', 'Custom Post Type Attachment', 'activate_plugins', 'custom_pdf_attachment',  array( $this,'custom_pdf_attachment_options' ) );
	}
	
	public function load_settings(){
		add_action('admin_menu', array( $this, 'custom_pdf_attachment_plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'custom_pdf_attachment_post_data' ) );
	}
	
	public function donate(){
		include( CPTA_PLUGIN_PATH . '/view/admin/donate.php');
	} 
	
}