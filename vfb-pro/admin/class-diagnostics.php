<?php
/**
 * VFB_Pro_Admin_Diagnostics class.
 *
 * @since 3.1.2
 */
class VFB_Pro_Admin_Diagnostics {
	/**
	 * Initial setup
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'save_report' ) );
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_settings' ) )
			return;
	?>
	<div class="wrap">
		<form method="post" id="vfbp-diagnostics" action="">
			<input name="_vfbp_action" type="hidden" value="save-diagnostics" />
			<?php
				wp_nonce_field( 'vfbp_diagnostics' );
			?>

			<p><?php _e( 'This page will detail information about your server setup, installed plugins, and other details powering your WordPress installation.', 'vfb-pro' ); ?></p>
			<p><?php _e( 'At our request, we may ask you to generate a report based on the following information to help us better assist you in troubleshooting VFB Pro issues.', 'vfb-pro' ); ?></p>

			<?php
				submit_button(
					__( 'Save Diagnostics Report', 'vfb-pro' ),
					'primary',
					'' // leave blank so "name" attribute will not be added
				);

				$this->server_information();
				$this->php_information();
				$this->suhosin();
				$this->php_sessions();
				$this->mysql_load();
				$this->installed_plugins();
				$this->remote_connections();
			?>
		</form>
	</div>
	<?php
	}

	/**
	 * save_report function.
	 *
	 * @access private
	 * @return void
	 */
	public function save_report() {
		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-diagnostics' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_diagnostics' );

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = "{$sitename}vfbp-diagnostics." . date( 'Y-m-d-Hi' ) . ".html";

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: text/html' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		echo '<html><head><title>VFB Pro Diagnostics Report</title><style type="text/css">' . $this->report_css() . '</style></head><body>';

		ob_start();
		$this->server_information();
		$this->php_information();
		$this->suhosin();
		$this->php_sessions();
		$this->mysql_load();
		$this->installed_plugins();
		$this->remote_connections();

		echo '</body></html>';
		exit();
	}

	/**
	 * report_css function.
	 *
	 * @access private
	 * @return void
	 */
	private function report_css() {
		return '.vfb-diagnostics-table {
			border-collapse: collapse;
			margin-bottom: 1em;
			width: 100%;
		}

		.vfb-diagnostics-table td,
		.vfb-diagnostics-table th {
			background: #eee;
			border: 1px solid #f1f1f1;
			font-family: sans-serif;
			padding: 7px;
			vertical-align: baseline;
		}

		.vfb-diagnostics-table th {
			background-color: #0074a2;
			color: white;
			font-weight: bold;
			text-align: left;
			width: 16em;
		}';
	}

	/**
	 * server_information function.
	 *
	 * @access private
	 * @return void
	 */
	private function server_information() {
		global $wp_version, $wp_db_version;
	?>
		<h2><?php _e( 'Server Information', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'WordPress Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $wp_version; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'WordPress DB Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $wp_db_version; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'WordPress Multisite' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ( is_multisite() ) ? __( 'Yes', 'vfb-pro' ) : __( 'No', 'vfb-pro' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'VFB Pro Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo VFB_PLUGIN_VERSION; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'VFB Pro DB Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo VFB_DB_VERSION; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Server Operating System' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo php_uname( 's' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Current PHP Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo 'PHP ' . phpversion(); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Required PHP Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo 'PHP 5.4'; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Current MySQL Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo 'MySQL ' . $this->get_mysql_variable( 'version' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Required MySQL Version' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo 'MySQL 5.5'; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'MySQL DB Name' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo DB_NAME; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'MySQL DB User' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo DB_USER; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Web Server Software' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $_SERVER['SERVER_SOFTWARE']; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Web Server IP Address' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $_SERVER['SERVER_ADDR']; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Web Server Port Number' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $_SERVER['SERVER_PORT']; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Web Server Document Root' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $_SERVER['DOCUMENT_ROOT']; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Domain Name' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $_SERVER['SERVER_NAME']; ?>
					</td>
				</tr>

			</tbody>
		</table>
	<?php
	}

	/**
	 * php_information function.
	 *
	 * @access private
	 * @return void
	 */
	private function php_information() {
		$loaded_extensions = implode( ', ', get_loaded_extensions() );
	?>
		<h2><?php printf( 'PHP %s Information', phpversion() ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'Loaded Extensions' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $loaded_extensions; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'WordPress Memory Limit' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo WP_MEMORY_LIMIT; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'WordPress Max Upload Size' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo size_format( wp_max_upload_size() ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP post_max_size' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'post_max_size' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP upload_max_filesize' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'upload_max_filesize' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP max_input_vars' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'max_input_vars' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP max_input_nesting_level' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'max_input_nesting_level' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP max_execution_time' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'max_execution_time' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP SMTP' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'SMTP' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'PHP smtp_port' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo ini_get( 'smtp_port' ); ?>
					</td>
				</tr>

			</tbody>
		</table>
	<?php
	}

	/**
	 * suhosin function.
	 *
	 * @access private
	 * @return void
	 */
	private function suhosin() {
	?>
		<h2><?php _e( 'Suhosin Information', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'Suhosin Installed' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo extension_loaded( 'suhosin' ) ? __( 'Yes', 'vfb-pro' ) : __( 'No', 'vfb-pro' ); ?>
					</td>
				</tr>

				<?php if ( extension_loaded( 'suhosin' ) ) : ?>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'suhosin.post.max_value_length' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'suhosin.post.max_value_length' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'suhosin.request.max_vars' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'suhosin.request.max_vars' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'suhosin.get.max_vars' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'suhosin.get.max_vars' ); ?>
						</td>
					</tr>
				<?php endif; ?>

			</tbody>
		</table>
	<?php
	}

	/**
	 * php_sessions function.
	 *
	 * @access private
	 * @return void
	 */
	private function php_sessions() {
	?>
		<h2><?php _e( 'PHP Sessions', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'Sessions Enabled' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $this->is_session_started() ? __( 'Yes', 'vfb-pro' ) : __( 'No', 'vfb-pro' ); ?>
					</td>
				</tr>

				<?php if ( $this->is_session_started() ) : ?>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions save_path' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.save_path' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions save_handler' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.save_handler' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions name' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.name' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions use_cookies' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.use_cookies' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions cookie_lifetime' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.cookie_lifetime' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions cookie_path' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.cookie_path' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions cookie_domain' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.cookie_domain' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions serialize_handler' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.serialize_handler' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions use_cookies' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.use_cookies' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions cookie_lifetime' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.cookie_lifetime' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e( 'Sessions cookie_lifetime' , 'vfb-pro' ); ?>
						</th>
						<td>
							<?php echo ini_get( 'session.gc_maxlifetime' ); ?>
						</td>
					</tr>
				<?php endif; ?>

			</tbody>
		</table>
	<?php
	}

	/**
	 * mysql_load function.
	 *
	 * @access private
	 * @return void
	 */
	private function mysql_load() {
		$ratio  = $this->get_mysql_status( 'Aborted_connects' ) / $this->get_mysql_status( 'Connections' );
		$percent = round( 100 * ( 1 - $ratio ), 2 );

		$time    = $this->get_mysql_status( 'uptime' );
		$days    = (int) floor( $time / 86400 );
		$hours   = (int) floor( $time / 3600 ) % 24;
		$minutes = (int) floor( $time / 60 ) % 60;
		$uptime  = $days . ' ' . _n( 'day', 'days', $days, 'vfb-pro' ) . ', '
				   . $hours . ' ' . _n( 'hour', 'hours', $hours, 'vfb-pro' ) . ' ' . __( 'and', 'vfb-pro' ) . ' '
				   . $minutes . ' ' . _n( 'minute', 'minutes', $minutes, 'vfb-pro' );

	?>
		<h2><?php _e( 'MySQL DB Server Load', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'Uptime' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $uptime; ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Queries per second' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $this->get_mysql_statistics( 'Queries', 'seconds' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Connections per minute' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $this->get_mysql_statistics( 'Connections', 'minutes' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e( 'Connections success rate' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php echo $percent . '%'; ?>
					</td>
				</tr>

			</tbody>
		</table>
	<?php
	}

	/**
	 * installed_plugins function.
	 *
	 * @access private
	 * @return void
	 */
	private function installed_plugins() {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

		$all_plugins = array();
	?>
		<h2><?php _e( 'Plugins', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<?php
					foreach ( $active_plugins as $plugin ) :
						$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						$dirname        = dirname( $plugin );
						$version_string = '';
				?>
				<tr valign="top">
					<th scope="row">
						<?php echo !empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : ''; ?>
					</th>
					<td>
						<strong><?php _e( 'Version', 'vfb-pro' ) ?>:</strong> <?php echo $plugin_data['Version']; ?>
					</td>
					<td>
						<strong><?php _e( 'Author', 'vfb-pro' ) ?>:</strong> <?php echo $plugin_data['Author']; ?>
					</td>
					<td>
						<strong><?php _e( 'Plugin Page', 'vfb-pro' ); ?>:</strong> <?php echo !empty( $plugin_data['PluginURI'] ) ? sprintf( '<a href="%1$s">%1$s</a>', $plugin_data['PluginURI'] ) : ''; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php
	}

	/**
	 * remote_connections function.
	 *
	 * @access private
	 * @return void
	 */
	private function remote_connections() {
		$args = array(
			'timeout' 		=> 60,
			'user-agent'	=> 'VFB Pro/' . VFB_PLUGIN_VERSION,
			'body'			=> array( 'cmd' => '_notify-validate' ),
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $args );
	?>
		<h2><?php _e( 'Remote Connections Test', 'vfb-pro' ); ?></h2>
		<table class="vfb-diagnostics-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'wp_remote_post()' , 'vfb-pro' ); ?>
					</th>
					<td>
						<?php
							if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 )
								_e('wp_remote_post() was successful', 'vfb-pro' );
							elseif ( is_wp_error( $response ) )
								_e( 'wp_remote_post() failed. Contact your hosting provider. Error:', 'vfb-pro' ) . ' ' . $response->get_error_message();
							else
								_e( 'wp_remote_post() failed. PayPal IPN may not work with your server.', 'vfb-pro' );
						?>
					</td>
				</tr>

			</tbody>
		</table>
	<?php
	}

	/**
	 * Get a MySQL variable.
	 *
	 * Query the DB and return the result as an associative array (ARRAY_A).
	 * We then return the value of it, using the key 'Value'.
	 *
	 * @since 1.0
	 *
	 * @global WPDB $wpdb
	 * @param string $variable
	 * @return string
	 */
	protected function get_mysql_variable( $variable ) {
		global $wpdb;

		$result = $wpdb->get_row( "SHOW VARIABLES LIKE '$variable';", ARRAY_A );

		return $result['Value'];
	}

	/**
	 * Get a MySQL status variable.
	 *
	 * Query the DB and return the result as an associative array (ARRAY_A).
	 * We then return the value of it, using the key 'Value'.
	 *
	 * @since 1.0
	 *
	 * @global WPDB $wpdb
	 * @param string $variable
	 * @return string
	 */
	protected function get_mysql_status( $variable ) {
		global $wpdb;

		$result = $wpdb->get_row( "SHOW STATUS LIKE '$variable';", ARRAY_A );

		return $result['Value'];
	}

	/**
	 * Retrieve and calculate some MySQL statistics.
	 *
	 * @since 1.0
	 *
	 * @param string $variable
	 * @param string $timeunit seconds|minutes|hours
	 * @return mixed
	 */
	protected function get_mysql_statistics( $variable, $timeunit ) {
		$amount         = $this->get_mysql_status( $variable );
		$uptime_seconds = $this->get_mysql_status( 'uptime' );

		switch ( $timeunit ) {
			case 'seconds':
				$result = $amount / $uptime_seconds;
				break;
			case 'minutes':
				$uptime = $uptime_seconds / MINUTE_IN_SECONDS;
				$result = $amount / $uptime;
				break;
			case 'hours':
				$uptime = $uptime_seconds / HOUR_IN_SECONDS;
				$result = $amount / $uptime;
				break;
		}

		return round( $result, 8 );
	}

	/**
	 * is_session_started function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function is_session_started() {
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
	            return session_status() === PHP_SESSION_ACTIVE ? true : false;
	        }
	        else {
	            return session_id() === '' ? false : true;
	        }
	    }

	    return false;
	}
}