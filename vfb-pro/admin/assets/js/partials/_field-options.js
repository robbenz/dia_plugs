jQuery(document).ready(function($) {
	// !Sort options
	$( '.vfb-options-table' ).sortable({
		items: '.vfb-option',
		handle: '.vfb-sort-option',
		helper: function( e, tr ) {
			var $originals = tr.children(),
				$helper = tr.clone();

			$helper.children().each( function( index ) {
				// Set helper cell sizes to match the original sizes
				$( this ).width( $originals.eq( index ).width() );
			});

			return $helper;
		}
	});

	// !Uncheck Radio button for Options
	$( '.vfb-options-table input[type="radio"]' ).mousedown( function() {
		// Save previous value before .click
		$( this ).attr( 'previousValue', $( this ).prop( 'checked' ) );
	}).click( function() {
		var previousValue = $( this ).attr( 'previousValue' );

		// Change checked value if previous value is true
		if ( previousValue === 'true' ) {
			$( this ).prop( 'checked', false );
		}
	});

	// !Add Options button
	$( document ).on( 'click', '.vfb-button-option-add', function( e ) {
		e.preventDefault();

		var clones = $(this).closest( '.vfb-options-table' ).children( '.vfb-cloned-options' ),
			children = clones.children(),
			num = children.length,
			newNum = num + 1,
			last_child = children[ num - 1 ],
			last_id = $( last_child ).attr( 'id' );

		var l_id = last_id.match( new RegExp( /\w+$/g ) );
		newNum = parseInt( l_id[0] ) + 1;

		// Strip out the last number (i.e. count) from the for to make a new ID
		var new_id = last_id.replace( new RegExp( /(\d+)$/g ), '' );

		// Clone this div and change the ID
		var newElem             = $( '#' + last_id ).clone().attr( 'id', new_id + newNum ),
			labelNameAttr       = $( last_child ).find( 'input[type="text"]' ).attr( 'name' ),
			defaultNameAttr     = $( last_child ).find( 'input[type="checkbox"]' ).attr( 'name' ),
			newLabelNameAttr    = labelNameAttr.replace( new RegExp( /\[(\d+)\]/g ), '[' + newNum + ']' ),
			newDefaultNameAttr  = defaultNameAttr.replace( new RegExp( /\[(\d+)\]/g ), '[' + newNum + ']' );

		// Update the ID, Name, and Value attributes of the cloned option
		newElem.find( 'input[type="text"]' ).attr( 'id', new_id + newNum ).attr( 'name', newLabelNameAttr );
		newElem.find( 'input[type="checkbox"]' ).attr( 'id', new_id + newNum ).attr( 'name', newDefaultNameAttr );
		newElem.find( 'input[type="radio"]' ).attr( 'value', newNum );

		// Insert our cloned option after the last one
		clones.append( newElem );
	});

	// !Delete Options button
	$( document ).on( 'click', '.vfb-delete-option', function( e ) {
		e.preventDefault();

		// Get how many options we already have
		var num = $( this ).closest( '.vfb-options-table' )
						   .children( '.vfb-cloned-options' )
						   .children( '.vfb-option' ).length;

		// If there's only one option left, don't let someone delete it
		if ( num - 1 === 0 ) {
			window.alert( 'You must have at least one option.' );
		}
		else {
			$( this ).closest( 'tr' ).remove();
		}
	});

	// !Bulk Add Options categories buttons
	$( document ).on( 'click', '.vfb-bulk-options', function(e){
		e.preventDefault();

		var options = [],
			id = $( this ).attr( 'id' ),
			field = id.match( new RegExp( /\w+$/g ) );

		$( this ).parent().find( 'li' ).each( function(){
			options.push( $( this ).text() );
		});

		$( '#bulk-choices-text-' + field ).val( options.join( '\n' ) );
	});

	// !Bulk Add Options button
	$( document ).on( 'click', '.vfb-bulk-add-options', function(e){
		e.preventDefault();

		var data = [],
			href = $( this ).attr( 'href' ),
			url  = href.split( '&' );

		for ( var i = 0; i < url.length; i++ ) {
			// break each pair at the first "=" to obtain the argname and value
			var pos     = url[i].indexOf( '=' ),
				argname = url[i].substring( 0, pos ),
				value   = url[i].substring( pos + 1 );

			data[ argname ] = value;
		}

		var field_id = data.field,
			choices  = $( '#bulk-choices-text-' + field_id ).val(),
			options  = choices.split( '\n' ),
			newElem  = [];

		var clones = $( '#vfb-field-item-' + field_id + ' .vfb-cloned-options' ),
			children = clones.children(),
			num = children.length,
			newNum = num + 1,
			last_child = children[ num - 1 ],
			last_id = $( last_child ).attr( 'id' );

		var l_id = last_id.match( new RegExp( /\w+$/g ) );
		newNum = parseInt( l_id[0] ) + 1;

		// Strip out the last number (i.e. count) from the for to make a new ID
		var new_id = last_id.replace( new RegExp( /(\d+)$/g ), '' );

		for ( i = 0; i < options.length; ++i ) {
			newElem[i] = $( '#' + last_id ).clone().attr( 'id', new_id + newNum );

			var labelNameAttr      = $( last_child ).find( 'input[type="text"]' ).attr( 'name' ),
				defaultNameAttr    = $( last_child ).find( 'input[type="checkbox"]' ).attr( 'name' ),
				newLabelNameAttr   = labelNameAttr.replace( new RegExp( /\[(\d+)\]/g ), '[' + newNum + ']' ),
				newDefaultNameAttr = defaultNameAttr.replace( new RegExp( /\[(\d+)\]/g ), '[' + newNum + ']' );

			// Update the ID, Name, and Value attributes of the cloned option
			newElem[i].find( 'input[type="text"]' )
					  .attr( 'id', new_id + newNum )
					  .attr( 'name', newLabelNameAttr )
					  .attr( 'value', options[i] );
			newElem[i].find( 'input[type="checkbox"]' ).attr( 'id', new_id + newNum ).attr( 'name', newDefaultNameAttr );
			newElem[i].find( 'input[type="radio"]' ).attr( 'value', newNum );

			newNum++;
		}

		// Add all cloned options at once
		$( '#' + last_id ).after( newElem );

	    // Remove the thickbox on submit
	    window.tb_remove();
	});

	// !Allow Other
	$( document ).on( 'change', '.vfb-allow-other', function() {
		var input = $( this ).parents( '.vfb-form-group' ).children( '.vfb-allow-other-input' );

		$( input ).toggle( $( this ).prop( 'checked' ) );
	});
});