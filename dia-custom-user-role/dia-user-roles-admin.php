<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_user_roles_meta_box() {
    add_meta_box("dia-user-roles-meta-box", "DiaMedical USA Product Info", "dia_user_roles_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_user_roles_meta_box");
/*** END ***/

/*** LOAD JQERY DATE PICKER ***/
function add_e2_date_picker(){
//jQuery UI date picker file
wp_enqueue_script('jquery-ui-datepicker');
//jQuery UI theme css file
wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
}
add_action('admin_enqueue_scripts', 'add_e2_date_picker');
/*** END ***/



/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_user_roles_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "dia-user-roles-meta-box-nonce");
  	global $post;
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
  } // dia_meta_box_markup
/*** END ***/


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
    }

        // dia_customer_favorite_position
        /*
        $dia_cust_fav_cust_fav_check_position = $_POST['dia_customer_favorite_position'];
        if( !empty( $dia_cust_fav_cust_fav_check_position ) ) {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }
        else {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }*/

} // end save_custom_meta_box

add_action("save_post", "dia_user_roles_save_that_shit", 10, 3);
