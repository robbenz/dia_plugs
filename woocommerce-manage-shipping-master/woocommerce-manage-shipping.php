<?php
/**
 * Woocommerce Manage Shipping
 *
 * @package   woocommerce-manage-shipping
 * @author    Niels Donninger <niels@donninger.nl>
 * @license   GPL-2.0+
 * @link      http://donninger.nl
 * @copyright 2013 Donninger Consultancy
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Manage Shipping
 * Plugin URI:        https://github.com/nielsdon/woocommerce-manage-shipping
 * Description:       Manage shipping and packaging of (partial) orders in a clear overview.
 * Version:           0.3
 * Author:            Niels Donninger
 * Author URI:        http://donninger.nl
 * Text Domain:       en_US
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nielsdon/woocommerce-manage-shipping
 * Depends:           WooCommerce
 *
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-woocommerce-manage-shipping.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Woocommerce_Manage_Shipping', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Woocommerce_Manage_Shipping', 'deactivate' ) );

/*
 */
add_action( 'plugins_loaded', array( 'Woocommerce_Manage_Shipping', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 */
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-woocommerce-manage-shipping-admin.php' );
	add_action( 'plugins_loaded', array( 'Woocommerce_Manage_Shipping_Admin', 'get_instance' ) );

}
