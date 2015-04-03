<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

// Add text at below of plugin row on Plugin Manager page
add_action('after_plugin_row_'.WC_EMAIL_INQUIRY_NAME, array('WC_Email_Inquiry_Upgrade', 'plugin_row_alert_new_version') );

add_action('install_plugins_pre_plugin-information', array('WC_Email_Inquiry_Upgrade', 'display_changelog'));

add_filter("pre_set_site_transient_update_plugins", array('WC_Email_Inquiry_Upgrade', 'check_update'));

add_filter('plugins_api_result', array('WC_Email_Inquiry_Upgrade', 'make_compatibility'), 11, 3);

add_filter( 'http_request_args', array('WC_Email_Inquiry_Upgrade', 'disable_ssl_verify'), 100, 2 );

// Defined this plugin as external so that WordPress don't call to the WordPress.org Plugin Install API
add_filter( 'plugins_api', array('WC_Email_Inquiry_Upgrade', 'is_external'), 11, 3 );
					
class WC_Email_Inquiry_Upgrade
{
	
	//Displays message on Plugin's page
    public static function plugin_row_alert_new_version($plugin_name){

        $new_version = self::get_version_info();
		
		if(is_array($new_version) && $plugin_name == WC_EMAIL_INQUIRY_NAME){
          	if($new_version['is_valid_key'] != 'valid' && !wc_orders_quotes_check_pin() ){ 
				echo '</tr>';
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">';
					echo '<a href="admin.php?page=email-cart-options" title="'.__('Enter Your Plugin Authorization Key', 'wc_email_inquiry').'"><img src="'.WC_EMAIL_INQUIRY_IMAGES_URL.'/key.png" style="vertical-align: -3px;" /> '.__('Authorization Key', 'wc_email_inquiry').'</a> '.__('or', 'wc_email_inquiry').' <a href="http://www.a3rev.com" target="_blank">'.__('Purchase one now', 'wc_email_inquiry').'</a>';
				echo '</div></td>';
			}
        }
    }
	
	public static function get_version_info($cache=true){
		//Getting version number
		$respone_api = get_transient("a3rev_wc_email_inquiry_update_info");
		if(!$cache)
            $respone_api = null;
		
		if(!$respone_api){
				$options = array(
					'method' 	=> 'POST', 
					'timeout' 	=> 45, 
					'body' 		=> array(
									'plugin' 		=> get_option('a3rev_wc_email_inquiry_plugin'),
									'key'			=> get_option('a3rev_auth_wc_email_inquiry'),
									'domain_name'	=> $_SERVER['SERVER_NAME'],
									'address_ip'	=> $_SERVER['SERVER_ADDR'],
									'v'				=> get_option('a3rev_wc_email_inquiry_version'),
									'owner'			=> base64_encode(get_bloginfo('admin_email'))
								) 
				);
			
				$raw_response = wp_remote_request(WC_EMAIL_INQUIRY_MANAGER_URL. "/version.php", $options);
				if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']){
					$respone_api = $raw_response['body'];
				} else {
					$respone_api = 'cannot_connect_api';
				}
				
			//caching responses.
			set_transient("a3rev_wc_email_inquiry_update_info", $respone_api, 86400); //caching for 24 hours
		}
		
		$version_info = explode('||', $respone_api);
		if(is_array($version_info)){
			if ($version_info[1] == 'unvalid') {
				delete_option ( 'a3rev_pin_wc_email_inquiry' );
				delete_option ( 'a3rev_auth_wc_email_inquiry' );	
			}
			$info = array("is_valid_key" => $version_info[1], "version" => $version_info[0], "url" => self::get_url_download(), "upgrade_notice" => $version_info[2]);
			return $info;
		}else{
			return '';
		}
    }
	
	public static function check_update($update_plugins_option){
        $new_version = self::get_version_info();
        if (!is_array($new_version))
            return $update_plugins_option;

        $plugin_name = WC_EMAIL_INQUIRY_NAME;
        if(empty($update_plugins_option->response[$plugin_name]))
            $update_plugins_option->response[$plugin_name] = new stdClass();

        //Empty response means that the key is invalid. Do not queue for upgrade
        if($new_version['is_valid_key'] != 'valid' || version_compare(get_option('a3rev_wc_email_inquiry_version'), $new_version['version'], '>=')){
            unset($update_plugins_option->response[$plugin_name]);
        }else{
            $update_plugins_option->response[$plugin_name]->url = "http://www.a3rev.com";
            $update_plugins_option->response[$plugin_name]->slug = get_option('a3rev_wc_email_inquiry_plugin');
            $update_plugins_option->response[$plugin_name]->package = $new_version["url"];
            $update_plugins_option->response[$plugin_name]->new_version = $new_version['version'];
			$update_plugins_option->response[$plugin_name]->upgrade_notice = $new_version['upgrade_notice'];
            $update_plugins_option->response[$plugin_name]->id = "0";
        }

        return $update_plugins_option;

    }
	
	//Displays current version details on Plugin's page
   	public static function display_changelog(){
        if($_REQUEST["plugin"] != get_option('a3rev_wc_email_inquiry_plugin'))
            return;

        $page_text = self::get_changelog();
        echo $page_text;

        exit;
    }

    public static function get_changelog(){
		$options = array(
			'method' 	=> 'POST', 
			'timeout' 	=> 45, 
			'body' 		=> array(
							'plugin' 		=> get_option('a3rev_wc_email_inquiry_plugin'),
							'key'			=> get_option('a3rev_auth_wc_email_inquiry'),
							'domain_name'	=> $_SERVER['SERVER_NAME'],
							'address_ip'	=> $_SERVER['SERVER_ADDR'],
						) 
				);

        $raw_response = wp_remote_request(WC_EMAIL_INQUIRY_MANAGER_URL . "/changelog.php", $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = __('Error: ', 'wc_email_inquiry').' '.$raw_response->get_error_message();
        }else{
            $page_text = $raw_response['body'];
        }
        return stripslashes($page_text);
    }
	
	public static function get_url_download(){
        $download_url = WC_EMAIL_INQUIRY_MANAGER_URL . "/download.php?plugin=".get_option('a3rev_wc_email_inquiry_plugin')."&key=".get_option('a3rev_auth_wc_email_inquiry')."&domain_name=".$_SERVER['SERVER_NAME']."&address_ip=" . $_SERVER['SERVER_ADDR']."&v=".get_option('a3rev_wc_email_inquiry_version')."&owner=".base64_encode(get_bloginfo('admin_email'));

        return $download_url;
	}
	
	public static function make_compatibility( $info, $action, $args ) {
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$our_plugin_name = get_option('a3rev_wc_email_inquiry_plugin');
		if ( $action == 'plugin_information' ) {
			if ( version_compare( $wp_version, '3.7', '<=' ) ) {
				if ( is_object( $args ) && isset( $args->slug ) && $args->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			} elseif ( version_compare( $wp_version, '3.7', '>' ) && is_array( $args ) && isset( $args['body']['request'] ) ) {
				$request = unserialize( $args['body']['request'] );
				if ( $request->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			}
		}
		return $info;
	}
	
	public static function disable_ssl_verify($args, $url) {
		if ( stristr($url, WC_EMAIL_INQUIRY_MANAGER_URL) !== false ) {
			$args['timeout'] = 45;
			$args['sslverify'] = false; 
		}
		
		return $args;
	}

	public static function is_external( $external, $action, $args ) {
		if ( 'plugin_information' == $action ) {
			if ( is_object( $args ) && isset( $args->slug ) &&  get_option('a3rev_wc_email_inquiry_plugin') == $args->slug ) {
				$external = array(
					'tested'  => '4.1.1'
				);
				$external = (object) $external;
			}
		}
		return $external;
	}
}
?>
