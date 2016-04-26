<?php
/**
 * Plugin Name: WPB WooCommerce Product slider
 * Plugin URI: http://wpbean.com/wpb-woocommarce-product-slider/
 * Description: WPB WooCommerce Product slider is a nice and cool carousel product slider. It has lots of cool feature like shortcode control, widget,custom settings etc. Shortcodes: For latest product slider &nbsp;&nbsp;&nbsp;&nbsp;[wpb-latest-product title="Latest Product"]&nbsp;&nbsp;&nbsp;&nbsp; & &nbsp;&nbsp; For feature product slider &nbsp;&nbsp;&nbsp;&nbsp; [wpb-feature-product title="Feature Products"] &nbsp;&nbsp;&nbsp;&nbsp; jQuery Plugin by: <a href="http://owlgraphic.com/owlcarousel/">owlcarousel</a> & animation script by <a href="http://tympanus.net/codrops/2013/06/18/caption-hover-effects/">MARY LOU</a>. &nbsp;&nbsp;&nbsp;&nbsp; WordPress Settings API PHP Class by: <a href="https://github.com/tareq1988/wordpress-settings-api-class" >Wedevs</a>.
 * Author: wpbean
 * Version: 1.0.5
 * Author URI: http://wpbean.com
 * Text Domain: wpb-wps
 * Domain Path: /languages
*/


/**
 * Define Path 
 */

define( 'WPB_WPS_URI', WP_CONTENT_URL. '/plugins/wpb-woocommerce-product-slider' );



/**
 * Require Files
 */

require_once dirname( __FILE__ ) . '/inc/wpb-scripts.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-widgets.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-shortcodes.php';
require_once dirname( __FILE__ ) . '/inc/class.settings-api.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-settings.php';
require_once dirname( __FILE__ ) . '/inc/wpb-wps-functions.php';




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