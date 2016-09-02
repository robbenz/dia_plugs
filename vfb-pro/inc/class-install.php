<?php
/**
 * Define the install process
 *
 * Installs the DB
 *
 * @since      3.0
 */
class VFB_Pro_Install {
	/**
	 * Initial setup
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Check database version and run SQL install, if needed
	 *
	 * @access	public
	 * @since	3.0
	 */
	public function upgrade_db_check() {

		$current_db_version = VFB_DB_VERSION;

		if ( get_site_option( 'vfbp_db_version' ) != $current_db_version )
			$this->install_db();
	}

	/**
	 * Install everything VFB Pro will need such as settings and database tables
	 *
	 * @access	static
	 * @since   3.0
	 */
	public function install_db() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Forms table
		$sql = "CREATE TABLE " . VFB_FORMS_TABLE_NAME . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			title text NOT NULL,
			data longtext NOT NULL,
			status varchar(20) DEFAULT 'publish',
			date_updated timestamp NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta( $sql );

		// Fields table
		$sql = "CREATE TABLE " . VFB_FIELDS_TABLE_NAME . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			field_type varchar(255) NOT NULL,
			field_order bigint(20) NOT NULL,
			data longtext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta( $sql );

		// Form Meta table
	 	$sql = "CREATE TABLE ". VFB_FORM_META_TABLE_NAME . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta( $sql );

		update_option( 'vfbp_db_version', VFB_DB_VERSION );
	}

	/**
	 * Add or update the custom VFB capabilities
	 *
	 * @access	static
	 * @since   3.0
	 */
	public function add_caps() {
		$role = get_role( 'administrator' );

		// If the capabilities have not been added, do so here
		if ( !empty( $role ) ) {
			// Setup the capabilities for each role that gets access
			$caps = array(
				'administrator' => array(
					'vfb_read',
					'vfb_create_forms',
					'vfb_edit_forms',
					'vfb_copy_forms',
					'vfb_delete_forms',
					'vfb_import_forms',
					'vfb_export_forms',
					'vfb_view_entries',
					'vfb_edit_entries',
					'vfb_delete_entries',
					'vfb_edit_email_design',
					'vfb_view_analytics',
					'vfb_edit_settings',
					'vfb_uninstall_plugin',
				),
				'editor' => array(
					'vfb_read',
					'vfb_view_entries',
					'vfb_edit_entries',
					'vfb_delete_entries',
					'vfb_view_analytics',
				)
			);

			// Assign the appropriate caps to the administrator role
			if ( !empty( $role ) ) {
				foreach ( $caps['administrator'] as $cap ) {
					$role->add_cap( $cap );
				}
			}

			// Assign the appropriate caps to the editor role
			$role = get_role( 'editor' );
			if ( !empty( $role ) ) {
				foreach ( $caps['editor'] as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}
	}

	/**
	 * Add our Preview Page in draft
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function add_preview_page() {
		$title        = 'VFB Pro - Form Preview';
		$preview_page = get_page_by_title( $title );

		if ( !$preview_page ) {
			$preview_post = array(
				'post_title'   => $title,
				'post_content' => 'This is a preview of how this form will appear on your website',
				'post_status'  => 'draft',
				'post_type'    => 'page',
			);

			// Insert the page
			$page_id = wp_insert_post( $preview_post );
		}
		else {
			$page_id = $preview_page->ID;
		}

		$data = get_option( 'vfbp_settings' );
		$data['preview-id'] = $page_id;

		update_option( 'vfbp_settings', $data );
	}

	/**
	 * Add our Preview Page in draft
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function add_email_preview_page() {
		$title        = 'VFB Pro - Email Preview';
		$preview_page = get_page_by_title( $title );

		if ( !$preview_page ) {
			$preview_post = array(
				'post_title'   => $title,
				'post_content' => 'This is a preview of how the email will look.',
				'post_status'  => 'draft',
				'post_type'    => 'page',
			);

			// Insert the page
			$page_id = wp_insert_post( $preview_post );
		}
		else {
			$page_id = $preview_page->ID;
		}

		$data = get_option( 'vfbp_settings' );
		$data['email-preview-id'] = $page_id;

		update_option( 'vfbp_settings', $data );
	}

	/**
	 * A wrapper to check DB version which then calls install_db
	 *
	 * @since    3.0
	 */
	public function install() {
		$this->upgrade_db_check();
		$this->add_caps();
		$this->add_preview_page();
		$this->add_email_preview_page();
	}
}