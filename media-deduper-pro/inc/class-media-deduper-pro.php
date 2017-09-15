<?php
/**
 * The main Media Deduper Pro plugin class.
 *
 * @package Media_Deduper_Pro
 */

register_activation_hook( MDD_PRO_FILE, array( 'Media_Deduper_Pro', 'activate' ) );
register_uninstall_hook( MDD_PRO_FILE, array( 'Media_Deduper_Pro', 'uninstall' ) );

/**
 * The main Media Deduper plugin class.
 */
class Media_Deduper_Pro {

	/**
	 * Plugin version.
	 */
	const VERSION = '1.0.1';

	/**
	 * Special hash value used to mark an attachment if its file can't be found.
	 */
	const NOT_FOUND_HASH = 'not-found';

	/**
	 * Default size value used if an attachment post's file can't be found.
	 */
	const NOT_FOUND_SIZE = 0;

	/**
	 * The ID of the admin screen for this plugin.
	 */
	const ADMIN_SCREEN = 'media_page_media-deduper';

	/**
	 * The number of attachments deleted during a 'smart delete' operation.
	 *
	 * @var int Set/incremented by Media_Deduper_Pro::smart_delete_media().
	 */
	protected $smart_deleted_count = 0;

	/**
	 * The number of attachments skipped during a 'smart delete' operation.
	 *
	 * @var int Set/incremented in Media_Deduper_Pro::smart_delete_media().
	 */
	protected $smart_skipped_count = 0;

	/**
	 * When the plugin is activated after being inactive, clear any previously cached transients, and
	 * make sure the `mdd_hash_index` DB index exists.
	 */
	static function activate() {
		static::delete_transients();
		static::db_index( 'add' );
	}

	/**
	 * Main constructor, primarily used for registering hooks.
	 */
	function __construct() {

		// Check version number. If there's a stored version number (meaning the plugin was previously
		// active) and it's lower than the current one, delete transients and show a message indicating
		// that the user should update the index. Note that this uses site options, not just options,
		// because multisite is a thing.
		$prev_version = get_site_option( 'mdd_pro_version', false );
		if ( ! $prev_version || version_compare( static::VERSION, $prev_version ) ) {
			add_option( 'mdd-pro-updated', true );
			update_site_option( 'mdd_pro_version', static::VERSION );

			// Delete transients, in case MDD was previously active and is being
			// re-enabled. Old duplicate counts, etc. are probably no longer accurate.
			static::delete_transients();
		}

		// When the plugin is deactivated, remove the db index.
		register_deactivation_hook( MDD_PRO_FILE, array( $this, 'deactivate' ) );

		// Class for premium plugin activation/deactivation.
		require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-license-manager.php' );
		$this->license_manager = new MDD_License_Manager();

		// Class for attachment reference tracking/replacement.
		require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-reference-handler.php' );
		$this->reference_handler = new MDD_Reference_Handler();

		// Class for processing the index task in the background.
		require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-indexer.php' );
		$this->indexer = new MDD_Indexer();

		// Class for handling outputting the duplicates.
		require_once( MDD_PRO_INCLUDES_DIR . 'class-mdd-media-list-table.php' );

		// Use an existing capabilty to check for privileges. manage_options may not be ideal, but gotta use something...
		$this->capability = apply_filters( 'media_deduper_cap', 'manage_options' );

		add_action( 'wp_ajax_mdd_index_status',   array( $this, 'ajax_index_status' ) );

		add_action( 'admin_menu',                 array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts',      array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices',              array( $this, 'admin_notices' ), 11 );

		// When add_metadata() or update_metadata() is called to set a new or
		// existing attachment's _wp_attached_file value, (re)calculate the
		// attachment's file hash.
		add_action( 'added_post_meta',            array( $this, 'after_add_file_meta' ), 10, 3 );
		add_action( 'update_post_metadata',       array( $this, 'before_update_file_meta' ), 10, 5 );

		// When an attachment is deleted, invalidate the cached list of duplicate
		// IDs, because there may be another attachment that would previously have
		// been considered a duplicate, but is now unique.
		add_action( 'delete_attachment',          array( 'Media_Deduper_Pro', 'delete_transients' ) );

		// When references are tracked in a post, invalidate the cached list of indexed post IDs.
		add_action( 'mdd_tracked_post_props',     array( 'Media_Deduper_Pro', 'delete_transients' ) );
		add_action( 'mdd_tracked_post_meta',      array( 'Media_Deduper_Pro', 'delete_transients' ) );
		add_action( 'mdd_tracked_deleted_post',   array( 'Media_Deduper_Pro', 'delete_transients' ) );

		// If the user tries to upload a file whose hash matches an existing file,
		// stop them.
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'block_duplicate_uploads' ) );

		add_filter( 'set-screen-option',          array( $this, 'save_screen_options' ), 10, 3 );

		// Set removable query args (used for displaying messages to the user).
		add_filter( 'removable_query_args',       array( $this, 'removable_query_args' ) );

		// Column handlers.
		add_filter( 'manage_upload_columns',          array( $this, 'media_columns' ) );
		add_filter( 'manage_upload_sortable_columns', array( $this, 'media_sortable_columns' ) );
		add_filter( 'manage_media_custom_column',     array( $this, 'media_custom_column' ), 10, 2 );

		// Query filters (for adding sorting options in wp-admin).
		if ( is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}
	}

	/**
	 * Enqueue the media js file from core. Also enqueue our own assets.
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		// Enqueue the main media JS + our own JS on the Manage Duplicates screen.
		if ( static::ADMIN_SCREEN === $screen->base ) {
			wp_enqueue_media();
			wp_enqueue_script( 'media-grid' );
			wp_enqueue_script( 'media' );
			wp_enqueue_script( 'media-deduper-js', plugins_url( 'media-deduper.js', MDD_PRO_FILE ), array( 'underscore' ), static::VERSION, true );

			// Add localization strings. If this is the indexer tab, additional data
			// will be added later.
			wp_localize_script( 'media-deduper-js', 'mdd_l10n', array(
				'warning_delete' => __( "Warning: This will modify your files and content!!!!!!! (Lots of exclamation points because it’s seriously that big of a deal.)\n\nWe strongly recommend that you BACK UP YOUR UPLOADS AND DATABASE before performing this operation.\n\nClick 'Cancel' to stop, 'OK' to delete.", 'media-deduper' ),
				'stopping'       => esc_html__( 'Stopping...', 'media-deduper' ),
				'index_errors'   => __( 'Errors:', 'media-deduper' ),
				'index_complete' => array(
					'issues' => '<p>'
						. esc_html__( 'Indexing complete;', 'media-deduper' )
						. ' <strong>'
						// translators: %s: The number of files that we failed to index.
						. esc_html( sprintf( __( '%s files could not be indexed.', 'media-deduper' ), '{NUM}' ) )
						. ' <a href=\'' . esc_url( admin_url( 'upload.php?page=media-deduper' ) ) . '\'>'
						. esc_html__( 'Manage duplicates now.', 'media-deduper' )
						. '</a></strong></p>',
					'perfect' => '<p>' . esc_html__( 'Indexing complete;', 'media-deduper' ) . ' <strong>' . esc_html__( 'All media and posts successfully indexed.', 'media-deduper' ) . '</strong></p>',
					'aborted' => '<p>' . esc_html__( 'Indexing aborted; only some items indexed.', 'media-deduper' ) . '</p>',
				),
			) );
		}

		// Enqueue our admin CSS on both the Manage Duplicates screen and the main
		// Media Library screen. We need it on the latter screen in order to style
		// the custom mdd_size column.
		if ( in_array( $screen->base, array( 'upload', static::ADMIN_SCREEN ), true ) ) {
			wp_enqueue_style( 'media-deduper', plugins_url( 'media-deduper.css', MDD_PRO_FILE ), array(), static::VERSION );
		}

		// Enqueue script to reformat uploader error messages.
		// This one's enqueued unconditionally, since the media frame could be used just about anywhere.
		wp_enqueue_script( 'media-deduper-uploader-js', plugins_url( 'media-deduper-uploader.js', MDD_PRO_FILE ), array(), static::VERSION );
	}

	/**
	 * Remind people they need to do things.
	 */
	public function admin_notices() {

		// If the current user isn't allowed to view the MDD admin screen, bail. None of the messages
		// we'd show here are relevant to users who can't rebuild the index, etc.
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		$screen = get_current_screen();
		$html = '';

		if ( get_option( 'mdd-pro-updated', false ) ) {

			// Update was just performed, not initial activation.
			$html = '<div class="updated notice is-dismissible"><p>';
			$html .= sprintf(
				// translators: %s: Link URL.
				__( 'Thanks for updating Media Deduper Pro. Due to recent enhancements you’ll need to <a href="%s">regenerate the index</a>. Sorry for the inconvenience!', 'media-deduper' ),
				admin_url( 'upload.php?page=media-deduper&tab=index' )
			);
			$html .= '</p></div>';
			delete_option( 'mdd-pro-updated' );

		} elseif ( ! get_option( 'mdd-pro-activated', false ) && $this->get_count( 'indexed' ) < $this->get_count() ) {

			// On initial plugin activation, point to the indexing page.
			add_option( 'mdd-pro-activated', true, '', 'no' );
			$html = '<div class="error notice is-dismissible"><p>';
			$html .= sprintf(
				// translators: %s: Link URL.
				__( 'In order to manage duplicate media you must first <strong><a href="%s">generate the media index</a></strong>.', 'media-deduper' ),
				admin_url( 'upload.php?page=media-deduper&tab=index' )
			);
			$html .= '</p></div>';

		} elseif ( 'upload' === $screen->base && $this->get_count( 'indexed' ) < $this->get_count() ) {

			// Otherwise, complain about incomplete indexing if necessary.
			$html = '<div class="error notice is-dismissible"><p>';
			$html .= sprintf(
				// translators: %s: Link URL.
				__( 'Media duplication index is not comprehensive, please <strong><a href="%s">update the index now</a></strong>.', 'media-deduper' ),
				admin_url( 'upload.php?page=media-deduper&tab=index' )
			);
			$html .= '</p></div>';

		} elseif ( 'dashboard' === $screen->base || static::ADMIN_SCREEN === $screen->base ) {

			// On the dashboard or the Manage Duplicates screen (but NOT the License tab), if there's
			// either no license key stored, or the stored license key isn't valid (i.e. expired or just
			// not real), ask the user to enter a valid one.
			if ( 'dashboard' === $screen->base || ! isset( $_GET['tab'] ) || 'license' !== $_GET['tab'] ) { // WPCS: CSRF ok.

				if ( empty( $this->license_manager->license_key ) ) {

					$html = '<div class="error notice is-dismissible"><p>';
					$html .= sprintf(
						// translators: %s: Link URL.
						__( 'Thank you for using Media Deduper Pro! Please <a href="%s">enter your license key</a> so you can receive updates when we release them.', 'media-deduper' ),
						admin_url( 'upload.php?page=media-deduper&tab=license' )
					);
					$html .= '</p></div>';

				} elseif ( 'valid' !== $this->license_manager->license_status ) {

					$html = '<div class="error notice is-dismissible"><p>';
					$html .= sprintf(
						// translators: %s: Link URL.
						__( 'The license key you have entered for Media Deduper Pro is not valid. Until you <a href="%s">enter a valid license key</a>, you will not be able to receive updates to the plugin.', 'media-deduper' ),
						admin_url( 'upload.php?page=media-deduper&tab=license' )
					);
					$html .= '</p></div>';

				}
			}

			// On the Manage Duplicates page, if a Delete or Smart Delete operation has just been
			// completed, show feedback.
			if ( static::ADMIN_SCREEN === $screen->base && isset( $_GET['smartdeleted'] ) ) {

				// The 'smartdelete' action has been performed. $_GET['smartdelete'] is
				// expected to be a comma-separated pair of values reflecting the number
				// of attachments deleted and the number of attachments that weren't
				// deleted (which happens if all other copies of an image have already
				// been deleted).
				list( $deleted, $skipped ) = array_map( 'absint', explode( ',', $_GET['smartdeleted'] ) );
				// Only output a message if at least one attachment was either deleted
				// or skipped.
				if ( $deleted || $skipped ) {
					$html = '<div class="updated notice is-dismissible"><p>';
					// translators: %1$d: Number of items deleted. %2$d: Number of items skipped.
					$html .= sprintf( __( 'Deleted %1$d items and skipped %2$d items.', 'media-deduper' ), $deleted, $skipped );
					$html .= '</p></div>';
				}
				// Remove the 'smartdeleted' query arg from the REQUEST_URI, since it's
				// served its purpose now and we don't want it weaseling its way into
				// redirect URLs or the like.
				$_SERVER['REQUEST_URI'] = remove_query_arg( 'smartdeleted', $_SERVER['REQUEST_URI'] );

			} elseif ( isset( $_GET['deleted'] ) ) {

				// The 'delete' action has been performed. $_GET['deleted'] is expected
				// to reflect the number of attachments deleted.
				// Only output a message if at least one attachment was deleted.
				$deleted = absint( $_GET['deleted'] );
				if ( $deleted ) {
					// Show a simpler message if only one file was deleted (based on
					// wp-admin/upload.php).
					if ( 1 === $deleted ) {
						$message = __( 'Media file permanently deleted.', 'media-deduper' );
					} else {
						/* translators: %s: number of media files */
						$message = _n( '%s media file permanently deleted.', '%s media files permanently deleted.', $deleted, 'media-deduper' );
					}
					$html = '<div class="updated notice is-dismissible"><p>';
					$html .= sprintf( $message, number_format_i18n( $deleted ) );
					$html .= '</p></div>';
				}
				// Remove the 'deleted' query arg from REQUEST_URI.
				$_SERVER['REQUEST_URI'] = remove_query_arg( 'deleted', $_SERVER['REQUEST_URI'] );

			} // End if().
		} // End if().

		echo $html; // WPCS: XSS ok.
	}

	/**
	 * Adds/removes DB index on meta_value to facilitate performance in finding dupes.
	 *
	 * @param string $task 'add' to add the index, any other value to remove it.
	 */
	static function db_index( $task = 'add' ) {

		global $wpdb;
		if ( 'add' === $task ) {
			$sql = "CREATE INDEX `mdd_hash_index` ON $wpdb->postmeta ( meta_value(32) );";
		} else {
			$sql = "DROP INDEX `mdd_hash_index` ON $wpdb->postmeta;";
		}

		$wpdb->query( $sql );

	}

	/**
	 * On deactivation, get rid of our index.
	 */
	public function deactivate() {

		global $wpdb;

		// Kill our index.
		static::db_index( 'remove' );
	}

	/**
	 * On uninstall, get rid of ALL junk.
	 */
	static function uninstall() {
		global $wpdb;

		// Kill our mdd_hashes and mdd_sizes. It's annoying to re-generate the
		// index but we don't want to pollute the DB.
		$wpdb->delete( $wpdb->postmeta, array(
			'meta_key' => 'mdd_hash',
		) );
		$wpdb->delete( $wpdb->postmeta, array(
			'meta_key' => 'mdd_size',
		) );

		// Kill our mysql table index.
		static::db_index( 'remove' );

		// Remove the option indicating activation.
		delete_option( 'mdd-pro-activated' );
	}

	/**
	 * Prevents duplicates from being uploaded.
	 *
	 * @param array $file An array of data for a single file, as passed to
	 *                    _wp_handle_upload().
	 */
	function block_duplicate_uploads( $file ) {

		global $wpdb;

		$upload_hash = md5_file( $file['tmp_name'] );

		// Does our hash match?
		$sql = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'mdd_hash' AND meta_value = %s LIMIT 1;", $upload_hash );
		$matches = $wpdb->get_var( $sql );
		if ( $matches ) {
			$file['error'] = sprintf(
				// translators: %s: The title of the preexisting attachment post.
				__( 'It appears this file is already present in your media library: %s', 'media-deduper' ),
				'#' . $matches . ' (' . get_the_title( $matches ) . ') [' . esc_url( get_edit_post_link( $matches ) ) . ']'
			);
		}
		return $file;
	}

	/**
	 * When add_post_meta() is called to set an attachment post's initial
	 * _wp_attached_file meta value, calculate the attachment's hash.
	 *
	 * @param int    $meta_id    The ID of the meta value in the postmeta table.
	 *                           Passed in by update_post_meta(), ignored here.
	 * @param int    $post_id    The ID of the post whose meta value has changed.
	 * @param string $meta_key   The meta key whose value has changed.
	 */
	function after_add_file_meta( $meta_id, $post_id, $meta_key ) {

		// If the meta key that was updated isn't _wp_attached_file, bail.
		if ( '_wp_attached_file' !== $meta_key ) {
			return;
		}

		// If this isn't an attachment post, bail.
		if ( 'attachment' !== get_post_field( 'post_type', $post_id, 'raw' ) ) {
			return;
		}

		// Calculate and save the file hash.
		$this->calc_media_meta( $post_id );
	}

	/**
	 * When update_post_meta() is called to set an attachment post's
	 * _wp_attached_file meta value, recalculate the attachment's hash.
	 *
	 * Note: the Enable Media Replace plugin uses a direct db query to set
	 * _wp_attached_file before calling update_attached_file(), so when a file is
	 * changed using EMR, the "new" meta value passed here may be the same as the
	 * old one, and updated_post_meta won't fire because the values are the same.
	 * That's why this function hooks into update_post_metadata, which _always_
	 * fires, instead of updated_post_meta.
	 *
	 * If the new value for the meta key is the same as the old value, this
	 * function will recalculate the attachment hash immediately; if the new value
	 * is different from the old one, this function will attach another hook that
	 * will recalculate the hash _after_ the new meta value has been saved.
	 *
	 * @uses Media_Deduper_Pro::after_update_file_meta()
	 *
	 * @param null|bool $check      Whether to allow updating metadata. Passed in
	 *                              by the update_post_metadata hook, but ignored
	 *                              here -- we don't want to change whether meta
	 *                              is saved, we just want to know if it changes.
	 * @param int       $post_id    Object ID.
	 * @param string    $meta_key   Meta key.
	 * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
	 * @param mixed     $prev_value Optional. If specified, only update existing
	 *                              metadata entries with the specified value.
	 *                              Otherwise, update all entries.
	 */
	function before_update_file_meta( $check, $post_id, $meta_key, $meta_value, $prev_value ) {

		// If the meta key that was updated isn't _wp_attached_file, bail.
		if ( '_wp_attached_file' !== $meta_key ) {
			return $check;
		}

		// If this isn't an attachment post, bail.
		if ( 'attachment' !== get_post_field( 'post_type', $post_id, 'raw' ) ) {
			return $check;
		}

		// Compare existing value to new value. See update_metadata() in
		// wp-includes/meta.php. If the old value and the new value are the same,
		// then the updated_post_meta action won't fire. The Enable Media Replace
		// plugin might have changed the actual contents of the file, though, even
		// if the filename/path hasn't changed, so now is our chance to update the
		// image hash and size.
		if ( empty( $prev_value ) ) {
			$old_value = get_post_meta( $post_id, $meta_key );
			if ( 1 === count( $old_value ) ) {
				if ( $old_value[0] === $meta_value ) {
					// Recalculate and save the file hash.
					$this->calc_media_meta( $post_id );
					// Leave $check as is to avoid affecting whether or not meta is saved.
					return $check;
				}
			}
		}

		// If the old and new meta values are NOT identical, wait until the metadata
		// is actually saved, and _then_ recalculate the hash.
		add_action( 'updated_post_meta', array( $this, 'after_update_file_meta' ), 10, 3 );

		// Leave $check as is to avoid affecting whether or not meta is saved.
		return $check;
	}

	/**
	 * Calculate the hash for a new attachment post or one whose attached file has
	 * changed.
	 *
	 * @param int    $meta_id    The ID of the meta value in the postmeta table.
	 *                           Passed in by update_post_meta(), ignored here.
	 * @param int    $post_id    The ID of the post whose meta value has changed.
	 * @param string $meta_key   The meta key whose value has changed.
	 */
	function after_update_file_meta( $meta_id, $post_id, $meta_key ) {

		// If the meta key that was updated isn't _wp_attached_file, bail.
		if ( '_wp_attached_file' !== $meta_key ) {
			return;
		}

		// If this isn't an attachment post, bail.
		if ( 'attachment' !== get_post_field( 'post_type', $post_id, 'raw' ) ) {
			return;
		}

		// Calculate the hash for this attachment.
		$this->calc_media_meta( $post_id );

		// Unhook this function from update_post_meta, so it doesn't keep firing for
		// future metadata changes. $this->before_update_meta() will add this
		// function back as needed.
		remove_action( 'updated_post_meta', array( $this, 'after_update_file_meta' ), 10 );
	}

	/**
	 * Calculate the hash for a just-uploaded file.
	 *
	 * @param int $post_id The ID of the attachment post to calculate meta for.
	 * @return array {
	 *     @type bool   $success Whether the hash & size could be calculated correctly.
	 *     @type string $message Human-readable info about what happened.
	 * }
	 */
	function calc_media_meta( $post_id ) {
		$mediafile = get_attached_file( $post_id );

		// If the file doesn't exist, save special "not found" hash + size.
		if ( false === $mediafile || ! file_exists( $mediafile ) ) {
			$this->save_media_meta( $post_id, self::NOT_FOUND_HASH );
			$this->save_media_meta( $post_id, self::NOT_FOUND_SIZE, 'mdd_size' );

			// Delete cached counts.
			static::delete_transients();

			// Return an error message for logging.
			return array(
				'success' => false,
				'message' => sprintf(
					// translators: %s: Attachment title (links to the Edit Attachment screen).
					__( 'Attachment file for %s could not be found.', 'media-deduper' ),
					'<a href="' . esc_url( get_edit_post_link( $post_id ) ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a>'
				),
			);
		}

		// Calculate and save hash and size.
		$hash = $this->calculate_hash( $mediafile );
		$size = $this->calculate_size( $mediafile );
		$this->save_media_meta( $post_id, $hash );
		$this->save_media_meta( $post_id, $size, 'mdd_size' );

		// Delete transients, most importantly the attachment count (but duplicate
		// IDs and shared file IDs may have been affected too, if this post was
		// copied meta-value-for-meta-value from another post).
		static::delete_transients();

		// If hash and size were saved, return a success message.
		return array(
			'success' => true,
			'message' => sprintf(
				// translators: %s: Attachment title.
				__( 'Hash and size for %s saved.', 'media-deduper' ),
				esc_html( get_the_title( $post_id ) )
			),
		);
	}

	/**
	 * Detect and store attachment references in a post.
	 *
	 * @param int $post_id The ID of the post to track references for.
	 * @return array {
	 *     @type bool   $success Whether the hash & size could be calculated correctly.
	 *     @type string $message Human-readable info about what happened.
	 * }
	 */
	function track_media_refs( $post_id ) {

		$post = get_post( $post_id );

		// If $post doesn't exist, throw an error.
		if ( ! $post ) {
			return array(
				'success' => false,
				'message' => sprintf(
					// translators: %d: Post ID.
					__( 'Post %d could not be found.', 'media-deduper' ),
					$post_id
				),
			);
		}

		$this->reference_handler->track_post( $post_id );

		// Delete transients, most importantly the duplicate ID list + count of
		// indexed attachments.
		static::delete_transients();

		return array(
			'success' => true,
			'message' => sprintf(
				// translators: %1$d: Post ID. %2$s: Post title.
				__( 'References in post %1$d (%2$s) tracked.', 'media-deduper' ),
				$post->ID,
				esc_html( $post->post_title )
			),
		);
	}

	/**
	 * Get indexer status data.
	 */
	function ajax_index_status() {
		$status = $this->indexer->get_status();
		wp_send_json( $status );
	}

	/**
	 * Calculate the size for a given file.
	 *
	 * @param string $file The path to the file for which to calculate size.
	 */
	private function calculate_size( $file ) {
		return filesize( $file );
	}

	/**
	 * Calculate the MD5 hash for a given file.
	 *
	 * @param string $file The path to the file for which to to calculate a hash.
	 */
	private function calculate_hash( $file ) {
		return md5_file( $file );
	}

	/**
	 * Save metadata for an attachment.
	 *
	 * @param int    $post_id  The ID of the post for which to save metadata.
	 * @param any    $value    The meta value to save.
	 * @param string $meta_key The meta key under which to save the value.
	 */
	private function save_media_meta( $post_id, $value, $meta_key = 'mdd_hash' ) {
		return update_post_meta( $post_id, $meta_key, $value );
	}

	/**
	 * Return either the total # of attachments, or the # of indexed attachments.
	 *
	 * @param string $type The type of count to return. Use 'all' to count all
	 *                     attachments, or 'indexed' to count only attachments
	 *                     whose hash and size have already been calculated.
	 *                     Default 'all'.
	 */
	private function get_count( $type = 'all' ) {

		global $wpdb;

		// Get all trackable post type slugs.
		$escaped_post_types = $this->get_post_types_sql();

		switch ( $type ) {
			case 'all':

				$sql = "SELECT COUNT(*) FROM $wpdb->posts
					WHERE post_type = 'attachment'
						OR post_type IN ( $escaped_post_types );";
				break;

			case 'indexed':
			default:

				$sql = "SELECT COUNT(*) FROM $wpdb->posts p
					LEFT JOIN $wpdb->postmeta ph
						ON p.ID = ph.post_id AND ph.meta_key = 'mdd_hash'
					LEFT JOIN $wpdb->postmeta ps
						ON p.ID = ps.post_id AND ps.meta_key = 'mdd_size'
					LEFT JOIN $wpdb->postmeta pr
						ON p.ID = pr.post_id AND pr.meta_key = '_mdd_references'
					WHERE (
						p.post_type = 'attachment'
						AND ph.meta_id IS NOT NULL
						AND ps.meta_id IS NOT NULL
					) OR (
						p.post_type IN ( $escaped_post_types )
						AND pr.meta_id IS NOT NULL
					)
				";
		}

		$result = get_transient( 'mdd_count_' . $type );

		// Because a prior version of MDD Pro had a very sad bug that caused DB errors when calculating
		// the indexed count, we may have a stored transient that's just an empty string. If that
		// happens, recalculate the count.
		if ( false === $result || ! is_numeric( $result ) ) {
			$result = $wpdb->get_var( $sql );
			set_transient( 'mdd_count_' . $type, $result, HOUR_IN_SECONDS );
		}
		return $result;

	}

	/**
	 * Add to admin menu.
	 */
	function add_admin_menu() {
		$this->hook = add_media_page( __( 'Manage Duplicates', 'media-deduper' ), __( 'Manage Duplicates', 'media-deduper' ), $this->capability, 'media-deduper', array( $this, 'admin_screen' ) );

		add_action( 'load-' . $this->hook, array( $this, 'screen_tabs' ) );
	}

	/**
	 * Implements screen options.
	 */
	function screen_tabs() {

		$option = 'per_page';
		$args = array(
			'label'   => 'Items',
			'default' => get_option( 'posts_per_page', 20 ),
			'option'  => 'mdd_per_page',
		);
		add_screen_option( $option, $args );

		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
			'<p>' . __( 'Media Deduper Pro was built to help you find and eliminate duplicate images and attachments from your WordPress media library.' )
				. '</p><p>' . __( 'Before Media Deduper Pro can identify duplicate assets, it first must build an index of all the files in your media library.' )
				. '</p><p>' . __( 'Once its index is complete, Media Deduper will also prevent users from uploading duplicates of files already present in your media library.' )
				. '</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'indexing',
			'title'   => __( 'Indexing' ),
			'content' =>
			'<p>' . __( 'Media Deduper needs to generate an index of your media files in order to determine which files match, and an index of which posts reference which media files in order to swap out duplicate items with their originals. When indexing media, it only looks at the files themselves, not any data in WordPress (such as title, caption or comments). Once the initial index is built, Media Deduper automatically adds new uploads to its index and detects references to attachments in posts as they are created or edited, so you shouldn’t have to generate the index again.' )
				. '</p><p>' . __( 'As a part of the indexing process, Media Deduper also stores information about each file’s size so duplicates can be sorted by disk space used, allow you to most efficiently perform cleanup.' )
				. '</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'deletion',
			'title'   => __( 'Deletion' ),
			'content' =>
			'<p>' . __( 'Once Media Deduper has indexed your files and found duplicates, you can easily delete them in one of two ways:' )
				. '</p><p>' . __( 'Option 1: Smart Delete. This option preserves references to images in post content, post excerpts, and certain post metadata fields (see the <a href="https://cornershop-creative.groovehq.com/knowledge_base/topics/using-media-deduper-pro">online documentation</a> for more information). Smart Delete replaces references to duplicate images with references to a single instance of the image, and only deletes orphaned copies of that image. Smart Delete will refuse to delete the last remaining copy of an item: even if you select all copies of an image, and none of them are used anywhere on the site, Smart Delete will leave one copy of the image in your library. In this sense, Smart Delete is safer than Delete Permanently. <em><strong>Please note:</strong></em> Although this option preserves featured images, post body content and excerpts, and a growing list of post meta fields, it does not currently replace attachment references in user meta, widgets, or site/network options, and it is not reversible. Please be careful.' )
				. '</p><p>' . __( 'Option 2: Delete Permanently. This option <em>permanently</em> deletes whichever files you select. This can be <em>very dangerous</em> as it cannot be undone, and you may inadvertently delete all versions of a file, regardless of how they are being used on the site.' )
				. '</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'shared',
			'title'   => __( 'Shared Files' ),
			'content' =>
			'<p>' . __( 'In a typical WordPress installation, each different Media "post" relates to a separate file uploaded to the filesystem. However, some plugins facilitate copying media posts in a way that produces multiple posts all referencing a single file.' )
				. '</p><p>' . __( 'Media Deduper considers such posts to be "duplicates" because they share the same image data. However, in most cases you would not want to actually delete any of these posts because deleting any one of them would remove the media file they all share.' )
				. '</p><p>' . __( 'Because this can lead to unintentional data loss, Media Deduper prefers to suppress showing duplicates that share a file. However, it is possible to show these media items if you wish to review or delete them. <strong>Be extremely cautious</strong> when working with duplicates that share files as unintentional data loss can easily occur.' )
				. '</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'about',
			'title'   => __( 'About' ),
			'content' =>
			'<p>' . __( 'Media Deduper was built by Cornershop Creative, on the web at <a href="https://cornershopcreative.com">https://cornershopcreative.com</a>' )
				. '</p><p>' . __( 'Need support? Got a feature idea? Contact us at <a href="mailto:support@cornershopcreative.com">support@cornershopcreative.com</a>, or check out our <a href="https://cornershop-creative.groovehq.com/knowledge_base/categories/media-deduper">knowledge base</a>.' )
				. '</p>',
		) );

		$this->get_duplicate_ids();

		// We use $wp_query (the main query) since Media_List_Table does and we extend that.
		global $wp_query;
		$query_parameters = array_merge(
			// Defaults that $_GET can override.
			array(
				'orderby'        => array(
					'mdd_size'  => 'desc',
					'post_date' => 'desc',
				),
			),
			// Query args (most of the time these will only affect sort order).
			$_GET,
			// Hard settings that should override anything in $_GET.
			array(
				'post__in'       => $this->duplicate_ids,
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => get_user_option( 'mdd_per_page' ),
			)
		);

		// If suppressing shared files (the default), do that.
		if ( ! isset( $_GET['show_shared'] ) || 1 !== absint( $_GET['show_shared'] ) ) {
			$this->get_shared_filename_ids();
			$query_parameters['post__in'] = array_diff( $this->duplicate_ids, $this->shared_filename_ids );
			if ( ! count( $query_parameters['post__in'] ) ) {
				// We do this otherwise WP_Query's post__in gets an empty array and
				// returns all posts.
				$query_parameters['post__in'] = array( '0' );
			}
		}

		$wp_query = new WP_Query( $query_parameters );

		$this->list_table = new MDD_Pro_Media_List_Table( array(
			// Even though this is really the 'media_page_media-deduper' screen,
			// we want to show the columns that would normally be shown on the
			// 'upload' screen, including taxonomy terms or any other columns
			// that other plugins might be adding.
			'screen' => 'upload',
		) );

		// Handle bulk actions, if any.
		$this->handle_bulk_actions();

		// If we got here via a form submission, but there was no bulk action to apply, then the user
		// probably just changed the 'Hide duplicates that share files' setting. Redirect to a slightly
		// cleaner URL: remove the _wp_http_referer and _wpnonce args. wp-admin/upload.php does this.
		if ( ! empty( $_GET['_wp_http_referer'] ) && ! empty( $_GET['filter_action'] ) ) {
			$redirect_url = add_query_arg( array(
				'show_shared' => absint( $_GET['show_shared'] ),
			), admin_url( 'upload.php?page=media-deduper' ) );
			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Allow the `mdd_per_page` screen option to be saved.
	 *
	 * @param bool|int $status Screen option value. Default false to skip.
	 * @param string   $option The option name.
	 * @param int      $value  The number of rows to use.
	 */
	function save_screen_options( $status, $option, $value ) {
		if ( 'mdd_per_page' === $option ) {
			return $value;
		}
	}

	/**
	 * The main admin screen!
	 */
	function admin_screen() {

		// Get the currently active tab.
		$active_tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'duplicates' );
		if ( ! in_array( $active_tab, array( 'duplicates', 'index', 'license' ), true ) ) {
			$active_tab = 'duplicates';
		}

		?>
		<div id="mdd-message" class="updated fade" style="display:none"></div>
		<div class="wrap deduper">
			<h1><?php esc_html_e( 'Media Deduper Pro', 'media-deduper' ); ?></h1>
			<aside class="mdd-column-2">
				<div class="mdd-box">
					<h2>Like Media Deduper?</h2>
					<ul>
						<li class="share"><a href="#" data-service="facebook">Share it on Facebook »</a></li>
						<li class="share"><a href="#" data-service="twitter">Tweet it »</a></li>
						<li><a href="https://wordpress.org/support/plugin/media-deduper/reviews/#new-post" target="_blank">Review it on WordPress.org »</a></li>
					</ul>
				</div>
			</aside>
			<div class="mdd-column-1">
				<div class="nav-tab-wrapper">
					<a href="<?php echo esc_url( admin_url( 'upload.php?page=media-deduper&tab=duplicates' ) ); ?>" class="nav-tab<?php echo ( 'duplicates' === $active_tab ? ' nav-tab-active' : '' ); ?>">Duplicates</a>
					<a href="<?php echo esc_url( admin_url( 'upload.php?page=media-deduper&tab=index' ) ); ?>" class="nav-tab<?php echo ( 'index' === $active_tab ? ' nav-tab-active' : '' ); ?>">Index</a>
					<a href="<?php echo esc_url( admin_url( 'upload.php?page=media-deduper&tab=license' ) ); ?>" class="nav-tab<?php echo ( 'license' === $active_tab ? ' nav-tab-active' : '' ); ?>">License Key</a>
				</div>

		<?php
		if ( 'index' === $active_tab ) : ?>

				<h2><?php esc_html_e( 'Index of Duplicate Media', 'media-deduper' ); ?></h2>

				<?php
				// Display the index screen.
				$this->show_index_screen();
				?>

			</div><!-- .mdd-column-1 -->

		<?php elseif ( 'license' === $active_tab ) : ?>

				<h2><?php esc_html_e( 'License Key', 'media-deduper' ); ?></h2>

				<?php
				// Display the license key form.
				$this->license_manager->license_form();
				?>

			</div><!-- .mdd-column-1 -->

		<?php else : ?>

				<p><?php esc_html_e( 'Use this tool to identify duplicate media files in your site. It only looks at the files themselves, not any data in WordPress (such as title, caption or comments).', 'media-deduper' ); ?></p>
				<p><?php esc_html_e( 'In order to identify duplicate files, an index of all media must first be generated.', 'media-deduper' ); ?></p>

				<?php $this->show_index_button(); ?>

			</div><!-- .mdd-column-1 -->

			<!-- the posts table -->
			<h2 style="clear:both;"><?php esc_html_e( 'Duplicate Media Files', 'media-deduper' ); ?></h2>
			<form id="posts-filter" method="get">
				<?php
				// Set the `page` query param when processing actions. This ensures that
				// $this->handle_bulk_actions() will run, which will process the bulk action and redirect
				// the user. Otherwise, it would fall to wp-admin/upload.php to process bulk actions, and
				// upload.php doesn't know how to smartdelete.
				?>
				<input type="hidden" name="page" value="media-deduper">
				<div class="wp-filter">
					<div class="view-switch">
						<select name="show_shared">
							<option value="0" <?php selected( ! isset( $_GET['show_shared'] ) || ( '0' === $_GET['show_shared'] ) ); ?>><?php esc_html_e( 'Hide duplicates that share files', 'media-deduper' ); ?></option>
							<option value="1" <?php selected( isset( $_GET['show_shared'] ) && ( '1' === $_GET['show_shared'] ) ); ?>><?php esc_html_e( 'Show duplicates that share files', 'media-deduper' ); ?></option>
						</select>
						<input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php esc_attr_e( 'Apply', 'media-deduper' ); ?>">
					</div>
					<a href="javascript:void(0);" id="shared-help"><?php esc_html_e( 'What\'s this?', 'media-deduper' ); ?></a>
				</div>
				<?php

				$this->list_table->prepare_items();
				$this->list_table->display();

				// This stuff makes the 'Attach' dialog work.
				wp_nonce_field( 'find-posts', '_ajax_nonce', false );
				?><input type="hidden" id="find-posts-input" name="ps" value="" /><div id="ajax-response"></div>
				<?php find_posts_div(); ?>
			</form>
		<?php endif; ?>

		</div><!-- .wrap -->
		<?php
	}


	/**
	 * Output the indexing progress page.
	 */
	private function show_index_screen() {

		// If the indexer isn't running...
		if ( ! $this->indexer->is_indexing() ) {

			// If the user didn't get here by clicking the Index Media button, then show the button,
			// explanatory text, and any errors from the last time the index was run.
			if ( empty( $_POST['mdd-build-index'] ) && empty( $_POST['mdd-build-index-clean'] ) ) {
				$this->show_index_button( true );
				return;
			}

			// Form nonce check.
			check_admin_referer( 'media-deduper-index' );

			// Check whether _all_ posts should be indexed ($clean === true), or only un-indexed ones.
			$clean = ( ! empty( $_POST['mdd-build-index-clean'] ) );

			// Get unhashed attachment IDs.
			$attachments = $this->get_attachment_ids( ! $clean );
			// Get untracked post IDs.
			$posts = $this->get_post_ids();
			// Get total number of items to process.
			$total_count = count( $attachments ) + count( $posts );

			if ( $total_count < 1 ) {

				if ( $clean ) {
					echo '<p>' . esc_html__( 'There are no indexable posts or attachments on this site! As you begin adding content, Media Deduper Pro will index the new content in the background.', 'media-deduper' ) . '</p>';
				} else {
					echo '<p>' . esc_html__( 'There are no unindexed items. Would you like to completely rebuild the index?', 'media-deduper' ) . '</p>';
					$this->show_index_button( true, false );
				}

				return;
			}

			// Add unhashed attachment IDs.
			foreach ( $attachments as $attachment_id ) {
				$this->indexer->push_to_queue( $attachment_id );
			}

			// Add untracked post IDs.
			foreach ( $posts as $post_id ) {
				$this->indexer->push_to_queue( $post_id );
			}

			// Kick off the indexer process.
			$this->indexer->index();
		}

		?>
		<p><?php esc_html_e( 'Please be patient while the index is generated. This can take a while if your server is slow or if you have many large media files. Once the indexing process is underway, it will continue on its own – feel free to close this window or navigate away from this page. You can return later to check on the indexing process.', 'media-deduper' ); ?></p>

		<noscript><p><em><?php esc_html_e( 'You must enable Javascript in order to proceed!', 'media-deduper' ) ?></em></p></noscript>

		<div id="mdd-bar" style="visibility: hidden;">
			<div id="mdd-meter"></div>
			<div id="mdd-bar-percent"></div>
		</div>

		<p>
			<a class="button" id="mdd-manage" href="<?php echo esc_url( admin_url( 'upload.php?page=media-deduper' ) ); ?>"><?php esc_attr_e( 'Manage Duplicates Now', 'media-deduper' ) ?></a>
		</p>

		<div class="error-files">
			<ul></ul>
		</div>

		<?php

		wp_localize_script( 'media-deduper-js', 'mdd_indexer_status', $this->indexer->get_status() );
	}

	/**
	 * Output the (Re-)Index Media button and preceding text.
	 *
	 * @param bool $always    Set to TRUE if the button should be displayed even if there are no
	 *                        un-indexed attachments. Default FALSE.
	 * @param bool $show_info Set to FALSE if only the button should be shown. Otherwise, contextual
	 *                        information will be added before and/or after the button, detailing the
	 *                        number of unindexed items, etc.
	 */
	private function show_index_button( $always = false, $show_info = true ) {

		$index_incomplete = ( $this->get_count( 'indexed' ) < $this->get_count() );

		if ( $index_incomplete ) {

			if ( $show_info ) {
				// If the index isn't comprehensive, show how many attachments have been indexed.
				?>
				<p>
					<?php echo esc_html( sprintf(
						// translators: %1$d: Number of attachment posts indexed. %2$d: Total number of attachment posts.
						__( 'Looks like %1$d of %2$d media items have been indexed.', 'media-deduper' ),
						$this->get_count( 'indexed' ),
						$this->get_count()
					) ); ?>
					<strong><?php esc_html_e( 'Please index all media now.', 'media-deduper' ); ?></strong>
				</p>
				<?php
			}

			$button_text = __( 'Index Media', 'media-deduper' );
			$button_name = 'mdd-build-index';

		} else {

			if ( $show_info ) {
				// If the index IS comprehensive, say so.
				?>
				<p><?php esc_html_e( 'All media have been indexed.', 'media-deduper' ); ?></p>
				<?php
			}

			$button_text = __( 'Re-Index Media', 'media-deduper' );
			$button_name = 'mdd-build-index-clean';

		}

		if ( $always && $show_info ) {

			// Get and display errors from the last indexer run, if any.
			$last_index_status = $this->indexer->get_status();
			$errors = wp_list_filter( $last_index_status['messages'], array(
				'success' => false,
			) );

			if ( ! empty( $errors ) ) {

				echo '<h4>' . esc_html( sprintf(
					// translators: %d: The number of errors.
					__( 'The last indexer process resulted in %d errors:', 'media-deduper' ),
					count( $errors )
				) ) . '</h4>';

				?>
				<div class="error-files">
					<ul>
						<?php foreach ( $errors as $error ) { ?>
							<li><?php echo wp_kses( $error['message'], array(
								'a' => array(
									'href' => array(),
									'title' => array(),
								),
							) ); ?></li>
						<?php } ?>
					</ul>
				</div>
				<?php
			}
		}

		// Show the button, if ether the index isn't comprehensive or we were asked to always show it.
		if ( $always || $index_incomplete ) {
			?>
			<form method="post" action="<?php echo esc_url( admin_url( 'upload.php?page=media-deduper&tab=index' ) ); ?>">
				<?php wp_nonce_field( 'media-deduper-index' ); ?>
				<p><input type="submit" class="button hide-if-no-js" name="<?php echo esc_attr( $button_name ); ?>" value="<?php echo esc_attr( $button_text ); ?>" /></p>
				<noscript><p><em><?php esc_html_e( 'You must enable Javascript in order to proceed!', 'media-deduper' ) ?></em></p></noscript>
			</form><br>
			<?php
		}
	}

	/**
	 * Retrieves a list of attachment posts that haven't yet had their file md5 hashes computed.
	 *
	 * @param bool $unhashed_only TRUE to return the IDs of attachments whose hash has not been
	 *                            calculated. FALSE to return all attachment IDs.
	 */
	private function get_attachment_ids( $unhashed_only = true ) {

		global $wpdb;

		$sql = "
			SELECT ID FROM $wpdb->posts p
			WHERE p.post_type = 'attachment'
			";

		if ( $unhashed_only ) {
			$sql .= "
				AND ( NOT EXISTS (
					SELECT * FROM $wpdb->postmeta pm
					WHERE pm.meta_key = 'mdd_hash'
					AND pm.post_id = p.ID
				) OR NOT EXISTS (
					SELECT * FROM $wpdb->postmeta pm2
					WHERE pm2.meta_key = 'mdd_size'
					AND pm2.post_id = p.ID
				) )
				";
		}

		$sql .= ';';

		return $wpdb->get_col( $sql );
	}

	/**
	 * Retrieves the IDs of all posts that should be indexed.
	 *
	 * Note that unlike get_attachment_ids(), this doesn't provide a way to only get the IDs of
	 * un-indexed posts. That's because it's better to err on the side of caution, and re-index ALL
	 * posts when the user triggers the indexer manually. Usually the user will only rebuild the index
	 * if they've just reactivated the plugin after leaving it inactive for a while, and unless we
	 * reindex everything, we won't catch any changes that were made to post content while the plugin
	 * was inactive.
	 */
	private function get_post_ids() {

		global $wpdb;

		// Get all trackable post type slugs.
		$escaped_post_types = $this->get_post_types_sql();

		$sql = "SELECT ID FROM $wpdb->posts p
			WHERE p.post_type IN ( $escaped_post_types );";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Return a comma-separated list of quoted post type slugs for use in a SQL IN (...) clause.
	 */
	private function get_post_types_sql() {

		// Get all trackable post types.
		$post_types = $this->reference_handler->get_post_types();

		// Build a comma-separated list of escaped, quoted post type slugs.
		$escaped_post_types = "'" . join( "', '", array_map( 'esc_sql', $post_types ) ) . "'";

		return $escaped_post_types;
	}

	/**
	 * Retrieves an array of post ids that have duplicate hashes.
	 */
	private function get_duplicate_ids() {

		global $wpdb;

		$duplicate_ids = get_transient( 'mdd_duplicate_ids' );

		if ( false === $duplicate_ids ) {
			$sql = "SELECT DISTINCT p.post_id
				FROM $wpdb->postmeta AS p
				JOIN (
					SELECT count(*) AS dupe_count, meta_value
					FROM $wpdb->postmeta
					WHERE meta_key = 'mdd_hash'
					AND meta_value != '" . self::NOT_FOUND_HASH . "'
					GROUP BY meta_value
					HAVING dupe_count > 1
				) AS p2
				ON p.meta_value = p2.meta_value;";

			$duplicate_ids = $wpdb->get_col( $sql );
			// If we don't do this, WP_Query's post__in gets an empty array and
			// returns all posts.
			if ( ! count( $duplicate_ids ) ) {
				$duplicate_ids = array( '0' );
			}
			set_transient( 'mdd_duplicate_ids', $duplicate_ids, HOUR_IN_SECONDS );
		}

		$this->duplicate_ids = $duplicate_ids;
		return $this->duplicate_ids;

	}

	/**
	 * Retrieves an array of post ids that have duplicate filenames/paths.
	 */
	private function get_shared_filename_ids() {

		global $wpdb;

		$sharedfile_ids = get_transient( 'mdd_sharedfile_ids' );

		if ( false === $sharedfile_ids ) {
			$sql = "SELECT DISTINCT p.post_id
				FROM $wpdb->postmeta AS p
				JOIN (
					SELECT count(*) AS sharedfile_count, meta_value
					FROM $wpdb->postmeta
					WHERE meta_key = '_wp_attached_file'
					GROUP BY meta_value
					HAVING sharedfile_count > 1
				) AS p2
				ON p.meta_value = p2.meta_value;";

			$sharedfile_ids = $wpdb->get_col( $sql );
			// If we don't do this, WP_Query's post__in gets an empty array and
			// returns all posts.
			if ( ! count( $sharedfile_ids ) ) {
				$sharedfile_ids = array( '0' );
			}
			set_transient( 'mdd_sharedfile_ids', $sharedfile_ids, HOUR_IN_SECONDS );
		}

		$this->shared_filename_ids = $sharedfile_ids;
		return $this->shared_filename_ids;

	}

	/**
	 * Clears out cached IDs and counts.
	 */
	static function delete_transients() {
		delete_transient( 'mdd_duplicate_ids' ); // Attachments that share hashes.
		delete_transient( 'mdd_sharedfile_ids' ); // Attachments that share files.
		delete_transient( 'mdd_count_all' ); // All attachments, period.
		delete_transient( 'mdd_count_indexed' ); // All attachments with known hashes and sizes.
	}

	/**
	 * Process a bulk action performed on the media table.
	 */
	public function handle_bulk_actions() {

		// Get the current action.
		$doaction = $this->list_table->current_action();

		// If the current action is neither 'smartdelete' nor 'delete', ignore it.
		if ( 'smartdelete' !== $doaction && 'delete' !== $doaction ) {
			return;
		}

		// Check nonce field. The type of request will determine which nonce field needs to be checked.
		if ( isset( $_REQUEST['post'] ) ) {

			// If the 'post' request variable is present, then this is a request to delete a single item.
			// Sanitize the post ID to operate on.
			$post_id = intval( $_REQUEST['post'] );

			// Check nonce field. This field is automatically generated for each "Delete Permanently" link
			// by WP_Media_List_Table.
			check_admin_referer( 'delete-post_' . $post_id );

			// Store the post ID in an array, so we can use the same foreach() loop we'd use if we were
			// performing a bulk action.
			$post_ids = array( $post_id );

		} else {

			// If the 'post' query var is absent, then this must be a bulk action request.
			// Check nonce field. This field is automatically generated for the Bulk Actions menu by
			// WP_Media_List_Table.
			check_admin_referer( 'bulk-media' );

			// Sanitize the list of post IDs to operate on.
			$post_ids = array_map( 'intval', $_REQUEST['media'] );
		}

		// Redirect to the Media Deduper page by default.
		$redirect_url = add_query_arg( array(
			'page' => 'media-deduper',
		), 'upload.php' );

		switch ( $doaction ) {
			case 'smartdelete':

				// Loop over the array of record IDs and delete them.
				foreach ( $post_ids as $id ) {
					self::smart_delete_media( $id );
				}

				// Add query args that will cause Media_Deduper_Pro::admin_notices() to
				// show messages.
				$redirect_url = add_query_arg( array(
					'page' => 'media-deduper',
					'smartdeleted' => $this->smart_deleted_count . ',' . $this->smart_skipped_count,
				), $redirect_url );

				break;

			case 'delete':

				$deleted_count = 0;

				// Handle normal delete action.
				foreach ( $post_ids as $id ) {
					if ( wp_delete_post( $id ) ) {
						$deleted_count++;
					}
				}

				// Add query args that will cause Media_Deduper_Pro::admin_notices() to
				// show messages.
				$redirect_url = add_query_arg( array(
					'page' => 'media-deduper',
					'deleted' => $deleted_count,
				), $redirect_url );

				break;

			default:
				// Ignore any other actions.
				break;
		} // End switch().

		// Redirect to the redirect URL set above.
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Declare the 'smartdeleted' query arg to be 'removable'.
	 *
	 * This causes users who visit upload.php?page=media-deduper&smartdeleted=1,0
	 * (which is where you're sent after 'smart-deleting' images) to only see
	 * upload.phpp?page=media-deduper in their URL bar.
	 *
	 * @param array $args An array of removable query args.
	 */
	public function removable_query_args( $args ) {
		$args[] = 'smartdeleted';
		return $args;
	}

	/**
	 * 'Smart-delete' an attachment post: delete only duplicate attachments, and replace references to
	 * deleted attachments.
	 *
	 * If there are no duplicates of the given attachment, this function will do nothing. If there
	 * are duplicates, then this function will check for references to the attachment and replace them
	 * with references to an older duplicate, and then delete the attachment.
	 *
	 * @param int $id The ID of the post to (maybe) delete.
	 */
	protected function smart_delete_media( $id ) {

		// Check whether there are other copies of this image.
		$this_post_hash = get_post_meta( $id, 'mdd_hash', true );
		if ( ! $this_post_hash ) {
			die( 'Something has gone horribly awry' );
		}
		$duplicate_media = new WP_Query( array(
			'ignore_sticky_posts' => true,
			'post__not_in'        => array( $id ),
			'post_type'           => 'attachment',
			'post_status'         => 'any',
			'orderby'             => 'ID',
			'order'               => 'ASC',
			'meta_key'            => 'mdd_hash',
			'meta_value'          => $this_post_hash,
		));

		// If no other media with this hash was found, don't delete this media item. This way, even if
		// the user selects both images in a pair of duplicates, one will always be preserved.
		if ( ! $duplicate_media->have_posts() ) {
			$this->smart_skipped_count++;
			return;
		}

		// If this attachment is referenced anywhere on the site, replace references to it with
		// references to the duplicate with the lowest post ID.
		if ( $this->reference_handler->attachment_is_referenced( $id ) ) {
			$preserved_id = $duplicate_media->posts[0]->ID;
			$this->reference_handler->replace_all_references( $id, $preserved_id );
		}

		// Finally, delete this attachment.
		if ( wp_delete_attachment( $id ) ) {
			$this->smart_deleted_count++;
		}
	}

	/**
	 * Filters the media columns to add another one for filesize.
	 *
	 * @param array $posts_columns An array of column machine-readable names =>
	 *                             human-readable titles.
	 */
	public function media_columns( $posts_columns ) {
		$posts_columns['mdd_size'] = _x( 'Size', 'column name', 'media-deduper' );
		return $posts_columns;
	}

	/**
	 * Filters the media columns to make the Size column sortable.
	 *
	 * @param array $sortable_columns An array of sortable column machine readable
	 *                                names => human-readable titles.
	 */
	public function media_sortable_columns( $sortable_columns ) {
		$sortable_columns['mdd_size'] = array( 'mdd_size', true );
		return $sortable_columns;
	}

	/**
	 * Handles the file size column output.
	 *
	 * @param string $column_name The machine-readable name of the column to
	 *                            display content for.
	 * @param int    $post_id     The ID of the post to display content for.
	 */
	public function media_custom_column( $column_name, $post_id ) {
		if ( 'mdd_size' === $column_name ) {
			$filesize = get_post_meta( $post_id, 'mdd_size', true );
			if ( ! $filesize ) {
				echo esc_html__( 'Unknown', 'media-deduper' );
			} else {
				echo esc_html( size_format( $filesize ) );
			}
		}
	}

	/**
	 * Add meta query clauses corresponding to custom 'orderby' values.
	 *
	 * @param WP_Query $query A WP_Query object for which to alter query vars.
	 */
	public function pre_get_posts( $query ) {

		// Get the orderby query var.
		$orderby = $query->get( 'orderby' );

		// If there's only one orderby option, cast it as an array.
		if ( ! is_array( $orderby ) ) {
			$orderby = array(
				$orderby => $query->get( 'order' ),
			);
		}

		if ( in_array( 'mdd_size', array_keys( $orderby ), true ) ) {

			// Get the current meta query.
			$meta_query = $query->get( 'meta_query' );
			if ( ! $meta_query ) {
				$meta_query = array();
			}

			// Add a clause to sort by.
			$meta_query['mdd_size'] = array(
				'key'     => 'mdd_size',
				'type'    => 'NUMERIC',
				'compare' => 'EXISTS',
			);

			// Set the new meta query.
			$query->set( 'meta_query', $meta_query );
		}
	}
}


/**
 * Start up this plugin.
 */
function media_deduper_pro_init() {

	// If the free version of Media Deduper is active, prevent it from initializing itself.
	remove_action( 'init', 'media_deduper_init' );

	global $media_deduper_pro;
	$media_deduper_pro = new Media_Deduper_Pro();
}
// Add init function at an earlier priority than the free plugin's init function.
add_action( 'init', 'media_deduper_pro_init', 9 );
