jQuery(document).ready(function($) {
	// Form items draggable button
	$( '#vfb-form-items .vfb-draggable-form-items' ).draggable({
		connectToSortable: '#vfb-fields-list',
		helper: function(){
			var helper = '<dl class="vfb-field-item-bar"><dt class="vfb-field-item-handle"><span class="vfb-field-item-title">' + $( this ).text() + '</span></dt></dl>';

			return helper;
		},
		distance: 2,
		zIndex: 5,
		cursorAt: { top: 20 },
		containment: 'document'
	});

	// Multi-use function to handle create on drag and the field sorting
	$( '#vfb-fields-list' ).sortable({
		handle: '.vfb-field-item-handle',
		placeholder: 'vfb-sortable-placeholder',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		tolerance: 'pointer',
		create: function() {
			// Make sure the page doesn't jump when at the bottom
			$( this ).css( 'min-height', $( this ).height() );
		},
		stop: function( event, ui ) {
			if ( ui.item.hasClass( 'ui-draggable-dragging' ) ) {

				// Hide the dropped sortable
				ui.item.hide();

				var d          = $( '#vfb-form-items' ).serializeArray(),
					field_type = ui.item.text(),
					$item      = ui.item,
					spinner    = $( '.vfb-accordion-section-content .spinner' ).show();

				$.post( ajaxurl,
					{
						action: 'vfbp-create-field',
						order: $( this ).sortable( 'toArray' ),
						data: d,
						field_type: field_type,
						vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
					}
				).done( function( response ) {
					spinner.hide();

					// Get new ID so we can properly fadeIn
                	var $new_item = $( response ),
                    	new_id = $new_item.closest( 'li.vfb-field-item' ).attr( 'id' );

                    // Insert the new field before the dropped item
					$new_item.hide().insertBefore( $item );

					$( '#' + new_id ).fadeIn();

					// Remove the dropped item
					$item.remove();
				});
			}
			else {
				$.post( ajaxurl,
					{
						action: 'vfbp-sort-field',
						order: $( this ).sortable( 'serialize' ),
						vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
					}
				).done();
			}
		}
	});

	// Click to create
	$( '#vfb-form-items .vfb-draggable-form-items' ).click( function( e ) {
		e.preventDefault();

		var d          = $( '#vfb-form-items' ).serializeArray(),
			field_type = $( this ).text(),
			spinner    = $( '.vfb-accordion-section-content .spinner' ).show();

		$.post( ajaxurl,
			{
				action: 'vfbp-create-field',
				data: d,
				field_type: field_type,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			spinner.hide();

			// Insert the new field last
			$( '#vfb-fields-list' ).append( response );
		});
	});
});