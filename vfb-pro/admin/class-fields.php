<?php
/**
 * Class that outputs Edit Fields box HTML and DB values
 *
 * @since 3.0
 */
class VFB_Pro_Admin_Fields {

	/**
	 * Main Edit Field Output
	 *
	 * Contains all inputs and settings for each form item
	 *
	 * @access public
	 * @param mixed $form_id
	 * @return void
	 */
	public function field_output( $form_id ) {
		$vfbdb  = new VFB_Pro_Data();
		$field  = $vfbdb->get_field_by_id( $form_id );

		$setting = new VFB_Pro_Admin_Fields_Settings( $field['id'] );

		$trash_url = add_query_arg(
			array(
				'page'       => 'vfb-pro',
				'vfb-action' => 'delete-field',
				'field'      => $field['id'],
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_trash_form' )
		);

		$dupe_url = add_query_arg(
			array(
				'page'       => 'vfb-pro',
				'vfb-action' => 'duplicate-field',
				'field'      => $field['id'],
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_duplicate_form' )
		);

		$required          = isset( $field['data']['required'] ) ? $field['data']['required'] : '';
		$required_asterisk = !empty( $required ) || 'captcha' == $field['field_type'] ? ' *' : '';
	?>
	<li id="vfb-field-item-<?php echo $field['id']; ?>" class="vfb-field-item">
    	<dl class="vfb-field-item-bar vfb-field-item-inactive">
        	<dt class="vfb-field-item-handle">
        		<span class="vfb-field-item-title">
        			<?php echo $field['data']['label']; ?>
        			<span class="vfb-required-asterisk"><?php echo $required_asterisk; ?></span>
        			<span class="vfb-hover-field-id"><?php _e( 'Field ID:', 'vfb-pro' ); ?> <?php echo $field['id']; ?></span>
        		</span>
        		<span class="vfb-field-item-controls">
        			<span class="vfb-field-item-type"><?php echo str_replace( '-', ' ', $field['field_type'] ); ?></span>
        			<a href="#" title="Edit Field Item" id="vfb-edit-<?php echo $field['id']; ?>" class="vfb-item-edit-link">
	        			<?php esc_html_e( 'Edit Field Item', 'vfb-pro' ); ?>
	        		</a>
        		</span>
        	</dt>
    	</dl>

    	<div class="vfb-field-item-settings">
	    	<div class="vfb-row">
				<div class="vfb-col-12">
					<?php $setting->name( $field ); ?>
				</div> <!-- .vfb-col-12 -->
	    	</div> <!-- .vfb-row -->

	    	<?php
		    	$type = str_replace( '-', '_', $field['field_type'] );
		    	if ( method_exists( $this, $type ) && is_callable( array( $this, $type ) ) ) {
		    		call_user_func( array( $this, $type ), $field, $setting );
		    	}
		    ?>
			<div class="vfb-row">
				<div class="vfb-col-6">
					<a href="<?php echo esc_url( $trash_url ); ?>" class="button vfb-button-trash vfb-field-delete vfb-pull-right">
						<i class="vfb-icon-remove"></i>&nbsp;<?php _e( 'Delete', 'vfb-pro' ); ?>
					</a>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<a href="<?php echo esc_url( $dupe_url ); ?>" class="button vfb-button-duplicate vfb-field-duplicate">
						<i class="vfb-icon-copy"></i>&nbsp;<?php _e( 'Duplicate', 'vfb-pro' ); ?>
					</a>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-row -->
    	</div> <!-- .vfb-field-item-settings -->
    	<?php if ( 'page-break' == $field['field_type'] ) : ?>
	    	<div class="vfb-page-break-instructions" style="">
				<span class="dashicons dashicons-arrow-down-alt"></span>
				<?php _e( 'All fields <strong>BELOW</strong> this Page Break will be contained on the page.', 'vfb-pro' ); ?>
				<span class="dashicons dashicons-arrow-down-alt"></span>
			</div> <!-- .vfb-page-break-instructions -->
    	<?php endif; ?>
    </li>
    <?php
	}

	/**
	 * Heading field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function heading( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field, 'wp_editor' ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<?php $setting->heading_settings( $field, 'heading-type' ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-4 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<?php $setting->heading_settings( $field, 'heading-bg' ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Submit field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function submit( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Text field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function text( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'type' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'minlength' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'maxlength' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'length' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'min' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'max' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'range' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-3">
					<?php $setting->validation_settings( $field, 'gt' ); ?>
				</div> <!-- .vfb-col-3 -->

				<div class="vfb-col-3">
					<?php $setting->validation_settings( $field, 'gte' ); ?>
				</div> <!-- .vfb-col-3 -->

				<div class="vfb-col-3">
					<?php $setting->validation_settings( $field, 'lt' ); ?>
				</div> <!-- .vfb-col-3 -->

				<div class="vfb-col-3">
					<?php $setting->validation_settings( $field, 'lte' ); ?>
				</div> <!-- .vfb-col-3 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Textarea field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function textarea( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->min_words( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->max_words( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->textarea_rows( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Checkbox field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function checkbox( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->options( $field ); ?>
			</div> <!-- .vfb-col-12 -->
    	</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout_options( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'mincheck' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'maxcheck' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->validation_settings( $field, 'check' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Radio field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function radio( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->options( $field ); ?>
			</div> <!-- .vfb-col-12 -->
    	</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->allow_other( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout_options( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Select field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function select( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->options( $field ); ?>
			</div> <!-- .vfb-col-12 -->
    	</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Address field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function address( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->country( $field ); ?>
			</div> <!-- .vfb-col-6 -->
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->address_settings( $field, 'hide-addr-2' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->address_settings( $field, 'hide-country' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->address_settings( $field, 'hide-city' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->address_settings( $field, 'hide-state' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->address_settings( $field, 'hide-zip' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Date field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function date( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-3">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-9">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-9 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-6">
					<?php $setting->date_settings( $field, 'format' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->date_settings( $field, 'days-of-week-disabled' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->date_settings( $field, 'start-date' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->date_settings( $field, 'end-date' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'week-start' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'start-view' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'min-view-mode' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'today-btn' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'language' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'orientation' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'autoclose' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'calendar-weeks' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->date_settings( $field, 'today-highlight' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-adv-settings -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Email field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function email( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * URL field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function url( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-field-validation-settings">
				<div class="vfb-col-12">
					<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
				</div> <!-- .vfb-col-12 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Currency field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function currency( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-3">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-9">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-9 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'sep' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'group' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'dec' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'sign' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'sign-place' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->currency_settings( $field, 'sign-display' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-adv-settings -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Number field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function number( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Time field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function time( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-3">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-9">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-9 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->time_settings( $field, 'donetext' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->time_settings( $field, 'placement' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->time_settings( $field, 'align' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->time_settings( $field, 'autoclose' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->time_settings( $field, 'twelvehour' ); ?>
				</div> <!-- .vfb-col-4 -->
			</div> <!-- .vfb-field-adv-settings -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Phone field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function phone( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-6">
					<?php $setting->phone_settings( $field, 'default-country' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->phone_settings( $field, 'type' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->phone_settings( $field, 'hide-flags' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->phone_settings( $field, 'nation-mode' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->phone_settings( $field, 'hide-dial-code' ); ?>
				</div> <!-- .vfb-col-4 -->

			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * HTML field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function html( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * File Upload field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function file_upload( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-6">
					<?php $setting->file_settings( $field, 'max-file-size' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->file_settings( $field, 'max-file-count' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->file_settings( $field, 'allowed-file-types' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->file_settings( $field, 'allowed-file-ext' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'label-browse' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'label-remove' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'label-upload' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'hide-preview' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'hide-remove' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'hide-caption' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'show-upload' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->file_settings( $field, 'multiple' ); ?>
				</div> <!-- .vfb-col-4 -->

			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Instructions field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function instructions( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field, 'wp_editor' ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Username field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function username( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->user_mode( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Password field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function password( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-3">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-9">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-9 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->password_settings( $field, 'hide-meter' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->password_settings( $field, 'hide-verdict' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->password_settings( $field, 'verdict-inside' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-6">
					<?php $setting->password_settings( $field, 'text-0' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->password_settings( $field, 'text-1' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->password_settings( $field, 'text-2' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->password_settings( $field, 'text-3' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->password_settings( $field, 'text-4' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-adv-settings -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Hidden field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function hidden( $field, $setting ) {
	?>

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->hidden_options( $field, 'option' ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->hidden_options( $field, 'custom' ); ?>
			</div> <!-- .vfb-col-6 -->

		</div>

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->hidden_options( $field, 'seq-start' ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->hidden_options( $field, 'seq-step' ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Color Picker field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function color_picker( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Autocomplete field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function autocomplete( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->options( $field ); ?>
			</div> <!-- .vfb-col-12 -->
    	</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Range Slider field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function range_slider( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-4 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'type' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'min' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'max' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'from' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'to' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->rangeSlider_settings( $field, 'step' ); ?>
				</div> <!-- .vfb-col-6 -->

			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Min field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function min( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->min_num( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Max field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function max( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->max_num( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Range field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function range( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->min_num( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->max_num( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-validation-settings-<?php echo $field['id']; ?>" class="vfb-field-validation-settings-link"><?php _e( 'Validation Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-validation-settings">
				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'pattern' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->validation_settings( $field, 'equalto' ); ?>
				</div> <!-- .vfb-col-6 -->
			</div> <!-- .vfb-field-validation-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Page Break field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function page_break( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Name field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function name( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->name_settings( $field, 'hide-title' ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->name_settings( $field, 'hide-suffix' ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Rating field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function rating( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'min' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'max' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'empty' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'icon' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'remove-text' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->rating_settings( $field, 'icon-remove' ); ?>
				</div> <!-- .vfb-col-4 -->

			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Likert field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function likert( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->likert_rows( $field ); ?>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-col-12">
				<?php $setting->likert_cols( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Knob field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function knob( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-4 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row vfb-field-adv-container">
			<div class="vfb-col-12">
				<a href="#" id="vfb-adv-settings-<?php echo $field['id']; ?>" class="vfb-field-adv-settings-link"><?php _e( 'Advanced Settings', 'vfb-pro' ); ?><span class="dashicons dashicons-arrow-down"></span></a>
			</div> <!-- .vfb-col-12 -->

			<div class="vfb-field-adv-settings">
				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'min' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'max' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'step' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'angle-offset' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'angle-arc' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'thickness' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'width' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'fg-color' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'input-color' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-4">
					<?php $setting->knob_settings( $field, 'bg-color' ); ?>
				</div> <!-- .vfb-col-4 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'rotation' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'line-cap' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'hide-input' ); ?>
				</div> <!-- .vfb-col-6 -->

				<div class="vfb-col-6">
					<?php $setting->knob_settings( $field, 'display-previous' ); ?>
				</div> <!-- .vfb-col-6 -->

			</div> <!-- .vfb-field-adv-settings -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Signature field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function signature( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-4 -->

		</div> <!-- .vfb-row -->
	<?php
	}
	/**
	 * Captcha field display
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function captcha( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-3">
				<?php $setting->captcha_settings( $field, 'theme' ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->captcha_settings( $field, 'type' ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-6">
				<?php $setting->captcha_settings( $field, 'lang' ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Post Title field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function post_title( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Post Content field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function post_content( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Post Excerpt field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function post_excerpt( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->default_value( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->placeholder( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->input_mask( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Post Category field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function post_category( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Post Tag field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function post_tag( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}

	/**
	 * Create Post - Custom Field field display.
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $setting
	 * @return void
	 */
	public function custom_field( $field, $setting ) {
	?>
		<div class="vfb-row">
			<div class="vfb-col-12">
				<?php $setting->description( $field ); ?>
			</div> <!-- .vfb-col-12 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->css( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-3">
				<?php $setting->description_position( $field ); ?>
			</div> <!-- .vfb-col-3 -->

			<div class="vfb-col-3">
				<?php $setting->required( $field ); ?>
			</div> <!-- .vfb-col-3 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-6">
				<?php $setting->layout( $field ); ?>
			</div> <!-- .vfb-col-6 -->

			<div class="vfb-col-6">
				<?php $setting->create_post_meta_key( $field ); ?>
			</div> <!-- .vfb-col-6 -->
		</div> <!-- .vfb-row -->
	<?php
	}
}