<?php

// Integrate WP List Table for Recover Abandon Cart

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FP_List_Table_RAC extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        $data = $this->table_data();

        if (isset($_REQUEST['s'])) {
            $searchvalue = $_REQUEST['s'];
            $keyword = "/$searchvalue/";

            $newdata = array();
            foreach ($data as $eacharray => $value) {
                $searchfunction = preg_grep($keyword, $value);
                if (!empty($searchfunction)) {
                    $newdata[] = $data[$eacharray];
                }
            }
            usort($newdata, array(&$this, 'sort_data'));

            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($newdata);

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            $newdata = array_slice($newdata, (($currentPage - 1) * $perPage), $perPage);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $newdata;
        } else {
            usort($data, array(&$this, 'sort_data'));

            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($data);

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $data;
        }
    }

    public function get_columns() {
        $columns = array(
            // 'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'sno' => __('S.No', 'recoverabandoncart'),
            'orderid' => __('Order ID', 'recoverabandoncart'),
            'amount' => __('Recover Sale Total', 'recoverabandoncart'),
            'date' => __('Date', 'recoverabandoncart'),
        );

        return $columns;
    }

    public function get_hidden_columns() {
        return array();
    }

    public function get_sortable_columns() {
        return array(
            'amount' => array('amount', false),
            'sno' => array('sno', false),
            'date' => array('date', false),
        );
    }

    private function table_data() {
        $data = array();
        $i = 1;

        $get_list_orderids = (array) array_filter(get_option('fp_rac_recovered_order_ids'));
        if (is_array($get_list_orderids) && (!empty($get_list_orderids))) {
            foreach ($get_list_orderids as $key => $value) {
                $data[] = array(
                    'sno' => $i,
                    'orderid' => "<a href=" . admin_url('post.php?post=' . $value["order_id"] . '&action=edit') . ">#" . $value['order_id'] . "</a>",
                    'amount' => self::format_price($value['order_total']),
                    'date' => $value['date'],
                );
                $i++;
            }
        }

        return $data;
    }

    public function format_price($price) {
        if (function_exists('woocommerce_price')) {
            return woocommerce_price($price);
        } else {
            return wc_price($price);
        }
    }

    public function column_id($item) {
        return $item['sno'];
    }

    public function column_default($item, $column_name) {

        switch ($column_name) {

            default:
                return $item[$column_name];
        }
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['orderid']
        );
    }

    private function sort_data($a, $b) {

        $orderby = 'sno';
        $order = 'asc';

        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

}
