jQuery(document).ready(function($) {
	$( '#vfb-forms-switch-btn' ).click( function(e) {
		e.preventDefault();

		var href     = window.location.href,
    		formID   = $( this ).prev().val(),
    		redirect = href.replace( new RegExp( /(form=)\d+/g ), '$1' + formID );

    	window.location = redirect;
	});
});