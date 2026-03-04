<?php

class CPTA_Attachments_List_Init {
	
	public function __construct(){
		add_filter('manage_posts_columns', array( $this, 'cpta_files_header' ), 10 );
		add_action('manage_posts_custom_column', array( $this, 'cpta_files_list' ), 10, 2);
		add_filter('manage_pages_columns', array( $this, 'cpta_files_header' ), 10);
		add_action('manage_pages_custom_column', array( $this, 'cpta_files_list' ), 10, 2);
	}
	
	public function cpta_files_header( $defaults ) {
		$defaults['cpta_files'] = __('Attached Files','custom-post-type-pdf-attachment');
		return $defaults;
	}

	public function cpta_files_list( $column_name, $post_id ) {
		if ($column_name == 'cpta_files'){
			$attachments = $this->custom_pdf_all_attachments( $post_id );
			echo $attachments;
		}
	}
	
	public function custom_pdf_all_attachments( $post_id ) {
		 $saved_no_of_pdf_attachment = (int)get_option('saved_no_of_pdf_attachment');
		 $ret = '';
		 for($i=1; $i <= $saved_no_of_pdf_attachment; $i++ ){
			 $file_att = $this->pdf_attachment_file($i, $post_id);
			 if($file_att){
				$ret .= '<p>';
				$ret .= $file_att;
				$ret .= '</p>';
			 }
		 }
		 return $ret;
	}
	
	public function pdf_attachment_file( $file, $post_id ){
		global $doc_images_array;
		
		if(!$file){
		return;
		}

		$file_url = cpta_get_file_url($post_id,$file);
		if(!$file_url){
			return;
		}

		$pdf_open_in = get_option('pdf_open_in');
		$target = ( $pdf_open_in == '' ? '_self' : $pdf_open_in ); 

		$file_mime_data = wp_check_filetype($file_url);
		
		$file_image = '<img src="'.plugins_url( CPTA_PLUGIN_DIR . '/images/'.$doc_images_array[$file_mime_data['type']] ).'" class="cpt-file-icon">';

		$file_mime_data = wp_check_filetype(cpta_get_file_url($post_id,$file));
		$file_info = pathinfo(cpta_get_file_url($post_id,$file));
		$ret = $file_image . ' ' . '<a href="'.cpta_get_file_url($post_id,$file).'" target="'.$target.'">'.$file_info['basename'].'</a>';

		return $ret;
	}
}