<?php
/**
 * Outputs public VFB form
 *
 * @since      3.0
 */
class VFB_Pro_Form_Display {
	/**
	 * The newly created entry ID
	 *
	 * Available only after form submission
	 *
	 * @var mixed
	 * @access public
	 * @static
	 */
	public static $entry_id;

	/**
	 * The main instanace
	 *
	 * @since	3.0.4
	 * @var 	mixed
	 * @access 	private
	 * @static
	 */
	private static $instance = null;

	/**
     * Protected constructor to prevent creating a new instance
     * via the 'new' operator from outside of this class.
     *
     * @return void
     */
	protected function __construct() {
	}

	/**
     * Private clone method to prevent cloning of the instance.
     *
     * @return void
     */
    private function __clone() {
    }

    /**
     * Private unserialize method to prevent unserializing of the instance.
     *
     * @return void
     */
    private function __wakeup() {
    }

	/**
	 * Create a single instance
	 *
	 * Insures that only one instance of this class is running.
	 * Otherwise known as the Singleton class pattern
	 *
	 * @since    3.0.4
	 * @access   public
	 * @static
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new VFB_Pro_Form_Display;

			add_shortcode( 'vfb', array( self::$instance, 'display' ) );
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'css' ) );
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'js' ) );
			add_action( 'init', array( self::$instance, 'process_email' ) );
			add_action( 'vfbp_after_email', array( self::$instance, 'process_redirect' ), 10, 2 );
			add_action( 'vfbp_after_email', array( self::$instance, 'process_confirmation' ), 10, 2 );
		}

		return self::$instance;
	}

	/**
	 * Load public CSS files
	 *
	 * @access public
	 * @return void
	 */
	public function css() {
		$scripts = new VFB_Pro_Scripts_Loader();
		$scripts->add_css();
	}

	/**
	 * Load public JS files
	 *
	 * @access public
	 * @return void
	 */
	public function js() {
		$scripts = new VFB_Pro_Scripts_Loader();
		$scripts->add_js();
	}

	/**
	 * Handle all security checks
	 *
	 * Called in the process_email function
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function security_checks() {
		$security = new VFB_Pro_Security();

		// reCAPTCHA check
		if ( true !== $security->recaptcha_check() )
			wp_die( $security->recaptcha_check() );

		// SPAM Bot check
		if ( true !== $security->bot_check() )
			wp_die( $security->bot_check() );

		// Honeypot check
		if ( true !== $security->honeypot_check() )
			wp_die( $security->honeypot_check() );

		// CSRF check
		if ( true !== $security->csrf_check() )
			wp_die( $security->csrf_check() );
	}

	/**
	 * license_check function.
	 *
	 * @access private
	 * @return void
	 */
	public static function license_check() {
		$license = get_option( 'vfbp_license_status' );

		if ( !$license || 0 == $license )
			return false;

		return true;
	}

	/**
	 * Handle the main Email
	 *
	 * Must be hooked into 'init' so it works properly
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function process_email() {
		$email = new VFB_Pro_Email();

		// If form hasn't been submitted, exit
		if ( null == $email->submit_check() )
			return;

		// Run security checks
		self::security_checks();

		$email->email();
	}

	/**
	 * Handle the confirmation redirects
	 *
	 * Must be hooked into 'init' so it works properly
	 *
	 * @access public
	 * @return void
	 */
	public static function process_redirect( $entry_id, $form_id ) {
		$confirmation = new VFB_Pro_Confirmation( $form_id );

		// If form hasn't been submitted, exit
		if ( null == $confirmation->submit_check() )
			return;

		// WP Page
		$confirmation->wp_page();

		// Custom URL
		$confirmation->redirect();
	}

	/**
	 * Handle the Text message confirmation action
	 *
	 * Called directly from the display() function below
	 *
	 * @access public
	 * @return void
	 */
	public static function process_confirmation( $entry_id, $form_id ) {
		$confirmation = new VFB_Pro_Confirmation( $form_id );
		$templating   = new VFB_Pro_Templating();

		// If form hasn't been submitted, exit
		if ( null == $confirmation->submit_check() )
			return;

		// Save entry ID to class instance
		self::$entry_id = $entry_id;
		$message        = $templating->general( $confirmation->text(), $entry_id, $form_id );

		return $message;
	}

	/**
	 * Returns the settings to prepend text message
	 *
	 * @access public
	 * @return void
	 */
	public static function prepend_confirmation( $form_id ) {
		$confirmation = new VFB_Pro_Confirmation( $form_id );

		// If form hasn't been submitted, exit
		if ( null == $confirmation->submit_check() )
			return;

		$prepend = $confirmation->prepend_text();

		return $prepend;
	}

	/**
	 * rules function.
	 *
	 * @access public
	 * @static
	 * @param mixed $form_id
	 * @return void
	 */
	public static function rules( $form_id ) {
		if ( !$form_id )
			return;

		$vfbdb    = new VFB_Pro_Data();
		$settings = $vfbdb->get_rule_settings( $form_id );

		$rules_enable = isset( $settings['rules-enable'] ) ? $settings['rules-enable'] : '';
		$rules        = isset( $settings['rules'] ) ? json_encode( $settings['rules'] ) : '';

		if ( empty( $rules_enable ) || 0 == $rules_enable )
			return;

		wp_localize_script( 'vfbp-js', 'vfbp_rules', array( 'rules' => $rules ) );
	}

	/**
	 * A wrapper for additional functionality to be executed on form display.
	 *
	 * This is a good place for add-ons to hook into
	 *
	 * @access public
	 * @static
	 * @param mixed $form_id
	 * @return void
	 */
	public static function additional_functions( $form_id ) {
		if ( class_exists( 'VFB_Pro_Addon_Payments_Main' ) ) {
			$payments = new VFB_Pro_Addon_Payments_Main();
			$payments->init( $form_id );
		}
	}

	/**
	 * A wrapper for additional output to be added on form display.
	 *
	 * This is a good place for add-ons to hook into
	 *
	 * @access public
	 * @static
	 * @param mixed $form_id
	 * @return void
	 */
	public static function additional_output( $form_id ) {
		$output = '';

		if ( class_exists( 'VFB_Pro_Addon_Payments_Main' ) ) {
			$payments = new VFB_Pro_Addon_Payments_Main();
			$output .= $payments->running_total( $form_id );
		}

		return $output;
	}

	/**
	 * Display form
	 *
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	public static function display( $atts, $output = '' ) {
		wp_enqueue_script( 'vfbp-js' );
		wp_enqueue_script( 'parsley-js' );
		wp_enqueue_script( 'jquery-mask' );

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'vfb'
		);

		$form_id = absint( $atts['id'] );

		// License check
		if ( !self::license_check() ) {
			$settings_text = sprintf( __( 'Your VFB Pro license is UNVERIFIED. You must <a href="%s">enter your license</a> information before VFB Pro can fully function.', 'vfb-pro' ), esc_url( admin_url( 'admin.php?page=vfbp-settings' ) ) );

			return printf( '<div id="message" class="error"><p>%s</p></div>', $settings_text );
		}

		// Rules
		self::rules( $form_id );

		// Additional functions (add-ons)
		self::additional_functions( $form_id );

		// Text confirmation
		$confirm_message = self::process_confirmation( self::$entry_id, $form_id );

		// If text message is returned, output
		if ( null !== $confirm_message ) {
			// If prepend setting is true, just echo
			if ( self::prepend_confirmation( $form_id ) ) {
				$output .= $confirm_message;
			}
			else {
				return $confirm_message;
			}
		}

		$vfbdb  = new VFB_Pro_Data();
		$form   = $vfbdb->get_form_by_id( $form_id );
		$fields = $vfbdb->get_fields( $form_id );

		$label_alignment = isset( $form['data']['label-alignment'] ) && !empty( $form['data']['label-alignment'] ) ? ' vfbp-form-horizontal' : '';
		$expiration      = isset( $form['data']['expiration'] ) ? strtotime( $form['data']['expiration'] ) : '';
		$expiration_msg  = isset( $form['data']['expiration-message'] ) ? $form['data']['expiration-message'] : '';
		$limit           = isset( $form['data']['limit'] ) ? absint( $form['data']['limit'] ) : '';
		$limit_msg       = isset( $form['data']['limit-message'] ) ? $form['data']['limit-message'] : '';
		$save_state      = isset( $form['data']['save-state'] ) ? $form['data']['save-state'] : '';
		$on_submit       = isset( $form['data']['on-submit'] ) ? $form['data']['on-submit'] : '';
		$page_title_display = isset( $form['data']['page-title-display'] ) ? $form['data']['page-title-display'] : 'false'; // Use string 'false' due to JS type conversion
		$page_title_click   = isset( $form['data']['page-title-click']   ) ? $form['data']['page-title-click']   : 'false'; // Use string 'false' due to JS type conversion
		$page_num_display   = isset( $form['data']['page-num-display']   ) ? $form['data']['page-num-display']   : 'false'; // Use string 'false' due to JS type conversion
		//$csrf_protection    = isset( $form['data']['csrf-setting'] ) ? $form['data']['csrf-setting'] : ''; use this after testing!
		$csrf_protection    = isset( $form['data']['csrf-protection'] ) ? $form['data']['csrf-protection'] : ''; // delete this after testing!
		$open_page       = false;
		$open_background = false;
		$page            = 1;
		$background      = 1;
		$cols_total      = 0;

		// Hide the form if not set to 'publish'
		if ( isset( $form['status'] ) && 'draft' == $form['status'] ) {
			// Don't hide drafts when previewing
			if ( !isset( $_GET['vfb-form-id'] ) && !isset( $_GET['preview'] ) )
				return;
		}

		// Load jQuery Cookie script
		if ( !empty( $save_state ) ) {
			wp_enqueue_script( 'jquery-phoenix' );
		}

		// Load custom ParsleyJS validation messages
		$validation_settings = get_option( 'vfbp_settings' );
		if ( isset( $validation_settings['custom-validation-msgs'] ) && 1 == $validation_settings['custom-validation-msgs'] ) {
			$validation_messages = array(
				'defaultMsg'    => $validation_settings['validation-msg-default'],
				'email'         => $validation_settings['validation-msg-email'],
				'url'           => $validation_settings['validation-msg-url'],
				'number'        => $validation_settings['validation-msg-number'],
				'integer'       => $validation_settings['validation-msg-integer'],
				'digits'        => $validation_settings['validation-msg-digits'],
				'alphanum'      => $validation_settings['validation-msg-alphanum'],
				'notblank'      => $validation_settings['validation-msg-notblank'],
				'required'      => $validation_settings['validation-msg-required'],
				'pattern'       => $validation_settings['validation-msg-pattern'],
				'min'           => $validation_settings['validation-msg-min'],
				'max'           => $validation_settings['validation-msg-max'],
				'range'         => $validation_settings['validation-msg-range'],
				'minlength'     => $validation_settings['validation-msg-minlength'],
				'maxlength'     => $validation_settings['validation-msg-maxlength'],
				'lengthMsg'     => $validation_settings['validation-msg-length'],
				'mincheck'      => $validation_settings['validation-msg-mincheck'],
				'maxcheck'      => $validation_settings['validation-msg-maxcheck'],
				'check'         => $validation_settings['validation-msg-check'],
				'equalto'       => $validation_settings['validation-msg-equalto'],
				'minwords'      => $validation_settings['validation-msg-minwords'],
				'maxwords'      => $validation_settings['validation-msg-maxwords'],
				'words'         => $validation_settings['validation-msg-words'],
				'gt'            => $validation_settings['validation-msg-gt'],
				'gte'           => $validation_settings['validation-msg-gte'],
				'lt'            => $validation_settings['validation-msg-lt'],
				'lte'           => $validation_settings['validation-msg-lte'],
			);

			wp_enqueue_script( 'parsley-js-custom' );
			wp_localize_script( 'parsley-js-custom', 'vfbp_validation_custom', array( 'vfbp_messages' => $validation_messages ) );
		}

		// Form Expiration
		$current_time = current_time( 'timestamp' );
		if ( !empty( $expiration ) && $current_time >= $expiration ) {
			return $expiration_msg;
		}

		// Limit Entries
		if ( !empty( $limit ) ) {
			$entries = $vfbdb->get_entries_count( $form_id );
			if ( $entries >= $limit ) {
				return $limit_msg;
			}
		}

		/**
		 * Filter the form "action" attribute
		 *
		 * Changing this value to anything but the current page or a blank value
		 * will disable VFB Pro email and entry saving features.
		 *
		 * This filter is best used when you want to build your own processing script to handle the values.
		 *
		 * @since 3.0
		 *
		 */
		$form_action = apply_filters( 'vfbp_form_action', '', $form_id );

		/**
		 * Filter the form "enctype" attribute
		 *
		 * Changing this value will affect whether or not File Upload fields
		 * will continue to function.
		 *
		 * @since 3.0
		 *
		 */
		$form_enctype = apply_filters( 'vfbp_form_enctype', 'multipart/form-data', $form_id );

		$form_atts   = array(
			'class'    => "vfbp-form{$label_alignment}",
			'id'       => "vfbp-form-$form_id",
			'enctype'  => $form_enctype,
			'onSubmit' => $on_submit,
		);

		$builder = new VFB_Pro_Form_Builder( $form_id, $form_action, $form_atts );

		$output .= $builder->open();

		// Add CSRF token to the form, if setting is enabled
		if ( !empty( $csrf_protection ) ) {
			$output .= $builder->token();
		}

		// Additional output (add-ons)
		$output .= self::additional_output( $form_id );

		foreach ( $fields as $field ) {
			$name                = 'vfb-field-' . $field['id'];
			$id                  = $name;
			$field_type          = $field['field_type'];
			$label               = isset( $field['data']['label'] )         ? $field['data']['label']         : '';
			$description         = isset( $field['data']['description'] )   ? $field['data']['description']   : '';
			$desc_position       = isset( $field['data']['description-position'] ) ? $field['data']['description-position'] : '';
			$default             = isset( $field['data']['default_value'] ) ? $field['data']['default_value'] : '';
			$required            = isset( $field['data']['required'] )      ? $field['data']['required']      : '';
			$placeholder         = isset( $field['data']['placeholder'] )   ? $field['data']['placeholder']   : '';
			$input_mask          = isset( $field['data']['input-mask'] )    ? $field['data']['input-mask']    : '';
			$css                 = isset( $field['data']['css'] )           ? $field['data']['css']           : '';
			$cols_layout         = isset( $field['data']['cols'] ) && !empty( $field['data']['cols'] ) ? $field['data']['cols'] : 12;
			$cols_options        = isset( $field['data']['cols-options'] )  ? $field['data']['cols-options']  : '';
			$heading_type        = isset( $field['data']['heading-type'] )  ? $field['data']['heading-type']  : 'div';
			$heading_bg          = isset( $field['data']['heading-bg'] )    ? $field['data']['heading-bg']    : '';
			$hidden              = isset( $field['data']['hidden'] )        ? $field['data']['hidden']        : '';

			$validation_settings = isset( $field['data']['validation'] )    ? $field['data']['validation']    : '';
			$address_settings    = isset( $field['data']['address'] )       ? $field['data']['address']       : '';
			$date_settings       = isset( $field['data']['date'] )          ? $field['data']['date']          : '';
			$time_settings       = isset( $field['data']['time'] )          ? $field['data']['time']          : '';
			$currency_settings   = isset( $field['data']['currency'] )      ? $field['data']['currency']      : '';
			$phone_settings      = isset( $field['data']['phone'] )         ? $field['data']['phone']         : '';
			$password_settings   = isset( $field['data']['password'] )      ? $field['data']['password']      : '';
			$file_settings       = isset( $field['data']['file'] )          ? $field['data']['file']          : '';
			$name_settings       = isset( $field['data']['name'] )          ? $field['data']['name']          : '';
			$rating_settings     = isset( $field['data']['rating'] )        ? $field['data']['rating']        : '';
			$captcha_settings    = isset( $field['data']['captcha'] )       ? $field['data']['captcha']       : '';
			$slider_settings     = isset( $field['data']['range-slider'] )  ? $field['data']['range-slider']  : '';
			$likert_settings	 = isset( $field['data']['likert'] )        ? $field['data']['likert']        : '';
			$knob_settings	     = isset( $field['data']['knob'] )          ? $field['data']['knob']          : '';
			$country             = isset( $field['data']['country'] )       ? $field['data']['country']       : 'US';
			$min_words			 = isset( $field['data']['min-words'] )     ? $field['data']['min-words']     : '';
			$max_words			 = isset( $field['data']['max-words'] )     ? $field['data']['max-words']     : '';
			$textarea_rows	     = isset( $field['data']['textarea-rows'] ) ? $field['data']['textarea-rows'] : 10;
			$min_num			 = isset( $field['data']['min-num'] )       ? $field['data']['min-num']       : '';
			$max_num			 = isset( $field['data']['max-num'] )       ? $field['data']['max-num']       : '';

			/**
			 * Filter the field's Name property
			 *
			 * Changing this value will alter the label that is displayed
			 * and will most commonly be used by WPML users.
			 *
			 * @since 3.0
			 *
			 */
			$label = apply_filters( 'vfbp_field_name', $label, $field['id'], $form_id );

			/**
			 * Filter the field's Description property
			 *
			 * Changing this value will alter the description that is displayed
			 * and will most commonly be used by WPML users.
			 *
			 * @since 3.0
			 *
			 */
			$description = apply_filters( 'vfbp_field_description', $description, $field['id'], $form_id );

			/**
			 * Filter the field's default value property
			 *
			 * Changing this value will alter the default value that is displayed
			 * and will most commonly be used by WPML users.
			 *
			 * @since 3.0
			 *
			 */
			$default = apply_filters( 'vfbp_field_default', $default, $field['id'], $form_id );

			/**
			 * Filter the field's Placeholder property
			 *
			 * Changing this value will alter the placeholder text that is displayed
			 * and will most commonly be used by WPML users.
			 *
			 * @since 3.0
			 *
			 */
			$placeholder = apply_filters( 'vfbp_field_placeholder', $placeholder, $field['id'], $form_id );

			$options = array(
				'id'            => $id,
				'class'         => 'vfb-form-control',
				'placeholder'   => $placeholder,
			);

			// Setup option arrays for use in horizontal label alignment
			$label_opts = $horizontal_opts = array();
			if ( !empty( $label_alignment ) ) {
				$label_opts['class']      = 'vfb-col-2 vfb-control-label';
				$horizontal_opts['class'] = 'vfb-col-10';
			}
			else {
				$label_opts['class'] = 'vfb-control-label';
			}

			// Input mask
			if ( !empty( $input_mask ) )
				$options['data-mask'] = $input_mask;

			// Required
			if ( !empty( $required ) || 'captcha' == $field_type ) {
				$label .= ' <span class="vfb-required-asterisk">*</span>';
				$options['required'] = 'required';
			}

			// CSS Classes
			if ( !empty( $css ) )
				$options['class'] .= " $css";

			$desc_output = '';
			// Description (with HTML wrapper)
			if ( !empty( $description ) )
				$desc_output = $builder->description( $description, array( 'class' => 'vfb-help-block' ) );

			// Validation : type
			if ( !empty( $validation_settings['type'] ) )
				$options['data-vfb-type'] = $validation_settings['type'];

			// Validation : pattern
			if ( !empty( $validation_settings['pattern'] ) )
				$options['data-vfb-pattern'] = $validation_settings['pattern'];

			// Validation : equalto
			if ( !empty( $validation_settings['equalto'] ) )
				$options['data-vfb-equalto'] = '#vfb-field-' . absint( $validation_settings['equalto'] );

			// Validation : minlength
			if ( !empty( $validation_settings['minlength'] ) )
				$options['data-vfb-minlength'] = absint( $validation_settings['minlength'] );

			// Validation : maxlength
			if ( !empty( $validation_settings['maxlength'] ) )
				$options['data-vfb-maxlength'] = absint( $validation_settings['maxlength'] );

			// Validation : minlength
			if ( !empty( $validation_settings['length'] ) )
				$options['data-vfb-length'] = sprintf( '[%s]', $validation_settings['length'] );

			// Validation : min
			if ( !empty( $validation_settings['min'] ) )
				$options['data-vfb-min'] = absint( $validation_settings['min'] );

			// Validation : max
			if ( !empty( $validation_settings['max'] ) )
				$options['data-vfb-max'] = absint( $validation_settings['max'] );

			// Validation : range
			if ( !empty( $validation_settings['range'] ) )
				$options['data-vfb-range'] = sprintf( '[%s]', $validation_settings['range'] );

			// Validation : gt
			if ( !empty( $validation_settings['gt'] ) )
				$options['data-vfb-gt'] = '#vfb-field-' . absint( $validation_settings['gt'] );

			// Validation : gte
			if ( !empty( $validation_settings['gte'] ) )
				$options['data-vfb-gte'] = '#vfb-field-' . absint( $validation_settings['gte'] );

			// Validation : lt
			if ( !empty( $validation_settings['lt'] ) )
				$options['data-vfb-lt'] = '#vfb-field-' . absint( $validation_settings['lt'] );

			// Validation : lte
			if ( !empty( $validation_settings['lte'] ) )
				$options['data-vfb-lte'] = '#vfb-field-' . absint( $validation_settings['lte'] );

			//////////////////
			// !Page Break
			//////////////////
			if ( 'page-break' == $field_type ) {
				wp_enqueue_script( 'vfb-steps' );

				// Only output one localize script
				if ( $page == 1 ) {
					wp_localize_script( 'vfb-steps', 'vfbp_pageBreak_L10n', array(
						'btnPrev'      => __( 'Previous', 'vfb-pro' ),
						'btnNext'      => __( 'Next', 'vfb-pro' ),
						'titleDisplay' => $page_title_display,
						'titleClick'   => $page_title_click,
						'numDisplay'   => $page_num_display,
					));
				}

				if ( $page >= 2 ) {
					// Close last Background
					if ( $open_background == true && $background >= 2 ) {
						$output .= '</div> <!-- .vfb-well -->';
						$open_background = false;
						$background = 1;
					}

					$output .= '</section>';
					$open_page = false;
				}

				$output .= '<section class="vfb-page-section">';
				$output .= '<h3 class="vfb-page-title">' . $label . '</h3>';

				if ( $open_page == true )
					$open_page = false;

				$open_page = true;
				$page++;
			}

			//////////////////
			// !Heading
			//////////////////
			if ( 'heading' == $field_type ) {
				// Background container
				if ( !empty( $heading_bg ) ) {
					if ( $background >= 2 ) {
						$output          .= '</div> <!-- .vfb-well -->';
						$open_background  = false;
					}

					$output .= sprintf( '<div class="vfb-well vfb-col-12" id="vfbField%d">', $field['id'] );

					$open_background = true;
					$background++;
				}

				// Open <div>
				$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Heading
						$output .= $builder->heading( $heading_type, $label, array( 'class' => $css ) );

						// Description
						$output .= do_shortcode( $desc_output );

					// Close </div>
					$output .= $builder->elem_close();

				// Close </div>
				$output .= $builder->elem_close();
			}

			$wrapper_id = 'vfbField' . $field['id'];

			// Open Layout column
			$cols_total 		  += $cols_layout;
			$css_layout_cols       = 'vfb-col-' . $cols_layout;
			$css_layout_field_type = 'vfb-fieldType-' . $field_type;
			$output .= $builder->elem_open( array( 'class' => "$css_layout_cols $css_layout_field_type", 'id' => $wrapper_id ) );

			switch ( $field_type ) {
				//////////////////
				// !Text
				//////////////////
				case 'text' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->text( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Textarea
				//////////////////
				case 'textarea' :

					// If Min and Max words are set, use Range words
					if ( !empty( $min_words ) && !empty( $max_words ) ) {
						$options['data-vfb-words'] = "[$min_words,$max_words]";
					}
					// If Min is empty and Max is set, use Max words
					elseif ( empty( $min_words ) && !empty( $max_words ) ) {
						$options['data-vfb-maxwords'] = "$max_words";
					}
					// If Min is set and Max is empty, use Min words
					elseif ( !empty( $min_words ) && empty( $max_words ) ) {
						$options['data-vfb-minwords'] = "$min_words";
					}

					if ( !empty( $textarea_rows ) )
						$options['rows'] = $textarea_rows;

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->textarea( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Radio
				//////////////////
				case 'radio' :
					$radio_options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';
					$allow_other   = isset( $field['data']['allow-other'] ) ? $field['data']['allow-other'] : '';

					// Flag to make sure extra validation options are only added to the first radio
					$radio_validations = true;

					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Inline wrapper open
						if ( !empty( $cols_options ) )
							$output .= $builder->elem_open( array( 'class' => 'vfb-inline-group' ) );

						if ( is_array( $radio_options ) && !empty( $radio_options ) ) {
							foreach ( $radio_options as $index => $radio ) {
								$label            = isset( $radio['label'] ) ? $radio['label'] : '';
								$checked          = isset( $radio['default'] ) ? true : false;
								$options['class'] = str_replace( 'vfb-form-control', '', $options['class'] );
								$options['id']    = "{$name}-{$index}";

								// Only add to first radio
								if ( true == $radio_validations ) {
									// Add required back, but only to the first one
									if ( !empty( $required ) )
										$options['required'] = 'required';
								}
								// Open <div>
								$output .= $builder->elem_open( array( 'class' => 'vfb-radio' ) );

								$output .= '<label>';
									// Input
									$output .= $builder->radio( $name, $label, $checked, $options );

									// Option name
									$output .= $label;
								$output .= '</label>';

								// Close </div>
								$output .= $builder->elem_close();

								// Unset extra options so they aren't added to the remaining checks
								unset( $options['required'] );
							}
						}

						if ( !empty( $allow_other ) ) {
							$options['class']  = str_replace( 'vfb-form-control', '', $options['class'] );
							$options['id']     = "{$name}-" . ++$index;
							$allow_other_input = isset( $field['data']['allow-other-input'] ) ? $field['data']['allow-other-input'] : '';

							// Open <div>
							$output .= $builder->elem_open( array( 'class' => 'vfb-radio' ) );

							$output .= '<label>';
								// Input
								$output .= $builder->radio( $name, $allow_other_input, '', $options );

								// Option name
								$output .= $allow_other_input;

								$output .= $builder->text( $name . '-other', '', array( 'class' => 'vfb-form-control', 'id' => $name . '-other' ) );
							$output .= '</label>';

							// Close </div>
							$output .= $builder->elem_close();
						}

						// Inline wrapper close
						if ( !empty( $cols_options ) )
							$output .= $builder->elem_close();

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Checkbox
				//////////////////
				case 'checkbox' :
					$check_options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';

					// Flag to make sure extra validation options are only added to the first checkbox
					$check_validations = true;

					// Remove required option so we can only add it to the first option
					unset( $options['required'] );

					// Set a Parsley validation indentifier
					// There's a bug with ParsleyJS where it won't use the namespace, so fallback to using Parsley
					$options['data-parsley-multiple'] = 'vfb-field-' . $field['id'];

					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Inline wrapper open
						if ( !empty( $cols_options ) )
							$output .= $builder->elem_open( array( 'class' => 'vfb-inline-group' ) );

						if ( is_array( $check_options ) && !empty( $check_options ) ) {
							foreach ( $check_options as $index => $check ) {
								$label            = isset( $check['label'] ) ? $check['label'] : '';
								$checked          = isset( $check['default'] ) ? true : false;
								$options['class'] = str_replace( 'vfb-form-control', '', $options['class'] );
								$options['id']    = "{$name}-{$index}";

								// Only add to first checkbox
								if ( true == $check_validations ) {
									// Add required back, but only to the first one
									if ( !empty( $required ) )
										$options['required'] = 'required';

									// Validation : mincheck
									if ( !empty( $validation_settings['mincheck'] ) )
										$options['data-vfb-mincheck'] = absint( $validation_settings['mincheck'] );

									// Validation : maxcheck
									if ( !empty( $validation_settings['maxcheck'] ) )
										$options['data-vfb-maxcheck'] = absint( $validation_settings['maxcheck'] );

									// Validation : check
									if ( !empty( $validation_settings['check'] ) )
										$options['data-vfb-check'] = sprintf( '[%s]', $validation_settings['check'] );

									$check_validations = false;
								}

								// Open <div>
								$output .= $builder->elem_open( array( 'class' => 'vfb-checkbox' ) );

								$output .= '<label>';
									// Input
									$output .= $builder->checkbox( $name . "[$index]", 1, $checked, $options );

									// Option name
									$output .= $label;
								$output .= '</label>';

								// Close </div>
								$output .= $builder->elem_close();

								// Unset extra options so they aren't added to the remaining checks
								unset( $options['required'] );
								unset( $options['data-vfb-mincheck'] );
								unset( $options['data-vfb-maxcheck'] );
								unset( $options['data-vfb-check'] );
							}
						}

						// Inline wrapper close
						if ( !empty( $cols_options ) )
							$output .= $builder->elem_close();

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Select
				//////////////////
				case 'select' :
					$select_options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';

					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Open <div>
						$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						$selects = array();

						if ( is_array( $select_options ) && !empty( $select_options ) ) {
							foreach ( $select_options as $index => $select ) {
								$label            = isset( $select['label'] ) ? $select['label'] : '';
								$selected         = isset( $select['default'] ) ? $label : null;

								$selects[ $index ]['value']    = $label;
								$selects[ $index ]['selected'] = $selected;
							}
						}

						// Input
						$output .= $builder->select( $name, $selects, $options );

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Address
				//////////////////
				case 'address' :
					wp_enqueue_script( 'jquery-addressfield' );
					wp_enqueue_script( 'jquery-addressfield-json' );

					$hide_addr_2  = isset( $address_settings['hide-addr-2'] )  ? $address_settings['hide-addr-2']  : '';
					$hide_country = isset( $address_settings['hide-country'] ) ? $address_settings['hide-country'] : '';
					$hide_city    = isset( $address_settings['hide-city'] )    ? $address_settings['hide-city']    : '';
					$hide_state   = isset( $address_settings['hide-state'] )   ? $address_settings['hide-state']   : '';
					$hide_zip     = isset( $address_settings['hide-zip'] )     ? $address_settings['hide-zip']     : '';

					$address_parts = array(
					    'address-1' => __( 'Street Address', 'vfb-pro' ),
					    'address-2' => __( 'Apt, suite, etc.', 'vfb-pro' ),
					    'country'   => __( 'Country', 'vfb-pro' ),
					    'city'      => __( 'City', 'vfb-pro' ),
					    'province'  => __( 'State', 'vfb-pro' ),
					    'zip'       => __( 'Zip code', 'vfb-pro' ),
					);

					if ( !empty( $hide_addr_2 ) )
						unset( $address_parts['address-2'] );

					if ( !empty( $hide_city ) )
						unset( $address_parts['city'] );

					if ( !empty( $hide_state ) )
						unset( $address_parts['province'] );

					if ( !empty( $hide_zip ) )
						unset( $address_parts['zip'] );

					// Set special address class on horizontal div
					// Address plugin needs a closer container than the vfb-form-group div
					if ( isset( $horizontal_opts['class'] ) )
						$horizontal_opts['class'] .= ' vfb-address-block';
					else
						$horizontal_opts['class'] = 'vfb-address-block';

					// Include the field ID on horizontal div
					// Used by the JS to localize address block on page load
					$horizontal_opts['id'] = $id;

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->address( $name, $country, $address_parts, $options, $hide_country );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Date
				//////////////////
				case 'date' :
					wp_enqueue_script( 'jquery-datepicker' );

					$format             = isset( $date_settings['format'] ) ? $date_settings['format'] : '';
					$disabled_days_week = isset( $date_settings['days-of-week-disabled'] ) ? $date_settings['days-of-week-disabled'] : '';
					$start_date         = isset( $date_settings['start-date'] ) ? $date_settings['start-date'] : '';
					$end_date           = isset( $date_settings['end-date'] ) ? $date_settings['end-date'] : '';
					$week_start         = isset( $date_settings['week-start'] ) ? $date_settings['week-start'] : '';
					$start_view         = isset( $date_settings['start-view'] ) ? $date_settings['start-view'] : '';
					$min_view_mode      = isset( $date_settings['min-view-mode'] ) ? $date_settings['min-view-mode'] : '';
					$today_btn          = isset( $date_settings['today-btn'] ) ? $date_settings['today-btn'] : '';
					$language           = isset( $date_settings['language'] ) ? $date_settings['language'] : '';
					$orientation        = isset( $date_settings['orientation'] ) ? $date_settings['orientation'] : '';
					$autoclose          = isset( $date_settings['autoclose'] ) ? $date_settings['autoclose'] : '';
					$calendar_wks       = isset( $date_settings['calendar-weeks'] ) ? $date_settings['calendar-weeks'] : '';
					$today_highlight    = isset( $date_settings['today-highlight'] ) ? $date_settings['today-highlight'] : '';

					$options['data-provide']  = 'datepicker';

					if ( !empty( $format ) )
						$options['data-date-format'] = $format;

					if ( !empty( $disabled_days_week ) )
						$options['data-date-days-of-week-disabled'] = $disabled_days_week;

					if ( !empty( $start_date ) )
						$options['data-date-start-date'] = $start_date;

					if ( !empty( $end_date ) )
						$options['data-date-end-date'] = $end_date;

					if ( !empty( $week_start ) )
						$options['data-date-week-start'] = $week_start;

					if ( !empty( $start_view ) )
						$options['data-date-start-view'] = $start_view;

					if ( !empty( $min_view_mode ) )
						$options['data-date-min-view-mode'] = $min_view_mode;

					if ( !empty( $today_btn ) )
						$options['data-date-today-btn'] = $today_btn;

					if ( !empty( $language ) && 'en' !== $language ) {
						wp_enqueue_script( 'jquery-datepicker-i18n-' . $language );
						$options['data-date-language'] = $language;
					}

					if ( !empty( $orientation ) )
						$options['data-date-orientation'] = $orientation;

					if ( !empty( $autoclose ) )
						$options['data-date-autoclose'] = $autoclose;

					if ( !empty( $calendar_wks ) )
						$options['data-date-calendar-weeks'] = $calendar_wks;

					if ( !empty( $today_highlight ) )
						$options['data-date-today-highlight'] = $today_highlight;

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->date( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Email
				//////////////////
				case 'email' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->email( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !URL
				//////////////////
				case 'url' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->url( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Currency
				//////////////////
				case 'currency' :
					wp_enqueue_script( 'jquery-autonumeric' );

					$sep = isset( $currency_settings['sep'] ) ? $currency_settings['sep'] : ',';
					$dec = isset( $currency_settings['dec'] ) ? $currency_settings['dec'] : '.';
					$group = isset( $currency_settings['group'] ) ? $currency_settings['group'] : 3;
					$sign = isset( $currency_settings['sign'] ) ? $currency_settings['sign'] : '';
					$sign_place = isset( $currency_settings['sign-place'] ) ? $currency_settings['sign-place'] : 'p';
					$sign_display = isset( $currency_settings['sign-display'] ) ? $currency_settings['sign-display'] : '';

					if ( !empty( $sep ) ) {
						switch( $sep ) {
							case 'slash' :
								$sep_char = '\\';
								break;

							case 'period' :
								$sep_char = '.';
								break;

							case 'space' :
								$sep_char = ' ';
								break;

							case 'none' :
								$sep_char = '';
								break;

							case 'comma' :
							default :
								$sep_char = ',';
								break;
						}

						$options['data-a-sep'] = $sep_char;
					}

					if ( !empty( $dec ) ) {
						switch( $dec ) {
							case 'comma' :
								$dec_char = ',';
								break;

							case 'period' :
							default :
								$dec_char = '.';
								break;
						}
						$options['data-a-dec'] = $dec_char;
					}

					if ( !empty( $group ) )
						$options['data-d-group'] = $group;

					if ( !empty( $sign ) )
						$options['data-a-sign'] = $sign;

					if ( !empty( $sign_place ) )
						$options['data-p-sign'] = $sign_place;

					if ( !empty( $sign_display ) )
						$options['data-w-empty'] = $sign_display;

					$options['class'] .= ' vfb-currency';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->currency( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Number
				//////////////////
				case 'number' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->number( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Time
				//////////////////
				case 'time' :
					wp_enqueue_script( 'jquery-clockpicker' );

					$donetext   = isset( $time_settings['donetext'] ) && !empty( $time_settings['donetext'] ) ? $time_settings['donetext'] : __( 'Done', 'vfb-pro' );
					$placement  = isset( $time_settings['placement'] ) ? $time_settings['placement'] : '';
					$align      = isset( $time_settings['align'] ) ? $time_settings['align'] : '';
					$autoclose  = isset( $time_settings['autoclose'] ) ? $time_settings['autoclose'] : '';
					$twelvehour = isset( $time_settings['twelvehour'] ) ? $time_settings['twelvehour'] : '';

					$options['class'] .= ' vfb-clockpicker';

					// Always display 'Done' text
					$options['data-donetext'] = $donetext;

					if ( !empty( $placement ) )
						$options['data-placement'] = $placement;

					if ( !empty( $align ) )
						$options['data-align'] = $align;

					if ( !empty( $autoclose ) )
						$options['data-autoclose'] = $autoclose;

					if ( !empty( $twelvehour ) )
						$options['data-twelvehour'] = $twelvehour;

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->time( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Phone
				//////////////////
				case 'phone' :
					wp_enqueue_script( 'jquery-intl-tel' );

					$default_country = isset( $phone_settings['default-country'] ) ? $phone_settings['default-country'] : 'us';
					$num_type        = isset( $phone_settings['type'] ) ? $phone_settings['type'] : '';
					$hide_flags      = isset( $phone_settings['hide-flags'] ) ? $phone_settings['hide-flags'] : '';
					$nation_mode     = isset( $phone_settings['nation-mode'] ) ? $phone_settings['nation-mode'] : '';
					$hide_dial_code  = isset( $phone_settings['hide-dial-code'] ) ? $phone_settings['hide-dial-code'] : '';

					if ( !empty( $default_country ) )
						$options['data-default-country'] = strtolower( $default_country );

					if ( !empty( $num_type ) )
						$options['data-number-type'] = $num_type;

					if ( !empty( $hide_flags ) )
						$options['data-hide-flags'] = true;

					if ( !empty( $nation_mode ) )
						$options['data-national-mode'] = true;

					if ( !empty( $hide_dial_code ) )
						$options['data-auto-hide-dial-code'] = true;

					$options['class'] .= ' vfb-intl-phone';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->phone( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !HTML
				//////////////////
				case 'html' :
					$options = array(
						'media_buttons' => false,
						'textarea_name' => $name,
						'textarea_rows' => 8,
					);

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Use output buffering to capture wp_editor
							ob_start();

							// Input
							$builder->html( $name, $default, $options );

							$output .= ob_get_clean();

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !File Upload
				//////////////////
				case 'file-upload' :
					wp_enqueue_script( 'vfb-jquery-fileupload' );

					$max_file_size      = isset( $file_settings['max-file-size'] ) ? $file_settings['max-file-size'] : 0;
					$max_file_count     = isset( $file_settings['max-file-count'] ) ? $file_settings['max-file-count'] : 0;
					$allowed_file_types = isset( $file_settings['allowed-file-types'] ) ? $file_settings['allowed-file-types'] : '';
					$allowed_file_ext   = isset( $file_settings['allowed-file-ext'] ) ? $file_settings['allowed-file-ext'] : '';
					$label_browse       = isset( $file_settings['label-browse'] ) ? $file_settings['label-browse'] : __( 'Browse', 'vfb-pro' );
					$label_remove       = isset( $file_settings['label-remove'] ) ? $file_settings['label-remove'] : __( 'Remove', 'vfb-pro' );
					$label_upload       = isset( $file_settings['label-upload'] ) ? $file_settings['label-upload'] : __( 'Upload', 'vfb-pro' );
					$hide_preview       = isset( $file_settings['hide-preview'] ) ? $file_settings['hide-preview'] : '';
					$hide_remove        = isset( $file_settings['hide-remove'] ) ? $file_settings['hide-remove'] : '';
					$hide_caption       = isset( $file_settings['hide-caption'] ) ? $file_settings['hide-caption'] : '';
					$show_upload        = isset( $file_settings['show-upload'] ) ? $file_settings['show-upload'] : '';
					$multiple           = isset( $file_settings['multiple'] ) ? $file_settings['multiple'] : '';

					if ( !empty( $max_file_size ) )
						$options['data-max-file-size'] = absint( $max_file_size );

					if ( !empty( $max_file_count ) )
						$options['data-max-file-count'] = absint( $max_file_count );

					if ( !empty( $allowed_file_types ) )
						$options['data-allowed-file-types'] = preg_replace( '/\s+/', '', $allowed_file_types );

					if ( !empty( $allowed_file_ext ) )
						$options['data-allowed-file-extensions'] = preg_replace( '/\s+/', '', $allowed_file_ext );

					if ( !empty( $label_browse ) )
						$options['data-browse-label'] = $label_browse;

					if ( !empty( $label_remove ) )
						$options['data-remove-label'] = $label_remove;

					if ( !empty( $label_upload ) )
						$options['data-upload-label'] = $label_upload;

					if ( !empty( $hide_preview ) )
						$options['data-show-preview'] = 'false'; // Use string 'false' due to JS type conversion

					if ( !empty( $hide_remove ) )
						$options['data-show-remove'] = 'false'; // Use string 'false' due to JS type conversion

					if ( !empty( $hide_caption ) )
						$options['data-show-caption'] = 'false'; // Use string 'false' due to JS type conversion

					if ( !empty( $show_upload ) )
						$options['data-show-upload'] = true;

					if ( !empty( $multiple ) ) {
						$name = $name . '[]';
						$options['multiple'] = true;
					}

					$options['class'] .= ' vfb-file-input';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->file( $name, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Instructions
				//////////////////
				case 'instructions' :
					// Make sure custom CSS classes are added
					if ( isset( $horizontal_opts['class'] ) )
						$horizontal_opts['class'] .= $css;
					else
						$horizontal_opts['class'] = $css;

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							$desc_output = wpautop( wptexturize( $desc_output ) );

							// Description
							$output .= do_shortcode( $desc_output );

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Username
				//////////////////
				case 'username' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->user( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Password
				//////////////////
				case 'password' :
					wp_enqueue_script( 'zxcvbn-async' );
					wp_enqueue_script( 'vfb-pw-meter' );

					$hide_meter     = isset( $password_settings['hide-meter'] )   ? $password_settings['hide-meter']   : '';
					$hide_verdict   = isset( $password_settings['hide-verdict'] ) ? $password_settings['hide-verdict'] : '';
					$verdict_inside = isset( $password_settings['verdict-inside'] ) ? $password_settings['verdict-inside'] : '';

					$short_text   = isset( $password_settings['text-0'] ) && !empty( $password_settings['text-0'] ) ? $password_settings['text-0'] : __( 'Too Short', 'vfb-pro' );
					$weakest_text = isset( $password_settings['text-1'] ) && !empty( $password_settings['text-1'] ) ? $password_settings['text-1'] : __( 'Very Weak', 'vfb-pro' );
					$weak_text    = isset( $password_settings['text-2'] ) && !empty( $password_settings['text-2'] ) ? $password_settings['text-2'] : __( 'Weak', 'vfb-pro' );
					$medium_text  = isset( $password_settings['text-3'] ) && !empty( $password_settings['text-3'] ) ? $password_settings['text-3'] : __( 'Medium', 'vfb-pro' );
					$strong_text  = isset( $password_settings['text-4'] ) && !empty( $password_settings['text-4'] ) ? $password_settings['text-4'] : __( 'Strong', 'vfb-pro' );

					if ( !empty( $hide_meter ) )
						$options['data-hide-progress-bar'] = true;

					if ( !empty( $hide_verdict ) )
						$options['data-hide-verdict'] = true;

					if ( !empty( $verdict_inside ) )
						$options['data-show-verdict-inside'] = true;

					$options['data-verdicts'] = implode( ',', array( $short_text, $weakest_text, $weak_text, $medium_text, $strong_text ) );

					$options['class'] .= ' vfb-password';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->password( $name, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Hidden
				//////////////////
				case 'hidden' :
					$hidden_option  = isset( $hidden['option'] ) ? $hidden['option'] : '';
					$hidden_value   = '';
					$current_user   = wp_get_current_user();

					if ( !empty( $hidden_option ) ) {
						switch( $hidden_option ) {
							case 'form_id' :
								$hidden_value = $form_id;
								break;

							case 'form_title' :
								$hidden_value = $form['title'];
								break;

							case 'ip' :
								$hidden_value = esc_attr( $_SERVER['REMOTE_ADDR'] );
								break;

							case 'uid' :
								$hidden_value = uniqid();
								break;

							case 'sequential-num' :
								$field_id    = $field['id'];
								$seq_num_opt = "vfb-hidden-sequential-num-{$form_id}-{$field_id}";
								$seq_num     = get_option( $seq_num_opt );
								$seq_start   = isset( $hidden['seq-start'] ) ? absint( $hidden['seq-start'] ) : 1000;
								$seq_step    = isset( $hidden['seq-step']  ) ? absint( $hidden['seq-step']  ) : 1;

								if ( !$seq_num ) {
									add_option( $seq_num_opt, $seq_start );
									$hidden_value = $seq_start;
								}
								else {
									$hidden_value = absint( $seq_num ) + $seq_step;
								}

								break;

							case 'date-today' :
								$hidden_value = date_i18n( get_option( 'date_format' ), current_time( 'timestamp' ) );
								break;

							case 'current-time' :
								$hidden_value = date_i18n( get_option( 'time_format' ), current_time( 'timestamp' ) );
								break;

							case 'post_id' :
								$hidden_value = get_the_id();
								break;

							case 'post_title' :
								$hidden_value = get_the_title();
								break;

							case 'post_url' :
								$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : substr( $_SERVER['PHP_SELF'], 1 );
								$hidden_value = site_url( wp_unslash( $request_uri ) );
								break;

							case 'current_user_id' :
								$hidden_value = $current_user instanceof WP_User ? $current_user->ID : '';
								break;

							case 'current_user_name' :
								$hidden_value = $current_user instanceof WP_User ? $current_user->display_name : '';
								break;

							case 'current_user_username' :
								$hidden_value = $current_user instanceof WP_User ? $current_user->user_login : '';
								break;

							case 'current_user_email' :
								$hidden_value = $current_user instanceof WP_User ? $current_user->user_email : '';
								break;

							case 'custom' :
								$hidden_value = esc_attr( $hidden['custom'] );

								break;

						}

						$hidden_value = apply_filters( 'vfbp_field_default', $hidden_value, $field['id'], $form_id );

						// Output
						$output .= $builder->hidden( $name, $hidden_value, $options );
					}

					break;

				//////////////////
				// !Color Picker
				//////////////////
				case 'color-picker' :
					wp_enqueue_script( 'vfb-iris' );

					$options['class'] .= ' vfb-color-picker';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->color( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Autocomplete
				//////////////////
				case 'autocomplete' :
					wp_enqueue_script( 'jquery-tokenize' );

					$autocomplete_options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';

					$options['class']    .= ' vfb-autocomplete';
					$options['multiple']  = 'multiple';

					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Open <div>
						$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						$selects = array();

						if ( is_array( $autocomplete_options ) && !empty( $autocomplete_options ) ) {
							foreach ( $autocomplete_options as $index => $select ) {
								$label            = isset( $select['label'] ) ? $select['label'] : '';
								$selected         = isset( $select['default'] ) ? $label : null;

								$selects[ $index ]['value']    = $label;
								$selects[ $index ]['selected'] = $selected;
							}
						}

						// Input
						$output .= $builder->select( $name, $selects, $options );

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Range Slider
				//////////////////
				case 'range-slider' :
					wp_enqueue_script( 'ion-range-slider' );

					$slider_type   = isset( $slider_settings['type'] ) ? $slider_settings['type'] : 'single';
					$slider_min    = isset( $slider_settings['min'] ) ? $slider_settings['min'] : '';
					$slider_max    = isset( $slider_settings['max'] ) ? $slider_settings['max'] : '';
					$slider_from   = isset( $slider_settings['from'] ) ? $slider_settings['from'] : '';
					$slider_to     = isset( $slider_settings['to'] ) ? $slider_settings['to'] : '';
					$slider_step   = isset( $slider_settings['step'] ) ? $slider_settings['step'] : '';

					if ( !empty( $slider_type ) )
						$options['data-type'] = $slider_type;

					if ( !empty( $slider_min ) || $slider_min === '0' )
						$options['data-min'] = $slider_min;

					if ( !empty( $slider_max ) || $slider_max === '0' )
						$options['data-max'] = $slider_max;

					if ( !empty( $slider_from ) || $slider_from === '0' )
						$options['data-from'] = $slider_from;

					if ( !empty( $slider_to ) || $slider_to === '0' )
						$options['data-to'] = $slider_to;

					if ( !empty( $slider_step ) )
						$options['data-step'] = $slider_step;

					$options['class'] .= ' vfb-range-slider';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->text( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Min
				//////////////////
				case 'min' :

					if ( !empty( $min_num ) )
						$options['data-vfb-min'] = absint( $min_num );

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->number( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Max
				//////////////////
				case 'max' :

					if ( !empty( $max_num ) )
						$options['data-vfb-max'] = absint( $max_num );

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->number( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Range
				//////////////////
				case 'range' :

					$options['data-vfb-range'] = sprintf( '[%d,%d]', absint( $min_num ), absint( $max_num ) );

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->number( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Name
				//////////////////
				case 'name' :
					$hide_title  = isset( $name_settings['hide-title'] )  ? $name_settings['hide-title']  : '';
					$hide_suffix = isset( $name_settings['hide-suffix'] ) ? $name_settings['hide-suffix'] : '';

					$name_parts = array(
					    'title'  => __( 'Title', 'vfb-pro' ),
					    'first'  => __( 'First', 'vfb-pro' ),
					    'last'   => __( 'Last', 'vfb-pro' ),
					    'suffix' => __( 'Suffix', 'vfb-pro' ),
					);

					if ( !empty( $hide_title ) )
						unset( $name_parts['title'] );

					if ( !empty( $hide_suffix ) )
						unset( $name_parts['suffix'] );

					// Set special name class on horizontal div
					// Name field needs a container class to control padding
					if ( isset( $horizontal_opts['class'] ) ) {
						$horizontal_opts['class'] .= ' vfb-name-block';

						// Send an option that labels are horizontal to fix grid size
						$options['vfb-horizontal'] = true;
					}
					else
						$horizontal_opts['class'] = 'vfb-name-block';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->name( $name, $default, $name_parts, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Rating
				//////////////////
				case 'rating' :
					wp_enqueue_script( 'jquery-rating' );

					$min_rating   = isset( $rating_settings['min'] ) ? $rating_settings['min'] : '';
					$max_rating   = isset( $rating_settings['max'] ) ? $rating_settings['max'] : '';
					$empty_rating = isset( $rating_settings['empty'] ) ? $rating_settings['empty'] : '';
					$icon_rating  = isset( $rating_settings['icon'] ) ? $rating_settings['icon'] : '';
					$icon_remove  = isset( $rating_settings['icon-remove'] ) ? $rating_settings['icon-remove'] : '';
					$remove_text  = isset( $rating_settings['remove-text'] ) ? $rating_settings['remove-text'] : '';

					$options['class']             .= ' vfb-rating-input';
					$options['data-icon-lib']      = 'vfb-rating-icon';
					$options['data-active-icon']   = 'vfb-rating-star';
					$options['data-inactive-icon'] = 'vfb-rating-star-2';

					if ( !empty( $min_rating ) )
						$options['data-min'] = $min_rating;

					if ( !empty( $max_rating ) )
						$options['data-max'] = $max_rating;

					if ( !empty( $empty_rating ) )
						$options['data-empty-value'] = $empty_rating;

					if ( !empty( $icon_rating ) ) {
						switch ( $icon_rating ) {
							case 'star-v1' :
								$icon_active   = 'star';
								$icon_inactive = 'star-2';
								break;

							case 'star-v2' :
								$icon_active   = 'star-3';
								$icon_inactive = 'star-4';
								break;

							case 'heart-v1' :
								$icon_active   = 'heart';
								$icon_inactive = 'heart-2';
								break;

							case 'heart-v2' :
								$icon_active   = 'heart-3';
								$icon_inactive = 'heart-4';
								break;

							case 'check-v1' :
								$icon_active   = 'checkmark';
								$icon_inactive = 'checkmark-2';
								break;

							case 'flag-v1' :
								$icon_active   = 'flag';
								$icon_inactive = 'flag-2';
								break;
						}

						$options['data-active-icon']   = "vfb-rating-{$icon_active}";
						$options['data-inactive-icon'] = "vfb-rating-{$icon_inactive}";
					}

					if ( !empty( $remove_text ) )
						$options['data-clearable'] = $remove_text;

					if ( !empty( $icon_remove ) ) {
						switch ( $icon_remove ) {
							case 'trash-v1' :
								$remove = 'remove';
								break;

							case 'close-v1' :
								$remove = 'close';
								break;

							case 'close-v2' :
								$remove = 'close-2';
								break;
						}

						$options['data-clearable-icon']   = "vfb-rating-{$remove}";
					}

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->number( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Likert
				//////////////////
				case 'likert' :
					$likert_rows = isset( $likert_settings['rows'] ) ? $likert_settings['rows'] : '';
					$likert_cols = isset( $likert_settings['cols'] ) ? $likert_settings['cols'] : '';

					$rows = $cols = array();
					if ( !empty( $likert_rows ) ) {
						$rows = explode( "\n", $likert_rows );
					}

					if ( !empty( $likert_cols ) ) {
						$cols = explode( "\n", $likert_cols );
					}

					// Set a Parsley validation indentifier
					// There's a bug with ParsleyJS where it won't use the namespace, so fallback to using Parsley
					$options['data-parsley-multiple'] = 'vfb-field-' . $field['id'];

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							$output .= $builder->likert( $name, $rows, $cols, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Knob
				//////////////////
				case 'knob' :
					wp_enqueue_script( 'jquery-knob' );

					$knob_min          = isset( $knob_settings['min']              ) ? $knob_settings['min']              : '';
					$knob_max          = isset( $knob_settings['max']              ) ? $knob_settings['max']              : '';
					$knob_step         = isset( $knob_settings['step']             ) ? $knob_settings['step']             : '';
					$knob_angle_offset = isset( $knob_settings['angle-offset']     ) ? $knob_settings['angle-offset']     : '';
					$knob_angle_arc    = isset( $knob_settings['angle-arc']        ) ? $knob_settings['angle-arc']        : '';
					$knob_rotation     = isset( $knob_settings['rotation']         ) ? $knob_settings['rotation']         : '';
					$knob_thickness    = isset( $knob_settings['thickness']        ) ? $knob_settings['thickness']        : '';
					$knob_line_cap     = isset( $knob_settings['line-cap']         ) ? $knob_settings['line-cap']         : '';
					$knob_width        = isset( $knob_settings['width']            ) ? $knob_settings['width']            : '';
					$knob_fg_color     = isset( $knob_settings['fg-color']         ) ? $knob_settings['fg-color']         : '';
					$knob_input_color  = isset( $knob_settings['input-color']      ) ? $knob_settings['input-color']      : '';
					$knob_bg_color     = isset( $knob_settings['bg-color']         ) ? $knob_settings['bg-color']         : '';
					$knob_hide_input   = isset( $knob_settings['hide-input']       ) ? $knob_settings['hide-input']       : '';
					$knob_display_prev = isset( $knob_settings['display-previous'] ) ? $knob_settings['display-previous'] : '';


					$options['class']  = str_replace( 'vfb-form-control', '', $options['class'] );
					$options['class'] .= ' vfb-knob';

					if ( empty( $default ) )
						$default = 0;

					if ( !empty( $knob_min ) )
						$options['data-min'] = $knob_min;

					if ( !empty( $knob_max ) )
						$options['data-max'] = $knob_max;

					if ( !empty( $knob_step ) )
						$options['data-step'] = $knob_step;

					if ( !empty( $knob_angle_offset ) )
						$options['data-angleOffset'] = $knob_angle_offset;

					if ( !empty( $knob_angle_arc ) )
						$options['data-angleArc'] = $knob_angle_arc;

					if ( !empty( $knob_rotation ) )
						$options['data-rotation'] = $knob_rotation;

					if ( !empty( $knob_thickness ) )
						$options['data-thickness'] = $knob_thickness;

					if ( !empty( $knob_line_cap ) )
						$options['data-linecap'] = $knob_line_cap;

					if ( !empty( $knob_width ) )
						$options['data-width'] = $knob_width;

					if ( !empty( $knob_fg_color ) )
						$options['data-fgColor'] = $knob_fg_color;

					if ( !empty( $knob_input_color ) )
						$options['data-inputColor'] = $knob_input_color;

					if ( !empty( $knob_bg_color ) )
						$options['data-bgColor'] = $knob_bg_color;

					if ( !empty( $knob_hide_input ) )
						$options['data-displayInput'] = 'false';

					if ( !empty( $knob_display_prev ) )
						$options['data-displayPrevious'] = 'true';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->text( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Signature
				//////////////////
				case 'signature' :
					wp_enqueue_script( 'jquery-signature' );

					$options['class'] .= ' vfb-signature-input';

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->hidden( $name, $default, $options );

							// Signature
							$output .= $builder->elem_open( array( 'class' => 'vfb-signature' ) );
							$output .= $builder->elem_close();

							// Reset button
							$output .= $builder->elem_open( array( 'class' => 'vfb-signature-buttons' ) );
								$output .= sprintf( '<a href="#" class="btn btn-primary">%s</a>', __( 'Reset Signature', 'vfb-pro' ) );
							$output .= $builder->elem_close();

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Captcha
				//////////////////
				case 'captcha' :
					$captcha_theme = isset( $captcha_settings['theme'] ) ? $captcha_settings['theme'] : '';
					$captcha_type  = isset( $captcha_settings['type'] ) ? $captcha_settings['type'] : '';
					$captcha_lang  = isset( $captcha_settings['lang'] ) ? $captcha_settings['lang'] : 'en';

					$vfb_settings  = get_option( 'vfbp_settings' );
					$public_key    = $vfb_settings['recaptcha-public-key'];

					$captcha_opts = array(
						'class'        => 'g-recaptcha',
						'data-sitekey' => $public_key,
					);

					if ( !empty( $captcha_theme ) )
						$captcha_opts['data-theme'] = $captcha_theme;

					if ( !empty( $captcha_type ) )
						$captcha_opts['data-type'] = $captcha_type;

					wp_enqueue_script( 'google-recaptcha-v2?hl=' . $captcha_lang );

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Output error message if Public Key isn't set
							if ( empty( $public_key ) ) {
								$output .= __( 'reCAPTCHA Public Key not found. Both Public and Private keys must be set in order for reCAPTCHA to function.', 'vfb-pro' );
							}
							else {
								// reCaptcha
								$output .= $builder->elem_open( $captcha_opts );
								$output .= $builder->elem_close();

								// Hidden field used during security check
								$output .= $builder->hidden( '_vfb_recaptcha_enabled', 1 );
							}

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Title
				//////////////////
				case 'post-title' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->text( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Content
				//////////////////
				case 'post-content' :
					$options = array(
						'media_buttons' => false,
						'textarea_name' => $name,
						'textarea_rows' => 8,
					);

					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Use output buffering to capture wp_editor
							ob_start();

							// Input
							$builder->html( $name, $default, $options );

							$output .= ob_get_clean();

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Excerpt
				//////////////////
				case 'post-excerpt' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->textarea( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Category
				//////////////////
				case 'post-category' :
					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Open <div>
						$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Input
						$output .= $builder->post_category( $name, $options );

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Tag
				//////////////////
				case 'post-tag' :
					wp_enqueue_script( 'jquery-tokenize' );

					$options['class']    .= ' vfb-autocomplete';
					$options['multiple']  = 'multiple';

					// Label
					$output .= $builder->label( $name, $label, $label_opts );

					// Open <div>
					$output .= $builder->elem_open( $horizontal_opts );

						// Description (before input)
						if ( !empty( $desc_position ) && 'before' == $desc_position )
							$output .= $desc_output;

						// Open <div>
						$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						$selects = array();

						$post_tags = get_terms( 'post_tag', array( 'hide_empty' => 0 ) );
						if ( is_array( $post_tags ) && !empty( $post_tags ) ) {
							foreach ( $post_tags as $index => $select ) {
								$selects[ $index ]['value']    = $select->name;
								$selects[ $index ]['selected'] = null;
							}
						}

						// Input
						$output .= $builder->select( $name, $selects, $options );

						// Description (after input)
						if ( empty( $desc_position ) )
							$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Post Custom Field
				//////////////////
				case 'custom-field' :
					// Open <div>
					$output .= $builder->elem_open( array( 'class' => 'vfb-form-group' ) );

						// Label
						$output .= $builder->label( $name, $label, $label_opts );

						// Open <div>
						$output .= $builder->elem_open( $horizontal_opts );

							// Description (before input)
							if ( !empty( $desc_position ) && 'before' == $desc_position )
								$output .= $desc_output;

							// Input
							$output .= $builder->text( $name, $default, $options );

							// Description (after input)
							if ( empty( $desc_position ) )
								$output .= $desc_output;

						// Close </div>
						$output .= $builder->elem_close();

					// Close </div>
					$output .= $builder->elem_close();

					break;

				//////////////////
				// !Submit
				//////////////////
				case 'submit' :
					$options['class'] = str_replace( 'vfb-form-control', '', $options['class'] );
					$options['class'] .= ' btn btn-primary';
					$options['type']  = 'submit';
					$options['name']  = '_vfb-submit';

					$output .= $builder->submit( $label, $options );
					break;
			}

			// Close Layout column
			$output .= $builder->elem_close();

			// Add a clearfix div so columns don't wrap
			if ( $cols_total >= 12 ) {
				$output .= '<div class="vfb-clearfix"></div>';
				$cols_total = 0;
			}
		}

		// Close last Background
		if ( $open_background == true && $background >= 2 ) {
			$output          .= '</div> <!-- .vfb-well -->';
			$open_background  = false;
		}

		// Close last Page Break
		if ( $open_page == true && $page >= 2 ) {
			$output .= '</section>';
			$open_page = false;
		}

		$output .= $builder->close();

		return $output;
	}
}