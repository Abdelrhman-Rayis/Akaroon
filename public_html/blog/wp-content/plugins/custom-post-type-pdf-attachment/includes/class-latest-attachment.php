<?php
class PDF_Latest_Attachment {

	public function get_latest_post_id( $instance ){
		if(empty($instance['wid_post_type'])){
			return;
		}	
		$saved_no_of_pdf_attachment = 1;

		$args = array(
			'posts_per_page' => 1,
			'post_type' => $instance['wid_post_type'],
			'meta_query' => array(
				'relation' => 'OR',
			),
		);
		
		for( $i=1; $i <= $saved_no_of_pdf_attachment; $i++ ){
			$args['meta_query'][] = array(
				'key'     => 'cpt_pdf_attachment'.$i,
				'compare' => 'EXISTS',
			);
		}
		
		$query = new WP_Query( $args );
		
		while ( $query->have_posts() ) {
			$query->the_post();
			$att_post_id = $query->post->ID;
		}
		
		return $att_post_id;
	}
	
	public function custom_pdf_all_attachments( $instance ) {
		 $latest_post_id = $this->get_latest_post_id($instance);
		 
		 if(empty($latest_post_id )){
			return '<div class="pdf-attachment-widget">' . __('No records found','custom-post-type-pdf-attachment') . '</div>';
		 }	
		 
		 $ret = '';
		 if ( $instance['wid_show_post_title'] == 'Yes' ) {
			$ret .= '<h4><a href="' . get_permalink($latest_post_id) . '">' . get_the_title($latest_post_id) . '</a></h4>';
		 }
		 
		 $saved_no_of_pdf_attachment = (int)get_option('saved_no_of_pdf_attachment');
		 for($i=1; $i <= $saved_no_of_pdf_attachment; $i++ ){
			 $file_att = $this->pdf_attachment_file($latest_post_id, $i);
			 if($file_att){
				$ret .= '<p>';
				$ret .= $file_att;
				$ret .= '</p>';
			 }
		 }
		
		return '<div class="pdf-attachment-widget">' . $ret . '</div>';
	}
	
	public function pdf_attachment_file( $post_id, $file ){
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
		 
		 $file_image = '<img src="'.plugins_url( CPTA_PLUGIN_DIR . '/images/'.$doc_images_array[$file_mime_data['type']] ).'" class="cpt-file-icon">';

		 if($name){
			$ret = $file_image . ' ' . '<a href="'.cpta_get_file_url($post_id,$file).'" target="'.$target.'">'.$name.'</a>';
		} else {
			$file_mime_data = wp_check_filetype(cpta_get_file_url($post_id,$file));
			$file_info = pathinfo(cpta_get_file_url($post_id,$file));
			$ret = $file_image . ' ' . '<a href="'.cpta_get_file_url($post_id,$file).'" target="'.$target.'">'.$file_info['basename'].'</a>';
		}
		 return $ret;
	}
	
}
