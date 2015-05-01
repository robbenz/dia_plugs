<?php
class pcfme_add_order_meta_class {
     
	 
	 private $billing_settings_key = 'pcfme_billing_settings';
	 private $shipping_settings_key = 'pcfme_shipping_settings';
	 private $additional_settings_key = 'pcfme_additional_settings';
     
	 public function __construct() {
	 
	      
	      add_filter('woocommerce_checkout_update_order_meta', array( &$this, 'update_order_meta' ) );
	      add_filter('woocommerce_admin_order_data_after_billing_address', array( &$this, 'data_after_billing_address' ) );
	      add_filter('woocommerce_email_order_meta', array( &$this, 'woocommerce_custom_new_order_templace' )  );
	      
	 }
	 
	 
	 	 public function update_order_meta($order_id) {
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
		   $additional_fields   = (array) get_option( $this->additional_settings_key );
	       
		   
		   
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			   
				   if (isset($billing_field['orderedition'])) {
				     if ( ! empty( $_POST[$billingkey] ) ) {
						 
						if (is_array($_POST[$billingkey]))  {
							$billingkeyvalue = implode(',', $_POST[$billingkey]);
						} else {
							$billingkeyvalue = $_POST[$billingkey];
						}
						 
                        update_post_meta( $order_id, $billingkey, sanitize_text_field( $billingkeyvalue ) );
                       } 
				   }
				
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if (isset($shipping_field['orderedition'])) {
				     if ( ! empty( $_POST[$shippingkey] ) ) {
						 
						if (is_array($_POST[$shippingkey]))  {
							$shippingkeyvalue = implode(',', $_POST[$shippingkey]);
						} else {
							$shippingkeyvalue = $_POST[$shippingkey];
						}
						
                        update_post_meta( $order_id, $shippingkey, sanitize_text_field( $shippingkeyvalue ) );
                       } 
				   }
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {
		   	    if (isset($additional_field['orderedition'])) {
				     if ( ! empty( $_POST[$additionalkey] ) ) {
						 
						if (is_array($_POST[$additionalkey]))  {
							$additionalkeyvalue = implode(',', $_POST[$additionalkey]);
						} else {
							$additionalkeyvalue = $_POST[$additionalkey];
						}
						
                        update_post_meta( $order_id, $additionalkey, sanitize_text_field( $additionalkeyvalue ) );
                       } 
				   }
		   }
		   
		   
	       
	 }
	 
	 	 public function data_after_billing_address($order)  {
	       global $woocommerce;
	      
		   
		   
		   $billing_fields      = (array) get_option( $this->billing_settings_key );
		   $shipping_fields     = (array) get_option( $this->shipping_settings_key );
           $additional_fields   = (array) get_option( $this->additional_settings_key );
		   
		   
		  
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			    
				  if (isset($billing_field['orderedition'])) {
				     echo '<p><strong>'.__(''.$billing_field['label'].'').':</strong> ' . get_post_meta( $order->id, $billingkey, true ) . '</p>';
				   }
				
			 }
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if (isset($shipping_field['orderedition'])) {
				     echo '<p><strong>'.__(''.$shipping_field['label'].'').':</strong> ' . get_post_meta( $order->id, $shippingkey, true ) . '</p>';
				   }
				
			 }
		   

		    foreach ($additional_fields as $additionalkey=>$additional_field) {
		   	    if (isset($additional_field['orderedition'])) {
				     echo '<p><strong>'.__(''.$additional_field['label'].'').':</strong> ' . get_post_meta( $order->id, $additionalkey, true ) . '</p>';
                 }
		   }
	       
	 }
	 
	 public function woocommerce_custom_new_order_templace () {
          
		   $billing_fields                = (array) get_option( $this->billing_settings_key );
		   $shipping_fields               = (array) get_option( $this->shipping_settings_key );
		   $additional_fields             = (array) get_option( $this->additional_settings_key );
		   
	
		   
		     foreach ($billing_fields as $billingkey=>$billing_field) {
			    
				   if (isset($billing_field['orderedition'])) {
				     if ( ! empty( $_POST[$billingkey] ) ) {
				     
				        if (is_array($_POST[$billingkey])) {
				         
				          $arrayvalue ='';
				         foreach($_POST[$billingkey] as $arrayval) {
				          $arrayvalue.= ''.$arrayval.',';
				         }
				          $arrayvalue = substr_replace($arrayvalue, "", -1);
				         echo '<br /><strong>'.$billing_field['label'].'</strong> :'.$arrayvalue.'<br />';
				        } else {
                                         echo '<br /><strong>'.$billing_field['label'].'</strong> :'.$_POST[$billingkey].'<br />';
                                        
                                        }
                                       
                                       
                                       } 
				   }
				
			 }
		   
		   
		   
		   
		     foreach ($shipping_fields as $shippingkey=>$shipping_field) {
			    
				   if (isset($shipping_field['orderedition'])) {
				      if ( ! empty( $_POST[$shippingkey] ) ) {
				     
				        if (is_array($_POST[$shippingkey])) {
				         
				          $arrayvalue ='';
				         foreach($_POST[$shippingkey] as $arrayval) {
				          $arrayvalue.= ''.$arrayval.',';
				         }
				          $arrayvalue = substr_replace($arrayvalue, "", -1);
				         echo '<br /><strong>'.$shipping_field['label'].'</strong> :'.$arrayvalue.'<br />';
				        } else {
                            echo '<br /><strong>'.$shipping_field['label'].'</strong> :'.$_POST[$shippingkey].'<br />';
                                        
                               }
                                       
                                       
                                       }
				   }
				
			 }
		   

		   foreach ($additional_fields as $additionalkey=>$additional_field) {
              if (isset($additional_field['orderedition'])) {

              	 if ( ! empty( $_POST[$additionalkey] ) ) {
				     
				        if (is_array($_POST[$additionalkey])) {
				         
				          $arrayvalue ='';
				         foreach($_POST[$additionalkey] as $arrayval) {
				          $arrayvalue.= ''.$arrayval.',';
				         }
				          $arrayvalue = substr_replace($arrayvalue, "", -1);
				         echo '<br /><strong>'.$additional_field['label'].'</strong> :'.$arrayvalue.'<br />';
				        } else {
                            echo '<br /><strong>'.$additional_field['label'].'</strong> :'.$_POST[$additionalkey].'<br />';
                                        
                               }
                                       
                                       
                                       }
              
              }

		   }
	 }
	 

	 

	 


}

new pcfme_add_order_meta_class();
?>