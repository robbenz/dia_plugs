<?php
/*
	Plugin Name: Woocommerce Easy Checkout Fields Editor
	Plugin URI: http://phppoet.com
	Description: lets you Add/edit/delete checkout fields for woocoomerce. 
    Version: 1.0.6
	Author: Parbat Chaudhari
	Author URI: http://phppoet.com
	
	Text Domain: phppoet-checkout-fields
	Domain Path: /languages
	Requires at least: 3.3
    Tested up to: 4.1
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 if( !defined( 'pcfme_PLUGIN_URL' ) )
define( 'pcfme_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


load_plugin_textdomain( 'pcfme', false, basename( dirname(__FILE__) ).'/languages' );

//include the classes
include dirname( __FILE__ ) . '/include/pcmfe_core_functions.php';
include dirname( __FILE__ ) . '/include/update_checkout_fields_class.php';
include dirname( __FILE__ ) . '/include/add_order_meta_fields_class.php';
include dirname( __FILE__ ) . '/include/manage_extrafield_class.php';

include dirname( __FILE__ ) . '/include/admin/pcfme_admin_settings.php';


?>