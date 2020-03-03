<?php

/* CALLBACK OPS - GIFT CERTS
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// ARE ANY OF THE PURCHASES GIFT CERTIFICATES?
if (!in_array($SALE_ORDER->paymentMethod, array('bank','cod','cheque','phone'))) {

  $gift_sent = 0;
  $q_cert = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes`
            WHERE `saleID` = '{$SALE_ID}'
            AND `active`   = 'no'
            ORDER BY `id`
            ") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($GIFT_CERTS = mysqli_fetch_object($q_cert)) {

    ++$gift_sent;

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Gift certificate found for "' . mc_cleanData($GIFT_CERTS->to_name) . '" from "' . mc_cleanData($GIFT_CERTS->from_name) . '"...creating code and activating certificate..');

    // CREATE CODE..
    $giftCode = $MCGIFT->codeCreator($GIFT_CERTS->id);

    // ACTIVATE..
    $MCGIFT->activateCertificate($giftCode, $GIFT_CERTS->id);

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Gift certificate activated with code "' . $giftCode . '"...sending email to "' . mc_cleanData($GIFT_CERTS->to_name) . ' / ' . $GIFT_CERTS->to_email . '"..');

    // MAIL TAGS..
    $MCMAIL->addTag('{TO_NAME}', $GIFT_CERTS->to_name);
    $MCMAIL->addTag('{FROM_NAME}', $GIFT_CERTS->from_name);
    $MCMAIL->addTag('{CURRENCY}', $SETTINGS->baseCurrency);
    $MCMAIL->addTag('{VALUE}', $GIFT_CERTS->value);
    $MCMAIL->addTag('{GIFT_CODE}', $giftCode);
    $MCMAIL->addTag('{CUSTOM_MESSAGE}', ($GIFT_CERTS->message ? $GIFT_CERTS->message : $public_checkout137));

    // SEND EMAIL..
    $sbj = str_replace(array(
      '{website}',
      '{from_name}',
      '{to_name}'
    ), array(
      mc_cleanData($SETTINGS->website),
      mc_cleanData($GIFT_CERTS->from_name),
      mc_cleanData($GIFT_CERTS->to_name)
    ), $msg_emails23);
    $msg = MCLANG . 'email-templates/gift-certificate.txt';
    $MCMAIL->sendMail(array(
      'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
      'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
      'to_email' => $GIFT_CERTS->to_email,
      'to_name' => $GIFT_CERTS->to_name,
      'subject' => $sbj,
      'replyto' => array(
        'name' => $GIFT_CERTS->from_name,
        'email' => $GIFT_CERTS->from_email
      ),
      'template' => $msg,
      'alive' => 'yes',
      'language' => (isset($PAY_ACC->language) && $PAY_ACC->language ? $PAY_ACC->language : $SETTINGS->languagePref)
    ));

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Email sent to "' . mc_cleanData($GIFT_CERTS->to_name) . ' / ' . $GIFT_CERTS->to_email . '" from "' . $GIFT_CERTS->from_name . ' / ' . $GIFT_CERTS->from_email . '". Gift certificate completed.');

  }

  if ($gift_sent > 0) {
    $MCMAIL->smtpClose();
  }

  // WAS A GIFT CERTIFICATE USED WITH THIS SALE? UPDATE REDEEM VALUE
  if ($SALE_ORDER->couponCode && $SALE_ORDER->codeType == 'gift') {

    $GIFT_CTF = mc_getTableData('giftcodes', '`code`', $SALE_ORDER->couponCode);

    if (isset($GIFT_CTF->id)) {

      // UPDATE REDEEM VALUE..
      $MCGIFT->redeemCode($GIFT_CTF, $SALE_ORDER->couponTotal);

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Gift certificate redeem value updated. Redeem value updated by ' . $SALE_ORDER->couponTotal . '..original full value was ' . $GIFT_CTF->value);

    } else {

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Gift certificate used, but database entry not found for "' . $SALE_ORDER->couponCode . '".');

    }

  }

}  else {

  $GATEWAY->writeLog($SALE_ID, 'Payment method none gateway (' . mc_paymentMethodName($SALE_ORDER->paymentMethod) . '), so gift routines ignored. If required, trigger email manually via admin CP');

}

?>