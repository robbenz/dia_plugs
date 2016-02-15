<?php
/**
 * WooCommerce Predictive Search
 *
 * Class Function into woocommerce plugin
 *
 * Table Of Contents
 *
 * install_databases()
 * set_tables_wpdbfix()
 */
class WC_Predictive_Search
{

	public function __construct() {
		// Set Predictive Search Tables
		add_action( 'init', array( $this, 'set_tables_wpdbfix' ), 0 );
		add_action( 'switch_blog', array( $this, 'set_tables_wpdbfix' ), 0 );
	}

	public function install_databases() {
		global $wc_ps_keyword_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_posts_data;
		global $wc_ps_product_categories_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_product_tags_data;
		global $wc_ps_exclude_data;
		global $wc_ps_term_relationships_data;

		$wc_ps_posts_data->install_database();
		$wc_ps_keyword_data->install_database();
		$wc_ps_postmeta_data->install_database();
		$wc_ps_product_sku_data->install_database();
		$wc_ps_product_categories_data->install_database();
		$wc_ps_product_tags_data->install_database();
		$wc_ps_exclude_data->install_database();
		$wc_ps_term_relationships_data->install_database();
	}

	public function set_tables_wpdbfix() {
		global $wc_ps_keyword_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_posts_data;
		global $wc_ps_product_categories_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_product_tags_data;
		global $wc_ps_exclude_data;
		global $wc_ps_term_relationships_data;

		$wc_ps_posts_data->set_table_wpdbfix();
		$wc_ps_keyword_data->set_table_wpdbfix();
		$wc_ps_postmeta_data->set_table_wpdbfix();
		$wc_ps_product_sku_data->set_table_wpdbfix();
		$wc_ps_product_categories_data->set_table_wpdbfix();
		$wc_ps_product_tags_data->set_table_wpdbfix();
		$wc_ps_exclude_data->set_table_wpdbfix();
		$wc_ps_term_relationships_data->set_table_wpdbfix();
	}

	public function general_sql( $main_sql ) {

		$select_sql = '';
		if ( is_array( $main_sql['select'] ) && count( $main_sql['select'] ) > 0 ) {
			$select_sql = implode( ', ', $main_sql['select'] );
		} elseif ( ! is_array( $main_sql['select'] ) ) {
			$select_sql = $main_sql['select'];
		}

		$from_sql = '';
		if ( is_array( $main_sql['from'] ) && count( $main_sql['from'] ) > 0 ) {
			$from_sql = implode( ', ', $main_sql['from'] );
		} elseif ( ! is_array( $main_sql['from'] ) ) {
			$from_sql = $main_sql['from'];
		}

		$join_sql = '';
		if ( is_array( $main_sql['join'] ) && count( $main_sql['join'] ) > 0 ) {
			$join_sql = implode( ' ', $main_sql['join'] );
		} elseif ( ! is_array( $main_sql['join'] ) ) {
			$join_sql = $main_sql['join'];
		}

		$where_sql = '';
		$where_search_sql = '';
		if ( is_array( $main_sql['where'] ) && count( $main_sql['where'] ) > 0 ) {
			if ( isset( $main_sql['where']['search'] ) ) {
				$where_search = $main_sql['where']['search'];
				unset( $main_sql['where']['search'] );
				if ( is_array( $where_search ) && count( $where_search ) > 0 ) {
					$where_search_sql = implode( ' ', $where_search );
				} elseif ( ! is_array( $where_search ) ) {
					$where_search_sql = $where_search;
				}
			}
			$where_sql = implode( ' ', $main_sql['where'] );
		} elseif ( ! is_array( $main_sql['where'] ) ) {
			$where_sql = $main_sql['where'];
		}

		$groupby_sql = '';
		if ( is_array( $main_sql['groupby'] ) && count( $main_sql['groupby'] ) > 0 ) {
			$groupby_sql = implode( ', ', $main_sql['groupby'] );
		} elseif ( ! is_array( $main_sql['groupby'] ) ) {
			$groupby_sql = $main_sql['groupby'];
		}

		$orderby_sql = '';
		if ( is_array( $main_sql['orderby'] ) && count( $main_sql['orderby'] ) > 0 ) {
			$orderby_sql = implode( ', ', $main_sql['orderby'] );
		} elseif ( ! is_array( $main_sql['orderby'] ) ) {
			$orderby_sql = $main_sql['orderby'];
		}

		$limit_sql = $main_sql['limit'];

		$sql = 'SELECT ';
		if ( '' != trim( $select_sql ) ) {
			$sql .= $select_sql;
		}

		$sql .= ' FROM ';
		if ( '' != trim( $from_sql ) ) {
			$sql .= $from_sql . ' ';
		}

		if ( '' != trim( $join_sql ) ) {
			$sql .= $join_sql . ' ';
		}

		if ( '' != trim( $where_sql ) || '' != trim( $where_search_sql ) ) {
			$sql .= ' WHERE ';
			$sql .= $where_sql . ' ';

			if ( '' != trim( $where_search_sql ) ) {
				if ( '' != trim( $where_sql ) ) {
					$sql .= ' AND ( ' . $where_search_sql . ' ) ';
				} else {
					$sql .= $where_search_sql;
				}
			}
		}

		if ( '' != trim( $groupby_sql ) ) {
			$sql .= ' GROUP BY ';
			$sql .= $groupby_sql . ' ';
		}

		if ( '' != trim( $orderby_sql ) ) {
			$sql .= ' ORDER BY ';
			$sql .= $orderby_sql . ' ';
		}

		if ( '' != trim( $limit_sql ) ) {
			$sql .= ' LIMIT ';
			$sql .= $limit_sql . ' ';
		}

		return $sql;
	}

	public function get_product_search_sql( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type = 'product', $term_id = 0, $current_lang = '', $check_exsited = false ) {
		global $wpdb;

		$row += 1;

		$search_keyword           = esc_sql( $wpdb->esc_like( trim( $search_keyword ) ) );
		$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
		if ( $search_keyword == $search_keyword_nospecial ) {
			$search_keyword_nospecial = '';
		}

		$main_sql               = array();
		$term_relationships_sql = array();
		$ps_keyword_sql         = array();
		$postmeta_sql           = array();
		$wpml_sql               = array();

		global $wc_ps_posts_data;
		$main_sql = $wc_ps_posts_data->get_sql( $search_keyword, $search_keyword_nospecial, $post_type, $row, $start, $check_exsited );

		if ( $term_id > 0 ) {
			global $wc_ps_term_relationships_data;
			$term_relationships_sql = $wc_ps_term_relationships_data->get_sql( $term_id );
		}

		if ( empty( $woocommerce_search_focus_enable ) || $woocommerce_search_focus_enable == 'yes' ) {
			global $wc_ps_keyword_data;
			$ps_keyword_sql = $wc_ps_keyword_data->get_sql( $search_keyword, $search_keyword_nospecial );

			if ( in_array( $woocommerce_search_focus_plugin, array( 'yoast_seo_plugin', 'all_in_one_seo_plugin' ) ) ) {
				global $wc_ps_postmeta_data;
				$meta_key_name = '_aioseop_keywords';
				if ( 'yoast_seo_plugin' == $woocommerce_search_focus_plugin ) {
					$meta_key_name = '_yoast_wpseo_focuskw';
				}

				$postmeta_sql = $wc_ps_postmeta_data->get_sql( $search_keyword, $search_keyword_nospecial, $meta_key_name );
			}
		}

		if ( class_exists('SitePress') && '' != $current_lang ) {
			$wpml_sql['join'] = " INNER JOIN ".$wpdb->prefix."icl_translations AS ic ON (ic.element_id = pp.post_id) ";
			$wpml_sql['where'][] = " AND ic.language_code = '".$current_lang."' AND ic.element_type = 'post_{$post_type}' ";
		}

		$main_sql = array_merge_recursive( $main_sql, $term_relationships_sql );
		$main_sql = array_merge_recursive( $main_sql, $wpml_sql );
		$main_sql = array_merge_recursive( $main_sql, $ps_keyword_sql );
		$main_sql = array_merge_recursive( $main_sql, $postmeta_sql );

		$sql = $this->general_sql( $main_sql );

		return $sql;
	}

	/**
	 * Check product is exsited from search term
	 */
	public function check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type = 'product', $term_id = 0, $current_lang = '' ) {
		global $wpdb;

		$sql = $this->get_product_search_sql( $search_keyword, 1, 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $term_id, $current_lang, true );

		$sql = "SELECT EXISTS( " . $sql . ")";

		$have_item = $wpdb->get_var( $sql );
		if ( $have_item == '1' ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get array product list
	 */
	public function get_product_results( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $product_term_id = 0, $text_lenght = 100, $current_lang = '', $include_header = true , $show_price = true, $show_sku = false, $show_addtocart = false, $show_categories = false, $show_tags = false ) {
		global $wpdb;

		$have_product = $this->check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 'product', $product_term_id, $current_lang );
		if ( ! $have_product ) {
			$item_list = array( 'total' => 0, 'search_in_name' => wc_ps_ict_t__( 'Product Name', __('Product Name', 'woops') ) );
			return $item_list;
		}

		$sql = $this->get_product_search_sql( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 'product', $product_term_id, $current_lang, false );

		$search_products = $wpdb->get_results( $sql );

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

				$product_data = get_post( $product->post_id );
				$product_description = WC_Predictive_Search_Functions::woops_limit_words( strip_tags( WC_Predictive_Search_Functions::strip_shortcodes( strip_shortcodes ( $product_data->post_content ) ) ), $text_lenght, '...' );
				if ( trim( $product_description ) == '' ) $product_description = WC_Predictive_Search_Functions::woops_limit_words( strip_tags( WC_Predictive_Search_Functions::strip_shortcodes( strip_shortcodes( $product_data->post_excerpt ) ) ), $text_lenght, '...' );

				$item_data = array(
					'title'		=> $product->post_title,
					'keyword'	=> $product->post_title,
					'url'		=> get_permalink( $product->post_id ),
					'image_url'	=> WC_Predictive_Search_Functions::get_product_thumbnail_url( $product->post_id, 'shop_catalog', 64, 64 ),
					'description' => $product_description,
					'type'		=> 'product'
				);

				if ( $show_price ) $item_data['price'] = WC_Predictive_Search_Functions::get_product_price( $product->post_id );
				if ( $show_sku ) {
					global $wc_ps_product_sku_data;
					$item_data['sku'] = stripslashes( $wc_ps_product_sku_data->get_item( $product->post_id ) );
				}
				if ( $show_addtocart ) $item_data['addtocart'] = WC_Predictive_Search_Functions::get_product_addtocart( $product->post_id );
				if ( $show_categories ) $item_data['categories'] = WC_Predictive_Search_Functions::get_terms_object( $product->post_id, 'product_cat' );
				if ( $show_tags ) $item_data['tags'] = WC_Predictive_Search_Functions::get_terms_object( $product->post_id, 'product_tag' );

				$item_list['items'][] = $item_data;

				$row-- ;
				if ( $row < 1 ) break;
			}
		}

		return $item_list;
	}

	public function get_product_sku_search_sql( $search_keyword, $row, $start = 0, $product_term_id = 0, $current_lang = '', $check_exsited = false ) {
		global $wpdb;

		$row += 1;

		$search_keyword           = esc_sql( $wpdb->esc_like( trim( $search_keyword ) ) );
		$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
		if ( $search_keyword == $search_keyword_nospecial ) {
			$search_keyword_nospecial = '';
		}

		$main_sql               = array();
		$term_relationships_sql = array();
		$wpml_sql               = array();

		global $wc_ps_product_sku_data;
		$main_sql = $wc_ps_product_sku_data->get_sql( $search_keyword, $search_keyword_nospecial, $row, $start, $check_exsited );

		if ( $product_term_id > 0 ) {
			global $wc_ps_term_relationships_data;
			$term_relationships_sql = $wc_ps_term_relationships_data->get_sql( $product_term_id );
		}

		if ( class_exists('SitePress') && '' != $current_lang ) {
			$wpml_sql['join'] = " INNER JOIN ".$wpdb->prefix."icl_translations AS ic ON (ic.element_id = pp.post_id) ";
			$wpml_sql['where'][] = " AND ic.language_code = '".$current_lang."' AND ic.element_type = 'post_product' ";
		}

		$main_sql = array_merge_recursive( $main_sql, $term_relationships_sql );
		$main_sql = array_merge_recursive( $main_sql, $wpml_sql );

		$sql = $this->general_sql( $main_sql );

		return $sql;
	}

	/**
	 * Check product sku is exsited from search term
	 */
	public function check_product_sku_exsited( $search_keyword, $product_term_id = 0, $current_lang = '' ) {
		global $wpdb;

		$sql = $this->get_product_sku_search_sql( $search_keyword, 1, 0, $product_term_id, $current_lang, true );

		$sql = "SELECT EXISTS( " . $sql . ")";

		$have_item = $wpdb->get_var( $sql );
		if ( $have_item == '1' ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get array product sku list
	 */
	public function get_product_sku_results( $search_keyword, $row, $start = 0, $product_term_id = 0, $text_lenght = 100, $current_lang = '', $include_header = true , $show_price = true, $show_sku = true, $show_addtocart = false, $show_categories = false, $show_tags = false ) {
		global $wpdb;

		$have_p_sku = $this->check_product_sku_exsited( $search_keyword, $product_term_id, $current_lang );
		if ( ! $have_p_sku ) {
			$item_list = array( 'total' => 0, 'search_in_name' => wc_ps_ict_t__( 'Product SKU', __('Product SKU', 'woops') ) );
			return $item_list;
		}

		$sql = $this->get_product_sku_search_sql( $search_keyword, $row, $start, $product_term_id, $current_lang, false );

		$search_products = $wpdb->get_results( $sql );

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

				$product_data = get_post( $product->post_id );
				$product_sku = stripslashes( $product->sku );

				$item_data = array(
					'title'		=> $product_data->post_title,
					'keyword'	=> $product_sku,
					'url'		=> get_permalink( $product->post_id ),
					'image_url'	=> WC_Predictive_Search_Functions::get_product_thumbnail_url( $product->post_id, 'shop_catalog', 64, 64 ),
					'type'		=> 'p_sku'
				);

				if ( $show_price ) $item_data['price'] = WC_Predictive_Search_Functions::get_product_price( $product->post_id );
				if ( $show_sku ) $item_data['sku'] = $product_sku;
				if ( $show_addtocart ) $item_data['addtocart'] = WC_Predictive_Search_Functions::get_product_addtocart( $product->post_id );
				if ( $show_categories ) $item_data['categories'] = WC_Predictive_Search_Functions::get_terms_object( $product->post_id, 'product_cat' );
				if ( $show_tags ) $item_data['tags'] = WC_Predictive_Search_Functions::get_terms_object( $product->post_id, 'product_tag' );

				$item_list['items'][] = $item_data;

				$row-- ;
				if ( $row < 1 ) break;
			}
		}

		return $item_list;
	}

	/**
	 * Get array post list
	 */
	public function get_post_results( $search_keyword, $row, $start = 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_term_id = 0, $text_lenght = 100, $current_lang = '', $post_type = 'post', $include_header = true , $show_categories = false, $show_tags = false ) {
		global $wpdb;

		$have_post = $this->check_product_exsited( $search_keyword, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $post_term_id, $current_lang );
		if ( ! $have_post ) {
			$item_list = array( 'total' => $total_post, 'search_in_name' => ( $post_type == 'post' ) ? wc_ps_ict_t__( 'Posts', __('Posts', 'woops') ) : wc_ps_ict_t__( 'Pages', __('Pages', 'woops') ) );
			return $item_list;
		}

		$sql = $this->get_product_search_sql( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_type, $post_term_id, $current_lang, false );

		$search_posts = $wpdb->get_results( $sql );

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

				$post_data = get_post( $item->post_id );
				$item_description = WC_Predictive_Search_Functions::woops_limit_words( strip_tags( WC_Predictive_Search_Functions::strip_shortcodes( strip_shortcodes ( $post_data->post_content ) ) ), $text_lenght, '...' );
				if ( trim( $item_description ) == '' ) $item_description = WC_Predictive_Search_Functions::woops_limit_words( strip_tags( WC_Predictive_Search_Functions::strip_shortcodes( strip_shortcodes( $post_data->post_excerpt ) ) ), $text_lenght, '...' );

				$item_data = array(
					'title'		=> $item->post_title,
					'keyword'	=> $item->post_title,
					'url'		=> get_permalink( $item->post_id ),
					'image_url'	=> WC_Predictive_Search_Functions::get_product_thumbnail_url( $item->post_id, 'shop_catalog', 64, 64 ),
					'description' => $item_description,
					'type'		=> $post_type
				);

				if ( $show_categories ) $item_data['categories'] = WC_Predictive_Search_Functions::get_terms_object( $item->post_id, 'category' );
				if ( $show_tags ) $item_data['tags'] = WC_Predictive_Search_Functions::get_terms_object( $item->post_id, 'post_tag' );

				$item_list['items'][] = $item_data;

				$row-- ;
				if ( $row < 1 ) break;
			}
		}

		return $item_list;
	}

	public function get_taxonomy_search_sql( $search_keyword, $row, $start = 0, $taxonomy = 'product_cat', $current_lang = '', $check_exsited = false ) {
		global $wpdb;

		$row += 1;

		$search_keyword           = esc_sql( $wpdb->esc_like( trim( $search_keyword ) ) );
		$search_keyword_nospecial = preg_replace( "/[^a-zA-Z0-9_.\s]/", "", $search_keyword );
		if ( $search_keyword == $search_keyword_nospecial ) {
			$search_keyword_nospecial = '';
		}

		$main_sql               = array();
		$wpml_sql               = array();

		if ( 'product_cat' == $taxonomy ) {
			global $wc_ps_product_categories_data;
			$table_alias = 'ppc';
			$main_sql = $wc_ps_product_categories_data->get_sql( $search_keyword, $search_keyword_nospecial, $row, $start, $check_exsited );
		} else {
			global $wc_ps_product_tags_data;
			$table_alias = 'ppt';
			$main_sql = $wc_ps_product_tags_data->get_sql( $search_keyword, $search_keyword_nospecial, $row, $start, $check_exsited );
		}

		if ( class_exists('SitePress') && '' != $current_lang ) {
			$wpml_sql['join'] = " INNER JOIN ".$wpdb->prefix."icl_translations AS ic ON (ic.element_id = {$table_alias}.term_taxonomy_id) ";
			$wpml_sql['where'][] = " AND ic.language_code = '".$current_lang."' AND ic.element_type = 'tax_{$taxonomy}' ";
		}

		$main_sql = array_merge_recursive( $main_sql, $wpml_sql );

		$sql = $this->general_sql( $main_sql );

		return $sql;
	}

	/**
	 * Check term is exsited from search term
	 */
	public function check_taxonomy_exsited( $search_keyword, $taxonomy = 'product_cat', $current_lang = '' ) {
		global $wpdb;

		$sql = $this->get_taxonomy_search_sql( $search_keyword, 1, 0, $taxonomy, $current_lang, true );

		$sql = "SELECT EXISTS( " . $sql . ")";

		$have_item = $wpdb->get_var( $sql );
		if ( $have_item == '1' ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get array taxonomy list
	 */
	public function get_taxonomy_results( $search_keyword, $row, $start = 0, $text_lenght = 100, $taxonomy = 'product_cat', $item_type = 'p_cat', $header_text = '', $current_lang = '', $include_header = true ) {
		global $wpdb;

		$have_term = $this->check_taxonomy_exsited( $search_keyword, $taxonomy, $current_lang );
		if ( ! $have_term ) {
			$item_list = array( 'total' => 0, 'search_in_name' => $header_text );
			return $item_list;
		}

		$sql = $this->get_taxonomy_search_sql( $search_keyword, $row, $start, $taxonomy, $current_lang, false );

		$search_cats = $wpdb->get_results( $sql );

		$total_cat = count($search_cats);
		$item_list = array( 'total' => $total_cat, 'search_in_name' => $header_text );
		if ( $search_cats && $total_cat > 0 ) {
			$item_list['items'] = array();

			if ( $include_header ) {
				$item_list['items'][] = array(
					'title' 	=> $header_text,
					'keyword'	=> $search_keyword,
					'type'		=> 'header'
				);
			}

			foreach ( $search_cats as $item ) {
				$term_description = $wpdb->get_var( $wpdb->prepare( "SELECT description FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = %s ", $item->term_id, $taxonomy ) );
				$item_description = WC_Predictive_Search_Functions::woops_limit_words( strip_tags( WC_Predictive_Search_Functions::strip_shortcodes( strip_shortcodes ( $term_description ) ) ), $text_lenght, '...' );

				$item_list['items'][] = array(
					'title'		=> $item->name,
					'keyword'	=> $item->name,
					'url'		=> get_term_link( (int)$item->term_id, $taxonomy ),
					'image_url'	=> WC_Predictive_Search_Functions::get_product_cat_thumbnail( $item->term_id, 64, 64 ),
					'description' => $item_description,
					'type'		=> $item_type
				);

				$end_row-- ;
				if ( $end_row < 1 ) break;
			}
		}

		return $item_list;
	}

}

global $wc_predictive_search;
$wc_predictive_search = new WC_Predictive_Search();

?>