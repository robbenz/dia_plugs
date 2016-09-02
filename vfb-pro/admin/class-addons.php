<?php
/**
 * Class that controls the Add-ons view
 *
 * @since 3.0
 */
class VFB_Pro_Edit_Addons {

	/**
	 * The form ID
	 *
	 * @var mixed
	 * @access private
	 */
	private $id;

	/**
	 * Assign form ID when class is loaded
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_forms' ) )
			return;

		$vfbdb = new VFB_Pro_Data();
		$data  = $vfbdb->get_addon_settings( $this->id );
	?>
	<form method="post" id="vfbp-addon-settings" action="">
		<input name="_vfbp_action" type="hidden" value="save-addon-settings" />
		<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
		<?php
			wp_nonce_field( 'vfbp_addon_settings' );
		?>

		<?php if ( is_plugin_active( 'vfbp-create-user/vfbp-create-user.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Create User Settings', 'vfb-pro' ); ?></h3>

					<?php
						$user = new VFB_Pro_Addon_Create_User_Admin_Settings();
						$user->settings( $data, $this->id );
					?>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php if ( is_plugin_active( 'vfbp-create-post/vfbp-create-post.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Create Post Settings', 'vfb-pro' ); ?></h3>

					<?php
						$post = new VFB_Pro_Addon_Create_Post_Admin_Settings();
						$post->settings( $data );
					?>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php if ( is_plugin_active( 'vfbp-display-entries/vfbp-display-entries.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Display Entries Settings', 'vfb-pro' ); ?></h3>
					<p><?php printf( __( 'Use the shortcode <code>[vfbp-display-entries id="%d"]</code> to display your entries on a WordPress page.', 'vfb-pro' ), $this->id ); ?></p>

					<?php
						$display_entries = new VFB_Pro_Addon_Display_Entries_Admin_Settings();
						$display_entries->settings( $data, $this->id );
					?>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php if ( is_plugin_active( 'vfbp-form-designer/vfbp-form-designer.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Form Designer Settings', 'vfb-pro' ); ?></h3>

					<?php
						$designer = new VFB_Pro_Addon_Form_Designer_Admin_Settings();
						$designer->settings( $data, $this->id );
					?>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php if ( is_plugin_active( 'vfbp-notifications/vfbp-notifications.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Notifications Settings', 'vfb-pro' ); ?></h3>
					<table class="form-table">
						<?php
							$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();

							// Mobile
							$notifications->mobile_settings( $data );

							// MailChimp
							$notifications->mailchimp_initial_settings( $data, $this->id );

							// Campaign Monitor
							$notifications->campaign_monitor_initial_settings( $data, $this->id );

							// Highrise
							$notifications->highrise_initial_settings( $data, $this->id );

							// Highrise
							$notifications->freshbooks_initial_settings( $data, $this->id );
						?>
					</table>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php if ( is_plugin_active( 'vfbp-payments/vfbp-payments.php' ) ) : ?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Payments Settings', 'vfb-pro' ); ?></h3>

					<?php
						$payments = new VFB_Pro_Addon_Payments_Admin_Settings();
						$payments->settings( $data, $this->id );
					?>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->
		<?php endif; ?>

		<?php
			submit_button(
				__( 'Save Changes', 'vfb-pro' ),
				'primary',
				'' // leave blank so "name" attribute will not be added
			);
		?>
	</form>
	<?php
	}
}