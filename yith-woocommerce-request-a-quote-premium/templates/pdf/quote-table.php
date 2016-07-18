  <?php
/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */

$border = true;

if( function_exists('icl_get_languages') ) {
    global $sitepress;
    $lang = get_post_meta( $order->id, 'wpml_language', true );
    YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}

?>

<?php if( ( $after_list = get_post_meta( $order->id, '_ywcm_request_response', true ) ) != ''): ?>
    <div class="after-list">
        <p><?php echo apply_filters( 'ywraq_quote_before_list', nl2br($after_list), $order->id ) ?></p>
    </div>
<?php endif; ?>

<?php do_action( 'yith_ywraq_email_before_raq_table', $order ); ?>

<div class="table-wrapper">
    <div class="mark"></div>
    <table class="quote-table" cellspacing="0" cellpadding="6" style="width: 100%;" border="0">
        <thead>
        <tr>
            <?php if( get_option('ywraq_show_preview') == 'yes'): ?>
                <th scope="col" style="text-align:left; border: 1px solid #464444;"><?php _e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
            <?php endif ?>
            <th scope="col" style="text-align:left; border: 1px solid #464444;"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #464444;"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #464444;"><?php _e( 'Unit Price', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #464444;"><?php _e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $items = $order->get_items();
        $colspan = 3;
        if( ! empty( $items ) ):

            foreach( $items as $item ):

                if( isset( $item['variation_id']) && $item['variation_id'] ){
                    $_product = wc_get_product( $item['variation_id'] );
                }else{
                    $_product = wc_get_product( $item['product_id'] );
                }

                $title = $_product->get_title();

                if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
                    $title .= apply_filters( 'ywraq_sku_label', __( ' SKU: ', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
                }

                $subtotal = wc_price( $item['line_total'] );

                if ( get_option( 'ywraq_show_old_price' ) == 'yes' ) {
                    $subtotal = ( $item['line_subtotal'] != $item['line_total'] ) ? '<small><del>' . wc_price( $item['line_subtotal'] ) . '</del></small> ' . wc_price( $item['line_total'] ) : wc_price( $item['line_subtotal'] );
                }

                $meta = yith_ywraq_get_product_meta_from_order_item( $item['item_meta'], false );

                ?>
                <tr>
                    <?php if( get_option('ywraq_show_preview') == 'yes'): ?>
                        <td scope="col" style="text-align:center;">
                            <?php
                            $thumbnail =  $_product->get_image( array(50,50));
                            $colspan = 4;
                            if ( ! $_product->is_visible() )
                                echo $thumbnail;
                            else
                                printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                            ?>
                        </td>

                    <?php endif ?>
                    <td scope="col" style="text-align:left; border-left: 1px solid #464444; border-right: 1px solid #464444"><?php echo $title ?>
                        <?php  if( $meta != '' ): ?><small><?php echo $meta; ?></small><?php endif ?></td>
                    <td scope="col" style="text-align:center; border-right: 1px solid #464444"><?php echo $item['qty'] ?></td>

                    <td scope="col" class="last-col" style="text-align:right;  border-right: 1px solid #464444"><?php
                    // echo $_product->get_price_html();
                    if ( isset( $item['line_total'] ) ) {
                      if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
                        echo '<del>' . wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_order_currency() ) ) . '</del> ';
                      }
                      echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_order_currency() ) );
                    }

                    ?></td>
                    <td scope="col" class="last-col" style="text-align:right;  border-right: 1px solid #464444"><?php
                    if ( isset( $item['line_total'] ) ) {
          						if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
          							echo '<del>' . wc_price( $item['line_subtotal'], array( 'currency' => $order->get_order_currency() ) ) . '</del> ';
          						}
          						echo wc_price( $item['line_total'], array( 'currency' => $order->get_order_currency() ) );
          					}

                    // echo apply_filters('ywraq_quote_subtotal_item', $order->get_formatted_line_subtotal( $item ), $item['line_total'], $_product);

                    ?></td>
                </tr>

            <?php endforeach; ?>

            <?php
            $shipping_fee   = $order->calculate_shipping();
            $order_total    = $order->get_formatted_order_total();
          //  $order_subtotal = $order->get_subtotal_to_display();
          ?>
        <!--  <tr>
            <th scope="col" colspan="<?php // echo $colspan ?>" style="text-align:right;border: 1px solid #464444;">Subtotal</th>
            <td scope="col" style="text-align:right;border: 1px solid #464444;"><?php // echo $order_subtotal ?></td>
          </tr> -->
          <tr>
            <th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;border: 1px solid #464444;">Shipping</th>
            <td scope="col" style="text-align:right;border: 1px solid #464444;">$<?php echo $shipping_fee ?></td>
          </tr>
          <tr>
            <th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;border: 1px solid #464444;">Total</th>
            <td scope="col" style="text-align:right;border: 1px solid #464444;"><?php echo $order_total ?></td>
          </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
<p>Quote valid for 45 days</p>
<?php if( get_option( 'ywraq_pdf_link' ) == 'yes'): ?>
<div style="height:26px; width:100%; margin-top:12px;text-align:center; background-color:#78be20; padding-top: 5.5px;">
  <?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ): ?>
    <a style="background-color:#78be20; color:#fff; margin-bottom:10px; text-decoration:none; font-weight:700;" href="<?php echo esc_url( add_query_arg( array( 'request_quote' => $order->id, 'status' => 'accepted', 'raq_nonce' => ywraq_get_token( 'accept-request-quote', $order->id, get_post_meta( $order->id, 'ywraq_customer_email', true ) ) ), YITH_Request_Quote()->get_raq_page_url() ) ) ?>" class="pdf-button"><?php ywraq_get_label('accept', true) ?></a></td>
  <?php endif; ?>
</div>
<?php endif ?>

<?php do_action( 'yith_ywraq_email_after_raq_table', $order ); ?>

<?php if ( ( $after_list = get_post_meta( $order->id, '_ywraq_request_response_after', true ) ) != '' ): ?>
    <div class="after-list">
        <p><?php echo apply_filters( 'ywraq_quote_after_list', nl2br( $after_list ), $order->id ) ?></p>
    </div>
<?php endif; ?>
