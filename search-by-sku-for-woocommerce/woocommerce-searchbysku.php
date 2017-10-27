<?php

/*
  Plugin Name: Search By SKU - for Woocommerce
  Plugin URI: https://DiaMedicalUSA.com
  Description: The search functionality in woocommerce doesn't search by sku by default, or dia_search_extra_terms. This plugin adds this functionality to both the admin site and regular search
  Author: DiaMedical USA
  Version: 1.0
  Author URI: https://DiaMedicalUSA.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Needs to be after woocommerce has initiated but before posts_search filter has run..
add_filter('init', 'searchbysku_init', 11);

function searchbysku_init() {
    include_once 'wp-filters-extra.php';
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    //0.6.0 was incompatible with relenvassi plugin and gave impression of "doing nothing"

    if ( is_plugin_active( 'relevanssi/relevanssi.php' ) || !function_exists('wc_clean')) {
      // Plugin is activated
      // Use the old style of sku searching ...
        include_once 'wc-searchbysku-relevanssi-compat.php';
    }
    else{
        //If relenvassi is not installed do a more advanced search that works with woo widgets
        include_once 'wc-searchbysku-widget-compat.php';
    }

}




?>
