/**
 * Add links to uploader error messages.
 *
 * Uploader error messages like the one returned by
 * Media_Deduper::block_duplicate_uploads() are run through esc_html() before
 * being displayed to the user, so they can't contain links or other HTML. As a
 * workaround, we can add event listeners that detect when a file upload has
 * been attempted, look through any new error messages, and use DOM
 * manipulation to add links where appropriate.
 */
jQuery( function( $ ) {

	/**
	 * Detect new error messages (i.e. elements matching the `selector`
	 * argument), look for strings matching the following format:
	 *   #123 (Attachment Title) [http://cool.website/attachment/edit/url]
	 * ...and replace them with links to the Edit Attachment URL.
	 *
	 * The `selector` argument lets us use different selectors in different
	 * contexts: /wp-admin/media-new.php uses an older-school uploader with one
	 * set of classes, while /wp-admin/upload.php and other pages that feature
	 * the Backbone-based media modal 'frame' interface use different classes &
	 * markup structure.
	 */
	var format_errors = function( selector ) {
		// Only loop over error messages that we haven't already reformatted.
		$( selector ).filter( ':not(.is-mdd-formatted)' ).each( function() {

			// Get the entire HTML content of each error message.
			var error_html = $( this ).html(),
				// Look for our placeholder pattern and parse it.
				matches = error_html.match( /#([0-9]*) \((.*)\) \[([^\]]*)\]/ );

			if ( matches ) {
				var attachment_id = matches[1],
					attachment_title = matches[2],
					// On upload.php, the error message may have been double-escaped,
					// since the `uploader-status-error` template uses {{ }} instead of
					// {{{ }}} to output the error message. So make sure to un-escape
					// any escaped ampersands.
					attachment_url = matches[3].replace( /&(amp|#038);/, '&' ),
					// Piece them together into a nice happy link.
					link = '<a href="' + attachment_url + '" target="_blank" rel="noopener noreferrer">' + attachment_title + '</a>';

				// Replace the placeholder pattern with the link.
				$( this ).html( error_html.replace( matches[0], link ) )
			}

			// Mark this message as formatted, so we don't have to check it again.
			$( this ).addClass( 'is-mdd-formatted' );

		} );
	};

	// If we're using a legacy(?) uploader, like on media-new.php, bind a
	// listener to the plupload object.
	if ( 'uploader' in window ) {

		uploader.bind( 'FileUploaded', function() {
			format_errors( '.error-div.error' );
		} );

	}
	// If this is a page that's set to use the media frame interface, bind a
	// listener to the global uploader errors collection.
	else if (
		( 'wp' in window )
		&& ( 'Uploader' in wp )
		&& ( 'errors' in wp.Uploader )
	) {

		wp.Uploader.errors.on( 'add', function() {
			// Delay execution of format_errors() infinitesimally. Thanks to the way
			// the media upload frame gets initialized on screens *other* than
			// upload.php, this event listener gets attached to wp.Uploader.errors
			// *before* the built-in event listener that actually inserts content
			// into the DOM. A setTimeout delay of 0 allows the other event handlers
			// to fire and insert the error message elements so we can manipulate
			// them afterwards.
			setTimeout( function() {
				format_errors( '.upload-errors .upload-error' );
			}, 0 );
		} );

	}
} );
