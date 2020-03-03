<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-offers.php');

// Adjust offer prices
if (isset($_POST['processUpdateOffers']) && $_POST['newPrice']>0) {
    $MCPROD->reloadOfferPrices();
    $OK2 = true;
}

// Add special offer..
if (isset($_POST['process'])) {
  if (!empty($_POST['products']) && $_POST['oRate']!='') {
    $MCPROD->addSpecialOffer();
    $OK = true;
  } else {
    header("Location: index.php?p=special-offers");
    exit;
  }
}

// View special offer..
if (isset($_GET['view']) && ctype_digit($_GET['view'])) {
  if (isset($_GET['product'])) {
    $MCPROD->clearSpecialOffer('product');
    $OK = true;
  }
  $pageTitle     = mc_cleanDataEntVars($msg_javascript62) . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/catalogue/product-offers-view.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Delete special offer..
if (isset($_GET['clearall']) && $uDel == 'yes') {
  $MCPROD->clearAllSpecialOffers();
  $OK2 = true;
}

// Delete special offer..
if (isset($_GET['clear']) && ctype_digit($_GET['clear']) && $uDel == 'yes') {
  $MCPROD->clearSpecialOffer('cat');
  $OK2 = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript62) . ': ' . $pageTitle;
$loadiBox   = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-offers.php');
include(PATH . 'templates/footer.php');

?>
