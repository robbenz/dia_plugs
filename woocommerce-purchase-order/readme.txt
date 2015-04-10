=== Plugin Name ===
Contributors: bheadrick
Tags: WooCommerce, Invoice, Payment Gateway, Payment
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is a a WooCommerce Payment Gateway that allows select customers to complete  a transaction by paying via invoice.

== Description ==

This is Payment Gateway that allows select customers to complete a transaction by 
paying via purchase order. 
Most of the settings are in the WooCommerce Payment Gateway section, although
this also adds a checkbox to customer user profiles so that you can enable Purchase Order
availability only to select customers if you wish (or you can select to enable for all users)

Once you are ready for the customer to pay, change the order status from "On Hold" 
to "Pending" and save the order, making any other changes to the order, such as 
shipping charges and order total, and click "Email Invoice". The customer will 
receive an email with a "Pay" link, where they will have the opportunity to pay 
for the order.

This is a customized version that disables all other payment methods when the 
specified shipping method is selected.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

== Changelog ==

= 1.0 =
* Initial Release

= 1.1 =
* Added option to enable for all users

= 1.1.5 = 
* Added shipping method exclusivity

= 1.1.6 = 
* Made shipping method exclusivity multiselect


