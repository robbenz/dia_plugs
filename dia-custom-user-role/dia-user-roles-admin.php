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
  	global $post;

    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_mft',
        'label'       => __( 'Manufacturer', 'woocommerce' )
        )
      );
    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_mft_part_number',
        'label'       => __( 'MFT Part Number', 'woocommerce' )
        )
      );
    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_list_price',
        'label'       => __( 'MFT List Price ($)', 'woocommerce' )
        )
      );

    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_supplier_1',
        'label'       => __( 'DiaMedical Primary Supplier', 'woocommerce' )
        )
      );
    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_cost_1',
        'label'       => __( 'DiaMedical Primary Supplier Cost ($)', 'woocommerce' )
        )
      );
    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_price_check_1',
        'label'       => __( 'Primary Supplier Date Verified', 'woocommerce' )
        )
      );
    woocommerce_wp_text_input(
      array(
        'id'          => 'dia_product_price_check_person_1',
        'label'       => __( 'Primary Supplier Verified By:', 'woocommerce' )
        )
      );

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
      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_supplier_2',
          'label'       => __( 'DiaMedical Secondary Supplier', 'woocommerce' )
          )
        );
      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_cost_2',
          'label'       => __( 'DiaMedical Secondary Supplier Cost ($)', 'woocommerce' )
          )
        );
      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_price_check_2',
          'label'       => __( 'Secondary Supplier Date Verified', 'woocommerce' )
          )
        );
      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_price_check_person_2',
          'label'       => __( 'Secondary Supplier Verified By:', 'woocommerce' )
          )
        );
        echo '</div>'; // #supplier_2_wrap
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

        // dia_customer_favorite
        $dia_user_role_chex = isset( $_POST['dia_product_multiple_suppliers'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'dia_product_multiple_suppliers', $dia_user_role_chex );


        // dia_customer_favorite_position
        $dia_cust_fav_cust_fav_check_position = $_POST['dia_customer_favorite_position'];
        if( !empty( $dia_cust_fav_cust_fav_check_position ) ) {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }
        else {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }

} // end save_custom_meta_box

add_action("save_post", "dia_user_roles_save_that_shit", 10, 3);
