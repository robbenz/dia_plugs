<?php

// RAC Table row can be deleted,
//to provide exact report we count it and have it options table
class FPRacCounter {

    public static function rac_do_recovered_count() {
        if (get_option('rac_recovered_count')) { // count started already
            $recovered_count = get_option('rac_recovered_count');
            $recovered_count++;
            update_option('rac_recovered_count', $recovered_count);
        } else {// first time counting
            update_option('rac_recovered_count', 1);
        }
    }

    public static function record_order_id_and_cart_id($order_id) {
        $save_recovered_order_id = (array) get_option('fp_rac_recovered_order_ids');
        $order_object = new WC_Order($order_id);
        $total = $order_object->order_total;
        $order_date = $order_object->order_date;

        $current_order_id = (array) array(
                    $order_id => array(
                        'order_id' => $order_id,
                        'order_total' => $total,
                        'date' => $order_date
                    )
        );
        $merge_data = array_merge($save_recovered_order_id, $current_order_id);

        $update_this_data = update_option('fp_rac_recovered_order_ids', $merge_data);
    }

    public static function add_list_table() {
        // echo "Donation Table";
        $newwp_list_table = new FP_List_Table_RAC();
        $newwp_list_table->prepare_items();
        $newwp_list_table->display();
    }

    public static function rac_do_abandoned_count() {
        if (get_option('rac_abandoned_count')) { // count started already
            $abandoned_count = get_option('rac_abandoned_count');
            $abandoned_count++;
            update_option('rac_abandoned_count', $abandoned_count);
        } else {// first time counting
            update_option('rac_abandoned_count', 1);
        }
    }

    public static function rac_do_mail_count() {
        if (get_option('rac_mail_count')) { // count started already
            $mail_count = get_option('rac_mail_count');
            $mail_count++;
            update_option('rac_mail_count', $mail_count);
        } else {// first time counting
            update_option('rac_mail_count', 1);
        }
    }

    public static function rac_do_linkc_count() {
        if (get_option('rac_link_count')) { // count started already
            $link_count = get_option('rac_link_count');
            $link_count++;
            update_option('rac_link_count', $link_count);
        } else {// first time counting
            update_option('rac_link_count', 1);
        }
    }

}

?>