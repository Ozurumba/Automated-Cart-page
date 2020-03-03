<?php

if (!defined('CHECKOUT_LOADED')) {
  exit;
}

define('OTHER_TRANS', 1);

$redrWin = '';
$_POST['txn_id'] = 'N/A';

// BUILD MESSAGE..
if (isset($mcSystemPaymentMethods[$paymentMethod]['ID'])) {
  $htmlInstructions = mc_txtParsingEngine($mcSystemPaymentMethods[$paymentMethod]['html']);
  $mailInstructions = mc_cleanData($mcSystemPaymentMethods[$paymentMethod]['plain']);
  // Are we redirecting to custom page..
  if (substr($mcSystemPaymentMethods[$paymentMethod]['redirect'], 0, 5) == 'http:' ||
    substr($mcSystemPaymentMethods[$paymentMethod]['redirect'], 0, 6) == 'https:') {
    $redrWin = $mcSystemPaymentMethods[$paymentMethod]['redirect'];
  }

  // LOAD PAYMENT CLASS..
  include(PATH . 'control/gateways/methods/class.other.php');

  // INITIATE GATEWAY CLASS..
  $GATEWAY               = new otherpayment();
  $GATEWAY->settings     = $SETTINGS;
  $GATEWAY->gateway_name = $mcSystemPaymentMethods[$paymentMethod]['lang'];
  $GATEWAY->gateway_url  = $mcSystemPaymentMethods[$paymentMethod]['web'];
  $GATEWAY->modules      = $mcSystemPaymentMethods;
  $GATEWAY->gateway      = $paymentMethod;

  // CREATE BUY CODE FOR SALE..
  $SALE_CODE = $MCCART->generateUniCode(40);

  // ADD TO DATABASE..
  $MCCKO->gwmethod = $GATEWAY;
  $SALE_ID         = $MCCKO->addOrderToDatabase('sales', $SALE_CODE, false, $paymentMethod, '', $form);

  // PROCESS ORDER..
  $SALE_ORDER = $GATEWAY->getOrderInfo($SALE_CODE, $SALE_ID);

  // ORDER OK?..
  if (isset($SALE_ORDER->id)) {
    $GATEWAY->writeLog($SALE_ID, 'New sale added that requires payment via ' . $mcSystemPaymentMethods[$paymentMethod]['lang']);
    // GLOBAL MAIL TAGS..
    $MCMAIL->addTag('{GATEWAY_NAME}', $GATEWAY->gateway_name);
    $MCMAIL->addTag('{GATEWAY_URL}', $GATEWAY->gateway_url);
    $MCMAIL->addTag('{ORDER_IP}', $SALE_ORDER->ipAddress);
    $MCMAIL->addTag('{NAME}', mc_cleanData($SALE_ORDER->bill_1));

    // LOAD MAIL TEMPLATE FILE PREFERENCES..
    $MTEMP = $GATEWAY->mailTemplates();

    // ORDER ADDRESSES..
    $ORDER_ADDR = $GATEWAY->orderAddresses($SALE_ORDER);

    // LOAD CALLBACK TEMPLATE..
    include(PATH . 'control/gateways/callback-completed.php');

    // WHERE ARE WE DIRECTING TO?
    if ($redrWin == '') {
      $url = ($ssl == 'yes' ? str_replace('http://', 'https://', $SETTINGS->ifolder) . '/' : $SETTINGS->ifolder . '/');
      $redrWin = $url . 'index.php?checkout-rdr=' . $SALE_ID . '-' . $SALE_CODE;
    }

    // DONE..
    $mc_pay_status = 'ok';
  }

} else {

  $MCOPS->log('Payment methods array NOT found, checkout system terminated');

}

?>