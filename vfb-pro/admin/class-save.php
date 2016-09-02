<?php
/**
 * Save admin forms and field
 *
 * Saves new forms, edited forms, settings, etc
 *
 * @since      3.0
 */
class VFB_Pro_Admin_Save {

	/**
	 * Hook our save functions to the admin
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_new_form' ) );
		add_action( 'admin_init', array( $this, 'save_fields' ) );
		add_action( 'admin_init', array( $this, 'save_form_settings' ) );
		add_action( 'admin_init', array( $this, 'save_email_settings' ) );
		add_action( 'admin_init', array( $this, 'save_confirmation_settings' ) );
		add_action( 'admin_init', array( $this, 'save_design_settings' ) );
		add_action( 'admin_init', array( $this, 'save_rules_settings' ) );
		add_action( 'admin_init', array( $this, 'save_addon_settings' ) );
		add_action( 'admin_init', array( $this, 'save_vfb_settings' ) );
		add_action( 'admin_init', array( $this, 'trash_form' ) );
		add_action( 'admin_init', array( $this, 'duplicate_form' ) );
	}

	/**
	 * Add New form
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function add_new_form() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'create-form' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_create_form' );

		$data = array();

		foreach ( $_POST as $key => $val ) {
			if ( substr( $key, 0, 1 ) != '_' )
				$data[ $key ] = $val;
		}

		$data = stripslashes_deep( $data );

		$title  = $data['title'];
		$status = $data['status'];

		$save_data = array(
			'title'   => $title,
			//'data'    => serialize( $data ),
			'status'  => $status,
		);

		$wpdb->insert(
			VFB_FORMS_TABLE_NAME,
			$save_data,
			'%s' // formats of all $save_data
		);

		$form_id = $wpdb->insert_id;

		foreach ( $data['settings'] as $meta_key => $meta_value ) {
			$this->add_metadata( $form_id, $meta_key, $meta_value );
		}

		$redirect = add_query_arg( array( 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) );

		wp_redirect( esc_url_raw( $redirect ) );
		exit();
	}

	/**
	 * Save fields
	 *
	 * @access public
	 * @return void
	 */
	public function save_fields() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-fields' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_fields_settings' );

		$form_id = absint( $_POST['_vfbp_form_id'] );

		$vfbdb  = new VFB_Pro_Data();
		$fields = $vfbdb->get_fields( $form_id );

		$data = array();

		foreach ( $fields as $field ) {
			$field_id   = $field['id'];

			$val = '';
			if ( isset ( $_POST['vfb-field-' . $field_id ] ) ) {
				$val = stripslashes_deep( $_POST['vfb-field-' . $field_id ] );
			}

			$data[ $field_id ] = $val;
		}

		$order = 0;
		foreach ( $data as $field => $val ) {

			$field_data = array();
			if ( is_array( $val ) ) {
				foreach( $val as $k => $v ){
					$field_data[ $k ] = $v;
				}
			}

			$wpdb->update(
				VFB_FIELDS_TABLE_NAME,
				array(
					'data'        => serialize( $field_data ),
					'field_order' => $order,
				),
				array(
					'id' => $field,
				)
			);

			$order++;
		}

		$wpdb->update(
			VFB_FORMS_TABLE_NAME,
			array(
				'date_updated' => date( 'Y-m-d H:i:s', strtotime ( 'now' ) )
			),
			array(
				'id' => $form_id
			)
		);
	}

	/**
	 * Save form settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_form_settings() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-form-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_form_settings' );

		$data = array();

		foreach ( $_POST as $key => $val ) {
			if ( substr( $key, 0, 1 ) != '_' )
				$data[ $key ] = $val;
		}

		$data = stripslashes_deep( $data );

		$date_updated = date( 'Y-m-d H:i:s', strtotime ( 'now' ) );
		$form_id      = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		$title  = $data['title'];
		$status = $data['status'];

		unset( $data['title'], $data['status'] );

		$save_data = array(
			'title'        => $title,
			'data'         => serialize( $data ),
			'status'       => $status,
			'date_updated' => $date_updated,
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
	 * Save email settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_email_settings() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-email-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_email_settings' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data    = stripslashes_deep( $data );
		$form_id = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		foreach ( $data as $meta_key => $meta_value ) {
			$this->update_metadata( $form_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Save confirmation settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_confirmation_settings() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-confirmation-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_confirmation_settings' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data    = stripslashes_deep( $data );
		$form_id = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		foreach ( $data as $meta_key => $meta_value ) {
			$this->update_metadata( $form_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Save email design settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_design_settings() {
		global $wpdb;

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-design-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_design_settings' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data    = stripslashes_deep( $data );
		$form_id = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		foreach ( $data as $meta_key => $meta_value ) {
			$this->update_metadata( $form_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Save Rules settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_rules_settings() {
		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-rules-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_rules_settings' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data    = stripslashes_deep( $data );
		$form_id = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		foreach ( $data as $meta_key => $meta_value ) {
			$this->update_metadata( $form_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Save Add-on settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_addon_settings() {
		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-addon-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_addon_settings' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data    = stripslashes_deep( $data );
		$form_id = isset( $_POST['_vfbp_form_id'] ) ? absint( $_POST['_vfbp_form_id'] ) : 0;

		foreach ( $data as $meta_key => $meta_value ) {
			$this->update_metadata( $form_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Save main VFB Pro settings
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function save_vfb_settings() {
		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'save-settings' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_settings' );

		if ( isset( $_POST['vfbp-uninstall'] ) ) {
			$license_key   = $_POST['settings']['license-key'];
			$license_email = $_POST['settings']['license-email'];

			$this->uninstall_plugin( $license_key, $license_email );

			return;
		}

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data     = stripslashes_deep( $data );
		$settings = get_option( 'vfbp_settings' );

		// Make sure preview-id isn't overwritten
		if ( isset( $settings['preview-id'] ) )
			$data['preview-id'] = $settings['preview-id'];

		// Make sure email-preview-id isn't overwritten
		if ( isset( $settings['email-preview-id'] ) )
			$data['email-preview-id'] = $settings['email-preview-id'];

		update_option( 'vfbp_settings', $data );
	}

	/**
	 * Trash a form
	 *
	 * To delete a form, see the delete_form() method
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function trash_form() {
		global $wpdb;

		if ( !isset( $_GET['vfb-action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'trash-form' !== $_GET['vfb-action'] )
			return;

		check_admin_referer( 'vfbp_trash_form' );

		$form_id = absint( $_GET['form'] );

		$wpdb->update(
			VFB_FORMS_TABLE_NAME,
			array(
				'status'       => 'trash',
				'date_updated' => date( 'Y-m-d H:i:s', strtotime ( 'now' ) )
			),
			array(
				'id' => $form_id
			)
		);
	}

	/**
	 * Trash a form
	 *
	 * To delete a form, see the delete_form() method
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function duplicate_form() {
		global $wpdb;

		if ( !isset( $_GET['vfb-action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'duplicate-form' !== $_GET['vfb-action'] )
			return;

		check_admin_referer( 'vfbp_duplicate_form' );

		$form_id = absint( $_GET['form'] );

		$vfbdb  = new VFB_Pro_Data();
		$form   = $vfbdb->get_form_settings( $form_id );
		$fields = $vfbdb->get_fields( $form_id );
		$meta   = $vfbdb->get_email_settings( $form_id );

		unset( $form['id'] );

		$form['data'] = serialize( $form['data'] );

		$wpdb->insert(
			VFB_FORMS_TABLE_NAME,
			$form,
			'%s' // formats of all $form data
		 );

		 $form_id    = $wpdb->insert_id;
		 $form['id'] = $form_id;

		 // Duplicate fields
		 if ( is_array( $fields ) ) {
			 for ( $x = 0; $x < count( $fields ); $x++ ) {
				$fields[ $x ]['form_id'] = $form_id;
				$fields[ $x ]['data']    = serialize( $fields[ $x ]['data'] );
				$old_field_id            = $fields[ $x ]['id'];
				$fields[ $x ]['id']      = null;

				$wpdb->insert(
					VFB_FIELDS_TABLE_NAME,
					$fields[ $x ]
				);

				$fields[ $x ]['id']     = $wpdb->insert_id;
				$fields[ $x ]['old_id'] = $old_field_id;
				$fields[ $x ]['data']   = unserialize( $fields[ $x ]['data'] );
			 }
		 }

		 // Duplicate meta
		 if ( is_array( $meta ) ) {
			foreach ( $meta as $meta_key => $meta_value ) {
				// Update Merge Tags with new field IDs
				foreach ( $fields as $field ) {
					$meta_value = str_replace( '{entry:Field' . $field['old_id'] . '}', '{entry:Field' . $field['id'] . '}', $meta_value );

					// If Field or Email rules, update with new field IDs
					if ( in_array( $meta_key, array( 'rules', 'rules-email' ) ) ) {
						$rules      = json_encode( $meta_value );
						$meta_value = str_replace( '"field-id":"' . $field['old_id'] . '"', '"field-id":"' . $field['id'] . '"', $rules );
						$meta_value = json_decode( $meta_value, true );
					}
				}

				$this->add_metadata( $form_id, $meta_key, $meta_value );
			}
		}
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
	private function update_metadata( $id, $meta_key, $meta_value ) {
		$data = new VFB_Pro_Data();
		$data->update_metadata( $id, $meta_key, $meta_value );
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
	private function add_metadata( $id, $meta_key, $meta_value ) {
		$data = new VFB_Pro_Data();
		$data->add_metadata( $id, $meta_key, $meta_value );
	}

	/**
	 * Uninstall plugin.
	 *
	 * Run uninstall on Settings page instead of Plugins page so we can deactivate license
	 * and keep VFB Pro files on the server.
	 *
	 * @access private
	 * @param mixed $license_key
	 * @param mixed $license_email
	 * @return void
	 */
	private function uninstall_plugin( $license_key, $license_email ) {
		$uninstall = new VFB_Pro_Uninstall();
		$uninstall->uninstall( $license_key, $license_email );
	}
}