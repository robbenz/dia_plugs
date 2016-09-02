jQuery(document).ready(function($) {
	$( document ).on( 'click', '.vfb-item-edit-link', function( e ){
		e.preventDefault();

		$( e.target ).closest( 'li' ).children( '.vfb-field-item-settings' ).slideToggle( 'fast' );

		$( this ).toggleClass( 'opened' );
		var item = $( e.target ).closest( 'dl' );

		if ( item.hasClass( 'vfb-field-item-inactive' ) ) {
			item.removeClass( 'vfb-field-item-inactive' )
				.addClass( 'vfb-field-item-active' );
		}
		else {
			item.removeClass( 'vfb-field-item-active' )
				.addClass( 'vfb-field-item-inactive' );
		}
	});

	// Advanced Settings
	$( document ).on( 'click', '.vfb-field-adv-settings-link', function( e ){
		e.preventDefault();

		$( this ).parents( '.vfb-row' ).children( '.vfb-field-adv-settings' ).toggle();

		var item = $( this ).children( '.dashicons' );

		if ( item.hasClass( 'dashicons-arrow-down' ) ) {
			item.removeClass( 'dashicons-arrow-down' )
				.addClass( 'dashicons-arrow-up' );
		}
		else {
			item.removeClass( 'dashicons-arrow-up' )
				.addClass( 'dashicons-arrow-down' );
		}
	});

	// Validation Settings
	$( document ).on( 'click', '.vfb-field-validation-settings-link', function( e ){
		e.preventDefault();

		$( this ).parents( '.vfb-row' ).children( '.vfb-field-validation-settings' ).toggle();

		var item = $( this ).children( '.dashicons' );

		if ( item.hasClass( 'dashicons-arrow-down' ) ) {
			item.removeClass( 'dashicons-arrow-down' )
				.addClass( 'dashicons-arrow-up' );
		}
		else {
			item.removeClass( 'dashicons-arrow-up' )
				.addClass( 'dashicons-arrow-down' );
		}
	});

	// Hidden field "Custom value"
	$( document ).on( 'change', '.vfb-hidden-option', function() {
		var id = $( this ).attr( 'id' ).match( new RegExp( /(\d+)$/g ), '' );

		if ( $( this ).val() === 'custom' ) {
			$( '#vfb-hidden-custom-' + id ).show();
			$( '#vfb-hidden-seq-start-' + id ).hide();
			$( '#vfb-hidden-seq-step-' + id ).hide();
		}
		else if ( $( this ).val() === 'sequential-num' ) {
			$( '#vfb-hidden-custom-' + id ).hide();
			$( '#vfb-hidden-seq-start-' + id ).show();
			$( '#vfb-hidden-seq-step-' + id ).show();
		}
		else {
			$( '#vfb-hidden-custom-' + id ).hide();
			$( '#vfb-hidden-seq-start-' + id ).hide();
			$( '#vfb-hidden-seq-step-' + id ).hide();
		}
	});
});