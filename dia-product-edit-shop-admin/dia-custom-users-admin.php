<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_users_meta_box() {
    add_meta_box("dia-user-role-meta-box", "DiaMedical USA CUSTOM STUFF", "dia_users_CUSTOM_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_users_meta_box");
/*** END ***/

/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_users_CUSTOM_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "dia-users-meta-box-nonce");
  	global $post;


    echo '<div>';
    woocommerce_wp_checkbox(
    array(
      'id'            => 'dia_customer_favorite',
      'name'          => 'dia_customer_favorite',
    	'class'         => 'dia_customer_favorite checkbox',
    	'label'         => __('Customer Favorite?  ', 'woocommerce' ),
      'desc_tip'      => 'true',
    	'description'   => __( 'Check this box IF you want the Customer Favorite Badge', 'woocommerce' )
    	)
    );
    echo '</div>';

    echo '<div id="dia_users_fav_pos_drop">';
    woocommerce_wp_select(
    	array(
    		'id'          => 'dia_customer_favorite_position',
    		'label'       => __( 'Customer Favorite Badge Position  ', 'woocommerce' ),
        'options'     => array(
                            'N/A'          => __( 'N/A', 'woocommerce' ),
                            'Top Left'     => __( 'Top Left', 'woocommerce' ),
                            'Top Right'    => __( 'Top Right', 'woocommerce' ),
                            'Bottom Left'  => __( 'Bottom Left', 'woocommerce' ),
                            'Bottom Right' => __( 'Bottom Right', 'woocommerce' )
                            ),
    		'desc_tip'    => 'true',
    		'description' => __( 'Where should the badge go?' )
    	)
    );
      echo '</div><hr>';


echo'<div id="dia_stuff" class="options_group">';
      woocommerce_wp_text_input(
      	array(
      		'id'          => 'dia_product_list_price',
      		'label'       => __( 'MFT List Price', 'woocommerce' )
      //    'max-width' => '300px;',
      	//	'placeholder' => 'Probably an Infusion Pump',
      	//	'desc_tip'    => 'true',
      //		'description' => __( 'Enter in the list price ', 'woocommerce' )
      	)
      );

      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_cost',
          'label'       => __( 'DiaMedical Cost', 'woocommerce' )
      //    'max-width' => '300px;',
        //	'placeholder' => 'Probably an Infusion Pump',
        //  'desc_tip'    => 'true',
      //    'description' => __( 'Enter in the list price ', 'woocommerce' )
        )
      );

      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_supplier',
          'label'       => __( 'DiaMedical Supplier', 'woocommerce' )
      //    'max-width' => '300px;',
        //	'placeholder' => 'Probably an Infusion Pump',
        //  'desc_tip'    => 'true',
      //    'description' => __( 'Enter in the list price ', 'woocommerce' )
        )
      );

      woocommerce_wp_text_input(
        array(
          'id'          => 'dia_product_mft',
          'label'       => __( 'Manufacturer', 'woocommerce' )
      //    'max-width' => '300px;',
        //	'placeholder' => 'Probably an Infusion Pump',
        //  'desc_tip'    => 'true',
      //    'description' => __( 'Enter in the list price ', 'woocommerce' )
        )
      );
            woocommerce_wp_text_input(
              array(
                'id'          => 'dia_product_price_check',
                'label'       => __( 'Date Verified', 'woocommerce' )
            //    'max-width' => '300px;',
              //	'placeholder' => 'Probably an Infusion Pump',
              //  'desc_tip'    => 'true',
            //    'description' => __( 'Enter in the list price ', 'woocommerce' )
              )
            );


  echo '</div>';

} // dia_meta_box_markup
/*** END ***/

/*** SAVE THAT SHIT ***/
function dia_users_save_custom_stuff($post_id, $post, $update) {
    if (!isset($_POST["dia-users-meta-box-nonce"]) || !wp_verify_nonce($_POST["dia-users-meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "product";
    if($slug != $post->post_type)
        return $post_id;

        // dia_customer_favorite
        $dia_users_cust_fav_checkbox = isset( $_POST['dia_customer_favorite'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'dia_customer_favorite', $dia_users_cust_fav_checkbox );

        // dia_customer_favorite_position
        $dia_users_cust_fav_check_position = $_POST['dia_customer_favorite_position'];
        if( !empty( $dia_users_cust_fav_check_position ) ) {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_users_cust_fav_check_position ) );
        }
        else {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_users_cust_fav_check_position ) );
        }



} // end save_custom_meta_box

add_action("save_post", "dia_users_save_custom_stuff", 10, 3);
