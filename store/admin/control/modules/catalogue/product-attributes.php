<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'catalogue/product-related.php');
include(MCLANG . 'shipping/shipping-rates.php');
include(MCLANG . 'sales/sales-view.php');

// Adjust order..
if (isset($_GET['order'])) {
  $MCPROD->reOrderAttributes();
  exit;
}

// Copy attributes..
if ($cmd == 'copy-attributes') {
  if (!empty($_POST['product']) && !empty($_POST['attr'])) {
    $MCPROD->copyAttributes();
    $OK = true;
  }
  $pageTitle     = mc_cleanDataEntVars($msg_prodattributes24) . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/catalogue/product-attributes-copy.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Add attributes..
if (isset($_POST['process'])) {
  $ret = $MCPROD->addUpdateAttributes();
  if ($ret[0]>0) {
    $OK = true;
  }
}

// Update attributes..
if (isset($_POST['update'])) {
  $ret = $MCPROD->addUpdateAttributes();
  if ($ret[1] != $_GET['edit']) {
    header("Location: ?p=product-attributes&edit=" . $ret[1] . "&product=" . (int) $_GET['product'] . "&ok2=yes");
    exit;
  }
  $OK2 = true;
}

// Delete groups..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCPROD->deleteAttributeGroups();
  $OK3 = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript54) . ': ' . $pageTitle;
$loadiBox   = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-attributes.php');
include(PATH . 'templates/footer.php');

?>
