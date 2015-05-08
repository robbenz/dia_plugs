<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Predictive Search Hook Backbone
 *
 * Table Of Contents
 *
 * register_admin_screen()
 */
class WC_Predictive_Search_Hook_Backbone
{
	public function __construct() {
		
		// Add script into footer to hanlde the event from widget, popup
		//add_action( 'wp_footer', array( $this, 'register_plugin_scripts' ) );
		add_action( 'wp_head', array( $this, 'register_plugin_scripts' ) );
		add_action( 'wp_head', array( $this, 'include_result_shortcode_script' ) );
	}
	
	public function register_plugin_scripts() {
		global $woocommerce_search_page_id;
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	?>
    <!-- Predictive Search Widget Template -->
    <script type="text/template" id="wc_psearch_itemTpl">
		<div class="ajax_search_content">
			<div class="result_row">
				<a href="{{= url }}">
					<span class="rs_avatar"><img src="{{= image_url }}" /></span>
					<div class="rs_content_popup">
						{{ if ( type == 'p_sku' ) { }}<span class="rs_name">{{= sku }}</span>{{ } }}
						<span class="rs_name">{{= title }}</span>
						{{ if ( price != null && price != '' ) { }}<span class="rs_price"><?php wc_ps_ict_t_e( 'Price', __('Price', 'woops') ); ?>: {{= price }}</span>{{ } }}
						{{ if ( description != null && description != '' ) { }}<span class="rs_description">{{= description }}</span>{{ } }}
					</div>
				</a>
			</div>
		</div>
	</script>
    
    <script type="text/template" id="wc_psearch_footerTpl">
		<div rel="more_result" class="more_result">
			<span><?php wc_ps_ict_t_e( 'More result Text', __('See more search results for', 'woops') ); ?> '{{= title }}' <?php wc_ps_ict_t_e( 'in', __('in', 'woops') ); ?>:</span>
			{{ if ( description != null && description != '' ) { }}{{= description }}{{ } }}
		</div>
	</script>
    
    
    <?php
		wp_register_script( 'ajax-woo-autocomplete-script', WOOPS_JS_URL . '/ajax-autocomplete/jquery.autocomplete.js', array(), '1.2', true );
		wp_register_script( 'backbone.localStorage', WOOPS_JS_URL . '/backbone.localStorage.js', array() , '1.1.9', true );
		wp_register_script( 'wc-predictive-search-backbone', WOOPS_JS_URL . '/predictive-search.backbone.js', array(), '1.0.0', true );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'backbone.localStorage' );
		wp_enqueue_script( 'wc-predictive-search-popup-backbone', WOOPS_JS_URL . '/predictive-search-popup.backbone.min.js', array( 'ajax-woo-autocomplete-script', 'wc-predictive-search-backbone' ), '1.0.2', true );
		
		global $wc_ps_legacy_api;
		$legacy_api_url = $wc_ps_legacy_api->get_legacy_api_url();
		$legacy_api_url = add_query_arg( 'action', 'get_result_popup', $legacy_api_url );
		if ( class_exists( 'SitePress' ) ) {
			$legacy_api_url = add_query_arg( 'lang', ICL_LANGUAGE_CODE, $legacy_api_url );
		}
		$min_characters = get_option( 'woocommerce_search_min_characters', 1 );
		$delay_time = get_option( 'woocommerce_search_delay_time', 600 );
		wp_localize_script( 'wc-predictive-search-popup-backbone', 'wc_ps_vars', apply_filters( 'wc_ps_vars', array( 'minChars' => $min_characters, 'delay' => $delay_time, 'legacy_api_url' => $legacy_api_url, 'search_page_url' => get_permalink( $woocommerce_search_page_id ), 'permalink_structure' => get_option('permalink_structure' ) ) ) );
	}
	
	public function include_result_shortcode_script() {
		global $wp_query;
		global $post;
		global $woocommerce_search_page_id;
		
		if ( $post && $post->ID != $woocommerce_search_page_id ) return '';
		
		$search_keyword = '';
		$search_in = 'product';
		$search_other = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$extra_parameter_product = '';
		$extra_parameter_post = '';
		
		if ( isset( $wp_query->query_vars['keyword'] ) ) $search_keyword = stripslashes( strip_tags( urldecode( $wp_query->query_vars['keyword'] ) ) );
		elseif ( isset( $_REQUEST['rs'] ) && trim( $_REQUEST['rs'] ) != '' ) $search_keyword = stripslashes( strip_tags( $_REQUEST['rs'] ) );
		
		if ( isset( $wp_query->query_vars['pcat'] ) ) $pcat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['pcat'] ) ) );
		elseif ( isset( $_REQUEST['pcat'] ) && trim( $_REQUEST['pcat'] ) != '' ) $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		
		if ( isset( $wp_query->query_vars['ptag'] ) ) $ptag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['ptag'] ) ) );
		elseif ( isset( $_REQUEST['ptag'] ) && trim( $_REQUEST['ptag'] ) != '' ) $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		
		if ( isset( $wp_query->query_vars['scat'] ) ) $cat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['scat'] ) ) );
		elseif ( isset( $_REQUEST['scat'] ) && trim( $_REQUEST['scat'] ) != '' ) $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		
		if ( isset( $wp_query->query_vars['stag'] ) ) $tag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['stag'] ) ) );
		elseif ( isset( $_REQUEST['stag'] ) && trim( $_REQUEST['stag'] ) != '' ) $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		
		if ( isset( $wp_query->query_vars['search-in'] ) ) $search_in = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-in'] ) ) );
		elseif ( isset( $_REQUEST['search_in'] ) && trim( $_REQUEST['search_in'] ) != '' ) $search_in = stripslashes( strip_tags( $_REQUEST['search_in'] ) );
		
		if ( isset( $wp_query->query_vars['search-other'] ) ) $search_other = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-other'] ) ) );
		elseif ( isset( $_REQUEST['search_other'] ) && trim( $_REQUEST['search_other'] ) != '' ) $search_other = stripslashes( strip_tags( $_REQUEST['search_other'] ) );
		
		$permalink_structure = get_option( 'permalink_structure' );
		
		if ( $pcat_slug != '' ) {
			if ( $permalink_structure == '' ) 
				$extra_parameter_product .= '&pcat='.$pcat_slug;
			else
				$extra_parameter_product .= '/pcat/'.$pcat_slug;
		} elseif ( $ptag_slug != '' ) {
			if ( $permalink_structure == '' )
				$extra_parameter_product .= '&ptag='.$ptag_slug;
			else
				$extra_parameter_product .= '/ptag/'.$ptag_slug;
		}
		
		if ( $cat_slug != '' ) {
			if ( $permalink_structure == '' ) 
				$extra_parameter_post .= '&scat='.$cat_slug;
			else
				$extra_parameter_post .= '/scat/'.$cat_slug;
		} elseif ( $tag_slug != '' ) {
			if ( $permalink_structure == '' ) 
				$extra_parameter_post .= '&stag='.$tag_slug;
			else
				$extra_parameter_post .= '/stag/'.$tag_slug;
		}
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	?>
    <!-- Predictive Search Results Template -->
    <script type="text/template" id="wc_psearch_result_itemTpl">
		<span class="rs_rs_avatar"><img src="{{= image_url }}" /></span>
        
		<div class="rs_content">
        
			<a href="{{= url }}"><span class="rs_rs_name">{{ if ( type == 'p_sku' ) {  }}{{= keyword }}, {{ } }}{{= title }}</span></a>
            
			{{ if ( sku != null && sku != '' ) { }}<div class="rs_rs_sku"><?php wc_ps_ict_t_e( 'SKU', __('SKU', 'woops') ); ?>: {{= sku }}</div>{{ } }}
            
			{{ if ( categories.length > 0 ) { }}
            
				<div class="rs_rs_cat posted_in">
					<?php wc_ps_ict_t_e( 'Mft', __('Mft', 'woops') ); ?>: 
					{{ var number_cat = 0; }}
					{{ _.each( categories, function( cat_data ) { number_cat++; }}
						{{ if ( number_cat > 1 ) { }}, {{ } }}<a href="{{= cat_data.url }}">{{= cat_data.name }}</a>
					{{ }); }}
				</div>
                
			{{ } }}
            
			{{ if ( tags.length > 0 ) { }}
            
				<div class="rs_rs_tag tagged_as">
					<?php wc_ps_ict_t_e( 'List Price', __('List Price', 'woops') ); ?>: 
					{{ var number_tag = 0; }}
					{{ _.each( tags, function( tag_data ) { number_tag++; }}
						{{ if ( number_tag > 1 ) { }}, {{ } }}<a href="{{= tag_data.url }}">{{= tag_data.name }}</a>
					{{ }); }}
				</div>
			{{ } }}
            
            
            {{ if ( price != null && price != '' ) { }}<div class="rs_rs_price"><?php wc_ps_ict_t_e( 'Price', __('Price', 'woops') ); ?>: {{= price }}</div>{{ } }}
            
            {{ if ( description != null && description != '' ) { }}<div class="rs_rs_description">{{= description }}</div>{{ } }}
            
            {{ if ( addtocart != null && addtocart != '' ) { }}<div class="rs_rs_addtocart">{{= addtocart }}</div>{{ } }}
            <div id="viewprice-detail-search"><a class="popmake-32596" href="#" style="cursor: pointer;">View Price</a></div>  
		</div>
        
	</script>
    
    <script type="text/template" id="wc_psearch_result_footerTpl">
		<div style="clear:both"></div>
		{{ if ( next_page_number > 1 ) { }}
		<div id="ps_more_check"></div>
		{{ } else if ( total_items == 0 && first_load ) { }}
		<p><?php wc_ps_ict_t_e( 'No Result Text', __('<a href="#" class="popmake-32594 click-here-parts-link">Click Here</a> To Request A Quote For A Part That Hasn\'t Made It To Our Site Yet.', 'woops') ); ?></p>
		{{ } }}
	</script>
    
    
    <?php
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'wc-predictive-search-results-backbone', WOOPS_JS_URL . '/predictive-search-results.backbone.min.js', array( 'wc-predictive-search-backbone' ), '1.0.1', true );
		
		global $wc_ps_legacy_api;
		$legacy_api_url = $wc_ps_legacy_api->get_legacy_api_url();
		$legacy_api_url = add_query_arg( 'action', 'get_results', $legacy_api_url );
		if ( class_exists( 'SitePress' ) ) {
			$legacy_api_url = add_query_arg( 'lang', ICL_LANGUAGE_CODE, $legacy_api_url );
		}
		$legacy_api_url .= '&q=' . $search_keyword;
		if ( $pcat_slug != '' ) $legacy_api_url .= '&pcat=' . $pcat_slug;
		elseif ( $ptag_slug != '' ) $legacy_api_url .= '&ptag=' . $ptag_slug;
		elseif ( $cat_slug != '' ) $legacy_api_url .= '&scat=' . $cat_slug;
		elseif ( $tag_slug != '' ) $legacy_api_url .= '&stag=' . $tag_slug;
		
		$search_page_url = get_permalink( $woocommerce_search_page_id );
		$search_page_parsed = parse_url( $search_page_url );
		if ( $permalink_structure == '' ) {
			$search_page_path = $search_page_parsed['path'];
			$default_navigate = '?page_id='.$woocommerce_search_page_id.'&rs='.urlencode($search_keyword).'&search_in='.$search_in.$extra_parameter_product.$extra_parameter_post.'&search_other='.$search_other;
		} else {
			$host_name = $search_page_parsed['host'];
			$search_page_exploded = explode( $host_name , $search_page_url );
			$search_page_path = $search_page_exploded[1];
			$default_navigate = 'keyword/'.urlencode($search_keyword).'/search-in/'.$search_in.$extra_parameter_product.$extra_parameter_post.'/search-other/'.$search_other;
		}
		
		wp_localize_script( 'wc-predictive-search-results-backbone', 'wc_ps_results_vars', apply_filters( 'wc_ps_results_vars', array( 'default_navigate' => $default_navigate, 'search_in' => $search_in, 'legacy_api_url' => $legacy_api_url, 'search_page_path' => $search_page_path ) ) );
	}
}

global $wc_ps_hook_backbone;
$wc_ps_hook_backbone = new WC_Predictive_Search_Hook_Backbone();
?>
