<?php
/**
 * Form to Request a quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.0.0
 * @author  Yithemess
 */
$current_user = array();
if ( is_user_logged_in() ) {
    $current_user = get_user_by( 'id', get_current_user_id() );
}

$user_name = ( ! empty( $current_user ) ) ?  $current_user->display_name : '';
$user_email = ( ! empty( $current_user ) ) ?  $current_user->user_email : '';
$facility_name = ( ! empty( $current_user ) ) ? $current_user->facility_name : '';

?>
<div class="yith-ywraq-mail-form-wrapper">
  <h3><?php _e( 'Quick Quote', 'ywraq' ) ?></h3>
  <form id="yith-ywraq-mail-form" name="yith-ywraq-mail-form" action="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ) ?>" method="post">

    <p class="form-row form-row-wide validate-required" id="rqa_name_row">
      <label for="rqa-name" class=""><?php _e( 'Name', 'ywraq' ) ?>
      <abbr class="required" title="required">*</abbr></label>
      <input type="text" class="input-text " name="rqa_name" id="rqa-name" placeholder="" value="<?php echo $user_name ?>" required>
    </p>

    <p class="form-row form-row-wide validate-required" id="rqa_email_row">
      <label for="rqa-email" class=""><?php _e( 'Email', 'ywraq' ) ?>
      <abbr class="required" title="required">*</abbr></label>
      <input type="email" class="input-text " name="rqa_email" id="rqa-email" placeholder="" value="<?php echo $user_email ?>" required>
    </p>

    <p class="form-row form-row-wide validate-required" id="rqa_phone_row">
      <label for="rqa-phone" class=""><?php _e( 'Phone Number', 'ywraq' ) ?></label>
      <input type="text" class="input-text " name="rqa_phone" id="rqa-phone" placeholder="" value="<?php echo $phonenumber ?>" >
    </p>

    <p class="form-row form-row-wide validate-required" id="rqa_facility_row">
      <label for="rqa-facility" class=""><?php _e( 'Facility Name', 'ywraq' ) ?>
      <abbr class="required" title="required">*</abbr></label>
      <input type="text" class="input-text " name="rqa_facility" id="rqa-facility" placeholder="" value="<?php echo esc_attr( wp_unslash( $facility_name ) ); ?>" required>
    </p>

    <p class="form-row form-row-wide validate-required" id="rqa_zip_row">
      <label for="rqa-zip" class=""><?php _e( 'Shipping Zip Code', 'ywraq' ) ?>
      <abbr class="required" title="required">*</abbr></label>
      <input type="text" class="input-text " name="rqa_zip" id="rqa-zip" placeholder="" value="<?php echo $zipcode ?>" required>
    </p>

    <div id="userRows">
      <h3 style="margin-top:5em; display:block; width:85%;clear:both;">Additional Products</h3>
      <!-- <a href="javascript:;" id="addRow">Add Row</a><br /> -->

      <p style="clear:both;" class="form-row form-row-wide validate-required" id="add_pro_PN">
        <label for="rqa-part" class="">Part Number</label>
        <input type="text" class="input-text " name="rqa_part" value="<?php echo $partnumber ?>" placeholder="" id="rqa-part">
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_DS">
        <label for="rqa-desc" class="">Product Name / Description</label>
        <input type="text" class="input-text " name="rqa_desc" value="<?php echo $partdesc ?>" placeholder="" id="rqa-desc" >
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_QT">
        <label for="rqa-qty" class="">Quantity</label>
        <input type="text" class="input-text " name="rqa_qty" value="<?php echo $partqty ?>" placeholder="" id="rqa-qty" >
      </p>


      <p style="clear:both;" class="form-row form-row-wide validate-required" id="add_pro_PN">
        <label for="rqa-part" class="">Part Number</label>
        <input type="text" class="input-text " name="rqa_part1" value="<?php echo $partnumber1 ?>" placeholder="" id="rqa-part">
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_DS">
        <label for="rqa-desc" class="">Product Name / Description</label>
        <input type="text" class="input-text " name="rqa_desc1" value="<?php echo $partdesc1 ?>" placeholder="" id="rqa-desc" >
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_QT">
        <label for="rqa-qty" class="">Quantity</label>
        <input type="text" class="input-text " name="rqa_qty1" value="<?php echo $partqty1 ?>" placeholder="" id="rqa-qty" >
      </p>



      <p style="clear:both;" class="form-row form-row-wide validate-required" id="add_pro_PN">
        <label for="rqa-part" class="">Part Number</label>
        <input type="text" class="input-text " name="rqa_part2" value="<?php echo $partnumber2 ?>" placeholder="" id="rqa-part">
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_DS">
        <label for="rqa-desc" class="">Product Name / Description</label>
        <input type="text" class="input-text " name="rqa_desc2" value="<?php echo $partdesc2 ?>" placeholder="" id="rqa-desc" >
      </p>

      <p class="form-row form-row-wide validate-required" id="add_pro_QT">
        <label for="rqa-qty" class="">Quantity</label>
        <input type="text" class="input-text " name="rqa_qty2" value="<?php echo $partqty2 ?>" placeholder="" id="rqa-qty" >
      </p>

    </div>

    <p style="clear:both" class="form-row" id="rqa_message_row">
      <label for="rqa-message" class=""><?php _e( 'Message', 'ywraq' ) ?></label>
      <textarea name="rqa_message" class="input-text " id="rqa-message" placeholder="<?php _e( 'Notes on your request...', 'ywraq' ) ?>" rows="5" cols="5"></textarea>
    </p>

    <p class="form-row">
      <input type="hidden" id="raq-mail-wpnonce" name="raq_mail_wpnonce" value="<?php echo wp_create_nonce( 'send-request-quote' ) ?>">
      <input class="button raq-send-request" type="submit" value="<?php _e( 'Send Your Request', 'ywraq' ) ?>">
    </p>

  </form>
</div>
