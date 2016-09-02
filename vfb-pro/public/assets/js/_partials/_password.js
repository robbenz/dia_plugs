jQuery(document).ready(function($) {
	if ( $.fn.pwstrength ) {
		$( '.vfb-password' ).pwstrength({
			common: {
				zxcvbn: true
			}
		});
	}
});