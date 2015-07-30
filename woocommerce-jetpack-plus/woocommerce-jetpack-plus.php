<?php
/*
Plugin Name: WooCommerce Jetpack Plus
Plugin URI: http://woojetpack.com/plus/
Description: Unlock all WooCommerce Jetpack features and supercharge your WooCommerce site even more.
Version: 1.0.4
Author: Algoritmika Ltd
Author URI: http://www.algoritmika.com
Copyright: © 2014 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return; // Check if WooCommerce is active

if ( ! class_exists( 'WC_Jetpack_Plus' ) ) :

/**
 * Main WC_Jetpack_Plus Class
 *
 * @class WC_Jetpack_Plus
 */
final class WC_Jetpack_Plus {

	/**
	 * @var WC_Jetpack_Plus The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Jetpack_Plus Instance
	 *
	 * Ensures only one instance of WC_Jetpack_Plus is loaded or can be loaded.
	 *
	 * @static
	 * @see WCJP()
	 * @return WC_Jetpack_Plus - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '3.9.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '3.9.1' );
	}

	/**
	 * WC_Jetpack_Plus Constructor.
	 * @access public
	 */
	public function __construct() {

		// Include required files
		//$this->includes();

		// Main hooks
		//add_action( 'init', array( $this, 'init' ), 0 );

		// Unlocks - Global
		add_filter( 'get_wc_jetpack_plus_message', array( $this, 'remove_plus_message' ), 101 );
		add_filter( 'wcj_get_option_filter', array( $this, 'wcj_get_option' ), 101, 2 );
				
		// Loaded action
		//do_action( 'wcj_plus_loaded' );
	}

    /**
     * Unlocks - Global - wcj_get_option.
     */
	public function wcj_get_option( $value1, $value2 ) {

		return $value2;
	}

    /**
     * Unlocks - Global - remove_plus_message.
     */
	public function remove_plus_message() {

		return '';
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	private function includes() {

	}

	/**
	 * Init WC_Jetpack_Plus when WordPress Initialises.
	 *
	public function init() {

		// Before init action
		do_action( 'before_wcj_plus_init' );

		// Init action
		do_action( 'wcj_plus_init' );
	}
	
	/**
	 * Class ends here.
	 */	
}

endif;

/**
 * Returns the main instance of WCJP to prevent the need to use globals.
 *
 * @return WC_Jetpack_Plus
 */
function WCJP() {

	return WC_Jetpack_Plus::instance();
}

WCJP();
