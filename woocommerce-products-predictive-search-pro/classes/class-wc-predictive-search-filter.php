<?php
/**
 * WooCommerce Predictive Search Hook Filter
 *
 * Hook anf Filter into woocommerce plugin
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * a3_wp_admin()
 * yellow_message_dontshow()
 * yellow_message_dismiss()
 * plugin_extra_links()
 */
class WC_Predictive_Search_Hook_Filter
{

	public static function plugins_loaded() {
		global $woocommerce_search_page_id;

		$woocommerce_search_page_id = WC_Predictive_Search_Functions::get_page_id_from_shortcode( 'woocommerce_search', 'woocommerce_search_page_id');
	}

	public static function a3_wp_admin() {
		wp_enqueue_style( 'a3rev-wp-admin-style', WOOPS_CSS_URL . '/a3_wp_admin.css' );
	}

	public static function yellow_message_dontshow() {
		check_ajax_referer( 'wc_ps_yellow_message_dontshow', 'security' );
		$option_name   = $_REQUEST['option_name'];
		update_option( $option_name, 1 );
		die();
	}

	public static function yellow_message_dismiss() {
		check_ajax_referer( 'wc_ps_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { @session_start(); }
		$_SESSION[$session_name] = 1 ;
		die();
	}

	public static function plugin_extra_links($links, $plugin_name) {
		if ( $plugin_name != WOOPS_NAME) {
			return $links;
		}
		$links[] = '<a href="'.WOO_PREDICTIVE_SEARCH_DOCS_URI.'" target="_blank">'.__('Documentation', 'woops').'</a>';
		$links[] = '<a href="https://a3rev.com/forums/forum/woocommerce-plugins/predictive-search/" target="_blank">'.__('Support', 'woops').'</a>';
		return $links;
	}
}
?>