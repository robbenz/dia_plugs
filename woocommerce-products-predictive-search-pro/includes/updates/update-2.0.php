<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$exclude_products = get_option('woocommerce_search_exclude_products', '');
$exclude_p_categories = get_option('woocommerce_search_exclude_p_categories', '');
$exclude_p_tags = get_option('woocommerce_search_exclude_p_tags', '');
$exclude_posts = get_option('woocommerce_search_exclude_posts', '');
$exclude_pages = get_option('woocommerce_search_exclude_pages', '');

if ($exclude_products !== false) {
	$exclude_products_array = explode(",", $exclude_products);
	if (is_array($exclude_products_array) && count($exclude_products_array) > 0) {
		$exclude_products_array_new = array();
		foreach ($exclude_products_array as $exclude_products_item) {
			if ( trim($exclude_products_item) > 0) $exclude_products_array_new[] = trim($exclude_products_item);
		}
		$exclude_products = $exclude_products_array_new;
	} else {
		$exclude_products = array();
	}
	update_option('woocommerce_search_exclude_products', (array) $exclude_products);
} else {
	update_option('woocommerce_search_exclude_products', array());
}

if ($exclude_posts !== false) {
	$exclude_posts_array = explode(",", $exclude_posts);
	if (is_array($exclude_posts_array) && count($exclude_posts_array) > 0) {
		$exclude_posts_array_new = array();
		foreach ($exclude_posts_array as $exclude_posts_item) {
			if ( trim($exclude_posts_item) > 0) $exclude_posts_array_new[] = trim($exclude_posts_item);
		}
		$exclude_posts = $exclude_posts_array_new;
	} else {
		$exclude_posts = array();
	}
	update_option('woocommerce_search_exclude_posts', (array) $exclude_posts);
} else {
	update_option('woocommerce_search_exclude_posts', array());
}

if ($exclude_pages !== false) {
	$exclude_pages_array = explode(",", $exclude_pages);
	if (is_array($exclude_pages_array) && count($exclude_pages_array) > 0) {
		$exclude_pages_array_new = array();
		foreach ($exclude_pages_array as $exclude_pages_item) {
			if ( trim($exclude_pages_item) > 0) $exclude_pages_array_new[] = trim($exclude_pages_item);
		}
		$exclude_pages = $exclude_pages_array_new;
	} else {
		$exclude_pages = array();
	}
	update_option('woocommerce_search_exclude_pages', (array) $exclude_pages);
} else {
	update_option('woocommerce_search_exclude_pages', array());
}

if ($exclude_p_categories !== false) {
	$exclude_p_categories_array = explode(",", $exclude_p_categories);
	if (is_array($exclude_p_categories_array) && count($exclude_p_categories_array) > 0) {
		$exclude_p_categories_array_new = array();
		foreach ($exclude_p_categories_array as $exclude_p_categories_item) {
			if ( trim($exclude_p_categories_item) > 0) $exclude_p_categories_array_new[] = trim($exclude_p_categories_item);
		}
		$exclude_p_categories = $exclude_p_categories_array_new;
	} else {
		$exclude_p_categories = array();
	}
	update_option('woocommerce_search_exclude_p_categories', (array) $exclude_p_categories);
} else {
	update_option('woocommerce_search_exclude_p_categories', array());
}

if ($exclude_p_tags !== false) {
	$exclude_p_tags_array = explode(",", $exclude_p_tags);
	if (is_array($exclude_p_tags_array) && count($exclude_p_tags_array) > 0) {
		$exclude_p_tags_array_new = array();
		foreach ($exclude_p_tags_array as $exclude_p_tags_item) {
			if ( trim($exclude_p_tags_item) > 0) $exclude_p_tags_array[] = trim($exclude_p_tags_item);
		}
		$exclude_p_tags = $exclude_p_tags_array;
	} else {
		$exclude_p_tags = array();
	}
	update_option('woocommerce_search_exclude_p_tags', (array) $exclude_p_tags);
} else {
	update_option('woocommerce_search_exclude_p_tags', array());
}