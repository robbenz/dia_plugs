<?php
/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.4.4
 * @author  Yithemes
 */
?>
<?php if( ( $before_list = get_post_meta( $order->id, '_ywraq_request_response_before', true ) ) != ''): ?>
	<p><?php echo apply_filters( 'ywraq_quote_before_list', $before_list, $order->id ) ?></p>
<?php endif; ?>

<?php
$colspan = 3;

do_action( 'yith_ywraq_email_before_raq_table', $order );
?>
	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;border-collapse: collapse;">
		<thead>
		<tr>
			<?php if ( get_option( 'ywraq_show_preview' ) == 'yes' ): ?>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
			<?php endif ?>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Unit Price', 'yith-woocommerce-request-a-quote' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$items = $order->get_items();

		if ( ! empty( $items ) ):

			foreach ( $items as $item ):
				if( isset( $item['variation_id'] ) && $item['variation_id'] ){
					$_product = wc_get_product( $item['variation_id'] );
				}else{
					$_product = wc_get_product( $item['product_id'] );
				}

				$subtotal = wc_price( $item['line_total'] );

				if ( get_option( 'ywraq_show_old_price' ) == 'yes' ) {
					$subtotal = ( $item['line_subtotal'] != $item['line_total'] ) ? '<small><del>' . wc_price( $item['line_subtotal'] ) . '</del></small> ' . wc_price( $item['line_total'] ) : wc_price( $item['line_subtotal'] );
				}

				$meta = yith_ywraq_get_product_meta_from_order_item( $item['item_meta'], false );

				$title    = $_product->get_title();

				if ( $_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
					$title .= apply_filters( 'ywraq_sku_label', __( ' SKU: ', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
				}

				?>
				<tr>
					<?php if ( get_option( 'ywraq_show_preview' ) == 'yes' ):
						$colspan = 4;
						?>
						<td scope="col" style="text-align:center;border: 1px solid #eee;">
							<?php
							$thumbnail = $_product->get_image();

							if ( ! $_product->is_visible() ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
							}
							?>
						</td>
					<?php endif ?>

					<td scope="col" style="text-align:left;border: 1px solid #eee;">
						<a href="<?php echo $_product->get_permalink() ?>"><?php echo $title ?></a>
						<?php if ( $meta != '' ): ?>
							<small><?php echo $meta; ?></small><?php endif ?></td>
							<td scope="col" style="text-align:center;border: 1px solid #eee;"><?php echo $item['qty'] ?></td>
					<td scope="col" style="text-align:center;border: 1px solid #eee;"><?php echo $_product->get_price_html(); ?></td>
					<td scope="col" style="text-align:right;border: 1px solid #eee;"><?php echo apply_filters('ywraq_quote_subtotal_item', $order->get_formatted_line_subtotal( $item ), $item['line_total'], $_product); ?></td>

				</tr>

				<?php	endforeach; ?>

			<?php
			$shipping_fee = $order->calculate_shipping();
			$order_total = $order->get_formatted_order_total();
			$order_subtotal = $order->get_subtotal_to_display();

			?>
			<tr>
				<th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;border: 1px solid #eee;">Subtotal</th>
				<td scope="col" style="text-align:right;border: 1px solid #eee;"><?php echo $order_subtotal ?></td>
				</tr>
			<tr>
				<th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;border: 1px solid #eee;">Shipping</th>
				<td scope="col" style="text-align:right;border: 1px solid #eee;">$<?php echo $shipping_fee ?></td>
				</tr>
				<tr>
				<th scope="col" colspan="<?php echo $colspan ?>" style="text-align:right;border: 1px solid #eee;">Total</th>
				<td scope="col" style="text-align:right;border: 1px solid #eee;"><?php echo $order_total ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>

<?php do_action( 'yith_ywraq_email_after_raq_table', $order ); ?>
