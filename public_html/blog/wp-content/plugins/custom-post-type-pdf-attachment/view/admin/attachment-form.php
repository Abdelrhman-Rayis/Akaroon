<div class="attachment-form">
<p><strong><?php _e('Upload File', 'custom-post-type-pdf-attachment');?> - <?php echo $i;?></strong></p>

<?php if(cpta_use_media_library()){ ?>
<p>
<a href="javascript:cpta_pdf_file_uploader('<?php echo $i;?>');" class="button"><?php _e('Upload File','custom-post-type-pdf-attachment');?></a>
<input type="hidden" name="cpt_pdf_attachment_mf_<?php echo $i;?>" id="cpt_pdf_attachment_mf_<?php echo $i;?>" value="">
</p>
<p><span id="temp_file_show_<?php echo $i;?>"></span></p>
<?php } else { ?>
<p>
<?php Form_Class::form_input('file','cpt_pdf_attachment'.$i,'cpt_pdf_attachment'.$i,'','','','','','','','','','',true);?>
</p>
<?php } ?>

<p>
<?php
$file_url = cpta_get_file_url($post->ID,$i);
if($file_url){
    $file_info = pathinfo( $file_url );
    echo '<a href="'.cpta_get_file_url($post->ID,$i).'" target="_blank">'.$file_info['basename'].'</a>';
}
if($file_url){
    echo '<p><label>' . __('Check to remove','custom-post-type-pdf-attachment') . ' ' . Form_Class::form_checkbox('cpt_pdf_attachment_remove'.$i,'cpt_pdf_attachment_remove'.$i,$i,'','','','','','',false) . '</label></p>';
}
?>
</p>

<p><strong>Shortcode:</strong> [pdf_attachment file="<?php echo $i;?>" name="optional file name"]</p>
</div>