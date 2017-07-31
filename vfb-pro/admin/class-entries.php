<?php
/**
 * Class that handles our Entries list
 *
 * @since 3.0
 */
class VFB_Pro_Entries {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Setup custom columns
		add_filter( 'manage_vfb_entry_posts_columns', array( $this, 'fields_columns' ) );

		// Make our columns sortable.
		add_filter( 'manage_edit-vfb_entry_sortable_columns', array( $this, 'sortable_columns' ) );

		// Custom columns
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

		// Reset row actions
		add_filter( 'post_row_actions', array( $this, 'reset_row_actions' ), 1, 2 );

		// Add form filter
		add_action( 'restrict_manage_posts', array( $this, 'filters' ) );
		add_filter( 'parse_query', array( $this, 'table_filter' ) );
		add_filter( 'posts_clauses', array( $this, 'alter_query' ), 20 );

		add_filter( 'views_edit-vfb_entry', array( $this, 'views' ) );

		add_action( 'admin_footer', array( $this, 'edit_active_menu' ) );

		add_action( 'admin_init', array( $this, 'actions' ) );

		// Remove bulk actions
		add_filter( 'bulk_actions-edit-vfb_entry', array( $this, 'remove_bulk_actions' ) );

		// Add our metabox for editing field values
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_metaboxes' ) );

		// Save our metabox values
		add_action( 'save_post', array( $this, 'save_entry' ), 10, 2 );

		add_action( 'wp_ajax_vfbp-entry-columns', array( $this, 'save_columns' ) );

		// Add prev/next button navigation to entry detail pages
		add_action( 'edit_form_top', array( $this, 'navigation_links' ) );
	}

	/**
	 * Adds our form filters
	 *
	 * @access public
	 * @return void
	 */
	public function filters() {
		global $typenow;

		// Bail if we aren't in our submission custom post type.
		if ( $typenow != 'vfb_entry' )
			return false;

		$form_id = isset( $_GET['form-id'] ) ? absint( $_GET['form-id'] ) : '';

		if ( empty( $form_id ) )
			return;

		$this->forms_dropdown( $form_id );
	}

	/**
	 * Filter our submission list by Form ID
	 *
	 * @access public
	 * @return void
	 */
	public function table_filter( $query ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow )
			return;

		if ( isset ( $query->query['post_type'] ) &&  'vfb_entry' !== $query->query['post_type'] )
			return;

		if ( !is_main_query() )
			return;

		$qv = &$query->query_vars;

	    if( !empty( $_GET['form-id'] ) )
	    	$form_id = absint( $_GET['form-id'] );
	    else
	    	$form_id = 0;

	    if ( ! isset ( $qv['meta_query'] ) ) {
		     $qv['meta_query'] = array(
		    	array(
		    		'key'     => '_vfb_form_id',
		    		'value'   => $form_id,
		    		'compare' => '=',
		    	),
		    );
	    }
	}

	/**
	 * Adds fields as columns
	 *
	 * @access public
	 * @param mixed $cols
	 * @return $cols
	 */
	public function fields_columns( $cols ) {
		$cols = array(
			'cb' => '<input type="checkbox" />',
			'id' => __( 'Entry ID', 'vfb-pro' ),
		);

		if ( isset( $_GET['form-id'] ) && !empty( $_GET['form-id'] ) ) {
			$form_id = absint( $_GET['form-id'] );
			$vfbdb  = new VFB_Pro_Data();
			$fields = $vfbdb->get_fields( $form_id, "AND field_type NOT IN ('heading','instructions','page-break','captcha','submit') ORDER BY field_order ASC" );

			if ( is_array( $fields ) && !empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$label = '';
					$field_id = $field['id'];

					if ( isset( $field['data']['label'] ) ) {
						$label = $field['data']['label'];
						if ( strlen( $label ) > 140 )
							$label = substr( $label, 0, 140 );

						$cols[ 'form-' . $form_id . '-field-' . $field_id ] = $label;
					}

				}
			}
		}

		$cols['entry-date'] = __( 'Entry Date', 'vfb-pro' );

		return $cols;
	}

	/**
	 * Make our columns sortable
	 *
	 * @access public
	 * @return array
	 */
	public function sortable_columns() {
		// Get a list of all of our fields.
		$fields  = get_column_headers( 'edit-vfb_entry' );
		$columns = array();

		foreach ( $fields as $slug => $c ) {
			if ( $slug != 'cb' ) {
				$columns[ $slug ] = $slug;
			}
		}
		return $columns;
	}

	/**
	 * custom_columns function.
	 *
	 * @access public
	 * @param mixed $column
	 * @param mixed $entry_id
	 * @return void
	 */
	public function custom_columns( $column, $entry_id ) {
		if ( !isset( $_GET['form-id'] ) )
			return;

		$form_id = absint( $_GET['form-id'] );
		$vfbdb   = new VFB_Pro_Data();
		$actions = array();

		// View
		if ( current_user_can( 'vfb_view_entries' ) ) {
			$view_url = add_query_arg(
				array(
					'vfb-action' => 'view',
					'action'	 => 'edit',
					'form-id'    => $form_id,
					'post'       => $entry_id,
					'post_type'  => 'vfb_entry',
				),
				admin_url( 'post.php' )
			);

			$actions['view'] = sprintf( '<a href="%s">%s</a>', esc_url( $view_url ), __( 'View', 'vfb-pro' ) );
		}

		// Edit
		if ( current_user_can( 'vfb_edit_entries' ) ) {
			$edit_url = add_query_arg(
				array(
					'action'    => 'edit',
					'form-id'   => $form_id,
					'post'      => $entry_id,
					'post_type' => 'vfb_entry',
				),
				admin_url( 'post.php' )
			);

			$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'vfb-pro' ) );
		}

		// Spam
		$spam_url = add_query_arg(
			array(
				'vfb-action' => 'spam',
				'form-id'    => $form_id,
				'post'       => $entry_id,
				'post_type'  => 'vfb_entry',
			),
			admin_url( 'edit.php' )
		);

		$actions['spam'] = sprintf( '<a href="%s">%s</a>', esc_url( $spam_url ), __( 'Spam', 'vfb-pro' ) );

		// Trash
		if ( current_user_can( 'vfb_delete_entries' ) ) {
			$trash_url = get_delete_post_link( $entry_id );

			$actions['trash'] = sprintf( '<a href="%s">%s</a>', esc_url( $trash_url ), __( 'Trash', 'vfb-pro' ) );
		}

		// Trashed Entries view
		if ( current_user_can( 'vfb_delete_entries' ) ) {
			if ( 'trash' == $this->get_post_status() ) {
				unset( $actions['view'] );
				unset( $actions['edit'] );
				unset( $actions['spam'] );
				unset( $actions['trash'] );

				$untrash_url = add_query_arg(
					array(
						'action'     => 'untrash',
						'post'       => $entry_id,
						'post_type'  => 'vfb_entry',
					),
					wp_nonce_url( admin_url( 'post.php' ), 'untrash-post_' . $entry_id )
				);

				$actions['untrash'] = sprintf( '<a href="%s">%s</a>', esc_url( $untrash_url ), __( 'Restore', 'vfb-pro' ) );

				$delete_url = get_delete_post_link( $entry_id, '', true );

				if ( current_user_can( 'vfb_delete_entries' ) )
					$actions['delete'] = sprintf( '<a href="%s">%s</a>', esc_url( $delete_url ), __( 'Delete', 'vfb-pro' ) );
			}
		}

		// SPAM'd Entries view
		if ( 'spam' == $this->get_post_status() ) {
			unset( $actions['edit'] );
			unset( $actions['spam'] );
			unset( $actions['trash'] );

			$unspam_url = add_query_arg(
				array(
					'vfb-action' => 'unspam',
					'post'       => $entry_id,
					'post_type'  => 'vfb_entry',
				),
				admin_url( 'edit.php' )
			);

			$actions['unspam'] = sprintf( '<a href="%s">%s</a>', esc_url( $unspam_url ), __( 'Not Spam', 'vfb-pro' ) );

			$delete_url = get_delete_post_link( $entry_id, '', true );

			if ( current_user_can( 'vfb_delete_entries' ) )
				$actions['delete'] = sprintf( '<a href="%s">%s</a>', esc_url( $delete_url ), __( 'Delete Permanently', 'vfb-pro' ) );
		}

		// Entry ID
		if ( 'id' == $column ) {
			$meta_key = '_vfb_seq_num';
			$value = $vfbdb->get_entry_meta_by_id( $entry_id, $meta_key );

			echo $value;

			echo $this->row_actions( $actions );
		}
		// Entry Date
		else if ( 'entry-date' == $column ) {
			$post      = get_post( $entry_id );
			$date_time = mysql2date( __( 'Y/m/d g:i:s A', 'vfb-pro' ), $post->post_date, true );

			echo $date_time;
		}
		// Field columns
		else if ( strpos( $column, '-field-' ) !== false ) {
			$field_id = str_replace( 'form-' . $form_id . '-field-', '', $column );
			$meta_key = '_vfb_field-' . $field_id;
			$value    = esc_html( $vfbdb->get_entry_meta_by_id( $entry_id, $meta_key ) );
			$field    = $vfbdb->get_field_by_id( $field_id );

			// Link URLs and File Uploads
			if ( in_array( $field['field_type'], array( 'url', 'file-upload' ) ) ) {
				if ( !empty( $value ) ) {
					$urls       = explode( ',', $value );
					$url_output = '';

					foreach ( $urls as $url ) {
						$url_output .= sprintf( '<a href="%1$s">%1$s</a><br />', $url );
					}

					$value = $url_output;
				}
			}

			// Reverse escape Signature HTML to display as image
			if ( in_array( $field['field_type'], array( 'signature' ) ) ) {
				if ( !empty( $value ) ) {
					$value = html_entity_decode( $value );
				}
			}

			echo $value;
		}
	}

	/**
	 * Add views to All/Published area
	 *
	 * @access public
	 * @param mixed $views
	 * @return void
	 */
	public function views( $views ) {
		$form_id = isset( $_GET['form-id'] ) ? absint( $_GET['form-id'] ) : '';

		// Replace the view links with a form dropdown selection
		if ( empty( $form_id ) ) {
			$action_url = add_query_arg(
				array(
					'post_status' => 'all',
					'post_type'   => 'vfb_entry',

				),
				admin_url( 'edit.php' )
			);
			?>
			<div class="manage-menus">
				<form method="get" action="<?php echo esc_url( $action_url ); ?>">
					<input type="hidden" name="post_status" class="post_status_page" value="all">
					<input type="hidden" name="post_type" class="post_type_page" value="vfb_entry">

					<label for="vfb-entry-form-ids" class="selected-menu"><?php _e( 'Select a form to view entries', 'vfb-pro' ); ?>:</label>
					<?php $this->forms_dropdown(); ?>
					<span class="submit-btn">
						<?php
							submit_button(
								__( 'Select', 'vfb-pro' ),
								'secondary',
								'submit',
								false
							);
						?>
					</span>
				</form>
			</div>
			<?php

			return;
		}

		// Remove Publish and Trash views (Trash replaced below)
		unset( $views['all'] );
		unset( $views['publish'] );
		unset( $views['trash'] );

		$vfbdb       = new VFB_Pro_Data();
		$post_stati  = array(
			'all'   => __( 'All', 'vfb-pro' ),
			'trash' => __( 'Trash', 'vfb-pro' ),
			'spam'  => __( 'Spam', 'vfb-pro' ),
		);

		foreach ( $post_stati as $state => $label ) {
			$current = '';

			if ( isset( $_REQUEST['post_status'] ) && $state == $_REQUEST['post_status'] )
				$current = ' class="current"';

			$view = add_query_arg(
				array(
					'post_status' => $state,
					'post_type'   => 'vfb_entry',
					'form-id'     => $form_id
				),
				admin_url( 'edit.php' )
			);

			switch ( $state ) {
				case 'all' :
					$count = $vfbdb->get_entries_count( $form_id );
					break;

				case 'trash' :
					$count = $vfbdb->get_entries_count( $form_id, 'trash' );
					break;

				case 'spam' :
					$count = $vfbdb->get_entries_count( $form_id, 'spam' );
					break;
			}

			$views[ $state ] = sprintf( '<a href="%1$s"%2$s>%3$s <span class="count">(%4$d)</span></a>', esc_url( $view ), $current, $label, $count );
		}

		return $views;
	}

	/**
	 * Removes certain bulk actions
	 *
	 * @access public
	 * @param mixed $actions
	 * @return void
	 */
	public function remove_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Removes meta boxes
	 *
	 * @access public
	 * @return void
	 */
	public function remove_metaboxes() {
		remove_meta_box( 'submitdiv', 'vfb_entry', 'side' );
		remove_meta_box( 'postcustom', 'vfb_entry', 'normal' );
		remove_meta_box( 'slugdiv', 'vfb_entry', 'normal' );
	}

	/**
	 * Save user selected columns
	 *
	 * @access public
	 * @return void
	 */
	public function save_columns() {
		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_POST['action'] ) && 'vfbp-entry-columns' !== $_POST['action'] )
			return;

		$user = wp_get_current_user();

		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$columns = isset( $_POST['columns'] ) ? explode( ',', $_POST['columns'] ) : array();
		$columns = array_filter( $columns );

		update_user_option( $user->ID, 'manageedit-vfb_entrycolumnshidden-form-' . $form_id, $columns, true );

		die(1);
	}

	/**
	 * Resets row actions in case there are extras automatically added.
	 *
	 * @access public
	 * @param mixed $actions
	 * @param mixed $post
	 * @return void
	 */
	public function reset_row_actions( $actions, $post ) {
		if ( 'vfb_entry' == $post->post_type ) {
			$actions = array();
		}

		return $actions;
	}

	/**
	 * Generate row actions div
	 *
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	protected function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	/**
	 * Add custom metaboxes to Edit Entry view
	 *
	 * @access public
	 * @return void
	 */
	public function add_metaboxes() {
		if ( isset( $_GET['vfb-action'] ) && 'view' == $_GET['vfb-action'] ) {
			// Add our field metabox.
			add_meta_box( 'vfb_view_fields', __( 'Entry Data', 'vfb-pro' ), array( $this, 'view_entry' ), 'vfb_entry', 'normal', 'default');
		}
		else {
			// Add our field editing metabox.
			add_meta_box( 'vfb_fields', __( 'Entry Data', 'vfb-pro' ), array( $this, 'edit_entry' ), 'vfb_entry', 'normal', 'default');
		}

		// Add our save field values metabox
		add_meta_box( 'vfb_fields_save', __( 'Entry Details', 'vfb-pro' ), array( $this, 'save_entry_box' ), 'vfb_entry', 'side', 'default');

		// Add our Entry Notes metabox
		add_meta_box( 'vfb_entry_notes', __( 'Entry Notes', 'vfb-pro' ), array( $this, 'entry_notes_box' ), 'vfb_entry', 'side', 'default' );
	}

	/**
	 * view_entry function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function view_entry( $post ) {
		$form_id  = absint( $_GET['form-id'] );
		$entry_id = $post->ID;
		$vfbdb    = new VFB_Pro_Data();
		$fields   = $vfbdb->get_fields( $form_id, "AND field_type NOT IN ('page-break','captcha','submit') ORDER BY field_order ASC" );
	?>
	<table class="form-table">
		<tbody>
		<?php
		if ( is_array( $fields ) && !empty( $fields ) ) :
			foreach ( $fields as $field ) :
				$label    = isset( $field['data']['label'] ) ? $field['data']['label'] : '';
				$meta_key = '_vfb_field-' . $field['id'];
				$value    = esc_html( $vfbdb->get_entry_meta_by_id( $entry_id, $meta_key ) );

				// Link URLs and File Uploads
				if ( in_array( $field['field_type'], array( 'url', 'file-upload' ) ) ) {
					if ( !empty( $value ) ) {
						$urls       = explode( ',', $value );
						$url_output = '';

						foreach ( $urls as $url ) {
							$url_output .= sprintf( '<a href="%1$s">%1$s</a><br />', $url );
						}

						$value = $url_output;
					}
				}

				// Reverse escape Signature HTML to display as image
				if ( in_array( $field['field_type'], array( 'signature' ) ) ) {
					if ( !empty( $value ) ) {
						$value = html_entity_decode( $value );
					}
				}
			?>
			<tr>
				<td class="first">
					<?php echo $label; ?>
				</td>
				<td>
					<?php echo $value; ?>
				</td>
			</tr>
			<?php
			endforeach;
		endif;
		?>
		</tbody>
	</table>
	<?php
	}

	/**
	 * edit_entry function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function edit_entry( $post ) {
		$entry_id = $post->ID;
		$vfbdb    = new VFB_Pro_Data();
		$form_id  = $vfbdb->get_entry_meta_by_id( $entry_id, '_vfb_form_id' );
		$fields   = $vfbdb->get_fields( $form_id, "AND field_type NOT IN ('page-break','captcha','submit') ORDER BY field_order ASC" );
	?>
	<table class="form-table">
		<tbody>
		<?php
		if ( is_array( $fields ) && !empty( $fields ) ) :
			foreach ( $fields as $field ) :
				$label    = isset( $field['data']['label'] ) ? $field['data']['label'] : '';
				$meta_key = '_vfb_field-' . $field['id'];
				$value    = esc_html( $vfbdb->get_entry_meta_by_id( $entry_id, $meta_key ) );
			?>
			<tr>
				<td class="first">
					<?php echo $label; ?>
				</td>
				<td>
					<?php if ( in_array( $field['field_type'], array( 'textarea', 'address', 'html' ) ) ) : ?>
						<textarea name="fields[<?php echo $field['id']; ?>]" class="vfb-field-data" rows="5" cols="40"><?php echo $value; ?></textarea>
					<?php else : ?>
						<input type="text" name="fields[<?php echo $field['id']; ?>]" value="<?php echo $value; ?>" class="vfb-field-data" />
					<?php endif; ?>
				</td>
			</tr>
			<?php
			endforeach;
		endif;
		?>
		</tbody>
	</table>
	<?php
	}

	/**
	 * save_entry function.
	 *
	 * @access public
	 * @return void
	 */
	public function save_entry_box( $post ) {
		if ( $post->post_author != 0 ) {
			$user_data = get_userdata( $post->post_author );

			$first_name = $user_data->first_name;
			$last_name  = $user_data->last_name;

			if ( $first_name != '' && $last_name != '' )
				$name = $first_name . ' ' . $last_name;
			else if ( $user_data->display_name != '' )
				$name = $user_data->display_name;
			else
				$name = $user_data->user_login;
		}
	?>
	<input type="hidden" name="vfb-edit-entry" value="1">
	<div class="submitbox" id="submitpost">
		<div id="minor-publishing">
			<div id="misc-publishing-actions">
				<?php
					$datef = __( 'M j, Y @ G:i', 'vfb-pro' );
					$stamp = __( 'Submitted on: <b>%1$s</b>', 'vfb-pro' );
					$date  = date_i18n( $datef, strtotime( $post->post_date ) );
				?>
				<div class="misc-pub-section curtime misc-pub-curtime">
					<span id="timestamp"><?php printf( $stamp, $date ); ?></span>
				</div> <!-- .misc-pub-section -->
				<?php
					$stamp = __( 'Updated on: <b>%1$s</b>', 'vfb-pro' );
					$date  = date_i18n( $datef, strtotime( $post->post_modified ) );
				?>
				<div class="misc-pub-section curtime misc-pub-curtime">
					<span id="timestamp"><?php printf( $stamp, $date ); ?></span>
				</div> <!-- .misc-pub-section -->
				<?php
				if ( $post->post_author != 0 ) {
					?>
					<div class="misc-pub-section misc-pub-visibility" id="visibility">
						<?php _e( 'Submitted by', 'vfb-pro' ); ?>: <span id="post-visibility-display"><?php echo $name; ?></span>
					</div> <!-- .misc-pub-section -->
					<?php
				}
				?>
			</div> <!-- #misc-publishing-actions -->
		</div> <!-- #minor-publishing -->
		<div id="major-publishing-actions">
			<div id="delete-action">
			<?php
				if ( current_user_can( 'vfb_delete_entries' ) ) :
					$delete_text = !EMPTY_TRASH_DAYS ? __( 'Delete Permanently', 'vfb-pro' ) : __( 'Move to Trash', 'vfb-pro' );
			?>
				<a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo $delete_text; ?></a>
			<?php endif; ?>
			</div> <!-- #delete-action -->

			<div id="publishing-action">
			<?php
				do_action( 'vfb_entries_detail_metabox' );

				printf( '<a href="#" class="button" onclick="window.print();return false">%s</a>', __( 'Print', 'vfb-pro' ) );

				if ( current_user_can( 'vfb_edit_entries' ) ) :
					if ( !isset( $_GET['vfb-action'] ) ) :
			?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'vfb-pro' ); ?>" />
				<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e( 'Update', 'vfb-pro' ); ?>" />
			<?php
					endif;
				endif;
			?>
			</div> <!-- #publishing-action -->
			<div class="clear"></div> <!-- .clear -->
		</div> <!-- #major-publishing-actions -->
	</div> <!-- .submitbox -->
	<?php
	}

	/**
	 * save_entry function.
	 *
	 * @access public
	 * @param mixed $entry_id
	 * @return void
	 */
	public function save_entry( $entry_id, $post ) {

		if ( !isset( $_POST['vfb-edit-entry'] ) || 1 != $_POST['vfb-edit-entry'] )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		  return $entry_id;

		if ( !get_post( $entry_id ) )
			return;

		if ( 'vfb_entry' !== $post->post_type )
			return;

		foreach ( $_POST['fields'] as $field_id => $value ) {
			$meta_key = '_vfb_field-' . $field_id;
			update_post_meta( $entry_id, $meta_key, $value );
	    }

	    $sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'locked', 'ids'), wp_get_referer() );
	    $sendback = add_query_arg( 'message', 1, $sendback );

	    wp_redirect( esc_url_raw( $sendback ) );
		exit();
	}

	/**
	 * entry_notes_box function.
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function entry_notes_box( $post ) {
	?>
		<label class="screen-reader-text" for="excerpt">
			<?php _e('Excerpt') ?>
		</label>

		<?php if ( isset( $_GET['vfb-action'] ) && 'view' == $_GET['vfb-action'] ) : ?>
			<?php echo $post->post_excerpt; // textarea_escaped ?>
		<?php else : ?>
			<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; // textarea_escaped ?></textarea>
		<?php endif; ?>
	<?php
	}

	/**
	 * Make sure the VFB Pro menu is selected when editing an entry
	 *
	 * @access public
	 * @return void
	 */
	public function edit_active_menu() {
		global $pagenow, $typenow;

		if ( 'vfb_entry' !== $typenow )
			return;

		if ( 'post.php' !== $pagenow )
			return;
	?>
	<script type="text/javascript">
		jQuery(function(){
			jQuery( 'li#toplevel_page_vfb-pro' ).children( 'a' ).removeClass( 'wp-not-current-submenu' );
			jQuery( 'li#toplevel_page_vfb-pro' ).removeClass( 'wp-not-current-submenu' );
			jQuery( 'li#toplevel_page_vfb-pro' ).addClass( 'wp-menu-open wp-has-current-submenu' );
			jQuery( 'li#toplevel_page_vfb-pro' ).children( 'a' ).addClass( 'wp-menu-open wp-has-current-submenu' );
		});
	</script>
	<?php
	}

	/**
	 * Get current status
	 *
	 * @access public
	 * @return void
	 */
	public function get_post_status() {
		if ( !isset( $_REQUEST['post_status'] ) )
			return false;

		return esc_html( $_REQUEST['post_status'] );
	}

	/**
	 * Count entries from the _post table
	 *
	 * @access public
	 * @return void
	 */
	public function count_entries() {
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT post_status, COUNT(*) AS num_posts FROM {$wpdb->posts} WHERE post_type ='%s' GROUP BY post_status", 'vfb_entry' ), ARRAY_A );

		$counts = array();
		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
		}

		return $counts;
	}

	/**
	 * Alter the post list to accomodate for our custom queries
	 *
	 * @access public
	 * @param mixed $pieces
	 * @return void
	 */
	public function alter_query( $pieces ) {
		global $typenow, $wpdb, $wp_query;

		if ( 'vfb_entry' !== $typenow )
			return $pieces;

		if ( !isset( $_GET['form-id'] ) )
			return $pieces;

		$form_id = absint( $_GET['form-id'] );

		// Spam
		if ( 'spam' == $this->get_post_status() ) {
			$pieces['where'] = "AND {$wpdb->posts}.post_type = 'vfb_entry' AND ({$wpdb->posts}.post_status = 'spam' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'private') AND ( ( {$wpdb->postmeta}.meta_key = '_vfb_form_id' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '{$form_id}' ) )";
		}

		// Today's Entries
		if ( isset( $_GET['today'] ) && 1 == $_GET['today'] ) {
			$pieces['where'] = "AND {$wpdb->posts}.post_type = 'vfb_entry' AND DATE({$wpdb->posts}.post_date) >= DATE(curdate()) AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'private') AND ( ( {$wpdb->postmeta}.meta_key = '_vfb_form_id' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '{$form_id}' ) )";
		}

		// Search
		if ( isset( $_GET['s'] ) && !empty( $_GET['s'] ) && is_search() ) {
			$search = $wpdb->prepare( "AND ( {$wpdb->postmeta}.meta_key LIKE '_vfb_field%%' AND {$wpdb->postmeta}.meta_value LIKE '%%%s%%' )", get_query_var('s') );

			$pieces['join']  = $pieces['join'] . " INNER JOIN {$wpdb->postmeta} AS mt1 ON ({$wpdb->posts}.ID = mt1.post_id)";
			$pieces['where'] = "$search AND {$wpdb->posts}.post_type = 'vfb_entry' AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'private') AND ( mt1.meta_key = '_vfb_form_id' AND CAST(mt1.meta_value AS CHAR) = '{$form_id}' )";
		}

		return $pieces;
	}

	/**
	 * Handler for our custom actions (i.e. Spam)
	 *
	 * @access public
	 * @return void
	 */
	public function actions() {
		global $typenow, $wpdb;

		if ( 'vfb_entry' !== $typenow )
			return;

		if ( !isset( $_GET['vfb-action'] ) )
			return;

		if ( !isset( $_GET['post'] ) )
			return;

		$action = esc_html( $_GET['vfb-action'] );
		$entry_id = absint( $_GET['post'] );

		switch ( $action ) {
			case 'spam' :
				if ( $this->akismet_check( 'spam', $entry_id ) ) {
					wp_update_post(
						array(
							'ID'		  => $entry_id,
							'post_status' => 'spam',
						)
					);
				}
				break;

			case 'unspam' :
				if ( $this->akismet_check( 'ham', $entry_id ) ) {
					wp_update_post(
						array(
							'ID'		  => $entry_id,
							'post_status' => 'publish',
						)
					);
				}
				break;
		}
	}

	/**
	 * Akismet check for marking an entry as Spam/Not Spam
	 *
	 * @access public
	 * @param mixed $type
	 * @param mixed $entry_id
	 * @return void
	 */
	public function akismet_check( $type, $entry_id ) {
		if ( !method_exists( 'Akismet', 'http_post' ) || !function_exists( 'akismet_http_post' ) )
			return false;

		global $akismet_api_host, $akismet_api_port;

		$query_string = '';
		$api = ( 'spam' == $type ) ? 'submit-spam' : 'submit-ham';

		// Get Akismet data
		$data = get_post_meta( $entry_id, '_vfb_akismet-data', true );

		// Sanity check: if no Akismet data, add it
		if ( empty( $data ) ) {
			$akismet = new VFB_Pro_Akismet();
			$akismet->spam_check( $entry_id );

			$data = get_post_meta( $entry_id, '_vfb_akismet-data', true );
		}

		foreach ( array_keys( $data ) as $k ) {
			$query_string .= $k . '=' . urlencode( $data[ $k ] ) . '&';
		}

		if ( method_exists( 'Akismet', 'http_post' ) )
		    $response = Akismet::http_post( $query_string, $api );
		else
		    $response = akismet_http_post( $query_string, $akismet_api_host, "/1.1/$api", $akismet_api_port );

		// Only update post if a response is true
		if ( $response ) {
			if ( 'Thanks for making the web a better place.' == $response[1] )
				return true;
		}

		return false;
	}

	/**
	 * Output a dropdown of all forms.
	 *
	 * @access public
	 * @param string $form_id (default: '')
	 * @return void
	 */
	public function forms_dropdown( $form_id = '' ) {
		$vfbdb = new VFB_Pro_Data();
		$forms = $vfbdb->get_all_forms();
	?>
		<select id="vfb-entry-form-ids" name="form-id">
			<option><?php _e( 'Select a Form', 'vfb-pro' ); ?></option>
		<?php
			if ( is_array( $forms ) && !empty( $forms ) ) {
				foreach ( $forms as $form ) {
					$entry_count = $vfbdb->get_entries_count( $form['id'] );

					echo sprintf(
						'<option value="%1$d"%3$s>%1$d - %2$s (%4$d)</option>',
						$form['id'],
						$form['title'],
						selected( $form['id'], $form_id, false ),
						$entry_count
					);
				}
			}
		?>
		</select>
	<?php
	}

	/**
	 * Display Prev/Next navigation links on Entry Detail page
	 *
	 * @access public
	 * @return void
	 */
	public function navigation_links() {
		global $typenow, $wpdb;

		// Bail if we aren't in our submission custom post type.
		if ( $typenow != 'vfb_entry' )
			return false;

		$entry_id = absint( $_GET['post'] );
		$form_id  = isset( $_GET['form-id']    ) ? absint( $_GET['form-id'] )      : '';
		$action   = isset( $_GET['vfb-action'] ) ? esc_html( $_GET['vfb-action'] ) : '';

		$prev_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT SQL_CALC_FOUND_ROWS {$wpdb->posts}.ID
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
			WHERE
			{$wpdb->posts}.ID = (SELECT MAX({$wpdb->posts}.ID) FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID < %d)
			AND ( ( {$wpdb->postmeta}.meta_key = '_vfb_form_id' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '%d' )
			)
			AND {$wpdb->posts}.post_type = 'vfb_entry' AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'private')
			GROUP BY {$wpdb->posts}.ID
			ORDER BY {$wpdb->posts}.post_date DESC",
			$entry_id,
			$form_id
		));

		$next_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT SQL_CALC_FOUND_ROWS {$wpdb->posts}.ID
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
			WHERE
			{$wpdb->posts}.ID = (SELECT MIN({$wpdb->posts}.ID) FROM {$wpdb->posts} WHERE {$wpdb->posts}.ID > %d)
			AND ( ( {$wpdb->postmeta}.meta_key = '_vfb_form_id' AND CAST({$wpdb->postmeta}.meta_value AS CHAR) = '%d' )
			)
			AND {$wpdb->posts}.post_type = 'vfb_entry' AND ({$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'future' OR {$wpdb->posts}.post_status = 'draft' OR {$wpdb->posts}.post_status = 'pending' OR {$wpdb->posts}.post_status = 'private')
			GROUP BY {$wpdb->posts}.ID
			ORDER BY {$wpdb->posts}.post_date DESC",
			$entry_id,
			$form_id
		));

		$prev_disabled = $next_disabled = '';

		// Disable the Prev link if no prev entry found
		if ( !$prev_id ) {
			$prev_id       = $entry_id;
			$prev_disabled = ' disabled';
		}

		// Disable the Next link if no next entry found
		if ( !$next_id ) {
			$next_id       = $entry_id;
			$next_disabled = ' disabled';
		}

		$url = add_query_arg(
			array(
				'action'	 => 'edit',
				'form-id'    => $form_id,
				'post_type'  => 'vfb_entry',
			),
			admin_url( 'post.php' )
		);

		// Add 'view' action
		if ( !empty( $action ) ) {
			$url = add_query_arg( 'vfb-action', 'view', $url );
		}

		$prev = sprintf(
			'<a href="%1$s" class="prev-page%2$s">&lsaquo;</a>',
			esc_url( add_query_arg( 'post', $prev_id, $url ) ),
			$prev_disabled
		);

		$next = sprintf(
			'<a href="%1$s" class="next-page%2$s">&rsaquo;</a>',
			esc_url( add_query_arg( 'post', $next_id, $url ) ),
			$next_disabled
		);
	?>
	<div class="tablenav top">
		<div class="tablenav-pages">
			<span class="pagination-links">
				<?php echo "$prev $next"; ?>
			</span>
		</div> <!-- .tablenav-pages -->

		<br class="clear">
	</div> <!-- .tablenav -->
	<?php
	}
}