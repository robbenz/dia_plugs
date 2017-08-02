=== YITH WooCommerce Request a Quote ===

Contributors: yithemes
Tags: request a quote, quote, yithemes, message, woocommerce, shop, ecommece, e-commerce
Requires at least: 3.5.1
Tested up to: 4.5.1
Stable tag: 1.4.8

The YITH Woocommerce Request A Quote plugin lets your customers ask for an estimate of a list of products they are interested into.

== Changelog ==
= 1.4.8 - Released on May 05, 2016 =
Added: Option to force users to register when requesting a quote
Added: Javascript min files

= 1.4.7 - Released on May 04, 2016 =
Added: pt_BR translation
Added: Compatibility with WooCommerce Advanced Quantity
Fixed: Compatibility with YITH WooCommerce Product Add-Ons 1.0.8
Fixed: Compatibility with WooCommerce Product Add-ons 2.7.17
Fixed: Woocommerce Taxes in order created from a request
Fixed: Variation's thumbnails in the quote email and pdf

= 1.4.6 - Released on Apr 19, 2016 =
Added: Option to disable/enable orders
Added: External/Affiliate products
Fixed: Issue in the request a quote email
Fixed: Variation details in the order

= 1.4.5 - Released on Apr 12, 2016 =
Fixed: Contact form 7 issue after the latest update
Fixed: The add to quote of grouped products

= 1.4.4 - Released on Apr 11, 2016 =
Added: An option to hide or show the details of the quote after send the request of quote
Added: A button "Return to shop" when the list is empty
Added: A button "Return to shop" at the bottom of the list
Added: Css classes inside the message when the list is empty
Added: Compatibility with YITH WooCommerce Advanced Product Options
Added: Compatibility with WooCommerce Composite Products
Added: Options to customize the text message to show after request a quote sending
Added: Options hide "Accept" button in the Quote
Added: Options to change "Accept" button Label
Added: Option to choose the page linked by Accept Quote Button. The default value is the page Checkout, change the page to disable the checkout process
Added: Options hide "Reject" button in the Quote
Added: Options to change "Reject" Button Label
Added: A new order status Accepted used when the process to checkout is disabled
Added: For default form you can choose now if each additional field is required or not
Added: Option to hide the total column from the list
Updated: Template email quote-table.php and request-quote-table.php removed double border to the table
Updated: Plugin Core Framework
Tweak: Contact form 7 hidden when the list is empty
Tweak: Shipping methods and shipping prices are now set in the checkout
Tweak: Compatibility with YITH Woocommerce Email Templates Premium
Fixed: Download PDF now is showed after that the order is completed
Fixed: Additional Field on Contact form 7 now are added into the quote email and in the Quote page details
Removed: File inlcudes/hooks.php all content now is in  YITH_YWRAQ_Frontend Class constructor

= 1.4.3 - Released on Mar 14, 2016 =
Added: compatibility with YITH WooCommerce Minimum Maximum Quantity
Added: compatibility with YITH WooCommerce Customize My Account Page
Added: Attribute 'show_form' on shortcode 'yith_ywraq_request_quote' can be 'yes'|'no'

= 1.4.2 - Released on Mar 07, 2016 =
Fixed: Ajax Calls for WooCommerce previous to 2.4.0
Fixed: Notice in compatibility with Multi Vendor Premium
Updated: Plugin Core Framework

= 1.4.1 - Released on Mar 04, 2016 =
Fixed: Request a quote order settings saving fields
Fixed: Enable CC Options in Request a quote email settings

= 1.4.0 - Released on Mar 02, 2016 =
Added: YITH WooCommerce Multi Vendor Premium 1.9.5 compatibility
Added: Filter 'ywraq_clear_list_after_send_quote' to clear/not the list in request quote page
Added: More details in the Quote Order Metabox
Updated: button loading time for variations products
Fixed: Loading of metabox in specific pages
Fixed: Calculation totals for enables taxes

= 1.3.5 - Released on Jan 19, 2016 =
Added: WooCommerce 2.5 compatibility
Fixed: Send quote issue

= 1.3.4 - Released on Jan 18, 2016 =
Added: Two more text field in default form
Added: WooCommerce 2.5 RC 3 compatibility
Fixed: compatibility with WooCommerce Product Addons
Updated: Plugin Core Framework

= 1.3.3 - Released on Dec 30, 2015 =
Fixed: Update plugin error

= 1.3.2 - Released on Dec 30, 2015 =
Added: WooCommerce 2.5 beta 3 compatibility
Fixed: Endpoints for View Detail page
Fixed: Email recipients settings to send quote

= 1.3.1 - Released on Dec 15, 2015 =
Fixed: Issue on Number of Request Quote Details after sent the request
Fixed: Issues on Contact Form 7 list in settings

= 1.3.0 - Released on Dec 10, 2015 =
Added: Wordpress 4.4 compatibility
Added: Optional Attachment in the email of quote
Added: Fee and shipping cost to the email and pdf document of quote
Added: Two text field to show before and after the product table in the quote email and pdf
Added: Admin notice if WooCommerce Coupons are disabled
Added: Product Grouped can be added into the request
Added: A tab in the settings of the plugin to manage pdf options
Added: An option to show "Download PDF" in my account page
Added: Option to add a footer in the pdf document
Added: An option to show Accept/Reject Quote in pdf document
Added: An option to show the button only for out of stock products
Added: Autosave increase/decrease quantity in the request quote page
Added: The possibility to increase price of products on the quote
Added: The possibility to choose the rule of users to show the request a quote button
Added: Compatibility with WooCommerce Min/Max Quantities
Added: Compatibility with WooCommerce Subscriptions
Updated: Changed Text Domain from 'ywraq' to 'yith-woocommerce-request-a-quote'
Updated: Plugin Core Framework
Fixed: Email settings on request quote

= 1.2.3 - Released on Oct 02, 2015 =
Added: Select products to exclude by category

= 1.2.2 - Released on Sep 30, 2015 =
Fixed: Product quantity when button Request a Quote is clicked
Added: Woocommerce Addons details in Request Quote Email
Added: Compatibily with YITH Essential Kit for WooCommerce #1

= 1.2.1 - Released on Sep 21, 2015 =
Fix: Show button for Guests
Updated: Plugin Core Framework

= 1.2.0 - Released on Sep 11, 2015 =
Fix: Quote send options
Fix: Contact form 7 send email
Added: WooCommerce Subscriptions

= 1.1.9 - Released on Aug 11, 2015 =
Added: WooCommerce 2.4.1 compatibility
Updated: Changed the spinner file position, it is added to the plugin assets/images
Fixed: Email Send Quote changed order id with order number in Accepted/Reject link

= 1.1.8 - Released on Jul 27, 2015 =
Added: 'ywraq_quantity_max_value' for max quantity in the request a quote list
Added: Compatibility with WooCommerce Product Add-ons
Added: Compatibility with YITH WooCommerce Email Templates Premium
Added: Option to choose the link to quote request details to show in "Request a Quote" email
Added: Option to choose if after click the button "Request a Quote" go to the list page
Added: Options to choose Email "From" Name and Email "From" Address in Woocommerce > Settings > Emails
Fixed: Refresh the page after that contact form 7 sent email
Fixed: Default Request a Quote form
Fixed: Line breaks in request message
Fixed: Minor bugs

= 1.1.7 - Released on Jul 03, 2015 =
Fixed: Sending double email for quote
Fixed: Reverse exclusion list in single product

= 1.1.6 - Released on Jun 29, 2015 =
Added: Option to show the product sku on request list and quote
Added: Option to show the product image on request list and quote
Added: Reverse exclusion list
Added: Send an email to Administrator when a Quote is Accepted/Rejected
Fixed: Contact form 7 send email
Fixed: Hide price in variation products

= 1.1.5 - Released on Jun 10, 2015 =
Added: filter for 'add to quote' button label the name is 'ywraq_product_add_to_quote'
Fixed: PDF Options settings

= 1.1.4 - Released on Jun 04, 2015 =
Fixed: Show quantity if hide add to cart button
Fixed: Minor bugs in backend panel

= 1.1.3 - Released on May 28, 2015 =
Added: Additional text field in default form
Added: Additional upload field in default form
Fixed: Price of variation in email table
Fixed: Request Number in Contact form 7

= 1.1.2 - Released on May 21, 2015 =
Added: Compatibility with YITH Woocommerce Quick View
Fixed: Message of success for guest users
Fixed: Show quantity if hide add to cart button
Fixed: Layout option tab issue with YIT Framework

= 1.1.1 - Released on May 06, 2015 =
Added: Compatibility with YITH WooCommerce Catalog Mode
Fixed: When hide "add to cart" button, the variation will not removed

= 1.1.0 - Released on Apr 21, 2015 =
Added: Wrapper div to 'yith_ywraq_request_quote' shortcode
Updated: Plugin Core Framework
Fixed: add_query_arg() and remove_query_arg() usage
Fixed: Minor bugs

= 1.0.2 - Released on Apr 21, 2015 =
Added: Attach PDF quote to the email
Updated: Compatibility with YITH Infinite Scrolling
Updated: Plugin Core Framework
Fixed: Template to overwrite

= 1.0.1 - Released: Mar 31, 2015 =
Updated: Plugin Core Framework

= 1.0.0 =
Initial release