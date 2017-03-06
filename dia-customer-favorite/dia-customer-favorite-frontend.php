<?php
// add to galery page
add_action( 'woocommerce_after_shop_loop_item', 'dia_cust_fav_admin_galpage' );
add_action( 'woocommerce_product_thumbnails', 'dia_cust_fav_admin_galpage' );
function dia_cust_fav_admin_galpage() {
  global $product;
    if ( 'yes' == get_post_meta( get_the_ID(), 'dia_customer_favorite', true ) ) {
   $your_favorite_position = get_post_meta( get_the_ID(), 'dia_customer_favorite_position', true );
   echo '<img id="customer_fav_img" src="'.site_url().'/wp-content/imgs/dia-customer-favorite.png" style="width:125px; position:relative;" class="';
   if ($your_favorite_position == 'Top Left'){
     echo 'favimg_topleft';
   }
   elseif ($your_favorite_position == 'Bottom Left'){
     echo 'favimg_bottomleft';
   }
   elseif ($your_favorite_position == 'Top Right'){
     echo 'favimg_topright';
   }
   elseif ($your_favorite_position == 'Bottom Right'){
    echo 'favimg_bottomright';
   }
   echo '" />';
 }
}
