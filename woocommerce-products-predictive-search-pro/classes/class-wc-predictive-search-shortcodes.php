<?php
/**
 * WC Predictive Search Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * parse_shortcode_search_widget()
 * add_search_widget_icon()
 * add_search_widget_mce_popup()
 * parse_shortcode_search_result()
 * display_search()
 */
class WC_Predictive_Search_Shortcodes 
{
	public static function parse_shortcode_search_widget($attributes) {
		$items_search_default = WC_Predictive_Search_Widgets::get_items_search();
		$items_array = array();
		
		extract(array_merge(array(
			 'show_price' => 1,
			 'character_max' => 100,
			 'style' => '',
			 'wrap'	=> 'false',
			 'search_box_text' => '',
        ), $attributes));
		
		foreach ($items_search_default as $key => $data) {
			if (isset(${$key.'_items'}) )
				$items_array[$key] = ${$key.'_items'};
			else 
				$items_array[$key] = $data['number'];
		}
		
		$widget_id = rand(100, 10000);
		
		$break_div = '<div style="clear:both;"></div>';
		if ($wrap == 'true') $break_div = '';
		
		if ( trim($search_box_text) == '' ) {
			if ( class_exists('SitePress') ) {
				$current_lang = ICL_LANGUAGE_CODE;
				$search_box_texts = get_option('woocommerce_search_box_text', array() );
				if ( is_array($search_box_texts) && isset($search_box_texts[$current_lang]) ) $search_box_text = esc_attr( stripslashes( trim( $search_box_texts[$current_lang] ) ) );
				else $search_box_text = '';
			} else {
				$search_box_text = get_option('woocommerce_search_box_text', '' );
				if ( is_array($search_box_text) ) $search_box_text = '';
			}
		}
		
		return WC_Predictive_Search_Widgets::woops_results_search_form($widget_id, $items_array, $character_max, $style, 1, $search_box_text, $show_price).$break_div;
	}
	
	public static function add_search_widget_icon($context){
		$image_btn = WOOPS_IMAGES_URL . "/ps_icon.png";
		$out = '<a href="#TB_inline?width=670&height=500&modal=false&inlineId=woo_search_widget_shortcode" class="thickbox" title="'.__('Insert WooCommerce Predictive Search Shortcode', 'woops').'"><img class="search_widget_shortcode_icon" src="'.$image_btn.'" alt="'.__('Insert WooCommerce Predictive Search Shortcode', 'woops').'" /></a>';
		return $context . $out;
	}
	
	//Action target that displays the popup to insert a form to a post/page
	public static function add_search_widget_mce_popup(){
		$items_search_default = WC_Predictive_Search_Widgets::get_items_search();
		?>
		<script type="text/javascript">
			function woo_search_widget_add_shortcode(){
				var number_items = '';
				<?php foreach ($items_search_default as $key => $data) {?>
				var woo_search_<?php echo $key ?>_items = '<?php echo $key ?>_items="' + jQuery("#woo_search_<?php echo $key ?>_items").val() + '" ';
				number_items += woo_search_<?php echo $key ?>_items;
				<?php } ?>
				var woo_search_show_price = 0;
				if ( jQuery('#woo_search_show_price').is(":checked") ) {
					var woo_search_show_price = 1;
				}
				var woo_search_text_lenght = jQuery("#woo_search_text_lenght").val();
				var woo_search_align = jQuery("#woo_search_align").val();
				var woo_search_width = jQuery("#woo_search_width").val();
				var woo_search_padding_top = jQuery("#woo_search_padding_top").val();
				var woo_search_padding_bottom = jQuery("#woo_search_padding_bottom").val();
				var woo_search_padding_left = jQuery("#woo_search_padding_left").val();
				var woo_search_padding_right = jQuery("#woo_search_padding_right").val();
				var woo_search_box_text = jQuery("#woo_search_box_text").val();
				var woo_search_style = '';
				var wrap = '';
				if (woo_search_align == 'center') woo_search_style += 'float:none;margin:auto;display:table;';
				else if (woo_search_align == 'left-wrap') woo_search_style += 'float:left;';
				else if (woo_search_align == 'right-wrap') woo_search_style += 'float:right;';
				else woo_search_style += 'float:'+woo_search_align+';';
				
				if(woo_search_align == 'left-wrap' || woo_search_align == 'right-wrap') wrap = 'wrap="true"';
				
				if (parseInt(woo_search_width) > 0) woo_search_style += 'width:'+parseInt(woo_search_width)+'px;';
				if (parseInt(woo_search_padding_top) >= 0) woo_search_style += 'padding-top:'+parseInt(woo_search_padding_top)+'px;';
				if (parseInt(woo_search_padding_bottom) >= 0) woo_search_style += 'padding-bottom:'+parseInt(woo_search_padding_bottom)+'px;';
				if (parseInt(woo_search_padding_left) >= 0) woo_search_style += 'padding-left:'+parseInt(woo_search_padding_left)+'px;';
				if (parseInt(woo_search_padding_right) >= 0) woo_search_style += 'padding-right:'+parseInt(woo_search_padding_right)+'px;';
				var win = window.dialogArguments || opener || parent || top;
				win.send_to_editor('[woocommerce_search_widget ' + number_items + ' show_price="'+woo_search_show_price+'" character_max="'+woo_search_text_lenght+'" style="'+woo_search_style+'" '+wrap+' search_box_text="'+woo_search_box_text+'" ]');
			}
			
			
		</script>
		<style type="text/css">
		#TB_ajaxContent{width:auto !important;}
		#TB_ajaxContent p {
			padding:2px 0;	
		}
		.field_content {
			padding:0 40px;
		}
		.field_content label{
			width:150px;
			float:left;
			text-align:left;
		}
		.a3-view-docs-button {
			background-color: #FFFFE0 !important;
			border: 1px solid #E6DB55 !important;
			border-radius: 3px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			color: #21759B !important;
			outline: 0 none;
			text-shadow:none !important;
			font-weight:normal !important;
			font-family: sans-serif;
			font-size: 12px;
			text-decoration: none;
			padding: 3px 8px;
			position: relative;
			margin-left: 4px;
			white-space:nowrap;
		}
		.a3-view-docs-button:hover {
			color: #D54E21 !important;
		}
		@media screen and ( max-width: 782px ) {
			#woo_search_box_text {
				width:100% !important;	
			}
		}
		@media screen and ( max-width: 480px ) {
			.a3_woocommerce_search_exclude_item {
				float:none !important;
				display:block;
			}
		}
		</style>
		<div id="woo_search_widget_shortcode" style="display:none;">
		  <div>
			<h3><?php _e('Customize the Predictive Search Shortcode', 'woops'); ?> <a class="add-new-h2 a3-view-docs-button" target="_blank" href="<?php echo WOO_PREDICTIVE_SEARCH_DOCS_URI; ?>#section-16" ><?php _e('View Docs', 'woops'); ?></a></h3>
			<div style="clear:both"></div>
			<div class="field_content">
                <?php foreach ($items_search_default as $key => $data) { ?>
                <p><label for="woo_search_<?php echo $key ?>_items"><?php echo $data['name']; ?>:</label> <input style="width:100px;" size="10" id="woo_search_<?php echo $key ?>_items" name="woo_search_<?php echo $key ?>_items" type="text" value="<?php echo $data['number'] ?>" /> <span class="description"><?php _e('Number of', 'woops'); echo ' '.$data['name'].' '; _e('results to show in dropdown', 'woops'); ?></span></p> 
                <?php } ?>
                <p><label for="woo_search_show_price"><?php _e('Price', 'woops'); ?>:</label> <input type="checkbox" checked="checked" id="woo_search_show_price" name="woo_search_show_price" value="1" /> <span class="description"><?php _e('Show Product prices', 'woops'); ?></span></p>
            	<p><label for="woo_search_text_lenght"><?php _e('Characters', 'woops'); ?>:</label> <input style="width:100px;" size="10" id="woo_search_text_lenght" name="woo_search_text_lenght" type="text" value="100" /> <span class="description"><?php _e('Number of product description characters', 'woops'); ?></span></p>
                <p><label for="woo_search_align"><?php _e('Alignment', 'woops'); ?>:</label> <select style="width:100px" id="woo_search_align" name="woo_search_align"><option value="none" selected="selected"><?php _e('None', 'woops'); ?></option><option value="left-wrap"><?php _e('Left - wrap', 'woops'); ?></option><option value="left"><?php _e('Left - no wrap', 'woops'); ?></option><option value="center"><?php _e('Center', 'woops'); ?></option><option value="right-wrap"><?php _e('Right - wrap', 'woops'); ?></option><option value="right"><?php _e('Right - no wrap', 'woops'); ?></option></select> <span class="description"><?php _e('Horizontal aliginment of search box', 'woops'); ?></span></p>
                <p><label for="woo_search_width"><?php _e('Search box width', 'woops'); ?>:</label> <input style="width:100px;" size="10" id="woo_search_width" name="woo_search_width" type="text" value="200" />px</p>
                <p><label for="woo_search_box_text"><?php _e('Search box text message', 'woops'); ?>:</label> <input style="width:300px;" size="10" id="woo_search_box_text" name="woo_search_box_text" type="text" value="" /></p>
                <p><label for="woo_search_padding"><strong><?php _e('Padding', 'woops'); ?></strong>:</label><br /> 
				<label for="woo_search_padding_top" style="width:auto; float:none"><?php _e('Above', 'woops'); ?>:</label><input style="width:50px;" size="10" id="woo_search_padding_top" name="woo_search_padding_top" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_bottom" style="width:auto; float:none"><?php _e('Below', 'woops'); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_bottom" name="woo_search_padding_bottom" type="text" value="10" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_left" style="width:auto; float:none"><?php _e('Left', 'woops'); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_left" name="woo_search_padding_left" type="text" value="0" />px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="woo_search_padding_right" style="width:auto; float:none"><?php _e('Right', 'woops'); ?>:</label> <input style="width:50px;" size="10" id="woo_search_padding_right" name="woo_search_padding_right" type="text" value="0" />px
                </p>
			</div>
            <p><input type="button" class="button-primary" value="<?php _e('Insert Shortcode', 'woops'); ?>" onclick="woo_search_widget_add_shortcode();"/>&nbsp;&nbsp;&nbsp;
            <a class="button" style="" href="#" onclick="tb_remove(); return false;"><?php _e('Cancel', 'woops'); ?></a>
			</p>
            <div style="clear:both;"></div>
		  </div>
          <div style="clear:both;"></div>
		</div>
<?php
	}
	
	public static function parse_shortcode_search_result($attributes) {
		$search_results = '';
		global $woocommerce_search_page_id;
		global $wp_query;
		
		$woocommerce_search_enable_google_analytic = get_option( 'woocommerce_search_enable_google_analytic', 'no' ); 
		$woocommerce_search_google_analytic_id = trim( get_option( 'woocommerce_search_google_analytic_id', '' ) ); 
		$woocommerce_search_google_analytic_query_parameter = trim( get_option( 'woocommerce_search_google_analytic_query_parameter', 'ps' ) ); 
		
		$search_keyword = '';
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = stripslashes( strip_tags( urldecode( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['rs'] ) );
		
		$search_result_google_tracking = '';
		if ( $woocommerce_search_enable_google_analytic == 'yes' && $woocommerce_search_google_analytic_id != '' ) {
			ob_start();
	?>
		<!-- Google Analytics -->
		<script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '<?php echo $woocommerce_search_google_analytic_id; ?>', 'auto');
        ga('send', 'pageview', {
		  'page': '/<?php echo add_query_arg( array( $woocommerce_search_google_analytic_query_parameter => $search_keyword ) , get_page_uri( $woocommerce_search_page_id ) ); ?>',
		  'title': '<?php echo get_the_title( $woocommerce_search_page_id ); ?>'
		});
        </script>
        <!-- End Google Analytics -->
    <?php
			$search_result_google_tracking = ob_get_clean();
		}
		
		$search_results .= $search_result_google_tracking;
		$search_results .= WC_Predictive_Search_Shortcodes::display_search();
    	return $search_results;	
    }
						
	public static function display_search() {
		global $wp_query;
		global $wpdb;
		global $woocommerce_search_page_id;
	
		$items_search_default = WC_Predictive_Search_Widgets::get_items_search();
		$search_keyword = '';
		$search_in = 'product';
		$search_other = '';
		$pcat_slug = '';
		$ptag_slug = '';
		$cat_slug = '';
		$tag_slug = '';
		$extra_parameter_product = '';
		$extra_parameter_post = '';
		
		if (isset($wp_query->query_vars['keyword'])) $search_keyword = stripslashes( strip_tags( urldecode( $wp_query->query_vars['keyword'] ) ) );
		else if (isset($_REQUEST['rs']) && trim($_REQUEST['rs']) != '') $search_keyword = stripslashes( strip_tags( $_REQUEST['rs'] ) );
		
		if (isset($wp_query->query_vars['pcat'])) $pcat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['pcat'] ) ) );
		else if (isset($_REQUEST['pcat']) && trim($_REQUEST['pcat']) != '') $pcat_slug = stripslashes( strip_tags( $_REQUEST['pcat'] ) );
		
		if (isset($wp_query->query_vars['ptag'])) $ptag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['ptag'] ) ) );
		else if (isset($_REQUEST['ptag']) && trim($_REQUEST['ptag']) != '') $ptag_slug = stripslashes( strip_tags( $_REQUEST['ptag'] ) );
		
		if (isset($wp_query->query_vars['scat'])) $cat_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['scat'] ) ) );
		else if (isset($_REQUEST['scat']) && trim($_REQUEST['scat']) != '') $cat_slug = stripslashes( strip_tags( $_REQUEST['scat'] ) );
		
		if (isset($wp_query->query_vars['stag'])) $tag_slug = stripslashes( strip_tags( urldecode( $wp_query->query_vars['stag'] ) ) );
		else if (isset($_REQUEST['stag']) && trim($_REQUEST['stag']) != '') $tag_slug = stripslashes( strip_tags( $_REQUEST['stag'] ) );
		
		if (isset($wp_query->query_vars['search-in'])) $search_in = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-in'] ) ) );
		else if (isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = stripslashes( strip_tags( $_REQUEST['search_in'] ) );
		
		if (isset($wp_query->query_vars['search-other'])) $search_other = stripslashes( strip_tags( urldecode( $wp_query->query_vars['search-other'] ) ) );
		else if (isset($_REQUEST['search_other']) && trim($_REQUEST['search_other']) != '') $search_other = stripslashes( strip_tags( $_REQUEST['search_other'] ) );
		
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
				
		if ( $search_keyword != '' && $search_in != '' ) {
			
			$search_other_list = explode(",", $search_other);
			if ( ! is_array( $search_other_list ) ) {
				$search_other_list = array();
			}
			
			ob_start();
		?>
		<div id="ps_results_container" class="woocommerce">
			<style type="text/css">
				.rs_result_heading{margin:15px 0;}
				.ajax-wait{display: none; position: absolute; width: 100%; height: 100%; top: 0px; left: 0px; background:url("<?php echo WOOPS_IMAGES_URL; ?>/ajax-loader.gif") no-repeat center center #EDEFF4; opacity: 1;text-align:center;}
				.ajax-wait img{margin-top:14px;}
				.p_data,.r_data,.q_data{display:none;}
				.rs_date{color:#777;font-size:small;}
				.rs_result_row {background-color: #00426a;}
                .rs_result_row:hover{opacity:1;}
                                			
                .rs_rs_description{text-transform: uppercase;}  
                .rs_rs_avatar{width:64px;margin-right:10px;overflow: hidden;float:left; text-align:center;}
				.rs_rs_avatar img{width:100%;height:auto; padding:0 !important; margin:0 !important; border: none !important;}
				.rs_rs_name{margin-left:0px; pointer-events: none;}
				.rs_content{margin-left:20px;pointer-events: none;}
				.ps_more_result{display:none;width:240px;text-align:center;position:fixed;bottom:50%;left:50%;margin-left:-125px;background-color: black;opacity: .75;color: white;padding: 10px;border-radius:10px;-webkit-border-radius: 10px;-moz-border-radius: 10px}
                
				.rs_rs_price .oldprice{text-decoration:line-through; font-size:80%;}
				.rs_result_others { margin-bottom:20px; }
				.rs_result_others_heading {font-weight:bold;} 
				.ps_navigation_activated { font-weight:bold;}
                
                .rs_rs_addtocart {pointer-events:auto;}
                
                .rs_rs_avatar{display:none;}
                
			</style>
		
			<p class="rs_result_heading"><?php wc_ps_ict_t_e( 'Viewing all', __('Viewing all', 'woops') ); ?> <strong><span class="ps_heading_search_in_name"><?php echo $items_search_default[$search_in]['name']; ?></span></strong> <?php wc_ps_ict_t_e( 'Search Result Text', __('search results for your search query', 'woops') ); ?> <strong><?php echo $search_keyword; ?></strong></p>
		<?php	
			if ( count( $search_other_list ) > 0 ) {
				if ( $permalink_structure == '')
					$other_link_search = get_permalink( $woocommerce_search_page_id ).'&rs='. urlencode($search_keyword);
				else
					$other_link_search = rtrim( get_permalink( $woocommerce_search_page_id ), '/' ).'/keyword/'. urlencode($search_keyword);
				$line_vertical = '';
		?>
			<div class="rs_result_others"><div class="rs_result_others_heading"><?php wc_ps_ict_t_e( 'Sort Text', __('Sort Search Results by', 'woops') ); ?></div>
		<?php
				foreach ( $search_other_list as $search_other_item ) {
					if ( $permalink_structure == '' ) {
		?>
        		<?php echo $line_vertical; ?><span class="rs_result_other_item"><a class="ps_navigation ps_navigation<?php echo $search_other_item; ?>" href="<?php echo $other_link_search; ?>&search_in=<?php echo $search_other_item; ?><?php echo $extra_parameter_product.$extra_parameter_post; ?>&search_other=<?php echo $search_other; ?>" data-href="?page_id=<?php echo $woocommerce_search_page_id; ?>&rs=<?php echo urlencode($search_keyword); ?>&search_in=<?php echo $search_other_item; ?><?php echo $extra_parameter_product.$extra_parameter_post; ?>&search_other=<?php echo $search_other; ?>" alt=""><?php echo $items_search_default[$search_other_item]['name']; ?></a></span>
        <?php
					} else {
		?>
				<?php echo $line_vertical; ?><span class="rs_result_other_item"><a class="ps_navigation ps_navigation<?php echo $search_other_item; ?>" href="<?php echo $other_link_search; ?>/search-in/<?php echo $search_other_item; ?><?php echo $extra_parameter_product.$extra_parameter_post; ?>/search-other/<?php echo $search_other; ?>" data-href="keyword/<?php echo urlencode($search_keyword); ?>/search-in/<?php echo $search_other_item; ?><?php echo $extra_parameter_product.$extra_parameter_post; ?>/search-other/<?php echo $search_other; ?>" alt=""><?php echo $items_search_default[$search_other_item]['name']; ?></a></span>
		<?php
					}
					$line_vertical = ' | ';
				}
		?>
			</div>
		<?php
			}
		?>
        	<div id="ps_list_items_container">
            </div>
            <div style="clear:both"></div>
            <div class="ps_more_result" id="ps_more_result_popup">
                <img src="<?php echo WOOPS_IMAGES_URL; ?>/more-results-loader.gif" />
                <div><em><?php wc_ps_ict_t_e( 'Loading Text', __('Loading More Results...', 'woops') ); ?></em></div>
            </div>
            <div class="ps_more_result" id="ps_no_more_result_popup"><em><?php wc_ps_ict_t_e( 'No More Result Text', __('No More Results to Show', 'woops') ); ?></em></div>
            <div class="ps_more_result" id="ps_fetching_result_popup">
                <img src="<?php echo WOOPS_IMAGES_URL; ?>/more-results-loader.gif" />
                <div><em><?php wc_ps_ict_t_e( 'Fetching Text', __('Fetching search results...', 'woops') ); ?></em></div>
            </div>
            <div class="ps_more_result" id="ps_no_result_popup"><em><?php wc_ps_ict_t_e( 'No Fetching Result Text', __('No Results to Show', 'woops') ); ?></em></div>
            <div id="ps_footer_container">
                <p style="float:left;margin:0;">Searching For Your Part!</p>
            </div>
		</div>
        <script type="text/javascript">
		(function($) {
		$(function(){
			wc_ps_app.start();
		});
		})(jQuery);
		</script>
		<?php
			
			$output = ob_get_clean();
			
			return $output;
        }
	}	
}
?>