<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooQuote_Order')) {

/**
 * Mockup order class (mimics some of WC_Order properties)
 * 
 * @class WooQuote
 * @package WooCommerce_Quote
 * @author RightPress
 */
class WooQuote_Order
{
    /**
     * Class constructor
     * 
     * @access public
     * @param array $cart
     * @return void
     */
    function __construct($cart, $customer_details = array())
    {
        // General properties
        $this->items = $this->prepare_items($cart);
        $this->fees = $this->prepare_fees($cart);
        $this->discount_total = $cart->discount_cart;
        $this->total_tax = $cart->tax_total;
        $this->total = $cart->total;
        $this->order_total = $cart->total;
        $this->order_date = date('Y-m-d H:i:s');
        $this->cart_discount = $cart->discount_cart;

        // Customer details
        $this->billing_first_name = $this->get_customer_property('billing_first_name');
        $this->billing_last_name = $this->get_customer_property('billing_last_name');
        $this->billing_company = $this->get_customer_property('billing_company');
        $this->billing_address_1 = $this->get_customer_property('billing_address_1');
        $this->billing_address_2 = $this->get_customer_property('billing_address_2');
        $this->billing_city = $this->get_customer_property('billing_city');
        $this->billing_postcode = $this->get_customer_property('billing_postcode');
        $this->billing_country = $this->get_customer_property('billing_country');
        $this->billing_state = $this->get_customer_property('billing_state');
        $this->billing_email = $this->get_customer_property('billing_email');
        $this->billing_phone = $this->get_customer_property('billing_phone');

    }

    /**
     * Get customer property
     * 
     * @access public
     * @param string $property
     * @return string
     */
    public function get_customer_property($property)
    {
        // Do we have custom details entered?
        if (!empty($customer_details)) {
            return isset($customer_details[$property]) ? $customer_details[$property] : '';
        }

        // Do we know this user?
        else if (is_user_logged_in()) {
            return get_user_meta(get_current_user_id(), $property, true);
        }

        return '';
    }

    /**
     * Prepare order items
     * 
     * @access public
     * @param object $cart
     * @return array
     */
    public function prepare_items($cart)
    {
        $items = array();
        $i = 1;

        $cart_items = $cart->get_cart();

        if (is_array($cart_items)) {
            foreach ($cart_items as $item_key => $item) {

                $_product = $item['data'];

                $items[$i] = array(
                    'name'              => $_product->get_title(),
                    'type'              => 'line_item',
                    'qty'               => $item['quantity'],
                    'tax_class'         => $_product->get_tax_class(),
                    'product_id'        => $item['product_id'],
                    'variation_id'      => $item['variation_id'],
                    'line_subtotal'     => $item['line_subtotal'],
                    'line_total'        => $item['line_total'],
                    'line_tax'          => $item['line_tax'],
                    'line_subtotal_tax' => $item['line_subtotal_tax'],
                );

                $item_meta = array(
                    '_qty'                  => $item['quantity'],
                    '_tax_class'            => $_product->get_tax_class(),
                    '_product_id'           => $item['product_id'],
                    '_variation_id'         => $item['variation_id'],
                    '_line_subtotal'        => $item['line_subtotal'],
                    '_line_total'           => $item['line_total'],
                    '_line_tax'             => $item['line_tax'],
                    '_line_subtotal_tax'    => $item['line_subtotal_tax'],
                );

                $items[$i]['item_meta'] = array_merge($item_meta, (is_array($item['data']->product_custom_fields) ? $item['data']->product_custom_fields : array()));

                $i++;
            }
        }

        return $items;
    }

    /**
     * Prepare fees
     * 
     * @access public
     * @param object $cart
     * @return array
     */
    public function prepare_fees($cart)
    {
        $fees = array();

        if (is_array($cart->fees)) {
            foreach ($cart->fees as $fee) {
                $fees[$fee->id] = array(
                    'name'          => $fee->name,
                    'line_total'    => $fee->fee_total,
                );
            }
        }

        return $fees;
    }

    /**
     * Get items
     * 
     * @access public
     * @return array
     */
    public function get_items()
    {
        return $this->items;
    }

    /**
     * Get product from item
     * 
     * @access public
     * @param mixed $item
     * @return object
     */
    public function get_product_from_item($item)
    {
        return get_product($item['variation_id'] ? $item['variation_id'] : $item['product_id']);
    }

    /**
     * Get line subtotal
     * 
     * @access public
     * @param mixed $item
     * @return float
     */
    public function get_line_subtotal($item)
    {
        return round($item['line_subtotal'], 2);
    }

    /**
     * Get fees
     * 
     * @access public
     * @return array
     */
    public function get_fees()
    {
        return $this->fees;
    }

    /**
     * Get total order discount
     * 
     * @access public
     * @return float
     */
    public function get_total_discount()
    {
        $this->discount_total;
    }

    /**
     * Get order discount
     * 
     * @access public
     * @return float
     */

    /**
     * Get total
     * 
     * @access public
     * @return float
     */
    public function get_total()
    {
        return $this->total;
    }

    /**
     * Get total tax
     * 
     * @access public
     * @return float
     */
    public function get_total_tax()
    {
        return $this->total_tax;
    }

}

}