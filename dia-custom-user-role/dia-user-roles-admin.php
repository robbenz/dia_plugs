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
          'label' => __( $value, 'woocommerce' ),
          'type'  => 'text'
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
      <div id="var_product_alert">This is a Variable Product.<br /> Please input dia_specs values in the variations tab above</div>
      <?php

  } // dia_meta_box_markup

/*** END ***/

// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );

/** Create new fields for variations **/
function variation_settings_fields( $loop, $variation_data, $variation ) {
  echo '<hr><div id="all_dia_var_specs_wrapp"><div id="dia_var_specs_30">';
  // Var Manufacturer
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_mft[' . $variation->ID . ']',
      'class'       => 'dia_var_mft',
			'label'       => __( 'Manufacturer', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_mft', true )
		)
	);
  // Var Manufacturer Part Number
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_mft_pn[' . $variation->ID . ']',
      'class'       => 'dia_var_mft_pn',
      'label'       => __( 'Manufacturer Part Number', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_mft_pn', true )
    )
  );
  // Var List Price
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_list_price[' . $variation->ID . ']',
      'class'       => 'dia_var_list_price',
			'label'       => __( 'Variation List Price', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_list_price', true ),
			'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						)
		)
	);
  echo '</div><div id="dia_var_specs_20">';
  // Var Vendor 1
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_vendor1[' . $variation->ID . ']',
      'class'       => 'dia_var_vendor1',
			'label'       => __( 'Primary Vendor', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_vendor1', true )
		)
	);
  // Var cost 1
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_cost[' . $variation->ID . ']',
      'class'       => 'dia_var_cost',
      'label'       => __( 'Vendor 1 Cost', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_cost', true ),
      'custom_attributes' => array(
              'step' 	=> 'any',
              'min'	=> '0'
            )
    )
  );
	// Var Vendor1 Part Number
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_vendor_pn[' . $variation->ID . ']',
      'class'       => 'dia_var_vendor_pn',
			'label'       => __( 'Vendor 1 Part Number', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_vendor_pn', true )
		)
	);
  // Var Vendor1 date
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_date_check[' . $variation->ID . ']',
      'class'       => 'dia_var_date_check',
      'label'       => __( 'Date Verified', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_date_check', true ),
    )
  );
// Var Vendor1 person
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_date_check_person1[' . $variation->ID . ']',
      'class'       => 'dia_var_date_check_person1',
      'label'       => __( 'Price Verified By: ', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_date_check_person1', true ),
    )
  );

echo '<div id="var_vendor_2_wrapp">';
  // Var Vendor 2
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_vendor2[' . $variation->ID . ']',
      'class'       => 'dia_var_vendor2',
			'label'       => __( 'Secondary Vendor', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_vendor2', true )
		)
	);
  // Var cost 2
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_cost2[' . $variation->ID . ']',
      'class'       => 'dia_var_cost2',
      'label'       => __( 'Vendor 2 Cost', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_cost2', true ),
      'custom_attributes' => array(
              'step' 	=> 'any',
              'min'	=> '0'
            )
    )
  );
	// Var Vendor2 Part Number
	woocommerce_wp_text_input(
		array(
			'id'          => 'dia_var_vendor_pn2[' . $variation->ID . ']',
      'class'       => 'dia_var_vendor_pn2',
			'label'       => __( 'Vendor 2 Part Number', 'woocommerce' ),
			'value'       => get_post_meta( $variation->ID, 'dia_var_vendor_pn2', true )
		)
	);
  // Var Vendor2 date
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_date_check2[' . $variation->ID . ']',
      'class'       => 'dia_var_date_check2',
      'label'       => __( 'Date Verified', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_date_check2', true ),
    )
  );
// Var Vendor2 person
  woocommerce_wp_text_input(
    array(
      'id'          => 'dia_var_date_check_person2[' . $variation->ID . ']',
      'class'       => 'dia_var_date_check_person2',
      'label'       => __( 'Price Verified By: ', 'woocommerce' ),
      'value'       => get_post_meta( $variation->ID, 'dia_var_date_check_person2', true ),
    )
  );

echo '</div></div></div>';
}

/**  Save new fields for variations  **/
function save_variation_settings_fields( $post_id ) {

/*
  $dia_var_specs_array = array(
    'dia_var_mft',
    'dia_var_mft_pn',
    'dia_var_list_price',
    'dia_var_vendor1',
    'dia_var_cost',
    'dia_var_vendor_pn',
    'dia_var_date_check',
    'dia_var_date_check_person1',
    'dia_var_vendor2',
    'dia_var_cost2',
    'dia_var_vendor_pn2',
    'dia_var_date_check2',
    'dia_var_date_check_person2'
  );
*/

  $dia_var_mft = $_POST['dia_var_mft'][ $post_id ];
  if( ! empty( $dia_var_mft ) ) {
    update_post_meta( $post_id, 'dia_var_mft', esc_attr( $dia_var_mft) );
  } else {
    update_post_meta( $post_id, 'dia_var_mft', esc_attr( $dia_var_mft) );
  }

  $dia_var_mft_pn = $_POST['dia_var_mft_pn'][ $post_id ];
  if( ! empty( $dia_var_mft_pn ) ) {
    update_post_meta( $post_id, 'dia_var_mft_pn', esc_attr( $dia_var_mft_pn) );
  } else {
    update_post_meta( $post_id, 'dia_var_mft_pn', esc_attr( $dia_var_mft_pn) );
  }

  $dia_var_list_price = $_POST['dia_var_list_price'][ $post_id ];
  if( ! empty( $dia_var_list_price ) ) {
    update_post_meta( $post_id, 'dia_var_list_price', esc_attr( $dia_var_list_price ) );
  } else {
    update_post_meta( $post_id, 'dia_var_list_price', esc_attr( $dia_var_list_price ) );
  }

  $dia_var_vendor1 = $_POST['dia_var_vendor1'][ $post_id ];
  if( ! empty( $dia_var_vendor1 ) ) {
    update_post_meta( $post_id, 'dia_var_vendor1', esc_attr( $dia_var_vendor1) );
  } else {
    update_post_meta( $post_id, 'dia_var_vendor1', esc_attr( $dia_var_vendor1) );
  }

  $dia_var_cost = $_POST['dia_var_cost'][ $post_id ];
	if( ! empty( $dia_var_cost ) ) {
		update_post_meta( $post_id, 'dia_var_cost', esc_attr( $dia_var_cost ) );
	} else {
    update_post_meta( $post_id, 'dia_var_cost', esc_attr( $dia_var_cost ) );
  }

  $dia_var_vendor_pn = $_POST['dia_var_vendor_pn'][ $post_id ];
  if( ! empty( $dia_var_vendor_pn ) ) {
    update_post_meta( $post_id, 'dia_var_vendor_pn', esc_attr( $dia_var_vendor_pn ) );
  } else {
    update_post_meta( $post_id, 'dia_var_vendor_pn', esc_attr( $dia_var_vendor_pn ) );
  }

  $dia_var_date_check = $_POST['dia_var_date_check'][ $post_id ];
  if( ! empty( $dia_var_date_check) ) {
    update_post_meta( $post_id, 'dia_var_date_check', esc_attr( $dia_var_date_check ) );
  } else {
    update_post_meta( $post_id, 'dia_var_date_check', esc_attr( $dia_var_date_check ) );
  }

  $dia_var_date_check_person1 = $_POST['dia_var_date_check_person1'][ $post_id ];
  if( ! empty( $dia_var_date_check_person1 ) ) {
    update_post_meta( $post_id, 'dia_var_date_check_person1', esc_attr( $dia_var_date_check_person1 ) );
  } else {
    update_post_meta( $post_id, 'dia_var_date_check_person1', esc_attr( $dia_var_date_check_person1 ) );
  }

  $dia_var_vendor2 = $_POST['dia_var_vendor2'][ $post_id ];
  if( ! empty( $dia_var_vendor2) ) {
    update_post_meta( $post_id, 'dia_var_vendor2', esc_attr( $dia_var_vendor2 ) );
  } else {
    update_post_meta( $post_id, 'dia_var_vendor2', esc_attr( $dia_var_vendor2 ) );
  }

  $dia_var_cost2 = $_POST['dia_var_cost2'][ $post_id ];
  if( ! empty( $dia_var_cost2) ) {
    update_post_meta( $post_id, 'dia_var_cost2', esc_attr( $dia_var_cost2) );
  } else {
    update_post_meta( $post_id, 'dia_var_cost2', esc_attr( $dia_var_cost2) );
  }

  $dia_var_vendor_pn2 = $_POST['dia_var_vendor_pn2'][ $post_id ];
  if( ! empty( $dia_var_vendor_pn2) ) {
    update_post_meta( $post_id, 'dia_var_vendor_pn2', esc_attr( $dia_var_vendor_pn2) );
  } else {
    update_post_meta( $post_id, 'dia_var_vendor_pn2', esc_attr( $dia_var_vendor_pn2) );
  }

  $dia_var_date_check2 = $_POST['dia_var_date_check2'][ $post_id ];
  if( ! empty( $dia_var_date_check2) ) {
    update_post_meta( $post_id, 'dia_var_date_check2', esc_attr( $dia_var_date_check2) );
  } else {
    update_post_meta( $post_id, 'dia_var_date_check2', esc_attr( $dia_var_date_check2) );
  }

  $dia_var_date_check_person2 = $_POST['dia_var_date_check_person2'][ $post_id ];
  if( ! empty( $dia_var_date_check_person2) ) {
    update_post_meta( $post_id, 'dia_var_date_check_person2', esc_attr( $dia_var_date_check_person2) );
  } else {
    update_post_meta( $post_id, 'dia_var_date_check_person2', esc_attr( $dia_var_date_check_person2) );
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
