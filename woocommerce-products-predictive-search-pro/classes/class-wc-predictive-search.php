<?php
/**
 * WooCommerce Predictive Search
 *
 * Class Function into woocommerce plugin
 *
 * Table Of Contents
 *
 * get_id_excludes()
 * woops_limit_words()
 * create_page()
 * create_page_wpml()
 * auto_create_page_for_wpml()
 * strip_shortcodes()
 * upgrade_version_2_0()
 */
class WC_Predictive_Search
{
	public static function get_id_excludes() {
		global $wc_predictive_id_excludes;

		$exclude_p_categories = get_option('woocommerce_search_exclude_p_categories', '');
		if (!is_array($exclude_p_categories)) {
			$exclude_p_categories_array = explode(",", $exclude_p_categories);
			if (is_array($exclude_p_categories_array) && count($exclude_p_categories_array) > 0) {
				$exclude_p_categories_array_new = array();
				foreach ($exclude_p_categories_array as $exclude_p_categories_item) {
					if ( trim($exclude_p_categories_item) > 0) $exclude_p_categories_array_new[] = $exclude_p_categories_item;
				}
				$exclude_p_categories = implode(",", $exclude_p_categories_array_new);
			} else {
				$exclude_p_categories = '';
			}
		} else {
			$exclude_p_categories = implode(",", $exclude_p_categories);
		}

		$exclude_p_tags = get_option('woocommerce_search_exclude_p_tags', '');
		if (!is_array($exclude_p_tags)) {
			$exclude_p_tags_array = explode(",", $exclude_p_tags);
			if (is_array($exclude_p_tags_array) && count($exclude_p_tags_array) > 0) {
				$exclude_p_tags_array_new = array();
				foreach ($exclude_p_tags_array as $exclude_p_tags_item) {
					if ( trim($exclude_p_tags_item) > 0) $exclude_p_tags_array_new[] = $exclude_p_tags_item;
				}
				$exclude_p_tags = implode(",", $exclude_p_tags_array_new);
			} else {
				$exclude_p_tags = '';
			}
		} else {
			$exclude_p_tags = implode(",", $exclude_p_tags);
		}

		$exclude_products = get_option('woocommerce_search_exclude_products', '');
		if (is_array($exclude_products)) {
			$exclude_products = implode(",", $exclude_products);
		}

		$exclude_posts = get_option('woocommerce_search_exclude_posts', '');
		if (is_array($exclude_posts)) {
			$exclude_posts = implode(",", $exclude_posts);
		}

		$exclude_pages = get_option('woocommerce_search_exclude_pages', '');
		if (is_array($exclude_pages)) {
			$exclude_pages = implode(",", $exclude_pages);
		}

		$wc_predictive_id_excludes = array();
		$wc_predictive_id_excludes['exclude_products'] = $exclude_products;
		$wc_predictive_id_excludes['exclude_p_categories'] = $exclude_p_categories;
		$wc_predictive_id_excludes['exclude_p_tags'] = $exclude_p_tags;
		$wc_predictive_id_excludes['exclude_posts'] = $exclude_posts;
		if ( class_exists('SitePress') && get_option('woocommerce_search_page_id', 0 ) > 0 ) {
			global $wpdb;
			$translation_page_data = $wpdb->get_results( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page'", get_option('woocommerce_search_page_id') ) );
			if ( is_array( $translation_page_data ) && count( $translation_page_data ) > 0 ) {
				foreach ( $translation_page_data as $translation_page ) {
					$exclude_pages .= ",".$translation_page->element_id;
				}
			}
		}
		$wc_predictive_id_excludes['exclude_pages'] = get_option('woocommerce_search_page_id').','.$exclude_pages;

		return $wc_predictive_id_excludes;
	}

	public static function woops_limit_words($str='',$len=100,$more) {
		if (trim($len) == '' || $len < 0) $len = 100;
	   if ( $str=="" || $str==NULL ) return $str;
	   if ( is_array($str) ) return $str;
	   $str = trim($str);
	   $str = strip_tags(str_replace("\r\n", "", $str));
	   if ( strlen($str) <= $len ) return $str;
	   $str = substr($str,0,$len);
	   if ( $str != "" ) {
			if ( !substr_count($str," ") ) {
					  if ( $more ) $str .= " ...";
					return $str;
			}
			while( strlen($str) && ($str[strlen($str)-1] != " ") ) {
					$str = substr($str,0,-1);
			}
			$str = substr($str,0,-1);
			if ( $more ) $str .= " ...";
			}
			return $str;
	}

	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option($option);

		if ( $option_value > 0 && get_post( $option_value ) )
			return $option_value;

		$page_id = $wpdb->get_var( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%$page_content%'  AND `post_type` = 'page' AND post_status = 'publish' ORDER BY ID ASC LIMIT 1" );

		if ( $page_id != NULL ) :
			if ( ! $option_value )
				update_option( $option, $page_id );
			return $page_id;
		endif;

		$page_data = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'post_parent' 		=> $post_parent,
			'comment_status' 	=> 'closed'
		);
		$page_id = wp_insert_post( $page_data );

		if ( class_exists('SitePress') ) {
			global $sitepress;
			$source_lang_code = $sitepress->get_default_language();
			$wpdb->query( "UPDATE ".$wpdb->prefix . "icl_translations SET trid=".$page_id." WHERE element_id=".$page_id." AND language_code='".$source_lang_code."' AND element_type='post_page' " );
		}

		update_option( $option, $page_id );

		return $page_id;
	}

	public static function create_page_wpml( $trid, $lang_code, $source_lang_code, $slug, $page_title = '', $page_content = '' ) {
		global $wpdb;

		$element_id = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " AS p INNER JOIN " . $wpdb->prefix . "icl_translations AS ic ON p.ID = ic.element_id WHERE p.post_content LIKE '%$page_content%' AND p.post_type = 'page' AND p.post_status = 'publish' AND ic.trid=".$trid." AND ic.language_code = '".$lang_code."' AND ic.element_type = 'post_page' ORDER BY p.ID ASC LIMIT 1" );

		if ( $element_id != NULL ) :
			return $element_id;
		endif;

		$page_data = array(
			'post_date'			=> gmdate( 'Y-m-d H:i:s' ),
			'post_modified'		=> gmdate( 'Y-m-d H:i:s' ),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'comment_status' 	=> 'closed'
		);
		$wpdb->insert( $wpdb->posts , $page_data);
		$element_id = $wpdb->insert_id;

		//$element_id = wp_insert_post( $page_data );

		$wpdb->insert( $wpdb->prefix . "icl_translations", array(
				'element_type'			=> 'post_page',
				'element_id'			=> $element_id,
				'trid'					=> $trid,
				'language_code'			=> $lang_code,
				'source_language_code'	=> $source_lang_code,
			) );

		return $element_id;
	}

	public static function auto_create_page_for_wpml(  $trid, $slug, $page_title = '', $page_content = '' ) {
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$active_languages = $sitepress->get_active_languages();
			if ( is_array($active_languages)  && count($active_languages) > 0 ) {
				$source_lang_code = $sitepress->get_default_language();
				foreach ( $active_languages as $language ) {
					if ( $language['code'] == $source_lang_code ) continue;
					WC_Predictive_Search::create_page_wpml( $trid, $language['code'], $source_lang_code, $slug.'-'.$language['code'], $page_title.' '.$language['display_name'], $page_content );
				}
			}
		}
	}

	public static function get_page_id_from_shortcode( $shortcode, $option ) {
		global $wpdb;
		global $wp_version;
		$page_id = get_option($option);
		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$shortcode = esc_sql( like_escape( $shortcode ) );
		} else {
			$shortcode = esc_sql( $wpdb->esc_like( $shortcode ) );
		}
		$page_data = null;
		if ($page_id)
			$page_data = $wpdb->get_row( "SELECT ID FROM " . $wpdb->posts . " WHERE post_content LIKE '%[{$shortcode}]%' AND ID = '".$page_id."' AND post_type = 'page' LIMIT 1" );
		if ( $page_data == null )
			$page_data = $wpdb->get_row( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%[{$shortcode}]%' AND `post_type` = 'page' ORDER BY post_date DESC LIMIT 1" );

		$page_id = $page_data->ID;

		// For WPML
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$translation_page_data = null;
			$translation_page_data = $wpdb->get_row( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page' AND language_code = %s LIMIT 1", $page_id , $sitepress->get_current_language() ) );
			if ( $translation_page_data != null )
				$page_id = $translation_page_data->element_id;
		}

		return $page_id;
	}

	public static function strip_shortcodes ($content='') {
		$content = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content);

		return $content;
	}

	public static function upgrade_version_2_0() {
		$exclude_products = get_option('woocommerce_search_exclude_products', '');
		$exclude_p_categories = get_option('woocommerce_search_exclude_p_categories', '');
		$exclude_p_tags = get_option('woocommerce_search_exclude_p_tags', '');
		$exclude_posts = get_option('woocommerce_search_exclude_posts', '');
		$exclude_pages = get_option('woocommerce_search_exclude_pages', '');

		if ($exclude_products !== false) {
			$exclude_products_array = explode(",", $exclude_products);
			if (is_array($exclude_products_array) && count($exclude_products_array) > 0) {
				$exclude_products_array_new = array();
				foreach ($exclude_products_array as $exclude_products_item) {
					if ( trim($exclude_products_item) > 0) $exclude_products_array_new[] = trim($exclude_products_item);
				}
				$exclude_products = $exclude_products_array_new;
			} else {
				$exclude_products = array();
			}
			update_option('woocommerce_search_exclude_products', (array) $exclude_products);
		} else {
			update_option('woocommerce_search_exclude_products', array());
		}

		if ($exclude_posts !== false) {
			$exclude_posts_array = explode(",", $exclude_posts);
			if (is_array($exclude_posts_array) && count($exclude_posts_array) > 0) {
				$exclude_posts_array_new = array();
				foreach ($exclude_posts_array as $exclude_posts_item) {
					if ( trim($exclude_posts_item) > 0) $exclude_posts_array_new[] = trim($exclude_posts_item);
				}
				$exclude_posts = $exclude_posts_array_new;
			} else {
				$exclude_posts = array();
			}
			update_option('woocommerce_search_exclude_posts', (array) $exclude_posts);
		} else {
			update_option('woocommerce_search_exclude_posts', array());
		}

		if ($exclude_pages !== false) {
			$exclude_pages_array = explode(",", $exclude_pages);
			if (is_array($exclude_pages_array) && count($exclude_pages_array) > 0) {
				$exclude_pages_array_new = array();
				foreach ($exclude_pages_array as $exclude_pages_item) {
					if ( trim($exclude_pages_item) > 0) $exclude_pages_array_new[] = trim($exclude_pages_item);
				}
				$exclude_pages = $exclude_pages_array_new;
			} else {
				$exclude_pages = array();
			}
			update_option('woocommerce_search_exclude_pages', (array) $exclude_pages);
		} else {
			update_option('woocommerce_search_exclude_pages', array());
		}

		if ($exclude_p_categories !== false) {
			$exclude_p_categories_array = explode(",", $exclude_p_categories);
			if (is_array($exclude_p_categories_array) && count($exclude_p_categories_array) > 0) {
				$exclude_p_categories_array_new = array();
				foreach ($exclude_p_categories_array as $exclude_p_categories_item) {
					if ( trim($exclude_p_categories_item) > 0) $exclude_p_categories_array_new[] = trim($exclude_p_categories_item);
				}
				$exclude_p_categories = $exclude_p_categories_array_new;
			} else {
				$exclude_p_categories = array();
			}
			update_option('woocommerce_search_exclude_p_categories', (array) $exclude_p_categories);
		} else {
			update_option('woocommerce_search_exclude_p_categories', array());
		}

		if ($exclude_p_tags !== false) {
			$exclude_p_tags_array = explode(",", $exclude_p_tags);
			if (is_array($exclude_p_tags_array) && count($exclude_p_tags_array) > 0) {
				$exclude_p_tags_array_new = array();
				foreach ($exclude_p_tags_array as $exclude_p_tags_item) {
					if ( trim($exclude_p_tags_item) > 0) $exclude_p_tags_array[] = trim($exclude_p_tags_item);
				}
				$exclude_p_tags = $exclude_p_tags_array;
			} else {
				$exclude_p_tags = array();
			}
			update_option('woocommerce_search_exclude_p_tags', (array) $exclude_p_tags);
		} else {
			update_option('woocommerce_search_exclude_p_tags', array());
		}
	}
}
?>