<table width="100%" border="0" class="ap-table">
  <tr>
    <td><h3><?php _e('Custom Post Type Attachment');?></h3></td>
  </tr>
  
  <tr>
    <td>
    
      <div class="ap-tabs">
        <div class="ap-tab"><?php _e('Settings','login-sidebar-widget');?></div>
        <div class="ap-tab"><?php _e('Shortcode','login-sidebar-widget');?></div>
    </div>
    <div class="ap-tabs-content">
        <div class="ap-tab-content">
        <table width="100%">
          <tr>
            <td><h3><?php _e('Select Post Types Where You Want Custom File Attachments','custom-post-type-pdf-attachment');?></h3></td>
          </tr>
          <tr>
            <td><?php $this->post_types_selected($saved_types); ?></td>
          </tr>
          <tr>
            <td><h3><?php _e('Enter Number of Attachment Files','custom-post-type-pdf-attachment');?></h3></td>
          </tr>
          <tr>
            <td><?php Form_Class::form_input('number', 'no_of_pdf_attachment', 'no_of_pdf_attachment', $saved_no_of_pdf_attachment, '', '', '', '', 5);?></td>
          </tr>
          <tr>
            <td><h3><?php _e('PDF Open in','custom-post-type-pdf-attachment');?></h3></td>
          </tr>
          <tr>
            <td><?php Form_Class::form_select('pdf_open_in','',$this->get_pdf_open_in_selected($pdf_open_in));?></td>
          </tr>
          <tr>
            <td><h3><?php _e('Use Media Library','custom-post-type-pdf-attachment');?></h3></td>
          </tr>
          <tr>
            <td>
            <label>
            <?php 
            $use_default_media_library_value = ($use_default_media_library == 'Yes'?true:false);
            Form_Class::form_checkbox('use_default_media_library','use_default_media_library','Yes','','','', $use_default_media_library_value);?>
            <?php _e('If enabled then default media library will be used.','custom-post-type-pdf-attachment');?>
            </label>
            </td>
          </tr>
          <tr>
            <td><h3><?php _e('Display File Size','custom-post-type-pdf-attachment');?></h3></td>
          </tr>
          <tr>
            <td>
            <label>
            <?php 
            $display_file_size_value = ($display_file_size == 'Yes'?true:false);
            Form_Class::form_checkbox('display_file_size','display_file_size','Yes','','','', $display_file_size_value);?>
            <?php _e('If enabled then file size will be displayed beside file name.','custom-post-type-pdf-attachment');?>
            </label>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><?php Form_Class::form_input('submit','submit','',__('Save','custom-post-type-pdf-attachment'),'button button-primary button-large button-ap-large');?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          </table>
        </div>
        <div class="ap-tab-content">
        <table width="100%">
           <tr>
            <td><?php $this->help_info($saved_no_of_pdf_attachment); ?></td>
          </tr> 
        </table>
        </div>
    </div>
    </td>
  </tr>
</table>