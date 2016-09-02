<?php
/**
 * Class that controls the Add New Form view
 *
 * @since 3.0
 */
class VFB_Pro_Forms_Edit_Confirmation {

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
		$data  = $vfbdb->get_confirmation_settings( $this->id );

		$type     = isset( $data['confirmation-type'] ) ? $data['confirmation-type'] : '';
		$message  = isset( $data['text-message'] ) ? $data['text-message'] : '';
		$prepend  = isset( $data['text-prepend'] ) ? $data['text-prepend'] : '';
		$page     = isset( $data['wp-page'] ) ? $data['wp-page'] : '';
		$redirect = isset( $data['redirect'] ) ? $data['redirect'] : '';
	?>
	<form method="post" id="vfbp-confirmation-settings" action="">
		<input name="_vfbp_action" type="hidden" value="save-confirmation-settings" />
		<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
		<?php
			wp_nonce_field( 'vfbp_confirmation_settings' );
		?>

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<p><?php _e( 'After someone submits a form, you can control what is displayed.', 'vfb-pro' ); ?></p>
				<p><?php _e( 'By default, it is a message but you can send them to another WordPress Page or a custom URL.', 'vfb-pro' ); ?></p>

				<table class="form-table">
					<tbody id="vfb-confirmation-main">
						<tr valign="top">
							<th scope="row">
								<label for="type"><?php _e( 'Type' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<select name="settings[confirmation-type]" id="type">
									<option value="text"<?php selected( 'text', $type ); ?>><?php _e( 'Text', 'vfb-pro' ); ?></option>
									<option value="wp-page"<?php selected( 'wp-page', $type ); ?>><?php _e( 'WordPress Page', 'vfb-pro' ); ?></option>
									<option value="redirect"<?php selected( 'redirect', $type ); ?>><?php _e( 'Redirect', 'vfb-pro' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody> <!-- #vfb-confirmation-main -->

					<tbody id="vfb-confirmation-text" class="vfb-confirmation-type<?php echo 'text' == $type || empty( $type ) ? ' active' : ''; ?>">
						<tr valign="top">
							<th scope="row">
								<label for="text_message"><?php _e( 'Success Text' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<?php wp_editor( $message, 'text_message', array( 'textarea_name' => 'settings[text-message]' ) ); ?>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="prepend-message"><?php esc_html_e( 'Show Form After Submission', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[text-prepend]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[text-prepend]" id="prepend-message" value="1"<?php checked( $prepend, 1 ); ?> /> <?php _e( "Display the Success Text above a blank form.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="template-tags"><?php esc_html_e( 'Template Tags', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<p><?php _e( 'Templating is a way to dynamically replace specially formatted variables with data submitted from the form.', 'vfb-pro' ); ?></p>
								<a href="#TB_inline?width=1000&height=600&inlineId=vfb-template-tags" class="button thickbox">
									<?php _e( ' View Template Options', 'vfb-pro' ); ?>
								</a>
								<div id="vfb-template-tags" style="display: none;">
									<?php $this->template_tags( $this->id ); ?>
								</div> <!-- #vfb-template-tags -->
							</td>
						</tr>
					</tbody> <!-- #vfb-confirmation-text -->

					<tbody id="vfb-confirmation-wp-page" class="vfb-confirmation-type<?php echo 'wp-page' == $type ? ' active' : ''; ?>">
						<tr valign="top">
							<th scope="row">
								<label for="wp-page"><?php _e( 'WordPress Page' , 'vfb-pro'); ?></label>
							</th>
							<td>
							<?php
		                        // Display all Pages
		                        wp_dropdown_pages( array(
		                            'name' 				=> 'settings[wp-page]',
		                            'id' 				=> 'wp-page',
		                            'class' 			=> 'widefat',
		                            'show_option_none' 	=> __( 'Select a Page' , 'vfb-pro'),
		                            'selected' 			=> $page
		                        ));
		                    ?>
							</td>
						</tr>
					</tbody> <!-- #vfb-confirmation-page -->

					<tbody id="vfb-confirmation-redirect" class="vfb-confirmation-type<?php echo 'redirect' == $type ? ' active' : ''; ?>">
						<tr valign="top">
							<th scope="row">
								<label for="redirect"><?php _e( 'Redirect URL' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="url" name="settings[redirect]" id="redirect" class="regular-text" placeholder="http://" value="<?php esc_html_e( $redirect ); ?>" maxlength="255" />
							</td>
						</tr>
					</tbody> <!-- #vfb-confirmation-redirect -->
				</table>
			</div> <!-- .vfb-edit-section-inside -->
		</div> <!-- .vfb-edit-section -->

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

	/**
	 * template_tags function.
	 *
	 * @access private
	 * @param mixed $form_id
	 * @return void
	 */
	private function template_tags( $form_id ) {
		$tags = new VFB_Pro_Template_Tags( $form_id );
		$tags->display();
	}
}