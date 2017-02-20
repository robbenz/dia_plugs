<?php
/**
 * VFB Form Builder Class
 *
 * This class handles HTML output
 *
 * @since      3.0
 */
class VFB_Pro_Form_Builder {

	/**
	 * The form instance.
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $form;

	/**
	 * The form ID.
	 *
	 * @var integer
	 * @access protected
	 */
	protected $form_id;

	/**
	 * The CSRF token used by the form builder.
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $csrf_token;

	/**
	 * The session store
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $session;

	/**
	 * An array of label names created
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $labels = array();

	/**
	 * An array of inputs created
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $inputs = array();

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param string $action (default: '')
	 * @param mixed $options
	 * @return void
	 */
	public function __construct( $form_id, $action = '', $options = false ) {

		// Set the form ID
		$this->form_id = (int) $form_id;

		$this->csrf_token = VFB_Pro_NoCSRF::generate( '_vfb-token-' . $form_id );

		// Default form attributes
		$defaults = array(
			'action'       => $action,
			'method'       => 'post',
			'enctype'      => 'multipart/form-data',
			'class'        => array(),
			'id'           => '',
			'onSubmit'     => '',
			'novalidate'   => false,
			'add_honeypot' => true,
			'add_submit'   => true
		);

		$settings = $defaults;

		// If arguments are present, merge with defaults
		if ( $options )
			$settings = array_merge( $defaults, $options );

		foreach ( $settings as $key => $val ) {
			// Set to user argument, if set, otherwise default
			if ( !$this->set_form_attr( $key, $val ) )
				$this->set_form_attr( $key, $defaults[ $key ] );
		}
	}

	/**
	 * Open a new HTML form.
	 *
	 * @access public
	 * @return void
	 */
	public function open() {
		$output = '';
		$output .= sprintf( '<form method="%s"', $this->form['method'] );

		// enctype attribute
		if ( !empty( $this->form['enctype'] ) )
			$output .= sprintf( ' enctype="%s"', $this->form['enctype'] );

		// action attribute
		if ( !empty( $this->form['action'] ) )
			$output .= sprintf( ' action="%s"', $this->form['action'] );

		// id attribute
		if ( !empty( $this->form['id'] ) )
			$output .= sprintf( ' id="%s"', $this->form['id'] );

		// onSubmit attribute
		if ( !empty( $this->form['onSubmit'] ) )
			$output .= sprintf( ' onSubmit="%s"', $this->form['onSubmit'] );

		// class attribute
		if ( !empty( $this->form['class'] ) ) {
			$classes = is_array( $this->form['class'] ) ? $this->set_classes( $this->form['class'] ) : $this->form['class'];
			$output .= sprintf( ' class="%s"', $classes );
		}

		if ( $this->form['novalidate'] )
			$output .= ' novalidate';

		$output .= '>';

		$output .= $this->get_appendage( $this->form['method'] );

		return $output;
	}

	/**
	 * Close the current form.
	 *
	 * @access public
	 * @return void
	 */
	public function close() {
		$this->labels = array();

		return '</form>';
	}

	/**
	 * Set form attributes
	 *
	 * @access public
	 * @param mixed $attribute
	 * @param mixed $val
	 * @return void
	 */
	public function set_form_attr( $attribute, $val ) {
		switch ( $attribute ) {
			case 'action':
				break;

			case 'method':
				if ( !in_array( $val, array( 'post', 'get' ) ) )
					return false;

				break;

			case 'enctype':
				if ( !in_array( $val, array( 'application/x-www-form-urlencoded', 'multipart/form-data' ) ) )
					return false;

				break;

			case 'class':
			case 'id':
			case 'onSubmit':
				// validate these?
				break;

			case 'novalidate':
			case 'add_honeypot':
			case 'add_submit':
				if ( !is_bool( $val ) )
					return false;

				break;

			default:
				return false;
		}

		$this->form[ $attribute ] = $val;

		return true;
	}

	/**
	 * If form class is an array, return a spaced list
	 *
	 * @access private
	 * @param array $classes (default: array())
	 * @return void
	 */
	private function set_classes( $classes = array() ) {
		if ( !is_array( $classes ) )
			return;

		if ( !empty( $classes) )
			return implode( ' ', $classes );
	}

	/**
	 * Get the form appendage for the given method.
	 *
	 * @param  string  $method
	 * @return string
	 */
	protected function get_appendage( $method ) {
		list( $method, $appendage ) = array( strtoupper( $method ), '' );

		// Add honeypot
		if ( $this->form['add_honeypot'] )
			$appendage .= $this->honeypot();

		// Add the form ID hidden field
		$appendage .= $this->form_id();

		return $appendage;
	}

	/**
	 * Add input
	 *
	 * @access public
	 * @param mixed $label
	 * @param array $options (default: array())
	 * @return void
	 */
	public function input( $type, $name, $value = null, $options = array() ) {

		$id = isset( $options['id'] ) ? sanitize_key( $options['id'] ) : '';

		// Once we have the type, name, and ID we can merge them into the rest of the
		// attributes array so we can convert them into their HTML attribute format
		// when creating the HTML element. Then, we will return the entire input.
		$merge   = compact( 'type', 'name', 'value', 'id' );
		$options = array_merge( $options, $merge );

		// Add to our inputs property
		//$this->inputs[ $name ] = $options;
		return '<input' . $this->attributes( $options ) . '>';
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	public function attributes( $attributes ) {
		$html = array();

		// For numeric keys we will assume that the key and the value are the same
		// as this will convert HTML attributes such as "required" to a correct
		// form like required="required" instead of using incorrect numerics.
		foreach ( (array) $attributes as $key => $value ) {
			$element = $this->attribute_element( $key, $value );

			if ( !is_null( $element ) )
				$html[] = $element;
		}

		return count( $html ) > 0 ? ' ' . implode( ' ', $html ) : '';
	}

	/**
	 * Build a single attribute element.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @return string
	 */
	protected function attribute_element( $key, $value ) {
		if ( is_numeric( $key ) )
			$key = $value;

		if ( !is_null( $value ) )
			return $key . '="' . htmlentities( $value, ENT_QUOTES, 'UTF-8', false ) . '"';
	}

	/**
	 * Generate a hidden field with the current CSRF token.
	 *
	 * @return string
	 */
	public function token() {
		return $this->input( 'hidden', '_vfb-token-' . $this->form_id, $this->csrf_token );
	}

	/**
	 * Generate a hidden field with the current form ID.
	 *
	 * @return string
	 */
	public function form_id() {
		return $this->input( 'hidden', '_vfb-form-id', $this->form_id );
	}

	/**
	 * Generate a honeypot field.
	 *
	 * @access public
	 * @return void
	 */
	public function honeypot() {
		$output = '<div style="display:none;">';
			$output .= $this->label( 'vfbp-spam', __( 'If you are human, leave this field blank.', 'vfb-pro' ) );
			$output .= $this->text( 'vfbp-spam', '', array( 'size' => 25, 'autocomplete' => 'off' ) );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Create a form label element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function label( $name, $value = null, $options = array() ) {
		return '<label for="' . $name . '"' . $this->attributes( $options ) . '>' . $value . '</label>';
	}

	/**
	 * Create a text input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function text( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create a password input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function password( $name, $options = array() ) {
		return $this->input( 'password', $name, null, $options );
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function hidden( $name, $value = null, $options = array() ) {
		return $this->input( 'hidden', $name, $value, $options );
	}

	/**
	 * Create an e-mail input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function email( $name, $value = null, $options = array() ) {
		return $this->input( 'email', $name, $value, $options );
	}

	/**
	 * Create a url input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function url( $name, $value = null, $options = array() ) {
		return $this->input( 'url', $name, $value, $options );
	}

	/**
	 * Create a currency input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function currency( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create a file input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function file( $name, $options = array() ) {
		return $this->input( 'file', $name, null, $options );
	}

	/**
	 * Create a number input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function number( $name, $value = null, $options = array() ) {
		return $this->input( 'number', $name, $value, $options );
	}

	/**
	 * Create a phone input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function phone( $name, $value = null, $options = array() ) {
		return $this->input( 'tel', $name, $value, $options );
	}

	/**
	 * Create a textarea input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function textarea( $name, $value = null, $options = array() ) {

		$id   = isset( $options['id']   ) ? sanitize_key( $options['id'] )   : '';

		// Once we have the type, name, and ID we can merge them into the rest of the
		// attributes array so we can convert them into their HTML attribute format
		// when creating the HTML element. Then, we will return the entire input.
		$merge   = compact( 'name', 'id' );
		$options = array_merge( $options, $merge );

		// Next we will look for the rows and cols attributes, as each of these are put
		// on the textarea element definition. If they are not present, we will just
		// assume some sane default values for these attributes for the developer.
		$options = $this->set_textarea_size( $options );

		// Remove size attribute
		unset( $options['size'] );

		return '<textarea' . $this->attributes( $options ) . '>' . $value . '</textarea>';
	}

	/**
	 * Set the text area size on the attributes.
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	public function set_textarea_size( $options ) {
		if ( isset( $options['size'] ) )
			return $this->set_quick_textarea_size( $options );

		// If the "size" attribute was not specified, we will just look for the regular
		// columns and rows attributes, using sane defaults if these do not exist on
		// the attributes array. We'll then return this entire options array back.
		$cols = isset( $options['cols'] ) ? $options['cols'] : 50;
		$rows = isset( $options['rows'] ) ? $options['rows'] : 10;

		return array_merge( $options, compact( 'cols', 'rows' ) );
	}

	/**
	 * Set the text area size using the quick "size" attribute.
	 *
	 * @param  array  $options
	 * @return array
	 */
	public function set_quick_textarea_size( $options ) {
		$segments = explode( 'x', $options['size'] );

		return array_merge( $options, array( 'cols' => $segments[0], 'rows' => $segments[1] ) );
	}

	/**
	 * Create a radio button input field.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  bool    $checked
	 * @param  array   $options
	 * @return string
	 */
	public function radio( $name, $value = null, $checked = null, $options = array() ) {
		if ( is_null( $value ) )
			$value = $name;

		return $this->checkable( 'radio', $name, $value, $checked, $options );
	}

	/**
	 * Create a checkbox input field.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  bool    $checked
	 * @param  array   $options
	 * @return string
	 */
	public function checkbox( $name, $value = 1, $checked = null, $options = array() ) {
		return $this->checkable( 'checkbox', $name, $value, $checked, $options );
	}

	/**
	 * Create a checkable input field.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  bool    $checked
	 * @param  array   $options
	 * @return string
	 */
	protected function checkable( $type, $name, $value, $checked, $options ) {
		if ( $checked )
			$options['checked'] = 'checked';

		return $this->input( $type, $name, $value, $options );
	}

	/**
	 * Create a select box field.
	 *
	 * @param  string  $name
	 * @param  array   $list
	 * @param  string  $selected
	 * @param  array   $options
	 * @return string
	 */
	public function select( $name, $list = array(), $options = array() ) {

		$id = isset( $options['id'] ) ? sanitize_key( $options['id'] ) : '';

		if ( !isset( $options['name'] ) )
			$options['name'] = $name;

		// We will simply loop through the options and build an HTML value for each of
		// them until we have an array of HTML declarations. Then we will join them
		// all together into one single HTML element that can be put on the form.
		$html = array();
		foreach ( $list as $select ) {
			$html[] = $this->option( $select['value'], $select['selected'] );
		}

		// Once we have all of this HTML, we can join this into a single element after
		// formatting the attributes into an HTML "attributes" string, then we will
		// build out a final select statement, which will contain all the values.
		$options = $this->attributes( $options );

		$list = implode('', $html);
		return "<select{$options}>{$list}</select>";
	}

	/**
	 * Create a select element option.
	 *
	 * @param  string  $value
	 * @param  string  $selected
	 * @return string
	 */
	protected function option( $value, $selected ) {

		$selected = ( (string) $value == (string) $selected ) ? 'selected' : null;

		$options = array(
			'value'    => $value,
			'selected' => $selected
		);

		return '<option' . $this->attributes( $options ) . '>' . $value . '</option>';
	}

	/**
	 * Create a date input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function date( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create a username input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function user( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create the special Address block.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $value (default: null)
	 * @param array $parts (default: array())
	 * @param array $options (default: array())
	 * @param bool $hide_country (default: false)
	 * @return void
	 */
	public function address( $name, $value = null, $parts = array(), $options = array(), $hide_country = false ) {
		$output = '';
		foreach ( $parts as $part => $label ) {
			// Append address part to make a unique ID
			$options['id'] .= "-{$part}";
			$options['class'] = "vfb-form-control vfb-addresspart-{$part}";

			// Hide Country instead of remove from $parts array because JS needs it
			$display_country = '';
			if ( $hide_country && 'country' == $part )
				$display_country = 'display:none;';

			$output .= $this->elem_open( array( 'class' => 'vfb-form-group', 'style' => $display_country ) );

				if ( 'address-1' !== $part )
					$output .= sprintf( '<label for="%s" class="vfb-address-label">%s</label>', $options['id'], $label );

				if ( 'country' == $part ) {
					$output .= $this->countries( $name . "[$part]", $value, $options );
				}
				elseif ( 'province' == $part ) {
					$output .= $this->states_US( $name . "[$part]", $options );
				}
				else {
					$output .= $this->input( 'text', $name . "[$part]", null, $options );
				}

			$output .= $this->elem_close();

			// Reset ID attribute
			$options['id'] = $name;
		}

		return $output;
	}

	/**
	 * Create the Country dropdown
	 *
	 * @access public
	 * @param mixed $name
	 * @param string $selected (default: 'US')
	 * @param array $options (default: array())
	 * @return void
	 */
	public function countries( $name, $value = 'US', $options = array() ) {
		$options['name'] = $name;

		$html = array();
		$countries = include( VFB_PLUGIN_DIR . '/inc/countries.php' );
		foreach ( $countries as $code => $country ) {
			$selected = selected( $value, $code, false );
			$html[]   = sprintf( '<option value="%1$s"%3$s>%2$s</option>', $code, $country, $selected );
		}

		$options = $this->attributes( $options );

		$list = implode('', $html);
		return "<select{$options}>{$list}</select>";
	}

	/**
	 * Create the US States dropdown
	 *
	 * @access protected
	 * @param mixed $name
	 * @param mixed $selected (default: null)
	 * @param array $options (default: array())
	 * @return void
	 */
	protected function states_US( $name, $options = array() ) {
		$options['name'] = $name;

		$states = array(
			''   => '',
			'AL' => __( 'Alabama', 'vfb-pro' ),
			'AK' => __( 'Alaska', 'vfb-pro' ),
			'AZ' => __( 'Arizona', 'vfb-pro' ),
			'AR' => __( 'Arkansas', 'vfb-pro' ),
			'CA' => __( 'California', 'vfb-pro' ),
			'CO' => __( 'Colorado', 'vfb-pro' ),
			'CT' => __( 'Connecticut', 'vfb-pro' ),
			'DE' => __( 'Delaware', 'vfb-pro' ),
			'DC' => __( 'District Of Columbia', 'vfb-pro' ),
			'FL' => __( 'Florida', 'vfb-pro' ),
			'GA' => _x( 'Georgia', 'US state of Georgia', 'vfb-pro' ),
			'HI' => __( 'Hawaii', 'vfb-pro' ),
			'ID' => __( 'Idaho', 'vfb-pro' ),
			'IL' => __( 'Illinois', 'vfb-pro' ),
			'IN' => __( 'Indiana', 'vfb-pro' ),
			'IA' => __( 'Iowa', 'vfb-pro' ),
			'KS' => __( 'Kansas', 'vfb-pro' ),
			'KY' => __( 'Kentucky', 'vfb-pro' ),
			'LA' => __( 'Louisiana', 'vfb-pro' ),
			'ME' => __( 'Maine', 'vfb-pro' ),
			'MD' => __( 'Maryland', 'vfb-pro' ),
			'MA' => __( 'Massachusetts', 'vfb-pro' ),
			'MI' => __( 'Michigan', 'vfb-pro' ),
			'MN' => __( 'Minnesota', 'vfb-pro' ),
			'MS' => __( 'Mississippi', 'vfb-pro' ),
			'MO' => __( 'Missouri', 'vfb-pro' ),
			'MT' => __( 'Montana', 'vfb-pro' ),
			'NE' => __( 'Nebraska', 'vfb-pro' ),
			'NV' => __( 'Nevada', 'vfb-pro' ),
			'NH' => __( 'New Hampshire', 'vfb-pro' ),
			'NJ' => __( 'New Jersey', 'vfb-pro' ),
			'NM' => __( 'New Mexico', 'vfb-pro' ),
			'NY' => __( 'New York', 'vfb-pro' ),
			'NC' => __( 'North Carolina', 'vfb-pro' ),
			'ND' => __( 'North Dakota', 'vfb-pro' ),
			'OH' => __( 'Ohio', 'vfb-pro' ),
			'OK' => __( 'Oklahoma', 'vfb-pro' ),
			'OR' => __( 'Oregon', 'vfb-pro' ),
			'PA' => __( 'Pennsylvania', 'vfb-pro' ),
			'RI' => __( 'Rhode Island', 'vfb-pro' ),
			'SC' => __( 'South Carolina', 'vfb-pro' ),
			'SD' => __( 'South Dakota', 'vfb-pro' ),
			'TN' => __( 'Tennessee', 'vfb-pro' ),
			'TX' => __( 'Texas', 'vfb-pro' ),
			'UT' => __( 'Utah', 'vfb-pro' ),
			'VT' => __( 'Vermont', 'vfb-pro' ),
			'VA' => __( 'Virginia', 'vfb-pro' ),
			'WA' => __( 'Washington', 'vfb-pro' ),
			'WV' => __( 'West Virginia', 'vfb-pro' ),
			'WI' => __( 'Wisconsin', 'vfb-pro' ),
			'WY' => __( 'Wyoming', 'vfb-pro' ),
			'AA' => __( 'Armed Forces (AA)', 'vfb-pro' ),
			'AE' => __( 'Armed Forces (AE)', 'vfb-pro' ),
			'AP' => __( 'Armed Forces (AP)', 'vfb-pro' ),
			'AS' => __( 'American Samoa', 'vfb-pro' ),
			'GU' => __( 'Guam', 'vfb-pro' ),
			'MP' => __( 'Northern Mariana Islands', 'vfb-pro' ),
			'PR' => __( 'Puerto Rico', 'vfb-pro' ),
			'UM' => __( 'US Minor Outlying Islands', 'vfb-pro' ),
			'VI' => __( 'US Virgin Islands', 'vfb-pro' ),
		);

		$html = array();
		foreach ( $states as $code => $state ) {
			$html[] = sprintf( '<option value="%s">%s</option>', $code, $state );
		}

		$options = $this->attributes( $options );

		$list = implode('', $html);
		return "<select{$options}>{$list}</select>";
	}

	/**
	 * Create a time input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function time( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create an HTML field
	 *
	 * Uses wp_editor()
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function html( $name, $value = null, $options = array() ) {
		$editor_id = str_replace( '-', '_', $name );

		return wp_editor( $value, $editor_id, $options );
	}

	/**
	 * Create a color picker input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function color( $name, $value = null, $options = array() ) {
		return $this->input( 'text', $name, $value, $options );
	}

	/**
	 * Create a Name field.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $value (default: null)
	 * @param array $parts (default: array())
	 * @param array $options (default: array())
	 * @return void
	 */
	public function name( $name, $value = null, $parts = array(), $options = array() ) {
		$output = '';

		$col_pad = 0;
		if ( 2 == count( $parts ) )
			$col_pad = 2;
		elseif ( 3 == count( $parts ) )
			$col_pad = 1;

		// If labels are horizontal, trigger grid size to 12
		if ( isset( $options['vfb-horizontal'] ) ) {
			$col_full = true;
			unset( $options['vfb-horizontal'] );
		}

		foreach ( $parts as $part => $label ) {
			// Append name part to make a unique ID
			$options['id'] .= "-{$part}";
			$options['class'] = "vfb-form-control vfb-namepart-{$part}";

			// Inlude a placeholder
			$options['placeholder'] = $label;
			$col = 4;

			$output .= $this->elem_open( array( 'class' => 'vfb-form-group' ) );

				if ( 'title' == $part || 'suffix' == $part )
					$col = 2;

				if ( 'first' == $part || 'last' == $part )
					$col = 4 + $col_pad;

				if ( isset( $col_full ) )
					$col = 12;

				$output .= $this->elem_open( array( 'class' => 'vfb-col-' . $col ) );
					//$output .= sprintf( '<label for="%s" class="vfb-name-label">%s</label>', $options['id'], $label );
					$output .= $this->input( 'text', $name . "[$part]", null, $options );
				$output .= $this->elem_close();

			$output .= $this->elem_close();

			// Reset ID attribute
			$options['id'] = $name;
		}

		return $output;
	}

	/**
	 * Create a Likert scale field
	 *
	 * @access public
	 * @param mixed $name
	 * @param array $rows (default: array())
	 * @param array $cols (default: array())
	 * @param array $options (default: array())
	 * @return void
	 */
	public function likert( $name, $rows = array(), $cols = array(), $options = array() ) {
		// Table start
		$output = sprintf(
			'<table class="vfb-table vfb-likert vfb-likert-cols-%d">
				<tr class="vfb-likert-head">
					<th>&nbsp;</th>',
			count( $cols )
		);

		// Columns
		foreach ( $cols as $col ) {
			$output .= sprintf(
				'<th align="center"><label>%1$s</label></th>',
				$col
			);
		}

		$output .= '</tr>';

		// Rows
		foreach ( $rows as $key => $row ) {
			$output .= sprintf(
				'<tr class="vfb-likert-row"><th><label>%1$s</label></th>',
				$row
			);

			// Radio inputs
			foreach ( $cols as $index => $col ) {
				// Append name part to make a unique ID
				$options['id'] .= "-{$key}-{$index}";

				$output .= '<td align="center">';
					$output .= $this->radio( $name . "[$key]", trim( $col ), null, $options );
				$output .= '</td>';

				// Reset ID attribute
				$options['id'] = $name;
			}

			$output .= '</tr>';
		}

		$output .= '</tr></table>';

		return $output;
	}

	/**
	 * Create a Category dropdown for the Create Post add-on.
	 *
	 * @access public
	 * @param mixed $name
	 * @param array $options (default: array())
	 * @return void
	 */
	public function post_category( $name, $options = array() ) {
		$id = isset( $options['id'] ) ? sanitize_key( $options['id'] ) : '';

		if ( !isset( $options['name'] ) )
			$options['name'] = $name;

		$list = get_terms( 'category' );

		// We will simply loop through the options and build an HTML value for each of
		// them until we have an array of HTML declarations. Then we will join them
		// all together into one single HTML element that can be put on the form.
		$html = array();
		foreach ( $list as $select ) {
			$html[] = sprintf( '<option value="%1$d">%2$s</option>', $select->term_id, $select->name );
		}

		// Once we have all of this HTML, we can join this into a single element after
		// formatting the attributes into an HTML "attributes" string, then we will
		// build out a final select statement, which will contain all the values.
		$options = $this->attributes( $options );

		$list = implode('', $html);
		return "<select{$options}>{$list}</select>";
	}

	/**
	 * Create a submit button element.
	 *
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function submit( $value = null, $options = array() ) {
		if ( !array_key_exists( 'type', $options ) )
			$options['type'] = 'button';

		return '<button' . $this->attributes( $options ) . '>' . $value . '</button>';
	}

	/**
	 * Output the field item description
	 *
	 * @access public
	 * @param mixed $value
	 * @param array $options (default: array())
	 * @return void
	 */
	public function description( $value, $options = array() ) {
		return '<span' . $this->attributes( $options ) . '>' . $value . '</span>';
	}

	/**
	 * Output the field item heading (previously the Fieldset)
	 *
	 * @access public
	 * @param mixed $value
	 * @param array $options (default: array())
	 * @return void
	 */
	public function heading( $element = 'div', $value, $options = array() ) {
		return "<$element" . $this->attributes( $options ) . '>' . $value . "</$element>";
	}

	/**
	 * Create start of element wrapper.
	 *
	 * @param  array   $options
	 * @return string
	 */
	public function elem_open( $options = array() ) {
		return '<div' . $this->attributes( $options ) . '>';
	}

	/**
	 * Close the element wrapper.
	 *
	 * @access public
	 * @return void
	 */
	public function elem_close() {
		return '</div>';
	}
}