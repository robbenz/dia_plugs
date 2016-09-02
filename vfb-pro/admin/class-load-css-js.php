<?php
/**
 * Loads all CSS and JS files that VFB Pro needs
 *
 * This class should be called when the menu is added
 * so the CSS and JS is added to ONLY our VFB Pro pages.
 *
 * @since      3.0
 */
class VFB_Pro_Admin_Scripts_Loader {

	/**
	 * Load CSS on VFB admin pages.
	 *
	 * Called from the VFB_Pro_Admin_Menu class
	 *
	 * @access public
	 * @return void
	 */
	public function add_css() {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'vfb-admin', VFB_PLUGIN_URL . "admin/assets/css/vfb-admin.min.css", array(), '2015.07.22' );
	}

	/**
	 * Load JS on VFB admin pages
	 *
	 * Called from the VFB_Pro_Admin_Menu class
	 *
	 * @access public
	 * @return void
	 */
	public function add_js() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_media();

		wp_enqueue_script( 'vfb-admin', VFB_PLUGIN_URL . "admin/assets/js/vfb-admin.min.js", array( 'jquery' ), '2015.05.29', true );
		wp_enqueue_script( 'jquery-datetime-picker', VFB_PLUGIN_URL . "admin/assets/js/datetimepicker.min.js", array( 'jquery' ), '2.4.0', true );
		wp_enqueue_script( 'jquery-token-field', VFB_PLUGIN_URL . "admin/assets/js/token-field.min.js", array( 'jquery' ), '1.3.3', true );
		wp_enqueue_script( 'jquery-confirm', VFB_PLUGIN_URL . "admin/assets/js/confirm.min.js", array(), '2.3.1', true );
		wp_enqueue_script( 'jquery-accordion', VFB_PLUGIN_URL . "admin/assets/js/accordion.min.js", array(), '2015.02.07', true );
		wp_enqueue_script( 'codemirror', VFB_PLUGIN_URL . "admin/assets/js/codemirror.min.js", array(), '4.11.1', true );

		wp_localize_script( 'vfb-admin', 'vfbp_settings', array( 'vfbp_ajax_nonce' => wp_create_nonce( 'vfbp_ajax') ) );
		wp_localize_script( 'vfb-admin', 'vfbpL10n', array(
			'confirmTitle'    => __( 'Are you sure you want to <span>delete</span> this field?', 'vfb-pro' ),
			'confirmText'     => __( 'Deleting a field means that <strong>all data collected by the field will be deleted immediately</strong>. Because this action <strong>cannot</strong> be undone, you might want to consider exporting your data first.', 'vfb-pro' ),
			'confirmButton'   => __( 'Yes. Delete this field.', 'vfb-pro' ),
			'deleteCondition' => __( 'You must have at least one condition.', 'vfb-pro' ),
			'deleteRule'      => __( 'You must have at least one rule.', 'vfb-pro' ),
			'chooseImage'	  => __( 'Choose Image', 'vfb-pro' )
		));
	}
}