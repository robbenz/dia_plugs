jQuery(document).ready(function($) {
	if ( $.fn.intlTelInput ) {
		$( '.vfb-intl-phone' ).intlTelInput({
			utilsScript: vfbp_phone_format.vfbp_phone_url
		});
	}
});