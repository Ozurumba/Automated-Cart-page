<?php

if (!defined('PARENT')) {
  include(PATH.'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG.'shipping/shipping-services.php');

// Add service..
if (isset($_POST['process']) && !empty($_POST['inZone'])) {
  $run = $MCSHIP->addService();
  if ($run[0]>0 && $run[1]>0) {
    $OK = true;
  }
}

// Update service..
if (isset($_POST['update'])) {
  $run = $MCSHIP->updateService();
  if ($run>0) {
    $OK2 = true;
  }
}

// Delete service..
if (isset($_GET['del']) && $uDel=='yes') {
  $cnt = $MCSHIP->deleteService();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript100).': '.$pageTitle;

include(PATH.'templates/header.php');
include(PATH.'templates/shipping/shipping-services.php');
include(PATH.'templates/footer.php');

?>
