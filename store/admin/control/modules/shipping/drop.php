<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-drop.php');

// Add drop shipper..
if (isset($_POST['process'])) {
  $MCSHIP->addDropShipper();
  $OK = true;
}

// Update drop shipper..
if (isset($_POST['update'])) {
  $MCSHIP->updateDropShipper();
  $OK2 = true;
}

// Delete drop shipper..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteDropShipper();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars((isset($_GET['edit']) ? $msg_dropship2 : $msg_dropship)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-drop.php');
include(PATH . 'templates/footer.php');

?>
