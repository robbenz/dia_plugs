<?php
/**
 * WooCommerce Jetpack General
 *
 * The WooCommerce Jetpack General class.
 *
 * @version 2.5.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_General' ) ) :

class WCJ_General extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 2.5.0
	 */
	public function __construct() {

		$this->id         = 'general';
		$this->short_desc = __( 'General', 'woocommerce-jetpack' );
		$this->desc       = __( 'Separate custom CSS for front and back end. Shortcodes in Wordpress text widgets.', 'woocommerce-jetpack' );
		$this->link       = 'http://booster.io/features/woocommerce-booster-general-tools/';
		parent::__construct();

		$this->add_tools( array(
			'products_atts'    => array(
				'title' => __( 'Products Atts', 'woocommerce-jetpack' ),
				'desc'  => __( 'All Products and All Attributes.', 'woocommerce-jetpack' ),
			),
			'export_customers' => array(
				'title' => __( 'Export Customers', 'woocommerce-jetpack' ),
				'desc'  => __( 'Export Customers.', 'woocommerce-jetpack' ),
			),
			'export_customers_from_orders' => array(
				'title' => __( 'Export Customers from Orders', 'woocommerce-jetpack' ),
				'desc'  => __( 'Export Customers (extracted from orders).', 'woocommerce-jetpack' ),
			),
			'export_orders' => array(
				'title' => __( 'Export Orders', 'woocommerce-jetpack' ),
				'desc'  => __( 'Export Orders.', 'woocommerce-jetpack' ),
			),
		) );

		if ( $this->is_enabled() ) {

			if ( 'yes' === get_option( 'wcj_product_revisions_enabled', 'no' ) ) {
				add_filter( 'woocommerce_register_post_type_product', array( $this, 'enable_product_revisions' ) );
			}

			if ( 'yes' === get_option( 'wcj_general_shortcodes_in_text_widgets_enabled' ) ) {
				add_filter( 'widget_text', 'do_shortcode' );
			}

			if ( '' != get_option( 'wcj_general_custom_css' ) ) {
				add_action( 'wp_head', array( $this, 'hook_custom_css' ) );
			}
			if ( '' != get_option( 'wcj_general_custom_admin_css' ) ) {
				add_action( 'admin_head', array( $this, 'hook_custom_admin_css' ) );
			}

			add_action( 'init', array( $this, 'export_csv' ) );
		}
	}

	/**
	 * enable_product_revisions.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function enable_product_revisions( $args ) {
		$args['supports'][] = 'revisions';
		return $args;
	}

	/**
	 * export.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function export( $tool_id ) {
		$data = array();
		switch ( $tool_id ) {
			case 'customers':
				$data = $this->export_customers();
				break;
			case 'customers_from_orders':
				$data = $this->export_customers_from_orders();
				break;
			case 'orders':
				$data = $this->export_orders();
				break;
		}
		return $data;
	}

	/**
	 * export_csv.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function export_csv() {
		if ( isset( $_POST['wcj_export'] ) ) {
			$data = $this->export( $_POST['wcj_export'] );
			$csv = '';
			foreach ( $data as $row ) {
				$csv .= implode( ',', $row ) . PHP_EOL;
			}
			header( "Content-Type: application/octet-stream" );
			header( "Content-Disposition: attachment; filename=" . $_POST['wcj_export'] . ".csv" );
			header( "Content-Type: application/octet-stream" );
			header( "Content-Type: application/download" );
			header( "Content-Description: File Transfer" );
			header( "Content-Length: " . strlen( $csv ) );
			echo $csv;
		}
	}

	/**
	 * create_export_tool.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function create_export_tool( $tool_id ) {
		$data = $this->export( $tool_id );
		echo '<p><form method="post" action="">';
		echo '<button class="button-primary" type="submit" name="wcj_export" value="' . $tool_id . '">' . __( 'Download CSV', 'woocommerce-jetpack' ) . '</button>';
		echo '</form></p>';
		echo wcj_get_table_html( $data, array( 'table_class' => 'widefat striped' ) );
	}

	/**
	 * create_export_customers_tool.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function create_export_customers_tool() {
		$this->create_export_tool( 'customers' );
	}

	/**
	 * create_export_orders_tool.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function create_export_orders_tool() {
		$this->create_export_tool( 'orders' );
	}

	/**
	 * create_export_customers_from_orders_tool.
	 *
	 * @version 2.4.8
	 * @since   2.3.9
	 */
	function create_export_customers_from_orders_tool() {
		$this->create_export_tool( 'customers_from_orders' );
	}

	/**
	 * export_customers.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function export_customers() {
		$data = array();
		$data[] = array(
			__( 'Customer ID', 'woocommerce-jetpack' ),
			__( 'Customer Email', 'woocommerce-jetpack' ),
			__( 'Customer First Name', 'woocommerce-jetpack' ),
			__( 'Customer Last Name', 'woocommerce-jetpack' ),
		);
		$customers = get_users( 'role=customer' );
		foreach ( $customers as $customer ) {
			$data[] = array( $customer->ID, $customer->user_email, $customer->first_name, $customer->last_name, );
		}
		return $data;
	}

	/**
	 * export_orders.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function export_orders() {
		$data = array();
		$data[] = array(
			__( 'Order ID', 'woocommerce-jetpack' ),
			__( 'Customer Email', 'woocommerce-jetpack' ),
			__( 'Customer First Name', 'woocommerce-jetpack' ),
			__( 'Customer Last Name', 'woocommerce-jetpack' ),
			__( 'Order Date', 'woocommerce-jetpack' ),
		);
		$offset = 0;
		$block_size = 96;
		while( true ) {
			$args_orders = array(
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'posts_per_page' => $block_size,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'offset'         => $offset,
			);
			$loop_orders = new WP_Query( $args_orders );
			if ( ! $loop_orders->have_posts() ) break;
			while ( $loop_orders->have_posts() ) : $loop_orders->the_post();
				$order_id = $loop_orders->post->ID;
				$order = wc_get_order( $order_id );
				$data[] = array( $order_id, $order->billing_email, $order->billing_first_name, $order->billing_last_name, get_the_date( 'Y/m/d' ), );
			endwhile;
			$offset += $block_size;
		}
		return $data;
	}

	/**
	 * export_customers_from_orders.
	 *
	 * @version 2.4.8
	 * @since   2.4.8
	 */
	function export_customers_from_orders() {
		$data = array();
		$data[] = array(
			__( 'Nr.', 'woocommerce-jetpack' ),
			__( 'Email', 'woocommerce-jetpack' ),
			__( 'First Name', 'woocommerce-jetpack' ),
			__( 'Last Name', 'woocommerce-jetpack' ),
			__( 'Last Order Date', 'woocommerce-jetpack' ),
		);
		$total_customers = 0;
		$orders = array();
		$offset = 0;
		$block_size = 96;
		while( true ) {
			$args_orders = array(
				'post_type'      => 'shop_order',
				'post_status'    => 'any',
				'posts_per_page' => $block_size,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'offset'         => $offset,
			);
			$loop_orders = new WP_Query( $args_orders );
			if ( ! $loop_orders->have_posts() ) break;
			while ( $loop_orders->have_posts() ) : $loop_orders->the_post();
				$order_id = $loop_orders->post->ID;
				$order = wc_get_order( $order_id );
				if ( isset( $order->billing_email ) && '' != $order->billing_email && ! in_array( $order->billing_email, $orders ) ) {
					$emails_to_skip = array();
					if ( ! in_array( $order->billing_email, $emails_to_skip ) ) {
						$total_customers++;
						$data[] = array( $total_customers, $order->billing_email, $order->billing_first_name, $order->billing_last_name, get_the_date( 'Y/m/d' ), );
						$orders[] = $order->billing_email;
					}
				}
			endwhile;
			$offset += $block_size;
		}
		return $data;
	}

	/**
	 * create_products_atts_tool.
	 *
	 * @version 2.3.9
	 * @since   2.3.9
	 */
	function create_products_atts_tool() {
		$html = '';
		$html .= $this->get_products_atts();
		echo $html;
	}

	/*
	 * get_products_atts.
	 *
	 * @version 2.3.9
	 * @since   2.3.9
	 */
	function get_products_atts() {

		$total_products = 0;

		$products_attributes = array();
		$attributes_names = array();
		$attributes_names['wcj_title']    = __( 'Product', 'woocommerce-jetpack' );
		$attributes_names['wcj_category'] = __( 'Category', 'woocommerce-jetpack' );

		$offset = 0;
		$block_size = 96;
		while( true ) {

			$args_products = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => $block_size,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'offset'         => $offset,
			);
			$loop_products = new WP_Query( $args_products );
			if ( ! $loop_products->have_posts() ) break;
			while ( $loop_products->have_posts() ) : $loop_products->the_post();

				$total_products++;
				$product_id = $loop_products->post->ID;
				$the_product = wc_get_product( $product_id );

				$products_attributes[ $product_id ]['wcj_title']    = '<a href="' . get_permalink( $product_id ) . '">' . $the_product->get_title() . '</a>';
				$products_attributes[ $product_id ]['wcj_category'] = $the_product->get_categories();

				foreach ( $the_product->get_attributes() as $attribute ) {
					$products_attributes[ $product_id ][ $attribute['name'] ] = $the_product->get_attribute( $attribute['name'] );
					if ( ! isset( $attributes_names[ $attribute['name'] ] ) ) {
						$attributes_names[ $attribute['name'] ] = wc_attribute_label( $attribute['name'] );
					}
				}

			endwhile;

			$offset += $block_size;

		}

		$table_data = array();
		if ( isset( $_GET['wcj_attribute'] ) && '' != $_GET['wcj_attribute'] ) {
			$table_data[] = array(
				__( 'Product', 'woocommerce-jetpack' ),
				__( 'Category', 'woocommerce-jetpack' ),
				$_GET['wcj_attribute'],
			);
		} else {
//			$table_data[] = array_values( $attributes_names );
			$table_data[] = array_keys( $attributes_names );
		}
		foreach ( $attributes_names as $attributes_name => $attribute_title ) {

			if ( isset( $_GET['wcj_attribute'] ) && '' != $_GET['wcj_attribute'] ) {
				if ( 'wcj_title' != $attributes_name && 'wcj_category' != $attributes_name && $_GET['wcj_attribute'] != $attributes_name ) {
					continue;
				}
			}

			foreach ( $products_attributes as $product_id => $product_attributes ) {
				$table_data[ $product_id ][ $attributes_name ] = isset( $product_attributes[ $attributes_name ] ) ? $product_attributes[ $attributes_name ] : '';
			}
		}

		return '<p>' . __( 'Total Products:', 'woocommerce-jetpack' ) . ' ' . $total_products . '</p>' . wcj_get_table_html( $table_data, array( 'table_class' => 'widefat striped' ) );
	}

	/**
	 * hook_custom_css.
	 */
	public function hook_custom_css() {
		$output = '<style>' . get_option( 'wcj_general_custom_css' ) . '</style>';
		echo $output;
	}

	/**
	 * hook_custom_admin_css.
	 */
	public function hook_custom_admin_css() {
		$output = '<style>' . get_option( 'wcj_general_custom_admin_css' ) . '</style>';
		echo $output;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.4.8
	 * @todo    add link to Booster's shortcodes list
	 */
	function get_settings() {

		/* $links_html = '';
		if ( class_exists( 'RecursiveIteratorIterator' ) && class_exists( 'RecursiveDirectoryIterator' ) ) {
			$dir = untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/../../woocommerce/templates' ) );
			$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
			foreach ( $rii as $file ) {
				$the_name = str_replace( $dir, '', $file->getPathname() );
				$the_name_link = str_replace( DIRECTORY_SEPARATOR, '%2F', $the_name );
				if ( $file->isDir() ) {
					/* $links_html .= '<strong>' . $the_name . '</strong>' . PHP_EOL; *//*
				} else {
					$links_html .= '<a href="' . get_admin_url( null, 'plugin-editor.php?file=woocommerce' . '%2F' . 'templates' . $the_name_link . '&plugin=woocommerce' ) . '">' .
							'templates' . $the_name . '</a>' . PHP_EOL;
				}
			}
		} else {
			$links_html = __( 'PHP 5 is required.', 'woocommerce-jetpack' );
		} */

		$settings = array(

			array(
				'title'    => __( 'Shortcodes Options', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_general_shortcodes_options',
			),

			array(
				'title'    => __( 'Enable All Shortcodes in WordPress Text Widgets', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'This will enable all (including non Booster\'s) shortcodes in WordPress text widgets.', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_shortcodes_in_text_widgets_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'title'    => __( 'Disable Booster\'s Shortcodes', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Disable all Booster\'s shortcodes (for memory saving).', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_shortcodes_disable_booster_shortcodes',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_general_shortcodes_options',
			),

			array(
				'title'    => __( 'Custom CSS Options', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'desc'     => __( 'Another custom CSS, if you need one.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_custom_css_options',
			),

			array(
				'title'    => __( 'Custom CSS - Front end (Customers)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_custom_css',
				'default'  => '',
				'type'     => 'custom_textarea',
				'css'      => 'width:66%;min-width:300px;min-height:300px;',
			),

			array(
				'title'    => __( 'Custom CSS - Back end (Admin)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_custom_admin_css',
				'default'  => '',
				'type'     => 'custom_textarea',
				'css'      => 'width:66%;min-width:300px;min-height:300px;',
			),

			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_general_custom_css_options',
			),

			array(
				'title'    => __( 'Product Revisions', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_product_revisions_options',
			),

			array(
				'title'    => __( 'Product Revisions', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_revisions_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_product_revisions_options',
			),

			array(
				'title'    => __( 'Advanced Options', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_general_advanced_options',
			),

			array(
				'title'    => __( 'Disable Loading Datepicker/Weekpicker CSS', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_disable_datepicker_css',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'title'    => __( 'Datepicker/Weekpicker CSS', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_datepicker_css',
				'default'  => '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',
				'type'     => 'text',
				'css'      => 'width:66%;min-width:300px;',
			),

			array(
				'title'    => __( 'Disable Loading Datepicker/Weekpicker JavaScript', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_disable_datepicker_js',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'title'    => __( 'Disable Loading Timepicker CSS', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_disable_timepicker_css',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'title'    => __( 'Disable Loading Timepicker JavaScript', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_disable_timepicker_js',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'title'    => __( 'Disable Saving PDFs in PHP directory for temporary files', 'woocommerce-jetpack' ),
				'desc'     => __( 'Disable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_advanced_disable_save_sys_temp_dir',
				'default'  => 'no',
				'type'     => 'checkbox',
			),

			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_general_advanced_options',
			),

			/* array(
				'title'    => __( 'WooCommerce Templates Editor Links', 'woocommerce-jetpack' ),
				'type'     => 'title',
				'id'       => 'wcj_general_wc_templates_editor_links_options',
			),

			array(
				'title'    => __( 'Templates', 'woocommerce-jetpack' ),
				'id'       => 'wcj_general_wc_templates_editor_links',
				'type'     => 'custom_link',
				'link'     => '<pre>' . $links_html . '</pre>',
			),

			array(
				'type'     => 'sectionend',
				'id'       => 'wcj_general_wc_templates_editor_links_options',
			), */
		);

		return $this->add_standard_settings( $settings );
	}
}

endif;

return new WCJ_General();
