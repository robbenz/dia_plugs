<?php
/**
 * WC Email Inquiry Functions
 *
 * Table Of Contents
 *
 * check_hide_add_cart_button()
 * check_hide_price()
 * check_add_email_inquiry_button()
 * check_add_email_inquiry_button_on_shoppage()
 * reset_products_to_global_settings()
 * email_inquiry()
 * get_from_address()
 * get_from_name()
 * get_content_type()
 * plugin_extension()
 * wc_ei_yellow_message_dontshow()
 * wc_ei_yellow_message_dismiss()
 */
class WC_Email_Inquiry_Functions 
{	
	
	public static function check_hide_add_cart_button ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt'])) $wc_email_inquiry_hide_addcartbt = $wc_email_inquiry_rules_roles_settings['hide_addcartbt'] ;
		else $wc_email_inquiry_hide_addcartbt = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt']);
		
		// dont hide add to cart button if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt == 'no' && !is_user_logged_in() ) return false;
		
		// hide add to cart button if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt != 'no' &&  !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt_after_login'])) $wc_email_inquiry_hide_addcartbt_after_login = $wc_email_inquiry_rules_roles_settings['hide_addcartbt_after_login'] ;
		else $wc_email_inquiry_hide_addcartbt_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt_after_login']);		

		// don't hide add to cart if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_addcartbt_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_hide_cart'])) {
			$role_apply_hide_cart = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
		} else {
			$role_apply_hide_cart = (array) $wc_email_inquiry_settings_custom['role_apply_hide_cart'];
		}
		
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_hide_cart );
			
			// hide add to cart button if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		return false;
		
	}
	
	public static function check_hide_price ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'])) $wc_email_inquiry_hide_price = $wc_email_inquiry_rules_roles_settings['hide_price'];
		else $wc_email_inquiry_hide_price = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price']);
		
		// dont hide price if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_price == 'no' && !is_user_logged_in() ) return false;
		
		// alway hide price if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_price != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price_after_login'])) $wc_email_inquiry_hide_price_after_login = $wc_email_inquiry_rules_roles_settings['hide_price_after_login'] ;
		else $wc_email_inquiry_hide_price_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price_after_login']);		

		// don't hide price if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_price_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_hide_price'])) {
			$role_apply_hide_price = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'];
		} else {
			$role_apply_hide_price = (array) $wc_email_inquiry_settings_custom['role_apply_hide_price'];
		}
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_hide_price );
			
			// hide price if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
	}
	
	public static function check_add_email_inquiry_button ($product_id) {
		global $wc_email_inquiry_global_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button'])) $wc_email_inquiry_show_button = $wc_email_inquiry_global_settings['show_button'];
		else $wc_email_inquiry_show_button = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button']);
		
		// dont show email inquiry button if setting is not checked and not logged in users
		if ($wc_email_inquiry_show_button == 'no' && !is_user_logged_in() ) return false;
		
		// alway show email inquiry button if setting is checked and not logged in users
		if ($wc_email_inquiry_show_button != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button_after_login'])) $wc_email_inquiry_show_button_after_login = $wc_email_inquiry_global_settings['show_button_after_login'] ;
		else $wc_email_inquiry_show_button_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button_after_login']);		

		// don't show email inquiry button if for logged in users is deacticated
		if ( $wc_email_inquiry_show_button_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'])) $role_apply_show_inquiry_button = (array) $wc_email_inquiry_global_settings['role_apply_show_inquiry_button'];
		else $role_apply_show_inquiry_button = (array) $wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'];
		
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_show_inquiry_button );
			
			// show email inquiry button if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
		
	}
	
	public static function check_add_email_inquiry_button_on_shoppage ($product_id=0) {
		global $wc_email_inquiry_global_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_single_only'])) $wc_email_inquiry_single_only = $wc_email_inquiry_global_settings['inquiry_single_only'];
		else $wc_email_inquiry_single_only = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_single_only']);
		
		if ($wc_email_inquiry_single_only == 'yes') return false;
		
		return WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id);
		
	}
	
	public static function reset_products_to_global_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_email_inquiry_settings_custom' " );
	}
	
	public static function email_inquiry($product_id, $your_name, $your_email, $your_phone, $your_message, $send_copy_yourself = 1) {
		global $wc_email_inquiry_contact_form_settings;
		$wc_email_inquiry_contact_success = stripslashes( get_option( 'wc_email_inquiry_contact_success', '' ) );
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
		
		if ( WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id) ) {
			
			if ( trim( $wc_email_inquiry_contact_success ) != '') $wc_email_inquiry_contact_success = wpautop(wptexturize(   $wc_email_inquiry_contact_success ));
			else $wc_email_inquiry_contact_success = __("Thanks for your inquiry - we'll be in touch with you as soon as possible!", 'wc_email_inquiry');
		
			if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to']) || trim(esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to'])) == '') $to_email = $wc_email_inquiry_contact_form_settings['inquiry_email_to'];
			else $to_email = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to']);
			if (trim($to_email) == '') $to_email = get_option('admin_email');
			
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
				$from_email = get_option('admin_email');
			else
				$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
				
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
				$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
			else
				$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
			if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc']) || trim(esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc'])) == '') $cc_emails = $wc_email_inquiry_contact_form_settings['inquiry_email_cc'];
			else $cc_emails = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc']);
			if (trim($cc_emails) == '') $cc_emails = '';
			
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset='. get_option('blog_charset');
			$headers[] = 'From: '.$from_name.' <'.$from_email.'>';
			$headers_yourself = $headers;
			
			if (trim($cc_emails) != '') {
				$cc_emails_a = explode("," , $cc_emails);
				if (is_array($cc_emails_a) && count($cc_emails_a) > 0) {
					foreach ($cc_emails_a as $cc_email) {
						$headers[] = 'Cc: '.$cc_email;
					}
				} else {
					$headers[] = 'Cc: '.$cc_emails;
				}
			}
			
			$product_name = get_the_title($product_id);
			$product_url = get_permalink($product_id);
			$subject = wc_ei_ict_t__( 'Default Form - Email Subject', __('Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			$subject_yourself = wc_ei_ict_t__( 'Default Form - Copy Email Subject', __('[Copy]: Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			
			$content = '
	<table width="99%" cellspacing="0" cellpadding="1" border="0" bgcolor="#eaeaea"><tbody>
	  <tr>
		<td>
		  <table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="#ffffff"><tbody>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Name', __('Name', 'wc_email_inquiry') ).'</strong></font> 
			  </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_name]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Email', __('Email', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="mailto:[your_email]">[your_email]</a></font> 
			  </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Phone', __('Phone', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_phone]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Product Name', __('Product Name', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="[product_url]">[product_name]</a></font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Message', __('Message', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_message]</font> 
		  </td></tr></tbody></table></td></tr></tbody></table>';
		  
			$content = str_replace('[your_name]', $your_name, $content);
			$content = str_replace('[your_email]', $your_email, $content);
			$content = str_replace('[your_phone]', $your_phone, $content);
			$content = str_replace('[product_name]', $product_name, $content);
			$content = str_replace('[product_url]', $product_url, $content);
			$your_message = str_replace( '://', ':&#173;­//', $your_message );
			$your_message = str_replace( '.com', '&#173;.com', $your_message );
			$your_message = str_replace( '.net', '&#173;.net', $your_message );
			$your_message = str_replace( '.info', '&#173;.info', $your_message );
			$your_message = str_replace( '.org', '&#173;.org', $your_message );
			$your_message = str_replace( '.au', '&#173;.au', $your_message );
			$content = str_replace('[your_message]', wpautop( $your_message ), $content);
			
			$content = apply_filters('wc_email_inquiry_inquiry_content', $content);
			
			// Filters for the email
			add_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			wp_mail( $to_email, $subject, $content, $headers, '' );
			
			if ($send_copy_yourself == 1) {
				wp_mail( $your_email, $subject_yourself, $content, $headers_yourself, '' );
			}
			
			// Unhook filters
			remove_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			return $wc_email_inquiry_contact_success;
		} else {
			return wc_ei_ict_t__( 'Default Form - Contact Not Allow', __("Sorry, this product don't enable email inquiry.", 'wc_email_inquiry') );
		}
	}
	
	public static function get_from_address() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
			$from_email = get_option('admin_email');
		else
			$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
			
		return $from_email;
	}
	
	public static function get_from_name() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
			$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
		else
			$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
		return $from_name;
	}
	
	public static function get_content_type() {
		return 'text/html';
	}
	
	
	public static function get_product_information( $product_id, $show_product_name = 0, $width = 220, $height = 180, $class_image = '' ) {
		$image_src = WC_Email_Inquiry_Functions::get_post_thumbnail( $product_id, $width, $height, $class_image );
		if ( trim($image_src) == '' ) {
			$image_src = '<img alt="" src="'. ( ( version_compare( WC_VERSION, '2.1', '<' ) ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() ) .'" class="'.$class_image.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
		}
		
		$product_information = '';
		ob_start();
	?>
    	<?php if ($show_product_name == 1) { ?>
        <div style="clear:both; margin-top:10px"></div>
		<div style="float:left; margin-right:10px;" class="wc_email_inquiry_default_image_container"><?php echo $image_src; ?></div>
        <div style="display:block; margin-bottom:10px; padding-left:22%;" class="wc_email_inquiry_product_heading_container">
        	<h1 class="wc_email_inquiry_custom_form_product_heading"><?php echo esc_html( get_the_title($product_id) ); ?></h1>
			<div class="wc_email_inquiry_custom_form_product_url_div"><a class="wc_email_inquiry_custom_form_product_url" href="<?php echo esc_url( get_permalink($product_id) ); ?>" title=""><?php echo esc_url( get_permalink($product_id) ); ?></a></div>
        </div>
        <div style="clear:both;"></div>
        <?php } else { ?>
        <?php echo $image_src; ?>
        <?php } ?>
	<?php
		$product_information = ob_get_clean();
		
		return $product_information;
	}
	
	public static function get_post_thumbnail( $postid=0, $width=220, $height=180, $class='') {
		$mediumSRC = '';
		// Get the product ID if none was passed
		if ( empty( $postid ) )
			$postid = get_the_ID();

		// Load the product
		$product = get_post( $postid );

		if (has_post_thumbnail($postid)) {
			$thumbid = get_post_thumbnail_id($postid);
			$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
			$mediumSRC = $attachmentArray[0];
			if (trim($mediumSRC != '')) {
				return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
			}
		}
		if (trim($mediumSRC == '')) {
			$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
			$attachments = get_posts($args);
			if ($attachments) {
				foreach ( $attachments as $attachment ) {
					$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
					break;
				}
			}
		}

		if (trim($mediumSRC == '')) {
			// Get ID of parent product if one exists
			if ( !empty( $product->post_parent ) )
				$postid = $product->post_parent;

			if (has_post_thumbnail($postid)) {
				$thumbid = get_post_thumbnail_id($postid);
				$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
				$mediumSRC = $attachmentArray[0];
				if (trim($mediumSRC != '')) {
					return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
				}
			}
			if (trim($mediumSRC == '')) {
				$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
				$attachments = get_posts($args);
				if ($attachments) {
					foreach ( $attachments as $attachment ) {
						$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
						break;
					}
				}
			}
		}
		return $mediumSRC;
	}
	
	public static function plugin_extension_start() {
		global $wc_ei_admin_init;
		
		$wc_ei_admin_init->plugin_extension_start();
	}
	
	public static function plugin_extension_end() {
		global $wc_ei_admin_init;
		
		$wc_ei_admin_init->plugin_extension_end();
	}
	
	public static function plugin_extension() {
		$html = '';
		$html .= '<a href="http://a3rev.com/shop/" target="_blank" style="float:right;margin-top:5px; margin-left:10px;" ><div class="a3-plugin-ui-icon a3-plugin-ui-a3-rev-logo"></div></a>';
		$html .= '<h3>'.__('Thanks for purchasing a WooCommerce Email Inquiry and cart Options Pro License.', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("All of the plugins features have been activated and are ready for you to use.", 'wc_email_inquiry').'</p>';
		$html .= '<h3>'.__('Documentation', 'wc_email_inquiry').':</h3>';
		$html .= '<p>'.__('View the', 'wc_email_inquiry').' <a href="http://docs.a3rev.com/user-guides/plugins-extensions/woocommerce/woo-email-inquiry-cart-options/" target="_blank">'.__('plugins docs', 'wc_email_inquiry').'</a> ' . __('for help with plugin set up.', 'wc_email_inquiry') . '</p>';
		$html .= '<h3>'.__('Support', 'wc_email_inquiry').':</h3>';
		$html .= '<p>'.__('Post all support requests to the', 'wc_email_inquiry').' <a href="https://a3rev.com/forums/forum/woocommerce-plugins/email-inquiry-cart-options/" target="_blank">'.__('plugins support forum', 'wc_email_inquiry').'</a>.</p>';
		$html .= '<h3>'.__('Whats this Yellow Section about?', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__('There are 2 additional upgrades to this plugin. All the functions inside the Yellow border are the extra functionality added by these to upgrades.', 'wc_email_inquiry').'</p>';
		$html .= '<h3>* <a href="http://a3rev.com/shop/woocommerce-email-inquiry-ultimate/" target="_blank">'.__('WooCommerce Email Inquiry Ultimate', 'wc_email_inquiry').'</a> '.__('Features', 'wc_email_inquiry').':</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>1. '.__("Includes all Email Inquiry and Cart Option Pro features.", 'wc_email_inquiry').'</li>';
		$html .= '<li>2. '.__('Full integration with Gravity Forms, Conatct Form 7.', 'wc_email_inquiry').'</li>';
		$html .= '<li>3. '.__("Custom Inquiry forms with Gravity Forms shortcode.", 'wc_email_inquiry').'</li>';
		$html .= '<li>4. '.__('Custom Inquiry forms using Contact Form 7 shortcode.', 'wc_email_inquiry').'</li>';
		$html .= '<li>5. '.__('Inquiry form opens On Page below button.', 'wc_email_inquiry').'</li>';
		$html .= '<li>6. '.__('Open Email Inquiry form on new page option.', 'wc_email_inquiry').'</li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<h3>* <a href="http://a3rev.com/shop/woocommerce-quotes-and-orders/" target="_blank">'.__('WooCommerce Quotes and Orders', 'wc_email_inquiry').'</a> '.__('Features', 'wc_email_inquiry').':</h3>';
		$html .= '<p>';
		$html .= '<ul style="padding-left:10px;">';
		$html .= '<li>1. '.__("Includes all features listed above.", 'wc_email_inquiry').'</li>';
		$html .= '<li>2. '.__('Extends WooCommerce add to cart mode to 3 new modes.', 'wc_email_inquiry').'</li>';
		$html .= '<li>3. '.__("Converts add to cart function into an add to Quote function.", 'wc_email_inquiry').'</li>';
		$html .= '<li>4. '.__("Manual' Quote Mode - quote prices off-line after request.", 'wc_email_inquiry').'</li>';
		$html .= '<li>5. '.__('Auto Quote Mode - Auto sends full quote to user.', 'wc_email_inquiry').'</li>';
		$html .= '<li>6. '.__('Converts add to cart function into add to Order function.', 'wc_email_inquiry').'</li>';
		$html .= '<li>7. '.__('Full integration with WooCommerce.', 'wc_email_inquiry').'</li>';
		$html .= '</ul>';
		$html .= '</p>';
		
		$html .= '<h3>'.__('Special Offer', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("If you let us know that you have upgraded to either of the annual subscription license within 60 days of purchasing this plugin we will send you a 100% refund for the WooCommerce Email Inquiry and Cart options License you have purchased.", 'wc_email_inquiry').'</p>';
		
		$html .= '<h3>'.__('If you do upgrade ...', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("Please note if you upgrade and are installing it on this site you must deactivate this plugin before you activate the upgrade plugin.", 'wc_email_inquiry').'</p>';
		
		return $html;
	}
	
	public static function wc_ei_yellow_message_dontshow() {
		check_ajax_referer( 'wc_ei_yellow_message_dontshow', 'security' );
		$option_name   = $_REQUEST['option_name'];
		update_option( $option_name, 1 );
		die();
	}
	
	public static function wc_ei_yellow_message_dismiss() {
		check_ajax_referer( 'wc_ei_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { @session_start(); } 
		$_SESSION[$session_name] = 1 ;
		die();
	}
}

?>
