<?php
/**
 * Class that displays widgets on the WordPress dashboard
 *
 * @since 3.0
 */
class VFB_Pro_Admin_Toolbar {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'add_edit_menu' ), 998 );
		add_action( 'admin_bar_menu', array( $this, 'add_menu' ), 999 );
		add_action( 'wp_head', array( $this, 'icon' ) );
		add_action( 'admin_head', array( $this, 'icon' ) );
	}

	/**
	 * Output Form Icon for VFB Pro admin toolbar items.
	 *
	 * @access public
	 * @return void
	 */
	public function icon() {
		$css = '<style type="text/css">';
		$css .= '#wpadminbar #wp-admin-bar-vfbp-toolbar-edit-form > .ab-item:before {content: "\f175";top: 2px;}';
		$css .= '#wpadminbar #wp-admin-bar-vfbp-admin-toolbar > .ab-item:before {content: "\f175";top: 2px;}';
		$css .= '</style>';

		echo $css;
	}

	/**
	 * Edit Form.
	 *
	 * @access public
	 * @param mixed $wp_admin_bar
	 * @return void
	 */
	public function add_edit_menu( $wp_admin_bar ) {
		global $post;

		if ( !current_user_can( 'vfb_edit_forms' ) )
			return;

		if ( !$post )
			return;

		if( has_shortcode( $post->post_content, 'vfb' ) ) {
			preg_match_all( '/id=[\'"]?(\d+)[\'"]?/', $post->post_content, $matches );

			$edit_url = add_query_arg(
				array(
					'page'       => 'vfb-pro',
					'vfb-action' => 'edit',
					'form'       => $matches[1][0],
				),
				admin_url( 'admin.php' )
			);

			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-toolbar-edit-form',
				'title'		=> __( 'Edit Form', 'vfb-pro' ),
				'parent'	=> false,
				'href'		=> esc_url( $edit_url ),
				)
			);
		}
	}

	/**
	 * VFB Pro dropdown.
	 *
	 * @access public
	 * @return void
	 */
	public function add_menu( $wp_admin_bar ) {
		// License check
		if ( !$this->license_check() ) {
			return;
		}

		// Entire menu will be hidden if user does not have vfb_edit_forms cap
		if ( current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar',
				'title'		=> 'VFB Pro',
				'parent'	=> false,
				'href'		=> admin_url( 'admin.php?page=vfb-pro' ),
				)
			);
		}

		// All Forms
		if ( current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-all',
				'title'		=> 'All Forms',
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfb-pro' ),
				)
			);
		}

		// Add New Form
		if ( current_user_can( 'vfb_create_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-add',
				'title'		=> __( 'Add New Form', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfbp-add-new' )
				)
			);
		}

		// Entries
		if ( current_user_can( 'vfb_view_entries' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-entries',
				'title'		=> __( 'Entries', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'edit.php?post_type=vfb_entry' )
				)
			);
		}

		// Import
		if ( current_user_can( 'vfb_import_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-import',
				'title'		=> __( 'Import', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfbp-import' )
				)
			);
		}

		// Export
		if ( current_user_can( 'vfb_export_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-export',
				'title'		=> __( 'Export', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfbp-export' )
				)
			);
		}

		// Settings
		if ( current_user_can( 'vfb_edit_settings' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-settings',
				'title'		=> __( 'Settings', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfbp-settings' )
				)
			);
		}

		// Add-ons
		if ( current_user_can( 'vfb_edit_forms' ) ) {
			$wp_admin_bar->add_node( array(
				'id' 		=> 'vfbp-admin-toolbar-addons',
				'title'		=> __( 'Add-ons', 'vfb-pro' ),
				'parent'	=> 'vfbp-admin-toolbar',
				'href'		=> admin_url( 'admin.php?page=vfbp-addons' )
				)
			);
		}
	}

	/**
	 * license_check function.
	 *
	 * @access private
	 * @return void
	 */
	private function license_check() {
		$license = get_option( 'vfbp_license_status' );

		if ( !$license || 0 == $license )
			return false;

		return true;
	}
}