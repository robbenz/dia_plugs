<?php

if ( ! function_exists( 'pcmfe_admin_form_field' ) ) {

	/**
	 * Outputs a checkout/address form field.
	 *
	 * @access public
	 * @subpackage	Forms
	 * @param mixed $key
	 * @param mixed $args
	 * @param string $value (default: null)
	 * @return void
	 * @todo This function needs to be broken up in smaller pieces
	 */
	function pcmfe_admin_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
		);

		$args = wp_parse_args( $args, $defaults  );

	    if (isset($args['options'])) {
	      if (is_array($args['options'])) {
            $tempoptions = $args['options'];
	      } else {
	      	$tempoptions = explode(',',$args['options']);
	      }
	     
		}

        if ($args['disable_past']) {
			$datepicker_class='pcfme-datepicker-disable-past';
		} else {
			$datepicker_class='pcfme-datepicker';
		}		
		      
		$optionsarray = array();
         
        if (isset($tempoptions)) {            
          foreach($tempoptions as $val){
    
                         $optionsarray[$val]  = $val;
      
          }	
         }



	switch ( $args['type'] ) {

		case "country":
		    
            $countries = $key == 'shipping_country' ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

			if ( sizeof( $countries ) == 1 ) {


				$field = '<strong>' . current( array_values( $countries ) ) . '</strong>';

				$field .= '<input type="hidden"   value="' . current( array_keys($countries ) ) . '" class="country_to_state" />';

				

			} else {

				$field = '<select  class="pcfme-country-select country_to_state country_select">
						 <option value="">'.__( 'Select a country&hellip;', 'pcn' ) .'</option>';

				foreach ( $countries as $ckey => $cvalue )
					$field .= '<option value="' . esc_attr( $ckey ) . '" '.selected( $value, $ckey, false ) .'>'.__( $cvalue, 'woocommerce' ) .'</option>';

				$field .= '</select>';

				

				
			}

			break;

			case "state" :

	          $field = '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '"   />';

			

			break;


		    
		case "textarea" :

			$field = '<textarea  class="input-text "  placeholder="' . esc_attr( $args['placeholder'] ) . '"></textarea>';

			break;

		case "checkbox" :

			$field = '<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox"  value="1"  />';
					

			break;

		case "password" :

			$field = '<input type="password" class="input-text "  placeholder="' . esc_attr( $args['placeholder'] ) . '" value="" />';

			break;

		case "text" :

		    $field = '<input type="text" class="input-text "   placeholder="' . esc_attr( $args['placeholder'] ) . '" '.$args['maxlength'].' value="" />';

		    break;

		case "select" :

		    

			$options = '';

			if ( ! empty( $optionsarray ) )
				foreach ( $optionsarray as $option_key => $option_text )
					$options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) .'</option>';

				$field = '<select class="select" style="width:200px;">
						' . $options . '
					</select>';
        break;

        case "pcfmeselect" :

		    

			$options = '';

			if ( ! empty( $optionsarray ) )
				foreach ( $optionsarray as $option_key => $option_text )
					$options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) .'</option>';

				$field = '<select class="select pcfme-singleselect" style="width:200px;">
						' . $options . '
					</select>';
        break;

		case "radio" :

		  $options = '';
          $field   = '';
			if ( ! empty( $optionsarray ) ) {
				foreach ( $optionsarray as $option_key => $option_text ) {
					$field .= '<input type="radio" class="input-radio" value="" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_" /><label for="' . esc_attr( $option_text ) . '_' . esc_attr( $option_text ) . '" >' . $option_text . '</label>&emsp;';
					
				}
			}

		   break;

		 
		 
	     case "multiselect":


	        $options = '';

            if ( ! empty( $optionsarray ) ) {
              foreach ( $optionsarray as $option_key => $option_text ) {
              $options .= '<option value="' . $option_key . '">' . $option_text .'</option>';
             }

             $field = '<select id="' . $key . '" class="select pcfme-multiselect" style="width:200px;" multiple="multiple">
                ' . $options . '
               </select>';
            
             }


	        break;

	      case "datepicker":

              $field = '<input type="text" class="'.$datepicker_class.' input-text"  placeholder="' . $args['placeholder'] . '" '.$args['maxlength'].' value="" />';
			
              break;
		}

		if (isset($field)) {

			echo $field;
		}
	}
}


if ( ! function_exists( 'pcfme_get_woo_version_number' ) ) {

    /**
	 * Outputs a installed woocommerce version
	 *
	 * @access public
	 * @subpackage	Forms
	 */



    function pcfme_get_woo_version_number() {
        // If get_plugins() isn't available, require it
	   
	   if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	   $plugin_folder = get_plugins( '/' . 'woocommerce' );
	   $plugin_file = 'woocommerce.php';
	
	   // If the plugin version number is set, return it 
	   if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		 return $plugin_folder[$plugin_file]['Version'];

	   } else {
	// Otherwise return null
		return NULL;
	  }
   }


}
?>