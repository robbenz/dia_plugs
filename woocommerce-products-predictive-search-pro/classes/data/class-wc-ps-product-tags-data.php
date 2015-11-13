<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Product_Tags_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_product_tags = $wpdb->prefix. "ps_product_tags";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_product_tags'") != $table_ps_product_tags) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_product_tags}` (
					term_id bigint(20) NOT NULL,
					term_taxonomy_id bigint(20) NOT NULL,
					name varchar(200) NOT NULL,
					PRIMARY KEY  (term_id)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Product Tags Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_product_tags';

		$wpdb->ps_product_tags = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_product_tags';
	}

	/**
	 * Predictive Search Product Tags Table - return sql
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

		$items_excluded = $wc_ps_exclude_data->get_array_items( 'product_tag' );
		$id_excluded    = implode( ',', $items_excluded );

		$sql['select']   = array();
		if ( $check_existed ) {
			$sql['select'][] = " 1 ";
		} else {
			$sql['select'][] = " ppt.* ";
		}

		$sql['from']   = array();
		$sql['from'][] = " {$wpdb->ps_product_tags} AS ppt ";

		$sql['join']   = $join;

		$where[] = " 1=1 ";

		if ( '' != trim( $id_excluded ) ) {
			$where[] = " AND ppt.term_id NOT IN ({$id_excluded}) ";
		}

		$where_title = ' ( ';
		$where_title .= $wpdb->prepare( WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' OR " . WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' ", $search_keyword.'%', '% '.$search_keyword.'%' );
		if ( '' != $search_keyword_nospecial ) {
			$where_title .= " OR ". $wpdb->prepare( WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' OR " . WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' ", $search_keyword_nospecial.'%', '% '.$search_keyword_nospecial.'%' );
		}
		$search_keyword_no_s_letter = WC_Predictive_Search_Functions::remove_s_letter_at_end_word( $search_keyword );
		if ( $search_keyword_no_s_letter != false ) {
			$where_title .= " OR ". $wpdb->prepare( WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' OR " . WC_Predictive_Search_Functions::remove_special_characters_in_mysql( 'ppt.name' ) . " LIKE '%s' ", $search_keyword_no_s_letter.'%', '% '.$search_keyword_no_s_letter.'%' );
		}
		$where_title .= ' ) ';

		$where['search']   = array();
		$where['search'][] = ' ( ' . $where_title . ' ) ';

		$sql['where']      = $where;

		$sql['groupby']    = array();
		$sql['groupby'][]  = ' ppt.term_id ';

		$sql['orderby']    = array();
		if ( $check_existed ) {
			$sql['limit']      = " 0 , 1 ";
		} else {
			$sql['orderby'][]  = $wpdb->prepare( " ppt.name NOT LIKE '%s' ASC, ppt.name ASC ", $search_keyword.'%' );

			$sql['limit']      = " {$start} , {$number_row} ";
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Product Category
	 */
	public function insert_item( $term_id, $term_taxonomy_id, $name = '' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_product_tags} VALUES(%d, %d, %s)", $term_id, $term_taxonomy_id, stripslashes( $name ) ) );
	}

	/**
	 * Update Predictive Search Product Tag
	 */
	public function update_item( $term_id, $name = '' ) {
		global $wpdb;

		$value = $this->get_item( $term_id );
		if ( NULL == $value ) {
			return $this->insert_item( $term_id, $name );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_product_tags} SET name = %s WHERE term_id = %d ", stripslashes( $name ), $term_id ) );
		}
	}

	/**
	 * Get Predictive Search Product Tag
	 */
	public function get_item( $term_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->ps_product_tags} WHERE term_id = %d LIMIT 0,1 ", $term_id ) );
	}

	/**
	 * Delete Predictive Search Product Tag
	 */
	public function delete_item( $term_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_product_tags} WHERE term_id = %d ", $term_id ) );
	}

	/**
	 * Empty Predictive Search Product Tags
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_product_tags}" );
	}
}

global $wc_ps_product_tags_data;
$wc_ps_product_tags_data = new WC_PS_Product_Tags_Data();
?>
