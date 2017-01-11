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

  <label for="meta-box-checkbox">Check Box</label>
             <?php
                 $checkbox_value = get_post_meta($object->ID, "meta-box-checkbox", true);

                 if($checkbox_value == "")
                 {
                     ?>
                         <input name="meta-box-checkbox" type="checkbox" value="true">
                     <?php
                 }
                 else if($checkbox_value == "true")
                 {
                     ?>
                         <input name="meta-box-checkbox" type="checkbox" value="true" checked>
                     <?php
                 }
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
    <label for="meta-box-text">Tab <?php echo $y ;?> Heading: </label>
    <input name="meta-box-text" type="text" value="<?php echo $dia_tab_title; ?>">
    <br>
    <?php
        $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );
        	if ( ! $dia_tab_content ) {
        		$dia_tab_content = '';
        	}
        	$settings = array( 'textarea_name' => 'dia-product-tabs-details' );
        	?>
        	<tr class="form-field">
        		<th scope="row" valign="top"><label for="dia-product-tabs-details">Tab <?php echo $y ;?> Content: </label></th>
        		<td>
        			<?php wp_nonce_field( basename( __FILE__ ), 'dia_product_tabs_details_nonce' ); ?>
        			<?php wp_editor( wp_kses_post( $dia_tab_content ), 'dia_tab_content', $settings ); ?>
        		</td>
        	</tr>
          <br><hr><br>

    <?php
  } // end for loop
} //custom_meta_box_markup
/*** END ***/
