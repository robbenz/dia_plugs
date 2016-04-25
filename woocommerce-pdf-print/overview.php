<?php

	global $wpdb;
	$wpp_options = array(	'wpp_onoff_saving_manager',
							'wpp_onoff_saving_doc',
							'wpp_onoff_saving_pdf',
							'wpp_saving_position', 
							'wpp_saving_align',
							'wpp_saving_location_postend',
							'wpp_saving_location_custom',
							'wpp_saving_type',
							'wpp_onoff_save_follow',
							'wpp_save_text_align',
							'wpp_onoff_print_manager', 
							'wpp_print_location_postend', 
							'wpp_print_location_custom',
							'wpp_print_type',
							'wpp_print_app',
							'wpp_global_jquery_load',
							
							'wpp_ph_sku',
							'wpp_ph_in_stock',
							'wpp_ph_rating',
							'wpp_ph_prod_cats',
							'wpp_ph_prod_tags',
							'wpp_ph_prod_page',
							'wpp_ph_prod_vars',
							'wpp_ph_prod_summ',
							'wpp_ph_prod_desc',
							'wpp_ph_prod_attr',
							'wpp_ph_prod_gall',
							'wpp_ph_print_post',
							'wpp_ph_dimensions',
							'wpp_ph_weight',
							'wpp_ph_price',
							'wpp_ph_prod_date',
							'wpp_ph_prod_mod_date',
							'wpp_ph_real_price',
							'wpp_ph_curr_sale_price',
							'wpp_ph_exp_date',
							'wpp_ph_links',
							'wpp_ph_page_exported_from',
							
							//componets 1.4.0
							'wpp_T_logo',
							'wpp_T_featured_image',
							'wpp_T_price_html',
							'wpp_T_sku',
							'wpp_T_stock',
							'wpp_T_rating',
							'wpp_T_categories',
							'wpp_T_tags',
							'wpp_T_permalink',
							'wpp_T_variants',
							'wpp_T_summary',
							'wpp_T_description',
							'wpp_T_attribute',
							'wpp_T_custom_tab',
							'wpp_T_gallery'
							);
	
	$components = array(    'wpp_T_featured_image' => 'Product Image',
							'wpp_T_price_html' => 'Price',
							'wpp_T_sku' => 'SKU',
							'wpp_T_stock' => 'Stock',
							'wpp_T_rating' => 'Rating',
							'wpp_T_categories' => 'Categories',
							'wpp_T_tags' => 'Tags',
							'wpp_T_permalink' => 'Product Page URL',
							'wpp_T_variants' => 'Product Variants',
							'wpp_T_summary' => 'Product Summary',
							'wpp_T_description' => 'Product Description',
							'wpp_T_attribute' => 'Product Attribute',
							'wpp_T_custom_tab' => 'Custom Tabs',
							'wpp_T_gallery' => 'Product Gallery',
							);
	
	if( isset($_POST[ 'wpp_hidden' ]) && $_POST[ 'wpp_hidden' ] == 'x' ) {
		foreach( $wpp_options as $wpp ) {
			( $_POST[ $wpp ] == '' && strpos($wpp, 'wpp_ph_') === FALSE ) ? $wpp_op = 0 : $wpp_op = $_POST[ $wpp ];
			update_option( $wpp, $wpp_op );
		}
		?> <div class="updated"><p><strong><?php _e( 'Options saved.' ); ?></strong></p></div> <?php
	}
	
	foreach( $wpp_options as $wpp ) {
		if( get_option($wpp) ) {
			$wpp_checked['checkbox'][ $wpp ][ 'checked' ] = 'checked="checked"';
			$wpp_checked['radio'][ $wpp ][ 'on_check' ] = 'checked="checked"';
			$wpp_checked['radio'][ $wpp ][ 'off_check' ] = '';
		} 
		else {
			$wpp_checked['checkbox'][ $wpp ][ 'checked' ] = '';
			$wpp_checked['radio'][ $wpp ][ 'on_check' ] = '';
			$wpp_checked['radio'][ $wpp ][ 'off_check' ] = 'checked="checked"';
		}
	}


?>
<br />
<style type="text/css">
.wpp_option_table {
background-color:#CCCCCC;
}
.wpp_option_th {
background-color:#F9F9F9;
text-align:left;
font-weight:100;
padding:2px;
width:60%;
}
.wpp_option_td {
background-color:#F9F9F9;
text-align:left;
font-weight:100;
padding:2px;
width:40%;
}
.wpp_option_top_th {
background-color:#F0F0F0;
text-align:left;
font-weight:bold;
padding:2px;
width:60%;
}
.wpp_option_top_td {
background-color:#F0F0F0;
text-align:left;
font-weight:bold;
padding:2px;
width:40%;
}
ul li{
padding-left:0px;
font-size:13px;
}
.wpp_yes{
	list-style-image:url(<?php echo WPP_PATH ?>img/1.gif);
}
.wpp_no{
	list-style-image:url(<?php echo WPP_PATH ?>img/0.gif);
}
</style>
    <link rel="stylesheet" href="<?php echo WPP_PATH ?>bxslider/jquery.bxslider.css" type="text/css" />
    <script src="<?php echo WPP_PATH ?>bxslider/jquery.min.js"></script>
    <script src="<?php echo WPP_PATH ?>bxslider/jquery.bxslider.js"></script>
 		<table width="100%" border="0">
          <tr>
            <td style="padding:10px; padding-left:0px; vertical-align:top; width:500px;">
                    <div class="slider">
                        <ul class="bxslider">
                          <li><a href="https://wordpress.org/plugins/woocommerce-category-slider/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/4.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/woodiscuz-woocommerce-comments/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/3.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/wpdiscuz/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/5.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/advanced-content-pagination/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/1.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/author-and-post-statistic-widgets/"><img src="<?php echo WPP_PATH ?>img/gc/2.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                        </ul>
                    </div>
                    <div style="clear:both"></div>
            </td>
            <td valign="top" style="padding:10px; padding-right:0px;">
            
            <table width="100%" border="0" cellspacing="1" class="widefat">
                <thead>
                <tr>
                <th>&nbsp;Information</th>
                </tr>
                </thead>
                <tbody>
                    <tr valign="top">
                        <td style="background:#FFF; text-align:left; font-size:12px;">
                        	If you'd like to have the similar buttons on simple Posts and Pages please use our <a href="https://wordpress.org/plugins/universal-post-manager/"><strong>Universal Post Manager</strong></a> plugin. This plugin allows your visitors to <span style="color:#FF6600">Print</span> post/page content and download as <span style="color:#FF6600">PDF, Doc, TXT, HTML, XML</span> files.
                        </td>
                    </tr>
                </tbody>
              </table>
			  <br />
              <table width="100%" cellspacing="1" border="0" class="widefat">
                <thead>
                    <tr>
                        <th style="font-size:16px; background-color:#FEFCE7;"><strong>Like WooCommerce PDF & Print?</strong> <br><span style="font-size:15px">We really need your reviews!</span></th>
                    </tr>
                </thead>
                <tbody><tr valign="top">
                    <td style="background:#FFF; text-align:left; font-size:13px;">
                        We do our best to make WooCommerce PDF & Print better and better. Hundreds of users are currently satisfied with WooCommerce PDF & Print but only about 1% of them give us 5 start rating.
                        Please help us keep plugin rating high, encouraging us to develop and maintain this plugin. Take a one minute to leave <a title="Go to WooCommerce PDF & Print Reviews section on Wordpress.org" href="https://wordpress.org/support/view/plugin-reviews/woocommerce-pdf-print"><img border="0" align="absmiddle" src="<?php echo WPP_PATH ?>img/gc/5s.png"></a> star review on <a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-pdf-print">Wordpress.org</a>
                        <hr style="border-style:dotted;">
                        <div style="width:200px; float:right;">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="VNMGTCF9NRH5C">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                        </div>
                        We spend as much of my spare time as possible working on WooCommerce PDF & Print and any donation is appreciated. Donations play a crucial role in supporting Free and Open Source Software projects.            
                    </td>
                </tr>
            </tbody>
            </table>
                
            </td>
          </tr>
        </table>
        
<form name="form_options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="wpp_hidden" value="x">
		<br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
				<strong><?php _e( 'Turn On/Off Saving Manager' ) ?></strong>
				</th>
				<th style="padding-left:0px;">
				<input type="radio" id="wpp_onoff_saving_manager_1" name="wpp_onoff_saving_manager" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_manager' ]['on_check'] ?>/> 
				<label for="wpp_onoff_saving_manager_1"><?php _e( 'On' ) ?></label>&nbsp;
				<input type="radio" id="wpp_onoff_saving_manager_0" name="wpp_onoff_saving_manager" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_manager' ]['off_check'] ?> />
				<label for="wpp_onoff_saving_manager_0"><?php _e( 'Off' ) ?></label>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Turn on/off Saving as Word Document' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_saving_doc_1" name="wpp_onoff_saving_doc" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_doc' ][ 'on_check' ] ?>/> 
				<label for="wpp_onoff_saving_doc_1"><?php _e( 'On' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_saving_doc_0" name="wpp_onoff_saving_doc" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_doc' ][ 'off_check' ] ?> />
				<label for="wpp_onoff_saving_doc_0"><?php _e( 'Off' ) ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Turn on/off Saving as PDF' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_saving_pdf_1" name="wpp_onoff_saving_pdf" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_pdf' ][ 'on_check' ] ?>/> 
				<label for="wpp_onoff_saving_pdf_1"><?php _e( 'On' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_saving_pdf_0" name="wpp_onoff_saving_pdf" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_pdf' ][ 'off_check' ] ?> />
				<label for="wpp_onoff_saving_pdf_0"><?php _e( 'Off' ) ?></label>
				</td>
			</tr>
            <tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Allow search engines to index saving documents' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_save_follow_1" name="wpp_onoff_save_follow" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_save_follow' ]['on_check'] ?>/> 
				<label for="wpp_onoff_save_follow_1"><?php _e( 'Allow' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_save_follow_0" name="wpp_onoff_save_follow" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_save_follow' ]['off_check'] ?> />
				<label for="wpp_onoff_save_follow_0"><?php _e( 'Disable' ) ?></label>
				</td>
			</tr>
            <tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Alignment of saving and printing documents' ) ?>
				</th>
				<td class="wpp_option_td">
                <?php 
				$wpp_save_text_align = array( 'left'=>'', 'right'=>'', 'center'=>'', 'justify'=>'');
				switch( get_option( 'wpp_save_text_align' ) ){
					case 'left' : $wpp_save_text_align['left'] = 'selected="selected"' ;break;
					case 'right' : $wpp_save_text_align['right'] = 'selected="selected"' ;break;
					case 'center' : $wpp_save_text_align['center'] = 'selected="selected"' ;break;
					case 'justify' : $wpp_save_text_align['justify'] = 'selected="selected"' ;break;
				}
				?>
				<select name="wpp_save_text_align">
                    <option value="left" <?php echo $wpp_save_text_align['left'] ?>>left &nbsp;</option>
                    <option value="right" <?php echo $wpp_save_text_align['right'] ?>>right &nbsp;</option>
                    <option value="center" <?php echo $wpp_save_text_align['center'] ?>>center &nbsp;</option>
                    <option value="justify" <?php echo $wpp_save_text_align['justify'] ?>>justify &nbsp;</option>
                </select>
				</td>
			</tr>
			
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance type of saving buttons and strings' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_saving_type_0" name="wpp_saving_type" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_saving_type' ][ 'off_check' ] ?>/> 
				<label for="wpp_saving_type_0"><?php _e( 'String' ) ?></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" id="wpp_saving_type_1" name="wpp_saving_type" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_saving_type' ][ 'on_check' ] ?> /> 
				<label for="wpp_saving_type_1"><?php _e( 'Button' ) ?></label> 
				</td>
			</tr>
		</table>
		<br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
				<strong><?php _e( 'Turn On/Off Print Manager' ) ?></strong>
				</th>
				<th style="padding-left:0px;">
				<input type="radio" id="wpp_onoff_print_manager_1" name="wpp_onoff_print_manager" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_print_manager' ]['on_check'] ?>/> 
				<label for="wpp_onoff_print_manager_1"><?php _e( 'On' ) ?></label>&nbsp;
				<input type="radio" id="wpp_onoff_print_manager_0" name="wpp_onoff_print_manager" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_print_manager' ]['off_check'] ?> />
				<label for="wpp_onoff_print_manager_0"><?php _e( 'Off' ) ?></label>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance mode of print button and string' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_print_app_0" name="wpp_print_app" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_app' ][ 'off_check' ] ?>/> 
				<label for="wpp_print_app_0"><?php _e( 'Single' ) ?></label>&nbsp;
				
				<input type="radio" id="wpp_print_app_1" name="wpp_print_app" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_app' ][ 'on_check' ] ?> /> 
				<label for="wpp_print_app_1"><?php _e( 'With saving buttons or strings' ) ?></label> <br />
				</td>
			</tr>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance type of print button and string' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_print_type_0" name="wpp_print_type" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_type' ][ 'off_check' ] ?>/> 
				<label for="wpp_print_type_0"><?php _e( 'String' ) ?></label> &nbsp;
				<input type="radio" id="wpp_print_type_1" name="wpp_print_type" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_type' ][ 'on_check' ] ?> /> 
				<label for="wpp_print_type_1"><?php _e( 'Button' ) ?></label> <br />
				</td>
			</tr>
		</table>
        
        
        <br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
					<strong><?php _e( 'Turn On/Off Components' ) ?></strong>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<td class="wpp_option_td">
                	<div style="width:100%; margin:5px; border-left:<?php echo ( get_option('wpp_T_logo') )? '#79C69D' : '#CCCCCC'; ?> 5px solid; padding-left:5px;">
                        <div style="float:left; padding:0px 5px; width:25%;"><label for="wpp_T_logo">Document Header Image URL ( Logo ) :</label></div>
                        <div style="float:right; padding:0px 5px; width:70%;"><input id="wpp_T_logo" type="text" name="wpp_T_logo" style="width:50%" value="<?php echo trim(get_option('wpp_T_logo')); ?>" /> </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div style="clear:both;"></div>
                    <center><hr width="95%" style="margin:10px 0px 5px 0px;" /></center>
					<?php foreach( $components as $name => $title ): ?>
                        <div style="float:left; width:30%; margin:5px; border-left:<?php echo ( $wpp_checked['checkbox'][ $name ][ 'checked' ])?'#79C69D' : '#CCCCCC'; ?> 5px solid; padding-left:5px;">
                            <div style="float:left; padding:0px 5px; width:60%;"><label for="<?php echo $name ?>"><?php echo $title ?></label></div>
                            <div style="float:right; padding:0px 5px; width:30%;"><input id="<?php echo $name ?>" type="checkbox" name="<?php echo $name ?>" value="1" <?php echo $wpp_checked['checkbox'][ $name ][ 'checked' ] ?> /> </div>
                            <div style="clear:both;"></div>
                        </div>
                    <?php endforeach; ?>
                    <div style="clear:both;"></div>
				</td>
			</tr>
		</table>
        
        
        <br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
				<strong><?php _e( 'Save and Print Document Phrases' ) ?></strong>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<td class="wpp_option_td">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><input placeholder="SKU" type="text" name="wpp_ph_sku" value="<?php  echo get_option( 'wpp_ph_sku' ) ?>" /></td>
                        <td><input placeholder="In stock"  type="text" name="wpp_ph_in_stock" value="<?php  echo get_option( 'wpp_ph_in_stock' ) ?>" /></td>
                        <td><input placeholder="Rating"  type="text" name="wpp_ph_rating" value="<?php  echo get_option( 'wpp_ph_rating' ) ?>" /></td>
                        <td><input placeholder="Product Categories"  type="text" name="wpp_ph_prod_cats" value="<?php  echo get_option( 'wpp_ph_prod_cats' ) ?>" /></td>
                      </tr>
                      <tr>
                        <td><input placeholder="Product Tags"  type="text" name="wpp_ph_prod_tags" value="<?php  echo get_option( 'wpp_ph_prod_tags' ) ?>" /></td>
                        <td><input placeholder="Product Page"  type="text" name="wpp_ph_prod_page" value="<?php  echo get_option( 'wpp_ph_prod_page' ) ?>" /></td>
                        <td><input placeholder="Product Variants"  type="text" name="wpp_ph_prod_vars" value="<?php  echo get_option( 'wpp_ph_prod_vars' ) ?>" /></td>
                        <td><input placeholder="Product Summary"  type="text" name="wpp_ph_prod_summ" value="<?php  echo get_option( 'wpp_ph_prod_summ' ) ?>" /></td>
                      </tr>
                      <tr>
                        <td><input placeholder="Product Description"  type="text" name="wpp_ph_prod_desc" value="<?php  echo get_option( 'wpp_ph_prod_desc' ) ?>" /></td>
                        <td><input placeholder="Product Attributes"  type="text" name="wpp_ph_prod_attr" value="<?php  echo get_option( 'wpp_ph_prod_attr' ) ?>" /></td>
                        <td><input placeholder="Product Gallery"  type="text" name="wpp_ph_prod_gall" value="<?php  echo get_option( 'wpp_ph_prod_gall' ) ?>" /></td>
                        <td><input placeholder="Print this Post"  type="text" name="wpp_ph_print_post" value="<?php  echo get_option( 'wpp_ph_print_post' ) ?>" /></td>
                      </tr>
                      <tr>
                        <td><input placeholder="Dimensions"  type="text" name="wpp_ph_dimensions" value="<?php  echo get_option( 'wpp_ph_dimensions' ) ?>" /></td>
                        <td><input placeholder="Weight"  type="text" name="wpp_ph_weight" value="<?php  echo get_option( 'wpp_ph_weight' ) ?>" /></td>
                        <td><input placeholder="Price"  type="text" name="wpp_ph_price" value="<?php  echo get_option( 'wpp_ph_price' ) ?>" /></td>
                        <td><input placeholder="Product added date"  type="text" name="wpp_ph_prod_date" value="<?php  echo get_option( 'wpp_ph_prod_date' ) ?>" /></td>
                      </tr>
                      <tr>
                        <td><input placeholder="Product modified date"  type="text" name="wpp_ph_prod_mod_date" value="<?php  echo get_option( 'wpp_ph_prod_mod_date' ) ?>" /></td>
                        <td><input placeholder="Real Price"  type="text" name="wpp_ph_real_price" value="<?php  echo get_option( 'wpp_ph_real_price' ) ?>" /></td>
                        <td><input placeholder="Current Sale Price"  type="text" name="wpp_ph_curr_sale_price" value="<?php  echo get_option( 'wpp_ph_curr_sale_price' ) ?>" /></td>
                        <td><input placeholder="Export date"  type="text" name="wpp_ph_exp_date" value="<?php  echo get_option( 'wpp_ph_exp_date' ) ?>" /></td>
                      </tr>
                      <tr>
                        <td><input placeholder="Links"  type="text" name="wpp_ph_links" value="<?php  echo get_option( 'wpp_ph_links' ) ?>" /></td>
                        <td><input placeholder="Product data were exported from"  type="text" name="wpp_ph_page_exported_from" value="<?php  echo get_option( 'wpp_ph_page_exported_from' ) ?>" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>

				</td>
			</tr>
		</table>
			<p class="submit" align="right">
			<input type="submit" class="button button-primary" name="Submit" value="<?php _e( 'Update Options' ) ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</form>
		</div>
        <script>
$('.bxslider').bxSlider({
  mode: 'fade',
  captions: false,
  auto: true
});
</script>