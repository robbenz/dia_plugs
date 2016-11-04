<?php
/*
Plugin Name: css Load Last Version
Plugin URI: http://robbenz.com
Description: yeah
Author: Benz
Version: 1.0.1
Author URI: http://robbenz.com

*/

add_action( 'wp_enqueue_scripts', 'dia_enqueue_scripts', 999 );
function dia_enqueue_scripts() {

	if ( ! wp_style_is( 'style', 'done' ) ) {

		wp_deregister_style( 'style' );
		wp_dequeue_style( 'style' );

		$style_filepath = get_stylesheet_directory() . '/bst.css';
		if ( file_exists($style_filepath) ) {
			wp_enqueue_style( 'bst', get_stylesheet_uri() . '?' . filemtime( $style_filepath ) );
		}

	}

}
