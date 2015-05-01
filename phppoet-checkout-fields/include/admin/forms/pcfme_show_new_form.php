           <div class="panel-group panel panel-default checkoutfield pcfme_list_item" style="width:90%; display:none;">
           <div class="panel-heading">

           <table class="heading-table">
			<tr>
			    <td width="20%">
			     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="">
                  <span class="glyphicon glyphicon-edit pull-left"></span>
			     </a>
			    </td>
			    
				<td width="30%">
	              <label  class="new-field-label"></label>
        
                </td>
	            <td width="30%">
	  	          <input type="text" placeholder="<?php _e('Placeholder Text','pcn'); ?>">
	            </td>
			 
			    <td width="20%">
		          <span class="glyphicon glyphicon-remove-circle pull-right "></span>
		         </td>
                </tr>
		    </table>

           </div>
           <div id="" class="panel-collapse collapse">
			  <table class="table"> 
			   
			 
			   
			   <tr>
	           <td><label for="<?php echo $field; ?>_type"><?php _e('Field Type','pcn'); ?></label></td>
		       <td>
		          <select class="checkout_field_type" name="" >
			        <option value="text"  ><?php _e('Text','pcn'); ?></option>
			        <option value="textarea" ><?php _e('Textarea','pcn'); ?></option>
			        <option value="password" ><?php _e('Password','pcn'); ?></option>
			        <option value="checkbox" ><?php _e('Checkbox','pcn'); ?></option>
			        <option value="pcfmeselect" ><?php _e('Select','pcn'); ?></option>
			        <option value="multiselect"><?php _e('Multi Select','pcn'); ?></option>
			        <option value="radio" ><?php _e('Radio Select','pcn'); ?></option>
			        <option value="datepicker" ><?php _e('Date Picker','pcn'); ?></option>
					
			       </select>
		       </td>
	           </tr>
			   <tr>
                <td><label><?php  _e('Label','pcn'); ?></label></td>
	            <td><input type="text" class="checkout_field_label" name="" value="" size="35"></td>
               </tr>
			   
			
			   
			   <tr>
	           <td><label><?php _e('Class','pcn'); ?></label></td>
		       <td>
		       <select class="checkout_field_width" name="">
			    
				<option value="form-row-wide" ><?php _e('Full Width','pcn'); ?></option>
			    <option value="form-row-first" ><?php _e('First Half','pcn'); ?></option>
			    <option value="form-row-last" ><?php _e('Second Half','pcn'); ?></option>
				
				
			     
			   </select>
		       </td>
	           </tr>
			   
			   
		       <tr>
                <td><label ><?php  _e('Required','pcn'); ?></label></td>
                <td><input class="checkout_field_required" type="checkbox" name=""  value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label><?php  _e('Clearfix','pcn'); ?></label></td>
                <td><input class="checkout_field_clear" type="checkbox" name="" value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label for="<?php echo $field; ?>_label"><?php  _e('Placeholder ','pcn'); ?></label></td>
	            <td><input type="text" class="checkout_field_placeholder" name="" value="" size="35"></td>
               </tr>
			   
			   
			   <tr class="add-field-options" style="">
	           <td>
		         <label><?php _e('Options','pcn'); ?></label>
		       </td>
		       <td>
		       <input type="text" class="checkout_field_options" name="" placeholder="<?php _e('Separated by comma. For Example: option1,option2','pcn'); ?>" value="" size="35">
		       </td>
	           </tr>
			   
			 
			   
			
			   
			   <tr>
                <td><label><?php  _e('Validate','pcn'); ?></label></td>
	            <td>
		           <select name="" class="checkout_field_validate" multiple>
			         <option value="state" ><?php _e('state','pcn'); ?></option>
			         <option value="postcode" ><?php _e('postcode','pcn'); ?></option>
			         <option value="email" ><?php _e('email','pcn'); ?></option>
			         <option value="phone" ><?php _e('phone','pcn'); ?></option>
			       </select>
		       </td>
	 
               </tr>
			   
			   <tr>
                <td><label ><?php  _e('Show field detail on woocommerce order email and order edition page','pcn'); ?></label></td>
                <td><input class="checkout_field_orderedition" type="checkbox" name=""  value="1"></td>
			   </tr>
			   
			   <tr class="disable_datepicker_tr" style="display:none;">
                <td><label ><?php  _e('Disable Past Date Selection In Datepicker','pcn'); ?></label></td>
                <td><input class="checkout_field_disable_past_dates" type="checkbox" name=""  value="1"></td>
			   </tr>
			   
			   </table>
		   </div>
        </div>