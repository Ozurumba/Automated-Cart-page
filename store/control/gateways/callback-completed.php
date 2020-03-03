<?php

/* CALLBACK OPS - COMPLETED
----------------------------------*/

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Prevent timeouts on completion callback..
mc_memoryLimit();

// MAIL..
if (!defined('MAIL_SWITCH')) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
}

// IS WISH LIST PURCHASE?
if ($SETTINGS->en_wish == 'yes' && $SALE_ORDER->wishlist > 0) {
  $WS_ACC = mc_getTableData('accounts', 'id', $SALE_ORDER->wishlist, ' AND `verified` = \'yes\'');
  if (isset($WS_ACC->id)) {
    $GATEWAY->writeLog($SALE_ID, 'Wish list sale identified, recipient of purchase is: ' . mc_cleanData($WS_ACC->name));
    define('WISH_LIST_ACTIVE', 1);
  }
}

// GET NEXT INVOICE NUMBER..
$invoice = $GATEWAY->getInvoiceNo();

// AFFILIATE CODE FROM MAIAN AFFILIATE
define('MSW_AFF_SALE_CODE', $SALE_ORDER->affcode);

// MAIL TAGS..
include(PATH . 'control/gateways/callback-mail-tags.php');

// UPDATE ORDER / SALE STATUS..
$accountCreationStatus = ($SALE_ORDER->gatewayID == 'create-account' ? 'yes' : 'no');

// ACCOUNT CREATION..
if ($SALE_ORDER->account == 0) {
  if ($accountCreationStatus == 'yes') {
    define('ACC_FLAG_YES', 1);
    $GATEWAY->writeLog($SALE_ID, 'Account creation set to YES on checkout, preparing to create account..');
    include(PATH . 'control/gateways/callback-account-creation.php');
  } else {
    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Account WILL NOT be created on checkout.');
  }
} else {
  $GATEWAY->writeLog($SALE_ID, 'Account already exists for this order, so account options ignored');
}

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Updating sale in database..');

// ACTIVATE
$GATEWAY->changeStatusToLiveSale(array(
  'id' => $SALE_ID,
  'code' => $SALE_CODE,
  'trans' => (isset($INCOMING['trans-id']) ? $INCOMING['trans-id'] : ''),
  'invoice' => $invoice,
  'account' => (isset($nOID) ? $nOID : $SALE_ORDER->account)
));

// Get account..
$PAY_ACC = mc_getTableData('accounts', 'email', $SALE_ORDER->bill_2, ' AND `verified` = \'yes\'');

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Sale updated..checking order type and writing status..');

// WRITE ORDER STATUS..
$isDownloadOnly = $GATEWAY->checkOrderForDownloadsOnly($SALE_ID);
$isVirtualOnly  = $GATEWAY->checkOrderForVirtualOnly($SALE_ID);
if ($isDownloadOnly == 'yes') {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Download ONLY order detected..');

  // Set status for download order..
  if (!in_array($SALE_ORDER->paymentMethod, array('bank','cod','cheque','phone'))) {
    $wos = $GATEWAY->setOrderStatus('download');
    $GATEWAY->writeOrderStatus($SALE_ID, $public_checkout58, $wos);
  } else {
    // If total is free, products are available and sale is completed..
    if (in_array($SALE_ORDER->grandTotal, array('0','0.00'))) {
      $wos = $GATEWAY->setOrderStatus('download');
      $GATEWAY->writeOrderStatus($SALE_ID, $public_checkout58, $wos);
    } else {
      // Pending for none gateway..
      $wos = $GATEWAY->setOrderStatus('pending');
      $GATEWAY->writeOrderStatus($SALE_ID, $mc_checkout[31], $wos);
    }
  }
  define('DOWNLOAD_ONLY', 1);

} elseif ($isVirtualOnly == 'yes') {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Gift Certificate ONLY order detected..');

  $GATEWAY->writeOrderStatus($SALE_ID, $public_checkout26, $GATEWAY->setOrderStatus('virtual'));
  define('VIRTUAL_ONLY', 1);

} else {

  $GATEWAY->writeOrderStatus($SALE_ID, $public_checkout33, $GATEWAY->setOrderStatus('completed'));

}

// If free or on account transaction, write completion status
if (defined('FREE_TRANS') || defined('ONACCOUNT_TRANS')) {
  $GATEWAY->writeOrderStatus($SALE_ID, $mc_checkout[30], $GATEWAY->setOrderStatus('completed'));
}

// LOG..
$GATEWAY->writeLog($SALE_ID, 'Status written and completed. Updating coupons if applicable..');

// DOES THIS ORDER HAVE A DISCOUNT COUPON..
if ($SALE_ORDER->couponCode && $SALE_ORDER->codeType == 'discount') {

  // Search for campaign code..
  $CAMPAIGN = mc_getTableData('campaigns', 'cDiscountCode', $SALE_ORDER->couponCode);

  if (isset($CAMPAIGN->id)) {

    $GATEWAY->addCouponUsage($CAMPAIGN, $SALE_ORDER->couponCode, $SALE_ID, $SALE_ORDER->couponTotal);

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Coupon completed for code "' . $SALE_ORDER->couponCode . '". Checking gift certificates if applicable..');

  } else {

    // LOG..
    $GATEWAY->writeLog($SALE_ID, 'Coupon used, but campaign not found for "' . $SALE_ORDER->couponCode . '". Checking gift certificates if applicable..');

  }

} else {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'No coupon used. Checking gift certificates if applicable..');

}

// GIFT CERTIFICATES..
include(PATH . 'control/gateways/callback-gift.php');

$GATEWAY->writeLog($SALE_ID, 'Beginning stock level checks..');

// STOCK LEVEL ADJUSTMENT AND LOW STOCK NOTIFICATION..
$adjStkFlag = 'no';
if (!isset($PAY_ACC->type)) {
  $adjStkFlag = 'yes';
} else {
  if ($PAY_ACC->type != 'trade') {
    $adjStkFlag = 'yes';
  }
}
if ($adjStkFlag == 'yes') {
  // Cache clear..
  $MCCACHE->clear_cache_file('categories');
  $lowStock = $GATEWAY->stockLevelAdjustment($SALE_ID);
  if (!empty($lowStock)) {
    // Send email for low stock notification..
    if (!empty($ls)) {
      $sbj = str_replace(array(
        '{website}'
      ), array(
        mc_cleanData($this->settings->website)
      ), $msg_emails5);
      $msg = MCLANG . 'email-templates/low-stock-notification.txt';
      $MCMAIL->addTag('{PRODUCTS}', implode(mc_defineNewline() . mc_defineNewline(), $ls));
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $SETTINGS->email,
        'to_name' => $SETTINGS->website,
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'add-emails' => $SETTINGS->addEmails,
        'language' => $SETTINGS->languagePref
      ));
      $MCMAIL->smtpClose();

      // LOG..
      $GATEWAY->writeLog($SALE_ID, 'Sending low stock notification emails for ' . count($ls) . ' products to ' . $SETTINGS->email . ($SETTINGS->addEmails ? ',' . $SETTINGS->addEmails : '') . '.');
    }
  }

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Stock levels updated successfully.');

} else {

  // LOG..
  $GATEWAY->writeLog($SALE_ID, 'Stock levels not updated for trade accounts.');

}

// ORDER EMAILS..
$GATEWAY->writeLog($SALE_ID, 'Sending order emails..');

// Send mail for valid email address..
if (mswIsValidEmail($SALE_ORDER->bill_2)) {

  // SEND MAIL TO WEBMASTER..
  $sbj = str_replace(array(
    '{website}',
    '{invoice}',
    '{gateway}'
  ), array(
    mc_cleanData($SETTINGS->website),
    mc_saleInvoiceNumber($invoice, $SETTINGS),
    $GATEWAY->gateway_name
  ), ($SALE_ORDER->type == 'trade' ? $msg_emails43 : $msg_emails7));
  $msg = MCLANG . 'email-templates/' . $MTEMP['completed-wm' . (($isDownloadOnly == 'yes' || $isVirtualOnly == 'yes') && $SALE_ORDER->grandTotal > 0 ? '-dl' : '')];
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
  $GATEWAY->writeLog($SALE_ID, 'Email(s) sent to ' . $SETTINGS->website . ' webmaster > ' . $SETTINGS->email . ($SETTINGS->addEmails ? ',' . $SETTINGS->addEmails : ''));

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
    '{invoice}',
    '{gateway}'
  ), array(
    mc_cleanData($SETTINGS->website),
    mc_saleInvoiceNumber($invoice, $SETTINGS),
    $GATEWAY->gateway_name
  ), $msg_emails6);
  $msg = MCLANG . 'email-templates/' . $MTEMP['completed' . (defined('WISH_LIST_ACTIVE') ? '-wish' : '') . (($isDownloadOnly == 'yes' || $isVirtualOnly == 'yes') && $SALE_ORDER->grandTotal > 0 ? '-dl' : '')];
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

// SEND EMAIL TO WISH LIST RECIPIENT
if (defined('WISH_LIST_ACTIVE')) {

  $sbj = str_replace(array(
    '{website}',
    '{invoice}',
    '{gateway}'
  ), array(
    mc_cleanData($SETTINGS->website),
    mc_saleInvoiceNumber($invoice, $SETTINGS),
    $GATEWAY->gateway_name
  ), $msg_emails42);
  $msg = MCLANG . 'email-templates/' . $MTEMP['completed-wish-recipient' . ($isDownloadOnly == 'yes' || $isVirtualOnly == 'yes' ? '-dl' : '')];
  $MCMAIL->sendMail(array(
    'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
    'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
    'to_email' => $WS_ACC->email,
    'to_name' => mc_cleanData($WS_ACC->name),
    'subject' => $sbj,
    'replyto' => array(
      'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
      'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
    ),
    'template' => $msg,
    'language' => (isset($WS_ACC->language) && $WS_ACC->language ? $WS_ACC->language : $SETTINGS->languagePref)
  ));

  $MCMAIL->smtpClose();

  $GATEWAY->writeLog($SALE_ID, 'Email sent to wish list recipient ' . mc_cleanData($WS_ACC->name) . ' @ ' . $WS_ACC->email);
}

// DROP SHIPPERS..
include(PATH . 'control/gateways/callback-drop-shippers.php');

// AFFILIATE
if (isset($cfg['affiliate']['en']) && $cfg['affiliate']['en'] == 'yes' && MSW_AFF_SALE_CODE) {
  define('CALLBACK_AFFILIATE', 1);
  include(PATH . 'control/classes/class.affiliate.php');
  $AFF = new affiliate();
  $AFF->cfg = $cfg;
  $AFF->json = $MCJSON;
  include(PATH . 'control/gateways/callback-affiliate.php');
}

?>