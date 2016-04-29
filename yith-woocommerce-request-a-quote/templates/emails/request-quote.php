<?php
/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemess
 */
?>

<?php
if(empty( $raq_data['partnumber']) && empty( $raq_data['partdesc']) && empty( $raq_data['partqty']) && empty( $raq_data['raq_content'] )):
$catalog_header = __( 'Request A Catalog', 'ywraq' );
do_action( 'woocommerce_email_header', $catalog_header );
?>
<p><?php printf( __( 'You received a Catalog request from %s.', 'ywraq' ), $raq_data['user_name'] ); ?></p>

<?php  elseif( ! empty( $raq_data['address']) ):
  if (!empty( $raq_data['raq_content'] )|| !empty( $raq_data['partqty']) || !empty( $raq_data['partdesc']) || !empty( $raq_data['partnumber']) ) {
$catalog_quote_header = __( 'Request A Quote &amp; Mail Catalog', 'ywraq' );
do_action( 'woocommerce_email_header', $catalog_quote_header );
}
?>
<p><?php printf( __( 'You received a quote request from %s.', 'ywraq' ), $raq_data['user_name'] ); ?></p>
<p><?php printf( __( '%s would also like a catalog in the mail.', 'ywraq' ), $raq_data['user_name'] ); ?></p>



<?php else: ?>
<?php do_action( 'woocommerce_email_header', $email_heading ); ?>
<p><?php printf( __( 'You received a quote request from %s. The request is the following:', 'ywraq' ), $raq_data['user_name'] ); ?></p>

<?php endif ?>
<?php do_action( 'yith_ywraq_email_before_raq_table', $raq_data ); ?>


<?php if ( !empty( $raq_data['raq_content'] ) ): ?>
<h2><?php _e('Request Quote', 'ywraq') ?></h2>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
    <tr>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'ywraq' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'ywraq' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Unit Price', 'ywraq' ); ?></th>
        <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Subtotal', 'ywraq' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if( ! empty( $raq_data['raq_content'] ) ):
        foreach( $raq_data['raq_content'] as $item ):
            if( isset( $item['variation_id']) ){
                $_product = wc_get_product( $item['variation_id'] );
            }else{
                $_product = wc_get_product( $item['product_id'] );
            }
            ?>
            <tr>
                <td scope="col" style="text-align:left; border: 1px solid #eee;"><a href="<?php echo get_edit_post_link( $_product->id )?>"><?php echo $_product->post->post_title . '<br> (# ' . $_product->get_sku(). ')' ?></a>
                 <?php  if( isset($item['variations'])): ?><small><?php echo yith_ywraq_get_product_meta($item); ?></small><?php endif ?></td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;"><?php echo $item['quantity'] ?></td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">
                  <?php
                  $send_benzy_price = WC()->cart->get_product_price( $_product, $raq['price'] );
                  echo str_replace("$0.00", "Preparing Quote", $send_benzy_price);
                  ?>
                </td>
                <td scope="col" style="text-align:left; border: 1px solid #eee;">
                  <?php
                  $send_benzy_sub_price = WC()->cart->get_product_subtotal( $_product, $item['quantity'] );
                  echo str_replace("$0.00", " ", $send_benzy_sub_price);
                  ?>
              </td>
            </tr>
        <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>
<?php endif ?>

<?php do_action( 'yith_ywraq_email_after_raq_table', $raq_data ); ?>

<?php  if(empty( $raq_data['partnumber']) && empty( $raq_data['partdesc']) && empty( $raq_data['partqty'])): ?>
  <?php  echo ''; ?>
<?php  else: ?>
<h2><?php _e( 'Additional Products', 'ywraq' ); ?></h2>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
  <thead>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Part Number', 'ywraq' ); ?></th>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Description', 'ywraq' ); ?></th>
  <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'ywraq' ); ?></th>
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
<?php   endif ?>

<?php if( ! empty( $raq_data['user_message']) ): ?>
<h2><?php _e( 'Customer message', 'ywraq' ); ?></h2>
    <p><?php echo $raq_data['user_message'] ?></p>
  <?php endif ?>

<h2><?php _e( 'Customer details', 'ywraq' ); ?></h2>

<p><strong><?php _e( 'Name:', 'ywraq' ); ?></strong> <?php echo $raq_data['user_name'] ?></p>
<p><strong><?php _e( 'Email:', 'ywraq' ); ?></strong> <a href="mailto:<?php echo $raq_data['user_email']; ?>"><?php echo $raq_data['user_email']; ?></a></p>
<p><strong><?php _e( 'Facility Name:', 'ywraq' ); ?></strong> <?php echo $raq_data['facility_name'] ?></p>
<?php if( ! empty( $raq_data['address']) ): ?>
  <h3><?php _e( 'Mail Catalog To:', 'ywraq' ); ?></h3>
<p><strong><?php _e( 'Address:', 'ywraq' ); ?></strong> <?php echo $raq_data['address'] ?></p>
<p><strong><?php _e( 'City:', 'ywraq' ); ?></strong> <?php echo $raq_data['city'] ?></p>
<p><strong><?php _e( 'State:', 'ywraq' ); ?></strong> <?php echo $raq_data['state'] ?></p>
<?php endif ?>
<p><strong><?php _e( 'Zip Code:', 'ywraq' ); ?></strong> <?php echo $raq_data['zipcode'] ?></p>
<p><strong><?php _e( 'Phone Number:', 'ywraq' ); ?></strong> <?php echo $raq_data['phonenumber'] ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
