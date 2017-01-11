<?php
/*
add_action( 'create_product_cat', 'wpm_product_cat_details_meta_save' );
add_action( 'edit_product_cat', 'wpm_product_cat_details_meta_save' );
/**
 * Save Product Category details meta.
 *
 * Save the product_cat details meta POSTed from the
 * edit product_cat page or the add product_cat page.
 *
 * @param  int $term_id The term ID of the term to update.
 */
 /*
function wpm_product_cat_details_meta_save( $term_id ) {
	if ( ! isset( $_POST['wpm_product_cat_details_nonce'] ) || ! wp_verify_nonce( $_POST['wpm_product_cat_details_nonce'], basename( __FILE__ ) ) ) {
		return;
	}
	$old_details = get_term_meta( $term_id, 'details', true );
	$new_details = isset( $_POST['wpm-product-cat-details'] ) ? $_POST['wpm-product-cat-details'] : '';
	if ( $old_details && '' === $new_details ) {
		delete_term_meta( $term_id, 'details' );
	} else if ( $old_details !== $new_details ) {
		update_term_meta(
			$term_id,
			'details',
			wpm_sanitize_details( $new_details )
		);
	}
}
*/

function save_custom_meta_box($post_id, $post) {
  if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    $slug = "post";
    if($slug != $post->post_type)
        return $post_id;


    $dia_tab_count_value = "";

    $meta_box_text_value = "";
    $meta_box_dropdown_value = "";
    $meta_box_checkbox_value = "";


    if(isset($_POST["_wcj_custom_product_tabs_local_total_number"])) {
        $dia_tab_count_value = $_POST["_wcj_custom_product_tabs_local_total_number"];
    }

    update_post_meta($post_id, "_wcj_custom_product_tabs_local_total_number", $dia_tab_count_value);


    if(isset($_POST["meta-box-text"])) {
        $meta_box_text_value = $_POST["meta-box-text"];
    }

    update_post_meta($post_id, "meta-box-text", $meta_box_text_value);

    if(isset($_POST["meta-box-dropdown"])) {
        $meta_box_dropdown_value = $_POST["meta-box-dropdown"];
    }

    update_post_meta($post_id, "meta-box-dropdown", $meta_box_dropdown_value);

    if(isset($_POST["meta-box-checkbox"])) {
        $meta_box_checkbox_value = $_POST["meta-box-checkbox"];
    }

    update_post_meta($post_id, "meta-box-checkbox", $meta_box_checkbox_value);
}


add_action('save_post', 'save_custom_meta_box', 10, 3);
add_action('create_product', 'save_custom_meta_box', 10, 3);
add_action('edit_product', 'save_custom_meta_box', 10, 3);
