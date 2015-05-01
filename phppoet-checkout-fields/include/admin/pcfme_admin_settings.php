<?php

	
class pcfme_add_settings_page_class {
	
	
	
	private $billing_settings_key       = 'pcfme_billing_settings';
	private $shipping_settings_key      = 'pcfme_shipping_settings';
	private $additional_settings_key    = 'pcfme_additional_settings';
	private $pcfme_plugin_options       = 'pcfme_plugin_options';
    private $pcfme_pcfme_plugin_settings_tabs = array();	
	
	
	public function __construct() {
	    
		
		
	    add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_billing_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_shipping_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_additional_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) ,100);
		add_action( 'admin_enqueue_scripts', array(&$this, 'pcfme_register_admin_scripts'));
		add_action( 'wp_ajax_restore_billing_fields', array( &$this, 'restore_billing_fields' ) );
		add_action( 'wp_ajax_restore_shipping_fields', array( &$this, 'restore_shipping_fields' ) );
		
	}
	
	
	public function restore_billing_fields() {
	   delete_option( $this->billing_settings_key );
	   die();
	}
	
	public function restore_shipping_fields() {
	   delete_option( $this->shipping_settings_key );
	   die();
	}

	
	
	
	
	
	public function load_settings() {
		$this->billing_settings = (array) get_option( $this->billing_settings_key );
		
		$this->billing_settings = array_merge( array(
		), $this->billing_settings );
		
		$this->shipping_settings = (array) get_option( $this->shipping_settings_key );
		
		$this->shipping_settings = array_merge( array(
		), $this->shipping_settings );

		$this->additional_settings = (array) get_option( $this->additional_settings_key );
		
		$this->additional_settings = array_merge( array(
		), $this->additional_settings );
		
		
	}
	
	

	
	/*
	 * registers admin scripts via admin enqueue scripts
	 */
	public function pcfme_register_admin_scripts($hook) {
	        global $billing_pcfmesettings_page;
			
			if( $hook != $billing_pcfmesettings_page ) 
		     return;
 
		 wp_enqueue_script( 'jquery-1.8.3', ''.pcfme_PLUGIN_URL.'assets/js/jquery-1.8.3.js');
		 wp_enqueue_script( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/js/pcfmeadmin.js' );
		 wp_enqueue_script( 'chosen.jquery', ''.pcfme_PLUGIN_URL.'assets/js/chosen.jquery.js' );
		 
		 wp_enqueue_script( 'bootstrap-min', ''.pcfme_PLUGIN_URL.'assets/js/bootstrap-min.js' );
		 wp_enqueue_script( 'jquery-ui-sortable');
		 
         
		 wp_enqueue_style( 'pcfmeadmin', ''.pcfme_PLUGIN_URL.'assets/css/pcfmeadmin.css' );
		 wp_enqueue_style ( 'bootstrap',''.pcfme_PLUGIN_URL.'assets/css/bootstrap.css');
		 wp_enqueue_style( 'chosen.min', ''.pcfme_PLUGIN_URL.'assets/css/chosen.min.css' );

		 wp_enqueue_script( 'jquery-ui-datepicker' );
		 wp_enqueue_script( 'pcfme-frontend1', ''.pcfme_PLUGIN_URL.'assets/js/frontend1.js' );
		 wp_enqueue_style( 'jquery-ui', ''.pcfme_PLUGIN_URL.'assets/css/jquery-ui.css' );
		 wp_enqueue_style( 'pcfme-frontend', ''.pcfme_PLUGIN_URL.'assets/css/frontend.css' );
		
		 
		 $translation_array = array( 
		     'removealert' => __( 'Are you sure you want to delete?' ,'pcn'),
		     'checkoutfieldtext' => __( 'billing_field_' ,'pcn'),
		     'checkoutfieldtext2' => __( 'shipping_field_' ,'pcn'),
		     'checkoutfieldtext3' => __( 'additional_field_' ,'pcn'),
		     'checkoutfieldtext4' => __( 'billing field ' ,'pcn'),
		     'checkoutfieldtext5' => __( 'shipping field ' ,'pcn'),
		     'checkoutfieldtext6' => __( 'additional field ' ,'pcn'),
		     'placeholder' => __( 'Search and Select ' ,'pcn'),
		     'restorealert' => __( 'Restoring Default fields will undo all your Changes. Are you sure you want to do this ?' ,'pcn')
		 );
                 wp_localize_script( 'pcfmeadmin', 'pcfmeadmin', $translation_array );
	}
	
	
	public function register_billing_settings() {
		$this->pcfme_plugin_settings_tabs[$this->billing_settings_key] = 'Billing Fields';
		
		register_setting( $this->billing_settings_key, $this->billing_settings_key );
		add_settings_section( 'pcfme_section_billing', '', '', $this->billing_settings_key );
		add_settings_field( 'pcfme_billing_option', '', array( &$this, 'pcfme_field_billing_option' ), $this->billing_settings_key, 'pcfme_section_billing' );
	}
	
	public function register_shipping_settings() {
		$this->pcfme_plugin_settings_tabs[$this->shipping_settings_key] = 'Shipping Fields';
		
		register_setting( $this->shipping_settings_key, $this->shipping_settings_key );
		add_settings_section( 'pcfme_section_shipping', '', '', $this->shipping_settings_key );
		add_settings_field( 'pcfme_shipping_option', '', array( &$this, 'pcfme_field_shipping_option' ), $this->shipping_settings_key, 'pcfme_section_shipping' );
	}


	public function register_additional_settings() {
		$this->pcfme_plugin_settings_tabs[$this->additional_settings_key] = 'Additional Fields';
		
		register_setting( $this->additional_settings_key, $this->additional_settings_key );
		add_settings_section( 'pcfme_section_additional', '', '', $this->additional_settings_key );
		add_settings_field( 'pcfme_additional_option', '', array( &$this, 'pcfme_field_additional_option' ), $this->additional_settings_key, 'pcfme_section_additional' );
	}
	


	

	
	
	public function pcfme_field_billing_option() {
	    
		include ('forms/pcfme_admin_billing_fields_form.php');
  
		
	}
	
	public function pcfme_field_shipping_option() { 
	     
       include ('forms/pcfme_admin_shipping_fields_form.php');
		 
		 
	 }

	 public function pcfme_field_additional_option() { 
	     
       include ('forms/pcfme_admin_additional_fields_form.php');
		 
		 
	 }
	
	
	
	

	public function add_admin_menus() {
	   global $billing_pcfmesettings_page;
	    $billing_pcfmesettings_page = add_submenu_page( 'woocommerce', __('Checkout fields'), __('Checkout fields'), 'manage_woocommerce', $this->pcfme_plugin_options, array(&$this, 'plugin_options_page'));
	}
	
	
	public function plugin_options_page() {
	    global $woocommerce;
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
		global $billing_fields;
		$billing_fields = '';
		?>
		<div class="wrap">
		    <?php $this->plugin_options_tabs(); ?>
		
			<form method="post" class="<?php echo $tab; ?>" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				
				
				<br />
				<center><input type="submit" name="submit" id="submit" class="btn btn-success" value="<?php _e('Save Changes','pcn'); ?>"></center>
				
			    
			</form>
			<br />
			
		</div>
		<div id="responsediv" style="display:none; font-size:20px; background-color:#ffffe0; border-color: #e6db55;  border-width: 1px; border-style: solid; width :auto; height:30px;">
		</div>
		<?php
	}


	public function display_visual_preview($key,$field) { 
     global $woocommerce;

		?>
	 
	  <td width="30%">
	    <label for="billing_company" class=""><?php if (isset($field['label'])) { echo $field['label']; } ?>
          <?php if (isset($field['required']) && ($field['required'] == 1)) { ?>
          &nbsp;&nbsp;<span class="pcmfe-required" title="required">*</span>
          <?php } ?>
	    </label>
        
      </td>
	  <td width="30%">
	  	<?php pcmfe_admin_form_field($key,$field); ?>
	  </td>
	 
	<?php }
	
	
	public function show_fields_form($key,$field,$noticerowno,$slug,$required_slugs,$core_fields,$country_fields,$address2_field) { ?>
	      <?php
               if (isset($field['width'])) {
                 $fieldwidth= $field['width'];
               	 
               } elseif (isset($field['class'])) {
                  
               	
               	  foreach($field['class'] as $class) {
               	  	if (isset($class)) {
                    switch($class) {
                    	case "form-row-first":
                          $fieldwidth='form-row-first';
						break;

                    	case "form-row-last":
                          $fieldwidth='form-row-last';
						break;

                    	default:
                    	 $fieldwidth='form-row-wide';

                    }
                }
               	  } 
               }



        switch($slug) {
		
		  case "pcfme_billing_settings":
		    $headlingtext=''.__('billing_field_','pcn').''.$noticerowno.'';
		    break;
	
          case "pcfme_shipping_settings":
		    $headlingtext=''.__('shipping_field_','pcn').''.$noticerowno.'';
		    break;

		   case "pcfme_additional_settings":
		     $headlingtext=''.__('additional_field_','pcn').''.$noticerowno.'';
		     break;
		
		
	       } ?>   

       
	   <div class="panel-group panel panel-default pcfme_list_item <?php if (isset($field['hide']) && ($field['hide'] == 1)) { echo "pcfme-hidden-row";} ?>" id="pcfme_list_items_<?php echo $noticerowno; ?>" style="width:90%; display:block;">
           <div class="panel-heading"> 
		
	     <table class="heading-table">
			<tr>
			    <td width="20%">
			     <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#pcfme<?php echo $noticerowno; ?>">
                  <span class="glyphicon glyphicon-edit pull-left"></span>
			     </a>
			    </td>
			    
				<?php $this->display_visual_preview($key,$field); ?>
			 
			    <td width="20%">
		         <?php if (!preg_match('/\b'.$key.'\b/', $core_fields )) { ?>    
		           <span class="glyphicon glyphicon-remove-circle pull-right "></span>
		         <?php } ?>
			    </td>
                </tr>
		  </table>
           </div>
           <div id="pcfme<?php echo $noticerowno; ?>" class="panel-collapse collapse">

		     <table class="table"> 
			 
			 <?php if (preg_match('/\b'.$key.'\b/', $core_fields )) { ?>    
		       <tr>
                <td><label for="<?php echo $field; ?>_hide"><?php  _e('Hide/Remove This Core Field','pcn'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][hide]" <?php if (isset($field['hide']) && ($field['hide'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
		     <?php }  ?>

		     <tr>
			   <td><label for="<?php echo $field; ?>_type"><?php _e('Field Key','pcn'); ?></label></td>
			   <td><span claas="wsp_field_key"><?php echo $key; ?></span></td>
		     </tr> 

			 <?php if (!preg_match('/\b'.$key.'\b/', $country_fields )) { ?>   
		       <tr>
	           <td><label for="<?php echo $field; ?>_type"><?php _e('Field Type','pcn'); ?></label></td>
		       <td>
		          <select class="checkout_field_type" name="<?php echo $slug; ?>[<?php echo $key; ?>][type]" >
			        <option value="text" <?php if (isset($field['type']) && ($field['type'] == "text")) { echo "selected";} ?> ><?php _e('Text','pcn'); ?></option>
			        <option value="textarea" <?php if (isset($field['type']) && ($field['type'] == "textarea")) { echo "selected";} ?>><?php _e('Textarea','pcn'); ?></option>
			        <option value="password" <?php if (isset($field['type']) && ($field['type'] == "password")) { echo "selected";} ?>><?php _e('Password','pcn'); ?></option>
			        <option value="checkbox" <?php if (isset($field['type']) && ($field['type'] == "checkbox")) { echo "selected";} ?>><?php _e('Checkbox','pcn'); ?></option>
			        <option value="pcfmeselect" <?php if (isset($field['type']) && ($field['type'] == "pcfmeselect")) { echo "selected";} ?>><?php _e('Select','pcn'); ?></option>
					<option value="multiselect" <?php if (isset($field['type']) && ($field['type'] == "multiselect")) { echo "selected";} ?>><?php _e('multiselect','pcn'); ?></option>
			        <option value="radio" <?php if (isset($field['type']) && ($field['type'] == "radio")) { echo "selected";} ?>><?php _e('Radio Select','pcn'); ?></option>
			        <option value="datepicker" <?php if (isset($field['type']) && ($field['type'] == "datepicker")) { echo "selected";} ?>><?php _e('Date Picker','pcn'); ?></option>
			       </select>
		       </td>
	           </tr>
               <?php }  ?>
               
               <?php if (!preg_match('/\b'.$key.'\b/', $address2_field )) { ?>
			   <tr>
                <td><label for="<?php echo $field; ?>_label"><?php  _e('Label','pcn'); ?></label></td>
	            <td><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][label]" value="<?php if (isset($field['label']) && ($field['label'] != '')) { echo $field['label']; } else { echo $headlingtext;} ?>" size="35"></td>
               </tr>
			   <?php }  ?>
			
			   
			   
			   <tr>
	           <td><label for="<?php echo $field; ?>_width"><?php _e('Width','pcn'); ?></label></td>
		       <td>
		       <select class="checkout_field_width" name="<?php echo $slug; ?>[<?php echo $key; ?>][width]" >
			    
				<option value="form-row-wide" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-wide" )) { echo 'selected'; } ?>><?php _e('Full Width','pcn'); ?></option>
			    <option value="form-row-first" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-first" )) { echo 'selected'; } ?>><?php _e('First Half','pcn'); ?></option>
			    <option value="form-row-last" <?php if (isset($fieldwidth) && ($fieldwidth == "form-row-last" )) { echo 'selected'; } ?>><?php _e('Second Half','pcn'); ?></option>
				
				
			   </select>
		       </td>
	           </tr>
			   
			   <?php if (!preg_match('/\b'.$key.'\b/', $required_slugs )) { ?>
		       <tr>
                <td><label for="<?php echo $field; ?>_required"><?php  _e('Required','pcn'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][required]" <?php if (isset($field['required']) && ($field['required'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   <?php } ?>
			   
			   <tr>
                <td><label for="<?php echo $field; ?>_clear"><?php  _e('Clearfix','pcn'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][clear]" <?php if (isset($field['clear']) && ($field['clear'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   
			   
			   <tr>
                <td><label for="<?php echo $field; ?>_label"><?php  _e('Placeholder ','pcn'); ?></label></td>
	            <td><input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][placeholder]" value="<?php if (isset($field['placeholder'])) { echo $field['placeholder']; } ?>" size="35"></td>
               </tr>
			   
			   
			   <tr class="add-field-options" style="">
	           <td>
		         <label for="<?php echo $field; ?>_options"><?php _e('Options','pcn'); ?></label>
		       </td>
		       <td>
		       <input type="text" name="<?php echo $slug; ?>[<?php echo $key; ?>][options]" placeholder="<?php _e('Separated by comma. For Example: option1,option2','pcn'); ?>" value="<?php if (isset($field['options'])) { echo $field['options']; } ?>" size="35">
		       </td>
	           </tr>
			   
		
			   
			   
			   <?php 
			   $validatearray='';
			   if (isset($field['validate'])) {
			   foreach ($field['validate'] as $z=>$value) {
			    $validatearray.=''.$value.',';
			   }
			   $validatearray=substr_replace($validatearray, "", -1);
			   }
			   
			   
			   ?>
			   
			   <tr>
                <td><label for="<?php echo $field; ?>_label"><?php  _e('Validate','pcn'); ?></label></td>
	            <td>
		           <select name="<?php echo $slug; ?>[<?php echo $key; ?>][validate][]" class="row-validate-multiselect" multiple>
			         <option value="state" <?php if (preg_match('/\bstate\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('state','pcn'); ?></option>
			         <option value="postcode" <?php if (preg_match('/\bpostcode\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('postcode','pcn'); ?></option>
			         <option value="email" <?php if (preg_match('/\bemail\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('email','pcn'); ?></option>
			         <option value="phone" <?php if (preg_match('/\bphone\b/', $validatearray )) { echo 'selected'; } ?>><?php _e('phone','pcn'); ?></option>
			       </select>
		       </td>
	 
               </tr>
			   
			   <tr>
                <td><label for="<?php echo $field; ?>_clear"><?php  _e('Show field detail on woocommerce order email and order edition page','pcn'); ?></label></td>
                <td><input type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][orderedition]" <?php if (isset($field['orderedition']) && ($field['orderedition'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   
			   <tr class="disable_datepicker_tr" style="<?php if (isset($field['type']) && ($field['type'] == "datepicker")) { echo "display:;";} else { echo "display:none;"; } ?>">
                <td><label ><?php  _e('Disable Past Date Selection In Datepicker','pcn'); ?></label></td>
                <td><input class="checkout_field_disable_past_dates" type="checkbox" name="<?php echo $slug; ?>[<?php echo $key; ?>][disable_past]" <?php if (isset($field['disable_past']) && ($field['disable_past'] == 1)) { echo "checked";} ?> value="1"></td>
			   </tr>
			   </table>

             </div>
			
          </div>
	<?php }
	
	
	

	

	public function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->billing_settings_key;
        echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->pcfme_plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->pcfme_plugin_options . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
	
	public function show_new_form() { 
	     include ('forms/pcfme_show_new_form.php');
    }
	

}




new pcfme_add_settings_page_class();

?>