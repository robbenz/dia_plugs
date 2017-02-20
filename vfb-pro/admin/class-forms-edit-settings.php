<?php
/**
 * Class that controls the Add New Form view
 *
 * @since 3.0
 */
class VFB_Pro_Forms_Edit_Settings {

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
		$data  = $vfbdb->get_form_settings( $this->id );

		$title              = isset( $data['title'] ) ? $data['title'] : '';
		$status             = isset( $data['status'] ) ? $data['status'] : '';
		$label_alignment    = isset( $data['data']['label-alignment']    ) ? $data['data']['label-alignment']    : '';
		$save_state         = isset( $data['data']['save-state']         ) ? $data['data']['save-state']         : '';
		$csrf_protection    = isset( $data['data']['csrf-protection']    ) ? $data['data']['csrf-protection']    : '';
		$on_submit          = isset( $data['data']['on-submit']          ) ? $data['data']['on-submit']          : '';
		$limit              = isset( $data['data']['limit']              ) ? $data['data']['limit']              : '';
		$limit_message      = isset( $data['data']['limit-message']      ) ? $data['data']['limit-message']      : '';
		$expiration         = isset( $data['data']['expiration']         ) ? $data['data']['expiration']         : '';
		$expiration_message = isset( $data['data']['expiration-message'] ) ? $data['data']['expiration-message'] : '';
		$page_title_display = isset( $data['data']['page-title-display'] ) ? $data['data']['page-title-display'] : '';
		$page_title_click   = isset( $data['data']['page-title-click']   ) ? $data['data']['page-title-click']   : '';
		$page_num_display   = isset( $data['data']['page-num-display']   ) ? $data['data']['page-num-display']   : '';
	?>
	<form method="post" id="vfbp-form-settings" action="">
		<input name="_vfbp_action" type="hidden" value="save-form-settings" />
		<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
		<?php
			wp_nonce_field( 'vfbp_form_settings' );
		?>

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<h3><?php _e( 'General Form Settings', 'vfb-pro' ); ?></h3>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="title"><?php _e( 'Form Name' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" autofocus="autofocus" class="regular-text required" id="title" name="title" value="<?php esc_html_e( $title ); ?>" maxlength="255" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="status"><?php _e( 'Form Status' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<select name="status" id="status">
									<option value="publish"<?php selected( 'publish', $status ); ?>><?php _e( 'Published', 'vfb-pro' ); ?></option>
									<option value="draft"<?php selected( 'draft', $status ); ?>><?php _e( 'Draft', 'vfb-pro' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Change the status from Published to Draft to prevent form output without removing the shortcode.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="label-alignment"><?php _e( 'Label Alignment' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<select name="label-alignment" id="label-alignment">
									<option value=""<?php selected( '', $label_alignment ); ?>><?php _e( 'Vertical', 'vfb-pro' ); ?></option>
									<option value="horizontal"<?php selected( 'horizontal', $label_alignment ); ?>><?php _e( 'Horizontal', 'vfb-pro' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Align labels either on top of the inputs or off to the side.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="save-state"><?php esc_html_e( 'Save Form State', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="save-state" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="save-state" id="save-state" value="1"<?php checked( $save_state, 1 ); ?> /> <?php _e( "Saves data entered inside form fields locally and restores it in case the page is refreshed accidentally.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="csrf-protection"><?php esc_html_e( 'CSRF Protection', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="csrf-protection" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="csrf-protection" id="csrf-protection" value="1"<?php checked( $csrf_protection, 1 ); ?> /> <?php _e( "Protects your forms from CSRF (Cross-Site Request Forgery) attacks.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
								<p class="description"><?php _e( 'Your server is required have PHP Sessions enabled and properly configured for this setting to function correctly.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="on-submit"><?php _e( 'onSubmit attribute' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" class="regular-text" id="on-submit" name="on-submit" value="<?php esc_html_e( $on_submit ); ?>" />
								<p class="description"><?php _e( 'The onSubmit form attribute will allow you to run a script action, such as recording a hit with Google Analytics, when the form is submitted.' , 'vfb-pro'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</div> <!-- .vfb-edit-section-inside -->
		</div> <!-- .vfb-edit-section -->

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<h3><?php _e( 'Submission Limits', 'vfb-pro' ); ?></h3>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="limit"><?php _e( 'Limit Entries' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="number" class="regular-text" id="limit" name="limit" value="<?php esc_html_e( $limit ); ?>" />
								<p class="description"><?php _e( 'Set the total number of entries you want your form to accept. Leave this blank if you want unlimited entries.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="status"><?php _e( 'Limit Message' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<?php wp_editor( $limit_message, 'vfbLimitMessage', array( 'textarea_name' => 'limit-message' ) ); ?>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="expiration"><?php _e( 'Expiration Date' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" class="regular-text" id="expiration" name="expiration" value="<?php esc_html_e( $expiration ); ?>" />
								<p class="description"><?php _e( 'Set a date and time to deactivate your form. Leave this blank if you want your form to always be active.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="expiration-message"><?php _e( 'Expiration Message' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<?php wp_editor( $expiration_message, 'vfbExpirationMessage', array( 'textarea_name' => 'expiration-message' ) ); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div> <!-- .vfb-edit-section-inside -->
		</div> <!-- .vfb-edit-section -->

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<h3><?php _e( 'Page Break Options', 'vfb-pro' ); ?></h3>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="page-title-display"><?php esc_html_e( 'Hide Page Titles', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<!-- Page Break checkboxes use true/false strings due to type conversion problems in JS. Do not use this anywhere else -->
										<input type="hidden" name="page-title-display" value="false" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="page-title-display" id="page-display-title" value="true"<?php checked( $page_title_display, 'true' ); ?> /> <?php _e( "Hides the Page Break titles.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="page-title-click"><?php esc_html_e( 'Prevent Page Title Clicks', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<!-- Page Break checkboxes use true/false strings due to type conversion problems in JS. Do not use this anywhere else -->
										<input type="hidden" name="page-title-click" value="true" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="page-title-click" id="page-title-click" value="false"<?php checked( $page_title_click, 'false' ); ?> /> <?php _e( "Prevents user from clicking on Page Break titles to advance the page.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="page-num-display"><?php esc_html_e( 'Hide Page Numbers', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<!-- Page Break checkboxes use true/false strings due to type conversion problems in JS. Do not use this anywhere else -->
										<input type="hidden" name="page-num-display" value="true" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="page-num-display" id="page-num-display" value="false"<?php checked( $page_num_display, 'false' ); ?> /> <?php _e( "Hides the Page Break numbers.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
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
}