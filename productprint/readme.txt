=== WooCommerce ProductPrint ===
Contributors: kenrichman
Tags: productprint, product print, print product, product, ecommerce, e-commerce, commerce, woo commerce, woocommerce, woo-commerce, wordpress ecommerce, store, sales, sell, shop, shopping, cart, configurable, reports, print, printing, printer, print button, printer friendly, print friendly, printer-friendly, printout
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WLA6U8VXF4WUW
Plugin URI: http://productprintpro.com/productprint
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 1.2.1
Requires at least: 3.5
Tested up to: 4.3
Stable tag: 1.2.1

WooCommerce extension to create printer-friendly product pages. Adds a Print button to the single product page.

== Description ==

This plugin for WooCommerce adds a print button to every product page, giving website visitors the ability to print off the product information, in a format designed for print. You can configure the button to appear in one of a selection of positions on the product description page for any product. A variety of options are available via the WordPress dashboard settings to control the appearance of the printed page. For example, you can change the size and position of the featured image, and you can choose from a selection of fonts. Requires WooCommerce.

= Automatic installation =

To do an automatic install of ProductPrint, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

1. In the search field type “ProductPrint” and click Search Plugins. Once you’ve found the plugin, you can install it by simply clicking “Install Now”.
2. Click 'Activate plugin'.
3. Go to Settings -> ProductPrint
4. Click on the Settings tab and configure your options.

= Manual installation =

1. The manual installation method involves downloading our ProductPrint plugin for WooCommerce and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).
2. Click 'Activate plugin'.
3. Go to Settings -> ProductPrint
4. Click on the Settings tab and configure your options.

== Frequently Asked Questions ==


== Screenshots ==

1. The print button added to the single product page.
2. The dashboard settings page.
3. Sample print-out.

== Changelog ==

= Version 1.2.1 =

Bugfix: File gallery-images.php somehow got missed out of v1.2 - now included. This means gallery images will now display if selected to do so.

= Version 1.2 =

Improvement: Added 'nofollow' to the print button link to discourage indexing by search engines and prevent potential 404 errors.

Improvement: Added new printer font size setting to avoid having to amend this by css, should you feel the need to do so.

Improvement: Changed the way the gallery images are extracted from database to more closely follow how WooCommerce does it, and created separate php file for this code.

Tested compatible with WordPress 4.3 and updated readme.txt accordingly

Improvement: 

= Version 1.1 =

Bugfix: added test for presence of variant images to prevent warnings about missing variant images appearing in print-out, if variants don't have variant images.

Name changed in readme.txt from ProductPrint to WooCommerce ProductPrint

Added .pot file for translation

Tested compatible with WordPress 4.1 and updated readme.txt accordingly

= Version 1.0 =

Initial release.
