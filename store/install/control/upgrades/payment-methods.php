<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// PAYMENT METHODS
//==========================

if (mswCheckTable('paymentmethods') == 'yes') {

  // Load current information into array..
  $METHODS = @mysqli_fetch_object(@mysqli_query($GLOBALS["___msw_sqli"], "select * from `" . DB_PREFIX . "paymentmethods`"));

  mc_upgradeLog('Loading old payment method data');

  // Process if something found..
  if (property_exists($METHODS, 'id')) {

    // Get original information arrays..
    include(PATH . 'control/upgrades/appendix/method-arrays.php');

    // Insert data into new methods table..
    include(PATH . 'control/upgrades/appendix/methods.php');

    // Update original data into new fields..
    foreach (array_keys($gateways) AS $gw) {
      @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "methods` set
      `status`       = '" . (in_array($gateways[$gw]['status'], array(
        'yes',
        'no'
      )) ? $gateways[$gw]['status'] : 'no') . "',
      `plaintext`    = '" . mc_safeSQL($gateways[$gw]['plain']) . "',
      `htmltext`     = '" . mc_safeSQL($gateways[$gw]['html']) . "',
      `info`         = '" . mc_safeSQL($gateways[$gw]['info']) . "'
      where `method` = '{$gw}'
      limit 1
      ");
      mc_upgradeLog('Completed: Updated original payment method: ' . $gw);
    }

    // Insert data into new methods parameters table..
    if (mswCheckTable('methods_params') == 'yes') {
      include(PATH . 'control/upgrades/appendix/methods-params.php');
    }

    // Drop original table..
    mc_upgradeLog('Completed: Dropping old payment methods table');
    @mysqli_query($GLOBALS["___msw_sqli"], "drop table `" . DB_PREFIX . "paymentmethods`");

  }

} else {

  // New columns..
  if (mswCheckColumn('methods', 'defmeth') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "methods` add column `defmeth` enum('yes', 'no') not null default 'no' after `status`");
    mc_upgradeLog('Completed: defmeth column added to `' . DB_PREFIX . 'methods` table');
  }

  if (mswCheckColumn('methods', 'viewtype') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "methods` add column `viewtype` varchar(20) not null default 'a' after `statuses`");
    mc_upgradeLog('Completed: viewtype column added to `' . DB_PREFIX . 'methods` table');
  }

  // Add new gateways..
  $countMethods = mc_rowCount('methods WHERE `orderby` < 99');
  $serialize = 'a:5:{s:9:"completed";s:8:"shipping";s:8:"download";s:9:"completed";s:7:"virtual";s:9:"completed";s:7:"pending";s:7:"pending";s:8:"refunded";s:6:"refund";}';
  if (mc_rowCount('methods WHERE `method` = \'sectrade\'') == 0) {
    @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "methods` (`orderby`, `method`, `display`, `status`, `defmeth`, `liveserver`, `sandboxserver`, `plaintext`, `htmltext`, `info`, `redirect`, `image`, `docs`, `webpage`, `statuses`) values (" . (++$countMethods) . ", 'sectrade', 'Secure Trading', 'no', 'no', 'https://payments.securetrading.net/process/payments/details', 'https://payments.securetrading.net/process/payments/details', NULL, NULL, '', '', 'sectrade.png', 'payment-26', 'http://www.securetrading.com', '" . mc_safeSQL($serialize) . "')");
    mc_upgradeLog('Completed: payment gateway Secure Trading added to `' . DB_PREFIX . 'methods` table');

    // Add params..
    @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "methods_params` (`method`, `param`, `value`) VALUES
    ('sectrade', 'site-reference', ''),
    ('sectrade', 'notify-password', ''),
    ('sectrade', 'merchant-password', '')");
    mc_upgradeLog('Completed: default gateway Secure Trading params added to `' . DB_PREFIX . 'methods_params` table');
  }
  if (mc_rowCount('methods WHERE `method` = \'paysense\'') == 0) {
    @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "methods` (`orderby`, `method`, `display`, `status`, `defmeth`, `liveserver`, `sandboxserver`, `plaintext`, `htmltext`, `info`, `redirect`, `image`, `docs`, `webpage`, `statuses`, `viewtype`) values (" . (++$countMethods) . ", 'paysense', 'PaymentSense', 'no', 'no', 'https://mms.paymentprocessor.net/Pages/PublicPages/PaymentForm.aspx', 'https://mms.paymentprocessor.net/Pages/PublicPages/PaymentForm.aspx', NULL, NULL, '', '', 'paysense.png', 'payment-31', 'http://www.paymentsense.co.uk/', '" . mc_safeSQL($serialize) . "', 'g,p,t')");
    mc_upgradeLog('Completed: payment gateway PaymentSense added to `' . DB_PREFIX . 'methods` table');

    // Add params..
    @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "methods_params` (`method`, `param`, `value`) VALUES
    ('paysense', 'pre-share-key', ''),
    ('paysense', 'merchant-id', ''),
    ('paysense', 'password', '')");
    mc_upgradeLog('Completed: default gateway PaymentSense params added to `' . DB_PREFIX . 'methods_params` table');
  }
  if (mc_rowCount('methods WHERE `method` = \'stripe\'') == 0) {
    @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "methods` (`orderby`, `method`, `display`, `status`, `defmeth`, `liveserver`, `sandboxserver`, `plaintext`, `htmltext`, `info`, `redirect`, `image`, `docs`, `webpage`, `statuses`, `viewtype`) values (" . (++$countMethods) . ", 'stripe', 'Stripe', 'no', 'no', '', '', NULL, NULL, '', '', 'stripe.png', 'payment-3', 'https://stripe.com/gb', '" . mc_safeSQL($serialize) . "', 'g,p,t')");
    mc_upgradeLog('Completed: payment gateway Stripe added to `' . DB_PREFIX . 'methods` table');

    // Add params..
    @mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "methods_params` (`method`, `param`, `value`) VALUES
    ('stripe', 'perishable-key', ''),
    ('stripe', 'secret-key', ''),
    ('stripe', 'image', ''),
    ('stripe', 'locale', ''),
    ('stripe', 'endpoint-secret', '')");
    mc_upgradeLog('Completed: default gateway Stripe params added to `' . DB_PREFIX . 'methods_params` table');
  }
  if (mc_rowCount('methods WHERE `method` = \'account\'') == 0) {
    @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "methods` (`orderby`, `method`, `display`, `status`, `defmeth`, `liveserver`, `sandboxserver`, `plaintext`, `htmltext`, `info`, `redirect`, `image`, `docs`, `webpage`, `statuses`, `viewtype`) VALUES (" . (++$countMethods) . ", 'account', 'On Account', 'yes', 'no', '', '', '', '', '', '', 'account.png', 'payment-6', '', '" . mc_safeSQL($serialize) . "', 't')");
    mc_upgradeLog('Completed: On Account payment method added to `' . DB_PREFIX . 'methods` table');
  }

}

// Delete obsolete gateways..
@mysqli_query($GLOBALS["___msw_sqli"], "delete from `" . DB_PREFIX . "methods`
where `method` IN('google','nochex','liqpay','eway','paypoint','beanstream','iris',
'realex','pay360','jambok','jambot','fetch','paya','payza','charity')");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: obsolete gateways removed');
}

@mysqli_query($GLOBALS["___msw_sqli"], "delete from `" . DB_PREFIX . "methods_params`
where `method` IN('google','nochex','liqpay','eway','paypoint','beanstream',
'iris','realex','pay360','jambok','jambot','fetch','paya','payza','charity')");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: obsolete gateway params removed');
}

// Update existing gateways..
@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "methods` set
`method` = 'payvector',
`display` = 'Pay Vector',
`liveserver` = 'https://mms.payvector.net/Pages/PublicPages/PaymentForm.aspx',
`sandboxserver` = 'https://mms.payvector.net/Pages/PublicPages/PaymentForm.aspx',
`image` = 'payvector.png',
`webpage` = 'http://www.payvector.co.uk'
WHERE `method` = 'iridium'");

foreach (array(
  'moneybookers' => 'skrill',
  'iridium' => 'payvector',
  'suvtoy' => 'paytrail',
  '2checkout' => 'twocheckout'
) AS $k => $v
) {

  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "methods` set `method` = '{$v}' where `method` = '{$k}'");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "methods_params` set `method` = '{$v}' where `method` = '{$k}'");
  mc_upgradeLog('Completed: gateway method name ' . $k . ' renamed to ' . $v . ' for method and params');

}

mc_upgradeLog('Completed: Existing gateways updated and gateway ops completed');

?>