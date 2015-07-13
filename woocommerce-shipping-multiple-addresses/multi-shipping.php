<?php
/**
 * woocommerce_multi_shipping_init function.
 *
 * @access public
 * @return void
 */
function woocommerce_multi_shipping_init() {

    if ( ! class_exists( 'WC_Shipping_Method' ) ) return;

    /**
     * WC_Multiple_Shipping class.
     *
     * @extends WC_Shipping_Method
     */
    class WC_Multiple_Shipping extends WC_Shipping_Method {
        function __construct() {
            $this->id           = 'multiple_shipping';
            $this->method_title = __('Multiple Shipping', 'wc_shipping_multiple_address');
            $this->method_description = __('Multiple Shipping is used automatically by the WooCommerce Ship to Multiple Addresses.', 'wc_shipping_multiple_address');
            $this->init();
        }

        function init() {
            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables
            $this->enabled              = 'yes';
            $this->title                = $this->settings['title'];
            $this->cart_duplication     = $this->settings['cart_duplication'];
            $this->lang_notification    = $this->settings['lang_notification'];
            $this->lang_btn_items       = $this->settings['lang_btn_items'];

            add_action('woocommerce_update_options_shipping_multiple_shipping', array( $this, 'process_admin_options' ) );
            add_filter('woocommerce_settings_api_sanitized_fields_'. $this->id, array($this, 'save_settings') );

        }

        function calculate_shipping( $package = array() ) {
            /*$rate = array(
                'id'        => $this->id,
                'label'     => $this->title
            );
            $this->add_rate($rate);*/
        }

        function init_form_fields() {
            global $woocommerce;
            $this->form_fields = array(
                'title' => array(
                    'title'         => __( 'Title', 'wc_shipping_multiple_address' ),
                    'type'          => 'text',
                    'description'   => __( 'This controls the title which the user sees during checkout.', 'wc_shipping_multiple_address' ),
                    'default'       => __( 'Multiple Shipping', 'wc_shipping_multiple_address' )
                ),
                'cart_duplication_section' => array(
                    'type'          => 'title',
                    'title'         => __('Cart Duplication', 'wc_shipping_multiple_address'),
                    'description'   => __('This functionality will allow your customers to duplicate the contents of their cart in order to be able to ship the same cart to multiple addresses in addition to individual products.', 'wc_shipping_multiple_address')
                ),
                'cart_duplication' => array(
                    'title'         => __('Enable Cart Duplication', 'wc_shipping_multiple_address'),
                    'type'          => 'checkbox',
                    'label'         => 'Enable'
                ),
                'gift_section'  => array(
                    'type'          => 'title',
                    'title'         => __('Gift Packages', 'wc_shipping_multiple_address'),
                    'description'   => __('Allow customers to mark certain shipping packages as gifts', 'wc_shipping_multiple_address')
                ),
                'gift_packages'     => array(
                    'title'         => __('Enable Gift Packages', 'wc_shipping_multiple_address'),
                    'type'          => 'checkbox',
                    'label'         => 'Enable'
                ),
                'exclusions'        => array(
                    'type'          => 'title',
                    'title'         => __('Excluded Products &amp; Categories', 'wc_shipping_multiple_address'),
                    'description'   => __('Do not allow multiple shipping addresses when any of the products and categories below are in the cart', 'wc_shipping_multiple_address')
                ),
                'excluded_products' => array(
                    'title'         => __('Products', 'wc_shipping_multiple_address'),
                    'type'          => 'ms_product_select'
                ),
                'excluded_categories' => array(
                    'title'         => __('Categories', 'wc_shipping_multiple_address'),
                    'type'          => 'ms_category_select'
                ),
                'language_section' => array(
                    'type'          => 'title',
                    'title'         => __('Text your shoppers see when Multiple Shipping is enabled at checkout', 'wc_shipping_multiple_address')
                ),
                'lang_notification' => array(
                    'type'          => 'text',
                    'title'         => __('Checkout Notification', 'wc_shipping_multiple_address'),
                    'css'           => 'width: 350px;',
                    'default'       => 'You may use multiple shipping addresses on this cart'
                ),
                'lang_btn_items' => array(
                    'type'          => 'text',
                    'title'         => __('Button: Item Addresses', 'wc_shipping_multiple_address'),
                    'css'           => 'width: 350px;',
                    'default'       => 'Set Addresses'
                ),
                'partial_orders'    => array(
                    'title'         => __('Partially Complete Orders', 'wc_shipping_multiple_address'),
                    'type'          => 'checkbox',
                    'label'         => 'Enable',
                    'description'   => __('Partially complete order by shipping address', 'wc_shipping_multiple_address'),
                    'desc_tip'      => true
                ),
                'email_section'     => array(
                    'type'          => 'title',
                    'title'         => __('Partial Order Completed Email', 'wc_shipping_multiple_address')
                ),
                'partial_orders_email' => array(
                    'title'         => __('Send Email', 'wc_shipping_multiple_address'),
                    'type'          => 'checkbox',
                    'label'         => 'Enable',
                    'description'   => __('Send an email when an order has been marked as partially complete', 'wc_shipping_multiple_address'),
                    'desc_tip'      => true
                ),
                'email_subject'     => array(
                    'type'          => 'text',
                    'title'         => __('Subject', 'wc_shipping_multiple_address'),
                    'css'           => 'width: 350px;',
                    'default'       => __('Part of your order has been shipped', 'wc_shipping_multiple_address')
                ),
                'email_message'     => array(
                    'type'          => 'ms_wp_editor',
                    'description'   => 'Leave empty to use the default email message',
                    'desc_tip'      => true,
                    'title'         => __('Message', 'wc_shipping_multiple_address'),
                    'css'           => 'width: 350px;',
                    'default'       => '<p>Hi there. Part of your recent order on '. get_option( 'blogname' ) .' has been completed. Your order details are shown below for your reference:</p>
                                        <h2>Order: {order_id}</h2>
                                        {products_table}
                                        {addresses_table}'
                ),
            );
        }

        /**
         * is_available function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        function is_available( $package ) {
            global $woocommerce;

            $packages = $woocommerce->cart->get_shipping_packages();

            if ( !empty($packages) && count($packages) > 1 ) {
                return true;
            }

            return false;
        }

        function generate_ms_wp_editor_html( $key, $data ) {
            global $woocommerce;

            $settings   = get_option( 'woocommerce_multiple_shipping_settings', array() );
            $html       = '';

            $data['title']          = isset( $data['title'] ) ? $data['title'] : '';
            $data['desc_tip']       = isset( $data['desc_tip'] ) ? $data['desc_tip'] : false;
            $data['description']    = isset( $data['description'] ) ? $data['description'] : '';

            // Description handling
            if ( $data['desc_tip'] === true ) {
                $description = '';
                $tip         = $data['description'];
            } elseif ( ! empty( $data['desc_tip'] ) ) {
                $description = $data['description'];
                $tip         = $data['desc_tip'];
            } elseif ( ! empty( $data['description'] ) ) {
                $description = $data['description'];
                $tip         = '';
            } else {
                $description = $tip = '';
            }

            // Custom attribute handling
            $custom_attributes = array();
            $editor     = '';
            $content    = (isset($settings['email_message'])) ? $settings['email_message'] : $data['default'];

            ob_start();
            wp_editor( $content, esc_attr( $this->plugin_id . $this->id . '_' . $key ) );
            $editor = ob_get_clean();

            $html .= '<tr valign="top">' . "\n";
                $html .= '<th scope="row" class="titledesc">';
                $html .= '<label for="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '">' . wp_kses_post( $data['title'] ) . '</label>';

                if ( $tip )
                    $html .= '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

                $html .= '<br/><br/><div class="vars-box">
                <strong>Available Variables</strong><br/>
                <em>{order_id}</em><br/>
                <em>{order_date}</em><br/>
                <em>{order_time}</em><br/>
                <em>{products_table}</em><br/>
                <em>{addresses_table}</em>
                </div>';

                $html .= '</th>' . "\n";
                $html .= '<td class="forminp">' . "\n";
                    $html .= '<fieldset><legend class="screen-reader-text"><span>' . wp_kses_post( $data['title'] ) . '</span></legend>' . "\n";
                    $html .= $editor;

                    if ( $description )
                        $html .= ' <p class="description">' . wp_kses_post( $description ) . '</p>' . "\n";

                $html .= '</fieldset>';
                $html .= '</td>' . "\n";
            $html .= '</tr>' . "\n";

            return $html;
        }

        function generate_ms_product_select_html( $key, $data ) {
            global $woocommerce;

            $settings   = get_option( 'woocommerce_multiple_shipping_settings', array() );

            $html = '';

            $data['title']          = isset( $data['title'] ) ? $data['title'] : '';
            $data['desc_tip']       = isset( $data['desc_tip'] ) ? $data['desc_tip'] : false;
            $data['description']    = isset( $data['description'] ) ? $data['description'] : '';

            // Description handling
            if ( $data['desc_tip'] === true ) {
                $description = '';
                $tip         = $data['description'];
            } elseif ( ! empty( $data['desc_tip'] ) ) {
                $description = $data['description'];
                $tip         = $data['desc_tip'];
            } elseif ( ! empty( $data['description'] ) ) {
                $description = $data['description'];
                $tip         = '';
            } else {
                $description = $tip = '';
            }

            // Custom attribute handling
            $custom_attributes = array();

            if ( WC_MS_Compatibility::is_wc_version_gte( '2.3' ) ) {
                $product_ids = array_filter( array_map( 'absint', $settings['excluded_products'] ) );
                $json_ids    = array();

                foreach ( $product_ids as $product_id ) {
                    $product = wc_get_product( $product_id );
                    $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
                }

                $html .= '<tr valign="top">' . "\n";
                $html .= '<th scope="row" class="titledesc">';
                $html .= '<label for="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '">' . wp_kses_post( $data['title'] ) . '</label>';

                if ( $tip )
                    $html .= '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

                $html .= '</th>' . "\n";
                $html .= '<td class="forminp">' . "\n";
                $html .= '<fieldset><legend class="screen-reader-text"><span>' . wp_kses_post( $data['title'] ) . '</span></legend>' . "\n";
                $html .= '<input
                            type="hidden"
                            data-multiple="true"
                            id="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '"
                            name="'. esc_attr( $this->plugin_id . $this->id . '_' . $key ) .'[]"
                            class="wcms-product-search"
                            data-placeholder="'. __('Search for a product&hellip;', 'woocommerce') .'"
                            style="width: 400px"
                            value="'. implode( ',', array_keys( $json_ids ) ) .'"
                            data-selected="'. esc_attr( json_encode( $json_ids ) ) .'"
                        >';

                if ( $description )
                    $html .= ' <p class="description">' . wp_kses_post( $description ) . '</p>' . "\n";

                $html .= '</fieldset>';
                $html .= '</td>' . "\n";
                $html .= '</tr>' . "\n";
            } else {
                $html .= '<tr valign="top">' . "\n";
                $html .= '<th scope="row" class="titledesc">';
                $html .= '<label for="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '">' . wp_kses_post( $data['title'] ) . '</label>';

                if ( $tip )
                    $html .= '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

                $html .= '</th>' . "\n";
                $html .= '<td class="forminp">' . "\n";
                $html .= '<fieldset><legend class="screen-reader-text"><span>' . wp_kses_post( $data['title'] ) . '</span></legend>' . "\n";
                $html .= '<select multiple="multiple" id="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '" name="'. esc_attr( $this->plugin_id . $this->id . '_' . $key ) .'[]" class="wcms-product-search" data-placeholder="'. __('Search for a product&hellip;', 'woocommerce') .'" style="width: 400px"><option></option>';

                if ( !empty($settings['excluded_products']) ) {
                    foreach ( $settings['excluded_products'] as $pid )
                        $html .= '<option value="'. $pid .'" selected>#'. $pid .' - '. get_the_title($pid) .'</option>';
                }

                $html .= '</select>';

                if ( $description )
                    $html .= ' <p class="description">' . wp_kses_post( $description ) . '</p>' . "\n";

                $html .= '</fieldset>';
                $html .= '</td>' . "\n";
                $html .= '</tr>' . "\n";
            }

            return $html;
        }

        function generate_ms_category_select_html( $key, $data ) {
            global $woocommerce;

            $settings   = get_option( 'woocommerce_multiple_shipping_settings', array() );

            $html = '';

            $data['title']          = isset( $data['title'] ) ? $data['title'] : '';
            $data['desc_tip']       = isset( $data['desc_tip'] ) ? $data['desc_tip'] : false;
            $data['description']    = isset( $data['description'] ) ? $data['description'] : '';

            // Description handling
            if ( $data['desc_tip'] === true ) {
                $description = '';
                $tip         = $data['description'];
            } elseif ( ! empty( $data['desc_tip'] ) ) {
                $description = $data['description'];
                $tip         = $data['desc_tip'];
            } elseif ( ! empty( $data['description'] ) ) {
                $description = $data['description'];
                $tip         = '';
            } else {
                $description = $tip = '';
            }

            // Custom attribute handling
            $custom_attributes  = array();
            $categories         = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
            $html .= '<tr valign="top">' . "\n";
                $html .= '<th scope="row" class="titledesc">';
                $html .= '<label for="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '">' . wp_kses_post( $data['title'] ) . '</label>';

                if ( $tip )
                    $html .= '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

                $html .= '</th>' . "\n";
                $html .= '<td class="forminp">' . "\n";
                    $html .= '<fieldset><legend class="screen-reader-text"><span>' . wp_kses_post( $data['title'] ) . '</span></legend>' . "\n";
                    $html .= '<select multiple="multiple" id="' . esc_attr( $this->plugin_id . $this->id . '_' . $key ) . '" name="'. esc_attr( $this->plugin_id . $this->id . '_' . $key ) .'[]" class="chosen_select " multiple data-placeholder="'. __('Select categories&hellip;', 'woocommerce') .'" style="width: 400px">';

                    foreach ( $categories as $category ) {
                        $selected = (isset($settings['excluded_categories']) && is_array($settings['excluded_categories']) && in_array($category->term_id, $settings['excluded_categories']) ) ? 'selected' : '';
                        $html .= '<option value="'. __($category->term_id) .'" '. $selected .'>'. esc_html($category->name) .'</option>';
                    }
                    $html .= '</select>';

                    if ( $description )
                        $html .= ' <p class="description">' . wp_kses_post( $description ) . '</p>' . "\n";

                $html .= '</fieldset>';
                $html .= '</td>' . "\n";
            $html .= '</tr>' . "\n";

            return $html;
        }

        function save_settings( $settings ) {
            $settings['email_subject']          = (isset($_POST['woocommerce_multiple_shipping_email_subject'])) ? $_POST['woocommerce_multiple_shipping_email_subject'] : '';
            $settings['email_message']          = (isset($_POST['woocommerce_multiple_shipping_email_message'])) ? $_POST['woocommerce_multiple_shipping_email_message'] : '';
            $settings['excluded_categories']    = (isset($_POST['woocommerce_multiple_shipping_excluded_categories'])) ? $_POST['woocommerce_multiple_shipping_excluded_categories'] : array();

            $products   = array();
            $key        = 'woocommerce_multiple_shipping_excluded_products';
            if ( !empty($_POST[ $key ]) ) {

                if ( !empty($_POST[ $key ][0]) && strpos( $_POST[ $key ][0], ',' ) !== false ) {
                    $products = array_filter( explode( ',', $_POST[ $key ][0] ) );
                } else {
                    $products = $_POST[ $key ];
                }

            }
            $settings['excluded_products'] = $products;

            return $settings;
        }

        function validate_ms_product_select_field( $key ) {
            $text = $this->get_option( $key );

            if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) && is_array( $_POST[ $this->plugin_id . $this->id .'_'. $key ] ) ) {
                $val    = $_POST[ $this->plugin_id . $this->id . '_' . $key ];
                $new    = array();

                foreach ( $val as $value ) {
                    $new[] = wp_kses_post( trim( stripslashes( $value ) ) );
                }

                $text = $new;
            }

            return $text;
        }

        function validate_ms_category_select_field( $key ) {
            $text = $this->get_option( $key );

            if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) && is_array( $_POST[ $this->plugin_id . $this->id .'_'. $key ] ) ) {
                $val    = $_POST[ $this->plugin_id . $this->id . '_' . $key ];
                $new    = array();

                foreach ( $val as $value ) {
                    $new[] = wp_kses_post( trim( stripslashes( $value ) ) );
                }

                $text = $new;
            }

            return $text;
        }
    }

    function add_multiple_shipping_method($methods) {
        if ( in_array('woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-address.php', get_option('active_plugins')) )
            $methods[] = 'WC_Multiple_Shipping';

        return $methods;
    }

    add_filter( 'woocommerce_shipping_methods','add_multiple_shipping_method' );
}

add_action('plugins_loaded', 'woocommerce_multi_shipping_init', 100);
