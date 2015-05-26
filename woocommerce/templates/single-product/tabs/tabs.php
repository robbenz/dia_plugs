<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) :

  $tab_num = count( $tabs );

  switch ( $tab_num ) {
    case '1' :
      $tab_num_class = 'one-up';
      break;
    case '2' :
      $tab_num_class = 'two-up';
      break;
    case '3' :
      $tab_num_class = 'three-up';
      break;
    case '4' :
      $tab_num_class = 'four-up';
      break;
    case '5' :
      $tab_num_class = 'five-up';
      break;
  }

  ?>

  <?php if ( x_get_option( 'x_woocommerce_product_tabs_enable', '1' ) == '1' ) : ?>

    <div class="woocommerce-tabs">
      <ul class="tabs x-nav x-nav-tabs <?php echo $tab_num_class; ?> top">

        <?php foreach ( $tabs as $key => $tab ) : ?>
          <li class="<?php echo $key ?>_tab x-nav-tabs-item">
            <a href="#tab-<?php echo $key ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
      <div class="x-tab-content">

        <?php foreach ( $tabs as $key => $tab ) : ?>
          <div class="panel x-tab-pane" id="tab-<?php echo $key ?>">
            <?php call_user_func( $tab['callback'], $key, $tab ) ?>
          </div>
        <?php endforeach; ?>

      </div>
    </div>

  <?php endif; ?>

<?php endif; ?>
