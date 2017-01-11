<?php


add_filter( 'woocommerce_product_tabs', 'dia_new_product_tabs' );

/*** ADD TABS ***/
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
        'title' 	=> __( $dia_tab_title, 'woocommerce' ),
        'priority' 	=> $y+50,
        'callback' 	=> 'dia_new_product_tab_content'.$y
      );
    } // end foreach
  }
  return $tabs;
}
/*** END ***/

/*** CALL BACK FOR TAB CONTENT PER DB ***/
/*** MAKE THIS SMARTER ***/
function dia_new_product_tab_content1() {
  global $post;
   $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_1", true );
   if (strlen($dia_tab_content) > 0) {
     echo $dia_tab_content;
   }
}

function dia_new_product_tab_content2() {
  global $post;
   $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_2", true );
   if (strlen($dia_tab_content) > 0) {
     echo $dia_tab_content;
   }
}

function dia_new_product_tab_content3() {
  global $post;
   $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_3", true );
   if (strlen($dia_tab_content) > 0) {
     echo $dia_tab_content;
   }
}

function dia_new_product_tab_content4() {
  global $post;
   $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_4", true );
   if (strlen($dia_tab_content) > 0) {
     echo $dia_tab_content;
   }
}

function dia_new_product_tab_content5() {
  global $post;
   $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_5", true );
   if (strlen($dia_tab_content) > 0) {
     echo $dia_tab_content;
   }
}
/*** END ***/
