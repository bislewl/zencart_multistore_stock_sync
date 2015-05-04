<?php
require('../includes/configure.php');
ini_set('include_path', DIR_FS_CATALOG . PATH_SEPARATOR . ini_get('include_path'));
chdir(DIR_FS_CATALOG);
require_once('includes/application_top.php');

$products_updated = lambda_connector_get_stock();
?>
<table>
    <tr>
        <td>Products Model</td>
        <td>Products Name</td>
        <td>Products Quantity</td>
        <td>Products Status</td>
    </tr>
    <?php
    foreach($products_updated as $product){
    ?>
    <tr>
        <td><?php echo $product['products_model'];?></td>
        <td><?php echo $product['products_name'];?></td>
        <td><?php echo $product['products_quantity'];?></td>
        <td><?php echo $product['products_status'];?></td>
    </tr>
      <?php
      }
      ?>
</table>
