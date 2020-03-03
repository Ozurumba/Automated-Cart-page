<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/gift-certificates.php');

// Gift class
include(PATH . 'control/classes/class.gift.php');
$MCGIFT            = new gift();
$MCGIFT->settings  = $SETTINGS;

// Re-order..
if (isset($_GET['order'])) {
  $MCGIFT->reOrderGiftCerts();
  exit;
}

// Gift cert info..
if (isset($_GET['viewGift'])) {
  // Update..
  if (isset($_POST['process'])) {
    $MCGIFT->updateGiftCertInfo();
    $OK = true;
  }
  $GIFT      = mc_getTableData('giftcodes', 'code', $_GET['viewGift']);
  $pageTitle = ($cmd == 'cert-report' ? mc_cleanDataEntVars($msg_javascript29) . ' ' . $msg_script12 : mc_cleanDataEntVars($msg_header23)) . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/gift-overview-view.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Gift cert info (initial sale)..
if (isset($_GET['viewSaleGift'])) {
  // Update..
  if (isset($_POST['process'])) {
    $MCGIFT->updateGiftCertInfo();
    $OK = true;
  }
  include(MCLANG . 'sales/view-sales.php');
  include(MCLANG . 'sales/sales-view.php');
  $pageTitle      = mc_cleanDataEntVars($msg_giftcerts20) . ': ' . $pageTitle;
  $loadiBox    = true;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-gift-certificate.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Add discount coupon..
if (isset($_POST['process'])) {
  if ($_POST['name'] && $_POST['value'] > 0) {
    $MCGIFT->addGiftCertificate();
    $OK = true;
  } else {
    header("Location: index.php?p=gift");
    exit;
  }
}

// Update discount coupon..
if (isset($_POST['update'])) {
  if ($_POST['name'] && $_POST['value'] > 0) {
    $MCGIFT->updateGiftCertificate();
    $OK2 = true;
  } else {
    header("Location: index.php?p=gift");
    exit;
  }
}

// Delete discount coupon..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCGIFT->deleteGiftCertificate();
  $OK3 = true;
}

$pageTitle = ($cmd == 'cert-report' ? mc_cleanDataEntVars($msg_javascript29) . ' ' . $msg_script12 : mc_cleanDataEntVars($msg_header23)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/' . ($cmd == 'gift' ? 'gift-certificates' : 'gift-report') . '.php');
include(PATH . 'templates/footer.php');

?>