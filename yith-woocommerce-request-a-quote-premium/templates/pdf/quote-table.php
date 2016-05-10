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
                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
            <?php endif ?>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
            <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $items = $order->get_items();
        $colspan = 2;
        if( ! empty( $items ) ):

            foreach( $items as $item ):

                if( isset( $item['variation_id']) && $item['variation_id'] ){
                    $_product = wc_get_product( $item['variation_id'] );
                }else{
                    $_product = wc_get_product( $item['product_id'] );
                }

                $title = $_product->get_title();

                if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
                    $title .= apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
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
                            $colspan = 3;
                            if ( ! $_product->is_visible() )
                                echo $thumbnail;
                            else
                                printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                            ?>
                        </td>
                    <?php endif ?>
                    <td scope="col" style="text-align:left; border-left: 1px solid #eee"><?php echo $title ?>
                        <?php  if( $meta != '' ): ?><small><?php echo $meta; ?></small><?php endif ?></td>
                    <td scope="col" style="text-align:center;"><?php echo $item['qty'] ?></td>

                    <td scope="col" class="last-col" style="text-align:right;  border-right: 1px solid #eee"><?php echo apply_filters('ywraq_quote_subtotal_item', $order->get_formatted_line_subtotal( $item ), $item['line_total'], $_product); ?></td>
                </tr>

            <?php
            endforeach; ?>

            <?php
            foreach ( $order->get_order_item_totals() as $key => $total ) {
                ?>
                    <tr>
                        <th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;"><?php echo $total['label']; ?></th>
                        <td scope="col" class="last-col" style="text-align:right;"><?php echo $total['value']; ?></td>
                    </tr>
                <?php    } ?>
        <?php endif; ?>


        </tbody>
    </table>
</div>
<?php if( get_option( 'ywraq_pdf_link' ) == 'yes'): ?>
<div>
    <table>
        <tr>
            <?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ): ?>
            <td><a href="<?php echo esc_url( add_query_arg( array( 'request_quote' => $order->id, 'status' => 'accepted', 'raq_nonce' => ywraq_get_token( 'accept-request-quote', $order->id, get_post_meta( $order->id, 'ywraq_customer_email', true ) ) ), YITH_Request_Quote()->get_raq_page_url() ) ) ?>" class="pdf-button"><?php ywraq_get_label('accept', true) ?></a></td>
            <?php endif;
            echo ( get_option( 'ywraq_show_accept_link' ) != 'no' && get_option( 'ywraq_show_reject_link' ) != 'no' ) ? '<td><span style="color: #666666">|</span></td>' : '';
            if ( get_option( 'ywraq_show_reject_link' ) != 'no' ): ?>
            <td><a href="<?php echo esc_url( add_query_arg( array( 'request_quote' => $order->id, 'status' => 'rejected', 'raq_nonce' => ywraq_get_token( 'reject-request-quote', $order->id, get_post_meta( $order->id, 'ywraq_customer_email', true ) ) ), YITH_Request_Quote()->get_raq_page_url() ) ) ?>" class="pdf-button"><?php ywraq_get_label('reject', true) ?></a></td>
            <?php endif ?>
        </tr>
    </table>
</div>
<?php endif ?>
  
<?php do_action( 'yith_ywraq_email_after_raq_table', $order ); ?>

<?php if ( ( $after_list = get_post_meta( $order->id, '_ywraq_request_response_after', true ) ) != '' ): ?>
    <div class="after-list">
        <p><?php echo apply_filters( 'ywraq_quote_after_list', nl2br( $after_list ), $order->id ) ?></p>
    </div>
<?php endif; ?>