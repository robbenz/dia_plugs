<?php

class VFB_Pro_Template_Tags {
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

	public function display() {
		$form_id = $this->id;

		$vfbdb  = new VFB_Pro_Data();
		$form   = $vfbdb->get_form_by_id( $form_id );
		$fields = $vfbdb->get_fields( $form_id, "AND field_type NOT IN ('captcha','submit') ORDER BY field_order ASC" );
	?>
	<div class="vfb-template-tags">
		<h3 class="media-title"><?php _e( 'Template Options', 'vfb-pro' ); ?></h3>
		<p><?php _e( 'The following template options will allow you to use entry data, form data, or email template options.', 'vfb-pro' ); ?></p>

		<h4><?php _e( 'Form Templates', 'vfb-pro' ); ?></h4>
		<p><?php _e( 'Form templates are available for use in the Email Subject, Email Message and Template, and Confirmation Success Text.', 'vfb-pro' ); ?></p>
		<table>
			<tr>
				<td class="vfb-template-tag">[form:FormID]</td>
				<td><?php _e( 'The current form ID:', 'vfb-pro' ); ?> <strong><?php echo $form_id; ?></strong></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[form:Title]</td>
				<td><?php _e( 'The current form title:', 'vfb-pro' ); ?> <strong><?php echo $form['title']; ?></strong></td>
			</tr>
		</table>

		<h4><?php _e( 'Email Templates', 'vfb-pro' ); ?></h4>
		<p><?php _e( 'Email templates are available for use in the Email Template on the Email Design tab.', 'vfb-pro' ); ?></p>
		<table>
			<tr>
				<td class="vfb-template-tag">[vfb-fields]</td>
				<td><?php _e( 'Outputs the list of fields in a table row (&lt;tr&gt;).', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-bg]</td>
				<td><?php _e( 'Background color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-link]</td>
				<td><?php _e( 'Link color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-h1]</td>
				<td><?php _e( 'Heading 1 color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[font-h1]</td>
				<td><?php _e( 'Heading 1 font size.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-h2]</td>
				<td><?php _e( 'Heading 2 color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[font-h2]</td>
				<td><?php _e( 'Heading 2 font size.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-h3]</td>
				<td><?php _e( 'Heading 3 color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[font-h3]</td>
				<td><?php _e( 'Heading 3 font size.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[color-text]</td>
				<td><?php _e( 'Paragraph text color.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[font-text]</td>
				<td><?php _e( 'Paragraph text font size.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[header-img]</td>
				<td><?php _e( 'Custom header image.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[vfb-link-love]</td>
				<td><?php _e( 'Show your appreciation by linking back to VFB Pro.', 'vfb-pro' ); ?></td>
			</tr>
		</table>

		<h4><?php _e( 'Entry Templates', 'vfb-pro' ); ?></h4>
		<p><?php _e( "Entry data, that's the data the user typed into the form, will replace the Entry Template tags.", 'vfb-pro' ); ?></p>
		<table>
			<tr>
				<td class="vfb-template-tag">[entry:EntryID]</td>
				<td><?php _e( 'The entry ID.', 'vfb-pro' ); ?></td>
			</tr>

			<tr>
				<td class="vfb-template-tag">[entry:DateCreated]</td>
				<td><?php _e( 'The date that the entry was created.', 'vfb-pro' ); ?></td>
			</tr>

			<?php foreach ( $fields as $field ) : ?>
				<tr>
					<td class="vfb-template-tag">[entry:Field<?php echo $field['id']; ?>]</td>
					<td><?php echo $field['data']['label']; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php
	}
}