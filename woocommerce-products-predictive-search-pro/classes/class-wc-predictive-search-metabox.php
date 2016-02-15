<?php
/**
 * Predictive Search Meta
 *
 * Class Function into WP e-Commerce plugin
 *
 * Table Of Contents
 *
 *
 * create_custombox()
 * a3_people_metabox()
 */
class WC_Predictive_Search_Meta
{
	public static function create_custombox() {
		global $post;
		global $wc_ps_exclude_data;

		$check = '';
		if ( $wc_ps_exclude_data->get_item( $post->ID, get_post_type( $post->ID ) ) > 0 ) {
			$check = 'checked="checked"';
		}

		$hide_item_from_result_text = ' <span style="float:right;" class="a3_ps_exclude_item"><label><input style="position: relative; top: 2px;" type="checkbox" '.$check.' value="1" name="ps_exclude_item" /> '.__('Hide from Predictive Search results.', 'woops').'</label></span>';

		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'post', 'normal', 'high' );
		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'page', 'normal', 'high' );
		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'product', 'normal', 'high' );
	}

	public static function data_metabox() {
		global $post;
		$postid = $post->ID;

		global $wc_ps_keyword_data;

		$ps_focuskw = $wc_ps_keyword_data->get_item( $postid );
	?>
    	<table class="form-table" cellspacing="0">
        	<tr valign="top">
				<th scope="rpw" class="titledesc"><label for="_predictive_search_focuskw"><?php _e('Focus Keywords', 'woops'); ?></label></th>
				<td class="forminp"><div class="wide_div"><input type="text" value="<?php esc_attr_e( $ps_focuskw );?>" id="_predictive_search_focuskw" name="_predictive_search_focuskw" style="width:98%;" /></div></td>
			</tr>
        </table>
	<?php

	}

	public static function save_custombox($post_id) {
		$post_status = get_post_status($post_id);
		$post_type = get_post_type($post_id);
		if ( in_array($post_type, array('post', 'page', 'product') ) && isset( $_REQUEST['_predictive_search_focuskw'] ) && $post_status != false  && $post_status != 'inherit' ) {

			global $wc_ps_keyword_data;
			global $wc_ps_exclude_data;

			$predictive_search_focuskw = trim( $_REQUEST['_predictive_search_focuskw'] );
			if ( '' != $predictive_search_focuskw ) {
				$wc_ps_keyword_data->update_item( $post_id, $predictive_search_focuskw );
			} else {
				$wc_ps_keyword_data->delete_item( $post_id );
			}

			if ( isset( $_REQUEST['ps_exclude_item'] ) && $_REQUEST['ps_exclude_item'] == 1 ) {
				$wc_ps_exclude_data->insert_item( $post_id , $post_type );
			} else {
				$wc_ps_exclude_data->delete_item( $post_id, $post_type );
			}

		}
	}
}
?>