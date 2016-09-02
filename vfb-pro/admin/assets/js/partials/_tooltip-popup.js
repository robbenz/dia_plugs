jQuery(document).ready(function($) {
	// !Initialize our tooltip timeout var
	var tooltipTimeout = null;

	// !Display/Hide the tooltip
	$( document ).on( 'mouseenter mouseleave', '.vfb-tooltip', function( e ) {
		var tipTitle = $( this ).attr( 'data-title' ),
			tip      = $( this ).attr( 'data-content' ),
			template = '<div class="vfb-tooltip-popup"><div class="vfb-tooltip-arrow"></div><h3>' + tipTitle + '</h3><p>' + tip + '</p></div>';

		// If mouse over tooltips
		if( e.type === 'mouseenter' ) {
			// Clear the timeout of our tooltip, if it exists
			if ( tooltipTimeout ) {
				clearTimeout( tooltipTimeout );
				tooltipTimeout = null;
			}

			// Create our tooltip popup
			$( this ).append( template );

			// Move over the div so it's not on top of the link
			$( this ).find( '.vfb-tooltip-popup' ).css({top: '-62px', left: 25});

			// Set a timer for hover intent
			tooltipTimeout = setTimeout( function(){
				$( '.vfb-tooltip-popup' ).fadeIn( 300 );
			}, 500 );
		}
		else {
			// Close the tooltip
			$( '.vfb-tooltip-popup' ).fadeOut( 500 );

			// Remove the appended tooltip div
			$( this ).children( '.vfb-tooltip-popup' ).detach();
		}
	});
});