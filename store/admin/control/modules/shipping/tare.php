<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-tare-weight.php');

// Add rates..
if (isset($_POST['process']) && !empty($_POST['rService'])) {
  $run = $MCSHIP->addTareRates();
  if ($run[0]>0 && $run[1]>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updateTareRates();
  if ($run>0) {
    $OK2 = true;
  }
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteTareRates();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_header20) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-tare-weight.php');
include(PATH . 'templates/footer.php');

?>
