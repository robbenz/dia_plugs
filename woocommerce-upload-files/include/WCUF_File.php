<?php
class WCUF_File
{
	var $current_order;
	var $email_sender;
	
	public function __construct()
	{
		add_action( 'before_delete_post', array( &$this, 'delete_all_order_uploads' ), 10 );
	}
	
	public function wcup_ovveride_upload_directory( $dir ) 
	{ 
		return array(
			'path'   => $dir['basedir'] . '/wcuf/'.$this->current_order->id,//get_current_user_id(),
			'url'    => $dir['baseurl'] . '/wcuf/'.$this->current_order->id,//get_current_user_id(),
			'subdir' => '/wcuf/'.$this->current_order->id,//get_current_user_id(),
		) + $dir;
	}
	public function generate_unique_file_name($dir, $name, $ext = "")
	{
		return rand(0,1000000)."_".$name.$ext;
	}
	public function upload_files($order,$file_order_metadata, $options)
	{
		$order_id = $order->id ;	
		//var_dump($_FILES);
		 //var_dump($_POST);
		 
		 //$_POST['wcuf']
		 //array(1) { ["wcuf"]=> array(3) { [0]=> array(2) { ["title"]=> string(7) "Title 1" ["id"]=> string(1) "0" } [1]=> array(2) { ["title"]=> string(7) "Title 2" ["id"]=> string(1) "1" } [4]=> array(2) { ["title"]=> string(7) "Ttile 4" ["id"]=> string(1) "4" } } } 
		 
		 //foreach ogni file, si recupera lo id, e si salva nei meta titolo e path del file
		 //wp_handle_upload
		 
		 //$mail_sent = false;
		 $links_to_notify_via_mail = array();
		 $links_to_attach_to_mail = array();
		 foreach($_FILES as $fieldname_id => $file_data)
		 {
			 list($fieldname, $id) = explode("_", $fieldname_id );
			 if($file_data["name"] != '')
			 {
				$this->current_order = $order;
				$upload_overrides = array( 'test_form' => false, 'unique_filename_callback' => array( $this , 'generate_unique_file_name') );
				add_filter( 'upload_dir', array( &$this,'wcup_ovveride_upload_directory' ));
				
				$movefile = wp_handle_upload( $file_data, $upload_overrides );
				remove_filter( 'upload_dir', array( &$this,'wcup_ovveride_upload_directory' ));
				
				$upload_dir = wp_upload_dir();
				if( !file_exists ($upload_dir['basedir'].'/wcuf/index.html'))
					touch ($upload_dir['basedir'].'/wcuf/index.html');
					
				if ( $movefile && !isset( $movefile['error'] ) ) 
				{
					//echo "File is valid, and was successfully uploaded.\n";
					//var_dump( $movefile); //['url'], ['file']
					/* if(!isset($file_order_metadata[$id]))
							$file_order_metadata[$id] = array(); */
					
					if( !file_exists ($upload_dir['basedir'].'/wcuf/'.$order_id.'/index.html'))
						touch ($upload_dir['basedir'].'/wcuf/'.$order_id.'/index.html');
					
					$file_order_metadata[$id]['absolute_path'] = $movefile['file'];
					$file_order_metadata[$id]['url'] = $movefile['url'];
					$file_order_metadata[$id]['title'] = $_POST['wcuf'][$id]['title'];
					$file_order_metadata[$id]['id'] = $id;
					$original_option_id = $id;
					$needle = strpos($original_option_id , "-");
					if($needle !== false)
						$original_option_id = substr($original_option_id, 0, $needle);
					foreach($options as $option)
					{
						if(/* !$mail_sent &&  */$option['id'] == $original_option_id && $option['notify_admin'] )
							array_push($links_to_notify_via_mail, array('title' => $file_order_metadata[$id]['title'], 'url'=> $file_order_metadata[$id]['url']));
						if($option['id'] == $original_option_id && $option['notify_admin'] && $option['notify_attach_to_admin_email'])
							array_push($links_to_attach_to_mail, $file_order_metadata[$id]['absolute_path'] );
					}
					 
				}
				else
				{
					
					//var_dump($movefile['error']);
				}

			 }
		 }
		 //Notification via mail
		if(count($links_to_notify_via_mail) > 0)
		{
			$this->email_sender = new WCUF_Email();
			$this->email_sender->trigger($links_to_notify_via_mail, $order, $links_to_attach_to_mail );	
		}
		update_post_meta( $order_id, '_wcst_uploaded_files_meta', $file_order_metadata);
		return $file_order_metadata;
	}
	public function upload_and_decode_files($order,$file_order_metadata, $options)
	{
		$order_id = $order->id ;	
		 $links_to_notify_via_mail = array();
		 $links_to_attach_to_mail = array();
		 foreach($_POST['wcuf-encoded-file'] as $id => $file_data)
		 {
			$this->current_order = $order;
			
			//decode data
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wcuf/'.$order->id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
				
			$unique_file_name = $this->generate_unique_file_name(null, $_POST['wcuf'][$id]['file_name']);
			$ifp = fopen($upload_complete_dir.$unique_file_name, "w"); 
			fwrite($ifp, base64_decode($file_data)); 
			fclose($ifp); 
		
			if( !file_exists ($upload_dir['basedir'].'/wcuf/index.html'))
				touch ($upload_dir['basedir'].'/wcuf/index.html');
				
			
			if( !file_exists ($upload_dir['basedir'].'/wcuf/'.$order_id.'/index.html'))
				touch ($upload_dir['basedir'].'/wcuf/'.$order_id.'/index.html');
			
			$file_order_metadata[$id]['absolute_path'] = $upload_complete_dir.$unique_file_name;
			$file_order_metadata[$id]['url'] = $upload_dir['baseurl'].'/wcuf/'.$order->id.'/'.$unique_file_name;
			$file_order_metadata[$id]['title'] = $_POST['wcuf'][$id]['title'];
			$file_order_metadata[$id]['id'] = $id;
			$original_option_id = $id;
			$needle = strpos($original_option_id , "-");
			if($needle !== false)
				$original_option_id = substr($original_option_id, 0, $needle);
			foreach($options as $option)
			{
				if(/* !$mail_sent &&  */$option['id'] == $original_option_id && $option['notify_admin'] )
					array_push($links_to_notify_via_mail, array('title' => $file_order_metadata[$id]['title'], 'url'=> $file_order_metadata[$id]['url']));
				if($option['id'] == $original_option_id && $option['notify_admin'] && $option['notify_attach_to_admin_email'])
					array_push($links_to_attach_to_mail, $file_order_metadata[$id]['absolute_path'] );
			}
				 
			
		 }
		 //Notification via mail
		if(count($links_to_notify_via_mail) > 0)
		{
			$this->email_sender = new WCUF_Email();
			$this->email_sender->trigger($links_to_notify_via_mail, $order, $links_to_attach_to_mail );	
		}
		update_post_meta( $order_id, '_wcst_uploaded_files_meta', $file_order_metadata);
		return $file_order_metadata;
	}
	public function delete_file($id, $file_order_metadata, $order_id)
	{
		/* var_dump("delete ".$file_order_metadata[$id]['absolute_path']);*/
		try{
			@unlink($file_order_metadata[$id]['absolute_path']);
		}catch(Exception $e){};
		unset($file_order_metadata[$id]);
		update_post_meta( $order_id, '_wcst_uploaded_files_meta', $file_order_metadata);
		return $file_order_metadata; 
	}
	public function delete_all_order_uploads($order_id)
	{
		$post = get_post($order_id);
		if ($post->post_type == 'shop_order')
		{
			$file_order_metadata = get_post_meta($order_id, '_wcst_uploaded_files_meta');
			$file_order_metadata = !$file_order_metadata ? array():$file_order_metadata[0];
			
			foreach($file_order_metadata as $file_to_delete)
			{
				try{
					@unlink($file_to_delete['absolute_path']);
				}catch(Exception $e){};
			}
			delete_post_meta( $order_id, '_wcst_uploaded_files_meta');
		}
	}
}	
?>