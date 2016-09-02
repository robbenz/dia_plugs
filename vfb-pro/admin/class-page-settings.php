<?php

/**
 * Class that controls the Settings page view
 *
 * @since      3.0
 */
class VFB_Pro_Page_Settings {
	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Get the current selected tab
		$current_tab = $this->get_current_tab();

		$settings_url    = esc_url( add_query_arg( array( 'vfb-tab' => 'settings' ), admin_url( 'admin.php?page=vfbp-settings' ) ) );
		$diagnostics_url = esc_url( add_query_arg( array( 'vfb-tab' => 'diagnostics' ), admin_url( 'admin.php?page=vfbp-settings' ) ) );
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo $settings_url; ?>" class="nav-tab<?php echo 'settings' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'General Settings', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $diagnostics_url; ?>" class="nav-tab<?php echo 'diagnostics' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Diagnostics', 'vfb-pro' ); ?>
			</a>
		</h2>

		<?php
			// Display current tab content
			switch( $current_tab ) :
				case 'settings' :
					$this->settings();
					break;

				case 'diagnostics' :
					$this->diagnostics();
					break;
			endswitch;
		?>

	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Display the General Settings tab.
	 *
	 * @access public
	 * @return void
	 */
	public function settings() {

		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_settings' ) )
			return;

		$vfbdb = new VFB_Pro_Data();
		$data  = $vfbdb->get_vfb_settings();

		$license           = isset( $data['license-key'] ) ? $data['license-key'] : '';
		$license_email     = isset( $data['license-email'] ) ? $data['license-email'] : '';
		$recaptcha_public  = isset( $data['recaptcha-public-key'] ) ? $data['recaptcha-public-key'] : '';
		$recaptcha_private = isset( $data['recaptcha-private-key'] ) ? $data['recaptcha-private-key'] : '';
		$smtp_host         = isset( $data['smtp-host'] ) ? $data['smtp-host'] : '';
		$smtp_port         = isset( $data['smtp-port'] ) ? $data['smtp-port'] : '';
		$smtp_encryption   = isset( $data['smtp-encryption'] ) ? $data['smtp-encryption'] : '';
		$smtp_auth         = isset( $data['smtp-auth'] ) ? $data['smtp-auth'] : '';
		$smtp_username     = isset( $data['smtp-username'] ) ? $data['smtp-username'] : '';
		$smtp_password     = isset( $data['smtp-password'] ) ? $data['smtp-password'] : '';

		$license_status  = get_option( 'vfbp_license_status' );
		$license_message = get_option( 'vfbp_license_message' );

		// Default License message
		if ( empty( $license_message ) )
			$license_message = sprintf( '<span class="status-0">%s</span>', __( 'UNVERIFIED', 'vfb-pro' ) );

		// Check status and display license message
		if ( $license_status )
			$license_message = sprintf( '<span class="status-1">%s</span>', $license_message );
		else
			$license_message = sprintf( '<span class="status-0">%s</span>', $license_message );

		$enable_validation_msgs      = isset( $data['custom-validation-msgs'] ) ? $data['custom-validation-msgs'] : '';

		$validation_msg = array();
		$validation_msg['default']   = isset( $data['validation-msg-default']   ) ? $data['validation-msg-default']   : __( 'This value seems to be invalid.', 'vfb-pro' );
		$validation_msg['email']     = isset( $data['validation-msg-email']     ) ? $data['validation-msg-email']     : __( 'This value should be a valid email.', 'vfb-pro' );
		$validation_msg['url']       = isset( $data['validation-msg-url']       ) ? $data['validation-msg-url']       : __( 'This value should be a valid url.', 'vfb-pro' );
		$validation_msg['number']    = isset( $data['validation-msg-number']    ) ? $data['validation-msg-number']    : __( 'This value should be a valid number.', 'vfb-pro' );
		$validation_msg['integer']   = isset( $data['validation-msg-integer']   ) ? $data['validation-msg-integer']   : __( 'This value should be a valid integer.', 'vfb-pro' );
		$validation_msg['digits']    = isset( $data['validation-msg-digits']    ) ? $data['validation-msg-digits']    : __( 'This value should be digits.', 'vfb-pro' );
		$validation_msg['alphanum']  = isset( $data['validation-msg-alphanum']  ) ? $data['validation-msg-alphanum']  : __( 'This value should be alphanumeric.', 'vfb-pro' );
		$validation_msg['notblank']  = isset( $data['validation-msg-notblank']  ) ? $data['validation-msg-notblank']  : __( 'This value should not be blank.', 'vfb-pro' );
		$validation_msg['required']  = isset( $data['validation-msg-required']  ) ? $data['validation-msg-required']  : __( 'This value is required', 'vfb-pro' );
		$validation_msg['pattern']   = isset( $data['validation-msg-pattern']   ) ? $data['validation-msg-pattern']   : __( 'This value seems to be invalid.', 'vfb-pro' );
		$validation_msg['min']       = isset( $data['validation-msg-min']       ) ? $data['validation-msg-min']       : __( 'This value should be greater than or equal to %s.', 'vfb-pro' );
		$validation_msg['max']       = isset( $data['validation-msg-max']       ) ? $data['validation-msg-max']       : __( 'This value should be lower than or equal to %s.', 'vfb-pro' );
		$validation_msg['range']     = isset( $data['validation-msg-range']     ) ? $data['validation-msg-range']     : __( 'This value should be between %s and %s.', 'vfb-pro' );
		$validation_msg['minlength'] = isset( $data['validation-msg-minlength'] ) ? $data['validation-msg-minlength'] : __( 'This value is too short. It should have %s characters or more.', 'vfb-pro' );
		$validation_msg['maxlength'] = isset( $data['validation-msg-maxlength'] ) ? $data['validation-msg-maxlength'] : __( 'This value is too long. It should have %s characters or fewer.', 'vfb-pro' );
		$validation_msg['length']    = isset( $data['validation-msg-length']    ) ? $data['validation-msg-length']    : __( 'This value length is invalid. It should be between %s and %s characters long.', 'vfb-pro' );
		$validation_msg['mincheck']  = isset( $data['validation-msg-mincheck']  ) ? $data['validation-msg-mincheck']  : __( 'You must select at least %s choices.', 'vfb-pro' );
		$validation_msg['maxcheck']  = isset( $data['validation-msg-maxcheck']  ) ? $data['validation-msg-maxcheck']  : __( 'You must select %s choices or fewer.', 'vfb-pro' );
		$validation_msg['check']     = isset( $data['validation-msg-check']     ) ? $data['validation-msg-check']     : __( 'You must select between %s and %s choices.', 'vfb-pro' );
		$validation_msg['equalto']   = isset( $data['validation-msg-equalto']   ) ? $data['validation-msg-equalto']   : __( 'This value should be the same.', 'vfb-pro' );
		$validation_msg['minwords']  = isset( $data['validation-msg-minwords']  ) ? $data['validation-msg-minwords']  : __( 'This value is too short. It should have %s words or more.', 'vfb-pro' );
		$validation_msg['maxwords']  = isset( $data['validation-msg-maxwords']  ) ? $data['validation-msg-maxwords']  : __( 'This value is too long. It should have %s words or fewer.', 'vfb-pro' );
		$validation_msg['words']     = isset( $data['validation-msg-words']     ) ? $data['validation-msg-words']     : __( 'This value length is invalid. It should be between %s and %s words long.', 'vfb-pro' );
		$validation_msg['gt']        = isset( $data['validation-msg-gt']        ) ? $data['validation-msg-gt']        : __( 'This value should be greater.', 'vfb-pro' );
		$validation_msg['gte']       = isset( $data['validation-msg-gte']       ) ? $data['validation-msg-gte']       : __( 'This value should be greater or equal.', 'vfb-pro' );
		$validation_msg['lt']        = isset( $data['validation-msg-lt']        ) ? $data['validation-msg-lt']        : __( 'This value should be less.', 'vfb-pro' );
		$validation_msg['lte']       = isset( $data['validation-msg-lte']       ) ? $data['validation-msg-lte']       : __( 'This value should be less or equal.', 'vfb-pro' );
	?>
	<div class="wrap">
		<form method="post" id="vfbp-settings" action="">
			<input name="_vfbp_action" type="hidden" value="save-settings" />
			<?php
				wp_nonce_field( 'vfbp_settings' );
			?>
			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="license-key"><?php _e( 'License Key' , 'vfb-pro'); ?></label>
								</th>
								<td>
									<input type="text" class="regular-text" id="license-key" name="settings[license-key]" value="<?php esc_html_e( $license ); ?>" />
									<p class="description"><?php _e( 'Your license key is your order number. You must validate your order before VFB Pro will function.', 'vfb-pro'); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="license-email"><?php _e( 'License Email' , 'vfb-pro'); ?></label>
								</th>
								<td>
									<input type="text" class="regular-text" id="license-email" name="settings[license-email]" value="<?php esc_html_e( $license_email ); ?>" />
									<p class="description"><?php _e( 'This is the email where you received your VFB Pro downloads. Both license key and email are required.', 'vfb-pro'); ?></p>
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<?php _e( 'License Status' , 'vfb-pro'); ?>
								</th>
								<td>
									<p>
										<span id="vfb-verified-text">
											<?php echo $license_message; ?>
										</span>
										<a href="#" id="vfb-verify-license" class="button">
											<?php _e( 'Verify License', 'vfb-pro' ); ?>
											<span class="spinner"></span>
										</a>

										<a href="#" id="vfb-deactivate-license" class="button">
											<?php _e( 'Deactivate', 'vfb-pro' ); ?>
											<span class="spinner"></span>
										</a>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->

			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'reCAPTCHA Settings', 'vfb-pro' ); ?></h3>
					<p><a href="http://www.google.com/recaptcha/"><?php _e( "Sign up for Google's reCAPTCHA", 'vfb-pro' ); ?></a></p>

					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="recaptcha-public-key"><?php esc_html_e( 'Site Key', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<input type="text" name="settings[recaptcha-public-key]" id="recaptcha-public-key" value="<?php esc_html_e( $recaptcha_public ); ?>" class="regular-text" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="recaptcha-private-key"><?php esc_html_e( 'Secret Key', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<input type="text" name="settings[recaptcha-private-key]" id="recaptcha-private-key" value="<?php esc_html_e( $recaptcha_private ); ?>" class="regular-text" />
							</td>
						</tr>
					</table>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->

			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'SMTP Settings', 'vfb-pro' ); ?></h3>
					<p><?php _e( "These settings will reconfigure VFB Pro to send emails through your web host's SMTP email server instead of using wp_mail().", 'vfb-pro' ); ?></p>

					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="smtp-host"><?php esc_html_e( 'SMTP Host', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<input type="text" name="settings[smtp-host]" id="smtp-host" value="<?php esc_html_e( $smtp_host ); ?>" class="regular-text" />
								<label for="smtp-port"><?php esc_html_e( 'Port', 'vfb-pro' ); ?></label>
								<input type="text" name="settings[smtp-port]" id="smtp-port" value="<?php esc_html_e( $smtp_port ); ?>" class="small-text" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="smtp-encryption"><?php esc_html_e( 'Encryption', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="radio" name="settings[smtp-encryption]" id="smtp-encryption" value="none"<?php checked( $smtp_encryption, 'none' ); ?> /> <?php _e( 'None', 'vfb-pro' ); ?>
									</label>
									<br />
									<label>
										<input type="radio" name="settings[smtp-encryption]" id="smtp-encryption" value="ssl"<?php checked( $smtp_encryption, 'ssl' ); ?> /> <?php _e( 'SSL (recommended)', 'vfb-pro' ); ?>
									</label>
									<br />
									<label>
										<input type="radio" name="settings[smtp-encryption]" id="smtp-encryption" value="tls"<?php checked( $smtp_encryption, 'tls' ); ?> /> <?php _e( 'TLS', 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="smtp-auth"><?php esc_html_e( 'Authentication', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="checkbox" name="settings[smtp-auth]" id="smtp-auth" value="1"<?php checked( $smtp_auth, 1 ); ?> /> <?php _e( 'Use SMTP Authentication', 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tbody id="smtp-auth-details" class="<?php echo !empty( $smtp_auth ) ? 'active' : ''; ?>">
							<tr valign="top">
								<th scope="row">
									<label for="smtp-username"><?php esc_html_e( 'Username', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[smtp-username]" id="smtp-username" value="<?php esc_html_e( $smtp_username ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="smtp-password"><?php esc_html_e( 'Password', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[smtp-password]" id="smtp-password" value="<?php esc_html_e( $smtp_password ); ?>" class="regular-text" />
								</td>
							</tr>
						</tbody>
					</table>

					<h4><?php _e( 'Send Test Email', 'vfb-pro' ); ?></h4>
					<p><?php _e( 'Enter a "From" name, "From" email address, and an email address to receive the test.', 'vfb-pro' ); ?></p>
					<p><?php _e( 'Click the Send Test button to test your SMTP settings. A debug log will appear below with the results.', 'vfb-pro' ); ?></p>

					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="smtp-test-name"><?php esc_html_e( 'Test From Name', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<input type="text" name="vfb-smtp-test-from-name" id="smtp-test-name" value="" class="regular-text" placeholder="<?php _e( 'WordPress', 'vfb-pro' ); ?>" />
								<p class="description"><?php _e( 'This option sets the "From" name.', 'vfb-pro' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="smtp-test-from-email"><?php esc_html_e( 'Test From Email', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<?php
									// Get the site domain and get rid of www.
									$sitename = strtolower( $_SERVER['SERVER_NAME'] );
									if ( substr( $sitename, 0, 4 ) == 'www.' ) {
										$sitename = substr( $sitename, 4 );
									}

									$test_from_email = 'wordpress@' . $sitename;
								?>
								<input type="email" name="vfb-smtp-test-from-email" id="smtp-test-from-email" value="" class="regular-text" placeholder="<?php echo $test_from_email; ?>" />
								<p class="description"><?php _e( 'This option sets the "From" email.', 'vfb-pro' ); ?></p>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="smtp-test"><?php esc_html_e( 'Test Email To', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<input type="email" name="vfb-smtp-test-email" id="smtp-test" value="" class="regular-text" />
								<?php
									submit_button(
										__( 'Send Test', 'vfb-pro' ),
										'secondary',
										'vfbp-smtp-test',
										false
									);
								?>
								<p class="description"><?php _e( 'Enter an email address and click the Send Test button to test your SMTP settings. A debug log will appear below with the results.', 'vfb-pro' ); ?></p>
							</td>
						</tr>

						<?php if ( isset( $_POST['vfbp-smtp-test'] ) ) : ?>
						<tr valign="top">
							<th scope="row">
							</th>
							<td>
								<div class="vfb-notices vfb-notice-warning">
								<?php
									add_action( 'phpmailer_init', array( $this, 'smtp_test_init' ) );

									$email = isset( $_POST['vfb-smtp-test-email'] ) ? sanitize_email( $_POST['vfb-smtp-test-email'] ) : '';
									$this->smtp_test( $email );
								?>
								</div> <!-- .vfb-notice-warning -->
							</td>
						</tr>
						<?php endif; ?>
					</table>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->

			<div class="vfb-edit-section">
				<div class="vfb-edit-section-inside">
					<h3><?php _e( 'Validation Message Settings', 'vfb-pro' ); ?></h3>
					<p><?php _e( "Customize the JS validation messages. These messages are displayed when validation fails a certain rule.", 'vfb-pro' ); ?></p>

					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="custom-validation-msgs"><?php esc_html_e( 'Custom Validation Messages', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="checkbox" name="settings[custom-validation-msgs]" id="custom-validation-msgs" value="1"<?php checked( $enable_validation_msgs, 1 ); ?> /> <?php _e( 'Use Custom Validation Messages', 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tbody id="vfb-validation-msgs" class="<?php echo !empty( $enable_validation_msgs ) ? 'active' : ''; ?>">

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-default"><?php esc_html_e( 'Default Message', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-default]" id="validation-msg-default" value="<?php esc_html_e( $validation_msg['default'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-email"><?php esc_html_e( 'Email', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-email]" id="validation-msg-email" value="<?php esc_html_e( $validation_msg['email'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-url"><?php esc_html_e( 'URL', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-url]" id="validation-msg-url" value="<?php esc_html_e( $validation_msg['url'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-number"><?php esc_html_e( 'Number', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-number]" id="validation-msg-number" value="<?php esc_html_e( $validation_msg['number'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-integer"><?php esc_html_e( 'Integer', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-integer]" id="validation-msg-integer" value="<?php esc_html_e( $validation_msg['integer'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-digits"><?php esc_html_e( 'Digits', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-digits]" id="validation-msg-digits" value="<?php esc_html_e( $validation_msg['digits'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-alphanum"><?php esc_html_e( 'Alphanumeric', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-alphanum]" id="validation-msg-alphanum" value="<?php esc_html_e( $validation_msg['alphanum'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-notblank"><?php esc_html_e( 'Not Blank', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-notblank]" id="validation-msg-notblank" value="<?php esc_html_e( $validation_msg['notblank'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-required"><?php esc_html_e( 'Required', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-required]" id="validation-msg-required" value="<?php esc_html_e( $validation_msg['required'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-pattern"><?php esc_html_e( 'Regex Pattern', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-pattern]" id="validation-msg-pattern" value="<?php esc_html_e( $validation_msg['pattern'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-min"><?php esc_html_e( 'Min', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-min]" id="validation-msg-min" value="<?php esc_html_e( $validation_msg['min'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-max"><?php esc_html_e( 'Max', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-max]" id="validation-msg-max" value="<?php esc_html_e( $validation_msg['max'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-range"><?php esc_html_e( 'Range', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-range]" id="validation-msg-range" value="<?php esc_html_e( $validation_msg['range'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-minlength"><?php esc_html_e( 'Min Length', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-minlength]" id="validation-msg-minlength" value="<?php esc_html_e( $validation_msg['minlength'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-maxlength"><?php esc_html_e( 'Max Length', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-maxlength]" id="validation-msg-maxlength" value="<?php esc_html_e( $validation_msg['maxlength'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-length"><?php esc_html_e( 'Length', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-length]" id="validation-msg-length" value="<?php esc_html_e( $validation_msg['length'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-mincheck"><?php esc_html_e( 'Min Check', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-mincheck]" id="validation-msg-mincheck" value="<?php esc_html_e( $validation_msg['mincheck'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-maxcheck"><?php esc_html_e( 'Max Check', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-maxcheck]" id="validation-msg-maxcheck" value="<?php esc_html_e( $validation_msg['maxcheck'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-check"><?php esc_html_e( 'Check', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-check]" id="validation-msg-check" value="<?php esc_html_e( $validation_msg['check'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-equalto"><?php esc_html_e( 'Equal To', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-equalto]" id="validation-msg-equalto" value="<?php esc_html_e( $validation_msg['equalto'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-minwords"><?php esc_html_e( 'Min Words', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-minwords]" id="validation-msg-minwords" value="<?php esc_html_e( $validation_msg['minwords'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-maxwords"><?php esc_html_e( 'Max Words', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-maxwords]" id="validation-msg-maxwords" value="<?php esc_html_e( $validation_msg['maxwords'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-words"><?php esc_html_e( 'Words Range', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-words]" id="validation-msg-words" value="<?php esc_html_e( $validation_msg['words'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-gt"><?php esc_html_e( 'Greater Than', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-gt]" id="validation-msg-gt" value="<?php esc_html_e( $validation_msg['gt'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-gte"><?php esc_html_e( 'Greater Than Equal To', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-gte]" id="validation-msg-gte" value="<?php esc_html_e( $validation_msg['gte'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-lt"><?php esc_html_e( 'Less Than', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-lt]" id="validation-msg-lt" value="<?php esc_html_e( $validation_msg['lt'] ); ?>" class="regular-text" />
								</td>
							</tr>

							<tr valign="top">
								<th scope="row">
									<label for="validation-msg-lte"><?php esc_html_e( 'Less Than Equal To', 'vfb-pro' ); ?></label>
								</th>
								<td>
									<input type="text" name="settings[validation-msg-lte]" id="validation-msg-lte" value="<?php esc_html_e( $validation_msg['lte'] ); ?>" class="regular-text" />
								</td>
							</tr>
						</tbody>
					</table>
				</div> <!-- .vfb-edit-section-inside -->
			</div> <!-- .vfb-edit-section -->

			<?php if ( current_user_can( 'vfb_uninstall_plugin' ) ) : ?>
				<div class="vfb-notices vfb-notice-danger" style="width: 50%;">
					<h3><?php _e( 'Uninstall VFB Pro', 'vfb-pro' ); ?></h3>
					<p><?php _e( "Running this uninstall process will delete all VFB Pro data and deactivate your license for this site. This process cannot be reversed.", 'vfb-pro' ); ?></p>
					<?php
						submit_button(
							__( 'Uninstall', 'vfb-pro' ),
							'delete',
							'vfbp-uninstall',
							false
						);
					?>
				</div> <!-- .vfb-notices -->
			<?php endif; ?>

			<?php
				submit_button(
					__( 'Save Changes', 'vfb-pro' ),
					'primary',
					'' // leave blank so "name" attribute will not be added
				);
			?>
		</form>
	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * This function sets up the phpmailer vars temporarily
	 *
	 * @access public
	 * @param mixed $phpmailer
	 * @return void
	 */
	public function smtp_test_init( $phpmailer ) {
		$smtp_host         = isset( $_POST['settings']['smtp-host']       ) ? $_POST['settings']['smtp-host']       : '';
		$smtp_port         = isset( $_POST['settings']['smtp-port']       ) ? $_POST['settings']['smtp-port']       : '';
		$smtp_encryption   = isset( $_POST['settings']['smtp-encryption'] ) ? $_POST['settings']['smtp-encryption'] : '';
		$smtp_auth         = isset( $_POST['settings']['smtp-auth']       ) ? $_POST['settings']['smtp-auth']       : '';
		$smtp_username     = isset( $_POST['settings']['smtp-username']   ) ? $_POST['settings']['smtp-username']   : '';
		$smtp_password     = isset( $_POST['settings']['smtp-password']   ) ? $_POST['settings']['smtp-password']   : '';
		$from_name         = isset( $_POST['vfb-smtp-test-from-name']     ) ? $_POST['vfb-smtp-test-from-name']     : '';
		$from_email        = isset( $_POST['vfb-smtp-test-from-email']    ) ? $_POST['vfb-smtp-test-from-email']    : '';

		// Exit if Host and Port aren't set
		if ( empty( $smtp_host ) && empty( $smtp_port ) )
			return;

		// Set SMTPDebug to true
		$phpmailer->SMTPDebug = true;

		// Tell the PHPMailer class to use SMTP
		$phpmailer->isSMTP();

		// Set the Host and Port number
	    $phpmailer->Host = $smtp_host;
	    $phpmailer->Port = $smtp_port;

	    // If we're using smtp auth, set the username & password
	    if ( $smtp_auth ) {
		    // Set the SMTPSecure value, if set to none, leave this blank
			$phpmailer->SMTPSecure = $smtp_encryption == 'none' ? '' : $smtp_encryption;
			$phpmailer->SMTPAuth   = true;
		    $phpmailer->Username   = $smtp_username;
		    $phpmailer->Password   = $smtp_password;
	    }

		// Set the From email and name header
	    if ( !empty( $from_name ) && !empty( $from_email ) ) {
		    $phpmailer->From     = $from_email;
		    $phpmailer->FromName = $from_name;
	    }
	}

	/**
	 * Sends the test email and outputs debug info.
	 *
	 * @access public
	 * @param mixed $email
	 * @return void
	 */
	public function smtp_test( $email ) {
		// Set up the mail variables
		$to      = $email;
		$subject = 'VFB Pro SMTP: ' . __( 'Test mail to ', 'vfb-pro') . $to;
		$message = __( 'This is a test email generated by VFB Pro.', 'vfb-pro' );

		// Start output buffering to grab smtp debugging output
		ob_start();

		// Send the test mail
		$result = wp_mail( $to, $subject, $message );

		// Grab the smtp debugging output
		$smtp_debug = ob_get_clean();

		// wp_mail result
		printf( '<h3>%s</h3>', __( 'Email Result', 'vfb-pro' ) );
		echo '<pre>'; print_r( $result ); echo '</pre>';

		// SMTP debug log
		printf( '<h3>%s</h3>', __( 'SMTP Debug Output', 'vfb-pro' ) );
		echo '<pre>'; print_r( $smtp_debug ); echo '</pre>';
	}

	/**
	 * Display the Diagnostics tab
	 *
	 * @access public
	 * @return void
	 */
	public function diagnostics() {
		$diagnostics = new VFB_Pro_Admin_Diagnostics();
		$diagnostics->display();
	}

	/**
	 * Returns the current tab
	 *
	 * @access private
	 * @return void
	 */
	private function get_current_tab() {
		$tab = '';

		if ( !isset( $_GET['vfb-tab'] ) || isset( $_GET['vfb-tab'] ) && 'settings' == $_GET['vfb-tab'] )
			$tab = 'settings';
		elseif ( isset( $_GET['vfb-tab'] ) )
			$tab = esc_html( $_GET['vfb-tab'] );

		return $tab;
	}
}