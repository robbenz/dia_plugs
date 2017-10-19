<?php
/*
Plugin Name: DiaMedical USA WooCommerce Product Tabs
Plugin URI: robbenz.com
Description: This plugin will replace the custom tabs created by woocommerce jetpack because woocommerce jetpack is no longer free -- and does a bunch of shit we dont need
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/

// check to make sure woocommerce is active -- and break if its not
add_action( 'admin_init', 'dia_check_woo' );
function dia_check_woo() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'dia_woo_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

// throw error code if its not active
function dia_woo_notice(){
    ?><div class="error"><p>Sorry, but this plugin requires Woocommerce to be installed and active, you idiot.</p></div><?php
}

// include the respective php files after successful activation
register_activation_hook( __FILE__, 'dia_woo_include_files' );
function dia_woo_include_files() {
  $mypluginrequires = array(
    'dia-tabs-admin.php',
    'dia-tabs-frontend.php'
  );
  foreach ( $mypluginrequires as $need ) {
    include_once( plugin_dir_path( __FILE__ ) . $need );
  }
}
