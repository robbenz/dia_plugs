jQuery(document).ready(function($) {
	if ( $.fn.jSignature ) {
		if ( $( '.vfb-signature' ).length > 0 ) {
			$( '.vfb-signature' ).each( function() {
				var sig        = $( this ).jSignature(),
					sigInput   = $( this ).prev( '.vfb-signature-input' ),
					sigButtons = sig.next( '.vfb-signature-buttons' ),
					sigData;

				// If signature has been used
				sig.on( 'change', function(e) {
					var data     = $( e.target ).jSignature( 'getData', 'native' );

					// Display reset button
					sigButtons.show();

					// Check if there are more than 2 strokes in the signature
					// Or, if there is just one stroke that it has more than 20 points
					if ( data.length > 2 || ( data.length === 1 && data[0].x.length > 20 ) ) {
						sigData = sig.jSignature( 'getData' );
					}
				});

				// Action for Reset button
				sig.next( '.vfb-signature-buttons' ).click( function(e){
					e.preventDefault();
					sig.jSignature( 'reset' );
				});

				// Load base64 data in the hidden input value on submit
				$( '.vfbp-form' ).submit( function() {
					if ( $( '.vfbp-form' ).parsley( 'isValid' ) ) {
						sig.prev( sigInput ).val( sigData );
					}
				});
			});
		}
	}
});