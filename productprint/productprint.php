<?php
/**
 * Plugin Name: ProductPrint
 * Author URI: http://togethernet.ltd.uk
 * Plugin URI: http://productprintpro.com/productprint
 * Description: WooCommerce extension to create printer-friendly product pages.
 * Version: 1.2.1
 * Author: Togethernet
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Tags: WooCommerce, print, product, cart, commerce, configurable, e-commerce, ecommerce, print button, print friendly, printer, printer friendly, printing, printout, reports, sales, sell, shop, shopping, store, woo commerce, wordpress ecommerce
 * Requires at least: 3.5
 * Tested up to: 4.3
 * 
 * Text Domain productprint
 */


  // Prevent direct file access
  if( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
  }


defined('SB_DS') or define('SB_DS', DIRECTORY_SEPARATOR); //this checks for the separator on the hosting environment. So "/" for linux "\" for windows

define('SC_PRODUCTPRINT_PLUGIN_DIR', dirname(__FILE__)); //the directory to the plugin on the web server

define('SC_PRODUCTPRINT_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename(SC_PRODUCTPRINT_PLUGIN_DIR)); //the url directory of the plugin on the browser




function ppp_init(){ /*** tell WordPress about the new set of options ***/
	register_setting( 'ppp_plugin_options', 'productprint_ops', 'ppp_validate_options' );
}
add_action('admin_init', 'ppp_init' );



 class SC_PRODUCTPRINT { //the main plugin class, main logic is here for admin page

 	public function __construct ()

	{

		//if this is not the admin page, or the user is not an admin, go to the redirect handler

		if(!is_admin()) add_action('template_redirect', array($this, 'action_template_redirect'));

		add_action('init', array($this,'productprint_localize') );  //makes sure that the localization is run at the beginning

		add_action('admin_menu', array($this, 'action_admin_menu')); //adds the menu on the admin page for the application

/***		add_action('admin_print_scripts', array($this, 'productprint_addjavascriptfiles')); ***/ //loads plugins scripts

		add_action('admin_print_styles', array($this,'productprint_addcssfiles')); //loads plugins css files

		//we are going to use a PHP switch function to determine the print select option set in the admin settings.
		
		/*** my new init stuff ***/
		$option_name = 'productprint_ops' ;

		if ( get_option( $option_name ) !== false ) {

    	// The option already exists, so we just take a copy.
			$options = get_option('productprint_ops'); //gets the variables stored in the plugin options

		} else {

    	// The option hasn't been added yet. We'll add it with $autoload set to 'no'.
    		$deprecated = null;
    		$autoload = 'no';
			//default setting for the plugin, normally used after first install as a default

			$options = array(
						'featured_image' => 1, 
						'gallery' => 1, 
						'product_description' => 1,
						'price'=> 1, 
						'product_attributes'=> 1, 
						'short_description' => 1, 
						'stock_level' => 1, 
						'reviews' => 1,
						'img_position' => 'right',
						'img_width' => '50%',
						'img_marginleft' => '20px',
						'img_marginright' => '0px',
						'img_margintop' => '0px',
						'img_marginbottom' => '20px',
						'show_border' => '1',
						'gallery_img_width' =>'15%',
						'gallery_border' => '1',
						'font_family' => 'Arial',
						'font_size' => '16px',
						'button_position' => '4', 
						'button_legend' => 'Print',
						'sku' => '',
						);
    		add_option( $option_name, $options, $deprecated, $autoload );
		}
		
		switch($options['button_position']) {

		case 1:
		add_action('woocommerce_before_single_product', array($this, 'productprint_button')); break;
		case 2:
		add_action('woocommerce_before_single_product_summary', array($this, 'productprint_button')); break;
		case 3:
		add_action('woocommerce_single_product_summary', array($this, 'productprint_button'), 7); break;
		case 4:
		add_action('woocommerce_single_product_summary', array($this, 'productprint_button'), 12); break;
		case 5:
		add_action('woocommerce_single_product_summary', array($this, 'productprint_button'), 25); break;
		case 6:
		add_action('woocommerce_single_product_summary', array($this, 'productprint_button'), 35); break;
		case 7:
		add_action('woocommerce_after_single_product', array($this, 'productprint_button')); break;

		}; // end Switch statement
 	}	



public function productprint_addcssfiles() {

	//function to add the css files to the plugin
	wp_enqueue_style('thickbox');
}

	

public function productprint_localize() {

	// Localization
	load_plugin_textdomain('productprint', false, dirname(plugin_basename(__FILE__)). "/languages" );
} 

		

public function action_admin_menu() {

	//this function adds the page to WordPress and calls the function that is responsible of displaying the plugin settings
	add_options_page(__('ProductPrint' , 'productprint'), __('ProductPrint' , 'productprint'), 'manage_options', 'productprint-settings', array($this, 'productprint_settings'));
}

	

public function productprint_settings() {

	//default setting for the plugin, normally used after first install as a default
	$def = array(		'featured_image' => '1', 
						'gallery' => '1', 
						'product_description' => '1', 
						'price' => '1', 
						'product_attributes' => '1', 
						'short_description' => '1', 
						'stock_level' => '1', 
						'reviews' => '1',
						'img_position' => 'right',
						'img_width' => '50%',
						'img_marginleft' => '20px',
						'img_marginright' => '0px',
						'img_margintop' => '0px',
						'img_marginbottom' => '20px',
						'show_border' => '0',
						'gallery_img_width' =>'15%',
						'gallery_border' => '0',
						'font_family' => 'Arial',
						'font_size' => '16px',
						'button_position' => '4', 
						'button_legend' => 'Print',
						'sku' => '',
						);

	//defines the KEYS to available image positions.
	$img_positions = array( 'left', 'none', 'right' );

	//the font for the text page, 
	$fonts = array('Arial', 'Calibri', 'Courier', 'Garamond', 'Georgia', 'Helvetica', 'Minion', 'Monospace', 'Palatino', 'Sans-serif', 'Serif', 'Times', 'Times New Roman', 'Verdana');

		?>


		<div class="wrap">
	
			<h1><img style="margin-right:15px;"src="<?php print (SC_PRODUCTPRINT_PLUGIN_URL . '/assets/icon.png') ?>"><?php _e('Your ProductPrint Settings', 'productprint'); ?></h1>
			
			<hr />
			
			<?php $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings'; ?>

			<h2 class="nav-tab-wrapper">
				
				<a href="?page=productprint-settings&tab=settings" class="nav-tab"<?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
				<a href="?page=productprint-settings&tab=upgrade" class="nav-tab"<?php echo $active_tab == 'upgrade' ? 'nav-tab-active' : ''; ?>"><?php _e ('Upgrade to ProductPrint Pro', 'productprint'); ?></a>
				
			</h2>
			<?php
		if( $active_tab == 'upgrade' ) { /****************** UPGRADE TAB **********************************************************/

			?>

<strong></strong>ProductPrint Pro turns your WooCommerce store's product information into print friendly sales literature - handy for your customers and so easy for you!</strong><BR>
<ul>
	<li><strong>Choose what you want printed</strong> - image galleries, reviews, variations, prices... it's up to you.</li>
	<li><strong>Irrelevant information won't be printed</strong>, such as sidebars - just the product information you select.</li>
	<li><strong>Add your own headers and footers - including custom images</strong>. It's a great way to brand your products and add your contact details - but why stop there? Add your terms of business, returns policy, or how about a requisition order, purchase order form, a guarantee or even a discount coupon?</li>
</ul>

<hr />

<h2>Produce flyers and PDFs like this one, with ease:</h2>
<a href="http://productprintpro.com/wp-content/uploads/2014/04/Productprintpro-sample.jpg"><img class="size-medium wp-image-502" src="http://productprintpro.com/wp-content/uploads/2014/04/Productprintpro-sample-283x400.jpg" alt="Sample output from ProductPrint Pro" width="283" height="400" /></a>

<P<(NB: PDF output is generated by selecting 'PDF' by the browerr printer dialog box not by the plug-in.)</P>

Click for the pdf: <a href="http://productprintpro.com/wp-content/uploads/2014/04/productprintpro-sample.pdf">productprintpro-sample</a>

<hr />

&nbsp;
<h2>Check the <a href="http://productprintpro.com/product/from-so-simple-a-beginning/">demo store here</a>.</h2>

<hr />

&nbsp;
<h2>Get your copy</h2>
Click to <a href="http://www.togethernet.ltd.uk/productprintpro-woocommerce-print-product-extension/">purchase your copy of ProductPrint Pro</a>, complete with 12 months' support and updates license and the option to renew your license at a 50% discount.

<h3>Tailored headers and footers, many more options and access to developer support.</h3>
ProductPrint Pro is the big brother of ProductPrint. ProductPrint Pro turns your WooCommerce store's product information into print-friendly sales literature with tailored headers and footers, and gives you fine control over the elements that are printed. Your single product page is also improved, with the option to add your own printer icon to the print button - and easy adjustment of the space around the print button. Get it looking just perfect - without diving into css!

This chart shows the extra features you get by upgrading, and users also have access to the ProductPrint Pro support forum.
<h2>ProductPrint v <a title="About ProductPrint Pro" href="http://productprintpro.com/">ProductPrint Pro</a> Comparison Chart</h2>
<table style="height: 2241px;" width="628">
<tbody>
<tr>
<td width="135"><strong>FEATURE</strong></td>
<td style="text-align: center;" width="78"><strong>ProductPrint</strong></td>
<td style="text-align: center;" width="106"><a title="About ProductPrint Pro" href="http://productprintpro.com/"><strong>ProductPrint Pro</strong></a></td>
</tr>
<tr>
<td width="135">Print Button Position control</td>
<td style="text-align: center;" width="78">
<h2>•</h2>
7 positions</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
<p style="text-align: center;">7 positions plus fine adjustment of margins</p>
</td>
</tr>
<tr>
<td width="135">Print Button Design</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
<p style="text-align: center;">Standard button with customisable label text</p>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
<p style="text-align: center;">Customisable icon and/or customisable label text</p>
</td>
</tr>
<tr>
<td width="135">Featured Image Show/Hide</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Featured Image Width</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Featured Image Position</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Featured Image Margin control</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Featured Image Border</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Gallery Show/Hide</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Gallery Image Width</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Gallery Border</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Printer Font</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Printer Font size</td>
<td width="78">
<h2 style="text-align: center;">•</h2>
</td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr><td width="135">Header Image</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Header Text</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Footer Text</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Footer Image</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Product Short Description Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Product Description Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Price Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">SKU Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Attributes Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Stock Level Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Reviews Show/Hide</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
<tr>
<td width="135">Access to Developer Support</td>
<td width="78"></td>
<td width="106">
<h2 style="text-align: center;">•</h2>
</td>
</tr>
</tbody>
</table>
&nbsp;
<h1>Click to get <a title="Buy ProductPrint Pro for WooCommerce" href="http://www.togethernet.ltd.uk/productprintpro-woocommerce-print-product-extension/">ProductPrint Pro</a></h1>
Click this link to <a href="http://www.togethernet.ltd.uk/productprintpro-woocommerce-print-product-extension/"><strong>purchase your copy of ProductPrint Pro</strong></a>, complete with 12 months' support and updates license and the option to renew your license at a 50% discount.

<hr />

<h3>Hey, if you don't want to upgrade right now, would you consider making a donation to fund more development?</h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="hosted_button_id" type="hidden" value="8YPD6SEAW9G5L" />
<input alt="PayPal – The safer, easier way to pay online." name="submit" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" type="image" />
<img src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>I would also really appreciate a <a href="https://wordpress.org/support/view/plugin-reviews/productprint">review on wordpress.org</a> - thank you very much. That way you'll be helping the WordPress community as well.
			<?php
		} else { /************************************* MAIN OPTIONS TAB ***********************************************************/ ?>
				<P>Do you like this plug-in? Please help to support development by making a small donation.</P>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="8YPD6SEAW9G5L">
					<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
				</form>
				<P>Better still, why not upgrade to the Pro version?</P>
				<P>I would also really appreciate a <a href="https://wordpress.org/support/view/plugin-reviews/productprint">review on wordpress.org</a> - thank you very much.</P>
				<HR>
				<form action="options.php" method="post">

				<?php
				settings_fields('ppp_plugin_options');

				$options = get_option('productprint_ops');
				//gets the variables stored in the plugin options

				/*** $options should be set assuming user has visited the admin pages once ***/
				/* $options = array_merge($def, $options); */
				?>
				
				<h3><?php _e('Print button', 'productprint'); ?></h3>

				<table>
					<th>
					<tr>
					<td style="width: 250px;"></td>
					<td style="width: 250px;"></td>
					</tr>
					</th>
					<tr><td><label><?php _e('Position on the page', 'productprint'); ?></label></td><td>
						
						<select name="productprint_ops[button_position]" id="button_position">	
						
						<option value="1" <?php selected($options['button_position'], 1); ?>> <?php _e('Before product', 'productprint'); ?></option>
		
						<option value="2" <?php selected($options['button_position'], 2); ?>> <?php _e('Before product title', 'productprint'); ?></option>
		
						<option value="3" <?php selected($options['button_position'], 3); ?>> <?php _e('After product title', 'productprint'); ?></option>
		
						<option value="4" <?php selected($options['button_position'], 4); ?>> <?php _e('After product price', 'productprint'); ?></option>
		
						<option value="5" <?php selected($options['button_position'], 5); ?>> <?php _e('After short description', 'productprint'); ?></option>
		
						<option value="6" <?php selected($options['button_position'], 6); ?>> <?php _e('After add to cart button', 'productprint'); ?></option>
		
						<option value="7" <?php selected($options['button_position'], 7); ?>> <?php _e('After product', 'productprint'); ?></option>
		
						</select></td></tr>
								
					<tr><td><span><?php _e('Label for the button', 'productprint'); ?></span></td><td>
						
					<input id="button_legend" type="text" size="20" name="productprint_ops[button_legend]" value="<?php print sanitize_text_field($options['button_legend']) ?>" /></td></tr>
		
				</table>
		
				</p>
				<hr />

				<?php // gallery settings ?>
		
					<p>
						<h3><?php _e('Featured Image Settings', 'productprint'); ?></h3>
						<table>
						<th>
						<tr>
						<td style="width: 250px;"></td>
						<td style="width: 250px;"></td>
						</tr>
						</th>
						<tr><td><span><?php _e('Show featured image?', 'productprint'); ?></span></td><td><select name="productprint_ops[featured_image]"><option value='1'><?php _e('Yes', 'productprint'); ?></option><option value='0' 
							<?php if (isset($options['featured_image']) && $options['featured_image'] == 0)
							echo "selected='selected'"; ?>>
							<?php _e('No', 'productprint'); ?></option></select>
						</td></tr>
						<tr>
						<td>
						<label><?php _e('Position', 'productprint'); ?></label>
						</td>
						<td>
						<select name="productprint_ops[img_position]">
						<option value="">-- <?php _e('Position', 'productprint'); ?> --</option>
						<?php foreach($img_positions as $position): ?>
						<option value="<?php print $position; ?>" <?php print ($options['img_position'] == $position) ? 'selected="selected"' : ''; ?>>
						<?php print $position; ?>
						</option>
						<?php endforeach; ?>
						</select>
						</td>
						</tr>
		
				<?php // featured image width and margin settings ?>
		
					<span> <?php _e('Append either px or % to the width. The height will scale in proportion.', 'productprint'); ?> </span>
					<tr><td><span><?php _e('Width', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[img_width]" value="<?php print $options['img_width'] ?>" /></td></tr>
					<span> <?php _e('Append either px or % to the margin values.', 'productprint'); ?> </span>
					<tr><td><span><?php _e('Left margin', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[img_marginleft]" value="<?php print $options['img_marginleft'] ?>" /></td></tr>
		
					<tr><td><span><?php _e('Right margin', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[img_marginright]" value="<?php print $options['img_marginright'] ?>" /></td></tr>
		
					<tr><td><span><?php _e('Top margin', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[img_margintop]" value="<?php print $options['img_margintop'] ?>" /></td></tr>
		
					<tr><td><span><?php _e('Bottom margin', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[img_marginbottom]" value="<?php print $options['img_marginbottom'] ?>" /></td></tr>
		
					<tr><td><span><?php _e('Show border?', 'productprint'); ?></span></td><td><select name="productprint_ops[show_border]"><option value='1'><?php _e('Yes', 'productprint'); ?></option><option value='0' 
							<?php
						if (isset($options['show_border']) && $options['show_border'] == 0)
							echo "selected='selected'";
					?>>
							<?php _e('No', 'productprint'); ?></option></select></td></tr>
				</table>
		
				</p>
		
				<hr />
				
				<?php //Gallery Options for the plugin ?>
		
				<p>
		
				<h3><?php _e('Gallery Options', 'productprint'); ?></h3>
					<span> <?php _e('Append either px or % to the width. The height will scale in proportion.', 'productprint'); ?> </span>
					<table>
						<th>
						<tr>
						<td style="width: 250px;"></td>
						<td style="width: 250px;"></td>
						</tr>
						</th>
					<tr><td><span><?php _e('Show gallery?', 'productprint'); ?></span></td><td><select name="productprint_ops[gallery]"><option value='1'><?php _e('Yes', 'productprint'); ?></option><option value='0' 
							<?php if (isset($options['gallery']) && $options['gallery'] == 0)
							echo "selected='selected'"; ?>>
							<?php _e('No', 'productprint'); ?></option></select></td></tr>
								
					<tr><td><span><?php _e('Gallery Image Width', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[gallery_img_width]" value="<?php print $options['gallery_img_width'] ?>" /></td></tr>
		
					<tr><td><span><?php _e('Show borders?', 'productprint'); ?></span></td><td><select name="productprint_ops[gallery_border]"><option value='1'><?php _e('Yes', 'productprint'); ?></option><option value='0' 
							<?php
						if (isset($options['gallery_border']) && $options['gallery_border'] == 0)
							echo "selected='selected'";
					?>>
							<?php _e('No', 'productprint'); ?></option></select></td></tr>
					</table>
		
				</p>
		
				<hr />
		
				<?php //plugins font settings ?>
		
				<p>
		
				<h3><?php _e('Printer font', 'productprint'); ?></h3>
				
				
				
				<table>
					<th>
					<tr>
					<td style="width: 250px;"></td>
					<td style="width: 250px;"></td>
					</tr>
					</th>
					<tr><td><label><?php _e('Font Family', 'productprint'); ?></label></td><td>
						
					<select name="productprint_ops[font_family]">
		
						<option value="">-- <?php _e('font family', 'productprint'); ?> --</option>
		
						<?php foreach($fonts as $font): ?>
		
						<option value="<?php print $font; ?>" <?php print ($options['font_family'] == $font) ? 'selected="selected"' : ''; ?>>
		
							<?php print $font; ?>
		
						</option>
		
						<?php endforeach; ?>
		
					</select>
							
					</td></tr>
								
					<tr><td><span><?php _e('Font Size', 'productprint'); ?></span></td><td><input type="text" name="productprint_ops[font_size]" value="<?php print $options['font_size'] ?>" /></td></tr>
		
				</table>
		
				</p>
		
				<hr />

				<?php submit_button(); ?>
		
				</form>
			<?php } /* end of else section */ ?>
		</div> <?php

				} //that is the HTML code completed for the admin settings on the page

				public function productprint_button()
				{
				global $post; //gets the global class Post for wordpress so we can get the Page, Post or Products details

				$link = home_url('/index.php?task=productprint&pid='.$post->ID); //sets the URL for the post page

				$nonced_url = wp_nonce_url($link, $post->ID); /*** adds a nonce to the URL ***/

				$ops = get_option('productprint_ops');

				//this produces the print link on the products page
		?>

		<a href="<?php print $nonced_url; ?>"  id="print_button_id" target="_blank" rel="nofollow" class="button print-button"><?php print (sanitize_text_field($ops['button_legend'])) ?></a>

		<script type="text/javascript">
		jQuery('document').ready(function($) 
		{
    		if(jQuery("input[name='variation_id']"))
    		{
    		    jQuery("input[name='variation_id']" ).change(function() {
        			variationnn_id=jQuery("input[name='variation_id']" ).val();
        			//alert("variationn_id="+variationnn_id);
        			cur_href=document.getElementById("print_button_id").href; 
        			cur_href2=cur_href.split('&variation_id');
        			cur_href=cur_href2[0];
        			document.getElementById("print_button_id").href=cur_href+"&variation_id="+variationnn_id;
        			return false;
    		});
    		}
		});
		</script>
		<?php
	}

	public function action_template_redirect()
	{
	if( isset($_REQUEST['task']) && $_REQUEST['task'] == 'productprint' && isset($_REQUEST['pid']) && $_REQUEST['pid'] )
	{
	$retrieved_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($retrieved_nonce, $_REQUEST['pid'] ) )
	die( 'Failed security check' );

	require_once SC_PRODUCTPRINT_PLUGIN_DIR . SB_DS . 'tpl-print.php';
	die();
	}
	}
	}

	function ppp_validate_options($input) {
	// Sanitize textarea input (strip html tags, and escape characters)
	//$input['textarea_one'] = wp_filter_nohtml_kses($input['textarea_one']);
	//$input['textarea_two'] = wp_filter_nohtml_kses($input['textarea_two']);
	return $input;
	}

	$sc_productprint = new SC_PRODUCTPRINT(); //this simply calls the class meaning the construct method is run