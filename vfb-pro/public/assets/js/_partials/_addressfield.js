/* global _vfbCountryConfig */
jQuery(document).ready(function($) {
	if ( $.fn.addressfield ) {
		var configuredFields = [
			'vfb-addresspart-address-1',
			'vfb-addresspart-city',
			'vfb-addresspart-province',
			'vfb-addresspart-zip'
		];

		// On page load, localize the address block based on the country
		if ( $( '.vfb-address-block' ).length > 0 ) {
			$( '.vfb-address-block' ).each( function() {
				var addrID 		= $( this ).attr( 'id' ),
					addrCountry = $( this ).find( '.vfb-addresspart-country' ).val();

				$( '#' + addrID ).addressfield( _vfbCountryConfig[addrCountry], configuredFields );
			});
		}

		// On country change
		$( '.vfb-addresspart-country' ).change( function() {
			var addressBlock = $( this ).closest( '.vfb-address-block' );

			// Trigger the addressfield plugin with the country's data.
			addressBlock.addressfield( _vfbCountryConfig[this.value], configuredFields );
		});
	}
});