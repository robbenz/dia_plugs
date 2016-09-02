<?php
/**
 * Class that controls the Email Settings tab
 *
 * @since 3.0
 */
class VFB_Pro_Forms_Edit_Email {

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
		$data  = $vfbdb->get_email_settings( $this->id );
		$vfb_settings  = $vfbdb->get_vfb_settings();

		$from_name        = isset( $data['from-name'] ) ? $data['from-name'] : '';
		$reply_to         = isset( $data['reply-to'] ) ? $data['reply-to'] : '';
		$reply_to_user    = isset( $data['reply-to-user-email'] ) ? $data['reply-to-user-email'] : '';
		$subject          = isset( $data['subject'] ) ? $data['subject'] : '';
		$email_to         = isset( $data['email-to'] ) ? $data['email-to'] : '';
		$from_email       = isset( $data['from-email'] ) ? $data['from-email'] : '';
		$from_email_user  = isset( $data['from-email-user'] ) ? $data['from-email-user'] : '';
		$cc               = isset( $data['cc'] ) ? $data['cc'] : '';
		$bcc              = isset( $data['bcc'] ) ? $data['bcc'] : '';
		$send_attachments = isset( $data['send-attachments'] ) ? $data['send-attachments'] : '';

		$notify_name       = isset( $data['notify-name'] ) ? $data['notify-name'] : '';
		$notify_email      = isset( $data['notify-email'] ) ? $data['notify-email'] : '';
		$notify_subject    = isset( $data['notify-subject'] ) ? $data['notify-subject'] : '';
		$notify_email_to   = isset( $data['notify-email-to'] ) ? $data['notify-email-to'] : '';
		$notify_message    = isset( $data['notify-message'] ) ? $data['notify-message'] : '';
		$notify_entry_copy = isset( $data['notify-entry-copy'] ) ? $data['notify-entry-copy'] : '';

		$smtp_host         = isset( $vfb_settings['smtp-host'] ) ? $vfb_settings['smtp-host'] : '';
		$smtp_port         = isset( $vfb_settings['smtp-port'] ) ? $vfb_settings['smtp-port'] : '';

		$readonly = $disabled = '';
		if ( empty( $smtp_host ) && empty( $smtp_port ) ) {
			// Get the site domain and get rid of www.
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}

			$from_email = 'no-reply@' . $sitename;

			// Can't change the From Email b/c SMTP isn't setup
			$readonly = ' readonly="readonly"';

			// Can't change the From Email User dropdown b/c SMTP isn't setup
			$disabled = ' disabled="disabled"';
		}

		// Get email fields to be used for $notify_email_to
		$email_fields = $vfbdb->get_fields( $this->id, "AND field_type = 'email' ORDER BY field_order ASC" );
	?>
	<form method="post" id="vfbp-email-settings" action="" novalidate>
		<input name="_vfbp_action" type="hidden" value="save-email-settings" />
		<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
		<?php
			wp_nonce_field( 'vfbp_email_settings' );
		?>

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<h3><?php _e( 'Notification', 'vfb-pro' ); ?></h3>
				<p><?php _e( 'Receive an email of the submitted data when someone completes the form.', 'vfb-pro' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="from-name"><?php _e( 'Your Name' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" value="<?php esc_html_e( $from_name ); ?>" placeholder="" class="regular-text required" id="from-name" name="settings[from-name]" />
								<p class="description"><?php _e( 'This option sets the "From" display name of the email that is sent.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="reply-to"><?php _e( 'Reply-To Email' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $reply_to ); ?>" placeholder="" class="regular-text required" id="reply-to" name="settings[reply-to]" maxlength="255" />
								<?php _e( 'or', 'vfb-pro' ); ?>
								<select id="reply-to-user" name="settings[reply-to-user-email]">
									<option value=""<?php selected( '', $reply_to_user ); ?>></option>
									<?php
										if ( is_array( $email_fields ) && !empty( $email_fields ) ) {
											foreach ( $email_fields as $email ) {
												$label    = isset( $email['data']['label'] ) ? $email['data']['label'] : '';
												$field_id = $email['id'];

												printf( '<option value="%1$d"%3$s>%1$d - %2$s</option>', $field_id, $label, selected( $field_id, $reply_to_user, false ) );
											}
										}
										else {
											printf( '<option>(%s)</option>', __( 'No Email Fields Found', 'vfb-pro' ) );
										}
									?>
								</select>
								<p class="description"><?php _e( 'Replies to the submission email will go here.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="subject"><?php _e( 'Email Subject' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" value="<?php esc_html_e( $subject ); ?>" placeholder="" class="regular-text" id="subject" name="settings[subject]" />
								<p class="description"><?php _e( 'This sets the subject of the email that is sent.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="email-to"><?php _e( 'Email To' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $email_to ); ?>" placeholder="" class="regular-text" id="email-to" name="settings[email-to]" />
								<p class="description"><?php _e( 'Who to send the submitted data to.' , 'vfb-pro'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<h4><?php _e( 'Advanced Settings', 'vfb-pro' ); ?></h4>
				<p><?php _e( 'These settings allow you to modify or include additional email headers.', 'vfb-pro' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="from-email"><?php _e( 'From Email' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $from_email ); ?>" placeholder=""<?php echo $readonly; ?> class="regular-text required" id="from-email" name="settings[from-email]" maxlength="255" />
								<?php _e( 'or', 'vfb-pro' ); ?>
								<select id="from-email-user" name="settings[from-email-user]"<?php echo $disabled; ?>>
									<option value=""<?php selected( '', $from_email_user ); ?>></option>
									<?php
										if ( is_array( $email_fields ) && !empty( $email_fields ) ) {
											foreach ( $email_fields as $email ) {
												$label    = isset( $email['data']['label'] ) ? $email['data']['label'] : '';
												$field_id = $email['id'];

												printf( '<option value="%1$d"%3$s>%1$d - %2$s</option>', $field_id, $label, selected( $field_id, $from_email_user, false ) );
											}
										}
										else {
											printf( '<option>(%s)</option>', __( 'No Email Fields Found', 'vfb-pro' ) );
										}
									?>
								</select>
								<p class="description"><?php printf( __( 'This option sets the "From" email and can only be customized if <a href="%s">SMTP settings</a> have been configured.', 'vfb-pro' ), esc_url( admin_url( 'admin.php?page=vfbp-settings' ) ) ); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="cc"><?php _e( 'Cc' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $cc ); ?>" placeholder="" class="regular-text" id="cc" name="settings[cc]" maxlength="255" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="bcc"><?php _e( 'Bcc' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $bcc ); ?>" placeholder="" class="regular-text" id="bcc" name="settings[bcc]" maxlength="255" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="send-attachments"><?php esc_html_e( 'Include Attachments', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[send-attachments]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[send-attachments]" id="send-attachments" value="1"<?php checked( $send_attachments, 1 ); ?> /> <?php _e( "If File Uploads are present, include the uploaded files with the email.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
								<p class="description">
									<?php _e( 'NOTE: Many servers restrict email attachment size to 25MB or even smaller.', 'vfb-pro' ); ?><br/>
									<?php _e( 'If you experience trouble sending or receiving emails with this setting on, reduce the File Upload max file size.', 'vfb-pro' ); ?>
								</p>
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
					</tbody>
				</table>
			</div> <!-- .vfb-edit-section-inside -->
		</div> <!-- .vfb-edit-section -->

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<h3><?php _e( 'Autoresponder', 'vfb-pro' ); ?></h3>
				<p><?php _e( 'Send an email to the person who completed the form.', 'vfb-pro' ); ?></p>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="notify-name"><?php _e( 'Your Name' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" value="<?php esc_html_e( $notify_name ); ?>" placeholder="" class="regular-text required" id="notify-name" name="settings[notify-name]" />
								<p class="description"><?php _e( 'This option sets the "From" display name of the email that is sent.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="notify-email"><?php _e( 'Reply-To Email' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="email" value="<?php esc_html_e( $notify_email ); ?>" placeholder="" class="regular-text required" id="notify-email" name="settings[notify-email]" maxlength="255" />
								<p class="description"><?php _e( 'Replies to the submission email will go here.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="notify-subject"><?php _e( 'Email Subject' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" value="<?php esc_html_e( $notify_subject ); ?>" placeholder="" class="regular-text" id="notify-subject" name="settings[notify-subject]" />
								<p class="description"><?php _e( 'This sets the subject of the email that is sent.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="notify-email-to"><?php _e( 'Email To' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<select id="notify-email-to" name="settings[notify-email-to]">
									<option value=""<?php selected( '', $notify_email_to ); ?>></option>
									<?php
										if ( is_array( $email_fields ) && !empty( $email_fields ) ) {
											foreach ( $email_fields as $email ) {
												$label    = isset( $email['data']['label'] ) ? $email['data']['label'] : '';
												$field_id = $email['id'];

												printf( '<option value="%1$d"%3$s>%1$d - %2$s</option>', $field_id, $label, selected( $field_id, $notify_email_to, false ) );
											}
										}
										else {
											printf( '<option>(%s)</option>', __( 'No Email Fields Found', 'vfb-pro' ) );
										}
									?>
								</select>
								<p class="description"><?php _e( 'Who to send the submitted data to.' , 'vfb-pro'); ?></p>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="notify-message"><?php _e( 'Message' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<textarea id="notify-message" name="settings[notify-message]" class="large-text" rows="10"><?php echo $notify_message; ?></textarea>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="notify-entry-copy"><?php esc_html_e( 'Append Entry', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[notify-entry-copy]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[notify-entry-copy]" id="notify-entry-copy" value="1"<?php checked( $notify_entry_copy, 1 ); ?> /> <?php _e( "Include a Copy of the User's Entry", 'vfb-pro' ); ?>
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