<?php        
       global $woocommerce;
         
       
        $this->countries    = new WC_Countries();
	    
	    
		
		$additional_settings  = $this->additional_settings;
		$additional_settings  = array_filter($additional_settings);
		$core_fields          = '';
		$country_fields       = '';
		$address2_field       = '';

		$requiredadditional_slugs = '';
		
		
		$noticerowno3 = 1;
		 ?>
		 <center>
		 <div class="panel-group pcfme-sortable-list" id="accordion" >
		<?php if (isset($additional_settings) && (sizeof($additional_settings) >= 1)) { 
		    foreach ($additional_settings as $key =>$field) { 
		      $this->show_fields_form($key,$field,$noticerowno3,$this->additional_settings_key,$requiredadditional_slugs,$core_fields,$country_fields,$address2_field);
		    $noticerowno3++;
		 } 
		 } 
		  ?>
		<script>
		 var hash= <?php echo $noticerowno3; ?>;
		 $(".checkout_field_width").chosen({width: "250px" ,"disable_search": true});  
         $(".row-validate-multiselect").chosen({width: "250px" });  
         $(".checkout_field_type").chosen({width: "250px" }); 
		 $(".checkout_field_validate").chosen({width: "250px" }); 
		 </script>
		</div>
		<div class="additional-buttondiv">
         <input type="button" id="add-additional-field" class="btn button-primary" style="float:left;" value="<?php _e('Add additional Field','pcn'); ?>">
	    </div>
		</center> <?php
		
	     $this->show_new_form();