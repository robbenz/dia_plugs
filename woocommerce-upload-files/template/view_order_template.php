<style>
.spacer
{
	display:block; height:10px;
}
.spacer2
{
	display:block; height:25px;
}
</style>

<!--<h2 id="upload-title"><?php _e('Upload files', 'woocommerce-files-upload'); ?></h2> -->
<form method="post" action="" enctype="multipart/form-data">
<input type="hidden" value="yes" name="wcuf-uploading-data"></input>
<?php 
$render_upload_button = false;
foreach($file_fields_groups as $file_fields):  
	
	global $sitepress;
	$enable_for = isset($file_fields['enable_for']) ? $file_fields['enable_for']:'always';
	$disable_stacking = isset($file_fields['disable_stacking']) ? (bool)$file_fields['disable_stacking']:false;
	$selected_categories = isset($file_fields['category_ids']) ? $file_fields['category_ids']:array();
	$all_products_cats_ids = array();
	$products_for_which_stacking_is_disabled = array();
	$can_render = $enable_for == 'always' ? true:false;
	
	if(($enable_for === 'always' && $disable_stacking) || $enable_for !== 'always' && count($selected_categories) > 0)
	{
		//for every product in the order, look for its categories and parent categories ids
		WCUF_AdminMenu::WCUF_switch_to_default_lang();
		foreach($order->get_items() as $product)
		{
			//product categories
			$product_cats = wp_get_post_terms( $product["product_id"], 'product_cat' );
			$current_product_categories_ids = array();
			foreach($product_cats as $category)
			{
				$category_id = $category->term_id;
				
				if(!$disable_stacking)
					array_push($all_products_cats_ids, (string)$category_id);
				else
					array_push($current_product_categories_ids, (string)$category_id);
				
				//parent categories
				if($enable_for == "categories_children" || $enable_for == "disable_categories_children")
				{
					$parents =  get_ancestors( $category->term_id, 'product_cat' ); 
					foreach($parents as $parent_id)
					{
						$temp_category = $parent_id;
						if(!$disable_stacking)
							array_push($all_products_cats_ids, (string)$temp_category);
						else
							array_push($current_product_categories_ids, (string)$category_id);
					}
				}
			}
			//Can enable upload for this product? (if stacking uploads are disabled)
			if($disable_stacking)
			{
				if($enable_for === 'categories' || $enable_for === 'categories_children')
				{
					if(array_intersect($selected_categories, $current_product_categories_ids))
					{
						array_push($products_for_which_stacking_is_disabled, $product);
						$can_render = true;
					}
				}
				else
				{
					if(!array_intersect($selected_categories, $current_product_categories_ids))
					{
						array_push($products_for_which_stacking_is_disabled, $product);
						$can_render = true;
					}
				}	
			}
		} //ends product foreach
		WCUF_AdminMenu::WCUF_restore_current_lang();
		if(!$disable_stacking)
			if($enable_for === 'categories' || $enable_for === 'categories_children')
			{  
				if(array_intersect($selected_categories, $all_products_cats_ids))
					$can_render = true;
			}
			else
			{ 
				if(!array_intersect($selected_categories, $all_products_cats_ids))
					$can_render = true;
			}	
	}
			
			if($can_render):
				if(!$disable_stacking):
				?>
				<h4 style="margin-bottom:5px;"><?php  echo $file_fields['title'] ?></h4>
				<p><?php echo $file_fields['description'] ?></p>
				<?php if(!isset($file_order_metadata[$file_fields['id']])): ?>
						<input type="hidden" name="wcuf[<?php echo $file_fields['id']; ?>][title]" value="<?php echo $file_fields['title']; ?>"></input>
						<input type="hidden" name="wcuf[<?php echo $file_fields['id']; ?>][id]" value="<?php echo $file_fields['id']; ?>"></input>
						<input type="file" class="file_input" name="wcufuploadedfile_<?php echo $file_fields['id']?>" <?php if($file_fields['types'] != '') echo 'accept="'.$file_fields['types'].'"';?> data-size="<?php echo $file_fields['size']*1048576; ?>" value="<?php echo $file_fields['size']*1048576; ?>"></input><strong>(<?php echo __('Max size: ', 'woocommerce-files-upload').$file_fields['size']; ?>MB)</strong>
						<div class="spacer2"></div>
				<?php   $render_upload_button = true;
					else: ?>
							<p><?php 
							    if(!isset($file_fields['message_already_uploaded']))
									_e('File already uploaded.', 'woocommerce-files-upload'); 
								else
									echo $file_fields['message_already_uploaded'];
								?></p>
						 <?php if($file_fields['user_can_delete']):?>
								<button class="button delete_button" data-id="<?php echo $file_fields['id'];?>">Delete it</button>
						<?php endif; ?>
		 <?php 		endif;
			  else://else disable stacking 
			   foreach($products_for_which_stacking_is_disabled as $product):
					//var_dump($product);
					$product_id = $product["item_meta"]['_product_id'][0];
					$product_name = $product['name'];
					$product_var_id = $product["item_meta"]['_variation_id'][0]==""? false:$product["item_meta"]['_variation_id'][0];
					$product_variation = null;
					
					if($product_var_id)	
					{
						$product_in_order = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $product ), $product );
						$variation = new WC_Product_Variation($product_var_id);
						$item_meta    = new WC_Order_Item_Meta( $product['item_meta'], $product_in_order );
						$product_id .= "-".$product_var_id;
						// $product_name .= " - ";
						//$product_name .= $item_meta->display(true, true); 
						
						 $product_name = $variation->get_title()." - ";	
						$attributes_counter = 0;
						foreach($variation->get_variation_attributes( ) as $attribute_name => $value){
							
							if($attributes_counter > 0)
								$product_name .= ", ";
							$meta_key = urldecode( str_replace( 'attribute_', '', $attribute_name ) ); 
							$product_name .= " ".wc_attribute_label( $meta_key, $product_in_order ).": ".$value;
							//$product_name .= wc_get_order_item_meta( $product_var_id, $attribute_name, array( 'fields' => 'names' )).": ".$value;
							$attributes_counter++;
						} 
						
						//$product_name = $variation->get_title()." ".implode( ', ', $variation->get_variation_attributes( ) );//$variation->get_formatted_name();
						
					}
					/* echo "******************************************</br>";
					var_dump($product_variation->get_attributes());
					echo "<br/>****************************************** end<br/><br/>"; */
			  ?>
				  <h4 style="margin-bottom:5px;"><?php  echo $file_fields['title'].' ('.$product_name.')'?></h4>
					<p><?php echo $file_fields['description'] ?></p>
					<?php if(!isset($file_order_metadata[$file_fields['id']."-".$product_id])): ?>
							<input type="hidden" name="wcuf[<?php echo $file_fields['id']."-".$product_id; ?>][title]" value="<?php echo $file_fields['title'].' ('.$product_name.')'; ?>"></input>
							<input type="hidden" name="wcuf[<?php echo $file_fields['id']."-".$product_id; ?>][id]" value="<?php echo $file_fields['id']."-".$product_id; ?>"></input>
							<input type="file" class="file_input" name="wcufuploadedfile_<?php echo $file_fields['id']."-".$product_id?>" <?php if($file_fields['types'] != '') echo 'accept="'.$file_fields['types'].'"';?> data-size="<?php echo $file_fields['size']*1048576; ?>" value="<?php echo $file_fields['size']*1048576; ?>"></input><strong>(<?php echo __('Max size: ', 'woocommerce-files-upload').$file_fields['size']; ?>MB)</strong>
							<div class="spacer2"></div>
					<?php   $render_upload_button = true;
						else: ?>
								<p><?php 
							    if(!isset($file_fields['message_already_uploaded']))
									_e('File already uploaded.', 'woocommerce-files-upload'); 
								else
									echo $file_fields['message_already_uploaded'];
								?></p>
							 <?php if($file_fields['user_can_delete']):?>
									<button class="button delete_button" data-id="<?php echo $file_fields['id']."-".$product_id;?>">Delete it</button>
							<?php endif; ?>
			 <?php 		endif;
			 
				endforeach;//products
			endif;//disable stacking
		endif;//can render
	endforeach; //upload fields
	
	if($render_upload_button): ?> 
		<div class="spacer"></div>
		<input id="submit_button" type="submit" class="button" value="<?php _e('Upload file(s)', 'woocommerce-files-upload'); ?>"></input>
		<div class="spacer"></div>
	<?php else: ?>
		<style>
		#upload-title
		{
			display:none;
		}
		</style>
	<? endif; ?>
</form>


<script> 
jQuery(document).ready(function()
{
	jQuery('.delete_button').on('click',function(event)
	{
		//console.log(jQuery(this).data('id'));
		event.preventDefault();
		jQuery.post( window.location.href , { type: "wcup_delete", id: jQuery(this).data('id') } ).done( function(){ window.location.href = window.location.href;});
	});
	
	jQuery('#submit_button').click( function(event) {
		//check whether browser fully supports all File API
		if (window.File && window.FileReader && window.FileList && window.Blob)
		{
			jQuery('.file_input').each(function(index, object)
			{
				//get the file size and file type from file input field
				if(jQuery(this)[0].files[0] != undefined)
				{
					var fsize = jQuery(this)[0].files[0].size;
					var ftype = jQuery(this)[0].files[0].type;
					var fname = jQuery(this)[0].files[0].name;
					
					
					
					if(fsize>jQuery(this).data('size')) //do something if file size more than 1 mb (1048576)
					{
						var size = fsize/1048576;
						size = size.toFixed(2);
						alert("File: "+fname+" (<?php _e('size:', 'woocommerce-files-upload'); ?> "+size+" MB) <?php _e('too big', 'woocommerce-files-upload'); ?>!");
						event.preventDefault();
					}/* else{
						alert("Type :"+ ftype +" | "+ fsize +" bites\n(File :"+fname+") You are good to go!");
					} */
				}
			});
		}else{
			alert("Please upgrade your browser, because your current browser lacks some new features we need!");
		}
	});
});
</script>