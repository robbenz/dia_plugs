<?php

if( function_exists('icl_get_languages') ) {
    global $sitepress;
    $lang = get_post_meta( $order->id, 'wpml_language', true );
    YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}

$logo = get_option('ywraq_pdf_logo');
$user_email      = get_post_meta( $order->id, 'ywraq_customer_email', true );
$user_name       = get_post_meta( $order->id, 'ywraq_customer_name', true );

$billing_address          = get_post_meta( $order->id, 'ywraq_billing_address', true );
$billing_phone            = get_post_meta( $order->id, 'ywraq_billing_phone', true );
$billing_vat              = get_post_meta( $order->id, 'ywraq_billing_vat', true );

$exdata = get_post_meta($order->id, '_ywcm_request_expire', true );
$expiration_data  = ( $exdata != '') ? date_i18n( wc_date_format(), strtotime( $exdata ) ): '';
$order_date       = date_i18n( wc_date_format(), strtotime( $order->order_date ) );

?>
<div class="logo">
    <img src=<?php echo $logo ?>>
</div>
<div class="admin_info right">
    <table>
        <tr>
            <td valign="top" class="small-title"><?php echo __( 'From', 'yith-woocommerce-request-a-quote' ) ?></td>
            <td valign="top" class="small-info">
                <p><?php echo nl2br( get_option( 'ywraq_pdf_info' ) ) ?></p>
            </td>
        </tr>
        <tr>
            <td valign="top" class="small-title"><?php echo __( 'Customer', 'yith-woocommerce-request-a-quote' ) ?></td>
            <td valign="top" class="small-info">
                <p><strong><?php echo $user_name ?></strong><br>
                    <?php echo $user_email ?><br>
                    <?php
                    if( $billing_address != ''){
                        echo $billing_address.'<br>';
                    }

                    if( $billing_phone != ''){
                        echo $billing_phone.'<br>' ;
                    }

                    if( $billing_vat != ''){
                        echo $billing_vat.'<br>' ;
                    } ?>
                </p>
            </td>
        </tr>
        <?php if ( $expiration_data != '' ): ?>
            <tr>
                <td valign="top" class="small-title"><?php echo __( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></td>
                <td valign="top" class="small-info">
                    <p><strong><?php echo $expiration_data ?></strong></p>
                </td>
            </tr>
        <?php endif ?>
    </table>
</div>
<div class="clear"></div>
<div class="quote-title">
    <h2><?php printf( __( 'Quote #EC-%s', 'yith-woocommerce-request-a-quote' ), $order->id ) ?></h2>
</div>
