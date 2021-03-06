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
function dia_user_roles_woo_fail_notice(){
    ?><div class="error"><p>Sorry, but this plugin requires Woocommerce to be installed and active, you idiot.</p></div><?php
}

// include the respective php files after successful activation
add_action( 'init', 'dia_user_roles_include_files' );
function dia_user_roles_include_files() {
    $mypluginrequires = array(
      'dia-user-roles-admin.php',
      'dia-user-roles-users.php',
      'dia-user-roles-frontend.php'
    );
    foreach ( $mypluginrequires as $need ) {
      include_once( plugin_dir_path( __FILE__ ) . $need );
    }
}

// include JS for admin stuff
add_action( 'admin_enqueue_scripts', 'dia_user_roles_admin_js_script' );
function dia_user_roles_admin_js_script() {
  global $pagenow;
  $screen = get_current_screen();
  if( $pagenow == 'post.php' && $screen->post_type === 'product' ) {
    wp_enqueue_script('dia-user-roles-admin-js', plugins_url( '/js/dia-user-roles-admin-js.js', __FILE__ ), array('jquery'));
    wp_register_style( 'custom_wp_admin_css', plugins_url('/css/admin-style.css', __FILE__) );
    wp_enqueue_style( 'custom_wp_admin_css' );
  }
}

// include front end css
add_action( 'wp_enqueue_scripts', 'dia_user_roles_admin_css' );
function dia_user_roles_admin_css() {
    wp_register_style( 'dia-user-roles-admin-css', plugins_url('/css/dia-user-roles-css.css', __FILE__) );
    wp_enqueue_style( 'dia-user-roles-admin-css' );
    wp_register_script('front-end-js', plugins_url( '/js/front-end-js.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('front-end-js');
}


/* Really nice clean admin menu debugging
if (!function_exists('debug_admin_menus')):
  function debug_admin_menus() {
    if ( !is_admin())
    return;
    global $submenu, $menu, $pagenow;
    if ( current_user_can('manage_options') ) { // ONLY DO THIS FOR ADMIN
      if( $pagenow == 'index.php' ) {  // PRINTS ON DASHBOARD
            echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
            echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
        }
    }
}
add_action( 'admin_notices', 'debug_admin_menus' );
endif;
*/

// Debug user role caps
/*
add_action( 'admin_notices', 'debug_user_roles' );
function debug_user_roles() {
  global $pagenow;
  if( $pagenow == 'index.php' ) {
    $MYrole = get_role("seo_specialist");
    echo '<pre>';
    print_r($MYrole->capabilities);
    echo '</pre>';

    $MY_other_role = get_role("shop_manager");
    echo '<pre>';
    print_r($MY_other_role->capabilities);
    echo '</pre>';

  }
}
*/
