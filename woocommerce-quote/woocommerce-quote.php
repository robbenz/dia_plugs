<?php

/**
 * Plugin Name: WooCommerce Quote
 * Plugin URI: http://www.rightpress.net/woocommerce-quote
 * Description: Let your customers save WooCommerce cart as a PDF Quote
 * Version: 1.0.2
 * Author: RightPress
 * Author URI: http://www.rightpress.net
 * Requires at least: 3.5
 * Tested up to: 3.8
 *
 * Text Domain: woo_quote
 * Domain Path: /languages
 * 
 * @package WooCommerce_Quote
 * @category Core
 * @author RightPress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('WOOQUO_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('WOOQUO_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('WOOQUO_VERSION', '1.0.2');

if (!class_exists('WooCommerce_Quote')) {

    /**
     * Main plugin class
     * 
     * @class WooCommerce_Quote
     * @package WooCommerce_Quote
     * @author RightPress
     */
    class WooCommerce_Quote
    {

        /**
         * Class constructor
         * 
         * @access public
         * @return void
         */
        public function __construct()
        {
            // Load translation
            load_plugin_textdomain('woo_quote', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            // Additional plugin page links
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'plugin_settings_link'));

            // Load plugin configuration
            require WOOQUO_PATH . '/includes/woo-quote-plugin-structure.inc.php';
            $this->get_config();

            // Load options
            $this->opt = $this->get_options();

            // Add settings page
            if (is_admin()) {
                add_action('admin_menu', array($this, 'add_admin_menu'));
                add_action('admin_init', array($this, 'admin_construct'));
            }

            // Load resources conditionally
            if (preg_match('/page=woocommerce_quotes/i', $_SERVER['QUERY_STRING'])) {
                add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
            }

            // Hook into WooCommerce / WordPress
            if ($this->opt['woo_quote_method'] != 'disabled') {

                // Display download quote button on cart page
                add_action($this->opt['woo_quote_button_position'], array($this, 'render_quote_button'));



                // Disable checkout
                if ($this->opt['woo_quote_request_disable_checkout']) {
                    add_filter('woocommerce_before_cart', array($this, 'hide_checkout_objects_using_css'));
                    //remove_action('init', 'woocommerce_proceed_to_checkout', 10);
                    //remove_action('init', 'woocommerce_pay_action', 10);
                }

                // Some hooks need to be attached after init is triggered
                add_action('init', array($this, 'on_init'));

            }

            // Intercept download calls
            if (isset($_GET['get_woo_quote'])) {
                add_action('wp', array($this, 'push_quote'));
            }
        }

        /**
         * Function hooked to init
         * 
         * @access public
         * @return void
         */
        public function on_init()
        {
            // Change Add To Cart button text
            if (!empty($this->opt['woo_quote_title_add_to_cart'])) {
                $prefix = self::wc_version_gte('2.1') ? 'woocommerce_' : '';
                add_filter($prefix . 'product_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
                add_filter($prefix . 'product_variable_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
                add_filter($prefix . 'product_grouped_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
                add_filter($prefix . 'product_external_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
                add_filter($prefix . 'product_out_of_stock_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
                add_filter($prefix . 'product_single_add_to_cart_text', array($this, 'change_add_to_cart_label'), 99);
            }

            // Mask prices
            if ($this->opt['woo_quote_request_hide_prices']) {
                // Prices
                add_filter('woocommerce_cart_item_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_empty_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_free_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_free_sale_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_get_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_grouped_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_sale_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variable_empty_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variable_free_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variable_free_sale_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variable_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variable_sale_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variation_empty_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variation_free_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variation_price_html', array($this, 'mask_price'), 99);
                add_filter('woocommerce_variation_sale_price_html', array($this, 'mask_price'), 99);

                // Subtotal, total etc.
                add_filter('woocommerce_cart_item_subtotal', array($this, 'mask_price'), 99);
                add_filter('woocommerce_cart_subtotal', array($this, 'mask_price'), 99);
                add_filter('woocommerce_cart_total', array($this, 'mask_price'), 99);

                // Hide totals block using CSS
                add_filter('woocommerce_before_cart', array($this, 'hide_cart_objects_using_css'));
            }
        }

        /**
         * Loads/sets configuration values from structure file and database
         * 
         * @access public
         * @return void
         */
        public function get_config()
        {
            // Settings tree
            $this->settings = woo_quote_plugin_settings();

            // Load some data from config
            $this->hints = $this->options('hint');
            $this->validation = $this->options('validation', true);
            $this->titles = $this->options('title');
            $this->options = $this->options('values');
            $this->section_info = $this->get_section_info();
        }

        /**
         * Get settings options: default, hint, validation, values
         * 
         * @access public
         * @param string $name
         * @param bool $split_by_page
         * @return array
         */
        public function options($name, $split_by_page = false)
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page => $page_value) {
                $page_options = array();

                foreach ($page_value['children'] as $section => $section_value) {
                    foreach ($section_value['children'] as $field => $field_value) {
                        if (isset($field_value[$name])) {
                            $page_options['woo_quote_' . $field] = $field_value[$name];
                        }
                    }
                }

                $results[preg_replace('/_/', '-', $page)] = $page_options;
            }

            $final_results = array();

            if (!$split_by_page) {
                foreach ($results as $value) {
                    $final_results = array_merge($final_results, $value);
                }
            }
            else {
                $final_results = $results;
            }

            return $final_results;
        }

        /**
         * Get array of section info strings
         * 
         * @access public
         * @return array
         */
        public function get_section_info()
        {
            $results = array();

            // Iterate over settings array and extract values
            foreach ($this->settings as $page_value) {
                foreach ($page_value['children'] as $section => $section_value) {
                    if (isset($section_value['info'])) {
                        $results[$section] = $section_value['info'];
                    }
                }
            }

            return $results;
        }

        /*
         * Get plugin options set by user
         * 
         * @access public
         * @return array
         */
        public function get_options()
        {
            $saved_options = get_option('woo_quote_options', $this->options('default'));

            if (is_array($saved_options)) {
                return array_merge($this->options('default'), $saved_options);
            }
            else {
                return $this->options('default');
            }
        }

        /*
         * Update options
         * 
         * @access public
         * @return bool
         */
        public function update_options($args = array())
        {
            return update_option('woo_quote_options', array_merge($this->get_options(), $args));
        }

        /**
         * Add link to admin page under Woocommerce menu
         * 
         * @access public
         * @return void
         */
        public function add_admin_menu()
        {            
            global $current_user;

            get_currentuserinfo();
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            if (!in_array($user_role, array('administrator', 'shop_manager'))) {
                return;
            }

            global $submenu;

            if (isset($submenu['woocommerce'])) {
                add_submenu_page(
                    'woocommerce',
                    __('WooCommerce Quotes', 'woo_quote'),
                    __('Quotes', 'woo_quote'),
                    'edit_posts',
                    'woocommerce_quotes',
                    array($this, 'set_up_admin_page')
                );
            }
        }

        /*
         * Set up admin page
         * 
         * @access public
         * @return void
         */
        public function set_up_admin_page()
        {
            // Check for general warnings
            if (!$this->image_library_exists()) {
                add_settings_error(
                    'woo_quote',
                    'general',
                    __('Image processing library not found on your server.<br>You must have either GD or Imagick extension enabled on your server for this module to work correctly.', 'woo_quote')
                );
            }

            // Print notices
            settings_errors('woo_quote');

            $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general_settings';
            $current_tab = isset($this->settings[$current_tab]) ? $current_tab : 'general_settings';

            // Print page tabs
            $this->render_tabs($current_tab);

            // Print page content
            $this->render_page($current_tab);

        }

        /**
         * Admin interface constructor
         * 
         * @access public
         * @return void
         */
        public function admin_construct()
        {
            global $current_user;

            get_currentuserinfo();
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            if (!in_array($user_role, array('administrator', 'shop_manager'))) {
                return;
            }

            // Iterate pages
            foreach ($this->settings as $page => $page_value) {

                register_setting(
                    'woo_quote_opt_group_' . $page,
                    'woo_quote_options',
                    array($this, 'options_validate')
                );

                // Iterate sections
                foreach ($page_value['children'] as $section => $section_value) {

                    add_settings_section(
                        $section,
                        $section_value['title'],
                        array($this, 'render_section_info'),
                        'woo-quote-admin-' . str_replace('_', '-', $page)
                    );

                    // Iterate fields
                    foreach ($section_value['children'] as $field => $field_value) {

                        add_settings_field(
                            'woo_quote_' . $field,
                            $field_value['title'],
                            array($this, 'render_options_' . $field_value['type']),
                            'woo-quote-admin-' . str_replace('_', '-', $page),
                            $section,
                            array(
                                'name' => 'woo_quote_' . $field,
                                'options' => $this->opt,
                            )
                        );

                    }
                }
            }
        }

        /**
         * Render admin page navigation tabs
         * 
         * @access public
         * @param string $current_tab
         * @return void
         */
        public function render_tabs($current_tab = 'general-settings')
        {
            $current_tab = preg_replace('/-/', '_', $current_tab);

            // Output admin page tab navigation
            echo '<div class="woo_quote_tabs_container">';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ($this->settings as $page => $page_value) {
                $class = ($page == $current_tab) ? ' nav-tab-active' : '';
                echo '<a class="nav-tab'.$class.'" href="?page=woocommerce_quotes&tab='.$page.'">'.((isset($page_value['icon']) && !empty($page_value['icon'])) ? $page_value['icon'] . '&nbsp;' : '').$page_value['title'].'</a>';
            }
            echo '</h2>';
            echo '</div>';
        }

        /**
         * Render settings page
         * 
         * @access public
         * @param string $page
         * @return void
         */
        public function render_page($page){

            $page_name = preg_replace('/_/', '-', $page);

            ?>

                <div class="wrap woocommerce woo-quote">
                <div class="woo_quote_container">
                    <form method="post" action="options.php" enctype="multipart/form-data">
                        <input type="hidden" name="current_tab" value="<?php echo $page_name; ?>" />

                        <?php
                            settings_fields('woo_quote_opt_group_' . $page);
                            do_settings_sections('woo-quote-admin-' . $page_name);

                            echo '<div></div>';

                            submit_button();
                        ?>

                    </form>
                </div>
                </div>
            <?php

            // Get uploads url and path
            $uploads_dir = wp_upload_dir();

            // Pass variables to JavaScript
            ?>
                <script language="JavaScript">
                    var woo_quote_hints = <?php echo json_encode($this->hints); ?>;
                    var woo_quote_home_url = '<?php echo home_url(); ?>';
                    var woo_quote_url_fopen_allowed = '<?php echo (ini_get('allow_url_fopen') ? '1' : '0'); ?>';
                    var woo_quote_uploads_url = '<?php echo $uploads_dir['baseurl']; ?>';
                    var woo_quote_uploads_path = '<?php echo $uploads_dir['basedir']; ?>';
                </script>
            <?php
        }

        /**
         * Render section info
         * 
         * @access public
         * @param array $section
         * @return void
         */
        public function render_section_info($section)
        {
            if (isset($this->section_info[$section['id']])) {
                echo $this->section_info[$section['id']];
            }
        }

        /*
         * Render a text field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_text($args = array())
        {
            printf(
                '<input type="text" id="%s" name="woo_quote_options[%s]" value="%s" class="woo-quote-field-width" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a text area
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_textarea($args = array())
        {
            printf(
                '<textarea id="%s" name="woo_quote_options[%s]" class="woo_quote_textarea">%s</textarea>',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );
        }

        /*
         * Render a checkbox
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_checkbox($args = array())
        {
            printf(
                '<input type="checkbox" id="%s" name="woo_quote_options[%s]" value="1" %s />',
                $args['name'],
                $args['name'],
                checked($args['options'][$args['name']], true, false)
            );
        }

        /*
         * Render a dropdown
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_dropdown($args = array())
        {
            printf(
                '<select id="%s" name="woo_quote_options[%s]" class="woo-quote-field-width">',
                $args['name'],
                $args['name']
            );
            foreach ($this->options[$args['name']] as $key => $name) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    $key,
                    selected($key, $args['options'][$args['name']], false),
                    $name
                );
            }
            echo '</select>';
        }

        /**
         * Render select from media library field
         * 
         * @access public
         * @param array $args
         * @return void
         */
        public function render_options_media($args = array())
        {
            // Render text input field
            printf(
                '<input id="%s" type="text" name="woo_quote_options[%s]" value="%s" class="woo-quote-field-width" />',
                $args['name'],
                $args['name'],
                $args['options'][$args['name']]
            );

            // Render "Open Library" button 
            printf(
                '<input id="%s_upload_button" type="button" value="%s" />',
                $args['name'],
                __('Open Library', 'woo_quote')
            );
        }

        /**
         * Validate admin form input
         * 
         * @access public
         * @param array $input
         * @return array
         */
        public function options_validate($input)
        {
            $current_tab = isset($_POST['current_tab']) ? $_POST['current_tab'] : 'general-settings';
            $output = $this->get_options();

            $errors = array();

            // Iterate over fields and validate/sanitize input
            foreach ($this->validation[$current_tab] as $field => $rule) {

                // Different routines for different field types
                switch($rule['rule']) {

                    // Validate numbers
                    case 'number':
                        if (is_numeric($input[$field]) || ($input[$field] == '' && $rule['empty'] == true)) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'number'));
                        }
                        break;

                    // Validate boolean values (actually 1 and 0)
                    case 'bool':
                        $input[$field] = (!isset($input[$field]) || $input[$field] == '') ? '0' : $input[$field];
                        if (in_array($input[$field], array('0', '1')) || ($input[$field] == '' && $rule['empty'] == true)) {
                            $output[$field] = $input[$field];
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'bool'));
                        }
                        break;

                    // Validate predefined options
                    case 'option':
                        if (isset($input[$field]) && (isset($this->options[$field][$input[$field]]) || ($input[$field] == '' && $rule['empty'] == true))) {
                            $output[$field] = $input[$field];
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'option'));
                        }
                        break;

                    // Validate emails
                    case 'email':
                        if (isset($input[$field]) && (filter_var(trim($field), FILTER_VALIDATE_EMAIL) || ($input[$field] == '' && $rule['empty'] == true))) {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            array_push($errors, array('setting' => $field, 'code' => 'email'));
                        }
                        break;

                    // Validate URLs
                    case 'url':
                        // FILTER_VALIDATE_URL for filter_var() does not work as expected
                        if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
                            array_push($errors, array('setting' => $field, 'code' => 'url'));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        break;

                    // Default validation rule (text fields etc)
                    default:
                        if (isset($input[$field]) && ($input[$field] == '' && $rule['empty'] != true)) {
                            array_push($errors, array('setting' => $field, 'code' => 'string'));
                        }
                        else if (!isset($input[$field])) {
                            $output[$field] = '';
                        }
                        else {
                            $output[$field] = esc_attr(trim($input[$field]));
                        }
                        break;
                }
            }

            // Display settings updated message
            add_settings_error(
                'woo_quote',
                'woo_quote_' . 'settings_updated',
                __('Your settings have been saved.', 'woo_quote'),
                'updated'
            );

            // Display errors
            foreach ($errors as $error) {
                $reverted = __('Reverted to a previous value.', 'woo_quote');

                $messages = array(
                    'number' => __('must be numeric', 'woo_quote') . '. ' . $reverted,
                    'bool' => __('must be either 0 or 1', 'woo_quote') . '. ' . $reverted,
                    'option' => __('is not allowed', 'woo_quote') . '. ' . $reverted,
                    'email' => __('is not a valid email address', 'woo_quote') . '. ' . $reverted,
                    'url' => __('is not a valid URL', 'woo_quote') . '. ' . $reverted,
                    'string' => __('is not a valid text string', 'woo_quote') . '. ' . $reverted,
                );

                add_settings_error(
                    'woo_quote',
                    $error['code'],
                    __('Value of', 'woo_quote') . ' "' . $this->titles[$error['setting']] . '" ' . $messages[$error['code']]
                );
            }

            return $output;
        }

        /**
         * Get next quote number
         * 
         * @access public
         * @return int
         */
        public function get_next_quote_number()
        {
            // Get next quote number
            $next_quote_number = $this->opt['woo_quote_next_quote_number'];

            // Store next quote number in options storage
            $this->update_options(array('woo_quote_next_quote_number' => ($next_quote_number + 1)));

            return $next_quote_number;
        }

        /**
         * Generates and pushes quote to the browser
         * 
         * @access public
         * @return void
         */
        public function push_quote()
        {
            define('WOOCOMMERCE_CART', '1');

            global $woocommerce;

            if (empty($woocommerce->cart->cart_contents)) {
                return;
            }

            $woocommerce->cart->calculate_totals();
            $woocommerce->cart->calculate_fees();

            // Load PDF class
            if (!class_exists('TCPDF')) {
                require WOOQUO_PATH.'/includes/tcpdf/tcpdf.php';
            }
            if (!class_exists('WooQuote')) {
                require WOOQUO_PATH.'/includes/woo-quote.class.php';
            }

            $current_quote_number = $this->get_next_quote_number();

            // Custom customer details
            $customer_details = array();

            // Generate quote and push it directly to browser
            $pdf = new WooQuote($this->get_options(), $current_quote_number, $customer_details, 'P', 'pt', 'A4');
            $pdf->CreateQuote();
            $pdf->Output($this->opt['woo_quote_title_filename_prefix'] . $current_quote_number.'.pdf', 'D');
            exit();
        }

        /**
         * Load scripts required for admin
         * 
         * @access public
         * @return void
         */
        public function enqueue_scripts() {
            // Scripts
            wp_enqueue_script('jquery');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-tooltip');

            // Our own
            wp_register_script('woo-quote-js', WOOQUO_URL . '/assets/js/woo-quote.js', array('jquery'), WOOQUO_VERSION);
            wp_enqueue_script('woo-quote-js');

            // Styles
            wp_register_style('woo-quote-admin-css', WOOQUO_URL . '/assets/css/style-admin.css', array(), WOOQUO_VERSION);
            wp_enqueue_style('woo-quote-admin-css');
            wp_register_style('woo-quote-jquery-ui', WOOQUO_URL . '/assets/css/jquery-ui.css', array(), '1.10.3');
            wp_enqueue_style('woo-quote-jquery-ui');
            wp_register_style('woo-quote-font-awesome', WOOQUO_URL . '/assets/css/font-awesome/css/font-awesome.min.css', array(), '4.0.3');
            wp_enqueue_style('woo-quote-font-awesome');
            wp_enqueue_style('thickbox');
        }

        /**
         * Check if PHP image processing extension is installed
         * 
         * @access public
         * @return bool
         */
        public function image_library_exists()
        {
            if (extension_loaded('imagick') || (extension_loaded('gd') && function_exists('gd_info'))) {
                return true;
            }

            return false;
        }

        /**
         * Add settings link on plugins page
         * 
         * @access public
         * @return void
         */
        public function plugin_settings_link($links)
        {
            $settings_link = '<a href="http://support.rightpress.net/" target="_blank">'.__('Support', 'woo_quote').'</a>';
            array_unshift($links, $settings_link);
            $settings_link = '<a href="admin.php?page=woocommerce_quotes">'.__('Settings', 'woo_quote').'</a>';
            array_unshift($links, $settings_link);
            return $links; 
        }

        /**
         * Render quote download button
         * 
         * @access public
         * @return void
         */
        public function render_quote_button()
        {
            echo apply_filters(
                    'woo_quote_button',
                    '<a  href="' . home_url('/?get_woo_quote=1') . '"><button type="button" id="quote-button-id" class="' .
                    join(' ', apply_filters('woocommerce_quote_button_classes', array('button', 'alt', 'woo_quote_button')))
                    . '">' .  $this->opt['woo_quote_title_download_quote'] . '</button></a>',
                    $this->opt['woo_quote_title_download_quote'],
                    home_url('/?get_woo_quote=1')
                );
        }

        /**
         * Check WooCommerce version
         * 
         * @access public
         * @param string $version
         * @return bool
         */
        public static function wc_version_gte($version)
        {
            if (defined('WC_VERSION') && WC_VERSION) {
                return version_compare(WC_VERSION, $version, '>=');
            }
            else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
                return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
            }
            else {
                global $woocommerce;

                if (is_object($woocommerce) && version_compare($woocommerce->version, $version, '>=')) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }

        /**
         * Change Add To Cart button label
         * 
         * @access public
         * @return string
         */
        public function change_add_to_cart_label()
        {
            return $this->opt['woo_quote_title_add_to_cart'];
        }

        /**
         * Mask price
         * 
         * @access public
         * @return string
         */
        public function mask_price()
        {
            return '<span class="amount">' . $this->opt['woo_quote_title_instead_of_price'] . '</span>';
        }

        /**
         * Hide checkout objects using CSS
         * 
         * @access public
         * @return string
         */
        public function hide_checkout_objects_using_css()
        {
            $selectors = apply_filters('woocommerce_quote_hide_checkout_elements_from_cart', array(
                '#page .woocommerce input.checkout-button'
            ));

            echo '<style>';

            foreach ($selectors as $selector) {
                echo  $selector . ' { display: none; }';
            }

            echo '</style>';
        }

        /**
         * Hide cart objects using CSS
         * 
         * @access public
         * @return string
         */
        public function hide_cart_objects_using_css()
        {
            $selectors = apply_filters('woocommerce_quote_hide_cart_elements', array(
                '.woocommerce .cart-collaterals .cart_totals',
                '.woocommerce .cart-collaterals .shipping_calculator',
                '#page .woocommerce .actions .coupon',
            ));

            echo '<style>';

            foreach ($selectors as $selector) {
                echo  $selector . ' { display: none; }';
            }

            echo '</style>';
        }

    }

    $GLOBALS['WooCommerce_Quote'] = new WooCommerce_Quote();

}

?>
