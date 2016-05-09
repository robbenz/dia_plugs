<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


//@since 1.4.4
return array(

	'quote' => array(

		'layout_general_settings'     => array(
			'name' => __( 'Quote settings', 'yith-woocommerce-request-a-quote' ),
			'type' => 'title',
			'id'   => 'ywraq_quote_settings'
		),

		'enable_order_creation' => array(
			'name'    => __( 'Enable order creation', 'yith-woocommerce-request-a-quote' ),
			'desc'    => __( 'If checked, the orders will be created. (Reccomended)', 'yith-woocommerce-request-a-quote' ),
			'id'      => 'ywraq_enable_order_creation',
			'type'    => 'checkbox',
			'default' => 'yes'
		),

		'show_accept_link' => array(
			'name'    => __( 'Show "Accept" link', 'yith-woocommerce-request-a-quote' ),
			'desc'    => __( 'If checked, "Accept" link will be shown in the quote', 'yith-woocommerce-request-a-quote' ),
			'id'      => 'ywraq_show_accept_link',
			'class'   => 'field_with_deps',
			'type'    => 'checkbox',
			'default' => 'yes'
		),

		'accept_link_label' => array(
			'name'    => __( 'Write in the text for your "Accept" link', 'yith-woocommerce-request-a-quote' ),
			'desc'    => '',
			'id'      => 'ywraq_accept_link_label',
			'type'    => 'text',
			'default' => __('Accept', 'yith-woocommerce-request-a-quote'),
		),

		'page_after_accept_quote' => array(
			'name'              => __( 'After-accept page', 'yith-woocommerce-request-a-quote' ),
			'desc'              => __( 'Select the page where to redirect your users after the quote has been accepted', 'yith-woocommerce-request-a-quote' ),
			'id'                => 'ywraq_page_accepted',
			'type'              => 'single_select_page',
			'default'           => get_option( 'woocommerce_checkout_page_id' ),
			'class'             => 'yith-ywraq-chosen',
			'css'               => 'min-width:300px',
			'desc_tip'          => false,
		),


		'show_reject_link' => array(
			'name'    => __( 'Show "Reject" link', 'yith-woocommerce-request-a-quote' ),
			'desc'    => __( 'If checked, "Reject" link will be shown in the quote', 'yith-woocommerce-request-a-quote' ),
			'id'      => 'ywraq_show_reject_link',
			'class'   => 'field_with_deps',
			'type'    => 'checkbox',
			'default' => 'yes'
		),

		'reject_link_label' => array(
			'name'              => __( 'Write in the text for your "Reject" link', 'yith-woocommerce-request-a-quote' ),
			'desc'              => '',
			'id'                => 'ywraq_reject_link_label',
			'custom_attributes' => array( 'data-deps' => 'ywraq_show_reject_link' ),
			'type'              => 'text',
			'default'           => __( 'Reject', 'yith-woocommerce-request-a-quote' ),
		),


		'layout_pdf_settings_end_form'             => array(
			'type'              => 'sectionend',
			'id'                => 'ywraq_quote_settings_end_form'
		),
	)
);
