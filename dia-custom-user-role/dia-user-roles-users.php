<?php


add_action( 'admin_init', 'dia_userss_remove_menu_pages' );
function dia_userss_remove_menu_pages() {

    global $user_ID;
// edit.php?post_type=shop_coupon
    if ( current_user_can( 'shop_manager' ) ) {
      remove_menu_page('edit.php?post_type=soliloquy');
      remove_menu_page('edit.php?post_type=shop_order');
      remove_menu_page('edit.php?post_type=scroll-triggered-box');
      remove_menu_page('authorhreview');
      remove_menu_page('upload.php');
      remove_menu_page('link-manager.php');
      remove_menu_page('edit-comments.php');
      remove_menu_page('edit.php?post_type=page');
      remove_menu_page('plugins.php');
      remove_menu_page('themes.php');
      remove_menu_page('users.php');
      remove_menu_page('tools.php');
      remove_menu_page('options-general.php');
      remove_menu_page('easy-modal');
    }
}
