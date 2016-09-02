<?php
/**
 * Class that displays widgets on the WordPress dashboard
 *
 * @since 3.0
 */
class VFB_Pro_Dashboard_Widgets {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct() {
		// Adds a Dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register our Dashboard widget
	 *
	 * @access public
	 * @return void
	 */
	public function register_widgets() {
		if ( current_user_can( 'vfb_view_entries' ) || current_user_can( 'vfb_edit_entries' ) ) {
			wp_add_dashboard_widget(
				'vfbp-dashboard',
				__( 'VFB Pro Activity', 'vfb-pro' ),
				array( $this, 'widget_entries' )
			);
		}
	}

	/**
	 * Displays recent submitted entries
	 *
	 * @access public
	 * @return void
	 */
	public function widget_entries() {

		echo '<div class="vfb-activity">';

		$recent_entries = $this->recent_entries( array(
			'max'     => 10,
			'status'  => 'publish',
			'order'   => 'DESC',
			'title'   => __( 'Recently Submitted', 'vfb-pro' ),
			'id'      => 'vfb-published-entries',
		) );

		if ( !$recent_entries ) {
			echo '<div class="no-activity">';
			echo '<p class="smiley"></p>';
			echo '<p>' . __( 'No entries yet!', 'vfb-pro' ) . '</p>';
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Show VFB Pro Activity widget
	 *
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	public function recent_entries( $args ) {
		$query_args = array(
			'post_type'      => 'vfb_entry',
			'post_status'    => $args['status'],
			'orderby'        => 'date',
			'order'          => $args['order'],
			'posts_per_page' => intval( $args['max'] ),
			'no_found_rows'  => true,
			'cache_results'  => false,
		);

		$entries = new WP_Query( $query_args );

		if ( $entries->have_posts() ) {
			echo '<style type="text/css">
				#vfb-published-entries ul span {
					color: #777;
					float: left;
					margin-right: 8px;
					min-width: 150px;
				}

				#vfb-published-entries ul a {
					float: right;
				}
			</style>';

			printf( '<div id="%s" class="activity-block">', $args['id'] );

			printf( '<h4>%s</h4>', $args['title'] );

			echo '<ul>';

			$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
			$tomorrow = date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

			while ( $entries->have_posts() ) {
				$entries->the_post();

				$time = get_the_time( 'U' );
				if ( date( 'Y-m-d', $time ) == $today )
					$relative = __( 'Today' );
				elseif ( date( 'Y-m-d', $time ) == $tomorrow )
					$relative = __( 'Tomorrow' );
				else
					$relative = date_i18n( __( 'M jS' ), $time );

				$entry_id   = get_the_ID();
				$form_id    = get_post_meta( $entry_id, '_vfb_form_id', true );
				$seq_num    = get_post_meta( $entry_id, '_vfb_seq_num', true );
				$form_title = $this->get_form_title( $form_id );

				if ( current_user_can( 'vfb_edit_entries' ) ) {
					$action_url = get_edit_post_link();
				}
				elseif ( current_user_can( 'vfb_view_entries' ) ) {
					$action_url = add_query_arg(
						array(
							'vfb-action' => 'view',
							'action'	 => 'edit',
							'form-id'    => $form_id,
							'post'       => $entry_id,
							'post_type'  => 'vfb_entry',
						),
						admin_url( 'post.php' )
					);
				}

				printf(
					'<li><span>%1$s, %2$s</span> %3$s%4$s <a href="%5$s">%6$s</a></li>',
					$relative,
					get_the_time(),
					__( 'Entry #', 'vfb-pro' ),
					$seq_num,
					esc_url( $action_url ),
					esc_html( $form_title )
				);

			}

			echo '</ul>';
			echo '</div>';

		} else {
			return false;
		}

		wp_reset_postdata();

		return true;
	}

	/**
	 * Get the form title for use in dashboard widgets
	 *
	 * @access private
	 * @param mixed $form_id
	 * @return void
	 */
	private function get_form_title( $form_id ) {
		global $wpdb;

		$title = $wpdb->get_var( $wpdb->prepare( "SELECT title FROM " . VFB_FORMS_TABLE_NAME . " WHERE id = %d", $form_id ) );

		return $title;
	}
}