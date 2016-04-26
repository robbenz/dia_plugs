<?php

/**
 * WPB WooCommerce Product slider
 * By WpBean
 */

/**
 * Ading the shortcodes
 */

add_shortcode('wpb-latest-product', 'wpb_wps_shortcode');
add_shortcode('wpb-feature-product', 'wpb_wps_feature_shortcode');
add_shortcode('wpb-sidebar-latest-product', 'wpb_wps_sideber_shortcode');
add_shortcode('wpb-sidebar-feature-product', 'wpb_wps_sideber_feature_shortcode');


/**
 * Latest product Slider
 */

if( !function_exists( 'wpb_wps_shortcode' ) ):
	function wpb_wps_shortcode($atts){
		extract(shortcode_atts(array(
			'title' => __( 'Latest Products','wpb-wps' ),
		), $atts));

		$return_string = '<div class="wpb_slider_area wpb_fix_cart">';
		$return_string .= '<h3 class="wpb_area_title">'.$title.'</h3>';
	    $return_string .= '<div id="wpb-wps-latest" class="wpb-wps-wrapper owl-carousel '.wpb_ez_get_option( "wpb_slider_type_gen_lat", "wpb_wps_style", "grid cs-style-3" ).'">';
		
	    $args = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> wpb_ez_get_option( 'wpb_num_pro', 'wpb_wps_general', 12 )
		);
						
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				global $post, $product;
		        $return_string .= '<div class="item">';
				$return_string .= '<figure>';			
				$return_string .= '<a href="'.get_permalink().'" class="wpb_pro_img_url">';
				if (has_post_thumbnail( $loop->post->ID )){
					$return_string .= get_the_post_thumbnail($loop->post->ID, 'shop_catalog', array('class' => "wpb_pro_img"));
				}else{
				    $return_string .= '<img id="place_holder_thm" src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
				}
				$return_string .='</a>';
				$return_string .='<figcaption>';
				$return_string .='<h3 class="pro_title">';
				if (strlen($post->post_title) > 20) {
					$return_string .= substr(the_title($before = '', $after = '', FALSE), 0, wpb_ez_get_option( 'wpb_title_mx_ch', 'wpb_wps_style', 10 )) . '...';
				}else{
					$return_string .= get_the_title();
				}
				$return_string .='</h3>';
				if( $price_html = $product->get_price_html() ){
					$return_string .='<div class="pro_price_area">'. $price_html .'</div>';
				}
				$return_string .= '<div class="wpb_wps_cart_button"><a href="'.esc_url( $product->add_to_cart_url() ).'" rel="nofollow" data-product_id="'.esc_attr( $product->id ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" class="button '. ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '') .' product_type_'.esc_attr( $product->product_type ).'">'.esc_html( $product->add_to_cart_text()).'</a></div>';
				$return_string .='</figcaption>';
				$return_string .= '</figure>';
				$return_string .= '</div>';
	    	endwhile;
		} else {
			echo __( 'No products found','wpb-wps' );
		}
		wp_reset_postdata();
	    $return_string .= '</div>';
	    $return_string .= '</div>';
	    wp_reset_query();
	    return $return_string;   
	}
endif;


/**
 * Feature products Slider
 */

if( !function_exists('wpb_wps_feature_shortcode') ):
	function wpb_wps_feature_shortcode($atts){
		extract(shortcode_atts(array(
			'title' => __( 'Feature Products','wpb-wps' )
		), $atts));

		$return_string = '<div class="wpb_slider_area wpb_fix_cart">';
		$return_string .= '<h3 class="wpb_area_title">'.$title.'</h3>';
	    $return_string .= '<div id="wpb-wps-feature" class="wpb-wps-wrapper owl-carousel '.wpb_ez_get_option( 'wpb_slider_type_gen_fea', 'wpb_wps_style', 'grid cs-style-3' ).'">';
		
	    $args = array(
			'post_type' 		=> 'product',
			'meta_key' 			=> '_featured',
			'meta_value' 		=> 'yes', 
			'posts_per_page' 	=> wpb_ez_get_option( 'wpb_num_pro', 'wpb_wps_general', 12 )
		);
						
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				global $post, $product;
		        $return_string .= '<div class="item">';
				$return_string .= '<figure>';			
				$return_string .= '<a href="'.get_permalink().'" class="wpb_pro_img_url">';
				if (has_post_thumbnail( $loop->post->ID )){
					$return_string .= get_the_post_thumbnail($loop->post->ID, 'shop_catalog', array('class' => "wpb_pro_img"));
				}else{
				    $return_string .= '<img id="place_holder_thm" src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
				}
				$return_string .='</a>';
				$return_string .='<figcaption>';
				$return_string .='<h3 class="pro_title">';
				if (strlen($post->post_title) > 20) {
					$return_string .= substr(the_title($before = '', $after = '', FALSE), 0, wpb_ez_get_option( 'wpb_title_mx_ch', 'wpb_wps_style', 10 )) . '...';
				}else{
					$return_string .= get_the_title();
				}
				$return_string .='</h3>';
				if( $price_html = $product->get_price_html() ){
					$return_string .='<div class="pro_price_area">'. $price_html .'</div>';
				}
				$return_string .= '<div class="wpb_wps_cart_button"><a href="'.esc_url( $product->add_to_cart_url() ).'" rel="nofollow" data-product_id="'.esc_attr( $product->id ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" class="button '. ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '') .' product_type_'.esc_attr( $product->product_type ).'">'.esc_html( $product->add_to_cart_text()).'</a></div>';
				$return_string .='</figcaption>';
				$return_string .= '</figure>';
				$return_string .= '</div>';
	    	endwhile;
		} else {
			echo __( 'No products found','wpb-wps' );
		}
		wp_reset_postdata();
	    $return_string .= '</div>';
	    $return_string .= '</div>';
	    wp_reset_query();
	    return $return_string;
	}
endif;


/**
 * Sidebar latest product Slider
 */

if( !function_exists('wpb_wps_sideber_shortcode') ):
	function wpb_wps_sideber_shortcode($atts){
		extract(shortcode_atts(array(
			'posts' => 5,
		), $atts));

		$return_string = '<div class="wpb_slider_area wpb_sidebar_slider wpb_fix_cart">';
	    $return_string .= '<div id="wpb-wps-latest-sidebar" class="wpb-wps-wrapper owl-carousel '.wpb_ez_get_option( 'wpb_slider_type_sid_lat', 'wpb_wps_style', 'grid cs-style-3' ).'">';
		
	    $args = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> $posts
		);
						
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				global $product, $post;
		        $return_string .= '<div class="item">';
				$return_string .= '<figure>';
				$return_string .= '<a href="'.get_permalink().'" class="wpb_pro_img_url">';
				if ( has_post_thumbnail( $loop->post->ID ) ){
					$return_string .= get_the_post_thumbnail($loop->post->ID, 'shop_catalog', array('class' => "wpb_pro_img"));
				}else{
				    $return_string .= '<img id="place_holder_thm" src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
				}
				$return_string .='</a>';
				$return_string .='<figcaption>';
				$return_string .='<h3 class="pro_title">';
				if (strlen($post->post_title) > 20) {
					$return_string .= substr(the_title($before = '', $after = '', FALSE), 0, wpb_ez_get_option( 'wpb_title_mx_ch', 'wpb_wps_style', 10 )) . '...';
				}else{
					$return_string .= get_the_title();
				}
				$return_string .='</h3>';
				if( $price_html = $product->get_price_html() ){
					$return_string .='<div class="pro_price_area">'. $price_html .'</div>';
				}
				$return_string .= '<div class="wpb_wps_cart_button"><a href="'.esc_url( $product->add_to_cart_url() ).'" rel="nofollow" data-product_id="'.esc_attr( $product->id ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" class="button '. ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '') .' product_type_'.esc_attr( $product->product_type ).'">'.esc_html( $product->add_to_cart_text()).'</a></div>';
				$return_string .='</figcaption>';
				$return_string .= '</figure>';
				$return_string .= '</div>';
	    	endwhile;
		} else {
			echo __( 'No products found','wpb-wps' );
		}
		wp_reset_postdata();
				
	    $return_string .= '</div>';
	    $return_string .= '</div>';

	    wp_reset_query();
	    return $return_string;
	}
endif;



/**
 * Sidebar Feature Product Slider
 */

if( !function_exists('wpb_wps_sideber_feature_shortcode') ):
	function wpb_wps_sideber_feature_shortcode($atts){
		extract(shortcode_atts(array(
			  'posts' => 5,
		   ), $atts));

		$return_string = '<div class="wpb_slider_area wpb_sidebar_slider wpb_fix_cart">';
	    $return_string .= '<div id="wpb-wps-latest-sidebar-feature" class="wpb-wps-wrapper owl-carousel '.wpb_ez_get_option( 'wpb_slider_type_sid_fea', 'wpb_wps_style', 'grid cs-style-3' ).'">';
		
	    $args = array(
			'post_type' 		=> 'product',
			'meta_key' 			=> '_featured',
			'meta_value' 		=> 'yes', 
			'posts_per_page' 	=> $posts
		);
						
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				global $post, $product;
		        $return_string .= '<div class="item">';
				$return_string .= '<figure>';			
				$return_string .= '<a href="'.get_permalink().'" class="wpb_pro_img_url">';
				if (has_post_thumbnail( $loop->post->ID )){
					$return_string .= get_the_post_thumbnail($loop->post->ID, 'shop_catalog', array('class' => "wpb_pro_img"));
				}else{
				    $return_string .= '<img id="place_holder_thm" src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
				}
				$return_string .='</a>';
				$return_string .='<figcaption>';
				$return_string .='<h3 class="pro_title">';
				if (strlen($post->post_title) > 20) {
					$return_string .= substr(the_title($before = '', $after = '', FALSE), 0, wpb_ez_get_option( 'wpb_title_mx_ch', 'wpb_wps_style', 10 )) . '...';
				}else{
					$return_string .= get_the_title();
				}
				$return_string .='</h3>';
				if( $price_html = $product->get_price_html() ){
					$return_string .='<div class="pro_price_area">'. $price_html .'</div>';
				}
				$return_string .= '<div class="wpb_wps_cart_button"><a href="'.esc_url( $product->add_to_cart_url() ).'" rel="nofollow" data-product_id="'.esc_attr( $product->id ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" class="button '. ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '') .' product_type_'.esc_attr( $product->product_type ).'">'.esc_html( $product->add_to_cart_text()).'</a></div>';
				$return_string .='</figcaption>';
				$return_string .= '</figure>';
				$return_string .= '</div>';
	    	endwhile;
		} else {
			echo __( 'No products found','wpb-wps' );
		}
		wp_reset_postdata();
	    $return_string .= '</div>';
	    $return_string .= '</div>';
	    wp_reset_query();
	    return $return_string;   
	}
endif;