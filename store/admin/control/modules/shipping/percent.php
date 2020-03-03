<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-percent.php');

// Add rates..
if (isset($_POST['process'])) {
  $cnt = $MCSHIP->addPercentRate();
  if ($cnt>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updatePercentRate();
  if ($run>0) {
    $OK2 = true;
  }
}

// Batch update..
if (isset($_POST['enabdis'])) {
  $run = $MCSHIP->batchUpdateRatesRoutine('percent');
  $OK2 = true;
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deletePercentRate();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript439) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-percent.php');
include(PATH . 'templates/footer.php');

?>
