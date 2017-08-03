<?php

/**
 * WPB WooCommerce Product slider
 * By WpBean
 */


if ( !class_exists('wpb_wps_lite_settings' ) ):
class wpb_wps_lite_settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WPB_wps_mother_class;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }
	
    function admin_menu() {
        add_options_page( __( 'WPB Woo Product Slider', 'wpb-wps' ), __( 'WPB Woo Product Slider', 'wpb-wps' ), 'delete_posts', 'wpb_woocommerce_product_slider', array($this, 'wpb_wps_plugin_page') );
    }
	// setings tabs
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wpb_wps_general',
                'title' => __( 'General Settings', 'wpb-wps' )
            ),
            array(
                'id'    => 'wpb_wps_sidebar',
                'title' => __( 'Sidebar Settings', 'wpb-wps' )
            ),
            array(
                'id'    => 'wpb_wps_style',
                'title' => __( 'Style Settings', 'wpb-wps' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'wpb_wps_general' => array(
				array(
                    'name'      => 'wpb_num_col',
                    'label'     => __( 'Number Of column', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin how many column you want. For best result use 3 to 5 column.Default value 4.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '4'
				),
				array(
                    'name'      => 'wpb_num_pro',
                    'label'     => __( 'Number Of Product', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin how many Product you want in your slider. Default value 12.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '12'
				),
				array(
                    'name'      => 'wpb_slider_auto',
                    'label'     => __( 'Slider Auto Play', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need auto play or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'false',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_stop_hover_i',
                    'label'     => __( 'Slider Stop on Hover', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need stop on mouse hover or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_stop_nav',
                    'label'     => __( 'Navigation', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need navigation on top or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_nav_speed',
                    'label'     => __( 'Navigation Speed', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin navigation speed in millisecond. Default value 1000.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '1000'
				),
				array(
                    'name'      => 'wpb_stop_pagi',
                    'label'     => __( 'Pagination', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need pagination on bottom or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'false',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_pagi_speed',
                    'label'     => __( 'Pagination Speed', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin pagination speed in millisecond. Default value 1000.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '1000'
				),
				array(
                    'name'      => 'wpb_num_count',
                    'label'     => __( 'Pagination Number Counting', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need pagination need counting or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'false',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_touch_drag',
                    'label'     => __( 'Touch Drag', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need touch drag or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_mouse_drag',
                    'label'     => __( 'Mouse Drag', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need mouse drag or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				
            ),
			
            'wpb_wps_sidebar' => array(
				array(
                    'name'      => 'wpb_slider_auto_side_i',
                    'label'     => __( 'Slider Auto Play', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need auto play on sidebar or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_stop_hover_side',
                    'label'     => __( 'Slider Stop on Hover', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your sidebar slider need stop on mouse hover or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_stop_nav_side',
                    'label'     => __( 'Navigation', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your sidebar slider need navigation on top or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'false',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_nav_speed_side',
                    'label'     => __( 'Navigation Speed', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin navigation speed for sidebar slider in millisecond. Default value 1000.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '1000'
				),
				array(
                    'name'      => 'wpb_stop_pagi_side',
                    'label'     => __( 'Pagination', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your sidebar slider need pagination on bottom or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_pagi_speed_side',
                    'label'     => __( 'Pagination Speed', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin pagination speed for sidebar slider in millisecond. Default value 1000.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '1000'
				),
				array(
                    'name'      => 'wpb_num_count_side',
                    'label'     => __( 'Pagination Number Counting', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need pagination need counting or not. Default NO.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'false',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_touch_drag_side',
                    'label'     => __( 'Touch Drag', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need touch drag or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
				array(
                    'name'      => 'wpb_mouse_drag_side',
                    'label'     => __( 'Mouse Drag', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin your slider need mouse drag or not. Default Yes.', 'wpb-wps' ),
                    'type'      => 'radio',
    				'default'   => 'true',
                    'options'   => array(
                        'true'  => __( 'Yes', 'wpb-wps' ),
                        'false' => __( 'No', 'wpb-wps' ),
                    )
				),
            ),
            'wpb_wps_style' => array(
				array(
                    'name'      => 'wpb_slider_type_gen_lat',
                    'label'     => __( 'General latest product Slider type', 'wpb-wps' ),
                    'desc'      => __( 'Your plugin support two different type style slider, tell the plugin which style You Need for latest product general slider.', 'wpb-wps' ),
                    'type'      => 'select',
                    'default'   => 'grid cs-style-3',
                    'options'   => array(
                        'grid cs-style-3'   => __( 'Hover Animation', 'wpb-wps' ),
    					'grid_no_animation' => __( 'No Animation', 'wpb-wps' ),
    				)
				),
				array(
                    'name'      => 'wpb_slider_type_gen_fea',
                    'label'     => __( 'General feature product Slider type', 'wpb-wps' ),
                    'desc'      => __( 'Your plugin support two different type style slider, tell the plugin which style You Need for feature product general slider.', 'wpb-wps' ),
                    'type'      => 'select',
                    'default'   => 'grid cs-style-3',
                    'options'   => array(
                        'grid cs-style-3'   => __( 'Hover Animation', 'wpb-wps' ),
                        'grid_no_animation' => __( 'No Animation', 'wpb-wps' ),
    				)
				),
				array(
                    'name'      => 'wpb_slider_type_sid_lat',
                    'label'     => __( 'Sidebar latest product Slider type', 'wpb-wps' ),
                    'desc'      => __( 'Your plugin support two different type style slider, tell the plugin which style You Need for latest product sidebar slider.', 'wpb-wps' ),
                    'type'      => 'select',
                    'default'   => 'grid cs-style-3',
                    'options'   => array(
                        'grid cs-style-3'   => __( 'Hover Animation', 'wpb-wps' ),
                        'grid_no_animation' => __( 'No Animation', 'wpb-wps' ),
    				)
				),
				array(
                    'name'      => 'wpb_slider_type_sid_fea',
                    'label'     => __( 'Sidebar feature product Slider type', 'wpb-wps' ),
                    'desc'      => __( 'Your plugin support two different type style slider, tell the plugin which style You Need for feature product sidebar slider.', 'wpb-wps' ),
                    'type'      => 'select',
                    'default'   => 'grid cs-style-3',
                    'options'   => array(
                        'grid cs-style-3'   => __( 'Hover Animation', 'wpb-wps' ),
                        'grid_no_animation' => __( 'No Animation', 'wpb-wps' ),
    				)
				),
				array(
                    'name'      => 'wpb_title_mx_ch',
                    'label'     => __( 'Product Title max character', 'wpb-wps' ),
                    'desc'      => __( 'Tell the plugin how many character you want to show in product title. For best result use 10. Default value 10.', 'wpb-wps' ),
                    'type'      => 'number',
                    'default'   => '10'
				),
				array(
                    'name'      => 'wpb_pro_price_color_i',
                    'label'     => __( 'Product price color (only for No animation style slider)', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for product price. Default #16A085', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#16A085'
                ),
				array(
                    'name'      => 'wpb_wps_btn_bg',
                    'label'     => __( 'Button Background Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your buttons background color. Default #1abc9c', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#1abc9c'
                ),
				array(
                    'name'      => 'wpb_wps_btn_bg_hover',
                    'label'     => __( 'Button Background Hover Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your buttons background hover color. Default #16a085', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#16a085'
                ),
				array(
                    'name'      => 'wpb_pagi_btn_bg',
                    'label'     => __( 'Pagination Button Background Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your pagination buttons background color. Default #8BCFC2', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#8BCFC2'
                ),
				array(
                    'name'      => 'wpb_pagi_btn_bg_ac',
                    'label'     => __( 'Pagination Button Background Hover & active Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your pagination buttons background hover & active color. Default #16A085', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#16A085'
                ),
				array(
                    'name'      => 'wpb_nav_btn_bg',
                    'label'     => __( 'Navigation Button Background Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your navigation buttons background color. Default #CCCCCC', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#CCCCCC'
                ),
				array(
                    'name'      => 'wpb_nav_btn_bg_ac',
                    'label'     => __( 'Navigation Button Background Hover & active Color', 'wpb-wps' ),
                    'desc'      => __( 'Select a color for your navigation buttons background hover & active color. Default #999999', 'wpb-wps' ),
                    'type'      => 'color',
                    'default'   => '#999999'
                ),
            )
        );
		return $settings_fields;
    }
	
	// warping the settings
    function wpb_wps_plugin_page() {
        ?>
            <?php do_action ( 'wpb_wps_before_settings' ); ?>
            <div class="wpb_wps_settings_area">
                <div class="wrap wpb_wps_settings">
                    <?php
                        $this->settings_api->show_navigation();
                        $this->settings_api->show_forms();
                    ?>
                </div>
                <div class="wpb_wps_settings_content">
                    <?php do_action ( 'wpb_wps_settings_content' ); ?>
                </div>
            </div>
            <?php do_action ( 'wpb_wps_after_settings' ); ?>
        <?php
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}
endif;

$settings = new wpb_wps_lite_settings();