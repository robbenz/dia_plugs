<?php
class pcfme_manage_extrafield_class {

     public function __construct() {
     	add_filter( 'woocommerce_form_field_pcfmeselect', array( &$this, 'pcfmeselect_form_field' ), 10, 4 );
	    add_filter( 'woocommerce_form_field_datepicker', array( &$this, 'datepicker_form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_multiselect', array( &$this, 'multiselect_form_field' ), 10, 4 );
		add_filter( 'wp_enqueue_scripts', array( &$this, 'add_checkout_scripts' ));
	 }



	 
	 public function add_checkout_scripts() {
	   global $post;

	   $pcfme_woo_version=pcfme_get_woo_version_number();

	    if ( is_checkout() ) {
	     wp_enqueue_script( 'jquery-ui-datepicker' );
		 
		 wp_enqueue_style( 'jquery-ui', ''.pcfme_PLUGIN_URL.'assets/css/jquery-ui.css' );
		 
		 if ($pcfme_woo_version < 2.3) {
		 	wp_enqueue_script( 'pcfme-frontend1', ''.pcfme_PLUGIN_URL.'assets/js/frontend1.js' );
		 } else {
            wp_enqueue_script( 'pcfme-frontend2', ''.pcfme_PLUGIN_URL.'assets/js/frontend2.js' );
		 }
		 wp_enqueue_style( 'pcfme-frontend', ''.pcfme_PLUGIN_URL.'assets/css/frontend.css' );
		}
	 }

     

      

     public function pcfmeselect_form_field($field = '', $key, $args, $value) {

     if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	 if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
	    $options = '';

    if ( ! empty( $args['options'] ) ) {
        foreach ( $args['options'] as $option_key => $option_text ) {
            $options .= '<option value="' . $option_key . '" '. selected( $value, $option_key, false ) . '>' . $option_text .'</option>';
        }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <select name="' . $key . '" id="' . $key . '" class="select pcfme-singleselect" >
                ' . $options . '
            </select>
        </p>' . $after;
      }

       return $field;
     }


	 
	 public function multiselect_form_field($field = '', $key, $args, $value) {
	  if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';
	  
	 if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
	    $options = '';

    if ( ! empty( $args['options'] ) ) {
        foreach ( $args['options'] as $option_key => $option_text ) {
            $options .= '<option value="' . $option_key . '" '. selected( $value, $option_key, false ) . '>' . $option_text .'</option>';
        }

        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field">
            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required . '</label>
            <select name="' . $key . '[]" id="' . $key . '" class="select pcfme-multiselect" multiple="multiple">
                ' . $options . '
            </select>
        </p>' . $after;
      }

       return $field;
	 }
	 
	 
	 public function datepicker_form_field( $field = '', $key, $args, $value) {
	    if ( ( ! empty( $args['clear'] ) ) ) $after = '<div class="clear"></div>'; else $after = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'pcfme'  ) . '">*</abbr>';
		} else {
			$required = '';
		}
		
		if ($args['disable_past']) {
			$datepicker_class='pcfme-datepicker-disable-past';
		} else {
			$datepicker_class='pcfme-datepicker';
		}

		$args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

		if ( ! empty( $args['validate'] ) )
			foreach( $args['validate'] as $validate )
				$args['class'][] = 'validate-' . $validate;

		$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

		if ( $args['label'] )
			$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'] . $required . '</label>';

		$field .= '<input type="text" class="'. $datepicker_class .' input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="' . esc_attr( $value ) . '" />
			</p>' . $after;

		return $field;
	 }
}

new pcfme_manage_extrafield_class();
?>