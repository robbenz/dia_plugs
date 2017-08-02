<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
    exit; // Exit if accessed directly
}

$ywraq_layout_button_bg_color          = get_option( 'ywraq_layout_button_bg_color' );
$ywraq_layout_button_bg_color_hover      = get_option( 'ywraq_layout_button_bg_color_hover' );
$ywraq_layout_button_color        = get_option( 'ywraq_layout_button_color' );
$ywraq_layout_button_color_hover = get_option( 'ywraq_layout_button_color_hover' );

return "
.woocommerce .add-request-quote-button.button{
    background-color: {$ywraq_layout_button_bg_color};
    color: {$ywraq_layout_button_color};
}
.woocommerce .add-request-quote-button.button:hover{
    background-color: {$ywraq_layout_button_bg_color_hover};
    color: {$ywraq_layout_button_color_hover};
}
.woocommerce a.add-request-quote-button{
    color: {$ywraq_layout_button_color};
}

.woocommerce a.add-request-quote-button:hover{
    color: {$ywraq_layout_button_color_hover};
}
";