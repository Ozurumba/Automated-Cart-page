<?php

/* CALLBACK OPS - REFUNDED
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// MAIL..
if (!defined('MAIL_SWITCH')) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
}

// Get account..
$PAY_ACC = mc_getTableData('accounts', 'email', $SALE_ORDER->bill_2, ' AND `verified` = \'yes\'');

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Processing ' . $paymentStatus . ' actions..');

// UPDATE ORDER..
$GATEWAY->addRefunded($SALE_CODE, $SALE_ID);

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Updated sale/order with ' . $paymentStatus . ' status..writing status..');

// WRITE ORDER STATUS..
$price = (substr($INCOMING['refund-amount'], 0, 1) == '-' ? substr($INCOMING['refund-amount'], 1) : $INCOMING['refund-amount']);
$GATEWAY->writeOrderStatus($SALE_ID, str_replace('{amount}', $price, $public_checkout57) . ' ' . $GATEWAY->gateway_name, $GATEWAY->setOrderStatus('refunded'));

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Status written..sending email..');

// MAIL TAGS..
$MCMAIL->addTag('{PRODUCT_ORDER}', $GATEWAY->buildProductOrder($SALE_ID));
$MCMAIL->addTag('{CURRENCY}', $SETTINGS->baseCurrency);
$MCMAIL->addTag('{REF_AMOUNT}', $price);
$MCMAIL->addTag('{INVOICE_NO}', mc_saleInvoiceNumber($SALE_ORDER->invoiceNo, $SETTINGS));

//SEND MAIL TO BUYER..
$sbj = str_replace(array(
  '{website}',
  '{invoice}',
  '{gateway}'
), array(
  mc_cleanData($SETTINGS->website),
  mc_saleInvoiceNumber($SALE_ORDER->invoiceNo, $SETTINGS),
  $GATEWAY->gateway_name
), $msg_emails10);
$msg = MCLANG . 'email-templates/' . $MTEMP['refunded'];
$MCMAIL->sendMail(array(
  'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
  'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
  'to_email' => $SALE_ORDER->bill_2,
  'to_name' => $SALE_ORDER->bill_1,
  'subject' => $sbj,
  'replyto' => array(
    'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
    'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
  ),
  'template' => $msg,
  'language' => (isset($PAY_ACC->language) && $PAY_ACC->language ? $PAY_ACC->language : $SETTINGS->languagePref)
));
$MCMAIL->smtpClose();

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Refund confirmation sent to ' . $SALE_ORDER->bill_1 . ' @ ' . $SALE_ORDER->bill_2);

?>