<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-rates.php');

// Add rates..
if (isset($_POST['process']) && !empty($_POST['rService'])) {
  $run = $MCSHIP->addRates();
  if ($run[0]>0 && $run[1]>0) {
    $OK = true;
  }
}

// Update rates..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updateRates();
  if ($run>0) {
    $OK2 = true;
  }
}

// Delete rates..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteRates();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript33) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-rates.php');
include(PATH . 'templates/footer.php');

?>
