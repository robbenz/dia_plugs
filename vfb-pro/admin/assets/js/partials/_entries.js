/* global columns: {} */
jQuery(document).ready(function($) {
	if ( pagenow === 'edit-vfb_entry' ) {
		// Current Form ID
		var selected = $( '#vfb-entry-form-ids' ).val();

		$( document ).on( 'click', '.hide-column-tog', function(){
			// Comma-separated list of hidden columns
			var hidden = columns.hidden();

			$.post( ajaxurl,
				{
					action: 'vfbp-entry-columns',
					columns: hidden,
					form_id: selected,
					vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
				}
			);
		});
	}
});