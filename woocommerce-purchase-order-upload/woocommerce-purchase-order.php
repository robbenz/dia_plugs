<?php
/**
 * Plugin Name: WooCommerce Purchase Order Payment Gateway w/ PO Upload
 *
 * Description: A Payment Gateway that allows select customers to complete a transaction by paying via purchase order. 
 * Once you are ready for the customer
 * to pay, change the order status from "On Hold" to "Pending" and save the 
 * order, making any other changes to the order, such as shipping charges and 
 * order total, and click "Email Invoice". The customer will receive an email
 * with a "Pay" link, where they will have the opportunity to pay for the order.
 * 

 *

 *
 * Plugin URI: http://bryanheadrick.com/downloads/woocommerce-purchaseorder/
 * Version: 2.8.11
 * Author: Catman Studios
 * Author URI: http://www.catmanstudios.com
 * License: GPLv2
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
        $this->id	= 'purchaseorder';
        $this->icon = apply_filters('woocommerce_purchaseorder_icon', '');
        $this->has_fields = true;
        $this->method_title = __('Purchase Order', 'woocommerce' );

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
		$this->title = $this->get_option( 'title' );
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

    	$this->form_fields = array(
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
			'poprompt' => array(
							'title' => __( 'Purchase Order# Prompt', 'woocommerce' ),
							'type' => 'checkbox',
							'description' => __( 'Prompt the customer to enter a Purchase Order Number.', 'woocommerce' ),
							'default' => __( 'yes', 'woocommerce' )
						),
                        'porequired' => array(
                                                        'title' => __("Purchase Order# Required", 'woocommerce'),
                                                        'type' => 'checkbox',
                                                        'description' => 'If the Purchase Order Prompt is enabled and this option is also enabled, the order cannot be placed unless this field is filled'
                                                ),
            'pouploadenabled' => array(
							'title' => __( 'Enable/Disable Upload', 'woocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Enable Purchase Order Document Upload', 'woocommerce' ),
							'default' => 'yes'
						),
			'pouploadrequired' => array(
							'title' => __( 'Purchase Order Form Upload Required', 'woocommerce' ),
							'type' => 'checkbox',
							'description' => __( 'Require the user to upload a purchase order form document.', 'woocommerce' ),
							'default' => __( 'no', 'woocommerce' )
						),
               'allowedfiletypes'=> array(
							'title' => __( 'Allowed File Types', 'woocommerce' ),
							'type' => 'multiselect',
							'description' => __( 'Select the filetypes you want customers to be able to upload. If none are selected, then only PDF and Doc files are allowed' , 'woocommerce' ),
							'default' => '',
                                                       'options' => array(
                                                          'application/msword' =>'doc'
                                                       ,  'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docs'
                                                        ,'application/vnd.ms-excel' =>'xls'
                                                        ,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'   =>'xlsx'
                                                        ,'application/pdf' =>'pdf'
                                                        ,'image/jpeg'       =>'jpg/jpeg'
                                                        ,'image/png'       =>'png'
                                                        ,'image/svg+xml'   => 'svg'
                                                        ,'image/eps'       =>'eps'
                                                        ,'application/zip' =>'zip'
                                                        
                                                       )
						), 
                                                 
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
						),
                          'documenttitle' => array(
							'title' => __( 'Document Upload Label ', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'Label for Document upload field', 'woocommerce' ),
							'default' => __( 'Purchase Order Document', 'woocommerce' )
						),
            'ponumtitle' => array(
							'title' => __( 'Purchase Order Number Label', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'Label for PO# field', 'woocommerce' ),
							'default' => __( 'Purchase Order Number', 'woocommerce' )
						),
            
                        'shippingexclusive'=> array(
							'title' => __( 'Shipping Method Exclusivity', 'woocommerce' ),
							'type' => 'multiselect',
							'description' => __( 'If selected, this payment method will only be available when one of the selected shipping are chosen' , 'woocommerce' ),
							'default' => '',
                                                       'options' => $this->get_shipping_methods_list()
						),   
                        'paymentexclusivity'=> array(
                                                        'title' => __('Mutual Exclusivity', 'woocommerce'),
                                                        'type'=>    'checkbox',
                                                        'description' => __('If checked, When Purchase Order is available as a payment method, it is the only available payment method', 'woocommerce'),
                                                        'default'=> 'yes'
                                                )
                            );

    }

function get_shipping_methods_list(){
      global $woocommerce, $woocommerce_settings;
    $methods =  $woocommerce->shipping->load_shipping_methods();
    
    $shipping_methods = array();
    $shipping_methods[''] = 'Select Shipping Method';
    foreach ($methods as $key=>$value){
        $shipping_methods[$key] = $value->method_title;
    }
    
    return $shipping_methods;
    
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
 echo $option['description'];
               if($option['poprompt']== 'yes'):
              ?>	
  <?php  
                $required= ($option['porequired']=='yes'?'Required':'Optional');
                
            echo       woocommerce_form_field( 'purchase_order_number', array(
        'type'          => 'text',
        'class'         => array('purchase-order-number form-row-wide'),
        'label'         => isset($option['ponumtitle'])?$option['ponumtitle']:__('Purchase Order Number'),
        'required'      => (isset($option['porequired']) && $option['porequired']=='yes'),
        'placeholder'       => 'Enter ' . (isset($option['ponumtitle'])?$option['ponumtitle']:'Purchase Order Number'). ' ('.$required.')',
        ), '');   
            
            endif;
          if(!isset($option['pouploadenabled'])||$option['pouploadenabled']=='yes'){
                $uploadrequired= (isset($option['pouploadrequired'])&&$option['pouploadrequired']=='yes'?'Required':'Optional');
            echo       woocommerce_form_field( 'purchase_order_doc_path', array(
        'type'          => 'hidden',
                  'label'         => isset($option['documenttitle'])?$option['documenttitle']: __('Purchase Order Document'),
                'placeholder'=>_('Select '). (isset($option['documenttitle'])?$option['documenttitle']: __('Purchase Order Document')) .' (' . _($uploadrequired ). ')',
                'required'=>(isset($option['pouploadrequired']) && $option['pouploadrequired']=='yes'),
        'class'         => array('purchase-order-docpath')
       
        ), '');       
         
      echo '<script type="text/javascript">window.wcpoUploadCount = 0;</script><div id="wcpo-upload-container">
    <a id="wcpo-uploader" class="wcpo_button" style="display:none" href="#">'._('Select '). (isset($option['documenttitle'])?$option['documenttitle']: __('Purchase Order Document')) .' (' . _($uploadrequired ). ')</a>

    <div id="wcpo-upload-imagelist">
        <ul id="wcpo-ul-list" class="wcpo-upload-list"></ul>
    </div>

</div>';      
         echo '<span id="wcpo-upload-loader">'. _('Please Wait'). '...</span>';  
         echo '<script> initializeUpload();</script>';
          }
          }


}

add_filter('woocommerce_form_field_hidden', 'wcpo_form_field_hidden', 999, 4);

function wcpo_form_field_hidden($no_parameter, $key, $args, $value) {

    $field = '<p style="display:none" class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field">
        <input type="hidden" class="input-hidden" name="' . $key . '" id="' . $key . '" placeholder="' . $args['placeholder'] . '" value="'. $value.'" />
        </p>' ;

    return $field;
}

function wcpo_doc_download($orderid, $text='PO Document'){
    if(is_object($orderid)){
        $orderid = $orderid->id;
    }
    $podoc = get_post_meta( (int)$orderid , '_purchase_order_doc_path' , true );
    if($podoc){
    if(is_array($podoc))
            $podoc = $podoc['url'];
    
            if(strpos($podoc, 'http')===false){
                $podoc = home_url().$podoc;
            }
            
                     echo '<a target="_BLANK" href="' .$podoc. '">'.$text.'</a>'; 
    
                
}
}
add_action ('woocommerce_purchaseorder_document_link','wcpo_doc_download', 10,2);
//add_action('woocommerce_after_checkout_form', 'wcpo_upload_form');
function wcpo_upload_form(){
    
      ?>

		 
                    <div class="form-row docupload form-row-hide" style="width:250px;">
                      <form method="post" enctype="multipart/form-data"  action="#" id="po_upload_form" class="poupload">
    		<input type="file" name="docs" id="docs" />
                <?php
                $nonce = wp_create_nonce("po_upload_func_nonce");
            echo '<input type="hidden" id="po_upload_nonce" name="po_upload_nonce" value="' . $nonce . '"/>';
                ?>
  <input type="hidden" name="action" id="action" value="my_upload_action">
  <input id="submit-ajax" name="submit-ajax" type="submit" value="upload">
    		<button id="btn">Upload Files!</button>
    	</form></div>
                    <?php
              
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
add_action("wp_ajax_wcpo_upload", "po_upload_func");
add_action("wp_ajax_nopriv_wcpo_upload", "po_upload_func");

function po_upload_func() {

  check_ajax_referer('wcpo_allow', 'nonce');
    $file = array(
            'name' => $_FILES['wcpo_upload_file']['name'],
            'type' => $_FILES['wcpo_upload_file']['type'],
            'tmp_name' => $_FILES['wcpo_upload_file']['tmp_name'],
            'error' => $_FILES['wcpo_upload_file']['error'],
            'size' => $_FILES['wcpo_upload_file']['size']
        );
     $file = wcpo_fileupload_process($file);

}
  function wcpo_fileupload_process($file)
    {
        $attachment = wcpo_handle_file($file);
$uploads = wp_upload_dir();
 
            if (is_array($attachment)) {
            $html = wcpo_getHTML($attachment);

            $response = array(
                'success' => true,
                'html' => $html,
                'url'=>$attachment['url']
            );

            echo json_encode($response);
            exit;
        }

        $response = array('success' => false);
        echo json_encode($response);
        exit;
    }
add_filter('wp_handle_upload_prefilter', 'upload_filter' );

function upload_filter( $file ){
//    $file['name'] = 'wordpress-is-awesome-' . $file['name'];
    return $file;
}
    function wcpo_handle_file($upload_data)
    {

            $return = false;
        
        
        $uploaded_file = wp_handle_upload($upload_data, array('test_form' => false));

        if (isset($uploaded_file['file'])) {
            $file_loc = $uploaded_file['file'];
            $file_name = basename($upload_data['name']);
            $file_type = wp_check_filetype($file_name);

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file_loc);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $return = array('data' => $attach_data, 'id' => $attach_id, 'url'=>$uploaded_file['url']);

            return $return;
        }

        return $return;
    }
    
    function wcpo_getHTML($attachment)
    {
        $image = '';
        $path = ''; 
        $attach_id = $attachment['id'];
      if(isset($attachment['data']) && isset($attachment['data']['file'])){
        $file = explode('/', $attachment['data']['file']);
        $file = array_slice($file, 0, count($file) - 1);
        $path = implode('/', $file);
        $image = $attachment['data']['sizes']['thumbnail']['file'];
      
        
        $dir = wp_upload_dir();
        $path = $dir['baseurl'] . '/' . $path;
      }
      $post = get_post($attach_id);
        $html = '';
        $html .= '<li class="wcpo-uploaded-files">';
        if($path !='' && $image != '')
        $html .= sprintf('<img src="%s" name="' . $post->post_title . '" />', $path . '/' . $image);
        else
        $html .= $post->post_title ;
        $html .= sprintf('<br /><a href="#" class="action-delete" data-upload_id="%d">%s</a></span>', $attach_id, __('Delete'));
        $html .= sprintf('<input type="hidden" name="wcpo_image_id[]" value="%d" />', $attach_id);
        $html .= '</li>';

        return $html;
    }

function wcpo_scripts_method() {
    if(is_page(woocommerce_get_page_id('checkout'))){
        $settings = get_option('woocommerce_purchaseorder_settings');

          wp_enqueue_script('jquery');
         wp_enqueue_script('plupload-handlers');
        
	wp_enqueue_script(
		'purchaseorder-script',
		 plugins_url( 'script.js' , __FILE__ ),
		array( 'jquery' ), false, false
	);
        $max_file_size = 20000000;
    wp_enqueue_style('wcpo_style',plugins_url( 'style.css' , __FILE__ ) );
    wp_localize_script( 'purchaseorder-script', 'wcpo_upload',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) , 
    'filetypes' => isset($settings['allowedfiletypes'])?$settings['allowedfiletypes']:array(),
                'nonce' => wp_create_nonce('wcpo_upload'),
            'remove' => wp_create_nonce('wcpo_remove'),
            'number' => 1,
            'upload_enabled' => true,
            'confirmMsg' => __('Are you sure you want to delete this?'),
              'plupload' => array(
                'runtimes' => 'html5,flash,html4',
                'browse_button' => 'wcpo-uploader',
                'container' => 'wcpo-upload-container',
                'file_data_name' => 'wcpo_upload_file',
                'max_file_size' => $max_file_size . 'b',
                'url' => admin_url('admin-ajax.php') . '?action=wcpo_upload&nonce=' . wp_create_nonce('wcpo_allow'),
                'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
                'filters' => array(array('title' => __('Allowed Files'), 'extensions' => wcpo_get_ext())),
                'multipart' => true,
                'urlstream_upload' => true,
            )
            ));}
}

add_action( 'wp_enqueue_scripts', 'wcpo_scripts_method' );

 
add_filter('woocommerce_payment_gateways', 'add_purchaseorder_gateway' );
function wcpo_mime_types( $mimes ){
	$mimes['svg'] = 'image/svg+xml';
        $mimes['eps'] = 'image/eps';
        $mimes['zip'] = 'application/zip';
        $mimes['rar'] = 'application/x-rar-compressed';
	return $mimes;
}
add_filter( 'upload_mimes', 'wcpo_mime_types' );

add_filter('woocommerce_available_payment_gateways','wcpo_conditionally_availabile');
 // Hook in
/**
 * Add the field to the checkout
 **/

add_filter('woocommerce_form_field_file', 'wcpo_file_upload_field', 10, 4);

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'wcpou_checkout_field_process');
 

function wcpo_get_ext(){
    
    $mime_array = array(  'application/msword' =>'doc'
                                                       ,  'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docs'
                                                        ,'application/vnd.ms-excel' =>'xls'
                                                        ,'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'   =>'xlsx'
                                                        ,'application/pdf' =>'pdf'
                                                        ,'image/jpeg'       =>'jpg/jpeg'
                                                        ,'image/png'       =>'png'
                                                        ,'image/svg+xml'   => 'svg'
                                                        ,'image/eps'       =>'eps'
                                                        ,'application/zip' =>'zip');
    $option = get_option('woocommerce_purchaseorder_settings');
     if(isset($option['allowedfiletypes']) && count($option['allowedfiletypes'])>0){
        $allowed_mimes =  $option['allowedfiletypes'];
    }else{
        return '';
    }
          
$allowed_ext = array();
if(is_array($allowed_mimes) && count($allowed_mimes)>0){
    foreach($allowed_mimes as  $mime){
       if(isset($mime_array[$mime])){
           $str_ext = $mime_array[$mime];
           $arr_ext = explode('/', $str_ext);
           foreach($arr_ext as $ext){
               $allowed_ext[] = $ext; 
           }
           
       }
       
}}else{
    $allowed_ext = array();
}
    
    return implode(',',$allowed_ext); 
}

function wcpou_checkout_field_process() {
    global $woocommerce; 
    // if required...
    // Check if set, if its not set add an error.
      $option = get_option('woocommerce_purchaseorder_settings');
    if (!$_POST['purchase_order_number'] && $_POST['payment_method']=='purchaseorder' && $option['poprompt']=='yes' && $option['porequired']=='yes')
         $woocommerce->add_error( __('A '. isset($option['ponumtitle'])?$option['ponumtitle']:__('Purchase Order Number') .' is required.') );
       if (!$_POST['purchase_order_doc_path'] && $_POST['payment_method']=='purchaseorder' && $option['pouploadrequired']=='yes' )
         $woocommerce->add_error( __('A '. isset($option['ponumtitle'])?$option['ponumtitle']:__('Purchase Order Number') .' must be uploaded.') );
   
    
   
}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'wcpou_checkout_field_update_order_meta');
 
function wcpou_checkout_field_update_order_meta( $order_id ) {
    if ($_POST['purchase_order_number']) update_post_meta( $order_id, 'Purchase Order Number', esc_attr($_POST['purchase_order_number']));
    if ($_POST['purchase_order_doc_path'] && $_POST['purchase_order_doc_path']!='') update_post_meta( $order_id, '_purchase_order_doc_path',  $_POST['purchase_order_doc_path']);
    
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
    /*** If the current user is allowed to use the purchase order and this is the checkout page and not the pay page **/
    if (( (get_current_user_id() && get_the_author_meta('haspurchaseorder',get_current_user_id()  )==1  ) || $options['allusers'] == 'yes' ) && !$ispaypage) {
    //we can disable all other payment methods here when purchaseorder is available if desired          
            if(isset($options['shippingexclusive']) && (is_array($options['shippingexclusive'] && $options['shippingexclusive'][0]  !='') || 
                    $options['shippingexclusive']  !='' )){
               $available_methods = apply_filters('woocommerce_available_shipping_methods',wcpo_get_available_shipping_methods());
              if(is_array($available_methods)){
                  $shipping_method = (isset($_POST['shipping_method']) && is_array($_POST['shipping_method'])?$_POST['shipping_method'][0]:(isset($_POST['shipping_method'])?$_POST['shipping_method']:''));
          if(isset($_POST['shipping_method'])&& (isset($available_methods[$shipping_method])))             
              $shipping = $shipping_method;
          else $shipping = '';
              }
           /*** if selected shipping option is in the list or a shipping option isn't speified ***/
           if((isset($options['shippingexclusive']) && is_array($options['shippingexclusive'])&& 
                   in_array($shipping,$options['shippingexclusive']))||(is_array($options['shippingexclusive']) && 
                           count($options['shippingexclusive'])==1 && 
                           $options['shippingexclusive'][0]==''  ) ||$options['shippingexclusive'] =='' || !isset($options['shippingexclusive']) || $shipping == null){
            if(isset($options['paymentexclusivity']) && $options['paymentexclusivity']=='yes'){
                $newgateways['purchaseorder'] = $_available_gateways['purchaseorder'];
             if($options['paymentexclusivity']=='yes')
               $_available_gateways = $newgateways;
            } 
           }
           else{unset($_available_gateways['purchaseorder']);}
        }else{
             $newgateways['purchaseorder'] = $_available_gateways['purchaseorder'];
             if(isset($options['paymentexclusivity']) && $options['paymentexclusivity']=='yes')
               $_available_gateways = $newgateways;
        }
           
           
       
} else {
    //if this user isn't allowed to use the purchaseorder, then make it unavailable
    
    unset($_available_gateways['purchaseorder']);
}
    if($ispaypage)unset($_available_gateways['purchaseorder']);
    return $_available_gateways;
}

 function wcpo_get_available_shipping_methods(){
            global $woocommerce; 
            if(version_compare(WOOCOMMERCE_VERSION, '2.1.0', '<')){
                 $available_methods = $woocommerce->shipping->get_available_shipping_methods();
            }ELSE{
                $available_methods = array();
                $packages = $woocommerce->shipping->get_packages();
            foreach ( $packages as $i => $package ) {
		foreach($package['rates'] as $key=>$val){
                    if(!isset($available_methods['$key']))
                        $available_methods[$key] = $val;
                }
                
                
            }
        }
            
                     return    $available_methods;
        }
add_action( 'edit_user_profile', 'wcpo_cof_user_option' );     // displays field to another administrator
add_action( 'show_user_profile', 'wcpo_cof_user_option' );     // displays field to another administrator

function wcpo_cof_user_option( $user ) { 
     if(!current_user_can('manage_woocommerce')) return; 
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

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_user_meta( $user_id, 'haspurchaseorder', $_POST['haspurchaseorder'] );
}

		
add_action('woocommerce_email_after_order_table','display_po_number');
add_action('woocommerce_order_details_after_order_table','display_po_number');
function display_po_number($order){
      $option = get_option('woocommerce_purchaseorder_settings');
    $ponum = ''; 
    $ponum=get_post_meta($order->id, 'Purchase Order Number', true);
    if($ponum == false){
        $ponum = get_post_meta($order->id, 'PurchaseOrderNumber', true);
    }
    if($ponum){
    echo '<h3>';
    echo isset($option['ponumtitle'])?$option['ponumtitle']:__('Purchase Order Number');
        echo ': </h3>' ;
    echo ' ' . $ponum;
    }
}
add_action( 'manage_shop_order_posts_custom_column' , 'wcpo_custom_order_columns_values', 10, 2 );
add_filter( 'manage_edit-shop_order_columns', 'wcpo_custom_order_columns' , 99);
function wcpo_custom_order_columns($columns){
    $columns["podoc"] = "PO Document";
    return $columns; 
}
add_action( 'add_meta_boxes', 'wcpo_add_order_metaboxes' );
function wcpo_add_order_metaboxes(){
    global $post; 
    if(get_post_meta($post->ID, '_payment_method', true)== 'purchaseorder')
    add_meta_box('wcpo_docs', 'Purchase Order Document', 'wcpo_docs_box', 'shop_order', 'side', 'default');
}
function wcpo_docs_box(){
    global $post;
if(!is_admin()) return;
$podoc = get_post_meta( (int)$post->ID , '_purchase_order_doc_path' , true );
    if($podoc){
    if(is_array($podoc))
            $podoc = $podoc['url'];
    
            if(strpos($podoc, 'http')===false){
                $podoc = home_url().$podoc;
            }
            
                if(strpos($podoc, home_url())!==false){
                     echo '<a target="_BLANK" href="' . $podoc. '">PO Document</a>'; 
    
                }else{
                     echo '<a target="_BLANK" href="' .$podoc. '">PO Document</a>'; 
    
                }
            
       
    
    }
    else echo 'No Documents Found';
}

function wcpo_custom_order_columns_values( $column, $post_id ) {
    switch ( $column ) {
	case 'podoc' :
            get_post_meta( $post_id , '_purchase_order_doc_path' , true );
            if(strpos($podoc, 'http')===false){
                $podoc = home_url().$podoc;
            }
	   if(get_post_meta( $post_id , '_purchase_order_doc_path' , true ))
	    echo '<a target="_BLANK" href="' . $podoc. '">Document</a>'; 
	    break;
    }
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


}

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Displays an inactive message if the API License Key has not yet been activated
 */
if ( get_option( 'api_manager_wcpou_activated' ) != 'Activated' ) {
    add_action( 'admin_notices', 'API_Manager_wcpou::am_wcpou_inactive_notice' );
}
class API_Manager_wcpou {

	/**
	 * Self Upgrade Values
	 */
	// Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
	public $upgrade_url = 'http://catmanstudios.com/';

	/**
	 * @var string
	 */
	public $version = '2.8.11';

	/**
	 * @var string
	 * This version is saved after an upgrade to compare this db version to $version
	 */
	public $api_manager_wcpou_version_name = 'plugin_api_manager_wcpou_version';

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

	public $ame_update_check = 'am_wcpou_plugin_update_check';

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
			$this->ame_software_product_id = 'Purchase Order w PO Upload';

			/**
			 * Set all data defaults here
			 */
			$this->ame_data_key 				= 'api_manager_wcpou';
			$this->ame_api_key 					= 'api_key';
			$this->ame_activation_email 		= 'activation_email';
			$this->ame_product_id_key 			= 'api_manager_wcpou_product_id';
			$this->ame_instance_key 			= 'api_manager_wcpou_instance';
			$this->ame_deactivate_checkbox_key 	= 'api_manager_wcpou_deactivate_checkbox';
			$this->ame_activated_key 			= 'api_manager_wcpou_activated';

			/**
			 * Set all admin menu data
			 */
			$this->ame_deactivate_checkbox 			= 'am_deactivate_wcpou_checkbox';
			$this->ame_activation_tab_key 			= 'api_manager_wcpou_dashboard';
			$this->ame_deactivation_tab_key 		= 'api_manager_wcpou_deactivation';
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
		 * Deletes all data if plugin deleted
		 */
                
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

	}

	/** Load Shared Classes as on-demand Instances **********************************************/

	/**
	 * API Key Class.
	 *
	 * @return Api_Manager_wcpou_Key
	 */
	public function key() {
		return Api_Manager_wcpou_Key::instance();
	}

	/**
	 * Update Check Class.
	 *
	 * @return API_Manager_wcpou_Update_API_Check
	 */
	public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

		return API_Manager_wcpou_Update_API_Check::instance( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
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
                if(!get_option($this->ame_data_key)){
                    update_option( $this->ame_data_key, $global_options );
                }
		require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-api-manager-passwords.php' );

		$api_manager_wcpou_password_management = new API_Manager_wcpou_Password_Management();

		// Generate a unique installation $instance id
		$instance = $api_manager_wcpou_password_management->generate_password( 12, false );

		$single_options = array(
			$this->ame_product_id_key 			=> $this->ame_software_product_id,
			$this->ame_instance_key 			=> $instance,
			$this->ame_deactivate_checkbox_key 	=> 'on',
			$this->ame_activated_key 			=> 'Deactivated',
			);

		foreach ( $single_options as $key => $value ) {
			update_option( $key, $value );
		}
                
		

		$curr_ver = get_option( $this->api_manager_wcpou_version_name );

		// checks if the current plugin version is lower than the version being installed
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			// update the version
			update_option( $this->api_manager_wcpou_version_name, $this->version );
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
//					$this->ame_data_key,
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
//					$this->ame_data_key,
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
	public static function am_wcpou_inactive_notice() { ?>
		<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
		<?php if ( isset( $_GET['page'] ) && 'api_manager_wcpou_dashboard' == $_GET['page'] ) return; ?>
		<div id="message" class="error">
			<p><?php printf( __( 'The WooCommerce Purchase Order Payment Gateway License Key has not been activated, so the plugin updates are inactive (don\'t worry, the plugin will still work)! %sClick here%s to activate the license key and the plugin updates.', 'api-manager-wcpo' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=api_manager_wcpou_dashboard' ) ) . '">', '</a>' ); ?></p>
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
    return API_Manager_wcpou::instance();
}

// Initialize the class instance only once
AMWCPO();

