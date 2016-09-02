<?php
/**
 * Class that controls the Edit Fields tab
 *
 * @since 3.0
 */
class VFB_Pro_Forms_Edit_Fields {
	/**
	 * The form ID
	 *
	 * @var mixed
	 * @access private
	 */
	private $id;

	/**
	 * Assign form ID when class is loaded
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_forms' ) )
			return;

		$vfbdb  = new VFB_Pro_Data();
		$fields = $vfbdb->get_fields( $this->id );
		$forms  = $vfbdb->get_all_forms( ' WHERE status = "publish" ' );

		$trash_url = add_query_arg(
			array(
				'page'       => 'vfb-pro',
				'vfb-action' => 'trash-form',
				'form'       => $this->id,
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_trash_form' )
		);

		$dupe_url = add_query_arg(
			array(
				'page'       => 'vfb-pro',
				'vfb-action' => 'duplicate-form',
				'form'       => $this->id,
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_duplicate_form' )
		);

		$preview_url = add_query_arg(
			array(
				'preview'     => true,
				'vfb-form-id' => $this->id,
			),
			get_permalink( get_page_by_title( 'VFB Pro - Form Preview' ) )
		);
	?>
	<div id="vfb-edit-frame">
		<div id="vfb-fields-column" class="metabox-holder">
			<div id="side-sortables" class="meta-box-sortables">
				<div class="vfb-accordion-container">
					<ul class="outer-border">
						<li class="vfb-control-section vfb-accordion-section open">
							<h3 class="vfb-accordion-section-title">
								<?php _e( 'Fields', 'vfb-pro' ); ?>
							</h3>
							<div class="vfb-accordion-section-content">
								<p><?php _e( 'Click or Drag to add fields', 'vfb-pro' ); ?><span class="spinner"></span></p>

								<form id="vfb-form-items" method="post" action="">
									<input name="_vfbp_action" type="hidden" value="create-field" />
									<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
									<?php
										wp_nonce_field( 'vfbp_create_field' );

										$this->standard_fields();
										$this->advanced_fields();

										if ( is_plugin_active( 'vfbp-create-user/vfbp-create-user.php' ) ) {
											$this->create_user_fields();
										}

										if ( is_plugin_active( 'vfbp-create-post/vfbp-create-post.php' ) ) {
											$this->create_post_fields();
										}
									?>
								</form>
							</div> <!-- .vfb-accordion-section-content -->
						</li>
						<li class="vfb-control-section vfb-accordion-section">
							<h3 class="vfb-accordion-section-title">
								<?php _e( 'Shortcode', 'vfb-pro' ); ?>
							</h3>
							<div class="vfb-accordion-section-content">
								<p><?php _e( 'Add forms to your Posts or Pages by locating the <span class="dashicons dashicons-feedback"></span> <strong>VFB Pro</strong> button in the area above your post/page editor.', 'vfb-pro' ); ?></p>
								<p>
						    		<?php _e( 'Shortcode', 'vfb-pro' ); ?>
						    		<input value="[vfb id=<?php echo $this->id; ?>]" readonly="readonly" />
						    	</p>
							</div> <!-- .vfb-accordion-section-content -->
						</li>
						<li class="vfb-control-section vfb-accordion-section">
							<h3 class="vfb-accordion-section-title">
								<?php _e( 'Other Forms', 'vfb-pro' ); ?>
							</h3>
							<div class="vfb-accordion-section-content">
								<p><?php _e( 'Select a form to edit:', 'vfb-pro' ); ?></p>
								<select id="vfb-forms-switcher">
								<?php
									if ( is_array( $forms ) && !empty( $forms ) ) {
										foreach ( $forms as $form ) {
											echo sprintf(
												'<option value="%1$d"%3$s>%1$d - %2$s</option>',
												$form['id'],
												$form['title'],
												selected( $form['id'], $this->id, 0 )
											);
										}
									}
								?>
								</select>
								<a href="#" id="vfb-forms-switch-btn" class="button"><?php _e( 'Select', 'vfb-pro' ); ?></a>
							</div> <!-- .vfb-accordion-section-content -->
						</li>
					</ul>
				</div> <!-- .vfb-accordion-container -->
			</div> <!-- .meta-box-sortables -->
		</div> <!-- #vfb-fields-column -->

		<div id="vfb-fields-management">
			<div id="vfb-fields-edit">
				<form method="post" action="">
					<input name="_vfbp_action" type="hidden" value="save-fields" />
					<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
					<?php
						wp_nonce_field( 'vfbp_fields_settings' );
					?>

					<div id="vfb-form-editor-header">
						<?php if ( current_user_can( 'vfb_delete_forms' ) ) : ?>
							<a href="<?php echo esc_url( $trash_url ); ?>" class="button vfb-button-trash">
								<i class="vfb-icon-remove"></i>&nbsp;<?php _e( 'Trash', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( current_user_can( 'vfb_copy_forms' ) ) : ?>
							<a href="<?php echo esc_url( $dupe_url ); ?>" class="button vfb-button-duplicate">
								<i class="vfb-icon-copy"></i>&nbsp;<?php _e( 'Duplicate', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( current_user_can( 'vfb_edit_forms' ) ) : ?>
							<a href="<?php echo esc_url( $preview_url ); ?>" class="button vfb-button-preview" target="_blank">
								<i class="vfb-icon-search"></i>&nbsp;<?php _e( 'Preview', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php
							submit_button(
								__( 'Save Changes', 'vfb-pro' ),
								'primary',
								'', // leave blank so "name" attribute will not be added
								false
							);
						?>
					</div> <!-- #vfb-form-editor-header -->

					<div id="vfb-fields-container">
						<div id="vfb-fields-content">
							<ul id="vfb-fields-list" class="ui-sortable droppable">
						        <?php
							        if( is_array( $fields ) && !empty( $fields ) ) {
										foreach( $fields as $field ){
											$this->edit_field( $field['id'] );
										}
									}
							    ?>
							</ul>
						</div> <!-- #vfb-fields-content -->
					</div> <!-- #vfb-fields-container -->

					<div id="vfb-form-editor-footer">
						<?php if ( current_user_can( 'vfb_delete_forms' ) ) : ?>
							<a href="<?php echo esc_url( $trash_url ); ?>" class="button vfb-button-trash">
								<i class="vfb-icon-remove"></i>&nbsp;<?php _e( 'Trash', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( current_user_can( 'vfb_copy_forms' ) ) : ?>
							<a href="<?php echo esc_url( $dupe_url ); ?>" class="button vfb-button-duplicate">
								<i class="vfb-icon-copy"></i>&nbsp;<?php _e( 'Duplicate', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( current_user_can( 'vfb_edit_forms' ) ) : ?>
							<a href="<?php echo esc_url( $preview_url ); ?>" class="button vfb-button-preview" target="_blank">
								<i class="vfb-icon-search"></i>&nbsp;<?php _e( 'Preview', 'vfb-pro' ); ?>
							</a>
						<?php endif; ?>

						<?php
							submit_button(
								__( 'Save Changes', 'vfb-pro' ),
								'primary',
								'', // leave blank so "name" attribute will not be added
								false
							);
						?>
					</div> <!-- #vfb-form-editor-footer -->
				</form>
			</div> <!-- #vfb-fields-edit -->
		</div> <!-- #vfb-fields-management -->
	</div> <!-- #vfb-edit-frame -->
	<?php
	}

	/**
	 * standard_fields function.
	 *
	 * @access private
	 * @return void
	 */
	private function standard_fields() {
	?>
	<div id="vfb-standard-fields">
		<ul class="vfb-fields-col-1">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-heading">Heading</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-text"><i class="vfb-icon-type"></i>Text</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-checkbox"><i class="vfb-icon-checkbox-checked"></i>Checkbox</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-select"><i class="vfb-icon-menu"></i>Select</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-datepicker"><i class="vfb-icon-calendar"></i>Date</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-url"><i class="vfb-icon-link"></i>URL</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-digits"><i class="vfb-icon-seven-segment-2"></i>Number</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-phone"><i class="vfb-icon-phone"></i>Phone</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-file"><i class="vfb-icon-attachment"></i>File Upload</a></li>
		</ul>

		<ul class="vfb-fields-col-2">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-submit"><i class="vfb-icon-key-keyboard"></i>Submit</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-textarea"><i class="vfb-icon-file"></i>Textarea</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-radio"><i class="vfb-icon-radio-checked"></i>Radio</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-address"><i class="vfb-icon-notebook"></i>Address</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-email"><i class="vfb-icon-envelop"></i>Email</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-currency"><i class="vfb-icon-coin"></i>Currency</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-time"><i class="vfb-icon-alarm"></i>Time</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-html"><i class="vfb-icon-code"></i>HTML</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-instructions"><i class="vfb-icon-bubble"></i>Instructions</a></li>
		</ul>
		<div class="clear"></div>
	</div> <!-- #vfb-standard-fields -->
	<?php
	}

	/**
	 * advanced_fields function.
	 *
	 * @access private
	 * @return void
	 */
	private function advanced_fields() {
	?>
	<div id="vfb-advanced-fields">
		<ul class="vfb-fields-col-1">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-name"><i class="vfb-icon-vcard"></i>Name</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-hidden"><i class="vfb-icon-eye"></i>Hidden</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-autocomplete"><i class="vfb-icon-puzzle"></i>Autocomplete</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-min"><i class="vfb-icon-arrow-down"></i>Min</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-range"><i class="vfb-icon-arrow"></i>Range</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-likert"><i class="vfb-icon-table"></i>Likert</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-knob"><i class="vfb-icon-spinner"></i>Knob</a></li>
		</ul>

		<ul class="vfb-fields-col-2">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-captcha"><i class="vfb-icon-warning"></i>Captcha</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-color"><i class="vfb-icon-eyedropper"></i>Color Picker</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-range-slider"><i class="vfb-icon-settings"></i>Range Slider</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-max"><i class="vfb-icon-arrow-up"></i>Max</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-pagebreak"><i class="vfb-icon-page-break"></i>Page Break</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-rating"><i class="vfb-icon-thumbs-up"></i>Rating</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-signature"><i class="vfb-icon-quill"></i>Signature</a></li>
		</ul>
		<div class="clear"></div>
	</div> <!-- #vfb-advanced-fields -->
	<?php
	}

	/**
	 * create_user_fields function.
	 *
	 * @access private
	 * @return void
	 */
	private function create_user_fields() {
	?>
	<div id="vfb-create-user-fields">
		<p><?php _e( 'Create User fields', 'vfb-pro' ); ?></p>
		<ul class="vfb-fields-col-1">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-username"><i class="vfb-icon-user"></i>Username</a></li>
		</ul>

		<ul class="vfb-fields-col-2">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-password"><i class="vfb-icon-key"></i>Password</a></li>
		</ul>
		<div class="clear"></div>
	</div> <!-- #vfb-create-user-fields -->
	<?php
	}

	/**
	 * create_post_fields function.
	 *
	 * @access private
	 * @return void
	 */
	private function create_post_fields() {
	?>
	<div id="vfb-create-post-fields">
		<p><?php _e( 'Create Post fields', 'vfb-pro' ); ?></p>
		<ul class="vfb-fields-col-1">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-posttitle"><i class="vfb-icon-pushpin"></i>Post Title</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-postexcerpt"><i class="vfb-icon-quotes-left"></i>Post Excerpt</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-posttag"><i class="vfb-icon-tag"></i>Post Tag</a></li>
		</ul>

		<ul class="vfb-fields-col-2">
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-postcontent"><i class="vfb-icon-pencil"></i>Post Content</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-postcategory"><i class="vfb-icon-drawer"></i>Post Category</a></li>
			<li><a href="#" class="vfb-draggable-form-items" id="form-element-postcustomfield"><i class="vfb-icon-starburst"></i>Custom Field</a></li>
		</ul>
		<div class="clear"></div>
	</div> <!-- #vfb-create-post-fields -->
	<?php
	}

	/**
	 * A wrapper function to output the Edit Field box
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function edit_field( $id ) {
		$field = new VFB_Pro_Admin_Fields();
		$field->field_output( $id );
	}
}