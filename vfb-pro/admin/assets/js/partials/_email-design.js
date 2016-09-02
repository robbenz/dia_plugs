jQuery(document).ready(function($) {
	// !Code Editor
	CodeMirror.defineMode( 'wordpress', function(config, parserConfig) {
		var wordpressOverlay = {
			token: function( stream ) {
				var ch;

				if ( stream.match( '[' ) ) {
					while ( typeof ( ch = stream.next() ) !== 'undefined' ) {
						if ( ch === ']' ) {
							break;
						}
					}

					return 'wordpress';
				}

				while ( typeof stream.next() !== 'undefined' && !stream.match( '[', false ) ) {}

				return null;
			}
		};

		return CodeMirror.overlayMode( CodeMirror.getMode( config, parserConfig.backdrop || 'htmlmixed'), wordpressOverlay );
	});

	if ( $( '#email-template' ).length ) {
		CodeMirror.fromTextArea( document.getElementById( 'email-template' ), {
	        lineNumbers: true,
	        lineWrapping: true,
	        mode: 'wordpress',
	        theme: 'base16-light'
	    });
    }

	// !Create color pickers
	$( '.vfb-color-picker' ).each( function() {
		var $this     = $( this ),
			id        = $this.attr( 'id' ),
			vfb_color = $( '#' + id );

		vfb_color.wpColorPicker({
            change: function(event, ui) {
                vfb_color.css( 'background-color', ui.color.toString() );
            },
            clear: function() {
                vfb_color.css( 'background-color', '' );
            }
        });
	});

	// !Tab in Textareas
	$( '#email-template' ).bind( 'keydown.vfbInsertTab', function(e) {
		var el = e.target, selStart, selEnd, val, scroll, sel;

		// Escape key
		if ( e.keyCode === 27 ) {
			$( el ).data( 'tab-out', true );
			return;
		}

		// Tab key
		if ( e.keyCode !== 9 || e.ctrlKey || e.altKey || e.shiftKey ) {
			return;
		}

		if ( $( el ).data( 'tab-out' ) ) {
			$( el ).data( 'tab-out', false );
			return;
		}

		selStart = el.selectionStart;
		selEnd   = el.selectionEnd;
		val      = el.value;

		// Not a standard DOM property, lastKey is to help stop Opera tab event. See blur handler below.
		try {
			this.lastKey = 9;
		}
		catch( err ) {
		}

		if ( document.selection ) {
			el.focus();
			sel = document.selection.createRange();
			sel.text = '\t';
		}
		else if ( selStart >= 0 ) {
			scroll   = this.scrollTop;
			el.value = val.substring( 0, selStart ).concat( '\t', val.substring( selEnd ) );
			el.selectionStart = el.selectionEnd = selStart + 1;
			this.scrollTop = scroll;
		}

		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		if ( e.preventDefault ) {
			e.preventDefault();
		}
	});

	$( '#email-template' ).bind('blur.vfbInsertTab', function() {
		if ( this.lastKey && 9 === this.lastKey ) {
			this.focus();
		}
	});

	// !Hide Font and Color options if Plain Text
	$( '#format' ).change( function() {
		var type = $( this ).val();

		$( '.vfb-email-type' ).hide();
		$( '#vfb-email-' + type ).show();
	});


    var frame,
    	imgContainer = $( '.vfb-header-img-container' ),
    	imgIdInput   = $( '.vfb-header-img-id' ),
    	addImgLink   = $( '.vfb-header-img-upload' ),
    	delImgLink   = $( '.vfb-header-img-delete' );

	addImgLink.click( function(e){
	    e.preventDefault();

	    // If the media frame already exists, reopen it
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			states: [
				new wp.media.controller.Library({
					title:     vfbpL10n.chooseImage,
					library:   wp.media.query({ type: 'image' }),
					multiple:  false,
					date:      false,
					priority:  20,
					suggestedWidth: 600,
					suggestedHeight: 200
				})
			]
		});

		// When an image is selected in the media frame...
		frame.on( 'select', function() {

			// Get media attachment details from the frame state
			var attachment = frame.state().get( 'selection' ).first().toJSON();

			// Send the attachment URL to our custom image input field.
			imgContainer.append( '<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>' );

			// Send the attachment id to our hidden input
			imgIdInput.val( attachment.id );

			// Hide the add image link
			addImgLink.addClass( 'hidden' );

			// Unhide the remove image link
			delImgLink.removeClass( 'hidden' );
		});

		// Finally, open the modal on click
		frame.open();
    });

    // Delete Image Link
	delImgLink.on( 'click', function(e){
		e.preventDefault();

		// Clear out the preview image
		imgContainer.html( '' );

		// Un-hide the add image link
		addImgLink.removeClass( 'hidden' );

		// Hide the delete image link
		delImgLink.addClass( 'hidden' );

		// Delete the image id from the hidden input
		imgIdInput.val( '' );
	});
});