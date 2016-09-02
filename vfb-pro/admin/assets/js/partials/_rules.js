jQuery(document).ready(function($) {
	var deleteCondition  = vfbpL10n.deleteCondition,
		deleteRule       = vfbpL10n.deleteRule;

	// !Add Condition button
	$( document ).on( 'click', '.vfb-add-condition', function( e ) {
		e.preventDefault();

		var clones     = $( this ).closest( '.vfb-rules-table' ).children( '.vfb-cloned-conditions' ),
			children   = clones.children(),
			num        = children.length,
			newNum     = num + 1,
			lastChild  = children[ num - 1 ],
			lastId    = $( lastChild ).attr( 'id' );

		var lId = lastId.match( new RegExp( /\w+$/g ) );
		newNum = parseInt( lId[0] ) + 1;

		// Strip out the last number (i.e. count) from the for to make a new ID
		var newId = lastId.replace( new RegExp( /(\d+)$/g ), '' );

		// Clone this div and change the ID
		var newElem     = $( '#' + lastId ).clone().attr( 'id', newId + newNum ),
			nameAttr    = newElem.find( 'input[type="text"], select' );

		// Update the name attributes
		var i = 0;
		nameAttr.each( function() {

			this.name = this.name.replace( new RegExp( /(\d+)/g ), function( match ) {
				// if at the second digit
				if ( i === 1 ) {
					return newNum;
				}

				i++;
				return match;
			});

			i = 0;
		});

		// Insert our cloned option after the last one
		clones.append( newElem );
	});

	// !Delete Condition button
	$( document ).on( 'click', '.vfb-delete-condition', function( e ) {
		e.preventDefault();

		// Get how many options we already have
		var num = $( this ).closest( '.vfb-rules-table' )
						   .children( '.vfb-cloned-conditions' )
						   .children( '.vfb-condition' ).length;

		// If there's only one option left, don't let someone delete it
		if ( num - 1 === 0 ) {
			window.alert( deleteCondition );
		}
		else {
			$( this ).closest( 'tr' ).remove();
		}
	});

	// !Add Rule button
	$( document ).on( 'click', '.vfb-rules-actions-add', function( e ) {
		e.preventDefault();

		var rules = $( this ).closest( '.vfb-rules' ),
			//clonesRules = rules.find( '.vfb-cloned-rules' ).children(),
			numRules = rules.length,
			newNum     = numRules + 1,
			lastChild  = rules[ numRules - 1 ],
			lastId     = $( lastChild ).attr( 'id' );

		var lId = lastId.match( new RegExp( /\w+$/g ) );
		newNum = parseInt( lId[0] ) + 1;

		// Strip out the last number (i.e. count) from the for to make a new ID
		var newId = lastId.replace( new RegExp( /(\d+)$/g ), '' );

		// Clone this div and change the ID
		var newElem     = $( '#' + lastId ).clone().attr( 'id', newId + newNum ),
			nameAttr    = newElem.find( 'input[type="text"], select' ),
			conditions  = newElem.find( '.vfb-condition' );

		// Update the name attributes
		var h = 0;
		nameAttr.each( function() {
			this.name = this.name.replace( new RegExp( /(\d+)/g ), function( match ) {
				// if at the second digit
				if ( h === 1 ) {
					return match;
				}

				h++;

				return newNum;
			});

			h = 0;
		});

		// Update the conditions ID attributes
		var i = 0;
		conditions.each( function() {
			this.id = this.id.replace( new RegExp( /(\d+)/g ), function( match ) {
				// if at the second digit
				if ( i === 1 ) {
					return match;
				}

				i++;

				return newNum;
			});

			i = 0;
		});

		// Insert our cloned option after the last one
		newElem.insertAfter( rules );
	});

	// !Delete Rule button
	$( document ).on( 'click', '.vfb-rules-actions-delete', function( e ) {
		e.preventDefault();

		// Get how many options we already have
		var num = $( this ).closest( 'tbody' ).children( '.vfb-rules' ).length;

		// If there's only one option left, don't let someone delete it
		if ( num - 1 === 0 ) {
			window.alert( deleteRule );
		}
		else {
			$( this ).closest( '.vfb-rules' ).remove();
		}
	});
});