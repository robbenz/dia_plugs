<?php
/*
 * Plugin Name: DiaMedical Product Categories - special fields
 * Plugin URI: http://www.robbenz.com
 * Description: Adds a field for SEO text at the bottom of each woocommerce category. and checkboxes for new sidebar menu - shop by facility type
 * Version: 1.0
 * Author: RobBenz
 * Author URI: http://www.robbenz.com
 * License: GPL2
 */

// hook into woocommerce product category page & new category
add_action( 'product_cat_add_form_fields', 'benz_taxonomy_edit_meta_field', 10, 2 );
add_action( 'product_cat_edit_form_fields', 'benz_taxonomy_edit_meta_field', 10, 2 );

function benz_taxonomy_edit_meta_field($term) {
	$dia_header_style ="color:#00426a; margin-bottom:4px; font-weight:800; font-size:1.4em;";

	// put the term ID into a variable
	$t_id = $term->term_id;

	// get value: Category Status Drop Down
	$cat_status = get_option( "product_cat_featured_$t_id" );

	// get value: Category Bottom SEO Description
	$term_meta = get_option( "taxonomy_$t_id" );

	// get value: Left Menu Checkboxes
  // hospital
  $_hospital_cbx = get_option("newside_hospital_cbx_$t_id");
  $hoscheck =''; if ($_hospital_cbx == 'yes') {$hoscheck = 'checked'; }
  // nursing
  $_nursing_cbx = get_option("newside_nursing_cbx_$t_id");
  $nscheck =''; if ($_nursing_cbx == 'yes') {$nscheck = 'checked'; }
  // Sim Lab Solutions
  $_sls_cbx = get_option("newside_sls_cbx_$t_id");
  $slscheck =''; if ($_sls_cbx == 'yes') {$slscheck = 'checked'; }
  // LTC
  $_ltc_cbx = get_option("newside_ltc_cbx_$t_id");
  $ltccheck =''; if ($_ltc_cbx == 'yes') {$ltccheck = 'checked'; }
  // EMS EDU
  $_emsedu_cbx = get_option("newside_emsedu_cbx_$t_id");
  $emseducheck =''; if ($_emsedu_cbx == 'yes') {$emseducheck = 'checked'; }
  // EMS FR
  $_emsfr_cbx = get_option("newside_emsfr_cbx_$t_id");
  $emsfrcheck =''; if ($_emsfr_cbx == 'yes') {$emsfrcheck = 'checked'; }
  // PT
  $_pt_cbx = get_option("newside_pt_cbx_$t_id");
  $ptcheck =''; if ($_pt_cbx == 'yes') {$ptcheck = 'checked'; }
  // VET
  $_vet_cbx = get_option("newside_vet_cbx_$t_id");
  $vetcheck =''; if ($_vet_cbx == 'yes') {$vetcheck = 'checked'; }

	?>

	<!-- markup for seo text -->
	<label style="<?php echo $dia_header_style ;?>" for="term_meta[custom_term_meta]">Bottom Description </label>
	<input
		type="text"
    style="width:100%;"
    name="term_meta[custom_term_meta]"
    id="term_meta[custom_term_meta]"
    class="megan-isa-diva"
    value="<?php echo esc_attr( $term_meta['custom_term_meta'] ) ? esc_attr( $term_meta['custom_term_meta'] ) : ''; ?>">

		<p class="description">This will go underneath the product gallery SEO text</p>

		<!-- markup for Category Status< -->
		<label style="<?php echo $dia_header_style ;?>">Category Status</label>
		<select style="width:300px;" name="featured" id="featured" class="postform">
      <option value="0">Select</option>
      <option <?= $cat_status=='Parts'?'selected':'' ?>   value="Parts">Parts</option>
      <option <?= $cat_status=='Equipment'?'selected':'' ?>   value="Equipment">Equipment</option>
      <option <?= $cat_status=='Repairs'?'selected':'' ?>   value="Repairs">Repairs</option>
    </select>
		<p class="description">Does this category contain Parts, Equipment, or Repairs?</p>

  <div style="width:300px; margin:15px 0 ;border: 3px solid #00426a;padding:1em;">
		<span style="<?php echo $dia_header_style ;?>">Left Menu Facility Options</span> <hr>
      <label><input type="checkbox" value="1" <?php echo $hoscheck; ?> name="newside_hospital_cbx" />Hospital</label><br>
      <label><input type="checkbox" value="1" <?php echo $nscheck; ?> name="newside_nursing_cbx" />Nursing Schools</label><br>
      <label><input type="checkbox" value="1" <?php echo $slscheck; ?> name="newside_sls_cbx" />SimLabSolutions.com</label><br>
      <label><input type="checkbox" value="1" <?php echo $ltccheck; ?> name="newside_ltc_cbx" />Long Term Care</label><br>
      <label><input type="checkbox" value="1" <?php echo $emseducheck; ?> name="newside_emsedu_cbx" />EMS Education</label><br>
      <label><input type="checkbox" value="1" <?php echo $emsfrcheck; ?> name="newside_emsfr_cbx" />EMS Field Ready</label><br>
      <label><input type="checkbox" value="1" <?php echo $ptcheck; ?> name="newside_pt_cbx" />Physical Therapy</label><br>
      <label><input type="checkbox" value="1" <?php echo $vetcheck; ?> name="newside_vet_cbx" />Veterinary</label><br>
		</div>

	<?php
}

// save that shit
add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );

function save_taxonomy_custom_meta( $term_id ) {
	// Save Value : SEO bottom Description
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		update_option( "taxonomy_$t_id", $term_meta );
	}

	// Save Value : Left Menu Checkboxes
	// hospital
  $hospital_cbx_value = isset( $_POST['newside_hospital_cbx'] ) ? 'yes' : 'no';
  $hospital_cbx_option = 'newside_hospital_cbx_' . $term_id;
  update_option( $hospital_cbx_option, $hospital_cbx_value );
  // Nursing
  $nursing_cbx_value = isset( $_POST['newside_nursing_cbx'] ) ? 'yes' : 'no';
  $nursing_cbx_option = 'newside_nursing_cbx_' . $term_id;
  update_option( $nursing_cbx_option, $nursing_cbx_value );
  // Sim Lab Solutions
  $sls_cbx_value = isset( $_POST['newside_sls_cbx'] ) ? 'yes' : 'no';
  $sls_cbx_option = 'newside_sls_cbx_' . $term_id;
  update_option( $sls_cbx_option, $sls_cbx_value );
  // Long Term Care
  $ltc_cbx_value = isset( $_POST['newside_ltc_cbx'] ) ? 'yes' : 'no';
  $ltc_cbx_option = 'newside_ltc_cbx_' . $term_id;
  update_option( $ltc_cbx_option, $ltc_cbx_value );
  // EMS EDU
  $emsedu_cbx_value = isset( $_POST['newside_emsedu_cbx'] ) ? 'yes' : 'no';
  $emsedu_cbx_option = 'newside_emsedu_cbx_' . $term_id;
  update_option( $emsedu_cbx_option, $emsedu_cbx_value );
  // EMS Field
  $emsfr_cbx_value = isset( $_POST['newside_emsfr_cbx'] ) ? 'yes' : 'no';
  $emsfr_cbx_option = 'newside_emsfr_cbx_' . $term_id;
  update_option( $emsfr_cbx_option, $emsfr_cbx_value );
  // Physical Therapy
  $pt_cbx_value = isset( $_POST['newside_pt_cbx'] ) ? 'yes' : 'no';
  $pt_cbx_option = 'newside_pt_cbx_' . $term_id;
  update_option( $pt_cbx_option, $pt_cbx_value );
  // Vet
  $vet_cbx_value = isset( $_POST['newside_vet_cbx'] ) ? 'yes' : 'no';
  $vet_cbx_option = 'newside_vet_cbx_' . $term_id;
  update_option( $vet_cbx_option, $vet_cbx_value );

	// Save Value : Category status Drop Down
  if ( isset( $_POST['featured'] ) ) {
    $option_name = 'product_cat_featured_' . $term_id;
    update_option( $option_name, $_POST['featured'] );
  }

}
