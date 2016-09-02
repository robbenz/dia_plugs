var vfbFormValidate = jQuery.extend({
	invalidateForm: function ( selector ) {
		return jQuery( selector ).addClass( 'form-invalid' ).find('input:visible').change( function() { jQuery(this).closest('.form-invalid').removeClass( 'form-invalid' ); } );
	},
	validateForm: function( selector ) {
		selector = jQuery( selector );
		return !vfbFormValidate.invalidateForm( selector.find('.form-required').filter( function() { return jQuery('input:visible', this).val() === ''; } ) ).size();
	}
}, vfbFormValidate);

jQuery(document).ready(function($) {
	$( '#vfbp-new-form' ).submit( function() {
		return vfbFormValidate.validateForm( $( this ) );
	});
});