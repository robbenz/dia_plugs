<?php
class WCUF_AdminMenu
{
	public static  $WCUF_current_lang;
	public function WCUF_AdminMenu()
	{
		
	}
	public static function WCUF_switch_to_default_lang()
	{
		if(defined("ICL_LANGUAGE_CODE") && ICL_LANGUAGE_CODE != null)
		{
			global $sitepress;
			WCUF_AdminMenu::$WCUF_current_lang = ICL_LANGUAGE_CODE;
			$sitepress->switch_lang($sitepress->get_default_language());
		}
	}
	public static function WCUF_restore_current_lang()
	{
		if(defined("ICL_LANGUAGE_CODE") && ICL_LANGUAGE_CODE != null)
		{
			global $sitepress;
			$sitepress->switch_lang(WCUF_AdminMenu::$WCUF_current_lang);
		}
	}
	
	private function update_settings()
	{
		global $option_model;
		$file_metas = array();
		$last_id = $current_last_id = 0;
		if(isset($_REQUEST['wcup_file_meta']))
		{
			$counter = 0; 
			$current_last_id = 0; 
			$last_id = $option_model->get_option('wcuf_last_file_id');
			$last_id = !isset($last_id) ? 0 :$last_id;
			$ids_deleted = array(); //used for WPML
			for($i = 0; $i <= $last_id; $i++)
				$ids_deleted[$i] = true;
			
			foreach($_REQUEST['wcup_file_meta'] as $file_meta)
			{
				if(isset($file_meta['id']))
				{
					$ids_deleted[$file_meta['id']] = false;
					
					$category_ids = array();
					$enable_for = 'always';
					if($file_meta['enable_for'] != 'always' && isset($file_meta['categories'] ))
					{
						$category_ids =  $file_meta['categories'];
						$enable_for = isset($file_meta['enable_for']) ? $file_meta['enable_for'] : "";
					}
					
					$file_meta['user_can_delete'] = !isset($file_meta['user_can_delete']) ?  false:$file_meta['user_can_delete'];
					$file_meta['notify_admin'] = !isset($file_meta['notify_admin']) ?  false:$file_meta['notify_admin'];
					$file_meta['notify_attach_to_admin_email'] = !isset($file_meta['notify_attach_to_admin_email']) ?  false:$file_meta['notify_attach_to_admin_email'];
					$file_meta['disable_stacking'] = !isset($file_meta['disable_stacking']) ?  false:$file_meta['disable_stacking'];
					$file_meta['display_on_checkout'] = !isset($file_meta['display_on_checkout']) ?  false:$file_meta['display_on_checkout'];
					array_push($file_metas, array( "id"=> $file_meta['id'], //$counter++,
												  "title"=> $file_meta['title'], 
												  "description"=>isset($file_meta['description']) ? $file_meta['description']:"",
												  "message_already_uploaded"=>isset($file_meta['message_already_uploaded']) ? $file_meta['message_already_uploaded']:"",
												  "allow"=> 'allow',//$file_meta['allow'],
												  "types"=>isset($file_meta['types']) ? $file_meta['types']:null,
												  "size"=>$file_meta['size'],
												  "user_can_delete" => $file_meta['user_can_delete'],
												  "notify_admin" => $file_meta['notify_admin'],
												  "notify_attach_to_admin_email" => $file_meta['notify_attach_to_admin_email'],
												  "disable_stacking" => $file_meta['disable_stacking'],
												  "display_on_checkout" => $file_meta['display_on_checkout'],
												  "enable_for" => $enable_for,
												  "category_ids" => $category_ids
												  ));
					$current_last_id = isset($file_meta['id']) ? $file_meta['id'] : 0;
				}
			}
			$option_model->update_option( 'wcuf_files_fields_meta', $file_metas ); 
			//$option_model->unregister_wpml_strings( $ids_deleted); 
		}
		else
		{
			$option_model->delete_option( 'wcuf_files_fields_meta');
		}
		if($current_last_id > $last_id)
		  $option_model->update_option( 'wcuf_last_file_id', $current_last_id );
	}
	/* private function reset_data()
	{
		delete_option( 'wcuf_last_file_id');
		delete_option( 'wcuf_files_fields_meta');
	}  */
	public function render_page()
	{
		global $option_model;
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			$this->update_settings();
		
		$file_fields_meta = $option_model->get_fields_meta_data();
		//wcuf_var_dump($file_fields_meta );
		$last_id = $option_model->get_option( 'wcuf_last_file_id');
		if(!$last_id)
			$last_id = 0;
		else
			$last_id ++;
		$counter  = 0;
		
		
		ob_start();
		
		wp_enqueue_style( 'select2.css', wcuf_PLUGIN_PATH.'/css/select2.min.css' ); 
		wp_enqueue_style( 'wpuf-backend.css', wcuf_PLUGIN_PATH.'/css/wcuf-backend.css' ); 
		wp_enqueue_script( 'select2-js', wcuf_PLUGIN_PATH.'/js/select2.min.js', array('jquery') );
		
			
		?>
		<div id="icon-themes" class="icon32"><br></div> 
		<h2><?php _e('Uploads options', 'woocommerce-files-upload');?></h2>
		<?php if ($_SERVER['REQUEST_METHOD'] == 'POST') echo '<div id="message" class="updated"><p>' . __('Saved successfully.', 'woocommerce-files-upload') . '</p></div>'; ?>
		<div class="wrap">
			<h2><?php _e('Define how many uploads can be done in View Oder page.', 'woocommerce-files-upload');?></h2>
			<h3><b><?php _e('For every upload you can define some options as an HTML snippet, upload file size, allowed file types:', 'woocommerce-files-upload');?></b></h3>
			<br>
			<form action="" method="post"  style="padding-left:20px">
			<?php //settings_fields('wcuf_files_fields_meta_groups'); ?> 
				<button class="add_field_button button-primary"><?php _e('Add one more Upload', 'woocommerce-files-upload');?></button>
				<ul class="input_fields_wrap sortable">
				<?php if($file_fields_meta):
						foreach($file_fields_meta as $file_meta): 
						
						$file_meta['enable_for'] = !isset($file_meta['enable_for']) ?  'always':$file_meta['enable_for'];
						$file_meta['notify_admin'] = !isset($file_meta['notify_admin']) ?  false:$file_meta['notify_admin'];
						$file_meta['notify_attach_to_admin_email'] = !isset($file_meta['notify_attach_to_admin_email']) ?  false:$file_meta['notify_attach_to_admin_email'];
						$file_meta['message_already_uploaded'] = !isset($file_meta['message_already_uploaded']) ?  __('File already uploaded.', 'woocommerce-files-upload'):$file_meta['message_already_uploaded'];
						$selected_categories = !isset($file_meta['category_ids']) ? array():$file_meta['category_ids'];
						
						?>
						<li class="input_box">
							<label><?php _e('Title', 'woocommerce-files-upload');?></label>
							<input type ="hidden" name= "wcup_file_meta[<?php echo $counter ?>][id]" value="<?php echo $file_meta['id'] ?>" ></input>
							<input type="text" value="<?php echo $file_meta['title']; ?>" name="wcup_file_meta[<?php echo $counter ?>][title]"  placeholder=" "  size="80" required></textarea >
							<br/>
							<label><?php _e('Description (HTML code permitted)', 'woocommerce-files-upload');?></label>
							<textarea  class="upload_description"  rows="5" cols="80" name="wcup_file_meta[<?php echo $counter ?>][description]" placeholder="<?php _e('Description (you can use HTML code)', 'woocommerce-files-upload'); ?>"><?php echo $file_meta['description']; ?></textarea>
							<label><?php _e('Message for already uploaded file (HTML code permitted)', 'woocommerce-files-upload');?></label>
							<textarea  class="upload_description"  rows="5" cols="80" name="wcup_file_meta[<?php echo $counter ?>][message_already_uploaded]" placeholder="<?php _e('This message is displayed after file description only if a file have been uploaded (you can use HTML code)', 'woocommerce-files-upload'); ?>"><?php echo $file_meta['message_already_uploaded']; ?></textarea>
							<label><?php _e('Allowed file type(s)', 'woocommerce-files-upload');?></label>
						
							<input type="text" name="wcup_file_meta[<?php echo $counter ?>][types]" placeholder="<?php _e('File type(s), ex: .jpg,.bmp,.png leave empty to accept all file types. ', 'woocommerce-files-upload'); ?>" value="<?php echo $file_meta['types']; ?>" size="80"></input>
							<label><?php _e('File size (MB)', 'woocommerce-files-upload');?></label>
							<input type="number" min="1" name="wcup_file_meta[<?php echo $counter ?>][size]" value="<?php echo $file_meta['size']; ?>" required></input>
							<label><?php _e('Can user delete file?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[<?php echo $counter ?>][user_can_delete]" value="true" <?php if($file_meta['user_can_delete']) echo 'checked="checked"'?> ></input>
							
							<h3><?php _e('Checkout', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Display field on checkout page?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[<?php echo $counter ?>][display_on_checkout]" value="true" <?php if(isset($file_meta['display_on_checkout']) && $file_meta['display_on_checkout']) echo 'checked="checked"'?> ></input>
							
							<h3><?php _e('Notifications', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Notify admin via email when customer completed the upload?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[<?php echo $counter ?>][notify_admin]" value="true" <?php if($file_meta['notify_admin']) echo 'checked="checked"'?> ></input>
							
							<label style="margin-top:20px;"><?php _e('Attach uploaded file to admin notification email? (<strong>NOTE:</strong> this option works only if admin notification email option has been enabled</i>)', 'woocommerce-files-upload');?></label>
							<p><small><?php _e('Remember that some some server email provider will not receive emails with attachments bigger than 10MB (<a target="_blank" href="https://www.outlook-apps.com/maximum-email-size/">Gmail: 25MB, Outlook and Hotmail 10MB,...</a>)', 'woocommerce-files-upload'); ?></small></p>
							<input type="checkbox" name="wcup_file_meta[<?php echo $counter ?>][notify_attach_to_admin_email]" value="true" <?php if($file_meta['notify_attach_to_admin_email']) echo 'checked="checked"'?> ></input>
							
							<h3><?php _e('Filtering and stacking', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Multiple uploads: if in the order there are different products of the same category, there will be an upload field for every product type.<br/>(Default Settings: only one upload field per order and only if at least one products bought matches the filter criteria)', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[<?php echo $counter ?>][disable_stacking]" value="true" <?php if(isset($file_meta['disable_stacking']) && $file_meta['disable_stacking']) echo 'checked="checked"'?> ></input>
							
							<label style="margin-top:20px;"><?php _e('Filtering criteria: This upload field will be', 'woocommerce-files-upload');?></label>							
							<select  class="upload_type" data-id="<?php echo $counter ?>" name="wcup_file_meta[<?php echo $counter ?>][enable_for]">
							  <option value="always" <?php if($file_meta['enable_for'] == 'always') echo 'selected'; ?>><?php _e('Enabled for every order (or item. Depends on "Multiple uploads" option)', 'woocommerce-files-upload');?></option>
							  <!-- <option value="product" <?php if($file_meta['enable_for'] == 'product') echo 'selected'; ?>><?php _e('Enabled for selected products', 'woocommerce-files-upload');?></option> -->
							  <option value="categories" <?php if($file_meta['enable_for'] == 'categories') echo 'selected'; ?>><?php _e('Enabled for selected categories', 'woocommerce-files-upload');?></option>
							  <option value="categories_children" <?php if($file_meta['enable_for'] == 'categories_children') echo 'selected'; ?>><?php _e('Enabled for selected categories and all its children', 'woocommerce-files-upload');?></option>
							  <option value="disable_categories"  <?php if($file_meta['enable_for'] == 'disable_categories') echo 'selected'; ?>><?php _e('Disabled selected categories', 'woocommerce-files-upload');?></option>
							  <option value="disable_categories_children"  <?php if($file_meta['enable_for'] == 'disable_categories_children') echo 'selected'; ?>><?php _e('Disable for selected categories and all its children', 'woocommerce-files-upload');?></option>
							</select>
							<div class="spacer" ></div>
							<div class="upload_categories_box" id='upload_categories_box<?php echo $counter ?>'>
							<?php  
								WCUF_AdminMenu::WCUF_switch_to_default_lang();
								$select_cats = wp_dropdown_categories( array( 'echo' => 0, 'hide_empty' => 0, 'taxonomy' => 'product_cat', 'hierarchical' => 1) );
								WCUF_AdminMenu::WCUF_restore_current_lang();
								
								if(count($selected_categories) > 0)
								{
									//set selected (if exists)
									foreach($selected_categories as $category_id)
										$select_cats = str_replace('value="'.$category_id.'"', 'value="'.$category_id.'" selected', $select_cats);
										
								}
								// var_dump($select_cats);
								$select_cats = str_replace( "name='cat' id='cat' class='postform'", "style='width:200px;' id='upload_type_id".$counter."' name='wcup_file_meta[".$counter."][categories][]' class='js-multiple' multiple='multiple' ", $select_cats ); 
								 echo $select_cats; 
								/* wp_dropdown_categories( array( 'class' => 'js-multiple',  'multiple' =>'multiple') );*/
								 ?>
							</div>
							<div class="spacer" ></div>
							<button class="remove_field button-secondary"><?php _e('Remove upload', 'woocommerce-files-upload');?></button>
						</li>
				<?php $counter++; endforeach; else: ;?>
					<li class="input_box">
							<label>Title</label>
							<input type="text" name="wcup_file_meta[0][title]" placeholder=" "  size="80" required></input>
							<br/>
							<label><?php _e('Title', 'woocommerce-files-upload');?></label>
							<!--<p align="right">
								<a class="button toggleVisual">Visual</a>
								<a class="button toggleHTML">HTML</a>
							</p>-->
							<input type ="hidden" name= "wcup_file_meta[0][id]" value="<?php echo ($last_id+1); ?>" ></input>
							<label><?php _e('Description (HTML code permitted)', 'woocommerce-files-upload');?></label>
							<textarea class="upload_description" name="wcup_file_meta[0][description]" rows="5" cols="80" placeholder="<?php _e('Description (you can use HTML code)', 'woocommerce-files-upload'); ?>"></textarea >
							<label><?php _e('Message for already uploaded file (HTML code permitted)', 'woocommerce-files-upload');?></label>
							<textarea  class="upload_description"  rows="5" cols="80" name="wcup_file_meta[0][message_already_uploaded]" placeholder="<?php _e('This message is displayed after file description only if a file have been uploaded (you can use HTML code)', 'woocommerce-files-upload'); ?>"><?php _e('File already uploaded.', 'woocommerce-files-upload') ?></textarea>
							
							<label><?php _e('Allowed file type(s)', 'woocommerce-files-upload');?></label>
							<input type="text" name="wcup_file_meta[0][types]"  placeholder="<?php _e('File type(s), ex: .jpg,.bmp,.png leave empty to accept all file types. ', 'woocommerce-files-upload'); ?>" size="80" ></input>
							<label><?php _e('File size (MB)', 'woocommerce-files-upload');?></label>
							<input type="number" min="1" name="wcup_file_meta[0][size]" value="20" required></input>
							<label><?php _e('Can user delete file?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[0][user_can_delete]" value="true" checked="checked"></input>
							
							<h3><?php _e('Checkout', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Display field on checkout page?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[0][display_on_checkout]" value="true"></input>
							
							<h3><?php _e('Notifications', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Notify admin via mail when customer completed the upload?', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[0][notify_admin]" value="true"></input>
							
							<label style="margin-top:20px;"><?php _e('Attach uploaded file to admin notification email? (<strong>NOTE:</strong> this option works only if admin notification email option has been enabled)', 'woocommerce-files-upload');?></label>
							<p><small><?php _e('Remember that some some server email provider will not receive emails with attachments bigger than 10MB (<a target="_blank" href="https://www.outlook-apps.com/maximum-email-size/">Gmail: 25MB, Outlook and Hotmail 10MB,...</a>)', 'woocommerce-files-upload'); ?></small></p>
							<input type="checkbox" name="wcup_file_meta[0][notify_attach_to_admin_email]" value="true" ></input>
							
							<h3><?php _e('Filtering and stacking', 'woocommerce-files-upload');?></h3>
							<label style="margin-top:20px;"><?php _e('Multiple uploads: if in the order there are different products of the same category, there will be an upload field for every product type.<br/>(Default Settings: only one upload field per order and only if at least one products bought matches the filter criteria)', 'woocommerce-files-upload');?></label>
							<input type="checkbox" name="wcup_file_meta[0][disable_stacking]" value="true" ></input>
							<label style="margin-top:20px;"><?php _e('Filtering criteria: This upload field will be', 'woocommerce-files-upload');?></label>
							<select  class="upload_type" data-id="0" name="wcup_file_meta[0][enable_for]">
							  <option value="always" selected><?php _e('Enabled for every order (or item. Depends on "Multiple uploads" option)', 'woocommerce-files-upload');?></option>
							  <option value="categories" ><?php _e('Enabled for selected categories', 'woocommerce-files-upload');?></option>
							  <option value="categories_children" ><?php _e('Enabled for selected categories and all its children', 'woocommerce-files-upload');?></option>
							  <option value="disable_categories" ><?php _e('Disabled selected categories', 'woocommerce-files-upload');?></option>
							  <option value="disable_categories_children" ><?php _e('Disabled for selected categories and all its children', 'woocommerce-files-upload');?></option>
							</select>
							
							<div class="spacer" ></div>
							<div class="upload_categories_box" id='upload_categories_box0'>
							<?php 
								  WCUF_AdminMenu::WCUF_switch_to_default_lang();
								  $select_cats = wp_dropdown_categories( array( 'echo' => 0, 'hide_empty' => 0, 'taxonomy' => 'product_cat', 'hierarchical' => 1) );
								  WCUF_AdminMenu::WCUF_restore_current_lang();
								  $select_cats = str_replace( "name='cat' id='cat' class='postform'", "style='width:200px;' id='upload_type_id0' name='wcup_file_meta[0][categories][]' class='js-multiple' multiple='multiple' ", $select_cats ); 
								 echo $select_cats; 
								/* wp_dropdown_categories( array( 'class' => 'js-multiple',  'multiple' =>'multiple') );*/
								 ?>
							</div>
							<div class="spacer" ></div>
							<button class="remove_field button-secondary"><?php _e('Remove upload', 'woocommerce-files-upload');?></button>
					</li>
				<?php endif ?>
				</ul>
				<script>
				jQuery(document).ready(function() 
				{
						var max_fields      = 50; //maximum input boxes allowed
						var wrapper         = jQuery(".input_fields_wrap"); //Fields wrapper
						var add_button      = jQuery(".add_field_button"); //Add button ID
						var x = <?php echo ($last_id+1); ?>; //initlal text box count
						
						/* jQuery( "#sortable" ).sortable();
						jQuery( "#sortable" ).disableSelection(); */
						jQuery(".js-multiple").select2({'width':300});
						jQuery(".upload_type").on('change', setSelectBoxVisibility);
						jQuery(".upload_type").trigger('change');
						
						/* jQuery('.upload_type').each(function(index, value)
						{
							console.log(jQuery(this).val());
							//jQuery("#upload_categories_box"+jQuery(this).data('id')).show();
						}); */
						
						function setSelectBoxVisibility(event)
						{
							if(jQuery(event.target).val() != 'always')
							   {
								   jQuery("#upload_categories_box"+jQuery(event.target).data('id')).show();
							   }
							   else
								   jQuery("#upload_categories_box"+jQuery(event.target).data('id')).hide();
						}

						jQuery(add_button).click(function(e)
						{ //on add input button click
							e.preventDefault();
							if(x < max_fields){ //max input box allowed
								x++; //text box increment
								
								jQuery(wrapper).append(getHtmlTemplate(x)); //add input box
							}
							jQuery(".js-multiple").select2({'width':300});
							jQuery(".upload_type").on('change', setSelectBoxVisibility);
						});
					   
						jQuery(wrapper).on("click",".remove_field", function(e)
						{ //user click on remove text
							e.preventDefault(); 
							jQuery(this).parent('.input_box').remove(); 
							x--;
						})
				});
				function getHtmlTemplate(index)
				{
					var categories = '<?php
								  WCUF_AdminMenu::WCUF_switch_to_default_lang();
								  $select_cats = wp_dropdown_categories( array( 'echo' => 0,'hide_empty' => 0, 'taxonomy' => 'product_cat', 'hierarchical' => 1) );
								  WCUF_AdminMenu::WCUF_restore_current_lang();
								  $select_cats = str_replace( "name='cat' id='cat' class='postform'", 'style="width:200px;" id="upload_type_id_index_to_replace" name="wcup_file_meta[_index_to_replace][categories][]" class="js-multiple" multiple="multiple" ', $select_cats ); 
								 $select_cats = str_replace("'", '', $select_cats);
								echo str_replace("\n", '', $select_cats); ?>'; 
								 
					//categories = categories.replace("_index_to_replace", index);
					categories = categories.replace(/_index_to_replace/g, index);
					var template = '<li class="input_box">';
							template += '<label><?php _e('Title', 'woocommerce-files-upload');?> </label>';
							template += '<input type ="hidden" name= "wcup_file_meta['+index+'][id]" value="'+index+'" ></input>';
							template += '<input type="text"  name="wcup_file_meta['+index+'][title]"  placeholder=" "  size="80" required></textarea >';
							template += '<br/>';
							template += '<label><?php _e('Description (HTML code permitted)', 'woocommerce-files-upload');?> </label>';
							template += '<textarea  class="upload_description"  rows="5" cols="80" name="wcup_file_meta['+index+'][description]" placeholder="<?php _e('Description (you can use HTML code)', 'woocommerce-files-upload'); ?>"></textarea>';
							template += '<label><?php _e('Message for already uploaded file (HTML code permitted)', 'woocommerce-files-upload');?></label>';
							template += '<textarea  class="upload_description"  rows="5" cols="80" name="wcup_file_meta['+index+'][message_already_uploaded]" placeholder="<?php _e('This message is displayed after file description only if a file have been uploaded (you can use HTML code)', 'woocommerce-files-upload'); ?>"><?php _e('File already uploaded.', 'woocommerce-files-upload') ?></textarea>';
							template += '<label><?php _e('Allowed file type(s)', 'woocommerce-files-upload');?></label>';
							//template += '<select  name="wcup_file_meta['+index+'][allow]">';
							//template += '  <option value="allow" >Allow</option>';
							//template += '  <option value="disallow" >Disallow</option>';
							//template += '</select>';
							template += '<input type="text" name="wcup_file_meta['+index+'][types]" placeholder="<?php _e('File type(s), ex: .jpg,.bmp,.png leave empty to accept all file types. ', 'woocommerce-files-upload'); ?>" size="80" ></input>';
							template += '<label><?php _e('File size (MB)', 'woocommerce-files-upload');?></label>';
							template += '<input type="number" min="1" name="wcup_file_meta['+index+'][size]" value="20" required></input>';
							template += '<label><?php _e('Can user delete file?', 'woocommerce-files-upload');?></label>';
							template += '<input type="checkbox" name="wcup_file_meta['+index+'][user_can_delete]" value="true" checked="checked"></input>';
							
							template += '<h3><?php _e('Checkout', 'woocommerce-files-upload');?></h3>';
							template += '<label style="margin-top:20px;"><?php _e('Display field on checkout page?', 'woocommerce-files-upload');?></label>';
							template += '<input type="checkbox" name="wcup_file_meta['+index+'][display_on_checkout]" value="true"></input>';
							
							template += '<h3><?php _e('Notifications', 'woocommerce-files-upload');?></h3>';
							template += '<label style="margin-top:20px;"><?php _e('Notify admin via mail when customer completed the upload?', 'woocommerce-files-upload');?></label>';
							template += '<input type="checkbox" name="wcup_file_meta['+index+'][notify_admin]" value="true" > </input>';
							
							template += '<h3><?php _e('Filtering and stacking', 'woocommerce-files-upload');?></h3>';
							template += '<label style="margin-top:20px;"><?php _e('Attach uploaded file to admin notification email? (<strong>NOTE:</strong> this option works only if admin notification email option has been enabled)', 'woocommerce-files-upload');?></label>';
							template += '<p><small><?php _e('Remember that some some server email provider will not receive emails with attachments bigger than 10MB (<a target="_blank" href="https://www.outlook-apps.com/maximum-email-size/">Gmail: 25MB, Outlook and Hotmail 10MB,...</a>)', 'woocommerce-files-upload'); ?></small></p>';
							template += '<input type="checkbox" name="wcup_file_meta['+index+'][notify_attach_to_admin_email]" value="true" ></input>';
							
							template += '<label style="margin-top:20px;"><?php _e('Multiple uploads: if in the order there are different products of the same category, there will be an upload field for every product type.<br/>(Default Settings: only one upload field per order and only if at least one products bought matches the filter criteria)', 'woocommerce-files-upload');?></label>';
							template += '<input type="checkbox" name="wcup_file_meta['+index+'][disable_stacking]" value="true" ></input>'; 
							
							
							template += '<label style="margin-top:20px;"><?php _e('Filtering criteria: This upload field will be', 'woocommerce-files-upload');?></label>';
							template += '<select  class="upload_type" data-id="'+index+'" name="wcup_file_meta['+index+'][enable_for]">';
							template += '  <option value="always" selected><?php _e('Enabled for every order (or item. Depends on "Multiple uploads" option)', 'woocommerce-files-upload');?></option>';
							template += '  <option value="disable_categories" ><?php _e('Enabled for selected categories', 'woocommerce-files-upload');?></option>';
							template += '  <option value="disable_categories_children" ><?php _e('Enabled for selected categories (and all its children)', 'woocommerce-files-upload');?></option>';
							template += '  <option value="disable_categories"  ><?php _e('Disabled selected categories', 'woocommerce-files-upload');?></option>';
							template += '  <option value="disable_categories_children" ><?php _e('Disabled for selected categories (and all its children)', 'woocommerce-files-upload');?></option>';
							template += '</select>';
							template += '<div class="spacer" ></div>';
							template += '<div class="upload_categories_box" id="upload_categories_box'+index+'">';
							template += categories;
							template += '</div>';
							template += '<div class="spacer"></div>';
							template += '<button class="remove_field button-secondary"><?php _e('Remove upload', 'woocommerce-files-upload');?></button>';
						template += '</li>';			
					return template;
				}
				</script>
				<button class="add_field_button button-primary"><?php _e('Add one more Upload', 'woocommerce-files-upload');?></button>
				<div class="spacer"></div><div class="spacer"></div>
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wshipinfo-patsatech'); ?>" />
				</p>
			</form>
		</div>
		<?php
		echo ob_get_clean();
	}
}
?>