(function(jQuery) {
    jQuery( document ).ready(function() {
        if (jQuery('a#WooCommerceWooCartProInMenu').length > 0) {
            jQuery('a#WooCommerceWooCartProInMenu').parent().parent().replaceWith(fesiWooCartInMenu.cartContent);
        }
    })
}(jQuery)); 