<?php
if(!function_exists('custom_pdf_attachment_shortcode')){
	function custom_pdf_attachment_shortcode( $atts ) {
		 global $post, $doc_images_array;
		 extract( shortcode_atts( array(
			  'file' => '',
			  'name' => ''
		 ), $atts ) );
		 
		 if(!$file){
			return;
		 }
		 
		 $file_url = cpta_get_file_url($post->ID,$file);
		 if(!$file_url){
			return;
		 }
		 
		 $pdf_open_in = get_option('pdf_open_in');
		 $target = ( $pdf_open_in == '' ? '_self' : $pdf_open_in ); 
		 
		 $file_mime_data = wp_check_filetype($file_url);

		 $file_image = '<img src="'.plugins_url( CPTA_PLUGIN_DIR . '/images/'.$doc_images_array[$file_mime_data['type']] ).'" class="cpt-file-icon">';

		 $file_size = '';
		 if(get_option('display_file_size') == 'Yes'){
			$file_size = '('.cpta_get_file_size($file_url).')';
		 }

		 if($name){
			$ret = $file_image . ' ' . '<a href="'.cpta_get_file_url($post->ID,$file).'" target="'.$target.'">'.$name.'</a>' . ' ' . $file_size;
		} else {
			$file_mime_data = wp_check_filetype(cpta_get_file_url($post->ID,$file));
			$file_info = pathinfo(cpta_get_file_url($post->ID,$file));
			$ret = $file_image . ' ' . '<a href="'.cpta_get_file_url($post->ID,$file).'" target="'.$target.'">'.$file_info['basename'].'</a>' . ' ' . $file_size;
		}
		 return $ret;
	}
}

if(!function_exists('pdf_attachment_file')){
	function pdf_attachment_file($file = '', $name = ''){
		if(!$file){
			return;
		}
		return do_shortcode('[pdf_attachment file="'.$file.'" name="'.$name.'"]');
	}
}

if(!function_exists('custom_pdf_all_attachments_shortcode')){
	function custom_pdf_all_attachments_shortcode() {
		 global $post;
		 $saved_no_of_pdf_attachment = (int)get_option('saved_no_of_pdf_attachment');
		 $ret = '';
		 for($i=1; $i <= $saved_no_of_pdf_attachment; $i++ ){
			 $file_att = pdf_attachment_file($i);
			 if($file_att){
				$ret .= '<p>';
				$ret .= $file_att;
				$ret .= '</p>';
			 }
		 }
		 return $ret;
	}
}

if(!function_exists('pdf_all_attachment_files')){
	function pdf_all_attachment_files(){
		return do_shortcode('[pdf_all_attachments]');
	}
}

if(!function_exists('cpta_get_file_size')){
	function cpta_get_file_size($url = ''){
		if(!$url){
			return;
		}
		$fs = new CPTA_File_Size;
		return $fs->get_file_size($url);
	}
}