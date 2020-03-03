<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/update-stock.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-related.php');

if (isset($_POST['import_from_csv'])) {
  mc_memoryLimit();
  $updated = $MCPROD->batchUpdatePricesFromCSV();
  $OK      = true;
}

if (isset($_POST['process'])) {
  if ($_POST['price'] && isset($_POST['pCat'])) {
    mc_memoryLimit();
    $MCPROD->updateProductPrices();
    $OK = true;
  } else {
    header("Location: index.php?p=update-prices");
    exit;
  }
}

$pageTitle    = mc_safeHTML(($cmd == 'update-prices-csv' ? $msg_admin3_0[5] : $msg_javascript60)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/product-prices' . ($cmd == 'update-prices-csv' ? '-import' : '') . '.php');
include(PATH . 'templates/footer.php');

?>