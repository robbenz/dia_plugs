<?php
/**
 * Setup the Email Preview page redirect
 *
 * @since      3.0
 */
class VFB_Pro_Preview_Email {

	/**
	 * Setup hooks when loaded
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		//Template fallback
		add_action( 'template_redirect', array( $this, 'preview_redirect' ) );
	}

	/**
	 * Check which page is being viewed
	 *
	 * @access public
	 * @return void
	 */
	public function preview_redirect() {
		global $wp;

		$title        = 'VFB Pro - Email Preview';
		$preview_page = get_page_by_title( $title );

		if ( !isset( $wp->query_vars['page_id'] ) )
			return;

		// Check page slug
	    if ( $wp->query_vars['page_id'] == $preview_page->ID ) {
	        $return_template = VFB_PLUGIN_DIR . 'inc/preview-email.php';

	        $this->do_preview_redirect( $return_template );
	    }
	}

	/**
	 * Display our preview URL
	 *
	 * @access public
	 * @param mixed $url
	 * @return void
	 */
	public function do_preview_redirect( $url ) {
		global $wp_query;

		// Get Form ID
		$form_id = isset( $_GET['vfb-form-id'] ) ? absint( $_GET['vfb-form-id'] ) : '';

	    if ( have_posts() ) {
		    // Clean the output buffer
		    ob_start();

		    // Include the file
	        include( $url );

	        // Get the contents of the file
	        $content = ob_get_clean();

	        // Output the file, but apply design settings and process template tags
	        echo $this->process_email_design( $form_id, $content );

	        die();
	    }
	    else {
	        $wp_query->is_404 = true;
	    }
	}

	/**
	 * Process the email templating
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $template
	 * @return void
	 */
	public function process_email_design( $form_id, $template ) {
		$vfbdb = new VFB_Pro_Data();
		$vfbp_email_template_data  = $vfbdb->get_email_design_settings( $form_id );

		$template   = isset( $vfbp_email_template_data['email-template']             )  ? $vfbp_email_template_data['email-template']             : $template;
		$color_bg   = isset( $vfbp_email_template_data['email-design']['color-bg']   )	? $vfbp_email_template_data['email-design']['color-bg']   : '#fbfbfb';
		$color_link = isset( $vfbp_email_template_data['email-design']['color-link'] ) 	? $vfbp_email_template_data['email-design']['color-link'] : '#41637e';
		$color_h1   = isset( $vfbp_email_template_data['email-design']['color-h1']   ) 	? $vfbp_email_template_data['email-design']['color-h1']   : '#565656';
		$font_h1    = isset( $vfbp_email_template_data['email-design']['font-h1']    ) 	? $vfbp_email_template_data['email-design']['font-h1']    : 'Arial';
		$color_h2   = isset( $vfbp_email_template_data['email-design']['color-h2']   ) 	? $vfbp_email_template_data['email-design']['color-h2']   : '#555555';
		$font_h2    = isset( $vfbp_email_template_data['email-design']['font-h2']    ) 	? $vfbp_email_template_data['email-design']['font-h2']	  : 'Georgia';
		$color_h3   = isset( $vfbp_email_template_data['email-design']['color-h3']   ) 	? $vfbp_email_template_data['email-design']['color-h3']   : '#555555';
		$font_h3    = isset( $vfbp_email_template_data['email-design']['font-h3']    ) 	? $vfbp_email_template_data['email-design']['font-h3']	  : 'Georgia';
		$color_text = isset( $vfbp_email_template_data['email-design']['color-text'] )	? $vfbp_email_template_data['email-design']['color-text'] : '#565656';
		$font_text  = isset( $vfbp_email_template_data['email-design']['font-text']  ) 	? $vfbp_email_template_data['email-design']['font-text']  : 'Georgia';
		$header_img = isset( $vfbp_email_template_data['email-design']['header-img'] ) 	? $vfbp_email_template_data['email-design']['header-img'] : '';
		$link_love  = isset( $vfbp_email_template_data['email-design']['link-love']  ) 	? $vfbp_email_template_data['email-design']['link-love']  : '';

		// Hide the Link Love text, if setting is checked
		$link_love_text = 1 == $link_love ? '' : __( 'This email was built and sent using <a href="http://vfbpro.com">VFB Pro</a>.', 'vfb-pro' );

		// Get header image URL from attachment ID
		$header_img_src = $this->get_header_image( $header_img );

		$defaults = array(
			'color-bg'      => $color_bg,
			'color-link'    => $color_link,
			'color-h1'      => $color_h1,
			'font-h1'       => $font_h1,
			'color-h2'      => $color_h2,
			'font-h2'       => $font_h2,
			'color-h3'      => $color_h3,
			'font-h3'       => $font_h3,
			'color-text'    => $color_text,
			'font-text'     => $font_text,
			'header-img'    => $header_img_src,
			'vfb-link-love' => $link_love_text,
		);

		$template = $this->email_templating( $form_id, $template, $defaults );

		return $template;
	}

	/**
	 * Email templating to replace template tags: i.e. [color-bg]
	 *
	 * @access public
	 * @param mixed $form_id
	 * @param mixed $key
	 * @param mixed $defaults
	 * @return void
	 */
	public function email_templating( $form_id, $key, $defaults ) {
		$search = preg_match_all( '/\[(.*?)\]/', $key, $matches );

		if ( $search ) {
			foreach ( $matches[1] as $match ) {
				if ( isset( $defaults[ $match ] ) )
					$key = str_ireplace( "[$match]", $defaults[ $match ], $key );
			}
		}

		return $key;
	}

	/**
	 * Email templating to replace header image tag [header-img]
	 *
	 * @access public
	 * @param mixed $attachment_id
	 * @return void
	 */
	public function get_header_image( $attachment_id ) {
		if ( empty( $attachment_id ) )
			return '';

		$attachment_src = wp_get_attachment_image_src( $attachment_id, 'full' );

		if ( is_array( $attachment_src ) ) {
			return sprintf( '<img src="%s" />', $attachment_src[0] );
		}

		return '';
	}
}