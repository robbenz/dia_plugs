<?php

//woocommerce single product page
add_action( 'woocommerce_before_single_product', 'dia_product_meta_display_product' );
function dia_product_meta_display_product() {
  if (is_user_logged_in() ) {
    global $product;

    $current_user = wp_get_current_user();
    $allowed_roles = array('shop_manager', 'administrator', 'shop_observer');
    if( array_intersect($allowed_roles, $current_user->roles ) ) {
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
      <input type="checkbox" id="show_specs_product">
      <label for="show_specs_product">Show Specs</label>

      <div id="specs_wrap_p_page">
        <table class="dia_tg">
          <tr>
            <th colspan="2">Main Info</th>
            <th>Vendors</th>
            <th>Primary</th>
            <th>Secondary</th>
          </tr>
          <tr>
            <td rowspan="2">Manufacturer</td>
            <td rowspan="2"><?php echo $_mft; ?></td>
            <td>Vendor</td>
            <td><?php echo $_supplier_1 ;?></td>
            <td><?php echo $_supplier_2 ;?></td>
          </tr>
          <tr>
            <td>Vendor Cost ($)</td>
            <td><?php echo '$'.$_cost_1 ;?></td>
            <td><?php echo '$'.$_cost_2 ;?></td>
          </tr>
          <tr>
            <td rowspan="2">MFT Part Number</td>
            <td rowspan="2"><?php echo $_mft_part_number; ?></td>
            <td>Vendor Part Number</td>
            <td><?php echo $_vendor_pn_1 ;?></td>
            <td><?php echo $_vendor_pn_2 ;?></td>
          </tr>
          <tr>
            <td>Vendor Date Verified</td>
            <td><?php echo $_price_check_1 ;?></td>
            <td><?php echo $_price_check_2 ;?></td>
          </tr>
          <tr>
            <td>MFT List Price ($)</td>
            <td><?php echo '$'.$_list_price; ?></td>
            <td>Vendor Verified By:</td>
            <td><?php echo $_price_check_person_1 ;?></td>
            <td><?php echo $_price_check_person_2 ;?></td>
          </tr>
        </table>
      </div>
      <?php
    } else {
      return;
    }
  }
} //  END dia_product_meta_display_product

// reset priority of opening a tag so check box works
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 50 );
//END

// hook into li product before a tag with new priority
add_action ( 'woocommerce_before_shop_loop_item' , 'dia_product_meta_display_archive', 10 );
function dia_product_meta_display_archive() {
  if ( is_user_logged_in() ) {
    global $product, $woocommerce_loop, $product, $post;
    $_id = $product->id;
    $current_user = wp_get_current_user();
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
    $allowed_roles = array('shop_manager', 'administrator', 'shop_observer');
    if( array_intersect($allowed_roles, $current_user->roles ) ) {
      ?>
      <input id="show_spec_chex_<?php echo $_id; ?>" type="checkbox" class="show_specs_archive_each">
      <label for="show_spec_chex_<?php echo $_id; ?>">&nbsp;&nbsp;Show Specs</label>

      <script type="text/javascript">
      jQuery(document).ready(function() {
        jQuery("#show_spec_div_wrap_<?php echo $_id; ?>").hide();
        jQuery("#show_spec_chex_<?php echo $_id; ?>").click(function() {
          if(jQuery(this).is(":checked")) {
            jQuery("#show_spec_div_wrap_<?php echo $_id; ?>").show(369);
          } else {
            jQuery("#show_spec_div_wrap_<?php echo $_id; ?>").hide(269);
          }
        });
      });
      </script>

      <div id="show_spec_div_wrap_<?php echo $_id; ?>" class="show_spec_div_wrap">

        <?php if( $product->is_type( 'variable' ) ): ?>

          <?php
          $available_variations = $product->get_available_variations();
          $attributes = $product->get_variation_attributes();
          $attribute_keys = array_keys( $attributes );
          $selected_attributes = $product->get_variation_default_attributes();
          ?>

          <table class="variations" style="display:block !important;" cellspacing="0">
            <tbody>
              <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                <tr>
                  <td class="value">
                    <?php
                    $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
                    wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
                    echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
                    ?>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>



        <?php elseif( $product->is_type( 'simple' ) ): ?>

        <table class="dia_tg" id="imfuckingsweetatcoding">
          <tr><td>Manufacturer: </td><td><?php echo $_mft; ?></td></tr>
          <tr><td>MFT Part #: </td><td><?php echo $_mft_part_number;?></td></tr>
          <tr><td>MFT List Price: </td><td><?php echo '$'.$_list_price;?></td></tr>
          <tr><td>Vendor 1: </td><td><?php echo $_supplier_1;?></td></tr>
          <tr><td>Cost: </td><td><?php echo '$'.$_cost_1;?></td></tr>
          <tr><td>Verified on: </td><td><?php echo $_price_check_1;?></td></tr>
          <tr><td>Vendor 2: </td><td><?php echo $_supplier_2;?></td></tr>
          <tr><td>Cost: </td><td><?php echo '$'.$_cost_2;?></td></tr>
          <tr><td>Verified on: </td><td><?php echo $_price_check_2;?></td></tr>
        </table>

      <?php endif ; ?>

      </div>

      <?php
    } else {
      return;
    }
  }
}
