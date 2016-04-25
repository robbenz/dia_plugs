<?php

	global $wpdb;
	
	#############################################################################################
	if( isset($_POST[ 'wpp_hidden' ]) && $_POST[ 'wpp_hidden' ] == 'wpp_print' ) {
	
		update_option( 'wpp_save_print_img_max_width', intval( $_POST[ 'wpp_save_print_img_max_width' ] ));
		update_option( 'wpp_save_print_button_type', intval( $_POST[ 'wpp_save_print_button_type' ] ));
		update_option( 'wpp_save_print_button_url', wpp_filter_strip( $_POST[ 'wpp_save_print_button_url' ] ));
		update_option( 'wpp_save_print_button_text', wpp_filter_strip( $_POST[ 'wpp_save_print_button_text' ] ));
		update_option( 'wpp_save_print_icon_url', wpp_filter_strip( $_POST[ 'wpp_save_print_icon_url' ] ));
		update_option( 'wpp_print_css', wpp_filter_strip( $_POST[ 'wpp_print_css' ] ));
		
		echo '<div class="updated"><p><strong>'. __( 'Options saved.' ) .'</strong></p></div>';
	}
	elseif( isset($_POST[ 'wpp_hidden' ]) && $_POST[ 'wpp_hidden' ] == 'wpp_pt' ) {
	
		update_option( 'wpp_pt_head_date', wpp_filter_strip( $_POST[ 'wpp_pt_head_date' ] ));
		update_option( 'wpp_pt_head_site', wpp_filter_strip( $_POST[ 'wpp_pt_head_site' ] ));
		update_option( 'wpp_pt_head_url', wpp_filter_strip( $_POST[ 'wpp_pt_head_url' ] ));
		update_option( 'wpp_pt_title', wpp_filter_strip( $_POST[ 'wpp_pt_title' ] ));
		update_option( 'wpp_pt_image', wpp_filter_strip( $_POST[ 'wpp_pt_image' ] ));
		update_option( 'wpp_pt_excerpt', wpp_filter_strip( $_POST[ 'wpp_pt_excerpt' ] ));
		update_option( 'wpp_pt_date', wpp_filter_strip( $_POST[ 'wpp_pt_date' ] ));
		update_option( 'wpp_pt_md', wpp_filter_strip( $_POST[ 'wpp_pt_md' ] ));
		update_option( 'wpp_pt_links', wpp_filter_strip( $_POST[ 'wpp_pt_links' ] ));
		update_option( 'wpp_pt_header', wpp_filter_strip( $_POST[ 'wpp_pt_header' ] ));
		
		echo '<div class="updated"><p><strong>'. __( 'Options saved.' ) .'</strong></p></div>';
	}
	#############################################################################################
		
?>
		
<br />
<table width="100%" border="0" cellspacing="1" class="wpp_option_table">
  <tr>
    <td class="wpp_table_td">
	<div class="wpp_top_desc">
		<?php _e('Here you can manage product print options') ?>
	</div>
	</td>
  </tr>
</table> 
<br />
