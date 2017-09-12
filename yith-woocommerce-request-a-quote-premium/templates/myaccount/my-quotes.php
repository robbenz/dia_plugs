<?php
/**
 * My Quotes
 *
 * Shows recent orders on the account page
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !apply_filters( 'yith_ywraq_before_print_my_account_my_quotes', true ) ) {
    return;
}

$customer_quotes = get_posts( apply_filters( 'ywraq_my_account_my_quotes_query', array(
  'numberposts' => 15,
  'meta_query'  => array(
    array(
      'key'     => 'ywraq_raq',
      'compare' => 'EXISTS',
    ),
    array(
      'key'   => '_customer_user',
      'value' => get_current_user_id()
    ),
  ),
  'post_type'   => 'shop_order',
  'post_status' => array_merge( YITH_YWRAQ_Order_Request()->raq_order_status, array_keys( wc_get_order_statuses() ) )
  )
  )
);

if ( $customer_quotes ) : ?>
<h2><?php echo apply_filters( 'ywraq_my_account_my_quotes_title', __( 'Recent Quotes', 'yith-woocommerce-request-a-quote' ) ); ?></h2>

<table class="shop_table shop_table_responsive my_account_quotes">
  <thead>
		<tr>
			<th class="order-status">
				<span class="nobr"><?php _e( 'Status', 'yith-woocommerce-request-a-quote' ); ?></span></th>
			<th class="order-number">
				<span class="nobr"><?php _e( 'Quote', 'yith-woocommerce-request-a-quote' ); ?></span></th>
			<th class="order-date"><span class="nobr"><?php _e( 'Date', 'yith-woocommerce-request-a-quote' ); ?></span>
			</th>
			<th class="order-actions">&nbsp;</th>
		</tr>
  </thead>

  <tbody><?php

  foreach ( $customer_quotes as $customer_order ) {
    $order = wc_get_order( $customer_order->ID );
    $order->populate( $customer_order );
    $item_count = $order->get_item_count();

    ?><tr class="quotes">

      <td class="quotes-status" data-title="<?php _e( 'Status', 'yith-woocommerce-request-a-quote' ); ?>" style="text-align:left; white-space:nowrap;">
        <?php ywraq_get_order_status_tag( $order->get_status()); ?>
      </td>

      <td class="quotes-number" data-title="<?php _e( 'Order Number', 'yith-woocommerce-request-a-quote' ); ?>">
        <a href="<?php echo YITH_YWRAQ_Order_Request()->get_view_order_url( $order->id ); ?>">
          #<?php echo $order->get_order_number(); ?>
        </a>
      </td>

      <td class="quotes-date" data-title="<?php _e( 'Date', 'yith-woocommerce-request-a-quote' ); ?>">
        <time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>
      </td>

      <td class="quotes-actions" data-order_id="<?php echo $order->id ?>">
        <?php
          $actions = array();

          if ( YITH_Request_Quote()->enabled_checkout() && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_payment', array( 'pending' ), $order ) ) ) {
            $actions['accept'] = array(
              'url'  => '#',
              'data' => '',
              'name' => __( 'Checkout', 'yith-woocommerce-request-a-quote' )
            );
          }

          if ( get_option( 'ywraq_show_accept_link' ) != 'no' && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_reject', array( 'ywraq-pending' ), $order ) ) ) {
            $actions['accept'] = array(
              'url'  => "#",
              'data' => '',
              'name' => ywraq_get_label( 'accept' )
            );
          }

          if ( get_option( 'ywraq_show_reject_link' ) != 'no' && in_array( $order->get_status(), apply_filters( 'ywraq_valid_order_statuses_for_reject', array( 'ywraq-pending', 'ywraq-accepted', 'pending' ), $order ) ) ) {
            $actions['reject'] = array(
              'url'  => '#reject-quote-modal',
              'data' => 'data-rel="prettyPhoto"',
              'name' => ywraq_get_label( 'reject' )
            );
          }

          $actions['view'] = array(
            'url'  => YITH_YWRAQ_Order_Request()->get_view_order_url( $order->id ),
            'data' => '',
            'name' => __( 'View', 'yith-woocommerce-request-a-quote' )
          );

          $actions = apply_filters( 'ywraq_my_account_my_quotes_actions', $actions, $order );

          if ($actions) {
        //    echo '<pre>';var_dump($actions);echo '</pre>';
            foreach ( $actions as $key => $action ) {
              echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '" '.$action['data'].'">' . esc_html( $action['name'] ) . '</a>';
            }
          }
          ?>
        </td>
      </tr><?php
    }
    ?></tbody>
  </table>

  <?php if( get_option( 'ywraq_show_reject_link' ) != 'no' ): ?>
        <!-- REQUEST A QUOTE POPUP OPENER -->
	    <div id="reject-quote-modal" class="hide-modal">
			<p>Are you sure that you want reject the quote n. <span id="modal-order-number"></span>?</p>
	        <p><a class="ywraq-button button reject-quote-modal-button" href="#" data-order_id=""><?php _e('Yes, I want to reject the quote', 'yith-woocommerce-request-a-quote') ?></a>
	            <a class="ywraq-button button close-quote-modal-button" href="#"><?php _e('Close', 'yith-woocommerce-request-a-quote') ?></a> </p>
		</div>
	<?php endif ?>

<?php endif; ?>
