<?php

class PDF_Attachment_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'pdf_attachment_wid',
			'Latest Attachment Widget',
			array( 'description' => __( 'Widget to display latest attachment files', 'custom-post-type-pdf-attachment' ), )
		);
	 }

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['wid_title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['wid_title'] ) . $args['after_title'];
		}
		$pla = new PDF_Latest_Attachment;
		echo $pla->custom_pdf_all_attachments( $instance );
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		$instance['wid_post_type'] = sanitize_text_field( $new_instance['wid_post_type'] );
		$instance['wid_show_post_title'] = sanitize_text_field( $new_instance['wid_show_post_title'] );
		return $instance;
	}
	
	public function post_types_selected($sel = ''){
		$args = array(
		'public'   => true,
		);
		$ret = '';
		$post_types = get_post_types( $args, 'names' ); 
		$post_types = array_diff($post_types, array('attachment'));
		foreach ( $post_types as $post_type ) {
			if($sel == $post_type){
				$ret .= '<option value="'.$post_type.'" selected>'.$post_type.'</option>';
			} else{
				$ret .= '<option value="'.$post_type.'">'.$post_type.'</option>';
			}
		}
		return $ret;
	}
	
	public function form( $instance ) {
		$wid_title = '';
		if(!empty($instance[ 'wid_title' ])){
			$wid_title = esc_html($instance[ 'wid_title' ]);
		}
		$wid_post_type = '';
		if(!empty($instance[ 'wid_post_type' ])){
			$wid_post_type = sanitize_text_field($instance[ 'wid_post_type' ]);
		}
		$wid_show_post_title = '';
		if(!empty($instance[ 'wid_show_post_title' ])){
			$wid_show_post_title = sanitize_text_field($instance[ 'wid_show_post_title' ]);
		}
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title','custom-post-type-pdf-attachment'); ?> </label>
        <input type="text" name="<?php echo $this->get_field_name('wid_title');?>" id="<?php echo $this->get_field_id('wid_title');?>" value="<?php echo $wid_title;?>" class="widefat">
		</p>
        
        <p><label for="<?php echo $this->get_field_id('wid_post_type'); ?>"><?php _e('Post Type','custom-post-type-pdf-attachment'); ?> </label>
        <select name="<?php echo $this->get_field_name('wid_post_type');?>" id="<?php echo $this->get_field_id('wid_post_type');?>" class="widefat">
        	<option value="">-</option>
			<?php echo $this->post_types_selected($wid_post_type);?>
        </select>
		</p>
        
        <p><label for="<?php echo $this->get_field_id('wid_show_post_title'); ?>"><?php _e('Display post title','custom-post-type-pdf-attachment'); ?> </label>
        <input type="checkbox" name="<?php echo $this->get_field_name('wid_show_post_title');?>" id="<?php echo $this->get_field_id('wid_show_post_title');?>" value="Yes" <?php echo ($wid_show_post_title == 'Yes'?'checked':'');?>>
		</p>
        <p><em><?php _e('Attachment 1 has to be present in the post to display it as the latest post','custom-post-type-pdf-attachment'); ?></em></p>
		<?php 
	}
	
} 