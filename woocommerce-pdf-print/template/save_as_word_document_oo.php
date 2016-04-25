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

$doc = '<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset='.get_option('blog_charset').'">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Universal Post Manager by">
<meta name=Originator content="ProfProjects.com">
<title>'. $doc_title.' : '. get_option('blogname') .' : '.get_option('siteurl').'</title>
<style>

<!--
 /* Font Definitions */

 @font-face

	{font-family:"Cambria Math";
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:204;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1107304683 0 0 159 0;}
	
@font-face
	{font-family:Cambria;
	panose-1:2 4 5 3 5 4 6 3 2 4;
	mso-font-charset:204;
	mso-generic-font-family:roman;
	mso-font-pitch:variable;
	mso-font-signature:-1610611985 1073741899 0 0 159 0;}
 /* Style Definitions */

 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-parent:"";
	margin:0in;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman","serif";
	mso-fareast-font-family:"Times New Roman";}
h2
	{mso-style-priority:9;
	mso-style-unhide:no;
	mso-style-qformat:yes;
	mso-style-link:"";
	mso-margin-top-alt:auto;
	margin-right:0in;
	mso-margin-bottom-alt:auto;
	margin-left:0in;
	mso-pagination:widow-orphan;
	mso-outline-level:2;
	font-size:18.0pt
	font-family:"Times New Roman","serif";}

a:link, span.MsoHyperlink
	{mso-style-noshow:yes;
	mso-style-priority:99;
	color:blue;
	text-decoration:underline;
	text-underline:single;}

a:visited, span.MsoHyperlinkFollowed

	{mso-style-noshow:yes;
	mso-style-priority:99;
	color:purple;
	text-decoration:underline;
	text-underline:single;}

span.2

	{mso-style-name:"";
	mso-style-noshow:yes;
	mso-style-priority:9;
	mso-style-unhide:no;
	mso-style-locked:yes;
	mso-style-link:"";
	mso-ansi-font-size:13.0pt;
	mso-bidi-font-size:13.0pt;
	font-family:"Cambria","serif";
	mso-ascii-font-family:Cambria;
	mso-fareast-font-family:"Times New Roman";
	mso-hansi-font-family:Cambria;
	mso-bidi-font-family:"Times New Roman";
	color:#4F81BD;
	font-weight:bold;}

@page Section1

	{size:595.3pt 841.9pt;
	margin:56.7pt 42.5pt 56.7pt 85.05pt;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}

div.Section1

	{page:Section1;}
'.get_option('pppm_save_doc_css').'

img {max-width:500px;}

-->

</style>
</head>
<body link=blue vlink=purple style=\'tab-interval:35.4pt;text-align:'.get_option('pppm_save_text_align').'\'>
<div class=Section1>
<div align=center>
<table class=MsoNormalTable border=0 cellspacing=1 cellpadding=0 width=700 style=\'width:525.0pt;mso-cellspacing:.7pt;mso-yfti-tbllook:1184;mso-padding-alt:3.75pt 3.75pt 3.75pt 3.75pt;text-align:'.get_option('pppm_save_text_align').'\'>

	<tr style=\'mso-yfti-irow:0;mso-yfti-firstrow:yes\'>
		<td style=\'padding:3.75pt 3.75pt 3.75pt 3.75pt\'>
	
		<p class=MsoNormal>
		<span style=\'font-size:9.0pt;color:#000000;\'>
		This page was exported from '.get_option('blogname').' 
		[ </span>
		<span style=\'font-size:9.0pt;color:#000000\'>
			<a href="'.get_option('siteurl').'" target="_blank">
				'.get_option('siteurl').'
			</a>
		</span>
		<span style=\'font-size:9.0pt;color:#000000;\'> ]
		<br>
		Export date: '. date("D M j G:i:s Y / O ") .' GMT 
		<o:p></o:p>
		</span>
		' . $doc_head . '
		</p>

	  </td>
	</tr>

 	<tr style=\'mso-yfti-irow:1\'>
		<td style=\'background:#FFFFFF;padding:3.75pt 3.75pt 3.75pt 3.75pt\'>

  	  <h1>'. $wpp_title .'</h1>
	'.str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:40px;" ', $wpp_featured_image).' 
	'.(($wpp_price_html) ? '<p style="font-size:20px; color:#006600;">' .$wpp_price.'</p>
	' : '').''.(($wpp_sku) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_sku' ).'</b>: '.$wpp_sku.'</p>
	' : '' ).''.(($wpp_stock) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_in_stock' ).'</b>: '.$wpp_stock.'</p>
	' : '' ).''.(($wpp_rating) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_rating' ).'</b>: '.$wpp_rating.'</p>
	' : '' ).''.(($wpp_categories) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_prod_cats' ).'</b>: '.$wpp_categories.'</p>
	' : '' ).''.(($wpp_tags) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_prod_tags' ).'</b>: '.$wpp_tags.'</p>
	' : '' ).''.(($wpp_permalink) ? '<p style="font-size:16px;"><b>'.get_option( 'wpp_ph_prod_page' ).'</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p>
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p><div style="clear:both"></div>
	'.(($wpp_has_variants) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_vars' )."</h2><p>".$wpp_variants.'</p>
	' : "\r\n" ).''.(($wpp_summary) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_summ' )."</h2><p>".$wpp_summary.'</p>
	' : '' ).''.(($wpp_description) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_desc' )."</h2><p>".$wpp_description.'</p>
	' : '' ).''.((trim(strip_tags($attribute_html))) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_attr' )."</h2><p>".$attribute_html.'</p>
	' : '' ).''.wpp_doc_tabs($custom_tab_title_html, $custom_tab_content_html).'
	'.((!empty($wpp_gallery)) ? "<h2 style=\"color:#333333\">".get_option( 'wpp_ph_prod_gall' )."</h2>".str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:5px;" ', $wpp_gallery_images) .'
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p>
	<div style="clear:both"></div>
	 </p>

	  </td>
	</tr>

	<tr style=\'mso-yfti-irow:3\'>
		<td style=\'background:#F9F9F9;padding:3.75pt 3.75pt 3.75pt 3.75pt\'>
		
			  <p class=MsoNormal>
				  <span style=\'font-size:10.0pt;color:#000000;\'>
					'.$doc_t_date.'
					'.$doc_t_md_date.' 
					<o:p></o:p>
				   </span>
			  </p>
		
		</td>
	</tr>

	<tr style=\'mso-yfti-irow:4;mso-yfti-lastrow:yes\'>
	  <td style=\'background:#F9F9F9;padding:3.75pt 3.75pt 3.75pt 3.75pt\'>
		<p class=MsoNormal>
			<span style=\'font-size:9.0pt;color:#666666;\'>
			Product export as MS Document by <a href="http://www.gvectors.com/?wpp" target="_blank">WooCommerce PDF & Print</a> plugin.
			</span>
		</p>
	  </td>
	</tr>
</table>

</div>
<p class=MsoNormal><o:p>&nbsp;</o:p></p>
</div>
</body>
</html>';

function wpp_oo_tabs($title_html, $content_html){
	foreach($title_html as $k => $title){ $h .= ((trim(strip_tags($content_html[$k]))) ? "<h2 style=\"color:#333333\">".$title."</h2><p>".$content_html[$k].'</p>' : '' );}
	if( !get_option( 'wpp_T_custom_tab')) $h = '';
	return $h;
}

?>