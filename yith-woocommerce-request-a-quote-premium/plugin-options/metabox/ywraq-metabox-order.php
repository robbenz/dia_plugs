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

$customer_name            = '';
$customer_message         = '';
$customer_email           = '';
$additional_field         = '';
$additional_field_2       = '';
$additional_field_3       = '';
$additional_email_content = '';
$customer_attachment      = '';
$status                   = '';
$button_disabled          = '';
$pdf_file                 = '';
$attachment_text          = '';

$billing_address = '';
$billing_phone   = '';
$billing_vat     = '';


if ( isset( $_REQUEST['post'] ) ) {

    $customer_name            = get_post_meta( $_REQUEST['post'], 'ywraq_customer_name', true );
    $customer_message         = get_post_meta( $_REQUEST['post'], 'ywraq_customer_message', true );
    $request_response         = get_post_meta( $_REQUEST['post'], 'ywraq_request_response', true );
    $request_response_after   = get_post_meta( $_REQUEST['post'], 'ywraq_request_response_after', true );
    $customer_email           = get_post_meta( $_REQUEST['post'], 'ywraq_customer_email', true );
    $additional_field         = get_post_meta( $_REQUEST['post'], 'ywraq_customer_additional_field', true );
    $additional_field_2       = get_post_meta( $_REQUEST['post'], 'ywraq_customer_additional_field_2', true );
    $additional_field_3       = get_post_meta( $_REQUEST['post'], 'ywraq_customer_additional_field_3', true );
    $customer_attachment      = get_post_meta( $_REQUEST['post'], 'ywraq_customer_attachment', true );
    $additional_email_content = get_post_meta( $_REQUEST['post'], 'ywraq_other_email_content', true );
    $billing_address          = get_post_meta( $_REQUEST['post'], 'ywraq_billing_address', true );
    $billing_phone            = get_post_meta( $_REQUEST['post'], 'ywraq_billing_phone', true );
    $billing_vat              = get_post_meta( $_REQUEST['post'], 'ywraq_billing_vat', true );


    if( $customer_name != ''){
        $additional_email_content .= sprintf('<strong>%s</strong>: %s</br></br>', __('Customer Name', 'yith-woocommerce-request-a-quote'), $customer_name) ;
    }
    if( $customer_email != ''){
        $additional_email_content .= sprintf('<strong>%s</strong>: <a href="mailto:%s">%s</a></br>', __('Email Address', 'yith-woocommerce-request-a-quote'), $customer_email, $customer_email) ;
    }

    if( $billing_address != ''){
        $additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Address', 'yith-woocommerce-request-a-quote'), $billing_address) ;
    }

    if( $billing_phone != ''){
        $additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Phone', 'yith-woocommerce-request-a-quote'), $billing_phone) ;
    }

    if( $billing_vat != ''){
        $additional_email_content .= sprintf('<strong>%s</strong>: %s</br>', __('Billing Vat', 'yith-woocommerce-request-a-quote'), $billing_vat) ;
    }

    if( $customer_message != ''){
        $customer_message =  '<strong>'. __( 'Message', 'yith-woocommerce-request-a-quote' ). '</strong>: '.  $customer_message;
    }

    if( $additional_field != ''){
        $additional_field =  '<strong>'. get_option('ywraq_additional_text_field_label') .'</strong>: '. $additional_field;
    }

    if( $additional_field_2 != ''){
        $additional_field_2 =  '<strong>'. get_option('ywraq_additional_text_field_label_2') .'</strong>: '. $additional_field_2;
    }

    if( $additional_field_3 != ''){
        $additional_field_3 =  '<strong>'. get_option('ywraq_additional_text_field_label_3') .'</strong>: '. $additional_field_3;
    }

    if ( !empty( $customer_attachment ) && isset( $customer_attachment['url'] ) ) {
        $attachment_text = '<strong>' . __( 'Attachment', 'yith-woocommerce-request-a-quote' ) . '</strong>:  <a href="' . $customer_attachment['url'] . '" target="_blank">' . $customer_attachment['url'] . '</a>';
    }

    if ( !empty( $customer_attachment ) && isset( $customer_attachment['url'] ) ) {
        $attachment_text = '<strong>' . __( 'Attachment', 'yith-woocommerce-request-a-quote' ) . '</strong>:  <a href="' . $customer_attachment['url'] . '" target="_blank">' . $customer_attachment['url'] . '</a>';
    }

    $order = wc_get_order($_REQUEST['post']);

    if ( !empty( $order ) ) {
        $status = $order->get_status();
        if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) && ! $order->has_status( array( 'ywraq-new', 'ywraq-rejected' ) ) ) {
            $button_disabled = 'disabled="disabled"';
        }
        if ( file_exists( YITH_Request_Quote_Premium()->get_pdf_file_path( $order->id ) ) ) {
            $pdf_file = YITH_Request_Quote_Premium()->get_pdf_file_url( $order->id );
        }
    }
}

$order_meta = array(
    'label'    => __( 'Request a Quote Order Settings', 'yith-woocommerce-request-a-quote' ),
    'pages'    => 'shop_order', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'high',
    'tabs'     => array(
        'settings' => array(
            'label'  => __( 'Settings', 'yith-woocommerce-request-a-quote' ),
            'fields' => apply_filters( 'ywraq_order_metabox', array(

                    'ywraq_current_status_title' => array(
                        'desc'  => sprintf( __('Current Status: <span>%s</span>', 'yith-woocommerce-request-a-quote'), wc_get_order_status_name($status) ),
                        'type'  => 'title'
                    ),

                    'ywraq_customer_message_title' => array(
                        'desc'  => __( 'Customer\'s request', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'title'
                    ),

                    'ywraq_customer_message' => array(
                        'label' => __( 'Customer\'s message', 'yith-woocommerce-request-a-quote' ),
                        'desc'  =>  $customer_message,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_additional_email_content' => array(
                        'label' => __( 'Additional email content', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => $additional_email_content,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_additional_field' => array(
                        'label' => __( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => $additional_field,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_additional_field_2' => array(
                        'label' => __( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => $additional_field_2,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_additional_field_3' => array(
                        'label' => __( 'Customer\'s additional field', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => $additional_field_3,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_attachment' => array(
                        'label' => __( 'Customer\'s attachment', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => $attachment_text,
                        'type'  => 'simple-text'
                    ),

                    'ywraq_customer_sep' => array(
                        'type'  => 'sep'
                    ),

                    //@since 1.3.0
                    'ywcm_request_response' => array(
                        'label' => __( 'Attach message to the quote before the table list (optional)', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'textarea',
                        'desc'  => __( 'Write a message that will be attached to the quote', 'yith-woocommerce-request-a-quote' ),
                        'std'   => '' ),

                    //@since 1.3.0
                    'ywraq_request_response_after' => array(
                        'label' => __( 'Attach message to the quote after the table list (optional)', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'textarea',
                        'desc'  => __( 'Write a message that will be attached to the quote after the list', 'yith-woocommerce-request-a-quote' ),
                        'std'   => '' ),

                    //@since 1.3.0
                    'ywraq_optional_attachment' => array(
                        'label' => __( 'Optional Attachment', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'upload',
                        'desc'  => __( 'Use this field to add additional attachment to the email', 'yith-woocommerce-request-a-quote' ),
                        'std'   => '' ),

                    'ywcm_request_expire' => array(
                        'label' => __( 'Expire date (optional)', 'yith-woocommerce-request-a-quote' ),
                        'desc'  => __( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'datepicker',
                        'std'   => '' ),

                    'ywraq_safe_submit_field' => array(
                        'desc'  => __( 'Set an expiration date for this quote', 'yith-woocommerce-request-a-quote' ),
                        'type'  => 'hidden',
                        'std'   => '',
                        'val'   => ''
                    ),
                )
            )
        )
    )
);

if( $pdf_file != ''){
    $order_meta['tabs']['settings']['fields']['ywraq_pdf_file'] =  array(
        'desc'  => '<strong>PDF QUOTE: </strong>  <a href="'.$pdf_file.'" target="_blank">'.$pdf_file.'</a>',
        'type'  => 'simple-text'
    );
}

$order_meta['tabs']['settings']['fields']['ywraq_submit_button'] =  array(
    'desc'  => '<input type="submit" class="button button-primary" id="ywraq_submit_button" value="'.__('Send Quote','yith-woocommerce-request-a-quote').'" '.$button_disabled.'>',
    'type'  => 'simple-text'
);
return $order_meta;