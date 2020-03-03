<?php

if (!isset($_GET['gw'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Check incoming get parameters..
$split = explode('-', $_GET['gw']);
$id    = (isset($split[0]) && (int) $split[0] > 0 ? $split[0] : 'inv');
$code  = (isset($split[1]) && ctype_alnum($split[1]) ? $split[1] : 'inv');
$count = (isset($_GET['count']) && (int) $_GET['count'] > 0 ? $_GET['count'] : '1');
$meta  = '';

// If either 0, invalid..
if ($id == 'inv' || $code == '0') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Grab sale data and check its valid..
$ORDER = mc_getTableData('sales', '`buyCode`', $code, 'AND `id` = \'' . $id . '\'');
if (!isset($ORDER->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Processing on refresh page..
// Only for certain gateways..
switch($ORDER->paymentMethod) {
  // For SagePay, ping handler as they don`t send a trigger to the callback file..
  case 'sagepay':
    // CLASS..
    if (isset($_GET['crypt'])) {
      include(PATH . 'control/gateways/methods/class.sagepay.php');
      $GW               = new sagepay();
      $GW->settings     = $SETTINGS;
      $GW->modules      = $mcSystemPaymentMethods;
      $GW->gateway      = 'sagepay';
      $GW->gateway_name = $mcSystemPaymentMethods['sagepay']['lang'];
      $GW->gateway_url  = $mcSystemPaymentMethods['sagepay']['web'];
      $GW->writeLog($ORDER->id, 'Data received from Sage Pay. Preparing to ping handler..');
      $GW->pingHandler($ORDER);
    }
    break;
}

// Is redirect set..
$redirectUrl = '';
if (isset($mcSystemPaymentMethods[$ORDER->paymentMethod]['redirect'])) {
  if (substr($mcSystemPaymentMethods[$ORDER->paymentMethod]['redirect'], 0, 5) == 'http:' ||
      substr($mcSystemPaymentMethods[$ORDER->paymentMethod]['redirect'], 0, 6) == 'https:') {
    $redirectUrl = $mcSystemPaymentMethods[$ORDER->paymentMethod]['redirect'];
  }
}

// Check to see if gateway has responded. If it has, sale confirmation will have been updated..
if ($count < RESPONSE_PAGE_REFRESHES) {
  if ($ORDER->saleConfirmation == 'yes') {
    // Are we redirecting to custom page..
    if ($redirectUrl) {
      header("Location: " . str_replace(array(
        '{id}',
        '{code}'
      ), array(
        $id,
        $code
      ), $redirectUrl));
      exit;
    } else {
      $meta = $SETTINGS->ifolder . '/index.php?vOrder=' . $id . '-' . $code;
    }
  } else {
    $meta = $SETTINGS->ifolder . '/index.php?gw=' . $_GET['gw'] . '&amp;count=' . ($count + 1);
  }
} else {
  $meta = $SETTINGS->ifolder . '/index.php?p=message';
}

$headerTitleText = $public_checkout23 . ': ' . $headerTitleText;
$tpl             = mc_getSavant();
$tpl->assign('META', array(
  $meta,
  RESPONSE_REFRESH_TIME
));
$tpl->assign('TITLE', $public_checkout23);
$tpl->assign('TXT', array(
  $public_checkout24,
  $public_checkout23
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/checkout-response.tpl.php');

?>