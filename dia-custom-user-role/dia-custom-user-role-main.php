<?php
/*
Plugin Name: DiaMedical USA Custom User Role
Plugin URI: robbenz.com
Description: This Plugin will allow for a Shop Admin and a Shop Observer eeach role will have respective capibilities, it will allow create an admin meta box with applicaible Dia-product-meta for shop admin to edit, and shop observe to read only
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/


// check to make sure woocommerce is active -- and break if its not
add_action( 'admin_init', 'dia_user_roles_check_woocommerce' );
function dia_user_roles_check_woocommerce() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'dia_user_roles_woo_fail_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

// throw error code if its not active
function dia_user_roles_woo_fail_notice(){
    ?><div class="error"><p>Sorry, but this plugin requires Woocommerce to be installed and active, you idiot.</p></div><?php
}

// include the respective php files after successful activation
add_action( 'init', 'dia_user_roles_include_files' );
function dia_user_roles_include_files() {
  if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
    $mypluginrequires = array(
      'dia-user-roles-admin.php',
      'dia-user-roles-frontend.php'
    );
    foreach ( $mypluginrequires as $need ) {
      include_once( plugin_dir_path( __FILE__ ) . $need );
    }
  }
}

// include JS for admin stuff
add_action( 'admin_enqueue_scripts', 'dia_user_roles_admin_js_script' );
function dia_user_roles_admin_js_script() {
    wp_enqueue_script('dia-user-roles-admin-js', plugins_url( '/js/dia-user-roles-admin-js.js', __FILE__ ), array('jquery'));
    wp_register_style( 'custom_wp_admin_css', plugins_url('/css/admin-style.css', __FILE__) );
    wp_enqueue_style( 'custom_wp_admin_css' );
}

// include front end css
add_action( 'wp_enqueue_scripts', 'dia_user_roles_admin_css' );
function dia_user_roles_admin_css() {
    wp_register_style( 'dia-user-roles-admin-css', plugins_url('/css/dia-user-roles-css.css', __FILE__) );
    wp_enqueue_style( 'dia-user-roles-admin-css' );
    wp_register_script('front-end-js', plugins_url( '/js/front-end-js.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('front-end-js');
}
