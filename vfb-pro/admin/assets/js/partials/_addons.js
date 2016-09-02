jQuery(document).ready(function($) {
	// !Create User
	$( '#user-enable' ).change( function() {
		$( '.vfb-create-user-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Create Post
	$( '#post-enable' ).change( function() {
		$( '.vfb-create-post-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Form Designer
	$( '#design-enable' ).change( function() {
		$( '.vfb-form-design-settings' ).toggle( $( this ).prop( 'checked' ) );

		// Refresh CodeMirror so an empty CSS box will display a line number
		setTimeout(function() {
		    designCSS.refresh();
		},1);
	});

	// !Notifications - Mobile Phone
	$( '#cell-enable' ).change( function() {
		$( '.vfb-cell-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Notifications - MailChimp
	$( '#mailchimp-enable' ).change( function() {
		$( '.vfb-mailchimp-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Notifications - Campaign Monitor
	$( '#campaign-monitor-enable' ).change( function() {
		$( '.vfb-campaign-monitor-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Notifications - Highrise
	$( '#highrise-enable' ).change( function() {
		$( '.vfb-highrise-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Notifications - Freshbooks
	$( '#freshbooks-enable' ).change( function() {
		$( '.vfb-freshbooks-settings' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Payments
	$( '#payments-enable' ).change( function() {
		$( '.vfb-payments-settings' ).toggle( $( this ).prop( 'checked' ) );

		// Hide PayPal settings if unchecked
		if ( ! $( this ).prop( 'checked' ) ) {
			$( '.vfb-paypal-settings' ).hide();
		}
	});

	// !Payments - Merchant Settings
	$( '#payments-merchant' ).change( function() {
		var value = $( this ).val();

		$( '.vfb-paypal-settings' ).hide();

		switch ( value ) {
			case 'paypal' :
				$( '.vfb-paypal-settings' ).show();
				break;
		}
	});

	// !Payments - PayPal Billing
	$( '#paypal-prepop-billing' ).change( function() {
		$( '.vfb-paypal-billing' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Payments - PayPal Recurring
	$( '#paypal-recurring' ).change( function() {
		$( '.vfb-paypal-recurring' ).toggle( $( this ).prop( 'checked' ) );
	});

	// !Form Designer - Custom CSS code syntax
	if ( $( '#design-css' ).length ) {
		var designCSS = CodeMirror.fromTextArea( document.getElementById( 'design-css' ), {
	        lineNumbers: true,
	        lineWrapping: true,
	        mode: 'css',
	        theme: 'base16-light'
	    });
    }

	// !Form Designer - Copy settings
	$( document ).on( 'submit', '#vfbp-designer-copy-settings', function(e) {
		e.preventDefault();

		var d = $( this ).serialize();

		$.post( ajaxurl,
			{
				action: 'vfbp-form-designer-copy-settings-save',
				data: d,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function() {
			window.tb_remove();
		});
	});

	// Ensure spinners are hidden
	$( '#vfb-mailchimp-verify-api .spinner, #vfb-mailchimp-deactivate-api .spinner,' +
	   '#vfb-campaign-monitor-verify-api .spinner, #vfb-campaign-monitor-deactivate-api .spinner #vfb-campaign-monitor-select-client .spinner,' +
	   '#vfb-highrise-verify-api .spinner, #vfb-highrise-deactivate-api .spinner,' +
	   '#vfb-freshbooks-verify-api .spinner, #vfb-freshbooks-deactivate-api .spinner'
	).hide();

	// !Notifications - MailChimp Connect
	$( '#vfb-mailchimp-verify-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#mailchimp-api' ).val(),
			verifiedText = $( '#vfb-verified-mailchimp-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-mailchimp-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-connect-mailchimp',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - MailChimp Disconnect
	$( '#vfb-mailchimp-deactivate-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#mailchimp-api' ).val(),
			verifiedText = $( '#vfb-verified-mailchimp-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-mailchimp-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-disconnect-mailchimp',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Campaign Monitor Connect
	$( '#vfb-campaign-monitor-verify-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#campaign-monitor-api' ).val(),
			verifiedText = $( '#vfb-verified-campaign-monitor-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-campaign-monitor-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-connect-campaign-monitor',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Campaign Monitor Disconnect
	$( '#vfb-campaign-monitor-deactivate-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#campaign-monitor-api' ).val(),
			verifiedText = $( '#vfb-verified-campaign-monitor-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-campaign-monitor-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-disconnect-campaign-monitor',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Campaign Monitor Select Client
	$( '#vfb-campaign-monitor-select-client' ).click( function(e) {
		e.preventDefault();

		var apiKey   = $( '#campaign-monitor-api' ).val(),
			clientID = $( '#campaign-monitor-client-id' ).val(),
			formID   = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner  = $( '#vfb-campaign-monitor-select-client .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-campaign-monitor-select-client',
				api: apiKey,
				client: clientID,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();

			$( '#vfb-campaign-monitor-client-response' ).after( response );
		});
	});

	// !Notifications - Highrise Connect
	$( '#vfb-highrise-verify-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#highrise-api' ).val(),
			subDomain	 = $( '#highrise-subdomain' ).val(),
			verifiedText = $( '#vfb-verified-highrise-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-highrise-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-connect-highrise',
				api: apiKey,
				domain: subDomain,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Highrise Disconnect
	$( '#vfb-highrise-deactivate-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#highrise-api' ).val(),
			verifiedText = $( '#vfb-verified-highrise-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-highrise-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-disconnect-highrise',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Freshbooks Connect
	$( '#vfb-freshbooks-verify-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#freshbooks-api' ).val(),
			subDomain	 = $( '#freshbooks-subdomain' ).val(),
			verifiedText = $( '#vfb-verified-freshbooks-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-freshbooks-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-connect-freshbooks',
				api: apiKey,
				domain: subDomain,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	// !Notifications - Freshbooks Disconnect
	$( '#vfb-freshbooks-deactivate-api' ).click( function(e) {
		e.preventDefault();

		var apiKey       = $( '#freshbooks-api' ).val(),
			verifiedText = $( '#vfb-verified-freshbooks-text' ),
			formID       = $( 'input[name="_vfbp_form_id"]' ).val(),
			spinner      = $( '#vfb-freshbooks-verify-api .spinner' ).show().css( 'visibility', 'visible' );

		$.get( ajaxurl,
			{
				action: 'vfbp-disconnect-freshbooks',
				api: apiKey,
				form: formID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {

			spinner.hide();
			var json = $.parseJSON( response );

			$( verifiedText ).html( '<span class="status-' + json.status + '">' + json.message + '</span>' );

			$( '#vfbp-addon-settings' ).submit();
		});
	});

	var fieldIDs  = [],
		hiddenIDs = $( '.vfb-payment-field-ids' );

	// If options exist on the page, don't add to the Assign Prices drop down
	if ( hiddenIDs.length ) {
		$( hiddenIDs ).each( function(){
			fieldIDs.push( $( this ).val() );
		});
	}

	// !Payments - Assign Prices dropdown
	$( document ).on( 'change', '#vfb-payment-fields', function(){
		var id     = $( this ).val(),
			formID = $( 'input[name="_vfbp_form_id"]' ).val(),
			that = $( this );

		if ( id === '' ) {
			return;
		}

		// Build array of already selected IDs
		fieldIDs.push( id );

		vfbp_display_options( id, $( '.vfb-payment-assign-prices-header' ) );

		$.post( ajaxurl,
			{
				action: 'vfbp-price-fields',
				form: formID,
				fields: fieldIDs,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			that.html( response );
		});
	});

	/**
	 * vfbp_display_options function.
	 *
	 * @access public
	 * @param mixed fieldID
	 * @param mixed element
	 * @return void
	 */
	function vfbp_display_options( fieldID, element ) {
		$.post( ajaxurl,
			{
				action: 'vfbp-price-fields-options',
				field: fieldID,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			element.before( response );
		});
	}

	// Payments - Delete Field
	$( document ).on( 'click', '.vfb-payment-remove-field', function( e ) {
		e.preventDefault();

		var data   = [],
			formID = $( 'input[name="_vfbp_form_id"]' ).val(),
			parent = $( this ).parents( '.vfb-pricing-fields-container' ),
			href   = $( this ).attr( 'href' ),
			url    = href.split( '&' );

		for ( var i = 0; i < url.length; i++ ) {
			// break each pair at the first "=" to obtain the argname and value
			var pos     = url[i].indexOf( '=' ),
				argname = url[i].substring( 0, pos ),
				value   = url[i].substring( pos + 1 );

			data[ argname ] = value;
		}

		var fieldID = data.field;

		// Get index of field ID in array
		var index = fieldIDs.indexOf( fieldID );

		// Remove from array if field is found
		if ( index !== -1 ) {
			fieldIDs.splice( index, 1 );
		}

		// Remove the pricing field box
		parent.fadeOut( 350, function() {
			$( this ).remove();
		});

		$.post( ajaxurl,
			{
				action: 'vfbp-price-fields',
				form: formID,
				field: fieldIDs,
				vfbp_ajax_nonce: vfbp_settings.vfbp_ajax_nonce
			}
		).done( function( response ) {
			$( '#vfb-payment-fields' ).html( response );
		});
	});
});