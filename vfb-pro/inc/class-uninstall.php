<?php
/**
 * Define the uninstall process
 *
 * Installs the DB
 *
 * @since      3.0.1
 */
class VFB_Pro_Uninstall {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * uninstall function.
	 *
	 * @access public
	 * @param mixed $license_key
	 * @param mixed $license_email
	 * @return void
	 */
	public function uninstall( $license_key, $license_email ) {
		$this->deactivate_license( $license_key, $license_email );
		$this->uninstall_data();
		$this->deactivate_plugin();
	}

	/**
	 * Deactivate VFB Pro license.
	 *
	 * @access public
	 * @param mixed $license_key
	 * @param mixed $license_email
	 * @return void
	 */
	public function deactivate_license( $license_key, $license_email ) {
		$deactivate = new VFB_Pro_Plugin_Updater();
		$deactivate->uninstall_deactivate_license( $license_key, $license_email );
	}

	/**
	 * Deactivate VFB Pro plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function deactivate_plugin() {
		deactivate_plugins( 'vfb-pro/vfb-pro.php' );
		update_option(
			'recently_activated',
			array( $plugin => time() ) + (array) get_option( 'recently_activated' )
		);

		wp_redirect( admin_url( 'plugins.php' ) );
		exit();
	}

	/**
	 * Delete all tables and data.
	 *
	 * @access public
	 * @return void
	 */
	public function uninstall_data() {
		global $wpdb;

		$forms  = $wpdb->prefix . 'vfbp_forms';
		$fields = $wpdb->prefix . 'vfbp_fields';
		$meta   = $wpdb->prefix . 'vfbp_formmeta';

		$wpdb->query( "DROP TABLE IF EXISTS $forms" );
		$wpdb->query( "DROP TABLE IF EXISTS $fields" );
		$wpdb->query( "DROP TABLE IF EXISTS $meta" );

		delete_option( 'vfbp_db_version' );
		delete_option( 'vfbp_migration' );
		// Don't delete license or VFB settings in case of reinstallation
		//delete_option( 'vfbp_license_message' );
		//delete_option( 'vfbp_license_status' );
		//delete_option( 'vfbp_settings' );

		$wpdb->query( "DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'vfb-hidden-sequential-num-%'" );
	}
}