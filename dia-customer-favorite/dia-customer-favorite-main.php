<?php
/*
Plugin Name: DiaMedical USA Customer Favorite
Plugin URI: robbenz.com
Description: This plugin will allow for a Shop admin to Add a customer favorite button to the gallery & product page
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/

// check to make sure woocommerce is active -- and break if its not
add_action( 'admin_init', 'dia_cust_fav_check_woocommerce' );
function dia_cust_fav_check_woocommerce() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'dia_cust_fav_woo_fail_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

// throw error code if its not active
function dia_cust_fav_woo_fail_notice(){
    ?><div class="error"><p>Sorry, but this plugin requires Woocommerce to be installed and active, you idiot.</p></div><?php
}

// include the respective php files after successful activation
add_action( 'init', 'dia_cust_fav_include_files' );
function dia_cust_fav_include_files() {
    $mypluginrequires = array(
      'dia-customer-favorite-admin.php',
      'dia-customer-favorite-frontend.php'
    );
    foreach ( $mypluginrequires as $need ) {
      include_once( plugin_dir_path( __FILE__ ) . $need );
    }
}

// include JS for admin stuff
add_action( 'admin_enqueue_scripts', 'dia_cust_fav_admin_js_script' );
function dia_cust_fav_admin_js_script() {
    wp_enqueue_script('dia-cust-fav-admin-js', plugins_url( '/js/dia-customer-fav-admin-js.js', __FILE__ ), array('jquery'));
    wp_register_style( 'dia-cust-fav-admin-css', plugins_url('/css/dia-customer-fav-admin-css.css', __FILE__) );
    wp_enqueue_style( 'dia-cust-fav-admin-css' );
}
