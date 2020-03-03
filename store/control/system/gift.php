<?php

if (!defined('PARENT') || defined('MC_TRADE_DISCOUNT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Is there at least 1 gift cert?
$hmGift = mc_rowCount('giftcerts WHERE `enabled` = \'yes\'');
if ($hmGift == 0) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Add to basket..
if (isset($_POST['giftID'])) {
  $MCAJAX->addGiftItemToBasket($MCGIFT);
  exit;
}

$headerTitleText = mc_cleanData($gift_cert . ': ' . $headerTitleText);
$breadcrumbs = array(
  $gift_cert
);

// Javascript..
$loadJS['swipe']  = 'load';

// Left menu boxes..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['brands']  = true;
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $MCRWR->url(array('gift'));
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$giftCertsHTML = $MCGIFT->giftCertificates();

$tpl = mc_getSavant();
$tpl->assign('TXT', array(
  $gift_cert,
  ($giftCertsHTML ? $gift_cert2 : $gift_cert9),
  $gift_cert3,
  $gift_cert4,
  $gift_cert5,
  $gift_cert6,
  $gift_cert7,
  $gift_cert8,
  $gift_cert17,
  $mc_giftcert
));
$tpl->assign('GIFT_CERTIFICATES', $giftCertsHTML);
$tpl->assign('FROM', array(
  'name' => (isset($loggedInUser['name']) ? mc_safeHTML($loggedInUser['name']) : ''),
  'email' => (isset($loggedInUser['email']) ? mc_safeHTML($loggedInUser['email']) : '')
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/gift-certificates.tpl.php');

include(PATH . 'control/footer.php');

?>