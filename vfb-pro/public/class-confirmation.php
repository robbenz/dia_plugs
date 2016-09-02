<?php
/**
 * Handles form confirmation actions
 *
 * @since      3.0
 */
class VFB_Pro_Confirmation {

	/**
	 * form
	 *
	 * @var mixed
	 * @access public
	 */
	public $form_id;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $form_id ) {
		$this->form_id = $form_id;
	}

	/**
	 * Prepend the text message to the form
	 *
	 * Instead of replacing the form with the message,
	 * this will prepend the message to a fresh form
	 *
	 * @access public
	 * @return void
	 */
	public function prepend_text() {
		$data = $this->get_settings();

		$type     = isset( $data['confirmation-type'] ) ? $data['confirmation-type'] : 'text';
		$prepend  = isset( $data['text-prepend'] ) ? $data['text-prepend'] : false;

		if ( 'text' !== $type )
			return;

		return $prepend;
	}

	/**
	 * Text message confirmation
	 *
	 * @access public
	 * @param mixed $message
	 * @return void
	 */
	public function text() {
		$data = $this->get_settings();

		$type     = isset( $data['confirmation-type'] ) ? $data['confirmation-type'] : 'text';
		$message  = isset( $data['text-message'] ) ? $data['text-message'] : '';

		if ( 'text' !== $type )
			return;

		return $message;
	}

	/**
	 * WordPress Page redirect
	 *
	 * @access public
	 * @param mixed $page
	 * @return void
	 */
	public function wp_page() {
		$data = $this->get_settings();

		$type     = isset( $data['confirmation-type'] ) ? $data['confirmation-type'] : 'text';
		$page     = isset( $data['wp-page'] ) ? $data['wp-page'] : '';

		if ( 'wp-page' !== $type )
			return;

		$permalink = get_permalink( $page );
		wp_redirect( esc_url_raw( $permalink ) );

		exit();
	}

	/**
	 * Custom URL redirect
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function redirect() {
		$data = $this->get_settings();

		$type     = isset( $data['confirmation-type'] ) ? $data['confirmation-type'] : 'text';
		$redirect = isset( $data['redirect'] ) ? $data['redirect'] : '';

		if ( 'redirect' !== $type )
			return;

		wp_redirect( esc_url_raw( $redirect ) );

		exit();
	}

	/**
	 * Get confirmaton settings
	 *
	 * @access public
	 * @return void
	 */
	public function get_settings() {
		$form_id = $this->get_form_id();
		if ( !$form_id )
			return;

		$vfbdb  = new VFB_Pro_Data();
		$form   = $vfbdb->get_confirmation_settings( $form_id );

		return $form;
	}

	/**
	 * Get just created Entry ID.
	 *
	 * @access public
	 * @return void
	 */
	public function get_entry_id() {
		$form_id = $this->get_form_id();
		if ( !$form_id )
			return;

		$vfbdb    = new VFB_Pro_Data();
		$settings = $vfbdb->get_form_settings( $form_id );

		if ( !isset( $settings['data']['last-entry'] ) )
			return 0;

		return $settings['data']['last-entry'];
	}

	/**
	 * Get form ID
	 *
	 * @access private
	 * @return void
	 */
	public function get_form_id() {
		if ( !isset( $this->form_id ) )
			return false;

		return (int) $this->form_id;
	}

	/**
	 * Basic check to exit if the form hasn't been submitted
	 *
	 * @access public
	 * @return void
	 */
	public function submit_check() {
		// If class form ID hasn't been set, exit
		if ( !$this->get_form_id() )
			return;

		// If form ID hasn't been submitted by $_POST, exit
		if ( !isset( $_POST['_vfb-form-id'] ) )
			return;

		// If class form ID doesn't match $_POST form ID, exit
		if ( $this->get_form_id() !== absint( $_POST['_vfb-form-id'] ) )
			return;

		return true;
	}
}