/*!
 * jQuery Page Break - A Wizard Plugin
 * --------------------------------------------------------------
 *
 * jQuery Page Break is a plugin that generates a customizable wizard.
 *
 * Licensed under The MIT License
 *
 * @version         1.1.3
 * @since           2010-07-03
 * @modified		from the jQuery Stepy plugin https://github.com/wbotelhos/stepy
 * --------------------------------------------------------------
 *
 */
;(function($) {

	var methods = {
		init: function( settings ) {
			return this.each( function() {
				methods.destroy.call( this );

				this.opt = $.extend( {}, $.fn.vfbSteps.defaults, settings );

				var self = this,
					that = $( this ),
					id   = that.attr( 'id' );

				if ( id === undefined || id === '' ) {
					id = methods._hash.call( self );

					that.attr( 'id', id );
				}

				self.header = methods._header.call( self );
				self.steps  = that.children( 'section' );

				self.steps.each( function( index ) {
					methods._createHead.call( self, this, index );
					methods._createButtons.call( self, this, index );
				});

				self.heads = self.header.children( 'li' );

				self.heads.first().addClass( 'current' );

				if (self.opt.finishButton) {
					methods._bindFinish.call(self);
				}

				// WIP...
				if ( self.opt.titleClick === 'true' ) {
					self.heads.click(function() {
						var array   = self.heads.filter( '.current' ).attr( 'id' ).split( '-' ), // TODO: try keep the number in an attribute.
							current = parseInt( array[array.length - 1], 10 ),
							clicked = $( this ).index();

						if ( clicked > current ) {
							if ( self.opt.next && !methods._execute.call( that, self.opt.next, clicked ) ) {
								return false;
							}
						}
						else if ( clicked < current ) {
							if ( self.opt.back && !methods._execute.call( that, self.opt.back, clicked ) ) {
								return false;
							}
						}

						if ( clicked !== current ) {
							methods.step.call( self, ( clicked ) + 1 );
						}
					});
				}
				else {
					self.heads.children( 'div' ).css( 'cursor', 'default' );
				}

				if ( self.opt.enter ) {
					methods._bindEnter.call( self );
				}

				self.steps.first().find( ':input:visible:enabled' ).first().select().focus();

				that.data({ 'settings': this.opt, 'vfbStep': true });
			});
		},
	    _bindEnter: function() {
			var self = this;

			self.steps.delegate( 'input[type="text"], input[type="password"]', 'keypress', function( evt ) {
				var key = ( evt.keyCode ? evt.keyCode : evt.which );

				if ( key === 13 ) {
					evt.preventDefault();

					var buttons = $( this ).closest( 'section' ).find( '.vfb-wizard.actions' );

					if ( buttons.length ) {
						var next = buttons.children( '.btn-next' );

						if ( next.length ) {
							next.click();
						}
						else if ( self.finish ) {
							self.finish.click();
						}
					}
				}
			});
	    },
	    _bindFinish: function() {
			var self  = this,
			that      = $( this ),
			finish    = that.find( ':submit' );

			self.finish = ( finish.length === 1 ) ? finish : that.children( '.finish' );

			if ( self.finish.length ) {
				var isForm = that.is( 'form' ),
					onSubmit;

				if ( isForm && self.opt.finish ) {
					onSubmit = that.attr( 'onsubmit' );

					that.attr( 'onsubmit', 'return false;' );
				}

				self.finish.on( 'click.vfbSteps', function(evt) {
					if ( self.opt.finish && !methods._execute.call( that, self.opt.finish, self.steps.length - 1 ) ) {
						evt.preventDefault();
					}
					else if ( isForm ) {
						if ( onSubmit ) {
							that.attr( 'onsubmit', onSubmit );
						}
						else {
							that.removeAttr( 'onsubmit' );
						}

						var isSubmit = self.finish.attr( 'type' ) === 'submit';

						if ( !isSubmit && ( !self.opt.validate || methods.validate.call( that, self.steps.length - 1 ) ) ) {
							that.submit();
						}
					}
				});

				self.steps.last().children( '.vfb-wizard.actions' ).append( self.finish );
			}
			else {
				$.error( 'Submit button or element with class "finish" missing!' );
			}
	    },
		_createBackButton: function( nav, index ) {
			var self       = this,
				attributes = { href: '#', 'class': 'btn btn-primary btn-back', html: self.opt.backLabel };

			$( '<a />', attributes ).on( 'click.vfbSteps', function(e) {
				e.preventDefault();

				if ( !self.opt.back || methods._execute.call( self, self.opt.back, index - 1 ) ) {
					methods.step.call( self, ( index - 1 ) + 1 );
				}
			}).appendTo( nav );
		},
		_createButtons: function( step, index ) {
			var nav = methods._navigator.call( this ).appendTo( step );

			if ( index === 0 ) {
				if ( this.steps.length > 1 ) {
					methods._createNextButton.call( this, nav, index );
				}
			}
			else {
				$( step ).hide();

				methods._createBackButton.call( this, nav, index );

				if ( index < this.steps.length - 1 ) {
					methods._createNextButton.call( this, nav, index );
				}
			}
		},
		_createHead: function( step, index ) {
			var newStep = $( step ).attr( 'id', $( this ).attr( 'id' ) + '-step-' + index ).addClass( 'vfb-step' ),
				num     = index + 1,
				head;

			head = methods._head.call( this, index );

			head.append( methods._title.call( this, newStep, num ) );

			this.header.append( head );
		},
	    _createNextButton: function( nav, index ) {
			var self       = this,
				that       = $( this ),
				attributes = { href: '#', 'class': 'btn btn-primary btn-next', html: self.opt.nextLabel };

			$( '<a/>', attributes).on( 'click.vfbSteps', function(e) {
				e.preventDefault();

				if ( !self.opt.next || methods._execute.call( that, self.opt.next, index + 1 ) ) {
					methods.step.call( self, ( index + 1 ) + 1 );
				}
			}).appendTo( nav );
	    },
	    _error: function( message ) {
			$( this ).html( message );

			$.error( message );
	    },
	    _execute: function( callback, index ) {
			var isValid = callback.call( this, index + 1 );

			return isValid || isValid === undefined;
	    },
	    _hash: function() {
			this.hash = 'vfbSteps-' + Math.random().toString().substring(2);

			return this.hash;
	    },
	    _head: function( index ) {
			return $( '<li />', { id: $( this ).attr( 'id' ) + '-head-' + index } );
	    },
	    _header: function() {
			var header = $( '<ul />', { id: $( this ).attr( 'id' ) + '-header', 'role': 'tablist' } );

			if ( this.opt.titleTarget ) {
				header.appendTo( this.opt.titleTarget );
			}
			else {
				header.insertBefore( this );
			}

			header.wrap( '<div class="vfb-wizard vfb-col-12"></div>' );

			return header;
	    },
	    _navigator: function() {
			return $( '<div class="vfb-wizard actions vfb-col-12" />' );
	    },
	    _title: function( step, num ) {
			var text  = step.children( 'h3.vfb-page-title' ).text(),
				title = $( '<div />', { html: text || '--' } );

			// True is checked here instead of false
			if ( this.opt.titleDisplay === 'true' ) {
				title.hide();
			}

			if ( this.opt.numDisplay === 'true' ) {
				title.prepend( '<span class="number">' + num + '.</span> ' );
			}

			return title;
	    },
	    destroy: function() {
			return $( this ).each( function() {
				var that = $(this);

				if ( that.data( 'vfbSteps' ) ) {
					var steps = that.data( 'vfbSteps', false ).children( 'section' ).css( 'display', '' );

					that.children( '.errors' ).remove();
					this.finish.appendTo( steps.last() );
					steps.find( 'vfb-wizard.actions' ).remove();
				}
			});
	    },
	    step: function( index ) {
			var that = $( this ),
				opt  = that[0].opt;

			index--;

			var steps = that.children( 'section' );

			if ( index > steps.length - 1 ) {
				index = steps.length - 1;
			}

			var max = index;

			// Validator
			if ( opt.validate ) {
				var isValid = true;

				for ( var i = 0; i < index; i++ ) {
					isValid = methods.validate.call( this, i );

					if ( opt.block && !isValid ) {
						max = i;
						break;
					}
				}
			}

			// WIP...
			var stepsCount = steps.length;

			if ( opt.transition === 'fade' ) {
				steps.fadeOut( opt.duration, function() {

					if ( --stepsCount > 0 ) {
						return;
					}

					steps.eq( max ).fadeIn( opt.duration );
				});
			}
			else if ( opt.transition === 'slide' ) {
				steps.slideUp( opt.duration, function() {
					if ( --stepsCount > 0 ) {
						return;
					}

					steps.eq( max ).slideDown( opt.duration );
				});
			}
			else {
				steps.hide( opt.duration ).eq( max ).show( opt.duration );
			}

			that[0].heads.removeClass( 'current' ).eq( max ).addClass( 'current' );

			if ( that.is( 'form' ) ) {
				var $fields;

				if ( max === index ) {
					$fields = steps.eq( max ).find( ':input:enabled:visible' );
				}
				else {
					$fields = steps.eq( max ).find( '.error' ).select().focus();
				}

				$fields.first().select().focus();
			}

			if ( opt.select ) {
				opt.select.call( this, max + 1 );
			}

			return that;
	    },
	    validate: function( index ) {
			// If Parsley library isn't loaded, exit
			if ( !$.fn.parsley ) {
				return true;
			}

			var that = $( this );

			if ( !that.is( 'form' ) ) {
				return true;
			}

			// Validate form
			that.parsley().validate();

			var step    = that.children( 'section' ).eq( index ),
				isValid = true,
				fields  = step.find( ':input' ).not( ':input[type=button], :input[type=submit], :input[type=reset], :input[type=hidden]' ).get();

			$( fields ).each( function() {
				var fieldID      = '#' + $( this ).attr( 'id' ),
					fieldIsValid = $( fieldID ).parsley().isValid();

				if ( fieldIsValid.length === 0 ) {
					fieldIsValid = true;
				}

				if ( $( this ).is( ':hidden' ) ) {
					fieldIsValid = true;
				}

				//isValid &= fieldIsValid;
				isValid = fieldIsValid ? 1 : 0;

				if ( !isValid ) {
					return false;
				}
			});

			return isValid;
		}
	};

	$.fn.vfbSteps = function( method ) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		}
		else if ( typeof method === 'object' || !method ) {
			return methods.init.apply( this, arguments );
		}
		else {
			$.error( 'Method ' + method + ' does not exist!' );
		}
	};

	$.fn.vfbSteps.defaults = {
		back         : undefined,
		backLabel    : 'Previous',
		block        : false, // WIP...
		duration     : undefined,
		enter        : true,
		errorImage   : false, // WIP...
		finish       : undefined,
		finishButton : true,
		ignore       : '', // WIP...
		titleDisplay : false, // False is the default since this option hides the title
		next         : undefined,
		nextLabel    : 'Next',
		select       : undefined,
		titleClick   : true,
		titleTarget  : undefined,
		numDisplay   : true,
		transition   : undefined,
		validate     : false
	};

})(jQuery);