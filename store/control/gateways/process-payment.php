<?php

if (!defined('CHECKOUT_LOADED') || empty($form) || !isset($_POST['payment-type'])) {
  exit;
}

// SET GATEWAY FLAG
$gatewayFlagVar = (array_key_exists($_POST['payment-type'], $mcSystemPaymentMethods) ? $_POST['payment-type'] : 'fail');

// CLASS..
if (file_exists(PATH . 'control/gateways/methods/class.' . $gatewayFlagVar . '.php')) {
  include(PATH . 'control/gateways/methods/class.' . $gatewayFlagVar . '.php');

  // Create buy code for sale..
  switch($gatewayFlagVar) {
    // Sage pay has a restricted length, so use shorter value..
    case 'sagepay':
      $buyCode = $MCCART->generateUniCode(25);
      break;
    default:
      $buyCode = $MCCART->generateUniCode(40);
      break;
  }

  // Add to database..
  $GATEWAY               = new $gatewayFlagVar();
  $GATEWAY->gateway_name = $mcSystemPaymentMethods[$gatewayFlagVar]['lang'];
  $GATEWAY->gateway_url  = $mcSystemPaymentMethods[$gatewayFlagVar]['web'];
  $GATEWAY->settings     = $SETTINGS;
  $GATEWAY->modules      = $mcSystemPaymentMethods;
  $GATEWAY->gateway      = $gatewayFlagVar;
  $MCCKO->gwmethod       = $GATEWAY;
  $id                    = $MCCKO->addOrderToDatabase('sales', $buyCode, true, $paymentMethod, '', $form);

  // Process..
  if ($id > 0) {

    // SEND PRE PAYMENT NOTIFICATIONS..
    if ($SETTINGS->presalenotify == 'yes') {
      $GATEWAY->writeLog($id, 'Preparing to send pre-payment notifications..');
      $psem = array_map('trim', explode(',', $SETTINGS->presaleemail));
      $ot   = array();
      if (count($psem) > 1) {
        $ot = $psem;
        unset($ot[0]);
      }
      if (isset($psem[0]) && mswIsValidEmail($psem[0])) {
        $SALE_ORDER = $GATEWAY->getOrderInfo($buyCode, $id);
        if (isset($SALE_ORDER->id)) {
          $SALE_CODE  = $buyCode;
          $SALE_ID    = $id;
          $ORDER_ADDR = $GATEWAY->orderAddresses($SALE_ORDER);
          $GATEWAY->writeLog($id, 'Sending pre payment-notifications to: ' . PHP_EOL . PHP_EOL . print_r($psem, true));
          include(PATH . 'control/gateways/callback-mail-tags.php');
          if (!defined('MAIL_SWITCH')) {
            include(PATH . 'control/classes/mailer/global-mail-tags.php');
          }
          $sbj = str_replace('{website}', $SETTINGS->website, $msg_emails41);
          $msg = MCLANG . 'email-templates/prepayment-notification.txt';
          $MCMAIL->sendMail(array(
            'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
            'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'to_email' => $psem[0],
            'to_name' => $SETTINGS->website,
            'subject' => $sbj,
            'replyto' => array(
              'name' => $SETTINGS->website,
              'email' => $psem[0]
            ),
            'template' => $msg,
            'add-emails' => (!empty($ot) ? implode(',',$ot) : ''),
            'alive' => 'yes',
            'language' => $SETTINGS->languagePref
          ));
          $MCMAIL->smtpClose();
        } else {
          $GATEWAY->writeLog($id, 'Could not send pre-payment notifications, order not found. Possible database issue.');
        }
      } else {
        $GATEWAY->writeLog($id, 'Could not send pre-payment notifications, email addresses not set or invalid. Store Settings > Global Store Settings > Checkout Options');
      }
    }

    // REDIRECT URL..
    $url = ($ssl == 'yes' ? str_replace('http://', 'https://', $SETTINGS->ifolder) . '/' : $SETTINGS->ifolder . '/');
    $redrWin = $url . 'index.php?checkout-pay=' . $id . '-' . $buyCode;

    // DONE..
    $mc_pay_status = 'ok';

  } else {

    $MCOPS->log('Sale ID NOT found (Database issue maybe), checkout system terminated');

  }

} else {

  $MCOPS->log('Database payment flag (' . $gatewayFlagVar . ') invalid, checkout system terminated');

}

?>