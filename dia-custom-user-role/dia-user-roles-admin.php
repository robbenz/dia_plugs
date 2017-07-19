<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_user_roles_meta_box() {
    add_meta_box("dia-user-roles-meta-box", "DiaMedical USA Product Info", "dia_user_roles_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_user_roles_meta_box");
/*** END ***/

/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_user_roles_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "dia-user-roles-meta-box-nonce");
  	global $post, $product;
    ?>
    <div id="all_dia_specs_wrapp">

    <?php
    $dia_stuff_array = array(
      'dia_product_mft'             => 'Manufacturer',
      'dia_product_mft_part_number' => 'MFT Part Number',
      'dia_product_list_price'      => 'MFT List Price ($)'
    );
    foreach ($dia_stuff_array as $key => $value ) {
      woocommerce_wp_text_input( array(
          'id'    => $key,
          'label' => __( $value, 'woocommerce' )
          ) );
    } // end foreach $dia_stuff_array
    $dia_supplier_array = array (
      'dia_product_supplier'           => 'Vendor',
      'dia_product_cost'               => 'Vendor Cost ($)',
      'dia_product_vendor_pn'          => 'Vendor Part Number',
      'dia_product_price_check'        => 'Vendor Date Verified',
      'dia_product_price_check_person' => 'Vendor Verified By:'
    );

    for ( $x = 1; $x < 3; $x++ ) {
      if ($x == 1) { $ZZ = "Primary "; }
      elseif ($x == 2) { $ZZ = "Secondary ";
        woocommerce_wp_checkbox(
          array(
            'id'          => 'dia_product_multiple_suppliers',
            'name'        => 'dia_product_multiple_suppliers',
            'class'       => 'dia_product_multiple_suppliers checkbox',
            'label'       => __('Multiple Suppliers?  ', 'woocommerce' ),
            'desc_tip'    => 'true',
            'description' => __( 'Check this box If there are two suppliers for this product', 'woocommerce' )
            )
          );
          echo '<div id="supplier_2_wrap">';
        }
        foreach ($dia_supplier_array as $key => $value){
          woocommerce_wp_text_input( array(
            'id'    => $key."_$x",
            'label' => __( $ZZ.$value, 'woocommerce' )
            ) );
          } // end foreach
          if ($x == 2) { echo '</div>'; }
        } // end for loop

        ?>
      </div>
      <div id="var_product_alert">This is a Variable Product. Please input values per variation in the variations tab above</div>
      <?php

  } // dia_meta_box_markup

/*** END ***/

// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
/**
 * Create new fields for variations
 *
*/
function variation_settings_fields( $loop, $variation_data, $variation ) {
  echo '<hr>';
	// Text Field
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_vendor_pn[' . $variation->ID . ']',
      'class'       => 'dia_var_vendor_pn',
      'wrapper'       => 'dia_var_vendor_pn',
			'label'       => __( 'Vendor Part Number', 'woocommerce' ),
			'desc_tip'    => 'true',
			'description' => __( 'Enter the Vendor Part Number for this variation.', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_vendor_pn', true )
		)
	);
	// Number Field
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_list_price[' . $variation->ID . ']',
      'class'       => 'dia_var_list_price',
			'label'       => __( 'Variation List Price', 'woocommerce' ),
			'desc_tip'    => 'true',
			'description' => __( 'Enter the List Price for this variation.', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_list_price', true ),
			'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						)
		)
	);
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_cost[' . $variation->ID . ']',
      'class'       => 'dia_var_cost',
      'label'       => __( 'Variation Cost', 'woocommerce' ),
      'desc_tip'    => 'true',
      'description' => __( 'Enter the Cost for this variation.', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_cost', true ),
      'custom_attributes' => array(
              'step' 	=> 'any',
              'min'	=> '0'
            )
    )
  );

  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_date_check[' . $variation->ID . ']',
      'class'       => 'dia_var_date_check',
      'label'       => __( 'Date Verified', 'woocommerce' ),
      'desc_tip'    => 'true',
      'description' => __( 'Enter the Date you verified the prices for this variation.', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_date_check', true ),
    )
  );

}

/**  Save new fields for variations  **/
function save_variation_settings_fields( $post_id ) {

	$dia_var_date_check = $_POST['dia_var_date_check'][ $post_id ];
	if( ! empty( $dia_var_date_check ) ) {
		update_post_meta( $post_id, 'dia_var_date_check', esc_attr( $dia_var_date_check ) );
	}
	$dia_var_cost = $_POST['dia_var_cost'][ $post_id ];
	if( ! empty( $dia_var_cost ) ) {
		update_post_meta( $post_id, 'dia_var_cost', esc_attr( $dia_var_cost ) );
	}
  $dia_var_list_price = $_POST['dia_var_list_price'][ $post_id ];
  if( ! empty( $dia_var_list_price ) ) {
    update_post_meta( $post_id, 'dia_var_list_price', esc_attr( $dia_var_list_price ) );
  }
  $dia_var_vendor_pn = $_POST['dia_var_vendor_pn'][ $post_id ];
  if( ! empty( $dia_var_vendor_pn ) ) {
    update_post_meta( $post_id, 'dia_var_vendor_pn', esc_attr( $dia_var_vendor_pn ) );
  }

}

/*** SAVE THAT SHIT ***/
function dia_user_roles_save_that_shit($post_id, $post, $update) {
    if (!isset($_POST["dia-user-roles-meta-box-nonce"]) || !wp_verify_nonce($_POST["dia-user-roles-meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "product";
    if($slug != $post->post_type)
        return $post_id;

    // dia_multiple supplier checkbox
    $dia_user_role_chex = isset( $_POST['dia_product_multiple_suppliers'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, 'dia_product_multiple_suppliers', $dia_user_role_chex );

    $dia_text_inputs = array (
      'dia_product_mft',
      'dia_product_mft_part_number',
      'dia_product_list_price',
      'dia_product_supplier_1',
      'dia_product_cost_1',
      'dia_product_vendor_pn_1',
      'dia_product_price_check_1',
      'dia_product_price_check_person_1',
      'dia_product_supplier_2',
      'dia_product_cost_2',
      'dia_product_vendor_pn_2',
      'dia_product_price_check_2',
      'dia_product_price_check_person_2'
    );
    foreach ($dia_text_inputs as $inputt) {
      $dia_users_meta_value = "";
      if(isset($_POST[$inputt])) {
        $dia_users_meta_value = $_POST[$inputt];
      }
      update_post_meta($post_id, $inputt, $dia_users_meta_value);
    } // end foreach

} // end dia_user_roles_save_that_shit

add_action("save_post", "dia_user_roles_save_that_shit", 10, 3);
