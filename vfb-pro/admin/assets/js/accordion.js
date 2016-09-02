/**
 * Accordion-folding functionality.
 *
 * Markup with the appropriate classes will be automatically hidden,
 * with one section opening at a time when its title is clicked.
 * Use the following markup structure for accordion behavior:
 *
 * <div class="vfb-accordion-container">
 *	<div class="vfb-accordion-section open">
 *		<h3 class="vfb-accordion-section-title"></h3>
 *		<div class="vfb-accordion-section-content">
 *		</div>
 *	</div>
 *	<div class="vfb-accordion-section">
 *		<h3 class="vfb-accordion-section-title"></h3>
 *		<div class="vfb-accordion-section-content">
 *		</div>
 *	</div>
 *	<div class="vfb-accordion-section">
 *		<h3 class="vfb-accordion-section-title"></h3>
 *		<div class="vfb-accordion-section-content">
 *		</div>
 *	</div>
 * </div>
 *
 * Note that any appropriate tags may be used, as long as the above classes are present.
 *
 * @since 3.6.0.
 */

( function( $ ){

	$( document ).ready( function () {

		// Expand/Collapse accordion sections on click.
		$( '.vfb-accordion-container' ).on( 'click keydown', '.vfb-accordion-section-title', function( e ) {
			if ( e.type === 'keydown' && 13 !== e.which ) { // "return" key
				return;
			}

			e.preventDefault(); // Keep this AFTER the key filter above

			vfbAccordionSwitch( $( this ) );
		});

	});

	/**
	 * Close the current accordion section and open a new one.
	 *
	 * @param {Object} el Title element of the accordion section to toggle.
	 * @since 3.6.0
	 */
	function vfbAccordionSwitch ( el ) {
		var section = el.closest( '.vfb-accordion-section' ),
			siblings = section.closest( '.vfb-accordion-container' ).find( '.open' ),
			content = section.find( '.vfb-accordion-section-content' );

		// This section has no content and cannot be expanded.
		if ( section.hasClass( 'cannot-expand' ) ) {
			return;
		}

		if ( section.hasClass( 'open' ) ) {
			section.toggleClass( 'open' );
			content.toggle( true ).slideToggle( 150 );
		} else {
			siblings.removeClass( 'open' );
			siblings.find( '.vfb-accordion-section-content' ).show().slideUp( 150 );
			content.toggle( false ).slideToggle( 150 );
			section.toggleClass( 'open' );
		}
	}

})(jQuery);
