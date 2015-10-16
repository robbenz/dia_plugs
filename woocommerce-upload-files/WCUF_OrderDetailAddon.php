<?php
class WCUF_OrderDetailAddon
{
	var $current_order;
	var $email_sender;
	public function WCUF_OrderDetailAddon()
	{
		add_action( 'add_meta_boxes', array( &$this, 'woocommerce_metaboxes' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( &$this, 'front_end_order_page_addon' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_process_shop_ordermeta' ), 5, 2 );
		add_action( 'woocommerce_after_checkout_billing_form', array( &$this, 'add_uploads_checkout_page' ), 10, 1 ); //Checkout page
		//add_action('woocommerce_checkout_update_order_meta', array( &$this, 'save_uploads_after_checkout' )); //After checkout
		add_action('woocommerce_checkout_order_processed', array( &$this, 'save_uploads_after_checkout' )); //After checkout
		//add_action('save_post', array( &$this, 'save_uploads_after_checkout' )); //After checkout
		
	}
	
	function save_uploads_after_checkout( $order_id)
	{
		global $file_model, $option_model;
		if(!wp_verify_nonce($_POST['wcuf_attachment_nonce'], 'wcuf_checkout_upload')) 
		  return $order_id;
		

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		  return $order_id;
		 
	
		if(isset($_POST['wcuf-encoded-file']) && isset($_POST['wcuf-uploading-data']) ) 
		{
			$order = new WC_Order($order_id);
			$file_fields_groups =  $option_model->get_fields_meta_data();
			$file_order_metadata = $option_model->get_order_uploaded_files_meta_data($order_id);
			$file_order_metadata = !$file_order_metadata ? array():$file_order_metadata[0];
			$file_order_metadata = $file_model->upload_and_decode_files($order, $file_order_metadata, $file_fields_groups);
			
		}
	}
	function woocommerce_process_shop_ordermeta( $post_id, $post ) 
	{
		global $file_model, $option_model;
		//Used when admin save order from order detail page in backend
		if(isset($_POST['files_to_delete']))
		{
			$file_order_metadata = $option_model->get_order_uploaded_files_meta_data($post_id);
			$file_order_metadata = $file_order_metadata[0];
		
			foreach($_POST['files_to_delete'] as $value)
			{
				//var_dump(intval($value)." ".$file_order_metadata[$value]['absolute_path']." ".$post_id);
				$file_order_metadata = $file_model->delete_file($value, $file_order_metadata, $post_id);
			}
		}
	}
	function woocommerce_metaboxes() 
	{

		add_meta_box( 'woocommerce-files-upload', __('File(s) uploaded', 'woocommerce-files-upload'), array( &$this, 'woocommerce_order_uploaded_files_box' ), 'shop_order', 'side', 'high');

	}
	function woocommerce_order_uploaded_files_box($post) 
	{
		global $option_model;
		$data = get_post_custom( $post->ID );
		$file_fields_meta = $option_model->get_fields_meta_data();
		$uploaded_files = $option_model->get_order_uploaded_files_meta_data($post->ID);
		?>
		<div id="upload-box">
		<p><i><?php _e('Click "Save Order" button after one or more file deletion otherwise changes will no take effects.', 'woocommerce-files-upload'); ?></i></p>
			<?php if(!$uploaded_files || empty($uploaded_files[0])): echo '<p><strong>'.__('Customer hasn\'t uploaded any file...yet.', 'woocommerce-files-upload').'</strong></p>'; 
			else:?>
			<ul class="totals">
			 <?php foreach($uploaded_files[0] as $file_meta): ?>
				<li>
					<h4 style="margin-bottom:5px;"><?php echo $file_meta['title'] ?></h4>
					<a target="_blank" class="button button-primary" style="text-decoration:none; color:white;" href="<?php echo $file_meta['url']; ?>">Download</a>
					<input  type="submit" class="button delete_button" data-fileid="<?php echo $file_meta['id'] ?>" value="<?php _e('Delete', 'woocommerce-files-upload'); ?>" onclick="clicked(event);" ></input>
				</li>
			  <?php endforeach;?>
			</ul>
			<?php endif; ?>
		</div>
		<script type="text/javascript">
		var index = 0;
		function clicked(e) 
			{ 
			  /*  console.log(e.target); */
			   e.preventDefault();
			   if(confirm('<?php _e('Are you sure?', 'woocommerce-files-upload'); ?>'))
			   {
				   jQuery("#upload-box").append( '<input type="hidden" name="files_to_delete['+index+']" value="'+jQuery(e.target).data('fileid')+'"></input>');
				   jQuery(e.target).parent().remove();
				   index++;
			   }
			}
		</script>
		<div class="clear"></div>
		<?php 
	}	
	
	
	function add_uploads_checkout_page($checkout)
	{
		global $option_model;
		$cart_items = WC()->cart->cart_contents;
		$file_order_metadata = array();
		$file_fields_groups = $option_model->get_fields_meta_data();
		/*  wp_dequeue_script( 'wc-checkout' );*/
		wp_enqueue_script('wcuf-submit-overide', plugins_url( '/js/checkout-encoder.js' , __FILE__ ),array('jquery'));  
		include 'template/checkout_page_template.php';	
	}
	function front_end_order_page_addon( $order )
	{	
		global $file_model, $option_model;
		$file_fields_groups = $option_model->get_fields_meta_data();
		$order_id = $order->id ;
		$file_order_metadata =$option_model->get_order_uploaded_files_meta_data($order_id);
		$file_order_metadata = !$file_order_metadata ? array():$file_order_metadata[0];
		
		if(isset($_POST) && isset($_POST['type']) && $_POST['type'] === 'wcup_delete')
		{
			$file_order_metadata = $this->delete_file($_POST['id'], $file_order_metadata, $order_id);
		}
		
		else if($_FILES) 
		{
			$file_order_metadata = $file_model->upload_files($order, $file_order_metadata, $file_fields_groups);
		}
	
		if($order->status != 'completed' && $file_fields_groups)
		{
			include 'template/view_order_template.php';
		}
					
	}
	function var_dump($var)
	{
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}
?>