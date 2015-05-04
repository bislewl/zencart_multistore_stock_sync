<?php

class lambda_connector_reduce_inventory extends base {

    function lambda_connector_reduce_inventory() {
        global $zco_notifier;
        $zco_notifier->attach($this, array('NOTIFY_HEADER_START_CHECKOUT_SUCCESS'));
    }

    function update(&$class, $eventID, $paramsArray) {
        global $db, $_SESSION;
        $orders_products_query = $db->Execute("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id=" . (int) $_SESSION['order_number_created']);
        while (!$orders_products_query->EOF) {
            $orders_products[] = array(
                'products_model' => $orders_products_query->fields['products_model'],
                'products_quantity' => (int) $orders_products_query->fields['products_quantity'],
            );
            $orders_products_query->MoveNext();
        }
        $data = array(
            'token' => 'token',
            'action' => 'update_stock',
            'orders_id' => (int) $_SESSION['order_number_created'],
            'sales' => $orders_products
        );
        $url = 'http://herbalaxation.com/lambda_connector/stock.php';
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        curl_close($ch);
    }

}
