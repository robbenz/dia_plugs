<?php
/**
 * Premailer API PHP class
 * Premailer is a library/service for making HTML more palatable for various inept email clients, in particular GMail
 * Primary function is to convert style tags into equivalent inline styles so styling can survive <head> tag removal
 * Premailer is owned by Dialect Communications group
 */
class VFB_Pro_Premailer {
	/**
	 * endpoint
	 *
	 * @var string
	 * @access protected
	 * @static
	 */
	protected static $endpoint = 'http://premailer.dialect.ca/api/0.1/documents';

	/**
	 * Central static method for submitting either an HTML string or a URL, optionally retrieving converted versions
	 * @static
	 * @throws Exception
	 * @param string $html Raw HTML source
	 * @param string $url URL of the source file
	 * @param bool $fetchresult Whether to also fetch the converted output
	 * @param string $adaptor Which document handler to use (hpricot (default) or nokigiri)
	 * @param string $base_url Base URL for converting relative links
	 * @param int $line_length Length of lines in the plain text version (default 65)
	 * @param string $link_query_string Query string appended to links
	 * @param bool $preserve_styles Whether to preserve any link rel=stylesheet and style elements
	 * @param bool $remove_ids Remove IDs from the HTML document?
	 * @param bool $remove_classes Remove classes from the HTML document?
	 * @param bool $remove_comments Remove comments from the HTML document?
	 * @return array Either a single strclass object containing the decoded JSON response, or a 3-element array containing result, html and plain parts if $fetchresult is set
	 */
	protected static function convert($html = '', $url = '', $fetchresult = true, $adaptor = 'hpricot', $base_url = '', $line_length = 65, $link_query_string = '', $preserve_styles = true, $remove_ids = false, $remove_classes = false, $remove_comments = false) {
		$params = array();

		if ( !empty( $html ) ) {
			$params['html'] = $html;
		}
		elseif ( !empty( $url ) ) {
			$params['url'] = $url;
		}
		else {
			return __( 'Must supply an html or url value', 'vfb-pro' );
		}

		if ( $adaptor == 'hpricot' or $adaptor == 'nokigiri' ) {
			$params['adaptor'] = $adaptor;
		}

		if ( !empty( $base_url ) ) {
			$params['base_url'] = $base_url;
		}

		$params['line_length'] = (int) $line_length;

		if ( !empty( $link_query_string ) ) {
			$params['link_query_string'] = $link_query_string;
		}

		$params['preserve_styles']    = ( $preserve_styles ? 'true' : 'false' );
		$params['remove_ids']         = ( $remove_ids      ? 'true' : 'false' );
		$params['remove_classes']     = ( $remove_classes  ? 'true' : 'false' );
		$params['remove_comments']    = ( $remove_comments ? 'true' : 'false' );

		$options = array(
			'timeout'        => 15,
			'connecttimeout' => 15,
			'useragent'      => 'PHP Premailer',
			'ssl'            => array( 'verifypeer' => false, 'verifyhost' => false ),
		);

		$request = wp_remote_post(
			self::$endpoint,
			array(
				'timeout'        => 15,
				'connecttimeout' => 15,
				'useragent'      => 'PHP Premailer',
				'ssl'            => array( 'verifypeer' => false, 'verifyhost' => false ),
				'body'           => $params
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = wp_remote_retrieve_body( $request );

			if ( $request ) {
				$response = json_decode( $request );

				$code = $response->status;
				if ( $code !== 201 ) {
					switch ( $code ) {
						case 400 :
							return __( 'Content missing', 'vfb-pro' );
							break;

						case 403 :
							return __( 'Access forbidden', 'vfb-pro' );
							break;

						case 500 :
						default :
							return __( 'Error ' . $code, 'vfb-pro' );
							break;
					}
				}

				$return = array( 'result' => $response );

				// Get HTML content from URL
				$html = wp_remote_get(
					$response->documents->html,
					array(
						'timeout' => 15,
					)
				);

				// Get TXT content from URL
				$txt = wp_remote_get(
					$response->documents->txt,
					array(
						'timeout' => 15,
					)
				);

				// Set HTML to return array
				if ( ! is_wp_error( $html ) ) {
					$html = wp_remote_retrieve_body( $html );

					if ( $html ) {
						$return['html'] = $html;
					}
				}

				// Set TXT to return array
				if ( ! is_wp_error( $txt ) ) {
					$txt = wp_remote_retrieve_body( $txt );

					if ( $txt ) {
						$return['txt'] = $txt;
					}
				}

				return $return;
			}
		}

		return false;
	}

	/**
	 * Central static method for submitting either an HTML string or a URL, optionally retrieving converted versions
	 * @static
	 * @throws Exception
	 * @param string $html Raw HTML source
	 * @param bool $fetchresult Whether to also fetch the converted output
	 * @param string $adaptor Which document handler to use (hpricot (default) or nokigiri)
	 * @param string $base_url Base URL for converting relative links
	 * @param int $line_length Length of lines in the plain text version (default 65)
	 * @param string $link_query_string Query string appended to links
	 * @param bool $preserve_styles Whether to preserve any link rel=stylesheet and style elements
	 * @param bool $remove_ids Remove IDs from the HTML document?
	 * @param bool $remove_classes Remove classes from the HTML document?
	 * @param bool $remove_comments Remove comments from the HTML document?
	 * @return array Either a single element array containing the 'result' object, or three elements containing result, html and plain if $fetchresult is set
	 */
	public static function html($html, $fetchresult = true, $adaptor = 'hpricot', $base_url = '', $line_length = 65, $link_query_string = '', $preserve_styles = true, $remove_ids = false, $remove_classes = false, $remove_comments = false) {
		return self::convert($html, '', $fetchresult, $adaptor, $base_url, $line_length, $link_query_string, $preserve_styles, $remove_ids, $remove_classes, $remove_comments);
	}

	/**
	 * Central static method for submitting either an HTML string or a URL, optionally retrieving converted versions
	 * @static
	 * @throws Exception
	 * @param string $url URL of the source file
	 * @param bool $fetchresult Whether to also fetch the converted output
	 * @param string $adaptor Which document handler to use (hpricot (default) or nokigiri)
	 * @param string $base_url Base URL for converting relative links
	 * @param int $line_length Length of lines in the plain text version (default 65)
	 * @param string $link_query_string Query string appended to links
	 * @param bool $preserve_styles Whether to preserve any link rel=stylesheet and style elements
	 * @param bool $remove_ids Remove IDs from the HTML document?
	 * @param bool $remove_classes Remove classes from the HTML document?
	 * @param bool $remove_comments Remove comments from the HTML document?
	 * @return array Either a single element array containing the 'result' object, or three elements containing result, html and plain if $fetchresult is set
	 */
	public static function url($url, $fetchresult = true, $adaptor = 'hpricot', $base_url = '', $line_length = 65, $link_query_string = '', $preserve_styles = true, $remove_ids = false, $remove_classes = false, $remove_comments = false) {
		return self::convert('', $url, $fetchresult, $adaptor, $base_url, $line_length, $link_query_string, $preserve_styles, $remove_ids, $remove_classes, $remove_comments);
	}
}