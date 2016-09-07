<?php
/**
 * Plugin Name: WPB WooCommerce Product slider
 * Plugin URI: https://wpbean.com/wpb-woocommarce-product-slider/
 * Description: WPB WooCommerce Product slider is a nice and cool carousel product slider. It has lots of cool feature like shortcode control, widget,custom settings etc. Shortcodes: For latest product slider &nbsp;&nbsp;&nbsp;&nbsp;[wpb-latest-product title="Latest Product"]&nbsp;&nbsp;&nbsp;&nbsp; & &nbsp;&nbsp; For feature product slider &nbsp;&nbsp;&nbsp;&nbsp; [wpb-feature-product title="Feature Products"]
 * Author: wpbean
 * Version: 2.0
 * Author URI: https://wpbean.com
 * Text Domain: wpb-wps
 * Domain Path: /languages
*/


/**
 * Define Path 
 */

define( 'WPB_WPS_URI', WP_CONTENT_URL. '/plugins/wpb-woocommerce-product-slider' );

/**
 * Localization
 */

function wpb_wps_localization() {
	load_plugin_textdomain( 'wpb-wps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'wpb_wps_localization' );


/**
 * Plugin Activation redirect 
 */

if( !function_exists( 'wpb_wps_activation_redirect' ) ){
	function wpb_wps_activation_redirect( $plugin ) {
	    if( $plugin == plugin_basename( __FILE__ ) ) {
	        exit( wp_redirect( admin_url( 'options-general.php?page=wpb_woocommerce_product_slider' ) ) );
	    }
	}
}
add_action( 'activated_plugin', 'wpb_wps_activation_redirect' );



/**
 * Plugin Action Links
 */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpb_wps_add_action_links' );
function wpb_wps_add_action_links ( $links ) {

	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=wpb_woocommerce_product_slider') ) .'">'. __( 'Settings', 'wpb-wps' ) .'</a>';
	$links[] = '<a style="color: red; font-weight: bold" href="'. esc_url( 'http://bit.ly/1PAAzv6' ) .'">'. __( 'Go PRO!', 'wpb-wps' ) .'</a>';

	return $links;
}


/**
 * Require Files
 */

require_once dirname( __FILE__ ) . '/inc/wpb-scripts.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-widgets.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-shortcodes.php';
require_once dirname( __FILE__ ) . '/inc/class.settings-api.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-settings.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-functions.php';