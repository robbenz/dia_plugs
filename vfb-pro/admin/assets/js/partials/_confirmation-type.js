jQuery(document).ready(function($) {
	$( '#type' ).change( function() {
		var type = $( this ).val();

		$( '.vfb-confirmation-type' ).hide();
		$( '#vfb-confirmation-' + type ).show();
	});
});