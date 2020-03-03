<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/update-tax.php');
include(MCLANG . 'tools/update-prices.php');

// Update rates..
if (isset($_POST['process'])) {
  $run = $MCSYS->batchUpdateTaxRates();
  if ($run>0) {
    $OK = true;
  }
}

$pageTitle = mc_cleanDataEntVars($msg_javascript283) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-update-tax.php');
include(PATH . 'templates/footer.php');

?>
