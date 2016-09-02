<?php
/**
 * Class that controls the Import page view
 *
 * @since      3.0
 */
class VFB_Pro_Import {

	/**
	 * id
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $id;

	/**
	 * __construct function
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_import_forms' ) )
			return;
	?>
	<div class="wrap">
		<h2><?php _e( 'Import', 'vfb-pro' ); ?></h2>
	<?php
		$this->dispatch();

		$import_url = add_query_arg(
			array(
				'page'       => 'vfbp-import',
				'import'     => 'vfb',
				'step'      => 1,
			),
			wp_nonce_url( admin_url( 'admin.php' ), 'vfbp_import_form' )
		);

		$this->import_upload_form( $import_url );
	?>
	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Manages the separate stages of the XML import process
	 *
	 * @since 1.7
	 *
	 */
	public function dispatch() {

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

		switch ( $step ) {
			case 0:
				printf( '<p>%s</p>', __( 'Select a Visual Form Builder Pro backup file (.json), then click Upload file and import.', 'vfb-pro' ) );

				break;

			case 1:
				check_admin_referer( 'vfbp_import_form' );

				if ( $this->handle_upload() ) {
					$file = get_attached_file( $this->id );
					set_time_limit(0);
					$this->import( $file );
				}

				break;
		}
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @access public
	 * @param mixed $file
	 * @return void
	 */
	public function import( $file ) {

		$data = file_get_contents( $file );

		// Turns cache invalidation off
		wp_suspend_cache_invalidation( true );

		$this->import_data( $data );

		// Turns cache invalidation on
		wp_suspend_cache_invalidation( false );

		// Deletes the upload file
		wp_import_cleanup( $this->id );

		// Removes all cache items
		wp_cache_flush();

		// All Done message
		printf( '<p>%1$s <a href="%2$s">%3$s</a></p>',
			__( 'All done.', 'vfb-pro' ),
			admin_url( 'admin.php?page=vfb-pro' ),
			__( 'View Forms', 'vfb-pro' )
		);
	}

	/**
	 * Import forms, fields, meta, and settings
	 *
	 * @access public
	 * @param mixed $file
	 * @return void
	 */
	public function import_data( $file ) {
		global $wpdb;

		// Remove potential BOM(Byte Order Mark) from UTF-8 file_get_contents
		$file = $this->remove_utf8_bom( $file );

		// Parse the JSON from our uploaded file
		$data = $this->parseJSON( $file );

		if ( !$data )
			return;

		$form   = $data['form'];
		$fields = $data['fields'];
		$meta   = $data['meta'];

		unset( $form['id'] );

		$form['data'] = maybe_serialize( $form['data'] );

		$wpdb->insert(
			VFB_FORMS_TABLE_NAME,
			$form,
			'%s'
		);

		$form_id    = $wpdb->insert_id;
		$form['id'] = $form_id;

		if ( is_array( $fields ) ) {
			 for ( $x = 0; $x < count( $fields ); $x++ ) {
				$fields[ $x ]['form_id'] = $form_id;
				$fields[ $x ]['data']    = serialize( $fields[ $x ]['data'] );
				$old_field_id            = $fields[ $x ]['id'];
				$fields[ $x ]['id']      = null;

				$wpdb->insert(
					VFB_FIELDS_TABLE_NAME,
					$fields[ $x ]
				);

				$fields[ $x ]['id']     = $wpdb->insert_id;
				$fields[ $x ]['old_id'] = $old_field_id;
				$fields[ $x ]['data']   = unserialize( $fields[ $x ]['data'] );
			 }
		}

		if ( is_array( $meta ) ) {
			foreach ( $meta as $meta_key => $meta_value ) {
				// Update Merge Tags with new field IDs
				foreach ( $fields as $field ) {
					$meta_value = str_replace( '{entry:Field' . $field['old_id'] . '}', '{entry:Field' . $field['id'] . '}', $meta_value );

					// If Field or Email rules, update with new field IDs
					if ( in_array( $meta_key, array( 'rules', 'rules-email' ) ) ) {
						$rules      = json_encode( $meta_value );
						$meta_value = str_replace( '"field-id":"' . $field['old_id'] . '"', '"field-id":"' . $field['id'] . '"', $rules );
						$meta_value = json_decode( $meta_value, true );
					}
				}

				$wpdb->insert(
					VFB_FORM_META_TABLE_NAME,
					array(
						'form_id'    => $form['id'],
						'meta_key'   => $meta_key,
						'meta_value' => maybe_serialize( $meta_value ),
					)
				);
			}
		}
	}

	/**
	 * Decodes the JSON string
	 *
	 * @access public
	 * @param mixed $json
	 * @return void
	 */
	public function parseJSON( $json ) {
		return json_decode( $json, true );
	}

	/**
	 * Return a meaningful JSON error
	 *
	 * Used during the handle_upload method to test JSON parsing before processing import
	 *
	 * @access public
	 * @param mixed $error
	 * @return void
	 */
	public function get_JSON_error_msg( $error ) {
		$output = '';

		switch ( $error ) {
	        case JSON_ERROR_NONE:
	            $output = 'No errors';
				break;

	        case JSON_ERROR_DEPTH:
	            $output = 'Maximum stack depth exceeded';
				break;

	        case JSON_ERROR_STATE_MISMATCH:
	            $output = 'Underflow or the modes mismatch';
				break;

	        case JSON_ERROR_CTRL_CHAR:
	            $output = 'Unexpected control character found';
				break;

	        case JSON_ERROR_SYNTAX:
	            $output = 'Syntax error, malformed JSON';
				break;

	        case JSON_ERROR_UTF8:
	            $output = 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;

	        default:
	            $output = 'Unknown error';
				break;
	    }

	    return $output;
	}

	/**
	 * Handles the upload and initial parsing of the file to prepare for
	 *
	 * @since 3.0
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	public function handle_upload() {
		$file = $this->import_handle_upload();

		if ( isset( $file['error'] ) ) {
			printf(
				'<p><strong>%s</strong><br>%s</p>',
				__( 'Sorry, there has been an error.', 'vfb-pro' ),
				esc_html( $file['error'] )
			);

			return false;
		}
		elseif ( ! file_exists( $file['file'] ) ) {
			printf(
				'<p><strong>%s</strong><br>',
				__( 'Sorry, there has been an error.', 'vfb-pro' )
			);

			printf(
				__( 'The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.</p>', 'vfb-pro' ),
				esc_html( $file['file'] )
			);

			return false;
		}

		// Set the global File ID
		$this->id = (int) $file['id'];

		$data = file_get_contents( $file['file'] );
		if ( false === $data ) {
			printf(
				'<p><strong>%s</strong><br>%s</p>',
				__( 'Sorry, there has been an error.', 'vfb-pro' ),
				__( 'Could not read the imported file.', 'vfb-pro' )
			);

			return false;
		}
		elseif ( null === $data ) {
			printf(
				'<p><strong>%s</strong><br>%s</p>',
				__( 'file_get_contents() disabled', 'vfb-pro' ),
				__( 'The PHP file_get_contents() function is required to import data. Please contact your host to enable this function.', 'vfb-pro' )
			);

			return false;
		}

		// Remove potential BOM(Byte Order Mark) from UTF-8 file_get_contents
		$data = $this->remove_utf8_bom( $data );

		// Parse the JSON from our uploaded file
		$json = $this->parseJSON( $data );

		// Test parsing JSON to determine if there are errors before processing
		if ( !$json ) {
			$json_error = $this->get_JSON_error_msg( json_last_error() );
			printf(
				'<p><strong>%s</strong><br>%s</p>',
				__( 'JSON decoding of the file has failed.', 'vfb-pro' ),
				$json_error
			);

			return false;
		}

		return true;
	}

	/**
	 * Displays the import upload form
	 *
	 * This is a copy of the wp_import_upload_form and modified to allow custom nonce
	 *
	 * @access public
	 * @param mixed $action
	 * @return void
	 */
	public function import_upload_form( $action ) {

		// Allowed upload size. Default is 1 MB
		$bytes      = wp_max_upload_size();
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['error'] ) ) :
	?>
		<div class="error">
			<p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'vfb-pro' ); ?></p>
			<p><strong><?php echo $upload_dir['error']; ?></strong></p>
		</div> <!-- .error -->
	<?php
		else :
	?>
		<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" action="<?php echo esc_url( $action ); ?>">
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
			<p>
				<label for="vfb-upload"><?php _e( 'Choose a file from your computer:', 'vfb-pro' ); ?></label> (<?php printf( __( 'Maximum size: %s', 'vfb-pro' ), $size ); ?>)
				<input type="file" id="vfb-upload" name="vfb-import" size="25" />
			</p>
			<?php
				submit_button(
					__( 'Upload file and import', 'vfb-pro' ),
					'button',
					'' // leave blank so "name" attribute will not be added
				);
			?>
			</form>
	<?php
		endif;
	}

	/**
	 * Handles the import upload form
	 *
	 * This is a copy of the wp_import_handle_upload and modified to allow custom $_FILES input
	 *
	 * @access public
	 * @return void
	 */
	public function import_handle_upload() {
		if ( !isset( $_FILES['vfb-import'] ) ) {
			$file['error'] = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'vfb-pro' );
			return $file;
		}

		$overrides = array( 'test_form' => false, 'test_type' => false );
		$_FILES['vfb-import']['name'] .= '.txt';
		$file = wp_handle_upload( $_FILES['vfb-import'], $overrides );

		if ( isset( $file['error'] ) )
			return $file;

		$url      = $file['url'];
		$type     = $file['type'];
		$file     = $file['file'];
		$filename = basename( $file );

		// Construct the object array
		$object = array(
			'post_title'     => $filename,
			'post_content'   => $url,
			'post_mime_type' => $type,
			'guid'           => $url,
			'context'        => 'import',
			'post_status'    => 'private',
		);

		// Save the data
		$id = wp_insert_attachment( $object, $file );

		/*
		 * Schedule a cleanup for one day from now in case of failed
		 * import or missing wp_import_cleanup() call.
		 */
		wp_schedule_single_event( time() + DAY_IN_SECONDS, 'vfb_importer_scheduled_cleanup', array( $id ) );

		return array( 'file' => $file, 'id' => $id );
	}

	/**
	 * remove_utf8_bom function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	public function remove_utf8_bom( $text ) {
	    $bom  = pack('H*','EFBBBF');
	    $text = preg_replace("/^$bom/", '', $text);

	    return $text;
	}
}