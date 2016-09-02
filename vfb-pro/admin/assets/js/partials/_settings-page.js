jQuery(document).ready(function($) {
	$( '#smtp-auth' ).change( function() {
		$( '#smtp-auth-details' ).toggle( $( this ).prop( 'checked' ) );
	});

	$( '#custom-validation-msgs' ).change( function() {
		$( '#vfb-validation-msgs' ).toggle( $( this ).prop( 'checked' ) );
	});

	// Ensure spinners are hidden
	$( '#vfb-verify-license .spinner, #vfb-deactivate-license .spinner' ).hide();

	$( '#vfb-verify-license' ).click( function(e) {
		e.preventDefault();

		var licenseKey   = $( '#license-key' ).val(),
			licenseEmail = $( '#license-email' ).val(),
			verifiedText = $( '#vfb-verified-text' ),
			spinner      = $( '#vfb-verify-license .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-verify-license',
				license: licenseKey,
				email: licenseEmail,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			if ( 1 === json.status ) {
				$( '#vfbp-settings' ).submit();
			}
		});
	});

	$( '#vfb-deactivate-license' ).click( function(e) {
		e.preventDefault();

		var licenseKey   = $( '#license-key' ).val(),
			licenseEmail = $( '#license-email' ).val(),
			verifiedText = $( '#vfb-verified-text' ),
			spinner      = $( '#vfb-deactivate-license .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-deactivate-license',
				license: licenseKey,
				email: licenseEmail,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-settings' ).submit();
		});
	});
});