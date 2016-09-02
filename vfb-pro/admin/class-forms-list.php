<?php
/**
 * Class that builds our All Forms list
 *
 * @since 3.0
 */
class VFB_Pro_Forms_List extends VFB_List_Table {

	/**
	 * errors
	 *
	 * @var mixed
	 * @access public
	 */
	public $errors;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){
		// Set parent defaults
		parent::__construct( array(
			'singular'  => 'form',
			'plural'    => 'forms',
			'ajax'      => false
		) );

		// Handle our bulk actions
		$this->process_bulk_action();

		// Handle row action links
		$this->process_row_action_links();

		// Ensure a Form Preview page exists
		$this->preview_page_check();
	}

	/**
	 * Ensure an Form Preview page exists.
	 *
	 * @access private
	 * @return void
	 */
	private function preview_page_check() {
		$title        = 'VFB Pro - Form Preview';
		$preview_page = get_page_by_title( $title );

		if ( !$preview_page ) {
			$preview_post = array(
				'post_title'   => $title,
				'post_content' => 'This is a preview of how this form will appear on your website.',
				'post_status'  => 'draft',
				'post_type'    => 'page',
			);

			// Insert the page
			$page_id = wp_insert_post( $preview_post );
		}
	}

	/**
	 * Display column names
	 *
	 * @since 3.0
	 * @returns $item string Column name
	 */
	public function column_default( $item, $column_name ){
		switch ( $column_name ) {
			case 'id':
			case 'date_updated':
				return $item[ $column_name ];

				break;
		}
	}

	/**
	 * Builds the on:hover links for the Form column
	 *
	 * @since 3.0
	 */
	public function column_form_title( $item ){

		$actions = array();

		$form_title = sprintf( '<strong>%s</strong>', $item['form_title'] );

		$draft_state = '';

		// Default Entries view
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
			// Edit Form
			if ( current_user_can( 'vfb_edit_forms' ) ) :
				// Append Draft status to title
				if ( 'draft' == $item['status'] && 'draft' !== $this->get_form_status() )
					$draft_state = sprintf( '<strong> - %s</strong>', __( 'Draft', 'vfb-pro' ) );

				$form_title_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'edit',
						'form'       => $item['form_id'],
					),
					admin_url( 'admin.php' )
				);

				$form_title = sprintf( '<a href="%s">%s</a>%s', esc_url( $form_title_url ), $form_title, $draft_state );

				$edit_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'edit',
						'form'       => $item['form_id'],
					),
					admin_url( 'admin.php' )
				);

				$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'vfb-pro' ) );
			endif;

			// Duplicate Form
			if ( current_user_can( 'vfb_copy_forms' ) ) :
				$dupe_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'duplicate-form',
						'form'       => $item['form_id'],
					),
					wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_duplicate_form' )
				);

				$actions['duplicate'] = sprintf( '<a href="%s">%s</a>', esc_url( $dupe_url ), __( 'Duplicate', 'vfb-pro' ) );
			endif;
		endif;

		// Trashed Forms view
		if ( current_user_can( 'vfb_delete_forms' ) ) :
			if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) {
				$trash_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'trash-form',
						'form'       => $item['form_id'],
					),
					wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_trash_form' )
				);

				$actions['trash'] = sprintf( '<a href="%s">%s</a>', esc_url( $trash_url ), __( 'Trash', 'vfb-pro' ) );
			}
			elseif ( $this->get_form_status() && 'trash' == $this->get_form_status() ) {
				$restore_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'restore',
						'form'       => $item['form_id'],
					),
					wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_undo_trash' )
				);

				$actions['restore'] = sprintf( '<a href="%s">%s</a>', esc_url( $restore_url ), __( 'Restore', 'vfb-pro' ) );

				$delete_url = add_query_arg(
					array(
						'page'       => 'vfb-pro',
						'vfb-action' => 'delete',
						'form'       => $item['form_id'],
					),
					wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_delete_form' )
				);

				$actions['delete'] = sprintf( '<a href="%s">%s</a>', esc_url( $delete_url ), __( 'Delete Permanently', 'vfb-pro' ) );
			}
		endif;

		// Preview Form (always last)
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) :
			if ( current_user_can( 'vfb_edit_forms' ) ) {
				$preview_url = add_query_arg(
					array(
						'preview'     => true,
						'vfb-form-id' => $item['form_id'],
					),
					get_permalink( get_page_by_title( 'VFB Pro - Form Preview' ) )
				);

				$actions['view'] = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $preview_url ), __( 'Preview', 'vfb-pro' ) );
			}
		endif;

		return sprintf( '%1$s %2$s', $form_title, $this->row_actions( $actions ) );
	}

	/**
	 * column_entries function.
	 *
	 * @access public
	 * @param mixed $item
	 * @return void
	 */
	public function column_entries( $item ) {
		echo '<div class="post-com-count-wrapper">';

		$this->comments_bubble( $item['form_id'], $item['entries'] );

		echo '</div> <!-- .post-com-count-wrapper -->';
	}

	/**
	 * comments_bubble function.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $count
	 * @return void
	 */
	public function comments_bubble( $form_id, $count ) {

		$entries_url = add_query_arg(
			array(
				'form-id'    => $form_id,
				'post_type'  => 'vfb_entry',
			),
			admin_url( 'edit.php' )
		);

		echo sprintf(
			'<a href="%1$s" title="%2$s" class="vfb-meta-entries-total post-com-count"><span class="comment-count">%4$s</span></a> %3$s',
			esc_url( $entries_url ),
			esc_attr__( 'Entries Total', 'vfb-pro' ),
			__( 'Total', 'vfb-pro' ),
			number_format_i18n( $count['total'] )
		);

		echo '<br class="clear"/>';

		if ( $count['today'] )
			echo '<strong>';


		echo sprintf(
			'<a href="%1$s" title="%2$s" class="vfb-meta-entries-total post-com-count"><span class="comment-count">%4$s</span></a> %3$s',
			esc_url( add_query_arg( array( 'today' => 1 ), $entries_url ) ),
			esc_attr__( 'Entries Today', 'vfb-pro' ),
			__( 'Today', 'vfb-pro' ),
			number_format_i18n( $count['today'] )
		);

		if ( $count['today'] )
			echo '</strong>';
	}

	/**
	 * Used for checkboxes and bulk editing
	 *
	 * @since 3.0
	 */
	public function column_cb( $item ){
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['form_id'] );
	}

	/**
	 * Builds the actual columns
	 *
	 * @since 3.0
	 */
	public function get_columns(){
		$columns = array(
			'cb' 	       => '<input type="checkbox" />', //Render a checkbox instead of text
			'form_title'   => __( 'Form' , 'vfb-pro'),
			'id' 	       => __( 'Form ID' , 'vfb-pro'),
			'entries'      => '<span class="vers"><span title="' . esc_attr__( 'Entries', 'vfb-pro' ) . '" class="comment-grey-bubble"></span></span>',
			'date_updated' => __( 'Date', 'vfb-pro' ),
		);

		return $columns;
	}

	/**
	 * A custom function to get the entries and sort them
	 *
	 * @since 3.0
	 * @returns array() $cols SQL results
	 */
	public function get_forms( $orderby = 'form_id', $order = 'ASC', $per_page, $offset = 0, $search = '' ){
		global $wpdb;

		// Set OFFSET for pagination
		$offset = ( $offset > 0 ) ? "OFFSET $offset" : '';

		$where = apply_filters( 'vfb_pre_get_forms', '' );

		// If the form filter dropdown is used
		if ( $this->current_filter_action() )
			$where .= ' AND forms.id = ' . $this->current_filter_action();

		// Forms type filter
		$where .= ( $this->get_form_status() && 'all' !== $this->get_form_status() ) ? $wpdb->prepare( ' AND forms.status = %s', $this->get_form_status() ) : '';

		// Always display all forms, unless an Form Type filter is set
		if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) )
			$where .= $wpdb->prepare( ' AND forms.status IN("%s","%s")', 'publish', 'draft' );

		$sql_order = sanitize_sql_orderby( "$orderby $order" );
		$cols = $wpdb->get_results( "SELECT * FROM " . VFB_FORMS_TABLE_NAME . " AS forms WHERE 1=1 $where $search ORDER BY $sql_order LIMIT $per_page $offset" );

		return $cols;
	}

	/**
	 * Get the form status: All, Trash
	 *
	 * @since 3.0
	 * @returns string Form status
	 */
	public function get_form_status() {
		if ( !isset( $_GET['status'] ) )
			return false;

		return esc_html( $_GET['status'] );
	}

	/**
	 * Build the different views for the entries screen
	 *
	 * @since 3.0
	 * @returns array $status_links Status links with counts
	 */
	public function get_views() {
		$status_links = array();
		$num_forms    = $this->get_forms_count();
		$class        = '';
		$link         = '?page=vfb-pro';

		$stati = array(
			'all'    => _n_noop( 'All <span class="count">(<span class="pending-count">%s</span>)</span>', 'All <span class="count">(<span class="pending-count">%s</span>)</span>' ),
			'draft'  => _n_noop( 'Draft <span class="count">(<span class="pending-count">%s</span>)</span>', 'Drafts <span class="count">(<span class="pending-count">%s</span>)</span>' ),
			'trash'  => _n_noop( 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>', 'Trash <span class="count">(<span class="trash-count">%s</span>)</span>' ),
		);

		$total_forms = (int) $num_forms->all;
		$entry_status = isset( $_GET['status'] ) ? $_GET['status'] : 'all';

		foreach ( $stati as $status => $label ) {
			$class = ( $status == $entry_status ) ? ' class="current"' : '';

			if ( !isset( $num_forms->$status ) )
				$num_forms->$status = 10;

			$link = esc_url( add_query_arg( 'status', $status, $link ) );

			$status_links[ $status ] = "<li class='$status'><a href='$link'$class>" . sprintf(
				translate_nooped_plural( $label, $num_forms->$status ),
				number_format_i18n( $num_forms->$status )
			) . '</a>';
		}

		return $status_links;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 3.0
	 * @returns array $stats Counts of different entry types
	 */
	public function get_entries_count( $form_id ) {
		$vfbdb = new VFB_Pro_Data();
		$count = $vfbdb->get_entries_count( $form_id );

		return $count;
	}

	/**
	 * Get the number of entries for use with entry statuses
	 *
	 * @since 3.0
	 * @returns array $stats Counts of different entry types
	 */
	public function get_entries_today_count( $form_id ) {
		$vfbdb = new VFB_Pro_Data();
		$count = $vfbdb->get_entries_count( $form_id, 'publish', 'AND DATE(p.post_date) >= DATE(curdate())' );

		return $count;
	}

	/**
	 * Get the number of forms
	 *
	 * @since 3.0
	 * @returns int $count Form count
	 */
	public function get_forms_count() {
		global $wpdb;

		$stats = array();

		$forms = $wpdb->get_results( "SELECT forms.status, COUNT(*) AS num_forms FROM " . VFB_FORMS_TABLE_NAME . " AS forms GROUP BY forms.status", ARRAY_A );

		$total = 0;
		$published = array( 'publish' => 'publish', 'draft' => 'draft', 'trash' => 'trash' );
		foreach ( (array) $forms as $row ) {
			// Don't count trashed toward totals
			if ( 'trash' != $row['status'] )
				$total += $row['num_forms'];
			if ( isset( $published[ $row['status' ] ] ) )
				$stats[ $published[ $row['status' ] ] ] = $row['num_forms'];
		}

		$stats['all'] = $total;
		foreach ( $published as $key ) {
			if ( empty( $stats[ $key ] ) )
				$stats[ $key ] = 0;
		}

		$stats = (object) $stats;

		return $stats;
	}

	/**
	 * Setup which columns are sortable. Default is by Date.
	 *
	 * @since 3.0
	 * @returns array() $sortable_columns Sortable columns
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' 	       => array( 'id', false ),
			'form_title'   => array( 'title', true ),
			'entries'      => array( 'entries', false ),
			'date_updated' => array( 'date_updated', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Define our bulk actions
	 *
	 * @since 3.0
	 * @returns array() $actions Bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array();

		// Build the row actions
		if ( current_user_can( 'vfb_delete_forms' ) ) {
			if ( !$this->get_form_status() || in_array( $this->get_form_status(), array( 'all', 'draft' ) ) ) {
				$actions['trash'] = __( 'Move to Trash', 'vfb-pro' );
			}
			elseif ( $this->get_form_status() && 'trash' == $this->get_form_status() ) {
				$actions['restore'] = __( 'Restore', 'vfb-pro' );
				$actions['delete']  = __( 'Delete Permanently', 'vfb-pro' );
			}
		}

		return apply_filters( 'vfb_forms_bulk_actions', $actions );
	}

	/**
	 * Process Bulk Actions form
	 *
	 * @since 3.0
	 */
	public function process_bulk_action() {
		global $wpdb;

		$form_id = '';

		// Set the Entry ID array
		if ( isset( $_POST['form'] ) ) {
			if ( is_array( $_POST['form'] ) )
				$form_id = $_POST['form'];
			else
				$form_id = (array) $_POST['form'];
		}

		switch( $this->current_action() ) :
			case 'trash' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->update( VFB_FORMS_TABLE_NAME, array( 'status' => 'trash' ), array( 'id' => $id ) );
				}

				break;

			case 'restore' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->update( VFB_FORMS_TABLE_NAME, array( 'status' => 'publish' ), array( 'id' => $id ) );
				}

				break;

			case 'delete' :
				foreach ( $form_id as $id ) {
					$id = absint( $id );
					$wpdb->delete( VFB_FORMS_TABLE_NAME, array( 'id' => $id ) );
					$wpdb->delete( VFB_FIELDS_TABLE_NAME, array( 'form_id' => $id ) );
					$wpdb->delete( VFB_FORM_META_TABLE_NAME, array( 'form_id' => $id ) );
				}

				break;

		endswitch;
	}

	/**
	 * Process action row links below form title
	 *
	 * This is different than bulk action processing
	 *
	 * @access public
	 * @return void
	 */
	public function process_row_action_links() {
		global $wpdb;

		if ( !isset( $_GET['vfb-action'] ) )
			return;

		if ( !isset( $_GET['form'] ) )
			return;

		$action  = esc_html( $_GET['vfb-action'] );
		$form_id = absint( $_GET['form'] );

		switch( $action ) :
			case 'trash' :

				$wpdb->update( VFB_FORMS_TABLE_NAME, array( 'status' => 'trash' ), array( 'id' => $form_id ) );

				break;

			case 'restore' :
				check_admin_referer( 'vfbp_undo_trash' );

				$wpdb->update( VFB_FORMS_TABLE_NAME, array( 'status' => 'publish' ), array( 'id' => $form_id ) );
				break;

			case 'delete' :
				check_admin_referer( 'vfbp_delete_form' );

				$wpdb->delete( VFB_FORMS_TABLE_NAME, array( 'id' => $form_id ) );
				$wpdb->delete( VFB_FIELDS_TABLE_NAME, array( 'form_id' => $form_id ) );
				$wpdb->delete( VFB_FORM_META_TABLE_NAME, array( 'form_id' => $form_id ) );

				break;

		endswitch;
	}

	/**
	 * Set our forms filter action
	 *
	 * @since 3.0
	 * @returns int Form ID
	 */
	public function current_filter_action() {
		if ( isset( $_POST['form-filter'] ) && -1 != $_POST['form-filter'] )
			return absint( $_POST['form-filter'] );

		return false;
	}

	/**
	 * Display Search box
	 *
	 * @since 3.0
	 * @returns html Search Form
	 */
	public function search_box( $text, $input_id ) {
	    parent::search_box( $text, $input_id );
	}

	/**
	 * Prepares our data for display
	 *
	 * @since 3.0
	 */
	public function prepare_items() {
		global $wpdb;

		// get the current user ID
		$user = get_current_user_id();

		// get the current admin screen
		$screen = get_current_screen();

		// retrieve the "per_page" option
		$screen_option = $screen->get_option( 'per_page', 'option' );

		// retrieve the value of the option stored for the current user
		$per_page = get_user_meta( $user, $screen_option, true );

		// get the default value if none is set
		if ( empty ( $per_page ) || $per_page < 1 )
			$per_page = 20;

		// Get the date/time format that is saved in the options table
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		// What page are we looking at?
		$current_page = $this->get_pagenum();

		// Use offset for pagination
		$offset = ( $current_page - 1 ) * $per_page;

		// Get column headers
		$columns  = $this->get_columns();
		$hidden   = get_hidden_columns( $this->screen );

		// Get sortable columns
		$sortable = $this->get_sortable_columns();

		// Build the column headers
		$this->_column_headers = array($columns, $hidden, $sortable);

		// Get entries search terms
		$search_terms = !empty( $_POST['s'] ) ? explode( ' ', $_POST['s'] ) : array();

		$searchand = $search = '';
		// Loop through search terms and build query
		foreach( $search_terms as $term ) {
			$term = esc_sql( $wpdb->esc_like( $term ) );

			$search .= "{$searchand}(forms.title LIKE '%{$term}%')";
			$searchand = ' AND ';
		}

		$search = ( !empty($search) ) ? " AND ({$search}) " : '';

		// Set our ORDER BY and ASC/DESC to sort the entries
		$orderby  = !empty( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';
		$order    = !empty( $_GET['order'] ) ? $_GET['order'] : 'desc';

		// Get the sorted entries
		$forms = $this->get_forms( $orderby, $order, $per_page, $offset, $search );

		$data = array();

		// Loop trough the entries and setup the data to be displayed for each row
		foreach ( $forms as $form ) {
			// Get entries totals
			$entries_total = $this->get_entries_count( $form->id );
			$entries_today = $this->get_entries_today_count( $form->id );

			$entries_counts = array(
				'total' => $entries_total,
				'today' => $entries_today,
			);

			$date_time = mysql2date( __( 'Y/m/d g:i:s A', 'vfb-pro' ), $form->date_updated, true );

			$data[] = array(
				'id' 			=> $form->id,
				'form_id'		=> $form->id,
				'form_title' 	=> stripslashes( esc_html( $form->title ) ),
				'entries'		=> $entries_counts,
				'status'		=> $form->status,
				'date_updated'  => $date_time,
			);
		}

		$where = '';

		// Forms type filter
		$where .= ( $this->get_form_status() && 'all' !== $this->get_form_status() ) ? $wpdb->prepare( ' AND forms.status = %s', $this->get_form_status() ) : '';

		// Always display all forms, unless an Form Type filter is set
		if ( !$this->get_form_status() || 'all' == $this->get_form_status() )
			$where .= $wpdb->prepare( ' AND forms.status = %s', 'publish' );

		// How many form do we have?
		$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM " . VFB_FORMS_TABLE_NAME . " AS forms WHERE 1=1 $where" );

		// Add sorted data to the items property
		$this->items = $data;

		// Register our pagination
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Display the pagination.
	 * Customize default function to work with months and form drop down filters
	 *
	 * @since 3.0
	 * @access protected
	 */
	public function pagination( $which ) {
		global $current_user;

		if ( empty( $this->_pagination_args ) )
			return;

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];

		$output = '<span class="displaying-num">' . sprintf( _n( '1 form', '%s forms', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		// Added to pick up the months dropdown
		$m = isset( $_REQUEST['m'] ) ? (int) $_REQUEST['m'] : 0;

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		// Modified the add_query_args to include my custom dropdowns
		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( array( 'paged' => max( 1, $current-1 ), 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( array( 'paged' => min( $total_pages, $current+1 ), 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&rsaquo;'
		);

		// Modified the add_query_args to include my custom dropdowns
		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( array( 'paged' => $total_pages, 'm' => $m, 'form-filter' => $this->current_filter_action() ), $current_url ) ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) )
			$pagination_links_class = ' hide-if-js';
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}
}
