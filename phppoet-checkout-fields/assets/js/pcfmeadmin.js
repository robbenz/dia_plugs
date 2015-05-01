jQuery(document).ready(function(pcfme) {
var pcfmecheckoutfield = pcfme(".checkoutfield");

pcfme("#add-billing-field").on("click", function () {
   var pcfmenewPanel = pcfmecheckoutfield.clone();
    pcfmenewPanel.find(".collapse").removeClass("in");
    pcfmenewPanel.find(".accordion-toggle").attr("href", "#pcfme" + (hash));
    pcfmenewPanel.find(".new-field-label").append(pcfmeadmin.checkoutfieldtext4 + hash);
		
	pcfmenewPanel.find(".checkoutfield").attr("id", "pcfme_list_items_" + (hash));
	pcfmenewPanel.find(".panel-collapse").attr("id", "pcfme" + (hash));
	 
         var randomnumber=Math.floor(Math.random()*1000);


    
	pcfmenewPanel.find(".checkout_field_type").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][type]");
	pcfmenewPanel.find(".checkout_field_label").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][label]");
	pcfmenewPanel.find(".checkout_field_label").attr("value", "" + pcfmeadmin.checkoutfieldtext4 + hash + "");     
	pcfmenewPanel.find(".checkout_field_width").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][width]");
	pcfmenewPanel.find(".checkout_field_required").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][required]");
	
	pcfmenewPanel.find(".checkout_field_clear").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][clear]");
	pcfmenewPanel.find(".checkout_field_placeholder").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][placeholder]");
	pcfmenewPanel.find(".checkout_field_options").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][options]");
	pcfmenewPanel.find(".checkout_field_validate").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber+ "][validate][]");
	pcfmenewPanel.find(".checkout_field_orderedition").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][orderedition]");
	
	
	pcfmenewPanel.find(".checkout_field_disable_past_dates").attr("name", "pcfme_billing_settings[" + pcfmeadmin.checkoutfieldtext + randomnumber + "][disable_past]");
	
	pcfmenewPanel.find(".checkout_field_width").chosen({width: "250px","disable_search": true});
	pcfmenewPanel.find(".row-validate-multiselect").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_type").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_validate").chosen({width: "250px"});
	
	pcfme("#accordion").append(pcfmenewPanel.fadeIn());
	hash++;
	
});

pcfme("#add-shipping-field").on("click", function () {
   var pcfmenewPanel = pcfmecheckoutfield.clone();
   pcfmenewPanel.find(".collapse").removeClass("in");
   pcfmenewPanel.find(".accordion-toggle").attr("href", "#pcfme" + (hash));
   pcfmenewPanel.find(".new-field-label").append(pcfmeadmin.checkoutfieldtext5 + hash);
    
		
	pcfmenewPanel.find(".checkoutfield").attr("id", "pcfme_list_items_" + (hash));
	pcfmenewPanel.find(".panel-collapse").attr("id", "pcfme" + (hash));
	
        var randomnumber2=Math.floor(Math.random()*1000);
        
	pcfmenewPanel.find(".checkout_field_type").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][type]");
	pcfmenewPanel.find(".checkout_field_label").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][label]");
	pcfmenewPanel.find(".checkout_field_label").attr("value", "" + pcfmeadmin.checkoutfieldtext5 + hash + "");
	pcfmenewPanel.find(".checkout_field_width").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][width]");
	pcfmenewPanel.find(".checkout_field_required").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][required]");
	
	pcfmenewPanel.find(".checkout_field_clear").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][clear]");
	pcfmenewPanel.find(".checkout_field_placeholder").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][placeholder]");
	pcfmenewPanel.find(".checkout_field_options").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][options]");
	pcfmenewPanel.find(".checkout_field_validate").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][validate][]");
	pcfmenewPanel.find(".checkout_field_orderedition").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][orderedition]");
	
	pcfmenewPanel.find(".checkout_field_disable_past_dates").attr("name", "pcfme_shipping_settings[" + pcfmeadmin.checkoutfieldtext2 + randomnumber2 + "][disable_past]");
	
	pcfmenewPanel.find(".checkout_field_width").chosen({width: "250px","disable_search": true});
	pcfmenewPanel.find(".row-validate-multiselect").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_type").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_validate").chosen({width: "250px"});
	
	pcfme("#accordion").append(pcfmenewPanel.fadeIn());
	hash++;
	
});


pcfme("#add-additional-field").on("click", function () {
   var pcfmenewPanel = pcfmecheckoutfield.clone();
    pcfmenewPanel.find(".collapse").removeClass("in");
    pcfmenewPanel.find(".accordion-toggle").attr("href", "#pcfme" + (hash));
    pcfmenewPanel.find(".new-field-label").append(pcfmeadmin.checkoutfieldtext6 + hash);
    
		
	pcfmenewPanel.find(".checkoutfield").attr("id", "pcfme_list_items_" + (hash));
	pcfmenewPanel.find(".panel-collapse").attr("id", "pcfme" + (hash));
	
        var randomnumber3=Math.floor(Math.random()*1000);
        
	pcfmenewPanel.find(".checkout_field_type").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][type]");
	pcfmenewPanel.find(".checkout_field_label").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][label]");
	pcfmenewPanel.find(".checkout_field_label").attr("value", "" + pcfmeadmin.checkoutfieldtext6 + hash + "");
	pcfmenewPanel.find(".checkout_field_width").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][width]");
	pcfmenewPanel.find(".checkout_field_required").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][required]");
	
	pcfmenewPanel.find(".checkout_field_clear").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][clear]");
	pcfmenewPanel.find(".checkout_field_placeholder").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][placeholder]");
	pcfmenewPanel.find(".checkout_field_options").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][options]");
	pcfmenewPanel.find(".checkout_field_validate").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][validate][]");
	pcfmenewPanel.find(".checkout_field_orderedition").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][orderedition]");
	pcfmenewPanel.find(".checkout_field_disable_past_dates").attr("name", "pcfme_additional_settings[" + pcfmeadmin.checkoutfieldtext3 + randomnumber3 + "][disable_past]");
	
	pcfmenewPanel.find(".checkout_field_width").chosen({width: "250px","disable_search": true});
	pcfmenewPanel.find(".row-validate-multiselect").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_type").chosen({width: "250px"});
	pcfmenewPanel.find(".checkout_field_validate").chosen({width: "250px"});
	
	pcfme("#accordion").append(pcfmenewPanel.fadeIn());
	hash++;
	
});


pcfme(".pcfme-sortable-list").sortable({
    items   : '.pcfme_list_item',
    opacity : 0.7,
	cursor  : 'move',
	
});

pcfme(function() {
pcfme('.checkout_field_type').live('change',function(){
    var typevalue = $(this).val();
	if (typevalue == "datepicker") {
		$(this).parents('table:eq(0)').find('.disable_datepicker_tr').show();
	} else {
		$(this).parents('table:eq(0)').find('.disable_datepicker_tr').hide();
	}
});
});


pcfme(document).on('click', '.glyphicon-remove-circle', function () {

   var result = confirm(pcfmeadmin.removealert);
   if (result==true) {
     pcfme(this).parents('.panel').get(0).remove();
   }
   });

pcfme("#restore-billing-fields").click(function() {
   var result2 = confirm(pcfmeadmin.restorealert);
   if (result2 == true) {
     
     pcfme.ajax({
            data: {action: "restore_billing_fields" },
            type: 'POST',
            url: ajaxurl,
            success: function( response ) { 
			  window.location.reload();
			}
        });
   }
});

pcfme("#restore-shipping-fields").click(function() {
   var result3 = confirm(pcfmeadmin.restorealert);
   if (result3 == true) {
     
     pcfme.ajax({
            data: {action: "restore_shipping_fields" },
            type: 'POST',
            url: ajaxurl,
            success: function( response ) { 
			  window.location.reload();
			}
        });
   }
});
});



