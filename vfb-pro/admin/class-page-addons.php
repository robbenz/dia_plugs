<?php

/**
 * Class that controls the Add-ons page view
 *
 * @since      3.0
 */
class VFB_Pro_Page_AddOns {
	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Double check permissions before display
		if ( !current_user_can( 'vfb_edit_settings' ) )
			return;

		// Get the current selected tab
		$current_tab = $this->get_current_tab();

		$addons_url     = esc_url( add_query_arg( array( 'vfb-tab' => 'addons-list' ), admin_url( 'admin.php?page=vfbp-addons' ) ) );
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo $addons_url; ?>" class="nav-tab<?php echo 'addons-list' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Available Add-Ons', 'vfb-pro' ); ?>
			</a>
		</h2>

		<?php
			// Display current tab content
			switch( $current_tab ) :
				case 'addons-list' :
					$this->addons_list();
					break;
			endswitch;
		?>
	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Displays the Add-ons tab
	 *
	 * @access public
	 * @return void
	 */
	public function addons_list() {
	?>
	<div class="vfb-addons-list-container">
		<div class="vfb-row">
			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-create-user">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Create User', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'Wish you could go ahead and create a WordPress user when someone submits a form? Create a form and watch as user sign-ups are now a piece of cake.', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/visual-form-builder-pro-create-user" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-create-post">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Create Post', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'Easily create a WordPress post with your forms. Gain access to six new field items: Title, Content, Excerpt, Category, Tag, and Custom Field.', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/visual-form-builder-pro-create-post" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-display-entries">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Display Entries', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'With the Display Entries add-on you can now show off your entries with the ease of checking a few boxes. Build tables that are fully sortable, filterable, and searchable.', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/visual-form-builder-pro-display-entries" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->
		</div> <!-- .vfb-row -->

		<div class="vfb-row">
			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-payments">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Payments', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'Looking to sell more than one item? The Payments add-on allows you to sell multiple items, subscriptions, and even display a running total of your cart. PayPal Standard only (for now).', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/visual-form-builder-pro-payments" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-form-designer">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Form Designer', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'Quickly and easily customize the design of your form without writing any CSS. Create a custom design based on your settings.', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/visual-form-builder-pro-form-designer" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->

			<div class="vfb-col-4">
				<div class="vfb-addons-widget" id="vfb-addon-notifications">
					<div class="vfb-addons-widget-header">
						<h3><?php _e( 'Notifications', 'vfb-pro' ); ?></h3>
					</div> <!-- .vfb-addons-widget-header -->

					<div class="vfb-addons-widget-content">
						<p><?php _e( 'Connect your forms with a number of third-party services such as MailChimp, Campaign Monitor, Highrise, and FreshBooks after new form submissions. You can even send SMS to your phone!', 'vfb-pro' ); ?></p>
					</div> <!-- .vfb-addons-widget-content -->

					<div class="vfb-addons-widget-footer">
						<a href="http://vfbpro.com/collections/add-ons/products/notifications" class="button button-primary"><?php _e( 'Learn More', 'vfb-pro' ); ?></a>
					</div> <!-- .vfb-addons-widget-footer -->
				</div> <!-- .vfb-addons-widget -->
			</div> <!-- .vfb-col-4 -->
		</div> <!-- .vfb-row -->
	</div> <!-- .vfb-addons-list-container -->
	<?php
	}

	/**
	 * Returns the current tab
	 *
	 * @access private
	 * @return void
	 */
	private function get_current_tab() {
		$tab = '';

		if ( !isset( $_GET['vfb-tab'] ) || isset( $_GET['vfb-tab'] ) && 'addons-list' == $_GET['vfb-tab'] )
			$tab = 'addons-list';
		elseif ( isset( $_GET['vfb-tab'] ) )
			$tab = esc_html( $_GET['vfb-tab'] );

		return $tab;
	}
}