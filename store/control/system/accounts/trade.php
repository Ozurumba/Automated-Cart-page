<?php

if (!defined('PARENT') || !isset($loggedInUser['type'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Set the global trade overrides..
if ($loggedInUser['type'] == 'trade') {

  if (!defined('MC_TRADE_DISCOUNT')) {
    define('MC_TRADE_DISCOUNT', (int) $loggedInUser['tradediscount']);
  }
  if (!defined('MC_TRADE_MIN')) {
    define('MC_TRADE_MIN', (int) $loggedInUser['minqty']);
  }
  if (!defined('MC_TRADE_MAX')) {
    define('MC_TRADE_MAX', (int) $loggedInUser['maxqty']);
  }
  if (!defined('MC_TRADE_STOCK')) {
    define('MC_TRADE_STOCK', (int) $loggedInUser['stocklevel']);
  }
  if (!defined('MC_TRADE_MIN_CHECKOUT')) {
    define('MC_TRADE_MIN_CHECKOUT', $loggedInUser['mincheckout']);
  }

}

?>