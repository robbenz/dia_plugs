<?php

/*** ADD CUSTOM META BOX ***/
function add_dia_meta_box() {
    add_meta_box("dia-tab-meta-box", "DiaMedical USA Product Tabs", "dia_meta_box_markup", "product", "normal", "high", null);
}
add_action("add_meta_boxes", "add_dia_meta_box");
/*** END ***/


/*** ADD CUSTOM META BOX MARKUP FOR ADMIN ***/
function dia_meta_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "meta-box-nonce");
  	global $post;
    $dia_tab_count = get_post_meta( $post->ID, '_dia_tabs_local_total_number', true );
  ?>

  <label for="_dia_tabs_local_total_number"><?php echo __( 'How Many Tabs', 'woocommerce' ); ?></label>
  <input name="_dia_tabs_local_total_number" type="number" value="<?php echo $dia_tab_count; ?>" step="any" min="0" max="10" style="width: 50px;" />
  <br />

<?php for ( $x = 1; $x <= $dia_tab_count; $x++ ) {

  $dia_tab_title = get_post_meta( $post->ID, "_dia_tabs_title_local_$x", true );
  $dia_tab_content = get_post_meta( $post->ID, "_dia_tabs_content_local_$x", true );
?>
  <div>
    <label for="_dia_tabs_title_local_<?php echo $x; ?>">Tab <?php echo $x ;?> Heading: </label>
    <input name="_dia_tabs_title_local_<?php echo $x; ?>" type="text" value="<?php echo $dia_tab_title; ?>">
    <br>
    <?php
        $dia_tab_content = get_post_meta( $post->ID, "_dia_tabs_content_local_$x", true );
        	if ( ! $dia_tab_content ) {
        		$dia_tab_content = '';
        	}
        	$settings = array( 'textarea_name' => "dia-product-tabs-details_$x" );
        	?>
        	<tr class="form-field">
        		<th scope="row" valign="top"><label for="dia-product-tabs-details_<?php echo $x ;?>">Tab <?php echo $x ;?> Content: </label></th>
        		<td>
        			<?php wp_nonce_field( basename( __FILE__ ), "dia_product_tabs_details_nonce_$x" ); ?>
        			<?php wp_editor( wp_kses_post( $dia_tab_content ), "dia_tab_content_$x", $settings ); ?>
        		</td>
        	</tr>
          <br><hr><br></div>
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
    if(isset($_POST["_dia_tabs_local_total_number"])) {
        $meta_box_tab_count_value = $_POST["_dia_tabs_local_total_number"];
    }
    update_post_meta($post_id, "_dia_tabs_local_total_number", $meta_box_tab_count_value);

    global $post;
    $dia_tab_count = get_post_meta( $post->ID, '_dia_tabs_local_total_number', true );
    for ( $x = 1; $x <= $dia_tab_count; $x++ ) {

      // DYNAMICALLY SAVE TAB TITLES - PER TAB COUNT
      $meta_box_tab_tab_value = "";
      if(isset($_POST["_dia_tabs_title_local_$x"])) {
        $meta_box_tab_tab_value = $_POST["_dia_tabs_title_local_$x"];
      }
      update_post_meta($post_id, "_dia_tabs_title_local_$x", $meta_box_tab_tab_value);

      // DYNAMICALLY SAVE THE STUFF IN THE TAB CONTENTS BOX
      if ( ! isset( $_POST["dia_product_tabs_details_nonce_$x"] ) || ! wp_verify_nonce( $_POST["dia_product_tabs_details_nonce_$x"], basename( __FILE__ ) ) ) {
        return;
      }
      $old_details = get_post_meta( $post->ID, "_dia_tabs_content_local_1", true );
      $new_details = isset( $_POST["dia-product-tabs-details_$x"] ) ? $_POST["dia-product-tabs-details_$x"] : '';
      if ( $old_details && '' === $new_details ) {
        delete_post_meta( $post_id, "_dia_tabs_content_local_$x" );
      } else if ( $old_details !== $new_details ) {
        update_post_meta( $post_id, "_dia_tabs_content_local_$x", wp_kses_post( $new_details ) );
      }
    } // end for loop

} // end save_custom_meta_box

add_action("save_post", "save_custom_meta_box", 10, 3);
