(function($) {

    "use strict";

    $(document).ready(function() {

      // show hide chart on product page
      $("#specs_wrap_p_page").hide();
      $("#show_specs_product").click(function() {
        if($(this).is(":checked")) {
          $("#specs_wrap_p_page").show(269);
        } else {
          $("#specs_wrap_p_page").hide(169);
        }
      });
      // end


    });
  }(jQuery));
