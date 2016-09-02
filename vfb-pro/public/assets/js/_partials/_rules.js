(function(window, document, $) {
	'use strict';

	var VFBProRuleLogic = function() {
		this.isHidden = false;
		this.init();
	};

	VFBProRuleLogic.prototype = {
		init : function() {
			var self = this;

			if ( window.vfbp_rules ) {
				var obj = $.parseJSON( vfbp_rules.rules ),
					selectors = [];

				this.processRules( obj );

				$( obj ).each( function() {
					$.each( this.conditions, function(){
						selectors.push( '[name^=vfb-field-' + this['field-id'] + ']' );
					});
				});

				$( selectors.join(',') ).change( function(){
					self.processRules( obj );
				});
			}
		},
		processRules: function( obj ) {
			var self = this;

			$( obj ).each( function() {
				var theField  = this['field-id'],
					type      = this.type,			// Show or Hide
					matchType = this['match-type'],	// Any or All
					rules     = this.conditions;

				$.each( rules, function( rule ) {
					if ( self.match( rules, matchType ) ) {
						self.toggleDisplay( theField, rules[ rule ], false, type );
	                }
	                else {
	                    self.toggleDisplay( theField, rules[ rule ], true, type );
	                }
				});
			});
		},
		toggleDisplay: function( theField, rule, inverse, type ) {
			var el = this.getFieldEl( theField );
		    var $el = $( el );
		    var className = 'vfb-rule-hide';

		    if ( $el.length ) {
	            var originalClassName = this.trim( el.className ).replace(/\n/g, '');

	            if ( ( type === 'show' && !inverse ) || ( type === 'hide' && inverse ) ) {
	                $el.removeClass( className );
	            }

	            if ( ( type === 'hide' && !inverse ) || ( type === 'show' && inverse ) ) {
	                $el.removeClass( 'error' );
	                $el.removeClass( 'vfb-hide' );
	                $el.removeClass( 'vfb-rule-hide' );
	                $el.addClass( className );
	            }

	            var newClassName = this.trim( el.className ).replace(/\n/g, '');
	            if ( originalClassName !== newClassName ) {
	                //vfbProcessElAfterShow( el, theField );
	            }
	        }
		},
		match: function( rules, matchType ) {
			var ret;
	        for ( var i = 0; i < rules.length; i++ ) {
		        var fieldType = this.getFieldType( $( '#vfbField' + rules[i]['field-id'] ) );

	            ret = this.compare( rules[i], fieldType );

	            if ( matchType === 'any' && ret === true ) {
	                ret = true;
	                break;
	            }

	            if ( matchType === 'all' && ret === false ) {
	                ret = false;
	                //break;
	            }
	        }

	        return ret;
		},
		compare: function( condition, fieldType ) {
			var filter         = condition.filter,
				fieldValue     = this.cleanForComparison( this.getFieldValue( condition['field-id'], fieldType ) ),
				conditionValue = this.cleanForComparison( condition.value );

			var ret = ( this.isHidden ) ? false : this.filter( filter, conditionValue, fieldValue );

			return ret;
		},
		getFieldType: function( field ) {
		    return field.find( ':input' ).prop( 'type' ).toLowerCase();
		},
		getField: function( fieldType, columnId ) {
			if ( fieldType === 'radio' ) {
	            return this.getRadioField( fieldType, columnId );
	        }
	        else if ( fieldType === 'checkbox' ) {
		        return this.getCheckboxField( fieldType, columnId );
	        }
	        else {
	            return $( '#vfb-field-' + columnId )[0];
	        }
	    },
	    getFieldValue: function( columnId, fieldType ) {
		    var field = this.getField( fieldType, columnId ),
		    	value = '';

			this.isHidden = this.isFieldHidden( field );

			if ( !this.isHidden) {
				value = this.getInputValue( fieldType, field );
	        }

	        return value;
	    },
	    getFieldWrapper: function( field ) {
		    var $div = $( field ).closest( 'div[id^="vfbField"]' );
			return $div.length ? $div[0] : field;
	    },
	    getFieldEl: function( fieldName ) {
			var el = $( '#vfbField' + fieldName );
			el = ( el.length ) ? el : $( '#vfbField' + fieldName );

			return el[0];
	    },
	    getRadioField: function( fieldType, columnId ) {
		    var counter = ( fieldType === 'radio' ) ? 0 : 1,
		    	keepSearching = true,
		    	field = false;

	        while ( keepSearching ) {
	            var $radioField = $( '#vfb-field-' + columnId + '-' + counter );

	            if ( $radioField.length ) {
	                field = $radioField[0];

	                if ( $radioField.prop( 'checked' ) ) {
	                    keepSearching = false;
	                }
	                else {
	                    counter = counter + 1;
	                }
	            }
	            else {
	                keepSearching = false;
	            }
	        }

	        if ( field && fieldType === 'radio' ) {
	            if ( field.value === 'Other' ) {
	                var otherField = $( '#vfb-field-' + columnId + '-other' );

	                if ( otherField.length ) {
	                    field = otherField[0];
	                }
	            }
	        }

	        return field;
	    },
	    getCheckboxField: function( fieldType, columnId ) {
		    var field = false;

			var $checkboxField = $( '#vfbField' + columnId ).find( ':checkbox' );

			if ( $checkboxField.length ) {
				for ( var i = 0; i < $checkboxField.length; i++ ) {
					if ( $checkboxField[i].checked ) {
						field = $checkboxField[i];
					}
				}

				return field;
			}

			return field;
	    },
	    getInputValue: function( fieldType, field ) {
		    var value = '';
	        switch ( fieldType ) {
	            case'checkbox':
	                value = this.getCheckboxInputValue( field );
	                break;

	            case'radio':
	                value = this.getRadioInputValue( field );
	                break;

	            default:
	                value = this.getSimpleInputValue( field );
	                break;
	        }

	        return value;
	    },
	    getCheckboxInputValue: function( field ) {
		    var label = $( '#' + field.id ).parent( 'label' ).text();

			if ( field.checked ) {
				return this.cleanForComparison( label );
			}

			return '';
	    },
	    getRadioInputValue: function( field ) {
		    return ( field.checked ) ? field.value : '';
	    },
	    getSimpleInputValue: function( field ) {
		    return field.value;
	    },
	    isFieldHidden: function( field ) {
			if ( field ) {
				var fieldDIV = this.getFieldWrapper( field );

				if ( $( fieldDIV ).hasClass( 'vfb-rule-hide' ) ) {
					return true;
				}
			}

			return false;
	    },
	    filter: function( filter, conditionValue, fieldValue ) {
		    var value = '';

			switch ( filter ) {
				case 'is' :
					value = this.is( conditionValue, fieldValue );
					break;

				case 'is not' :
					value = this.isNot( conditionValue, fieldValue );
					break;

				case 'contains' :
					value = this.contains( conditionValue, fieldValue );
					break;

				case 'does not contain' :
					value = this.doesNotContain( conditionValue, fieldValue );
					break;

				case 'begins with' :
					value = this.beginsWith( conditionValue, fieldValue );
					break;

				case 'ends with' :
					value = this.endsWith( conditionValue, fieldValue );
					break;
			}

			return value;
	    },
	    is: function( needle, haystack ) {
		    return ( needle === haystack );
	    },
	    isNot: function( needle, haystack ) {
		    return ( needle !== haystack );
	    },
	    contains: function( needle, haystack ) {
		    if ( needle === '' && haystack === '' ) {
	            return true;
	        }

	        if ( needle === '' && haystack !== '' ) {
	            return false;
	        }

	        if ( String( haystack ).indexOf( needle ) === -1 ) {
	            return false;
	        }

	        return true;
	    },
	    doesNotContain: function( needle, haystack ) {
		    if ( needle === '' && haystack === '' ) {
	            return false;
	        }

	        if ( needle === '' && haystack !== '' ) {
	            return true;
	        }

			if ( String( haystack ).indexOf( needle ) === -1 ) {
	            return true;
	        }

	        return false;
	    },
	    beginsWith: function( needle, haystack ) {
		    return ( String( haystack ).indexOf( needle ) === 0 );
	    },
		endsWith: function( needle, haystack ) {
		    var d = haystack.length - needle.length;
	        return ( d >= 0 && String( haystack ).lastIndexOf( needle ) === d );
	    },
		cleanForComparison: function( string ) {
            return this.trim( this.stripTags( String( string ) ).toLowerCase() );
        },
        trim: function( str ) {
	        return str.replace(/^\s+|\s+$/g, '');
        },
        stripTags: function( string ) {
	        return $( '<div></div>' ).html( string ).text();
	    }
	};

	window.VFBProRuleLogic = new VFBProRuleLogic();
}(window, document, jQuery));