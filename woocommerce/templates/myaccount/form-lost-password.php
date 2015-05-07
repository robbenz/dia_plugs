<?php
/**
 * Lost password form
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>
<div id="lost-pass-wrap" class="border-dash">
<form method="post" class="lost_reset_password">

	<?php if( 'lost_password' == $args['form'] ) : ?>
    
    <div id="lost-pass-header-orange">
    Lost your password? 
    </div>

		<p class="lost-pass-copy"><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p>

<div id="lost-pass-input-field">   
		<p class="form-row form-row-first"><label for="user_login"><?php _e( 'Email Address', 'woocommerce' ); ?></label> <input class="input-text" type="text" name="user_login" id="user_login" /></p>
</div> <!-- lost-pass-input-field -->
    
	<?php else : ?>

		<p><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce') ); ?></p>

		<p class="form-row form-row-first">
			<label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="password" class="input-text" name="password_1" id="password_1" />
		</p>
		<p class="form-row form-row-last">
			<label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="password" class="input-text" name="password_2" id="password_2" />
		</p>

		<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
		<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />

	<?php endif; ?>

	<div class="clear"></div>
<div id="submit-button-for-lost-pass">
	<p class="form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<input type="submit" class="button" value="<?php echo 'lost_password' == $args['form'] ? __( 'Reset Password', 'woocommerce' ) : __( 'Save', 'woocommerce' ); ?>" />
	</p>
</div><!-- submit-button-for-lost-pass -->
	<?php wp_nonce_field( $args['form'] ); ?>

</form>
</div><!-- lost-pass-wrap -->
