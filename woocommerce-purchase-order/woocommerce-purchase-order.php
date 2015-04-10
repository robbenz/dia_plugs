<?php
/**
 * Plugin Name: WooCommerce Purchase Order Payment Gateway
 *
 * Description: A Payment Gateway that allows select customers to complete a transaction by paying via purchase order. 
 * Once you are ready for the customer to pay, change the order status from 
 * "On Hold" to "Pending" and save the 
 * order, making any other changes to the order, such as shipping charges and 
 * order total, and click "Email Invoice". The customer will receive an email
 * with a "Pay" link, where they will have the opportunity to pay for the order.
 * 
 *
 *
 * Plugin URI: http://bryanheadrick.com/downloads/woocommerce-purchaseorder/
 * Version: 2.4.2
 * Author: Catman Studios
 * Author URI: http://catmanstudios.com
 * License: GPLv3
 * @class 		WC_Purchase_Order
 * @extends		WC_Payment_Gateway
 * @author		Catman Studios
 *****************************
 * 
 */

add_action('plugins_loaded', 'init_woocommerce_purchaseorder', 0);


function init_woocommerce_purchaseorder(){

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) { return; }

class WC_Purchase_Order extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
	public function __construct() {
        $this->id				= 'purchaseorder';
        $this->icon 			= apply_filters('woocommerce_purchaseorder_icon', '');
        $this->has_fields 		= false;
        $this->method_title     = __( 'Purchase Order', 'woocommerce' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables
if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
  $this->title = $this->settings['title'];
		$this->description = $this->settings['description'];
} else {
  //wc2.0
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
}
	
		// Actions
		/* 1.6.6 */
add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
 
/* 2.0.0 */
add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    	add_action('woocommerce_thankyou_purchaseorder', array(&$this, 'thankyou_page'));

    	// Customer Emails
    	add_action('woocommerce_email_before_order_table', array(&$this, 'email_instructions'), 10, 2);
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

    	$fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'woocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Enable Purchase Order Payment', 'woocommerce' ),
							'default' => 'yes'
						),
                       
			'allusers' => array(
							'title' => __( 'Enable for All Users', 'woocommerce' ),
							'type' => 'checkbox',
							'description' => __( 'Allow Purchase Order Payment for all users.', 'woocommerce' ),
							'default' => __( 'no', 'woocommerce' )
						),
            'exclusive' => array(
							'title' => __( 'Mutually Exclusive', 'woocommerce' ),
							'type' => 'checkbox',
							'description' => __( 'When Purchase Order is available, all other Payment Methods are hidden', 'woocommerce' ),
							'default' => __( 'no', 'woocommerce' )
						),
			'poprompt' => array(
							'title' => __( 'Purchase Order# Prompt', 'woocommerce' ),
							'type' => 'checkbox',
							'description' => __( 'Prompt the customer to enter a Purchase Order Number.', 'woocommerce' ),
							'default' => __( 'yes', 'woocommerce' )
						),
              'porequired' => array(
                                                        'title' => __("Purchase Order# Prompt Required", 'woocommerce'),
                                                        'type' => 'checkbox',
                                                        'description' => 'If the Purchase Order Prompt is enabled and this option is also enabled, the order cannot be placed unless that field is filled'
                                                ),
			//'poupload' => array(
			//				'title' => __( 'Purchase Order Form Upload', 'woocommerce' ),
			//				'type' => 'checkbox',
			//				'description' => __( 'Allow the user to upload a purchase order form document.', 'woocommerce' ),
			//				'default' => __( 'no', 'woocommerce' )
			//			),
			'title' => array(
							'title' => __( 'Title', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
							'default' => __( 'Purchase Order', 'woocommerce' )
						),
			'description' => array(
							'title' => __( 'Customer Message', 'woocommerce' ),
							'type' => 'textarea',
							'description' => __( 'Let the customer know that their order won\'t be shipping until you receive payment.', 'woocommerce' ),
							'default' => __( 'You will be contacted to arrange payment once shipping costs have been calculated.', 'woocommerce' )
						)
			);
        
        $this->form_fields = apply_filters('wc_purchaseorder_fields', $fields );

    }



	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {

    	?>
    	<h3><?php _e('Purchase Order Payment', 'woocommerce'); ?></h3>
    	<p><?php _e('Allows purchaseorder payments. ', 'woocommerce'); ?></p>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
    	<?php
    }


    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
	function thankyou_page() {
		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( $description ) );
	}


    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @return void
     */
	function email_instructions( $order, $sent_to_admin ) {
    	if ( $sent_to_admin ) return;

    	if ( $order->status !== 'on-hold') return;

    	if ( $order->payment_method !== 'purchaseorder') return;

		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( $description ) );
	}


    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
	function process_payment( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the purchaseorder)
		$order->update_status('on-hold', __('Awaiting Purchase order Completion', 'woocommerce'));

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		
if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {

 // Empty awaiting payment session
		unset($_SESSION['order_awaiting_payment']);
} 

if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
    $woocommerce->cart->empty_cart();
    return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('thanks'))))
		);
}else{
    WC()->cart->empty_cart();
    return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_return_url( $order )
		);
}
		// Return thankyou redirect
		

	}
          function payment_fields() {
             $option = get_option('woocommerce_purchaseorder_settings');
             ob_start();
               if($option['poprompt']== 'yes'):
              ?>	<p class="form-row" style="width:250px;">
  <?php  
            $required= ($option['porequired']=='yes'?'Required':'Optional');
                
            echo       woocommerce_form_field( 'purchase_order_number', array(
        'type'          => 'text',
        'class'         => array('purchase-order-number form-row-wide'),
        'label'         => __('Purchase Order Number'),
        'required'      => ($option['porequired']=='yes'?true:false),
        'placeholder'       => 'Enter Purchase Order Number ('.$required.')',
        ), '');    ?>

		</p> <?php
                endif;
                echo $option['description'];
                $output['standard'] = ob_get_flush();
                
                $output =  apply_filters('wc_purchaseorder_payment_fields', $output);
                $strout = ''; 
                foreach($output as $out){
                    $strout .= $out; 
                }
                return $strout; 
          }


}
/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'wcpo_checkout_field_process');
 
function wcpo_checkout_field_process() {
    global $woocommerce; 
    // if required...
    // Check if set, if its not set add an error.
      $option = get_option('woocommerce_purchaseorder_settings');
    if (!$_POST['purchase_order_number'] && $_POST['payment_method']=='purchaseorder' && $option['poprompt']=='yes' && $option['porequired']=='yes')
         $woocommerce->add_error( __('A purchase order number is required.') );
}

/**
 * Add the gateway to WooCommerce
 *
 * @access public
 * @param array $methods
 * @package		WooCommerce/Classes/Payment
 * @return array
 */
function add_purchaseorder_gateway( $methods ) {
	$methods[] = 'WC_Purchase_Order';
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_purchaseorder_gateway' );


add_filter('woocommerce_available_payment_gateways','wcpo_conditionally_availabile');
 // Hook in
/**
 * Add the field to the checkout
 **/



/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'wcpo_checkout_field_update_order_meta');
 
function wcpo_checkout_field_update_order_meta( $order_id ) {
    if ($_POST['purchase_order_number']) update_post_meta( $order_id, 'Purchase Order Number', esc_attr($_POST['purchase_order_number']));
}
function wcpo_conditionally_availabile($_available_gateways){
      $paypageid = get_option('woocommerce_pay_page_id');
    $options = get_option('woocommerce_purchaseorder_settings');
    global $woocommerce;
    if(!$paypageid ){
        /*** No pay page is set ***/
        if(isset($_GET['pay_for_order']) && $_GET['pay_for_order']=='true'){
            $ispaypage = true; 
        }
        else $ispaypage = false;
    }else{
        if(is_page($paypageid)) $ispaypage = true; 
        if(isset($_GET['pay_for_order']) && $_GET['pay_for_order']=='true'){
            $ispaypage = true; 
        }
        else $ispaypage = false; 
    }
    
    $availability_filter = apply_filters('wc_purchase_order_isavailable', true,  $_POST);
    if (( (get_current_user_id() && get_the_author_meta('haspurchaseorder',get_current_user_id()  )==1  ) || $options['allusers'] == 'yes' ) && (!$ispaypage && !isset($_GET['pay_for_order']) ) && $availability_filter ) {
    //we can disable all other payment methods here when purchaseorder is available if desired
     if((isset($options['exclusive']) && $options['exclusive']=='yes' && ($options['allusers'] == 'yes' ||  get_the_author_meta('haspurchaseorder',get_current_user_id()  )==1  ))&& (!isset($_GET['action']) ||$_GET['action']!='woocommerce-checkout') && ((!isset($_POST['woocommerce_pay'] ) || $_POST['woocommerce_pay']!=1) && !isset($_GET['pay_for_order']))){  
           $newgateways['purchaseorder'] = $_available_gateways['purchaseorder'];
       $_available_gateways = $newgateways;
     
     }  
} else {
    //if this user isn't allowed to use the purchaseorder, then make it unavailable
    unset($_available_gateways['purchaseorder']);
}
    
    return $_available_gateways;
}

add_action( 'edit_user_profile', 'wcpo_cof_user_option' );     // displays field to another administrator
add_action( 'show_user_profile', 'wcpo_cof_user_option' );     // displays field to another administrator

function wcpo_cof_user_option( $user ) { 
    if(!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) return; 
		
    ?>

	<h3>Payment Settings</h3>

	<table class="form-table">

		<tr>
			<th><label for="haspurchaseorder">Allow Purchase Order</label></th>

			<td>
                            <input type="checkbox" name="haspurchaseorder" id="haspurchaseorder"  value="1"<?php checked(get_the_author_meta('haspurchaseorder',$user->ID),1 ); ?> /><br />
				<span class="description">Do you want allow this customer to use to purchase orders?</span>
			</td>
		</tr>

	</table>
        
       
<?php }

add_action( 'personal_options_update', 'save_wcpo_profile_fields' );
add_action( 'edit_user_profile_update', 'save_wcpo_profile_fields' );

function save_wcpo_profile_fields( $user_id ) {

	 if(!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) return; 
		

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_user_meta( $user_id, 'haspurchaseorder', $_POST['haspurchaseorder'] );
}

add_action('woocommerce_email_after_order_table','display_po_number');
add_action('woocommerce_order_details_after_order_table','display_po_number');
function display_po_number($order){
    $option = get_option('woocommerce_purchaseorder_settings');
               
    if($ponum=get_post_meta($order->id, 'Purchase Order Number', true)){
    echo '<h3>Purchase Order Number:</h3>' .  get_post_meta($order->id, 'Purchase Order Number', true);
    }
}
//add_action('wp_enqueue_scripts', 'wcpo_scripts');
function wcpo_scripts(){
//    wp_enqueue_script('purchaseorder_ajaxupload',plugins_url('', __FILE__) . '/assets/js/ajaxupload.js',array('jquery'));

  //  wp_enqueue_script('purchaseorder_script',plugins_url('', __FILE__) . '/assets/js/script.js',array('jquery', 'purchaseorder_ajaxupload'));
                        
//   $l10n = array( 'wpajaxurl'=>admin_url( 'admin-ajax.php' ));                        
  //                      wp_localize_script('purchaseorder_script', 'wcpo', $l10n);
                       
}

// Add settings link on plugin page
function wcpo_settings_link($links) { 
    if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
        $settings_link = '<a href="admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Purchase_Order">Settings</a>'; 
        
    }else{
  $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=wc_purchase_order">Settings</a>'; 
    }
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wcpo_settings_link' );

add_action( 'add_meta_boxes', 'wcpo_add_order_sendinvoice_metaboxes' );
function wcpo_add_order_sendinvoice_metaboxes(){
    global $post;
    
    if(version_compare( WOOCOMMERCE_VERSION, '2.2', '<' )){
		$status_terms = wp_get_post_terms($post->ID, 'shop_order_status');
		$order_status = $status_terms[0]->slug;
		}else{
		global $wpdb;
		$order_status = $post->post_status;
                $order_status = str_replace('wc-', '', $order_status);
                
			}
                       
if($order_status == 'on-hold'){
    add_meta_box('wcpo_sendinvoice', 'Send Invoice Email', 'wcpo_invoice_box', 'shop_order', 'side', 'default');
}else return;
    
}
add_action( 'wp_ajax_send_pay_email', 'wcpo_send_pay_email' );
add_action( 'wp_ajax_nopriv_send_pay_email', 'wcpo_send_pay_email' );
add_action( 'add_meta_boxes', 'wcpo_add_order_metaboxes' );
function wcpo_add_order_metaboxes(){
    global $post; 
    
    add_meta_box('wcpo_num', 'Purchase Order Number', 'wcpo_num_box', 'shop_order', 'side', 'default');
}
function wcpo_num_box(){
    global $post;
if(!is_admin()) return;
$ponum = get_post_meta( $post->ID , 'Purchase Order Number' , true );
    if($ponum){
   
    echo 'PO#: ' . $ponum;
    }
    else {
         ?>
    <label for="po_num">Enter PO#</label>
    <input type="text" name="po_num" id="po_num" />
    <?php  
    wp_nonce_field( 'wcpo_nonce', 'wcpo_nonce' );
    }
}
add_action( 'save_post', 'wcpo_meta_save' );
function wcpo_meta_save($post_id){
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['wcpo_nonce'] ) || !wp_verify_nonce( $_POST['wcpo_nonce'], 'wcpo_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
    if( isset( $_POST['po_num'] ) )
        update_post_meta( $post_id, 'Purchase Order Number', $_POST['po_num'] );
     
}
function wcpo_send_pay_email() {
	global $wpdb; // this is how you get access to the database
        $orderid = $_POST['orderid'];
        if(version_compare( WOOCOMMERCE_VERSION, '2.2', '<' )){
	$update = wp_set_post_terms( $orderid, 'pending', 'shop_order_status' );
        }
        else{
            $post = get_post($orderid);
            wp_transition_post_status( 'pending', 'on-hold', $post );
        }
        if(!is_wp_error($update)){
            //send invoice email
            $order =  new WC_Order($orderid);
            do_action( 'woocommerce_before_resend_order_emails', $order );

				// Ensure gateways are loaded in case they need to insert data into the emails
				WC()->payment_gateways();
				WC()->shipping();

				// Load mailer
				$mailer = WC()->mailer();

				$email_to_send = 'customer_invoice';

				$mails = $mailer->get_emails();

				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( $mail->id == $email_to_send ) {
							$mail->trigger( $order->id );
						}
					}
				}

				do_action( 'woocommerce_after_resend_order_email', $order, $email_to_send );

            
            
            
            
            
            
        }else{
            echo 'An error was encountered';
        }
       
        echo 'Email Sent Successfully!';
	die(); // this is required to return a proper result
}

function wcpo_invoice_box(){
    
    
    echo '<button data-order-id="'.$_GET['post'].'"  id="send_pay_email">Request Payment</button><span class="invoice_loading" style="display:none">Loading...</span><span class="invoice-email-sent" style="display:none">Invoice Email Sent!</span>';
?>
        <script>
        jQuery('#send_pay_email').click(function(e){
           e.preventDefault();
           console.log(ajaxurl);
           var data = {
		action: 'send_pay_email',
                orderid:jQuery(this).data('order-id')
	};
        jQuery('.invoice_loading').show();
	jQuery.post(ajaxurl, data, function(response) {
            console.log(response);
		jQuery('.invoice_loading').hide();
                jQuery('.invoice-email-sent').html(response).show();
	});
        });
        </script>
        
        
        <?php



}




}

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Displays an inactive message if the API License Key has not yet been activated
 */
if ( get_option( 'api_manager_wcpo_activated' ) != 'Activated' ) {
    add_action( 'admin_notices', 'API_Manager_wcpo::am_wcpo_inactive_notice' );
}
class API_Manager_wcpo {

	/**
	 * Self Upgrade Values
	 */
	// Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
	public $upgrade_url = 'http://catmanstudios.com/';

	/**
	 * @var string
	 */
	public $version = '2.4.2';

	/**
	 * @var string
	 * This version is saved after an upgrade to compare this db version to $version
	 */
	public $api_manager_wcpo_version_name = 'plugin_api_manager_wcpo_version';

	/**
	 * @var string
	 */
	public $plugin_url;

	/**
	 * @var string
	 * used to defined localization for translation, but a string literal is preferred
	 *
	 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/issues/59
	 * http://markjaquith.wordpress.com/2011/10/06/translating-wordpress-plugins-and-themes-dont-get-clever/
	 * http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 */
	public $text_domain = 'api-manager-wcpo';

	/**
	 * Data defaults
	 * @var mixed
	 */
	private $ame_software_product_id;

	public $ame_data_key;
	public $ame_api_key;
	public $ame_activation_email;
	public $ame_product_id_key;
	public $ame_instance_key;
	public $ame_deactivate_checkbox_key;
	public $ame_activated_key;

	public $ame_deactivate_checkbox;
	public $ame_activation_tab_key;
	public $ame_deactivation_tab_key;
	public $ame_settings_menu_title;
	public $ame_settings_title;
	public $ame_menu_tab_activation_title;
	public $ame_menu_tab_deactivation_title;

	public $ame_options;
	public $ame_plugin_name;
	public $ame_product_id;
	public $ame_renew_license_url;
	public $ame_instance_id;
	public $ame_domain;
	public $ame_software_version;
	public $ame_plugin_or_theme;

	public $ame_update_version;

	public $ame_update_check = 'am_wcpo_plugin_update_check';

	/**
	 * Used to send any extra information.
	 * @var mixed array, object, string, etc.
	 */
	public $ame_extra;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
        	self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	private function __wakeup() {}

	public function __construct() {

		// Run the activation function
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		// Ready for translation
		//load_plugin_textdomain( $this->text_domain, false, dirname( untrailingslashit( plugin_basename( __FILE__ ) ) ) . '/languages' );

		if ( is_admin() ) {

			// Check for external connection blocking
			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );

			/**
			 * Software Product ID is the product title string
			 * This value must be unique, and it must match the API tab for the product in WooCommerce
			 */
			$this->ame_software_product_id = 'WooCommerce Purchase Order Payment Gateway';

			/**
			 * Set all data defaults here
			 */
			$this->ame_data_key 				= 'api_manager_wcpo';
			$this->ame_api_key 					= 'api_key';
			$this->ame_activation_email 		= 'activation_email';
			$this->ame_product_id_key 			= 'api_manager_wcpo_product_id';
			$this->ame_instance_key 			= 'api_manager_wcpo_instance';
			$this->ame_deactivate_checkbox_key 	= 'api_manager_wcpo_deactivate_checkbox';
			$this->ame_activated_key 			= 'api_manager_wcpo_activated';

			/**
			 * Set all admin menu data
			 */
			$this->ame_deactivate_checkbox 			= 'am_deactivate_wcpo_checkbox';
			$this->ame_activation_tab_key 			= 'api_manager_wcpo_dashboard';
			$this->ame_deactivation_tab_key 		= 'api_manager_wcpo_deactivation';
			$this->ame_settings_menu_title 			= 'Purchase Order Activation';
			$this->ame_settings_title 				= 'Purchase Order Activation';
			$this->ame_menu_tab_activation_title 	= __( 'License Activation', 'api-manager-wcpo' );
			$this->ame_menu_tab_deactivation_title 	= __( 'License Deactivation', 'api-manager-wcpo' );

			/**
			 * Set all software update data here
			 */
			$this->ame_options 				= get_option( $this->ame_data_key );
			$this->ame_plugin_name 			= untrailingslashit( plugin_basename( __FILE__ ) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
			$this->ame_product_id 			= get_option( $this->ame_product_id_key ); // Software Title
			$this->ame_renew_license_url 	= 'http://catmanstudios.com/my-account'; // URL to renew a license. Trailing slash in the upgrade_url is required.
			$this->ame_instance_id 			= get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)
				$this->ame_domain 				= str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			$this->ame_software_version 	= $this->version; // The software version
			$this->ame_plugin_or_theme 		= 'plugin'; // 'theme' or 'plugin'

			// Performs activations and deactivations of API License Keys
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-key-api.php' );

			// Checks for software updatess
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-plugin-update.php' );

			// Admin menu with the license key and license email form
			require_once( plugin_dir_path( __FILE__ ) . 'am/admin/class-wc-api-manager-menu.php' );

			$options = get_option( $this->ame_data_key );

			/**
			 * Check for software updates
			 */
			if ( ! empty( $options ) && $options !== false ) {

				$this->update_check(
					$this->upgrade_url,
					$this->ame_plugin_name,
					$this->ame_product_id,
					$this->ame_options[$this->ame_api_key],
					$this->ame_options[$this->ame_activation_email],
					$this->ame_renew_license_url,
					$this->ame_instance_id,
					$this->ame_domain,
					$this->ame_software_version,
					$this->ame_plugin_or_theme,
					$this->text_domain
					);

			}

		}

		/**
		 * Deletes all license data if plugin deactivated
		 */
		
                 register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
                

	}

	/** Load Shared Classes as on-demand Instances **********************************************/

	/**
	 * API Key Class.
	 *
	 * @return Api_Manager_wcpo_Key
	 */
	public function key() {
		return Api_Manager_wcpo_Key::instance();
	}

	/**
	 * Update Check Class.
	 *
	 * @return API_Manager_wcpo_Update_API_Check
	 */
	public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

		return API_Manager_wcpo_Update_API_Check::instance( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
	}

	public function plugin_url() {
		if ( isset( $this->plugin_url ) ) {
			return $this->plugin_url;
		}

		return $this->plugin_url = plugins_url( '/', __FILE__ );
	}

	/**
	 * Generate the default data arrays
	 */
	public function activation() {
		global $wpdb;

		$global_options = array(
			$this->ame_api_key 				=> '',
			$this->ame_activation_email 	=> '',
					);

		update_option( $this->ame_data_key, $global_options );

		require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-api-manager-passwords.php' );

		$api_manager_wcpo_password_management = new API_Manager_wcpo_Password_Management();

		// Generate a unique installation $instance id
		$instance = $api_manager_wcpo_password_management->generate_password( 12, false );

		$single_options = array(
			$this->ame_product_id_key 			=> $this->ame_software_product_id,
			$this->ame_instance_key 			=> $instance,
			$this->ame_deactivate_checkbox_key 	=> 'on',
			$this->ame_activated_key 			=> 'Deactivated',
			);

		foreach ( $single_options as $key => $value ) {
			update_option( $key, $value );
		}

		$curr_ver = get_option( $this->api_manager_wcpo_version_name );

		// checks if the current plugin version is lower than the version being installed
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			// update the version
			update_option( $this->api_manager_wcpo_version_name, $this->version );
		}

	}

	/**
	 * Deletes all data if plugin deactivated
	 * @return void
	 */
	public function uninstall() {
		global $wpdb, $blog_id;

		$this->license_key_deactivation();

		// Remove options
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key,
					) as $option) {

					delete_option( $option );

					}

			restore_current_blog();

		} else {

			foreach ( array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key
					) as $option) {

					delete_option( $option );

					}

		}

	}

	/**
	 * Deactivates the license on the API server
	 * @return void
	 */
	public function license_key_deactivation() {

		$activation_status = get_option( $this->ame_activated_key );

		$api_email = $this->ame_options[$this->ame_activation_email];
		$api_key = $this->ame_options[$this->ame_api_key];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$this->key()->deactivate( $args ); // reset license key activation
		}
	}

    /**
     * Displays an inactive notice when the software is inactive.
     */
	public static function am_wcpo_inactive_notice() { ?>
		<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
		<?php if ( isset( $_GET['page'] ) && 'api_manager_wcpo_dashboard' == $_GET['page'] ) return; ?>
		<div id="message" class="error">
			<p><?php printf( __( 'The WooCommerce Purchase Order Payment Gateway License Key has not been activated, so the plugin updates are inactive (don\'t worry, the plugin will still work)! %sClick here%s to activate the license key and the plugin updates.', 'api-manager-wcpo' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=api_manager_wcpo_dashboard' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check for external blocking contstant
	 * @return string
	 */
	public function check_external_blocking() {
		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
		if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				?>
				<div class="error">
					<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', 'api-manager-wcpo' ), $this->ame_software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
				</div>
				<?php
			}

		}
	}

} // End of class

function AMWCPO() {
    return API_Manager_wcpo::instance();
}

// Initialize the class instance only once
AMWCPO();