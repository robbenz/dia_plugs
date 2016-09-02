<?php
/**
 * Class that controls the Edit Form view
 *
 * @since 3.0
 */
class VFB_Pro_Forms_Edit {
	/**
	 * display function.
	 *
	 * @access public
	 * @return void
	 */
	public function display() {
		// Get the current selected tab
		$current_tab = $this->get_current_tab();

		// Get the current form ID
		$form_id = $this->get_form_id();

		$edit_url     = esc_url( add_query_arg( array( 'vfb-tab' => 'fields', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$settings_url = esc_url( add_query_arg( array( 'vfb-tab' => 'settings', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$email_url    = esc_url( add_query_arg( array( 'vfb-tab' => 'email', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$confirm_url  = esc_url( add_query_arg( array( 'vfb-tab' => 'confirmation', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$design_url   = esc_url( add_query_arg( array( 'vfb-tab' => 'email-design', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$rules_url    = esc_url( add_query_arg( array( 'vfb-tab' => 'rules', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
		$addons_url   = esc_url( add_query_arg( array( 'vfb-tab' => 'addons', 'form' => $form_id, 'vfb-action' => 'edit' ), admin_url( 'admin.php?page=vfb-pro' ) ) );
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo $edit_url; ?>" class="nav-tab<?php echo 'fields' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Edit Fields', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $settings_url; ?>" class="nav-tab<?php echo 'settings' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Form Settings', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $email_url; ?>" class="nav-tab<?php echo 'email' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Email Settings', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $confirm_url; ?>" class="nav-tab<?php echo 'confirmation' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Confirmation', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $design_url; ?>" class="nav-tab<?php echo 'email-design' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Email Design', 'vfb-pro' ); ?>
			</a>
			<a href="<?php echo $rules_url; ?>" class="nav-tab<?php echo 'rules' == $current_tab ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e( 'Rules', 'vfb-pro' ); ?>
			</a>
			<?php if ( $this->addons_active() ) : ?>
				<a href="<?php echo $addons_url; ?>" class="nav-tab<?php echo 'addons' == $current_tab ? ' nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Add-ons', 'vfb-pro' ); ?>
				</a>
			<?php endif; ?>
		</h2>

		<?php
			// Display current tab content
			switch( $current_tab ) :
				case 'fields' :
					$this->fields( $form_id );
					break;

				case 'settings' :
					$this->settings( $form_id );
					break;

				case 'email' :
					$this->email( $form_id );
					break;

				case 'confirmation' :
					$this->confirmation( $form_id );
					break;

				case 'email-design' :
					$this->email_design( $form_id );
					break;

				case 'rules' :
					$this->rules( $form_id );
					break;

				case 'addons' :
					$this->addons( $form_id );
					break;
			endswitch;
		?>

	</div> <!-- .wrap -->
	<?php
	}

	/**
	 * Displays the Edit Fields tab
	 *
	 * @access public
	 * @return void
	 */
	public function fields( $id ) {
		$fields = new VFB_Pro_Forms_Edit_Fields( $id );
		$fields->display();
	}

	/**
	 * Displays the Form Settings tab
	 *
	 * @access public
	 * @return void
	 */
	public function settings( $id ) {
		$settings = new VFB_Pro_Forms_Edit_Settings( $id );
		$settings->display();
	}

	/**
	 * Displays the Email Settings tab
	 *
	 * @access public
	 * @return void
	 */
	public function email( $id ) {
		$email = new VFB_Pro_Forms_Edit_Email( $id );
		$email->display();
	}

	/**
	 * Displays the Confirmation tab
	 *
	 * @access public
	 * @return void
	 */
	public function confirmation( $id ) {
		$confirmation = new VFB_Pro_Forms_Edit_Confirmation( $id );
		$confirmation->display();
	}

	/**
	 * Displays the Email Design tab
	 *
	 * @access public
	 * @return void
	 */
	public function email_design( $id ) {
		$email_design = new VFB_Pro_Edit_Email_Design( $id );
		$email_design->display();
	}

	/**
	 * Displays the Rules tab
	 *
	 * @access public
	 * @return void
	 */
	public function rules( $id ) {
		$rules = new VFB_Pro_Edit_Rules( $id );
		$rules->display();
	}

	/**
	 * Displays the Add-ons tab
	 *
	 * @access public
	 * @return void
	 */
	public function addons( $id ) {
		$addons = new VFB_Pro_Edit_Addons( $id );
		$addons->display();
	}

	/**
	 * Tests if add-on plugin is active and whether or not to display the tab.
	 *
	 * @access private
	 * @return void
	 */
	private function addons_active() {
		$active = false;

		$create_user     = is_plugin_active( 'vfbp-create-user/vfbp-create-user.php' );
		$create_post     = is_plugin_active( 'vfbp-create-post/vfbp-create-post.php' );
		$display_entries = is_plugin_active( 'vfbp-display-entries/vfbp-display-entries.php' );
		$form_designer   = is_plugin_active( 'vfbp-form-designer/vfbp-form-designer.php' );
		$notifications   = is_plugin_active( 'vfbp-notifications/vfbp-notifications.php' );
		$payments        = is_plugin_active( 'vfbp-payments/vfbp-payments.php' );

		if ( $create_user || $create_post || $display_entries || $form_designer || $notifications || $payments ) {
			$active = true;
		}

		return $active;
	}

	/**
	 * Returns the current tab
	 *
	 * @access private
	 * @return void
	 */
	private function get_current_tab() {
		$tab = '';

		if ( !isset( $_GET['vfb-tab'] ) || isset( $_GET['vfb-tab'] ) && 'fields' == $_GET['vfb-tab'] )
			$tab = 'fields';
		elseif ( isset( $_GET['vfb-tab'] ) )
			$tab = esc_html( $_GET['vfb-tab'] );

		return $tab;
	}

	/**
	 * Returns the Form ID
	 *
	 * @access private
	 * @return void
	 */
	private function get_form_id() {
		// Exit if not on vfb-pro page
		if ( isset( $_GET['page'] ) && 'vfb-pro' !== $_GET['page'] )
			return;

		// Exit if form var isn't passed
		if ( !isset( $_GET['form'] ) )
			return;

		return absint( $_GET['form'] );
	}
}