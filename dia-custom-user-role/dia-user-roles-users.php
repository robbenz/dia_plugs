<?php

/*** TWO NEW CUSTOM USER ROLES  ***/
$result = add_role( 'shop_observer', __( 'Shop Observer' ), array( 'read' => true ) );
$SEOresult = add_role( 'seo_specialist', __(
  'SEO Specialist' ), array(
    'read' => true,
    'edit_posts' => true,
    'edit_pages' => true,
    'edit_others_posts' => true,
    'create_posts' => false,
    'publish_posts' => true,
    'edit_themes' => false,
    'install_plugins' => false,
    'update_plugin' => false,
    'update_core' => false
  )
);
/*** END ***/

/*** ADD / REMOVE CAPS FOR THESE USERS  ***/
add_action( 'admin_init', 'dia_seo_special_add_caps' );
function dia_seo_special_add_caps() {
  $role = get_role("seo_specialist");


  $SEO_add_caps = array (  // Add these to SEO role
    "manage_woocommerce",
    "edit_product",
    "read_product",
    "edit_products",
    "edit_others_products",
    "read_private_products",
    "edit_private_products",
    "edit_published_products",
    "edit_product_terms",
    "assign_product_terms"
  );
  foreach ( $SEO_add_caps as $cap ) {
    $role->add_cap( $cap );
  }

  $SEO_deny_caps = array (  // Deny these to SEO role
    "woocommerce_duplicate_product_capability",
    "delete_products",
    "delete_published_products",
    "delete_others_products",
    "delete_product",
    "edit_shop_order",
    "read_shop_order",
    "delete_shop_order",
    "edit_shop_orders",
    "edit_others_shop_orders",
    "publish_shop_orders",
    "read_private_shop_orders",
    "delete_shop_orders",
    "delete_private_shop_orders",
    "delete_published_shop_orders",
    "delete_others_shop_orders",
    "edit_private_shop_orders",
    "edit_published_shop_orders",
    "manage_shop_order_terms",
    "edit_shop_order_terms",
    "delete_shop_order_terms",
    "assign_shop_order_terms",
    "edit_shop_coupon",
    "read_shop_coupon",
    "delete_shop_coupon",
    "edit_shop_coupons",
    "edit_others_shop_coupons",
    "publish_shop_coupons",
    "read_private_shop_coupons",
    "delete_shop_coupons",
    "delete_private_shop_coupons",
    "delete_published_shop_coupons",
    "delete_others_shop_coupons",
    "edit_private_shop_coupons",
    "edit_published_shop_coupons",
    "manage_shop_coupon_terms",
    "edit_shop_coupon_terms",
    "delete_shop_coupon_terms",
    "assign_shop_coupon_terms",
    "view_woocommerce_reports",
    "delete_product_terms",
    "manage_product_terms",
    "delete_private_products",
    "publish_products",
  );
  foreach ( $SEO_deny_caps as $dcap ) {
    $role->remove_cap( $dcap );
  }

}
/*** END ***/

/*** ADMIN LEFT MENU ***/
add_action( 'admin_menu', 'dia_users_remove_menu_pages' );
function dia_users_remove_menu_pages() {
  global $user_ID, $submenu;
  if ( current_user_can( 'shop_manager' ) || current_user_can( 'seo_specialist' ) ) {
    remove_menu_page('edit.php?post_type=soliloquy');
    remove_menu_page('edit.php?post_type=shop_order');
    remove_menu_page('edit.php?post_type=scroll-triggered-box');
    remove_menu_page('authorhreview');
    remove_menu_page('index.php');
    remove_menu_page('upload.php');
    remove_menu_page('link-manager.php');
    remove_menu_page('edit-comments.php');
    remove_menu_page('edit.php?post_type=page');
    remove_menu_page('plugins.php');
    remove_menu_page('themes.php');
    remove_menu_page('users.php');
    remove_menu_page('edit.php');
    remove_menu_page('tools.php');
    remove_menu_page('options-general.php');
    remove_menu_page('easy-modal');
    remove_menu_page('woocommerce');
    remove_submenu_page('edit.php?post_type=product', 'product_attributes' );
    remove_submenu_page('edit.php?post_type=product', 'global_addons');
    unset( $submenu['edit.php?post_type=product'][10] ); // add new product
    unset( $submenu['edit.php?post_type=product'][16] ); // Tags page
    unset( $submenu['edit.php?post_type=product'][17] ); // shipping class page
  }
}
/*** END ***/

/*** EDIT PRODUCT META BOXES ***/
add_action( 'add_meta_boxes' , 'dia_users_remove_metaboxes', 50 );
function dia_users_remove_metaboxes() {
  if ( current_user_can( 'shop_manager' ) || current_user_can( 'seo_specialist' ) ) {
    remove_meta_box( 'postexcerpt' , 'product' , 'normal' );
    remove_meta_box( 'commentsdiv' , 'product' , 'normal' );
    remove_meta_box( 'tagsdiv-product_tag' , 'product' , 'side' );
    remove_meta_box( 'product_catdiv' , 'product' , 'side' );
    remove_meta_box( 'yith-ywraq-metabox' , 'product' , 'normal' );
  }
  if ( current_user_can( 'seo_specialist' ) ) {
    remove_meta_box( 'dia-user-roles-meta-box' , 'product' , 'normal' );
    remove_meta_box( 'dia-cust-fav-role-meta-box' , 'product' , 'normal' );
    remove_meta_box( 'woocommerce-product-data' , 'product' , 'normal' );
    remove_meta_box( 'woocommerce-product-images' , 'product' , 'side' );
  }
  if ( current_user_can( 'shop_manager' ) ) {
    remove_meta_box( 'wpseo_meta' , 'product' , 'normal' );
  }
}
/*** END ***/

/*** ADMIN TOP BAR MENU ***/
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );
function remove_admin_bar_links() {
  global $wp_admin_bar;
  if ( current_user_can( 'shop_manager' ) || current_user_can( 'seo_specialist' ) ) {
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('wpseo-menu');
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
  }

}
/*** END ***/

/*** REMOVE DUPLICATE AND QUICK EDIT LINKS ***/
add_filter( 'post_row_actions', 'dia_users_remove_row_actions', 15, 2 );
function dia_users_remove_row_actions( $actions ) {
  if ( current_user_can( 'shop_manager' ) || current_user_can( 'seo_specialist' ) ) {
    if ( get_post_type() === 'product' ) {
      unset( $actions['inline hide-if-no-js'] );   // QUICK EDIT
      unset( $actions['duplicate'] );
      return $actions;
    }
  }
}
/*** END ***/


function wpse_203917_admin_bar_menu( $wp_admin_bar ) {
    if ( ! $node = $wp_admin_bar->get_node( 'my-account' ) )
        return;

    $roles = wp_get_current_user()->roles;

    $node->title .= sprintf( '  | (%s)', implode( ', ', $roles ) );

    $wp_admin_bar->add_node( $node );
}

add_action( 'admin_bar_menu', 'wpse_203917_admin_bar_menu' );
