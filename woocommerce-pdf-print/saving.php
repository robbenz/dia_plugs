<?php

	global $wpdb;
	
	#############################################################################################
	if( isset( $_POST[ 'wpp_hidden' ]) && $_POST[ 'wpp_hidden' ] == 'wpp_saving' ) {
		update_option( 'wpp_doc_t_title', wpp_filter_strip( $_POST[ 'wpp_doc_t_title' ] ));
		update_option( 'wpp_doc_t_image', wpp_filter_strip( $_POST[ 'wpp_doc_t_image' ] ));
		update_option( 'wpp_doc_t_excerpt', wpp_filter_strip( $_POST[ 'wpp_doc_t_excerpt' ] ));
		update_option( 'wpp_doc_t_date', wpp_filter_strip( $_POST[ 'wpp_doc_t_date' ] ));
		update_option( 'wpp_doc_t_md', wpp_filter_strip( $_POST[ 'wpp_doc_t_md' ] ));
		update_option( 'wpp_save_doc_img_max_width', intval( $_POST[ 'wpp_save_doc_img_max_width' ] ));
		update_option( 'wpp_save_doc_template', intval( $_POST[ 'wpp_save_doc_template' ] ));
		update_option( 'wpp_save_doc_button_type', intval( $_POST[ 'wpp_save_doc_button_type' ] ));
		update_option( 'wpp_save_doc_button_url', wpp_filter_strip( $_POST[ 'wpp_save_doc_button_url' ] ));
		update_option( 'wpp_save_doc_button_text', wpp_filter_strip( $_POST[ 'wpp_save_doc_button_text' ] ));
		update_option( 'wpp_save_doc_icon_url', wpp_filter_strip( $_POST[ 'wpp_save_doc_icon_url' ] ));
		update_option( 'wpp_save_doc_css', wpp_filter_strip( $_POST[ 'wpp_save_doc_css' ] ));
		
		update_option( 'wpp_save_pdf_img_show', intval( $_POST[ 'wpp_save_pdf_img_show' ] ));
		update_option( 'wpp_save_pdf_img_max_width', intval( $_POST[ 'wpp_save_pdf_img_max_width' ] ));
		update_option( 'wpp_save_pdf_rus', intval( $_POST[ 'wpp_save_pdf_rus' ] ));
		update_option( 'wpp_save_pdf_button_type', intval( $_POST[ 'wpp_save_pdf_button_type' ] ));
		update_option( 'wpp_save_pdf_button_url', wpp_filter_strip( $_POST[ 'wpp_save_pdf_button_url' ] ));
		update_option( 'wpp_save_pdf_button_text', wpp_filter_strip( $_POST[ 'wpp_save_pdf_button_text' ] ));
		update_option( 'wpp_save_pdf_icon_url', wpp_filter_strip( $_POST[ 'wpp_save_pdf_icon_url' ] ));
	
	}
	#############################################################################################
	
		
?>
		
<br />
<table width="100%" border="0" cellspacing="1" class="wpp_option_table">
  <tr>
    <td class="wpp_table_td">
	<div class="wpp_top_desc">
		<?php _e('Here you can manage saving of products as PDF and MS Word Document.') ?>
	</div>
	</td>
  </tr>
</table> 
<br />
<form id="wpp_form_saving" name="wpp_form_saving" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" name="wpp_hidden" value="wpp_saving">