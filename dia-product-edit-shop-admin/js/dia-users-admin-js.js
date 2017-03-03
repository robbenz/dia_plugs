jQuery(document).ready(function() {

  // customer favorite show hide
  if(jQuery(".dia_customer_favorite").is(":checked")) {
    jQuery("#dia_users_fav_pos_drop").show();
  } else {
    jQuery("#dia_users_fav_pos_drop").hide();
  }
  jQuery(".dia_customer_favorite").click(function() {
    if(jQuery(this).is(":checked")) {
      jQuery("#dia_users_fav_pos_drop").show(300);
    } else {
      jQuery("#dia_users_fav_pos_drop").hide(200);
    }
  });
  // END customer favorite

});
