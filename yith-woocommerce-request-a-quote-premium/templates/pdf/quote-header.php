<?php

if( function_exists('icl_get_languages') ) {
    global $sitepress;
    $lang = get_post_meta( $order->id, 'wpml_language', true );
    YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}

$logo = get_option('ywraq_pdf_logo');
$user_email      = get_post_meta( $order->id, 'ywraq_customer_email', true );
$user_name       = get_post_meta( $order->id, 'ywraq_customer_name', true );

/* This stuff didnt work
$billing_address          = get_post_meta( $order->id, 'ywraq_billing_address', true );
$billing_phone            = get_post_meta( $order->id, 'ywraq_billing_phone', true );
$billing_vat              = get_post_meta( $order->id, 'ywraq_billing_vat', true );
*/
$billing_first_name =  get_post_meta($order->id, '_billing_first_name',true);
$billing_last_name = get_post_meta($order->id, '_billing_last_name',true);
$billing_company = get_post_meta($order->id, '_billing_company',true);
$billing_address = get_post_meta( $order->id, '_billing_address_1', true );
$billing_address2 = get_post_meta($order->id, '_billing_address_2',true);
$billing_city = get_post_meta($order->id, '_billing_city',true);
$billing_postcode = get_post_meta($order->id, '_billing_postcode',true);
$billing_country = get_post_meta($order->id, '_billing_country',true);
$billing_state = get_post_meta($order->id, '_billing_state',true);
$billing_email = get_post_meta($order->id, '_billing_email',true);
$billing_phone = get_post_meta($order->id, '_billing_phone',true);

$shipping_first_name =  get_post_meta($order->id, '_shipping_first_name',true);
$shipping_last_name = get_post_meta($order->id, '_shipping_last_name',true);
$shipping_company = get_post_meta($order->id, '_shipping_company',true);
$shipping_address = get_post_meta( $order->id, '_shipping_address_1', true );
$shipping_address2 = get_post_meta($order->id, '_shipping_address_2',true);
$shipping_city = get_post_meta($order->id, '_shipping_city',true);
$shipping_postcode = get_post_meta($order->id, '_shipping_postcode',true);
$shipping_country = get_post_meta($order->id, '_shipping_country',true);
$shipping_state = get_post_meta($order->id, '_shipping_state',true);
$shipping_phone = get_post_meta($order->id, '_shipping_phone',true);



$exdata = get_post_meta($order->id, '_ywcm_request_expire', true );
$expiration_data  = ( $exdata != '') ? date_i18n( wc_date_format(), strtotime( $exdata ) ): '';
$order_date       = date_i18n( wc_date_format(), strtotime( $order->order_date ) );

?>
<div style="width:60%; float:right;">
    <img style="width:340px; height:130px; "src=<?php echo $logo; ?> />
</div>
<div style="width:40%; float:left; margin-top:-8px">
  <ul style="list-style-type:none; line-height:18px;">
    <li style="list-style-type:none;"><strong>DiaMedical USA</strong></li>
    <li style="list-style-type:none;">5807 W. Maple, Suite #175</li>
    <li style="list-style-type:none;">West Bloomfield, MI 48322</li>
    <li style="list-style-type:none;">P. (248) 855-3966</li>
    <li style="list-style-type:none;">F. (248) 671-1550</li>
  </ul>
</div>

<div class="clear"></div>

<div style="float:left; width:46% margin:0 2%; border:1px solid #464444; height:153px;" class="admin_info">
  <p style="border-bottom:1px solid #464444; font-size:16px; margin-top:1px;">&nbsp;&nbsp;&nbsp;Bill To: </p>
                <p style="padding-left:10px;"><strong><?php echo $billing_first_name . ' ' . $billing_last_name; ?></strong><br>
                    <?php echo $user_email ?><br>
                    <?php
                    if( $billing_address != ''){
                        echo $billing_address.' '.$billing_address2.'<br>';
                    }
                    if( $billing_city != ''){
                      echo $billing_city;
                    }
                    if( $billing_state != ''){
                      echo ', ' . $billing_state.'<br>';
                    }
                    if( $billing_postcode != ''){
                      echo $billing_postcode.'<br>';
                    }

                    if( $billing_phone != ''){
                        echo $billing_phone.'<br>' ;
                    }

                    if( $billing_vat != ''){
                        echo $billing_vat.'<br>' ;
                    } ?>
                </p>
</div>

<div style="float:right; width:46% margin:0 2%; border:1px solid #464444; height:153px;" class="admin_info">
  <p style="border-bottom:1px solid #464444; font-size:16px; margin-top:1px;">&nbsp;&nbsp;&nbsp;Ship To: </p>
                <p style="padding-left:10px;"><strong><?php echo $shipping_first_name . ' ' . $shipping_last_name; ?></strong><br>
                    <?php echo $user_email ?><br>
                    <?php
                    if( $shipping_address != ''){
                        echo $shipping_address.' '.$shipping_address2.'<br>';
                    }
                    if( $shipping_city != ''){
                      echo $shipping_city;
                    }
                    if( $shipping_state != ''){
                      echo ', ' . $shipping_state.'<br>';
                    }
                    if( $shipping_postcode != ''){
                      echo $shipping_postcode.'<br>';
                    }

                    if( $shipping_phone != ''){
                        echo $shipping_phone.'<br>' ;
                    }
 ?>
                </p>
</div>


<div class="clear"></div>
<?php if ( $expiration_data != '' ): ?>
    <tr>
        <td valign="top" class="small-title"><?php echo __( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></td>
        <td valign="top" class="small-info">
            <p><strong><?php echo $expiration_data ?></strong></p>
        </td>
    </tr>
<?php endif ?>
<div class="clear"></div>

<div class="quote-title">
    <h2><?php printf( __( 'Quote #EC-%s', 'yith-woocommerce-request-a-quote' ), $order->id ) ?></h2>
</div>
