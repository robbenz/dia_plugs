<?php
/*
Plugin Name: WooCommerce Upload Files
Description: WCUF plugin lets your customers to attach files to their orders.
Author: Lagudi Domenico
Version: 2.7
*/

define('wcuf_PLUGIN_PATH', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );


	
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
{
	if(!class_exists('WCUF_Email'))
			require_once('include/WCUF_Email.php');
	if(!class_exists('WCUF_File'))
	{
			require_once('include/WCUF_File.php');
			$file_model = new WCUF_File();
	} 
	if(!class_exists('WCUF_Option'))
	{
			require_once('include/WCUF_Option.php');
			$option_model = new WCUF_Option();
	}
	if(!class_exists('WCUF_AdminMenu')) 
		require_once('WCUF_AdminMenu.php');
	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}
	if(!class_exists('WCUF_WooCommerceAddon'))
	{
			require_once('WCUF_OrderDetailAddon.php');
			$woocommerce_addon = new WCUF_OrderDetailAddon();
	}
	if(!class_exists('WCUF_OrdersTableAddon'))
	{
			require_once('WCUF_OrdersTableAddon.php');
			$woocommerce_orderstable_addon = new WCUF_OrdersTableAddon();
	}
	
	add_action('admin_menu', 'wcuf_init_admin_panel');
	add_action( 'admin_init', 'wcuf_register_settings');
	/* add_action( 'manage_product_posts_custom_column', array($woocommerce_orderstable_addon, 'manage_upload_counter_column'), 10, 2 );
	add_filter( 'manage_edit-product_columns', array($woocommerce_orderstable_addon, 'add_upload_counter_column'),15 ); */
} 


 function wcuf_register_settings()
{ 
	/*register_setting('wcuf_files_fields_meta_groups', 'wcuf_files_fields_meta');
	wp_enqueue_script('tiny_mce');
	wp_enqueue_script('wcuf-js', wcuf_PLUGIN_PATH.'/js/wcst.js', array('jquery')); */
} 

function wcuf_init_admin_panel()
{
	load_plugin_textdomain('woocommerce-files-upload', false, basename( dirname( __FILE__ ) ) . '/languages' );
	add_submenu_page('woocommerce', __('Upload files options','woocommerce-files-upload'), __('Upload files options','woocommerce-files-upload'), 'edit_shop_orders', 'woocommerce-files-upload', 'render_wcuf_option_page');

}

function render_wcuf_option_page()
{
	$page = new WCUF_AdminMenu();
	$page->render_page();
}
function wcuf_var_dump($var)
{
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}
?>