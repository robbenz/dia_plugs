<?php
/**
 * Media Deduper Pro: async indexer class.
 *
 * @package Media_Deduper_Pro
 */

// Load parent classes.
require_once( MDD_PRO_INCLUDES_DIR . 'vendor/wp-async-request.php' );
require_once( MDD_PRO_INCLUDES_DIR . 'vendor/wp-background-process.php' );

/**
 * Background process class for executing indexing tasks in the background.
 */
class MDD_Indexer extends WP_Background_Process {

	/**
	 * The namespace for options, etc.
	 *
	 * @var string
	 */
	protected $prefix = 'mdd_pro';

	/**
	 * A name for the specific action performed by this class.
	 *
	 * @var string
	 */
	protected $action = 'index';

	/**
	 * Save queue, initialize stored indexer status data and being processing.
	 */
	public function index() {

		// Save queue.
		$this->save();

		// Clear old indexer status data, if any.
		delete_option( $this->identifier . '_status' );

		// Set & store total count.
		$status = $this->get_status();
		$status['total'] = count( $this->data );
		$this->update_status( $status );

		return $this->dispatch();
	}

	/**
	 * Check whether the indexer is running.
	 */
	public function is_indexing() {
		return ( ! $this->is_queue_empty() || $this->is_process_running() );
	}

	/**
	 * Get status data.
	 */
	public function get_status() {

		// Get raw data from option.
		$status = get_option( $this->identifier . '_status' );

		// If option was empty or something other than an array, initialize it.
		if ( ! $status || ! is_array( $status ) ) {
			$status = array();
		}

		// Make sure 'processed', 'failed', and 'total' are integers.
		$int_keys = array( 'processed', 'failed', 'total' );
		foreach ( $int_keys as $key ) {
			if ( ! isset( $status[ $key ] ) ) {
				$status[ $key ] = 0;
			} else {
				$status[ $key ] = (int) $status[ $key ];
			}
		}

		// Make sure 'messages' is an array.
		if ( ! isset( $status['messages'] ) || ! is_array( $status['messages'] ) ) {
			$status['messages'] = array();
		}

		// Make sure 'state' is a string.
		if ( ! isset( $status['state'] ) || empty( $status['state'] ) ) {
			$status['state'] = 'processing';
		}

		return $status;
	}

	/**
	 * Save updated status data.
	 *
	 * @param array $status The status data to save.
	 */
	protected function update_status( $status ) {
		return update_option( $this->identifier . '_status', $status );
	}

	/**
	 * Process an item in the queue.
	 *
	 * @param int $post_id The ID of the post to index.
	 */
	protected function task( $post_id ) {

		// Sanitize $post_id.
		$post_id = absint( $post_id );
		// Get post data.
		$post = get_post( $post_id );

		// Get the stored status data.
		$status = $this->get_status();

		if ( ! $post ) {

			// If this isn't really a post, skip it.
			$status['failed'] += 1;
			$status['messages'][] = array(
				'success' => false,
				'message' => sprintf(
					// translators: %d: The ID of the missing post.
					__( 'No post found with ID %d', 'media-deduper' ),
					$post_id
				),
			);

		} else {

			// If the post was found, process it.
			// Get the global MDD plugin object.
			global $media_deduper_pro;

			if ( 'attachment' === $post->post_type ) {
				// If this is an attachment, calculate its hash.
				$result = $media_deduper_pro->calc_media_meta( $post_id );
			} else {
				// If this isn't an attachment, check for references to attachments and store them.
				$result = $media_deduper_pro->track_media_refs( $post_id );
			}

			// If this item failed to process, bump the failed count.
			if ( ! $result['success'] ) {
				$status['failed'] += 1;
			}

			// Add the latest message.
			$status['messages'][] = $result;
		}

		// Bump the processed count.
		$status['processed'] += 1;

		// Store the updated status data.
		$this->update_status( $status );

		// Remove this item from the queue.
		return false;
	}

	/**
	 * Fires once all items in the queue have been processed.
	 */
	protected function complete() {

		// Call parent class's complete handler to unschedule this process's cron task.
		parent::complete();

		// Update status with 'complete' state.
		$status = $this->get_status();
		$status['state'] = 'complete';
		$this->update_status( $status );
	}
}
