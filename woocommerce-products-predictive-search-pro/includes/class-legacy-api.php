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
		global $wc_predictive_search;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = $_REQUEST['lang'];
		}

		$rs_items = array();
		$row = 6;
		$text_lenght = 100;
		$show_price = 0;
		$search_keyword = '';
		$cat_in = 'all';
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
		if ( isset($_REQUEST['cat_in']) && trim($_REQUEST['cat_in']) != '') $cat_in = stripslashes( strip_tags( $_REQUEST['cat_in'] ) );
		if ( isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = json_decode( stripslashes( $_REQUEST['search_in'] ), true );
		if ( ! is_array($search_in) || count($search_in) < 1 || array_sum($search_in) < 1) $search_in = $search_in_default;

		if ( $search_keyword != '' ) {
			$search_list = array();
			foreach ($search_in as $key => $number) {
				if ( ! isset( $items_search_default[$key] ) ) continue;
				if ($number > 0)
					$search_list[$key] = $key;
			}

			$woocommerce_search_focus_enable = get_option( 'woocommerce_search_focus_enable' );
			$woocommerce_search_focus_plugin = get_option( 'woocommerce_search_focus_plugin' );

			$all_items = array();
			$product_list = array();
			$sku_list = array();
			$post_list = array();
			$page_list = array();
			$category_list = array();
			$tag_list = array();

			$permalink_structure = get_option( 'permalink_structure' );

			$product_term_id = 0;
			$post_term_id = 0;
			if ( ( ( isset( $search_in['product'] ) && $search_in['product'] > 0 ) || ( isset( $search_in['p_sku'] ) && $search_in['p_sku'] > 0 ) ) && 'all' != $cat_in ) {
				$term_data = get_term_by( 'slug', $cat_in, 'product_cat' );
				if ( $term_data ) {
					$product_term_id = (int) $term_data->term_id;
				} else {
					$term_data = get_term_by( 'slug', $cat_in, 'product_tag' );

					if ( $term_data ) {
						$product_term_id = (int) $term_data->term_id;
					}
				}
			} elseif ( isset( $search_in['post'] ) && $search_in['post'] > 0 && 'all' != $cat_in ) {
				$term_data = get_term_by( 'slug', $cat_in, 'category' );
				if ( $term_data ) {
					$post_term_id = (int) $term_data->term_id;
				} else {
					$term_data = get_term_by( 'slug', $cat_in, 'post_tag' );

					if ( $term_data ) {
						$post_term_id = (int) $term_data->term_id;
					}
				}
			}

			if ( isset( $search_in['product'] ) && $search_in['product'] > 0 ) {
				$product_list = $wc_predictive_search->get_product_results( $search_keyword, $search_in['product'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $product_term_id, $text_lenght, $current_lang, true, $show_price );
				$total_product = $product_list['total'];
				if ( $total_product > 0 ) {
					$found_items = true;
					$rs_items['product'] = $product_list['items'];
				}
			}

			if ( isset( $search_in['p_sku'] ) && $search_in['p_sku'] > 0 ) {
				$sku_list = $wc_predictive_search->get_product_sku_results( $search_keyword, $search_in['p_sku'], 0, $product_term_id, $text_lenght, $current_lang, true, $show_price );
				$total_p_sku = $sku_list['total'];
				if ( $total_p_sku > 0 ) {
					$found_items = true;
					$rs_items['p_sku'] = $sku_list['items'];
				}
			}

			if ( isset( $search_in['post'] ) && $search_in['post'] > 0 ) {
				$post_list = $wc_predictive_search->get_post_results( $search_keyword, $search_in['post'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_term_id, $text_lenght, $current_lang, 'post' );
				$total_post = $post_list['total'];
				if ( $total_post > 0 ) {
					$found_items = true;
					$rs_items['post'] = $post_list['items'];
				}
			}

			if ( isset( $search_in['page'] ) && $search_in['page'] > 0 ) {
				$page_list = $wc_predictive_search->get_post_results( $search_keyword, $search_in['page'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 0, $text_lenght, $current_lang, 'page' );
				$total_page = $page_list['total'];
				if ( $total_page > 0 ) {
					$found_items = true;
					$rs_items['page'] = $page_list['items'];
				}
			}

			if ( isset( $search_in['p_cat'] ) && $search_in['p_cat'] > 0 ) {
				$header_text = wc_ps_ict_t__( 'Product Categories', __('Product Categories', 'woops') );
				$category_list = $wc_predictive_search->get_taxonomy_results( $search_keyword, $search_in['p_cat'], 0, $text_lenght, 'product_cat', 'p_cat', $header_text, $current_lang );
				$total_pcat = $category_list['total'];
				if ( $total_pcat > 0 ) {
					$found_items = true;
					$rs_items['p_cat'] = $category_list['items'];
				}
			}

			if ( isset( $search_in['p_tag'] ) && $search_in['p_tag'] > 0 ) {
				$header_text = wc_ps_ict_t__( 'Product Tags', __('Product Tags', 'woops') );
				$tag_list = $wc_predictive_search->get_taxonomy_results( $search_keyword, $search_in['p_tag'], 0, $text_lenght, 'product_tag', 'p_tag', $header_text, $current_lang );
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
							$link_search = get_permalink( $woocommerce_search_page_id ).'&rs='. urlencode($search_keyword) .$search_in_parameter.'&search_other='.implode(",", $search_other).'&cat_in='.$cat_in;
						else
							$link_search = rtrim( get_permalink( $woocommerce_search_page_id ), '/' ).'/keyword/'. urlencode($search_keyword) .$search_in_parameter.'/cat-in/'.$cat_in.'/search-other/'.implode(",", $search_other);
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
		global $wc_predictive_search;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = $_REQUEST['lang'];
		}

		$psp = 1;
		$row = 10;
		$search_keyword = '';
		$cat_in = 'all';
		$search_in = 'product';

		if ( get_option('woocommerce_search_result_items') > 0  ) $row = get_option('woocommerce_search_result_items');

		if ( isset( $_REQUEST['psp'] ) && $_REQUEST['psp'] > 0 ) $psp = stripslashes( strip_tags( $_REQUEST['psp'] ) );
		if ( isset( $_REQUEST['q'] ) && trim( $_REQUEST['q'] ) != '' ) $search_keyword = stripslashes( strip_tags( $_REQUEST['q'] ) );
		if ( isset( $_REQUEST['cat_in'] ) && trim( $_REQUEST['cat_in'] ) != '' ) $cat_in = stripslashes( strip_tags( $_REQUEST['cat_in'] ) );
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

			$product_term_id = 0;
			$post_term_id = 0;
			if ( in_array( $search_in, array( 'product', 'p_sku' ) ) && 'all' != $cat_in ) {
				$term_data = get_term_by( 'slug', $cat_in, 'product_cat' );

				if ( $term_data ) {
					$product_term_id = (int) $term_data->term_id;
				} else {
					$term_data = get_term_by( 'slug', $cat_in, 'product_tag' );

					if ( $term_data ) {
						$product_term_id = (int) $term_data->term_id;
					}
				}
			} elseif ( 'post' == $search_in && 'all' != $cat_in ) {
				$term_data = get_term_by( 'slug', $cat_in, 'category' );
				if ( $term_data ) {
					$post_term_id = (int) $term_data->term_id;
				} else {
					$term_data = get_term_by( 'slug', $cat_in, 'post_tag' );

					if ( $term_data ) {
						$post_term_id = (int) $term_data->term_id;
					}
				}
			}

			$start = ( $psp - 1) * $row;

			$woocommerce_search_focus_enable = get_option('woocommerce_search_focus_enable');
			$woocommerce_search_focus_plugin = get_option('woocommerce_search_focus_plugin');

			if ( $search_in == 'product' ) {
				$item_list = $wc_predictive_search->get_product_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $product_term_id, $text_lenght, $current_lang, false, $show_price, $show_sku, $show_addtocart, $show_categories, $show_tags );
			} elseif ( $search_in == 'p_sku' ) {
				$item_list = $wc_predictive_search->get_product_sku_results( $search_keyword, $row, $start, $product_term_id, $text_lenght, $current_lang, false, $show_price, $show_sku, $show_addtocart, $show_categories, $show_tags );
			} elseif ( $search_in == 'post' ) {
				$item_list = $wc_predictive_search->get_post_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_term_id, $text_lenght, $current_lang, 'post', false , $show_categories, $show_tags );
			} elseif ( $search_in == 'page' ) {
				$item_list = $wc_predictive_search->get_post_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 0, $text_lenght, $current_lang, 'page', false );
			} elseif ( $search_in == 'p_cat' ) {
				$header_text = wc_ps_ict_t__( 'Product Categories', __('Product Categories', 'woops') );
				$item_list = $wc_predictive_search->get_taxonomy_results( $search_keyword, $row, $start, $text_lenght, 'product_cat', 'p_cat', $header_text, $current_lang, false );
			} elseif ( $search_in == 'p_tag' ) {
				$header_text = wc_ps_ict_t__( 'Product Tags', __('Product Tags', 'woops') );
				$item_list = $wc_predictive_search->get_taxonomy_results( $search_keyword, $row, $start, $text_lenght, 'product_tag', 'p_tag', $header_text, $current_lang, false );
			}
		}

		header( 'Content-Type: application/json', true, 200 );
		die( json_encode( $item_list ) );
	}

}

global $wc_ps_legacy_api;
$wc_ps_legacy_api = new WC_Predictive_Search_Legacy_API();