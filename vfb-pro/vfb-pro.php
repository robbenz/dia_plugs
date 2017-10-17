<?php
/*
Plugin Name:	VFB Pro
Plugin URI:		http://vfbpro.com
Description:	VFB Pro is the easiest form builder on the market.
Version:		3.2.5
Author:			Matthew Muro
Author URI:		http://matthewmuro.com
Text Domain:	vfb-pro
Domain Path:	/lang/
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	exit;

class VFB_Pro {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = 'vfb-pro';

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version = '3.2.5';

	/**
	 * The current DB version. Used if we need to update the DB later.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      string    $db_version    The current DB version
	 */
	protected $db_version = '1.4';

	/**
	 * The main instanace of VFB_Pro
	 *
	 * @since	3.0
	 * @var 	mixed
	 * @access 	private
	 * @static
	 */
	private static $instance = null;

	/**
     * Protected constructor to prevent creating a new instance of VFB_Pro
     * via the 'new' operator from outside of this class.
     *
     * @return void
     */
	protected function __construct() {
	}

	/**
     * Private clone method to prevent cloning of the instance.
     *
     * @return void
     */
    private function __clone() {
    }

    /**
     * Private unserialize method to prevent unserializing of the instance.
     *
     * @return void
     */
    private function __wakeup() {
    }

	/**
	 * Create a single VFB Pro instance
	 *
	 * Insures that only one instance of VFB Pro is running.
	 * Otherwise known as the Singleton class pattern
	 *
	 * @since    3.0
	 * @access   public
	 * @static
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new VFB_Pro;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->autoload_classes();

			// Setup Entries CPT
			self::$instance->entries_cpt = new VFB_Pro_Entries_CPT();

			// Install DB
			register_activation_hook( __FILE__, array( self::$instance, 'install' ) );

			// Update DB
			add_action( 'plugins_loaded', array( self::$instance, 'upgrade_db_check' ) );

			// Load i18n
			add_action( 'plugins_loaded', array( self::$instance, 'lang' ) );

			// Start Session
			add_action( 'init', array( self::$instance, 'start_session' ), 1 );

			$screen_options = new VFB_Pro_Admin_Screen_Options();
			add_filter( 'set-screen-option', array( $screen_options, 'save_option' ), 10, 3 );
		}

		return self::$instance;
	}

	/**
	 * Setup constants
	 *
	 * @since 3.0
	 * @access private
	 * @return void
	 */
	private function setup_constants() {
		global $wpdb;

		// Database version
		if ( !defined( 'VFB_DB_VERSION' ) )
			define( 'VFB_DB_VERSION', $this->db_version );

		// Plugin version
		if ( !defined( 'VFB_PLUGIN_VERSION' ) )
			define( 'VFB_PLUGIN_VERSION', $this->version );

		// Plugin Folder Path
		if ( !defined( 'VFB_PLUGIN_DIR' ) )
			define( 'VFB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL
		if ( !defined( 'VFB_PLUGIN_URL' ) )
			define( 'VFB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Root File
		if ( !defined( 'VFB_PLUGIN_FILE' ) )
			define( 'VFB_PLUGIN_FILE', __FILE__ );

		// Form table name
		if ( !defined( 'VFB_FORMS_TABLE_NAME' ) )
			define( 'VFB_FORMS_TABLE_NAME', $wpdb->prefix . 'vfbp_forms' );

		// Field table name
		if ( !defined( 'VFB_FIELDS_TABLE_NAME' ) )
			define( 'VFB_FIELDS_TABLE_NAME', $wpdb->prefix . 'vfbp_fields' );

		// Form meta table name
		if ( !defined( 'VFB_FORM_META_TABLE_NAME' ) )
			define( 'VFB_FORM_META_TABLE_NAME', $wpdb->prefix . 'vfbp_formmeta' );
	}

	/**
	 * Include files
	 *
	 * @since 3.0
	 * @access private
	 * @return void
	 */
	private function includes() {
		require_once( VFB_PLUGIN_DIR . 'inc/class-entries-cpt.php' );				// VFB_Pro_Entries_CPT class
		require_once( VFB_PLUGIN_DIR . 'inc/class-install.php' );					// VFB_Pro_Install class
		require_once( VFB_PLUGIN_DIR . 'inc/class-uninstall.php' );					// VFB_Pro_Uninstall class
		require_once( VFB_PLUGIN_DIR . 'inc/class-i18n.php' );						// VFB_Pro_i18n class
		require_once( VFB_PLUGIN_DIR . 'inc/class-vfb-list-table.php' );			// VFB_List_Table class (a copy of the WP_List_Table class)
		require_once( VFB_PLUGIN_DIR . 'inc/class-preview-form.php' );				// VFB_Pro_Preview_Form class
		require_once( VFB_PLUGIN_DIR . 'inc/class-preview-email.php' );				// VFB_Pro_Preview_email class
		require_once( VFB_PLUGIN_DIR . 'inc/class-db-data.php' );					// VFB_Pro_Data class
		require_once( VFB_PLUGIN_DIR . 'inc/class-plugin-updater.php' );			// VFB_Pro_Plugin_Updater class
		require_once( VFB_PLUGIN_DIR . 'inc/class-migration.php' );					// VFB_Pro_Migration class
		require_once( VFB_PLUGIN_DIR . 'admin/class-admin-menu.php' );				// VFB_Pro_Admin_Menu class
		require_once( VFB_PLUGIN_DIR . 'admin/class-screen-options.php' );			// VFB_Pro_Admin_Screen_Options class
		require_once( VFB_PLUGIN_DIR . 'admin/class-media-button.php' );			// VFB_Pro_Media_Button class
		require_once( VFB_PLUGIN_DIR . 'admin/class-dashboard-widgets.php' );		// VFB_Pro_Dashboard_Widgets class
		require_once( VFB_PLUGIN_DIR . 'admin/class-widget.php' );					// VFB_Pro_Widget class
		require_once( VFB_PLUGIN_DIR . 'admin/class-admin-toolbar.php' );			// VFB_Pro_Admin_Toolbar class
		require_once( VFB_PLUGIN_DIR . 'admin/class-admin-notices.php' );			// VFB_Pro_Admin_Notices class
		require_once( VFB_PLUGIN_DIR . 'admin/class-load-css-js.php' );				// VFB_Pro_Admin_Loader class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-list.php' );				// VFB_Pro_Forms_List class
		require_once( VFB_PLUGIN_DIR . 'admin/class-entries.php' );					// VFB_Pro_Entries class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-new.php' );				// VFB_Pro_Forms_New class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-edit.php' );				// VFB_Pro_Forms_Edit class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-edit-fields.php' );		// VFB_Pro_Forms_Edit_Fields class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-edit-settings.php' );		// VFB_Pro_Forms_Edit_Settings class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-edit-email.php' );		// VFB_Pro_Forms_Edit_Email class
		require_once( VFB_PLUGIN_DIR . 'admin/class-forms-edit-confirmation.php' );	// VFB_Pro_Forms_Edit_Confirmation class
		require_once( VFB_PLUGIN_DIR . 'admin/class-email-design.php' );			// VFB_Pro_Edit_Email_Design class
		require_once( VFB_PLUGIN_DIR . 'admin/class-rules.php' );					// VFB_Pro_Edit_Rules class
		require_once( VFB_PLUGIN_DIR . 'admin/class-addons.php' );					// VFB_Pro_Edit_Addons class
		require_once( VFB_PLUGIN_DIR . 'admin/class-fields.php' );					// VFB_Pro_Admin_Fields class
		require_once( VFB_PLUGIN_DIR . 'admin/class-fields-settings.php' );			// VFB_Pro_Admin_Fields_Settings class
		require_once( VFB_PLUGIN_DIR . 'admin/class-page-settings.php' );			// VFB_Pro_Page_Settings class
		require_once( VFB_PLUGIN_DIR . 'admin/class-page-addons.php' );				// VFB_Pro_Page_AddOns class
		require_once( VFB_PLUGIN_DIR . 'admin/class-diagnostics.php' );				// VFB_Pro_Admin_Diagnostics class
		require_once( VFB_PLUGIN_DIR . 'admin/class-ajax.php' );					// VFB_Pro_Admin_AJAX class
		require_once( VFB_PLUGIN_DIR . 'admin/class-save.php' );					// VFB_Pro_Admin_Save class
		require_once( VFB_PLUGIN_DIR . 'admin/class-import.php' );					// VFB_Pro_Import class
		require_once( VFB_PLUGIN_DIR . 'admin/class-export.php' );					// VFB_Pro_Export class
		require_once( VFB_PLUGIN_DIR . 'admin/class-template-tags.php' );			// VFB_Pro_Template_tags class
		require_once( VFB_PLUGIN_DIR . 'public/class-form-display.php' );			// VFB_Pro_Form_Display class
		require_once( VFB_PLUGIN_DIR . 'inc/class-form-builder.php' );				// VFB_Pro_Form_Builder class
		require_once( VFB_PLUGIN_DIR . 'inc/class-csrf.php' );						// VFB_Pro_NoCSRF class
		require_once( VFB_PLUGIN_DIR . 'inc/class-premailer.php' );					// VFB_Pro_Premailer class
		require_once( VFB_PLUGIN_DIR . 'public/class-load-css-js.php' );			// VFB_Pro_Scripts_Loader class
		require_once( VFB_PLUGIN_DIR . 'public/class-confirmation.php' );			// VFB_Pro_Confirmation class
		require_once( VFB_PLUGIN_DIR . 'public/class-email.php' );					// VFB_Pro_Email class
		require_once( VFB_PLUGIN_DIR . 'public/class-save-entry.php' );				// VFB_Pro_Save_Entry class
		require_once( VFB_PLUGIN_DIR . 'public/class-format-field.php' );			// VFB_Pro_Format class
		require_once( VFB_PLUGIN_DIR . 'public/class-akismet.php' );				// VFB_Pro_Akismet class
		require_once( VFB_PLUGIN_DIR . 'public/class-security.php' );				// VFB_Pro_Security class
		require_once( VFB_PLUGIN_DIR . 'public/class-templating.php' );				// VFB_Pro_Templating class
		require_once( VFB_PLUGIN_DIR . 'public/class-upload.php' );					// VFB_Pro_Upload class
	}

	/**
	 * Install DB
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function install() {
		$install = new VFB_Pro_Install();
		$install->install();
	}

	/**
	 * Check database version and run SQL install, if needed
	 *
	 * @access	public
	 * @since	3.1
	 */
	public function upgrade_db_check() {

		$current_db_version = VFB_DB_VERSION;

		if ( get_site_option( 'vfbp_db_version' ) != $current_db_version ) {
			$install = new VFB_Pro_Install();
			$install->install_db();
			$install->add_caps();
		}
	}

	/**
	 * Load localization file
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function lang() {
		$i18n = new VFB_Pro_i18n();
		$i18n->set_domain( $this->plugin_name );

		$i18n->load_lang();
	}

	/**
	 * Autoload some VFB_Pro classes that aren't loaded via other files
	 *
	 * @since 3.0
	 * @access public
	 * @return void
	 */
	public function autoload_classes() {
		$admin_menu           = new VFB_Pro_Admin_Menu();
		$admin_ajax           = new VFB_Pro_Admin_AJAX();
		$admin_save           = new VFB_Pro_Admin_Save();
		$admin_notices        = new VFB_Pro_Admin_Notices();
		$admin_toolbar_menu   = new VFB_Pro_Admin_Toolbar();
		$dashboard_widgets    = new VFB_Pro_Dashboard_Widgets();
		$entries_list         = new VFB_Pro_Entries();
		$export               = new VFB_Pro_Export();
		$media_button         = new VFB_Pro_Media_Button();
		$migration            = new VFB_Pro_Migration();
		$preview_email        = new VFB_Pro_Preview_Email();
		$preview_form         = new VFB_Pro_Preview_Form();
		$plugin_updater       = new VFB_Pro_Plugin_Updater();
		$diagnostics          = new VFB_Pro_Admin_Diagnostics();

		VFB_Pro_Form_Display::instance();
	}

	/**
	 * Start $_SESSION on init
	 *
	 * @since 3.2.2
	 * @access public
	 * @return void
	 */
	public function start_session() {
		if ( $this->is_session_started() === false ) {
			session_start();
		}
	}

	/**
     * Utility function to test if PHP Sessions are supported.
     *
     * @since 3.2.2
     * @access protected
     * @return void
     */
    private static function is_session_started() {
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
	            return session_status() === PHP_SESSION_ACTIVE ? true : false;
	        }
	        else {
	            return session_id() === '' ? false : true;
	        }
	    }

	    return false;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Outputs the form when called directly.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @return void
	 */
	public function form( $form_id ) {
		if ( !$form_id )
			return;

		echo VFB_Pro_Form_Display::display( array( 'id' => $form_id ) );
	}

	/**
	 * Returns associative array of a single entry's metadata.
	 *
	 * @access public
	 * @param mixed $entry_id
	 * @param mixed $form_id
	 * @return void
	 */
	public function entry( $entry_id, $form_id ) {
		if ( !$form_id || !$entry_id )
			return;

		$vfbdb = new VFB_Pro_Data();
		$entry = $vfbdb->get_entry_by_seq_num( $entry_id, $form_id );

		return $entry;
	}
}

/**
 * The main function responsible for returning VFB Pro forms and functionality.
 *
 * Example: <?php $vfb = vfbp(); $vfb->form( 1 ); ?>
 *
 * @since 3.0
 * @return object VFB_Pro instance
 */
function vfbp() {
	return VFB_Pro::instance();
}

vfbp();