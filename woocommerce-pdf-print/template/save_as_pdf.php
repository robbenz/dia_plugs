<?php 

$T_featured_image = $wpp_featured_image;
$T_price_html = ($wpp_price_html) ? $wpp_price . '<br>
<br>
' : '';
$T_sku = ($wpp_sku) ? '<p><b>'.get_option( 'wpp_ph_sku' ).'</b>: '.$wpp_sku.'</p><br>
' : "";
$T_stock = ($wpp_stock) ? '<p><b>'.get_option( 'wpp_ph_in_stock' ).'</b>: '.$wpp_stock.'</p><br>
' : '';
$T_rating = ($wpp_rating) ? '<p><b>'.get_option( 'wpp_ph_rating' ).'</b>: '.$wpp_rating.'</p>
' : '';
$T_categories = ($wpp_categories) ? '<p><b>'.get_option( 'wpp_ph_prod_cats' ).'</b>: '.$wpp_categories.'</p><br>
' : '';
$T_tags = ($wpp_tags) ? '<p><b>'.get_option( 'wpp_ph_prod_tags' ).'</b>: '.$wpp_tags.'</p><br>
' : '';
$T_permalink = ($wpp_permalink) ? '<p><b>'.get_option( 'wpp_ph_prod_page' ).'</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p><br>
' : '';
$T_variants = ($wpp_has_variants) ? "\r\n\r\n<h2>".get_option( 'wpp_ph_prod_vars' )."</h2><br>\r\n<p>".$wpp_variants.'</p><br>
' : '';
$T_summary = ($wpp_summary) ? "\r\n\r\n<h2>".get_option( 'wpp_ph_prod_summ' )."</h2><br>\r\n<p>".$wpp_summary.'</p><br>
' : '';
$T_description = ($wpp_description) ? "\r\n\r\n<h2>".get_option( 'wpp_ph_prod_desc' )."</h2>\r\n<p>".$wpp_description.'</p><br>
' : '';
$T_attribute = (trim(strip_tags( $attribute_html ))) ? "\r\n\r\n<h2>" . get_option( 'wpp_ph_prod_attr' ) . "</h2><br>\r\n<p>" . $attribute_html . '</p><br>
' : '';
$T_custom_tab = wpp_pdf_tabs($custom_tab_title_html, $custom_tab_content_html);

$T_gallery = (!empty($wpp_gallery)) ? "\r\n\r\n<h2>".get_option( 'wpp_ph_prod_gall' )."</h2><br>\r\n<p>".$wpp_gallery_images.'</p>' : '';


if( !get_option( 'wpp_T_featured_image')) $T_featured_image = '';
if( !get_option( 'wpp_T_price_html')) $T_price_html = '';
if( !get_option( 'wpp_T_sku')) $T_sku = '';
if( !get_option( 'wpp_T_stock')) $T_stock = '';
if( !get_option( 'wpp_T_rating')) $T_rating = '';
if( !get_option( 'wpp_T_categories')) $T_categories = '';
if( !get_option( 'wpp_T_tags')) $T_tags = '';
if( !get_option( 'wpp_T_permalink')) $T_permalink = '';
if( !get_option( 'wpp_T_variants')) $T_variants = '';
if( !get_option( 'wpp_T_summary')) $T_summary = '';
if( !get_option( 'wpp_T_description')) $T_description = '';
if( !get_option( 'wpp_T_attribute')) $T_attribute = '';
if( !get_option( 'wpp_T_custom_tab')) $T_custom_tab = '';
if( !get_option( 'wpp_T_gallery')) $T_gallery = '';

$STR['body'] = '<h1>'. $wpp_title .'</h1>
	' . 
	$T_featured_image . 
	$T_price_html .
	$T_sku . 
	$T_stock .
	$T_rating .
	$T_categories .
	$T_tags .
	$T_permalink .
	$T_variants .
	$T_summary . 
	$T_description . 
	$T_attribute .
	$T_custom_tab .
	$T_gallery;

function wpp_pdf_tabs($title_html, $content_html){
	foreach($title_html as $k => $title){ $h .= ((trim(strip_tags($content_html[$k]))) ? "\r\n\r\n<h2>".$title."</h2><br>\r\n<p>".$content_html[$k].'</p><br>' : '' );} return $h;
}

?>