<?php
/*
Plugin Name: DiaMedical WooCommerce Product Tabs
Plugin URI: robbenz.com
Description: This plugin will replace the custom tabs created by woocommerce jetpack because woocommerce jetpack is no longer free -- and does a bunch of shit we dont need
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/


include( plugin_dir_path( __FILE__ ) . 'dia-tabs-admin.php');
include( plugin_dir_path( __FILE__ ) . 'dia-tabs-save-stuff.php');
include( plugin_dir_path( __FILE__ ) . 'dia-tabs-frontend.php');
