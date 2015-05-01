<?php       
        global $woocommerce;
	    
        $this->countries     = new WC_Countries();

	    $fields              = $this->countries->get_address_fields( $this->countries->get_base_country(),'billing_');
		$billing_settings    = $this->billing_settings;
		$billing_settings    = array_filter($billing_settings);
		$required_slugs      = 'billing_address_1,billing_city,billing_state,billing_postcode';
		$core_fields         = 'billing_country,billing_first_name,billing_last_name,billing_company,billing_address_1,billing_address_2,billing_city,billing_state,billing_postcode,billing_email,billing_phone';
		$country_fields      = 'billing_country,billing_state';
		$address2_field      = 'billing_address_2';
	
	    $noticerowno = 1;
		 ?>
       
		 <center>	   
         <div class="panel-group pcfme-sortable-list" id="accordion" >
		<?php if (isset($billing_settings) && (sizeof($billing_settings) >= 1)) { 
		    
			foreach ($billing_settings as $key =>$field) { 
		       $this->show_fields_form($key,$field,$noticerowno,$this->billing_settings_key,$required_slugs,$core_fields,$country_fields,$address2_field);
		       $noticerowno++;
		    } 
			
		 } else {
		 
		 
		    foreach ($fields as $key =>$field) { 
               $this->show_fields_form($key,$field,$noticerowno,$this->billing_settings_key,$required_slugs,$core_fields,$country_fields,$address2_field);
		       $noticerowno++;
		 }
		 }
		  ?>
		 <script>
		 var hash= <?php echo $noticerowno; ?>;
		 $(".checkout_field_width").chosen({width: "250px" ,"disable_search": true});  
         $(".row-validate-multiselect").chosen({width: "250px" });  
         $(".checkout_field_type").chosen({width: "250px" }); 
		 $(".checkout_field_validate").chosen({width: "250px" }); 
		 $(".pcfme-country-select").chosen({width: "200px" }); 
		 
		 </script>
		  
		 </div>
        
         <div class="buttondiv">		  
			    <input type="button" id="add-billing-field" class="btn button-primary" style="float:left;" value="<?php _e('Add Billing Field','pcn'); ?>">
			    <input type="button" style="float:right;" id="restore-billing-fields" class="btn button-primary" value="<?php _e('Restore Billing Fields','pcn'); ?>">
		 </div>
		 </center> <?php
	
         $this->show_new_form();
