<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-view.php');
include(MCLANG . 'sales/sales-incomplete.php');
include(MCLANG . 'catalogue/product-manage.php');

if (isset($_GET['ordered'])) {
  include(PATH . 'templates/windows/sale-products.php');
  exit;
}

if (!empty($_POST['del'])) {
  foreach ($_POST['del'] AS $delID) {
    $MCSALE->deleteOrderSale($delID);
  }
  $OK   = true;
}

$pageTitle    = mc_cleanDataEntVars($msg_header21) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-incomplete.php');
include(PATH . 'templates/footer.php');

?>
