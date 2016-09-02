<?php
add_action( 'widgets_init', create_function( '', 'return register_widget( "VFB_Pro_Widget" );' ) );

/**
 * VFB_Pro_Widget class.
 *
 * @extends WP_Widget
 */
class VFB_Pro_Widget extends WP_Widget {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			'vfbp-widget',
			__( 'VFB Pro', 'vfb-pro' ),
			array(
				'description' => __( 'VFB Pro Widget', 'vfb-pro' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$form_id = absint( $instance['id'] );

     	echo $args['before_widget'];

		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo VFB_Pro_Form_Display::display( array( 'id' => $form_id ) );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$vfbdb  = new VFB_Pro_Data();
		$forms  = $vfbdb->get_all_forms( " WHERE status = 'publish'" );

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'vfb-pro' );
		}

		if ( isset( $instance['id'] ) ) {
			$form_id = $instance['id'];
		}
		else {
			$form_id = 0;
		}
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Form to display:', 'vfb-pro' ); ?></label>
			<select id="vfb-entry-form-ids" name="<?php echo $this->get_field_name( 'id' ); ?>" class="widefat">
				<option><?php _e( 'Select a Form', 'vfb-pro' ); ?></option>
				<?php
					if ( is_array( $forms ) && !empty( $forms ) ) {
						foreach ( $forms as $form ) {
							echo sprintf(
								'<option value="%1$d"%3$s>%1$d - %2$s</option>',
								$form['id'],
								$form['title'],
								selected( $form['id'], $instance['id'], false )
							);
						}
					}
				?>
			</select>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['id']    = !empty( $new_instance['id'] ) ? absint( $new_instance['id'] ) : '';
		$instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}