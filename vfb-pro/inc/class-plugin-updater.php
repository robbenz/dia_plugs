<?php
/**
 * Class that controls plugin update API
 *
 * @since 3.0
 */
class VFB_Pro_Plugin_Updater {
	/**
	 * The update API
	 *
	 *
	 * @var string
	 * @access protected
	 */
	protected $api_url = 'https://api.vfbpro.com/update-check/';

	/**
	 * The license check API
	 *
	 *
	 * @var string
	 * @access protected
	 */
	protected $license_url = 'https://api.vfbpro.com/license-check/';

	/**
	 * The plugin base name
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $name;

	/**
	 * The plugin slug
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $slug;

	/**
	 * Various API data such as name, slug, version, etc
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $api_data = array();

	public function __construct() {
		$this->name    = plugin_basename( VFB_PLUGIN_FILE );
		$this->slug    = basename( VFB_PLUGIN_FILE, '.php');
		$this->version = VFB_PLUGIN_VERSION;

		$this->api_data['name']    = $this->name;
		$this->api_data['slug']    = $this->slug;
		$this->api_data['version'] = $this->version;
		$this->api_data['license'] = get_option( 'vfbp_license_status' );

		// Hook into the plugin update check
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );

		// Display plugin details screen for updating
		add_filter( 'plugins_api', array( $this, 'api_info' ), 10, 3 );

		add_action( 'wp_ajax_vfbp-verify-license', array( $this, 'verify_license' ) );
		add_action( 'wp_ajax_vfbp-deactivate-license', array( $this, 'deactivate_license' ) );

		add_action( 'wp_ajax_nopriv_vfbp-verify-license', array( $this, 'verify_license' ) );
		add_action( 'wp_ajax_nopriv_vfbp-deactivate-license', array( $this, 'deactivate_license' ) );

		// For testing only
		//add_action( 'init', array( $this, 'delete_transient' ) );
	}

	/**
	 * Delete transients on page load
	 *
	 * FOR TESTING PURPOSES ONLY
	 *
	 * @access public
	 * @return void
	 */
	public function delete_transient() {
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * Check the plugin version to see if there's a new one
	 *
	 * @access public
	 * @param mixed $transient
	 * @return void
	 */
	public function api_check( $transient ) {
		// If no checked transiest, just return its value without hacking it
		if ( empty( $transient ) )
			return $transient;

		// Send request checking for an update
		$response = $this->api_request(
			'update-check',
			array(
				'license' => $this->api_data['license'],
				'slug'    => $this->slug,
			)
		);

		// If response is false, don't alter the transient
		if( false !== $response && is_object( $response ) && isset( $response->new_version ) ) {
			// If this version is less than the new version
			if( version_compare( $this->version, $response->new_version, '<' ) )
				$transient->response[ $this->name ] = $response;
		}

		return $transient;
	}

	/**
	 * Return the plugin details for the plugin update screen
	 *
	 * @access public
	 * @param mixed $data
	 * @param string $action (default: '')
	 * @param mixed $args (default: null)
	 * @return void
	 */
	public function api_info( $data, $action = '', $args = null ) {
		if ( ( $action != 'plugin_information' ) || !isset( $args->slug ) || ( $args->slug != $this->slug ) )
			return $data;

		// Send request checking for an update
		$response = $this->api_request(
			'plugin-info',
			array(
				'slug' => $this->slug,
			)
		);

		if ( false !== $response )
			$data = $response;

		return $data;
	}

	/**
	 * Send a request to the custom API
	 *
	 * @access public
	 * @param mixed $action
	 * @param mixed $data
	 * @return void
	 */
	public function api_request( $action, $data ) {
		global $wp_version;

		$data = array_merge( $this->api_data, $data );

		if ( $data['slug'] != $this->slug )
			return;

		if ( empty( $data['license'] ) )
			return;

		$api_params = array(
			'vfb-action' 	=> $action,
			'license' 		=> $data['license'],
			'slug' 			=> $this->slug,
		);

		$request = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			if( $request && isset( $request->sections ) )
				$request->sections = (array) maybe_unserialize( $request->sections );

			return $request;
		}
		else
			return false;
	}

	/**
	 * Verify license with API
	 *
	 * @access public
	 * @return void
	 */
	public function verify_license() {
		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_GET['action'] ) && 'vfbp-verify-license' !== $_GET['action'] )
			return;

		$license = isset( $_GET['license'] ) ? esc_html( $_GET['license'] ) : '';
		$email   = isset( $_GET['email'] ) ? esc_html( $_GET['email'] ) : '';
		$url     = get_bloginfo( 'url' );

		$api_params = array(
			'vfb-action' => 'verify_license',
			'license' 	 => $license,
			'email'		 => $email,
			'url'		 => $url,
		);

		$request = wp_remote_post(
			$this->license_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = wp_remote_retrieve_body( $request );

			if ( $request ) {
				$response = json_decode( $request );

				if ( $response && isset( $response->status ) ) {
					if ( !isset( $response->multiple ) ) {
						update_option( 'vfbp_license_status', $response->status );
						update_option( 'vfbp_license_message', $response->message );
					}
				}

				echo $request;
			}
		}
		else {
			$response = array(
				'status'  => 0,
				'message' => sprintf( 'ERROR: %1$s. Your site appears to be blocking WordPress AJAX requests. <a href="%2$s" target="_blank">See help</a>', $request->get_error_message(), 'http://support.vfbpro.com/support/solutions/articles/4000057454' ),
			);

			echo json_encode( $response );
		}

		die(1);
	}

	/**
	 * Deactivate a license.
	 *
	 * @access public
	 * @return void
	 */
	public function deactivate_license() {
		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_GET['action'] ) && 'vfbp-deactivate-license' !== $_GET['action'] )
			return;

		$license = isset( $_GET['license'] ) ? esc_html( $_GET['license'] ) : '';
		$email   = isset( $_GET['email'] ) ? esc_html( $_GET['email'] ) : '';
		$url     = get_bloginfo( 'url' );

		$api_params = array(
			'vfb-action' => 'deactivate_license',
			'license' 	 => $license,
			'email'		 => $email,
			'url'		 => $url,
		);

		$request = wp_remote_post(
			$this->license_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = wp_remote_retrieve_body( $request );

			if ( $request ) {
				$response = json_decode( $request );

				if ( $response && isset( $response->status ) ) {
					update_option( 'vfbp_license_status', 0 );
					update_option( 'vfbp_license_message', $response->message );
				}

				echo $request;
			}
		}
		else {
			$response = array(
				'status'  => 0,
				'message' => sprintf( 'ERROR: %1$s. Your site appears to be blocking WordPress AJAX requests. <a href="%2$s" target="_blank">See help</a>', $request->get_error_message(), 'http://support.vfbpro.com/support/solutions/articles/4000057454' ),
			);

			echo json_encode( $response );
		}

		die(1);
	}

	/**
	 * uninstall_deactivate_license function.
	 *
	 * @access public
	 * @param mixed $license
	 * @param mixed $email
	 * @return void
	 */
	public function uninstall_deactivate_license( $license, $email ) {
		$url     = get_bloginfo( 'url' );

		$api_params = array(
			'vfb-action' => 'deactivate_license',
			'license' 	 => $license,
			'email'		 => $email,
			'url'		 => $url,
		);

		$request = wp_remote_post(
			$this->license_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = wp_remote_retrieve_body( $request );

			if ( $request ) {
				$response = json_decode( $request );

				if ( $response && isset( $response->status ) ) {
					update_option( 'vfbp_license_status', 0 );
					update_option( 'vfbp_license_message', $response->message );
				}
			}
		}
	}
}