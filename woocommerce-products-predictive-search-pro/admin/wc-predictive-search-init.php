<?php
/**
 * Register Activation Hook
 */
update_option('wc_predictive_search_plugin', 'woo_predictive_search');
function wc_predictive_install(){
	global $wpdb;
	$woocommerce_search_page_id = WC_Predictive_Search::create_page( _x('woocommerce-search', 'page_slug', 'woops'), 'woocommerce_search_page_id', __('Woocommerce Predictive Search', 'woops'), '[woocommerce_search]' );
	WC_Predictive_Search::auto_create_page_for_wpml( $woocommerce_search_page_id, _x('woocommerce-search', 'page_slug', 'woops'), __('Woocommerce Predictive Search', 'woops'), '[woocommerce_search]' );

	// Set Settings Default from Admin Init
	global $wc_predictive_search_admin_init;
	$wc_predictive_search_admin_init->set_default_settings();

	update_option('wc_predictive_search_version', '2.4.1');
	update_option('wc_predictive_search_plugin', 'woo_predictive_search');
	delete_transient("woo_predictive_search_update_info");
	flush_rewrite_rules();

	update_option('wc_predictive_search_just_installed', true);
}


function wc_predictive_deactivate(){

	delete_transient("woo_predictive_search_update_info");

	$respone_api = __('Connection Error! Could not reach the a3API on Amazon Cloud, the network may be busy. Please try again in a few minutes.', 'woops');
	$options = array(
		'method' 	=> 'POST',
		'timeout' 	=> 45,
		'body' 		=> array(
			'act'			=> 'deactivate',
			'ssl'			=> get_option('a3rev_auth_woo_predictive_search'),
			'plugin' 		=> get_option('wc_predictive_search_plugin'),
			'domain_name'	=> $_SERVER['SERVER_NAME'],
			'address_ip'	=> $_SERVER['SERVER_ADDR'],
		)
	);
	$server_a3 = base64_decode("aHR0cDovL2EzYXBpLmNvbS9hdXRoYXBpL2luZGV4LnBocA==");
	$raw_response = wp_remote_request($server_a3 , $options);
	if ( !is_wp_error( $raw_response ) && $raw_response['response']['code'] >= 200 && $raw_response['response']['code'] < 300) {
		$respone_api = $raw_response['body'];
	}

	delete_option ( 'a3rev_pin_woo_predictive_search' );
	delete_option ( 'a3rev_auth_woo_predictive_search' );

}

function woops_init() {
	if ( get_option('wc_predictive_search_just_installed') ) {
		delete_option('wc_predictive_search_just_installed');
		wp_redirect( admin_url( 'admin.php?page=woo-predictive-search', 'relative' ) );
		exit;
	}
	load_plugin_textdomain( 'woops', false, WOOPS_FOLDER.'/languages' );
}

// Add language
add_action('init', 'woops_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WC_Predictive_Search_Hook_Filter', 'a3_wp_admin' ) );

add_action( 'plugins_loaded', array( 'WC_Predictive_Search_Hook_Filter', 'plugins_loaded' ), 8 );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WC_Predictive_Search_Hook_Filter', 'plugin_extra_links'), 10, 2 );

function register_widget_woops_predictive_search() {
	register_widget('WC_Predictive_Search_Widgets');
}

if(isset($_POST['wc_predictive_pin_submit'])){
	wc_predictive_confirm_pin();
}

$check_encryp_file = false;
$str = "THlvTkNsQnNkV2RwYmlCT1lXMWxPaUJYVUMxQ2JHOW5VM1J2Y21VZ1ptOXlJRmR2Y21Sd2NtVnpjdzBLVUd4MVoybHVJRlZTU1RvZ2FIUjBjRG92TDNkM2R5NWlkV2xzWkdGaWJHOW5jM1J2Y21VdVkyOXRMdzBLUkdWelkzSnBjSFJwYjI0NklFRjFkRzl0WVhScFkyRnNiSGtnWjJWdVpYSmhkR1VnWlVKaGVTQmhabVpwYkdsaGRHVWdZbXh2WjNNZ2QybDBhQ0IxYm1seGRXVWdkR2wwYkdWekxDQjBaWGgwTENCbFFtRjVJR0YxWTNScGIyNXpMZzBLVm1WeWMybHZiam9nTXk0d0RRcEVZWFJsT2lCTllYSmphQ0F4TENBeU1EQTVEUXBCZFhSb2IzSTZJRUoxYVd4a1FVSnNiMmRUZEc5eVpRMEtRWFYwYUc5eUlGVlNTVG9nYUhSMGNEb3ZMM2QzZHk1aWRXbHNaR0ZpYkc5bmMzUnZjbVV1WTI5dEx3MEtLaThnRFFvTkNnMEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRmRRTFVKc2IyZFRkRzl5WlNCWGIzSmtjSEpsYzNNZ1VHeDFaMmx1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLRFFvTkNpTWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU09";
if(file_exists(WOOPS_FILE_PATH."/encryp.inc")){
	$getfile = file_get_contents(WOOPS_FILE_PATH ."/encryp.inc");
	if(strpos($getfile, $str) !== FALSE){
		$check_encryp_file = true;
	}
}
if($check_encryp_file == true && woo_predictive_search_check_pin() ){

// Need to call Admin Init to show Admin UI
global $wc_predictive_search_admin_init;
$wc_predictive_search_admin_init->init();

// Custom Rewrite Rules
add_action('init', array('WC_Predictive_Search_Hook_Filter', 'custom_rewrite_rule'), 101 );

// Registry widget
add_action('widgets_init', 'register_widget_woops_predictive_search');

// AJAX hide yellow message dontshow
add_action('wp_ajax_wc_ps_yellow_message_dontshow', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dontshow') );
add_action('wp_ajax_nopriv_wc_ps_yellow_message_dontshow', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dontshow') );

// AJAX hide yellow message dismiss
add_action('wp_ajax_wc_ps_yellow_message_dismiss', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dismiss') );
add_action('wp_ajax_nopriv_wc_ps_yellow_message_dismiss', array('WC_Predictive_Search_Hook_Filter', 'yellow_message_dismiss') );

// Add shortcode [woocommerce_search]
add_shortcode('woocommerce_search', array('WC_Predictive_Search_Shortcodes', 'parse_shortcode_search_result'));

// Add shortcode [woocommerce_widget_search]
add_shortcode('woocommerce_search_widget', array('WC_Predictive_Search_Shortcodes', 'parse_shortcode_search_widget'));

// Add Predictive Search Meta Box to all post type
add_action( 'add_meta_boxes', array('WC_Predictive_Search_Meta','create_custombox'), 9 );

// Save Predictive Search Meta Box to all post type
if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	add_action( 'save_post', array('WC_Predictive_Search_Meta','save_custombox' ) );
}

// Add search widget icon to Page Editor
if (in_array (basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php') ) ) {
	add_action('media_buttons_context', array('WC_Predictive_Search_Shortcodes', 'add_search_widget_icon') );
	add_action('admin_footer', array('WC_Predictive_Search_Shortcodes', 'add_search_widget_mce_popup'));
}

if (!is_admin()) {
	add_filter( 'posts_search', array('WC_Predictive_Search_Hook_Filter', 'search_by_title_only'), 500, 2 );
	add_filter( 'posts_orderby', array('WC_Predictive_Search_Hook_Filter', 'predictive_posts_orderby'), 500, 2 );
	add_filter( 'get_meta_sql', array('WC_Predictive_Search_Hook_Filter', 'remove_where_on_focuskw_meta'), 500, 6 );
	// Check role scoper plugin is activated
	if ( function_exists('scoper_activate') ) {
		add_filter( 'posts_request', array('WC_Predictive_Search_Hook_Filter', 'posts_request_unconflict_role_scoper_plugin'), 500, 2);
	}
}

add_filter( 'pre_get_posts', array('WC_Predictive_Search_Hook_Filter', 'pre_get_posts'), 500 );

if ( ! is_admin() )
	add_action('init',array('WC_Predictive_Search_Hook_Filter','add_frontend_style'));

function woo_predictive_search_widget($ps_echo = true, $product_items = 0, $p_sku_items = 0, $p_cat_items = 0, $p_tag_items = 0, $post_items = 0, $page_items = 0, $character_max = 100, $style='', $global_search = true, $show_price = true) {

	if ($global_search) $global_search = 'yes'; else $global_search = 'no';
	if ($show_price) $show_price = 'yes'; else $show_price = 'no';

	$product_items = get_option('woocommerce_search_product_items', $product_items);
	$p_sku_items = get_option('woocommerce_search_p_sku_items', $p_sku_items);
	$p_cat_items = get_option('woocommerce_search_p_cat_items', $p_cat_items);
	$p_tag_items = get_option('woocommerce_search_p_tag_items', $p_tag_items);
	$post_items = get_option('woocommerce_search_post_items', $post_items);
	$page_items = get_option('woocommerce_search_page_items', $page_items);
	$show_price = get_option('woocommerce_search_show_price', $show_price);
	$character_max = get_option('woocommerce_search_character_max', $character_max);
	if ( class_exists('SitePress') ) {
		$current_lang = ICL_LANGUAGE_CODE;
		$search_box_texts = get_option('woocommerce_search_box_text', array() );
		if ( is_array($search_box_texts) && isset($search_box_texts[$current_lang]) ) $search_box_text = esc_attr( stripslashes( trim( $search_box_texts[$current_lang] ) ) );
		else $search_box_text = '';
	} else {
		$search_box_text = get_option('woocommerce_search_box_text', '' );
		if ( is_array($search_box_text) ) $search_box_text = '';
	}

	$custom_style = '';
	if (get_option('woocommerce_search_custom_style', $style) != '') $custom_style .= get_option('woocommerce_search_custom_style', $style); else $custom_style .= $style;
	if (get_option('woocommerce_search_width') != '') $custom_style .= 'width:'.get_option('woocommerce_search_width').'px !important;';
	if (get_option('woocommerce_search_padding_top') != '') $custom_style .= 'padding-top:'.get_option('woocommerce_search_padding_top').'px !important;';
	if (get_option('woocommerce_search_padding_bottom') != '') $custom_style .= 'padding-bottom:'.get_option('woocommerce_search_padding_bottom').'px !important;';
	if (get_option('woocommerce_search_padding_left') != '') $custom_style .= 'padding-left:'.get_option('woocommerce_search_padding_left').'px !important;';
	if (get_option('woocommerce_search_padding_right') != '') $custom_style .= 'padding-right:'.get_option('woocommerce_search_padding_right').'px !important;';

	$global_search = get_option('woocommerce_search_product_name', $global_search);

	$items_search_default = WC_Predictive_Search_Widgets::get_items_search();
	$items = array();
	foreach ($items_search_default as $key => $data) {
		if (isset(${$key.'_items'}) )
			$items[$key] = ${$key.'_items'};
		else
			$items[$key] = $data['number'];
	}
	$widget_id = rand(100, 10000);
	if ($global_search == 'yes') $global_search = 1;
	else $global_search = 0;
	if ($show_price == 'yes') $show_price = 1;
	else $show_price = 0;

	$ps_search_html = WC_Predictive_Search_Widgets::woops_results_search_form($widget_id, $items, $character_max, $custom_style, $global_search, $search_box_text, $show_price);

	if ( $ps_echo != false ) echo $ps_search_html;
	else return $ps_search_html;
}

// Upgrade to 2.0
if(version_compare(get_option('wc_predictive_search_version'), '2.0') === -1){
	WC_Predictive_Search::upgrade_version_2_0();
	update_option('wc_predictive_search_version', '2.0');
}

update_option('wc_predictive_search_version', '2.4.1');

}else{
	add_action('admin_menu', 'wc_predictive_authorization_admin_menu' );
}

function wc_predictive_authorization_admin_menu() {
	$woo_page = 'woocommerce';
	$admin_page = add_submenu_page( $woo_page , __('Predictive Search', 'woops'), __('Predictive Search', 'woops'), 'manage_options', 'woo-predictive-search', 'wc_predictive_authorization' );
}


function wc_predictive_confirm_pin() {

	/**
	* Check pin for confirm plugin
	*/
	if(isset($_POST['wc_predictive_pin_submit'])){

		$respone_api = __('Connection Error! Could not reach the a3API on Amazon Cloud, the network may be busy. Please try again in a few minutes.', 'woops');
		$ji = md5(trim($_POST['P_pin']));
		$options = array(
			'method' 	=> 'POST',
			'timeout' 	=> 45,
			'body' 		=> array(
				'act'			=> 'activate',
				'ssl'			=> $ji,
				'plugin' 		=> get_option('wc_predictive_search_plugin'),
				'domain_name'	=> $_SERVER['SERVER_NAME'],
				'address_ip'	=> $_SERVER['SERVER_ADDR'],
			)
		);
		$server_a3 = base64_decode("aHR0cDovL2EzYXBpLmNvbS9hdXRoYXBpL2luZGV4LnBocA==");
		$raw_response = wp_remote_request($server_a3 , $options);
		if ( !is_wp_error( $raw_response ) && $raw_response['response']['code'] >= 200 && $raw_response['response']['code'] < 300) {
			$respone_api = $raw_response['body'];
		} elseif ( is_wp_error( $raw_response ) ) {
			$respone_api = __('Error: ', 'woops').' '.$raw_response->get_error_message();
		}

		if($respone_api == md5('valid')) {
			update_option( 'a3rev_pin_woo_predictive_search', sha1(md5('a3rev.com_'.str_replace( array( 'www.', 'http://', 'https://' ), '', get_option('siteurl') ).'_woo_predictive_search')));
			update_option( 'a3rev_auth_woo_predictive_search', $ji );
			update_option( 'woo_predictive_search_message', __('Thank you. This Authorization Key is valid.', 'woops') );
		}else{
			delete_option('a3rev_pin_woo_predictive_search' );
			delete_option('a3rev_auth_woo_predictive_search' );
			update_option('woo_predictive_search_message', $respone_api );
		}
		if( woo_predictive_search_check_pin() ){
			delete_transient("woo_predictive_search_update_info");
			update_option('a3rev_woo_predictivesearch_just_confirm', 1);
		}
	}
}

function woo_predictive_search_check_pin() {
	$domain_name = get_option('siteurl');
	$a3rev_auth_key = get_option('a3rev_auth_woo_predictive_search');
	$a3rev_pin_key = get_option('a3rev_pin_woo_predictive_search');
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
	if ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonwww_domain_name.'_woo_predictive_search'))) return true;
	elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$nonhttp_domain_name.'_woo_predictive_search'))) return true;
	elseif ( $a3rev_auth_key != '' && $a3rev_pin_key == sha1(md5('a3rev.com_'.$www_domain_name.'_woo_predictive_search'))) return true;
	else return false;
}

function wc_predictive_authorization(){
	// Determine the current tab in effect.
	if(isset($_REQUEST['wc_predictive_pin_submit'])){
		echo '<div id="" class="error"><p>'.get_option("woo_predictive_search_message").'</p></div>';
	}
	if(!file_exists(WOOPS_FILE_PATH."/encryp.inc")){
		echo '<font size="+2" color="#FF0000"> '. __("No find the encryp.inc file. Please copy encryp.inc file to folder", "woops") .' '.WOOPS_FILE_PATH.' </font>';
	}else{
		$getfile = file_get_contents(WOOPS_FILE_PATH ."/encryp.inc");
		$str = "THlvTkNsQnNkV2RwYmlCT1lXMWxPaUJYVUMxQ2JHOW5VM1J2Y21VZ1ptOXlJRmR2Y21Sd2NtVnpjdzBLVUd4MVoybHVJRlZTU1RvZ2FIUjBjRG92TDNkM2R5NWlkV2xzWkdGaWJHOW5jM1J2Y21VdVkyOXRMdzBLUkdWelkzSnBjSFJwYjI0NklFRjFkRzl0WVhScFkyRnNiSGtnWjJWdVpYSmhkR1VnWlVKaGVTQmhabVpwYkdsaGRHVWdZbXh2WjNNZ2QybDBhQ0IxYm1seGRXVWdkR2wwYkdWekxDQjBaWGgwTENCbFFtRjVJR0YxWTNScGIyNXpMZzBLVm1WeWMybHZiam9nTXk0d0RRcEVZWFJsT2lCTllYSmphQ0F4TENBeU1EQTVEUXBCZFhSb2IzSTZJRUoxYVd4a1FVSnNiMmRUZEc5eVpRMEtRWFYwYUc5eUlGVlNTVG9nYUhSMGNEb3ZMM2QzZHk1aWRXbHNaR0ZpYkc5bmMzUnZjbVV1WTI5dEx3MEtLaThnRFFvTkNnMEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJRmRRTFVKc2IyZFRkRzl5WlNCWGIzSmtjSEpsYzNNZ1VHeDFaMmx1SUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeUFnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJdzBLSXlBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSXcwS0l5QWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0lDQWdJQ0FnSUNBZ0l3MEtJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJdzBLRFFvTkNpTWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU1qSXlNakl5TWpJeU09";
		if(strpos($getfile, $str) === FALSE){
			echo '<font size="+2" color="#FF0000"> '.__("encryp.inc was modified. Please keep it by default", "woops").'. </font>';
		}else{
?>
	<style>
		.woocommerce p.submit {display:none;}
	</style>
    <div class="wrap">
	<div class="main_title"><div id="icon-ms-admin" class="wpec-compare-icon icon32"><br></div><h2><?php _e("Enter Your Plugin Authorization Key", "woops") ; ?></h2></div>
    <div style="clear:both;height:30px;"></div>
    <div>
    	<form method="post" action="">
    	<p>
                <?php _e("Authorization Key", "woops"); ?>: <input name="P_pin" type="text" id="P_pin" style="padding:10px; width:250px;" />
          		<br/>
          		<div class="submit">
            		<input class="button button-primary" type="submit" name="wc_predictive_pin_submit" value="<?php _e("Validate", "woops"); ?>" />
         		</div>
        </p>
        </form>
    </div>
    </div>
<?php
		}
	}
}
?>