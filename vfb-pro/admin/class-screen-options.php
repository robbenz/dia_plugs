<?php
/**
 * Handle Screen Options
 *
 * Defines and saves all options in Screen Options tabs
 *
 * @since      3.0
 */
class VFB_Pro_Admin_Screen_Options {

	/**
	 * Add options to Screen Options
	 *
	 * @access public
	 * @return void
	 */
	public function add_option() {
		// Forms per page
		add_screen_option(
			'per_page',
			array(
			    'label'   => __( 'Forms per page', 'vfb-pro' ),
			    'default' => 10,
			    'option'  => 'vfbp_forms_per_page'
			)
		);
	}

	/**
	 * Save Screen Options
	 *
	 * @access public
	 * @param mixed $status		Return this so we don't break other plugins
	 * @param mixed $option		The option name
	 * @param mixed $value		The submitted value
	 * @return void
	 */
	public function save_option( $status, $option, $value ) {

		if ( 'vfbp_forms_per_page' == $option )
			return $value;

		return $status;
	}
}