<?php
/**
 * WP Mock bootstrap file. Loads test framework and defines a few functions so
 * we can load the plugin without loading WordPress.
 *
 * @package Media_Deduper_Pro
 */

// Load Composer dependencies, including WP_Mock.
require_once( __DIR__ . '/../../vendor/autoload.php' );

// Set up WP_Mock.
WP_Mock::bootstrap();

/**
 * Ignore the register_activation_hook() function.
 */
function register_activation_hook() {}

/**
 * Ignore the register_uninstall_hook() function.
 */
function register_uninstall_hook() {}

// The following functions are ripped straight from WP core, so if they don't
// fit the coding standards, that's not our problem.
// @codingStandardsIgnoreStart

/**
 * Convert a value to non-negative integer.
 *
 * @since 2.5.0
 *
 * @param mixed $maybeint Data you wish to have converted to a non-negative integer.
 * @return int A non-negative integer.
 */
function absint( $maybeint ) {
	return abs( intval( $maybeint ) );
}

/**
 * i18n friendly version of basename()
 *
 * @since 3.1.0
 *
 * @param string $path   A path.
 * @param string $suffix If the filename ends in suffix this will also be cut off.
 * @return string
 */
function wp_basename( $path, $suffix = '' ) {
	return urldecode( basename( str_replace( array( '%2F', '%5C' ), '/', urlencode( $path ) ), $suffix ) );
}

/**
 * A wrapper for PHP's parse_url() function that handles consistency in the return
 * values across PHP versions.
 *
 * PHP 5.4.7 expanded parse_url()'s ability to handle non-absolute url's, including
 * schemeless and relative url's with :// in the path. This function works around
 * those limitations providing a standard output on PHP 5.2~5.4+.
 *
 * Secondly, across various PHP versions, schemeless URLs starting containing a ":"
 * in the query are being handled inconsistently. This function works around those
 * differences as well.
 *
 * Error suppression is used as prior to PHP 5.3.3, an E_WARNING would be generated
 * when URL parsing failed.
 *
 * @since 4.4.0
 * @since 4.7.0 The $component parameter was added for parity with PHP's parse_url().
 *
 * @param string $url       The URL to parse.
 * @param int    $component The specific component to retrieve. Use one of the PHP
 *                          predefined constants to specify which one.
 *                          Defaults to -1 (= return all parts as an array).
 *                          @see http://php.net/manual/en/function.parse-url.php
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a sting or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function wp_parse_url( $url, $component = -1 ) {
	$to_unset = array();
	$url = strval( $url );

	if ( '//' === substr( $url, 0, 2 ) ) {
		$to_unset[] = 'scheme';
		$url = 'placeholder:' . $url;
	} elseif ( '/' === substr( $url, 0, 1 ) ) {
		$to_unset[] = 'scheme';
		$to_unset[] = 'host';
		$url = 'placeholder://placeholder' . $url;
	}

	$parts = @parse_url( $url );

	if ( false === $parts ) {
		// Parsing failure.
		return $parts;
	}

	// Remove the placeholder values.
	foreach ( $to_unset as $key ) {
		unset( $parts[ $key ] );
	}

	return _get_component_from_parsed_url_array( $parts, $component );
}

/**
 * Retrieve a specific component from a parsed URL array.
 *
 * @internal
 *
 * @since 4.7.0
 *
 * @param array|false $url_parts The parsed URL. Can be false if the URL failed to parse.
 * @param int    $component The specific component to retrieve. Use one of the PHP
 *                          predefined constants to specify which one.
 *                          Defaults to -1 (= return all parts as an array).
 *                          @see http://php.net/manual/en/function.parse-url.php
 * @return mixed False on parse failure; Array of URL components on success;
 *               When a specific component has been requested: null if the component
 *               doesn't exist in the given URL; a sting or - in the case of
 *               PHP_URL_PORT - integer when it does. See parse_url()'s return values.
 */
function _get_component_from_parsed_url_array( $url_parts, $component = -1 ) {
	if ( -1 === $component ) {
		return $url_parts;
	}

	$key = _wp_translate_php_url_constant_to_key( $component );
	if ( false !== $key && is_array( $url_parts ) && isset( $url_parts[ $key ] ) ) {
		return $url_parts[ $key ];
	} else {
		return null;
	}
}

/**
 * Translate a PHP_URL_* constant to the named array keys PHP uses.
 *
 * @internal
 *
 * @since 4.7.0
 *
 * @see   http://php.net/manual/en/url.constants.php
 *
 * @param int $constant PHP_URL_* constant.
 * @return string|bool The named key or false.
 */
function _wp_translate_php_url_constant_to_key( $constant ) {
	$translation = array(
		PHP_URL_SCHEME   => 'scheme',
		PHP_URL_HOST     => 'host',
		PHP_URL_PORT     => 'port',
		PHP_URL_USER     => 'user',
		PHP_URL_PASS     => 'pass',
		PHP_URL_PATH     => 'path',
		PHP_URL_QUERY    => 'query',
		PHP_URL_FRAGMENT => 'fragment',
	);

	if ( isset( $translation[ $constant ] ) ) {
		return $translation[ $constant ];
	} else {
		return false;
	}
}

// @codingStandardsIgnoreEnd

// Load the plugin files.
require_once( __DIR__ . '/../../media-deduper-pro.php' );
require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-license-manager.php' );
require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-reference-handler.php' );
