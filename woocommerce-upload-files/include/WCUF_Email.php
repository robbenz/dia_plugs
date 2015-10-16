<?php
class WCUF_Email  
{
	public function __construct() 
	{
	}
	public function trigger( /* $order_id */$links_to_notify_via_mail, $order , $attachment = array()) {
	 
		/* global $woocommerce; */

		$recipient = get_option( 'admin_email' );
		$subject = __('User submitted new upload for order #', 'woocommerce-files-upload').$order->id;
		$content = __('User submitted new upload for order #', 'woocommerce-files-upload').'<a href="'.admin_url('post.php?post='.$order->id.'&action=edit').'">'.$order->id.'</a>';
		$content .= __('<br/>You can directly download by clicking on following links:', 'woocommerce-files-upload');
		$content .= '<table>';
		foreach($links_to_notify_via_mail as $download)
		{
			$content .='<tr><a href="'.$download['url'].'">'.$download['title'].'</a></tr>';
		}
		$content .= '</table>';
	
		/* if(!class_exists('WC_Email'))
			include_once( WP_PLUGIN_DIR.'/woocommerce/includes/emails/class-wc-email.php' ); */
		//$mail = new WC_Emails();
		$mail = WC()->mailer();
		$email_heading = get_bloginfo('name');
		
		/* ob_start();
		$mail->email_header($email_heading );
        $message =  ob_get_contents();
		ob_end_clean();
		$message .=$content;
		
		ob_start();
		$mail->email_footer();
        $message .=  ob_get_contents();
		ob_end_clean(); */
		
		ob_start();
		$mail->email_header($email_heading );
		echo $content;
		$mail->email_footer();
        $message .=  ob_get_contents();
		ob_end_clean(); 
		
		$mail->send( $recipient, $subject, $message, "Content-Type: text/html\r\n", $attachment);
		
	}
} 