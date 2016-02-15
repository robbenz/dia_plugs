<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_Predictive_Search_Synch
{
	public function __construct() {

		// Synch for post
		add_action( 'init', array( $this, 'sync_process_post' ), 1 );

		// Synch for Product Category
		add_action( 'created_product_cat', array( $this, 'synch_save_product_cat' ), 10, 2 );
		add_action( 'edited_product_cat', array( $this, 'synch_save_product_cat' ), 10, 2 );
		add_action( 'delete_product_cat', array( $this, 'synch_delete_product_cat' ), 10, 3 );

		// Synch for Product Tag
		add_action( 'created_product_tag', array( $this, 'synch_save_product_tag' ), 10, 2 );
		add_action( 'edited_product_tag', array( $this, 'synch_save_product_tag' ), 10, 2 );
		add_action( 'delete_product_tag', array( $this, 'synch_delete_product_tag' ), 10, 3 );

		// Synch for Term Relationships
		add_action( 'delete_term', array( $this, 'synch_delete_term_relationships' ), 10, 4 );

		/*
		 *
		 * Synch for custom mysql query from 3rd party plugin
		 * Call below code on 3rd party plugin when create post by mysql query
		 * do_action( 'mysql_inserted_post', $post_id );
		 */
		add_action( 'mysql_inserted_post', array( $this, 'synch_mysql_inserted_post' ) );
	}

	public function sync_process_post() {
		add_action( 'save_post', array( $this, 'synch_save_post' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'synch_delete_post' ) );
	}

	public function migrate_posts() {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		// Check if synch data is stopped at latest run then continue synch without empty all the tables
		$synched_data = get_option( 'wc_predictive_search_synched_data', 0 );

		if ( 0 == $synched_data ) {
			// continue synch data from stopped post ID
			$stopped_ID = $wc_ps_posts_data->get_latest_post_id();
			if ( empty( $stopped_ID ) || is_null( $stopped_ID ) ) {
				$stopped_ID = 0;
			}
		} else {
			// Empty all tables
			$wc_ps_posts_data->empty_table();
			$wc_ps_postmeta_data->empty_table();
			$wc_ps_product_sku_data->empty_table();

			update_option( 'wc_predictive_search_synched_data', 0 );

			$stopped_ID = 0;
		}

		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product' ) );

		$all_posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE ID > %d AND post_status = %s AND post_type IN ('". implode("','", $post_types ) ."') ORDER BY ID ASC" , $stopped_ID, 'publish'
			)
		);

		if ( $all_posts ) {

			$woocommerce_search_focus_enable = get_option( 'woocommerce_search_focus_enable', 'no' );
			$woocommerce_search_focus_plugin = get_option( 'woocommerce_search_focus_plugin', 'none' );

			foreach ( $all_posts as $item ) {
				$post_id       = $item->ID;

				$item_existed = $wc_ps_posts_data->get_item( $post_id );
				if ( NULL == $item_existed ) {
					$wc_ps_posts_data->insert_item( $post_id, $item->post_title, $item->post_type );
				}

				if ( 'yes' == $woocommerce_search_focus_enable && 'none' != $woocommerce_search_focus_plugin ) {

					if ( 'yoast_seo_plugin' == $woocommerce_search_focus_plugin ) {
						$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
						if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
							$wc_ps_postmeta_data->add_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
						}
					}

					if ( 'all_in_one_seo_plugin' == $woocommerce_search_focus_plugin ) {
						$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );
						if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
							$wc_ps_postmeta_data->add_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
						}
					}
				}

				if ( in_array( $item->post_type, array( 'product', 'product_variation' ) ) ) {
					$sku = get_post_meta( $post_id, '_sku', true );
					if ( ! empty( $sku ) && '' != trim( $sku ) ) {
						$item_existed = $wc_ps_product_sku_data->get_item( $post_id );
						if ( NULL == $item_existed ) {
							$wc_ps_product_sku_data->insert_item( $post_id, $sku );
						}
					}

					// Migrate Product Out of Stock
					$outofstock = get_post_meta( $post_id, '_stock_status', true );
					if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
						$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
					} else {
						$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
					}

				}
			}
		}

		update_option( 'wc_predictive_search_synched_data', 1 );
	}

	public function migrate_products_out_of_stock() {
		global $wpdb;
		global $wc_ps_postmeta_data;

		$all_out_of_stock = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s" ,'_stock_status', 'outofstock'
			)
		);

		if ( $all_out_of_stock ) {
			foreach ( $all_out_of_stock as $item ) {
				$wc_ps_postmeta_data->update_item_meta( $item->post_id, '_stock_status', 'outofstock' );
			}
		}
	}

	public function migrate_product_categories() {
		global $wpdb;
		global $wc_ps_product_categories_data;

		// Empty table
		$wc_ps_product_categories_data->empty_table();

		$all_categories = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE taxonomy = %s ", 'product_cat'
			)
		);

		if ( $all_categories ) {
			foreach ( $all_categories as $item ) {
				$item_existed = $wc_ps_product_categories_data->get_item( $item->term_id );
				if ( NULL == $item_existed ) {
					$wc_ps_product_categories_data->insert_item( $item->term_id, $item->term_taxonomy_id, $item->name );
				}
			}
		}
	}

	public function migrate_product_tags() {
		global $wpdb;
		global $wc_ps_product_tags_data;

		// Empty table
		$wc_ps_product_tags_data->empty_table();

		$all_tags = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT t.term_id, t.name, tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE taxonomy = %s ", 'product_tag'
			)
		);

		if ( $all_tags ) {
			foreach ( $all_tags as $item ) {
				$item_existed = $wc_ps_product_tags_data->get_item( $item->term_id );
				if ( NULL == $item_existed ) {
					$wc_ps_product_tags_data->insert_item( $item->term_id, $item->term_taxonomy_id, $item->name );
				}
			}
		}
	}

	public function migrate_term_relationships() {
		global $wpdb;
		global $wc_ps_term_relationships_data;

		// Empty table
		$wc_ps_term_relationships_data->empty_table();

		$all_relationships = $wpdb->get_results( "SELECT tr.object_id, tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('category', 'post_tag', 'product_cat', 'product_tag') ORDER BY tr.object_id ASC" );
		if ( is_array( $all_relationships)  && count( $all_relationships ) > 0 ) {
			foreach ( $all_relationships as $item ) {
				$wc_ps_term_relationships_data->insert_item( $item->object_id, $item->term_id );
			}
		}
	}

	public function synch_full_database() {
		$this->migrate_posts();
		$this->migrate_product_categories();
		$this->migrate_product_tags();
		$this->migrate_term_relationships();
	}

	public function delete_post_data( $post_id ) {
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;

		$wc_ps_posts_data->delete_item( $post_id );
		$wc_ps_postmeta_data->delete_item_metas( $post_id );
		$wc_ps_product_sku_data->delete_item( $post_id );
	}

	public function synch_save_post( $post_id, $post ) {
		global $wpdb;
		global $wc_ps_posts_data;
		global $wc_ps_postmeta_data;
		global $wc_ps_product_sku_data;
		global $wc_ps_term_relationships_data;

		$this->delete_post_data( $post_id );

		if ( 'publish' == $post->post_status ) {
			$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
			// For Yoast SEO need to check if $_POST['yoast_wpseo_focuskw_text_input'] is existed then use it instead of use post meta
			if ( isset( $_POST['yoast_wpseo_focuskw_text_input'] ) ) {
				$yoast_keyword = trim( $_POST['yoast_wpseo_focuskw_text_input'] );
			}
			$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );

			$wc_ps_posts_data->update_item( $post_id, $post->post_title, $post->post_type );

			if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
				$wc_ps_postmeta_data->update_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
			}

			if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
				$wc_ps_postmeta_data->update_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
			}

			$wc_ps_term_relationships_data->delete_object( $post_id );

			if ( 'post' == $post->post_type ) {
				$all_relationships = $wpdb->get_results( "SELECT tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('category', 'post_tag') AND tr.object_id = {$post_id} ORDER BY tr.object_id ASC" );
				if ( is_array( $all_relationships)  && count( $all_relationships ) > 0 ) {
					foreach ( $all_relationships as $item ) {
						$wc_ps_term_relationships_data->insert_item( $post_id, $item->term_id );
					}
				}
			} elseif ( 'product' == $post->post_type ) {
				$sku = get_post_meta( $post_id, '_sku', true );
				if ( ! empty( $sku ) && '' != trim( $sku ) ) {
					$wc_ps_product_sku_data->update_item( $post_id, $sku );
				}
				$all_relationships = $wpdb->get_results( "SELECT tt.term_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.taxonomy IN ('product_cat', 'product_tag') AND tr.object_id = {$post_id} ORDER BY tr.object_id ASC" );
				if ( is_array( $all_relationships)  && count( $all_relationships ) > 0 ) {
					foreach ( $all_relationships as $item ) {
						$wc_ps_term_relationships_data->insert_item( $post_id, $item->term_id );
					}
				}

				// Migrate Product Out of Stock
				$outofstock = get_post_meta( $post_id, '_stock_status', true );
				if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}

			if ( 'page' == $post->post_type ) {
				global $woocommerce_search_page_id;

				// flush rewrite rules if page is editing is WooCommerce Search Result page
				if ( $post_id == $woocommerce_search_page_id ) {
					flush_rewrite_rules();
				}
			}

		}
	}

	public function synch_delete_post( $post_id ) {
		global $wc_ps_keyword_data;
		global $wc_ps_exclude_data;
		global $wc_ps_term_relationships_data;

		$this->delete_post_data( $post_id );

		$post_type = get_post_type( $post_id );

		$wc_ps_keyword_data->delete_item( $post_id );
		$wc_ps_exclude_data->delete_item( $post_id, $post_type );

		$wc_ps_term_relationships_data->delete_object( $post_id );
	}

	public function synch_save_product_cat( $term_id, $tt_id ) {
		global $wc_ps_product_categories_data;

		$term = get_term( $term_id, 'product_cat' );
		$wc_ps_product_categories_data->update_item( $term_id, $term->name );
	}

	public function synch_save_product_tag( $term_id, $tt_id ) {
		global $wc_ps_product_tags_data;

		$term = get_term( $term_id, 'product_tag' );
		$wc_ps_product_tags_data->update_item( $term_id, $term->name );
	}

	public function synch_delete_product_cat( $term_id, $tt_id, $deleted_term ) {
		global $wc_ps_product_categories_data;
		global $wc_ps_exclude_data;

		$wc_ps_product_categories_data->delete_item( $term_id );
		$wc_ps_exclude_data->delete_item( $term_id, 'product_cat' );
	}

	public function synch_delete_product_tag( $term_id, $tt_id, $deleted_term ) {
		global $wc_ps_product_tags_data;
		global $wc_ps_exclude_data;

		$wc_ps_product_tags_data->delete_item( $term_id );
		$wc_ps_exclude_data->delete_item( $term_id, 'product_tag' );
	}

	public function synch_delete_term_relationships( $term_id, $tt_id, $taxonomy, $deleted_term ) {
		global $wc_ps_term_relationships_data;
		$wc_ps_term_relationships_data->delete_term( $term_id );
	}

	public function synch_mysql_inserted_post( $post_id = 0 ) {
		if ( $post_id < 1 ) return;

		global $wpdb;
		$post_types = apply_filters( 'predictive_search_post_types_support', array( 'post', 'page', 'product' ) );

		$item = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->posts} WHERE ID = %d AND post_status = %s AND post_type IN ('". implode("','", $post_types ) ."')" , $post_id, 'publish'
			)
		);

		if ( $item ) {
			global $wc_ps_posts_data;
			global $wc_ps_postmeta_data;
			global $wc_ps_product_sku_data;

			$yoast_keyword = get_post_meta( $post_id, '_yoast_wpseo_focuskw', true );
			$wpseo_keyword = get_post_meta( $post_id, '_aioseop_keywords', true );

			$item_existed = $wc_ps_posts_data->get_item( $post_id );
			if ( NULL == $item_existed ) {
				$wc_ps_posts_data->insert_item( $post_id, $item->post_title, $item->post_type );
			}

			if ( ! empty( $yoast_keyword ) && '' != trim( $yoast_keyword ) ) {
				$wc_ps_postmeta_data->add_item_meta( $post_id, '_yoast_wpseo_focuskw', $yoast_keyword );
			}

			if ( ! empty( $wpseo_keyword ) && '' != trim( $wpseo_keyword ) ) {
				$wc_ps_postmeta_data->add_item_meta( $post_id, '_aioseop_keywords', $wpseo_keyword );
			}

			if ( 'product' == $item->post_type ) {
				$sku = get_post_meta( $post_id, '_sku', true );
				if ( ! empty( $sku ) && '' != trim( $sku ) ) {
					$item_existed = $wc_ps_product_sku_data->get_item( $post_id );
					if ( NULL == $item_existed ) {
						$wc_ps_product_sku_data->insert_item( $post_id, $sku );
					}
				}

				// Migrate Product Out of Stock
				$outofstock = get_post_meta( $post_id, '_stock_status', true );
				if ( ! empty( $outofstock ) && 'outofstock' == trim( $outofstock ) ) {
					$wc_ps_postmeta_data->update_item_meta( $post_id, '_stock_status', 'outofstock' );
				} else {
					$wc_ps_postmeta_data->delete_item_meta( $post_id, '_stock_status' );
				}
			}
		}
	}
}

global $wc_ps_synch;
$wc_ps_synch = new WC_Predictive_Search_Synch();
?>
