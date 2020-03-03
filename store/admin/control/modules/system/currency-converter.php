<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/currency-converter.php');

// Update currency converter..
if (isset($_POST['process'])) {
  $MCSYS->updateCurrencyConverter();
  $OK = true;
}

if (isset($_GET['processAuto'])) {
  $MCSYS->updateCurrencyConverter();
  $MCCRV->downloadExchangeRates();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

$pageTitle = mc_cleanDataEntVars($msg_javascript30) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/currency-converter.php');
include(PATH . 'templates/footer.php');

?>
