/**
 * WooCommerce Quote - Scripts
 */

/**
 * Based on jQuery
 */
jQuery(document).ready(function() {

    /**
     * Admin hints
     */
    jQuery('form').each(function(){
        jQuery(this).find(':input').each(function(){
            if (typeof woo_quote_hints !== 'undefined' && typeof woo_quote_hints[this.id] !== 'undefined') {
                jQuery(this).parent().parent().find('th').append('<div class="woo_quote_tip" title="'+woo_quote_hints[this.id]+'"><i class="fa fa-question"></div>');
            }
        });
    });
    jQuery.widget('ui.tooltip', jQuery.ui.tooltip, {
        options: {
            content: function() {
                return jQuery(this).prop('title');
            }
        }
    });
    jQuery('.woo_quote_tip').tooltip();

    /**
     * Logo upload
     */
    jQuery('#woo_quote_seller_logo_upload_button').click(function() {

        // Store original send_to_editor function
        window.woo_quote_restore_send_to_editor = window.send_to_editor;

        // Overwrite send to editor function with ours
        window.send_to_editor = function(html) {
            imgurl = jQuery('img',html).attr('src');

            // Handle cases when allow_url_fopen is disabled in PHP configuration
            if (typeof woo_quote_uploads_url !== 'undefined' && typeof woo_quote_uploads_path !== 'undefined') {
                if (typeof woo_quote_url_fopen_allowed !== 'undefined' && woo_quote_url_fopen_allowed == '0') {
                    imgurl = imgurl.replace(woo_quote_uploads_url, woo_quote_uploads_path);
                }
            }

            jQuery('#woo_quote_seller_logo').val(imgurl);
            tb_remove();
            window.send_to_editor = window.woo_quote_restore_send_to_editor;
        };

        formfield = jQuery('woo_quote_seller_logo').attr('name');
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        return false;
    });
});
