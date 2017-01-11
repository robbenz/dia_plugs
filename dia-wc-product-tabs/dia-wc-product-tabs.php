<?php
/*
Plugin Name: DiaMedical WooCommerce Product Tabs
Plugin URI: robbenz.com
Description: This plugin will replace the custom tabs created by woocommerce jetpack because woocommerce jetpack is no longer free -- and does a bunch of shit we dont need
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/




function custom_meta_box_markup($object) {
  wp_nonce_field(basename(__FILE__), "meta-box-nonce");
  	global $post;
    // _wcj_custom_product_tabs_local_total_number

    $dia_tab_count = get_post_meta( $post->ID, '_wcj_custom_product_tabs_local_total_number', true );
  ?>

  <label for="custom_field_type"><?php echo __( 'How Many Tabs', 'woocommerce' ); ?></label>
  <input class="" type="number" name=" " value="<?php echo $dia_tab_count; ?>" step="any" min="0" max="5" style="width: 50px;" />
  <br />

<?php for ( $x = 0; $x < $dia_tab_count; $x++ ) {
  $y=$x+1;
  $dia_tab_title = get_post_meta( $post->ID, "_wcj_custom_product_tabs_title_local_$y", true );
  $dia_tab_content = get_post_meta( $post->ID, "_wcj_custom_product_tabs_content_local_$y", true );
//  echo $x+1;
?>
  <div>
    <label for="meta-box-text">Tab <?php echo $x+1 ;?> Heading: </label>
    <input name="meta-box-text" type="text" value="<?php echo $dia_tab_title; ?>">
    <br>
    <?php
}
?>









      <label for="meta-box-checkbox">Check Box</label>
      <?php
      $checkbox_value = get_post_meta($object->ID, "meta-box-checkbox", true);
      if($checkbox_value == "") { ?>
        <input name="meta-box-checkbox" type="checkbox" value="true">
        <?php }
        else if($checkbox_value == "true") {
          ?>
          <input name="meta-box-checkbox" type="checkbox" value="true" checked>
          <?php } ?>
        </div>
    <?php
}

/*** ADD CUSTOM META BOX ***/
function add_custom_meta_box()
{
    add_meta_box("demo-meta-box", "DiaMedical Product Tabs", "custom_meta_box_markup", "product", "normal", "low", null);
}

add_action("add_meta_boxes", "add_custom_meta_box");
/*** END ***/




add_action( 'woocommerce_product_options_general_product_data', 'wc_custom_add_custom_fields' );
function wc_custom_add_custom_fields() {
    // Print a custom text field
    woocommerce_wp_textarea_input( array(
        'id' => '_custom_text_field',
        'label' => 'Custom Text Field',
        'description' => 'This is a custom field, you can write here anything you want.',
        'desc_tip' => 'true',
        'placeholder' => 'Custom text',
        'options' => array( 'textarea_rows' => 5, )
    ) );
}






add_filter( 'woocommerce_product_tabs', 'wpb_new_product_tab0' );
function wpb_new_product_tab0( $tabs ) {
    // Add the new tab
    global $post;
    $dia_title0   = get_post_meta( $post->ID, '_wcj_custom_product_tabs_title_local_2', true );
    if (strlen($dia_title0) > 0 ) {
      $tabs['test_tab'] = array(
        //'title'       => __( 'Discount', 'text-domain' ),
          'title'       => $dia_title0,
          'priority'    => 50,
          'callback'    => 'wpb_new_product_tab_content0'
      );
    } else {
      $tabs['test_tab'] = array(
        // 'title'       => __( 'Discount', 'text-domain' ),
        //  'title'       => $dia_title0,
          // 'priority'    => 50,
          // 'callback'    => 'wpb_new_product_tab_content0'
      );
    }


    return $tabs;

}

function wpb_new_product_tab_content0() {
    // The new tab content
  global $post;
  $dia_content0   = get_post_meta( $post->ID, '_wcj_custom_product_tabs_content_local_2', true );
  echo $dia_content0;
}





/*

add_filter( 'cmb_meta_boxes', 'diaMed_core_cpt_metaboxes' );

function diaMed_core_cpt_metaboxes( $meta_boxes ) {

  //global $prefix;
  $prefix = '_diaMed_'; // Prefix for all fields

  // Add metaboxes to the 'Product' CPT
  $meta_boxes[] = array(
    'id'         => 'diaMed_woo_tabs_metabox',
    'title'      => 'Additional Product Information - <strong>Optional</strong>',
    'pages'      => array( 'product' ), // Which post type to associate with?
    'context'    => 'normal',
    'priority'   => 'default',
    'show_names' => true,
    'fields'     => array(
      array(
        'name'    => __( 'Ingredients', 'cmb' ),
        'desc'    => __( 'Anything you enter here will be displayed on the Ingredients tab.', 'cmb' ),
        'id'      => $prefix . 'ingredients_wysiwyg',
        'type'    => 'wysiwyg',
        'options' => array( 'textarea_rows' => 5, ),
      ),
      array(
        'name'    => __( 'Benefits', 'cmb' ),
        'desc'    => __( 'Anything you enter here will be displayed on the Benefits tab.', 'cmb' ),
        'id'      => $prefix . 'benefits_wysiwyg',
        'type'    => 'wysiwyg',
        'options' => array( 'textarea_rows' => 5, ),
      ),
    ),
  );

  return $meta_boxes;

}


add_filter( 'woocommerce_product_tabs', 'diaMed_woo_extra_tabs' );

	function diaMed_woo_extra_tabs( $tabs ) {

		global $post;
		$product_ingredients = get_post_meta( $post->ID, '_diaMed_ingredients_wysiwyg', true );
		$product_benefits    = get_post_meta( $post->ID, '_diaMed_benefits_wysiwyg', true );

		if ( ! empty( $product_ingredients ) ) {

			$tabs['ingredients_tab'] = array(
				'title'    => __( 'Ingredients', 'woocommerce' ),
				'priority' => 15,
				'callback' => 'diaMed_woo_ingredients_tab_content'
			);

		}

		if ( ! empty( $product_benefits ) ) {

			$tabs['benefits_tab'] = array(
				'title'    => __( 'Benefits', 'woocommerce' ),
				'priority' => 16,
				'callback' => 'diaMed_woo_benefits_tab_content'
			);

		}

		return $tabs;

	}

	function diaMed_woo_ingredients_tab_content() {

		global $post;
		$product_ingredients = get_post_meta( $post->ID, '_diaMed_ingredients_wysiwyg', true );

		if ( ! empty( $product_ingredients ) ) {

			echo '<h2>' . esc_html__( 'Product Ingredients', 'woocommerce' ) . '</h2>';

			// Updated to apply the_content filter to WYSIWYG content
			echo apply_filters( 'the_content', $product_ingredients );

		}

	}

	function diaMed_woo_benefits_tab_content() {

		global $post;
		$product_benefits = get_post_meta( $post->ID, '_diaMed_benefits_wysiwyg', true );

		if ( ! empty( $product_benefits ) ) {

			echo '<h2>' . esc_html__( 'Product Benefits', 'woocommerce' ) . '</h2>';

			// Updated to apply the_content filter to WYSIWYG content
			echo apply_filters( 'the_content', $product_benefits );

		}

	}
*/
