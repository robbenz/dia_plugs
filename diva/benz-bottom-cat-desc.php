<?php
/*
 * Plugin Name: Megan is a Diva
 * Plugin URI: http://www.robbenz.com
 * Description: Megan is a Diva and needs SEO text at the bottom of each woocommerce category. -- GEEZE
 * Version: 1.0
 * Author: RobBenz
 * Author URI: http://www.robbenz.com
 * License: GPL2
 */


// Add term page
function benz_taxonomy_add_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[custom_term_meta]"><?php _e( 'Bottom Description', 'benz' ); ?></label>
        <input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
		<p class="description"><?php _e( 'This will go underneath the gallery pages. Because I said so','benz' ); ?></p>
	</div>
<?php
}
add_action( 'product_cat_add_form_fields', 'benz_taxonomy_add_new_meta_field', 10, 2 );


// Edit term page
function benz_taxonomy_edit_meta_field($term) {

	// put the term ID into a variable
	$t_id = $term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top">
    <label
      style="font-weight:800; font-size:1.4em;"
      for="term_meta[custom_term_meta]">
      <?php _e( 'Bottom Description', 'benz' ); ?>
    </label>
  </th>
		<td>
      <input
        type="text"
        style="width:100%;"
        name="term_meta[custom_term_meta]"
        id="term_meta[custom_term_meta]"
        class="megan-isa-diva"
        value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">

      <p class="description">
        <?php _e( 'This will go underneath the gallery pages. Because I said so','benz' ); ?>
      </p>

		</td>
	</tr>

<?php
}
add_action( 'product_cat_edit_form_fields', 'benz_taxonomy_edit_meta_field', 10, 2 );

function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}
add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
