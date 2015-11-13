jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  
		
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $wc_predictive_search_inline_data = jQuery('#wc_predictive_search_inline_' + post_id );
		
		var predictive_search_focuskw 				= $wc_predictive_search_inline_data.find('.predictive_search_focuskw').text();
		var predictive_search_exclude_item 			= $wc_predictive_search_inline_data.find('.ps_exclude_item').text();
		
		jQuery('#wc-predictive-search-fields-quick textarea[name="_predictive_search_focuskw"]', '.inline-edit-row').text(predictive_search_focuskw);
		
		if (predictive_search_exclude_item=='yes') {
			jQuery('#wc-predictive-search-fields-quick input[name="ps_exclude_item"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('#wc-predictive-search-fields-quick input[name="ps_exclude_item"]', '.inline-edit-row').removeAttr('checked'); 
		}
    });  
    
    jQuery('#wpbody').on('click', '#doaction, #doaction2', function(){  

		jQuery('select, input.text', '.inline-edit-row').val('');
		jQuery('select option', '.inline-edit-row').removeAttr('checked');
		jQuery('#wc-predictive-search-fields-bulk .wc-predictive-keyword-value').hide();
		
	});
	
	 jQuery('#wpbody').on('change', '#wc-predictive-search-fields-bulk .change_to', function(){  
    
    	if (jQuery(this).val() > 0) {
    		jQuery(this).closest('div').find('.wc-predictive-keyword-value').show();
    	} else {
    		jQuery(this).closest('div').find('.wc-predictive-keyword-value').hide();
    	}
    
    });
});  