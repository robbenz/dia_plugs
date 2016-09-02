jQuery(document).ready(function($) {
	$( document ).on( 'click', '.vfb-field-delete', function( e ) {
		e.preventDefault();

		var data = [],
			href = $( this ).attr( 'href' ),
			url  = href.split( '&' );

		var confirmTitle  = vfbpL10n.confirmTitle,
			confirmText   = vfbpL10n.confirmText,
			confirmButton = vfbpL10n.confirmButton;

		$.confirm({
		    title: confirmTitle,
		    text: confirmText,
		    confirmButton: confirmButton,
		    confirm: function() {
			    for ( var i = 0; i < url.length; i++ ) {
					// break each pair at the first "=" to obtain the argname and value
					var pos     = url[i].indexOf( '=' ),
						argname = url[i].substring( 0, pos ),
						value   = url[i].substring( pos + 1 );

					data[ argname ] = value;
				}

				$.post( ajaxurl,
					{
						action: 'vfbp-delete-field',
						field: data.field,
						vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
					}
				).done( function() {
					$( '#vfb-field-item-' + data.field ).animate({
						opacity : 0,
						height: 0
					}, 350, function() {
						$( this ).remove();
					});
				});
		    },
		    cancel: function() {
		        return false;
		    }
		});
	});
});