<!--<style>
.spacer
{
	display:block; height:10px;
}
.spacer2
{
	display:block; height:25px;
}
</style>-->

<!--<h2 id="upload-title"><?php _e('Upload files', 'woocommerce-files-upload'); ?></h2> -->

<input type="hidden" value="yes" name="wcuf-uploading-data"></input>
<div id="wcuf-files-box"></div>
<?php 
wp_nonce_field('wcuf_checkout_upload', 'wcuf_attachment_nonce');

foreach($file_fields_groups as $file_fields):  
	
		global $sitepress;
		$enable_for = isset($file_fields['enable_for']) ? $file_fields['enable_for']:'always';
		$display_on_checkout = isset($file_fields['display_on_checkout']) ? $file_fields['display_on_checkout']:false;
		$disable_stacking = isset($file_fields['disable_stacking']) ? (bool)$file_fields['disable_stacking']:false;
		$selected_categories = isset($file_fields['category_ids']) ? $file_fields['category_ids']:array();
		$all_products_cats_ids = array();
		$products_for_which_stacking_is_disabled = array();
		$can_render = $enable_for == 'always' ? true:false;
		if($display_on_checkout)
		{
			if(($enable_for === 'always' && $disable_stacking) || $enable_for !== 'always' && count($selected_categories) > 0)
			{
				//for every product in the order, look for its categories and parent categories ids
				WCUF_AdminMenu::WCUF_switch_to_default_lang();
				foreach($cart_items as $product)
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
    <!--BENZ--> <div id="purchase-order-upload-wrap">
				<h4><?php  echo $file_fields['title'] ?></h4>
				<p><?php echo $file_fields['description'] ?></p>
				<?php if(!isset($file_order_metadata[$file_fields['id']])): ?>
					
						<input type="hidden" name="wcuf[<?php echo $file_fields['id']; ?>][title]" value="<?php echo $file_fields['title']; ?>"></input>
						<input type="hidden" name="wcuf[<?php echo $file_fields['id']; ?>][id]" value="<?php echo $file_fields['id']; ?>"></input>
						<input type="hidden" id="wcuf-filename-<?php echo $file_fields['id']; ?>" name="wcuf[<?php echo $file_fields['id']; ?>][file_name]" value=""></input>
						<input type="file"  data-id="<?php echo $file_fields['id']; ?>" multiple="true" class="wcuf_file_input" name="wcufuploadedfile_<?php echo $file_fields['id']?>" <?php if($file_fields['types'] != '') echo 'accept="'.$file_fields['types'].'"';?> data-size="<?php echo $file_fields['size']*1048576; ?>" value="<?php echo $file_fields['size']*1048576; ?>"></input><br /><strong>(<?php echo __('Max size: ', 'woocommerce-files-upload').$file_fields['size']; ?>MB)</strong>
						<div class="spacer2"></div></div>
					
			<?php endif;
				  else://else disable stacking 
				   foreach($products_for_which_stacking_is_disabled as $product):
						//var_dump($product);
						$product_id = $product["product_id"];
						$product_data = $product["data"];
						$product_name = $product_data->post->post_title;
						$product_var_id = $product["variation_id"]== "" ? false:$product["variation_id"];
						$product_variation = null;
						
						if($product_var_id)	
						{
							$_product = wc_get_product( $product_var_id );
							$_item = apply_filters( 'woocommerce_get_product_from_item', $_product, $product, null );
							
							$product_in_order = apply_filters( 'woocommerce_order_item_product', $_item , $product );
							$variation = new WC_Product_Variation($product_var_id);
							$item_meta    = new WC_Order_Item_Meta( $product, $product_in_order );
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
						}
				  ?>
					  <h4 style="margin-bottom:5px;"><?php  echo $file_fields['title'].' ('.$product_name.')'?></h4>
						<p><?php echo $file_fields['description'] ?></p>
						<?php if(!isset($file_order_metadata[$file_fields['id']."-".$product_id])): ?>
							
								<input type="hidden" name="wcuf[<?php echo $file_fields['id']."-".$product_id; ?>][title]" value="<?php echo $file_fields['title'].' ('.$product_name.')'; ?>"></input>
								<input type="hidden" name="wcuf[<?php echo $file_fields['id']."-".$product_id; ?>][id]" value="<?php echo $file_fields['id']."-".$product_id; ?>"></input>
								<input type="hidden" id="wcuf-filename-<?php echo $file_fields['id']."-".$product_id; ?>" name="wcuf[<?php echo $file_fields['id']."-".$product_id; ?>][file_name]" value=""></input>
								<input type="file"   data-id="<?php echo $file_fields['id']."-".$product_id; ?>" class="wcuf_file_input" name="wcufuploadedfile_<?php echo $file_fields['id']."-".$product_id?>" <?php if($file_fields['types'] != '') echo 'accept="'.$file_fields['types'].'"';?> data-size="<?php echo $file_fields['size']*1048576; ?>" value="<?php echo $file_fields['size']*1048576; ?>"></input><strong>(<?php echo __('Max size: ', 'woocommerce-files-upload').$file_fields['size']; ?>MB)</strong>
								<div class="spacer2"></div>
							
						<?php 
							endif;
					endforeach;//products
				endif;//disable stacking
			endif;//can render
		}
	endforeach; //upload field 
?> 
<script> 
jQuery(document).ready(function()
{
	if (window.File && window.FileReader && window.FileList && window.Blob) 
	{
		jQuery('.wcuf_file_input').on('change' ,wcuf_encode_file);
	} 
	else {
		alert("<?php _e('The File APIs are not fully supported in this browser.', 'woocommerce-files-upload'); ?>");
	}
		
	jQuery('#place_order').live('click', function(event) 
	{
		
		/* var formData = new FormData();
		jQuery('form input[type="file"]').each(function() 
		{
			if (jQuery(this).val() != '') 
				formData.append(jQuery(this).attr('name'), jQuery(this)[0].files[0]);
			
		}); 
		jQuery('form input:not([type="file"]):not([type="submit"]).wcuf_file_input').each(function() 
		{
			formData.append(jQuery(this).attr('name'), jQuery(this).val() );
		}); */
	
		//check whether browser fully supports all File API
		if (window.File && window.FileReader && window.FileList && window.Blob)
		{
			jQuery('.wcuf_file_input').each(function(index, object)
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
						event.stopImmediatePropagation();
						return false;
					}
					// else
					{
						//jQuery('form.woocommerce-checkout.checkout').addClass( 'processing' );
						
					}
				}
			});
		}else{
			alert("<?php _e('Please upgrade your browser, because your current browser lacks some new features we need!.', 'woocommerce-files-upload'); ?>");
		}
	});
});
</script>