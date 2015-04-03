<?php
/**
 * WC Email Inquiry WPML Functions
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * wpml_register_string()
 */
class WC_Email_Inquiry_WPML_Functions
{	
	public $plugin_wpml_name = 'WC Email Inquiry Pro';
	
	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		
		$this->wpml_ict_t();
		
	}
	
	/** 
	 * Register WPML String when plugin loaded
	 */
	public function plugins_loaded() {
		$this->wpml_register_dynamic_string();
		$this->wpml_register_static_string();
	}
	
	/** 
	 * Get WPML String when plugin loaded
	 */
	public function wpml_ict_t() {
		
		$plugin_name = 'wc_orders_quotes';
		
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_contact_form_settings' . '_get_settings', array( $this, 'ict_t_default_form_settings' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_customize_email_popup' . '_get_settings', array( $this, 'ict_t_default_form_style' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_contact_success' . '_get_setting', array( $this, 'ict_t_contact_success' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_customize_email_button' . '_get_settings', array( $this, 'ict_t_inquiry_button_style' ) );
		
		// For Read More Button
		add_filter( $plugin_name . '_' . 'wc_ei_read_more_hover_position_style' . '_get_settings', array( $this, 'ict_t_read_more_hover_style' ) );
		add_filter( $plugin_name . '_' . 'wc_ei_read_more_under_image_style' . '_get_settings', array( $this, 'ict_t_read_more_under_image_style' ) );
		
	}
	
	// Registry Dynamic String for WPML
	public function wpml_register_dynamic_string() {
		global $wc_ei_admin_interface;
		$wc_email_inquiry_contact_form_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_contact_form_settings', array() ) );
		$wc_ei_read_more_hover_position_style = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_ei_read_more_hover_position_style', array() ) );
		$wc_ei_read_more_under_image_style = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_ei_read_more_under_image_style', array() ) );
		$wc_email_inquiry_customize_email_popup = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_customize_email_popup', array() ) );
		$wc_email_inquiry_contact_success = esc_attr( stripslashes( get_option( 'wc_email_inquiry_contact_success', '' ) ) );
		$wc_email_inquiry_customize_email_button = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_customize_email_button', array() ) );
		
		if ( function_exists('icl_register_string') ) {
			
			// Default Form
			icl_register_string($this->plugin_wpml_name, 'Default Form - From Name', $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Header Title', $wc_email_inquiry_customize_email_popup['inquiry_contact_heading'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Button Title', $wc_email_inquiry_customize_email_popup['inquiry_contact_text_button'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Success Message', $wc_email_inquiry_contact_success );
			
			// Email Inquiry Button Title
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Button Title', $wc_email_inquiry_customize_email_button['inquiry_button_title'] );
			
			// Email Inquiry Button Hyperlink
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Text Before', $wc_email_inquiry_customize_email_button['inquiry_text_before'] );
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Hyperlink Text', $wc_email_inquiry_customize_email_button['inquiry_hyperlink_text'] );
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Trailing Text', $wc_email_inquiry_customize_email_button['inquiry_trailing_text'] );
			
			// Read More Hover Button Text
			icl_register_string($this->plugin_wpml_name, 'Read More Hover Button Text', $wc_ei_read_more_hover_position_style['hover_bt_text'] );
			
			// Read More Link Text Under Image
			icl_register_string($this->plugin_wpml_name, 'Read More Link Text', $wc_ei_read_more_under_image_style['under_image_link_text'] );
			
			// Read More Button Text Under Image
			icl_register_string($this->plugin_wpml_name, 'Read More Button Text', $wc_ei_read_more_under_image_style['under_image_bt_text'] );
			
		}
	}
	
	// Registry Static String for WPML
	public function wpml_register_static_string() {
		if ( function_exists('icl_register_string') ) {
			
			// Default Form
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Name', __( 'Name', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Email', __( 'Email', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Phone', __( 'Phone', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Subject', __( 'Subject', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Product Name', __( 'Product Name', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Message', __( 'Message', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Copy', __( 'Send a copy of this email to myself.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Email Subject', __( 'Email inquiry for',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Copy Email Subject', __( '[Copy]: Email inquiry for',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Copy', __( 'Send a copy of this email to myself.',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Name Error', __( 'Please enter your Name',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Email Error', __( 'Please enter valid Email addres',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Phone Error', __( 'Please enter your Phone',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Not Allow', __( "Sorry, this product don't enable email inquiry.", 'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Custom Form - Contact Form Title', __( 'You are making an Email Inquiry about:',  'wc_email_inquiry' ) );
			
		}
	}
	
	// Default Form Settings
	public function ict_t_default_form_settings( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_email_from_name'] ) ) 
			$current_settings['inquiry_email_from_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - From Name', $current_settings['inquiry_email_from_name'] ) : $current_settings['inquiry_email_from_name'] );
		
		return $current_settings;
	}
	
	// Default Form Style
	public function ict_t_default_form_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_contact_heading'] ) ) 
			$current_settings['inquiry_contact_heading'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Header Title', $current_settings['inquiry_contact_heading'] ) : $current_settings['inquiry_contact_heading'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_contact_text_button'] ) ) 
			$current_settings['inquiry_contact_text_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Send Button Title', $current_settings['inquiry_contact_text_button'] ) : $current_settings['inquiry_contact_text_button'] );
		
		return $current_settings;
	}
	
	// Default Form Contact Success Message
	public function ict_t_contact_success( $current_setting ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Contact Success Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Email Inquiry Button Title / Hyperlink
	public function ict_t_inquiry_button_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_button_title'] ) ) 
			$current_settings['inquiry_button_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Button Title', $current_settings['inquiry_button_title'] ) : $current_settings['inquiry_button_title'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_text_before'] ) ) 
			$current_settings['inquiry_text_before'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Text Before', $current_settings['inquiry_text_before'] ) : $current_settings['inquiry_text_before'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_hyperlink_text'] ) ) 
			$current_settings['inquiry_hyperlink_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Hyperlink Text', $current_settings['inquiry_hyperlink_text'] ) : $current_settings['inquiry_hyperlink_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_trailing_text'] ) ) 
			$current_settings['inquiry_trailing_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Trailing Text', $current_settings['inquiry_trailing_text'] ) : $current_settings['inquiry_trailing_text'] );
		
		return $current_settings;
	}
	
	// Read More Hover Button
	public function ict_t_read_more_hover_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['hover_bt_text'] ) ) 
			$current_settings['hover_bt_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Hover Button Text', $current_settings['hover_bt_text'] ) : $current_settings['hover_bt_text'] );
		
		return $current_settings;
	}
	
	// Read More Button Title / Hyperlink Under Image
	public function ict_t_read_more_under_image_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['under_image_bt_text'] ) ) 
			$current_settings['under_image_bt_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Button Text', $current_settings['under_image_bt_text'] ) : $current_settings['under_image_bt_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['under_image_link_text'] ) ) 
			$current_settings['under_image_link_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Link Text', $current_settings['under_image_link_text'] ) : $current_settings['under_image_link_text'] );
		
		return $current_settings;
	}
	
}

global $wc_ei_wpml;
$wc_ei_wpml = new WC_Email_Inquiry_WPML_Functions();

function wc_ei_ict_t_e( $name, $string ) {
	global $wc_ei_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $wc_ei_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	echo $string;
}

function wc_ei_ict_t__( $name, $string ) {
	global $wc_ei_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $wc_ei_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	return $string;
}
?>