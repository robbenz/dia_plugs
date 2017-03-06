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
jQuery(document).ready(function($) {
  $('.dia_product_price_check_1_field input').datepicker({
    dateFormat : 'mm-dd-yy'
  });
  $('.dia_product_price_check_2_field input').datepicker({
    dateFormat : 'mm-dd-yy'
  });
});
