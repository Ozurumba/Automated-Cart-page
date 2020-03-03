<?php

/*
  PRODUCT OPS
  See docs/catalogue-1.html
-----------------------------*/

date_default_timezone_set('UTC');

// SET PATH TO CART FOLDER..
define('PARENT', 1);
define('PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1) . '/');

// ERROR REPORTING..
include(PATH . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

// DATABASE CONNECTION..
include(PATH . 'control/connect.php');
include(PATH . 'control/functions.php');

// TIMEOUT..
mc_memAllocation();

// Connect..
mc_dbConnector();

// LOAD SETTINGS DATA
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));
define('MCLANG', mc_loadLangFile($SETTINGS));

include(PATH . 'control/defined.php');
include(PATH . 'control/system/constants.php');

// LOAD GLOBAL AND HEADER LANGUAGE FILES
include(MCLANG . 'global.php');
include(MCLANG . 'version2.1.php');
include(MCLANG . 'version3.0.php');
include(MCLANG . 'version3.4.php');
include(MCLANG . 'version3.7.php');
include(MCLANG . 'emails.php');

mc_fileController();

include(PATH . 'control/system/core/sys-controller.php');
include(PATH . 'control/classes/mailer/class.send.php');
include(PATH . 'control/classes/class.parser.php');
include(PATH . 'control/classes/class.rewrite.php');

$MCRWR             = new mcRewrite();
$MCMAIL            = new mcMail();
$MCPARSER          = new mcDataParser();
$MCMAIL->parser    = $MCPARSER;
$MCRWR->settings   = $SETTINGS;

// LOAD SETTINGS DATA..
// We can mask the error thrown here and redirect index file to installer..
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));

// Clear dead data..
$runOps = array();
mc_systemCartCleanUp($SETTINGS);
$runOps[] = 'Old sale data deleted and saved searched cleared';

// Expire special offers..
mc_clearProductOffers();
$runOps[] = 'Product offers that had expiry dates reset';

// Product Expiry...
$oString    = array();
$oStrCnt    = array();
$oDisable   = array();
$mailString = array();
$q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "products`
     WHERE `expiry` != '0000-00-00'
     AND `expiry` <= '" . date("Y-m-d") . "'
     ORDER BY `id`
     ") or die(mc_MySQLError(__LINE__, __FILE__));
while ($PD = mysqli_fetch_object($q)) {
  $v = array();
  // Are there expiry options?
  if ($PD->pStock > 0 && $PD->exp_price) {
    // Percentage or price set price reduction..
    switch(substr($PD->exp_price, -1)) {
      case '%';
        $perc     = substr($PD->exp_price, 0, -1);
        $sum      = @number_format(($perc * $PD->pPrice) / 100, 2, '.', '');
        $newPrice = @number_format(($PD->pPrice - $sum), 2, '.', '');
        break;
      default:
        $sum      = $PD->exp_price;
        $newPrice = $PD->exp_price;
        break;
    }
    // Mark as special offer?
    if ($PD->exp_special == 'yes') {
      $v['price'] = $PD->pPrice;
      $v['offer'] = $newPrice;
    } else {
      $v['price'] = $newPrice;
      $v['offer'] = '';
    }
    // Send notification?
    if ($PD->exp_send == 'yes') {
      $url = $MCRWR->url(array(
        $MCRWR->config['slugs']['prd'] . '/' . $PD->id . '/' . ($PD->rwslug ? $PD->rwslug : $MCRWR->title($PD->pName)),
        'pd=' . $PD->id
      ));
      $mailString[] = mc_cleanData($PD->pName);
      $mailString[] = str_replace('{price}', $sum, $msg_email_prod_expiry_string);
      $mailString[] = str_replace('{price}', $PD->pPrice, $msg_email_prod_expiry_string2);
      $mailString[] = str_replace('{price}', $newPrice, $msg_email_prod_expiry_string3);
      $mailString[] = str_replace('{offer}', ($PD->exp_special == 'yes' ? $msg_script5 : $msg_script6), $msg_email_prod_expiry_string4);
      $mailString[] = $url;
      $mailString[] = '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' . mc_defineNewline();
    }
    // Append text?
    if ($PD->exp_text) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "products` SET
      `pDescription` = CONCAT(`pDescription`,'" . mc_defineNewline() . mc_defineNewline() . mc_safeSQL(mc_cleanData($PD->exp_text)) . "'),
      `pPrice`       = '{$v['price']}',
      `pOffer`       = '{$v['offer']}',
      `expiry`       = '0000-00-00'
      WHERE `id` = '{$PD->id}'
      ");
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "products` SET
      `pPrice`       = '{$v['price']}',
      `pOffer`       = '{$v['offer']}',
      `expiry`       = '0000-00-00'
      WHERE `id` = '{$PD->id}'
      ");
    }
    $oStrCnt[] = $PD->id;
  } else {
    $oDisable[] = $PD->id;
  }
}

// Standard disable..
if (!empty($oDisable)) {
  mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "products` SET
  `pStock` = '0',
  `expiry` = '0000-00-00'
  WHERE `id` IN(" . mc_safeSQL(implode(',', $oDisable)) . ")
  ");
}

// Notification..
if (!empty($mailString)) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
  // SEND MAIL TO WEBMASTER..
  $sbj = str_replace(array(
    '{website}'
  ), array(
    mc_cleanData($SETTINGS->website)
  ), $msg_emails48);
  $msg = MCLANG . 'email-templates/product-expiry-notification.txt';
  $MCMAIL->addTag('{PRODUCTS}', rtrim(implode(mc_defineNewline(), $mailString)));
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
    'alive' => 'yes',
    'language' => $SETTINGS->languagePref
  ));
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
  $MCMAIL->smtpClose();
}

// Output..
if (POPS_CRON_OUTPUT) {
  echo '[' . date('j F Y @ H:iA') . '] Product Ops Completed' . mc_defineNewline() . mc_defineNewline();
  echo '[' . date('j F Y @ H:iA') . '] Products Expired with Price Adjustments: ' . count($oStrCnt) . mc_defineNewline();
  echo '[' . date('j F Y @ H:iA') . '] Products Expired and Disabled: ' . count($oDisable) . mc_defineNewline();
  foreach ($runOps AS $o) {
    echo mc_defineNewline() . '[' . date('j F Y @ H:iA') . '] ' . $o;
  }
  echo PHP_EOL . str_repeat('-=', 50) . PHP_EOL;
}

?>