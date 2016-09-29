<?php
/*
Plugin Name: Custom fields shortcode
Description: Use custom fields as shortcode in post content
Plugin URI:  http://gonahkar.com/wordpress-plugins/custom-fields-shortcode/
Version:     0.1
Author:      Gonahkar
Author URI:  http://gonahkar.com
*/

/*

USAGE:
use [cf] shortcode in your post content to show the custom field value without edit your theme files.

EXAMPLE:
[cf]somekey[/cf]
returns value of 'somekey' custom-filed.

*/
function customfields_shortcode($atts, $text) {
	global $post;
	return get_post_meta($post->ID, $text, true);
}

@add_shortcode('cf','customfields_shortcode');
?>