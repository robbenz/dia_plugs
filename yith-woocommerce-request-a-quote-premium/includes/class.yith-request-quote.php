<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of YITH Woocommerce Request A Quote
 *
 * @class   YITH_Request_Quote
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_Request_Quote' ) ) {

	/**
     * Class YITH_Request_Quote
     */
    class YITH_Request_Quote {

        /**
         * Single instance of the class
         *
         * @var \YITH_Request_Quote
         */

        protected static $instance;

        /**
         * Session object
         */
        public $session_class;


        /**
         * Content of session
         */
        public $raq_content = array();


        /**
         * List of variations
         */
        public $raq_variations = array();

        /**
         * Returns single instance of the class
         *
         * @return \YITH_Request_Quote
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {

            add_action( 'init', array( $this, 'start_session' ));
            add_action( 'shutdown', array( $this, 'fix_contact_form_7' ), -1 ); // Set cookies before shutdown and ob flushing

            /* plugin */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            /* ajax action */
            if( version_compare( WC()->version, '2.4.0', '<' ) ){
                add_action( 'wp_ajax_yith_ywraq_action', array( $this, 'ajax' ) );
                add_action( 'wp_ajax_nopriv_yith_ywraq_action', array( $this, 'ajax' ) );
            }else{
                add_action( 'wc_ajax_yith_ywraq_action', array( $this, 'ajax' ) );
            }

            /* session settings */
            add_action( 'wp_loaded', array( $this, 'init' ), 30 ); // Get raq after WP and plugins are loaded.
            add_action( 'wp_loaded', array( $this, 'ywraq_time_validation_schedule' ) );
            add_action( 'wp', array( $this, 'maybe_set_raq_cookies' ), 99 ); // Set cookies
            add_action( 'shutdown', array( $this, 'maybe_set_raq_cookies' ), 0 ); // Set cookies before shutdown and ob flushing

            /* email actions and filter */
            add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
            add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
            add_action( 'wp_loaded', array( $this, 'send_message' ), 30);

            /* general actions */
            add_filter( 'woocommerce_locate_core_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );
            add_filter( 'woocommerce_locate_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );

            add_action( 'ywraq_clean_cron', array( $this, 'clean_session') );

            /* compatibility with email template WC_Subscriptions */
            if( class_exists('WC_Subscriptions') ) {
                add_filter('ywraq_quote_subtotal_item', array( $this, 'update_subtotal_item_price'), 10, 3);
                add_filter('ywraq_quote_subtotal_item_plain', array( $this, 'update_subtotal_item_price_plain'), 10, 3);
            }

            /* compatibility with WooCommerce Min/Max Quantities */
            if ( function_exists( 'YITH_WMMQ' ) ) {
                add_filter( 'ywraq_quantity_input_value', array( $this, 'ywraq_quantity_input_value' ), 10 );
                add_filter( 'ywraq_quantity_max_value', array( YITH_WMMQ(), 'ywmmq_max_quantity_block' ), 10, 2 );
                add_filter( 'ywraq_quantity_min_value', array( YITH_WMMQ(), 'ywmmq_min_quantity_block' ), 10, 2 );
            }

            if( class_exists('Woo_Advanced_QTY_Public') ){
                add_filter( 'ywraq_quantity_input_value', array( $this, 'ywraq_quantity_input_value' ), 10 );
                add_filter( 'woocommerce_quantity_input_args', array( $this, 'woocommerce_quantity_input_args' ), 200 );
            }




        }


        /**
         * Initialize session and cookies
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        function start_session(){

            if( ! isset( $_COOKIE['woocommerce_items_in_cart'] ) ) {
                do_action( 'woocommerce_set_cart_cookies', true );
            }
            $this->session_class = new YITH_YWRAQ_Session();
            $this->set_session();



        }

        /**
         * Initialize functions
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        function init() {
            $this->get_raq_for_session();
            $this->session_class->set_customer_session_cookie(true);
            $this->raq_variations = $this->get_variations_list();
        }

        /**
         * Load YIT Plugin Framework
         *
         * @since  1.0.0
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }

        /**
         * Get request quote list
         *
         * @since  1.0.0
         * @return array
         * @author Emanuela Castorina
         */
        function get_raq_return() {
            return $this->raq_content;
        }

        /**
         * Get request quote list
         *
         * @since  1.0.0
         * @return array
         * @author Emanuela Castorina
         */
        function get_variations_list() {
            $variations = array();
            if ( ! empty( $this->raq_content ) ) {
                foreach ( $this->raq_content as $item ) {
                    if ( isset( $item['variation_id'] ) && $item['variation_id'] != 0 ) {
                        $variations[] = $item['variation_id'];
                    }
                }
            }

            return $variations;
        }

        /**
         * Get all errors in HTML mode or simple string.
         *
         * @param      $errors
         * @param bool $html
         *
         * @return string
         * @since 1.0.0
         */
        public function get_errors( $errors , $html = true ) {
            return implode( ( $html ? '<br />' : ', ' ), $errors );
        }

        /**
         * is_empty
         *
         * return true if the list is empty
         * @since  1.0.0
         * @return bool
         * @author Emanuela Castorina
         */
        public function is_empty() {
            return empty( $this->raq_content );
        }

        /**
         * get_item_number
         *
         * return true if the list is empty
         * @since  1.0.0
         * @return bool
         * @author Emanuela Castorina
         */
        public function get_raq_item_number() {
            return count( $this->raq_content );
        }




        /**
         * Get request quote list from session
         *
         * @since  1.0.0
         * @return array
         * @author Emanuela Castorina
         */
        function get_raq_for_session() {
            $this->raq_content = $this->session_class->get( 'raq', array() );
            return $this->raq_content;
        }

        /**
         * Sets the php session data for the request a quote
         *
         * @since  1.0.0
         *
         * @param array $raq_session
         * @param bool  $can_be_empty
         *
         * @author Emanuela Castorina
         */
        public function set_session( $raq_session = array(), $can_be_empty = false ) {
            if ( empty( $raq_session ) && ! $can_be_empty ) {
                $raq_session = $this->get_raq_for_session();
            }

            // Set raq  session data
            $this->session_class->set( 'raq', $raq_session );

            do_action( 'yith_raq_updated' );

        }

        /**
         * Unset the session
         *
         * @since  1.0.0
         * @return void
         * @author Emanuela Castorina
         */
        public function unset_session() {
            // Set raq and coupon session data
            $this->session_class->__unset( 'raq' );
        }

        /**
         * Set Request a quote cookie
         *
         * @since  1.0.0
         * @return void
         * @author Emanuela Castorina
         */
        function maybe_set_raq_cookies() {
            $set = true;

            if ( !headers_sent() ) {
                if ( sizeof( $this->raq_content ) > 0 ) {
                    $this->set_rqa_cookies( true );
                    $set = true;
                }
                elseif ( isset( $_COOKIE['yith_ywraq_items_in_raq'] ) ) {
                    $this->set_rqa_cookies( false );
                    $set = false;
                }
            }

            do_action( 'yith_ywraq_set_raq_cookies', $set );
        }

        /**
         * Set hash cookie and items in raq.
         *
         * @since  1.0.0
         * @access private
         *
         * @param bool $set
         *
         * @author Emanuela Castorina
         */
        private function set_rqa_cookies( $set = true ) {
            if ( $set ) {
                wc_setcookie( 'yith_ywraq_items_in_raq', 1 );
                wc_setcookie( 'yith_ywraq_hash', md5( json_encode( $this->raq_content ) ) );
            }
            elseif ( isset( $_COOKIE['yith_ywraq_items_in_raq'] ) ) {
                wc_setcookie( 'yith_ywraq_items_in_raq', 0, time() - HOUR_IN_SECONDS );
                wc_setcookie( 'yith_ywraq_hash', '', time() - HOUR_IN_SECONDS );
            }
            do_action( 'yith_ywraq_set_rqa_cookies', $set );
        }

        /**
         * Check if the product is in the list
         *
         * @param      $product_id
         * @param bool $variation_id
         * @param bool $postadata
         *
         * @return mixed|void
         */
        public function exists( $product_id, $variation_id = false, $postadata = false ) {

            $return = false;

            if ( $variation_id ) {
                //variation product
                $key_to_find = md5( $product_id . $variation_id );
            }
            else {
                $key_to_find = md5( $product_id );
            }


            if ( array_key_exists( $key_to_find, $this->raq_content ) ) {
                $this->errors[] = __( 'Product already in the list.', 'yith-woocommerce-request-a-quote' );
                $return = true;
            }

            return apply_filters( 'ywraq_exists_in_list', $return, $product_id, $variation_id, $postadata, $this->raq_content );
        }

        /**
         * Add an item to request quote list
         *
         * @param $product_raq
         *
         * @return string
         */
        public function add_item( $product_raq ) {
            $return = '';

            if ( ! ( isset( $product_raq['variation_id'] ) && $product_raq['variation_id'] != '' ) ) {

                $product = wc_get_product( $product_raq['product_id'] );
                $product_type = is_null( $product->product_type ) ? '' : $product->product_type;
                //grouped
                if( $product_type == 'grouped'){
                    if( is_array( $product_raq['quantity'] ) ){
                        foreach ( $product_raq['quantity'] as $item_id => $quantity  ) {
                            if ( ! $this->exists( $item_id ) && $quantity != 0 ) {
                                $raq = array(
                                    'product_id' => $item_id,
                                    'quantity'   => $quantity
                                );

                                $raq = apply_filters('ywraq_add_item', $raq, $product_raq );
                                $this->raq_content[ apply_filters( 'ywraq_quote_item_id', md5( $item_id ), $product_raq  )] = $raq;
                            }
                        }
                    }
                }else{
                    //single product
                    if ( !$this->exists( $product_raq['product_id'] ) ) {

                        $product_raq['quantity'] = ( isset( $product_raq['quantity'] ) ) ? (int) $product_raq['quantity'] : 1;

                        $raq = array(
                            'product_id' => $product_raq['product_id'],
                            'quantity'   => $product_raq['quantity']
                        );

                        $raq = apply_filters('ywraq_add_item', $raq, $product_raq );

                        $this->raq_content[ apply_filters( 'ywraq_quote_item_id', md5( $product_raq['product_id']), $product_raq  )] = $raq;

                    }
                    else {
                        $return = 'exists';
                    }
                }

            }
            else {

                //variable product
                if ( !$this->exists( $product_raq['product_id'], $product_raq['variation_id'] ) ) {


                    $product_raq['quantity'] = ( isset( $product_raq['quantity'] ) ) ? (int) $product_raq['quantity'] : 1;

                    $raq = array(
                        'product_id'   => $product_raq['product_id'],
                        'variation_id' => $product_raq['variation_id'],
                        'quantity'     => $product_raq['quantity']
                    );

                    $raq = apply_filters('ywraq_add_item', $raq, $product_raq );

                    $variations = array();

                    foreach ( $product_raq as $key => $value ) {

                        if ( stripos( $key, 'attribute' ) !== false ) {
                            $variations[$key] = $value;
                        }
                    }

                    $raq ['variations'] = $variations;

                    $this->raq_content[apply_filters( 'ywraq_quote_item_id', md5( $product_raq['product_id'] . $product_raq['variation_id'] ), $product_raq )] = $raq;

                }
                else {
                    $return = 'exists';
                }
            }

            if ( $return != 'exists' ) {

                $this->set_session( $this->raq_content );

                $return = 'true';

                $this->set_rqa_cookies( sizeof( $this->raq_content ) > 0 );


            }



            return $return;

        }

        /**
         * Remove an item form the request list
         *
         * @param $key
         *
         * @return bool
         */
        public function remove_item( $key ) {
            if ( isset( $this->raq_content[$key] ) ) {
                unset( $this->raq_content[$key] );
                $this->set_session( $this->raq_content, true );
                $this->raq_variations = $this->get_variations_list();
                return true;
            }
            else {
                return false;
            }
        }

        /**
         * Clear the list
         */
        public function clear_raq_list() {
            $this->raq_content = array();
            $this->set_session( $this->raq_content, true );
        }

        /**
         * Update an item in the raq list
         *
         * @param      $key
         * @param bool $field
         * @param      $value
         *
         * @return bool
         */
        public function update_item( $key, $field = false, $value ) {

            if ( $field && isset( $this->raq_content[$key][$field] ) ) {
                $this->raq_content[$key][$field] = $value;
                $this->set_session( $this->raq_content );

            }
            elseif ( isset( $this->raq_content[$key] ) ) {
                $this->raq_content[$key] = $value;
                $this->set_session( $this->raq_content );
            }
            else {
                return false;
            }

            $this->set_session( $this->raq_content );
            return true;
        }

        /**
         * Switch a ajax call
         */
        public function ajax() {

            if ( isset( $_POST['ywraq_action'] ) ) {
                if ( method_exists( $this, 'ajax_' . $_POST['ywraq_action'] ) ) {
                    $s = 'ajax_' . $_POST['ywraq_action'];
                    $this->$s();
                }
            }
        }

        /**
         * Add an item to request quote list in ajax mode
         *
         * @return string
         * @since  1.0.0
         */
        public function ajax_add_item() {

            $return  = 'false';
            $message = '';
            $errors = array();

            $product_id         = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
            $is_valid_variation = ( isset( $_POST['variation_id'] ) && ! empty( $_POST['variation_id'] ) ) ? is_numeric( $_POST['variation_id'] ) : true;

            $is_valid = $product_id && $is_valid_variation;

            $postdata = $_POST;

            $postdata = apply_filters('ywraq_ajax_add_item_prepare', $postdata, $product_id );


            if ( !$is_valid ) {
                $errors[] = __( 'Error occurred while adding product to Request a Quote list.', 'yith-woocommerce-request-a-quote' );
            }
            else {
                $return = $this->add_item( $postdata );
            }

            if ( $return == 'true' ) {
                $message = apply_filters( 'yith_ywraq_product_added_to_list_message', __( 'Product has been added to quote!', 'yith-woocommerce-request-a-quote' ) );
            }
            elseif ( $return == 'exists' ) {
                $message = apply_filters( 'yith_ywraq_product_already_in_list_message', __( 'Product already in the list.', 'yith-woocommerce-request-a-quote' ) );
            }
            elseif ( count( $errors ) > 0 ) {
                $message = apply_filters( 'yith_ywraq_error_adding_to_list_message', $this->get_errors($errors) );
            }



            wp_send_json(
                array(
                    'result'       => $return,
                    'message'      => $message,
                    'rqa_url'      => $this->get_raq_page_url(),
                    'variations'   => implode(',',$this->get_variations_list())
                 )
            );
        }

        /**
         * Remove an item from the list in ajax mode
         *
         * @return string
         * @since  1.0.0
         */
        public function ajax_remove_item() {
            $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
            $is_valid   = $product_id && isset( $_POST['key'] );
            if ( $is_valid ) {
                echo $this->remove_item( $_POST['key'] );
            }
            else {
                echo false;
            }
            die();
        }


        /**
         * Remove an item from the list in ajax mode
         *
         * @return string
         * @since  1.0.0
         */
        public function ajax_update_item_quantity() {

            $is_valid   = isset( $_POST['key'] ) && isset( $_POST['quantity'] );
            if ( $is_valid ) {
                echo $this->update_item_quantity( $_POST['key'], $_POST['quantity'] );
            }
            else {
                echo false;
            }
            die();
        }

        /**
         * Remove an item from the list in ajax mode
         *
         * @param $key
         * @param $quantity
         *
         * @return string
         * @since  1.0.0
         */
        public function update_item_quantity( $key, $quantity) {

            $min = $max = $quantity;

            if ( isset( $this->raq_content[$key] ) ) {

                if ( function_exists( 'YITH_WMMQ' ) ) {
                    $_product = wc_get_product($this->raq_content[$key]['product_id']);
                    $max = apply_filters('ywraq_quantity_max_value', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product );
                    $min = apply_filters('ywraq_quantity_min_value', 0, $_product);
                }
                $quantity = ( $quantity <= $min  ) ? $min : $quantity;
                $quantity = ( $quantity >= $max  ) ? $max : $quantity;
                $this->raq_content[ $key ]['quantity'] = $quantity;

                $this->set_session( $this->raq_content, true );
                return true;

            }

            return false;
        }

        /**
         * Check if an element exist the list in ajax mode
         *
         * @return string
         * @since  1.0.0
         */
        public function ajax_variation_exist() {
            if ( isset( $_POST['product_id'] ) && isset( $_POST['variation_id'] ) ) {

                $message = '';
                $label_browser = '';
                $product_id = ( $_POST['variation_id'] != '') ? $_POST['variation_id']  : $_POST['product_id'];
                $product = wc_get_product( $product_id );

                if( ( ! YITH_Request_Quote_Premium()->check_user_type() || ( get_option('ywraq_show_btn_only_out_of_stock') == 'yes' &&  $product->is_in_stock() ) ) ){
                    $message = apply_filters( 'yith_ywraq_product_not_quoted', __( 'This product is not quotable.', 'yith-woocommerce-request-a-quote' ) );
                }elseif( $this->exists( $_POST['product_id'], $_POST['variation_id'], $_POST ) == 'true'  ){
                    $message = apply_filters( 'yith_ywraq_product_already_in_list_message', __( 'Product already in the list.', 'yith-woocommerce-request-a-quote' ) );
                    $label_browser = ywraq_get_browse_list_message();
                }

                $return  = ( $message == '') ? false : true ;

                wp_send_json(
                    array(
                        'result'       => $return,
                        'message'      => $message,
                        'label_browse' => $label_browser,
                        'rqa_url'      => $this->get_raq_page_url(),
                    )
                );
            }
        }

        /**
         * Return the url of request quote page
         *
         * @return string
         * @since 1.0.0
         */
        public function get_raq_page_url() {
            $option_value = get_option( 'ywraq_page_id' );

            if( function_exists('wpml_object_id_filter')){
                global $sitepress;
                $option_value = wpml_object_id_filter( $option_value, 'post', true, $sitepress->get_current_language() );
            }

            $base_url     = get_the_permalink( $option_value );

            return apply_filters( 'ywraq_request_page_url', $base_url );
        }

        /**
         * Locate default templates of woocommerce in plugin, if exists
         *
         * @param $core_file     string
         * @param $template      string
         * @param $template_base string
         *
         * @return string
         * @since  1.0.0
         */
        public function filter_woocommerce_template( $core_file, $template, $template_base ) {
            $located = yith_ywraq_locate_template( $template );

            if( $located ){
                return $located;
            }
            else{
                return $core_file;
            }
        }

        /**
         * Get all errors in HTML mode or simple string.
         *
         * @return void
         * @since 1.0.0
         */
        public function send_message() {

            if( ! isset( $_POST['rqa_name'] ) ) return;

            $errors = array();
            $user_additional_field = '';
            $user_additional_field_2 = '';
            $user_additional_field_3 = '';
            $attachment = array();
            if ( isset( $_POST['raq_mail_wpnonce'] ) ) {

                if ( empty( $_POST['rqa_name'] ) ) {
                    $errors[] = '<p>' . __( 'Please enter a name', 'yith-woocommerce-request-a-quote' ) . '</p>';
                }

                if ( !isset( $_POST['rqa_email'] ) || empty( $_POST['rqa_email'] ) || ! is_email( $_POST['rqa_email'] ) ) {
                    $errors[] = '<p>' . __( 'Please enter a valid email', 'yith-woocommerce-request-a-quote' ) . '</p>';
                }

                if (  !empty( $_POST['rqa_name']) && ! empty( $_POST['rqa_email'] ) && isset( $_POST['createaccount']) &&  email_exists( $_POST['rqa_email'] ) ) {
                    $errors[] = '<p>' . __( 'An account is already registered with your email address. Please login.', 'yith-woocommerce-request-a-quote' ) . '</p>';
                }

                if ( get_option('ywraq_additional_text_field') == 'yes' && get_option( 'ywraq_additional_text_field_required' ) == 'yes' && isset( $_POST['rqa_text_field'] ) && empty( $_POST['rqa_text_field'] ) ) {
                    $errors[] = '<p>' . sprintf( __( 'Please enter a value for %s', 'yith-woocommerce-request-a-quote' ), get_option('ywraq_additional_text_field_label') ) . '</p>';
                }else{
                    $user_additional_field = isset( $_POST['rqa_text_field'] ) ? $_POST['rqa_text_field'] : '';
                }
                if ( empty( $_POST['rqa_facility'] ) ) {
                    $errors[] = '<p>' . __( 'Please enter your facility name', 'ywraq' ) . '</p>';
                }
                if ( empty( $_POST['rqa_zip'] ) ) {
                    $errors[] = '<p>' . __( 'Please a valid Zip Code', 'ywraq' ) . '</p>';
                }
                if ( empty( $_POST['rqa_phone'] ) ) {
                    $errors[] = '<p>' . __( 'Please a valid Phone Number', 'ywraq' ) . '</p>';
                }

                if ( get_option('ywraq_additional_text_field_2') == 'yes' && get_option( 'ywraq_additional_text_field_required_3' ) == 'yes' && isset( $_POST['rqa_text_field_2'] ) && empty( $_POST['rqa_text_field_2'] ) ) {
                    $errors[] = '<p>' . sprintf( __( 'Please enter a value for %s', 'yith-woocommerce-request-a-quote' ), get_option('ywraq_additional_text_field_label_2') ) . '</p>';
                }else{
                    $user_additional_field_2 = isset( $_POST['rqa_text_field_2'] ) ? $_POST['rqa_text_field_2'] : '';
                }

                if ( get_option('ywraq_additional_text_field_3') == 'yes' && get_option( 'ywraq_additional_text_field_required_3' ) == 'yes' && isset( $_POST['rqa_text_field_3'] ) && empty( $_POST['rqa_text_field_3'] ) ) {
                    $errors[] = '<p>' . sprintf( __( 'Please enter a value for %s', 'yith-woocommerce-request-a-quote' ), get_option('ywraq_additional_text_field_label_3') ) . '</p>';
                }else{
                    $user_additional_field_3 = isset( $_POST['rqa_text_field_3'] ) ? $_POST['rqa_text_field_3'] : '';
                }

                if ( get_option('ywraq_additional_upload_field') == 'yes' && ! empty( $_FILES['rqa_upload_field']['name'] ) ) {

                    if ( ! function_exists( 'wp_handle_upload' ) ) {
                        require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    }

                    $uploadedfile = $_FILES['rqa_upload_field'];
                    $upload_overrides = array( 'test_form' => false );
                    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                    if ( $movefile && !isset( $movefile['error'] ) ) {
                        $attachment = $movefile;
                    } else {
                        $errors[] = '<p>' .   $movefile['error'] . '</p>';
                    }

                }


          //      if ( YITH_Request_Quote()->is_empty() ) {
          //          $errors[] = ywraq_get_list_empty_message();
          //      }

                $errors = apply_filters( 'ywraq_request_validate_fields', $errors, $_POST);

                if ( empty( $errors ) ) {
                    $args = array(
                        'user_name'               => $_POST['rqa_name'],
                        'user_email'              => $_POST['rqa_email'],
                        'facility_name'           => $_POST['rqa_facility'],
                        'zipcode'                 => $_POST['rqa_zip'],
                        'phonenumber'             => $_POST['rqa_phone'],
                        'partnumber'              => $_POST['rqa_part'],
                        'partdesc'                => $_POST['rqa_desc'],
                        'partqty'                 => $_POST['rqa_qty'],
                        'partnumber1'             => $_POST['rqa_part1'],
                        'partdesc1'               => $_POST['rqa_desc1'],
                        'partqty1'                => $_POST['rqa_qty1'],
                        'partnumber2'             => $_POST['rqa_part2'],
                        'partdesc2'               => $_POST['rqa_desc2'],
                        'partqty2'                => $_POST['rqa_qty2'],
                        'address'                 => $_POST['rqa_address'],
                        'city'                    => $_POST['rqa_city'],
                        'state'                   => $_POST['rqa_state'],
                        'selectOption'            => $_POST['taskOption'],
                        'user_message'            => nl2br( $_POST['rqa_message'] ),
                        'user_additional_field'   => $user_additional_field,
                        'user_additional_field_2' => $user_additional_field_2,
                        'user_additional_field_3' => $user_additional_field_3,
                        'attachment'              => $attachment,
                        'raq_content'             => YITH_Request_Quote()->get_raq_return()
                    );

                    $current_customer_id = 0;

                    if ( is_user_logged_in() ) {
                        $current_customer_id = get_current_user_id();
                    } elseif ( isset( $_POST['createaccount'] ) ) {
                        if ( username_exists( $args['user_name'] ) ) {
                            $user_login = $this->get_username( $args['user_name'], $args['user_email'] );
                        } else {
                            $user_login = $args['user_name'];
                        }
                        $current_customer_id = $this->add_user( $user_login, $args['user_email'] );
                        wp_set_auth_cookie( $current_customer_id, true );
                    }

                    $args['customer_id'] =  $current_customer_id;

                    if ( isset( $_REQUEST['lang'] ) ) {
                        $args['lang'] = $_REQUEST['lang'];
                    }

                    if ( get_option( 'ywraq_enable_order_creation', 'yes' ) == 'yes' ) {
                        do_action( 'ywraq_process', $args );
                    }

                    do_action( 'send_raq_mail', $args );


                    if( ! $thank_you_page = $this->has_thank_you_page() ){
                        wp_redirect( YITH_Request_Quote()->get_raq_page_url(), 301 );
                    }else{
                        wp_redirect( $thank_you_page, 301 );
                    }

                    exit();
                }
            }
            else {
                $errors[] = '<p>' . __( 'There was a problem sending your request. Please try again.', 'yith-woocommerce-request-a-quote' ) . '</p>';
            }

            yith_ywraq_add_notice( $this->get_errors($errors), 'error' );

        }

	    /**
         * @return bool|false|string
         */
        public function has_thank_you_page(  ) {

            if( get_option('ywraq_activate_thank_you_page', 'no') == 'no' ){
                return false;
            }

            return ( get_option('ywraq_thank_you_page') ) ? get_permalink( get_option('ywraq_thank_you_page') ) : false;
        }

        /**
         * Return the username of user
         *
         * @since  1.0.0
         *
         * @param $hyb_user_login
         * @param $hyb_user_email
         *
         * @return string
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        function get_username( $hyb_user_login, $hyb_user_email ) {
            $yith_user_login = $hyb_user_login;
            if ( !empty( $hyb_user_login ) ) {
                if ( get_option( 'woocommerce_registration_generate_username' ) == 'yes' && !empty( $hyb_user_email ) ) {
                    $yith_user_login = sanitize_user( current( explode( '@', $hyb_user_email ) ) );
                    if ( username_exists( $hyb_user_login ) ) {
                        $append     = 1;
                        $o_username = $yith_user_login;

                        while ( username_exists( $yith_user_login ) ) {
                            $yith_user_login = $o_username . $append;
                            $append ++;
                        }
                    }
                }
            }

            return $yith_user_login;

        }

        /**
         * Filters woocommerce available mails, to add wishlist related ones
         *
         * @param $emails array
         *
         * @return array
         * @since 1.0
         */
        public function add_woocommerce_emails( $emails ) {
            $emails['YITH_YWRAQ_Send_Email_Request_Quote'] = include( YITH_YWRAQ_INC . 'emails/class.yith-ywraq-send-email-request-quote.php' );
            return $emails;
        }

        /**
         * Loads WC Mailer when needed
         *
         * @return void
         * @since 1.0
         */
        public function load_wc_mailer() {
            add_action( 'send_raq_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
        }

        /**
         * Add a new user
         *
         * @since  1.0.0
         *
         * @param $username
         * @param $user_email
         *
         * @return string
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function add_user( $username, $user_email ){

            $password = wp_generate_password();
            $args = array(
                'user_login' => $username,
                'user_pass'  => $password,
                'user_email' => $user_email,
                'remember'   => false,
                'role'       => apply_filters('ywraq_new_user_role','customer')
            );

            $customer_id = wp_insert_user( $args );
            wp_signon( $args, false );

            do_action( 'woocommerce_created_customer', $customer_id, $args, $password );

            return $customer_id;
        }


        /**
         *
         *
         * @since  1.1.8
         * @author Emanuela Castorina
         */
        public function fix_contact_form_7(  ) {

            if ( isset( $_POST['_wpcf7_is_ajax_call'] ) ) {

                die();
            }
        }

	    /**
         *
         */
        public function clean_session(){
            global $wpdb;

            $wpdb->query("DELETE FROM ". $wpdb->prefix ."options  WHERE option_name LIKE '_yith_ywraq_session_%'");

        }

		/**
         *
         */
        public function ywraq_time_validation_schedule(){

            if( ! wp_next_scheduled( 'ywraq_time_validation' ) ){
                $ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
                wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS'), 'daily', 'ywraq_time_validation' );
            }

            if ( !wp_next_scheduled( 'ywraq_clean_cron' ) ) {
                wp_schedule_event( time(), 'daily', 'ywraq_clean_cron' );
            }
        }

        /**
         * Update subtotal price for compatibility with WC_Subscriptions
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         *
         * @param $subtotal
         * @param $line_item
         * @param $product
         *
         * @return mixed
         */
        public function update_subtotal_item_price( $subtotal, $line_item, $product ) {
            if ( ! WC_Subscriptions_Product::is_subscription( $product->id ) ) {
                return $subtotal;
            }
            $this->subtotal = $line_item;
            add_filter( 'woocommerce_subscriptions_product_price_string_inclusions', array( $this, 'update_price' ), 10, 2 );

            return $product->get_price_html();
        }

        /**
         * Update price in plain email for compatibility with WC_Subscriptions
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         *
         * @param $subtotal
         * @param $line_item
         * @param $product
         *
         * @return string
         */
        public function update_subtotal_item_price_plain( $subtotal, $line_item, $product ){
            if ( ! WC_Subscriptions_Product::is_subscription( $product->id ) ) {
                return $subtotal;
            }
            $this->subtotal = $line_item;

            add_filter( 'woocommerce_subscriptions_product_price_string_inclusions', array( $this, 'update_price' ), 10, 2 );
            return wc_price( $this->subtotal );
        }

        /**
         * Update price for compatibility with WC_Subscriptions
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         *
         * @param $include
         * @param $product
         *
         * @return mixed
         */
        public function update_price(  $include, $product  ) {
            $include['price'] = '<ins><span class="amount">'. wc_price($this->subtotal).'</span></ins>';
            return $include;
        }


        /**
         * Change the value of quantity input for compatibility with WooCommerce Min/Max Quantities
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         *
         * @param $args
         *
         * @return mixed
         */
        public function woocommerce_quantity_input_args( $args ) {

            if( isset( $this->quantity) ){
                $args['input_value'] = $this->quantity;
            }
            return $args;
        }

        /**
         * Save the temp quantity in a param for compatibility with WooCommerce Min/Max Quantities
         *
         * @since  1.3.0
         * @author Emanuela Castorina
         *
         * @param $quantity
         *
         * @return mixed
         */
        public function ywraq_quantity_input_value( $quantity ) {
            $this->quantity = $quantity;
            return $quantity;
        }



	    /**
         * Check if the checkout is enabled after the acceptance of quote
         * @return bool
         */
        public function enabled_checkout( ) {
            $show_accept_link = get_option('ywraq_show_accept_link', 'yes');

            if( $show_accept_link == 'no' ){
                return false;
            }

            $accepted_page_id = get_option( 'ywraq_page_accepted' );
            $checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
            if( $accepted_page_id == $checkout_page_id ){
                return true;
            }

            return false;
        }

    }
}

/**
 * Unique access to instance of YITH_Request_Quote class
 *
 * @return \YITH_Request_Quote
 */
function YITH_Request_Quote() {
    return YITH_Request_Quote::get_instance();
}
