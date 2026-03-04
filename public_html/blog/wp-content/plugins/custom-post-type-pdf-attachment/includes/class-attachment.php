<?php

class CPTA_Attachments_Init {
	
	public function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'add_custom_pdf_attachment_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_custom_pdf_attachment_meta_data' ) );
		add_action( 'post_edit_form_tag', array( $this, 'update_edit_form_for_custom_pdf_attachment' ) );
	}
	
	public function add_custom_pdf_attachment_meta_boxes() {
		$saved_types = get_option('saved_post_types_for_pdf_attachment');
		if(is_array($saved_types)){
			foreach ( $saved_types as $saved_type ) {
				add_meta_box(
					'cpt_pdf_attachment',
					'File Attachments',
					array( $this, 'cpt_pdf_attachment' ),
					$saved_type
				);
			}
		}
	}
	
	public function cpt_pdf_attachment( $post ){
		wp_enqueue_media();
		$saved_no_of_pdf_attachment = (int)get_option('saved_no_of_pdf_attachment');
		
		if($saved_no_of_pdf_attachment){
			for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
				$this->cpt_pdf_attachment_defined($post,$i);
			}
		} else {
			$this->cpt_pdf_attachment_not_defined();
		}
	}

	public function cpt_pdf_attachment_not_defined() {
		include( CPTA_PLUGIN_PATH . '/view/admin/attachment-not-defined.php');
	} 

	public function cpt_pdf_attachment_defined($post,$i) {
		wp_nonce_field('cpt_pdf_attachment_check', 'cpt_pdf_attachment_nonce');
		include( CPTA_PLUGIN_PATH . '/view/admin/attachment-form.php');
	} 
	
	public function save_custom_pdf_attachment_meta_data($id) {
		global $doc_attachment_type_array;
		$saved_no_of_pdf_attachment = (int)get_option('saved_no_of_pdf_attachment');
		
		/* --- security verification --- */
		if(!wp_verify_nonce($this->getPostVar('cpt_pdf_attachment_nonce'), 'cpt_pdf_attachment_check')) {
			return $id;
		} 
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $id;
		} 
		if('page' == $this->getPostVar('post_type')) {
			if(!current_user_can('edit_page', $id)) {
				return $id;
				wp_die('this is a page');
			} 
		} else {
			if(!current_user_can('edit_page', $id)) {
				return $id;
			} 
		} 
		/* --- security verification --- */
		
		// check for delete file //
		for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
			if(!empty($this->getPostVar('cpt_pdf_attachment_remove'.$i))) {
				delete_post_meta($id, 'cpt_pdf_attachment'.$i);
			}
		}
		// check for delete file //
	
		// file upload //
		for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
			if(cpta_use_media_library()){
				if(!empty($this->getPostVar('cpt_pdf_attachment_mf_'.$i))) {
					$cpt_pdf_attachment_file = sanitize_text_field($this->getPostVar('cpt_pdf_attachment_mf_'.$i));
					$file_name = basename($cpt_pdf_attachment_file);
					$upload_dir = wp_upload_dir();	
					
					$pos = strpos($cpt_pdf_attachment_file, $upload_dir['baseurl']);
					if ($pos !== false) {
						$file_url = str_replace($upload_dir['baseurl'],'',$cpt_pdf_attachment_file);
					}
					update_post_meta($id, 'cpt_pdf_attachment'.$i, $file_url);
				}
			} else {
				if(!empty($_FILES['cpt_pdf_attachment'.$i]['name'])) {
					
					// Get the file type of the upload
					$arr_file_type = wp_check_filetype(basename(sanitize_text_field($_FILES['cpt_pdf_attachment'.$i]['name'])));
					$uploaded_type = $arr_file_type['type'];
					
					// Check if the type is supported. If not, throw an error.
					if(in_array($uploaded_type, $doc_attachment_type_array)) {
						// Use the WordPress API to upload the file
						$upload = wp_upload_bits($_FILES['cpt_pdf_attachment'.$i]['name'], NULL, file_get_contents($_FILES['cpt_pdf_attachment'.$i]['tmp_name']));
						if(isset($upload['error']) && $upload['error'] != 0) {
							wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
						} else {
							$cpt_pdf_attachment_file = str_replace("\\","*",$upload['file']);
							$file_name = basename($cpt_pdf_attachment_file);
							$upload_dir = wp_upload_dir();	
							$file_path = $upload_dir['subdir'].'/'.$file_name;
							
							$pos = strpos($upload['url'], $upload_dir['baseurl']);
							if ($pos !== false) {
								$file_url = str_replace($upload_dir['baseurl'],'',$upload['url']);
							}
							update_post_meta($id, 'cpt_pdf_attachment'.$i, $file_url);
						} // end if/else
					} else {
						wp_die("The file type that you've uploaded is not supported.");
					} // end if/else
				}
			}
		}
		// file upload //
	}

	public function update_edit_form_for_custom_pdf_attachment() {
		echo ' enctype="multipart/form-data"';
	}


	private function getPostVar ($k) {
		return isset($_POST[$k]) ? $_POST[$k] : null;
	}

}