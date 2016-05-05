jQuery(document).ready( function($){
    "use strict";


    var $body = $('body'),
        $add_to_cart_el = $('.add-request-quote-button'),
        $widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote'),
        ajax_loader    = ( typeof ywraq_frontend !== 'undefined' ) ? ywraq_frontend.block_loader : false,
        $remove_item = $('.yith-ywraq-item-remove');


    /* Variation change */
    $.fn.yith_ywraq_variations = function() {

        var $product_id = $('[name|="product_id"]'),
            product_id = $product_id.val(),
            button = $('.add-to-quote-' + product_id).find('a'),
            $button_wrap = button.parents('.yith-ywraq-add-to-quote'),
            $variation_id = $('[name|="variation_id"]');

        if( $variation_id.length <=0 ){
            return false;
        }

        button.parent().hide().removeClass('show'); 

        $variation_id.on('change', function () {

            var  $variation_data =[],
                variations =  '' + $button_wrap.attr('data-variation');

            if( variations != 'undefined' ){
                $variation_data = variations.split(',');
            }

            $('.yith_ywraq_add_item_product-response-' + $product_id.val()).hide().removeClass('show');

            if ( $(this).val() == '') {
                button.parent().hide().removeClass('show');
            } else {
                if(  $.inArray( $(this).val(), $variation_data ) >= 0 )  {

                    $('.yith_ywraq_add_item_response-' + $product_id.val()).show().removeClass('hide');
                    $('.yith_ywraq_add_item_browse-list-' + $product_id.val()).show().removeClass('hide');
                }else{
                    button.parent().show().addClass('show');

                    $('.yith_ywraq_add_item_response-' + $product_id.val()).hide().removeClass('show');
                    $('.yith_ywraq_add_item_browse-list-' + $product_id.val()).hide().removeClass('show');
                }
            }

        });
    };

    if( $body.hasClass('single-product') ){
        $.fn.yith_ywraq_variations();
    }

    /* Add to cart element */
    $(document).on( 'click' ,'.add-request-quote-button', function(e){

        e.preventDefault();

        var $t = $(this),
            $t_wrap = $t.closest('.yith-ywraq-add-to-quote'),
            add_to_cart_info = 'ac',
            $cart_form = '';

        // find the form
        if( $t.closest( '.cart' ).length ){
            $cart_form = $t.closest( '.cart' );
        }
        else if( $t.siblings( '.cart' ).first().length ) {
            $cart_form = $t.siblings( '.cart' ).first();
        }else if( $('.composite_form').length ){
            $cart_form = $('.composite_form') ;
        }
        else {
            $cart_form = $('.cart');
        }

        if ( $t.closest('ul.products').length > 0) {
            var $add_to_cart_el = '',
                $product_id_el = $t.closest('li.product').find('a.add_to_cart_button'),
                $product_id_el_val = $product_id_el.data( 'product_id' );
        }else{
            var $add_to_cart_el = $t.closest('.product').find('input[name="add-to-cart"]'),
                $product_id_el = $t.closest('.product').find('input[name="product_id"]'),
                $product_id_el_val = $product_id_el.length ? $product_id_el.val() : $add_to_cart_el.val();

        }

        var prod_id = ( typeof $product_id_el_val == 'undefined') ? $t.data('product_id') : $product_id_el_val;

        if ($add_to_cart_el.length > 0 && $product_id_el.length > 0) { //variable product
            add_to_cart_info = $cart_form.serialize();
        }else if ( $add_to_cart_el.length > 0 && $cart_form.length > 0) { //single product and form exists with cart class
            add_to_cart_info = $cart_form.serialize();
        }else if ( $add_to_cart_el.length == 0 && $cart_form.length > 0) { //single product and form exists with cart class
                add_to_cart_info = $cart_form.serialize();
        }else if ( $add_to_cart_el.length == 0) { //shop page - archive page
            add_to_cart_info = 'quantity=1';
        }

        add_to_cart_info += '&action=yith_ywraq_action&ywraq_action=add_item&product_id='+$t.data('product_id')+'&wp_nonce='+$t.data('wp_nonce');

        if( add_to_cart_info.indexOf('add-to-cart') >= 0){
            add_to_cart_info = add_to_cart_info.replace( /add-to-cart/g, 'yith-add-to-cart');
        }

        $(document).trigger( 'yith_ywraq_action_before' );

        if ( typeof yith_wapo_general !== 'undefined' ) {

            if( ! yith_wapo_general.do_submit ) {
                return false;
            }
        }

        $.ajax({
            type   : 'POST',
            url    : ywraq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'yith_ywraq_action' ),
            dataType: 'json',
            data   : add_to_cart_info,
            beforeSend: function(){
                $t.after( ' <img src="'+ajax_loader+'" >' );
            },
            complete: function(){
                $t.next().remove();
            },

            success: function (response) {
                    if( response.result == 'true' || response.result == 'exists'){

                        if( ywraq_frontend.go_to_the_list == 'yes' ){
                            window.location.href = response.rqa_url;
                        }else{

                            $('.yith_ywraq_add_item_product-response-' + prod_id).show().removeClass('hide').html( response.message );
                            $('.yith_ywraq_add_item_browse-list-' + prod_id).show().removeClass('hide');
                            $t.parent().hide().removeClass('show').addClass('addedd');
                            $('.add-to-quote-'+ prod_id).attr('data-variation', response.variations );

                            if( $widget.length ){
                                $widget.ywraq_refresh_widget();
                                $widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote');
                            }
                        }

                    }else if( response.result == 'false' ){
                        $('.yith_ywraq_add_item_response-' + prod_id ).show().removeClass('hide').html( response.message );
                    }
            }
        });

    });

    /* Refresh the widget */
    $.fn.ywraq_refresh_widget = function () {
        $widget.each(function () {
            var $t = $(this),
                $wrapper_list = $t.find('.yith-ywraq-list-wrapper'),
                $list = $t.find('.yith-ywraq-list'),
                data_widget = $t.find('.yith-ywraq-list-widget-wrapper').data('instance');

            $.ajax({
                type      : 'POST',
                url       : ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
                data      : data_widget + '&ywraq_action=refresh_quote_list&action=yith_ywraq_action',
                beforeSend: function () {
                    $list.css('opacity', 0.5);
                    if ($t.hasClass('widget_ywraq_list_quote')) {
                        $wrapper_list.prepend(' <img src="' + ajax_loader + '" >');
                    }
                },
                complete  : function () {
                    if ($t.hasClass('widget_ywraq_list_quote')) {
                        $wrapper_list.next().remove();
                    }
                    $list.css('opacity', 1);
                },
                success   : function (response) {
                    if ($t.hasClass('widget_ywraq_mini_list_quote')) {
                        $t.find('.yith-ywraq-list-widget-wrapper').html(response.mini);
                    } else {
                        $t.find('.yith-ywraq-list-widget-wrapper').html(response.large);
                    }
                }
            });
        });
    };

     /*Remove an item from rqa list*/
    $(document).on('click', '.yith-ywraq-item-remove', function (e) {

        e.preventDefault();

        var $t = $(this),
            key = $t.data('remove-item'),
            wrapper = $t.parents('.ywraq-wrapper'),
            form = $('#yith-ywraq-form'),
            cf7 = wrapper.find('.wpcf7-form'),
            remove_info = '';

        remove_info = 'action=yith_ywraq_action&ywraq_action=remove_item&key=' + $t.data('remove-item') + '&wp_nonce=' + $t.data('wp_nonce') + '&product_id=' + $t.data('product_id');

        $.ajax({
            type      : 'POST',
            url       : ywraq_frontend.ajaxurl.toString().replace('%%endpoint%%', 'yith_ywraq_action'),
            dataType  : 'json',
            data      : remove_info,
            beforeSend: function () {
                $t.find('.ajax-loading').css('visibility', 'visible');
            },
            complete  : function () {
                $t.siblings('.ajax-loading').css('visibility', 'hidden');
            },
            success: function (response) {
                if (response === 1) {
                    var $row_to_remove = $("[data-remove-item='" + key + "']").parents('.cart_item');

                    //compatibility with WC Composite Products
                    if ($row_to_remove.hasClass('composite-parent')) {
                        var composite_id = $row_to_remove.data('composite-id');
                        $("[data-composite-id='" + composite_id + "']").remove();
                    }

                    $row_to_remove.remove();

                    if ($('.cart_item').length === 0) {

                        if (cf7.length) {
                            cf7.remove();
                        }

                        $('#yith-ywraq-form, .yith-ywraq-mail-form-wrapper').remove();
                        $('#yith-ywraq-message').html(ywraq_frontend.no_product_in_list);
                    }
                    if ($widget.length) {
                        $widget.ywraq_refresh_widget();
                        $widget = $(document).find('.widget_ywraq_list_quote, .widget_ywraq_mini_list_quote');
                    }
                }
            }
        });
    });

    var content_data = '';
    var $cform7 =  $('.wpcf7-submit').closest('.wpcf7');

    if( $cform7.length > 0 ){

        $(document).find('.wpcf7').each( function()
        {
            var $cform7 = $(this);
            var idform = $cform7.find('input[name="_wpcf7"]').val();

            if ( idform == ywraq_frontend.cform7_id ) {

                $cform7.on('wpcf7:mailsent', function () {
                    $.ajax({
                        type    : 'POST',
                        url     : ywraq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'yith_ywraq_order_action' ),
                        dataType: 'json',
                        data    : {
                            lang:   ywraq_frontend.current_lang,
                            action: 'yith_ywraq_order_action',
                            ywraq_order_action: 'mail_sent_order_created'
                        },
                        success: function (response) {
                            if (response.rqa_url != '') {
                                window.location.href = response.rqa_url;
                            }
                        }
                    });
                });
            }
        });
    }


    var accepted_buttons = $('.quotes-actions').find('.accept');

    accepted_buttons.on('click', function(e){

        if( $(this).hasClass('pay') ){
            return true;
        }

        e.preventDefault();
        var $t = $(this),
            order_id = $t.parents('.quotes-actions').data('order_id'),
            request_info = 'action=yith_ywraq_order_action&ywraq_order_action=accept_order&order_id='+order_id;

        $.ajax({
            type   : 'POST',
            url    : ywraq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'yith_ywraq_order_action' ),
            dataType: 'json',
            data   : request_info,
            beforeSend: function(){
                $t.after( ' <img src="'+ajax_loader+'" >' );
            },
            complete: function(){
                $t.next().remove();
            },

            success: function (response) {
                if( response.result !== 0){
                    window.location.href = response.rqa_url;
                }else{

                }

            }
        });


    });


    var reject_buttons = $('.quotes-actions').find('.reject'),
        table = $('.my_account_quotes');

    reject_buttons.prettyPhoto({
        hook: 'data-rel',
        social_tools: false,
        theme: 'pp_woocommerce',
        horizontal_padding: 20,
        opacity: 0.8,
        deeplinking: false
    });

    reject_buttons.on('click', function(e){

        e.preventDefault();
        var $t = $(this),
            order_id = $t.parents('.quotes-actions').data('order_id'),
            modal = $('#modal-order-number'),
            request_info = 'action=yith_ywraq_order_action&ywraq_order_action=reject_order&order_id='+order_id;

        modal.text(order_id);
        $('.reject-quote-modal-button').attr('data-order_id', order_id);

        reject_buttons.prettyPhoto({
            hook: 'data-rel',
            social_tools: false,
            theme: 'pp_woocommerce',
            horizontal_padding: 20,
            opacity: 0.8,
            deeplinking: false
        });
/*

*/

    });

    $(document).on('click', '.close-quote-modal-button', function(e){
        e.preventDefault();
        $.prettyPhoto.close();
    });

    $(document).on('click', '.reject-quote-modal-button', function(e){

        e.preventDefault();
        var $t = $(this),
            order_id = $t.data('order_id'),
            modal = $('#modal-order-number'),
            table =$t.closest('body').find('.my_account_quotes'),
            row =table.find('[data-order_id="'+order_id+'"]').parent(),
            request_info = 'action=yith_ywraq_order_action&ywraq_order_action=reject_order&order_id='+order_id;


        $.ajax({
            type   : 'POST',
            url    : ywraq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'yith_ywraq_order_action' ),
            dataType: 'json',
            data   : request_info,
            beforeSend: function(){
                row.after( ' <img src="'+ajax_loader+'" >' );
            },
            complete: function(){
                row.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
            },

            success: function (response) {
                if( response.result !== 0){
                    //window.location.href = response.rqa_url;
                    row.find('.reject').hide();
                    row.find('.accept').hide();
                    row.find('.raq_status').removeClass('pending').addClass('rejected').text(response.status);
                    $.prettyPhoto.close();
                }

            }
        });
    });


    var request;
    $(document).on( 'click', '.input-text.qty.text', function(e){
        if( typeof request !== 'undefined' ){
            request.abort();
        }

        var $t = $(this),
            name = $t.attr('name'),
            value = $t.val(),
            item_keys = name.match(/[^[\]]+(?=])/g),
            request_info = 'action=yith_ywraq_action&ywraq_action=update_item_quantity&quantity='+value+'&key='+item_keys[0];

        request = $.ajax({
            type   : 'POST',
            url    : ywraq_frontend.ajaxurl.toString().replace( '%%endpoint%%', 'yith_ywraq_action' ),
            dataType: 'json',
            data   : request_info
        });
    });

});