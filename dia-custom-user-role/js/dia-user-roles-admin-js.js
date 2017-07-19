jQuery(document).ready(function() {

  // Supplier 2 SHOW / HIDE
  if(jQuery(".dia_product_multiple_suppliers").is(":checked")) {
    jQuery("#supplier_2_wrap").show();
  } else {
    jQuery("#supplier_2_wrap").hide();
  }
  jQuery(".dia_product_multiple_suppliers").click(function() {
    if(jQuery(this).is(":checked")) {
      jQuery("#supplier_2_wrap").show(300);
    } else {
      jQuery("#supplier_2_wrap").hide(200);
    }
  });
  // END Supplier 2

});

// add date picker and shit
jQuery(document).ready(function() {
  jQuery('.dia_product_price_check_1_field input').datepicker({
    dateFormat : 'mm/dd/yy'
  });
  jQuery('.dia_product_price_check_2_field input').datepicker({
    dateFormat : 'mm/dd/yy'
  });
//  jQuery("[id^=dia_var_date_check] input").datepicker({
//    dateFormat : 'mm/dd/yy'
//  });

});

jQuery(document).ready(function() {

  jQuery("#var_product_alert").hide();
  jQuery("#all_dia_specs_wrapp").hide();

  jQuery("#product-type").change(function() {
    var val = jQuery(this).val();
    if(val === "simple" ) {
      jQuery("#all_dia_specs_wrapp").show();
      jQuery("#var_product_alert").hide();
    }
    else if(val === "variable" ) {
      jQuery("#all_dia_specs_wrapp").hide();
      jQuery("#var_product_alert").show();
    }
  });

});
