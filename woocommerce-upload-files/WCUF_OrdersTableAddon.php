<?php
class WCUF_OrderstableAddon
{
	public function WCUF_OrderstableAddon()
	{
		add_action( 'manage_shop_order_posts_custom_column', array($this, 'manage_upload_counter_column'), 10, 2 );
		add_filter( 'manage_edit-shop_order_columns', array($this, 'add_upload_counter_column'),15 ); 
		 add_action('restrict_manage_posts', array( &$this,'add_uploads_select_box_filter'));
		add_filter('parse_query',array( &$this,'filter_query_by_uploads')); 
		//add_filter( 'manage_edit-shop_order_sortable_columns', array( &$this,'sort_columns') );
		add_action('admin_footer-edit.php', array( &$this,'add_bulk_delete_uploads_action'));
		add_action('load-edit.php', array( &$this,'delete_uploads_bulk_action'));
		add_action('admin_notices', array( &$this,'delete_uploads_admin_notices'));
	}
	
 
	function add_bulk_delete_uploads_action() 
	{
	  global $post_type;
	 
	  if($post_type == 'shop_order') {
		?>
		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery('<option>').val('wcuf_delete_uploads').text('<?php _e('Delete uploads', 'woocommerce-files-upload')?>').appendTo("select[name='action']");
			jQuery('<option>').val('wcuf_delete_uploads').text('<?php _e('Delete uploads', 'woocommerce-files-upload')?>').appendTo("select[name='action2']");
		  });
		</script>
		<?php
	  }
	}
	function delete_uploads_bulk_action() 
	{
		global $file_model;
	  // 1. get the action
	  $wp_list_table = _get_list_table('WP_Posts_List_Table');
	  $action = $wp_list_table->current_action();
	
	  /* if(!isset($_GET['post_type']) || $_GET['post_type'] != "shop_order")
			exit(); */
	  switch($action) 
	  {
		// 3. Perform the action
		case 'wcuf_delete_uploads':
		  $deleted = 0;
		  $post_ids = $_GET['post'];
		  foreach( $post_ids as $order_id ) {
			$file_model->delete_all_order_uploads($order_id);
			$deleted++;
		  }
	 
		  $sendback = add_query_arg( array('wcuf_deleted' => $deleted, 'post_type'=>'shop_order', 'ids' => join(',', $post_ids) ), $sendback );
		  //$sendback = add_query_arg( array('deleted' => $deleted, 'ids' => join(',', $post_ids) ) );
	 
		break;
		default: return;
	  }
	 
	  wp_redirect($sendback);
	 
	  exit();
	}
	
 
	function delete_uploads_admin_notices($args) 
	{
	  global $post_type, $pagenow;
	
	  if($pagenow == 'edit.php' && $post_type == 'shop_order' &&
		 isset($_REQUEST['wcuf_deleted']) && (int) $_REQUEST['wcuf_deleted']) 
		 {
		   $message = sprintf( _n( 'Order uploads deleted.', '%s orders uploads deleted.', $_REQUEST['wcuf_deleted'] ), number_format_i18n( $_REQUEST['wcuf_deleted'] ) );
		   echo '<div class="updated"><p>'.$message.'</p></div>';
	     }
	}
	public function manage_upload_counter_column( $column, $orderid ) 
	{
		global $option_model;
		if ( $column == 'upload-counter' ) 
		{
			$uploaded_files = $option_model->get_order_uploaded_files_meta_data($orderid);
			if(!$uploaded_files || empty($uploaded_files[0]))
				echo "0";
			else echo count($uploaded_files[0]);
		}
		
		
	}
	
	function sort_columns( $columns)
	{
		 $columns['upload-counter'] = 'upload-counter';
		return $columns;
	}
	public function add_upload_counter_column($columns)
	 {
		
	   //remove column
	   //unset( $columns['tags'] );

	   //add column
	   $columns['upload-counter'] =__('Upload counter', 'woocommerce-files-upload'); 

	   return $columns;
	}
	public function add_uploads_select_box_filter()
	{
		global $typenow, $wp_query; 
		if ($typenow=='shop_order') 
		{
			$selected = isset($_GET['wcuf_filter_by_uploads']) && $_GET['wcuf_filter_by_uploads'] ? $_GET['wcuf_filter_by_uploads']:"none";
			//onchange="this.form.submit()" ?>
			<select name="wcuf_filter_by_uploads" >
				<option value="all" <?php if($selected == "all") echo 'selected="selected"';?>><?php _e('Orders with and without uploads', 'woocommerce-files-upload') ?></option>
				<option value="uploads-only" <?php if($selected == "uploads-only") echo 'selected="selected"';?>><?php _e('Orders with uploads', 'woocommerce-files-upload') ?></option>
			</select>
			<?
		}
	}
	function filter_query_by_uploads($query) {
		global $pagenow;
		$qv = &$query->query_vars;
		if ($pagenow=='edit.php' && 
		    isset($qv['post_type']) && $qv['post_type']=='shop_order' && isset($_GET['wcuf_filter_by_uploads']) && $_GET['wcuf_filter_by_uploads'] == 'uploads-only') 
		{
			 $qv['meta_query'][] = 
				array(
				'key' => '_wcst_uploaded_files_meta',
				'compare' => 'NOT NULL'/*,
				 'type' => 'CHAR'  */
			  );
		}
		
	}
}
?>