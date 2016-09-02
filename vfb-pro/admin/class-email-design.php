<?php
/**
 * Class that controls the Add New Form view
 *
 * @since 3.0
 */
class VFB_Pro_Edit_Email_Design {

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

		//Template fallback
		add_action( 'template_redirect', array( $this, 'preview_redirect' ) );
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_email_design' ) )
			return;

		$vfbdb = new VFB_Pro_Data();
		$data  = $vfbdb->get_email_design_settings( $this->id );

		$format     = isset( $data['email-format'] ) ? $data['email-format'] : '';
		$template   = isset( $data['email-template'] ) ? $data['email-template'] : $this->html_template();
		$premailer  = isset( $data['email-premailer']  ) ? $data['email-premailer']  : '';
		$color_bg   = isset( $data['email-design']['color-bg']   ) ? $data['email-design']['color-bg']   : '#fbfbfb';
		$color_link = isset( $data['email-design']['color-link'] ) ? $data['email-design']['color-link'] : '#41637e';
		$color_h1   = isset( $data['email-design']['color-h1']   ) ? $data['email-design']['color-h1']   : '#565656';
		$font_h1    = isset( $data['email-design']['font-h1']    ) ? $data['email-design']['font-h1']    : 'Arial';
		$color_h2   = isset( $data['email-design']['color-h2']   ) ? $data['email-design']['color-h2']   : '#555555';
		$font_h2    = isset( $data['email-design']['font-h2']    ) ? $data['email-design']['font-h2']    : 'Georgia';
		$color_h3   = isset( $data['email-design']['color-h3']   ) ? $data['email-design']['color-h3']   : '#555555';
		$font_h3    = isset( $data['email-design']['font-h3']    ) ? $data['email-design']['font-h3']    : 'Georgia';
		$color_text = isset( $data['email-design']['color-text'] ) ? $data['email-design']['color-text'] : '#565656';
		$font_text  = isset( $data['email-design']['font-text']  ) ? $data['email-design']['font-text']  : 'Georgia';
		$link_love  = isset( $data['email-design']['link-love']  ) ? $data['email-design']['link-love']  : '';
		$skip_empty = isset( $data['email-design']['skip-empty'] ) ? $data['email-design']['skip-empty'] : '';
		$header_img = isset( $data['email-design']['header-img'] ) ? $data['email-design']['header-img'] : '';

		$header_img_src = wp_get_attachment_image_src( $header_img, 'full' );

		// Make sure an Email Preview page exists
		$this->preview_page_check();

		$preview_url = add_query_arg(
			array(
				'vfb-preview' => 1,
				'vfb-form-id' => $this->id,
				'TB_iframe'   => true,
				'width'       => 600,
				'height'      => 550,

			),
			get_permalink( get_page_by_title( 'VFB Pro - Email Preview' ) )
		);
	?>
	<form method="post" id="vfbp-confirmation-settings" action="">
		<input name="_vfbp_action" type="hidden" value="save-design-settings" />
		<input name="_vfbp_form_id" type="hidden" value="<?php echo $this->id; ?>" />
		<?php
			wp_nonce_field( 'vfbp_design_settings' );
		?>

		<div class="vfb-edit-section">
			<div class="vfb-edit-section-inside">
				<table class="form-table fixed">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="format"><?php _e( 'Format' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<select name="settings[email-format]" id="format">
									<option value="html"<?php selected( 'html', $format ); ?>><?php _e( 'HTML', 'vfb-pro' ); ?></option>
									<option value="plain-text"<?php selected( 'plain-text', $format ); ?>><?php _e( 'Plain Text', 'vfb-pro' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
					<tbody id="vfb-email-html" class="vfb-email-type<?php echo 'html' == $format || empty( $format ) ? ' active' : ''; ?>">
						<tr valign="top">
							<th scope="row">
								<label for="color-bg"><?php _e( 'Background Color' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-bg]" id="color-bg" class="vfb-color-picker" value="<?php esc_attr_e( $color_bg ); ?>" data-default-color="#fbfbfb" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="color-link"><?php _e( 'Link Color' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-link]" id="color-link" class="vfb-color-picker" value="<?php esc_attr_e( $color_link ); ?>" data-default-color="#41637e" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="color-h1"><?php _e( 'Heading 1' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-h1]" id="color-h1" class="vfb-color-picker" value="<?php esc_attr_e( $color_h1 ); ?>" data-default-color="#565656" />
								<br />
								<select name="settings[email-design][font-h1]" id="font-h1">
									<?php $this->font_select( $font_h1 ); ?>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="color-h2"><?php _e( 'Heading 2' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-h2]" id="color-h2" class="vfb-color-picker" value="<?php esc_attr_e( $color_h2 ); ?>" data-default-color="#555555" />
								<br />
								<select name="settings[email-design][font-h2]" id="font-h2">
									<?php $this->font_select( $font_h2 ); ?>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="color-h3"><?php _e( 'Heading 3' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-h3]" id="color-h3" class="vfb-color-picker" value="<?php esc_attr_e( $color_h3 ); ?>" data-default-color="#555555" />
								<br />
								<select name="settings[email-design][font-h3]" id="font-h3">
									<?php $this->font_select( $font_h3 ); ?>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="color-text"><?php _e( 'Normal Text' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<input type="text" name="settings[email-design][color-text]" id="color-text" class="vfb-color-picker" value="<?php esc_attr_e( $color_text ); ?>" data-default-color="#565656" />
								<br />
								<select name="settings[email-design][font-text]" id="font-text">
									<?php $this->font_select( $font_text ); ?>
								</select>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="header-img"><?php _e( 'Header Image' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<div class="vfb-header-img-container">
								    <?php if ( !empty( $header_img ) ) : ?>
								        <img src="<?php echo $header_img_src[0]; ?>" alt="" style="max-width:100%;" />
								    <?php endif; ?>
								</div>

								<p class="hide-if-no-js">
								    <a class="button vfb-button vfb-header-img-upload<?php echo is_array( $header_img_src ) ? ' hidden' : ''; ?>" href="<?php echo esc_url( get_upload_iframe_src( 'image', 0 ) ); ?>">
								        <i class="vfb-icon-plus-circle"></i>
								        <?php _e( 'Add new image', 'vfb-pro' ) ?>
								    </a>
								    <a class="button vfb-button-trash vfb-header-img-delete<?php echo !is_array( $header_img_src ) ? ' hidden' : ''; ?>" href="#">
								        <i class="vfb-icon-remove"></i>
								        <?php _e( 'Remove image', 'vfb-pro' ) ?>
								    </a>
								</p>

								<input class="vfb-header-img-id" name="settings[email-design][header-img]" type="hidden" value="<?php echo esc_attr( $header_img ); ?>" />
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="use-premailer"><?php esc_html_e( 'Enable Premailer', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[email-premailer]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[email-premailer]" id="use-premailer" value="1"<?php checked( $premailer, 1 ); ?> /> <?php _e( "Process template through the Premailer API.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
								<p class="description">
									<?php printf( __( '<a href="%s" target="_blank">Premailer</a> converts all CSS to inline styles for best HTML email results.', 'vfb-pro' ), esc_url( 'http://premailer.dialect.ca' ) ); ?><br />
									<?php _e( 'NOTE: if you experience problems with this enabled, you can manually process the email on the Premailer website and save it in the Email Template below.', 'vfb-pro' ) ?>
								</p>
							</td>
						</tr>
					</tbody>

					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="email-template"><?php _e( 'Email Template' , 'vfb-pro'); ?></label>
							</th>
							<td>
								<textarea id="email-template" name="settings[email-template]" cols="70" rows="30"><?php echo $template; ?></textarea>
								<iframe id="email-preview" src="<?php echo esc_url( $preview_url ); ?>"></iframe>
								<?php _e( 'Save changes to update the email preview.', 'vfb-pro' ); ?>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="link-love"><?php esc_html_e( 'Remove Link Love', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[email-design][link-love]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[email-design][link-love]" id="link-love" value="1"<?php checked( $link_love, 1 ); ?> /> <?php _e( "Remove the link back to VFB Pro.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row">
								<label for="skip-empty"><?php esc_html_e( 'Skip Empty Fields', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<fieldset>
									<label>
										<input type="hidden" name="settings[email-design][skip-empty]" value="0" /> <!-- This sends an unchecked value to the meta table -->
										<input type="checkbox" name="settings[email-design][skip-empty]" id="skip-empty" value="1"<?php checked( $skip_empty, 1 ); ?> /> <?php _e( "Hide fields in email that have no data.", 'vfb-pro' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>

					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="template-tags"><?php esc_html_e( 'Template Tags', 'vfb-pro' ); ?></label>
							</th>
							<td>
								<p><?php _e( 'Templating is a way to dynamically replace specially formatted variables with data submitted from the form.', 'vfb-pro' ); ?></p>
								<a href="#TB_inline?width=1000&height=600&inlineId=vfb-template-tags" class="button thickbox">
									<?php _e( ' View Template Options', 'vfb-pro' ); ?>
								</a>
								<div id="vfb-template-tags" style="display: none;">
									<?php $this->template_tags( $this->id ); ?>
								</div> <!-- #vfb-template-tags -->
							</td>
						</tr>
					</tbody>
				</table>
			</div> <!-- .vfb-edit-section-inside -->
		</div> <!-- .vfb-edit-section -->

		<?php
			submit_button(
				__( 'Save Changes', 'vfb-pro' ),
				'primary',
				'' // leave blank so "name" attribute will not be added
			);
		?>
	</form>
	<?php
	}

	/**
	 * Helper for the font selection
	 *
	 * @access private
	 * @param mixed $setting
	 * @return void
	 */
	private function font_select( $setting ) {
	?>
		<optgroup label="<?php esc_attr_e( 'Sans Serif Web Safe Fonts', 'vfb-pro' ); ?>">
			<option value="Arial"<?php selected( 'Arial', $setting ); ?>><?php _e( 'Arial', 'vfb-pro' ); ?></option>
			<option value="Arial Black"<?php selected( 'Arial Black', $setting ); ?>><?php _e( 'Arial Black', 'vfb-pro' ); ?></option>
			<option value="Century Gothic"<?php selected( 'Century Gothic', $setting ); ?>><?php _e( 'Century Gothic', 'vfb-pro' ); ?></option>
			<option value="Geneva"<?php selected( 'Geneva', $setting ); ?>><?php _e( 'Geneva', 'vfb-pro' ); ?></option>
			<option value="Helvetica"<?php selected( 'Helvetica', $setting ); ?>><?php _e( 'Helvetica', 'vfb-pro' ); ?></option>
			<option value="Lucida Grande"<?php selected( 'Lucida Grande', $setting ); ?>><?php _e( 'Lucida Grande', 'vfb-pro' ); ?></option>
			<option value="Tahoma"<?php selected( 'Tahoma', $setting ); ?>><?php _e( 'Tahoma', 'vfb-pro' ); ?></option>
			<option value="Trebuchet MS"<?php selected( 'Trebuchet MS', $setting ); ?>><?php _e( 'Trebuchet MS', 'vfb-pro' ); ?></option>
			<option value="Verdana"<?php selected( 'Verdana', $setting ); ?>><?php _e( 'Verdana', 'vfb-pro' ); ?></option>
		</optgroup>

		<optgroup label="<?php esc_attr_e( 'Serif Web Safe Fonts', 'vfb-pro' ); ?>">
			<option value="Cambria"<?php selected( 'Cambria', $setting ); ?>><?php _e( 'Cambria', 'vfb-pro' ); ?></option>
			<option value="Garamond"<?php selected( 'Garamond', $setting ); ?>><?php _e( 'Garamond', 'vfb-pro' ); ?></option>
			<option value="Georgia"<?php selected( 'Georgia', $setting ); ?>><?php _e( 'Georgia', 'vfb-pro' ); ?></option>
			<option value="Goudy Old Style"<?php selected( 'Goudy Old Style', $setting ); ?>><?php _e( 'Goudy Old Style', 'vfb-pro' ); ?></option>
			<option value="Lucida Bright"<?php selected( 'Lucida Bright', $setting ); ?>><?php _e( 'Lucida Bright', 'vfb-pro' ); ?></option>
			<option value="Palatino"<?php selected( 'Palatino', $setting ); ?>><?php _e( 'Palatino', 'vfb-pro' ); ?></option>
			<option value="Times New Roman"<?php selected( 'Times New Roman', $setting ); ?>><?php _e( 'Times New Roman', 'vfb-pro' ); ?></option>
		</optgroup>

		<optgroup label="<?php esc_attr_e( 'Monospaced Web Safe Fonts', 'vfb-pro' ); ?>">
			<option value="Consolas"<?php selected( 'Consolas', $setting ); ?>><?php _e( 'Consolas', 'vfb-pro' ); ?></option>
			<option value="Courier New"<?php selected( 'Courier New', $setting ); ?>><?php _e( 'Courier New', 'vfb-pro' ); ?></option>
			<option value="Lucida Console"<?php selected( 'Lucida Console', $setting ); ?>><?php _e( 'Lucida Console', 'vfb-pro' ); ?></option>
			<option value="Lucida Sans Typewriter"<?php selected( 'Lucida Sans Typewriter', $setting ); ?>><?php _e( 'Lucida Sans Typewriter', 'vfb-pro' ); ?></option>
			<option value="Monaco"<?php selected( 'Monaco', $setting ); ?>><?php _e( 'Monaco', 'vfb-pro' ); ?></option>
		</optgroup>
	<?php
	}

	/**
	 * The default HTML template
	 *
	 * @access private
	 * @param mixed $setting
	 * @return void
	 */
	private function html_template() {
		ob_start();

		require_once( VFB_PLUGIN_DIR . 'inc/preview-email.php' );

		$template = ob_get_clean();

		return $template;
	}

	/**
	 * template_tags function.
	 *
	 * @access private
	 * @param mixed $form_id
	 * @return void
	 */
	private function template_tags( $form_id ) {
		$tags = new VFB_Pro_Template_Tags( $form_id );
		$tags->display();
	}

	/**
	 * Ensure an Email Preview page exists.
	 *
	 * @access private
	 * @return void
	 */
	private function preview_page_check() {
		$title        = 'VFB Pro - Email Preview';
		$preview_page = get_page_by_title( $title );

		if ( !$preview_page ) {
			$preview_post = array(
				'post_title'   => $title,
				'post_content' => 'This is a preview of how the email will look.',
				'post_status'  => 'draft',
				'post_type'    => 'page',
			);

			// Insert the page
			$page_id = wp_insert_post( $preview_post );
		}
	}
}