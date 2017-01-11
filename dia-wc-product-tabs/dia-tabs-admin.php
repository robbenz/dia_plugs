<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_meta_box() {
    add_meta_box("dia-tab-meta-box", "DiaMedical Product Tabs", "dia_meta_box_markup", "product", "normal", "low", null);
}
add_action("add_meta_boxes", "add_dia_meta_box");
/*** END ***/

/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_meta_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "meta-box-nonce");
  	global $post;
    $dia_tab_count = get_post_meta( $post->ID, '_wcj_custom_product_tabs_local_total_number', true );
  ?>

  <label for="_wcj_custom_product_tabs_local_total_number"><?php echo __( 'How Many Tabs', 'woocommerce' ); ?></label>
  <input name="_wcj_custom_product_tabs_local_total_number" class="" type="number" name=" " value="<?php echo $dia_tab_count; ?>" step="any" min="0" max="5" style="width: 50px;" />
  <br />

<?php for ( $x = 0; $x < $dia_tab_count; $x++ ) {
  $y=$x+1;
  $dia_tab_title = get_post_meta( $post->ID, "_wcj_custom_product_tabs_title_local_$y", true );
  $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );
?>
  <div>
    <label for="_wcj_custom_product_tabs_title_local_<?php echo $y; ?>">Tab <?php echo $y ;?> Heading: </label>
    <input name="_wcj_custom_product_tabs_title_local_<?php echo $y; ?>" type="text" value="<?php echo $dia_tab_title; ?>">
    <br>
    <?php
        $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );
        	if ( ! $dia_tab_content ) {
        		$dia_tab_content = '';
        	}
        	$settings = array( 'textarea_name' => "dia-product-tabs-details_$y" );
        	?>
        	<tr class="form-field">
        		<th scope="row" valign="top"><label for="dia-product-tabs-details_<?php echo $y ;?>">Tab <?php echo $y ;?> Content: </label></th>
        		<td>
        			<?php wp_nonce_field( basename( __FILE__ ), "dia_product_tabs_details_nonce_$y" ); ?>
        			<?php wp_editor( wp_kses_post( $dia_tab_content ), 'dia_tab_content', $settings ); ?>
        		</td>
        	</tr>
          <br><hr><br>

    <?php
  } // end for loop
} //custom_meta_box_markup
/*** END ***/

/*** SAVE THAT SHIT ***/
function save_custom_meta_box($post_id, $post, $update) {
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "product";
    if($slug != $post->post_type)
        return $post_id;

/* Tab Count */
    $meta_box_tab_count_value = "";
    if(isset($_POST["_wcj_custom_product_tabs_local_total_number"])) {
        $meta_box_tab_count_value = $_POST["_wcj_custom_product_tabs_local_total_number"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_local_total_number", $meta_box_tab_count_value);

/*** SAVE THE TAB TITLES ***/ /*** MAKE THIS SMARTER ***/
/* Tab 1 */
    $meta_box_tab_tab_value1 = "";
    if(isset($_POST["_wcj_custom_product_tabs_title_local_1"])) {
        $meta_box_tab_tab_value1 = $_POST["_wcj_custom_product_tabs_title_local_1"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_1", $meta_box_tab_tab_value1);
/* Tab 2 */
    $meta_box_tab_tab_value2 = "";
    if(isset($_POST["_wcj_custom_product_tabs_title_local_2"])) {
        $meta_box_tab_tab_value2 = $_POST["_wcj_custom_product_tabs_title_local_2"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_2", $meta_box_tab_tab_value2);
/* Tab 3 */
    $meta_box_tab_tab_value3 = "";
    if(isset($_POST["_wcj_custom_product_tabs_title_local_3"])) {
        $meta_box_tab_tab_value3 = $_POST["_wcj_custom_product_tabs_title_local_3"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_3", $meta_box_tab_tab_value3);
/* Tab 4 */
    $meta_box_tab_tab_value4 = "";
    if(isset($_POST["_wcj_custom_product_tabs_title_local_4"])) {
        $meta_box_tab_tab_value4 = $_POST["_wcj_custom_product_tabs_title_local_4"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_4", $meta_box_tab_tab_value4);
/* Tab 5 */
    $meta_box_tab_tab_value5 = "";
    if(isset($_POST["_wcj_custom_product_tabs_title_local_5"])) {
        $meta_box_tab_tab_value5 = $_POST["_wcj_custom_product_tabs_title_local_5"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_5", $meta_box_tab_tab_value5);
/*** END ***/

/*** SAVE THE STUFF IN THE TAB CONTENTS BOX ***/ /*** MAKE THIS SMARTER ***/
/* Tab 1 */
    if ( ! isset( $_POST['dia_product_tabs_details_nonce_1'] ) || ! wp_verify_nonce( $_POST['dia_product_tabs_details_nonce_1'], basename( __FILE__ ) ) ) {
      return;
    }
    $old_details1 = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_1", true );
    $new_details1 = isset( $_POST['dia-product-tabs-details_1'] ) ? $_POST['dia-product-tabs-details_1'] : '';
    if ( $old_details1 && '' === $new_details1 ) {
      delete_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_1' );
    } else if ( $old_details1 !== $new_details1 ) {
      update_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_1', wp_kses_post( $new_details1 ) );
    }
/* Tab 2 */
    if ( ! isset( $_POST['dia_product_tabs_details_nonce_2'] ) || ! wp_verify_nonce( $_POST['dia_product_tabs_details_nonce_2'], basename( __FILE__ ) ) ) {
      return;
    }
    $old_details2 = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_2", true );
    $new_details2 = isset( $_POST['dia-product-tabs-details_2'] ) ? $_POST['dia-product-tabs-details_2'] : '';
    if ( $old_details2 && '' === $new_details2 ) {
      delete_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_2' );
    } else if ( $old_details2 !== $new_details2 ) {
      update_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_2', wp_kses_post( $new_details2 ) );
    }
/* Tab 3 */
    if ( ! isset( $_POST['dia_product_tabs_details_nonce_3'] ) || ! wp_verify_nonce( $_POST['dia_product_tabs_details_nonce_3'], basename( __FILE__ ) ) ) {
      return;
    }
    $old_details3 = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_3", true );
    $new_details3 = isset( $_POST['dia-product-tabs-details_3'] ) ? $_POST['dia-product-tabs-details_3'] : '';
    if ( $old_details3 && '' === $new_details3 ) {
      delete_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_3' );
    } else if ( $old_details3 !== $new_details3 ) {
      update_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_3', wp_kses_post( $new_details3 ) );
    }
/* Tab 4 */
    if ( ! isset( $_POST['dia_product_tabs_details_nonce_4'] ) || ! wp_verify_nonce( $_POST['dia_product_tabs_details_nonce_4'], basename( __FILE__ ) ) ) {
      return;
    }
    $old_details4 = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_4", true );
    $new_details4 = isset( $_POST['dia-product-tabs-details_4'] ) ? $_POST['dia-product-tabs-details_4'] : '';
    if ( $old_details4 && '' === $new_details4 ) {
      delete_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_4' );
    } else if ( $old_details4 !== $new_details4 ) {
      update_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_4', wp_kses_post( $new_details4 ) );
    }
/* Tab 5 */
    if ( ! isset( $_POST['dia_product_tabs_details_nonce_5'] ) || ! wp_verify_nonce( $_POST['dia_product_tabs_details_nonce_5'], basename( __FILE__ ) ) ) {
      return;
    }
    $old_details5 = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_5", true );
    $new_details5 = isset( $_POST['dia-product-tabs-details_4'] ) ? $_POST['dia-product-tabs-details_5'] : '';
    if ( $old_details5 && '' === $new_details5 ) {
      delete_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_5' );
    } else if ( $old_details5 !== $new_details5 ) {
      update_post_meta( $post_id, '_wcj_custom_product_tabs_content_local_5', wp_kses_post( $new_details5 ) );
    }
/*** END ***/

}

add_action("save_post", "save_custom_meta_box", 10, 3);
