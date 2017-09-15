<?php
/**
 * Media Deduper Pro: reference handler class.
 *
 * @package Media_Deduper_Pro
 */

// Disallow direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper class for finding/replacing references to attachment posts.
 */
class MDD_Reference_Handler {

	/**
	 * True if track_post_meta() is currently running. Used to prevent recursion.
	 *
	 * @var bool
	 */
	public $is_tracking_meta = false;

	/**
	 * An array of IDs for posts that are currently being deleted. Used to prevent
	 * unnecessary calls to track_deleted_meta().
	 *
	 * @var array
	 */
	public $posts_being_deleted = array();

	/**
	 * An array of attachment URLs to replace. Used by
	 * MDD_Reference_Handler::replace_mutli_url().
	 *
	 * @var array
	 */
	public $urls_to_replace = array();

	/**
	 * Constructor. Add detection/replacement filters and post/meta save hooks.
	 */
	public function __construct() {

		// Add hooks for int fields (single integer values).
		add_filter( 'mdd_detect_type__int', array( $this, 'detect_int' ), 10, 2 );
		add_filter( 'mdd_replace_type__int', array( $this, 'replace_int' ), 10, 3 );

		// Add hooks for multi-int fields (arrays comma-separated strings of ints).
		add_filter( 'mdd_detect_type__multi_int', array( $this, 'detect_multi_int' ), 10, 2 );
		add_filter( 'mdd_replace_type__multi_int', array( $this, 'replace_multi_int' ), 10, 3 );

		// Add hooks for URL fields.
		add_filter( 'mdd_detect_type__url', array( $this, 'detect_urls' ), 10, 2 );
		add_filter( 'mdd_replace_type__url', array( $this, 'replace_url' ), 10, 3 );

		// Add hooks for WYSIWYG fields.
		add_filter( 'mdd_detect_type__wysiwyg', array( $this, 'detect_urls' ), 10, 2 );
		add_filter( 'mdd_detect_type__wysiwyg', array( $this, 'detect_gallery_ids' ), 10, 2 );
		add_filter( 'mdd_detect_type__wysiwyg', array( $this, 'detect_caption_ids' ), 10, 2 );
		add_filter( 'mdd_detect_type__wysiwyg', array( $this, 'detect_img_classes' ), 10, 2 );
		add_filter( 'mdd_replace_type__wysiwyg', array( $this, 'replace_multi_url' ), 10, 3 );
		add_filter( 'mdd_replace_type__wysiwyg', array( $this, 'replace_gallery_ids' ), 10, 3 );
		add_filter( 'mdd_replace_type__wysiwyg', array( $this, 'replace_caption_ids' ), 10, 3 );
		add_filter( 'mdd_replace_type__wysiwyg', array( $this, 'replace_img_classes' ), 10, 3 );

		// When switch_to_blog() or restore_current_blog() is called, clear out
		// site-specific data.
		add_action( 'switch_blog', array( $this, 'switch_blog' ) );

		// When a post meta field is set, updated, or deleted, check for attachment
		// references.
		add_action( 'added_post_meta',    array( $this, 'track_post_meta' ), 10, 3 );
		add_action( 'updated_post_meta',  array( $this, 'track_post_meta' ), 10, 3 );
		add_action( 'deleted_post_meta',  array( $this, 'track_post_meta' ), 10, 3 );

		// When a post is created, updated, or deleted, check for references within
		// post fields (or delete previously indexed reference data).
		add_action( 'wp_insert_post',     array( $this, 'track_post_props' ) );
		add_action( 'before_delete_post', array( $this, 'track_deleted_post' ) );
		add_action( 'after_delete_post',  array( $this, 'done_deleting_post' ) );
	}

	/**
	 * Get fields in which to search for and/or replace attachment references.
	 *
	 * @param int|WP_Post $post A post ID or post object.
	 * @return An array describing post fields. See the documentation for the
	 *         `mdd_get_reference_fields` hook.
	 * @see mdd_get_reference_fields
	 */
	public function get_post_fields( $post ) {

		$post = get_post( $post );

		// If $post wasn't a valid post or post ID, return an empty array.
		if ( ! $post ) {
			return array();
		}

		// Initialize the array of fields.
		$fields = array(
			// Always track post content.
			'prop:post_content' => 'wysiwyg',
			// Excerpts mostly won't contain images (or markup at all), but some themes support it. And
			// WooCommerce even uses a WYSIWYG editor with an Add Media button, so better safe than sorry.
			'prop:post_excerpt' => 'wysiwyg',
		);

		// If this post type supports featured images, add the featured image field.
		if ( post_type_supports( $post->post_type, 'thumbnail' ) ) {
			$fields['meta:_thumbnail_id'] = 'int';
		}

		// If this is a WooCommerce product or product variation, add the gallery
		// image field.
		if ( in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ) {
			$fields['meta:_product_image_gallery'] = 'multi_int';
		}

		// Add Yoast SEO (free version) image fields.
		$fields['meta:_yoast_wpseo_twitter-image'] = 'url';
		$fields['meta:_yoast_wpseo_opengraph-image'] = 'url';

		/**
		 * Filter the list of post fields that may contain attachment references.
		 *
		 * @param array       $fields An array describing post fields. Array keys start with either
		 *                            'meta' or 'prop' (denoting a post meta field or post object
		 *                            property, respectively), followed by a colon character, followed
		 *                            by the field's name (e.g. '_thumbnail_id' or 'post_content').
		 *                            Array values are strings indicating the type of data stored in a
		 *                            field.
		 * @param int|WP_Post $post   The post to return fields for.
		 */
		$fields = apply_filters( 'mdd_get_reference_fields', $fields, $post );

		return $fields;
	}

	/**
	 * Get the list of post types in which to search for and/or replace attachment
	 * references.
	 */
	public function get_post_types() {

		// Get all post type slugs.
		$types = get_post_types( array(
			'public' => true,
		) );

		// Don't track attachments.
		array_diff( $types, array( 'attachment' ) );

		/**
		 * Filter the list of post types that may contain attachment references.
		 *
		 * @param array $types An array of post type slugs. Defaults to all public post types except
		 *                     'attachment'.
		 */
		return apply_filters( 'mdd_get_reference_post_types', $types );
	}

	/**
	 * Determine which attachments, if any, are referenced in a given field.
	 *
	 * @param int|WP_Post $post   A post ID or post object.
	 * @param string      $field  A string describing describing the field to check. See
	 *                            MDD_Reference_Handler::get_post_fields().
	 * @param string      $type   The type of value expected in $field. This
	 *                            determines which detection filters are applied
	 *                            to the value.
	 * @return array An array of referenced attachment IDs.
	 */
	public function detect_in_post_field( $post, $field, $type ) {
		$post = get_post( $post );

		// If $post wasn't a valid post or post ID, return an empty array.
		if ( ! $post ) {
			return array();
		}

		// Initialize an array where we'll store referenced attachment IDs.
		$refs = array();

		// Get the source and name of the field.
		list( $field_source, $field_name ) = explode( ':', $field, 2 );

		// Get field value(s) based on field source.
		if ( 'meta' === $field_source ) {
			// Get all values for the given meta key.
			$values = get_post_meta( $post->ID, $field_name );
			// Return an empty array if no values were found.
			if ( empty( $values ) ) {
				return array();
			}
		} elseif ( 'prop' === $field_source ) {
			// Return an empty array if the named object property doesn't really exist
			// or is empty.
			if ( empty( $post->$field_name ) ) {
				return array();
			}
			// Put property value in an array, so we can use the same detection code
			// for both meta values and object properties.
			$values = array( $post->$field_name );
		}

		// Check value(s) for attachment references.
		foreach ( $values as $value ) {

			// If $value is serialized, unserialize it.
			$value = maybe_unserialize( $value );

			/**
			 * Filter the list of attachment IDs referenced by a field.
			 *
			 * The dynamic portion of the hook name, `$type`, refers to a field data type, i.e. a value in
			 * the array returned by the `mdd_get_reference_fields` filter, such as 'wysiwyg', 'int', or
			 * 'multi_int'.
			 *
			 * @param array $refs  An array of attachment IDs to filter.
			 * @param mixed $value The value of the field.
			 */
			$refs = apply_filters( "mdd_detect_type__$type",   $refs, $value );

			/**
			 * Filter the list of attachment IDs referenced by a field.
			 *
			 * The dynamic portion of the hook name, `$field`, refers to a field descriptor, i.e. a key in
			 * the array returned by the `mdd_get_reference_fields` filter, such as 'prop:post_content' or
			 * 'meta:_thumbnail_id'.
			 *
			 * @param array $refs  An array of attachment IDs to filter.
			 * @param mixed $value The value of the field.
			 */
			$refs = apply_filters( "mdd_detect_field__$field", $refs, $value );
		}

		// Return referenced IDs, without duplicates.
		return array_unique( $refs );
	}

	/**
	 * Detection filter for attachment URLs within a string value.
	 *
	 * @uses MDD_Reference_Handler::get_attachment_id_from_filename()
	 * @uses MDD_Reference_Handler::get_attachment_url_regex()
	 *
	 * @param array  $refs    An array of attachment IDs to add to.
	 * @param string $subject The field value/post content in which to look for
	 *                        attachment references.
	 * @return array The $refs array, with any attachment IDs added.
	 */
	public function detect_urls( $refs, $subject ) {

		// If $subject isn't a string, pass $refs along untouched.
		if ( ! is_string( $subject ) ) {
			return $refs;
		}

		// If we got this far, $subject is a string. Look for attachment URLs.
		preg_match_all( $this->get_attachment_url_regex(), $subject, $matches );
		// URL-decode all filenames.
		$filenames = array_map( 'urldecode', $matches['filename'] );
		// Weed out duplicate filenames.
		$filenames = array_unique( $filenames );

		// Try to get attachment IDs for all filenames, and add them to $refs.
		foreach ( $filenames as $filename ) {
			$attachment_id = $this->get_attachment_id_from_filename( $filename );
			if ( $attachment_id ) {
				$refs[] = $attachment_id;
			}
		}

		// Weed out duplicate IDs.
		$refs = array_unique( $refs );

		return $refs;
	}

	/**
	 * Given an attachment filename, try to find the corresponding attachment, and
	 * return its ID.
	 *
	 * @param string $filename       The attachment filename.
	 * @param bool   $may_be_resized Set to TRUE if $filename might be the
	 *                               filename for a resized image. If this is
	 *                               true, and no attachment is found for
	 *                               $filename, this function will look for a
	 *                               filename suffix like "-123x456", strip it
	 *                               out, and try to find an attachment with
	 *                               _that_ name.
	 * @return bool|int The attachment ID, or false if no attachment was found.
	 */
	public function get_attachment_id_from_filename( $filename, $may_be_resized = true ) {

		// Find the first attachment, if any, whose _wp_attached_file value
		// matches $filename.
		$attachments = get_posts( array(
			'numberposts' => 1,
			'post_type'   => 'attachment',
			'post_status' => 'any',
			'meta_key'    => '_wp_attached_file',
			'meta_value'  => $filename,
		) );
		// If an attachment was found, return its ID.
		if ( ! empty( $attachments ) ) {
			return $attachments[0]->ID;
		} else {
			// If no attachment was found with $filename as its original filename,
			// then check whether $filename looks like a resized image filename. If
			// so, strip out the -__x__ suffix and try again.
			if ( preg_match( '/(-\d+x\d+)\.[^.]*/', $filename, $matches ) ) {
				$original_filename = str_replace( $matches[1], '', $filename );
				// Set $may_be_resized to false this time. If $filename was
				// kitten-640x480-16x16.jpg, and kitten-640x480.jpg can't be found, then
				// we should just give up. Even if it exists, kitten.jpg might be a
				// completely different image.
				return $this->get_attachment_id_from_filename( $original_filename, false );
			}
		}
	}

	/**
	 * Detection filter for multi-integer values.
	 *
	 * @uses MDD_Reference_Handler::detect_int()
	 *
	 * @param array        $refs An array of attachment IDs to add to.
	 * @param array|string $subject An array or comma-separated list of possible
	 *                     attachment IDs.
	 * @return array The $refs array, with any attachment IDs added.
	 */
	public function detect_multi_int( $refs, $subject ) {

		// If $subject isn't an array, see if we can turn it into one.
		if ( ! is_array( $subject ) ) {
			// Split a comma-separated string into an array.
			if ( is_string( $subject ) ) {
				$subject = explode( ',', $subject );
			} else {
				// If $subject isn't a string, give up and return $refs untouched.
				return $refs;
			}
		}

		// Check each array value like it was an individual int value, and add any
		// attachment IDs to $refs.
		foreach ( $subject as $value ) {
			$refs = $this->detect_int( $refs, $value );
		}

		// Weed out duplicate IDs.
		$refs = array_unique( $refs );

		return $refs;
	}

	/**
	 * Detection filter for single integer values.
	 *
	 * @uses MDD_Reference_Handler::check_attachment_id()
	 *
	 * @param array      $refs An array of attachment IDs to add to.
	 * @param int|string $subject The value to check.
	 * @return array The $refs array, with any attachment IDs added.
	 */
	public function detect_int( $refs, $subject ) {

		// If $subject isn't an int or a string, pass $refs along untouched.
		if ( ! is_int( $subject ) && ! is_string( $subject ) ) {
			return $refs;
		}

		// Check whether $subject is an attachment ID.
		if ( $this->check_attachment_id( $subject ) ) {

			// Sanitize the integer value.
			$subject = absint( $subject );

			// If $subject isn't already in $refs, add it.
			if ( ! in_array( $subject, $refs, true ) ) {
				$refs[] = $subject;
			}
		}

		return $refs;
	}

	/**
	 * Detect whether a single integer or integer-like value is a reference to an
	 * attachment.
	 *
	 * @param int|string $maybe_id The value to check.
	 * @return int|null The integer ID of an attachment post, or NULL if $maybe_id
	 *                  is not an attachment ID.
	 */
	public function check_attachment_id( $maybe_id ) {

		// If $maybe_id isn't an integer, then this detection method may not apply.
		if ( ! is_int( $maybe_id ) ) {

			// If it's not a string either, bail.
			if ( ! is_string( $maybe_id ) ) {
				return false;
			}

			// If it isn't a positive integer-like string (no floats or scientific
			// notation allowed!), bail.
			if ( ! preg_match( '/^\d+$/', trim( $maybe_id ) ) ) {
				return false;
			}
		}

		// If $maybe_id is a valid post ID, and the post is an attachment, return
		// true.
		$maybe_attachment = get_post( absint( $maybe_id ) );
		if ( $maybe_attachment && 'attachment' === $maybe_attachment->post_type ) {
			return true;
		}

		// If $maybe_id isn't a post ID, or if it's the ID of a post that isn't an
		// attachment, then return false.
		return false;
	}

	/**
	 * Replacement filter for multi-integer values.
	 *
	 * @uses MDD_Reference_Handler::replace_int()
	 *
	 * @param array|string $subject An array or comma-separated list of possible
	 *                     attachment IDs.
	 * @param int          $old_id  The value to look for.
	 * @param int          $new_id  The value to replace $old_id with.
	 * @return array The $refs array, with any attachment IDs added.
	 */
	public function replace_multi_int( $subject, $old_id, $new_id ) {

		$is_array = is_array( $subject );

		// If $subject isn't an array, see if we can turn it into one.
		if ( ! $is_array ) {
			// Split a comma-separated string into an array.
			if ( is_string( $subject ) ) {
				$subject = explode( ',', $subject );
			} else {
				// If $subject isn't a string, give up and return it untouched.
				return $subject;
			}
		}

		// Iterate over items in $subject, replacing as necessary.
		foreach ( $subject as $index => $value ) {
			$subject[ $index ] = $this->replace_int( $value, $old_id, $new_id );
		}

		// If $subject was originally a string, turn it back into a string.
		if ( ! $is_array ) {
			$subject = implode( ',', $subject );
		}

		return $subject;
	}

	/**
	 * Replacement filter for single integer values.
	 *
	 * @param int|string $subject The value to (maybe) replace.
	 * @param int        $old_id  The value to look for.
	 * @param int        $new_id  The value to replace $old_id with.
	 * @return array The $refs array, with any attachment IDs added.
	 */
	public function replace_int( $subject, $old_id, $new_id ) {

		// If $subject isn't an int or a string, leave it alone.
		if ( ! is_int( $subject ) && ! is_string( $subject ) ) {
			return $subject;
		}

		// If $subject matches the old ID, replace it with the new ID.
		if ( absint( $old_id ) === absint( $subject ) ) {
			// Match the type of $subject: if it's a string, return a string.
			// Otherwise, return an integer.
			if ( is_string( $subject ) ) {
				return (string) $new_id;
			} else {
				return absint( $new_id );
			}
		}

		return $subject;
	}

	/**
	 * Replace any number of URLs, relative or absolute, for the attachment
	 * identified by $old_id with URLs for the attachment identified by $new_id.
	 *
	 * @uses MDD_Reference_Handler::get_replacement_urls()
	 * @uses MDD_Reference_Handler::get_attachment_url_regex()
	 * @uses MDD_Reference_Handler::_replace_multi_url_callback()
	 *
	 * @param string|int $subject The value in which URLs should be replaced.
	 * @param int        $old_id  The ID of the old attachment post.
	 * @param int        $new_id  The ID of the new attachment post. References
	 *                            to the old attachment will be replaced with
	 *                            references to this attachment.
	 */
	public function replace_multi_url( $subject, $old_id, $new_id ) {

		// Set temporary array of replacement URLs, so they don't have to be
		// recalculated again and again.
		$this->urls_to_replace = $this->get_replacement_urls( $old_id, $new_id );

		// Find attachment URLs, and if any URL matches a key in $urls_to_replace,
		// replace it.
		$subject = preg_replace_callback(
			$this->get_attachment_url_regex(),
			array( $this, '_replace_multi_url_callback' ),
			$subject
		);

		return $subject;
	}

	/**
	 * Callback for preg_replace_callback().
	 *
	 * @param array $matches An array of matches/capturing groups, as passsed in
	 *                       by preg_replace_callback().
	 */
	public function _replace_multi_url_callback( $matches ) {

		$url = $matches['url'];

		// If $url matches any of the old URLs, return the entire match (including
		// context that may have been captured by the regex, i.e. opening quotes).
		foreach ( $this->urls_to_replace as $old_url => $new_url ) {
			if ( $url === $old_url ) {
				return str_replace( $old_url, $new_url, $matches[0] );
			}
		}
		return $matches[0];
	}

	/**
	 * Replace a URL, relative or absolute, for the attachment identified by
	 * $old_id with URLs for the attachment identified by $new_id.
	 *
	 * @uses MDD_Reference_Handler::get_replacement_urls()
	 *
	 * @param string|int $subject The value that should be replaced if it's a URL
	 *                            pointing to the attachment identified by
	 *                            $old_id.
	 * @param int        $old_id  The ID of the old attachment post.
	 * @param int        $new_id  The ID of the new attachment post. References to
	 *                            the old attachment will be replaced with
	 *                            references to this attachment.
	 */
	public function replace_url( $subject, $old_id, $new_id ) {

		// If $subject isn't a string, pass it along untouched.
		if ( ! is_string( $subject ) ) {
			return $subject;
		}

		// Get URLs to replace.
		$replacements = $this->get_replacement_urls( $old_id, $new_id );

		// If $subject matches any of the old URLs, return the equivalent new URL.
		foreach ( $replacements as $old_url => $new_url ) {
			if ( $subject === $old_url ) {
				return $new_url;
			}
		}

		return $subject;
	}

	/**
	 * Given an old attachment ID and a new attachment ID, return an array mapping
	 * URLs for the old attachment to URLs for the new attachment.
	 *
	 * @param int $old_id The ID of the attachment whose URLs should be replaced.
	 * @param int $new_id The ID of the attachment whose URLs should replace the
	 *                    other attachment's.
	 * @return array An array where keys are URLs for the old attachment and
	 *               values are equivalently formatted URLs for the new attachment.
	 */
	public function get_replacement_urls( $old_id, $new_id ) {
		// TODO: cache results.
		// Get absolute URLs of the old and new attachments.
		$old_url = wp_get_attachment_url( $old_id );
		$new_url = wp_get_attachment_url( $new_id );

		// If either $old_url or $new_url is empty, something went wrong. Return an
		// empty array (i.e. no replacements).
		if ( empty( $old_url ) || empty( $new_url ) ) {
			return array();
		}

		// Initialize an array of URLs to replace => replacement URLs.
		$replacements = array();
		$replacements[ $old_url ] = $new_url;
		// Add relative versions of the main attachment file URLs.
		$replacements[ $this->get_schemeless_url( $old_url ) ] = $this->get_schemeless_url( $new_url );
		$replacements[ $this->get_relative_url( $old_url ) ] = $this->get_relative_url( $new_url );

		// If both attachments are images, add URLs for alternate sizes.
		if ( wp_attachment_is_image( $old_id ) && wp_attachment_is_image( $new_id ) ) {

			// First, get metadata for both attachments.
			$old_meta = wp_get_attachment_metadata( $old_id );
			$new_meta = wp_get_attachment_metadata( $new_id );

			// Skip this step if metadata wasn't retrieved for either the old or the
			// new attachment. That'd be weird, though.
			if ( ! empty( $old_meta ) && ! empty( $new_meta ) ) {

				// Get the filename of the full-sized old & new images, so we can use them
				// to get image size URLs. See image_downsize() in wp-inclues/media.php.
				$old_basename = wp_basename( $old_url );
				$new_basename = wp_basename( $new_url );

				// Loop through each generated size of the old image, and add size URLs
				// to the replacement array.
				foreach ( $old_meta['sizes'] as $size_name => $old_size ) {

					// If a size that existed for the image is missing for the new image...
					if ( ! isset( $new_meta['sizes'][ $size_name ] ) ) {
						// TODO: output a warning; copy old image size file instead?!
						continue;
					}

					// Add absolute size URLs to the replacement array, by replacing
					// original-size filenames with filenames for resized images.
					$old_size_url = str_replace( $old_basename, $old_size['file'], $old_url );
					$new_size_url = str_replace( $new_basename, $new_meta['sizes'][ $size_name ]['file'], $new_url );
					$replacements[ $old_size_url ] = $new_size_url;
					// Add relative versions of size URLs.
					$replacements[ $this->get_schemeless_url( $old_size_url ) ] = $this->get_schemeless_url( $new_size_url );
					$replacements[ $this->get_relative_url( $old_size_url ) ] = $this->get_relative_url( $new_size_url );
				}
			}
		} // End if().

		return $replacements;
	}

	/**
	 * Return a relative version of an absolute URL.
	 *
	 * @param string $url An absolute URL (e.g. http://test.biz:8888/123.html).
	 * @return string A relative version of the URL (e.g. /123.html).
	 */
	public function get_relative_url( $url ) {
		$bits = $this->parse_url( $url );
		return $bits['path'] . $bits['query'] . $bits['fragment'];
	}

	/**
	 * Return a schemeless/protocol-relative version of an absolute URL.
	 *
	 * @param string $url An absolute URL (e.g. http://test.biz:8888/123.html).
	 * @return string A schemeless version of the URL (e.g.
	 *                //test.biz:8888/123.html).
	 */
	public function get_schemeless_url( $url ) {
		$bits = $this->parse_url( $url );
		return $bits['host'] . $bits['port'] . $bits['path'] . $bits['query'] . $bits['fragment'];
	}

	/**
	 * Return an array of URL components, with prefixes/suffixes as they would
	 * appear in an actual URL string, or empty strings for components that were
	 * missing from the provided URL.
	 *
	 * @param string $url An absolute URL (e.g. http://test.biz:8888/123.html).
	 * @return array An array containing URL components.
	 */
	public function parse_url( $url ) {
		$bits = wp_parse_url( $url );
		return array(
			'scheme'   => ( isset( $bits['scheme'] )   ? $bits['scheme'] . ':'   : '' ),
			'host'     => ( isset( $bits['host'] )     ? '//' . $bits['host']    : '' ),
			'port'     => ( isset( $bits['port'] )     ? ':' . $bits['port']     : '' ),
			'path'     => ( isset( $bits['path'] )     ? $bits['path']           : '' ),
			'query'    => ( isset( $bits['query'] )    ? '?' . $bits['query']    : '' ),
			'fragment' => ( isset( $bits['fragment'] ) ? '#' . $bits['fragment'] : '' ),
		);
	}

	/**
	 * Check whether a given attachment post is referenced anywhere on the site.
	 *
	 * @param int $attachment_id The ID of the attachment to check.
	 * @return bool True if the attachment is referenced in another post, false if
	 * not.
	 */
	public function attachment_is_referenced( $attachment_id ) {
		return (bool) get_post_meta( $attachment_id, '_mdd_referenced_by', true );
	}

	/**
	 * Replace all references to one attachment with references to another
	 * attachment.
	 *
	 * @uses MDD_Reference_Handler::replace_in_field()
	 *
	 * @param int $old_id The ID of the attachment to replace.
	 * @param int $new_id The ID of the attachment that should replace $old_id.
	 */
	public function replace_all_references( $old_id, $new_id ) {

		// Get all references to the old attachment.
		$refs = get_post_meta( $old_id, '_mdd_referenced_by', true );

		// Bail if the _mdd_referenced_by field doesn't exist or isn't an array.
		if ( ! is_array( $refs ) ) {
			return;
		}

		// Iterate over all posts containing references to this attachment.
		foreach ( $refs as $post_id => $post_fields ) {

			// Get data types of all trackable fields for this post.
			$trackable_fields = $this->get_post_fields( $post_id );

			foreach ( $post_fields as $field ) {

				// If this field isn't among the known trackable fields, skip it.
				if ( ! isset( $trackable_fields[ $field ] ) ) {
					continue;
				}

				// If this field is trackable, replace any references in it.
				$this->replace_in_field( $post_id, $field, $trackable_fields[ $field ], $old_id, $new_id );
			}
		}
	}

	/**
	 * Update a field, replacing references to one attachment with references to
	 * another attachment.
	 *
	 * @param int|WP_Post $post   A post ID or post object.
	 * @param string      $field  A string describing the field to check. See
	 *                            MDD_Reference_Handler::get_post_fields().
	 * @param string      $type   The type of value expected in $field. This
	 *                            determines which replacement filters are applied
	 *                            to the value.
	 * @param int         $old_id The ID of the attachment to replace.
	 * @param int         $new_id The ID of the attachment that should replace
	 *                            $old_id.
	 */
	public function replace_in_field( $post, $field, $type, $old_id, $new_id ) {
		$post = get_post( $post );

		// If $post wasn't a valid post or post ID, bail.
		if ( ! $post ) {
			return;
		}

		// Sanitize old and new ID arguments.
		$old_id = absint( $old_id );
		$new_id = absint( $new_id );

		// Get the source and name of the field.
		list( $field_source, $field_name ) = explode( ':', $field, 2 );

		// Update field value(s) based on field source.
		if ( 'meta' === $field_source ) {

			// Get all values for the given meta key.
			$values = get_post_meta( $post->ID, $field_name );
			// Bail if no values were found.
			if ( empty( $values ) ) {
				return;
			}

			foreach ( $values as $old_value ) {

				// If the value was serialized, unserialize it.
				$old_value = maybe_unserialize( $old_value );

				// Initialize the new value.
				$new_value = $old_value;

				/**
				 * Filter the value of a field, replacing references to one attachment with references to
				 * another.
				 *
				 * The dynamic portion of the hook name, `$type`, refers to a field data type, i.e. a value
				 * in the array returned by the `mdd_get_reference_fields` filter, such as 'wysiwyg', 'int',
				 * or 'multi_int'.
				 *
				 * @param mixed $new_value The field value to filter.
				 * @param int   $old_id    The ID of the 'old' attachment, references to which should be
				 *                         replaced.
				 * @param int   $new_id    The ID of the 'new' attachment, which should replace the 'old'
				 *                         attachment wherever it's referenced.
				 */
				$new_value = apply_filters( "mdd_replace_type__$type",   $new_value, $old_id, $new_id );

				/**
				 * Filter the value of a field, replacing references to one attachment with references to
				 * another.
				 *
				 * The dynamic portion of the hook name, `$field`, refers to a field descriptor, i.e. a key
				 * in the array returned by the `mdd_get_reference_fields` filter, such as
				 * 'prop:post_content' or 'meta:_thumbnail_id'.
				 *
				 * @param mixed $new_value The field value to filter.
				 * @param int   $old_id    The ID of the 'old' attachment, references to which should be
				 *                         replaced.
				 * @param int   $new_id    The ID of the 'new' attachment, which should replace the 'old'
				 *                         attachment wherever it's referenced.
				 */
				$new_value = apply_filters( "mdd_replace_field__$field", $new_value, $old_id, $new_id );

				// If the value has changed, update the meta field (and if there's more
				// than one metadata entry for this key, only update those whose value
				// is $old_value).
				update_post_meta( $post->ID, $field_name, $new_value, $old_value );
			} // End foreach().
		} elseif ( 'prop' === $field_source ) {

			// Bail if the named object propery doesn't really exist or is empty.
			if ( empty( $post->$field_name ) ) {
				return;
			}

			// Get the current field value.
			$old_value = $post->$field_name;

			/** This filter is documented in media-deduper-pro/inc/class-mdd-reference-handler.php */
			$post->$field_name = apply_filters( "mdd_replace_type__$type",   $post->$field_name, $old_id, $new_id );
			/** This filter is documented in media-deduper-pro/inc/class-mdd-reference-handler.php */
			$post->$field_name = apply_filters( "mdd_replace_field__$field", $post->$field_name, $old_id, $new_id );

			// If the value has changed, update the post.
			// TODO: only call wp_update_post() once per post, instead of once per field.
			if ( $post->$field_name !== $old_value ) {
				wp_update_post( $post );
			}
		} // End if().
	}

	/**
	 * Return a regex that matches all URLs that look like attachment URLs.
	 *
	 * @return string A regular expression for use with preg_match().
	 */
	public function get_attachment_url_regex() {

		// This function will always return the same value for a given site, unless
		// we're on multisite and switch_to_blog() or restore_current_blog() gets
		// called. So check for a cached value, and return it if found.
		if ( ! empty( $this->attachment_url_regex ) ) {
			return $this->attachment_url_regex;
		}

		// First, get the URL of the upload directory (pass FALSE as the second
		// parameter to avoid unnecessarily creating a new year/month subdir).
		$uploads = wp_upload_dir( null, false );
		$uploads_url = $uploads['baseurl'];
		// Create a partial regex pattern that will match domain-relative or
		// protocol-relative versions of the upload dir URL.
		$home_url = home_url();
		$home_pattern = preg_replace( '!^https?://!', '(?:https?:)?//', $home_url );
		$uploads_pattern = '(?:' . str_replace( $home_url, "(?:$home_pattern)?", $uploads_url ) . ')?';

		// Get all allowed media file extensions.
		$extensions = array_keys( wp_get_mime_types() );
		// Create a partial regex pattern that will match any allowed media extension.
		$extension_pattern = '(?:' . implode( '|', $extensions ) . ')';

		// Create a partial regex pattern matching valid URL characters (see
		// http://stackoverflow.com/q/7109143). Exclude hash and question mark
		// because we're only looking for file paths -- query strings and hashes
		// don't matter. The exclamation point is escaped because that's also going
		// to be our regex delimiter.
		$url_char_pattern = '[A-Za-z0-9-._~:/\[\]@\!$&\'()*+,;=%]';
		// And a negated version.
		$not_url_char_pattern = '[^A-Za-z0-9-._~:/\[\]@\!$&\'()*+,;=%]';

		// Create a regex that will match anything that looks like the URL for an
		// attachment file on this site.
		$this->attachment_url_regex = '!'
			. '(?:^|' // Non-capturing group: all URLs must be found at the start of $subject...
			. $not_url_char_pattern // ...or be preceded by something that isn't a valid URL character.
			. ')' // End non-capturing group.
			. '(["\']?)' // Capturing group #1: opening quote, if any.
			. '(?P<url>' // Begin named capturing group for entire URL.
			. $uploads_pattern // The upload directory URL.
			. '/' // Slash between upload dir and subdir or filename.
			. '(?P<filename>' // Begin named capturing group for filename (including year/month folders, because that's how _wp_attached_file values are stored).
			. '(?:\d{4}/\d{2}/)?' // Year/month folder, if any.
			. '[^/ "\']*' // Possible filename characters (anything other than a slash, space, or quote).
			. '\.' . $extension_pattern // A valid file extension.
			. ')' // End 'filename' capturing group.
			. ')' // End 'url' capturing group.
			. '(?:\1|[?#]|$)' // Either a closing quote, the start of a query string or hash, or the end of $subject.
			. '!'; // End of regex.

		return $this->attachment_url_regex;
	}

	/**
	 * Return a regex that matches (an) image ID(s) in the [gallery] shortcode.
	 *
	 * @param int|null $id An image ID, if the regex will be used to match
	 *                     galleries containing a specific ID, or NULL if the
	 *                     regex should match all gallery shortcodes.
	 * @return string A regular expression for use with preg_match() or
	 *                preg_replace().
	 */
	public function get_gallery_ids_regex( $id = null ) {

		if ( is_null( $id ) ) {
			// If no ID was given, use a pattern matching multiple comma-separated
			// IDs, and without other capturing groups, for simplicity's sake.
			return '/'
				. '\[gallery [^\]]*ids=' // Gallery shortcode, up to ID attribute.
				. '(|["\'])' // Capturing group #2: ID attribute opening quote.
				. '(?P<ids>(?:\d+,)*\d+)' // One or more IDs, separated by commas.
				. '\1' // ID attribute closing quote, to match the opening quote.
				. '[^\]]*' // Any other attributes inside the gallery shortcode.
				. '\]' // End of gallery shortcode.
				. '/';
		} else {
			// If an ID was given, use a pattern matching the ID preceded and followed
			// by any number of other IDs, separated by commas.
			return '/'
				. '(' // Begin capturing group #1.
				. '\[gallery [^\]]*ids=' // Gallery shortcode, up to ID attribute.
				. '(|["\'])' // Capturing group #2: ID attribute opening quote.
				. '(?:\d+,)*' // Preceding IDs, if any.
				. ')' // End capturing group #1.
				. absint( $id )
				. '(' // Begin capturing group #3.
				. '(?:,\d+)*' // Following IDs, if any.
				. '\2' // ID attribute closing quote, to match the opening quote.
				. '[^\]]*' // Any other attributes inside the gallery shortcode.
				. '\]' // End of gallery shortcode.
				. ')' // End capturing group #3.
				. '/';
		}
	}

	/**
	 * Detect gallery shortcodes with 'ids=' attributes.
	 *
	 * @uses MDD_Reference_Handler::get_gallery_ids_regex()
	 *
	 * @param array  $refs    Array to which referenced IDs will be added.
	 * @param string $subject The value in which to look for gallery shortcodes.
	 * @return array An array of attachment IDs (as integers).
	 */
	public function detect_gallery_ids( $refs, $subject ) {

		// If this isn't a string, pass $refs along untouched.
		if ( ! is_string( $subject ) ) {
			return $refs;
		}

		$re = $this->get_gallery_ids_regex();

		// Search for matches.
		preg_match_all( $re, $subject, $matches );

		// Initialize an array for all IDs.
		$ids = array();

		// For each set of IDs, add each comma-separated ID to the $ids array.
		foreach ( $matches['ids'] as $match ) {
			$ids = array_merge( $ids, explode( ',', $match ) );
		}

		// Cast all IDs as integers.
		$ids = array_map( 'absint', $ids );

		// Add $ids to $refs, and weed out duplicates.
		$refs = array_unique( array_merge( $refs, $ids ) );

		return $refs;
	}

	/**
	 * Replace an old image ID in the [gallery] shortcode.
	 *
	 * @uses MDD_Reference_Handler::get_gallery_ids_regex()
	 *
	 * @param string $subject The value in which to look for gallery shortcodes.
	 * @param int    $old_id  The attachment ID to replace with $new_id.
	 * @param int    $new_id  The attachment ID to replace $old_id with.
	 */
	public function replace_gallery_ids( $subject, $old_id, $new_id ) {
		$re = $this->get_gallery_ids_regex( $old_id );
		return preg_replace( $re, "\${1}$new_id\${3}", $subject );
	}

	/**
	 * Return a regex that matches an image ID in the [caption] shortcode.
	 *
	 * @param int|null $id An image ID, if the regex will be used to match
	 *                     shortcodes for a specific ID, or NULL if the regex
	 *                     should match all [caption] shortcodes with attachment_*
	 *                     ID attributes.
	 * @return string A regular expression for use with preg_match() or
	 *                preg_replace().
	 */
	public function get_caption_id_regex( $id = null ) {

		if ( is_null( $id ) ) {
			// If no ID was given, add a named capturing group for preg_match().
			$id_pattern = '(?P<id>\d+)';
		} else {
			// If an ID was given, sanitize it.
			$id_pattern = absint( $id );
		}

		return '/'
			. '(' // Begin capturing group #1.
			. '\[caption [^\]]*id=' // Caption shortcode, up to ID attribute.
			. '(|["\'])' // Capturing group #2: ID attribute opening quote.
			. 'attachment_' // Standard ID attribute prefix.
			. ')' // End capturing group #1.
			. $id_pattern
			. '(' // Begin capturing group #3.
			. '\2' // ID attribute closing quote, to match the opening quote.
			. '[^\]]*' // Any other attributes inside the caption shortcode.
			. '\]' // End of caption shortcode opening tag.
			. ')' // End capturing group #3.
			. '/';
	}

	/**
	 * Detect caption shortcodes with ID attributes like "attachment_*".
	 *
	 * @uses MDD_Reference_Handler::get_caption_id_regex()
	 *
	 * @param array  $refs    Array to which referenced IDs will be added.
	 * @param string $subject The value in which to look for caption shortcodes.
	 * @return array An array of attachment IDs (as integers).
	 */
	public function detect_caption_ids( $refs, $subject ) {

		// If this isn't a string, pass $refs along untouched.
		if ( ! is_string( $subject ) ) {
			return $refs;
		}

		$re = $this->get_caption_id_regex();

		// Search for matches.
		preg_match_all( $re, $subject, $matches );

		// Cast all matches as integers.
		$ids = array_map( 'absint', $matches['id'] );

		// Add $ids to $refs, and weed out duplicate IDs.
		$refs = array_unique( array_merge( $refs, $ids ) );

		return $refs;
	}

	/**
	 * Replace an old image ID in the [caption] shortcode.
	 *
	 * @uses MDD_Reference_Handler::get_caption_id_regex()
	 *
	 * @param string $subject The value in which to look for caption shortcodes.
	 * @param int    $old_id  The attachment ID to replace with $new_id.
	 * @param int    $new_id  The attachment ID to replace $old_id with.
	 */
	public function replace_caption_ids( $subject, $old_id, $new_id ) {
		$re = $this->get_caption_id_regex( $old_id );
		return preg_replace( $re, "\${1}$new_id\${3}", $subject );
	}

	/**
	 * Return a regex that matches wp-image-[ID] class attributes.
	 *
	 * @param int|null $id An image ID, if the regex will be used to match classes
	 *                     for a specific ID, or NULL if the regex should match
	 *                     all wp-image-* classes.
	 * @return string A regular expression for use with preg_match() or
	 *                preg_replace().
	 */
	public function get_img_class_regex( $id = null ) {

		if ( is_null( $id ) ) {
			// If no ID was given, add a named capturing group for preg_match().
			$id_pattern = '(?P<id>\d+)';
		} else {
			// If an ID was given, sanitize it.
			$id_pattern = absint( $id );
		}

		return '/'
			. '(' // Begin capturing group #1.
			. '<img[^>]*class=' // Image tag, up to class attribute.
			. '(["\'])' // Capturing group #2: class attribute opening quote.
			. '(?:[^"\']* )*' // Preceding classes, if any.
			. 'wp-image-' // Standard class attribute prefix.
			. ')' // End capturing group #1.
			. $id_pattern
			. '(' // Begin capturing group #3.
			. '(?: [^"\']*)*' // Following classes, if any.
			. '\2' // Class attribute closing quote, to match the opening quote.
			. ')' // End capturing group #3. The rest of the img tag is irrelevant.
			. '/';
	}

	/**
	 * Detect img tags with class attributes like "wp-caption-*".
	 *
	 * @uses MDD_Reference_Handler::get_img_class_regex()
	 *
	 * @param array  $refs    Array to which referenced IDs will be added.
	 * @param string $subject The value in which to look for img tags.
	 * @return array An array of attachment IDs (as integers).
	 */
	public function detect_img_classes( $refs, $subject ) {

		// If this isn't a string, pass $refs along untouched.
		if ( ! is_string( $subject ) ) {
			return $refs;
		}

		$re = $this->get_img_class_regex();

		// Search for matches.
		preg_match_all( $re, $subject, $matches );

		// Cast all matches as integers.
		$ids = array_map( 'absint', $matches['id'] );

		// Add $ids to $refs, and weed out duplicate IDs.
		$refs = array_unique( array_merge( $refs, $ids ) );

		return $refs;
	}

	/**
	 * Replace an old wp-image-[ID] class attribute value in an <img> tag.
	 *
	 * This is important for WP's built-in responsive image functionality, which
	 * uses the wp-image-* class to detect images inserted using TinyMCE. Without
	 * the right attachment ID, WordPress won't be able to find other sizes of the
	 * image and add them to the srcset attribute.
	 *
	 * Note that unlike the gallery and caption shortcode replacement functions,
	 * this one won't handle unquoted class attributes. Who leaves an HTML class
	 * attribute unquoted? Definitely not WordPress itself, and this function
	 * mainly targets img tags generated by WP.
	 *
	 * @uses MDD_Reference_Handler::get_img_class_regex()
	 *
	 * @param string $subject The value in which to look for caption shortcodes.
	 * @param int    $old_id  The attachment ID to replace with $new_id.
	 * @param int    $new_id  The attachment ID to replace $old_id with.
	 */
	public function replace_img_classes( $subject, $old_id, $new_id ) {
		$re = $this->get_img_class_regex( $old_id );
		return preg_replace( $re, "\${1}$new_id\${3}", $subject );
	}

	/**
	 * Clear out site-specific data.
	 */
	public function switch_blog() {
		$this->attachment_url_regex = false;
	}

	/**
	 * Track references to attachments in all fields (properties and metadata) of
	 * a given post.
	 *
	 * @uses MDD_Reference_Handler::get_post_fields()
	 * @uses MDD_Reference_Handler::track_post_fields()
	 *
	 * @param int $post_id The ID of the post to track references in.
	 */
	public function track_post( $post_id ) {

		// Get the list of all fields for this post that should be checked for
		// references.
		$tracked_fields = $this->get_post_fields( $post_id );

		// If there are no fields to track, store an empty _mdd_references value so
		// we know we've checked this post already.
		if ( empty( $tracked_fields ) ) {
			update_post_meta( $post_id, '_mdd_references', array() );
			return;
		}

		// Iterate over tracked fields.
		foreach ( $tracked_fields as $field => $method ) {
			// Track and store references in this field.
			$this->track_post_field( $post_id, $field, $method );
		}
	}

	/**
	 * Track references to attachments in the properties of a newly added or
	 * upated post.
	 *
	 * @uses MDD_Reference_Handler::get_post_fields()
	 * @uses MDD_Reference_Handler::track_post_field()
	 *
	 * @param int $post_id The ID of the post to track references in.
	 */
	public function track_post_props( $post_id ) {

		// Don't track properties on revisions, because we can't store metadata for them.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Get the list of all fields for this post that should be checked for
		// references.
		$tracked_fields = $this->get_post_fields( $post_id );

		// If there are no fields to track, bail. This way we don't fire the `mdd_tracked_post_props`
		// hook unnecessarily, and thus avoid unnecessarily clearing transients.
		if ( empty( $tracked_fields ) ) {
			return;
		}

		// Iterate over tracked fields.
		foreach ( $tracked_fields as $field => $method ) {

			// Get the type and name of the field.
			list( $field_source, $field_name ) = explode( ':', $field, 2 );

			// If this field isn't a property, skip it.
			if ( 'prop' !== $field_source ) {
				continue;
			}

			// Track and store references in this field.
			$this->track_post_field( $post_id, $field, $method );
		}

		/**
		 * Fires after a new post has been inserted and properties have been tracked for the post.
		 *
		 * @param int $post_id The ID of the post that has just been tracked.
		 */
		do_action( 'mdd_tracked_post_props', $post_id );
	}

	/**
	 * Track references to attachments in a newly added or updated post meta
	 * field.
	 *
	 * @uses MDD_Reference_Handler::get_post_fields()
	 * @uses MDD_Reference_Handler::track_post_field()
	 *
	 * @param int    $meta_id    The ID(s) of the meta value in the postmeta
	 *                           table. Passed in by update_metadata(), ignored
	 *                           here.
	 * @param int    $post_id    The post whose metadata is being set/changed.
	 * @param string $meta_key   The meta key whose value has been set/changed.
	 */
	public function track_post_meta( $meta_id, $post_id, $meta_key ) {

		// Save time by bailing if this action was fired while track_post_meta() was running. None of
		// the other meta values that track_post_meta() sets need to be tracked.
		if ( $this->is_tracking_meta ) {
			return;
		}

		// Save time by bailing if the post whose metadata has changed is actually
		// being deleted. All references related to the post will be untracked later
		// by MDD_Reference_Handler::track_deleted_post().
		if ( in_array( $post_id, $this->posts_being_deleted, true ) ) {
			return;
		}

		// Get the list of all fields for this post that should be checked for
		// references.
		$tracked_fields = $this->get_post_fields( $post_id );

		// If this field isn't among the fields that are worth tracking, bail.
		if ( ! isset( $tracked_fields[ "meta:$meta_key" ] ) ) {
			return;
		}

		// Detect and store references found in this field.
		$this->track_post_field( $post_id, "meta:$meta_key", $tracked_fields[ "meta:$meta_key" ] );

		/**
		 * Fires after post metadata has been changed and the updated metadata has been tracked.
		 *
		 * @param int    $meta_id  The ID of the added/updated/removed row in the postmeta table.
		 * @param int    $post_id  The ID of the post to which the metadata belongs.
		 * @param string $meta_key The key for the metadata that has been added/updated/removed.
		 */
		do_action( 'mdd_tracked_post_meta', $meta_id, $post_id, $meta_key );
	}

	/**
	 * Detect references in a given post field, and store references in post and
	 * attachment metadata.
	 *
	 * @uses MDD_Reference_Handler::detect_in_post_field()
	 * @uses MDD_Reference_Handler::track_post_reference()
	 * @uses MDD_Reference_Handler::untrack_post_reference()
	 *
	 * @param int    $post_id The ID of the post in which to look for references.
	 * @param string $field   String describing the field in which to look for
	 *                        references.
	 * @param string $type    The type of value expected in $field.
	 */
	public function track_post_field( $post_id, $field, $type ) {

		// If this is a meta field, set a flag indicating that we're tracking a meta
		// field.
		if ( 0 === strpos( $field, 'meta:' ) ) {
			$this->is_tracking_meta = true;
		}

		// Get referenced attachments in this field, using the field type specified
		// in $type.
		$refs = $this->detect_in_post_field( $post_id, $field, $type );

		// Get the list of attachments, if any, that were referenced in this post,
		// prior to this upate.
		$refs_by_field = get_post_meta( $post_id, '_mdd_references', true );

		// If no references were previously stored for this post, initialize an
		// array of references.
		if ( empty( $refs_by_field ) ) {
			$refs_by_field = array();
		}

		// Get the list of attachments, if any, that were referenced in this field.
		$old_refs = isset( $refs_by_field[ $field ] ) ? $refs_by_field[ $field ] : array();

		// Iterate over attachment IDs currently referenced by this field.
		foreach ( $refs as $attachment_id ) {
			$this->track_post_reference( $attachment_id, $post_id, $field );
		}

		// Get an array of all attachments that _were_ referenced by this field
		// until it was updated, but aren't anyore.
		$removed_refs = array_diff( $old_refs, $refs );

		// Iterate over attachment IDs no longer referenced in this field.
		foreach ( $removed_refs as $attachment_id ) {
			$this->untrack_post_reference( $attachment_id, $post_id, $field );
		}

		// Update this post's _mdd_references value.
		$refs_by_field[ $field ] = $refs;
		update_post_meta( $post_id, '_mdd_references', $refs_by_field );

		// If this was a meta field, clear the is_tracking_meta flag.
		if ( 0 === strpos( $field, 'meta:' ) ) {
			$this->is_tracking_meta = false;
		}
	}

	/**
	 * Update an attachment's _mdd_referenced_by value to reflect a newly added
	 * reference in a post field.
	 *
	 * @param int    $attachment_id The ID of the attachment.
	 * @param int    $post_id       The ID of the post.
	 * @param string $field         String describing the post field that no
	 *                              longer references $attachment_id.
	 */
	function track_post_reference( $attachment_id, $post_id, $field ) {

		// Get the attachment's _mdd_referenced_by value.
		$fields_by_post = get_post_meta( $attachment_id, '_mdd_referenced_by', true );

		// If no references were previously stored for this attachment, initialize
		// an array of references.
		if ( empty( $fields_by_post ) ) {
			$fields_by_post = array();
		}

		// Get the array of the posts's fields (if any) containing known references
		// to the attachment.
		$fields = isset( $fields_by_post[ $post_id ] ) ? $fields_by_post[ $post_id ] : array();

		// If $field is already among them, bail -- we don't need to add anything.
		if ( in_array( $field, $fields, true ) ) {
			return;
		}

		// Add $field to the array of fields referencing this attachment.
		$fields[] = $field;

		// Remove duplicate fields.
		$fields = array_unique( $fields );

		// Update the array of fields in $fields_by_post.
		$fields_by_post[ $post_id ] = $fields;

		// Update the _mdd_referenced_by value.
		update_post_meta( $attachment_id, '_mdd_referenced_by', $fields_by_post );
	}

	/**
	 * Update an attachment's _mdd_referenced_by value to reflect a post field
	 * that no longer references it.
	 *
	 * @param int    $attachment_id The ID of the attachment.
	 * @param int    $post_id       The ID of the post.
	 * @param string $field         String describing the post field that no
	 *                              longer references $attachment_id.
	 */
	function untrack_post_reference( $attachment_id, $post_id, $field ) {

		// Get the attachment's _mdd_referenced_by value.
		$fields_by_post = get_post_meta( $attachment_id, '_mdd_referenced_by', true );

		// If no references were previously stored for this attachment, bail --
		// there's nothing to remove.
		if ( empty( $fields_by_post ) ) {
			return;
		}

		// If $fields_by_post doesn't contain any fields for the post, bail --
		// there's nothing to remove.
		if ( ! isset( $fields_by_post[ $post_id ] ) ) {
			return;
		}

		// Get the array of the post's fields containing known references to the
		// attachment.
		$fields = isset( $fields_by_post[ $post_id ] ) ? $fields_by_post[ $post_id ] : array();

		// Remove $field from the array of fields referencing the attachment.
		$fields = array_diff( $fields, array( $field ) );

		// If this was the only field on the post that referenced the attachment,
		// then remove the key for the post from $fields_by_post altogether.
		// Otherwise, replace the old array with the new one.
		if ( empty( $fields ) ) {
			unset( $fields_by_post[ $post_id ] );
		} else {
			$fields_by_post[ $post_id ] = $fields;
		}

		// Update the _mdd_referenced_by value.
		update_post_meta( $attachment_id, '_mdd_referenced_by', $fields_by_post );
	}

	/**
	 * When WP begins deleting a post, untrack all references to attachments.
	 *
	 * @param int $post_id The ID of the post being deleted.
	 */
	function track_deleted_post( $post_id ) {

		// Don't track revisions, because they won't have metadata anyway.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Update the $posts_being_deleted property.
		$this->posts_being_deleted[] = $post_id;

		// Get the list of attachments referenced by this post.
		$refs_by_field = get_post_meta( $post_id, '_mdd_references', true );

		// Bail if the _mdd_references meta field doesn't exist or isn't an array.
		if ( ! is_array( $refs_by_field ) ) {
			return;
		}

		// Consolidate the list of attachments by field into one list of all
		// attachment IDs.
		$all_refs = array();
		foreach ( $refs_by_field as $refs ) {
			$all_refs = array_unique( array_merge( $all_refs, $refs ) );
		}

		// For each referenced attachment ID, remove the post being deleted from the
		// list of posts/fields referencing the attachment.
		foreach ( $all_refs as $attachment_id ) {
			$fields_by_post = get_post_meta( $attachment_id, '_mdd_referenced_by', true );
			unset( $fields_by_post[ $post_id ] );
			update_post_meta( $attachment_id, '_mdd_referenced_by', $fields_by_post );
		}
	}

	/**
	 * When WP is done deleting a post, remove its ID from the
	 * $posts_being_deleted property.
	 *
	 * @param int $post_id The ID of the post that was deleted.
	 */
	function done_deleting_post( $post_id ) {

		// If this post's ID isn't in the $posts_being_deleted array, then we didn't do anything to
		// track the deletion of this post. Bail.
		if ( ! in_array( $post_id, $this->posts_being_deleted, true ) ) {
			return;
		}

		$this->posts_being_deleted = array_diff( $this->posts_being_deleted, array( $post_id ) );

		/**
		 * Fires after a post has been deleted and tracked reference data has been removed.
		 *
		 * @param int $post_id The ID of the deleted post.
		 */
		do_action( 'mdd_tracked_deleted_post', $post_id );
	}
}
