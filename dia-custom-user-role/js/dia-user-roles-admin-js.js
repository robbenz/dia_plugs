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
