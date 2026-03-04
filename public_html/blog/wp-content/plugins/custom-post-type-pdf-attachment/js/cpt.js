function cpta_pdf_file_uploader( file ){
	var file_frame;
	if ( file_frame ) {
	  file_frame.open();
	  return;
	}
	file_frame = wp.media.frames.file_frame = wp.media({
	  title: jQuery( this ).data( 'uploader_title' ),
	  button: {
		text: jQuery( this ).data( 'uploader_button_text' ),
	  },
	  multiple: false  // Set to true to allow multiple files to be selected
	});
	file_frame.on( 'select', function() {
	var selection = file_frame.state().get('selection');
	selection.map( function( attachment ) {
		attachment = attachment.toJSON();
		jQuery('#cpt_pdf_attachment_mf_'+file).val(attachment.url);
		jQuery('#temp_file_show_'+file).html(attachment.filename);
	});
	});
	file_frame.open();  
}