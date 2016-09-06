<?php

/**
 * WPB WooCommerce Product slider
 * By WpBean
 */



/**
 * Getting settings
 */

function wpb_ez_get_option( $option, $section, $default = '' ) {
 
    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }
 
    return $default;
}


/**
 * Text Widget Shortcode Support
 */

add_filter('widget_text', 'do_shortcode'); 



/**
 * Trigger the carousel
 */

if( !function_exists('wpb_wps_trigger_the_slider') ):
	function wpb_wps_trigger_the_slider(){
		?>
			<script>
				jQuery(function($){
					/* Latest Product Slider */
				    $("#wpb-wps-latest").owlCarousel({
						autoPlay: <?php echo wpb_ez_get_option( 'wpb_slider_auto', 'wpb_wps_general', 'false' );?>,
						stopOnHover: <?php echo wpb_ez_get_option( 'wpb_stop_hover_i', 'wpb_wps_general', 'true' );?>,
						navigation: <?php echo wpb_ez_get_option( 'wpb_stop_nav', 'wpb_wps_general', 'true' );?>,
						navigationText: ["<i class='wpb-wps-fa-angle-left'></i>","<i class='wpb-wps-fa-angle-right'></i>"],
						slideSpeed: <?php echo wpb_ez_get_option( 'wpb_nav_speed', 'wpb_wps_general', 1000 );?>,
						paginationSpeed: <?php echo wpb_ez_get_option( 'wpb_pagi_speed', 'wpb_wps_general', 1000 );?>,
						pagination:<?php echo wpb_ez_get_option( 'wpb_stop_pagi', 'wpb_wps_general', 'false' );?>,
						paginationNumbers: <?php echo wpb_ez_get_option( 'wpb_num_count', 'wpb_wps_general', 'false' );?>,
				        items : <?php echo wpb_ez_get_option( 'wpb_num_col', 'wpb_wps_general', 4 );?>,
				        itemsDesktop : [1199,3],
				        itemsDesktopSmall : [979,3],
						mouseDrag:<?php echo wpb_ez_get_option( 'wpb_mouse_drag', 'wpb_wps_general', 'true' );?>,
						touchDrag:<?php echo wpb_ez_get_option( 'wpb_touch_drag', 'wpb_wps_general', 'true' );?>,
					});

					/* Feature Product Slider */
					$("#wpb-wps-feature").owlCarousel({
						autoPlay: <?php echo wpb_ez_get_option( 'wpb_slider_auto', 'wpb_wps_general', 'false' );?>,
						stopOnHover: <?php echo wpb_ez_get_option( 'wpb_stop_hover_i', 'wpb_wps_general', 'true' );?>,
						navigation: <?php echo wpb_ez_get_option( 'wpb_stop_nav', 'wpb_wps_general', 'true' );?>,
						navigationText: ["<i class='wpb-wps-fa-angle-left'></i>","<i class='wpb-wps-fa-angle-right'></i>"],
						slideSpeed: <?php echo wpb_ez_get_option( 'wpb_nav_speed', 'wpb_wps_general', 1000 );?>,
						paginationSpeed: <?php echo wpb_ez_get_option( 'wpb_pagi_speed', 'wpb_wps_general', 1000 );?>,
						pagination:<?php echo wpb_ez_get_option( 'wpb_stop_pagi', 'wpb_wps_general', 'false' );?>,
						paginationNumbers: <?php echo wpb_ez_get_option( 'wpb_num_count', 'wpb_wps_general', 'false' );?>,
				        items : <?php echo wpb_ez_get_option( 'wpb_num_col', 'wpb_wps_general', 4 );?>,
				        itemsDesktop : [1199,3],
				        itemsDesktopSmall : [979,3],
						mouseDrag:<?php echo wpb_ez_get_option( 'wpb_mouse_drag', 'wpb_wps_general', 'true' );?>,
						touchDrag:<?php echo wpb_ez_get_option( 'wpb_touch_drag', 'wpb_wps_general', 'true' );?>,
					});
					
					/* Latest Product Slider Sidebar */
				    $("#wpb-wps-latest-sidebar").owlCarousel({
				        autoPlay: <?php echo wpb_ez_get_option( 'wpb_slider_auto_side_i', 'wpb_wps_sidebar', 'true' );?>,
						stopOnHover: <?php echo wpb_ez_get_option( 'wpb_stop_hover_side', 'wpb_wps_sidebar', 'true' );?>,
						navigation: <?php echo wpb_ez_get_option( 'wpb_stop_nav_side', 'wpb_wps_sidebar', 'false' );?>,
						navigationText: ["<i class='wpb-wps-fa-angle-left'></i>","<i class='wpb-wps-fa-angle-right'></i>"],
						slideSpeed: <?php echo wpb_ez_get_option( 'wpb_nav_speed_side', 'wpb_wps_sidebar', 1000 );?>,
						paginationSpeed: <?php echo wpb_ez_get_option( 'wpb_pagi_speed_side', 'wpb_wps_sidebar', 1000 );?>,
						pagination: <?php echo wpb_ez_get_option( 'wpb_stop_pagi_side', 'wpb_wps_sidebar', 'true' );?>,
						paginationNumbers: <?php echo wpb_ez_get_option( 'wpb_num_count_side', 'wpb_wps_sidebar', 'true' );?>,
				        items : 1,
				        itemsDesktop : [1199,1],
				        itemsDesktopSmall : [979,1],
				        itemsTablet: [768,1],
				      	itemsMobile:[479,1],
						mouseDrag: <?php echo wpb_ez_get_option( 'wpb_mouse_drag_side', 'wpb_wps_sidebar', 'true' );?>,
						touchDrag: <?php echo wpb_ez_get_option( 'wpb_touch_drag_side', 'wpb_wps_sidebar', 'true' );?>,
				    });
					
					/* Feature Product Slider Sidebar */
					$("#wpb-wps-latest-sidebar-feature").owlCarousel({
				        autoPlay: <?php echo wpb_ez_get_option( 'wpb_slider_auto_side_i', 'wpb_wps_sidebar', 'true' );?>,
						stopOnHover: <?php echo wpb_ez_get_option( 'wpb_stop_hover_side', 'wpb_wps_sidebar', 'true' );?>,
						navigation: <?php echo wpb_ez_get_option( 'wpb_stop_nav_side', 'wpb_wps_sidebar', 'false' );?>,
						navigationText: ["<i class='wpb-wps-fa-angle-left'></i>","<i class='wpb-wps-fa-angle-right'></i> "],
						slideSpeed: <?php echo wpb_ez_get_option( 'wpb_nav_speed_side', 'wpb_wps_sidebar', 1000 );?>,
						paginationSpeed: <?php echo wpb_ez_get_option( 'wpb_pagi_speed_side', 'wpb_wps_sidebar', 1000 );?>,
						pagination: <?php echo wpb_ez_get_option( 'wpb_stop_pagi_side', 'wpb_wps_sidebar', 'true' );?>,
						paginationNumbers: <?php echo wpb_ez_get_option( 'wpb_num_count_side', 'wpb_wps_sidebar', 'true' );?>,
				        items : 1,
				        itemsDesktop : [1199,1],
				        itemsDesktopSmall : [979,1],
				    	itemsTablet: [768,1],
				      	itemsMobile:[479,1],
						mouseDrag: <?php echo wpb_ez_get_option( 'wpb_mouse_drag_side', 'wpb_wps_sidebar', 'true' );?>,
						touchDrag: <?php echo wpb_ez_get_option( 'wpb_touch_drag_side', 'wpb_wps_sidebar', 'true' );?>,
				    });
				});
			</script>
		<?php
	}
endif;
add_action('wp_footer','wpb_wps_trigger_the_slider');



/**
 * Settings Dynamic Style
 */

if( !function_exists('wpb_wps_adding_dynamic_styles') ):
	function wpb_wps_adding_dynamic_styles() {
		$wpb_wps_btn_bg = wpb_ez_get_option( 'wpb_wps_btn_bg', 'wpb_wps_style', '#1abc9c' );
		$wpb_wps_btn_bg_hover = wpb_ez_get_option( 'wpb_wps_btn_bg_hover', 'wpb_wps_style', '#16a085' );
		$wpb_pagi_btn_bg = wpb_ez_get_option( 'wpb_pagi_btn_bg', 'wpb_wps_style', '#8BCFC2' );
		$wpb_pagi_btn_bg_ac = wpb_ez_get_option( 'wpb_pagi_btn_bg_ac', 'wpb_wps_style', '#16A085' );
		$wpb_nav_btn_bg = wpb_ez_get_option( 'wpb_nav_btn_bg', 'wpb_wps_style', '#CCCCCC' );
		$wpb_nav_btn_bg_ac = wpb_ez_get_option( 'wpb_nav_btn_bg_ac', 'wpb_wps_style', '#999999' );
		$wpb_pro_price_color_i = wpb_ez_get_option( 'wpb_pro_price_color_i', 'wpb_wps_style', '#16A085' );

		$custom_css = ".grid figcaption a, div.grid_no_animation figcaption a.button { background: {$wpb_wps_btn_bg}!important; }";
		$custom_css .= ".grid figcaption a:hover, div.grid_no_animation figcaption a.button:hover { background: {$wpb_wps_btn_bg_hover}!important; }";
		$custom_css .= ".wpb_slider_area .owl-theme .owl-controls .owl-page span { background: {$wpb_pagi_btn_bg}; }";
		$custom_css .= ".wpb_slider_area .owl-theme .owl-controls .owl-page.active span, .wpb_slider_area .owl-theme .owl-controls.clickable .owl-page:hover span { background: {$wpb_pagi_btn_bg_ac}; }";
		$custom_css .= ".wpb_slider_area .owl-theme .owl-controls .owl-buttons > div { background: {$wpb_nav_btn_bg}; }";
		$custom_css .= ".wpb_slider_area .owl-theme .owl-controls.clickable .owl-buttons > div:hover { background: {$wpb_nav_btn_bg_ac}; }";
		$custom_css .= "div.grid_no_animation figcaption .pro_price_area .amount { color: {$wpb_pro_price_color_i}; }";

		wp_add_inline_style( 'wpb_wps_main_style', $custom_css );
	}
endif;
add_action( 'wp_enqueue_scripts', 'wpb_wps_adding_dynamic_styles' );



/**
 * WPBean Socail Info for Plugin settings page
 */

add_action( 'wpb_wps_before_settings','wpb_wps_socail_info' );
if( !function_exists('wpb_wps_socail_info') ){
	function wpb_wps_socail_info(){
		?>
		<div class="wpb_wpbean_socials">
			<h3><?php _e( 'For getting updates of our plugins, features update, WordPress new trend, New web technology etc. Follows Us.', 'wpb-wps' );?></h3>
			<a href="https://twitter.com/wpbean" title="Follow us on Twitter" class="wpb_twitter" target="_blank"><?php _e( 'Follow Us On Twitter', 'wpb-wps' );?></a>
			<a href="https://plus.google.com/u/0/+WpBean/posts" title="Follow us on Google+" class="wpb_googleplus" target="_blank"><?php _e( 'Follow Us On Google Plus', 'wpb-wps' );?></a>
			<a href="https://www.facebook.com/wpbean" title="Follow us on Facebook" class="wpb_facebook" target="_blank"><?php _e( 'Like Us On FaceBook', 'wpb-wps' );?></a>
			<a href="https://www.youtube.com/user/wpbean/videos" title="Follow us on Youtube" class="wpb_youtube" target="_blank"><?php _e( 'Subscribe Us on YouTube', 'wpb-wps' );?></a>
			<a href="http://mangotheme.com/doc/woo_pro_slider/" title="This plugin documentation" class="wpb_doc" target="_blank"><?php _e( 'Documentation', 'wpb-wps' );?></a>
		</div>
		<?php
	}
}



/**
 * PRO version Info
 */

add_action( 'wpb_wps_settings_content', 'wpb_wps_pro_version_info' );
if( !function_exists( 'wpb_wps_pro_version_info' ) ){
	function wpb_wps_pro_version_info(){
		?>
		<h3><?php _e( 'PRO Version Features:','wpb-wps' ); ?></h3>
		<ul>
			<li><?php _e( 'Six different new themes for slider.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product slider form selected tags.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product slider form selected products.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product slider form selected product SKU.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product slider form on sell products.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product slider form selected products attribute.','wpb-wps' ); ?></li>
			<li><?php _e( 'Option for controlling columns in phone, tab, small screen.','wpb-wps' ); ?></li>
			<li><?php _e( 'Remove out of stock products form slider.','wpb-wps' ); ?></li>
			<li><?php _e( 'Advance shortcode generator.','wpb-wps' ); ?></li>
			<li><?php _e( 'Visual Composer addon support.','wpb-wps' ); ?></li>
			<li><?php _e( 'RTL support.','wpb-wps' ); ?></li>
			<li><?php _e( 'Product order & orderby.','wpb-wps' ); ?></li>
		</ul>
		<a class="wpb_get_pro_btn" href="http://bit.ly/1PAAzv6" target="_blank"><?php _e( 'Get The Pro Version','wpb-wps' ); ?></a>
		<?php
	}
}
