<?php

function lambda_connector_get_stock() {
    global $db;
    $data = array(
        'token' => 'token',
        'action' => 'obtain_products'
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
    $json_object = json_decode($result, true);
    $products = $json_object['products'];
    foreach ($products as $product) {
        $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET products_quantity='" . (int) $product['products_quantity'] . "', products_status='" . (int) $product['products_status'] . "' WHERE products_model = '" . $product['products_model'] . "'");
        $products_updated_array[] = array(
            'products_model' => $product['products_model'],
            'products_name' => $product['products_name'],
            'products_quantity' => $product['products_quantity'],
            'products_status' => $product['products_status']
        );
    }
    return $products_updated_array;
}


