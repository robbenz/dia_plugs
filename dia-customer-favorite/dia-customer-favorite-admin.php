<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_cust_fav_meta_box() {
    add_meta_box("dia-cust-fav-role-meta-box", "DiaMedical USA Customer Favorite", "dia_cust_fav_CUSTOM_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_cust_fav_meta_box");
/*** END ***/

/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_cust_fav_CUSTOM_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "dia-cust-fav-meta-box-nonce");
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

    echo '<div id="dia_cust_fav_fav_pos_drop">';
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
    		'description' => __( 'What\'s your favorite posish? That\'s cool with me, it\'s not my favorite but I\'ll do it for you.' )
    	)
    );
      echo '</div>';
}
// dia_meta_box_markup
/*** END ***/

/*** SAVE THAT SHIT ***/
function dia_cust_fav_save_custom_stuff($post_id, $post, $update) {
    if (!isset($_POST["dia-cust-fav-meta-box-nonce"]) || !wp_verify_nonce($_POST["dia-cust-fav-meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "product";
    if($slug != $post->post_type)
        return $post_id;

        // dia_customer_favorite
        $dia_cust_fav_cust_fav_checkbox = isset( $_POST['dia_customer_favorite'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'dia_customer_favorite', $dia_cust_fav_cust_fav_checkbox );

        // dia_customer_favorite_position
        $dia_cust_fav_cust_fav_check_position = $_POST['dia_customer_favorite_position'];
        if( !empty( $dia_cust_fav_cust_fav_check_position ) ) {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }
        else {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }



} // end save_custom_meta_box

add_action("save_post", "dia_cust_fav_save_custom_stuff", 10, 3);
