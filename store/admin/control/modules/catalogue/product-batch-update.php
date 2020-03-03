<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-batch-update.php');

// Upload CSV file and update products..
if (isset($_POST['process'])) {
  mc_memoryLimit();
  // Refresh if no file was uploaded..
  if (!isset($_FILES['file']['tmp_name']) || $_FILES['file']['name'] == '') {
    header("Location: index.php?p=product-batch-update");
    exit;
  }
  $lines  = ($_POST['lines'] ? str_replace(array('.',','),array(),mc_cleanData($_POST['lines'])) : '0');
  $del    = ($_POST['del'] ? mc_cleanData($_POST['del']) : ',');
  $enc    = ($_POST['enc'] ? mc_cleanData($_POST['enc']) : '"');
  $count  = $MCPROD->batchUpdateProductsFromCSV($lines,$del,$enc);
  $OK     = true;
}

$pageTitle  = mc_cleanDataEntVars($msg_productmanage66) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-batch-update.php');
include(PATH . 'templates/footer.php');

?>