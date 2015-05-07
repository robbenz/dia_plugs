<?php
/*
Plugin Name: WooCommerce Predictive Search PRO
Description: WooCommerce Predictive Search - featuring "Smart Search" technology. Give your store customers the most awesome search experience on the web via widgets, shortcodes, Search results pages and the Predictive Search function.
Version: 2.4.1
Author: A3 Revolution
Author URI: http://www.a3rev.com/
Requires at least: 3.7
Tested up to: 4.1.1
License: GPLv2 or later

	WooCommerce Predictive Search. Plugin for the WooCommerce plugin.
	Copyright © 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define( 'WOOPS_FILE_PATH', dirname(__FILE__) );
define( 'WOOPS_DIR_NAME', basename(WOOPS_FILE_PATH) );
define( 'WOOPS_FOLDER', dirname(plugin_basename(__FILE__)) );
define( 'WOOPS_NAME', plugin_basename(__FILE__) );
define( 'WOOPS_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WOOPS_JS_URL',  WOOPS_URL . '/assets/js' );
define( 'WOOPS_CSS_URL',  WOOPS_URL . '/assets/css' );
define( 'WOOPS_IMAGES_URL',  WOOPS_URL . '/assets/images' );
define( 'WOOPS_WP_TESTED', '4.1.1' );
if(!defined("WOO_PREDICTIVE_SEARCH_MANAGER_URL"))
    define("WOO_PREDICTIVE_SEARCH_MANAGER_URL", "http://a3api.com/plugins");
if(!defined("WOO_PREDICTIVE_SEARCH_DOCS_URI"))
    define("WOO_PREDICTIVE_SEARCH_DOCS_URI", "http://docs.a3rev.com/user-guides/woocommerce/woo-predictive-search/");

// Predictive Search API
include('includes/class-legacy-api.php');

include('admin/admin-ui.php');
include('admin/admin-interface.php');

include('classes/class-wpml-functions.php');

include('admin/admin-pages/predictive-search-page.php');

include('admin/admin-init.php');

include 'classes/class-wc-predictive-search-filter.php';
include 'classes/class-wc-predictive-search.php';
include 'classes/class-wc-predictive-search-shortcodes.php';
include 'classes/class-wc-predictive-search-metabox.php';
include 'classes/class-wc-predictive-search-bulk-quick-editions.php';
include 'classes/class-wc-predictive-search-backbone.php';
include 'widget/wc-predictive-search-widgets.php';

// Editor
include 'tinymce3/tinymce.php';

include 'admin/wc-predictive-search-init.php';

include 'upgrade/plugin_upgrade.php';

/**
* Call when the plugin is activated
*/
register_activation_hook(__FILE__,'wc_predictive_install');
register_deactivation_hook(__FILE__,'wc_predictive_deactivate');

function wc_predictive_uninstall() {
	if ( get_option('woocommerce_search_clean_on_deletion') == 'yes' ) {
		delete_option('woocommerce_search_text_lenght');
		delete_option('woocommerce_search_result_items');
		delete_option('woocommerce_search_sku_enable');
		delete_option('woocommerce_search_price_enable');
		delete_option('woocommerce_search_addtocart_enable');
		delete_option('woocommerce_search_categories_enable');
		delete_option('woocommerce_search_tags_enable');
		delete_option('woocommerce_search_box_text');
		delete_option('woocommerce_search_page_id');
		delete_option('woocommerce_search_exclude_products');

		delete_option('woocommerce_search_exclude_p_categories');
		delete_option('woocommerce_search_exclude_p_tags');
		delete_option('woocommerce_search_exclude_posts');
		delete_option('woocommerce_search_exclude_pages');
		delete_option('woocommerce_search_focus_enable');
		delete_option('woocommerce_search_focus_plugin');
		delete_option('woocommerce_search_product_items');
		delete_option('woocommerce_search_p_sku_items');
		delete_option('woocommerce_search_p_cat_items');
		delete_option('woocommerce_search_p_tag_items');
		delete_option('woocommerce_search_post_items');
		delete_option('woocommerce_search_page_items');
		delete_option('woocommerce_search_character_max');
		delete_option('woocommerce_search_width');
		delete_option('woocommerce_search_padding_top');
		delete_option('woocommerce_search_padding_bottom');
		delete_option('woocommerce_search_padding_left');
		delete_option('woocommerce_search_padding_right');
		delete_option('woocommerce_search_custom_style');
		delete_option('woocommerce_search_global_search');

		delete_option('woocommerce_search_enable_google_analytic');
		delete_option('woocommerce_search_google_analytic_id');
		delete_option('woocommerce_search_google_analytic_query_parameter');

		delete_option('woocommerce_search_clean_on_deletion');

		delete_post_meta_by_key('_predictive_search_focuskw');

		wp_delete_post( get_option('woocommerce_search_page_id') , true );

		global $wpdb;
		$string_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}icl_strings WHERE context='WooCommerce Predictive Search' ");
		if ( is_array( $string_ids ) && count( $string_ids ) > 0 ) {
			$str = join(',', array_map('intval', $string_ids));
			$wpdb->query("
				DELETE s.*, t.* FROM {$wpdb->prefix}icl_strings s LEFT JOIN {$wpdb->prefix}icl_string_translations t ON s.id = t.string_id
				WHERE s.id IN ({$str})");
			$wpdb->query("DELETE FROM {$wpdb->prefix}icl_string_positions WHERE string_id IN ({$str})");
		}
	}
}
if ( get_option('woocommerce_search_clean_on_deletion') == 'yes' ) {
	register_uninstall_hook( __FILE__, 'wc_predictive_uninstall' );
}
?>