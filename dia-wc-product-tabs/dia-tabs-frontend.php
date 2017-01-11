<?php


add_filter( 'woocommerce_product_tabs', 'dia_new_product_tabs' );
function dia_new_product_tabs( $tabs ) {

	// Adds the new tab

	$tabs['test_tab'] = array(
		'title' 	=> __( 'Whatever', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'dia_new_product_tab_content'
	);



  $tabs['test_tab1'] = array(
		'title' 	=> __( 'Whatever', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'dia_new_product_tab_content1'
	);


  $tabs['test_tab2'] = array(
    'title' 	=> __( 'Whatever', 'woocommerce' ),
    'priority' 	=> 50,
    'callback' 	=> 'dia_new_product_tab_content2'
  );

  return $tabs;




}



function dia_new_product_tab_content() {

	// The new tab content

	echo '<h2>Sup Dawg</h2>';


}


function dia_new_product_tab_content1() {

	// The new tab content

	echo '<h2>Sup Dawg1</h2>';


}

function dia_new_product_tab_content2() {

	// The new tab content

	echo '<h2>Sup Dawg2</h2>';


}





/*
/*** HOOK INTO WC TABS  **
function dia_new_product_tab( $tabs ) {
    // Add the new tab
    global $post;

    $dia_tab_count = get_post_meta( $post->ID, '_wcj_custom_product_tabs_local_total_number', true );
     echo $dia_tab_count;
    for ( $x = 0; $x < $dia_tab_count; $x++ ) {
      $y=$x+1;
      echo $y;
      $dia_tab_title = get_post_meta( $post->ID, "_wcj_custom_product_tabs_title_local_$y", true );
      $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );
echo $y;
        $tabs['test_tab'] = array(
            'title'       => $dia_tab_title,
            'priority'    => $y+50,
            'callback'    => 'dia_new_product_tab_content'
        );

echo $y;
echo $dia_tab_title;
return $tabs;
    } // for loop






}
/*** END ***/


/*** The new tab content **
function dia_new_product_tab_content() {

  global $post;
//  $dia_content0   = get_post_meta( $post->ID, '_wcj_custom_product_tabs_content_local_2', true );
  echo $dia_content0;
}
/*** END ***/
