<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Term_Relationships_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_term_relationships = $wpdb->prefix. "ps_term_relationships";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_term_relationships'") != $table_ps_term_relationships) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_term_relationships}` (
					object_id bigint(20) NOT NULL,
					term_id bigint(20) NOT NULL,
					PRIMARY KEY (object_id,term_id),
					KEY term_id (term_id)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Term Relationships Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_term_relationships';

		$wpdb->ps_term_relationships = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_term_relationships';
	}

	/**
	 * Predictive Search Term Relationships Table - return sql
	 *
	 * @return void
	 */
	public function get_sql( $term_id ) {
		if ( $term_id > 0)

		global $wpdb;

		$sql   = array();
		$where = array();

		$items_include = $this->get_array_objects( $term_id );

		if ( is_array( $items_include ) && count( $items_include ) > 0 ) {
			$ids_include    = implode( ',', $items_include );

			$where[] = " AND pp.post_id IN ({$ids_include}) ";

			$sql['where'] = $where;
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Term Relationships
	 */
	public function insert_item( $object_id, $term_id, $check_existed = true ) {
		global $wpdb;

		if ( ! $check_existed || $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM {$wpdb->ps_term_relationships} WHERE object_id = %d AND term_id = %d LIMIT 0, 1 )", $object_id, $term_id ) ) != '1' ) {
			return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_term_relationships} VALUES(%d, %d)", $object_id, $term_id ) );
		} else {
			return false;
		}
	}

	/**
	 * Get Predictive Search Term Relationships
	 */
	public function get_terms( $object_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT term_id FROM {$wpdb->ps_term_relationships} WHERE object_id = %d ", $object_id ) );
	}

	/**
	 * Get Predictive Search Array Term Relationships
	 */
	public function get_array_terms( $object_id ) {
		global $wpdb;
		return $wpdb->get_col( $wpdb->prepare( "SELECT term_id FROM {$wpdb->ps_term_relationships} WHERE object_id = %d ", $object_id ) );
	}

	/**
	 * Get Predictive Search Term Relationships
	 */
	public function get_objects( $term_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT object_id FROM {$wpdb->ps_term_relationships} WHERE term_id = %d ", $term_id ) );
	}

	/**
	 * Get Predictive Search Array Term Relationships
	 */
	public function get_array_objects( $term_id ) {
		global $wpdb;
		return $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM {$wpdb->ps_term_relationships} WHERE term_id = %d ", $term_id ) );
	}

	/**
	 * Delete Predictive Search Term Relationships
	 */
	public function delete_object( $object_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_term_relationships} WHERE object_id = %d ", $object_id ) );
	}

	/**
	 * Delete Predictive Search Term Relationships
	 */
	public function delete_term( $term_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_term_relationships} WHERE term_id = %d ", $term_id ) );
	}

	/**
	 * Empty Predictive Search Term Relationships
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_term_relationships}" );
	}
}

global $wc_ps_term_relationships_data;
$wc_ps_term_relationships_data = new WC_PS_Term_Relationships_Data();
?>
