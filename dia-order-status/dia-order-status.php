<?php
/*
Plugin Name: DiaMedical USA Order Status Tracking
Plugin URI: robbenz.com
Description: This plugin will allow Stella to send shipping info on a per product basis for online orders
Version: 1.0
Author: Rob Benz
Author URI: robbenz.com
License: GPL2
*/


// Add custom column headers
add_action('woocommerce_admin_order_item_headers', 'dia_shipping_woocommerce_admin_order_item_headers');
function dia_shipping_woocommerce_admin_order_item_headers($order) {
    echo '<th>Tracking Number: wasssup Stella</th>';
    echo '<th>Freight Provider</th>';
}

// Add custom column values
add_action('woocommerce_admin_order_item_values', 'dia_shipping_admin_order_item_values', 10, 3);
function dia_shipping_admin_order_item_values($_product, $item, $item_id = null) {
    $value_num = wc_get_order_item_meta( $item_id, "tracking_item_number", true );
    $value_freight = wc_get_order_item_meta( $item_id, "tracking_item_freight_provider", true ); ?>

    <td>
      <input type="text"
             class="tracking_item_number"
             name="tracking_item_number_<?php echo $item_id; ?>"
             value="<?php echo $value_num; ?>"
             >
    </td>

    <td>
      <input type="text"
             class="tracking_item_freight_provider"
             name="tracking_item_freight_provider_<?php echo $item_id; ?>"
             value="<?php echo $value_freight; ?>"
             >
    </td>
<?php
}

// save that shit
add_action( 'save_post', 'dia_shipping_save_after_order_details', 10, 1 );
function dia_shipping_save_after_order_details( $post_id ) {

  $order = wc_get_order( $post_id );

  if ( !current_user_can( "edit_post", $post_id ) )    return $post_id;
  if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) return $post_id;

  if ( 'shop_order' == $_POST[ 'post_type' ] ) {

    $items = $order->get_items();
    foreach ($items as $item_id => $item_data){
      $item_update = $_POST["tracking_item_number_$item_id"];
      $item_update_freight = $_POST["tracking_item_freight_provider_$item_id"];

      if (! empty($item_update) ) {
        wc_update_order_item_meta ($item_id, "tracking_item_number", $item_update);
      } else {
        wc_update_order_item_meta ($item_id, "tracking_item_number", $item_update);
      }

      if (! empty($item_update_freight) ) {
        wc_update_order_item_meta ($item_id, "tracking_item_freight_provider", $item_update_freight);
      } else {
        wc_update_order_item_meta ($item_id, "tracking_item_freight_provider", $item_update_freight);
      }

    }
  }
}

/**
 * Add a custom action to order actions select box on edit order page
 *
 * @param array $actions order actions array to display
 * @return array - updated actions
 */
add_action( 'woocommerce_order_actions', 'dia_track_add_order_meta_box_action' );
function dia_track_add_order_meta_box_action( $actions ) {
	global $theorder;
  if ( $theorder->post_status == 'wc-on-hold' ||  $theorder->post_status == 'wc-processing'  ) {
    	$actions['wc_custom_order_action'] = __( 'Send Customer Tracking Info', 'woocommerce' );
	}
	return $actions;
}

// set to html
add_filter( 'wp_mail_content_type', 'set_html_dia_track_content_type' );
function set_html_dia_track_content_type() {
  return 'text/html';
}

/**
 * Add an order note when custom action is clicked
 * do a bunch of other shit
 *
 * @param WC_Order $order
 */
add_action( 'woocommerce_order_action_wc_custom_order_action', 'dia_track_process_order_meta_box_action' );
function dia_track_process_order_meta_box_action( $order ) {
  ob_start();
  remove_filter( 'wp_mail_content_type', 'set_html_dia_track_content_type' );

  $items = $order->get_items();

  $customer_ship_name = get_post_meta ($order->id, '_shipping_first_name', true).'&nbsp;'.get_post_meta ($order->id, '_shipping_last_name', true);
  $customer_ship_co   = get_post_meta ($order->id, '_shipping_company', true);
  $customer_ship_add1 = get_post_meta ($order->id, '_shipping_address_1', true);
  $customer_ship_add2 = get_post_meta ($order->id, '_shipping_address_2', true);
  $customer_ship_city = get_post_meta ($order->id, '_shipping_city', true);
  $customer_ship_st   = get_post_meta ($order->id, '_shipping_state', true);
  $customer_ship_zip  = get_post_meta ($order->id, '_shipping_postcode', true);

  $subject = "DiaMedical USA Shipping Confirmation - #EC-$order->id";
  $to      = 'rbenz@diamedicalusa.com';

  $message  = file_get_contents( plugin_dir_path( __FILE__ ) . 'email-track-send-head.php' ); // header

  $message .= '<p style="margin:0 0 10px; color:#00426a; font-size:16px;"><strong>Order Number:</strong> #EC-'.$order->id.'</p>';
  $message .= '<p style="margin:0 0 16px; color:#00426a; font-size:16px;"><strong>Order Date:</strong> ' . date_i18n( wc_date_format(), strtotime( $order->order_date ) ) . '</p>';
  $message .= '<p style="margin:0 0 10px; color:#000000; font-size:14px;">Dear ' . $customer_ship_name . ',<br>';
  $message .= '<p style="font-size:14px; color:#000000;">Thank you for shopping with DiaMedical USA. Please see below for the shipping status of your items.</p>';
  $message .= '<p style="color:#00426a;font-size:20px;">The following items have shipped...</p><br>';
  $message .= '<p style="margin:0 0 10px; color:#000000; font-size:14px;"><strong>Shipping to:</strong></p><br>';
  $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_name.'</p><br>';
  if (strlen($customer_ship_co) > 1){
    $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_co.'</p><br>';
  }
  $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_add1.'</p><br>';
    if (strlen($customer_ship_add2) > 1){
      $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_add2.'</p><br>';
    }
  $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_city.', '.$customer_ship_st.' '.$customer_ship_zip.'</p><br><br>';


  foreach ($items as $item_id => $item_data) {
    $message .= 'Product Name: ' . $item_data['name'].'<br>';
    $message .= 'Qty: ' . $item_data['qty'].'<br>';
    $message .= 'Tracking Number: ' . $item_data['tracking_item_number'].'<br>';
    $message .= 'Freight Provider: ' . $item_data['tracking_item_freight_provider'].'<br>';
  }

  $message .= file_get_contents( plugin_dir_path( __FILE__ ) . 'email-track-send-foot.php' ); // footer

  $headers[] = "From: DiaMedicalUSA.com <orders@diamedicalusa.com>" . "\r\n";
  $headers[] = "Bcc: Rob Benz <benz_rob@yahoo.com>" . "\r\n";

  if ( wp_mail( $to, $subject, $message, $headers ) ) {
    $message = sprintf( __( 'Tracking Email Sent By %s', 'woocommerce' ), wp_get_current_user()->display_name );
    $order->add_order_note( $message );
    update_post_meta( $order->id, '_sent_tracking_email', 'yes' );

  } else {
    $message = sprintf( __( 'Tracking Email Failed', 'woocommerce' ) );
    $order->add_order_note( $message );
  	update_post_meta( $order->id, '_sent_tracking_email', 'no' );
  }

  return ob_get_clean();
}

// Adding Meta container
// add_action( 'add_meta_boxes', 'dia_shipping_admin_add_meta_boxes' );
// function dia_shipping_admin_add_meta_boxes() {
//   add_meta_box( 'send_some_tracking_info', __('Send Tracking Button','woocommerce'), 'dia_shipping_send_button_box', 'shop_order', 'normal', 'high', NULL );
// }
// function dia_shipping_send_button_box() {
//   global $theorder;
// }
