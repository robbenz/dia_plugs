<?php

//woocommerce_after_single_product_summary

add_action( 'woocommerce_before_single_product', 'dia_product_meta_display_product' );

function dia_product_meta_display_product() {
  global $product;
  $current_user = wp_get_current_user();
  $allowed_roles = array('shop_manger', 'administrator');
//  if( array_intersect($allowed_roles, $current_user->roles ) ) {
    $current_user_name = $current_user->user_firstname ." ".$current_user->user_lastname;
    $_mft = get_post_meta( get_the_ID(), 'dia_product_mft', true );
    $_mft_part_number = get_post_meta( get_the_ID(), 'dia_product_mft_part_number', true );
    $_list_price = get_post_meta( get_the_ID(), 'dia_product_list_price', true );
    $_supplier_1 = get_post_meta( get_the_ID(), 'dia_product_supplier_1', true );
    $_cost_1 = get_post_meta( get_the_ID(), 'dia_product_cost_1', true );
    $_vendor_pn_1 = get_post_meta( get_the_ID(), 'dia_product_vendor_pn_1', true );
    $_price_check_1 = get_post_meta( get_the_ID(), 'dia_product_price_check_1', true );
    $_price_check_person_1 = get_post_meta( get_the_ID(), 'dia_product_price_check_person_1', true );
    $_supplier_2 = get_post_meta( get_the_ID(), 'dia_product_supplier_2', true );
    $_cost_2 = get_post_meta( get_the_ID(), 'dia_product_cost_2', true );
    $_vendor_pn_2 = get_post_meta( get_the_ID(), 'dia_product_vendor_pn_2', true );
    $_price_check_2 = get_post_meta( get_the_ID(), 'dia_product_price_check_2', true );
    $_price_check_person_2 = get_post_meta( get_the_ID(), 'dia_product_price_check_person_2', true );

?>

  <input type="checkbox" id="show_specs_product" value="first_checkbox">
  <label for="show_specs_product">Show Specs</label>
<h4 style="background-color:#00426a; margin:0; width:100%; text-align:center; color:#fff; padding: 0.25em 0; ">Greetings <?php echo $current_user_name; ?> </h4>

<div id="specs_wrap_p_page" style="border:3px solid #00426a; padding: 10px;margin:0 auto;">
<style type="text/css">
.dia_tg  {border-collapse:collapse;border-spacing:0;}
.dia_tg td{font-family:Arial, sans-serif;font-size:12px;padding:4px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.dia_tg th{font-family:Arial, sans-serif;font-size:12px;font-weight:normal;padding:4px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.dia_tg .dia_tg-yw4l{vertical-align:top}
</style>
<table class="dia_tg"style="margin:0 auto;">
  <tr>
    <th class="dia_tg-031e" colspan="2">Main Info</th>
    <th class="dia_tg-yw4l">Vendors</th>
    <th class="dia_tg-yw4l">Primary</th>
    <th class="dia_tg-yw4l">Secondary</th>
  </tr>
  <tr>
    <td class="tg-yw4l" rowspan="2">Manufacturer</td>
    <td class="tg-yw4l" rowspan="2"><?php echo $_mft; ?></td>
    <td class="tg-yw4l">Vendor</td>
    <td class="tg-yw4l"><?php echo $_supplier_1 ;?></td>
    <td class="tg-yw4l"><?php echo $_supplier_2 ;?></td>
  </tr>
  <tr>
    <td class="tg-yw4l">Vendor Cost ($)</td>
    <td class="tg-yw4l"><?php echo $_cost_1 ;?></td>
    <td class="tg-yw4l"><?php echo $_cost_2 ;?></td>
  </tr>
  <tr>
    <td class="tg-yw4l" rowspan="2">MFT Part Number</td>
    <td class="tg-yw4l" rowspan="2"><?php echo $_mft_part_number; ?></td>
    <td class="tg-yw4l">Vendor Part Number</td>
    <td class="tg-yw4l"><?php echo $_vendor_pn_1 ;?></td>
    <td class="tg-yw4l"><?php echo $_vendor_pn_2 ;?></td>
  </tr>
  <tr>
    <td class="tg-yw4l">Vendor Date Verified</td>
    <td class="tg-yw4l"><?php echo $_price_check_1 ;?></td>
    <td class="tg-yw4l"><?php echo $_price_check_2 ;?></td>
  </tr>
  <tr>
    <td class="tg-yw4l">MFT List Price ($)</td>
    <td class="tg-yw4l"><?php echo $_list_price; ?></td>
    <td class="tg-yw4l">Vendor Verified By:</td>
    <td class="tg-yw4l"><?php echo $_price_check_person_1 ;?></td>
    <td class="tg-yw4l"><?php echo $_price_check_person_2 ;?></td>
  </tr>
</table>




</div>
<?php
//} else {
//  return;
//}
}
