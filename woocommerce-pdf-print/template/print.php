<?php 

if( !get_option( 'wpp_T_featured_image')) $wpp_featured_image = '';
if( !get_option( 'wpp_T_price_html')) $wpp_price = '';
if( !get_option( 'wpp_T_sku')) $wpp_sku = '';
if( !get_option( 'wpp_T_stock')) $wpp_stock = '';
if( !get_option( 'wpp_T_rating'))$wpp_rating = '';
if( !get_option( 'wpp_T_categories')) $wpp_categories = '';
if( !get_option( 'wpp_T_tags')) $wpp_tags = '';
if( !get_option( 'wpp_T_permalink')) $wpp_permalink = '';
if( !get_option( 'wpp_T_variants')) $wpp_has_variants = false;
if( !get_option( 'wpp_T_summary')) $wpp_summary = '';
if( !get_option( 'wpp_T_description')) $wpp_description = '';
if( !get_option( 'wpp_T_attribute')) $attribute_html = '';
if( !get_option( 'wpp_T_gallery')) $wpp_gallery = '';

$print = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'. $print_title.' : '. get_option('blogname') .' : '.get_option('siteurl').'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.get_option('blog_charset').'" />
<style media="print" type="text/css">
#hidden_{
	display:none;
}
'.get_option('wpp_print_css').'
</style>
<style media="all" type="text/css">
'.get_option('wpp_print_css').'
img{
	border:hidden;
	padding:10px;
}
.alignleft {
    float: left;
}
.alignright {
    float: right;
}
.aligncenter {
    display: block;
    margin-left: auto;
    margin-right: auto;
}
</style>
</head>
<body style="font-family:Arial, Helvetica, sans-serif">
<div align="center">
<table width="595" style="width:595px;text-align:'.get_option('wpp_save_text_align').'" border="0"  bgcolor="#FFFFFF" cellspacing="1" cellpadding="5" >
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:12px; color:#000000;">
	'.$pt_header.'
	'.$pt_head.'
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="font-size:14px; color:#000000;">
	<h1>'. $wpp_title .'</h1>
	'.str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:40px;" ', $wpp_featured_image).' 
	'.(($wpp_price_html) ? '<p style="font-size:20px; color:#006600;">' .$wpp_price.'</p>
	' : '').''.(($wpp_sku) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_sku' ).'</b>: '.$wpp_sku.'</p>
	' : '' ).''.(($wpp_stock) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_in_stock' ).'</b>: '.$wpp_stock.'</p>
	' : '' ).''.(($wpp_rating) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_rating' ).'</b>: '.$wpp_rating.'</p>
	' : '' ).''.(($wpp_categories) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_prod_cats' ).'</b>: '.$wpp_categories.'</p>
	' : '' ).''.(($wpp_tags) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_prod_tags' ).'</b>: '.$wpp_tags.'</p>
	' : '' ).''.(($wpp_permalink) ? '<p style="font-size:14px;"><b>'.get_option( 'wpp_ph_prod_page' ).'</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p>
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p><div style="clear:both"></div>
	'.(($wpp_has_variants) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_vars' )."</h2><p>".$wpp_variants.'</p>
	' : "\r\n" ).''.(($wpp_summary) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_summ' )."</h2><p>".$wpp_summary.'</p>
	' : '' ).''.(($wpp_description) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_desc' )."</h2><p>".$wpp_description.'</p>
	' : '' ).''.((trim(strip_tags($attribute_html))) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_attr' )."</h2><p>".$attribute_html.'</p>
	' : '' ).''.wpp_print_tabs($custom_tab_title_html, $custom_tab_content_html).'
	'.((!empty($wpp_gallery)) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_gall' )."</h2>".str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:5px;" ', $wpp_gallery_images) .'
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p>
	<div style="clear:both"></div>
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:13px; color:#000000;">
	'.$pt_links.'
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:13px; color:#000000;">
	'.$pt_date.'
	'.$pt_md_date.'
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="font-size:12px; color:#000000">
	 <div id="hidden_" align="right">
	 <a href="javascript:window.print()">
	 <img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_icon_url').'" style="border:#999999 solid 1px;" alt="'.get_option( 'wpp_ph_print_post').'" title="'.get_option( 'wpp_ph_print_post').'"/></a>
	 </div>
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:12px; color:#000000;">
	'.$pt_footer.'
	</td>
  </tr>
</table>
</div><br>
</body>
</html>';

function wpp_print_tabs($title_html, $content_html){
	foreach($title_html as $k => $title){ $h .= ((trim(strip_tags($content_html[$k]))) ? "<h2 style=\"color:#333333\">".$title."</h2><p>".$content_html[$k].'</p>' : '' );} 
	if( !get_option( 'wpp_T_custom_tab')) $h = '';
	return $h;
}

?>