<?php
/**
 * Setup the Entries as a CPT
 *
 * Creates the Entries CPT
 *
 * @since      3.0
 */
class VFB_Pro_Entries_CPT {

	/**
	 * Initial setup
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// Register CPT
		add_action( 'init', array( $this, 'register_cpt' ), 5 );
	}

	/**
	 * register_cpt function.
	 *
	 * @since  3.0
	 * @access public
	 * @return void
	 */
	public function register_cpt() {
		$not_found = __( 'No entries found.', 'vfb-pro' );
		$edit_item = __( 'Edit Entry', 'vfb-pro' );

		if ( !isset( $_GET['form-id'] ) )
			$not_found = __( 'Select a form using the dropdown filter above to view entries.', 'vfb-pro' );

		if ( isset( $_GET['vfb-action' ] ) && 'view' == $_GET['vfb-action'] )
			$edit_item = __( 'View Entry', 'vfb-pro' );

		$labels = array(
			'name'               => _x( 'Entries', 'post type general name', 'vfb-pro' ),
			'singular_name'      => _x( 'Entry', 'post type singular name', 'vfb-pro' ),
			'menu_name'          => _x( 'Entries', 'admin menu', 'vfb-pro' ),
			'name_admin_bar'     => _x( 'Entry', 'add new on admin bar', 'vfb-pro' ),
			'add_new'            => _x( 'Add New', 'entry', 'vfb-pro' ),
			'add_new_item'       => __( 'Add New Entry', 'vfb-pro' ),
			'new_item'           => __( 'New Entry', 'vfb-pro' ),
			'edit_item'          => $edit_item,
			'view_item'          => __( 'View Entry', 'vfb-pro' ),
			'all_items'          => __( 'All Entries', 'vfb-pro' ),
			'search_items'       => __( 'Search Entries', 'vfb-pro' ),
			'parent_item_colon'  => __( 'Parent Entries:', 'vfb-pro' ),
			'not_found'          => $not_found,
			'not_found_in_trash' => __( 'No entries found in Trash.', 'vfb-pro' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'_builtin'           => false,
			'query_var'          => true,
			'has_archive'        => false,
			'show_in_menu'       => false,
			'hierarchical'       => false,
			'menu_events'        => null,
			'rewrite'            => array( 'slug' => 'vfb_entry' ),
			'supports'           => array( 'custom-fields' ),
			'capabilities' 		 => array(
				'publish_posts'       => 'vfb_edit_entries',
				'edit_posts'          => 'vfb_edit_entries',
				'edit_others_posts'   => 'vfb_edit_entries',
				'delete_posts'        => 'vfb_delete_entries',
				'delete_others_posts' => 'vfb_delete_entries',
				'read_private_posts'  => 'vfb_view_entries',
				'edit_post'           => 'vfb_edit_entries',
				'delete_post'         => 'vfb_delete_entries',
				'read_post'           => 'vfb_view_entries',
			),
		);

		register_post_type( 'vfb_entry', $args );
	}
}