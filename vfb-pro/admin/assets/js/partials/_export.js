jQuery(document).ready(function($) {
	// !Show/Hide Entries export options
	$( '#vfbp-export' ).find( 'input:radio' ).change( function() {
		if ( 'entries' === $( this ).val() ) {
			$( '.vfb-export-entries-options' ).show();
		}
		else {
			$( '.vfb-export-entries-options' ).hide();
		}
	});

	// !Fields Select All
	$( '#vfb-export-select-all' ).click( function( e ) {
		e.preventDefault();

		$( '#vfb-export-entries-fields input[type="checkbox"]' ).prop( 'checked', true );
	});

	// !Fields Unselect All
	$( '#vfb-export-unselect-all' ).click( function( e ) {
		e.preventDefault();

		$( '#vfb-export-entries-fields input[type="checkbox"]' ).prop( 'checked', false );
	});

	// !Date Range Start
	$( '#start-date' ).datetimepicker({
		format: 'Y/m/d',
		onShow: function() {
			this.setOptions({
				maxDate:$( '#end-date' ).val() ? $( '#end-date' ).val() : false
			});
		},
		timepicker: false,
		closeOnDateSelect: true
	});

	// !Date Range End
	$('#end-date').datetimepicker({
		format: 'Y/m/d',
		onShow: function() {
			this.setOptions({
				minDate: $( '#start-date' ).val() ? $( '#start-date' ).val() : false
			});
		},
		timepicker: false,
		closeOnDateSelect: true
	});

	$( '#vfb-export-forms-list' ).change( function() {
		var form_id = $( this ).val();

		$( '#vfb-export-entries-fields' ).html( 'Loading...' );

		$.get( ajaxurl,
			{
				action: 'vfbp-export-fields',
				id: form_id,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			$( '#vfb-export-entries-fields' ).html( response );
		});
	});
});