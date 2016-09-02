<?php
/**
 * Get VFB data from the database
 *
 * Queries the database for forms, fields, etc
 *
 * @since      3.0
 */
class VFB_Pro_Data {

	/**
	 * Get settings for a single form
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_form_settings( $id ) {
		global $wpdb;

		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . VFB_FORMS_TABLE_NAME . " WHERE id = %d", $id ), ARRAY_A );

		if ( $form != null ) {
			// Unserialize form options before returning results
			$form['data'] = unserialize( $form['data'] );
			$form['data'] = stripslashes_deep( $form['data'] );

			return $form;
		}

		return false;
	}

	/**
	 * Get email settings
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_email_settings( $id ) {
		global $wpdb;

		$objects  = array();
		$settings = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . VFB_FORM_META_TABLE_NAME . ' WHERE form_id = %d', $id ) );

		if ( is_array( $settings ) ) {
			foreach( $settings as $setting ) {
				if ( is_serialized ( $setting->meta_value ) ) {
					$setting->meta_value = unserialize( $setting->meta_value );
				}
				$objects[ $setting->meta_key ] = $setting->meta_value;
			}
		}

		return $objects;
	}

	/**
	 * Get confirmation settings
	 *
	 * This is a wrapper for get_email_settings in case
	 * we need to change this in the future
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_confirmation_settings( $id ) {
		$objects = $this->get_email_settings( $id );

		return $objects;
	}

	/**
	 * Get email design settings
	 *
	 * This is a wrapper for get_email_settings in case
	 * we need to change this in the future
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_email_design_settings( $id ) {
		$objects = $this->get_email_settings( $id );

		return $objects;
	}

	/**
	 * Get rule settings
	 *
	 * This is a wrapper for get_email_settings in case
	 * we need to change this in the future
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_rule_settings( $id ) {
		$objects = $this->get_email_settings( $id );

		return $objects;
	}

	/**
	 * Get add-on settings
	 *
	 * This is a wrapper for get_email_settings in case
	 * we need to change this in the future
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_addon_settings( $id ) {
		$objects = $this->get_email_settings( $id );

		return $objects;
	}

	/**
	 * Get main VFB settings
	 *
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_vfb_settings() {
		$settings = get_option( 'vfbp_settings' );

		return $settings;
	}

	/**
	 * Get all fields for a single form
	 *
	 * @access public
	 * @param mixed $id
	 * @param string $orderby (default: 'ORDER BY field_order ASC')
	 * @return void
	 */
	public function get_fields( $id, $orderby = 'ORDER BY field_order ASC' ) {
		global $wpdb;

		$fields = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . VFB_FIELDS_TABLE_NAME . " WHERE form_id = %d ". $orderby, $id ), ARRAY_A );

		if( is_array( $fields ) && !empty( $fields ) ) {
			$x = 0;
			$count = count( $fields ) - 1;

			while( $x <= $count ) {
				$fields[ $x ]['data'] = unserialize( $fields[ $x ]['data'] );
				$x++;
			}
		}

		return $fields;
	}

	/**
	 * Get field by form ID
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_field_by_id( $id ) {
		global $wpdb;

		$field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . VFB_FIELDS_TABLE_NAME . " WHERE id = %d", $id ), ARRAY_A );

		if( $field != null ) {
			$field['data'] = unserialize( $field['data'] );
			return $field;
		}
		else {
			return false;
		}
	}

	/**
	 * Get form by form ID
	 *
	 * @access public
	 * @return void
	 */
	public function get_form_by_id( $id ) {
		global $wpdb;

		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . VFB_FORMS_TABLE_NAME . " WHERE id = %d", $id ), ARRAY_A );

		if( $form != null ) {
			$form['data'] = unserialize( $form['data'] );
			return $form;
		}
		else {
			return false;
		}
	}

	/**
	 * Get meta by form ID
	 *
	 * This is a wrapper for get_email_settings in case
	 * we need to change this in the future
	 *
	 * @since 3.0
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_meta_by_id( $id ) {
		$objects = $this->get_email_settings( $id );

		return $objects;
	}

	/**
	 * Get single value of entry metadata from the _postmeta table
	 *
	 * @access public
	 * @param mixed $entry_id
	 * @param mixed $meta_key
	 * @return void
	 */
	public function get_entry_meta_by_id( $entry_id, $meta_key ) {
		if ( !$entry_id )
			return;

		$meta = get_post_meta( $entry_id, $meta_key, true );

		return $meta;
	}

	/**
	 * Get a single entry's metadata by sequence ID (referenced as Entry ID elsewhere).
	 *
	 * @access public
	 * @param mixed $seq_num
	 * @param mixed $form_id
	 * @return void
	 */
	public function get_entry_by_seq_num( $seq_num, $form_id ) {
		global $wpdb;

		$post_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT mt.post_id
			FROM {$wpdb->postmeta} AS mt
			INNER JOIN {$wpdb->postmeta} AS mt1
			ON (mt.post_id = mt1.post_id)
			WHERE (mt.meta_key = '_vfb_form_id' AND CAST(mt.meta_value AS CHAR) = %d)
			AND ( mt1.meta_key = '_vfb_seq_num' AND CAST(mt1.meta_value AS CHAR) = %d)
			",
			$form_id,
			$seq_num
			)
		);

		$meta = get_post_meta( $post_id );

		return $meta;
	}

	/**
	 * Get all forms
	 *
	 * @access public
	 * @return void
	 */
	public function get_all_forms( $where = '' ) {
		global $wpdb;

		$forms = $wpdb->get_results( "SELECT * FROM " . VFB_FORMS_TABLE_NAME . $where, ARRAY_A );

		if( is_array( $forms ) && !empty( $forms ) ) {
			$x = 0;
			$count = count( $forms ) - 1;

			while( $x <= $count ) {
				$forms[ $x ]['data'] = maybe_unserialize( $forms[ $x ]['data'] );
				$x++;
			}
		}

		return $forms;
	}

	/**
	 * Get all fields
	 *
	 * @access public
	 * @return void
	 */
	public function get_all_fields() {
		global $wpdb;

		$fields = $wpdb->get_results( "SELECT * FROM " . VFB_FIELDS_TABLE_NAME, ARRAY_A );

		if( is_array( $fields ) && !empty( $fields ) ) {
			$x = 0;
			$count = count( $fields ) - 1;

			while( $x <= $count ) {
				$fields[ $x ]['data'] = maybe_unserialize( $fields[ $x ]['data'] );
				$x++;
			}
		}

		return $fields;
	}

	/**
	 * Get all meta
	 *
	 * @access public
	 * @return void
	 */
	public function get_all_meta() {
		global $wpdb;

		$metas = $wpdb->get_results( "SELECT * FROM " . VFB_FORM_META_TABLE_NAME, ARRAY_A );

		if ( is_array( $metas ) && !empty( $metas ) ) {
			foreach( $metas as $meta ) {
				$meta['meta_value'] = maybe_unserialize( $meta['meta_value'] );
			}
		}

		return $metas;
	}

	/**
	 * get_entries_count function.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param string $status (default: 'publish')
	 * @return void
	 */
	public function get_entries_count( $form_id, $status = 'publish', $where = '' ) {
		global $wpdb;

		$where = esc_sql( $where );

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT pm.post_id)
			FROM {$wpdb->postmeta} pm
			JOIN {$wpdb->posts} p ON (p.id = pm.post_id)
			WHERE pm.meta_key = '_vfb_form_id'
			AND pm.meta_value = '%d'
			AND p.post_type = 'vfb_entry'
			AND p.post_status = '%s'
			{$where}",
			$form_id,
			$status
			)
		);

		if ( null == $count )
			$count = 0;

		return $count;
	}

	/**
	 * All entries data from the postmeta table for a single form
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param string $where (default: '')
	 * @return void
	 */
	public function get_entries_meta_by_form_id( $form_id, $where = '' ) {
		global $wpdb;

		$meta = $wpdb->get_results( $wpdb->prepare(
			"SELECT p.ID, p.post_date
			FROM {$wpdb->posts} AS p
			INNER JOIN {$wpdb->postmeta} AS pm
			ON (p.ID = pm.post_id)
			WHERE pm.meta_key = '_vfb_form_id'
			AND CAST(pm.meta_value AS CHAR) = '%d'
			{$where}
			",
			$form_id
			),
			ARRAY_A
		);

		return $meta;
	}

	/**
	 * Update metadata for the specified object. If no value already exists for the specified object
	 * ID and metadata key, the metadata will be added.
	 *
	 * @since 3.0
	 * @access private
	 * @param mixed $id
	 * @param mixed $meta_key
	 * @param mixed $meta_value
	 * @return void
	 */
	public function update_metadata( $id, $meta_key, $meta_value ) {
		global $wpdb;

		if ( ! $meta_id = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . VFB_FORM_META_TABLE_NAME . " WHERE form_id = %d AND meta_key = %s", $id, $meta_key ) ) )
			return $this->add_metadata( $id, $meta_key, $meta_value );

		$meta_value = maybe_serialize( $meta_value );

		$wpdb->update(
			VFB_FORM_META_TABLE_NAME,
			array(
				'meta_value' => $meta_value
			),
			array(
				'meta_key'  => $meta_key,
				'form_id' => $id
			)
		);
	}

	/**
	 * Add metadata for the specified object.
	 *
	 * @since 3.0
	 * @access private
	 * @param mixed $id
	 * @param mixed $meta_key
	 * @param mixed $meta_value
	 * @return void
	 */
	public function add_metadata( $id, $meta_key, $meta_value ) {
		global $wpdb;

		$meta_value = maybe_serialize( $meta_value );

		$wpdb->insert(
			VFB_FORM_META_TABLE_NAME,
			array(
				'form_id'    => $id,
				'meta_key'   => $meta_key,
				'meta_value' => $meta_value
			)
		);
	}
}