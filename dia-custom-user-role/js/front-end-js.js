(function($) {

    "use strict";

    $(document).ready(function() {

      // show hide chart on product page
      $("#specs_wrap_p_page").hide();
      jQuery("#show_specs_product").click(function() {
        if(jQuery(this).is(":checked")) {
          jQuery("#specs_wrap_p_page").show(269);
        } else {
          jQuery("#specs_wrap_p_page").hide(169);
        }
      });
      // end


    });
  }(jQuery));
