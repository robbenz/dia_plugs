<?php
/**
 * Setup the Form Preview page
 *
 * @since      3.0
 */
class VFB_Pro_Preview_Form {

	/**
	 * Setup hooks when loaded
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'preview' ), 9999 );
	}

	/**
	 * preview function.
	 *
	 * @access public
	 * @return void
	 */
	public function preview( $content ) {
		if ( !isset( $_GET['vfb-form-id'] ) )
			return $content;

		if ( !isset( $_GET['preview'] ) )
			return $content;

		// Get Form ID
		$form_id = absint( $_GET['vfb-form-id'] );

		return VFB_Pro_Form_Display::display( array( 'id' => $form_id ) );
	}
}