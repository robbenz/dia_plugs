<?php 
global $wpdb ;

switch( $_GET['page'] ) {
	
	####################################################################################################
	#######  PAGE  #####################################################################################
	####################################################################################################

	case 'wpp_saving' : 
	{
	
		switch( $cb ) {
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// CBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			case 'doc_save' : 
			{
				?>
				<table width="100%" class="wpp_box_table" border="0" cellspacing="1">
				  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Maximum image width in DOC document (px)' ) ?>
					</td>
					<td class="wpp_box_th">
                    <?php if( get_option('wpp_save_doc_img_max_width') ){ $wppiw = get_option('wpp_save_doc_img_max_width'); } else{ $wppiw = 500; } ?>
                    <input type="text" name="wpp_save_doc_img_max_width" value="<?php echo $wppiw; ?>" style="width:60px" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product excerpt' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_doc_t_excerpt') ) ? 
					$wpp_doc_t_excerpt_checked['button'][ 'wpp_doc_t_excerpt' ] = 'checked="checked"' : 
					$wpp_doc_t_excerpt_checked['icon'][ 'wpp_doc_t_excerpt' ] = 'checked="checked"';
					?>
					<label for="wpp_doc_t_excerpt_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_doc_t_excerpt_1" 
					<?php echo $wpp_doc_t_excerpt_checked['button'][ 'wpp_doc_t_excerpt' ] ?> 
					name="wpp_doc_t_excerpt" value="1" /> &nbsp;&nbsp;
					<label for="wpp_doc_t_excerpt_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_doc_t_excerpt_0" 
					<?php echo $wpp_doc_t_excerpt_checked['icon'][ 'wpp_doc_t_excerpt' ] ?> 
					name="wpp_doc_t_excerpt" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product date' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_doc_t_date') ) ? 
					$wpp_doc_t_date_checked['button'][ 'wpp_doc_t_date' ] = 'checked="checked"' : 
					$wpp_doc_t_date_checked['icon'][ 'wpp_doc_t_date' ] = 'checked="checked"';
					?>
					<label for="wpp_doc_t_date_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_doc_t_date_1" 
					<?php echo $wpp_doc_t_date_checked['button'][ 'wpp_doc_t_date' ] ?> 
					name="wpp_doc_t_date" value="1" /> &nbsp;&nbsp;
					<label for="wpp_doc_t_date_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_doc_t_date_0" 
					<?php echo $wpp_doc_t_date_checked['icon'][ 'wpp_doc_t_date' ] ?> 
					name="wpp_doc_t_date" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product modified date' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_doc_t_md') ) ? 
					$wpp_doc_t_md_checked['button'][ 'wpp_doc_t_md' ] = 'checked="checked"' : 
					$wpp_doc_t_md_checked['icon'][ 'wpp_doc_t_md' ] = 'checked="checked"';
					?>
					<label for="wpp_doc_t_md_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_doc_t_md_1" 
					<?php echo $wpp_doc_t_md_checked['button'][ 'wpp_doc_t_md' ] ?> 
					name="wpp_doc_t_md" value="1" /> &nbsp;&nbsp;
					<label for="wpp_doc_t_md_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_doc_t_md_0" 
					<?php echo $wpp_doc_t_md_checked['icon'][ 'wpp_doc_t_md' ] ?> 
					name="wpp_doc_t_md" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_th" colspan="2">&nbsp;
					
					</td>
				  </tr>
				 <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Template type' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_save_doc_template') ) ? 
					$wpp_btype_checked['ms'][ 'wpp_save_doc_template' ] = 'checked="checked"' : 
					$wpp_btype_checked['oo'][ 'wpp_save_doc_template' ] = 'checked="checked"';
					?>
					<label for="wpp_save_doc_ms"><?php _e( 'MicrosoftOffice' ) ?> </label>
					<input type="radio" id="wpp_save_doc_ms" 
					<?php echo $wpp_btype_checked['ms'][ 'wpp_save_doc_template' ] ?> 
					name="wpp_save_doc_template" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_doc_oo"><?php _e( 'OpenOffice' ) ?> </label>
					<input type="radio" id="wpp_save_doc_oo" 
					<?php echo $wpp_btype_checked['oo'][ 'wpp_save_doc_template' ] ?> 
					name="wpp_save_doc_template" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Type of button' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_save_doc_button_type') ) ? 
					$wpp_btype_checked['button'][ 'wpp_save_doc_button_type' ] = 'checked="checked"' : 
					$wpp_btype_checked['icon'][ 'wpp_save_doc_button_type' ] = 'checked="checked"';
					?>
					<label for="wpp_save_doc_button"><?php _e( 'Button' ) ?> </label>
					<input type="radio" id="wpp_save_doc_button" 
					<?php echo $wpp_btype_checked['button'][ 'wpp_save_doc_button_type' ] ?> 
					name="wpp_save_doc_button_type" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_doc_icon"><?php _e( 'Icon' ) ?> </label>
					<input type="radio" id="wpp_save_doc_icon" 
					<?php echo $wpp_btype_checked['icon'][ 'wpp_save_doc_button_type' ] ?> 
					name="wpp_save_doc_button_type" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Button image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_doc_button_url" value="<?php echo get_option('wpp_save_doc_button_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo WPP_PATH .'img/'.get_option('wpp_save_doc_button_url') ?>" align="absmiddle" /><br /><br />
					<table width="100%" border="0" style="padding-top:10px;empty-cells: hide; border-collapse:collapse;" cellspacing="0">
					  <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-b.png' ?>" align="absmiddle" /><br />100/doc-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-m.png' ?>" align="absmiddle" /><br />100/doc-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-s.png' ?>" align="absmiddle" /><br />100/doc-s.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-b-g.png' ?>" align="absmiddle" /><br />100/doc-b-g.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-m-g.png' ?>" align="absmiddle" /><br />100/doc-m-g.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/doc-s-g.png' ?>" align="absmiddle" /><br />100/doc-s-g.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/doc-b.png' ?>" align="absmiddle" /><br />000/doc-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/doc-m.png' ?>" align="absmiddle" /><br />000/doc-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   &nbsp; 
						</td>
					  </tr>
					</table>
                    </td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'Icon image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_doc_icon_url" value="<?php echo get_option('wpp_save_doc_icon_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo  WPP_PATH .'img/'.get_option('wpp_save_doc_icon_url') ?>" align="absmiddle" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'String text' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_doc_button_text" size="40" maxlength="255" value="<?php echo get_option('wpp_save_doc_button_text') ?>" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'CSS Code' ) ?><br />
					<span style="color:#777777; font-style:italic">(<?php _e( 'This style code is used in saving document\'s template.' ) ?>)</span>
					</td>
					<td class="wpp_box_th">
					<textarea cols="60" rows="4" name="wpp_save_doc_css"><?php echo get_option('wpp_save_doc_css') ?></textarea>
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_th" colspan="2">
					<p class="submit">
					<input type="submit"  class="button button-primary" name="Submit" value="<?php _e( 'Update Options' ) ?>" />
					</p>
					</td>
				  </tr>
				</table>
				</form>
				<?php
				
			} break ;
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// CBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			case 'pdf_save' : 
			{
				?>
				<table width="100%" class="wpp_box_table" border="0" cellspacing="1">
                 <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Images in PDF document' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_save_pdf_img_show') ) ? 
					$wpp_btype_checked['show'][ 'wpp_save_pdf_img_show' ] = 'checked="checked"' : 
					$wpp_btype_checked['hide'][ 'wpp_save_pdf_img_show' ] = 'checked="checked"';
					?>
					<label for="wpp_save_pdf_img_ss"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_img_ss" 
					<?php echo $wpp_btype_checked['show'][ 'wpp_save_pdf_img_show' ] ?> 
					name="wpp_save_pdf_img_show" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_pdf_img_hh"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_img_hh" 
					<?php echo $wpp_btype_checked['hide'][ 'wpp_save_pdf_img_show' ] ?> 
					name="wpp_save_pdf_img_show" value="0" />
					</td>
				  </tr>
                  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Maximum image width in PDF document (px)' ) ?>
					</td>
					<td class="wpp_box_th">
                    <?php if( get_option('wpp_save_pdf_img_max_width') ){ $wppiw = get_option('wpp_save_pdf_img_max_width'); } else{ $wppiw = 500; } ?>
                    <input type="text" name="wpp_save_pdf_img_max_width" value="<?php echo $wppiw; ?>" style="width:60px" />
					</td>
				  </tr>
                  <tr>
					<td class="wpp_box_td" style="width:40%;">
					<?php _e( 'Extension for Documents in Russian Language' ) ?><br />
                    <span style="color: rgb(119, 119, 119); font-style: italic;">
                    (If your blog is Russian you should turn on this extension.<br />Если ваш блог на Русском языке, вы должны включить это расширение.)</span>
					</td>
					<td class="wpp_box_th" style="vertical-align:top;">
					<?php 
					( get_option('wpp_save_pdf_rus') ) ? 
					$wpp_btype_checked['show'][ 'wpp_save_pdf_rus' ] = 'checked="checked"' : 
					$wpp_btype_checked['hide'][ 'wpp_save_pdf_rus' ] = 'checked="checked"';
					?>
					<label for="wpp_save_pdf_rus_on"><?php _e( 'Turn On' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_rus_on" 
					<?php echo $wpp_btype_checked['show'][ 'wpp_save_pdf_rus' ] ?> 
					name="wpp_save_pdf_rus" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_pdf_rus_off"><?php _e( 'Turn Off' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_rus_off" 
					<?php echo $wpp_btype_checked['hide'][ 'wpp_save_pdf_rus' ] ?> 
					name="wpp_save_pdf_rus" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Type of button' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_save_pdf_button_type') ) ? 
					$wpp_btype_checked['button'][ 'wpp_save_pdf_button_type' ] = 'checked="checked"' : 
					$wpp_btype_checked['icon'][ 'wpp_save_pdf_button_type' ] = 'checked="checked"';
					?>
					<label for="wpp_save_pdf_button"><?php _e( 'Button' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_button" 
					<?php echo $wpp_btype_checked['button'][ 'wpp_save_pdf_button_type' ] ?> 
					name="wpp_save_pdf_button_type" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_pdf_icon"><?php _e( 'Icon' ) ?> </label>
					<input type="radio" id="wpp_save_pdf_icon" 
					<?php echo $wpp_btype_checked['icon'][ 'wpp_save_pdf_button_type' ] ?> 
					name="wpp_save_pdf_button_type" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Button image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_pdf_button_url" value="<?php echo get_option('wpp_save_pdf_button_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo WPP_PATH .'img/'.get_option('wpp_save_pdf_button_url') ?>" align="absmiddle" /><br /><br />
					<table width="100%" border="0" style="padding-top:10px;empty-cells: hide; border-collapse:collapse;" cellspacing="0">
					  <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-b.png' ?>" align="absmiddle" /><br />100/pdf-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-m.png' ?>" align="absmiddle" /><br />100/pdf-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-s.png' ?>" align="absmiddle" /><br />100/pdf-s.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-b-g.png' ?>" align="absmiddle" /><br />100/pdf-b-g.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-m-g.png' ?>" align="absmiddle" /><br />100/pdf-m-g.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/pdf-s-g.png' ?>" align="absmiddle" /><br />100/pdf-s-g.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/pdf-b.png' ?>" align="absmiddle" /><br />000/pdf-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/pdf-m.png' ?>" align="absmiddle" /><br />000/pdf-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   &nbsp; 
						</td>
					  </tr>
					</table>
                    </td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'Icon image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_pdf_icon_url" value="<?php echo get_option('wpp_save_pdf_icon_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo  WPP_PATH .'img/'.get_option('wpp_save_pdf_icon_url') ?>" align="absmiddle" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'String text' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_pdf_button_text" size="40" maxlength="255" value="<?php echo get_option('wpp_save_pdf_button_text') ?>" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_th" colspan="2">
					<p class="submit">
					<input type="submit"  class="button button-primary"  name="Submit" value="<?php _e( 'Update Options' ) ?>" />
					</p>
					</td>
				  </tr>
				</table>
				</form>
				<?php
				
			} break ;
		
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// CBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
		}
		
	} break ;
	####################################################################################################
	#######  PAGE  #####################################################################################
	####################################################################################################
	case 'wpp_print' : 
	{
	
		switch ( $cb ) {
		
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// CBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			case 'print_img' : 
			{
				?>
				<form id="wpp_form_saving" name="wpp_form_print" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" name="wpp_hidden" value="wpp_print">
				<table width="100%" class="wpp_box_table" border="0" cellspacing="1">
				  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Type of button' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_save_print_button_type') ) ? 
					$wpp_btype_checked['button'][ 'wpp_save_print_button_type' ] = 'checked="checked"' : 
					$wpp_btype_checked['icon'][ 'wpp_save_print_button_type' ] = 'checked="checked"';
					?>
					<label for="wpp_save_print_button"><?php _e( 'Button' ) ?> </label>
					<input type="radio" id="wpp_save_print_button" 
					<?php echo $wpp_btype_checked['button'][ 'wpp_save_print_button_type' ] ?> 
					name="wpp_save_print_button_type" value="1" /> &nbsp;&nbsp;
					<label for="wpp_save_print_icon"><?php _e( 'Icon' ) ?> </label>
					<input type="radio" id="wpp_save_print_icon" 
					<?php echo $wpp_btype_checked['icon'][ 'wpp_save_print_button_type' ] ?> 
					name="wpp_save_print_button_type" value="0" />
					</td>
				  </tr>
                  <tr>
					<td class="wpp_box_td" style="width:40%">
					<?php _e( 'Maximum image width in Print document (px)' ) ?>
					</td>
					<td class="wpp_box_th">
                    <?php if( get_option('wpp_save_print_img_max_width') ){ $wppiw = get_option('wpp_save_print_img_max_width'); } else{ $wppiw = 500; } ?>
                    <input type="text" name="wpp_save_print_img_max_width" value="<?php echo $wppiw; ?>" style="width:60px" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Button image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_print_button_url" value="<?php echo get_option('wpp_save_print_button_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo WPP_PATH .'img/'.get_option('wpp_save_print_button_url') ?>" align="absmiddle" /><br /><br />
					<table width="100%" border="0" style="padding-top:10px;empty-cells: hide; border-collapse:collapse;" cellspacing="0">
					  <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-b.png' ?>" align="absmiddle" /><br />100/print-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-m.png' ?>" align="absmiddle" /><br />100/print-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-s.png' ?>" align="absmiddle" /><br />100/print-s.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-b-g.png' ?>" align="absmiddle" /><br />100/print-b-g.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-m-g.png' ?>" align="absmiddle" /><br />100/print-m-g.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   <img src="<?php echo WPP_PATH .'img/100/print-s-g.png' ?>" align="absmiddle" /><br />100/print-s-g.png 
						</td>
					  </tr>
                      <tr valign="top">
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%; padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/print-b.png' ?>" align="absmiddle" /><br />000/print-b.png 
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4; width:33%;padding:3px;font-size:11px; background:#FFFFFF;">
						   <img src="<?php echo WPP_PATH .'img/000/print-m.png' ?>" align="absmiddle" /><br />000/print-m.png
						</td>
						<td valign="middle" align="center" style="border:dotted 2px #d4d4d4;width:33%; padding:3px;font-size:11px">
						   &nbsp; 
						</td>
					  </tr>
					</table>
                    </td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'Icon image URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_print_icon_url" value="<?php echo get_option('wpp_save_print_icon_url') ?>" size="40" maxlength="255" /> &nbsp; <img src="<?php echo  WPP_PATH .'img/'.get_option('wpp_save_print_icon_url') ?>" align="absmiddle" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'String text' ) ?>
					</td>
					<td class="wpp_box_th">
					<input type="text" name="wpp_save_print_button_text" size="40" maxlength="255" value="<?php echo get_option('wpp_save_print_button_text') ?>" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td" >
					<?php _e( 'CSS Code' ) ?><br />
					<span style="color:#777777; font-style:italic">(<?php _e( 'This style code is used in printing template.' ) ?>)</span>
					</td>
					<td class="wpp_box_th">
					<textarea cols="60" rows="4" name="wpp_print_css"><?php echo get_option('wpp_print_css') ?></textarea>
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_th" colspan="2">
					<p class="submit">
					<input type="submit"  class="button button-primary" name="Submit" value="<?php _e( 'Update Options' ) ?>" />
					</p>
					</td>
				  </tr>
				</table>
				</form>
				<?php
			}break ;
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// CBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
			case 'print_template' : 
			{
				?>
				<form id="wpp_form_saving_" name="wpp_form_print_" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="wpp_hidden" value="wpp_pt">
				<table width="100%" class="wpp_box_table" border="0" cellspacing="1">
				  
                  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product Header Print Date' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_head_date') ) ? 
					$wpp_pt_head_date_checked['button'][ 'wpp_pt_head_date' ] = 'checked="checked"' : 
					$wpp_pt_head_date_checked['icon'][ 'wpp_pt_head_date' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_head_date_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_head_date_1" 
					<?php echo $wpp_pt_head_date_checked['button'][ 'wpp_pt_head_date' ] ?> 
					name="wpp_pt_head_date" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_head_date_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_head_date_0" 
					<?php echo $wpp_pt_head_date_checked['icon'][ 'wpp_pt_head_date' ] ?> 
					name="wpp_pt_head_date" value="0" />
					</td>
				  </tr>
                  
                  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product Header Site Name' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_head_site') ) ? 
					$wpp_pt_head_site_checked['button'][ 'wpp_pt_head_site' ] = 'checked="checked"' : 
					$wpp_pt_head_site_checked['icon'][ 'wpp_pt_head_site' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_head_site_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_head_site_1" 
					<?php echo $wpp_pt_head_site_checked['button'][ 'wpp_pt_head_site' ] ?> 
					name="wpp_pt_head_site" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_head_site_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_head_site_0" 
					<?php echo $wpp_pt_head_site_checked['icon'][ 'wpp_pt_head_site' ] ?> 
					name="wpp_pt_head_site" value="0" />
					</td>
				  </tr>
                  
                  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product Header Page URL' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_head_url') ) ? 
					$wpp_pt_head_url_checked['button'][ 'wpp_pt_head_url' ] = 'checked="checked"' : 
					$wpp_pt_head_url_checked['icon'][ 'wpp_pt_head_url' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_head_url_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_head_url_1" 
					<?php echo $wpp_pt_head_url_checked['button'][ 'wpp_pt_head_url' ] ?> 
					name="wpp_pt_head_url" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_head_url_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_head_url_0" 
					<?php echo $wpp_pt_head_url_checked['icon'][ 'wpp_pt_head_url' ] ?> 
					name="wpp_pt_head_url" value="0" />
					</td>
				  </tr>
                  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product Title' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_title') ) ? 
					$wpp_pt_title_checked['button'][ 'wpp_pt_title' ] = 'checked="checked"' : 
					$wpp_pt_title_checked['icon'][ 'wpp_pt_title' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_title_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_title_1" 
					<?php echo $wpp_pt_title_checked['button'][ 'wpp_pt_title' ] ?> 
					name="wpp_pt_title" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_title_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_title_0" 
					<?php echo $wpp_pt_title_checked['icon'][ 'wpp_pt_title' ] ?> 
					name="wpp_pt_title" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product images' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_image') ) ? 
					$wpp_pt_image_checked['button'][ 'wpp_pt_image' ] = 'checked="checked"' : 
					$wpp_pt_image_checked['icon'][ 'wpp_pt_image' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_image_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_image_1" 
					<?php echo $wpp_pt_image_checked['button'][ 'wpp_pt_image' ] ?> 
					name="wpp_pt_image" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_image_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_image_0" 
					<?php echo $wpp_pt_image_checked['icon'][ 'wpp_pt_image' ] ?> 
					name="wpp_pt_image" value="0" />
					</td>
				  </tr>
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product date' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_date') ) ? 
					$wpp_pt_date_checked['button'][ 'wpp_pt_date' ] = 'checked="checked"' : 
					$wpp_pt_date_checked['icon'][ 'wpp_pt_date' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_date_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_date_1" 
					<?php echo $wpp_pt_date_checked['button'][ 'wpp_pt_date' ] ?> 
					name="wpp_pt_date" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_date_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_date_0" 
					<?php echo $wpp_pt_date_checked['icon'][ 'wpp_pt_date' ] ?> 
					name="wpp_pt_date" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product modified date' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_md') ) ? 
					$wpp_pt_md_checked['button'][ 'wpp_pt_md' ] = 'checked="checked"' : 
					$wpp_pt_md_checked['icon'][ 'wpp_pt_md' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_md_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_md_1" 
					<?php echo $wpp_pt_md_checked['button'][ 'wpp_pt_md' ] ?> 
					name="wpp_pt_md" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_md_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_md_0" 
					<?php echo $wpp_pt_md_checked['icon'][ 'wpp_pt_md' ] ?> 
					name="wpp_pt_md" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Product links' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_links') ) ? 
					$wpp_pt_links_checked['button'][ 'wpp_pt_links' ] = 'checked="checked"' : 
					$wpp_pt_links_checked['icon'][ 'wpp_pt_links' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_links_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_links_1" 
					<?php echo $wpp_pt_links_checked['button'][ 'wpp_pt_links' ] ?> 
					name="wpp_pt_links" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_links_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_links_0" 
					<?php echo $wpp_pt_links_checked['icon'][ 'wpp_pt_links' ] ?> 
					name="wpp_pt_links" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_td">
					<?php _e( 'Footer information' ) ?>
					</td>
					<td class="wpp_box_th">
					<?php 
					( get_option('wpp_pt_header') ) ? 
					$wpp_pt_header_checked['button'][ 'wpp_pt_header' ] = 'checked="checked"' : 
					$wpp_pt_header_checked['icon'][ 'wpp_pt_header' ] = 'checked="checked"';
					?>
					<label for="wpp_pt_header_1"><?php _e( 'Show' ) ?> </label>
					<input type="radio" id="wpp_pt_header_1" 
					<?php echo $wpp_pt_header_checked['button'][ 'wpp_pt_header' ] ?> 
					name="wpp_pt_header" value="1" /> &nbsp;&nbsp;
					<label for="wpp_pt_header_0"><?php _e( 'Hidden' ) ?> </label>
					<input type="radio" id="wpp_pt_header_0" 
					<?php echo $wpp_pt_header_checked['icon'][ 'wpp_pt_header' ] ?> 
					name="wpp_pt_header" value="0" />
					</td>
				  </tr>
				  
				  <tr>
					<td class="wpp_box_th" colspan="2">
					<p class="submit">
					<input type="submit"  class="button button-primary" name="Submit" value="<?php _e( 'Update Options' ) ?>" />
					</p>
					</td>
				  </tr>
				</table>
				</form>
				<?php
			}break ;
			////////////////////////////////////////////////////////////////////////////////////////////
			//////// SBOX //////////////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////
		}
		
	}
	
}

?>
			