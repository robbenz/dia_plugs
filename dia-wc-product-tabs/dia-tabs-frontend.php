<?php

add_filter( 'woocommerce_product_tabs', 'dia_new_product_tabs' );

/*** ADD TABS & TAB TITLES ***/
function dia_new_product_tabs( $tabs ) {
  global $post;
  $dia_tab_count = get_post_meta( $post->ID, '_dia_tabs_local_total_number', true );
  for ( $x = 1; $x <= $dia_tab_count; $x++ ) {

    $dia_tab_title = get_post_meta( $post->ID, "_dia_tabs_title_local_$x", true );
    $dia_tab_title_clean = preg_replace('/\s+/', '-', $dia_tab_title);
    $dia_tab_content = get_post_meta( $post->ID, "_dia_tabs_content_local_$x", true );

    if ( strlen($dia_tab_title) > 0 && strlen($dia_tab_content) > 0 ) {
      $tabs[$dia_tab_title_clean] = array(
        'title'   	=> __( $dia_tab_title, 'woocommerce' ),
        'priority' 	=> $x+50,
        'callback' 	=> 'dia_new_product_tab_content',
        'args'      => '_dia_tabs_content_local_' . $x
      );
    } // end foreach
  }
  return $tabs;
}
/*** END ***/

/*** DYNAMICALLY LOAD TAB CONTENT ***/
function dia_new_product_tab_content($param, $args) {
  global $post;
  $table = end($args);
  $dia_tab_content = apply_filters('the_content', get_post_meta( $post->ID, $table, true ) );
  if(isset($GLOBALS['wp_embed'])) {
    $dia_tab_content = $GLOBALS['wp_embed']->autoembed($dia_tab_content);
  }

  if (0 === strpos($dia_tab_content, '<h2>')) {
    print_r ($dia_tab_content);
  } else {
    $param_clean = str_replace('-', ' ', $param);
    echo '<h2>'.$param_clean.'</h2>';
    print_r ($dia_tab_content);
  }
}
/*** END ***/
