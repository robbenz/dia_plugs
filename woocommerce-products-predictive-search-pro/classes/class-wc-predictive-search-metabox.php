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

		add_meta_box( 'wc_predictive_search_metabox', __('Predictive Search Meta', 'woops') , array('WC_Predictive_Search_Meta','data_metabox'), array( 'post', 'page', 'product' ), 'normal', 'high' );
	}

	public static function data_metabox() {
		global $post;
		$postid = $post->ID;

		global $wc_ps_keyword_data, $wc_ps_exclude_data;

		$is_excluded = false;
		if ( $wc_ps_exclude_data->get_item( $postid, get_post_type( $postid ) ) > 0 ) {
			$is_excluded = true;
		}

		$ps_focuskw = $wc_ps_keyword_data->get_item( $postid );
	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('input.a3_ps_exclude_item').change(function() {
					if( jQuery(this).is(":checked") ) {
						jQuery('.a3_ps_focus_keyword_container').slideUp();
					} else {
						jQuery('.a3_ps_focus_keyword_container').slideDown();
					}
				});
			});
		</script>
		<div>
			<label>
				<input type="checkbox" <?php checked( true, $is_excluded, true ); ?> value="1" name="ps_exclude_item" class="a3_ps_exclude_item" />
				<?php echo __( 'Hide from Predictive Search results', 'woops' ); ?>
			</label>
		</div>
		<div class="a3_ps_focus_keyword_container">
	    	<table class="form-table" cellspacing="0">
	        	<tr valign="top">
					<th scope="rpw" class="titledesc"><label for="_predictive_search_focuskw"><?php _e('Focus Keywords', 'woops'); ?></label></th>
					<td class="forminp">
						<div class="wide_div">
							<input type="text" value="<?php esc_attr_e( $ps_focuskw );?>" id="_predictive_search_focuskw" name="_predictive_search_focuskw" style="width:98%;" />
						</div>
						<span class="description"><?php echo __( 'Enter keywords by "," separating values. Example: iPhone, ios', 'woops' ); ?></span>
					</td>
				</tr>
	        </table>
        </div>
        <?php
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'a3_ps_metabox_action', 'a3_ps_metabox_nonce_field' );
		?>
		<div style="clear: both;"></div>
	<?php

	}

	public static function save_custombox( $post_id = 0 ) {
		if ( $post_id < 1 ) {
			global $post;
			$post_id = $post->ID;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['a3_ps_metabox_nonce_field'] ) || ! check_admin_referer( 'a3_ps_metabox_action', 'a3_ps_metabox_nonce_field' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$post_type = get_post_type( $post_id );

		if ( ! in_array( $post_type, array( 'product', 'post', 'page' ) ) )
			return $post_id;

		$post_status = get_post_status( $post_id );
		if ( $post_status == 'inherit' )
			return $post_id;

		if ( ! isset( $_REQUEST['_predictive_search_focuskw'] ) )
			return $post_id;

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
?>