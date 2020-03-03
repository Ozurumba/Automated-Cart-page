<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-qty.php');

// Add rates..
if (isset($_POST['process'])) {
  $cnt = $MCSHIP->addQtyRate();
  if ($cnt>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updateQtyRate();
  if ($run>0) {
    $OK2 = true;
  }
}

// Batch update..
if (isset($_POST['enabdis'])) {
  $run = $MCSHIP->batchUpdateRatesRoutine('qtyrates');
  $OK2 = true;
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteQtyRate();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_admin3_0[55]) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-qty.php');
include(PATH . 'templates/footer.php');

?>
