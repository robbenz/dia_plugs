<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

// Add text at below of plugin row on Plugin Manager page
add_action('after_plugin_row_'.WOOPS_NAME, array('WC_Predictive_Search_Upgrade', 'plugin_row_alert_new_version') );

add_action('install_plugins_pre_plugin-information', array('WC_Predictive_Search_Upgrade', 'display_changelog'));

add_filter("pre_set_site_transient_update_plugins", array('WC_Predictive_Search_Upgrade', 'check_update'));

add_filter('plugins_api_result', array('WC_Predictive_Search_Upgrade', 'make_compatibility'), 11, 3);

add_filter( 'http_request_args', array('WC_Predictive_Search_Upgrade', 'disable_ssl_verify'), 100, 2 );

// Defined this plugin as external so that WordPress don't call to the WordPress.org Plugin Install API
add_filter( 'plugins_api', array('WC_Predictive_Search_Upgrade', 'is_external'), 11, 3 );

class WC_Predictive_Search_Upgrade
{

	//Displays message on Plugin's page
    public static function plugin_row_alert_new_version($plugin_name){
    	if ( function_exists( 'responsi_premium_pack_check_pin' ) && responsi_premium_pack_check_pin() ) return;

        $new_version = self::get_version_info();

        if(is_array($new_version) && $plugin_name == WOOPS_NAME){
          	if($new_version['is_valid_key'] != 'valid' && !woo_predictive_search_check_pin() ){ 
				echo '</tr>';
				echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">';
					echo '<a href="admin.php?page=woo-predictive-search" title="'.__('Enter Your Plugin Authorization Key', 'woops').'"><img src="'.WOOPS_IMAGES_URL.'/key.png" style="vertical-align: -3px;" /> '.__('Authorization Key', 'woops').'</a> '.__('or', 'woops').' <a href="http://www.a3rev.com" target="_blank">'.__('Purchase one now', 'woops').'</a>';
				echo '</div></td>';
			}
        }
    }

	public static function get_version_info($cache=true){
		global $wc_predictive_search_admin_init;

		//Getting version number
		$respone_api = get_transient( $wc_predictive_search_admin_init->version_transient );

		if ( ! $cache ) {
            $respone_api = false;
		}

        // Fixed for work compatibility WP 4.3 when transient_timeout is deleted
        if ( false !== $respone_api ) {
			$transient_timeout = '_transient_timeout_' . $wc_predictive_search_admin_init->version_transient;
			$timeout = get_option( $transient_timeout, false );
			if ( false === $timeout ) {
				$respone_api = false;
			}
		}

		if ( ! $respone_api ) {

				// set caching first before call to server to solve server timeout issue and make cron run again everytime
				set_transient( $wc_predictive_search_admin_init->version_transient, 'cannot_connect_api', 86400); //caching for 24 hours

				$options = array(
					'method' 	=> 'POST',
					'timeout' 	=> 8,
					'body' 		=> array(
									'plugin' 		=> get_option('wc_predictive_search_plugin'),
									'key'			=> get_option('a3rev_auth_woo_predictive_search'),
									'domain_name'	=> $_SERVER['SERVER_NAME'],
									'address_ip'	=> $_SERVER['SERVER_ADDR'],
									'v'				=> get_option('wc_predictive_search_version'),
									'owner'			=> base64_encode(get_bloginfo('admin_email'))
								)
				);
				
				$raw_response = wp_remote_request(WOO_PREDICTIVE_SEARCH_MANAGER_URL. "/version.php", $options);
				if ( !is_wp_error( $raw_response ) && 200 == $raw_response['response']['code']){
					$respone_api = $raw_response['body'];
				} else {
					$respone_api = 'cannot_connect_api';
				}

			//caching responses.
            set_transient( $wc_predictive_search_admin_init->version_transient, $respone_api, 86400); //caching for 24 hours

            $version_info = explode('||', $respone_api);
			if ( FALSE !== stristr( $respone_api, '||' ) && is_array( $version_info ) ) {
				if ( isset( $version_info[1] ) && $version_info[1] == 'unvalid' ) {

					// if called is failed then check number of failures, just allow 10 times failures
					$number_failures = get_option( 'woo_predictive_search_number_failures', 0 );
					if ( $number_failures >= 10 ) {
						delete_option ( 'a3rev_auth_woo_predictive_search' );
						delete_option ( 'a3rev_pin_woo_predictive_search' );
					} else {
						$number_failures = (int) $number_failures + 1;
						update_option( 'woo_predictive_search_number_failures', $number_failures );
						set_transient( $wc_predictive_search_admin_init->version_transient, $respone_api, 7200); //change caching for 2 hours
					}
				} else {
					delete_option( 'woo_predictive_search_number_failures' );
				}
			}

            // save check new version to log
            /*
            $current_datetime = gmdate( 'Y-m-d H:i:s' );
            $called_log = dirname(__FILE__) . '/check_version_log.txt';
			$log_content = "\n". $current_datetime.' : Checking version - Key: '.get_option('a3rev_auth_woo_predictive_search'). ' - Pin: '. get_option('a3rev_pin_woo_predictive_search'). ' - Domain: '.$_SERVER['SERVER_NAME']. ' - IP: '. $_SERVER['SERVER_ADDR']. ' - Plugin: '.get_option('wc_predictive_search_plugin'). ' - Status: '.$respone_api ;
			// write new log time
			if ( function_exists('fopen') ) {
				$fh = @fopen($called_log, 'a+');
				@fwrite($fh, $log_content);
				@fclose($fh); 
			} else {
				@file_put_contents($called_log, $log_content, FILE_APPEND | LOCK_EX);
			}
			*/
		}

		$version_info = explode('||', $respone_api);
		if ( FALSE !== stristr( $respone_api, '||' ) && is_array( $version_info ) ) {
			$info = array("is_valid_key" => $version_info[1], "version" => $version_info[0], "url" => self::get_url_download(), "upgrade_notice" => $version_info[2]);
			return $info;
		} else {
			return '';
		}
    }
	
	public static function check_update($update_plugins_option){
        global $responsi_premium_addons;

        $new_version = false;
		if ( function_exists( 'responsi_premium_pack_check_pin' ) && responsi_premium_pack_check_pin() && $responsi_premium_addons && method_exists( $responsi_premium_addons, 'get_plugin_data' ) ) {
			$new_version = $responsi_premium_addons->get_plugin_data( get_option('wc_predictive_search_plugin'), 'woocommerce' );
		}

		if ( ! $new_version || ( is_array( $new_version ) && isset( $new_version['is_valid_key'] ) && $new_version['is_valid_key'] != 'valid' ) ) {
			$new_version = self::get_version_info();
		}

        if (!is_array($new_version))
            return $update_plugins_option;

        $plugin_name = WOOPS_NAME;
        if(empty($update_plugins_option->response[$plugin_name]))
            $update_plugins_option->response[$plugin_name] = new stdClass();

        //Empty response means that the key is invalid. Do not queue for upgrade
        if($new_version['is_valid_key'] != 'valid' || version_compare(get_option('wc_predictive_search_version'), $new_version['version'], '>=')){
            unset($update_plugins_option->response[$plugin_name]);
        }else{
            $update_plugins_option->response[$plugin_name]->url = "http://www.a3rev.com";
            $update_plugins_option->response[$plugin_name]->slug = get_option('wc_predictive_search_plugin');
            $update_plugins_option->response[$plugin_name]->package = $new_version["url"];
            $update_plugins_option->response[$plugin_name]->new_version = $new_version['version'];
			$update_plugins_option->response[$plugin_name]->upgrade_notice = $new_version['upgrade_notice'];
            $update_plugins_option->response[$plugin_name]->id = "0";
        }

        return $update_plugins_option;

    }
	
	//Displays current version details on Plugin's page
   	public static function display_changelog(){
        if($_REQUEST["plugin"] != get_option('wc_predictive_search_plugin'))
            return;

        $page_text = self::get_changelog();
        echo $page_text;

        exit;
    }

    public static function get_changelog(){
		$options = array(
			'method' 	=> 'POST', 
			'timeout' 	=> 8, 
			'body' 		=> array(
							'plugin' 		=> get_option('wc_predictive_search_plugin'),
							'key'			=> get_option('a3rev_auth_woo_predictive_search'),
							'domain_name'	=> $_SERVER['SERVER_NAME'],
							'address_ip'	=> $_SERVER['SERVER_ADDR'],
						) 
				);
        
		$raw_response = wp_remote_request(WOO_PREDICTIVE_SEARCH_MANAGER_URL . "/changelog.php", $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = __('Error: ', 'woops').' '.$raw_response->get_error_message();
        }else{
            $page_text = $raw_response['body'];
        }
        return stripslashes($page_text);
    }
	
	public static function get_url_download(){
        $download_url = WOO_PREDICTIVE_SEARCH_MANAGER_URL . "/download.php?plugin=".get_option('wc_predictive_search_plugin')."&key=".get_option('a3rev_auth_woo_predictive_search')."&domain_name=".$_SERVER['SERVER_NAME']."&address_ip=" . $_SERVER['SERVER_ADDR']."&v=".get_option('wc_predictive_search_version')."&owner=".base64_encode(get_bloginfo('admin_email'));

        return $download_url;
	}
	
	public static function make_compatibility( $info, $action, $args ) {
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$our_plugin_name = get_option('wc_predictive_search_plugin');
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
		if ( stristr($url, WOO_PREDICTIVE_SEARCH_MANAGER_URL . "/download.php" ) !== false ) {
			$args['timeout'] = 60;
			$args['sslverify'] = false;
		} elseif ( stristr($url, WOO_PREDICTIVE_SEARCH_MANAGER_URL) !== false ) {
			$args['timeout'] = 8;
			$args['sslverify'] = false; 
		}
		
		return $args;
	}

	public static function is_external( $external, $action, $args ) {
		if ( 'plugin_information' == $action ) {
			if ( is_object( $args ) && isset( $args->slug ) &&  get_option('wc_predictive_search_plugin') == $args->slug ) {
				global $wp_version;
				$external = array(
					'tested'  => $wp_version
				);
				$external = (object) $external;
			}
		}
		return $external;
	}
}

/*
add_action('init', 'a3rev_store_domain_hourly');

function a3rev_store_domain_hourly() {
	$domain_data = get_transient("a3rev_store_domain_5_mins");
		
	if(!$domain_data)
    	$domain_data = null;

    if(!$domain_data){

    	$domain_data = 'Domain: '.$_SERVER['SERVER_NAME']. ' - IP: '. $_SERVER['SERVER_ADDR'];
	    set_transient("a3rev_store_domain_5_mins", $domain_data, 300); //caching for 1 hour

	    // save check new version to log
	    $current_datetime = gmdate( 'Y-m-d H:i:s' );
	    $called_log = dirname(__FILE__) . '/check_server_info_log.txt';
		$log_content = "\n". $current_datetime.' : Domain: '.$_SERVER['SERVER_NAME']. ' - IP: '. $_SERVER['SERVER_ADDR'] ;
		// write new log time
		if ( function_exists('fopen') ) {
			$fh = @fopen($called_log, 'a+');
			@fwrite($fh, $log_content);
			@fclose($fh); 
		} else {
			@file_put_contents($called_log, $log_content, FILE_APPEND | LOCK_EX);
		}

	}
}
*/
?>
