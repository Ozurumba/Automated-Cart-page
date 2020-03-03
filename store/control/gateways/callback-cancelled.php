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

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Processing ' . $paymentStatus . ' actions..');

// MAIL TAGS..
$MCMAIL->addTag('{INVOICE_NO}', mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS));
$MCMAIL->addTag('{PRODUCT_ORDER}', $GATEWAY->buildProductOrder($ORDER->id));
$MCMAIL->addTag('{ORDER_ID}', $ORDER->id);
$MCMAIL->addTag('{ORDER_IP}', $ORDER->ipAddress);
$MCMAIL->addTag('{TRADE_SALE}', ($ORDER->type == 'trade' ? $msg_script5 : $msg_script6));

// WRITE STATUS..
$GATEWAY->writeOrderStatus($ORDER->id, str_replace('{amount}', $price, $public_checkout81) . ' ' . $GATEWAY->gateway_name, $GATEWAY->setOrderStatus('cancelled'));

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Updated sale/order with ' . $paymentStatus . ' status..writing status..');

// SEND MAIL TO WEBMASTER..
$sbj = str_replace(array(
  '{website}',
  '{gateway}'
), array(
  mc_cleanData($SETTINGS->website),
  $GATEWAY->gateway_name
), $msg_emails19);
$msg = MCLANG . 'email-templates/' . $MTEMP['cancelled'];
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

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Email sent to ' . $SETTINGS->website . ' webmaster @ ' . $SETTINGS->email . ($SETTINGS->addEmails ? ',' . $SETTINGS->addEmails : ''));

?>