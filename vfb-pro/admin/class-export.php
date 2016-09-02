<?php
/**
 * Class that controls the Export page view
 *
 * @since      3.0
 */
class VFB_Pro_Export {

	/**
	 * Default delimiter for CSV and Tab export
	 *
	 * Override using the vfb_csv_delimiter filter
	 *
	 * (default value: ',')
	 *
	 * @var string
	 * @access protected
	 */
	protected $delimiter = ',';

	/**
	 * __construct function
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct() {
		// CSV delimiter
		$this->delimiter = apply_filters( 'vfb_csv_delimiter', ',' );

		add_action( 'admin_init', array( $this, 'export_action' ) );
		add_action( 'wp_ajax_vfbp-export-fields', array( $this, 'load_fields' ) );
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_export_forms' ) )
			return;

		$vfbdb = new VFB_Pro_Data();
		$forms = $vfbdb->get_all_forms();
	?>
	<div class="wrap">
		<h2><?php _e( 'Export', 'vfb-pro' ); ?></h2>

		<form method="post" id="vfbp-export" action="">
			<input name="_vfbp_action" type="hidden" value="export" />
			<?php
				wp_nonce_field( 'vfbp_export' );
			?>

			<p><?php _e( 'Backup and save some or all of your Visual Form Builder Pro data.', 'vfb-pro' ); ?></p>
        	<p><?php _e( 'Once you have saved the file, you will be able to import Visual Form Builder Pro data from this site into another site.', 'vfb-pro' ); ?></p>
        	<h3><?php _e( 'Choose what to export', 'vfb-pro' ); ?></h3>

        	<p>
	        	<label for="content-forms">
	        		<input type="radio" id="content-forms" name="settings[content]" value="forms" checked="checked" /> <?php _e( 'Forms', 'vfb-pro' ); ?>
	        	</label>
	        </p>
        	<p class="description"><?php _e( 'This will export a single form with all fields and settings for that form.', 'vfb-pro' ); ?></p>

        	<p>
	        	<label for="content-entries">
	        		<input type="radio" id="content-entries" name="settings[content]" value="entries" /> <?php _e( 'Entries', 'vfb-pro' ); ?>
	        	</label>
	        </p>
        	<p class="description"><?php _e( 'This will export entries in either .csv, .txt, or .xls and cannot be used with the Import.', 'vfb-pro' ); ?></p>

			<h3><?php _e( 'Select a form', 'vfb-pro' ); ?></h3>
        	<select name="settings[form-id]" id="vfb-export-forms-list">
			<?php
				$first_form    = '';
				$entries_count = 0;

				if ( is_array( $forms ) && !empty( $forms ) ) {
					$first_form = $forms[0];

					foreach ( $forms as $form ) {
						echo sprintf(
							'<option value="%1$d">%1$d - %2$s</option>',
							$form['id'],
							$form['title']
						);
					}
				}
			?>
			</select>

			<div class="vfb-export-entries-options">
				<h3><?php _e( 'Customize your export', 'vfb-pro' ); ?></h3>

				<p>
					<label class="vfb-export-label" for="format"><?php _e( 'Format:', 'vfb-pro' ); ?></label>
	    			<select name="settings[format]">
	    				<option value="csv" selected="selected"><?php _e( 'Comma Separated (.csv)', 'vfb-pro' ); ?></option>
	    				<option value="txt"><?php _e( 'Tab Delimited (.txt)', 'vfb-pro' ); ?></option>
	    				<option value="xls"><?php _e( 'Excel (.xls)', 'vfb-pro' ); ?></option>
	    			</select>
    			</p>

				<p>
					<label class="vfb-export-label" for="start-date"><?php _e( 'Date Range:', 'vfb-pro' ); ?></label>
					<?php _e( 'Start', 'vfb-pro' ); ?> <input type="text" id="start-date" name="settings[start-date]" value="" />

					<label for="end-date"><?php _e( 'End', 'vfb-pro' ); ?></label>
					<input type="text" id="end-date" name="settings[end-date]" value="" />
				</p>

				<label class="vfb-export-label"><?php _e( 'Fields:', 'vfb-pro' ); ?></label>

				<p>
    				<a id="vfb-export-select-all" href="#"><?php _e( 'Select All', 'vfb-pro' ); ?></a>
    				<a id="vfb-export-unselect-all" href="#"><?php _e( 'Unselect All', 'vfb-pro' ); ?></a>
    			</p>
    			<div id="vfb-export-entries-fields">
	    			<?php $this->fields_list( $first_form['id'] ); ?>
    			</div> <!-- #vfb-export-entries-fields -->
			</div> <!-- .vfb-export-entries-options -->
		<?php
			submit_button(
				__( 'Download Export File', 'vfb-pro' ),
				'primary',
				'' // leave blank so "name" attribute will not be added
			);
		?>
		</form>
	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Determine which export function to execute based on selected options
	 *
	 * @access public
	 * @return void
	 */
	public function export_action() {

		if ( !isset( $_POST['_vfbp_action'] ) || !isset( $_GET['page'] ) )
			return;

		if ( 'export' !== $_POST['_vfbp_action'] )
			return;

		check_admin_referer( 'vfbp_export' );

		$data = array();

		foreach ( $_POST['settings'] as $key => $val ) {
			$data[ $key ] = $val;
		}

		$data       = stripslashes_deep( $data );
		$content    = isset( $data['content'] ) ? $data['content'] : 'forms';
		$form_id    = isset( $data['form-id'] ) ? $data['form-id'] : 0;
		$format     = isset( $data['format'] ) ? $data['format'] : 'csv';
		$start_date = isset( $data['start-date'] ) ? $data['start-date'] : '';
		$end_date   = isset( $data['end-date'] ) ? $data['end-date'] : '';
		$fields     = isset( $data['fields'] ) ? $data['fields'] : '';
		$vfbdb = new VFB_Pro_Data();

		if ( 0 == $form_id )
			return;

		switch ( $content ) {
			case 'forms' :

				$settings['form']   = $vfbdb->get_form_by_id( $form_id );
				$settings['fields'] = $vfbdb->get_fields( $form_id );
				$settings['meta']   = $vfbdb->get_meta_by_id( $form_id );

				$title = sanitize_key( $settings['form']['title'] );

				$this->export( $settings, $title );

				die(1);

				break;

			case 'entries' :
				// If no fields selected, exit because there's nothing to do
				if ( empty( $fields ) )
					return;

				$forms = $vfbdb->get_form_by_id( $form_id );
				$title = sanitize_key( $forms['title'] );

				$where = " AND p.post_status = 'publish'";
				if ( $start_date )
					$where .= sprintf( ' AND p.post_date >= "%s"', date( 'Y-m-d H:i:s', strtotime( $start_date ) ) );

				if ( $end_date )
					$where .= sprintf( ' AND p.post_date <= "%s"', date( 'Y-m-d H:i:s', strtotime( $end_date ) ) );

				$entry_data = $vfbdb->get_entries_meta_by_form_id( $form_id, $where );

				$entry_meta = $selected = array();
				$x = 0;

				foreach( $entry_data as $entry ) {
					// Get all postmeta for this entry
					$entry_meta = get_post_meta( $entry['ID'] );

					// Get the entry sequence number
					$seq_num = isset( $entry_meta['_vfb_seq_num'][0] ) ? $entry_meta['_vfb_seq_num'][0] : 0;

					// Setup initial selected fields array
					$selected[ $x ] = array(
						'seq-num'  => $seq_num,
						'entry-id' => $entry['ID'],
						'date'     => $entry['post_date'],
					);

					// Loop through postmeta for this entry
					foreach ( $entry_meta as $meta_key => $meta_value ) {
						$field_id = str_replace( '_vfb_field-', '', $meta_key );
						$field    = $vfbdb->get_field_by_id( $field_id );
						$label    = isset( $field['data']['label'] ) ? $field['data']['label'] : '';

						// Add field data to our selected array
						if ( isset( $fields[ $field_id ] ) ) {
							$value = isset( $meta_value[0] ) ? $meta_value[0] : '';

							$selected[ $x ]['data'][ $field_id ] = array(
								'label' => $label,
								'value' => $meta_value[0],
							);
						}
					}

					$x++;
				}

				$settings['format'] = $format;
				$settings['fields'] = $selected;

				$this->export_entries( $settings, $title );

				die(1);

				break;
		}
	}

	/**
	 * export function.
	 *
	 * @access public
	 * @param array $data (default: array())
	 * @param string $title (default: empty string)
	 * @return void
	 */
	public function export( $data = array(), $title = '' ) {
		if ( !is_array( $data ) || empty( $data ) )
			return;

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = "{$sitename}vfb-pro-export.{$title}." . date( 'Y-m-d-Hi' ) . '.json';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), true );

		echo json_encode( $data );
	}

	/**
	 * export_entries function.
	 *
	 * @access public
	 * @param array $data (default: array())
	 * @param string $title (default: '')
	 * @return void
	 */
	public function export_entries( $data = array(), $title = '' ) {
		if ( !is_array( $data ) || empty( $data ) )
			return;

		$format = $data['format'];

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty($sitename) ) $sitename .= '.';
		$filename = "{$sitename}vfb-pro-export.{$title}." . date( 'Y-m-d-Hi' ) . ".{$format}";

		// Set content type based on file format
		switch ( $format ) {
			case 'csv' :
				$content_type = 'text/csv';
				break;

			case 'txt' :
				$content_type = 'text/plain';
				break;

			case 'xls' :
				$content_type = 'application/vnd.ms-excel';
				break;
		}

		$upload_dir = wp_upload_dir();
		$file_path  = trailingslashit( $upload_dir['path'] ) . $filename;

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( "Content-Type: $content_type; charset=" . get_option( 'blog_charset' ), true );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		if ( in_array( $format, array( 'csv', 'txt' ) ) )
			$this->csv_tab( $data['fields'], $format, $file_path );
		elseif ( 'xls' == $format )
			$this->xls( $data['fields'], $file_path );
	}

	/**
	 * Outputs CSV or Tab Delimited file
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $format
	 * @param mixed $file_path
	 * @return void
	 */
	public function csv_tab( $fields, $format, $file_path ) {
		// Override delimiter if tab separated
		if ( 'txt' == $format )
			$this->delimiter = "\t";

		$file = fopen( $file_path, 'w' );

		$headers = $rows = array();

		// Headers
		$headers['entry_id'] = __( 'Entry ID', 'vfb-pro' );
		foreach ( $fields as $field ) {
			if ( isset( $field['data'] ) && is_array( $field['data'] ) ) {
				foreach ( $field['data'] as $field_id => $data ) {
					$headers[ $field_id ] = $data['label'];
				}
			}
		}
		$headers['entry_date'] = __( 'Entry Date', 'vfb-pro' );
		$headers = array_unique( $headers );

		fputcsv( $file, $headers, $this->delimiter );

		// Entry data
		foreach ( $fields as $field ) {
			$rows['entry_id'] = $field['seq-num'];

			if ( isset( $field['data'] ) && is_array( $field['data'] ) ) {
				foreach ( $headers as $field_id => $label ) {
					if ( isset( $field['data'][ $field_id ] ) ) {
						$rows[ $field_id ] = $field['data'][ $field_id ]['value'];
					}
					else if ( !in_array( $field_id, array( 'entry_id', 'entry_date' ) ) ) {
						$rows[ $field_id ] = '';
					}
				}
			}

			$rows['entry_date'] = $field['date'];

			fputcsv( $file, $rows, $this->delimiter );

			$rows = array();
		}

		// Close the file
		fclose( $file );

		// Reads file in uploads folder and writes to output buffer
		readfile( $file_path );

		exit();
	}

	/**
	 * Output Excel compatible file
	 *
	 * @access public
	 * @param mixed $fields
	 * @param mixed $file_path
	 * @return void
	 */
	public function xls( $fields, $file_path ) {

		$file = fopen( $file_path, 'w' );

		$output  = '';
		$headers = $rows = array();

		// Headers
		$headers['entry_id'] = __( 'Entry ID', 'vfb-pro' );
		foreach ( $fields as $field ) {
			if ( isset( $field['data'] ) && is_array( $field['data'] ) ) {
				foreach ( $field['data'] as $field_id => $data ) {
					$headers[ $field_id ] = $data['label'];
				}
			}
		}
		$headers['entry_date'] = __( 'Entry Date', 'vfb-pro' );
		$headers = array_unique( $headers );

		$output .= '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";

		$output .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
		<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
			<AllowPNG />
		</OfficeDocumentSettings>
		<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
			<WindowHeight>15020</WindowHeight>
			<WindowWidth>25360</WindowWidth>
			<WindowTopX>240</WindowTopX>
			<WindowTopY>240</WindowTopY>
			<Date1904 />
			<ProtectStructure>False</ProtectStructure>
			<ProtectWindows>False</ProtectWindows>
		</ExcelWorkbook>
		<Styles>
			<Style ss:ID="Default" ss:Name="Normal">
				<Alignment ss:Vertical="Bottom"/>
				<Borders/>
				<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
				<Interior/>
				<NumberFormat/>
				<Protection/>
			</Style>
			<Style ss:ID="s62">
				<NumberFormat ss:Format="General Date"/>
			</Style>
			<Style ss:ID="s72">
				<Borders>
					<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
				</Borders>
				<Font ss:FontName="Calibri" ss:Size="14" ss:Color="#333333" ss:Bold="1"/>
				<Interior ss:Color="#C0C0C0" ss:Pattern="Solid"/>
			</Style>
		</Styles>';

		$output .= '<Worksheet ss:Name="Sheet1">
			<Table x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="65" ss:DefaultRowHeight="15">
				<Row ss:AutoFitHeight="0" ss:StyleID="s72">';

		foreach ( $headers as $header ) {
			$output .= sprintf( '<Cell><Data ss:Type="String">%s</Data></Cell>', wp_specialchars_decode( esc_html( $header ), ENT_QUOTES ) );
		}

		$output .= '</Row>';

		foreach ( $fields as $field ) {
			$output .= '<Row ss:AutoFitHeight="0">';

			// Entry ID
			$output .= sprintf( '<Cell%3$s><Data ss:Type="%2$s">%1$s</Data></Cell>', $field['seq-num'], 'Number', '' );

			// Entry Data
			if ( isset( $field['data'] ) && is_array( $field['data'] ) ) {
				foreach ( $headers as $field_id => $label ) {
					if ( isset( $field['data'][ $field_id ] ) ) {
						$type      = 'String';
						$item      = $field['data'][ $field_id ]['value'];
						$style     = '';
						$timestamp = strtotime( $item );

						if ( preg_match( '/^-?\d+(?:[.,]\d+)?$/', $item ) && ( strlen( $item ) < 15 ) ) {
							$type = 'Number';
						}
						else if ( preg_match( '/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/', $item ) && $timestamp > 0 && $timestamp < strtotime( '+500 years' ) ) {
							$type    = 'DateTime';
							$item    = strftime( '%Y-%m-%dT%H:%M:%S', $timestamp );
							$style   = ' ss:StyleID="s62"';
						}

						if ( !empty( $item ) )
							$output .= sprintf( '<Cell%3$s><Data ss:Type="%2$s">%1$s</Data></Cell>', esc_html( $item ), $type, $style );
						else
							$output .= '<Cell><Data ss:Type="String"></Data></Cell>';
					}
					else if ( !in_array( $field_id, array( 'entry_id', 'entry_date' ) ) ) {
						$output .= '<Cell><Data ss:Type="String"></Data></Cell>';
					}
				}
			}

			// Entry Date
			$output .= sprintf( '<Cell%3$s><Data ss:Type="%2$s">%1$s</Data></Cell>', strftime( '%Y-%m-%dT%H:%M:%S', strtotime( $field['date'] ) ), 'DateTime', ' ss:StyleID="s62"' );

			$output .= '</Row>';
		}

		$output .= '</Table>
				<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
					<Unsynced/>
					<PageLayoutZoom>0</PageLayoutZoom>
					<Selected/>
					<Panes>
						<Pane>
							<Number>3</Number>
							<ActiveRow>3</ActiveRow>
							<ActiveCol>3</ActiveCol>
						</Pane>
					</Panes>
					<ProtectObjects>False</ProtectObjects>
					<ProtectScenarios>False</ProtectScenarios>
				</WorksheetOptions>
			</Worksheet>
		</Workbook>';

		fwrite( $file, $output );

		// Close the file
		fclose( $file );

		// Reads file in uploads folder and writes to output buffer
		readfile( $file_path );

		exit();
	}

	/**
	 * fields_list function.
	 *
	 * @access public
	 * @param mixed $form_id
	 * @return void
	 */
	public function fields_list( $form_id ) {
		$vfbdb  = new VFB_Pro_Data();
		$fields = $vfbdb->get_fields( $form_id, "AND field_type NOT IN ('heading','instructions','page-break','captcha','submit') ORDER BY field_order ASC" );

		$entries_count = $vfbdb->get_entries_count( $form_id );
		if ( 0 == $entries_count )
			return _e( 'No entries.', 'vfb-pro' );

		if ( is_array( $fields ) && !empty( $fields ) ) {
			foreach ( $fields as $field ) {
			?>
			<label for="vfb-export-fields-val-<?php echo $field['id']; ?>">
				<input name="settings[fields][<?php echo $field['id']; ?>]" class="vfb-export-fields-vals" id="vfb-export-fields-val-<?php echo $field['id']; ?>" type="checkbox" value="<?php echo $field['id']; ?>" /> <?php echo $field['data']['label']; ?>
			</label>
			<br />
			<?php
			}
		}
	}

	/**
	 * AJAX function to load new fields list when a new form is selected
	 *
	 * @access public
	 * @return void
	 */
	public function load_fields() {
		global $wpdb;

		// Check AJAX nonce set via wp_localize_script
		check_ajax_referer( 'vfbp_ajax', 'vfbp_ajax_nonce' );

		if ( isset( $_GET['action'] ) && 'vfbp-export-fields' !== $_GET['action'] )
			return;

		$form_id = absint( $_GET['id'] );

		$this->fields_list( $form_id );

		die(1);
	}
}