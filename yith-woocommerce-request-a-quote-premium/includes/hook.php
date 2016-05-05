<?php
if ( !defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements hooks for YITH Woocommerce Request A Quote Premium
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */

// Frontend hooks

add_filter('yith_ywraq-show_btn_single_page', 'yith_ywraq_show_button_in_single_page');
add_filter('yith_ywraq-btn_other_pages', 'yith_ywraq_show_button_in_other_pages', 10);

add_filter( 'yit_get_contact_forms', 'yith_ywraq_get_contact_forms'  );
add_filter( 'wpcf7_get_contact_forms', 'yith_ywraq_wpcf7_get_contact_forms' );
