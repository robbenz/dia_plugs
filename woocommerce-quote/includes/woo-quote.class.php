<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooQuote')) {

/**
 * PDF generation class
 * 
 * @class WooQuote
 * @package WooCommerce_Quote
 * @author RightPress
 */
class WooQuote extends TCPDF
{
    private $opt;
    private $number;
    private $current_page;
    private $page_count;
    private $wc_countries;

    /**
     * Class constructor
     * 
     * @access public
     * @param array $options
     * @param int $number
     * @param string $orientation
     * @param string $unit
     * @param string $format
     * @return void
     */
    function __construct($options, $number, $customer_details, $orientation, $unit, $format)
    {
        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);

        // Load order mockup class
        require 'woo-quote-order.class.php';

        // Set up properties
        $this->opt = $options;
        $this->number = $number;
        $this->customer = $customer_details;

        $this->order_tax = null;
        $this->totals = null;

        // Load cart
        global $woocommerce;
        $this->cart = $woocommerce->cart;
        $this->orderData = new WooQuote_Order($this->cart);

        // Development settings
        $this->showBorders = 0;

        // Check if different tax rates are used for different items and if so - get total for each of them
        $this->multiple_tax_rates = $this->maybe_get_multiple_tax_totals();

        // Convert HTML entities to regular characters
        $quote_options = $this->opt;
        $this->opt = array();
        foreach ($quote_options as $key => $value) {
            $this->opt[$key] = htmlspecialchars_decode($value);
        }

        // Initial setup
        $this->footer_height = $this->get_height('footer');
        define('WOOQUO_MAX_FIRST_PAGE_HEIGHT', (800 - $this->footer_height));
        define('WOOQUO_MAX_PAGE_HEIGHT', (800 - $this->footer_height));
        $this->current_page = 1;
        $this->page_count = 1;

        // Set document meta-information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor($this->opt['woo_quote_seller_name']);
        $this->SetTitle((!empty($this->opt['woo_quote_document_name']) ? $this->opt['woo_quote_document_name'] . ' #' : '#') . $this->number);
        $this->SetSubject($this->opt['woo_quote_document_name']);
        $this->SetKeywords($this->opt['woo_quote_seller_name'], '#' . $this->number);

        // Set the page margins: 36pt on all sides
        $this->SetMargins(36, 36, 36, true);
        $this->SetAutoPageBreak(true, 36);

        // Set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO); 

        // Set some language-dependent strings
        global $l;
        $this->setLanguageArray($l);
    }

    /**
     * Create PDF Quote
     * 
     * @access public
     * @return void
     */
    public function CreateQuote()
    {
        // Add first page
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->AddPage();

        // Track pointer
        $y = 40;
        $r = 12;
        $s = 5;

        $y = $this->render_first_page_header($y, $r, $s);

        // Padding after header
        $y = $y + 40;

        $y = $this->render_table_header($y, $r, $s);

        // Padding after table header
        $y = $y + 4;

        $this->style();

        if (sizeof($this->orderData->get_items()) > 0) {
            foreach ($this->orderData->get_items() as $item) {
                $_product = $this->orderData->get_product_from_item($item);
                $item_obj = new WC_Order_Item_Meta($item['item_meta']);
                $item_meta = $this->item_meta($item);

                // Maybe prepend short product description
                if ($this->opt['woo_quote_display_short_description'] && !empty($_product->post->post_excerpt)) {
                    $post_excerpt = $this->opt['woo_quote_title_description'] . ': ' . strip_tags($_product->post->post_excerpt);
                    $item_meta = empty($item_meta) ? $post_excerpt : $post_excerpt . PHP_EOL . $item_meta;
                }

                // Maybe prepend list of product categories
                if ($this->opt['woo_quote_display_category'] && $current_item_categories = $this->get_product_category_names($_product->id)) {
                    $current_item_categories = $this->opt['woo_quote_title_category'] . ': ' . join(', ', $current_item_categories);
                    $item_meta = empty($item_meta) ? $current_item_categories : $current_item_categories . PHP_EOL . $item_meta;
                }

                $current_meta_height = $this->get_height('meta', $item_meta);

                // Maybe display product images
                $product_images = array();
                $current_images_height = 0;

                if ($this->opt['woo_quote_display_product_thumbnails']) {
                    $product_image_ids = $_product->get_gallery_attachment_ids();

                    if (is_array($product_image_ids) && !empty($product_image_ids)) {
                        $product_images = (count($product_image_ids) > 5) ? array_slice($product_image_ids, 0, 5) : $product_image_ids;
                        $current_images_height = 100;
                    }
                }

                // Add new page if needed
                if (($y + $s + $r + 2 + (empty($item_meta) ? 0 : ($current_meta_height + 2)) + $current_images_height + 20) > WOOQUO_MAX_FIRST_PAGE_HEIGHT) {
                    $y = $this->add_page();
                }

                $row = array(
                    'name' => $item['name'],
                    'price' => $this->item_price($item, $this->orderData),
                    'quantity' => $item['qty'],
                    'meta' => $item_meta,
                    'images' => $product_images,
                    'net' => $item['line_subtotal'],
                    'tax_rate' => $this->get_item_tax_rate($item['line_subtotal'], $item['line_subtotal_tax']),
                    'tax' => $item['line_subtotal_tax'],
                    'total' => $this->row_total($item, $this->orderData),
                );

                // Check if we need to display product ID/SKU
                $item_name = $row['name'];
                if ($this->opt['woo_quote_display_product_id'] == 1) {
                    $item_name = $item['product_id'] . ' - ' . $item_name;
                }
                else if ($this->opt['woo_quote_display_product_id'] == 2) {
                    $item_sku = $_product->get_sku();
                    if ($item_sku != '')
                        $item_name = $item_sku . ' - ' . $item_name;
                }

                // Different tax rates used
                if (($this->opt['woo_quote_display_tax_inline'] == 0 && $this->multiple_tax_rates) || $this->opt['woo_quote_display_tax_inline'] == 1) {

                    // Name
                    $this->MultiCell(200, $r, $this->decode($item_name), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, $r, 'T', false);

                    // Price
                    $this->MultiCell(60, $r, $this->format_currency($row['price']), $this->showBorders, 'R', 0, 1, 240, $y, true, 0, false, false, $r, 'T', false);

                    // Quantity
                    $this->MultiCell(40, $r, $row['quantity'], $this->showBorders, 'R', 0, 1, 300, $y, true, 0, false, false, $r, 'T', false);

                    // Net
                    $this->MultiCell(60, $r, $this->format_currency($row['net']), $this->showBorders, 'R', 0, 1, 340, $y, true, 0, false, false, $r, 'T', false);

                    // Tax %
                    $this->MultiCell(50, $r, $row['tax_rate'] . ' %', $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, $r, 'T', false);

                    // Tax
                    $this->MultiCell(50, $r, $this->format_currency($row['tax']), $this->showBorders, 'R', 0, 1, 450, $y, true, 0, false, false, $r, 'T', false);

                    // Total
                    $this->MultiCell(60, $r, $this->format_currency($row['total']), $this->showBorders, 'R', 0, 1, 500, $y, true, 0, false, false, $r, 'T', false);

                    // Meta
                    if (!empty($row['meta'])) {
                        $y += $r + 2;
                        $this->style('gray');
                        $this->MultiCell(515, 0, $this->decode($row['meta']), $this->showBorders, 'L', 0, 1, 45, $y, true, 0, false, false, 0, 'T', false);
                        $y += $current_meta_height;
                        $this->style();
                    }

                    // Images
                    if (!empty($row['images'])) {
                        $y += empty($row['meta']) ? ($r + 7) : 0;

                        $i = 0;

                        foreach ($row['images'] as $attachment_id) {
                            $product_image_url = wp_get_attachment_url($attachment_id);
                            $this->Image($product_image_url, (50 + ($i * 100)), $y, 90, 90, '', '', 'T', true, 300, '', false, false, $this->showBorders, true, false, false, false, false);
                            $i++;
                        }

                        $y += $current_images_height;
                    }

                    $y += $s + ((!empty($row['meta']) || !empty($row['images'])) ? 0 : $r);
                }

                // Different tax rates not used or feature disabled
                else {

                    // Name
                    $this->MultiCell(280, $r, $this->decode($item_name), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, $r, 'T', false);

                    // Price
                    $this->MultiCell(80, $r, $this->format_currency($row['price']), $this->showBorders, 'R', 0, 1, 320, $y, true, 0, false, false, $r, 'T', false);

                    // Quantity
                    $this->MultiCell(80, $r, $row['quantity'], $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, $r, 'T', false);

                    // Total
                    $this->MultiCell(80, $r, $this->format_currency($row['total']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, $r, 'T', false);

                    // Meta
                    if (!empty($row['meta'])) {
                        $y += $r + 2;
                        $this->style('gray');
                        $this->MultiCell(515, 0, $this->decode($row['meta']), $this->showBorders, 'L', 0, 1, 45, $y, true, 0, false, false, 0, 'T', false);
                        $y += $current_meta_height;
                        $this->style();
                    }

                    // Images
                    if (!empty($row['images'])) {
                        $y += empty($row['meta']) ? ($r + 7) : 0;

                        $i = 0;

                        foreach ($row['images'] as $attachment_id) {
                            $product_image_url = wp_get_attachment_url($attachment_id);
                            $this->Image($product_image_url, (50 + ($i * 100)), $y, 90, 90, '', '', 'T', true, 300, '', false, false, $this->showBorders, true, false, false, false, false);
                            $i++;
                        }

                        $y += $current_images_height;
                    }

                    $y += $s + ((!empty($row['meta']) || !empty($row['images'])) ? 0 : $r);
                }

                // Draw bottom border
                $this->Line(40, ($y-2), 560, ($y-2), array('color' => array(192, 192, 192)));
                $y += 2;
            }
        }

        $y = $this->render_totals($y, $r, $s);

        // Reset cell padding
        $this->SetCellPaddings(2.835, 0, 2.835, 0);

        // Render custom content blocks
        foreach (array('1', '2', '3', '4') as $n) {
            if ($this->opt['woo_quote_block_'.$n.'_title'] != '' || $this->opt['woo_quote_block_'.$n.'_content'] != '') {
                $y = $this->render_left_block($y, $r, $s, 'block_'.$n);
            }
        }

        $this->render_footer();

        if ($this->page_count > 1) {
            $this->render_page_numbers();
        }
    }

    /**
     * Determine height of element
     * 
     * @access public
     * @param string $context
     * @param string $string
     * @return int
     */
    public function get_height($context, $string = '')
    {
        // Add blank page to test height
        $this->AddPage();
        $this->SetY(0);

        // Test render string (depending on context)
        if ($context == 'footer') {
            $this->render_footer(true);
        }
        else if ($context == 'meta') {
            $this->MultiCell(515, 0, $this->decode($string), $this->showBorders, 'L', 0, 1, 45, 0, true, 0, false, false, 0, 'T', false);
        }
        else if ($context == 'block') {
            $this->MultiCell(520, 0, $this->decode($string), $this->showBorders, 'L', 0, 1, 40, 0, true, 0, false, false, 0, 'T', false);
        }

        // Get y position and remove the page
        $y = $this->getY();
        $this->deletePage($this->getPage());

        return $y;
    }

    /**
     * Render table header
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_table_header($y = 240, $r = 0, $s = 0)
    {
        $this->style('b');
        $this->SetCellPadding(5);

        // Different tax rates used
        if (($this->opt['woo_quote_display_tax_inline'] == 0 && $this->multiple_tax_rates) || $this->opt['woo_quote_display_tax_inline'] == 1) {

            // Name
            $this->MultiCell(200, 20, $this->decode($this->opt['woo_quote_title_product']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);

            // Price
            $this->MultiCell(60, 20, $this->decode($this->opt['woo_quote_title_price']), $this->showBorders, 'R', 0, 1, 240, $y, true, 0, false, false, 20, 'M', false);

            // Quantity
            $this->MultiCell(40, 20, $this->decode($this->opt['woo_quote_title_quantity']), $this->showBorders, 'R', 0, 1, 300, $y, true, 0, false, false, 20, 'M', false);

            // Net
            $this->MultiCell(60, 20, $this->decode($this->opt['woo_quote_title_net']), $this->showBorders, 'R', 0, 1, 340, $y, true, 0, false, false, 20, 'M', false);

            // Tax %
            $this->MultiCell(50, 20, $this->decode($this->opt['woo_quote_title_tax_percent']), $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, 20, 'M', false);

            // Tax
            $this->MultiCell(50, 20, $this->decode($this->opt['woo_quote_title_tax']), $this->showBorders, 'R', 0, 1, 450, $y, true, 0, false, false, 20, 'M', false);

            // Total
            $this->MultiCell(60, 20, $this->decode($this->opt['woo_quote_title_line_total']), $this->showBorders, 'R', 0, 1, 500, $y, true, 0, false, false, 20, 'M', false);

        }

        // Different tax rates not used or feature disabled
        else {

            // Name
            $this->MultiCell(280, 20, $this->decode($this->opt['woo_quote_title_product']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);

            // Price
            $this->MultiCell(80, 20, $this->decode($this->opt['woo_quote_title_price']), $this->showBorders, 'R', 0, 1, 320, $y, true, 0, false, false, 20, 'M', false);

            // Quantity
            $this->MultiCell(80, 20, $this->decode($this->opt['woo_quote_title_quantity']), $this->showBorders, 'R', 0, 1, 400, $y, true, 0, false, false, 20, 'M', false);

            // Total
            $this->MultiCell(80, 20, $this->decode($this->opt['woo_quote_title_line_total']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, 20, 'M', false);

        }

        // Draw bottom border
        $this->Line(40, ($y+20), 560, ($y+20), array('color' => array(32, 32, 32)));

        return $this->getY();
    }

    /**
     * Render totals
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return int
     */
    public function render_totals($y, $r, $s)
    {
        // Get totals
        if (is_null($this->totals)) {
            $this->totals = $this->get_totals();
        }

        // Calculate proposed height
        $height = 10 + 15; // adding 15 for higher than usual "totals" field
        foreach ($this->totals as $key => $value) {
            if (!is_null($value['value'])) {
                $count = is_array($value['value']) ? count($value['value']) : 1;
                $height += ($r + $s) * $count;
            }
        }

        // Add new page if needed
        $allowed_height = $this->current_page == 1 ? WOOQUO_MAX_FIRST_PAGE_HEIGHT : WOOQUO_MAX_PAGE_HEIGHT;
        if (($y + $height) > $allowed_height) {
            $y = $this->add_page(false);
        }

        // Draw top border
        $this->Line(40, ($y-2), 560, ($y-2), array('color' => array(32, 32, 32)));
        $y += 10;

        foreach ($this->totals as $key => $value) {
            if (is_null($value['value'])) {
                continue;
            }

            // Check if we need to show tax rows
            if ($key == 'taxes' && ($this->opt['woo_quote_list_tax'] == 0 || ($this->opt['woo_quote_list_tax'] == 2 && $this->opt['woo_quote_display_tax_inline'] == 0 && $this->multiple_tax_rates) || ($this->opt['woo_quote_list_tax'] == 2 && $this->opt['woo_quote_display_tax_inline'] == 1))) {
                continue;
            }

            // Check if tax has been displayed inline and not set to always display on totals block
            if ($key == 'taxes' && $this->multiple_tax_rates && $this->opt['woo_quote_list_tax'] == '2') {
                continue;
            }

            // If we reached this point, proceed normally
            if (is_array($value['value'])) {
                foreach ($value['value'] as $code => $field) {
                    $field_name = (isset($field['rate'])) ? $field['name'] . ' (' . floatval($field['rate']) . ' %)' : $field['name'];
                    $this->MultiCell(140, $r, $this->decode($field_name), $this->showBorders, 'L', 0, 1, 340, $y, true, 0, false, false, $r, 'T', false);
                    $this->MultiCell(80, $r, $this->format_currency($field['amount']), $this->showBorders, 'R', 0, 1, 480, $y, true, 0, false, false, $r, 'T', false);
                    $y += $r + $s;
                }

                continue;
            }

            if (in_array($key, array('subtotal', 'total'))) {
                $this->style('b');
            }

            // Set up totals row
            $auto_padding = ($key != 'total') ? false : true;
            $borders_name = ($key != 'total') ? $this->showBorders : array('LTB' => array('width' => 1, 'color' => array(0, 0, 0)));
            $borders_value = ($key != 'total') ? $this->showBorders : array('TRB' => array('width' => 1, 'color' => array(0, 0, 0)));

            // Name
            $name = ($key == 'total') ? $value['name'] . ' (' . $this->decode($this->currency(false)) . ')' : $value['name'];
            $this->MultiCell(140, $r, $this->decode($name), $borders_name, 'L', 0, 1, 340, $y, true, 0, false, $auto_padding, $r, 'T', false);

            // Value
            $this->MultiCell(80, $r, $this->format_currency($value['value']), $borders_value, 'R', 0, 1, 480, $y, true, 0, false, $auto_padding, $r, 'T', false);

            $y += $r + $s + ($key == 'total' ? 10 : 0);

            $this->style();
        }

        return $y;

    }

    /**
     * Render left block
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @param string $type
     * @return int
     */
    public function render_left_block($y, $r, $s, $type)
    {
        // Define blocks
        $blocks = array();

        // Define all custom blocks
        foreach (array('1', '2', '3', '4') as $n) {
            $blocks['block_'.$n] = array(
                'title' => $this->replace_macros($this->opt['woo_quote_block_'.$n.'_title'], false),
                'text' => $this->replace_macros($this->opt['woo_quote_block_'.$n.'_content']),
            );
        }

        // Determine block content height
        $current_content_height = $this->get_height('block', $blocks[$type]['text']);

        // Add new page if needed
        $allowed_height = $this->current_page == 1 ? WOOQUO_MAX_FIRST_PAGE_HEIGHT : WOOQUO_MAX_PAGE_HEIGHT;
        if (($y + (20 + 15 + $s + $current_content_height + 20)) > $allowed_height) {
            $y = $this->add_page(false);
        }

        $y += 10;
        $this->style('b');
        $this->MultiCell(520, $r, $this->decode($blocks[$type]['title']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 20, 'M', false);
        $this->style();
        $y += 15 + $s;
        $this->MultiCell(520, 0, $this->decode($blocks[$type]['text']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

        return $this->GetY();
    }

    /**
     * Add new page
     * 
     * @access public
     * @param bool $has_products
     * @return int
     */
    public function add_page($has_products = true)
    {
        // Add footer to current page
        $this->render_footer();

        // Add new page
        $this->AddPage();

        // Print header
        $this->render_page_header();

        $this->style('gray');
        $this->MultiCell(100, 12, $this->decode($this->opt['woo_quote_title_additional_page']), $this->showBorders, 'L', 0, 1, 40, 40, true, 0, false, false, 0, 'M', false);
        $this->style();

        if ($has_products) {
            $this->render_table_header(120);
            $this->style();
        }

        // Increment counters
        $this->current_page = $this->page_count + 1;
        $this->page_count++;

        // Reset y
        $y = $has_products ? 145 : 130;

        return $y;
    }

    /**
     * Render page numbers on all pages
     * 
     * @access public
     * @return void
     */
    public function render_page_numbers()
    {
        $page_number = $this->page_count;
        while ($page_number) {
            $this->setPage($page_number);
            $this->style('gray');
            $this->MultiCell(100, 12, $this->decode($this->opt['woo_quote_title_page']) . ' ' . $page_number . ' / ' . $this->page_count, $this->showBorders, 'R', 0, 1, 460, 790, true, 0, false, false, 0, 'M', false);
            $this->style();
            $page_number--;
        }
    }

    /**
     * Render first page header
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_first_page_header($y, $r, $s)
    {
        $y_right_block = $y;

        // Show seller details block
        $y = $this->render_seller_details($y, $r, $s);

        // Space between seller and buyer details
        $y = $y + 20;

        // Show buyer details block
        $y = $this->render_buyer_details($y, $r, $s);

        // Show logo if set
        if (!empty($this->opt['woo_quote_seller_logo']) && is_array(getimagesize($this->opt['woo_quote_seller_logo']))) {
            $resize_factor = (isset($this->opt['woo_quote_seller_logo_resize']) && is_numeric($this->opt['woo_quote_seller_logo_resize'])) ? ($this->opt['woo_quote_seller_logo_resize'] / 100) : 1;
            $this->Image($this->opt['woo_quote_seller_logo'], 400, $y_right_block, (150 * $resize_factor), (100 * $resize_factor), '', '', 'T', true, 300, 'R', false, false, $this->showBorders, true, false, false, false, false);
            $y_right_block = $this->getImageRBY();
        }
        else {
            $y_right_block = 40;
        }

        if (($y_right_block + 20 + 40) < $y) {
            $y_right_block = $y - 40;
        }
        else {
            $y_right_block = $y_right_block + 20;
        }

        // Quote number
        $this->style('b');
        $this->MultiCell(140, 0, $this->decode($this->opt['woo_quote_document_name']), $this->showBorders, 'L', 0, 1, 340, $y_right_block, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(80, 0, '#' . $this->number, $this->showBorders, 'R', 0, 1, 480, $y_right_block, true, 0, false, false, 80, 'T', false);

        // Quote date
        $this->style();
        $y_right_block = $this->GetY() + 4;
        $this->MultiCell(140, 0, $this->decode($this->opt['woo_quote_title_date']), $this->showBorders, 'L', 0, 1, 340, $y_right_block, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(80, 0, $this->date(), $this->showBorders, 'R', 0, 1, 480, $y_right_block, true, 0, false, false, 80, 'T', false);

        // Order amount
        $this->style();
        $y_right_block = $this->GetY() + 2;
        $this->MultiCell(140, 0, $this->decode($this->opt['woo_quote_title_amount']) . ' (' . $this->decode($this->currency(false)) . ')', $this->showBorders, 'L', 0, 1, 340, $y_right_block, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(80, 0, $this->format_currency($this->total()), $this->showBorders, 'R', 0, 1, 480, $y_right_block, true, 0, false, false, 80, 'T', false);

        return $this->GetY();
    }

    /**
     * Render page header (all pages except first)
     * 
     * @access public
     * @return void
     */
    public function render_page_header()
    {

        // Quote number
        $this->style('b');
        $this->MultiCell(145, 0, $this->decode($this->opt['woo_quote_document_name']), $this->showBorders, 'L', 0, 1, 320, 40, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, '#' . $this->number, $this->showBorders, 'R', 0, 1, 465, 40, true, 0, false, false, 80, 'T', false);

        // Quote date
        $this->style();
        $this->MultiCell(145, 0, $this->decode($this->opt['woo_quote_title_date']), $this->showBorders, 'L', 0, 1, 320, 55, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, $this->date(), $this->showBorders, 'R', 0, 1, 465, 55, true, 0, false, false, 80, 'T', false);

        // Order amount
        $this->style();
        $this->MultiCell(145, 0, $this->decode($this->opt['woo_quote_title_amount']) . ' (' . $this->decode($this->currency(false)) . ')', $this->showBorders, 'L', 0, 1, 320, 68, true, 0, false, false, 80, 'T', false);
        $this->MultiCell(95, 0, $this->format_currency($this->total()), $this->showBorders, 'R', 0, 1, 465, 68, true, 0, false, false, 80, 'T', false);

    }

    /**
     * Render seller details block (6 lines at most)
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_seller_details($y, $r, $s)
    {
        // Seller block title
        $this->style('gray');
        $this->MultiCell(280, 0, $this->decode($this->opt['woo_quote_title_seller']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

        // Set black bold font
        $this->style(array('size' => 8, 'style' => 'b'));

        // Seller name
        if (!empty($this->opt['woo_quote_seller_name'])) {
            $this->MultiCell(280, 0, $this->decode($this->opt['woo_quote_seller_name']), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
        }

        // Seller details
        $this->style(array('size' => 8));
        $this->MultiCell(280, 0, $this->decode($this->opt['woo_quote_seller_content']), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);

        $y = $this->GetY();
        $y = ($y < 100) ? 100 : $y;

        return $y;
    }

    /**
     * Render buyer details block (7 lines at most)
     * 
     * @access public
     * @param int $y
     * @param int $r
     * @param int $s
     * @return void
     */
    public function render_buyer_details($y, $r, $s)
    {
        $buyer_content = '';

        // Seller block title
        $this->style('gray');
        $this->MultiCell(280, 0, $this->decode($this->opt['woo_quote_title_buyer']), $this->showBorders, 'L', 0, 1, 40, $y, true, 0, false, false, 0, 'T', false);

        // No user details?
        if (!is_user_logged_in() && empty($this->customer)) {
            $this->style();
            $this->MultiCell(280, 0, $this->decode($this->opt['woo_quote_title_not_specified']), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);

            $y = $this->GetY();
            $y = ($y < 180) ? 180 : $y;

            return $y;
        }

        // Set bold font
        $this->style('b');

        // Buyer company and name
        $name = array();
        if (!empty($this->orderData->billing_first_name)) {
            array_push($name, $this->orderData->billing_first_name);
        }
        if (!empty($this->orderData->billing_last_name)) {
            array_push($name, $this->orderData->billing_last_name);
        }

        if (!empty($this->orderData->billing_company)) {
            $this->MultiCell(280, 0, $this->decode($this->orderData->billing_company), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
            if (!empty($name)) {
                $buyer_content .= join(' ', $name) . PHP_EOL;
            }
        }
        else if (!empty($name)) {
            $this->MultiCell(280, 0, $this->decode(join(' ', $name)), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);
        }

        // Buyer details
        $buyer_content .= $this->replace_macros($this->opt['woo_quote_buyer_content']);

        $this->style();
        $this->MultiCell(280, 0, $this->decode($buyer_content), $this->showBorders, 'L', 0, 1, 40, ($this->GetY() + 2), true, 0, false, false, 0, 'T', false);

        $y = $this->GetY();
        $y = ($y < 180) ? 180 : $y;

        return $y;
    }

    /**
     * Render page footer
     * 
     * @access public
     * @return void
     */
    public function render_footer($is_test = false)
    {
        if (!empty($this->opt['woo_quote_footer'])) {

            $start_y = $is_test ? 0 : (780 - $this->footer_height);

            $this->style('footer');
            $this->Line(40, $start_y, 560, $start_y, array('color' => array(192, 192, 192)));
            $this->MultiCell(520, 0, $this->decode($this->replace_macros($this->opt['woo_quote_footer'])), $this->showBorders, 'C', 0, 1, 40, ($start_y + 10), true, 0, false, false, 500, 'T', false);
        }
    }


    /**
     * Set text style
     * 
     * @access public
     * @param string/array $input
     * @return void
     */
    public function style($input = null)
    {
        // General style
        $style = array(
                'family' => 'helvetica',
                'style' => '',
                'size' => 9,
                'color' => '#000000'
        );

        // Define styles
        $styles = array(
            'b' => array(
                'style' => 'b',
            ),
            'gray' => array(
                'size' => 8,
                'color' => '#C0C0C0',
            ),
            'footer' => array(
                'size' => 8,
            ),
        );

        // Make requested style
        if (!empty($input) && !is_array($input)) {
            $style = array_merge($style, $styles[$input]);
        }
        else if (!empty($input) && is_array($input)) {
            $style = array_merge($style, $input);
        }

        list($r, $g, $b) = $this->hex_rgb($style['color']);

        $this->SetFont($style['family'], $style['style'], $style['size']);
        $this->SetTextColor($r, $g, $b);
    }


    /**
     * Convert hex color to rgb
     * 
     * @access public
     * @param string $hex
     * @return array
     */
    public function hex_rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
        }

        return array($r, $g, $b);
    }

    /**
     * Get formatted date
     * 
     * @access public
     * @param string $real_date
     * @return string
     */
    public function date($real_date = false)
    {
        $formats = array(
            '0' => 'n/j/y',
            '1' => 'n/j/Y',
            '2' => 'j/n/y',
            '3' => 'j/n/Y',
            '4' => 'y-m-d',
            '5' => 'Y-m-d',
            '6' => 'F j, Y',
            '7' => 'd.m.Y',
            '8' => 'd-m-Y',
        );

        $format = $formats[$this->opt['woo_quote_date_format']];

        return date($format);
    }

    /**
     * Get order total amount
     * 
     * @access public
     * @return double
     */
    public function total()
    {
        return (double) $this->orderData->order_total;
    }

    /**
     * Format amount
     * 
     * @access public
     * @param double $amount
     * @param int $decimals
     * @return string
     */
    public function format_amount($amount, $decimals = null)
    {
        $decimals = is_null($decimals) ? get_option('woocommerce_price_num_decimals') : $decimals;

        return number_format(
                   $amount,
                   $decimals,
                   get_option('woocommerce_price_decimal_sep'),
                   get_option('woocommerce_price_thousand_sep')
               );
    }

    /**
     * Format amount with currency symbol
     * 
     * @access public
     * @param double $amount
     * @return string
     */
    public function format_currency($amount)
    {
        if ($this->opt['woo_quote_display_currency_symbol'] && $currency_symbol = $this->decode($this->currency(true))) {
            // Add currency symbol
            $amount = preg_replace('/&nbsp;/', ' ', sprintf(get_woocommerce_price_format(), $currency_symbol, $this->format_amount((double) $amount)));
        }
        else {
            $amount = $this->format_amount((double) $amount);
        }

        return $amount;
    }

    /**
     * Decode HTML special entities back to characters
     * 
     * @access public
     * @param string $string
     * @return string
     */
    public function decode($string)
    {
        return html_entity_decode(htmlspecialchars_decode($string, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get order item meta information
     * 
     * @access public
     * @param array $item
     * @return string
     */
    public function item_meta($item)
    {
        global $woocommerce;

        if (empty($item['item_meta'])) {
            return;
        }

        $meta = array();
        $meta_string = '';

        foreach ($item['item_meta'] as $key => $values) {

            if (empty($values) || substr($key, 0, 1) == '_') {
                continue;
            }

            foreach ($values as $value) {

                if (is_serialized($value)) {
                    continue;
                }

                if (taxonomy_exists(esc_attr(str_replace('attribute_', '', $key)))) {
                    $term = get_term_by('slug', $value, esc_attr(str_replace('attribute_', '', $key)));
                    if (!is_wp_error($term) && $term->name) {
                        $value = $term->name;
                    }
                }

                if (WooCommerce_Quote::wc_version_gte('2.3')) {
                    $meta[wc_attribute_label(str_replace('attribute_', '', $key))][] = $value;
                }
                else {
                    $meta[$woocommerce->attribute_label(str_replace('attribute_', '', $key))][] = $value;
                }
            }

        }

        $meta_final = array();

        // Glue values together
        foreach ($meta as $key => $values) {
            $meta_final[] = $key . ': ' . join(', ', $values) . '.';
        }

        // Make string with each property in separate line
        $meta_string = join(PHP_EOL, $meta_final);

        return $meta_string;
    }

    /**
     * Get single row total (excl. taxes)
     * 
     * @access public
     * @param object $item
     * @param object $order
     * @return int
     */
    public function row_total($item, $order)
    {
        if (!isset($item['line_subtotal']) || !isset($item['line_subtotal_tax'])) {
            return;
        }

        $line_subtotal = ($this->opt['woo_quote_inclusive_tax'] || ($this->multiple_tax_rates && $this->opt['woo_quote_display_tax_inline'] == 0) || $this->opt['woo_quote_display_tax_inline'] == 1) ? ($item['line_subtotal'] + $item['line_subtotal_tax']) : $item['line_subtotal'];

        return (double) $line_subtotal;
    }

    /**
     * Calculate single item price (excl. taxes)
     * 
     * @access public
     * @param object $item
     * @param object $order
     * @return string
     */
    public function item_price($item, $order)
    {
        if (!isset($item['line_subtotal']) || !isset($item['line_subtotal_tax']) || !isset($item['qty'])) {
            return;
        }

        $inclusive_tax = ((($this->opt['woo_quote_display_tax_inline'] == 0 && $this->multiple_tax_rates) || $this->opt['woo_quote_display_tax_inline'] == 1) || !$this->opt['woo_quote_inclusive_tax']) ? false : true;

        $line_subtotal = $inclusive_tax ? ((double) $item['line_subtotal'] + (double) $item['line_subtotal_tax']) : (double) $item['line_subtotal'];

        $price = $line_subtotal / (int) $item['qty'];

        if ((((double) $price) * (int) $item['qty']) == (double) $this->row_total($item, $this->orderData)) {
            return $price;
        }
        else {
            return $price;
        }
    }

    /**
     * Get unformatted subtotals, taxes and totals
     * 
     * @access public
     * @return array
     */
    public function get_totals()
    {
        global $woocommerce;

        $totals = array();

        /**
         * SUBTOTAL
         */
        $subtotal = 0;

        foreach ($this->orderData->get_items() as $item) {
            if (isset($item['line_subtotal'])) {
                if ($this->opt['woo_quote_inclusive_tax'] || ($this->multiple_tax_rates && $this->opt['woo_quote_display_tax_inline'] == 0) || $this->opt['woo_quote_display_tax_inline'] == 1) {
                    $subtotal += floatval($item['line_subtotal']);

                    if (isset($item['line_subtotal_tax'])) {
                        $subtotal += floatval($item['line_subtotal_tax']);
                    }
                }
                else {
                    $subtotal += $this->orderData->get_line_subtotal($item);
                }
            }
        }

        $totals['subtotal'] = array(
            'name' => $this->opt['woo_quote_title_subtotal'],
            'value' => (double) $subtotal,
        );

        /**
         * CART DISCOUNT
         */
        $cart_discount = (double) $this->orderData->cart_discount;
        $totals['cart_discount'] = ($cart_discount > 0) ? $cart_discount : null;

        if (!is_null($totals['cart_discount'])) {
            $totals['cart_discount'] = array(
                'name' => $this->opt['woo_quote_title_cart_discount'],
                'value' => $totals['cart_discount'],
            );
        }

        /**
         * FEES
         */
        $totals['fees'] = array();

        if ($fees = $this->orderData->get_fees()) {
            foreach ($fees as $id => $fee) {
                $totals['fees'][$id] = array(
                    'name' => $fee['name'],
                    'amount' => (double) $fee['line_total']
                );
            }
        }

        if (empty($totals['fees'])) {
            $totals['fees'] = null;
        }

        if (!is_null($totals['fees'])) {
            $totals['fees'] = array(
                'name' => 'Fees',
                'value' => $totals['fees'],
            );
        }

        /**
         * TAXES
         */
        // Get all order tax rows if we don't have them yet
        if (is_null($this->order_tax)) {
            $this->order_tax = $this->get_order_tax();
        }

        $totals_taxes = array();

        foreach ($this->order_tax as $tax_key => $tax) {
            $totals_taxes[$tax_key] = array(
                'name'      => $tax['label'],
                'amount'    => (float) $tax['tax_amount']
            );

            if (isset($tax['woo_quote_tax_rate'])) {
                $totals_taxes[$tax_key]['rate'] = (float) $tax['woo_quote_tax_rate'];
            }
        }

        if (empty($totals_taxes)) {
            $totals_taxes = null;
        }

        if (!is_null($totals_taxes)) {
            $totals_taxes = array(
                'name' => 'Taxes',
                'value' => $totals_taxes,
            );
        }

        /**
         *  Display taxes before total only if subtotal is displayed excluding taxes (so values sum up nicely)
         */
        if ($totals_taxes && !$this->opt['woo_quote_inclusive_tax']) {
            $totals['taxes'] = $totals_taxes;
        }

        /**
         * ORDER DISCOUNT
         */
        $order_discount = (double) $this->orderData->get_total_discount();

        $totals['order_discount'] = ($order_discount > 0) ? $order_discount : null;

        if (!is_null($totals['order_discount'])) {
            $totals['order_discount'] = array(
                'name' => $this->opt['woo_quote_title_order_discount'],
                'value' => $totals['order_discount'],
            );
        }

        /**
         * TOTAL
         */
        $totals['total'] = array(
            'name' => $this->opt['woo_quote_title_total'],
            'value' => (double) $this->orderData->order_total,
        );

        /**
         *  Display taxes below total only if subtotal is displayed including taxes
         */
        if ($totals_taxes && !isset($totals['taxes'])) {
            $totals['taxes'] = $totals_taxes;
        }

        /**
         * Optional total excl. tax row
         */
        if ($this->opt['woo_quote_total_excl_tax']) {
            $totals['total_excl_tax'] = array(
                'name' => $this->opt['woo_quote_title_total_excl_tax'],
                'value' => (double) ($this->orderData->get_total() - $this->orderData->get_total_tax()),
            );
        }

        return $totals;
    }

    /**
     * Check if order contains different tax rates and return totals for each tax rate if so
     * 
     * @access public
     * @return mixed
     */
    public function maybe_get_multiple_tax_totals()
    {
        // Get order items
        $items = $this->orderData->get_items();

        $result = array();

        // Iterate over all items and extract tax rates and tax subtotals
        foreach ($items as $item) {
            if (isset($item['line_subtotal']) && isset($item['line_subtotal_tax'])) {

                // Calculate tax rate used for current item
                $tax_rate = $this->get_item_tax_rate($item['line_subtotal'], $item['line_subtotal_tax']);
                $result[(string) $tax_rate] = $item['line_subtotal_tax'] + (isset($result[(string) $tax_rate]) ? $result[(string) $tax_rate] : 0);

            }

        }

        // Do we have more than one tax rate in use
        if (!empty($result) && count($result) > 1) {
            return $result;
        }

        return false;
    }

    /**
     * Get tax rate for single item
     * 
     * @access public
     * @param $line_subtotal
     * @param $line_subtotal_tax
     * @return float
     */
    public function get_item_tax_rate($line_subtotal, $line_subtotal_tax)
    {
        if (empty($line_subtotal) || !is_numeric($line_subtotal)) {
            return 0;
        }

        if (empty($line_subtotal_tax) || !is_numeric($line_subtotal_tax)) {
            $line_subtotal_tax = 0;
        }

        // Get all order tax rows if we don't have them yet
        if (is_null($this->order_tax)) {
            $this->order_tax = $this->get_order_tax();
        }

        // Calculate current tax rate from subtotal and subtotal tax
        $tax_rate = $line_subtotal_tax / $line_subtotal * 100;

        // Due to rounding we may have incorrect tax rate - attempt to fix by matching with tax rates from DB
        $tax_rate_fixed = $tax_rate;
        $tax_rate_fix_amount = 0;

        foreach ($this->order_tax as $tax) {
            if (!isset($tax['woo_quote_tax_rate'])) {
                continue;
            }

            if (($tax['woo_quote_tax_rate'] > $tax_rate && $tax['woo_quote_tax_rate'] < ($tax_rate + 0.49)) || ($tax['woo_quote_tax_rate'] < $tax_rate && $tax['woo_quote_tax_rate'] > ($tax_rate - 0.49))) {
                if ($tax_rate_fix_amount == 0 || abs($tax['woo_quote_tax_rate'] - $tax_rate) < $tax_rate_fix_amount) {
                    $tax_rate_fixed = $tax['woo_quote_tax_rate'];
                    $tax_rate_fix_amount = abs($tax['woo_quote_tax_rate'] - $tax_rate);
                }
            }
        }

        return round((float) $tax_rate_fixed, 2);
    }

    /**
     * Get order tax and tax rates
     * 
     * @access public
     * @return array
     */
    public function get_order_tax()
    {
        // Get tax rows from cart
        $tax = array();

        foreach (array_keys($this->cart->taxes) as $key) {
            $code = $this->cart->tax->get_rate_code($key);

            if ($code) {
                $tax[$key] = array(
                    'rate_id'       => $key,
                    'label'         => $this->cart->tax->get_rate_label($key),
                    'compound'      => absint($this->cart->tax->is_compound($key) ? 1 : 0),
                    'tax_amount'    => (isset($this->cart->taxes[$key ]) ? $this->cart->taxes[$key] : 0),
                );
            }
        }

        if (!is_array($tax) || empty($tax)) {
            return array();
        }

        global $wpdb;

        $all_rates = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'woocommerce_tax_rates');

        if (!is_array($all_rates) || empty($all_rates)) {
            return $tax;
        }

        foreach ($tax as $single_tax_key => $single_tax) {
            foreach ($all_rates as $rate) {
                if (isset($single_tax['rate_id']) && $single_tax['rate_id'] == $rate->tax_rate_id) {
                    $tax[$single_tax_key]['woo_quote_tax_rate'] = $rate->tax_rate;
                }
            }
        }

        return $tax;
    }

    /**
     * Replace macros in text
     * 
     * @access public
     * @param string $string
     * @param bool $remove_empty_lines
     * @return string
     */
    public function replace_macros($string, $remove_empty_lines = false)
    {
        // List supported macros and get their values
        $macros = array(
            '{{order_date}}'            => $this->date(true),
            '{{billing_first_name}}'    => $this->orderData->billing_first_name,
            '{{billing_last_name}}'     => $this->orderData->billing_last_name,
            '{{billing_company}}'       => $this->orderData->billing_company,
            '{{billing_address_1}}'     => $this->orderData->billing_address_1,
            '{{billing_address_2}}'     => $this->orderData->billing_address_2,
            '{{billing_city}}'          => $this->orderData->billing_city,
            '{{billing_postcode}}'      => $this->orderData->billing_postcode,
            '{{billing_country}}'       => $this->orderData->billing_country,
            '{{billing_state}}'         => $this->orderData->billing_state,
            '{{billing_email}}'         => $this->orderData->billing_email,
            '{{billing_phone}}'         => $this->orderData->billing_phone,
        );

        // Allow custom macros
        $macros = apply_filters('woo_quote_macros', $macros, $this->orderData);

        // Split entire block into array line by line
        $lines = explode(PHP_EOL, $string);
        $processed_lines = array();

        // Load WC countries to replace country macros
        if (!$this->wc_countries) {
            $this->wc_countries = new WC_Countries();
        }

        // Iterate over all lines and replace macros
        foreach ($lines as $line_key => $line) {

            $macros_found = 0;
            $macros_not_empty = 0;
            $processed_line = $line;

            // Search for regular macros and replace them
            foreach ($macros as $macro => $value) {

                $replace_count = 0;

                // Convert two-letter country code to full country name
                if (in_array($macro, array('{{billing_country}}')) && $this->wc_countries && isset($this->wc_countries->countries[$value])) {
                    $value = $this->wc_countries->countries[$value];
                }

                $processed_line = preg_replace('/'.$macro.'/', $value, $processed_line, -1, $replace_count);

                $macros_found = $macros_found + $replace_count;

                if ($value != '') {
                    $macros_not_empty = $macros_not_empty + $replace_count;
                }
            }

            // Search for custom macros and attempt to replace them
            preg_match_all('/\{\{.+(?:\}\})/U', $processed_line, $matches);

            if (!empty($matches) && !empty($matches[0]) && is_user_logged_in()) {
                foreach ($matches[0] as $match) {

                    $key = preg_replace('/\{\{(.+)\}\}/', '$1', $match);

                    // Check in user meta
                    $buyer_custom_field = get_user_meta(get_current_user_id(), $key, true);

                    if ($buyer_custom_field == '') {
                        $buyer_custom_field = get_user_meta(get_current_user_id(), '_'.$key, true);
                    }

                    if ($buyer_custom_field != '') {
                        $macros_found++;
                        $macros_not_empty++;
                        $processed_line = preg_replace('/'.$match.'/', $buyer_custom_field, $processed_line);
                    }
                }
            }

            if (!$remove_empty_lines || $macros_found == 0 || $macros_not_empty != 0) {
                $processed_lines[] = $processed_line;
            }
        }

        // Join lines
        $string = join(PHP_EOL, $processed_lines);

        // Remove all macros that were not found
        $string = preg_replace('/\{\{.+(?:\}\})/U', '', $string);

        return $string;
    }

    /**
     * Return currency symbol
     * 
     * @param bool $symbol
     * @return string
     */
    public function currency($symbol = true)
    {
        $currencies_symbols_list = array(
            'ALL' => 'Lek',
            'AFN' => '؋',
            'ARS' => '$',
            'AWG' => 'ƒ',
            'AUD' => '$',
            'AZN' => 'ман',
            'BSD' => '$',
            'BBD' => '$',
            'BYR' => 'p.',
            'BZD' => 'BZ$',
            'BMD' => '$',
            'BOB' => '$b',
            'BAM' => 'KM',
            'BWP' => 'P',
            'BGN' => 'лв',
            'BRL' => 'R$',
            'BND' => '$',
            'KHR' => '៛',
            'CAD' => '$',
            'KYD' => '$',
            'CLP' => '$',
            'CNY' => '¥',
            'COP' => '$',
            'CRC' => '₡',
            'HRK' => 'kn',
            'CUP' => '₱',
            'CZK' => 'Kč',
            'DKK' => 'kr',
            'DOP' => 'RD$',
            'XCD' => '$',
            'EGP' => '£',
            'SVC' => '$',
            'EEK' => 'kr',
            'EUR' => '€',
            'FKP' => '£',
            'FJD' => '$',
            'GHC' => '¢',
            'GIP' => '£',
            'GTQ' => 'Q',
            'GGP' => '£',
            'GYD' => '$',
            'HNL' => 'L',
            'HKD' => '$',
            'HUF' => 'Ft',
            'ISK' => 'kr',
            'INR' => '₹',
            'IDR' => 'Rp',
            'IRR' => '﷼',
            'IMP' => '£',
            'ILS' => '₪',
            'JMD' => 'J$',
            'JPY' => '¥',
            'JEP' => '£',
            'KZT' => 'лв',
            'KPW' => '₩',
            'KRW' => '₩',
            'KGS' => 'лв',
            'LAK' => '₭',
            'LVL' => 'Ls',
            'LBP' => '£',
            'LRD' => '$',
            'LTL' => 'Lt',
            'MKD' => 'ден',
            'MYR' => 'RM',
            'MUR' => '₨',
            'MXN' => '$',
            'MNT' => '₮',
            'MZN' => 'MT',
            'NAD' => 'N$',
            'NPR' => '₨',
            'ANG' => 'ƒ',
            'NZD' => '$',
            'NIO' => 'C$',
            'NGN' => '₦',
            'KPW' => '₩',
            'NOK' => 'kr',
            'OMR' => '﷼',
            'PKR' => '₨',
            'PAB' => 'B/.',
            'PYG' => 'Gs',
            'PEN' => 'S/.',
            'PHP' => '₱',
            'PLN' => 'zł',
            'QAR' => '﷼',
            'RON' => 'lei',
            'RUB' => 'руб',
            'SHP' => '£',
            'SAR' => '﷼',
            'RSD' => 'Дин.',
            'SCR' => '₨',
            'SGD' => '$',
            'SBD' => '$',
            'SOS' => 'S',
            'ZAR' => 'R',
            'KRW' => '₩',
            'LKR' => '₨',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'SRD' => '$',
            'SYP' => '£',
            'TWD' => 'NT$',
            'THB' => '฿',
            'TTD' => 'TT$',
            'TRY' => 'TL',
            'TRL' => '₤',
            'TVD' => '$',
            'UAH' => '₴',
            'GBP' => '£',
            'USD' => '$',
            'UYU' => '$U',
            'UZS' => 'лв',
            'VEF' => 'Bs',
            'VND' => '₫',
            'YER' => '﷼',
            'ZWD' => 'Z$',
        );

        if ($symbol) {
            if (isset($currencies_symbols_list[get_woocommerce_currency()])) {
                return $currencies_symbols_list[get_woocommerce_currency()];
            }
            else {
                return false;
            }
        }
        else {
            return get_woocommerce_currency();
        }
    }

    /**
     * Get product categories
     * 
     * @access public
     * @param array $product_id
     * @return mixed
     */
    public function get_product_category_names($product_id)
    {
        $categories = array();
        $current_categories = wp_get_post_terms($product_id, 'product_cat');

        $post_categories_raw = null;

        foreach ($current_categories as $category) {
            $category_name = $category->name;

            if ($category->parent) {

                if (is_null($post_categories_raw)) {
                    $post_categories_raw = get_terms(array('product_cat'), array('hide_empty' => 0));
                }

                $parent_id = $category->parent;
                $has_parent = true;

                while ($has_parent) {
                    foreach ($post_categories_raw as $parent_post_cat_key => $parent_post_cat) {
                        if ($parent_post_cat->term_id == $parent_id) {
                            $category_name = $parent_post_cat->name . ' &rarr; ' . $category_name;

                            if ($parent_post_cat->parent) {
                                $parent_id = $parent_post_cat->parent;
                            }
                            else {
                                $has_parent = false;
                            }

                            break;
                        }
                    }
                }
            }

            $categories[$category->term_id] = $category_name;
        }

        return !empty($categories) ? $categories : false;
    }

}

}

?>