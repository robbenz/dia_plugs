<?php
/**
 * Table view to Request A Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */

$shop_url           = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : get_permalink( woocommerce_get_page_id( 'shop' ) );
$colspan = get_option( 'ywraq_hide_total_column', 'yes' ) == 'yes' ? '4' : '5';

if( count($raq_content) == 0):

//	echo ywraq_get_list_empty_message();
include ('request-quote-form.php');

else: ?>
    <form id="yith-ywraq-form" name="yith-ywraq-form" action="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ) ?>" method="post">
	<table class="shop_table cart" id="yith-ywrq-table-list" cellspacing="0">
        <thead>
            <tr>
                <th class="product-remove">&nbsp;</th>
                <th class="product-thumbnail">&nbsp;</th>
                <th class="product-name"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ) ?></th>
                <th class="product-quantity"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ) ?></th>
								<th class="product-unit-price"><?php _e( 'Unit Price', 'yith-woocommerce-request-a-quote' ); ?></th>

	            <?php if( get_option( 'ywraq_hide_total_column', 'yes' ) == 'no' ): ?>
                    <th class="product-subtotal"><?php _e( 'Total', 'yith-woocommerce-request-a-quote' ); ?></th>
	            <?php endif ?>
            </tr>
        </thead>
		<tbody>
	<?php foreach($raq_content as $key=>$raq):
		$_product = wc_get_product( ( isset( $raq['variation_id'] ) && $raq['variation_id'] != '' ) ? $raq['variation_id'] : $raq['product_id'] );
		$show_price = true;

		if ( $_product->product_type == 'composite' ) {
			$per_product_pricing = get_post_meta( $_product->id, '_per_product_pricing_bto', true );
			$show_price          = ( $per_product_pricing == 'yes' ) ? false : true;
		}

		do_action( 'ywraq_before_request_quote_view_item', $raq_content, $key );
    ?>
			<tr class="<?php echo esc_attr( apply_filters( 'yith_ywraq_item_class', 'cart_item', $raq_content, $key ) ); ?>"  <?php echo esc_attr( apply_filters( 'yith_ywraq_item_attributes', '', $raq_content, $key ) ); ?>>

				<td class="product-remove">
					<?php
						echo apply_filters( 'yith_ywraq_item_remove_link', sprintf( '<a href="#"  data-remove-item="%s" data-wp_nonce="%s"  data-product_id="%d" class="yith-ywraq-item-remove remove" title="%s">&times;</a>', $key, wp_create_nonce( 'remove-request-quote-' . $_product->id ), $_product->id,  __( 'Remove this item', 'yith-woocommerce-request-a-quote' ) ), $key );
					?>

				</td>

				<td class="product-thumbnail">
					<?php $thumbnail =  $_product->get_image();

					if ( ! $_product->is_visible() )
						echo $thumbnail;
					else
						printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
					?>
				</td>

				<td class="product-name">
					<?php
                        $title = $_product->get_title();

                        if( $_product->get_sku() != '' && get_option('ywraq_show_sku') == 'yes' ){
                            $title .= apply_filters( 'ywraq_sku_label', __( '<br /> Part Number: ', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
                        }
                    ?>
                    <a href="<?php echo $_product->get_permalink() ?>"><?php echo $title ?></a>
                    <?php
					// Meta data

					$item_data = array();

					// Variation data
					if ( ! empty( $raq['variation_id'] ) && is_array( $raq['variations'] ) ) {

						foreach ( $raq['variations'] as $name => $value ) {
                            $label = '';

							if ( '' === $value )
								continue;

							$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

							// If this is a term slug, get the term's nice name
							if ( taxonomy_exists( $taxonomy ) ) {
								$term = get_term_by( 'slug', $value, $taxonomy );
								if ( ! is_wp_error( $term ) && $term && $term->name ) {
									$value = $term->name;
								}
								$label = wc_attribute_label( $taxonomy );

							}else {

                                if( strpos( $name, 'attribute_') !== false ) {
                                    $custom_att = str_replace( 'attribute_', '', $name );

                                    if ( $custom_att != '' ) {
                                        $label = wc_attribute_label( $custom_att );
                                    }
                                    else {
                                        $label = $name;
                                    }
                                }

                            }

							$item_data[] = array(
							//	'key'   => $label,
								'value' => $value
							);
						}
					}

					$item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data , $raq , $_product);


					// Output flat or in list format
					if ( sizeof( $item_data ) > 0 ) {
							foreach ( $item_data as $data ) {
								echo esc_html( $data['key'] ) . '<br />' . wp_kses_post( $data['value'] ) . "\n";
							}
						}


					?>
				</td>


				<td class="product-quantity">
					<?php
						$product_quantity = woocommerce_quantity_input( array(
                            'input_name'  => "raq[{$key}][qty]",
							'input_value' => apply_filters('ywraq_quantity_input_value', $raq['quantity'] ),
							'max_value'   => apply_filters('ywraq_quantity_max_value', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product ),
							'min_value'   => apply_filters('ywraq_quantity_min_value', 0, $_product)
						), $_product, false );

					    echo $product_quantity;
					?>
				</td>
				<?php if (is_user_logged_in() ): ?>
				                <td class="product-unit-price">
				                    <?php

														$benzy_price = WC()->cart->get_product_price( $_product, $raq['price'] );
														echo str_replace("$0.00", "Preparing Quote", $benzy_price );
				                    ?>
				                </td>

				<td class="product-subtotal">
				                    <?php
														$sub_benzy_price = WC()->cart->get_product_subtotal( $_product, $raq['quantity'] );
														echo str_replace("$0.00", " ", $sub_benzy_price );
				                    ?>
				                </td>
				<?php else: ?>
				<td colspan="1.5"  class="product-unit-price">

				<div style="margin:0 !important; width:120px !important; float:right;" id="viewprice-detail-search"><a href="#" class="eModal-1">View Price</a></div>

				</td>
				<?php endif; ?>

			</tr>
			<?php do_action( 'ywraq_after_request_quote_view_item', $raq_content, $key ); ?>
	<?php endforeach ?>
			<?php if( $show_price): ?>
			<tr>
				<td colspan="6" class="actions">
					<a class="button wc-backward" href="<?php echo apply_filters( 'yith_ywraq_return_to_shop_url' ,$shop_url ); ?>"><?php _e('Shop More Medical Equipment', 'yith-woocommerce-request-a-quote') ?></a>
					<input type="submit" class="button" name="update_raq" value="<?php _e('Update List', 'yith-woocommerce-request-a-quote') ?>">
					<input type="hidden" id="update_raq_wpnonce" name="update_raq_wpnonce" value="<?php echo wp_create_nonce( 'update-request-quote-quantity' ) ?>">
				</td>
			</tr>
			<?php endif ?>

			</tbody>
	</table>
    </form>
	<?php endif ?>
