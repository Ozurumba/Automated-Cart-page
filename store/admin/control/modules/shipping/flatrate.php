<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-flatrate.php');

// Add rates..
if (isset($_POST['process'])) {
  $run = $MCSHIP->addFlatRate();
  if ($run[0]>0 || $run[1]>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updateFlatRate();
  if ($run>0) {
    $OK2 = true;
  }
}

// Batch update..
if (isset($_POST['enabdis'])) {
  $run = $MCSHIP->batchUpdateRatesRoutine('flat');
  $OK2 = true;
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteFlatRate();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript438) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-flatrate.php');
include(PATH . 'templates/footer.php');

?>
