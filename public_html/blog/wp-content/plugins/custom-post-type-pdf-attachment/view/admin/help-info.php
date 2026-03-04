<table width="100%" border="0">
	<tr>
        <td colspan="2"><h3>Show All Attachments with a Single Shortcode or Function:</h3> </td>
      </tr>
    <tr>
        <td>
        <strong>Shortcode :</strong> <span style="color:#008EC2">[pdf_all_attachments]</span> use this in the post/ page content where you have uploaded the attachments.
        <br>
        <strong>Custom Function :</strong> <span style="color:#008EC2">&lt;?php echo pdf_all_attachment_files();?&gt;</span> use this in your template if you don't want to use shortcodes.</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
    <?php  
	if($saved_no_of_pdf_attachment > 0){
		for($i=1; $i<=$saved_no_of_pdf_attachment; $i++){
		?>
		<tr>
			<td colspan="2"><strong style="color:#008EC2">Attachment <?php echo $i;?> :</strong> </td>
		  </tr>
		<tr>
			<td>
			<strong>Shortcode :</strong> <span style="color:#008EC2">[pdf_attachment file="<?php echo $i;?>" name="optional file name"]</span> use this in the post/ page content where you have uploaded the attachments.
			<br>
			<strong>Custom Function :</strong> <span style="color:#008EC2">&lt;?php echo pdf_attachment_file(<?php echo $i;?>,"optional file name");?&gt;</span> use this in your template if you don't want to use shortcodes.</td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
      <?php
		}
	}
	?>
</table>