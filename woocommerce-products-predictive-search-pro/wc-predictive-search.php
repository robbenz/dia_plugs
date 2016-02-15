<?php
/*
Plugin Name: WooCommerce Predictive Search PRO
Description: WooCommerce Predictive Search - featuring "Smart Search" technology. Give your store customers the most awesome search experience on the web via widgets, shortcodes, Search results pages and the Predictive Search function.
Version: 3.3.0
Author: A3 Revolution
Author URI: http://www.a3rev.com/
Requires at least: 4.1
Tested up to: 4.4.2
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
define( 'WOOPS_DIR', WP_PLUGIN_DIR . '/' . WOOPS_FOLDER);
define( 'WOOPS_JS_URL',  WOOPS_URL . '/assets/js' );
define( 'WOOPS_CSS_URL',  WOOPS_URL . '/assets/css' );
define( 'WOOPS_IMAGES_URL',  WOOPS_URL . '/assets/images' );
if(!defined("WOO_PREDICTIVE_SEARCH_MANAGER_URL"))
    define("WOO_PREDICTIVE_SEARCH_MANAGER_URL", "http://a3api.com/plugins");
if(!defined("WOO_PREDICTIVE_SEARCH_DOCS_URI"))
    define("WOO_PREDICTIVE_SEARCH_DOCS_URI", "http://docs.a3rev.com/user-guides/woocommerce/woo-predictive-search/");

define( 'WOOPS_VERSION', '3.3.0' );

// Predictive Search API
include('includes/class-legacy-api.php');

include('admin/admin-ui.php');
include('admin/admin-interface.php');

include 'classes/class-wc-predictive-search-functions.php';
include('classes/class-wpml-functions.php');

include('admin/admin-pages/predictive-search-page.php');

include('admin/admin-init.php');

include 'classes/data/class-wc-ps-keyword-data.php';
include 'classes/data/class-wc-ps-product-sku-data.php';
include 'classes/data/class-wc-ps-postmeta-data.php';
include 'classes/data/class-wc-ps-exclude-data.php';
include 'classes/data/class-wc-ps-term-relationships-data.php';
include 'classes/data/class-wc-ps-posts-data.php';
include 'classes/data/class-wc-ps-product-categories-data.php';
include 'classes/data/class-wc-ps-product-tags-data.php';

include('includes/class-wc-predictive-search.php');

include 'classes/class-wc-predictive-search-filter.php';
include 'classes/class-wc-predictive-search-shortcodes.php';
include 'classes/class-wc-predictive-search-metabox.php';
include 'classes/class-wc-predictive-search-bulk-quick-editions.php';
include 'classes/class-wc-predictive-search-backbone.php';
include 'widget/wc-predictive-search-widgets.php';

include 'classes/class-wc-predictive-search-synch.php';

// Editor
include 'tinymce3/tinymce.php';

include 'admin/wc-predictive-search-init.php';

include 'upgrade/plugin_upgrade.php';

/**
* Call when the plugin is activated
*/
register_activation_hook(__FILE__,'wc_predictive_install');
register_deactivation_hook(__FILE__,'wc_predictive_deactivate');

?>