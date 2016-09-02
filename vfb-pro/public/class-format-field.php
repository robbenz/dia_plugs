<?php
/**
 * Format array fields into strings
 *
 * @since      3.0
 */
class VFB_Pro_Format {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Format fields
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function format_field( $form_id, $field_id, $value ) {
		$field = $this->get_field_settings( $field_id );
		$type  = $field['field_type'];

		switch( $type ) {
			case 'textarea' :
				return wpautop( wp_strip_all_tags( $value ) );
				break;

			case 'checkbox' :
				return $this->checkbox( $field, $value );
				break;

			case 'address' :
				return $this->address( $value );
				break;

			case 'name' :
				return $this->name( $value );
				break;

			case 'likert' :
				return $this->likert( $field, $value );
				break;

			case 'hidden' :
				return $this->hidden( $form_id, $field_id, $field, $value );
				break;

			case 'signature' :
				return sprintf( '<img src="%s"', $value );
				break;

			default :
				return $value;
				break;
		}
	}

	/**
	 * Format the Checkbox field
	 *
	 * Retrieves options from the database, loops through them, and sets the value
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $value
	 * @return void
	 */
	public function checkbox( $field, $value ) {
		$output  = array();
		$options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';

		if ( is_array( $options ) && !empty( $options ) ) {
			foreach ( $options as $index => $check ) {
				$label = isset( $check['label'] ) ? $check['label'] : '';

				if ( isset( $value[ $index ] ) )
					$output[] = $label;
			}
		}

		$output = implode( ', ', $output );

		return $output;
	}

	/**
	 * Format the Address field from an array into a string
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function address( $value ) {
		$output = '';

		// Sanity check in case you make it here somehow
		if ( !array_key_exists( 'address-1', $value ) )
			return $value;

		if ( !empty( $value['address-1'] ) )
			$output .= $value['address-1'];

		if ( !empty( $value['address-2'] ) ) {
			if ( !empty( $output ) )
				$output .= "\n";

			$output .= $value['address-2'];
		}

		if ( !empty( $value['city'] ) ) {
			if ( !empty( $output ) )
				$output .= "\n";

			$output .= $value['city'];
		}

		if ( !empty( $value['province'] ) ) {
			if ( !empty( $output ) && empty( $value['city'] ) )
				$output .= "\n";
			elseif ( !empty( $output ) && !empty( $value['city'] ) )
				$output .= ', ';

			$output .= $value['province'];
		}

		if ( !empty( $value['zip'] ) ) {
			if ( !empty( $output ) && ( empty( $value['city'] ) && empty( $value['province'] ) ) )
				$output .= "\n";
			elseif ( !empty( $output ) && ( !empty( $value['city'] ) || !empty( $value['province'] ) ) )
				$output .= ' ';

			$output .= $value['zip'];
		}

		if ( !empty( $value['country'] ) ) {
			if ( !empty( $output ) )
				$output .= "\n";

			$output .= $value['country'];
		}

		return $output;
	}

	/**
	 * Format the Name field from an array to a string
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function name( $value ) {
		$output = '';

		// Sanity check in case you make it here somehow
		if ( !array_key_exists( 'first', $value ) || !array_key_exists( 'last', $value ) )
			return $value;

		if ( !empty( $value['first'] ) )
			$output .= $value['first'];

		if ( !empty( $value['last'] ) ) {
			if ( !empty( $output ) )
				$output .= ' ';

			$output .= $value['last'];
		}

		if ( !empty( $value['title'] ) ) {
			if ( !empty( $output ) )
				$output = ' ' . $output;

			$output = $value['title'] . $output;
		}

		if ( !empty( $value['suffix'] ) ) {
			if ( !empty( $output ) )
				$output .= ' ';

			$output .= $value['suffix'];
		}

		return $output;
	}

	/**
	 * Format the Likert field from an array to a string.
	 *
	 * Loop through the row settings from the database
	 * and use the input value, which is the column, and build the output string
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $value
	 * @return void
	 */
	public function likert( $field, $value ) {
		$likert_rows = isset( $field['data']['likert']['rows'] ) ? $field['data']['likert']['rows'] : '';

		$rows = array();
		if ( !empty( $likert_rows ) ) {
			$rows = explode( "\n", $likert_rows );
		}

		$output = array();
		foreach ( $rows as $index => $row ) {
			if ( isset( $value[ $index ] ) )
				$output[] = sprintf( '* %1$s - %2$s', $row, $value[ $index ] );
		}

		$output = implode( "\n", $output );

		return $output;
	}

	/**
	 * hidden function.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $field_id
	 * @param mixed $field
	 * @param mixed $value
	 * @return void
	 */
	public function hidden( $form_id, $field_id, $field, $value ) {
		$options = isset( $field['data']['hidden']['option'] ) ? $field['data']['hidden']['option'] : '';

		if ( empty( $options ) )
			return;

		if ( 'sequential-num' !== $options )
			return $value;

		$seq_num_opt = "vfb-hidden-sequential-num-{$form_id}-{$field_id}";
		$seq_num     = get_option( $seq_num_opt );

		if ( $seq_num )
			update_option( $seq_num_opt, $value );

		return $value;
	}

	/**
	 * Get all field settings
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function get_field_settings( $id ) {
		$vfbdb = new VFB_Pro_Data();
		$field = $vfbdb->get_field_by_id( $id );

		return $field;
	}
}