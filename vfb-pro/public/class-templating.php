<?php
/**
 * Processes template tags
 *
 * @since      3.0
 */
class VFB_Pro_Templating {
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Process template tags such as form:FormID, entry:EntryID, etc
	 *
	 * @access public
	 * @param mixed $content
	 * @param mixed $entry_id
	 * @param mixed $form_id
	 * @return void
	 */
	public function general( $content, $entry_id, $form_id ) {
		$vfbdb = new VFB_Pro_Data();
		$form_settings = $vfbdb->get_form_settings( $form_id );

		$title     = isset( $form_settings['title'] ) ? $form_settings['title'] : '';
		$entry_num = isset( $form_settings['data']['last-entry'] ) ? $form_settings['data']['last-entry'] : $entry_id;

		$defaults = array(
			'form:FormID'	    => $form_id,
			'form:Title'	    => $title,
			'entry:EntryID'	    => $entry_num,
			'entry:DateCreated' => get_the_date( '', $entry_id ),
		);

		$field = preg_match_all( '/\[entry:Field(\d+)\]/', $content, $matches );

		if ( $field ) {
			foreach ( $matches[1] as $match ) {
				$meta = get_post_meta( $entry_id, "_vfb_field-$match", true );
				$content = str_ireplace( "[entry:Field$match]", $meta, $content );
			}
		}

		$search = preg_match_all( '/\[(.*?)\]/', $content, $matches );

		if ( $search ) {
			foreach ( $matches[1] as $match ) {
				if ( isset( $defaults[ $match ] ) )
					$content = str_ireplace( "[$match]", $defaults[ $match ], $content );
			}
		}

		return $content;
	}

	/**
	 * Process Email Design CSS template tags.
	 *
	 * @access public
	 * @param mixed $template
	 * @param mixed $settings
	 * @return void
	 */
	public function css( $template, $settings ) {
		$color_bg     = isset( $settings['email-design']['color-bg']   ) ? $settings['email-design']['color-bg']   : '#fbfbfb';
		$color_link   = isset( $settings['email-design']['color-link'] ) ? $settings['email-design']['color-link'] : '#41637e';
		$color_h1     = isset( $settings['email-design']['color-h1']   ) ? $settings['email-design']['color-h1']   : '#565656';
		$font_h1      = isset( $settings['email-design']['font-h1']    ) ? $settings['email-design']['font-h1']    : 'Arial';
		$color_h2     = isset( $settings['email-design']['color-h2']   ) ? $settings['email-design']['color-h2']   : '#555555';
		$font_h2      = isset( $settings['email-design']['font-h2']    ) ? $settings['email-design']['font-h2']    : 'Georgia';
		$color_h3     = isset( $settings['email-design']['color-h3']   ) ? $settings['email-design']['color-h3']   : '#555555';
		$font_h3      = isset( $settings['email-design']['font-h3']    ) ? $settings['email-design']['font-h3']    : 'Georgia';
		$color_text   = isset( $settings['email-design']['color-text'] ) ? $settings['email-design']['color-text'] : '#565656';
		$font_text    = isset( $settings['email-design']['font-text']  ) ? $settings['email-design']['font-text']  : 'Georgia';
		$font_family  = $this->font_family( $font_text );
		$header_img   = isset( $settings['email-design']['header-img'] ) ? $settings['email-design']['header-img'] : '';
		$link_love    = isset( $settings['email-design']['link-love']  ) ? $settings['email-design']['link-love']   : '';

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

		$search = preg_match_all( '/\[(.*?)\]/', $template, $matches );

		if ( $search ) {
			foreach ( $matches[1] as $match ) {
				if ( isset( $defaults[ $match ] ) )
					$template = str_ireplace( "[$match]", $defaults[ $match ], $template );
			}
		}

		return $template;
	}

	/**
	 * Process Email Design fields loop template tag
	 *
	 * @access public
	 * @param mixed $template
	 * @param mixed $format
	 * @param mixed $entry_id
	 * @param mixed $form_id
	 * @return void
	 */
	public function all_fields( $template, $format, $entry_id, $form_id ) {
		$vfbdb      = new VFB_Pro_Data();
		$fields     = $vfbdb->get_fields( $form_id, "AND field_type != 'submit' ORDER BY field_order ASC" );
		$design     = $vfbdb->get_email_design_settings( $form_id );
		$skip_empty = isset( $design['email-design']['skip-empty'] ) ? $design['email-design']['skip-empty'] : '';
		$output = '';

		foreach ( $fields as $field ) {
			$label    = isset( $field['data']['label'] ) ? $field['data']['label'] : '';
			$field_id = $field['id'];
			$meta_key = '_vfb_field-' . $field_id;
			$value    = $vfbdb->get_entry_meta_by_id( $entry_id, $meta_key );

			// If option to skip empties and value is empty, go to next field
			if ( 1 == $skip_empty && empty( $value ) )
				continue;

			if ( 'html' == $format ) {
				// Link URLs and File Uploads
				if ( in_array( $field['field_type'], array( 'url', 'file-upload' ) ) ) {
					if ( !empty( $value ) ) {
						$urls       = explode( ',', $value );
						$url_output = '';

						foreach ( $urls as $url ) {
							$url_output .= sprintf( '<a href="%1$s">%1$s</a><br />', $url );
						}

						$value = $url_output;
					}
				}

				// If value is empty, add a non-breaking space so it doesn't mess with table
				$value = !empty( $value ) ? $value : '&nbsp;';

				$output .= '<tr>';
					$output .= sprintf( '<td>%s</td>', $label );
		            $output .= sprintf( '<td class="align right">%s</td>', $value );
	            $output .= '</tr>';
            }
            else {
	            $output .= sprintf( '%s: %s' . "\n", $label, $value );
            }
		}

		$template = str_ireplace( '[vfb-fields]', $output, $template );

		return $template;
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



	/**
	 * font_family function.
	 *
	 * @access private
	 * @param mixed $font
	 * @return void
	 */
	private function font_family( $font ) {
		if ( empty( $font ) )
			return $font;

		$stack = '';
		switch ( $font ) {
			// Sans Serif
			case 'Arial' :
				$stack = "Arial, 'Helvetica Neue', Helvetica, sans-serif";
				break;

			case 'Arial Black' :
				$stack = "'Arial Black', 'Arial Bold', Gadget, sans-serif";
				break;

			case 'Century Gothic' :
				$stack = "'Century Gothic', CenturyGothic, AppleGothic, sans-serif";
				break;

			case 'Geneva' :
				$stack = "Geneva, Tahoma, Verdana, sans-serif";
				break;

			case 'Helvetica' :
				$stack = "'Helvetica Neue', Helvetica, Arial, sans-serif";
				break;

			case 'Lucida Grande' :
				$stack = "'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Geneva, Verdana, sans-serif";
				break;

			case 'Tahoma' :
				$stack = "Tahoma, Verdana, Segoe, sans-serif";
				break;

			case 'Trebuchet MS' :
				$stack = "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif";
				break;

			case 'Verdana' :
				$stack = "Verdana, Geneva, sans-serif";
				break;

			// Serif
			case 'Cambria' :
				$stack = "Cambria, Georgia, serif";
				break;

			case 'Garamond' :
				$stack = "Garamond, Baskerville, 'Baskerville Old Face', 'Hoefler Text', 'Times New Roman', serif";
				break;

			case 'Georgia' :
				$stack = "Georgia, Times, 'Times New Roman', serif";
				break;

			case 'Goudy Old Style' :
				$stack = "'Goudy Old Style', Garamond, 'Big Caslon', 'Times New Roman', serif";
				break;

			case 'Lucida Bright' :
				$stack = "'Lucida Bright', Georgia, serif";
				break;

			case 'Palatino' :
				$stack = "Palatino, 'Palatino Linotype', 'Palatino LT STD', 'Book Antiqua', Georgia, serif";
				break;

			case 'Times New Roman' :
				$stack = "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif";
				break;

			// Monospaced
			case 'Consolas' :
				$stack = "Consolas, monaco, monospace";
				break;

			case 'Courier New' :
				$stack = "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace";
				break;

			case 'Lucida Console' :
				$stack = "'Lucida Console', 'Lucida Sans Typewriter', monaco, 'Bitstream Vera Sans Mono', monospace";
				break;

			case 'Lucida Sans Typewriter' :
				$stack = "'Lucida Sans Typewriter', 'Lucida Console', monaco, 'Bitstream Vera Sans Mono', monospace";
				break;

			case 'Monaco' :
				$stack = "monaco, Consolas, 'Lucida Console', monospace";
				break;
		}

		return $stack;
	}
}