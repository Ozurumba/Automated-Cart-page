<?php

/* CALLBACK OPS - PENDING
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

// WRITE STATUS
$GATEWAY->writeOrderStatus($SALE_ID, str_replace('{REASON}', (isset($INCOMING['pending-reason']) && $INCOMING['pending-reason'] ? $INCOMING['pending-reason'] : 'N/A'), $public_checkout128), $GATEWAY->setOrderStatus('pending'));

// MAIL TAGS..
$MCMAIL->addTag('{PRODUCT_ORDER}', $GATEWAY->buildProductOrder($SALE_ID));
$MCMAIL->addTag('{REASON}', (isset($INCOMING['pending-reason']) ? $INCOMING['pending-reason'] : ''));
$MCMAIL->addTag('{INV_STATUS}', (isset($INCOMING['inv-status']) ? $INCOMING['inv-status'] : ''));
$MCMAIL->addTag('{FRAUD_STATUS}', (isset($INCOMING['fraud-status']) ? $INCOMING['fraud-status'] : ''));

// Send mail for valid email address..
if (mswIsValidEmail($SALE_ORDER->bill_2)) {

  // SEND MAIL TO WEBMASTER..
  $sbj = str_replace(array(
    '{website}',
    '{gateway}'
  ), array(
    mc_cleanData($SETTINGS->website),
    $GATEWAY->gateway_name
  ), $msg_emails9);
  $msg = MCLANG . 'email-templates/' . $MTEMP['pending-wm'];
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
    'alive' => 'yes',
    'language' => $SETTINGS->languagePref
  ));

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Email(s) sent to webmaster @ ' . $SETTINGS->email . ($SETTINGS->addEmails ? ',' . $SETTINGS->addEmails : ''));

  // SEND EMAIL TO OTHER USERS..
  $qUsr = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `userName`,`userEmail` FROM `" . DB_PREFIX . "users`
          WHERE `enableUser` = 'yes'
          AND `userNotify` = 'yes'
          ORDER BY `id`
          ");
  if (mysqli_num_rows($qUsr) > 0) {
    while ($USR = mysqli_fetch_object($qUsr)) {
      $ousr = array_map('trim', explode(',', $USR->userEmail));
      $ot   = array();
      if (count($ousr) > 1) {
        $ot = $ousr;
        unset($ot[0]);
      }
      if (isset($ousr[0]) && mswIsValidEmail($ousr[0])) {
        $GATEWAY->writeLog($SALE_ID, 'Email(s) sent to: ' . $USR->userName . ' @ ' . print_r($ousr, true));
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $ousr[0],
          'to_name' => $USR->userName,
          'subject' => $sbj,
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => $msg,
          'add-emails' => (!empty($ot) ? implode(',',$ot) : ''),
          'alive' => 'yes',
          'language' => $SETTINGS->languagePref
        ));
      }
    }
  }

  // SEND MAIL TO BUYER..
  $sbj = str_replace(array(
    '{website}',
    '{gateway}'
  ), array(
    mc_cleanData($SETTINGS->website),
    $GATEWAY->gateway_name
  ), $msg_emails8);
  $msg = MCLANG . 'email-templates/' . $MTEMP['pending'];
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
  $GATEWAY->writeLog($SALE_ID, 'Email sent to ' . $SALE_ORDER->bill_1 . ' @ ' . $SALE_ORDER->bill_2 . '..');

} else {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Email ' . $SALE_ORDER->bill_2 . ' invalid so no emails sent.');

}

?>