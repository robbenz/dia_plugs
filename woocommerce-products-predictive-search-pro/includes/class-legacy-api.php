<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Predictive Search Legacy API Class
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Predictive_Search_Legacy_API {

	/** @var string $base the route base */
	protected $base = '/wc_ps_legacy_api';
	protected $base_tag = 'wc_ps_legacy_api';

	/**
	* Default contructor
	*/
	public function __construct() {
		add_action( 'woocommerce_api_' . $this->base_tag, array( $this, 'wc_ps_api_handler' ) );
	}

	public function get_legacy_api_url() {

		$legacy_api_url = WC()->api_request_url( $this->base_tag );
		$legacy_api_url = str_replace( array( 'https:', 'http:' ), '', $legacy_api_url );

		return apply_filters( 'wc_ps_legacy_api_url', $legacy_api_url );
	}

	public function wc_ps_api_handler() {
		if ( isset( $_REQUEST['action'] ) ) {
			$action = addslashes( trim( $_REQUEST['action'] ) );
			switch ( $action ) {
				case 'get_result_popup' :
					$this->get_result_popup();
				break;

				case 'get_results' :
					$this->get_all_results();
				break;
			}
		}
	}

	public function get_result_popup() {
		@ini_set('display_errors', false );
		global $woocommerce_search_page_id;
		global $wc_predictive_id_excludes;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = $_REQUEST['lang'];
		}

		$rs_items = array();
		$row = 6;
		$text_lenght = 100;
		$show_price = 0;
		$search_keyword = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$found_items = false;
		$total_product = $total_p_sku = $total_post = $total_page = $total_pcat = $total_ptag = 0;
		$items_search_default = WC_Predictive_Search_Widgets::get_items_search();
		$search_in_default = array();
		foreach ( $items_search_default as $key => $data ) {
			if ( $data['number'] > 0 ) {
				$search_in_default[$key] = $data['number'];
			}
		}
		if ( isset($_REQUEST['row']) && $_REQUEST['row'] > 0) $row = stripslashes( strip_tags( $_REQUEST['row'] ) );
		if ( isset($_REQUEST['text_lenght']) && $_REQUEST['text_lenght'] >= 0) $text_lenght = stripslashes( strip_tags( $_REQUEST['text_lenght'] ) );
		if ( isset($_REQUEST['show_price']) && trim($_REQUEST['show_price']) != '') $show_price = stripslashes( strip_tags( $_REQUEST['show_price'] ) );
		if ( $show_price == 1 ) $show_price = true; else $show_price = false;
		if ( isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['q'] ) );
		if ( isset($_REQUEST['pcat']) && trim($_REQUEST['pcat']) != '') $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		if ( isset($_REQUEST['ptag']) && trim($_REQUEST['ptag']) != '') $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		if ( isset($_REQUEST['scat']) && trim($_REQUEST['scat']) != '') $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		if ( isset($_REQUEST['stag']) && trim($_REQUEST['stag']) != '') $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		if ( isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = json_decode( stripslashes( $_REQUEST['search_in'] ), true );
		if ( ! is_array($search_in) || count($search_in) < 1 || array_sum($search_in) < 1) $search_in = $search_in_default;

		if ( $search_keyword != '' ) {
			$search_list = array();
			foreach ($search_in as $key => $number) {
				if ($number > 0)
					$search_list[$key] = $key;
			}

			$woocommerce_search_focus_enable = get_option( 'woocommerce_search_focus_enable' );
			$woocommerce_search_focus_plugin = get_option( 'woocommerce_search_focus_plugin' );
			$meta_query_args = array();
			if ( empty( $woocommerce_search_focus_enable ) || $woocommerce_search_focus_enable == 'yes' ) {
				$meta_query_args['relation'] = 'OR';
				$meta_query_args[] = array( 'key' => '_predictive_search_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE' );

				if ( $woocommerce_search_focus_plugin == 'yoast_seo_plugin' )
					$meta_query_args[] = array( 'key' => '_yoast_wpseo_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE' );
				else if ( $woocommerce_search_focus_plugin == 'all_in_one_seo_plugin' )
					$meta_query_args[] = array( 'key' => '_aioseop_keywords', 'value' => $search_keyword, 'compare' => 'LIKE' );
			}

			$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
			$search_nospecial = false;
			if ( $search_keyword != $search_keyword_nospecial ) $search_nospecial = true;

			$extra_parameter_product = '';
			$extra_parameter_post = '';

			$all_items = array();
			$product_list = array();
			$sku_list = array();
			$post_list = array();
			$page_list = array();
			$category_list = array();
			$tag_list = array();

			$permalink_structure = get_option( 'permalink_structure' );

			$args_product = array();
			if ( $pcat_slug != '' ) {
				$args_product['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $pcat_slug ) );
				if ( $permalink_structure == '' )
					$extra_parameter_product .= '&pcat='.$pcat_slug;
				else
					$extra_parameter_product .= '/pcat/'.$pcat_slug;
			} elseif ( $ptag_slug != '' ) {
				$args_product['tax_query'] = array( array( 'taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug ) );
				if ( $permalink_structure == '' )
					$extra_parameter_product .= '&ptag='.$ptag_slug;
				else
					$extra_parameter_product .= '/ptag/'.$ptag_slug;
			}

			$args_post = array();
			if ( $cat_slug != '' ) {
				$args_post['category_name'] = $cat_slug;
				$extra_parameter_post_admin .= '&scat='.$cat_slug;
				if ( $permalink_structure == '')
					$extra_parameter_post .= '&scat='.$cat_slug;
				else
					$extra_parameter_post .= '/scat/'.$cat_slug;
			} elseif ( $tag_slug != '') {
				$args_post['tag'] = $tag_slug;
				$extra_parameter_post_admin .= '&stag='.$tag_slug;
				if ( $permalink_structure == '')
					$extra_parameter_post .= '&stag='.$tag_slug;
				else
					$extra_parameter_post .= '/stag/'.$tag_slug;
			}

			if ( isset( $search_in['product'] ) && $search_in['product'] > 0 ) {
				$product_list = $this->get_product_results( $search_keyword, $search_in['product'], 0, $wc_predictive_id_excludes['exclude_products'], $meta_query_args, $args_product, $text_lenght, true, $show_price );
				$total_product = $product_list['total'];
				if ( $total_product > 0 ) {
					$found_items = true;
					$rs_items['product'] = $product_list['items'];
				}
			}

			if ( isset( $search_in['p_sku'] ) && $search_in['p_sku'] > 0 ) {
				$sku_list = $this->get_product_sku_results( $search_keyword, $search_in['p_sku'], 0, $wc_predictive_id_excludes['exclude_products'], $args_product, $text_lenght, true, $show_price );
				$total_p_sku = $sku_list['total'];
				if ( $total_p_sku > 0 ) {
					$found_items = true;
					$rs_items['p_sku'] = $sku_list['items'];
				}
			}

			if ( isset( $search_in['post'] ) && $search_in['post'] > 0 ) {
				$post_list = $this->get_post_results( $search_keyword, $search_in['post'], 0, $wc_predictive_id_excludes['exclude_posts'], $meta_query_args, $args_post, $text_lenght, 'post' );
				$total_post = $post_list['total'];
				if ( $total_post > 0 ) {
					$found_items = true;
					$rs_items['post'] = $post_list['items'];
				}
			}

			if ( isset( $search_in['page'] ) && $search_in['page'] > 0 ) {
				$page_list = $this->get_post_results( $search_keyword, $search_in['page'], 0, $wc_predictive_id_excludes['exclude_pages'], $meta_query_args, array(), $text_lenght, 'page' );
				$total_page = $page_list['total'];
				if ( $total_page > 0 ) {
					$found_items = true;
					$rs_items['page'] = $page_list['items'];
				}
			}

			if ( isset( $search_in['p_cat'] ) && $search_in['p_cat'] > 0 ) {
				$header_text = wc_ps_ict_t__( 'Product Categories', __('Product Categories', 'woops') );
				$category_list = $this->get_taxonomy_results( $search_keyword, $search_keyword_nospecial, $search_nospecial, $search_in['p_cat'], 0, $wc_predictive_id_excludes['exclude_p_categories'], $text_lenght, 'product_cat', 'p_cat', $header_text, $current_lang );
				$total_pcat = $category_list['total'];
				if ( $total_pcat > 0 ) {
					$found_items = true;
					$rs_items['p_cat'] = $category_list['items'];
				}
			}

			if ( isset( $search_in['p_tag'] ) && $search_in['p_tag'] > 0 ) {
				$header_text = wc_ps_ict_t__( 'Product Tags', __('Product Tags', 'woops') );
				$tag_list = $this->get_taxonomy_results( $search_keyword, $search_keyword_nospecial, $search_nospecial, $search_in['p_tag'], 0, $wc_predictive_id_excludes['exclude_p_tags'], $text_lenght, 'product_tag', 'p_tag', $header_text, $current_lang );
				$total_ptag = $tag_list['total'];
				if ( $total_ptag > 0 ) {
					$found_items = true;
					$rs_items['p_tag'] = $tag_list['items'];
				}
			}

			if ( $found_items === false ) {
				$all_items[] = array(
					'title' 	=> wc_ps_ict_t__( 'Nothing found', __('Nothing found for that name. Try a different spelling or name.', 'woops') ),
					'keyword'	=> $search_keyword,
					'type'		=> 'nothing'
				);
			} else {
				foreach ( $search_in as $key => $number ) {
					if ( $number > 0 ) {
						if ( isset( $rs_items[$key] ) ) $all_items = array_merge( $all_items, $rs_items[$key] );
					}
				}

				$search_other = $search_list;
				if ( $total_product < 1 )  { unset($search_list['product']); unset($search_other['product']);
				} elseif ($total_product <= $search_in['product']) { unset($search_list['product']); }

				if ( $total_p_sku < 1 ) { unset($search_list['p_sku']); unset($search_other['p_sku']);
				} elseif ($total_p_sku <= $search_in['p_sku']) { unset($search_list['p_sku']); }

				if ( $total_post < 1 ) { unset($search_list['post']); unset($search_other['post']);
				} elseif ($total_post <= $search_in['post']) { unset($search_list['post']); }

				if ( $total_page < 1 ) { unset($search_list['page']); unset($search_other['page']);
				} elseif ($total_page <= $search_in['page']) { unset($search_list['page']); }

				if ( $total_pcat < 1 ) { unset($search_list['p_cat']); unset($search_other['p_cat']);
				} elseif ($total_pcat <= $search_in['p_cat']) { unset($search_list['p_cat']); }

				if ( $total_ptag < 1 ) { unset($search_list['p_tag']); unset($search_other['p_tag']);
				} elseif ($total_ptag <= $search_in['p_tag']) { unset($search_list['p_tag']); }

				if ( count( $search_list ) > 0 ) {
					$rs_footer_html = '';
					foreach ($search_list as $other_rs) {
						if ( $permalink_structure == '')
							$search_in_parameter = '&search_in='.$other_rs;
						else
							$search_in_parameter = '/search-in/'.$other_rs;
						if ( $permalink_structure == '')
							$link_search = get_permalink( $woocommerce_search_page_id ).'&rs='. urlencode($search_keyword) .$search_in_parameter.$extra_parameter_product.$extra_parameter_post.'&search_other='.implode(",", $search_other);
						else
							$link_search = rtrim( get_permalink( $woocommerce_search_page_id ), '/' ).'/keyword/'. urlencode($search_keyword) .$search_in_parameter.$extra_parameter_product.$extra_parameter_post.'/search-other/'.implode(",", $search_other);
						$rs_item = '<a href="'.$link_search.'">'.$items_search_default[$other_rs]['name'].' <span class="see_more_arrow"></span></a>';
						$rs_footer_html .= "$rs_item";
					}
					$all_items[] = array(
						'title' 	=> $search_keyword,
						'keyword'	=> $search_keyword,
						'description'	=> $rs_footer_html,
						'type'		=> 'footer'
					);
				}
			}

			header( 'Content-Type: application/json', true, 200 );
			die( json_encode( $all_items ) );
		} else {
			header( 'Content-Type: application/json', true, 200 );
			die( json_encode( array() ) );
		}

	}

	public function get_all_results() {
		@ini_set('display_errors', false );
		global $wc_predictive_id_excludes;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = $_REQUEST['lang'];
		}

		$psp = 1;
		$row = 10;
		$search_keyword = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$extra_parameter_post ='';
		$search_in = 'product';

		if ( get_option('woocommerce_search_result_items') > 0  ) $row = get_option('woocommerce_search_result_items');

		if ( isset( $_REQUEST['psp'] ) && $_REQUEST['psp'] > 0 ) $psp = stripslashes( strip_tags( $_REQUEST['psp'] ) );
		if ( isset( $_REQUEST['q'] ) && trim( $_REQUEST['q'] ) != '' ) $search_keyword = stripslashes( strip_tags( $_REQUEST['q'] ) );
		if ( isset( $_REQUEST['pcat'] ) && trim( $_REQUEST['pcat'] ) != '' ) $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		if ( isset( $_REQUEST['ptag'] ) && trim( $_REQUEST['ptag'] ) != '' ) $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		if ( isset( $_REQUEST['scat'] ) && trim( $_REQUEST['scat'] ) != '' ) $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		if ( isset( $_REQUEST['stag'] ) && trim( $_REQUEST['stag'] ) != '' ) $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		if ( isset( $_REQUEST['search_in'] ) && trim( $_REQUEST['search_in'] ) != '' ) $search_in = stripslashes( strip_tags( $_REQUEST['search_in'] ) );

		$item_list = array( 'total' => 0, 'items' => array() );

		if ( $search_keyword != '' && $search_in != '') {
			$show_sku = false;
			$show_price = false;
			$show_addtocart = false;
			$show_categories = false;
			$show_tags = false;
			if ( get_option('woocommerce_search_sku_enable') == '' || get_option('woocommerce_search_sku_enable') == 'yes' ) $show_sku = true;
			if ( get_option('woocommerce_search_price_enable') == '' || get_option('woocommerce_search_price_enable') == 'yes' ) $show_price = true;
			if ( get_option('woocommerce_search_addtocart_enable') == '' || get_option('woocommerce_search_addtocart_enable') == 'yes' ) $show_addtocart = true;
			if ( get_option('woocommerce_search_categories_enable') == '' || get_option('woocommerce_search_categories_enable') == 'yes' ) $show_categories = true;
			if ( get_option('woocommerce_search_tags_enable') == '' || get_option('woocommerce_search_tags_enable') == 'yes' ) $show_tags = true;

			$text_lenght = get_option('woocommerce_search_text_lenght');

			$args_product = array();
			if ( $pcat_slug != '' ) {
				$args_product['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $pcat_slug ) );
			} elseif ( $ptag_slug != '' ) {
				$args_product['tax_query'] = array( array( 'taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $ptag_slug ) );
			}

			$args_post = array();
			if ( $cat_slug != '' ) {
				$args_post['category_name'] = $cat_slug;
			} elseif ( $tag_slug != '' ) {
				$args_post['tag'] = $tag_slug;
			}

			$start = ( $psp - 1) * $row;

			$woocommerce_search_focus_enable = get_option('woocommerce_search_focus_enable');
			$woocommerce_search_focus_plugin = get_option('woocommerce_search_focus_plugin');

			$meta_query_args = array();
			if ( empty($woocommerce_search_focus_enable) || $woocommerce_search_focus_enable == 'yes' ) {
				$meta_query_args['relation'] = 'OR';
				$meta_query_args[] = array( 'key' => '_predictive_search_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE');

				if ($woocommerce_search_focus_plugin == 'yoast_seo_plugin')
					$meta_query_args[] = array( 'key' => '_yoast_wpseo_focuskw', 'value' => $search_keyword, 'compare' => 'LIKE');
				elseif ($woocommerce_search_focus_plugin == 'all_in_one_seo_plugin')
					$meta_query_args[] = array( 'key' => '_aioseop_keywords', 'value' => $search_keyword, 'compare' => 'LIKE');
			}

			$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
			$search_nospecial = false;
			if ( $search_keyword != $search_keyword_nospecial ) $search_nospecial = true;

			if ( $search_in == 'product' ) {
				$item_list = $this->get_product_results( $search_keyword, $row, $start, $wc_predictive_id_excludes['exclude_products'], $meta_query_args, $args_product, $text_lenght, false, $show_price, $show_sku, $show_addtocart, $show_categories, $show_tags );
			} elseif ( $search_in == 'p_sku' ) {
				$item_list = $this->get_product_sku_results( $search_keyword, $row, $start, $wc_predictive_id_excludes['exclude_products'], $args_product, $text_lenght, false, $show_price, $show_sku, $show_addtocart, $show_categories, $show_tags );
			} elseif ( $search_in == 'post' ) {
				$item_list = $this->get_post_results( $search_keyword, $row, $start, $wc_predictive_id_excludes['exclude_posts'], $meta_query_args, $args_post, $text_lenght, 'post', false , $show_categories, $show_tags );
			} elseif ( $search_in == 'page' ) {
				$item_list = $this->get_post_results( $search_keyword, $row, $start, $wc_predictive_id_excludes['exclude_pages'], $meta_query_args, array(), $text_lenght, 'page', false );
			} elseif ( $search_in == 'p_cat' ) {
				$header_text = wc_ps_ict_t__( 'Product Categories', __('Product Categories', 'woops') );
				$item_list = $this->get_taxonomy_results( $search_keyword, $search_keyword_nospecial, $search_nospecial, $row, $start, $wc_predictive_id_excludes['exclude_p_categories'], $text_lenght, 'product_cat', 'p_cat', $header_text, $current_lang, false );
			} elseif ( $search_in == 'p_tag' ) {
				$header_text = wc_ps_ict_t__( 'Product Tags', __('Product Tags', 'woops') );
				$item_list = $this->get_taxonomy_results( $search_keyword, $search_keyword_nospecial, $search_nospecial, $row, $start, $wc_predictive_id_excludes['exclude_p_tags'], $text_lenght, 'product_tag', 'p_tag', $header_text, $current_lang, false );
			}
		}

		header( 'Content-Type: application/json', true, 200 );
		die( json_encode( $item_list ) );
	}

	/**
	 * Get array product list
	 */
	public function get_product_results( $search_keyword, $row, $start = 0, $exclude_products = '', $meta_query_args = array(), $args_product = array(), $text_lenght = 100, $include_header = true , $show_price = true, $show_sku = false, $show_addtocart = false, $show_categories = false, $show_tags = false ) {

		$end_row = $row;
		$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'product', 'post_status' => 'publish', 'exclude' => $exclude_products, 'suppress_filters' => FALSE, 'ps_post_type' => 'product' );

		$args = array_merge( $args, $args_product );

		if ( count( $meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;

		$search_products = get_posts( $args );

		$total_product = count( $search_products );
		$item_list = array( 'total' => $total_product, 'search_in_name' => wc_ps_ict_t__( 'Product Name', __('Product Name', 'woops') ) );
		if ( $search_products && $total_product > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> wc_ps_ict_t__( 'Product Name', __('Product Name', 'woops') ),
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			foreach ( $search_products as $product ) {

				$product_description = WC_Predictive_Search::woops_limit_words( strip_tags( WC_Predictive_Search::strip_shortcodes( strip_shortcodes ( $product->post_content ) ) ), $text_lenght, '...' );
				if ( trim( $product_description ) == '' ) $product_description = WC_Predictive_Search::woops_limit_words( strip_tags( WC_Predictive_Search::strip_shortcodes( strip_shortcodes( $product->post_excerpt ) ) ), $text_lenght, '...' );

				$item_data = array(
					'title'		=> $product->post_title,
					'keyword'	=> $product->post_title,
					'url'		=> get_permalink( $product->ID ),
					'image_url'	=> $this->get_product_thumbnail_url( $product->ID, 'shop_catalog', 64, 64 ),
					'description' => $product_description,
					'type'		=> 'product'
				);

				if ( $show_price ) $item_data['price'] = $this->get_product_price( $product->ID );
				if ( $show_sku ) $item_data['sku'] = stripslashes( get_post_meta( $product->ID, '_sku', true ) );
				if ( $show_addtocart ) $item_data['addtocart'] = $this->get_product_addtocart( $product->ID );
				if ( $show_categories ) $item_data['categories'] = $this->get_terms_object( $product->ID, 'product_cat' );
				if ( $show_tags ) $item_data['tags'] = $this->get_terms_object( $product->ID, 'product_tag' );

				$item_list['items'][] = $item_data;

				$end_row-- ;
				if ( $end_row < 1 ) break;
			}
		}

		return $item_list;
	}

	/**
	 * Get array product sku list
	 */
	public function get_product_sku_results( $search_keyword, $row, $start = 0, $exclude_products = '', $args_product = array(), $text_lenght = 100, $include_header = true , $show_price = true, $show_sku = true, $show_addtocart = false, $show_categories = false, $show_tags = false ) {

		$end_row = $row;
		$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => 'product', 'post_status' => 'publish', 'meta_key' => '_sku', 'exclude' => $exclude_products, 'suppress_filters' => FALSE, 'ps_post_type' => 'product');

		$args = array_merge( $args, $args_product );

		$search_products = get_posts( $args );

		$total_product = count( $search_products );
		$item_list = array( 'total' => $total_product, 'search_in_name' => wc_ps_ict_t__( 'Product SKU', __('Product SKU', 'woops') ) );
		if ( $search_products && $total_product > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> wc_ps_ict_t__( 'Product SKU', __('Product SKU', 'woops') ),
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			foreach ( $search_products as $product ) {

				$product_sku = stripslashes( get_post_meta( $product->ID, '_sku', true ) );

				$item_data = array(
					'title'		=> $product->post_title,
					'keyword'	=> $product_sku,
					'url'		=> get_permalink( $product->ID ),
					'image_url'	=> $this->get_product_thumbnail_url( $product->ID, 'shop_catalog', 64, 64 ),
					'type'		=> 'p_sku'
				);

				if ( $show_price ) $item_data['price'] = $this->get_product_price( $product->ID );
				if ( $show_sku ) $item_data['sku'] = $product_sku;
				if ( $show_addtocart ) $item_data['addtocart'] = $this->get_product_addtocart( $product->ID );
				if ( $show_categories ) $item_data['categories'] = $this->get_terms_object( $product->ID, 'product_cat' );
				if ( $show_tags ) $item_data['tags'] = $this->get_terms_object( $product->ID, 'product_tag' );

				$item_list['items'][] = $item_data;

				$end_row-- ;
				if ( $end_row < 1 ) break;
			}
		}

		return $item_list;
	}

	/**
	 * Get array post list
	 */
	public function get_post_results( $search_keyword, $row, $start = 0, $exclude_posts = '', $meta_query_args = array(), $args_post = array(), $text_lenght = 100, $post_type = 'post', $include_header = true , $show_categories = false, $show_tags = false ) {

		$end_row = $row;
		$args = array( 's' => $search_keyword, 'numberposts' => $row+1, 'offset'=> $start, 'orderby' => 'predictive', 'order' => 'ASC', 'post_type' => $post_type, 'post_status' => 'publish', 'exclude' => $exclude_posts, 'suppress_filters' => FALSE, 'ps_post_type' => $post_type );

		$args = array_merge( $args, $args_post );

		if ( count( $meta_query_args) > 0 ) $args['meta_query'] = $meta_query_args;

		$search_posts = get_posts( $args );

		$total_post = count( $search_posts );
		$item_list = array( 'total' => $total_post, 'search_in_name' => ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woops') ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woops') ) );
		if ( $search_posts && $total_post > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woops') ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woops') ),
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			foreach ( $search_posts as $item ) {

				$item_description = WC_Predictive_Search::woops_limit_words( strip_tags( WC_Predictive_Search::strip_shortcodes( strip_shortcodes ( $item->post_content ) ) ), $text_lenght, '...' );
				if ( trim( $item_description ) == '' ) $item_description = WC_Predictive_Search::woops_limit_words( strip_tags( WC_Predictive_Search::strip_shortcodes( strip_shortcodes( $item->post_excerpt ) ) ), $text_lenght, '...' );

				$item_data = array(
					'title'		=> $item->post_title,
					'keyword'	=> $item->post_title,
					'url'		=> get_permalink( $item->ID ),
					'image_url'	=> $this->get_product_thumbnail_url( $item->ID, 'shop_catalog', 64, 64 ),
					'description' => $item_description,
					'type'		=> $post_type
				);

				if ( $show_categories ) $item_data['categories'] = $this->get_terms_object( $item->ID, 'category' );
				if ( $show_tags ) $item_data['tags'] = $this->get_terms_object( $item->ID, 'post_tag' );

				$item_list['items'][] = $item_data;

				$end_row-- ;
				if ( $end_row < 1 ) break;
			}
		}

		return $item_list;
	}

	/**
	 * Get array taxonomy list
	 */
	public function get_taxonomy_results( $search_keyword, $search_keyword_nospecial = '', $search_nospecial = false, $row, $start = 0, $exclude_categories = '', $text_lenght = 100, $taxonomy = 'product_cat', $item_type = 'p_cat', $header_text = '', $current_lang = '', $include_header = true ) {
		global $wpdb;
		global $wp_version;

		$support_wpml = false;
		$inner_wpml_table = '';
		$where_wpml = '';
		if ( class_exists('SitePress') ) {
			$support_wpml = true;
			$inner_wpml_table = "INNER JOIN ".$wpdb->prefix."icl_translations AS ic ON ic.element_id = tt.term_taxonomy_id";
			$where_wpml = " AND ic.language_code = '".$current_lang."'";
		}

		$end_row = $row;
		$number_items = $row+1;
		$inner_extra = '';
		$where = "tt.taxonomy IN ('".$taxonomy."') ";

		$original_search_keyword = $search_keyword;
		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$search_keyword = like_escape( $search_keyword );
			$search_keyword_nospecial = like_escape( $search_keyword_nospecial );
		} else {
			$search_keyword = $wpdb->esc_like( $search_keyword );
			$search_keyword_nospecial = $wpdb->esc_like( $search_keyword_nospecial );
		}
		//$where .= $wpdb->prepare( " AND ( ".WC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword . '%', '% '.$search_keyword . '%');
		//if ( $search_nospecial ) $where .= $wpdb->prepare( " OR ".WC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s OR ".WC_Predictive_Search_Hook_Filter::remove_special_characters_in_mysql( "t.name" )." LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
		$where .= $wpdb->prepare( " AND ( t.name LIKE %s OR t.name LIKE %s", $search_keyword . '%', '% '.$search_keyword . '%');
		if ( $search_nospecial ) $where .= $wpdb->prepare( " OR t.name LIKE %s OR t.name LIKE %s", $search_keyword_nospecial . '%', '% '.$search_keyword_nospecial . '%');
		$where .= ")";

		if ( trim( $exclude_categories ) != '' )
			$where .= " AND t.term_id NOT IN (".trim( $exclude_categories ).") ";

		if ( $support_wpml )
			$where .= $where_wpml. " AND ic.element_type='tax_".$taxonomy."' ";

		$orderby = $wpdb->prepare( "t.name NOT LIKE %s ASC, t.name ASC", $search_keyword . '%');
		$query = "SELECT t.*, tt.description FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id {$inner_wpml_table} WHERE {$where} ORDER BY {$orderby} LIMIT $start,$number_items";

		$search_cats = $wpdb->get_results($query);
		$total_cat = count($search_cats);
		$item_list = array( 'total' => $total_cat, 'search_in_name' => $header_text );
		if ( $search_cats && $total_cat > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> $header_text,
					'keyword'	=> $original_search_keyword,
					'type'		=> 'header'
				);
			}

			foreach ( $search_cats as $item ) {

				$item_description = WC_Predictive_Search::woops_limit_words( strip_tags( WC_Predictive_Search::strip_shortcodes( strip_shortcodes ( $item->description ) ) ), $text_lenght, '...' );

				$item_list['items'][] = array(
					'title'		=> $item->name,
					'keyword'	=> $item->name,
					'url'		=> get_term_link( $item->slug, $taxonomy ),
					'image_url'	=> $this->get_product_cat_thumbnail( $item->term_id, 64, 64 ),
					'description' => $item_description,
					'type'		=> $item_type
				);

				$end_row-- ;
				if ( $end_row < 1 ) break;
			}
		}

		return $item_list;
	}

	/**
	 * Get product price
	 */
	public function get_product_price( $product_id ) {
		$product_price_output = '';
		$current_db_version = get_option( 'woocommerce_db_version', null );
		if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
			$current_product = new WC_Product($product_id);
		} elseif ( version_compare( WC()->version, '2.2.0', '<' ) ) {
			$current_product = get_product( $product_id );
		} else {
			$current_product = wc_get_product( $product_id );
		}

		$product_price_output = $current_product->get_price_html();

		return $product_price_output;
	}

	/**
	 * Get product add to cart
	 */
	public function get_product_addtocart( $product_id ) {
		$product_addtocart_output = '';
		global $product;
		global $post;
		$current_db_version = get_option( 'woocommerce_db_version', null );
		if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
			$current_product = new WC_Product($product_id);
		} elseif ( version_compare( WC()->version, '2.2.0', '<' ) ) {
			$current_product = get_product( $product_id );
		} else {
			$current_product = wc_get_product( $product_id );
		}
		$product = $current_product;
		$post = get_post( $product_id );
		ob_start();
		if (function_exists('woocommerce_template_loop_add_to_cart') )
			woocommerce_template_loop_add_to_cart();
		$product_addtocart_output = ob_get_clean();

		return $product_addtocart_output;
	}

	/**
	 * Get product add to cart
	 */
	public function get_terms_object( $object_id, $taxonomy = 'product_cat' ) {
		$terms_list = array();

		$terms = get_the_terms( $object_id, $taxonomy );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $terms ) {
				$terms_list[] = array(
					'name'	=> $terms->name,
					'url'	=> get_term_link($terms->slug, $taxonomy )
				);
			}
		}

		return $terms_list;
	}

	/**
	 * Get product thumbnail url
	 */
	public function get_product_thumbnail_url( $post_id, $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $woocommerce;
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		$shop_catalog = ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? $woocommerce->get_image_size( 'shop_catalog' ) : wc_get_image_size( 'shop_catalog' ) );
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['width'] ) && $placeholder_width == 0 ) {
			$placeholder_width = $shop_catalog['width'];
		}
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['height'] ) && $placeholder_height == 0 ) {
			$placeholder_height = $shop_catalog['height'];
		}

		$mediumSRC = '';

		// Return Feature Image URL
		if ( has_post_thumbnail( $post_id ) ) {
			$thumbid = get_post_thumbnail_id( $post_id );
			$attachmentArray = wp_get_attachment_image_src( $thumbid, $size, false );
			if ( $attachmentArray ) {
				$mediumSRC = $attachmentArray[0];
				if ( trim( $mediumSRC != '' ) ) {
					return $mediumSRC;
				}
			}
		}

		// Return First Image URL in gallery of this product
		if ( trim( $mediumSRC == '' ) ) {
			$args = array( 'post_parent' => $post_id , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null );
			$attachments = get_posts( $args );
			if ( $attachments ) {
				foreach ( $attachments as $attachment ) {
					$attachmentArray = wp_get_attachment_image_src( $attachment->ID, $size, false );
					if ( $attachmentArray ) {
						$mediumSRC = $attachmentArray[0];
						if ( trim( $mediumSRC != '' ) ) {
							return $mediumSRC;
						}
					}
				}
			}
		}

		// Ger Image URL of parent product
		if ( trim( $mediumSRC == '' ) ) {
			// Load the product
			$product = get_post( $post_id );

			// Get ID of parent product if one exists
			if ( !empty( $product->post_parent ) )
				$post_id = $product->post_parent;

			if ( has_post_thumbnail( $post_id ) ) {
				$thumbid = get_post_thumbnail_id( $post_id );
				$attachmentArray = wp_get_attachment_image_src( $thumbid, $size, false );
				if ( $attachmentArray ) {
					$mediumSRC = $attachmentArray[0];
					if ( trim( $mediumSRC != '' ) ) {
						return $mediumSRC;
					}
				}
			}

			if ( trim( $mediumSRC == '' ) ) {
				$args = array( 'post_parent' => $post_id , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null );
				$attachments = get_posts( $args );
				if ( $attachments ) {
					foreach ( $attachments as $attachment ) {
						$attachmentArray = wp_get_attachment_image_src( $attachment->ID, $size, false );
						if ( $attachmentArray ) {
							$mediumSRC = $attachmentArray[0];
							if ( trim( $mediumSRC != '' ) ) {
								return $mediumSRC;
							}
						}
					}
				}
			}
		}

		// Use place holder image of Woo
		if ( trim( $mediumSRC == '' ) ) {
			$mediumSRC = ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) && null !== $woocommerce_db_version ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() );
		}

		return $mediumSRC;
	}

	public static function get_product_cat_thumbnail( $term_id, $placeholder_width = 0, $placeholder_height = 0  ) {
		global $woocommerce;
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );

		$image= '';

		$small_thumbnail_size  = apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
		$shop_catalog = ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? $woocommerce->get_image_size( 'shop_catalog' ) : wc_get_image_size( 'shop_catalog' ) );
		$image_width    = 150;
		$image_height    = 150;
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['width'] ) ) $image_width = $shop_catalog['width'];
		if ( is_array( $shop_catalog ) && isset( $shop_catalog['height'] ) ) $image_height = $shop_catalog['height'];
		if ( $placeholder_width == 0 )
			$placeholder_width = $image_width;
		if ( $placeholder_height == 0 )
			$placeholder_height = $image_height;

		$thumbnail_id  = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true  );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
			$image = $image[0];
		}

		if ( trim( $image ) != '' ) {
			return $image;
		} else {
			return ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() );
		}
	}

}

global $wc_ps_legacy_api;
$wc_ps_legacy_api = new WC_Predictive_Search_Legacy_API();