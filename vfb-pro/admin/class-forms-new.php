<?php
/**
 * Class that controls the Add New Form view
 *
 * @since 3.0
 */
class VFB_Pro_Forms_New {

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_create_forms' ) )
			return;
	?>
	<div class="wrap">
		<h2><?php _e( 'Add New Form', 'vfb-pro' ); ?></h2>

		<form method="post" id="vfbp-new-form" action="" novalidate>
			<input name="_vfbp_action" type="hidden" value="create-form" />
			<?php
				wp_nonce_field( 'vfbp_create_form' );
			?>

			<h3><?php _e( 'General Form Settings', 'vfb-pro' ); ?></h3>

			<table class="form-table">
				<tbody>
					<tr valign="top" class="form-required">
						<th scope="row">
							<label for="title">
								<?php _e( 'Name the form' , 'vfb-pro'); ?>
								<span class="description">(<?php _e( 'required', 'vfb-pro' ); ?>)</span>
							</label>
						</th>
						<td>
							<input type="text" autofocus="autofocus" class="regular-text" id="title" name="title" maxlength="255" />
							<p class="description"><?php _e( 'This name is used for admin purposes.' , 'vfb-pro'); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="status"><?php _e( 'Form Status' , 'vfb-pro'); ?></label>
						</th>
						<td>
							<select name="status" id="status">
								<option value="publish"><?php _e( 'Published', 'vfb-pro' ); ?></option>
								<option value="draft"><?php _e( 'Draft', 'vfb-pro' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Set the initial display status of the form.' , 'vfb-pro'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<h3><?php _e( 'Email Settings', 'vfb-pro' ); ?></h3>

			<table class="form-table">
				<tbody>
					<tr valign="top" class="form-required">
						<th scope="row">
							<label for="from-name">
								<?php _e( 'Your Name' , 'vfb-pro'); ?>
								<span class="description">(<?php _e( 'required', 'vfb-pro' ); ?>)</span>
							</label>
						</th>
						<td>
							<input type="text" value="" placeholder="" class="regular-text" id="from-name" name="settings[from-name]" />
							<p class="description"><?php _e( 'This option sets the "From" display name of the email that is sent.' , 'vfb-pro'); ?></p>
						</td>
					</tr>

					<tr valign="top" class="form-required">
						<th scope="row">
							<label for="reply-to">
								<?php _e( 'Reply-To Email' , 'vfb-pro'); ?>
								<span class="description">(<?php _e( 'required', 'vfb-pro' ); ?>)</span>
							</label>
						</th>
						<td>
							<input type="text" value="" placeholder="" class="regular-text" id="reply-to" name="settings[reply-to]" />
							<p class="description"><?php _e( 'Replies to your email will go here.' , 'vfb-pro'); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="subject"><?php _e( 'Email Subject' , 'vfb-pro'); ?></label>
						</th>
						<td>
							<input type="text" value="" placeholder="" class="regular-text" id="subject" name="settings[subject]" />
							<p class="description"><?php _e( 'This sets the subject of the email that is sent.' , 'vfb-pro'); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<label for="email-to"><?php _e( 'Email To' , 'vfb-pro'); ?></label>
						</th>
						<td>
							<input type="text" value="" placeholder="" class="regular-text" id="email-to" name="settings[email-to]" />
							<p class="description"><?php _e( 'Who to send the submitted data to.' , 'vfb-pro'); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<?php
				submit_button(
					__( 'Create Form', 'vfb-pro' ),
					'primary',
					'' // leave blank so "name" attribute will not be added
				);
			?>
		</form>
	</div> <!-- .wrap -->
	<?php
	}
}