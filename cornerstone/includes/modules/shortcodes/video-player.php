<?php

// Video Player
// =============================================================================

function x_shortcode_video_player( $atts ) {
  extract( shortcode_atts( array(
    'id'                => '',
    'class'             => '',
    'style'             => '',
    'type'              => '',
    'm4v'               => '',
    'ogv'               => '',
    'poster'            => '',
    'preload'           => '',
    'advanced_controls' => '',
    'hide_controls'     => '',
    'autoplay'          => '',
    'loop'              => '',
    'muted'             => '',
    'no_container'      => ''
  ), $atts, 'x_video_player' ) );

  $id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class = ( $class != '' ) ? 'x-video player ' . esc_attr( $class ) : 'x-video player';
  $style = ( $style != '' ) ? 'style="' . $style . '"' : '';
  switch ( $type ) {
    case '5:3' :
      $type = ' five-by-three';
      break;
    case '5:4' :
      $type = ' five-by-four';
      break;
    case '4:3' :
      $type = ' four-by-three';
      break;
    case '3:2' :
      $type = ' three-by-two';
      break;
    default :
      $type = '';
  }
  $m4v               = ( $m4v               != ''     ) ? '<source src="' . $m4v . '" type="video/mp4">' : '';
  $ogv               = ( $ogv               != ''     ) ? '<source src="' . $ogv . '" type="video/ogg">' : '';
  $poster            = ( $poster            != ''     ) ? $poster : '';
  $preload           = ( $preload           != ''     ) ? ' preload="' . $preload . '"' : ' preload="metadata"';
  $advanced_controls = ( $advanced_controls == 'true' ) ? ' advanced-controls' : '';
  $hide_controls     = ( $hide_controls     == 'true' ) ? ' hide-controls' : '';
  $autoplay          = ( $autoplay          == 'true' ) ? ' autoplay' : '';
  $loop              = ( $loop              == 'true' ) ? ' loop' : '';
  $muted             = ( $muted             == 'true' ) ? ' muted' : '';
  $no_container      = ( $no_container      == 'true' ) ? '' : ' with-container';

  if ( is_numeric( $poster ) ) {
    $poster_info = wp_get_attachment_image_src( $poster, 'full' );
    $poster      = $poster_info[0];
  }

  $poster_attr = ( $poster != '' ) ? ' poster="' . $poster . '"' : '';

  wp_enqueue_script( 'mediaelement' );

  $data = cs_generate_data_attributes( 'x_mejs' );

  $output = "<div {$id} class=\"{$class}{$hide_controls}{$autoplay}{$loop}{$muted}{$no_container}\" {$data} {$style}>"
            . "<div class=\"x-video-inner{$type}\">"
              . "<video class=\"x-mejs{$advanced_controls}\"{$poster_attr}{$preload}{$autoplay}{$loop}{$muted}>"
                . $m4v
                . $ogv
              . '</video>'
            . '</div>'
          . '</div>';

  return $output;
}

add_shortcode( 'x_video_player', 'x_shortcode_video_player' );