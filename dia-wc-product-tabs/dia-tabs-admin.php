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
        			<?php wp_editor( wp_kses_post( $dia_tab_content ), "dia_tab_content_$y", $settings ); ?>
        		</td>
        	</tr>
          <br><hr><br>

    <?php
  } // end for loop
} // dia_meta_box_markup
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

    // Tab Count
    $meta_box_tab_count_value = "";
    if(isset($_POST["_wcj_custom_product_tabs_local_total_number"])) {
        $meta_box_tab_count_value = $_POST["_wcj_custom_product_tabs_local_total_number"];
    }
    update_post_meta($post_id, "_wcj_custom_product_tabs_local_total_number", $meta_box_tab_count_value);

    global $post;
    $dia_tab_count = get_post_meta( $post->ID, '_wcj_custom_product_tabs_local_total_number', true );
    for ( $x = 0; $x < $dia_tab_count; $x++ ) {
      $y=$x+1;

      // DYNAMICALLY SAVE TAB TITLES - PER TAB COUNT
      $meta_box_tab_tab_value = "";
      if(isset($_POST["_wcj_custom_product_tabs_title_local_$y"])) {
        $meta_box_tab_tab_value = $_POST["_wcj_custom_product_tabs_title_local_$y"];
      }
      update_post_meta($post_id, "_wcj_custom_product_tabs_title_local_$y", $meta_box_tab_tab_value);

      // DYNAMICALLY SAVE THE STUFF IN THE TAB CONTENTS BOX
      if ( ! isset( $_POST["dia_product_tabs_details_nonce_$y"] ) || ! wp_verify_nonce( $_POST["dia_product_tabs_details_nonce_$y"], basename( __FILE__ ) ) ) {
        return;
      }
      $old_details = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_1", true );
      $new_details = isset( $_POST["dia-product-tabs-details_$y"] ) ? $_POST["dia-product-tabs-details_$y"] : '';
      if ( $old_details && '' === $new_details ) {
        delete_post_meta( $post_id, "_wcj_custom_product_tabs_content_local_$y" );
      } else if ( $old_details !== $new_details ) {
        update_post_meta( $post_id, "_wcj_custom_product_tabs_content_local_$y", wp_kses_post( $new_details ) );
      }
    } // end for loop

} // end save_custom_meta_box

add_action("save_post", "save_custom_meta_box", 10, 3);
