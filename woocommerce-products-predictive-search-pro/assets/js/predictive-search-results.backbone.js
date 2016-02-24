(function($) {
$(function(){
	var wc_ps_legacy_results_api_url = wc_ps_results_vars.legacy_api_url;
	var wc_ps_legacy_results_permalink_structure = wc_ps_results_vars.permalink_structure;
	
	var wc_psearch_results = { apps:{}, models:{}, collections:{}, views:{} };
	
	_.templateSettings = {
  		evaluate: /\{\{(.+?)\}\}/g,
    	interpolate: /\{\{=(.+?)\}\}/g,
    	escape: /\{\{-(.+?)\}\}/g
	}
	
	wc_psearch_results.models.Item = Backbone.Model.extend({
		defaults: {
			title: 'Empty Product',
			url: null,
			image_url: null,
			sku: null,
			price: null,
			description: null,
			addtocart: null,
			categories: [],
			tags: [],
			type: 'product',
			status: true
		}
	});
	
	wc_psearch_results.collections.Items = Backbone.Collection.extend({
		model: wc_psearch_results.models.Item	
	});
	
	wc_psearch_results.views.Item = Backbone.View.extend({
		tagName: 'div',
		className: 'rs_result_row',
		
		template: _.template( $('#wc_psearch_result_itemTpl').html().replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' ) ),
		
		initialize: function() {
			this.listenTo( this.model, 'destroy', this.remove );	
		},
		
		render: function() {
			//console.log('All Result Search - Rendering item ' + this.model.get('title'));
			this.$el.html( this.template( this.model.toJSON() ) );
			
			return this;
		}
			
	});
	
	wc_psearch_results.views.ResultContainer = Backbone.View.extend({
		
		cached: {},
		
		addCache: function(search_in, ps_lang, page, value) {
			this.cached[search_in+'-'+ps_lang+'-'+page] = value;
		},
		
		flushCached: function() {
			this.cached = {};
		},
		
		events: {
			'click .ps_navigation' : 'initRouter'
		},
		
		initRouter: function( event ) {
			event.preventDefault();
			
			var target = $(event.target);
			var href = target.data('href');
			Backbone.history.navigate( href, {trigger: true});
		},
		
		footerTpl: _.template( $('#wc_psearch_result_footerTpl').html().replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' ) ),
		
		initialize: function() {
			//console.log('All Result Search - init');
			this.total_items = 0;
			this.search_in = wc_ps_results_vars.search_in;
			this.ps_lang = wc_ps_results_vars.ps_lang;
			this.next_page_number = 1;
			this.is_first_load = true;
			this.listenTo( this.collection, 'add', this.addItem );
			
			this.list_items_container = this.$('#ps_list_items_container');
			this.footer = this.$('#ps_footer_container')
			this.ps_more_check = this.$('#ps_more_check');
			this.ps_more_result_popup = this.$('#ps_more_result_popup');
			this.ps_no_more_result_popup = this.$('#ps_no_more_result_popup');
			this.ps_fetching_result_popup = this.$('#ps_fetching_result_popup');
			this.ps_no_result_popup = this.$('#ps_no_result_popup');
			
			this.endless_loading = false;
			$(window).scroll( function() {
				if ( this.next_page_number > 1 ) {
					this.endlessScrollLoad();
				}
			}.bind( this ));
		},
		
		render: function() {
			//console.log('All Result Search - Rendering footer');
			this.footer.html( this.footerTpl({ next_page_number: this.next_page_number, first_load: this.is_first_load, total_items: this.total_items }) );
			
			return this;
		},
		
		addItem: function( itemModel ) {
			//console.log('All Result Search - Added item ' + itemModel.get('title') );
			var itemView = new wc_psearch_results.views.Item({ model: itemModel });
			this.list_items_container.append( itemView.render().el );
			this.list_items_container.append( '<div style="clear:both"></div>' );
		},
		
		clearAll: function() {
			_.invoke( this.collection.where({status: true}), 'destroy');
			return false;	
		},
		
		routeSearchIn: function() {
			// reset vars for new Search In
			this.total_items = 0;
			this.next_page_number = 1;
			this.is_first_load = true;
			this.endless_loading = false;
			this.clearAll();
			this.getItems();
		},
		
		getItems: function() {
			// Check if have cached
			if ( this.cached[this.search_in+'-'+this.ps_lang+'-'+this.next_page_number] ) {
					item_list = this.cached[this.search_in+'-'+this.ps_lang+'-'+this.next_page_number];
					this.addItems(item_list);
			} else {
				if ( this.is_first_load ) { 
					this.ps_fetching_result_popup.fadeIn('fast');
				} else {
					this.ps_more_result_popup.fadeIn('fast');	
				}
				
				$.get( wc_ps_legacy_results_api_url, { search_in: this.search_in, ps_lang: this.ps_lang, psp: this.next_page_number }, function( item_list ) {
					
					// Add to Cached
					this.addCache(this.search_in, this.ps_lang, this.next_page_number, item_list );
					this.addItems(item_list);
					
					if ( this.is_first_load ) {
						this.ps_fetching_result_popup.fadeOut('normal');
					} else {
						this.ps_more_result_popup.fadeOut('normal');
					}
					if ( item_list['total'] == 0 ) {
						if ( this.is_first_load ) {
							this.ps_no_result_popup.fadeIn('normal').fadeOut(1000);
						} else {
							this.ps_no_more_result_popup.fadeIn('normal').fadeOut(1000)
						}
					}
				}.bind( this ));
			}
		},
		
		addItems: function(item_list) {
			this.$('.ps_heading_search_in_name').html(item_list['search_in_name']);
			if ( item_list['total'] > 0 ) {
				this.total_items += item_list['items'].length;
				$.each( item_list['items'], function ( index, data ) {
					this.collection.add( data );
				}.bind( this ));
				if ( item_list['total'] > item_list['items'].length ) {
					this.next_page_number++;
					this.render();
					this.endless_loading = false;
				} else {
					this.next_page_number = 0;
					this.render();
				}
			} else {
				this.next_page_number = 0;
				this.render();
			}
		},
		
		endlessScrollLoad: function() {
			if ( this.endless_loading == false ) {
				var visibleAtTop = $('#ps_more_check').offset().top + this.ps_more_check.height() >= $(window).scrollTop();
				var visibleAtBottom = $('#ps_more_check').offset().top <= $(window).scrollTop() + $(window).height();
				if ( visibleAtTop && visibleAtBottom ) {
					this.endless_loading = true;
					this.is_first_load = false;
					this.getItems();
				}
			}
		}
		
	});
	
	wc_psearch_results.apps.App = Backbone.Router.extend({
		routes: {
			"?:query_parameters": "getResults_QueryString",
			"keyword/:s_k/search-in/:s_in": "getResults",
			"keyword/:s_k/search-in/:s_in/cat-in/:c_in": "getResults",
			"keyword/:s_k/search-in/:s_in/search-other/:s_other": "getResults",
			"keyword/:s_k/search-in/:s_in/cat-in/:c_in/search-other/:s_other": "getResults",
			"keyword/:s_k/search-in/:s_in/search-other/:s_other/cat-in/:c_in": "getResults"
		},
		
		initialize: function() {
			this.collection = new wc_psearch_results.collections.Items;
			this.resultCointainerView = new wc_psearch_results.views.ResultContainer( { collection: this.collection, el : $('#ps_results_container') } );
			if (Backbone.history){
				Backbone.history.start({pushState: true, root: wc_ps_results_vars.search_page_path });
			}
			Backbone.history.navigate( wc_ps_results_vars.default_navigate, {trigger: true});
		},
		
		getResults: function( keyword, search_in ) {
			this.resultCointainerView.search_in = search_in;
			this.resultCointainerView.$('.ps_navigation' ).removeClass('ps_navigation_activated');
			this.resultCointainerView.$('.ps_navigation' + search_in ).addClass('ps_navigation_activated');
			this.resultCointainerView.routeSearchIn();
		},
		
		getResults_QueryString: function( queryString ) {
			if ( wc_ps_legacy_results_permalink_structure == '' ) {
				var params = this.parseQueryString(queryString);
				this.resultCointainerView.search_in = params.search_in;
				this.resultCointainerView.$('.ps_navigation' ).removeClass('ps_navigation_activated');
				this.resultCointainerView.$('.ps_navigation' + params.search_in ).addClass('ps_navigation_activated');
				this.resultCointainerView.routeSearchIn();
			}
		},
		
		parseQueryString: function(queryString) {
			var params = {};
			if(queryString){
				_.each(
					_.map(decodeURI(queryString).split(/&/g),function(el,i){
						var aux = el.split('='), o = {};
						if(aux.length >= 1){
							var val = undefined;
							if(aux.length == 2)
								val = aux[1];
							o[aux[0]] = val;
						}
						return o;
					}),
					function(o){
						_.extend(params,o);
					}
				);
			}
			return params;
		}
	});
				
	wc_ps_app.addInitializer(function(){
		var wc_psearch_results_app = new wc_psearch_results.apps.App;
	});
	
});
})(jQuery);