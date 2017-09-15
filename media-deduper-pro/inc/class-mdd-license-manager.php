<?php
/**
 * Define and instantiate a class that handles licensing using the Easy Digital
 * Downloads Software Licensing add-on.
 *
 * @package Media_Deduper_Pro
 */

// Disallow direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require the Easy Digital Downloads plugin updater class.
if ( ! class_exists( 'MDD_EDD_SL_Plugin_Updater' ) ) {
	require( MDD_PRO_INCLUDES_DIR . 'vendor/EDD_SL_Plugin_Updater.php' );
}

/**
 * Helper class for checking license key status and automatically updating Media
 * Deduper Pro if the license key is valid.
 */
class MDD_License_Manager {

	/**
	 * The URL for the site running EDD from which this plugin was downloaded.
	 */
	const STORE_URL = 'https://cornershopcreative.com';

	/**
	 * The name of this plugin.
	 */
	const ITEM_NAME = 'Media Deduper Pro';

	/**
	 * The page where this plugin's options can be edited.
	 */
	const LICENSE_PAGE = 'upload.php?page=media-deduper&tab=license';

	/**
	 * The slug for the license key settings group.
	 */
	const SETTINGS_GROUP = 'media_deduper_license';

	/**
	 * The name of the license key option in the database.
	 */
	const OPTION_KEY = 'media_deduper_license_key';

	/**
	 * The name of the license key status option in the database.
	 */
	const OPTION_STATUS = 'media_deduper_license_status';

	/**
	 * True if sanitize_license() has been run.
	 *
	 * @var bool
	 */
	public $has_sanitized_key = false;

	/**
	 * Constructor. Set up an instance of the Easy Digital Downloads plugin
	 * updater class and add hooks to prompt the user for a license key.
	 */
	function __construct() {

		// Retrieve license key and status from the DB.
		$this->license_key    = get_option( static::OPTION_KEY );
		$this->license_status = get_option( static::OPTION_STATUS );

		// Set up the updater.
		$this->edd_updater = new MDD_EDD_SL_Plugin_Updater(
			static::STORE_URL,
			MDD_PRO_FILE,
			array(
				'version'   => Media_Deduper_Pro::VERSION,
				'license'   => $this->license_key,
				'item_name' => static::ITEM_NAME,
				'author'    => 'Cornershop Creative',
				'beta'      => false,
			)
		);

		// Display error messages relating to license key activation/deactivation.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		// Register the license key setting. The main purpose of this is to set the
		// sanitization callback, which clears out the license key status and
		// triggers reactivation when the license key changes.
		add_action( 'admin_init',    array( $this, 'register_option' ) );
	}

	/**
	 * Output HTML for a license form.
	 */
	function license_form() {
		?>

		<form method="post" action="options.php">

			<?php
			// Output hidden options_page and nonce fields. These are required in order for the license
			// key to be stored and for our setting's sanitization callback to be called, and therefore
			// for the license key to be activated/deactivated.
			settings_fields( static::SETTINGS_GROUP );
			?>

			<p>
				<label for="media_deduper_license_key">
					<?php esc_html_e( 'Enter your Media Deduper Pro license key here.', 'media-deduper' ); ?>
				</label>
			</p>
			<p>
				<input id="media_deduper_license_key" name="<?php echo esc_attr( static::OPTION_KEY ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $this->license_key ); ?>" <?php disabled( ! empty( $this->license_key ) && 'valid' === $this->license_status ); ?> />
			</p>
			<?php if ( ! empty( $this->license_key ) && 'valid' === $this->license_status ) { ?>
				<p>
					<label class="howto" for="media_deduper_license_key">
						<?php esc_html_e( 'This license key is currently active on this site.', 'media-deduper' ); ?>
					</label>
				</p>
				<p>
					<input type="submit" class="button-secondary" name="mdd_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'media-deduper' ); ?>"/>
				</p>
			<?php } else { ?>
				<p>
					<input type="submit" class="button-secondary" name="mdd_license_activate" value="<?php esc_attr_e( 'Activate License', 'media-deduper' ); ?>"/>
				</p>
			<?php } ?>

		</form>

		<?php
	}

	/**
	 * Register a setting & sanitization callback for the plugin license key.
	 */
	function register_option() {
		// Register the setting (with sanitization/validation callback).
		register_setting( static::SETTINGS_GROUP, static::OPTION_KEY, array(
			'sanitize_callback' => array( $this, 'sanitize_license' ),
		) );
	}

	/**
	 * Sanitization callback for the plugin license key.
	 *
	 * @param string $new_key The new license key to sanitize.
	 */
	function sanitize_license( $new_key ) {

		// Only process the activation/deactivation logic once per pageload. This check is necessary
		// because if the option is unset, this sanitize callback will be called twice, first by
		// update_option and then again by add_option().
		if ( $this->has_sanitized_key ) {
			return $new_key;
		}
		$this->has_sanitized_key = true;

		// Any time the user attempts to deactivate a license key or enter a new license key, clear out
		// the license status option so it reflects the status of the new license key.
		delete_option( 'media_deduper_license_status' );

		// If the user asked to deactivate this license key, try deactivating it now, _before_ we set
		// $this->license_key.
		if ( isset( $_POST['mdd_license_deactivate'] ) ) {
			$this->deactivate_license();
			// deactivate_license() may have altered the value of the license_key property. Return the
			// altered value to prevent wp-admin/options.php from trying to set the option and calling
			// this sanitize callback again.
			return $this->license_key;
		}

		// Set property on $this, which will be used by the activate function.
		$this->license_key = trim( $new_key );

		// If the user asked to activate this license key, try activating it.
		if ( isset( $_POST['mdd_license_activate'] ) ) {
			$this->activate_license();
		}

		// Return key to be stored in the database (WP will call set_option() for us).
		return $new_key;
	}

	/**
	 * This illustrates how to activate a license key.
	 */
	function activate_license() {

		// Send the licensing API request.
		$license_data = $this->send_api_request( 'activate_license' );

		// Handle errors.
		if ( is_wp_error( $license_data ) ) {

			// Get the error message.
			$message = $license_data->get_error_message();

			// Get the error code.
			$error_code = $license_data->get_error_code();

		} elseif ( false === $license_data->success ) {

			// Get the error code.
			$error_code = $license_data->error;

			// Set an error message based on the error code returned by the API.
			switch ( $error_code ) {

				case 'expired' :
					$message = sprintf(
						// translators: %s: The date on which the user's license key expired.
						__( 'Your license key expired on %s.', 'media-deduper' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :
					$message = __( 'Your license key has been disabled.', 'media-deduper' );
					break;

				case 'missing' :
					$message = __( 'Invalid license.', 'media-deduper' );
					break;

				case 'invalid' :
				case 'site_inactive' :
					$message = __( 'Your license is not active for this URL.', 'media-deduper' );
					break;

				case 'item_name_mismatch' :
					// translators: %s: The plugin name.
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'media-deduper' ), static::ITEM_NAME );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'media-deduper' );
					break;

				default :
					$message = __( 'An error occurred while attempting to activate your license key. Please try again.', 'media-deduper' );
					break;

			} // End switch().
		} // End if().

		// Check if anything passed on a message indicating a failure.
		if ( ! empty( $message ) ) {
			// Show the user an error message on the next pageload.
			add_settings_error( static::OPTION_KEY,
				$error_code,
				$message,
				'error'
			);
			return;
		}

		// Set and store license status.
		// $license_data->license will be either "valid" or "invalid".
		$this->license_status = $license_data->license;
		update_option( 'media_deduper_license_status', $this->license_status );

		// Show the user a message on the next pageload.
		add_settings_error( static::OPTION_KEY,
			'updated',
			__( 'Your license key has been activated. Thank you for purchasing Media Deduper Pro!', 'media-deduper' ),
			'updated'
		);
	}


	/**
	 * Illustrates how to deactivate a license key. This will decrease the site count.
	 */
	function deactivate_license() {

		// Send the licensing API request.
		$license_data = $this->send_api_request( 'deactivate_license' );

		// Handle errors.
		if ( is_wp_error( $license_data ) ) {
			$message = $license_data->get_error_message();
		} elseif ( false === $license_data->success ) {
			$message = __( 'An error occurred while attempting to deactivate your license key. Please try again.', 'media-deduper' );
		}

		// Check if anything passed on a message indicating a failure.
		if ( ! empty( $message ) ) {
			$base_url = admin_url( static::LICENSE_PAGE );
			// Show the user an error message on the next pageload.
			add_settings_error( static::OPTION_KEY,
				'deactivate-failure',
				$message,
				'error'
			);
			return;
		}

		// Clear out license key.
		$this->license_key = false;
		delete_option( 'media_deduper_license_key' );

		// Clear out license status.
		$this->license_status = false;
		delete_option( 'media_deduper_license_status' );

		// Show the user a message on the next pageload.
		add_settings_error( static::OPTION_KEY,
			'deactivated',
			__( 'Your license key has been deactivated.', 'media-deduper' ),
			'updated'
		);
	}

	/**
	 * On the plugin license page, display any messages that may have been set by the license key
	 * activation/deactivation functions.
	 */
	function admin_notices() {
		$screen = get_current_screen();
		if ( 'media_page_media-deduper' === $screen->id ) {
			if ( isset( $_GET['tab'] ) && 'license' === $_GET['tab'] ) {
				settings_errors();
			}
		}
	}

	/**
	 * Send a request to the EDD licensing API on the Cornershop site.
	 *
	 * @param string $action The action to send.
	 */
	function send_api_request( $action ) {
		// Data to send in our API request.
		$api_params = array(
			'edd_action' => $action,
			'license'    => $this->license_key,
			'item_name'  => rawurlencode( static::ITEM_NAME ), // The name of our product in EDD.
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( static::STORE_URL, array(
			'timeout' => 15,
			'sslverify' => false,
			'body' => $api_params,
		) );

		// If wp_remote_post() returned an error, pass it along untouched.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// If the remote server returned a status other than 200, return a generic
		// error object.
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'mdd_edd_api_generic', __( 'An error occurred while attempting to contact the Cornershop licensing API endpoint. Please try again.', 'media-deduper' ) );
		}

		// If request was successful, return the response data.
		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}
