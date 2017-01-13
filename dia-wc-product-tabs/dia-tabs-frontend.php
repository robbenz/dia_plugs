<?php


add_filter( 'woocommerce_product_tabs', 'dia_new_product_tabs' );

/*** ADD TABS & TAB TITLES ***/
function dia_new_product_tabs( $tabs ) {
  global $post;
  $dia_tab_count = get_post_meta( $post->ID, '_wcj_custom_product_tabs_local_total_number', true );
  for ( $x = 0; $x < $dia_tab_count; $x++ ) {
    $y=$x+1;
    $dia_tab_title = get_post_meta( $post->ID, "_wcj_custom_product_tabs_title_local_$y", true );
    $dia_tab_title_clean = preg_replace('/\s+/', '-', $dia_tab_title);
    $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );

    if ( strlen($dia_tab_title) > 0 && strlen($dia_tab_content) > 0 ) {
      $tabs[$dia_tab_title_clean] = array(
        'title'   	=> __( $dia_tab_title, 'woocommerce' ),
        'priority' 	=> $y+50,
        'callback' 	=> 'dia_new_product_tab_content',
        'args'      => '_wcj_custom_product_tabs_content_local_' . $y
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
  $dia_tab_content = get_post_meta( $post->ID, $table, true );
     echo $dia_tab_content;
}
/*** END ***/
