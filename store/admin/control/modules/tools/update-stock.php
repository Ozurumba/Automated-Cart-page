<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/update-stock.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-related.php');

if (isset($_POST['import_from_csv'])) {
  $updated = $MCPROD->batchUpdateStockFromCSV();
  $OK      = true;
}

if (isset($_POST['process'])) {
  if ($_POST['stock'] && isset($_POST['pCat'])) {
    $MCPROD->updateStockLevels();
    $OK = true;
  }
}

$pageTitle    = mc_safeHTML(($cmd == 'update-stock-csv' ? $msg_productstock18 : $msg_javascript61)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/product-stock' . ($cmd == 'update-stock-csv' ? '-import' : '') . '.php');
include(PATH . 'templates/footer.php');

?>