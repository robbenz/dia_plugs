<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="col2-set" id="customer_login">

    	<div class="col-2" class="border-dash">
            <div id="left-col-reg-left">
<div id ="header-left-orange-rect">
		<h2><?php _e( 'Register For An Account', 'woocommerce' ); ?></h2>
 </div>
		<form method="post" class="register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
<div id="left-col-reg-username">
				<p class="form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
				</p>
</div>
			<?php endif; ?>
            
<div id="left-col-reg-email">
			<p class="form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email Address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" tabindex="4" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
			</p>
</div>
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
<div id="left-col-reg-password">
    
				<p class="form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" tabindex="6" class="input-text" name="password" id="reg_password" />
				</p>
</div>        
        <!--    <p class="form-row form-row-wide">
<label for="password_2"><span class="required">*</span> </label>
<input id="password_2" class="input-text" type="password" name="password_2">
</p> -->

			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>
<div id="left-col-submit-button">
			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<input type="submit" tabindex="8" class="button" name="register" value="<?php _e( 'Submit', 'woocommerce' ); ?>" />
			</p>
</div>
			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>
</div><!-- left-col-reg-left -->
	</div>
    
    
    
	<div class="col-1" class="border-dash">
        <div id="right-col-reg-right">

<?php endif; ?>
<div id ="header-right-orange-rect">
		<h2><?php _e( 'Sign In', 'woocommerce' ); ?></h2>
</div>
		<form method="post" class="login">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="form-row form-row-wide">
				<label for="username"><?php _e( 'Email Address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" tabindex="9" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>
			<p class="form-row form-row-wide">
				<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="input-text" tabindex="10" type="password" name="password" id="password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login' ); ?>
				<input type="submit" tabindex="12" class="button" name="login" value="<?php _e( 'Submit', 'woocommerce' ); ?>" />
				<label for="rememberme" class="inline">
					<input name="rememberme" tabindex="11" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
				</label>
			</p>
			<p class="lost_password">
				<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

</div><!-- right-col-reg-right -->

</div><!-- customer_login -->
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
