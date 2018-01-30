<?php
/**
 * HTML Template Email Send Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.1.8
 * @author  Yithemes
 */

do_action( 'woocommerce_email_header', $email_heading, $email );

$args_accept = array(
    'request_quote' => $raq_data['order-id'], 'status' => 'accepted',
    'raq_nonce'     => ywraq_get_token( 'accept-request-quote', $raq_data['order-id'], $raq_data['user_email'] )
);

$args_reject = array(
    'request_quote' => $raq_data['order-id'],
    'status'        => 'rejected',
    'raq_nonce'     => ywraq_get_token( 'reject-request-quote', $raq_data['order-id'], $raq_data['user_email'] )
);

if( isset( $raq_data['lang']) ){
    $args_accept['lang'] = $raq_data['lang'];
    $args_reject['lang'] = $raq_data['lang'];
}
?>

<h2><?php printf( __( 'Click Accept to Order Quote #EC-%s Instantly', 'yith-woocommerce-request-a-quote' ), $order->id ) ?></h2>
<span style="font-size:14px; font-weight:bolder;">Please Note: </span>
<span style="font-size:14px;">Shipping has been calculated and added to your total. Shipping will be applied at checkout. </span>
<br />
<span style="font-size:14px; font-weight:bolder;">Please Note: </span>
<span style="font-size:14px;">You must be logged in to your DiaMedicalUSA.com account for this link to work.</span>

    <!-- <p> // echo $email_description; </p> -->

<?php if( get_option('ywraq_hide_table_is_pdf_attachment') == 'no'): ?>
    <p><strong><?php _e( 'Request date', 'yith-woocommerce-request-a-quote' ) ?></strong>: <?php echo $raq_data['order-date'] ?></p>
    <?php if ( $raq_data['expiration_data'] != '' ): ?>
        <p><strong><?php _e( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></strong>: <?php echo $raq_data['expiration_data'] ?></p>
    <?php endif ?>

    <?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ): ?>
        <div style="height:26px; width:100%; background-color:#78be20; text-align:center; margin-bottom:8px;"><a style="color:#fff; text-decoration:none; font-weight:700; font-size:15px;" href="<?php echo esc_url( add_query_arg( $args_accept, YITH_Request_Quote()->get_raq_page_url() ) ) ?>"><?php ywraq_get_label( 'accept', true ) ?></a></div>
    <?php endif;  ?>

    <?php if ( !empty( $raq_data['admin_message'] ) ): ?>
        <p><?php echo $raq_data['admin_message'] ?></p>
    <?php endif ?>

<?php
    wc_get_template( 'emails/quote-table.php', array(
        'order' => $order
    ) );
?>
    <p></p>
    <?php endif ?>

    <p>
          <?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ): ?>
              <div style="height:26px; width:100%; background-color:#78be20; text-align:center; "><a style="color:#fff; text-decoration:none; font-weight:700; font-size:15px;" href="<?php echo esc_url( add_query_arg( $args_accept, YITH_Request_Quote()->get_raq_page_url() ) ) ?>"><?php ywraq_get_label( 'accept', true ) ?></a></div>
          <?php endif;

          if ( get_option( 'ywraq_show_reject_link' ) != 'no' ): ?>
              <a style="margin-top:4px; color:#d6001c;" href="<?php echo site_url(); ?>/quote-rejected/&ccf_field_email-9=jsmith@email.com">No Thank You</a>
          <?php endif; ?>
    </p>

    <?php if( ( $after_list = get_post_meta( $order->id, '_ywraq_request_response_after', true ) ) != ''): ?>
        <p><?php echo apply_filters( 'ywraq_quote_after_list', nl2br( $after_list ), $order->id ) ?></p>
    <?php endif; ?>

    <h2><?php _e( 'Customer\'s details', 'yith-woocommerce-request-a-quote' ); ?></h2>

    <p><strong><?php
    $billing_first_name =  get_post_meta($order->id, '_billing_first_name',true);
    $billing_last_name = get_post_meta($order->id, '_billing_last_name',true);
    _e( 'Name:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $billing_first_name . ' ' . $billing_last_name; ?></p>

    <p><strong><?php _e( 'Email:', 'yith-woocommerce-request-a-quote' ); ?></strong>
      <a href="mailto:<?php echo $raq_data['user_email']; ?>"><?php echo $raq_data['user_email']; ?></a></p>

<?php
$billing_first_name  = get_post_meta($order->id, '_billing_first_name',true);
$billing_last_name   = get_post_meta($order->id, '_billing_last_name',true);
$billing_company     = get_post_meta($order->id, '_billing_company',true);
$billing_address     = get_post_meta($order->id, '_billing_address_1', true );
$billing_address2    = get_post_meta($order->id, '_billing_address_2',true);
$billing_city        = get_post_meta($order->id, '_billing_city',true);
$billing_postcode    = get_post_meta($order->id, '_billing_postcode',true);
$billing_country     = get_post_meta($order->id, '_billing_country',true);
$billing_state       = get_post_meta($order->id, '_billing_state',true);
$billing_email       = get_post_meta($order->id, '_billing_email',true);
$billing_phone       = get_post_meta($order->id, '_billing_phone',true);

if( $billing_company != ''): ?>
    <p><strong><?php _e( 'Company:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $billing_company; ?></p>
<?php endif;

if( $billing_address != ''): ?>
    <p><strong><?php _e( 'Address:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $billing_address . ' ' . $billing_address2; ?></p>
<?php endif;

if( $billing_city != ''): ?>
    <p><?php echo $billing_city . ', ' . $billing_state; ?></p>
<?php endif;

if( $billing_postcode != ''): ?>
    <p><?php echo $billing_postcode; ?></p>
<?php endif;

if( $billing_phone != ''): ?>
    <p><strong><?php _e( 'Billing Phone:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $billing_phone ?></p>
<?php endif;

$af1 = get_post_meta( $order->id, 'ywraq_customer_additional_field', true );
if( ! empty( $af1 ) ){
    printf( '<p><strong>%s</strong>: %s</p>', get_option('ywraq_additional_text_field_label'), get_post_meta( $order->id, 'ywraq_customer_additional_field', true ) );
}

$af2 = get_post_meta( $order->id, 'ywraq_customer_additional_field_2', true );
if( ! empty( $af2 ) ){
    printf( '<p><strong>%s</strong>: %s</p>', get_option('ywraq_additional_text_field_label_2'), get_post_meta( $order->id, 'ywraq_customer_additional_field_2', true ) );
}

$af3 = get_post_meta( $order->id, 'ywraq_customer_additional_field_3', true );
if( ! empty( $af3 ) ){
    printf( '<p><strong>%s</strong>: %s</p>', get_option('ywraq_additional_text_field_label_3'), get_post_meta( $order->id, 'ywraq_customer_additional_field_3', true ) );
}

$af4 = get_post_meta( $order->id, 'ywraq_customer_other_email_content', true );
if( ! empty( $af4 ) ){
    printf( '<p>%s</p>', $af4);
}

do_action( 'woocommerce_email_footer', $email); ?>
