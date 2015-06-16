<?php

// Audio Player
// =============================================================================

function x_shortcode_audio_player( $atts ) {
  extract( shortcode_atts( array(
    'id'                => '',
    'class'             => '',
    'style'             => '',
    'mp3'               => '',
    'oga'               => '',
    'advanced_controls' => '',
    'preload'           => '',
    'autoplay'          => '',
    'loop'              => ''
  ), $atts, 'x_audio_player' ) );

  $id                 = ( $id                != ''     ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class              = ( $class             != ''     ) ? 'x-audio player ' . esc_attr( $class ) : 'x-audio player';
  $style              = ( $style             != ''     ) ? 'style="' . $style . '"' : '';
  $mp3                = ( $mp3               != ''     ) ? '<source src="' . $mp3 . '" type="audio/mpeg">' : '';
  $oga                = ( $oga               != ''     ) ? '<source src="' . $oga . '" type="audio/ogg">' : '';
  $advanced_controls  = ( $advanced_controls == 'true' ) ? ' advanced-controls' : '';
  $preload            = ( $preload           != ''     ) ? ' preload="' . $preload . '"' : ' preload="metadata"';
  $autoplay           = ( $autoplay          == 'true' ) ? ' autoplay' : '';
  $loop               = ( $loop              == 'true' ) ? ' loop' : '';

  wp_enqueue_script( 'mediaelement' );

  $data = cs_generate_data_attributes( 'x_mejs' );

  $output = "<div {$id} class=\"{$class}{$autoplay}{$loop}\" {$data} {$style}>"
            . "<audio class=\"x-mejs{$advanced_controls}\"{$preload}{$autoplay}{$loop}>"
              . $mp3
              . $oga
            . '</audio>'
          . '</div>';

  return $output;
}

add_shortcode( 'x_audio_player', 'x_shortcode_audio_player' );