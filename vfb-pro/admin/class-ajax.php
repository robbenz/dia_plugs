<?php
/**
 * Class that handles all AJAX calls
 *
 * @since 3.0
 */
class VFB_Pro_Admin_AJAX {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_ajax_vfbp-create-field', array( $this, 'create_field' ) );
		add_action( 'wp_ajax_vfbp-sort-field', array( $this, 'sort_field' ) );
		add_action( 'wp_ajax_vfbp-delete-field', array( $this, 'delete_field' ) );
		add_action( 'wp_ajax_vfbp-duplicate-field', array( $this, 'duplicate_field' ) );

		// Form Designer
		add_action( 'wp_ajax_vfbp-form-desinger-copy-settings', array( $this, 'copy_design_settings' ) );
		add_action( 'wp_ajax_vfbp-form-designer-copy-settings-save', array( $this, 'copy_design_settings_save' ) );

		// Notifications - MailChimp
		add_action( 'wp_ajax_vfbp-connect-mailchimp', array( $this, 'connect_mailchimp' ) );
		add_action( 'wp_ajax_vfbp-disconnect-mailchimp', array( $this, 'disconnect_mailchimp' ) );

		// Notifications - Campaign Monitor
		add_action( 'wp_ajax_vfbp-connect-campaign-monitor', array( $this, 'connect_campaign_monitor' ) );
		add_action( 'wp_ajax_vfbp-disconnect-campaign-monitor', array( $this, 'disconnect_campaign_monitor' ) );
		add_action( 'wp_ajax_vfbp-campaign-monitor-select-client', array( $this, 'campaign_monitor_select_client' ) );

		// Notifications - Highrise
		add_action( 'wp_ajax_vfbp-connect-highrise', array( $this, 'connect_highrise' ) );
		add_action( 'wp_ajax_vfbp-disconnect-highrise', array( $this, 'disconnect_highrise' ) );

		// Notifications - Freshbooks
		add_action( 'wp_ajax_vfbp-connect-freshbooks', array( $this, 'connect_freshbooks' ) );
		add_action( 'wp_ajax_vfbp-disconnect-freshbooks', array( $this, 'disconnect_freshbooks' ) );

		// Payments - Price Fields
		add_action( 'wp_ajax_vfbp-price-fields', array( $this, 'price_fields' ) );
		add_action( 'wp_ajax_vfbp-price-fields-options', array( $this, 'price_fields_options' ) );
	}

	/**
	 * Create Field by click or drag and drop
	 *
	 * @access public
	 * @return void
	 */
	public function create_field() {
		global $wpdb;

		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_POST['action'] ) && 'vfbp-create-field' !== $_POST['action'] )
			return;

		$data = array();
		foreach ( $_POST['data'] as $k ) {
			$data[ $k['name'] ] = $k['value'];
		}

		$form_id     = absint( $data['_vfbp_form_id'] );
		$field_type  = strtolower( sanitize_title( $_POST['field_type'] ) );
		$field_order = isset( $_POST['order'] ) ? $_POST['order'] : '';
		$field_name  = esc_html( $_POST['field_type'] );
		$settings    = array( 'label' => $field_name );

		$save_data = array(
			'form_id'     => $form_id,
			'field_type'  => $field_type,
			'field_order' => 999,
			'data'		  => serialize( $settings )
		);

		$new_field = $wpdb->insert(
			VFB_FIELDS_TABLE_NAME,
			$save_data,
			array(
				'%d',	// form_id
				'%s',	// field_type
				'%d',	// field_order
				'%s',	// data
			)
		);

		$field_id = $wpdb->insert_id;

		// Update the field order of all fields
		if ( is_array( $field_order ) ) {
			foreach ( $field_order as $position => $item ) {

				if ( empty( $item ) )
					$f = $field_id;
				else
					$f = preg_replace( '/[^0-9]/', '', $item );

				$wpdb->update(
					VFB_FIELDS_TABLE_NAME,
					array( 'field_order' => $position ),
					array( 'id' => $f )
				);
			}
		}

		$field = new VFB_Pro_Admin_Fields();
		echo $field->field_output( $field_id );

		die(1);
	}

	/**
	 * Sort fields
	 *
	 * @access public
	 * @return void
	 */
	public function sort_field() {
		global $wpdb;

		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_POST['action'] ) && 'vfbp-sort-field' !== $_POST['action'] )
			return;

		$order = isset( $_POST['order'] ) ? parse_str( $_POST['order'], $field_order ) : '';

		// Update the field order of all fields
		if ( isset( $field_order['vfb-field-item'] ) && is_array( $field_order['vfb-field-item'] ) ) {
			foreach ( $field_order['vfb-field-item'] as $position => $item ) {
				$wpdb->update(
					VFB_FIELDS_TABLE_NAME,
					array( 'field_order' => $position ),
					array( 'id' => $item )
				);

			}
		}

		die(1);
	}

	/**
	 * Delete fields
	 *
	 * @access public
	 * @return void
	 */
	public function delete_field() {
		global $wpdb;

		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_POST['action'] ) && 'vfbp-delete-field' !== $_POST['action'] )
			return;

		$field = isset( $_POST['field'] ) ? absint( $_POST['field'] ) : '';

		if ( $field ) {
			// Delete field
			$wpdb->delete(
				VFB_FIELDS_TABLE_NAME,
				array( 'id' => $field ),
				array( '%d' )
			);

			// Delete matching meta from entries
			$wpdb->delete(
				$wpdb->prefix . 'postmeta',
				array( 'meta_key' => "_vfb_field-{$field}" ),
				array( '%s' )
			);
		}

		die(1);
	}

	/**
	 * Duplicate fields
	 *
	 * @access public
	 * @return void
	 */
	public function duplicate_field() {
		global $wpdb;

		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_POST['action'] ) && 'vfbp-duplicate-field' !== $_POST['action'] )
			return;

		$field       = isset( $_POST['field'] ) ? absint( $_POST['field'] ) : '';
		$field_order = isset( $_POST['order'] ) ? $_POST['order'] : '';

		if ( !$field )
			return;

		$vfbdb = new VFB_Pro_Data();
		$existing_field = $vfbdb->get_field_by_id( $field );
		$order = $existing_field['field_order'] + 1;

		$save_data = array(
			'form_id'     => $existing_field['form_id'],
			'field_type'  => $existing_field['field_type'],
			'field_order' => $order,
			'data'		  => serialize( $existing_field['data'] )
		);

		$new_field = $wpdb->insert(
			VFB_FIELDS_TABLE_NAME,
			$save_data,
			array(
				'%d',	// form_id
				'%s',	// field_type
				'%d',	// field_order
				'%s',	// data
			)
		);

		$field_id = $wpdb->insert_id;

		// Update the field order of all fields
		if ( is_array( $field_order ) ) {
			foreach ( $field_order as $position => $item ) {

				$f = preg_replace( '/[^0-9]/', '', $item );

				if ( $position > $existing_field['field_order'] ) {
					$position++;

					$wpdb->update(
						VFB_FIELDS_TABLE_NAME,
						array( 'field_order' => $position ),
						array( 'id' => $f )
					);
				}
			}
		}

		$field = new VFB_Pro_Admin_Fields();
		echo $field->field_output( $field_id );

		die(1);
	}

	/**
	 * A wrapper for the Form Designer add-on Copy Settings thickbox display
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function copy_design_settings() {
		$designer = new VFB_Pro_Addon_Form_Designer_Admin_Settings();
		$designer->copy_settings();
	}

	/**
	 * A wrapper for the Form Designer add-on Copy Settings save button
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function copy_design_settings_save() {
		$designer = new VFB_Pro_Addon_Form_Designer_Admin_Settings();
		$designer->copy_settings_save();
	}

	/**
	 * A wrapper for the Notifications add-on MailChimp API connect request.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function connect_mailchimp() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->connect_mailchimp();
	}

	/**
	 * A wrapper for the Notifications add-on MailChimp API disconnect.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect_mailchimp() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->disconnect_mailchimp();
	}

	/**
	 * A wrapper for the Notifications add-on Campaign Monitor API connect request.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function connect_campaign_monitor() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->connect_campaign_monitor();
	}

	/**
	 * A wrapper for the Notifications add-on Campaign Monitor API disconnect.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect_campaign_monitor() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->disconnect_campaign_monitor();
	}

	/**
	 * A wrapper for the Notifications add-on Campaign Monitor API disconnect.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function campaign_monitor_select_client() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->campaign_monitor_select_client();
	}

	/**
	 * A wrapper for the Notifications add-on Highrise API connect request.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function connect_highrise() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->connect_highrise();
	}

	/**
	 * A wrapper for the Notifications add-on Highrise API disconnect.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect_highrise() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->disconnect_highrise();
	}

	/**
	 * A wrapper for the Notifications add-on Freshbooks API connect request.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function connect_freshbooks() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->connect_freshbooks();
	}

	/**
	 * A wrapper for the Notifications add-on Freshbooks API disconnect.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect_freshbooks() {
		$notifications = new VFB_Pro_Addon_Notifications_Admin_Settings();
		$notifications->disconnect_freshbooks();
	}

	/**
	 * A wrapper for the Payments add-on price fields dropdown.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function price_fields() {
		$payments = new VFB_Pro_Addon_Payments_Admin_Settings();
		$payments->price_fields();
	}

	/**
	 * A wrapper for the Payments add-on getting price fields options.
	 *
	 * Called here because the AJAX request needs to happen earlier than the
	 * included file in the add-on that displays the settings
	 *
	 * @access public
	 * @return void
	 */
	public function price_fields_options() {
		$payments = new VFB_Pro_Addon_Payments_Admin_Settings();
		$payments->price_fields_options();
	}
}