<?php
/**
 * Handles saving the entry
 *
 * @since      3.0
 */
class VFB_Pro_Save_Entry {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * create function.
	 *
	 * @access public
	 * @param string $form_id
	 * @return void
	 */
	public function create( $form_id ) {
		$format  = new VFB_Pro_Format();
		$akismet = new VFB_Pro_Akismet();
		$upload  = new VFB_Pro_Upload( $form_id );

		$post = array(
		  'post_status' => 'publish',
		  'post_type'   => 'vfb_entry'
		);

		$current_user = wp_get_current_user();
		if ( $current_user instanceof WP_User )
			$post['post_author'] = $current_user->ID;

		$entry_id = wp_insert_post( $post );

		$entry_num = $this->get_seq_num( $form_id );
		$this->update_seq_num( $form_id, $entry_num );

		add_post_meta( $entry_id, '_vfb_form_id', $form_id );
		add_post_meta( $entry_id, '_vfb_seq_num', $entry_num );
		add_post_meta( $entry_id, '_vfb_entry_id', $entry_id );

		// Build array from $_POST
		$data = array();
		foreach ( $_POST as $key => $val ) {
			// Remove special form fields that begin with an underscore
			if ( substr( $key, 0, 1 ) != '_' )
				$data[ $key ] = $val;

			// Special case to handle Radio "Other" option
			if ( strpos( $key, '-other' ) !== false ) {
				// If "Other" text input is not empty
				if ( !empty( $val ) ) {
					// Get the "non-Other" name attr
					$temp_key = preg_replace( '/(.*?)-other/', '$1', $key );

					// Replace the vfb-field-{xx} value with the "Other" input
					$data[ $temp_key ] = $val;
				}

				// Remove "Other" radio value from entry
				unset( $data[ $key ] );
			}
		}

		// Build array from $_FILES
		$files = array();
		if ( isset( $_FILES ) ) {
			foreach ( $_FILES as $key => $file ) {
				$files[ $key ] = $upload->handle_upload( $file, $entry_id );
			}
		}

		// Add post meta
		foreach ( $data as $key => $val ) {
			$field_id = str_replace( 'vfb-field-', '', $key );
			$key      = str_replace( 'vfb-', '_vfb_', $key );
			$val      = $format->format_field( $form_id, $field_id, $val );

			add_post_meta( $entry_id, $key, $val );

			// Set Akismet variables
			$akismet->set_vars( $field_id, $val );
		}

		// Add post meta for uploaded files
		foreach ( $files as $key => $val ) {
			$field_id = str_replace( 'vfb-field-', '', $key );
			$key      = str_replace( 'vfb-', '_vfb_', $key );
			$val      = is_array( $val ) ? implode( ",\n", $val ) : $val;

			add_post_meta( $entry_id, $key, $val );
		}

		// Akismet check - updates post status to 'spam'
		$akismet->spam_check( $entry_id );

		/**
		 * Action that fires at the end of creating a new enty
		 *
		 * Passes the Entry ID and Form ID
		 *
		 * @since 3.0
		 *
		 */
		do_action( 'vfbp_after_save_entry', $entry_id, $form_id );

		return $entry_id;
	}

	/**
	 * Update the entry sequence number
	 *
	 * This is stored in the forms table for each form
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $entry_num
	 * @return void
	 */
	public function update_seq_num( $form_id, $entry_num ) {
		global $wpdb;

		$vfbdb = new VFB_Pro_Data();
		$form  = $vfbdb->get_form_by_id( $form_id );

		// Update the last entry sequence ID
		$form['data']['last-entry'] = $entry_num;

		$save_data = array(
			'data' => serialize( $form['data'] ),
		);

		$wpdb->update(
			VFB_FORMS_TABLE_NAME,
			$save_data,
			array(
				'id' => $form_id
			)
		);
	}

	/**
	 * Get the entry sequence number
	 *
	 * If no sequence number exists, start at 1
	 *
	 * @access public
	 * @param mixed $form_id
	 * @return void
	 */
	public function get_seq_num( $form_id ) {
		$vfbdb = new VFB_Pro_Data();
		$form  = $vfbdb->get_form_by_id( $form_id );

		$entry_num = 1;
		if ( isset( $form['data']['last-entry'] ) )
			$entry_num = $form['data']['last-entry'] + 1;

		return $entry_num;
	}
}