<?php
/**
 * Class that outputs field settings such as Name, Description, etc
 *
 * @since 3.0
 */
class VFB_Pro_Admin_Fields_Settings {

	/**
	 * The field ID
	 *
	 * @var mixed
	 * @access private
	 */
	private $id;

	/**
	 * Assign field ID when class is loaded
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * Name/Label edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function name( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['label'] ) ? $field['data']['label'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-name-<?php echo $id; ?>">
				<?php esc_html_e( 'Name', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Name', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "A field's name is the most visible and direct way to describe what that field is for.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[label]" class="vfb-form-control" id="vfb-edit-name-<?php echo $id; ?>" maxlength="255">

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Description edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function description( $field, $type = '' ) {
		$id    = $field['id'];
		$value = isset( $field['data']['description'] ) ? $field['data']['description'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-description-<?php echo $id; ?>">
				<?php esc_html_e( 'Description', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Description', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Add an optional description that displays the text to your users while they are filling out the field.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<?php if ( 'wp_editor' == $type && !defined( 'DOING_AJAX' ) ) : ?>
			<?php
				$wp_editor_id = str_replace( '-', '_', 'vfb-edit-description-' . $id );
				wp_editor(
					$value,
					$wp_editor_id,
					array(
						'textarea_name' => 'vfb-field-' . $id . '[description]',
						'textarea_rows' => 25,
						'editor_class'  => 'vfb-form-control',
					)
				);

				else :
			?>
			<textarea id="vfb-edit-description-<?php echo $id; ?>" name="vfb-field-<?php echo $id; ?>[description]" class="vfb-form-control" rows="3"><?php echo $value; ?></textarea>
			<?php endif; ?>

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Description position edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function description_position( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['description-position'] ) ? $field['data']['description-position'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-description-position-<?php echo $id; ?>">
				<?php esc_html_e( 'Description Position', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Description Position', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Change the Description position to either before or after the input field. Default position is after the input.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<select name="vfb-field-<?php echo $id; ?>[description-position]" class="vfb-form-control" id="vfb-edit-description-position-<?php echo $id; ?>">
				<option value=""<?php selected( $value, '' ); ?>><?php _e( 'After Input', 'vfb-pro' ); ?></option>
				<option value="before"<?php selected( $value, 'before' ); ?>><?php _e( 'Before Input', 'vfb-pro' ); ?></option>
			</select>

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Default Value edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function default_value( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['default_value'] ) ? $field['data']['default_value'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-default-<?php echo $id; ?>">
				<?php esc_html_e( 'Default Value', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Default Value', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set a default value that will be inserted automatically.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[default_value]" class="vfb-form-control" id="vfb-edit-default-<?php echo $id; ?>" maxlength="255" />

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Placeholder edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function placeholder( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['placeholder'] ) ? $field['data']['placeholder'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-placeholder-<?php echo $id; ?>">
				<?php esc_html_e( 'Placeholder', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Placeholder', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "A hint to the user of what can be entered.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[placeholder]" class="vfb-form-control" id="vfb-edit-placeholder-<?php echo $id; ?>" maxlength="255" />

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * CSS Classes edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function css( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['css'] ) ? $field['data']['css'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-css-<?php echo $id; ?>">
				<?php esc_html_e( 'CSS Classes', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About CSS Classes', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Insert your own CSS class names which can then be used in your theme stylesheet.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[css]" class="vfb-form-control" id="vfb-edit-css-<?php echo $id; ?>" maxlength="255" />

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Required edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function required( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['required'] ) ? $field['data']['required'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-required-<?php echo $id; ?>">
				<?php esc_html_e( 'Required', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Required', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Requires the field to be completed before the form is submitted. By default, all fields are set to No.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<select name="vfb-field-<?php echo $id; ?>[required]" class="vfb-form-control" id="vfb-edit-required-<?php echo $id; ?>">
				<option value=""<?php selected( $value, '' ); ?>><?php _e( 'No', 'vfb-pro' ); ?></option>
				<option value="1"<?php selected( $value, 1 ); ?>><?php _e( 'Yes', 'vfb-pro' ); ?></option>
			</select>

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Input Mask edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function input_mask( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['input-mask'] ) ? $field['data']['input-mask'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-input-mask-<?php echo $id; ?>">
				<?php esc_html_e( 'Input mask', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Input Mask', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Force user input to conform to a specific format.", 'vfb-pro' ); ?> <?php echo '&lt;a href=&quot;https://support.vfbpro.com/support/solutions/articles/4000043826&quot; target=&quot;_blank&quot;&gt;Input Mask Documentation&lt;/a&gt;'; ?>."><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[input-mask]" class="vfb-form-control" id="vfb-edit-input-mask-<?php echo $id; ?>" maxlength="255" />

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Layout Columns edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function layout( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['cols'] ) ? $field['data']['cols'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-cols-<?php echo $id; ?>">
				<?php esc_html_e( 'Layout Columns', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Layout Columns', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Used to create advanced, responsive layouts. Align fields side-by-side in various configurations.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<select name="vfb-field-<?php echo $id; ?>[cols]" class="vfb-form-control" id="vfb-edit-cols-<?php echo $id; ?>">
				<option value=""<?php selected( $value, '' ); ?>></option>
				<option value="1"<?php selected( $value, 1 ); ?>><?php _e( '1 (smallest)', 'vfb-pro' ); ?></option>
				<option value="2"<?php selected( $value, 2 ); ?>>2</option>
				<option value="3"<?php selected( $value, 3 ); ?>>3</option>
				<option value="4"<?php selected( $value, 4 ); ?>>4</option>
				<option value="5"<?php selected( $value, 5 ); ?>>5</option>
				<option value="6"<?php selected( $value, 6 ); ?>>6</option>
				<option value="7"<?php selected( $value, 7 ); ?>>7</option>
				<option value="8"<?php selected( $value, 8 ); ?>>8</option>
				<option value="9"<?php selected( $value, 9 ); ?>>9</option>
				<option value="10"<?php selected( $value, 10 ); ?>>10</option>
				<option value="11"<?php selected( $value, 11 ); ?>>11</option>
				<option value="12"<?php selected( $value, 12 ); ?>><?php _e( '12 (largest)', 'vfb-pro' ); ?></option>
			</select>

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Options Layout Columns edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function layout_options( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['cols-options'] ) ? $field['data']['cols-options'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-cols-options-<?php echo $id; ?>">
				<?php esc_html_e( 'Options Columns', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Options Columns', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Control the layout of Radio or Checkbox options. By default, options are arranged in a single column.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<select name="vfb-field-<?php echo $id; ?>[cols-options]" class="vfb-form-control" id="vfb-edit-cols-options<?php echo $id; ?>">
				<option value=""<?php selected( $value, '' ); ?>><?php _e( 'Vertical', 'vfb-pro' ); ?></option>
				<option value="inline"<?php selected( $value, 'inline' ); ?>><?php _e( 'Inline', 'vfb-pro' ); ?></option>
			</select>

		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * options function.
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function options( $field ) {
		$id      = $field['id'];
		$options = isset( $field['data']['options'] ) ? $field['data']['options'] : '';

		$count = 0;
	?>
		<div class="vfb-form-group">
			<label>
				<?php esc_html_e( 'Options', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Options', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "This property allows you to set predefined options to be selected by the user. At least one option must exist.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<table class="vfb-options-table">
				<thead>
					<tr>
						<th class="vfb-options-table-selected"></th>
						<th class="vfb-options-table-label"><?php _e( 'Name', 'vfb-pro' ); ?></th>
						<th class="vfb-options-table-actions"></th>
					</tr>
				<tbody class="vfb-cloned-options">
					<?php
						if ( is_array( $options ) && !empty( $options ) ) :
							foreach( $options as $option ) :

								$default = isset( $option['default'] ) ? $option['default'] : '';
								$label   = htmlspecialchars( str_replace( '&amp;', '&', $option['label'] ) );
					?>
					<tr id="vfb-clone-<?php echo "{$id}-{$count}"; ?>" class="vfb-option">
						<td>
							<input type="checkbox" value="1" name="vfb-field-<?php echo $id; ?>[options][<?php echo $count;?>][default]"<?php checked( $default, 1 ); ?> />
						</td>

						<td>
							<input type="text" value="<?php esc_attr_e( $label ); ?>" name="vfb-field-<?php echo $id; ?>[options][<?php echo $count;?>][label]" class="vfb-form-control" id="vfb-edit-options-<?php echo "{$id}-{$count}"; ?>" maxlength="150" />
						</td>

						<td class="vfb-options-table-actions">
							<span class="vfb-delete-option" title="<?php esc_attr_e( 'Delete Option', 'vfb-pro' ); ?>">
								<i class="vfb-icon-minus-circle"></i>&nbsp;
							</span>
							<span class="vfb-sort-option" title="<?php esc_attr_e( 'Drag and Drop to Sort Options', 'vfb-pro' ); ?>">
								<i class="vfb-icon-move"></i>
							</span>
						</td>
					</tr>
					<?php
							$count++;
							endforeach;
						else :
					?>
					<tr id="vfb-clone-<?php echo "{$id}-0"; ?>" class="vfb-option">
						<td>
							<input type="checkbox" value="1" name="vfb-field-<?php echo $id; ?>[options][0][default]" />
						</td>

						<td>
							<input type="text" value="" name="vfb-field-<?php echo $id; ?>[options][0][label]" class="vfb-form-control" id="vfb-edit-options-<?php echo "{$id}-0"; ?>" maxlength="150" />
						</td>

						<td class="vfb-options-table-actions">
							<span class="vfb-delete-option" title="<?php esc_attr_e( 'Delete Option', 'vfb-pro' ); ?>">
								<i class="vfb-icon-minus-circle"></i>&nbsp;
							</span>
							<span class="vfb-sort-option" title="<?php esc_attr_e( 'Drag and Drop to Sort Options', 'vfb-pro' ); ?>">
								<i class="vfb-icon-move"></i>
							</span>
						</td>
					</tr>
					<?php
						endif;
					?>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td class="vfb-options-table-buttons">
							<a href="#TB_inline?width=1000&height=600&inlineId=vfb-bulk-add-<?php echo $id; ?>" class="button thickbox vfb-button-option-add">
								<i class="vfb-icon-stack-plus"></i>&nbsp;<?php _e( 'Bulk Add Options', 'vfb-pro' ); ?>
							</a>

							<a href="#" class="button vfb-button-option-add">
								<i class="vfb-icon-plus-circle"></i>&nbsp;<?php _e( 'Add Option', 'vfb-pro' ); ?>
							</a>
						</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div> <!-- .vfb-form-group -->
		<div id="vfb-bulk-add-<?php echo $id; ?>" style="display: none;">
			<?php $this->bulk_add( $id ); ?>
		</div> <!-- #vfb-bulk-add-<?php echo $id; ?> -->
	<?php
	}

	/**
	 * Bulk Add Options
	 *
	 * @access public
	 * @return void
	 */
	public function bulk_add( $field_id ) {
		if ( !$field_id )
			return;
	?>
	<div class="vfb-bulk-add">
		<h3 class="media-title"><?php _e( 'Bulk Add Options', 'vfb-pro' ); ?></h3>
		<ol>
			<li><?php _e( 'Select from the predefined categories', 'vfb-pro' ); ?></li>
			<li><?php _e( 'If needed, customize the options. Place each option on a new line.', 'vfb-pro' ); ?></li>
			<li><?php _e( 'Add to your field', 'vfb-pro' ); ?></li>
		</ol>

		<?php
			$bulk_options = $days = $years = array();
			$countries = include( VFB_PLUGIN_DIR . '/inc/countries.php' );

			// Build Days array
			for ( $i = 1; $i <= 31; ++$i ) {
				$days[] = $i;
			}

			//Build Years array
			for ( $i = date( 'Y' ); $i >= 1925; --$i ) {
				$years[] = $i;
			}

			$bulk_options = array(
				'Countries'         => $countries,
				'U.S. States'		=> array( 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming' ),

				'U.S. States Abbreviations'	=> array( 'AK','AL','AR','AS','AZ','CA','CO','CT','DC','DE','FL','GA','GU','HI','IA','ID', 'IL','IN','KS','KY','LA','MA','MD','ME','MH','MI','MN','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY', 'OH','OK','OR','PA','PR','PW','RI','SC','SD','TN','TX','UT','VA','VI','VT','WA','WI','WV','WY' ),
				'Days of the Week'	=> array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
				'Days'				=> $days,
				'Months'			=> array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),
				'Years'				=> $years,
				'Gender'			=> array( 'Male', 'Female', 'Prefer not to answer' ),
				'Age Range'			=> array( 'Under 18', '18 - 24', '25 - 34', '35 - 44', '45 - 54', '55 - 64', '65 or older', 'Prefer not to answer' ),
				'Marital Status'	=> array( 'Single', 'Married', 'Divorced', 'Separated', 'Widowed', 'Domestic Partner', 'Unmarried Partner', 'Prefer not to answer' ),
				'Ethnicity'			=> array( 'American Indian/Alaskan Native', 'Asian', 'Native Hawaiian or Other Pacific Islander', 'Black or African-American', 'White', 'Not disclosed' ),
				'Prefix'			=> array( 'Mr.', 'Mrs.', 'Ms.', 'Miss', 'Dr.' ),
				'Suffix'			=> array( 'Sr.', 'Jr.', 'Ph.D', 'M.D' ),
				'Agree'				=> array( 'Strongly Agree', 'Agree', 'Neutral', 'Disagree', 'Strongly Disagree', 'N/A' ),
				'Education'			=> array( 'Some High School', 'High School/GED', 'Some College', 'Associate\'s Degree', 'Bachelor\'s Degree', 'Master\'s Degree', 'Doctoral Degree', 'Professional Degree' )
			);

			$more_options = apply_filters( 'vfb_bulk_add_options', array() );

			// Merge our pre-defined bulk options with possible additions via filter
			$bulk_options = array_merge( $bulk_options, $more_options );
		?>
		<div class="bulk-options-left">
			<ul>
			<?php foreach ( $bulk_options as $name => $values ) : ?>
				<li>
					<a id="bulk-option-field-<?php echo sanitize_title( $name ) . '-' . $field_id; ?>" class="vfb-bulk-options" href="#"><?php echo $name; ?></a>
					<ul style="display:none;">
					<?php
						foreach ( $values as $value ) {
							echo sprintf( '<li>%s</li>', $value );
						}
					?>
					</ul>
				</li>
			<?php endforeach; ?>
			</ul>
		</div> <!-- #bulk-options-left -->

		<div class="bulk-options-right">
			<textarea id="bulk-choices-text-<?php echo $field_id; ?>" class="bulk-choices-text textarea" name="choicesText"></textarea>
			<p>
				<!--<input type="submit" class="button-primary" value="Add Options" />-->
				<a href="#&field=<?php echo $field_id; ?>" class="button button-primary vfb-bulk-add-options">
					<?php _e( 'Add Options', 'vfb-pro' ); ?>
				</a>
			</p>
		</div> <!-- #bulk-options-right -->
	</div> <!-- #vfb-bulk-add -->
	<?php
	}

	/**
	 * Allow Other.
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function allow_other( $field ) {
		$id      = $field['id'];
		$value   = isset( $field['data']['allow-other'] ) ? $field['data']['allow-other'] : '';
		$other   = isset( $field['data']['allow-other-input'] ) ? $field['data']['allow-other-input'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-allow-other-<?php echo $id; ?>">
				<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[allow-other]" class="vfb-form-control vfb-allow-other" id="vfb-edit-allow-other-<?php echo $id; ?>" />
				<?php esc_html_e( 'Allow Other', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Allow Other', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Check this option if you would like the last choice in your multiple choice field to have a text field for additional input.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>


			<input type="text" value="<?php esc_attr_e( $other ); ?>" name="vfb-field-<?php echo $id; ?>[allow-other-input]" placeholder="<?php esc_attr_e( 'Other', 'vfb-pro' ); ?>" class="vfb-form-control vfb-allow-other-input<?php echo 1 == $value ? ' active' : ''; ?>" maxlength="150" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Minimum Words display, used by the Textarea field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function min_words( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['min-words'] ) ? $field['data']['min-words'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-min-words-<?php echo $id; ?>">
				<?php esc_html_e( 'Minimum Words', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Minimum Words', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set an minimum number of words allowed to be entered. For an unlimited number, leave blank or set to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[min-words]" class="vfb-form-control" id="vfb-edit-min-words-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Maximum Words display, used by the Textarea field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function max_words( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['max-words'] ) ? $field['data']['max-words'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-max-words-<?php echo $id; ?>">
				<?php esc_html_e( 'Maximum Words', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Maximum Words', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set an maximum number of words allowed to be entered. For an unlimited number, leave blank or set to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[max-words]" class="vfb-form-control" id="vfb-edit-max-words-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Set number of rows for height, used by the Textarea field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function textarea_rows( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['textarea-rows'] ) ? $field['data']['textarea-rows'] : 10;
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-textarea-rows-<?php echo $id; ?>">
				<?php esc_html_e( 'Textarea Rows', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Textarea Rows', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the number of rows to determine a Textarea's height. The larger the number, the taller this field will appear. Default is 10.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[textarea-rows]" class="vfb-form-control" id="vfb-edit-textarea-rows-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Minimum Number display, used by the Min, Max, and Range fields
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function min_num( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['min-num'] ) ? $field['data']['min-num'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-min-num-<?php echo $id; ?>">
				<?php esc_html_e( 'Minimum Number', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Minimum Number', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set a minimum number that must be entered.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[min-num]" class="vfb-form-control" id="vfb-edit-min-num-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Maximum Number display, used by the Min, Max, and Range fields
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function max_num( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['max-num'] ) ? $field['data']['max-num'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-max-num-<?php echo $id; ?>">
				<?php esc_html_e( 'Maximum Number', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Maximum Number', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set a maximum number that must be entered.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[max-num]" class="vfb-form-control" id="vfb-edit-max-num-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Heading settings edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function heading_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data'][ $type ] ) ? $field['data'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'heading-type' :
				?>
				<label for="vfb-edit-heading-type-<?php echo $id; ?>">
					<?php esc_html_e( 'Heading Type', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Heading Type', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the element's wrapper for the heading field.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-<?php echo "{$type}-{$id}"; ?>">
					<option value="div"<?php selected( $value, 'div' ); ?>><?php _e( '&lt;div&gt;', 'vfb-pro' ); ?></option>
					<option value="p"<?php selected( $value, 'p' ); ?>><?php _e( '&lt;p&gt;', 'vfb-pro' ); ?></option>
					<option value="h1"<?php selected( $value, 'h1' ); ?>><?php _e( '&lt;h1&gt;', 'vfb-pro' ); ?></option>
					<option value="h2"<?php selected( $value, 'h2' ); ?>><?php _e( '&lt;h2&gt;', 'vfb-pro' ); ?></option>
					<option value="h3"<?php selected( $value, 'h3' ); ?>><?php _e( '&lt;h3&gt;', 'vfb-pro' ); ?></option>
					<option value="h4"<?php selected( $value, 'h4' ); ?>><?php _e( '&lt;h4&gt;', 'vfb-pro' ); ?></option>
					<option value="h5"<?php selected( $value, 'h5' ); ?>><?php _e( '&lt;h5&gt;', 'vfb-pro' ); ?></option>
					<option value="h6"<?php selected( $value, 'h6' ); ?>><?php _e( '&lt;h6&gt;', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'heading-bg' :
				?>
				<label for="vfb-edit-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Background Container', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Background Container', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If true, will wrap all fields BELOW this heading in a container with a background color.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Username strict mode edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function user_mode( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['user-mode'] ) ? $field['data']['user-mode'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-user-mode-<?php echo $id; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[user-mode]" class="vfb-form-control" id="vfb-edit-user-mode-<?php echo $id; ?>" />
					<?php esc_html_e( 'Enable Strict Mode', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Enable Strict Mode', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, only alphanumeric characters plus these: &lt;code&gt;_ .-*@&lt;/code&gt; are returned.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Likert Rows display, used by the Likert field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function likert_rows( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['likert']['rows'] ) ? $field['data']['likert']['rows'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-likert-rows-<?php echo $id; ?>">
				<?php esc_html_e( 'Likert Rows', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Likert Rows', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Create a list of labels to be placed on the left side of the matrix. Separate each new option with a new line.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<textarea name="vfb-field-<?php echo $id; ?>[likert][rows]" class="vfb-form-control" id="vfb-edit-likert-rows-<?php echo $id; ?>" rows="5"><?php esc_attr_e( $value ); ?></textarea>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Likert Rows display, used by the Likert field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function likert_cols( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['likert']['cols'] ) ? $field['data']['likert']['cols'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-likert-rows-<?php echo $id; ?>">
				<?php esc_html_e( 'Likert Columns', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Likert Columns', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Create a list of labels to be placed on the top side of the matrix. Separate each new option with a new line.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<textarea name="vfb-field-<?php echo $id; ?>[likert][cols]" class="vfb-form-control" id="vfb-edit-likert-rows-<?php echo $id; ?>" rows="5"><?php esc_attr_e( $value ); ?></textarea>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Hidden options edit display
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function hidden_options( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['hidden'][ $type ] ) ? $field['data']['hidden'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'option' :
				?>
				<label for="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Hidden Option', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hidden Option', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the option to populate the hidden field's value.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[hidden][<?php echo $type; ?>]" class="vfb-form-control vfb-hidden-option" id="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>><?php _e( 'None', 'vfb-pro' ); ?></option>

	                <optgroup label="<?php esc_attr_e( 'VFB Pro', 'vfb-pro' ); ?>">
		                <option value="form_id"<?php selected( $value, 'form_id' ); ?>><?php _e( 'Form ID', 'vfb-pro' ); ?></option>
		                <option value="form_title"<?php selected( $value, 'form_title' ); ?>><?php _e( 'Form Title', 'vfb-pro' ); ?></option>
	                </optgroup>

	                <optgroup label="<?php esc_attr_e( 'Server', 'vfb-pro' ); ?>">
		                <option value="ip"<?php selected( $value, 'ip' ); ?>><?php _e( 'IP Address', 'vfb-pro' ); ?></option>
		                <option value="uid"<?php selected( $value, 'uid' ); ?>><?php _e( 'Unique ID', 'vfb-pro' ); ?></option>
		                <option value="sequential-num"<?php selected( $value, 'sequential-num' ); ?>><?php _e( 'Sequential Order Number', 'vfb-pro' ); ?></option>
		                <option value="date-today"<?php selected( $value, 'date-today' ); ?>><?php _e( "Today's Date", 'vfb-pro' ); ?></option>
		                <option value="current-time"<?php selected( $value, 'current-time' ); ?>><?php _e( 'Current Time', 'vfb-pro' ); ?></option>
	                </optgroup>

	                <optgroup label="<?php esc_attr_e( 'WordPress content', 'vfb-pro' ); ?>">
		                <option value="post_id"<?php selected( $value, 'post_id' ); ?>><?php _e( 'Post/Page ID', 'vfb-pro' ); ?></option>
		                <option value="post_title"<?php selected( $value, 'post_title' ); ?>><?php _e( 'Post/Page Title', 'vfb-pro' ); ?></option>
		                <option value="post_url"<?php selected( $value, 'post_url' ); ?>><?php _e( 'Post/Page URL', 'vfb-pro' ); ?></option>
	                </optgroup>

	                <optgroup label="<?php esc_attr_e( 'WordPress user', 'vfb-pro' ); ?>">
		                <option value="current_user_id"<?php selected( $value, 'current_user_id' ); ?>><?php _e( 'Current User - ID', 'vfb-pro' ); ?></option>
		                <option value="current_user_name"<?php selected( $value, 'current_user_name' ); ?>><?php _e( 'Current User - Display Name', 'vfb-pro' ); ?></option>
		                <option value="current_user_username"<?php selected( $value, 'current_user_username' ); ?>><?php _e( 'Current User - Username', 'vfb-pro' ); ?></option>
		                <option value="current_user_email"<?php selected( $value, 'current_user_email' ); ?>><?php _e( 'Current User - Email', 'vfb-pro' ); ?></option>
	                </optgroup>

	            	<option value="custom"<?php selected( $value, 'custom' ); ?>><?php _e( 'Custom', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

				case 'custom' :

					// If Custom is selected from the dropdown, make sure we display the Custom value field
					$selected_custom = '';
					if ( isset( $field['data']['hidden']['option'] ) && 'custom' == $field['data']['hidden']['option'] )
						$selected_custom = 'active';
					?>
					<div class="vfb-hidden-custom<?php echo $selected_custom; ?>" id="vfb-hidden-custom-<?php echo $id; ?>">
						<label for="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>">
							<?php esc_html_e( 'Custom Value', 'vfb-pro' ); ?>
						</label>

						<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Custom Value', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set a custom, static value to populate the hidden field's value.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

						<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[hidden][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>" />
					</div> <!-- .vfb-hidden-custom -->
					<?php
					break;

				case 'seq-start' :

					// If Sequential number is selected from the dropdown, make sure we display
					$selected_seq = '';
					if ( isset( $field['data']['hidden']['option'] ) && 'sequential-num' == $field['data']['hidden']['option'] )
						$selected_seq = 'active';
					?>
					<div class="vfb-hidden-seq<?php echo $selected_seq; ?>" id="vfb-hidden-seq-start-<?php echo $id; ?>">
						<label for="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>">
							<?php esc_html_e( 'Starting Number', 'vfb-pro' ); ?>
						</label>

						<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Sequential Starting Number', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the starting value for the Sequential Number option. Default is 1000.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

						<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[hidden][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>" />
					</div> <!-- .vfb-hidden-custom -->
					<?php
					break;

				case 'seq-step' :

					// If Sequential number is selected from the dropdown, make sure we display
					$selected_seq = '';
					if ( isset( $field['data']['hidden']['option'] ) && 'sequential-num' == $field['data']['hidden']['option'] )
						$selected_seq = 'active';
					?>
					<div class="vfb-hidden-seq<?php echo $selected_seq; ?>" id="vfb-hidden-seq-step-<?php echo $id; ?>">
						<label for="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>">
							<?php esc_html_e( 'Number Steps', 'vfb-pro' ); ?>
						</label>

						<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Sequential Number Steps', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the number of steps to add by for the Sequential Number option. Default is 1.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

						<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[hidden][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-hidden-<?php echo "{$type}-{$id}"; ?>" />
					</div> <!-- .vfb-hidden-custom -->
					<?php
					break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Create Post - Custom Field meta key
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function create_post_meta_key( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['create-post-meta-key'] ) ? $field['data']['create-post-meta-key'] : '';
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-create-post-meta-key-<?php echo $id; ?>">
				<?php esc_html_e( 'Create Post - Meta Key', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Create Post - Meta Key', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Enter a new or existing meta key. Data entered will be inserted as a Custom Field into the post created.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[create-post-meta-key]" class="vfb-form-control" id="vfb-edit-create-post-meta-key-<?php echo $id; ?>" />
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Date field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function date_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['date'][ $type ] ) ? $field['data']['date'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'format' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Date Format', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Date Format', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Default: 'mm/dd/yyyy'. The date format, combination of d, dd, D, DD, m, mm, M, MM, yy, yyyy.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'days-of-week-disabled' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Days of Week Disabled', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Days of Week Disabled', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday). Multiple values should be comma-separated.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'start-date' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Start Date', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Start Date', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The earliest date that may be selected; all earlier dates will be disabled. Must match Date Format format.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'end-date' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'End Date', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About End Date', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The latest date that may be selected; all later dates will be disabled. Must match Date Format format.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'week-start' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Week Start', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Week Start', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Day of the week start. 0 (Sunday) to 6 (Saturday).", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="0"<?php selected( $value, 0 ); ?>>0</option>
					<option value="1"<?php selected( $value, 1 ); ?>>1</option>
					<option value="1"<?php selected( $value, 2 ); ?>>2</option>
					<option value="1"<?php selected( $value, 3 ); ?>>3</option>
					<option value="1"<?php selected( $value, 4 ); ?>>4</option>
					<option value="1"<?php selected( $value, 5 ); ?>>5</option>
					<option value="1"<?php selected( $value, 6 ); ?>>6</option>
				</select>
				<?php
				break;

			case 'start-view' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Start View', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Start View', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The view that the datepicker should show when it is opened. Accepts values of 0 or 'month' for month view (the default), 1 or 'year' for the 12-month overview, and 2 or 'decade' for the 10-year overview. Useful for date-of-birth datepickers.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="0"<?php selected( $value, 0 ); ?>><?php _e( '0 / month', 'vfb-pro' ); ?></option>
					<option value="1"<?php selected( $value, 1 ); ?>><?php _e( '1 / year', 'vfb-pro' ); ?></option>
					<option value="2"<?php selected( $value, 2 ); ?>><?php _e( '2 / decade', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'min-view-mode' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Min View Mode', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Min View Mode', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set a limit for the view mode. Accepts: 'days' or 0, 'months' or 1, and 'years' or 2. Gives the ability to pick only a month or an year. The day is set to the 1st for 'months', and the month is set to January for 'years'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="0"<?php selected( $value, 0 ); ?>><?php _e( '0 / days', 'vfb-pro' ); ?></option>
					<option value="1"<?php selected( $value, 1 ); ?>><?php _e( '1 / months', 'vfb-pro' ); ?></option>
					<option value="2"<?php selected( $value, 2 ); ?>><?php _e( '2 / years', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'today-btn' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Today Button', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Today Button', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If true or 'linked', displays a 'Today' button at the bottom of the datepicker to select the current date. If true, the 'Today' button will only move the current date into view; if 'linked', the current date will also be selected.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="false"<?php selected( $value, 'false' ); ?>><?php _e( 'disabled', 'vfb-pro' ); ?></option>
					<option value="true"<?php selected( $value, 'true' ); ?>><?php _e( 'enabled (unlinked)', 'vfb-pro' ); ?></option>
					<option value="linked"<?php selected( $value, 'linked' ); ?>><?php _e( 'linked', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'language' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Language', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Language', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The language to use for month and day names.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="en"<?php selected( $value, 'en' ); ?>>en</option>
					<option value="ar"<?php selected( $value, 'ar' ); ?>>ar</option>
					<option value="az"<?php selected( $value, 'az' ); ?>>az</option>
					<option value="bg"<?php selected( $value, 'bg' ); ?>>bg</option>
					<option value="ca"<?php selected( $value, 'ca' ); ?>>ca</option>
					<option value="cs"<?php selected( $value, 'cs' ); ?>>cs</option>
					<option value="cy"<?php selected( $value, 'cy' ); ?>>cy</option>
					<option value="da"<?php selected( $value, 'da' ); ?>>da</option>
					<option value="de"<?php selected( $value, 'de' ); ?>>de</option>
					<option value="el"<?php selected( $value, 'el' ); ?>>el</option>
					<option value="es"<?php selected( $value, 'es' ); ?>>es</option>
					<option value="et"<?php selected( $value, 'et' ); ?>>et</option>
					<option value="fa"<?php selected( $value, 'fa' ); ?>>fa</option>
					<option value="fi"<?php selected( $value, 'fi' ); ?>>fi</option>
					<option value="fr"<?php selected( $value, 'fr' ); ?>>fr</option>
					<option value="gl"<?php selected( $value, 'gl' ); ?>>gl</option>
					<option value="he"<?php selected( $value, 'he' ); ?>>he</option>
					<option value="hr"<?php selected( $value, 'hr' ); ?>>hr</option>
					<option value="hu"<?php selected( $value, 'hu' ); ?>>hu</option>
					<option value="id"<?php selected( $value, 'id' ); ?>>id</option>
					<option value="is"<?php selected( $value, 'is' ); ?>>is</option>
					<option value="it"<?php selected( $value, 'it' ); ?>>it</option>
					<option value="ja"<?php selected( $value, 'ja' ); ?>>ja</option>
					<option value="ka"<?php selected( $value, 'ka' ); ?>>ka</option>
					<option value="kk"<?php selected( $value, 'kk' ); ?>>kk</option>
					<option value="kr"<?php selected( $value, 'kr' ); ?>>kr</option>
					<option value="lt"<?php selected( $value, 'lt' ); ?>>lt</option>
					<option value="lv"<?php selected( $value, 'lv' ); ?>>lv</option>
					<option value="mk"<?php selected( $value, 'mk' ); ?>>mk</option>
					<option value="ms"<?php selected( $value, 'ms' ); ?>>ms</option>
					<option value="nb"<?php selected( $value, 'nb' ); ?>>nb</option>
					<option value="nl"<?php selected( $value, 'nl' ); ?>>nl</option>
					<option value="nl-BE"<?php selected( $value, 'nl-BE' ); ?>>nl-BE</option>
					<option value="no"<?php selected( $value, 'no' ); ?>>no</option>
					<option value="pl"<?php selected( $value, 'pl' ); ?>>pl</option>
					<option value="pt-BR"<?php selected( $value, 'pt-BR' ); ?>>pt-BR</option>
					<option value="pt"<?php selected( $value, 'pt' ); ?>>pt</option>
					<option value="ro"<?php selected( $value, 'ro' ); ?>>ro</option>
					<option value="rs"<?php selected( $value, 'rs' ); ?>>rs</option>
					<option value="rs-latin"<?php selected( $value, 'rs-latin' ); ?>>rs-latin</option>
					<option value="ru"<?php selected( $value, 'ru' ); ?>>ru</option>
					<option value="sk"<?php selected( $value, 'sk' ); ?>>sk</option>
					<option value="sl"<?php selected( $value, 'sl' ); ?>>sl</option>
					<option value="sq"<?php selected( $value, 'sq' ); ?>>sq</option>
					<option value="sv"<?php selected( $value, 'sv' ); ?>>sv</option>
					<option value="sw"<?php selected( $value, 'sw' ); ?>>sw</option>
					<option value="th"<?php selected( $value, 'th' ); ?>>th</option>
					<option value="tr"<?php selected( $value, 'tr' ); ?>>tr</option>
					<option value="ua"<?php selected( $value, 'ua' ); ?>>ua</option>
					<option value="vi"<?php selected( $value, 'vi' ); ?>>vi</option>
					<option value="zh-CN"<?php selected( $value, 'zh-CN' ); ?>>zh-CN</option>
					<option value="zh-TW"<?php selected( $value, 'zh-TW' ); ?>>zh-TW</option>
				</select>
				<?php
				break;

			case 'orientation' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Orientation', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Orientation', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Allows for fixed placement of the picker popup.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="auto"<?php selected( $value, 'auto' ); ?>><?php _e( 'auto', 'vfb-pro' ); ?></option>
				    <option value="top auto"<?php selected( $value, 'top auto' ); ?>><?php _e( 'top auto', 'vfb-pro' ); ?></option>
				    <option value="bottom auto"<?php selected( $value, 'bottom auto' ); ?>><?php _e( 'bottom auto', 'vfb-pro' ); ?></option>
				    <option value="auto left"<?php selected( $value, 'auto left' ); ?>><?php _e( 'auto left', 'vfb-pro' ); ?></option>
				    <option value="top left"<?php selected( $value, 'top left' ); ?>><?php _e( 'top left', 'vfb-pro' ); ?></option>
				    <option value="bottom left"<?php selected( $value, 'bottom left' ); ?>><?php _e( 'bottom left', 'vfb-pro' ); ?></option>
				    <option value="auto right"<?php selected( $value, 'auto right' ); ?>><?php _e( 'auto right', 'vfb-pro' ); ?></option>
				    <option value="top right"<?php selected( $value, 'top right' ); ?>><?php _e( 'top right', 'vfb-pro' ); ?></option>
				    <option value="bottom right"<?php selected( $value, 'bottom right' ); ?>><?php _e( 'bottom right', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'autoclose' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Autoclose', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Autoclose', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Whether or not to close the datepicker immediately when a date is selected.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'calendar-weeks' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Calendar Weeks', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Calendar Weeks', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Whether or not to show week numbers to the left of week rows.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'today-highlight' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[date][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Today Highlight', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Today Highlight', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If true, highlights the current date.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Time field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function time_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['time'][ $type ] ) ? $field['data']['time'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'donetext' :
				?>
				<label for="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Close Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Close Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The text displayed on the button to close the clockpicker.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[time][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'placement' :
				?>
				<label for="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Orientation', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Orientation', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Allows for fixed placement of the picker popup.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[time][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="bottom"<?php selected( $value, 'bottom' ); ?>><?php _e( 'bottom', 'vfb-pro' ); ?></option>
					<option value="top"<?php selected( $value, 'top' ); ?>><?php _e( 'top', 'vfb-pro' ); ?></option>
				    <option value="left"<?php selected( $value, 'left' ); ?>><?php _e( 'left', 'vfb-pro' ); ?></option>
				    <option value="right"<?php selected( $value, 'right' ); ?>><?php _e( 'right', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'align' :
				?>
				<label for="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Arrow Alignment', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Arrow Alignment', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Allows for alignment of the arrow on the picker popup.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[time][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>></option>
					<option value="left"<?php selected( $value, 'left' ); ?>><?php _e( 'left', 'vfb-pro' ); ?></option>
				    <option value="right"<?php selected( $value, 'right' ); ?>><?php _e( 'right', 'vfb-pro' ); ?></option>
					<option value="bottom"<?php selected( $value, 'bottom' ); ?>><?php _e( 'bottom', 'vfb-pro' ); ?></option>
					<option value="top"<?php selected( $value, 'top' ); ?>><?php _e( 'top', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'autoclose' :
				?>
				<label for="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[time][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Autoclose', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Autoclose', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Whether or not to close the clockpicker immediately when a minute is selected.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'twelvehour' :
				?>
				<label for="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[time][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-time-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Twelve Hour', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Twelve Hour', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Enables twelve hour mode with AM and PM buttons.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Currency field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function currency_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['currency'][ $type ] ) ? $field['data']['currency'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'sep' :
				?>
				<label for="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Thousand Separator', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Thousand Separator', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Controls the character used for the thousand separator. Default is a , (comma). NOTE: the Thousand and Decimal separators cannot be the same.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<option value="comma"<?php selected( $value, 'comma' ); ?>><?php _e( ', (comma)', 'vfb-pro' ); ?></option>
				    <option value="slash"<?php selected( $value, 'slash' ); ?>><?php _e( '\ (slash)', 'vfb-pro' ); ?></option>
				    <option value="period"<?php selected( $value, 'period' ); ?>><?php _e( '. (period)', 'vfb-pro' ); ?></option>
				    <option value="space"<?php selected( $value, 'space' ); ?>><?php _e( ' (space)', 'vfb-pro' ); ?></option>
				    <option value="none"<?php selected( $value, 'none' ); ?>><?php _e( '(none)', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'group' :
				?>
				<label for="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Thousand Separator Placement', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Thousand Separator Placement', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Controls the placement of the Thousand Separator character. Default is 3.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<option value="3"<?php selected( $value, 3 ); ?>><?php _e( '3 (333,333,333)', 'vfb-pro' ); ?></option>
				    <option value="2"<?php selected( $value, 2 ); ?>><?php _e( '2 (22,22,22,333)', 'vfb-pro' ); ?></option>
				    <option value="4"<?php selected( $value, 4 ); ?>><?php _e( '4 (4,4444,4444)', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'dec' :
				?>
				<label for="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Decimal Separator', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Decimal Separator', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Controls the character used for the decimal separator. Default is a . (period).", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<option value="period"<?php selected( $value, 'period' ); ?>><?php _e( '. (period)', 'vfb-pro' ); ?></option>
				    <option value="comma"<?php selected( $value, 'comma' ); ?>><?php _e( ', (comma)', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'sign' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Currency Symbol', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Currency Symbol', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Symbols, text, and spacing are allowed. NOTE: cannot contain apostrophe, comma, or numeric character.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'sign-place' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Currency Symbol Placement', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Currency Symbol Placement', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Controls the placement of the currency symbol. Default is to the left.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<option value="p"<?php selected( $value, 'p' ); ?>><?php _e( 'Left', 'vfb-pro' ); ?></option>
				    <option value="s"<?php selected( $value, 's' ); ?>><?php _e( 'Right', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'sign-display' :
				?>
				<label for="vfb-edit-date-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Input Display', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Input Display', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Controls the what is initially displayed in the currency input.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[currency][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-currency-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>><?php _e( '', 'vfb-pro' ); ?></option>
				    <option value="zero"<?php selected( $value, 'zero' ); ?>><?php _e( 'Zero value (0)', 'vfb-pro' ); ?></option>
				    <option value="sign"<?php selected( $value, 'sign' ); ?>><?php _e( 'Currency symbol', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Address field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */

	public function address_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['address'][ $type ] ) ? $field['data']['address'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'hide-addr-2' :
				?>
				<label for="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[address][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Address Line 2', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Address Line 2', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the Address Line 2 field from the Address block.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-country' :
				?>
				<label for="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[address][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Country', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Country', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the Country field from the Address block. NOTE: removing this field will disable the country-specific configurations.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-city' :
				?>
				<label for="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[address][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide City', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide City', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the City field from the Address block.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-state' :
				?>
				<label for="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[address][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide State', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide State', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the State field from the Address block.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-zip' :
				?>
				<label for="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[address][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-address-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Zip Code', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Country', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the Zip Code field from the Address block.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Default Country, used by the Address field
	 *
	 * @access public
	 * @param mixed $field
	 * @return void
	 */
	public function country( $field ) {
		$id    = $field['id'];
		$value = isset( $field['data']['country'] ) ? $field['data']['country'] : 'US';

		$html = array();
		$countries = include( VFB_PLUGIN_DIR . '/inc/countries.php' );
		foreach ( $countries as $code => $country ) {
			$selected = selected( $value, $code, false );
			$html[]   = sprintf( '<option value="%1$s"%3$s>%2$s</option>', $code, $country, $selected );
		}

		$list = implode('', $html);
	?>
		<div class="vfb-form-group">
			<label for="vfb-edit-country-<?php echo $id; ?>">
				<?php esc_html_e( 'Default Country', 'vfb-pro' ); ?>
			</label>

			<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Default Country', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the default selected country in the Address block.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

			<select name="vfb-field-<?php echo $id; ?>[country]" id="vfb-edit-country-<?php echo $id; ?>" class="vfb-form-control">
			<?php echo $list; ?>
			</select>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Phone field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function phone_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['phone'][ $type ] ) ? $field['data']['phone'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'default-country' :
				$html = array();
				$countries = include( VFB_PLUGIN_DIR . '/inc/countries.php' );
				foreach ( $countries as $code => $country ) {
					$selected = selected( $value, $code, false );
					$html[]   = sprintf( '<option value="%1$s"%3$s>%2$s</option>', $code, $country, $selected );
				}

				$list = implode('', $html);
				?>
				<label for="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Default Country', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Default Country', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the default country by the country code. If no country is selected, US will be the default.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[phone][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<?php echo $list; ?>
				</select>
				<?php
				break;

			case 'type' :
				?>
				<label for="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Number Type', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Number Type', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Tells the plugin what type of phone number to expect. Currently, this only sets the placeholder to the right type of number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[phone][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<option value=""<?php selected( $value, '' ); ?>><?php _e( '', 'vfb-pro' ); ?></option>
				    <option value="MOBILE"<?php selected( $value, 'MOBILE' ); ?>><?php _e( 'Mobile (recommended)', 'vfb-pro' ); ?></option>
				    <option value="FIXED_LINE"<?php selected( $value, 'FIXED_LINE' ); ?>><?php _e( 'Land Line', 'vfb-pro' ); ?></option>
				    <option value="TOLL_FREE"<?php selected( $value, 'TOLL_FREE' ); ?>><?php _e( 'Toll Free', 'vfb-pro' ); ?></option>
				    <option value="PREMIUM_RATE"<?php selected( $value, 'PREMIUM_RATE' ); ?>><?php _e( 'Premium Rate', 'vfb-pro' ); ?></option>
				    <option value="SHARED_COST"<?php selected( $value, 'SHARED_COST' ); ?>><?php _e( 'Shared Cost', 'vfb-pro' ); ?></option>
				    <option value="VOIP"<?php selected( $value, 'VOIP' ); ?>><?php _e( 'VOIP', 'vfb-pro' ); ?></option>
				    <option value="PAGER"<?php selected( $value, 'PAGER' ); ?>><?php _e( 'Pager', 'vfb-pro' ); ?></option>
				    <option value="UAN"<?php selected( $value, 'UAN' ); ?>><?php _e( 'Universal Access Numbers', 'vfb-pro' ); ?></option>
				    <option value="VOICEMAIL"<?php selected( $value, 'VOICEMAIL' ); ?>><?php _e( 'Voicemail', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'hide-flags' :
				?>
				<label for="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[phone][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Flags Dropdown', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Flags Dropdown', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this removes the flags dropdown.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'nation-mode' :
				?>
				<label for="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[phone][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'National Mode', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About National Mode', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this allows users to enter national numbers and not have to think about entering country codes.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-dial-code' :
				?>
				<label for="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[phone][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-phone-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Auto Hide Country Code', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Auto Hide Country Code', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If there is just a dial code in the input: remove it on blur, and re-add it on focus. This is to prevent just a dial code getting submitted with the form.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for File field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function file_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['file'][ $type ] ) ? $field['data']['file'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'max-file-size' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Max File Size', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Max File Size', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The maximum file size for upload in KB. If set to 0, it means the size allowed is unlimited. Defaults to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'max-file-count' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Max File Count', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Max File Count', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The maximum number of files allowed for upload. If set to 0, it means the number of files allowed is unlimited. Defaults to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'allowed-file-types' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Allowed File Types', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Allowed File Types', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "A comma-separated list of allowed file types for upload. By default, it is set to null and allows all file types. Available values: 'image', 'html', 'text', 'video', 'audio', 'flash', 'object'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'allowed-file-ext' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Allowed File Extensions', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Allowed File Extensions', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "A comma-separated list of allowed file extensions for upload. By default, it is set to null and allows all file extensions.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'label-browse' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Browse Label', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Browse Label', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The label to display for the file picker/browse button. Defaults to Browse.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'label-remove' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Remove Label', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Remove Label', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The label to display for the file remove button. Defaults to Remove.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'label-upload' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Upload Label', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Upload Label', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The label to display for the file upload button. Defaults to Upload.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'hide-preview' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Preview', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Preview', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the file preview.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-remove' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Remove Button', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Remove Button', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the Remove/Clear button.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-caption' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Caption', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Caption', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the file caption or file name.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'show-upload' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Show Upload Button', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Show Upload Button', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will display the Upload button. This will default to a submit button.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'multiple' :
				?>
				<label for="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[file][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-file-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Allow Multiple Uploads', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Allow Multiple Uploads', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will multiple files to be uploaded from a single file input.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Rating field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function rating_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['rating'][ $type ] ) ? $field['data']['rating'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'min' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Minimum Rating', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Minimum Rating', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The minimum rating value to start with. Defaults to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'max' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Maximum Rating', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Maximum Rating', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The maximum rating value to end with. Defaults to 5.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'empty' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Empty Rating', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Empty Rating', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The value that indicates an empty rating. Defaults to 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'icon' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Icon', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Icon', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the icon you wish to display as the rating.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
				    <option value="star-v1"<?php selected( $value, 'star-v1' ); ?>><?php _e( 'Star (v1)', 'vfb-pro' ); ?></option>
				    <option value="star-v2"<?php selected( $value, 'star-v2' ); ?>><?php _e( 'Star (v2)', 'vfb-pro' ); ?></option>
				    <option value="heart-v1"<?php selected( $value, 'heart-v1' ); ?>><?php _e( 'Heart (v1)', 'vfb-pro' ); ?></option>
				    <option value="heart-v2"<?php selected( $value, 'heart-v2' ); ?>><?php _e( 'Heart (v2)', 'vfb-pro' ); ?></option>
				    <option value="check-v1"<?php selected( $value, 'check-v1' ); ?>><?php _e( 'Check', 'vfb-pro' ); ?></option>
				    <option value="flag-v1"<?php selected( $value, 'flag-v1' ); ?>><?php _e( 'Flag', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'icon-remove' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Remove Icon', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Remove Icon', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the icon you wish to display if a Remove link is set.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
				    <option value="trash-v1"<?php selected( $value, 'trash-v1' ); ?>><?php _e( 'Trash', 'vfb-pro' ); ?></option>
				    <option value="close-v1"<?php selected( $value, 'close-v1' ); ?>><?php _e( 'Close (v1)', 'vfb-pro' ); ?></option>
				    <option value="close-v2"<?php selected( $value, 'close-v2' ); ?>><?php _e( 'Close (v2)', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'remove-text' :
				?>
				<label for="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Remove Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Remove Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "By default once you set a value it remains set and you can only change it by another. Add text to display a link that will clear the selection.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[rating][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-rating-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Name field settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function name_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['name'][ $type ] ) ? $field['data']['name'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'hide-title' :
				?>
				<label for="vfb-edit-name-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[name][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-name-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Title', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Title', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the Title part of the Name field.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-suffix' :
				?>
				<label for="vfb-edit-name-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[name][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-name-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Suffix', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Suffix', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the Suffix part of the Name field", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Password field settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function password_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['password'][ $type ] ) ? $field['data']['password'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'hide-meter' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Strength Bar', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Strength Bar', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the visual strength bar.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'hide-verdict' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Verdict Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Verdict Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the verdict text.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'verdict-inside' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Verdict Inside Bar', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Inside Bar', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will move the verdict text inside the strength bar instead of below the bar.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'text-0' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Verdict Score 1 Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Score 1 Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The display text for a password that scores a 1 out of 5 on the scale of password strength. Default is 'Too Short'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'text-1' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Verdict Score 2 Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Score 2 Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The display text for a password that scores a 2 out of 5 on the scale of password strength. Default is 'Very Weak'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'text-2' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Verdict Score 3 Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Score 3 Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The display text for a password that scores a 3 out of 5 on the scale of password strength. Default is 'Weak'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'text-3' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Verdict Score 4 Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Score 4 Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The display text for a password that scores a 4 out of 5 on the scale of password strength. Default is 'Medium'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'text-4' :
				?>
				<label for="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Verdict Score 5 Text', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Verdict Score 5 Text', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "The display text for a password that scores a 5 out of 5 on the scale of password strength. Default is 'Strong'.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[password][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-password-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Rating field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function captcha_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['captcha'][ $type ] ) ? $field['data']['captcha'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'theme' :
				?>
				<label for="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'reCAPTCHA Theme', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About reCAPTCHA Theme', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the color theme you of the reCAPTCHA widget. Default is Light.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[captcha][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>><?php _e( 'Light', 'vfb-pro' ); ?></option>
				    <option value="dark"<?php selected( $value, 'dark' ); ?>><?php _e( 'Dark', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'type' :
				?>
				<label for="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'reCAPTCHA Type', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About reCAPTCHA Type', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the type of reCAPTCHA to server. Default is Image.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[captcha][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>><?php _e( 'Image', 'vfb-pro' ); ?></option>
				    <option value="audio"<?php selected( $value, 'audio' ); ?>><?php _e( 'Audio', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'lang' :
				?>
				<label for="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'reCAPTCHA Language', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About reCAPTCHA Language', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the language for the reCAPTCHA text. Default is English.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[captcha][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-captcha-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>></option>
					<option value="en"<?php selected( $value, 'en' ); ?>>en</option>
					<option value="ar"<?php selected( $value, 'ar' ); ?>>ar</option>
					<option value="bg"<?php selected( $value, 'bg' ); ?>>bg</option>
					<option value="ca"<?php selected( $value, 'ca' ); ?>>ca</option>
					<option value="cs"<?php selected( $value, 'cs' ); ?>>cs</option>
					<option value="da"<?php selected( $value, 'da' ); ?>>da</option>
					<option value="de"<?php selected( $value, 'de' ); ?>>de</option>
					<option value="el"<?php selected( $value, 'el' ); ?>>el</option>
					<option value="es"<?php selected( $value, 'es' ); ?>>es</option>
					<option value="fa"<?php selected( $value, 'fa' ); ?>>fa</option>
					<option value="fi"<?php selected( $value, 'fi' ); ?>>fi</option>
					<option value="fi"<?php selected( $value, 'fil' ); ?>>fil</option>
					<option value="fr"<?php selected( $value, 'fr' ); ?>>fr</option>
					<option value="hr"<?php selected( $value, 'hr' ); ?>>hr</option>
					<option value="hu"<?php selected( $value, 'hu' ); ?>>hu</option>
					<option value="id"<?php selected( $value, 'id' ); ?>>id</option>
					<option value="it"<?php selected( $value, 'it' ); ?>>it</option>
					<option value="ja"<?php selected( $value, 'ja' ); ?>>ja</option>
					<option value="lt"<?php selected( $value, 'lt' ); ?>>lt</option>
					<option value="nl"<?php selected( $value, 'nl' ); ?>>nl</option>
					<option value="no"<?php selected( $value, 'no' ); ?>>no</option>
					<option value="pl"<?php selected( $value, 'pl' ); ?>>pl</option>
					<option value="pt-BR"<?php selected( $value, 'pt-BR' ); ?>>pt-BR</option>
					<option value="pt"<?php selected( $value, 'pt-PT' ); ?>>pt-PT</option>
					<option value="ro"<?php selected( $value, 'ro' ); ?>>ro</option>
					<option value="ru"<?php selected( $value, 'ru' ); ?>>ru</option>
					<option value="sk"<?php selected( $value, 'sk' ); ?>>sk</option>
					<option value="sl"<?php selected( $value, 'sl' ); ?>>sl</option>
					<option value="sv"<?php selected( $value, 'sv' ); ?>>sv</option>
					<option value="tr"<?php selected( $value, 'tr' ); ?>>tr</option>
					<option value="ua"<?php selected( $value, 'uk' ); ?>>uk</option>
					<option value="vi"<?php selected( $value, 'vi' ); ?>>vi</option>
					<option value="zh-CN"<?php selected( $value, 'zh-CN' ); ?>>zh-CN</option>
					<option value="zh-TW"<?php selected( $value, 'zh-TW' ); ?>>zh-TW</option>
				</select>
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Range Slider field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function rangeSlider_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['range-slider'][ $type ] ) ? $field['data']['range-slider'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'type' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider Type', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider Type', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Choose the slider type: either single, for one handle, or double, for two handles. Default is Single.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>><?php _e( 'Single', 'vfb-pro' ); ?></option>
				    <option value="double"<?php selected( $value, 'double' ); ?>><?php _e( 'Double', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'min' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider Min', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider Min', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set slider minimum value. Default is 10.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'max' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider Max', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider Max', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set slider maximum value. Default is 100.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'from' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider From', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider From', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set start position for left handle (or for single handle).", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'to' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider To', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider To', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set start position for right handle.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'step' :
				?>
				<label for="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Slider Step', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Slider Step', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set sliders step. Always > 0. Could be fractional.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[range-slider][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-range-slider-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;
		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Knob field Advanced Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function knob_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['knob'][ $type ] ) ? $field['data']['knob'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'min' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Knob Min', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Min', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set knob minimum value. Default is 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'max' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Knob Max', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Max', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob maximum value. Default is 100.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'step' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Knob Step', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Step', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knobs step. Always > 0. Default is 1.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'angle-offset' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Angle Offset', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Angle Offset', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knobs starting angle in degrees. Default is 0.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'angle-arc' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Angle Arc', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Angle Arc', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knobs arc size in degrees. Default is 360.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'rotation' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Rotation', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Rotation', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the Knob direction of progress. Default is Clockwise.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>><?php _e( 'Clockwise', 'vfb-pro' ); ?></option>
				    <option value="anticlockwise"<?php selected( $value, 'anticlockwise' ); ?>><?php _e( 'Counter Clockwise', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'thickness' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Gauge Thickness', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Thickness', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob gauge thickness. Default is about 0.35.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'line-cap' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Line Cap', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Line Cap', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the Knob gauge stroke endings. Default is Flat.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>><?php _e( 'Flat', 'vfb-pro' ); ?></option>
				    <option value="round"<?php selected( $value, 'round' ); ?>><?php _e( 'Round', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'width' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Width', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Width', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob width, in pixels. Default is 200.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'fg-color' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Foreground Color', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Foreground Color', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob foreground color using six digit hex color code. Default is #87CEEB.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'input-color' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Input Color', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Input Color', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob input color using six digit hex color code. Default is #87CEEB.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'bg-color' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Background Color', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Knob Background Color', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set Knob background color using six digit hex color code. Default is #EEEEEE.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'hide-input' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Hide Input', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Hide Input', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, this will hide the number input box associated with the knob controller.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

			case 'display-previous' :
				?>
				<label for="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>">
					<input type="checkbox" value="1"<?php checked( $value, 1 ); ?> name="vfb-field-<?php echo $id; ?>[knob][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-knob-<?php echo "{$type}-{$id}"; ?>" />
					<?php esc_html_e( 'Display Previous Value', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Display Previous Value', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "If checked, the knob will display the previously selected value with transparency.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>
				<?php
				break;

		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}

	/**
	 * Helper for Validation Settings
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $type
	 * @return void
	 */
	public function validation_settings( $field, $type ) {
		$id    = $field['id'];
		$value = isset( $field['data']['validation'][ $type ] ) ? $field['data']['validation'][ $type ] : '';
	?>
		<div class="vfb-form-group">
	<?php
		switch ( $type ) :
			case 'type' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Validation Type', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Validation Type', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Set the validation type.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<select name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
				    <option value=""<?php selected( $value, '' ); ?>></option>
				    <option value="email"<?php selected( $value, 'email' ); ?>><?php _e( 'Email', 'vfb-pro' ); ?></option>
				    <option value="number"<?php selected( $value, 'number' ); ?>><?php _e( 'Number', 'vfb-pro' ); ?></option>
				    <option value="integer"<?php selected( $value, 'integer' ); ?>><?php _e( 'Integer', 'vfb-pro' ); ?></option>
				    <option value="digits"<?php selected( $value, 'digits' ); ?>><?php _e( 'Digits', 'vfb-pro' ); ?></option>
				    <option value="alphanum"<?php selected( $value, 'alphanum' ); ?>><?php _e( 'Alphanumeric', 'vfb-pro' ); ?></option>
				    <option value="url"<?php selected( $value, 'url' ); ?>><?php _e( 'URL', 'vfb-pro' ); ?></option>
				</select>
				<?php
				break;

			case 'pattern' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Regex Pattern', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Regex Pattern', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a value matches a specific regular expression (regex).", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'equalto' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Equal To', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Equal To', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the value is identical to another field value (useful for password confirmation check). Enter the VFB field ID number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'minlength' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Min Length', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Min Length', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the length of a string is at least as long as the given limit. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'maxlength' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Max Length', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Max Length', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the length of a string is not larger than the given limit. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'length' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Length', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Length', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a given string length is between some minimum and maximum value. Must be entered as two integers separated by a comma. Example: 6,10", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'min' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Min', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Min', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a given number is greater than some minimum number. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'max' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Max', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Max Length', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a given number is less than some maximum number. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'range' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Range', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Range', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a given number is between some minimum and maximum number. Must be entered as two integers separated by a comma. Example: 6,10", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'mincheck' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Min Check', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Min Check', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a certain minimum number of checkboxes in a group are checked. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'maxcheck' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Max Check', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Max Check', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that a certain maximum number of checkboxes in a group are checked. Integers only.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'check' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Check Range', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Check Range', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the number of checked checkboxes in a group is within a certain range. Must be entered as two integers separated by a comma. Example: 6,10", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="text" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'gt' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Greater Than', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Greater Than', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the value is greater than another field's one.  Enter the VFB field ID number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'gte' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Greater Than Equal To', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Greater Than Equal To', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the value is greater than or equal to another field's one.  Enter the VFB field ID number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'lt' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Less Than', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Less Than', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the value is less than another field's one.  Enter the VFB field ID number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

			case 'lte' :
				?>
				<label for="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>">
					<?php esc_html_e( 'Less Than Equal To', 'vfb-pro' ); ?>
				</label>

				<span class="vfb-tooltip vfb-pull-right" data-title="<?php esc_attr_e( 'About Less Than Equal To', 'vfb-pro' ); ?>" data-content="<?php esc_attr_e( "Validates that the value is less than or equal to another field's one.  Enter the VFB field ID number.", 'vfb-pro' ); ?>"><i class="vfb-icon-question"></i></span>

				<input type="number" value="<?php esc_attr_e( $value ); ?>" name="vfb-field-<?php echo $id; ?>[validation][<?php echo $type; ?>]" class="vfb-form-control" id="vfb-edit-validation-<?php echo "{$type}-{$id}"; ?>" />
				<?php
				break;

		endswitch;
	?>
		</div> <!-- .vfb-form-group -->
	<?php
	}
}