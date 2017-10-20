<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_cust_fav_meta_box() {
    add_meta_box("dia-cust-fav-role-meta-box", "DiaMedical USA Customer Favorite", "dia_cust_fav_CUSTOM_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_cust_fav_meta_box");
/*** END ***/

/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_cust_fav_CUSTOM_box_markup($post) {
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
      echo '</div>'; ?>

      <hr>

      <style type="text/css">
        .fh-profile-upload-options th,
        .fh-profile-upload-options td,
        .fh-profile-upload-options input {
          vertical-align: top;
        }

        .user-preview-image {
          display: block;
          height: auto;
          width: 300px;
        }

      </style>

      <table class="form-table fh-profile-upload-options">
        <tr>
          <th>
            <label for="mft_image">Manufacturer Image</label>
          </th>

          <td>
            <img class="user-preview-image" src="<?php echo esc_attr( get_post_meta($post->ID, 'mft_image', true ) ); ?>">

            <input type="text" name="mft_image" id="mft_image" value="<?php echo esc_attr( get_post_meta($post->ID, 'mft_image', true ) ); ?>" class="regular-text" />

            <input type='button' class="button-primary" value="Upload Image" id="uploadimage"/><br />

            <span class="description">Upload or copy/paste URL. Note: remove all whitespace from logo to ensure best quality</span>
          </td>
        </tr>


      </table>

      <script type="text/javascript">
        (function( $ ) {
          $( '#uploadimage' ).on( 'click', function() {
            tb_show('Sorry Mike, it\'s a little bit jank', 'media-upload.php?type=image&TB_iframe=1');

            window.send_to_editor = function( html )
            {
              imgurl = $( 'img',html ).attr( 'src' );
              $( '#mft_image' ).val(imgurl);
              tb_remove();
            }

            return false;
          });

        })(jQuery);
      </script>
<?php

  woocommerce_wp_checkbox(
  array(
    'id'            => 'dia_whitespace_adj',
    'name'          => 'dia_whitespace_adj',
    'class'         => 'dia_whitespace_adj checkbox',
    'label'         => __('Full Height Featured Image?  ', 'woocommerce' ),
    'desc_tip'      => 'true',
    'description'   => __( 'Check this box if the main product image is full height, and has no whitespace at the top', 'woocommerce' )
    )
  );

  echo '<hr><div id=""><h3 style="color:#00426a;">Product Slider Options</h3>';
  woocommerce_wp_checkbox(
  array(
    'id'            => 'ns_sale_slider',
    'name'          => 'ns_sale_slider',
    'class'         => 'ns_sale_slider checkbox',
    'label'         => __('Nursing School Sale Slider?  ', 'woocommerce' )
    )
  );
  woocommerce_wp_checkbox(
  array(
    'id'            => 'ns_featured_slider',
    'name'          => 'ns_featured_slider',
    'class'         => 'ns_featured_slider checkbox',
    'label'         => __('Nursing School Featured Slider?  ', 'woocommerce' )
    )
  );

echo '</div>';

}
// dia_meta_box_markup
/*** END ***/

add_action( 'admin_enqueue_scripts', 'enqueue_admin' );
function enqueue_admin() {
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style('thickbox');

	wp_enqueue_script('media-upload');
}


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

        // sliders
        $dia_ns_feat = isset( $_POST['ns_featured_slider'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'ns_featured_slider', $dia_ns_feat );
        $dia_ns_sale = isset( $_POST['ns_sale_slider'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'ns_sale_slider', $dia_ns_sale );

        // dia_customer_favorite_position
        $dia_cust_fav_cust_fav_check_position = $_POST['dia_customer_favorite_position'];
        if( !empty( $dia_cust_fav_cust_fav_check_position ) ) {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }
        else {
          update_post_meta( $post_id, 'dia_customer_favorite_position', esc_attr( $dia_cust_fav_cust_fav_check_position ) );
        }

        update_post_meta( $post_id, 'mft_image', $_POST[ 'mft_image' ] );

        // dia_customer_favorite
        $dia_whitespace_adj_checkbox = isset( $_POST['dia_whitespace_adj'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, 'dia_whitespace_adj', $dia_whitespace_adj_checkbox );


} // end save_custom_meta_box

add_action("save_post", "dia_cust_fav_save_custom_stuff", 10, 3);
