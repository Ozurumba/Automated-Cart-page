<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/update-rates.php');
include(MCLANG . 'tools/update-prices.php');

// Update rates..
if (isset($_POST['process'])) {
  $run = $MCSHIP->batchUpdateRates();
  if ($run>0) {
    $OK = true;
  }
}

$pageTitle = mc_cleanDataEntVars($msg_javascript173) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-update-rates.php');
include(PATH . 'templates/footer.php');

?>
