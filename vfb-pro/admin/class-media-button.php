<?php
/**
 * Class that handles the Media Button display
 *
 * @since 3.0
 */
class VFB_Pro_Media_Button {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'media_buttons', array( $this, 'add_button' ), 999 );
		add_action( 'wp_ajax_vfbp-media-button', array( $this, 'display' ) );
	}

	/**
	 * Add button above visual editor
	 *
	 * @access public
	 * @return void
	 */
	public function add_button() {
		// Check permission before display
		if ( !current_user_can( 'vfb_view_entries' ) )
			return;

		$button_url = add_query_arg(
			array(
				'page'   => 'vfb-pro',
				'action' => 'vfbp-media-button',
				'width'  => 600,
				'height' => 550,
			),
			wp_nonce_url( admin_url( 'admin-ajax.php' ), 'vfbp_media_button' )
		);
	?>
		<a href="<?php echo esc_url( $button_url ); ?>" class="button add_media thickbox" title="<?php _e( 'Add VFB Pro form', 'vfb-pro' ); ?>">
			<span class="dashicons dashicons-feedback" style="color:#888; display: inline-block; width: 18px; height: 18px; vertical-align: text-top; margin: 0 4px 0 0;"></span>
			<?php _e( 'VFB Pro', 'vfb-pro' ); ?>
		</a>
	<?php
	}

	/**
	 * Displays the form after add_button is clicked
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		global $wpdb;

		check_admin_referer( 'vfbp_media_button' );

		$vfbdb = new VFB_Pro_Data();
		$forms = $vfbdb->get_all_forms();

	?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$( '#vfbp-add-form' ).submit(function(e){
			        e.preventDefault();

			        window.send_to_editor( '[vfb id=' + $( '#vfbp-forms-list' ).val() + ']' );

			        window.tb_remove();
			    });
			});
	    </script>
		<div>
			<form id="vfbp-add-form" class="media-upload-form type-form validate">
				<h3><?php _e( 'Insert VFB Pro form', 'vfb-pro' ); ?></h3>
				<p><?php _e( 'Select a form below to insert into any Post or Page.', 'vfb-pro' ); ?></p>
				<select id="vfbp-forms-list" name="vfbp-forms">
					<?php foreach( $forms as $form ) : ?>
						<option value="<?php echo $form['id']; ?>">
							<?php echo $form['id']; ?> - <?php echo $form['title']; ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php
					submit_button(
						__( 'Add Form', 'vfb-pro' ),
						'primary',
						'' // leave blank so "name" attribute will not be added
					);
				?>
			</form>
		</div>
	<?php
		die(1);
	}
}