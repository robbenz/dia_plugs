jQuery(document).ready(function($) {
	$( document ).on( 'click', '.vfb-field-duplicate', function( e ) {
		e.preventDefault();

		var data = [],
			href = $( this ).attr( 'href' ),
			url  = href.split( '&' );

		for ( var i = 0; i < url.length; i++ ) {
			// break each pair at the first "=" to obtain the argname and value
			var pos     = url[i].indexOf( '=' ),
				argname = url[i].substring( 0, pos ),
				value   = url[i].substring( pos + 1 );

			data[ argname ] = value;
		}

		$.post( ajaxurl,
			{
				action: 'vfbp-duplicate-field',
				field: data.field,
				order: $( '#vfb-fields-list' ).sortable( 'toArray' ),
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			var id = $( response ).attr( 'id' );

			// Insert the duplicate field
			$( response ).hide().insertAfter( '#vfb-field-item-' + data.field );

			$( '#' + id ).fadeIn();
		});
	});
});