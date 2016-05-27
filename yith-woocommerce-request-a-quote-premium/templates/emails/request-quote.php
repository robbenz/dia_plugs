<?php
/**
 * HTML Template Email Request a Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.3.4
 * @author  Yithemes
 */
$order_id = $raq_data['order_id'];
$customer = get_post_meta( $order_id, '_customer_user', true);
$page_detail_admin = ( get_option('ywraq_quote_detail_link') == 'editor' ) ? true : false;

if(empty( $raq_data['partnumber']) && empty( $raq_data['partdesc']) && empty( $raq_data['partqty']) && empty( $raq_data['raq_content'] )):
$catalog_header = __( 'Your Catalog Is On The Way!', 'yith-woocommerce-request-a-quote' );
do_action( 'woocommerce_email_header', $catalog_header );
?>
<p><?php printf( __( 'You received a Catalog request from %s.', 'yith-woocommerce-request-a-quote' ), $raq_data['user_name'] ); ?></p>

<?php  elseif( ! empty( $raq_data['address']) ):
  if (!empty( $raq_data['raq_content'] )|| !empty( $raq_data['partqty']) || !empty( $raq_data['partdesc']) || !empty( $raq_data['partnumber']) ) {
$catalog_quote_header = __( 'Request A Quote &amp; Mail Catalog', 'yith-woocommerce-request-a-quote' );
do_action( 'woocommerce_email_header', $catalog_quote_header );
}
?>
<p><?php printf( __( 'You received a quote request from %s.', 'yith-woocommerce-request-a-quote' ), $raq_data['user_name'] ); ?></p>

<?php else: ?>
<?php do_action( 'woocommerce_email_header', $email_heading ); ?>
<p><?php printf( __( 'You received a quote request from %s. The request is the following:', 'yith-woocommerce-request-a-quote' ), $raq_data['user_name'] ); ?></p>

<?php endif ?>
<?php
    wc_get_template( 'emails/request-quote-table.php', array(
        'raq_data'      => $raq_data
    ) );
?>
<p></p>


<?php if( $customer != 0 && ( get_option( 'ywraq_enable_link_details' ) == "yes" && get_option( 'ywraq_enable_order_creation', 'yes' ) == 'yes' ) ): ?>
<p><?php printf( __( 'You can see details here: <a href="%s">#EC-%s</a>', 'yith-woocommerce-request-a-quote' ), YITH_YWRAQ_Order_Request()->get_view_order_url($order_id, $page_detail_admin), $order_id ); ?></p>
<?php endif ?>

<?php  if(empty( $raq_data['partnumber']) && empty( $raq_data['partdesc']) && empty( $raq_data['partqty'])): ?>
  <?php  echo ''; ?>
<?php  else: ?>
<h2><?php _e( 'Additional Products', 'yith-woocommerce-request-a-quote' ); ?></h2>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
  <thead>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Part Number', 'yith-woocommerce-request-a-quote' ); ?></th>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Description', 'yith-woocommerce-request-a-quote' ); ?></th>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
</thead>
<tbody>
<tr>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partnumber'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partdesc'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partqty'] ?></td>
</tr>
<tr>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partnumber1'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partdesc1'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partqty1'] ?></td>
</tr>
<tr>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partnumber2'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partdesc2'] ?></td>
  <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $raq_data['partqty2'] ?></td>
</tr>
</tbody>
</table>

<?php endif ?>

<?php if( ! empty( $raq_data['user_message']) ): ?>
<h2><?php _e( 'Customer\'s message', 'yith-woocommerce-request-a-quote' ); ?></h2>
    <p><?php echo $raq_data['user_message'] ?></p>
<?php endif ?>
<h2><?php _e( 'Customer details', 'yith-woocommerce-request-a-quote' ); ?></h2>

<p><strong><?php _e( 'Name:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['user_name'] ?></p>
<p><strong><?php _e( 'Email:', 'yith-woocommerce-request-a-quote' ); ?></strong> <a href="mailto:<?php echo $raq_data['user_email']; ?>"><?php echo $raq_data['user_email']; ?></a></p>
<p><strong><?php _e( 'Facility Name:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['facility_name'] ?></p>

<?php if( ! empty( $raq_data['selectOption']) ): ?>
<p><strong><?php _e( 'Catalog Selection: ', 'yith-woocommerce-request-a-quote' ); ?></strong><?php echo $raq_data['selectOption']; ?></p>
<?php endif ?>

<?php if( ! empty( $raq_data['address']) ): ?>
  <h3><?php _e( 'Mail Catalog To:', 'yith-woocommerce-request-a-quote' ); ?></h3>
<p><strong><?php _e( 'Address:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['address'] ?></p>
<p><strong><?php _e( 'City:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['city'] ?></p>
<p><strong><?php _e( 'State:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['state'] ?></p>
<?php endif ?>

<p><strong><?php _e( 'Zip Code:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['zipcode'] ?></p>
<p><strong><?php _e( 'Phone Number:', 'yith-woocommerce-request-a-quote' ); ?></strong> <?php echo $raq_data['phonenumber'] ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
