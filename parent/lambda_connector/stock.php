<?php

require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');
//$products_table = 'products';
$products_table = TABLE_PRODUCTS;
define('SYNC_SECURITY_TOKEN', 'token');
$json_payload = file_get_contents("php://input");
$payload = json_decode($json_payload);
if ($payload->token != SYNC_SECURITY_TOKEN) {
    die('Sorry Charlie! You aren\'t allowed here!');
}
$payload_action = zen_db_prepare_input($payload->action);
switch ($payload_action) {
    case 'update_stock':
        $current_inventory = $db->Execute("SELECT * FROM " . $products_table);
        $current_models = array();
        while (!$current_inventory->EOF) {
            $current_models[] = $current_inventory->fields['products_model'];
            $products_quantity[$current_inventory->fields['products_model']] = $current_inventory->fields['products_quantity'];
            $current_inventory->MoveNext();
        }
        foreach ($payload->sales as $product) {
            $products_model = zen_db_prepare_input($product->products_model);
            if (!in_array($products_model, $current_models)) {
                $mismatched_products = $products_model;
            } else {
                $decreased_quantity = (int) zen_db_prepare_input($product->products_quantity);
                $current_products_quantity = (int)$products_quantity[$products_model];
                $new_products_quantity = $current_products_quantity - $decreased_quantity;
                $sql = "UPDATE ".$products_table." set products_quantity='" . $new_products_quantity . "'  WHERE products_model='" . $products_model . "'";
                $db->Execute($sql);
            }
        }
        break;
    case 'obtain_products':
        $current_inventory = $db->Execute("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . $products_table . " p WHERE pd.products_id = p.products_id");
        $products = array();
        while (!$current_inventory->EOF) {
            $products[] = array(
                'products_model' => $current_inventory->fields['products_model'],
                'products_name' => $current_inventory->fields['products_name'],
                'products_quantity' => $current_inventory->fields['products_quantity'],
                'products_status' => $current_inventory->fields['products_status'],
            );
            $current_inventory->MoveNext();
        }
        $json_output_array = array('products' => $products);
        $json_out = json_encode($json_output_array);
        echo $json_out;
        break;
    default:
        echo 'your missing something';
}