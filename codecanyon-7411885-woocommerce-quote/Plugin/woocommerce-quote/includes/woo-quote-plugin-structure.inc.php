<?php

/*
 * Returns configuration for this plugin
 * 
 * @return array
 */
function woo_quote_plugin_settings()
{
    return array(
        'general_settings' => array(
            'title' => __('Settings', 'woo_quote'),
            'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
            'children' => array(
                'quote_settings' => array(
                    'title' => __('General Settings', 'woo_quote'),
                    'children' => array(
                        'method' => array(
                            'title' => __('Quotation method', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 'disabled',
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                'request' => __('Request a quote', 'woo_quote'),
                                'cart' => __('Cart to quote', 'woo_quote'),
                                'disabled' => __('Disabled', 'woo_quote'),
                            ),
                            'hint' => __('<p></p>', 'woo_quote'),
                        ),
                        'require_billing_address' => array(
                            'title' => __('Require billing address', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, customers will be required to either have full billing details on file (logged in users) or fill in a billing address form.</p>', 'woo_quote'),
                        ),
                        'allow_custom_billing_address' => array(
                            'title' => __('Allow custom billing address', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, customers will be allowed to override their current billing address. Please note that administrators and shop managers are always allowed to override billing address.</p>', 'woo_quote'),
                        ),
                        'request_disable_checkout' => array(
                            'title' => __('Disable checkout', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, this plugin will attempt to hide the checkout button on the cart page. You are adviced to test this functionality on your website to make sure it works as expected. You may need to remove Checkout link from your menu, for example.</p>', 'woo_quote'),
                        ),
                        'request_hide_prices' => array(
                            'title' => __('Hide all prices and totals', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, this plugin will attempt to hide all prices from visitors. If you use a custom theme, plugins or your own custom code, not all prices may be hidden. Test this functionality to make sure it works as expected on your website.</p>', 'woo_quote'),
                        ),
                        'title_instead_of_price' => array(
                            'title' => __('String instead of price', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('n/a', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'button_position' => array(
                            'title' => __('Cart button position', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 'woocommerce_proceed_to_checkout',
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                'woocommerce_before_cart' => __('Above cart', 'woo_quote'),
                                'woocommerce_after_cart' => __('Below cart', 'woo_quote'),
                                'woocommerce_before_cart_contents' => __('Above cart contents', 'woo_quote'),
                                'woocommerce_after_cart_contents' => __('Below cart contents', 'woo_quote'),
                                'woocommerce_before_cart_table' => __('Above cart table', 'woo_quote'),
                                'woocommerce_after_cart_table' => __('Below cart table', 'woo_quote'),
                                'woocommerce_proceed_to_checkout' => __('Next to checkout button', 'woo_quote'),
                            ),
                            'hint' => __('<p>Choose position on the cart page where quote download button should appear. Not all of positions will work with all themes.</p>', 'woo_quote'),
                        ),
                        'title_download_quote' => array(
                            'title' => __('Cart button label', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Quote', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_add_to_cart' => array(
                            'title' => __('Change Add To Cart button text', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Download Quote', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'advanced_settings' => array(
            'title' => __('Quote', 'woo_quote'),
            'icon' => '<i class="fa fa-cogs" style="font-size: 0.8em;"></i>',
            'children' => array(
                'other_quote_settings' => array(
                    'title' => __('Quote Settings', 'woo_quote'),
                    'children' => array(
                        'next_quote_number' => array(
                            'title' => __('Next quote number', 'woo_quote'),
                            'type' => 'text',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                        ),
                        'display_product_id' => array(
                            'title' => __('Display product ID/SKU', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('Do not display', 'woo_quote'),
                                '1' => __('Display product ID (WP post ID)', 'woo_quote'),
                                '2' => __('Display SKU', 'woo_quote'),
                            ),
                            'hint' => __('<p>If enabled, product ID/SKU will be displayed for each product just before its name.</p>', 'woo_quote'),
                        ),
                        'display_category' => array(
                            'title' => __('Display product category', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product category below each product on the quote.</p> <p>If there are multiple categories, they will be displayed in one line separated by commas.</p>', 'woo_quote'),
                        ),
                        'display_short_description' => array(
                            'title' => __('Display short description', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product short description below each product on the quote.</p> <p>This extension attempts to convert HTML to text but this feature is experimental. Use plain text to be sure that the final result looks as expected.</p>', 'woo_quote'),
                        ),
                        'display_product_thumbnails' => array(
                            'title' => __('Display product images', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>Controls whether or not to display product images below product name whenever available.</p> <p>This feature is experimental - use at your own risk.</p>', 'woo_quote'),
                        ),
                        'display_currency_symbol' => array(
                            'title' => __('Display currency symbol', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, currency symbol (e.g. $) will be displayed next to every amount on the quote. Currency code (e.g. USD) is displayed next to total amount in any way.</p>', 'woo_quote'),
                        ),
                    ),
                ),
                'tax_settings' => array(
                    'title' => __('Tax Settings', 'woo_quote'),
                    'children' => array(
                        'list_tax' => array(
                            'title' => __('Display tax rows', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 2,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '2' => __('When tax is not displayed inline', 'woo_quote'),
                                '1' => __('Always', 'woo_quote'),
                                '0' => __('Never', 'woo_quote'),
                            ),
                            'hint' => __('<p>If enabled, all applicable taxes will be listed just above (if Subtotal is exclusive of tax) or below (if Subtotal is inclusive of tax) Total row.</p>', 'woo_quote'),
                        ),
                        'display_tax_inline' => array(
                            'title' => __('Display tax inline', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('When different rates are present', 'woo_quote'),
                                '1' => __('Always', 'woo_quote'),
                                '2' => __('Never', 'woo_quote'),
                            ),
                            'hint' => __('<p>You may need to display net amount, tax rate and tax amount individually for each line item.</p> <p>This is useful when different rates of the same tax are used for different items on the same quote, e.g. reduced VAT rate is applied to specific group of products.</p>', 'woo_quote'),
                        ),
                        'inclusive_tax' => array(
                            'title' => __('Display amounts inclusive of tax', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 1,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, line item price, line subtotal and subtotal will be displayed inclusive of tax. This plugin always overrides related WooCommerce settings.</p><p>This setting is ignored when tax is displayed inline.</p>', 'woo_quote'),
                        ),
                        'total_excl_tax' => array(
                            'title' => __('Display total excl. tax row', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, additional line will be added to the totals block that displays total exclusive of tax.</p>', 'woo_quote'),
                        ),
                    ),
                ),
            ),
        ),
        'seller_details' => array(
            'title' => __('Seller & Buyer', 'woo_quote'),
            'icon' => '<i class="fa fa-briefcase" style="font-size: 0.8em;"></i>',
            'children' => array(
                'seller_block' => array(
                    'title' => __('Seller Block', 'woo_quote'),
                    'children' => array(
                        'seller_logo' => array(
                            'title' => __('Logo image', 'woo_quote'),
                            'type' => 'media',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'url',
                                'empty' => true
                            ),
                            'hint' => __('<p>Enter URL or select image from Media Library.</p>', 'woo_quote'),
                        ),
                        'seller_logo_resize' => array(
                            'title' => __('Logo resize factor', 'woo_quote'),
                            'type' => 'text',
                            'default' => '100',
                            'validation' => array(
                                'rule' => 'number',
                                'empty' => false
                            ),
                            'hint' => __('<p>In percent. Increase this number if you want to make your logo larger on the quote and vice versa.</p>', 'woo_quote'),
                        ),
                        'title_seller' => array(
                            'title' => __('Block title', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Seller', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'seller_name' => array(
                            'title' => __('Company name', 'woo_quote'),
                            'type' => 'text',
                            'default' => get_bloginfo(),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'seller_content' => array(
                            'title' => __('Company details', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => 'Tax ID: 123456789'. PHP_EOL . 'Demo Address #123'. PHP_EOL . 'London NW1 6XE'. PHP_EOL . 'United Kingdom',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Use this field to set up your company details, including address, company registration number, tax code etc.</p>', 'woo_quote'),
                        ),
                    ),
                ),
                'buyer_block' => array(
                    'title' => __('Buyer Block', 'woo_quote'),
                    'children' => array(
                        'title_buyer' => array(
                            'title' => __('Block title', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Buyer', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'buyer_content' => array(
                            'title' => __('Buyer details layout', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => '{{billing_address_1}}' . PHP_EOL . '{{billing_address_2}}' . PHP_EOL . '{{billing_postcode}} {{billing_city}} {{billing_state}}' . PHP_EOL . '{{billing_country}}',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => sprintf(__('<p>Use this field to set up the layout of the buyer details block.</p> <p>The following macros are available: %s</p> <p>You can use custom fields in the same way, e.g. {{my_custom_field_key}}.</p> <p>Do not include buyer first name, last name and company name - these fields are displayed automatically.</p>', 'woo_quote'), '<br />{{billing_address_1}}<br />{{billing_address_2}}<br />{{billing_postcode}}<br />{{billing_city}}<br />{{billing_state}}<br />{{billing_country}}<br />{{billing_email}}<br />{{billing_phone}}'),
                        ),
                        'buyer_remove_empty_lines' => array(
                            'title' => __('Remove lines with empty values', 'woo_quote'),
                            'type' => 'checkbox',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'bool',
                                'empty' => false
                            ),
                            'hint' => __('<p>If enabled, all lines that contain macros with empty values only will be removed.</p>', 'woo_quote'),
                        ),
                    ),
                ),
            ),
        ),
        'content_blocks' => array(
            'title' => __('Content Blocks', 'woo_quote'),
            'icon' => '<i class="fa fa-edit" style="font-size: 0.8em;"></i>',
            'children' => array(
                'block_footer' => array(
                    'title' => __('Page footer', 'woo_quote'),
                    'children' => array(
                        'footer' => array(
                            'title' => __('Content', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => 'Thank you for your interest!',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                    ),
                ),
                'block_1' => array(
                    'title' => __('Custom Block #1', 'woo_quote'),
                    'children' => array(
                        'block_1_title' => array(
                            'title' => __('Title', 'woo_quote'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_1_content' => array(
                            'title' => __('Content', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                    ),
                ),
                'block_2' => array(
                    'title' => __('Custom Block #2', 'woo_quote'),
                    'children' => array(
                        'block_2_title' => array(
                            'title' => __('Title', 'woo_quote'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_2_content' => array(
                            'title' => __('Content', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                    ),
                ),
                'block_3' => array(
                    'title' => __('Custom Block #3', 'woo_quote'),
                    'children' => array(
                        'block_3_title' => array(
                            'title' => __('Title', 'woo_quote'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_3_content' => array(
                            'title' => __('Content', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                    ),
                ),
                'block_4' => array(
                    'title' => __('Custom Block #4', 'woo_quote'),
                    'children' => array(
                        'block_4_title' => array(
                            'title' => __('Title', 'woo_quote'),
                            'type' => 'text',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                        'block_4_content' => array(
                            'title' => __('Content', 'woo_quote'),
                            'type' => 'textarea',
                            'default' => '',
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'translation' => array(
            'title' => __('Localization', 'woo_quote'),
            'icon' => '<i class="fa fa-font" style="font-size: 0.8em;"></i>',
            'children' => array(
                'date_time' => array(
                    'title' => __('Date & Time', 'woo_quote'),
                    'children' => array(
                        'date_format' => array(
                            'title' => __('Date format', 'woo_quote'),
                            'type' => 'dropdown',
                            'default' => 0,
                            'validation' => array(
                                'rule' => 'option',
                                'empty' => false
                            ),
                            'values' => array(
                                '0' => __('mm/dd/yy', 'woo_quote'),
                                '1' => __('mm/dd/yyyy', 'woo_quote'),
                                '2' => __('dd/mm/yy', 'woo_quote'),
                                '3' => __('dd/mm/yyyy', 'woo_quote'),
                                '4' => __('yy-mm-dd', 'woo_quote'),
                                '5' => __('yyyy-mm-dd', 'woo_quote'),
                                '6' => __('Month dd, yyyy', 'woo_quote'),
                                '7' => __('dd.mm.yyyy', 'woo_quote'),
                                '8' => __('dd-mm-yyyy', 'woo_quote'),
                            ),
                        ),
                    ),
                ),
                'translation' => array(
                    'title' => __('Field Labels', 'woo_quote'),
                    'children' => array(
                        'document_name' => array(
                            'title' => __('Quote', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Quote', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_date' => array(
                            'title' => __('Quote Date', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Quote Date', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_amount' => array(
                            'title' => __('Quote Amount', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Quote Amount', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_product' => array(
                            'title' => __('Product', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Product', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_price' => array(
                            'title' => __('Price', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Price', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_quantity' => array(
                            'title' => __('Quantity', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Qty.', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_net' => array(
                            'title' => __('Net', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Net', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line net amount column when different tax rates are used for different items.</p>', 'woo_quote'),
                        ),
                        'title_tax_percent' => array(
                            'title' => __('Tax %', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Tax %', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line tax rate column when different tax rates are used for different items.</p>', 'woo_quote'),
                        ),
                        'title_tax' => array(
                            'title' => __('Tax', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Tax', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                            'hint' => __('<p>Used as a column name for a line tax total column when different tax rates are used for different items.</p>', 'woo_quote'),
                        ),
                        'title_line_total' => array(
                            'title' => __('Line Total', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Line Total', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_subtotal' => array(
                            'title' => __('Subtotal', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Subtotal', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_cart_discount' => array(
                            'title' => __('Cart Discount', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Cart Discount', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_total' => array(
                            'title' => __('Total', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Total', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_total_excl_tax' => array(
                            'title' => __('Total Excluding Tax', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Total Excl. Tax', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_category' => array(
                            'title' => __('Category', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Category', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_description' => array(
                            'title' => __('Description', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Description', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_page' => array(
                            'title' => __('Page', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Page', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_additional_page' => array(
                            'title' => __('(additional page)', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('(additional page)', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_not_specified' => array(
                            'title' => __('Not Specified', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('Not Specified', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => false
                            ),
                        ),
                        'title_filename_prefix' => array(
                            'title' => __('quote_', 'woo_quote'),
                            'type' => 'text',
                            'default' => __('quote_', 'woo_quote'),
                            'validation' => array(
                                'rule' => 'string',
                                'empty' => true
                            ),
                            'hint' => __('<p>Value of this field is used as a file name prefix for quotes (e.g. quote_123.pdf).</p>', 'woo_quote'),
                        ),
                    ),
                ),
            ),
        ),
    );
}

?>
