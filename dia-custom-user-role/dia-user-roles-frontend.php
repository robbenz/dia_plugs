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
      <?php if( $product->is_type( 'variable' ) ):
        $available_variations = $product->get_available_variations();
        ?>

          <select id="var_pro_drop_<?php echo $_id; ?>" >
            <option class="nothing" value="select-option">Select Option</option>

            <?php foreach ( $available_variations as $attribute_name => $options ) :
              $new_array = array_values($options["attributes"]);
              $newstring = implode(' | ', $new_array);
              ?>
              <option class="var_specs_drop_option_<?php echo $options["variation_id"]; ?>"
                      value="var_product_<?php echo $options["variation_id"]; ?>">
                      <?php echo $newstring ;?>
             </option>
           <?php endforeach; ?>
         </select>

         <?php
            foreach ($available_variations as $_AV ) {
              echo '<div class="var_specs_wrap" id="var_specs_wrap_'. $_AV[ 'variation_id' ] . '">';
              echo 'Manufacturer: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_mft', true ) .'<br>';
              echo 'MFT Part #: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_mft_pn', true ) .'<br>';
              echo 'List Price: <span style="color:#78be20;">$' . number_format(get_post_meta( $_AV[ 'variation_id' ], 'dia_var_list_price', true )) .'</span><br>';

              echo 'Vendor 1: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor1', true ) .'<br>';
              echo 'Vendor PN: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor_pn', true ) .'<br>';
              echo 'Cost: <span style="color:#78be20;">$' . number_format(get_post_meta( $_AV[ 'variation_id' ], 'dia_var_cost', true )) .'</span><br>';
              echo 'Date Verified: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check', true ) .'<br>';
              echo 'Verified by: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check_person1', true ) .'<br>';

              $var_vend2 = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor2', true );
              if (strlen($var_vend2) > 0) { echo 'Vendor 2: ' . $var_vend2 .'<br>'; }

              $var_vendpn2 = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor_pn2', true );
              if (strlen($var_vendpn2) > 0) { echo 'Vender 2 PN: ' . $var_vendpn2 .'<br>'; }

              $var_vend2cost = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_cost2', true );
              if (strlen($var_vend2cost) > 0) {
                echo 'Vender 2 Cost: <span style="color:#78be20;">$' . number_format($var_vend2cost) .'</span><br>';
              }

              $var_vend2dv = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check2', true );
              if (strlen($var_vend2dv) > 0) { echo 'Date Verified: ' . $var_vend2dv .'<br>'; }

              $var_vend2dvname = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check_person2', true );
              if (strlen($var_vend2dvname) > 0) { echo 'Verified by: ' . $var_vend2dvname .'<br>'; }

              echo '</div>';
            }
          ?>

          <?php foreach ( $available_variations as $attribute_name => $options ) :?>

          <script type="text/javascript">
          jQuery(document).ready(function() {

            jQuery(".var_specs_wrap").hide();

            jQuery("#var_pro_drop_<?php echo $_id; ?>").change(function() {
                var val = jQuery(this).val();
                if(val === "var_product_<?php echo $options["variation_id"]; ?>") {
                    jQuery(".var_specs_wrap").hide();
                    jQuery("#var_specs_wrap_<?php echo $options["variation_id"]; ?>").show();
                }
              });

          });
          </script>

        <?php endforeach; ?>

      <?php elseif( $product->is_type( 'simple' ) ): ?>


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

  <?php endif ; ?>

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

        <?php if( $product->is_type( 'variable' ) ):
          $available_variations = $product->get_available_variations();
          ?>

            <select id="var_pro_drop_<?php echo $_id; ?>" >
              <option class="nothing" value="select-option">Select Option</option>

              <?php foreach ( $available_variations as $attribute_name => $options ) :
                $new_array = array_values($options["attributes"]);
                $newstring = implode(' | ', $new_array);
                ?>
                <option class="var_specs_drop_option_<?php echo $options["variation_id"]; ?>"
                        value="var_product_<?php echo $options["variation_id"]; ?>">
                        <?php echo $newstring ;?>
               </option>
             <?php endforeach; ?>
           </select>

           <?php
              foreach ($available_variations as $_AV ) {
                echo '<div style="color:#fff;border: 1px solid #fff;" class="var_specs_wrap" id="var_specs_wrap_'. $_AV[ 'variation_id' ] . '">';
                echo 'Manufacturer: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_mft', true ) .'<br>';
                echo 'MFT Part #: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_mft_pn', true ) .'<br>';
                echo 'List Price: <span style="color:#78be20;">$' . number_format(get_post_meta( $_AV[ 'variation_id' ], 'dia_var_list_price', true )) .'</span><br>';

                echo 'Vendor 1: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor1', true ) .'<br>';
                echo 'Vendor PN: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor_pn', true ) .'<br>';
                echo 'Cost: <span style="color:#78be20;">$' . number_format(get_post_meta( $_AV[ 'variation_id' ], 'dia_var_cost', true )) .'</span><br>';
                echo 'Date Verified: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check', true ) .'<br>';
                echo 'Verified by: ' . get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check_person1', true ) .'<br>';

                $var_vend2 = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor2', true );
                if (strlen($var_vend2) > 0) { echo 'Vendor 2: ' . $var_vend2 .'<br>'; }

                $var_vendpn2 = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_vendor_pn2', true );
                if (strlen($var_vendpn2) > 0) { echo 'Vender 2 PN: ' . $var_vendpn2 .'<br>'; }

                $var_vend2cost = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_cost2', true );
                if (strlen($var_vend2cost) > 0) {
                  echo 'Vender 2 Cost: <span style="color:#78be20;">$' . number_format($var_vend2cost) .'</span><br>';
                }

                $var_vend2dv = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check2', true );
                if (strlen($var_vend2dv) > 0) { echo 'Date Verified: ' . $var_vend2dv .'<br>'; }

                $var_vend2dvname = get_post_meta( $_AV[ 'variation_id' ], 'dia_var_date_check_person2', true );
                if (strlen($var_vend2dvname) > 0) { echo 'Verified by: ' . $var_vend2dvname .'<br>'; }

                echo '</div>';
              }
            ?>

            <?php foreach ( $available_variations as $attribute_name => $options ) :?>

            <script type="text/javascript">
            jQuery(document).ready(function() {

              jQuery(".var_specs_wrap").hide();

              jQuery("#var_pro_drop_<?php echo $_id; ?>").change(function() {
                  var val = jQuery(this).val();
                  if(val === "var_product_<?php echo $options["variation_id"]; ?>") {
                      jQuery(".var_specs_wrap").hide();
                      jQuery("#var_specs_wrap_<?php echo $options["variation_id"]; ?>").show();
                  }
                });

            });
            </script>

          <?php endforeach; ?>

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
