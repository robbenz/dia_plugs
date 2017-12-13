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

/*** add "Shipped" order status ***/
add_action( 'init', 'dia_register_shipped_order_status' );
function dia_register_shipped_order_status() {
    register_post_status( 'wc-shipped', array(
        'label'                     => _x( 'Shipped', 'Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped<span class="count">(%s)</span>', 'woocommerce' )
    ) );
}
// Register in wc_order_statuses
add_filter( 'wc_order_statuses', 'dia_shipped_status' );
function dia_shipped_status( $order_statuses ) {
    $order_statuses['wc-shipped'] = _x( 'Shipped', 'Order status', 'woocommerce' );
    return $order_statuses;
}
// add icon
add_action( 'wp_print_scripts', 'dia_shipped_icon' );
function dia_shipped_icon() {
  if( ! is_admin() ) return;
  ?>
  <style>
  .column-order_status mark.shipped { content: url("/parts_online/wp-content/plugins/dia-order-status/assets/shipped-icon.png"); }
</style> <?php
}
/*** END ***/

/*** Add custom column headers ***/
add_action('woocommerce_admin_order_item_headers', 'dia_shipping_woocommerce_admin_order_item_headers');
function dia_shipping_woocommerce_admin_order_item_headers($order) {
    echo '<th>Number of<br>Shipments</th>';
    echo '<th>Item Shippped</th>';
    echo '<th>Quantity<br>Shipped</th>';
    echo '<th>Tracking Number</th>';
    echo '<th>Freight Provider</th>';
}
/*** END ***/

/*** Add custom column values ***/
add_action('woocommerce_admin_order_item_values', 'dia_shipping_admin_order_item_values', 10, 3);
function dia_shipping_admin_order_item_values($_product, $item, $item_id = null) {

  $shippcount = wc_get_order_item_meta( $item_id, "number_of_shipments_$item_id", true );
  echo '<td><input type="number" style="width:38px;" step="any" min="0" max="1000" class="number_of_shipments" name="number_of_shipments_'.$item_id.'" value="'.$shippcount.'"></td>';

  $_z = intval($shippcount) + 1;

  for ($x=1 ; $x < $_z; $x++) {
    ${"value_item$x"}    = wc_get_order_item_meta( $item_id, "tracking_item_shipped$x", true );
    ${"value_qty$x"}     = wc_get_order_item_meta( $item_id, "tracking_item_qty$x", true );
    ${"value_num$x"}     = wc_get_order_item_meta( $item_id, "tracking_item_number$x", true );
    ${"value_freight$x"} = wc_get_order_item_meta( $item_id, "tracking_item_freight_provider$x", true );
  } ?>

  <td>
    <?php for ($x=1 ; $x < $_z; $x++) echo '<input type="text" class="tracking_item_shipped" name="tracking_item_shipped'.$x.'_'.$item_id.'" value="'.${"value_item$x"}.'"><br>'; ?>
  </td>
  <td>
    <?php for ($x=1 ; $x < $_z; $x++) echo '<input type="number" style="width:38px;" step="any" min="0" max="1000" class="tracking_item_qty" name="tracking_item_qty'.$x.'_'.$item_id.'" value="'.${"value_qty$x"}.'"><br>'; ?>
  </td>
  <td>
    <?php for ($x=1 ; $x < $_z; $x++) echo '<input type="text" class="tracking_item_number" name="tracking_item_number'.$x.'_'.$item_id.'" value="'.${"value_num$x"}.'"><br>'; ?>
  </td>
  <td>
    <?php for ($x=1 ; $x < $_z; $x++) echo '<input type="text" class="tracking_item_freight_provider" name="tracking_item_freight_provider'.$x.'_'.$item_id.'" value="'.${"value_freight$x"}.'"><br>'; ?>
  </td>

<?php
}
/*** END ***/

/*** save that shit ***/
add_action( 'save_post', 'dia_shipping_save_after_order_details', 10, 1 );
function dia_shipping_save_after_order_details( $post_id ) {
  $order = wc_get_order( $post_id );

  if ( !current_user_can( "edit_post", $post_id ) )    return $post_id;
  if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) return $post_id;

  if ( 'shop_order' == $_POST[ 'post_type' ] ) {
    $items = $order->get_items();
    foreach ($items as $item_id => $item_data){

      $_z = intval(wc_get_order_item_meta( $item_id, "number_of_shipments_$item_id", true )) + 1;

      $shippcount = $_POST['number_of_shipments_'.$item_id];
      if (! empty($shippcount) ) {
        wc_update_order_item_meta ($item_id, "number_of_shipments_$item_id", $shippcount);
      } else {
        wc_update_order_item_meta ($item_id, "number_of_shipments_$item_id", $shippcount);
      }

      for ($x=1 ; $x < $_z; $x++) {
        ${"item_update_shipped$x"} = $_POST['tracking_item_shipped'.$x.'_'.$item_id];
        ${"item_update_qty$x"}     = $_POST['tracking_item_qty'.$x.'_'.$item_id];
        ${"item_update$x"}         = $_POST['tracking_item_number'.$x.'_'.$item_id];
        ${"item_update_freight$x"} = $_POST['tracking_item_freight_provider'.$x.'_'.$item_id];

        if (! empty(${"item_update_shipped$x"}) ) {
          wc_update_order_item_meta ($item_id, "tracking_item_shipped$x", ${"item_update_shipped$x"});
        } else {
          wc_update_order_item_meta ($item_id, "tracking_item_shipped$x", ${"item_update_shipped$x"});
        }
        if (! empty(${"item_update_qty$x"}) ) {
          wc_update_order_item_meta ($item_id, "tracking_item_qty$x", ${"item_update_qty$x"});
        } else {
          wc_update_order_item_meta ($item_id, "tracking_item_qty$x", ${"item_update_qty$x"});
        }
        if (! empty(${"item_update$x"} ) ) {
          wc_update_order_item_meta ($item_id, "tracking_item_number$x", ${"item_update$x"} );
        } else {
          wc_update_order_item_meta ($item_id, "tracking_item_number$x", ${"item_update$x"} );
        }
        if (! empty(${"item_update_freight$x"} ) ) {
          wc_update_order_item_meta ($item_id, "tracking_item_freight_provider$x", ${"item_update_freight$x"} );
        } else {
          wc_update_order_item_meta ($item_id, "tracking_item_freight_provider$x", ${"item_update_freight$x"} );
        }
      }
    }
  }
}
/*** END ***/

/*** Add a custom action to order actions select box on edit order page ***/
add_action( 'woocommerce_order_actions', 'dia_track_add_order_meta_box_action' );
function dia_track_add_order_meta_box_action( $actions ) {
	global $theorder;
  if ( $theorder->post_status == 'wc-on-hold' ||  $theorder->post_status == 'wc-processing'  ||  $theorder->post_status == 'wc-shipped' ) {
    	$actions['wc_custom_order_action'] = __( 'Send Customer Tracking Info', 'woocommerce' );
	}
	return $actions;
}
/*** END ***/

/*** set cool email content type to html ***/
add_filter( 'wp_mail_content_type', 'set_html_dia_track_content_type' );
function set_html_dia_track_content_type() {
  return 'text/html';
}
/*** END ***/

/*** send cool email when action is clicked ***/
add_action( 'woocommerce_order_action_wc_custom_order_action', 'dia_track_process_order_meta_box_action' );
function dia_track_process_order_meta_box_action( $order ) {
  ob_start();
  remove_filter( 'wp_mail_content_type', 'set_html_dia_track_content_type' );

  $items = $order->get_items();

  $user = get_user_by( 'id', get_post_meta($order->id, '_customer_user', true) ) ;




  $customer_ship_name = get_post_meta ($order->id, '_shipping_first_name', true).'&nbsp;'.get_post_meta ($order->id, '_shipping_last_name', true);
  $customer_ship_co   = get_post_meta ($order->id, '_shipping_company', true);
  $customer_ship_add1 = get_post_meta ($order->id, '_shipping_address_1', true);
  $customer_ship_add2 = get_post_meta ($order->id, '_shipping_address_2', true);
  $customer_ship_city = get_post_meta ($order->id, '_shipping_city', true);
  $customer_ship_st   = get_post_meta ($order->id, '_shipping_state', true);
  $customer_ship_zip  = get_post_meta ($order->id, '_shipping_postcode', true);

  $message  = file_get_contents( plugin_dir_path( __FILE__ ).'email-track-send-head.php' ); // header

  $message .= '
  <p style="margin:0 0 10px; color:#00426a; font-size:16px;"><strong>Order Number:</strong> #EC-'.$order->id.'</p>
  <p style="margin:0 0 16px; color:#00426a; font-size:16px;"><strong>Order Date:</strong> '.date_i18n( wc_date_format(), strtotime( $order->order_date ) ).'</p>
  <p style="margin:0 0 10px; color:#000000; font-size:14px;">Dear '.$customer_ship_name.',<br>
  <p style="font-size:14px; color:#000000;">Thank you for shopping with DiaMedical USA. Please see below for the shipping status of your items.</p>
  <p style="color:#00426a;font-size:20px;">The following items have shipped...</p>
  <p style="margin:0 0 10px; color:#000000; font-size:14px;"><strong>Shipping to:</strong></p>
  <p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_name.'</p>
  ';

  if (strlen($customer_ship_co) > 1){
    $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_co.'</p>';
  }
  $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_add1.'</p>';
  if (strlen($customer_ship_add2) > 1){
    $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_add2.'</p>';
  }
  $message .= '<p style="margin:0;color:#000000; font-size:14px;">'.$customer_ship_city.', '.$customer_ship_st.' '.$customer_ship_zip.'</p><br>';

  foreach ($items as $item_id => $item_data) {
    $shippcount = wc_get_order_item_meta( $item_id, "number_of_shipments_$item_id", true );
  	$_z = intval($shippcount) + 1;

    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $item_data['product_id'] ), 'single-post-thumbnail' );

    $message .= '
    <table cellspacing="0" cellpadding="6" style="width:100%; color:#737373; border:1px solid #e4e4e4;">
      <thead>
        <tr>
          <th colspan="3" scope="col" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">Product</th>
          <th colspan="1" scope="col" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">Quantity Ordered</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="1" style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;color:#737373;padding:12px"><img width="50" height="50" src="'.$image[0].'" /></td>
          <td colspan="2" style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;color:#737373;padding:12px">'.$item_data['name'].'</td>
          <td colspan="1" style="text-align:left;vertical-align:middle;border:1px solid #eee;color:#737373;padding:12px">'.$item_data['qty'].'</td>
        </tr>
        </tbody>
        <tfoot>
          <tr>
            <th scope="row" colspan="1" style="text-align:left;border-top-width:4px;color:#737373;border:1px solid #e4e4e4;padding:12px">Item Shipped</th>
            <th scope="row" colspan="1" style="text-align:left;border-top-width:4px;color:#737373;border:1px solid #e4e4e4;padding:12px">Quantity Shipped</th>
            <th scope="row" colspan="1" style="text-align:left;border-top-width:4px;color:#737373;border:1px solid #e4e4e4;padding:12px">Freight Provider</th>
            <th scope="row" colspan="1" style="text-align:left;border-top-width:4px;color:#737373;border:1px solid #e4e4e4;padding:12px">Tracking Number</th>
          </tr>';

      for ($x=1 ; $x < $_z; $x++) {
        $message .= '
            <tr>
              <td scope="row" colspan="1" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">'.$item_data["tracking_item_shipped$x"].'</td>
              <td scope="row" colspan="1" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">'.$item_data["tracking_item_qty$x"].'</td>
              <td scope="row" colspan="1" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">'.$item_data["tracking_item_freight_provider$x"].'</td>
              <td scope="row" colspan="1" style="text-align:left;color:#737373;border:1px solid #e4e4e4;padding:12px">'.$item_data["tracking_item_number$x"].'</td>
            </tr>';
          }
      $message .= '
          </tfoot>
        </table><br>
        ';
    }

    $message .= file_get_contents( plugin_dir_path( __FILE__ ).'email-track-send-foot.php' ); // footer

    $subject = "[DiaMedical USA] Shipping Confirmation - (EC-$order->id)";
    $to      = $user->user_email;

    $headers[] = "From: DiaMedical USA <orders@diamedicalusa.com>"."\r\n";
    $headers[] = "Bcc: Gillian Peralta <gperalta@diamedicalusa.com>"."\r\n";
    $headers[] = "Bcc: Rob Benz <rbenz@diamedicalusa.com>"."\r\n";
    // $headers[] = "Bcc: Stella Lo <stellalo@diamedicalusa.com>"."\r\n";

  if ( wp_mail( $to, $subject, $message, $headers ) ) {
    $message = sprintf( __( 'Tracking Email Sent By %s', 'woocommerce' ), wp_get_current_user()->display_name );
    $order->add_order_note( $message );
    update_post_meta( $order->id, '_sent_tracking_email', 'yes' );
    $order->update_status( 'wc-shipped' );

  } else {
    $message = sprintf( __( 'Tracking Email Failed', 'woocommerce' ) );
    $order->add_order_note( $message );
  	update_post_meta( $order->id, '_sent_tracking_email', 'no' );
  }

  return ob_get_clean();
}
/*** END ***/

/**
 * Add Trackem toggle button in my orders actions.
 *
 * @param array $actions
 * @param WC_Order $order
 * @return array
 */
function dia_add_trackem_to_my_orders_actions( $actions, $order ) {
	if ( $order->has_status( 'shipped' ) ) {
		$actions['trackem'] = array(
		  'url'  => '#',
      'name' => __( 'Track Items', 'woocommerce' )
		);
	}
	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'dia_add_trackem_to_my_orders_actions', 100, 2 );

// // Adding Meta container
// add_action( 'add_meta_boxes', 'dia_shipping_admin_add_meta_boxes' );
// function dia_shipping_admin_add_meta_boxes() {
//   add_meta_box( 'send_some_tracking_info', __('Send Tracking Button','woocommerce'), 'dia_shipping_send_button_box', 'shop_order', 'normal', 'high', NULL );
// }
// function dia_shipping_send_button_box() {
//   global $theorder;
//   $items = $theorder->get_items();
//    foreach ($items as $item_id => $item_data) {
//     echo $item_data['product_id'];
//
//     $image = wp_get_attachment_image_src( get_post_thumbnail_id( $item_data['product_id'] ), 'single-post-thumbnail' );
//
// echo '<img width="50" height="50" src="'.$image[0].'"';
//   }
// }
