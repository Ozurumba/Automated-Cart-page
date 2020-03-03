<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/discount-coupons.php');

// Add discount coupon..
if (isset($_POST['process'])) {
  if ($_POST['cDiscount']!='') {
    $MCSYS->addDiscountCoupon();
    $OK = true;
  } else {
    header("Location: index.php?p=discount-coupons");
    exit;
  }
}

// Update discount coupon..
if (isset($_POST['update'])) {
  if ($_POST['cDiscount']!='') {
    $MCSYS->updateDiscountCoupon();
    $OK2 = true;
  } else {
    header("Location: index.php?p=discount-coupons");
    exit;
  }
}

// Delete discount coupon..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deleteDiscountCoupon();
  $OK3 = true;
}

$pageTitle     = ($cmd == 'coupon-report' ? mc_cleanDataEntVars($msg_javascript29).' '.$msg_script12 : mc_cleanDataEntVars($msg_javascript29)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/'.$cmd.'.php');
include(PATH . 'templates/footer.php');

?>
