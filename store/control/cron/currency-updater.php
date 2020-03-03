<?php

/*
  CURRENCY UPDATER CRON
  See docs/system-3.html
-----------------------------*/

date_default_timezone_set('UTC');

// PATHS..
define('PARENT', 1);
define('PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1) . '/');

// ERROR REPORTING..
include(PATH . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

// INCLUDE FILES..
include(PATH . 'control/system/constants.php');
include(PATH . 'control/connect.php');
include(PATH . 'control/functions.php');
include(PATH . 'control/currencies.php');
include(PATH . 'control/defined.php');

// TIMEOUT..
mc_memAllocation();

// CONNECTION..
mc_dbConnector();

// LOAD SETTINGS DATA..
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));

// UPDATE..
if (isset($SETTINGS->languagePref) && function_exists('curl_init')) {

  // DETECT TIMEZONE
  mc_dateTimeDetect($SETTINGS);

  // CLASS..
  include(PATH . 'control/classes/class.currencies.php');

  // DOWNLOAD NEW RATES..
  $MCCRV           = new curConverter();
  $MCCRV->prefix   = DB_PREFIX;
  $MCCRV->settings = $SETTINGS;
  $MCCRV->downloadExchangeRates();

  // CRON OUTPUT..
  if (CURRENCY_CONVERTER_CRON_OUTPUT) {
    echo '[' . date('j F Y @ H:iA') . '] Currencies successfully updated' . PHP_EOL . str_repeat('-=', 50) . PHP_EOL;
  }

} else {

  if (CURRENCY_CONVERTER_CRON_OUTPUT) {
    if (!function_exists('curl_init')) {
      echo 'Auto currency update failed: "CURL" functions not available. <a href="http://php.net/manual/en/book.curl.php" onclick="window.open(this);return false">CURL</a> functions must be enabled on server.';
    } else {
      echo 'Auto currency update failed: Settings not detected, currency update aborted @ ' . date('j F Y H:iA');
    }
  }

}

?>