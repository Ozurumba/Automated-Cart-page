<?php

/* CALLBACK OPS - CANCELLED
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// MAIL..
if (!defined('MAIL_SWITCH')) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
}

// LOAD GATEWAY CLASS..
$GATEWAY           = new paymentHandler();
$GATEWAY->settings = $SETTINGS;
$GATEWAY->modules  = $mcSystemPaymentMethods;
$GATEWAY->rwr      = $MCRWR;

// Try and find order for cancellation..
if (isset($_GET['o'])) {

  // GET BUY/SALE CODE AND ID..
  $DATA      = explode('-', $_GET['o']);
  $SALE_CODE = (isset($DATA[1]) && ctype_alnum($DATA[1]) ? $DATA[1] : '0');
  $SALE_ID   = (isset($DATA[0]) && (int) $DATA[0] > 0 ? $DATA[0] : '0');

  // GET SALE / ORDER INFO..
  $SALE_ORDER = $GATEWAY->getOrderInfo($SALE_CODE, $SALE_ID);

  if (isset($SALE_ORDER->id) && mswIsValidEmail($SALE_ORDER->bill_2)) {

    // Set gateway for log..
    $GATEWAY->gateway_name = (isset($mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang']) ? $mcSystemPaymentMethods[$SALE_ORDER->paymentMethod]['lang'] : 'other');
    
    // MAIL TAGS..
    $MCMAIL->addTag('{PRODUCT_ORDER}', $GATEWAY->buildProductOrder($SALE_ORDER->id));
    $MCMAIL->addTag('{NAME}', mc_cleanData($SALE_ORDER->bill_1));
    $MCMAIL->addTag('{ORDER_IP}', $SALE_ORDER->ipAddress);
    $MCMAIL->addTag('{TRADE_SALE}', ($SALE_ORDER->type == 'trade' ? $msg_script5 : $msg_script6));

    $sbj = str_replace(array(
      '{website}'
    ), array(
      mc_cleanData($SETTINGS->website)
    ), $msg_emails22);
    $msg = MCLANG . 'email-templates/buyer-cancelled.txt';
    $MCMAIL->sendMail(array(
      'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
      'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
      'to_email' => $SETTINGS->email,
      'to_name' => $SETTINGS->website,
      'subject' => $sbj,
      'replyto' => array(
        'name' => $SALE_ORDER->bill_1,
        'email' => $SALE_ORDER->bill_2
      ),
      'template' => $msg,
      'add-emails' => $SETTINGS->addEmails,
      'language' => $SETTINGS->languagePref
    ));
    $MCMAIL->smtpClose();

    $GATEWAY->writeLog($SALE_ORDER->id, 'Order cancelled at gateway by ' . $SALE_ORDER->bill_1 . '. Cancellation notification sent to ' . $SETTINGS->email . ($SETTINGS->addEmails ? ',' . $SETTINGS->addEmails : ''));

  }

}

?>