<?php
/**
 * Plugin Name: Media Deduper Pro
 * Version: 1.0.1
 * Description: Save disk space and bring some order to the chaos of your media library by removing and preventing duplicate files.
 * Plugin URI: https://cornershopcreative.com/
 * Author: Cornershop Creative
 * Author URI: https://cornershopcreative.com/
 *
 * @package Media_Deduper_Pro
 */

// Check PHP version. The main MDD class uses late static bindings which are only compatible with
// PHP 5.3 and higher.
if ( version_compare( phpversion(), '5.3', '>=' ) ) {

	// Load up the plugin class.
	define( 'MDD_PRO_FILE', __FILE__ );
	define( 'MDD_PRO_INCLUDES_DIR', dirname( MDD_PRO_FILE ) . '/inc/' );
	require_once( MDD_PRO_INCLUDES_DIR . 'class-media-deduper-pro.php' );

} else { // If we're running on PHP 5.2 or lower, show an error and deactivate.

	/**
	 * Display a notice about the user's PHP version.
	 */
	function media_deduper_pro_php53_notice() {
		echo '<div class="message error"><p>' . esc_html__( 'Sorry, but Media Deduper Pro requires PHP version 5.3 or higher.', 'media-deduper' ) . '</p></div>';
	}
	add_action( 'admin_notices', 'media_deduper_pro_php53_notice' );

	/**
	 * Deactivate Media Deduper.
	 */
	function media_deduper_pro_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	add_action( 'admin_init', 'media_deduper_pro_deactivate' );

}
