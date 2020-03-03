<?php

if (!defined('CHECKOUT_LOADED')) {
  exit;
}

define('ONACCOUNT_TRANS', 1);

// RANDOM TRANSACTION ID NUMBER..
$redrWin = '';
$_POST['txn_id'] = 'account-' . time() . rand(111, 999);

// LOAD PAYMENT CLASS..
include(PATH . 'control/gateways/methods/class.account.php');

// INITIATE GATEWAY CLASS..
$GATEWAY               = new onAccount();
$GATEWAY->settings     = $SETTINGS;
$GATEWAY->modules      = $mcSystemPaymentMethods;
$GATEWAY->gateway_name = $mc_checkout[33];
$GATEWAY->gateway_url  = '';
$GATEWAY->gateway      = 'account';

// CREATE BUY CODE FOR SALE..
$SALE_CODE = $MCCART->generateUniCode(40);

// ADD TO DATABASE..
$MCCKO->gwmethod = $GATEWAY;
$SALE_ID         = $MCCKO->addOrderToDatabase('sales', $SALE_CODE, false, 'account', '', $form);

// PROCESS ORDER..
$SALE_ORDER = $GATEWAY->getOrderInfo($SALE_CODE, $SALE_ID);

// NOTHING? RELOAD..
if (isset($SALE_ORDER->id)) {
  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'New on account sale added and updated. Send to callback operations to finalise');

  // GLOBAL MAIL TAGS..
  $MCMAIL->addTag('{GATEWAY_NAME}', $GATEWAY->gateway_name);
  $MCMAIL->addTag('{GATEWAY_URL}', $GATEWAY->gateway_url);
  $MCMAIL->addTag('{ORDER_IP}', $SALE_ORDER->ipAddress);
  $MCMAIL->addTag('{NAME}', mc_cleanData($SALE_ORDER->bill_1));

  // LOAD MAIL TEMPLATE FILE PREFERENCES..
  $MTEMP = $GATEWAY->mailTemplates();

  // ORDER ADDRESSES..
  $ORDER_ADDR = $GATEWAY->orderAddresses($SALE_ORDER);

  // CLEAR CART..
  $MCCART->clearCart();

  // LOAD CALLBACK TEMPLATE..
  include(PATH . 'control/gateways/callback-completed.php');

  // REDIRECT..
  $MCCART->clearCart();

  // IS ALTERNATIVE REDIRECT SET IF DOWNLOAD ONLY ORDER?
  $isDownloadOrderOnly = $GATEWAY->checkOrderForDownloadsOnly($SALE_ID);
  // Is redirect set..
  $redirectUrl = '';
  if (isset($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['redirect'])) {
    if (substr($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['redirect'], 0, 5) == 'http:' ||
        substr($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['redirect'], 0, 6) == 'https:') {
      $redirectUrl = $mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['redirect'];
    }
  }
  if ($isDownloadOrderOnly == 'yes' && $redirectUrl) {
    $redrWin = $redirectUrl;
  } else {
    $url = ($ssl == 'yes' ? str_replace('http://', 'https://', $SETTINGS->ifolder) . '/' : $SETTINGS->ifolder . '/');
    $redrWin = $url . 'index.php?gw=' . $SALE_ID . '-' . $SALE_CODE;
  }

  // DONE..
  $mc_pay_status = 'ok';

} else {

  $MCOPS->log('Sale ID NOT found, checkout system terminated');

}

?>