<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// DEFAULT TEXT FOR TITLE..
$headerTitleText = str_replace('{website_name}', mc_cleanData($SETTINGS->website), $msg_public_header);

// REMOVE SHIPPING SELECTOR FOR DOWNLOAD/VIRTUAL ONLY ORDERS..
$checkNoShipFlag = 'no';
if (isset($_GET['cart-ops']) && $_GET['cart-ops'] == 'checkout-ops') {
  $checkNoShipFlag = 'yes';
}
if (!defined('KILL_CHECKOUT_SHIPPING')) {
  if ((isset($_GET['p']) && $_GET['p'] == 'checkpay') || ($cmd == 'checkpay') || ($checkNoShipFlag == 'yes')) {
    if ($MCCART->doesCartContainOnlyDownloads() == 'yes' || $MCCART->allVirtualProducts() == 'yes') {
      define('KILL_CHECKOUT_SHIPPING', 1);
    }
  }
}

// TWEETS
if ($SETTINGS->twitterLatest == 'yes' && !empty($twPar) && isset($_GET['tweetscroller'])) {
  if (version_compare(phpversion(), MSW_PHP_MIN_VER, '>')) {
    $MCSOCIAL->twitter = array(
      'user' => $twPar['twitter']['username'],
      'limit' => $SETTINGS->tweetlimit,
      'consumerkey' => $twPar['twitter']['conkey'],
      'consumersecret' => $twPar['twitter']['consecret'],
      'accesstoken' => $twPar['twitter']['token'],
      'accesstokensecret' => $twPar['twitter']['key']
    );
    echo $MCSOCIAL->getTweets();
  } else {
    echo 'Error, your PHP version (' . phpversion() . ') is too old. PHP' . MSW_PHP_MIN_VER . ' or higher required. Please disable this feature.';
  }
  exit;
}

// CHECK ONLINE/OFFLINE STATUS..
if ($SETTINGS->enableCart == 'no') {
  // Check IPs..
  if ($SETTINGS->offlineIP) {
    $ipB = array_map('trim', explode(',', $SETTINGS->offlineIP));
    if (!in_array($_SERVER['REMOTE_ADDR'], $ipB)) {
      $cmd = 'offline';
    }
  } else {
    $cmd = 'offline';
  }
}

// GATEWAY RESPONSE..
if (isset($_GET['gw'])) {
  include(PATH . 'control/gateways/responses.php');
  exit;
}

// PUBLIC WISH LIST..
if (isset($_GET['wls'])) {
  $cmd = 'wish';
}

// ACCOUNT OPS..
if (isset($_GET['acop'])) {
  include(PATH . 'control/system/accounts/acc-ajax-ops.php');
  exit;
}

// VIEW ORDER..
if (isset($_GET['vodr']) || isset($_GET['pinfo'])) {
  include(PATH . 'control/system/accounts/view-order.php');
  exit;
}

// PDF..
if (isset($_GET['pdf']) || isset($_GET['pdfg'])) {
  include(PATH . 'control/system/pdf-invoice.php');
  exit;
}

// ACCOUNT VERIFICATION
if (isset($_GET['ve'])) {
  $cmd = 'acc-verification';
}

// PASS RESET
if (isset($_GET['prt'])) {
  $cmd = 'newpass';
}

// WISH RELOAD
if (isset($_GET['loadw'])) {
  $cmd = 'product';
}

// TERMS & CONDITIONS
if (isset($_GET['terms'])) {
  $cmd = 'checkpay';
}

//LOGOUT..
if ($cmd == 'logout') {
  $cmd = 'login';
}

// BASKET OPS
if (isset($_GET['cart-ops'])) {
  include(PATH . 'control/system/basket-ops.php');
  exit;
}

// CHECKOUT OPS..
if (isset($_GET['checkout-rdr'])) {
  include(PATH . 'control/system/checkout-screen-other.php');
  exit;
}

if (isset($_GET['checkout-pay'])) {
  include(PATH . 'control/system/checkout-screen-send.php');
  exit;
}

if (isset($_GET['mcbn'])) {
  include(PATH . 'control/system/buy-now.php');
  exit;
}

// GATEWAY MESSAGES..
if (in_array($cmd, array(
  'cancel',
  'message',
  'declined',
  'error',
  'rlerror'
))) {
  include(PATH . 'control/gateways/messages.php');
}

// PAGE LOAD..GENERAL..
elseif (in_array($cmd, array(
  'home',
  'category',
  'brands',
  'product',
  'special-offers',
  'latest-products',
  'search',
  'advanced-search',
  'checkpay',
  'offline',
  'sitemap',
  'gift'
))) {
  include(PATH . 'control/system/' . $cmd . '.php');
}

// PAGE LOAD..ACCOUNTS..
elseif (in_array($cmd, array(
  'account',
  'login',
  'profile',
  'newpass',
  'close',
  'history',
  'wishlist',
  'wish',
  'saved-searches',
  'create',
  'code-help'
))) {
  if ($cmd == 'code-help') {
    $cmd = 'wishlist';
  }
  include(PATH . 'control/system/accounts/' . ($cmd == 'wish' ? 'wishlist-public' : $cmd) . '.php');
}

// PAGE LOAD..VIEW ORDER..
elseif (in_array($cmd, array(
  'view-order',
  'pdl'
))) {
  include(PATH . 'control/system/view-order.php');
}

// PAGE LOAD..RSS FEED..
elseif (in_array($cmd, array(
  'feed'
))) {
  include(PATH . 'control/system/rss-feeds.php');
}

// PAGE MESSAGES..
elseif (in_array($cmd, array(
  'dl-code-error',
  'code-error',
  'acc-verification',
  'cart-error',
  'acc-closed',
  'out-of-stock',
  'acc-exists',
  'opt-out',
  'order-invalid',
  'status-err',
  'no-search',
  'gate1',
  'gate2',
  'no-category-assigned',
  'no-wish-country'
))) {
  include(PATH . 'control/system/messages.php');
}

// PAGE LOAD..NEW PAGES..
elseif (in_array($cmd, array(
  'np'
))) {
  include(PATH . 'control/system/new-pages.php');
}

else {
  // ANTHING ELSE 403..
  include(PATH . 'control/system/headers/403.php');
}

?>