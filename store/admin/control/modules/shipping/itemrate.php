<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-itemrate.php');

// Add rates..
if (isset($_POST['process'])) {
  $run = $MCSHIP->addPerItemRate();
  if ($run[0]>0 || $run[1]>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updatePerItemRate();
  if ($run>0) {
    $OK2 = true;
  }
}

// Batch update..
if (isset($_POST['enabdis'])) {
  $run = $MCSHIP->batchUpdateRatesRoutine('per');
  $OK2 = true;
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deletePerItemRate();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript577) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-itemrate.php');
include(PATH . 'templates/footer.php');

?>
