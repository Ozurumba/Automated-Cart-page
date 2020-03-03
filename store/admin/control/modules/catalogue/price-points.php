<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/price-points.php');
include(MCLANG . 'catalogue/categories.php');

if (isset($_GET['order'])) {
  $MCSYS->reOrderPricePoints();
  exit;
}

if (isset($_POST['process']) && $_POST['priceFrom'] && $_POST['priceTo']) {
  $MCSYS->addPricePoint();
  $OK = true;
}

if (isset($_POST['update']) && $_POST['priceFrom'] && $_POST['priceTo']) {
  $MCSYS->updatePricePoint();
  $OK2 = true;
}

if (isset($_GET['del']) && ctype_digit($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deletePricePoint();
  if ($cnt>0) {
    $OK3 = true;
  }
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript266) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/price-points.php');
include(PATH . 'templates/footer.php');

?>
