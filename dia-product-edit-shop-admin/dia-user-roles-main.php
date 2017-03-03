<?php
/*
Plugin Name: DiaMedical Shop Admin Editing
Plugin URI: robbenz.com
Description: This plugin will allow for a custom user role that can only edit certain things about our products, it will allow create custom product meta that our shop admins will need to use such as cost, supplier, and list price
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/

// check to make sure woocommerce is active -- and break if its not
add_action( 'admin_init', 'dia_users_check_woocommerce' );
function dia_users_check_woocommerce() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'dia_users_woo_fail_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

// throw error code if its not active
function dia_users_woo_fail_notice(){
    ?><div class="error"><p>Sorry, but this plugin requires Woocommerce to be installed and active, you idiot.</p></div><?php
}


// include the respective php files after successful activation
add_action( 'init', 'dia_users_include_files' );
function dia_users_include_files() {
  if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
    $mypluginrequires = array(
      'dia-custom-users-admin.php',
      'dia-custom-users-frontend.php'
    );
    foreach ( $mypluginrequires as $need ) {
      include_once( plugin_dir_path( __FILE__ ) . $need );
    }
  }
}


// include JS for admin stuff
add_action( 'admin_enqueue_scripts', 'dia_users_admin_js_script' );
function dia_users_admin_js_script() {
    wp_enqueue_script('dia-users-admin-js', plugins_url( '/js/dia-users-admin-js.js', __FILE__ ), array('jquery'));
}
