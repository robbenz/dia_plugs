<?php
/**
 * Request A Quote Reject Request
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */


?>
<div class="ywraq-question-message">
    <?php
    if( isset($message) && $message != ''): ?>
       <p><?php echo  $message ?></p>
    <?php
    elseif( isset($confirm) && $confirm == 'no'):
        $args =array(
            'status' => 'rejected',
            'raq_nonce' => $raq_nonce,
            'request_quote' => $order_id,
            'confirm' => 'yes'
        );
        ?>
        <p><?php printf( __('Are you sure you want to reject quote No. %d?' , 'yith-woocommerce-request-a-quote'), $order_id ) ?></p>
        <p><a class="ywraq-button button" href="<?php echo  esc_url(add_query_arg( $args, YITH_Request_Quote()->get_raq_page_url() ) )?>" ><?php _e('Yes, I want to reject the quote', 'yith-woocommerce-request-a-quote') ?></a> <a class="ywraq-button button" href="<?php echo get_permalink( function_exists('wc_get_page_id') ? wc_get_page_id( 'shop' ) : woocommerce_get_page_id( 'shop' ) ) ?>" ><?php _e('Go to the Shop', 'yith-woocommerce-request-a-quote') ?></a> </p>
    <?php endif ?>
</div>