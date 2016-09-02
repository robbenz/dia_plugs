<?php
/**
 * Add all admin menus
 *
 * Defines and adds all admin menus
 *
 * @since      3.0
 */
class VFB_Pro_Admin_Menu {

	/**
	 * Initial setup
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Add main menu
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_menu', array( $this, 'remove_menu' ), 999 );
	}

	/**
	 * Adds the main menu
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function add_menu() {
		global $pagenow, $typenow;

		$page = add_menu_page(
			'VFB Pro',
			__( 'VFB Pro', 'vfb-pro' ),
			'vfb_read',
			'vfb-pro',
			array( $this, 'admin' ),
			'dashicons-feedback'
		);

		$all_forms = add_submenu_page(
			'vfb-pro',
			__( 'VFB Pro', 'vfb-pro' ),
			__( 'All Forms', 'vfb-pro' ),
			'vfb_edit_forms',
			'vfb-pro',
			array( $this, 'admin' )
		);

		$add_new = add_submenu_page(
			'vfb-pro',
			__( 'Add New', 'vfb-pro' ),
			__( 'Add New', 'vfb-pro' ),
			'vfb_create_forms',
			'vfbp-add-new',
			array( $this, 'add_new_form' )
		);

		$entries = add_submenu_page(
			'vfb-pro',
			__( 'Entries', 'vfb-pro' ),
			__( 'Entries', 'vfb-pro' ),
			'vfb_view_entries',
			'edit.php?post_type=vfb_entry'
		);

		$import = add_submenu_page(
			'vfb-pro',
			__( 'Import', 'vfb-pro' ),
			__( 'Import', 'vfb-pro' ),
			'vfb_import_forms',
			'vfbp-import',
			array( $this, 'import' )
		);

		$export = add_submenu_page(
			'vfb-pro',
			__( 'Export', 'vfb-pro' ),
			__( 'Export', 'vfb-pro' ),
			'vfb_export_forms',
			'vfbp-export',
			array( $this, 'export' )
		);

		$settings = add_submenu_page(
			'vfb-pro',
			__( 'Settings', 'vfb-pro' ),
			__( 'Settings', 'vfb-pro' ),
			'vfb_edit_settings',
			'vfbp-settings',
			array( $this, 'settings' )
		);

		$addons = add_submenu_page(
			'vfb-pro',
			__( 'Add-ons', 'vfb-pro' ),
			'<span class="vfb-menu-item-addons">' . __( 'Add-ons', 'vfb-pro' ) . '</span>',
			'vfb_edit_forms',
			'vfbp-addons',
			array( $this, 'add_ons' )
		);

		$scripts = new VFB_Pro_Admin_Scripts_Loader();
		add_action( 'load-' . $page, array( $scripts, 'add_css' ) );
		add_action( 'load-' . $page, array( $scripts, 'add_js' ) );

		add_action( 'load-' . $add_new, array( $scripts, 'add_css' ) );
		add_action( 'load-' . $add_new, array( $scripts, 'add_js' ) );

		add_action( 'load-' . $export, array( $scripts, 'add_css' ) );
		add_action( 'load-' . $export, array( $scripts, 'add_js' ) );

		add_action( 'load-' . $settings, array( $scripts, 'add_css' ) );
		add_action( 'load-' . $settings, array( $scripts, 'add_js' ) );

		add_action( 'load-' . $addons, array( $scripts, 'add_css' ) );
		add_action( 'load-' . $addons, array( $scripts, 'add_js' ) );

		if ( ( $pagenow == 'edit.php' ) || $typenow == 'vfb_entry' ) {
			add_action( 'admin_print_styles', array( $scripts, 'add_css' ) );
			add_action( 'admin_print_styles', array( $scripts, 'add_js' ) );
		}

		// Enable Screen Options tabs here (saving is hooked in main plugin instance() )
		$screen_options = new VFB_Pro_Admin_Screen_Options();
		add_action( 'load-' . $page, array( $screen_options, 'add_option' ) );
	}

	/**
	 * Remove menus if a verified license isn't found
	 *
	 * @access public
	 * @return void
	 */
	public function remove_menu() {
		$license = get_option( 'vfbp_license_status' );

		if ( !$license || 0 == $license ) {
			remove_submenu_page( 'vfb-pro', 'vfbp-add-new' );
			remove_submenu_page( 'vfb-pro', 'edit.php?post_type=vfb_entry' );
			remove_submenu_page( 'vfb-pro', 'vfbp-import' );
			remove_submenu_page( 'vfb-pro', 'vfbp-export' );
			remove_submenu_page( 'vfb-pro', 'vfbp-addons' );
		}
	}

	/**
	 * Load either the All Forms list or Edit Form view
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function admin() {
		if ( isset( $_GET['form'] ) && 'edit' == $_GET['vfb-action'] )
			$this->edit_form();
		else
			$this->forms_list();
	}

	/**
	 * View for All Forms list
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function forms_list() {
		$forms = new VFB_Pro_Forms_List();
	?>
	<div class="wrap">
		<?php
			// License check
			if ( !$this->license_check() ) {
				$settings_text = sprintf( __( 'Your VFB Pro license is UNVERIFIED. You must <a href="%s">enter your license</a> information before VFB Pro can fully function.', 'vfb-pro' ), esc_url( admin_url( 'admin.php?page=vfbp-settings' ) ) );

				return printf( '<div id="message" class="error"><p>%s</p></div>', $settings_text );
			}
		?>
		<h2>
		<?php
			_e( 'VFB Pro', 'vfb-pro' );

			// Add New link
			echo sprintf(
				' <a href="%1$s" class="add-new-h2">%2$s</a>',
				esc_url( admin_url( 'admin.php?page=vfbp-add-new' ) ),
				esc_html( __( 'Add New', 'vfb-pro' ) )
			);

			// If searched, output the query
			if ( isset( $_POST['s'] ) && !empty( $_POST['s'] ) )
				echo '<span class="subtitle">' . sprintf( __( 'Search results for "%s"' , 'vfb-pro'), esc_html( $_POST['s'] ) );
		?>
		</h2>

		<form id="forms-filter" method="post" action="">
		<?php
			$forms->views();
			$forms->prepare_items();

			$forms->search_box( 'search', 'search_id' );
			$forms->display();
		?>
		</form>
	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Display the Add New form
	 *
	 * Uses the VFB_Pro_Forms_New class
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function add_new_form() {
		$add_new = new VFB_Pro_Forms_New();
		$add_new->display();
	}

	/**
	 * View for Edit Form
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function edit_form() {
		$edit = new VFB_Pro_Forms_Edit();
		$edit->display();
	}

	/**
	 * View for the Import page
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function import() {
		$import = new VFB_Pro_Import();
		$import->display();
	}

	/**
	 * View for the Export page
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function export() {
		$export = new VFB_Pro_Export();
		$export->display();
	}

	/**
	 * View for the Settings page
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function settings() {
		$settings = new VFB_Pro_Page_Settings();
		$settings->display();
	}

	/**
	 * View for the Add-ons page
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function add_ons() {
		$addons = new VFB_Pro_Page_AddOns();
		$addons->display();
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