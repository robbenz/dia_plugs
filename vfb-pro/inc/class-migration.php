<?php
/**
 * Handles migrations from free VFB or VFB Pro < 3.0
 *
 * @since      3.0
 */
class VFB_Pro_Migration {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Display banners
		add_action( 'admin_notices', array( $this, 'migrate_from_free' ) );
		add_action( 'admin_notices', array( $this, 'migrate_from_pro' ) );

		// Process migrations
		add_action( 'admin_init', array( $this, 'process_migration_free' ) );
		add_action( 'admin_init', array( $this, 'process_migration_pro' ) );
	}

	/**
	 * Deactivate plugins.
	 *
	 * Runs when Migrate Forms or Dismiss button has been pressed.
	 *
	 * @access public
	 * @param string $slug (default: 'visual-form-builder')
	 * @return void
	 */
	public function deactivate_plugins( $slug = 'visual-form-builder' ) {
		if ( 'visual-form-builder' == $slug ) {
			if ( is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) )
				deactivate_plugins( '/visual-form-builder/visual-form-builder.php' );
		}
		else if ( 'visual-form-builder-pro' == $slug ) {
			// Automatically deactivate older version of VFB Pro, if active
			if ( is_plugin_active( 'visual-form-builder-pro/visual-form-builder-pro.php' ) )
				deactivate_plugins( '/visual-form-builder-pro/visual-form-builder-pro.php' );

			// Automatically deactivate Create Post add-on, if active
			if ( is_plugin_active( 'vfb-pro-create-post/vfb-pro-create-post.php' ) )
				deactivate_plugins( '/vfb-pro-create-post/vfb-pro-create-post.php' );

			// Automatically deactivate Create User add-on, if active
			if ( is_plugin_active( 'vfb-pro-create-user/vfb-pro-create-user.php' ) )
				deactivate_plugins( '/vfb-pro-create-user/vfb-pro-create-user.php' );

			// Automatically deactivate Display Entries add-on, if active
			if ( is_plugin_active( 'vfb-pro-display-entries/vfb-pro-display-entries.php' ) )
				deactivate_plugins( '/vfb-pro-display-entries/vfb-pro-display-entries.php' );

			// Automatically deactivate Form Designer add-on, if active
			if ( is_plugin_active( 'vfb-pro-form-designer/vfb-pro-form-designer.php' ) )
				deactivate_plugins( '/vfb-pro-form-designer/vfb-pro-form-designer.php' );

			// Automatically deactivate Notifications add-on, if active
			if ( is_plugin_active( 'vfb-pro-notifications/vfb-pro-notifications.php' ) )
				deactivate_plugins( '/vfb-pro-notifications/vfb-pro-notifications.php' );

			// Automatically deactivate Payments add-on, if active
			if ( is_plugin_active( 'vfb-pro-payments/vfb-pro-payments.php' ) )
				deactivate_plugins( '/vfb-pro-payments/vfb-pro-payments.php' );
		}
	}

	/**
	 * Display migration banner if the upgrading from the free version
	 *
	 * @access public
	 * @return void
	 */
	public function migrate_from_free() {
		if ( !current_user_can( 'install_plugins' ) )
			return;

		// If verified VFB Pro license isn't available, don't display
		if ( !get_option( 'vfbp_license_status' ) || 0 == get_option( 'vfbp_license_status' ) )
			return;

		// If free version of VFB isn't active, don't display
		if ( !is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) )
			return;

		// If free version of VFB isn't installed, don't display
		if ( !get_option( 'vfb_db_version' ) )
			return;

		// If they have upgraded or dismissed, don't display
		if ( get_option( 'vfbp_migration' ) || get_option( 'vfbp_migration_ignore' ) )
			return;

		$migrate_url = add_query_arg(
			array(
				'page'        => 'vfb-pro',
				'vfbp-action' => 'free-migrate',
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_migrate_free' )
		);

		$dismiss_url = add_query_arg(
			array(
				'page'        => 'vfb-pro',
				'vfbp-action' => 'free-migrate',
				'ignore'      => 1,
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_migrate_free' )
		);
	?>
	<div class="update-nag">
		<h3><?php _e( 'Migrate your forms to VFB Pro', 'vfb-pro' ); ?></h3>
		<p>
			<?php _e( 'Transferring your existing data from Visual Form Builder to the Pro version is easy. Simply click on the Migrate Forms button below.', 'vfb-pro' ); ?>
		</p>
		<p>
			<strong style="color: red;">
				<?php _e( 'This process cannot be reversed, so if you have already started using the Pro version, please be aware that migrating will wipe the VFB Pro data tables clean.', 'vfb-pro' ); ?>
			</strong>
		</p>
		<p>
			<a href="<?php echo esc_url( $migrate_url ); ?>" class="button button-primary"><?php _e( 'Migrate Forms', 'vfb-pro' ); ?></a>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary"><?php _e( 'Dismiss', 'vfb-pro' ); ?></a>
		</p>
	</div> <!-- .update-nag -->
	<?php
	}

	/**
	 * Display migration banner if the upgrading from the free version
	 *
	 * @access public
	 * @return void
	 */
	public function migrate_from_pro() {
		if ( !current_user_can( 'install_plugins' ) )
			return;

		// If verified VFB Pro license isn't available, don't display
		if ( !get_option( 'vfbp_license_status' ) || 0 == get_option( 'vfbp_license_status' ) )
			return;

		// If old version of VFB Pro isn't active, don't display
		if ( !is_plugin_active( 'visual-form-builder-pro/visual-form-builder-pro.php' ) )
			return;

		// If neither new or old version of VFB Pro are installed, don't display
		if ( !get_option( 'vfbp_db_version' ) && !get_option( 'vfb_pro_db_version' ) )
			return;

		// If they have upgraded or dismissed, don't display
		if ( get_option( 'vfbp_migration' ) || get_option( 'vfbp_migration_ignore' ) )
			return;

		$migrate_url = add_query_arg(
			array(
				'page'        => 'vfb-pro',
				'vfbp-action' => 'pro-migrate',
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_migrate_pro' )
		);

		$dismiss_url = add_query_arg(
			array(
				'page'        => 'vfb-pro',
				'vfbp-action' => 'pro-migrate',
				'ignore'      => 1,
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_migrate_pro' )
		);
	?>
	<div class="update-nag">
		<h3><?php _e( 'Convert your old VFB Pro data', 'vfb-pro' ); ?></h3>
		<p>
			<?php _e( 'Starting with VFB Pro 3.0, fields, entries, and settings are saved differently in the database which means old entries and certain fields cannot be transferred. For more information, please see <a href="http://support.vfbpro.com">support.vfbpro.com</a>.', 'vfb-pro' ); ?>
		</p>
		<p>
			<strong style="color: red;">
				<?php _e( 'This process cannot be reversed, so if you have already started using the Pro version, please be aware that migrating will wipe the VFB Pro data tables clean.', 'vfb-pro' ); ?>
			</strong>
		</p>
		<p>
			<a href="<?php echo esc_url( $migrate_url ); ?>" class="button button-primary"><?php _e( 'Convert and Transfer', 'vfb-pro' ); ?></a>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary"><?php _e( 'Dismiss', 'vfb-pro' ); ?></a>
		</p>
	</div> <!-- .update-nag -->
	<?php
	}

	/**
	 * Process migration from free to Pro version
	 *
	 * @access public
	 * @return void
	 */
	public function process_migration_free() {
		global $wpdb;

		if ( !isset( $_GET['vfbp-action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'free-migrate' !== $_GET['vfbp-action'] )
			return;

		check_admin_referer( 'vfbp_migrate_free' );

		// If Dismiss is clicked, get out of here
		if ( isset( $_GET['ignore'] ) && 1 == $_GET['ignore'] ) {
			add_option( 'vfbp_migration_ignore', 1 );

			$this->deactivate_plugins();

			return;
		}

		// Set database names of free version
		$vfb_fields = $wpdb->prefix . 'visual_form_builder_fields';
		$vfb_forms  = $wpdb->prefix . 'visual_form_builder_forms';

		$forms = $wpdb->get_results( "SELECT * FROM $vfb_forms ORDER BY form_id" );

		// Truncate the tables in case any forms or fields have been added
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FORMS_TABLE_NAME );
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FIELDS_TABLE_NAME );
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FORM_META_TABLE_NAME );

		foreach ( $forms as $form ) {
			$data = array(
				'id'           => $form->form_id,
				'title'        => $form->form_title,
				'status'       => 'publish',
				'date_updated' => date( 'Y-m-d H:i:s', strtotime ( 'now' ) )
			);

			$meta = array(
				'confirmation-type' => '',
				'text-message'      => '',
				'wp-page'           => '',
				'redirect'          => '',
				'from-name'         => '',
				'reply-to'          => '',
				'subject'           => '',
				'email-to'          => '',
				'from-email'        => '',
				'cc'                => '',
				'bcc'               => '',
				'notify-name'       => '',
				'notify-email'      => '',
				'notify-subject'    => '',
				'notify-email-to'   => '',
				'notify-message'    => '',
				'notify-entry-copy' => '',
			);

			// Confirmation Settings
			if ( 'page' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'wp-page';
				$meta['wp-page']           = $form->form_success_message;
			}
			elseif ( 'text' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'text';
				$meta['text-message'] = htmlspecialchars_decode( stripslashes( $form->form_success_message ) );
			}
			elseif ( 'redirect' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'redirect';
				$meta['redirect']          = $form->form_success_message;
			}

			// Email Settings
			$meta['from-name'] = $form->form_email_from_name;
			$meta['reply-to']  = $form->form_email_from;
			$meta['subject']   = $form->form_email_subject;

			$emails = maybe_unserialize( $form->form_email_to );
			$meta['email-to'] = implode( ',', $emails );

			// Notify Settings
			$meta['notify-name']       = $form->form_notification_email_name;
			$meta['notify-email']      = $form->form_notification_email_from;
			$meta['notify-subject']    = $form->form_email_subject;
			//$meta['notify-email-to']   = '';
			$meta['notify-message']    = htmlspecialchars_decode( stripslashes( $form->form_notification_message ) );
			$meta['notify-entry-copy'] = $form->form_notification_entry;

			// Insert form
			$wpdb->insert( VFB_FORMS_TABLE_NAME, $data );

			// Insert form metadata
			foreach ( $meta as $meta_key => $meta_value ) {
				$wpdb->insert(
					VFB_FORM_META_TABLE_NAME,
					array(
						'form_id'    => $form->form_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value
					)
				);
			}

			$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $vfb_fields WHERE form_id = %d ORDER BY field_id", $form->form_id ) );
			foreach ( $fields as $field ) {

				// Don't import the verification, secret, or IP address fields
				if ( !in_array( $field->field_type, array( 'verification', 'secret', 'ip-address' ) ) ) {
					$data = array(
						'id' 		  => $field->field_id,
						'form_id' 	  => $form->form_id,
						'field_type'  => $field->field_type,
						'field_order' => $field->field_sequence,
					);

					// Convert Fieldset into Heading
					if ( 'fieldset' == $field->field_type )
						$data['field_type'] = 'heading';

					$field_options = '';
					if ( in_array( $field->field_type, array( 'radio', 'checkbox', 'select' ) ) ) {
						$options = maybe_unserialize( $field->field_options );

						$field_options = array();
						if ( !empty( $options ) && is_array( $options ) ) {
							foreach ( $options as $option => $value ) {
								$field_options[ $option ]['label'] = $value;

								// If default selection is available and not 0
								if ( !empty( $field->field_default ) && $field->field_default > 0 ) {
									// Reset to zero index to match new options
									$selected = $field->field_default - 1;

									if ( $selected == $option )
										$field_options[ $option ]['default'] = 1;
								}
							}
						}
					}

					$field_data = array(
						'label'         => $field->field_name,
						'description'   => $field->field_description,
						'css'           => $field->field_css,
						'default_value' => $field->field_default,
						'required'      => $field->field_required,
						'options'       => $field_options,
					);

					$data['data'] = serialize( $field_data );

					$wpdb->insert( VFB_FIELDS_TABLE_NAME, $data );
				}
			}
		}

		// Automatically deactivate free version of VFB, if active
		$this->deactivate_plugins();

		// Set upgrade as complete so admin notice closes
		update_option( 'vfbp_migration', 1 );
	}

	/**
	 * Process migration from older Pro to Pro 3.0+
	 *
	 * @access public
	 * @return void
	 */
	public function process_migration_pro() {
		global $wpdb;

		if ( !isset( $_GET['vfbp-action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'pro-migrate' !== $_GET['vfbp-action'] )
			return;

		check_admin_referer( 'vfbp_migrate_pro' );

		// If Dismiss is clicked, get out of here
		if ( isset( $_GET['ignore'] ) && 1 == $_GET['ignore'] ) {
			add_option( 'vfbp_migration_ignore', 1 );

			$this->deactivate_plugins( 'visual-form-builder-pro' );

			return;
		}

		// Set database names of older Pro version
		$vfb_fields = $wpdb->prefix . 'vfb_pro_fields';
		$vfb_forms  = $wpdb->prefix . 'vfb_pro_forms';

		$forms = $wpdb->get_results( "SELECT * FROM $vfb_forms ORDER BY form_id" );

		// Truncate the tables in case any forms or fields have been added
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FORMS_TABLE_NAME );
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FIELDS_TABLE_NAME );
		$wpdb->query( 'TRUNCATE TABLE ' . VFB_FORM_META_TABLE_NAME );

		foreach ( $forms as $form ) {
			$data = array(
				'id'           => $form->form_id,
				'title'        => $form->form_title,
				'status'       => $form->form_status,
				'date_updated' => date( 'Y-m-d H:i:s', strtotime ( 'now' ) )
			);

			// Expiration date
			$schedule = maybe_unserialize( $form->form_entries_schedule );
			$expire_date = !empty( $schedule['end'] ) ? date( 'Y/m/d H:i', strtotime( $schedule['end'] ) ) : '';

			$form_data = array(
				'limit'      => $form->form_entries_allowed,
				'expiration' => $expire_date,
			);

			$data['data'] = serialize( $form_data );

			// Email Design
			$email_design     = maybe_unserialize( $form->form_email_design );
			$email_format     = !empty( $email_design['format'] ) 			 ? $email_design['format'] 			  : 'html';
			$email_color_bg   = !empty( $email_design['background_color'] )  ? $email_design['background_color']  : '#fbfbfb';
			$email_color_link = !empty( $email_design['link_color'] ) 		 ? $email_design['link_color'] 		  : '#41637e';
			$email_color_h1   = !empty( $email_design['header_text_color'] ) ? $email_design['header_text_color'] : '#565656';
			$email_color_text = !empty( $email_design['text_color'] )        ? $email_design['text_color']        : '#565656';

			$email = array(
				'color-bg'   => $email_color_bg,
				'color-link' => $email_color_link,
				'color-h1'   => $email_color_h1,
				'font-h1'	 => 'Arial',
				'color-h2'   => '#555555',
				'font-h2'	 => 'Georgia',
				'color-h3'   => '#555555',
				'font-h3'	 => 'Georgia',
				'color-text' => $email_color_text,
				'font-text'  => 'Georgia',
			);

			$meta = array(
				'confirmation-type' => '',
				'text-message'      => '',
				'wp-page'           => '',
				'redirect'          => '',
				'from-name'         => '',
				'reply-to'          => '',
				'subject'           => '',
				'email-to'          => '',
				'from-email'        => '',
				'cc'                => '',
				'bcc'               => '',
				'notify-name'       => '',
				'notify-email'      => '',
				'notify-subject'    => '',
				'notify-email-to'   => '',
				'notify-message'    => '',
				'notify-entry-copy' => '',
				'email-format'		=> $email_format,
				'email-design'		=> serialize( $email ),
			);

			// Confirmation Settings
			if ( 'page' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'wp-page';
				$meta['wp-page']           = $form->form_success_message;
			}
			elseif ( 'text' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'text';
				$meta['text-message'] = htmlspecialchars_decode( stripslashes( $form->form_success_message ) );
			}
			elseif ( 'redirect' == $form->form_success_type ) {
				$meta['confirmation-type'] = 'redirect';
				$meta['redirect']          = $form->form_success_message;
			}

			// Email Settings
			$meta['from-name'] = $form->form_email_from_name;
			$meta['reply-to']  = $form->form_email_from;
			$meta['subject']   = $form->form_email_subject;

			$emails = maybe_unserialize( $form->form_email_to );
			$meta['email-to'] = implode( ',', $emails );

			// Notify Settings
			$meta['notify-name']       = $form->form_notification_email_name;
			$meta['notify-email']      = $form->form_notification_email_from;
			$meta['notify-subject']    = $form->form_email_subject;
			//$meta['notify-email-to']   = '';
			$meta['notify-message']    = htmlspecialchars_decode( stripslashes( $form->form_notification_message ) );
			$meta['notify-entry-copy'] = $form->form_notification_entry;

			// Insert form
			$wpdb->insert( VFB_FORMS_TABLE_NAME, $data );

			// Insert form metadata
			foreach ( $meta as $meta_key => $meta_value ) {
				$wpdb->insert(
					VFB_FORM_META_TABLE_NAME,
					array(
						'form_id'    => $form->form_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value
					)
				);
			}

			$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $vfb_fields WHERE form_id = %d ORDER BY field_id", $form->form_id ) );
			foreach ( $fields as $field ) {

				// Don't import the verification, secret, or IP address fields
				if ( !in_array( $field->field_type, array( 'verification', 'secret', 'ip-address' ) ) ) {
					$data = array(
						'id' 		  => $field->field_id,
						'form_id' 	  => $form->form_id,
						'field_type'  => $field->field_type,
						'field_order' => $field->field_sequence,
					);

					// Convert Fieldset into Heading
					if ( 'fieldset' == $field->field_type )
						$data['field_type'] = 'heading';

					$field_options = $likert_options = '';
					if ( in_array( $field->field_type, array( 'radio', 'checkbox', 'select', 'autocomplete' ) ) ) {
						$options = maybe_unserialize( $field->field_options );

						$field_options = array();
						if ( !empty( $options ) && is_array( $options ) ) {
							foreach ( $options as $option => $value ) {
								$field_options[ $option ]['label'] = $value;

								// If default selection is available and not 0
								if ( !empty( $field->field_default ) && $field->field_default > 0 ) {
									// Reset to zero index to match new options
									$selected = $field->field_default - 1;

									if ( $selected == $option )
										$field_options[ $option ]['default'] = 1;
								}
							}
						}
					}
					else if ( 'likert' == $field->field_type ) {
						$options = maybe_unserialize( $field->field_options );

						$likert_options = array();
						if ( !empty( $options ) && is_array( $options ) ) {
							$likert_options['rows'] = isset( $options['rows'] ) ? $options['rows'] : '';
							$likert_options['cols'] = isset( $options['cols'] ) ? $options['cols'] : '';
						}
					}

					$field_data = array(
						'label'         => $field->field_name,
						'description'   => $field->field_description,
						'css'           => $field->field_css,
						'default_value' => $field->field_default,
						'required'      => $field->field_required,
						'options'       => $field_options,
					);

					if ( !empty( $likert_options ) ) {
						$field_data['likert'] = $likert_options;
					}

					$data['data'] = serialize( $field_data );

					$wpdb->insert( VFB_FIELDS_TABLE_NAME, $data );
				}
			}
		}

		// Automatically deactivate older version of VFB Pro, if active
		$this->deactivate_plugins( 'visual-form-builder-pro' );

		// Set upgrade as complete so admin notice closes
		update_option( 'vfbp_migration', 1 );
	}
}