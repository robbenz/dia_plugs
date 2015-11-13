<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

@set_time_limit(86400);
@ini_set("memory_limit","640M");

global $wpdb;

global $wc_predictive_search;
$wc_predictive_search->install_databases();

global $wc_ps_synch;
$wc_ps_synch->synch_full_database();

global $wc_ps_keyword_data;
$list_old_ps_keywords = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key= '_predictive_search_focuskw' AND meta_value != '' " );
if ( is_array( $list_old_ps_keywords ) && count( $list_old_ps_keywords ) > 0 ) {
	foreach ( $list_old_ps_keywords as $post_data ) {
		$item_existed = $wc_ps_keyword_data->get_item( $post_data->post_id );
		if ( NULL == $item_existed ) {
			$wc_ps_keyword_data->insert_item( $post_data->post_id, $post_data->meta_value );
		}
	}
}

global $wc_ps_exclude_data;
$woocommerce_search_exclude_products = get_option( 'woocommerce_search_exclude_products', array() );
if ( is_array( $woocommerce_search_exclude_products ) && count( $woocommerce_search_exclude_products ) > 0 ) {
	foreach ( $woocommerce_search_exclude_products as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'product' );
	}
}

$woocommerce_search_exclude_p_categories = get_option( 'woocommerce_search_exclude_p_categories', array() );
if ( is_array( $woocommerce_search_exclude_p_categories ) && count( $woocommerce_search_exclude_p_categories ) > 0 ) {
	foreach ( $woocommerce_search_exclude_p_categories as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'product_cat' );
	}
}

$woocommerce_search_exclude_p_tags = get_option( 'woocommerce_search_exclude_p_tags', array() );
if ( is_array( $woocommerce_search_exclude_p_tags ) && count( $woocommerce_search_exclude_p_tags ) > 0 ) {
	foreach ( $woocommerce_search_exclude_p_tags as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'product_tag' );
	}
}

$woocommerce_search_exclude_posts = get_option( 'woocommerce_search_exclude_posts', array() );
if ( is_array( $woocommerce_search_exclude_posts ) && count( $woocommerce_search_exclude_posts ) > 0 ) {
	foreach ( $woocommerce_search_exclude_posts as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'post' );
	}
}

$woocommerce_search_exclude_pages = get_option( 'woocommerce_search_exclude_pages', array() );
if ( is_array( $woocommerce_search_exclude_pages ) && count( $woocommerce_search_exclude_pages ) > 0 ) {
	foreach ( $woocommerce_search_exclude_pages as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'page' );
	}
}

flush_rewrite_rules();

