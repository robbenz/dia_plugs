<?php


//add_action( 'woocommerce_before_shop_loop_item_title', 'custom_before_title' );
add_action( 'woocommerce_after_shop_loop_item', 'custom_before_title' );
function custom_before_title() {

  global $product;

    if ( 'yes' == get_post_meta( get_the_ID(), 'dia_customer_favorite', true ) ) {

   $your_favorite_position = get_post_meta( get_the_ID(), 'dia_customer_favorite_position', true );

   echo '<img src="'.site_url().'/wp-content/imgs/dia-customer-favorite.png" style="width:125px; position:relative;';

   if ($your_favorite_position == 'Top Left'){
     echo 'top: -374px; left: -13px;';
   }
   elseif ($your_favorite_position == 'Bottom Left'){
     echo 'top: -184px; left: -13px;';
   }
   elseif ($your_favorite_position == 'Top Right'){
      echo 'top: -374px; left: 132px;';
   }
   elseif ($your_favorite_position == 'Bottom Right'){
      echo 'top: -184px; left: 132px;';
   }

   echo '" />';

 }


}
