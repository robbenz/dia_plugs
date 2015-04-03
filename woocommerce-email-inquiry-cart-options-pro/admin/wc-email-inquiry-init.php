<?php
/**
 * Call this function when plugin is deactivated
 */
function wc_email_inquiry_deactivated(){

	delete_transient("a3rev_wc_email_inquiry_update_info");
	$respone_api = __('Connection Error! Could not reach the a3API on Amazon Cloud, the network may be busy. Please try again in a few minutes.', 'wc_email_inquiry');
	$options = array(
		'method' 	=> 'POST',
		'timeout' 	=> 45,
		'body' 		=> array(
			'act'			=> 'deactivate',
			'ssl'			=> get_option('a3rev_auth_wc_email_inquiry'),
			'plugin' 		=> get_option('a3rev_wc_email_inquiry_plugin'),
			'domain_name'	=> $_SERVER['SERVER_NAME'],
			'address_ip'	=> $_SERVER['SERVER_ADDR'],
		)
	);
	$server_a3 = base64_decode('aHR0cDovL2EzYXBpLmNvbS9hdXRoYXBpL2luZGV4LnBocA==');
	$raw_response = wp_remote_request($server_a3 , $options);
	if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']) {
		$respone_api = $raw_response['body'];
	}

	delete_option ( 'a3rev_pin_wc_email_inquiry' );
	delete_option ( 'a3rev_auth_wc_email_inquiry' );
}

function wc_email_inquiry_install(){
	update_option('a3rev_wc_email_inquiry_version', '1.2.5');
	update_option('a3rev_wc_email_inquiry_ultimate_version', '1.2.2');
	update_option('a3rev_wc_orders_quotes_version', '1.1.8');

	// Set Settings Default from Admin Init
	global $wc_ei_admin_init;
	$wc_ei_admin_init->set_default_settings();

	// Build sass
	global $wc_email_inquiry_less;
	$wc_email_inquiry_less->plugin_build_sass();

	delete_transient("a3rev_wc_email_inquiry_update_info");

	update_option('a3rev_wc_email_inquiry_just_installed', true);
}

update_option('a3rev_wc_email_inquiry_plugin', 'wc_email_inquiry');

/**
 * Load languages file
 */
function wc_email_inquiry_init() {
	if ( get_option('a3rev_wc_email_inquiry_just_installed') ) {
		delete_option('a3rev_wc_email_inquiry_just_installed');
		wp_redirect( admin_url( 'admin.php?page=email-cart-options', 'relative' ) );
		exit;
	}
	load_plugin_textdomain( 'wc_email_inquiry', false, WC_EMAIL_INQUIRY_FOLDER.'/languages' );
}
// Add language
add_action('init', 'wc_email_inquiry_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WC_Email_Inquiry_Hook_Filter', 'a3_wp_admin' ) );

// Add admin sidebar menu css
add_action( 'admin_enqueue_scripts', array( 'WC_Email_Inquiry_Hook_Filter', 'admin_sidebar_menu_css' ) );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WC_Email_Inquiry_Hook_Filter', 'plugin_extra_links'), 10, 2 );

if ( isset($_POST['wc_email_inquiry_pin_submit']) ) {
	wc_email_inquiry_confirm_pin();
}

$check_encryp_file = false;
$str = "THlvTkNsQnNkV2RwYmlCT1lXMWxPaUJYVUMxQ2JHOW5VM1J2Y21VZ1ptOXlJRmR2Y21Sd2NtVnpjdzBLVUd4MVoybHVJRlZTU1RvZ2FIUjBjRG92TDNkM2R5NWlkV2xzWkdGaWJHOW5jM1J2Y21VdVkyOXRMdzBLUkdWelkzSnBjSFJwYjI0NklFRjFkRzl0WVhScFkyRnNiSGtnWjJWdVpYSmhkR1VnWlVKaGVTQmhabVpwYkdsaGRHVWdZbXh2WjNNZ2QybDBhQ0IxYm1seGRXVWdkR2wwYkdWekxDQjBaWGgwTENCbFFtRjVJR0YxWTNScGIyNXpMZzBLVm1WeWMybHZiam9nTXk0d0RRcEVZWFJsT2lCTllYSmphQ0F4TENBeU1EQTVEUXBCZFhSb2IzSTZJRUoxYVd4a1FVSnNiMmRUZEc5eVpRMEtRWFYwYUc5eUlGVlNTVG9nYUhSMGNEb3ZMM2QzZHk1aWRXbHNaR0ZpYkc5bmMzUnZjbVV1WTI5dEx3MEtLaThnRFFvTkNnMEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRmRRTFVKc2IyZFRkRzl5WlNCWGIzSmtjSEpsYzNNZ1VHeDFaMmx1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLRFFvTkNpTWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU09";
	if(file_exists(WC_EMAIL_INQUIRY_FILE_PATH."/encryp.inc")){
		$getfile = file_get_contents(WC_EMAIL_INQUIRY_FILE_PATH ."/encryp.inc");
		if(strpos($getfile, $str) !== FALSE){
			$check_encryp_file = true;
		}
}

if ( $check_encryp_file && wc_orders_quotes_check_pin() ) {

	// Need to call Admin Init to show Admin UI
	global $wc_ei_admin_init;
	$wc_ei_admin_init->init();

	$woocommerce_db_version = get_option( 'woocommerce_db_version', null );

	// Add upgrade notice to Dashboard pages
	add_filter( $wc_ei_admin_init->plugin_name . '_plugin_extension', array( 'WC_Email_Inquiry_Functions', 'plugin_extension' ) );

	// Include style into header
	add_action('get_header', array('WC_Email_Inquiry_Hook_Filter', 'add_style_header') );

	// Include google fonts into header
	add_action( 'wp_head', array( 'WC_Email_Inquiry_Hook_Filter', 'add_google_fonts'), 11 );

	// Include script into footer
	add_action('get_footer', array('WC_Email_Inquiry_Hook_Filter', 'script_contact_popup'), 2);

	// Change item meta value as long url to short url
	add_filter('woocommerce_order_item_display_meta_value', array('WC_Email_Inquiry_Hook_Filter', 'change_order_item_display_meta_value' ) );

	// AJAX hide yellow message dontshow
	add_action('wp_ajax_wc_ei_yellow_message_dontshow', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dontshow') );
	add_action('wp_ajax_nopriv_wc_ei_yellow_message_dontshow', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dontshow') );

	// AJAX hide yellow message dismiss
	add_action('wp_ajax_wc_ei_yellow_message_dismiss', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dismiss') );
	add_action('wp_ajax_nopriv_wc_ei_yellow_message_dismiss', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dismiss') );

	// AJAX wc_email_inquiry contact popup
	add_action('wp_ajax_wc_email_inquiry_popup', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_popup') );
	add_action('wp_ajax_nopriv_wc_email_inquiry_popup', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_popup') );

	// AJAX wc_email_inquiry_action
	add_action('wp_ajax_wc_email_inquiry_action', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_action') );
	add_action('wp_ajax_nopriv_wc_email_inquiry_action', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_action') );

	// Hide Add to Cart button on Shop page
	add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_before_hide_add_to_cart_button'), 100, 4 );
	add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_after_hide_add_to_cart_button'), 1, 4 );

	// Hide Add to Cart button on Details page
	add_action('woocommerce_before_add_to_cart_button', array('WC_Email_Inquiry_Hook_Filter', 'details_before_hide_add_to_cart_button'), 100 );
	add_action('woocommerce_after_add_to_cart_button', array('WC_Email_Inquiry_Hook_Filter', 'details_after_hide_add_to_cart_button'), 1 );

	// Hide Quantity Control and Add to Cart button for Child Product of Grouped Product Type in Details Page
	add_action('woocommerce_before_add_to_cart_form', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart_style'), 100 );
	add_filter('single_add_to_cart_text', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart'), 100, 2 );
	add_filter('woocommerce_product_single_add_to_cart_text', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart'), 100, 2 ); // for Woo 2.1
	add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'before_grouped_product_hide_quatity_control'), 100, 4 );
	add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'after_grouped_product_hide_quatity_control'), 1, 4 );


	// Hide Price on Shop page and Details page
	add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_before_hide_price'), 100, 4 );
	add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_after_hide_price'), 1, 4 );

	// Hide Price
	add_filter('woocommerce_get_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
	add_filter('woocommerce_variation_sale_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
	add_filter('woocommerce_variation_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
	add_filter('woocommerce_variation_free_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
	add_filter('woocommerce_variation_empty_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
	if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
		add_filter('woocommerce_cart_item_price_html', array('WC_Email_Inquiry_Hook_Filter', 'hide_price_from_mini_cart'), 100, 3);
	} else {
		add_filter('woocommerce_cart_item_price', array('WC_Email_Inquiry_Hook_Filter', 'hide_price_from_mini_cart'), 100, 3); // for Woo 2.1
	}
	add_filter('woocommerce_widget_cart_item_quantity', array('WC_Email_Inquiry_Hook_Filter', 'remove_x_character_mini_cart'), 100, 3);
	add_filter('woocommerce_cart_product_subtotal', array('WC_Email_Inquiry_Hook_Filter', 'hide_cart_product_subtotal'), 100, 4 );

	// Add Email Inquiry Button on Shop page
	$wc_email_inquiry_customize_email_button_settings = get_option( 'wc_email_inquiry_customize_email_button', array( 'inquiry_button_position' => 'below' ) );
	$wc_email_inquiry_button_position = $wc_email_inquiry_customize_email_button_settings['inquiry_button_position'];
	if ($wc_email_inquiry_button_position == 'above' )
		add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_add_email_inquiry_button_above'), 9, 4);
	else
		add_action('woocommerce_after_shop_loop_item', array('WC_Email_Inquiry_Hook_Filter', 'shop_add_email_inquiry_button_below'), 12);

	// Add Email Inquiry Button on Product Details page
	if ($wc_email_inquiry_button_position == 'above' )
		add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'details_add_email_inquiry_button_above'), 9, 4 );
	else
		add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'details_add_email_inquiry_button_below'), 2, 4);


	// Add meta boxes to product page
	add_action( 'admin_menu', array('WC_Email_Inquiry_MetaBox', 'add_meta_boxes') );
	if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
		add_action('save_post', array('WC_Email_Inquiry_MetaBox','save_meta_boxes' ) );
	}

	// Include script admin plugin
	if (in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
		add_action('admin_footer', array('WC_Email_Inquiry_Hook_Filter', 'admin_footer_scripts'));
	}

	// Check upgrade functions
	add_action( 'init', 'a3rev_ei_pro_upgrade_plugin' );
	function a3rev_ei_pro_upgrade_plugin() {
		// Upgrade to 1.0.3
		if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.0.3' ) === -1 ) {
			include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.0.3.php' );
			update_option('a3rev_wc_email_inquiry_version', '1.0.3');
		}

		// Upgrade Ultimate to 1.0.5
		if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.1.2' ) === -1 ) {
			include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.1.2.php' );
			update_option('a3rev_wc_email_inquiry_version', '1.1.2');
		}

		if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.1.4.2' ) === -1 ) {
			include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.1.4.2.php' );
			update_option('a3rev_wc_email_inquiry_version', '1.1.4.2');
		}

		if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.2.0' ) === -1 ) {
			// Build sass
			global $wc_email_inquiry_less;
			$wc_email_inquiry_less->plugin_build_sass();

			update_option('a3rev_wc_email_inquiry_version', '1.2.0');
		}

		if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.2.4' ) === -1 ) {
			global $wc_ei_admin_init;
			$wc_ei_admin_init->set_default_settings();
		}

		update_option('a3rev_wc_email_inquiry_version', '1.2.5');
		update_option('a3rev_wc_email_inquiry_ultimate_version', '1.2.2');
		update_option('a3rev_wc_orders_quotes_version', '1.1.8');
	}

} else {
	// Add Predictive Search Activated Menu to Settings Menu
	add_action('admin_menu', 'wc_email_inquiry_authorization_admin_menu' );
}

function wc_email_inquiry_confirm_pin() {

	/**
	* Check pin for confirm plugin
	*/
	if(isset($_POST['wc_email_inquiry_pin_submit'])){
		$respone_api = __('Connection Error! Could not reach the a3API on Amazon Cloud, the network may be busy. Please try again in a few minutes.', 'wc_email_inquiry');
		$ji = md5(trim($_POST['P_pin']));
		$options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'sslverify'	=> false,
			'body' 		=> array(
				'act'			=> 'activate',
				'ssl'			=> $ji,
				'plugin' 		=> get_option('a3rev_wc_email_inquiry_plugin'),
				'domain_name'	=> $_SERVER['SERVER_NAME'],
				'address_ip'	=> $_SERVER['SERVER_ADDR'],
			)
		);
		$server_a3 = base64_decode('aHR0cDovL2EzYXBpLmNvbS9hdXRoYXBpL2luZGV4LnBocA==');
		$raw_response = wp_remote_request($server_a3 , $options);
		if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']) {
			$respone_api = $raw_response['body'];
		} elseif ( is_wp_error( $raw_response ) ) {
			$respone_api = __('Error: ', 'wc_email_inquiry').' '.$raw_response->get_error_message();
		}

		if($respone_api == md5('valid')) {
			update_option( 'a3rev_pin_wc_email_inquiry', sha1(md5('a3rev.com_'.str_replace( array( 'www.', 'http://', 'https://' ), '', get_option('siteurl') ).'_wc_email_inquiry')));
			update_option( 'a3rev_auth_wc_email_inquiry', $ji );
			update_option( 'a3rev_wc_email_inquiry_message', __('Thank you. This Authorization Key is valid.', 'wc_email_inquiry') );
		}else{
			delete_option('a3rev_pin_wc_email_inquiry' );
			delete_option('a3rev_auth_wc_email_inquiry' );
			update_option('a3rev_wc_email_inquiry_message', $respone_api );
		}

		delete_transient("a3rev_wc_email_inquiry_update_info");
		if( wc_orders_quotes_check_pin() ){
			update_option('a3rev_wc_email_inquiry_just_confirm', 1);
		}
	}
}

function wc_orders_quotes_check_pin() {
	$domain_name = get_option('siteurl');
	$a3rev_auth_key = get_option('a3rev_auth_wc_email_inquiry');
	$a3rev_pin_key = get_option('a3rev_pin_wc_email_inquiry');
	if (function_exists('is_multisite')){
		if (is_multisite()) {
			global $wpdb;
			$domain_name = $wpdb->get_var("SELECT option_value FROM ".$wpdb->options." WHERE option_name = 'siteurl'");
			if ( substr($domain_name, -1) == '/') {
				$domain_name = substr( $domain_name, 0 , -1 );
			}
		}
	}
	$nonwww_domain_name = str_replace( 'www.', '', $domain_name );
	$nonhttp_domain_name = str_replace( array( 'http://', 'https://' ), '', $nonwww_domain_name );
	$www_domain_name = str_replace( 'https://', 'https://www.', str_replace( 'http://', 'http://www.', $nonwww_domain_name ) );
	if ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonwww_domain_name.'_wc_email_inquiry'))) return true;
	elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonhttp_domain_name.'_wc_email_inquiry'))) return true;
	elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$www_domain_name.'_wc_email_inquiry'))) return true;
	else return false;
}

function wc_email_inquiry_authorization_admin_menu () {
	$admin_page = add_menu_page( __( 'Email & Cart Options', 'wc_email_inquiry' ), __( 'Email & Cart Options', 'wc_email_inquiry' ), 'manage_options', 'email-cart-options', 'wc_email_inquiry_authorization_form', null, '30.2456' );
}

function wc_email_inquiry_authorization_form() {
	if(isset($_POST['wc_email_inquiry_pin_submit'])){
		echo '<div id="" class="error"><p>'.get_option("a3rev_wc_email_inquiry_message").'</p></div>';
	}
	if(!file_exists(WC_EMAIL_INQUIRY_FILE_PATH."/encryp.inc")){
		echo '<font size="+2" color="#FF0000"> '. __("No find the encryp.inc file. Please copy encryp.inc file to folder", "wc_email_inquiry") .' '.WC_EMAIL_INQUIRY_FILE_PATH.' </font>';
	}else{
		$getfile = file_get_contents(WC_EMAIL_INQUIRY_FILE_PATH ."/encryp.inc");
		$str = "THlvTkNsQnNkV2RwYmlCT1lXMWxPaUJYVUMxQ2JHOW5VM1J2Y21VZ1ptOXlJRmR2Y21Sd2NtVnpjdzBLVUd4MVoybHVJRlZTU1RvZ2FIUjBjRG92TDNkM2R5NWlkV2xzWkdGaWJHOW5jM1J2Y21VdVkyOXRMdzBLUkdWelkzSnBjSFJwYjI0NklFRjFkRzl0WVhScFkyRnNiSGtnWjJWdVpYSmhkR1VnWlVKaGVTQmhabVpwYkdsaGRHVWdZbXh2WjNNZ2QybDBhQ0IxYm1seGRXVWdkR2wwYkdWekxDQjBaWGgwTENCbFFtRjVJR0YxWTNScGIyNXpMZzBLVm1WeWMybHZiam9nTXk0d0RRcEVZWFJsT2lCTllYSmphQ0F4TENBeU1EQTVEUXBCZFhSb2IzSTZJRUoxYVd4a1FVSnNiMmRUZEc5eVpRMEtRWFYwYUc5eUlGVlNTVG9nYUhSMGNEb3ZMM2QzZHk1aWRXbHNaR0ZpYkc5bmMzUnZjbVV1WTI5dEx3MEtLaThnRFFvTkNnMEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRmRRTFVKc2IyZFRkRzl5WlNCWGIzSmtjSEpsYzNNZ1VHeDFaMmx1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLRFFvTkNpTWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU09";
		if(strpos($getfile, $str) === FALSE){
			echo '<font size="+2" color="#FF0000"> '.__("encryp.inc was modified. Please keep it by default", "wc_email_inquiry").'. </font>';
		}else{
	?>
		<style>
		.woocommerce .submit {display:none;}
		</style>
        <div class="wrap">
		<div class="main_title"><div id="icon-ms-admin" class="icon32"><br></div><h2><?php _e("Enter Your Plugin Authorization Key", "wc_email_inquiry") ; ?></h2></div>
		<div style="clear:both;height:30px;"></div>
		<div>
        	<form method="post" action="">
			<p>
				<?php _e("Authorization Key", "wc_email_inquiry"); ?>: <input name="P_pin" type="text" id="P_pin" style="padding:10px; width:250px;" />
				<br/>
				<p>
					<input class="button button-primary" type="submit" name="wc_email_inquiry_pin_submit" value="<?php _e("Validate", "wc_email_inquiry"); ?>" />
				</p>
			</p>
            </form>
		</div>
        </div>
	<?php
		}
	}
}
?>
