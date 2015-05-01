(function( $pcfme ) {

	$pcfme(function() {
       if ($pcfme('.pcfme-datepicker').length) {
		 $pcfme('.pcfme-datepicker').datepicker({
           dateFormat : 'dd-mm-yy'
       });
	   }
	   var dateToday = new Date(); 
	   if ($pcfme('.pcfme-datepicker-disable-past').length) {
		 $pcfme('.pcfme-datepicker-disable-past').datepicker({
           dateFormat : 'dd-mm-yy',
		   minDate: dateToday
       });
	   }
	   
    });
   	
    $pcfme(function() {
	 
	 if ($pcfme('.pcfme-multiselect').length) {
		 $pcfme('.pcfme-multiselect').chosen();
	 }
	 
	 if ($pcfme('.pcfme-singleselect').length) {
		 $pcfme('.pcfme-singleselect').chosen();
	 }
      
    });
})(jQuery);