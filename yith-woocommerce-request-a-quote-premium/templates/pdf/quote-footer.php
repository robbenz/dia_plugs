<?php
if( function_exists('icl_get_languages') && class_exists('YITH_YWRAQ_Multilingual_Email')  ) {
	global $sitepress;
	$lang = get_post_meta( $order_id, 'wpml_language', true );
	YITH_Request_Quote_Premium()->change_pdf_language( $lang );
}
?>
<div class="footer">
	<?php // if ( $footer != '' ): ?>
			<?php // echo $footer ?>
		<div style="line-height:16px;" class="footer-content">
			<p style="color:#00426a; line-height:16px;font-size:14px;">Shop over 1,000,000 products on <a style="text-decoration:none; color:#00426a; font-size:14px;" href="http://www.diamedicalusa.com">DiaMedicalUSA.com!</a></p>
			<p style="color:#00426a; line-height:16px;font-size:14px;">Call Us Toll Free: (877) 593-6011 (M-F: 7-6 EST)</p>
			<p style="color:#00426a; line-height:16px;font-size:14px;">Email: orders@diamedicalusa.com</p>

</div>
	<?php  // endif; ?>
	<?php if ( $pagination != '' ): ?>
		<div class="page"><?php echo __( 'Page', 'yith-woocommerce-request-a-quote' ) ?> <span class="pagenum"></span>
		</div>
	<?php endif ?>
</div>
