<?php

class VFB_Pro_Upload {
	/**
	 * The form ID
	 *
	 * @var mixed
	 * @access private
	 */
	private $id;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id = (int) $id;

		add_filter( 'upload_dir', array( $this, 'set_upload_dir' ) );
	}

	/**
	 * Upload the file(s) and return file URL(s) if successful.
	 *
	 * @access public
	 * @param mixed $file
	 * @param mixed $entry_id
	 * @return void
	 */
	public function handle_upload( $file, $entry_id ) {
		if ( !is_array( $file ) )
			return;

		/**
		 * Filter whether or not to skip file uploads
		 *
		 * Passing a falsey value to the filter will effectively short-circuit
		 * any attempt at uploading files.
		 *
		 * @since 3.0
		 *
		 */
		if ( apply_filters( 'vfbp_skip_upload', false, $this->id ) )
			return;

		/**
		 * Action that fires before files are uploaded
		 *
		 * Passes the Form ID and a single element of the $_FILES array
		 *
		 * @since 3.0
		 *
		 */
		do_action( 'vfbp_before_upload', $this->id, $file );

		// We need to include the file that runs the wp_handle_upload function
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$uploads = array();

		// Multi uploads
		if ( is_array( $file['name'] ) ) {
			foreach ( $file['name'] as $key => $val ) {
				if ( $file['name'][ $key ] ) {
					// Rebuild file array, which wp_handle_upload requires
					$upload = array(
						'name'     => $file['name'][ $key ],
						'type'     => $file['type'][ $key ],
						'tmp_name' => $file['tmp_name'][ $key ],
						'error'    => $file['error'][ $key ],
						'size'     => $file['size'][ $key ]
					);

					// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
					$tmp_upload = wp_handle_upload( $upload, array( 'test_form' => false ) );

					$this->insert_attachment( $tmp_upload, $entry_id );

					if ( $tmp_upload ) {
						$uploads[] = $tmp_upload['url'];
					}
				}
			}

			return $uploads;
		}
		else {
			// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
			$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

			if ( $upload && !isset( $upload['error'] ) ) {
				$this->insert_attachment( $upload, $entry_id );

				return $upload['url'];
			}
		}

		return;
	}

	/**
	 * Insert the attachment into the Media Library.
	 *
	 * @access private
	 * @param mixed $upload
	 * @param int $entry_id (default: 0)
	 * @return void
	 */
	private function insert_attachment( $upload, $entry_id = 0 ) {
		// Retrieve the file type from the file name. Returns an array with extension and mime type
		$filetype = wp_check_filetype( basename( $upload['file'] ), null );

		// Return the current upload directory location
		$upload_dir = wp_upload_dir();

		$media_upload = array(
			'guid' 				=> $upload_dir['url'] . '/' . basename( $upload['file'] ),
			'post_mime_type' 	=> $filetype['type'],
			'post_title' 		=> preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
			'post_content' 		=> '',
			'post_status' 		=> 'inherit'
		);

		// Insert attachment into Media Library and get attachment ID
		$attach_id = wp_insert_attachment( $media_upload, $upload['file'], $entry_id );

		// Include the file that runs wp_generate_attachment_metadata()
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		// Setup attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );

		// Update the attachment metadata
		wp_update_attachment_metadata( $attach_id, $attach_data );
	}

	/**
	 * Set the upload directory to a custom directory.
	 *
	 * Places uploads in /wp-content/uploads/vfb/{year}/{month}
	 *
	 * @access public
	 * @param mixed $upload
	 * @return void
	 */
	public function set_upload_dir( $upload ) {
		$dir = 'vfb';

		$upload['subdir'] = "/$dir" . $upload['subdir'];
	    $upload['path']   = $upload['basedir'] . $upload['subdir'];
	    $upload['url']    = $upload['baseurl'] . $upload['subdir'];

	    return apply_filters( 'vfbp_upload_directory', $upload, $this->id );
	}
}