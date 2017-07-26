<?php
// gall page hook
add_action( 'woocommerce_before_shop_loop_item', 'dia_cust_fav_admin_galpage' );

// product page hook
add_action( 'woocommerce_product_thumbnails', 'dia_cust_fav_admin_galpage' );
function dia_cust_fav_admin_galpage() {
  global $product;

  if ( 'yes' == get_post_meta( get_the_ID(), 'dia_customer_favorite', true ) ) {
   $your_favorite_position = get_post_meta( get_the_ID(), 'dia_customer_favorite_position', true );
   echo '<img id="customer_fav_img" src="'.site_url().'/wp-content/imgs/dia-customer-favorite.png" style="width:125px; z-index:89; position:absolute;" class="';
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

// product page hook
add_action( 'woocommerce_before_single_product', 'dia_mft_img_placement' );
function dia_mft_img_placement() {
  global $product;
  $mft_img_path = get_post_meta( get_the_ID(), 'mft_image', true );
  if ( strlen($mft_img_path) > 0 ) {
    if ( 'yes' == get_post_meta( get_the_ID(), 'dia_whitespace_adj', true ) ) {
      echo '<div style="z-index:999;position:relative;width:100%;height:auto;margin-bottom: -30px;">';
      echo '<img style="max-width:150px; max-height:69px; margin-left:25px; " src="'.$mft_img_path.'" />';
      echo '</div>';
      echo '<style type="text/css">.woocommerce #content div.product div.summary{margin-top:-30px !important;}</style>';

    } else {

      echo '<div style="width:100%;height:auto;z-index:999;position:relative;">';
      echo '<img style="max-width:150px; max-height:69px; margin-left:25px; " src="'.$mft_img_path.'" />';
      echo '</div>';
      echo '<style type="text/css">.woocommerce #content div.product div.summary{margin-top:-50px !important;}</style>';

    }
  }
}
