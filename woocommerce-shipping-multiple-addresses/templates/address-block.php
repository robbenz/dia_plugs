<div class="address-block">
<?php

$addr = array(
    'first_name'    => $address['shipping_first_name'],
    'last_name'     => $address['shipping_last_name'],
    'company'       => $address['shipping_company'],
    'address_1'     => $address['shipping_address_1'],
    'address_2'     => $address['shipping_address_2'],
    'city'          => $address['shipping_city'],
    'state'         => $address['shipping_state'],
    'postcode'      => $address['shipping_postcode'],
    'country'       => $address['shipping_country']
);
$formatted_address = $woocommerce->countries->get_formatted_address( $addr );

if (!$formatted_address)
    _e( 'You have not set up a shipping address yet.', 'woocommerce' );
else
    echo '<address>'.$formatted_address.'</address>';

$edit_link      = add_query_arg( array('address-form' => 1, 'edit' =>  $idx) ) . '#add_address_form';
$delete_link    = add_query_arg( array( 'address-delete' => 1, 'id' => $idx) );
?>
    <div class="buttons">
        <a class="button" href="<?php echo esc_url( $edit_link ); ?>"><?php _e('Edit', 'wc_shipping_multiple_address'); ?></a>
        <a class="button" href="<?php echo esc_url( $delete_link ); ?>"><?php _e('Delete', 'wc_shipping_multiple_address'); ?></a>
    </div>

</div>