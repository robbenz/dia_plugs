<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Product_SKU_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_product_sku = $wpdb->prefix. "ps_product_sku";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_product_sku'") != $table_ps_product_sku) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_product_sku}` (
					post_id bigint(20) NOT NULL,
					sku text NULL,
					PRIMARY KEY  (post_id)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Product SKU Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_product_sku';

		$wpdb->ps_product_sku = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_product_sku';
	}

	/**
	 * Predictive Search Product SKU Table - return sql
	 *
	 * @return void
	 */
	public function get_sql( $search_keyword = '', $search_keyword_nospecial = '', $number_row, $start = 0, $check_existed = false ) {
		if ( '' == $search_keyword && '' == $search_keyword_nospecial ) {
			return false;
		}

		global $wpdb;
		global $wc_ps_exclude_data;

		$sql     = array();
		$join    = array();
		$where   = array();
		$groupby = array();
		$orderby = array();

		$items_excluded = $wc_ps_exclude_data->get_array_items( 'product' );
		$id_excluded    = implode( ',', $items_excluded );

		$sql['select']   = array();
		if ( $check_existed ) {
			$sql['select'][] = " 1 ";
		} else {
			$sql['select'][] = " pp.* ";
		}

		$sql['from']   = array();
		$sql['from'][] = " {$wpdb->ps_product_sku} AS pp ";

		$sql['join']   = $join;

		$where[] = " 1=1 ";

		if ( '' != trim( $id_excluded ) ) {
			$where[] = " AND pp.post_id NOT IN ({$id_excluded}) ";
		}

		$where_title = ' ( ';
		$where_title .= $wpdb->prepare( WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku' ) . " LIKE '%s' OR " . WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku' ) . " LIKE '%s' ", $search_keyword.'%', '% '.$search_keyword.'%' );
		if ( '' != $search_keyword_nospecial ) {
			$where_title .= " OR ". $wpdb->prepare( WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku' ) . " LIKE '%s' OR " . WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'pp.sku' ) . " LIKE '%s' ", $search_keyword_nospecial.'%', '% '.$search_keyword_nospecial.'%' );
		}
		$where_title .= ' ) ';

		$where['search']   = array();
		$where['search'][] = ' ( ' . $where_title . ' ) ';

		$sql['where']      = $where;

		$sql['groupby']    = array();
		$sql['groupby'][]  = ' pp.post_id ';

		$sql['orderby']    = array();
		if ( $check_existed ) {
			$sql['limit']      = " 0 , 1 ";
		} else {
			$sql['orderby'][]  = $wpdb->prepare( " pp.sku NOT LIKE '%s' ASC, pp.sku ASC ", $search_keyword.'%' );

			$sql['limit']      = " {$start} , {$number_row} ";
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Product SKU
	 */
	public function insert_item( $post_id, $sku = '' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_product_sku} VALUES(%d, %s)", $post_id, stripslashes( $sku ) ) );
	}

	/**
	 * Update Predictive Search Product SKU
	 */
	public function update_item( $post_id, $sku = '' ) {
		global $wpdb;

		$value = $this->get_item( $post_id );
		if ( NULL == $value ) {
			return $this->insert_item( $post_id, $sku );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_product_sku} SET sku = %s WHERE post_id = %d ", stripslashes( $sku ), $post_id ) );
		}
	}

	/**
	 * Get Predictive Search Product SKU
	 */
	public function get_item( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT sku FROM {$wpdb->ps_product_sku} WHERE post_id = %d LIMIT 0,1 ", $post_id ) );
	}

	/**
	 * Delete Predictive Search Product SKU
	 */
	public function delete_item( $post_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_product_sku} WHERE post_id = %d ", $post_id ) );
	}

	/**
	 * Empty Predictive Search Product SKU
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_product_sku}" );
	}
}

global $wc_ps_product_sku_data;
$wc_ps_product_sku_data = new WC_PS_Product_SKU_Data();
?>
