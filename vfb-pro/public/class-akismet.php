<?php
/**
 * Mark entries as spam
 *
 * @since      3.0
 */
class VFB_Pro_Akismet {

	/**
	 * author
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $author;

	/**
	 * email
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $email;

	/**
	 * url
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $url;

	/**
	 * content
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $content;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * spam_check function.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function spam_check( $post_id ) {
		if ( !method_exists( 'Akismet', 'http_post' ) || !function_exists( 'akismet_http_post' ) )
			return false;

		global $akismet_api_host, $akismet_api_port;

		$query_string = '';
		$result       = false;

		// Submitted data
		$data = $this->get_vars();

		// Save Akismet data in postmeta for reference in Entries view
		add_post_meta( $post_id, '_vfb_akismet-data', $data );

		foreach ( array_keys( $data ) as $k ) {
			$query_string .= $k . '=' . urlencode( $data[ $k ] ) . '&';
		}

		if ( method_exists( 'Akismet', 'http_post' ) )
		    $response = Akismet::http_post( $query_string, 'comment-check' );
		else
		    $response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );

		// Only update post if a response is true
		if ( $response ) {
			if ( 'true' == trim( $response[1] ) ) {
				wp_update_post(
					array(
						'ID'		  => $post_id,
						'post_status' => 'spam',
					)
				);
			}
		}
	}

	/**
	 * get_vars function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_vars() {
		$akismet_data = array();

		$akismet_data['comment_author']       = $this->author;
		$akismet_data['comment_author_email'] = $this->email;
		$akismet_data['comment_author_url']   = $this->url;
		$akismet_data['comment_content']      = $this->content;

		// Insert additional Akismet data
		$akismet_data['comment_type'] = 'contact-form';
		$akismet_data['user_ip']      = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$akismet_data['user_agent']   = $_SERVER['HTTP_USER_AGENT'];
		$akismet_data['referrer']     = $_SERVER['HTTP_REFERER'];
		$akismet_data['blog']         = get_option( 'home' );

		return $akismet_data;
	}

	/**
	 * set_vars function.
	 *
	 * @access public
	 * @param mixed $field_id
	 * @param mixed $value
	 * @return void
	 */
	public function set_vars( $field_id, $value ) {
		$field = $this->get_field_settings( $field_id );
		$type  = $field['field_type'];

		switch( $type ) {
			case 'text' :
				if ( !isset( $this->author ) )
					$this->author = $value;
				break;

			case 'email' :
				if ( !isset( $this->email ) )
					$this->email = $value;
				break;

			case 'url' :
				if ( !isset( $this->url ) )
					$this->url = $value;
				break;

			case 'textarea' :
				if ( !isset( $this->content ) )
					$this->content = $value;
				break;
		}
	}

	/**
	 * Get all field settings
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_field_settings( $id ) {
		$vfbdb = new VFB_Pro_Data();
		$field = $vfbdb->get_field_by_id( $id );

		return $field;
	}
}