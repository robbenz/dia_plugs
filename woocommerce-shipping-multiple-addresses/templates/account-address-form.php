<form action="" method="post" id="address_form">
    <p><a class="button add_address" href="#"><?php _e( 'Add another', 'wc_shipping_multiple_address' ); ?></a></p>

    <?php if (! empty( $otherAddr ) ): ?>

        <div id="addresses">

        <?php foreach ( $otherAddr as $idx => $address ): ?>
            <div class="shipping_address address_block" id="shipping_address_<?php echo $idx; ?>">
                <p align="right"><a href="#" class="button delete"><?php _e('delete', 'wc_shipping_multiple_address'); ?></a></p>

                <?php
                do_action( 'woocommerce_before_checkout_shipping_form', $checkout);

                foreach ( $shipFields as $key => $field ) {
                    $val = '';

                    if ( isset( $address[ $key ] ) ) {
                        $val = $address[$key];
                    }

                    $key .= '[]';

                    woocommerce_form_field( $key, $field, $val );
                }

                do_action( 'woocommerce_after_checkout_shipping_form', $checkout);
                ?>
            </div>
        <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div id="addresses">

        <?php
        foreach ( $shipFields as $key => $field ) :
            $key .= '[]';
            $val = '';

            woocommerce_form_field( $key, $field, $val );
        endforeach;
        ?>
        </div>
    <?php endif; ?>

    <div class="form-row">
        <input type="hidden" name="shipping_account_address_action" value="save" />
        <input type="submit" name="set_addresses" value="<?php _e( 'Save Addresses', 'wc_shipping_multiple_address' ); ?>" class="button alt" />
    </div>
</form>
<script type="text/javascript">
    var tmpl = '<div class="shipping_address address_block"><p align="right"><a href="#" class="button delete"><?php _e('delete', 'wc_shipping_multiple_address'); ?></a></p>';

    tmpl += '\
            <?php
            foreach ($shipFields as $key => $field) :
                $key .= '[]';
                $val = '';
                $field['return'] = true;
                $row = woocommerce_form_field( $key, $field, $val );
                echo str_replace("\n", "\\\n", $row);
            endforeach;
            ?>
    ';

    tmpl += '</div>';
    jQuery(".add_address").click(function(e) {
        e.preventDefault();

        jQuery("#addresses").append(tmpl);
    });

    jQuery(".delete").on("click", function(e) {
        e.preventDefault();
        jQuery(this).parents("div.address_block").remove();
    });

    jQuery(document).ready(function() {
        jQuery("#address_form").submit(function() {
            var valid = true;
            jQuery("input[type=text],select").each(function() {
                if (jQuery(this).prev("label").children("abbr").length == 1 && jQuery(this).val() == "") {
                    jQuery(this).focus();
                    valid = false;
                    return false;
                }
            });
            return valid;
        });
    });
</script>