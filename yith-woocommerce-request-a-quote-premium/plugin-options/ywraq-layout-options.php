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



return array(

	'ywraq-layout' => array(


		'layout_general_settings'     => array(
			'name' => __( 'Layout settings', 'yith-woocommerce-request-a-quote' ),
			'type' => 'title',
			'id'   => 'ywraq_layout_settings'
		),

        'show_btn_link' => array(
            'name'    => __( 'Button type', 'yith-woocommerce-request-a-quote' ),
            'desc'    => '',
            'id'      => 'ywraq_show_btn_link',
            'type'    => 'select',
            'options' => array(
                'link'   => __( 'Link', 'yith-woocommerce-request-a-quote' ),
                'button' => __( 'Button', 'yith-woocommerce-request-a-quote' ),
            ),
            'default' => 'button',
        ),

        'show_btn_link_text' => array(
            'name'    => __( 'Button/Link text', 'yith-woocommerce-request-a-quote' ),
            'desc'    => '',
            'id'      => 'ywraq_show_btn_link_text',
            'type'    => 'text',
            'default' => __('Add to quote', 'yith-woocommerce-request-a-quote'),
        ),


        'layout_settings_button_bg_color'       => array(
            'name'    => __( 'Button background color', 'yith-woocommerce-request-a-quote' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywraq_layout_button_bg_color',
            'default' => '#0066b4'
        ),

        'layout_settings_button_bg_color_hover'       => array(
            'name'    => __( 'Button background color on hover ', 'yith-woocommerce-request-a-quote' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywraq_layout_button_bg_color_hover',
            'default' => '#044a80'
        ),

        'layout_settings_button_color'          => array(
            'name'    => __( 'Button/Link text color', 'yith-woocommerce-request-a-quote' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywraq_layout_button_color',
            'default' => '#fff'
        ),

        'layout_settings_button_color_hover'          => array(
            'name'    => __( 'Button/Link text color hover', 'yith-woocommerce-request-a-quote' ),
            'type'    => 'color',
            'desc'    => '',
            'id'      => 'ywraq_layout_button_color_hover',
            'default' => '#fff'
        ),

        'layout_general_settings_end_form'             => array(
            'type'              => 'sectionend',
            'id'                => 'ywraq_layout_settings_end_form'
        ),


        //@since 1.1.6
        'layout_data_settings'     => array(
            'name' => __( 'Data Settings', 'yith-woocommerce-request-a-quote' ),
            'type' => 'title',
            'id'   => 'ywraq_data_settings'
        ),

        //@since 1.1.6
        'show_sku' => array(
            'name'    => __( 'Show SKU on list table', 'yith-woocommerce-request-a-quote' ),
            'desc'    => __( 'If checked, the sku will be added near the title of product in the request list', 'yith-woocommerce-request-a-quote' ),
            'id'      => 'ywraq_show_sku',
            'type'    => 'checkbox',
            'default' => 'no'
        ),

        //@since 1.1.6
        'show_preview' => array(
            'name'    => __( 'Show preview thumbnail on email list table', 'yith-woocommerce-request-a-quote' ),
            'desc'    => __( 'If checked, the thumbnail will be added in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
            'id'      => 'ywraq_show_preview',
            'type'    => 'checkbox',
            'default' => 'no'
        ),

        //@since 1.3.0
        'show_old_price' => array(
            'name'    => __( 'Show old price on list table', 'yith-woocommerce-request-a-quote' ),
            'desc'    => __( 'If checked, the old price will be showed in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
            'id'      => 'ywraq_show_old_price',
            'type'    => 'checkbox',
            'default' => 'yes'
        ),

        //@since 1.1.6
        'layout_data_settings_end_form'             => array(
            'type'              => 'sectionend',
            'id'                => 'ywraq_layout_data_end_form'
        ),


    )
);