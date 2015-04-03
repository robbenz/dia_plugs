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
		$exclude_items = array();
		if (get_post_type($post->ID) == 'product') {
			$exclude_items = (array) get_option('woocommerce_search_exclude_products');
		} elseif (get_post_type($post->ID) == 'page') {
			$exclude_items = (array) get_option('woocommerce_search_exclude_pages');
		} elseif (get_post_type($post->ID) == 'post') {
			$exclude_items = (array) get_option('woocommerce_search_exclude_posts');
		}
		$check = '';
		if (is_array($exclude_items) && in_array($post->ID, $exclude_items)) {
			$check = 'checked="checked"';
		}
		
		$hide_item_from_result_text = ' <span style="float:right;" class="a3_woocommerce_search_exclude_item"><label><input style="position: relative; top: 2px;" type="checkbox" '.$check.' value="1" name="_woocommerce_search_exclude_item" /> '.__('Hide from Predictive Search results.', 'woops').'</label></span>';
		
		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'post', 'normal', 'high' );
		
		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'page', 'normal', 'high' );
		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops').$hide_item_from_result_text , array('WC_Predictive_Search_Meta','data_metabox'), 'product', 'normal', 'high' );
	}
	
	public static function data_metabox() {
		global $post;
		$postid = $post->ID;
		
		$_predictive_search_focuskw = get_post_meta( $postid, '_predictive_search_focuskw', true );
		
	?>
    	<table class="form-table" cellspacing="0">
        	<tr valign="top">
				<th scope="rpw" class="titledesc"><label for="_predictive_search_focuskw"><?php _e('Focus Keywords', 'woops'); ?></label></th>
				<td class="forminp"><div class="wide_div"><input type="text" value="<?php esc_attr_e($_predictive_search_focuskw );?>" id="_predictive_search_focuskw" name="_predictive_search_focuskw" style="width:98%;" /></div></td>
			</tr>
        </table>
	<?php
		
	}
	
	public static function save_custombox($post_id) {
		$post_status = get_post_status($post_id);
		$post_type = get_post_type($post_id);
		if(in_array($post_type, array('post', 'page', 'product') ) && isset($_REQUEST['_predictive_search_focuskw']) && $post_status != false  && $post_status != 'inherit') {
			extract($_REQUEST);
			
			update_post_meta($post_id, '_predictive_search_focuskw', $_predictive_search_focuskw );
			
			if ($post_type == 'product') {
				$exclude_option = 'woocommerce_search_exclude_products';
			} elseif ($post_type == 'page') {
				$exclude_option = 'woocommerce_search_exclude_pages';
			} elseif ($post_type == 'post') {
				$exclude_option = 'woocommerce_search_exclude_posts';
			}
			
			$exclude_items = (array) get_option($exclude_option);
			if (!is_array($exclude_items)) $exclude_items = array();
			
			if (isset($_REQUEST['_woocommerce_search_exclude_item']) && $_REQUEST['_woocommerce_search_exclude_item'] == 1) {
				if (!in_array($post_id, $exclude_items)) $exclude_items[] = $post_id;
			} else {
				$exclude_items = array_diff($exclude_items, array($post_id));
			}
			update_option($exclude_option, $exclude_items);
		}
	}
}
?>