<?php
/*
Plugin Name: Brady Thinks Your Cool  
Plugin URI: http://www.robbenz.com
Description: If Brady thinks you are cool, this plugin will allow you to approve the purchase order payment gateway, also it should capture the facility name  
Version: 1.0
Author: Benz
Author URI: http://www.robbenz.com
License: GPL2
 */

//  --   add_actions for extra user profile fields 
add_action( 'show_user_profile', 'brady_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'brady_extra_user_profile_fields' );
add_action( 'personal_options_update', 'brady_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'brady_save_extra_user_profile_fields' );


//  --  Begin Registration form additions
//  --  1. Add a new form element...
add_action( 'register_form', 'brady_cool_register_form' );
function brady_cool_register_form() {
    $first_name = ( ! empty( $_POST['first_name'] ) ) ? trim( $_POST['first_name'] ) : '';
?>
<p class="form-row form-row-wide">
    <label for="first_name"><?php _e( 'First Name', 'MedMattress.com' ) ?><span class="required"> *</span><br />
        <input type="text" name="first_name" id="first_name" class="input-text" value="<?php echo esc_attr( wp_unslash( $first_name ) ); ?>"  />
    </label>
</p>

<?php
    $last_name = ( ! empty( $_POST['last_name'] ) ) ? trim( $_POST['last_name'] ) : '';
?>
<p class="form-row form-row-wide">
    <label for="last_name"><?php _e( 'Last Name', 'MedMattress.com' ) ?><span class="required"> *</span><br />
        <input type="text" name="last_name" id="last_name" class="input-text" value="<?php echo esc_attr( wp_unslash( $last_name ) ); ?>"  />
    </label>
</p>

<?php
    $facility_name = ( ! empty( $_POST['facility_name'] ) ) ? trim( $_POST['facility_name'] ) : '';
?>
<p class="form-row form-row-wide">
    <label for="last_name"><?php _e( 'Facility Name', 'MedMattress.com' ) ?><span class="required"> *</span><br />
        <input type="text" name="facility_name" id="facility_name" class="input-text" value="<?php echo esc_attr( wp_unslash( $facility_name ) ); ?>"  />
    </label>
</p>

<?php
}

//  --  2. Add validation. In this case, we make sure first_name & last_name is required.
add_filter( 'registration_errors', 'brady_cool_registration_errors', 10, 3 );
function brady_cool_registration_errors( $errors, $sanitized_user_login, $user_email ) {
    if ( empty( $_POST['first_name'] ) || ! empty( $_POST['first_name'] ) && trim( $_POST['first_name'] ) == '' ) {
        $errors->add( 'first_name_error', __( '<strong>ERROR</strong>: You must include a first name.', 'MedMattress.com' ) );
        return $errors;
    }
    if ( empty( $_POST['last_name'] ) || ! empty( $_POST['last_name'] ) && trim( $_POST['last_name'] ) == '' ) {
        $errors->add( 'last_name_error', __( '<strong>ERROR</strong>: You must include a Last name.', 'MedMattress.com' ) );
        return $errors;
    }
    if ( empty( $_POST['facility_name'] ) || ! empty( $_POST['facility_name'] ) && trim( $_POST['facility_name'] ) == '' ) {
        $errors->add( 'facility_name_error', __( '<strong>ERROR</strong>: You must include a Facility name.', 'MedMattress.com' ) );
        return $errors;
    }
}

//  --  3. Save extra registration user meta.
add_action( 'user_register', 'brady_cool_user_register' );
function brady_cool_user_register( $user_id ) {
    if ( ! empty( $_POST['first_name'] ) ) {
        update_user_meta( $user_id, 'first_name', trim( $_POST['first_name'] ) );
    }
    if ( ! empty( $_POST['last_name'] ) ) {
        update_user_meta( $user_id, 'last_name', trim( $_POST['last_name'] ) );
    }
    if ( ! empty( $_POST['facility_name'] ) ) {
        update_user_meta( $user_id, 'facility_name', trim( $_POST['facility_name'] ) );
    }
}

//  --  add facility name to wp dashboard
function brady_add_user_facility_name_column( $columns ) {
    $columns['facility_name'] = __( 'Facility Name', 'theme' );
    return $columns;
} 
add_filter( 'manage_users_columns', 'brady_add_user_facility_name_column' );

//  --  Show Facility Name
function brady_show_user_facility_name_data( $value, $column_name, $user_id ) {
    if( 'facility_name' == $column_name ) {
        return get_user_meta( $user_id, 'facility_name', true );
    }
}
add_action( 'manage_users_custom_column', 'brady_show_user_facility_name_data', 10, 3 );

//  --  Add Admin settings and Save settings for Facility Name
function brady_save_extra_user_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
    update_user_meta( $user_id, 'facility_name', $_POST['facility_name'] );
}

//  --  Display Facility Name Field on USER edit-post page
function brady_extra_user_profile_fields( $user ) { 
?>
<h3>Facility Name</h3> 
<table class="form-table">
    <tr>
        <th><label for="facility_name">Facility Name</label></th>
        <td>
            <input type="text" id="facility_name" name="facility_name" size="20" value="<?php echo esc_attr( get_the_author_meta( 'facility_name', $user->ID )); ?>">
            <span class="description">Please enter Facility Name</span>
        </td>
    </tr>
</table>
<?php
}

//  --  Add Custom Purchase Order Role
add_role('purchase_order', 'Purchase Order', array(
    'read'         => true, 
    'edit_posts'   => false,
    'delete_posts' => false, 
));

//  --  Disable Purchase order gateway unless I change User Role to "Purchase Order
global $woocommerce;
function brady_disable_po( $available_gateways ) {
    if ( isset($available_gateways['woocommerce_gateway_purchase_order']) && (current_user_can('customer') ) ) {
        //remove the woocommerce_gateway_purchase_order payment gateway if user is 'customer' ( all users are customer unless I say otherwise ).
        unset($available_gateways['woocommerce_gateway_purchase_order']);
     }
     return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'brady_disable_po', 99, 1);














