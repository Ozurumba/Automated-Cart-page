<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/order-statuses.php');

// Add status..
if (isset($_POST['process']) && $_POST['statname']) {
  $MCSYS->addStatus();
  $OK = true;
}

// Update status..
if (isset($_POST['update']) && $_POST['statname']) {
  $MCSYS->updateStatus();
  $OK2 = true;
}

// Delete status..
if (isset($_GET['del']) && $uDel == 'yes' && mc_rowCount('statuses WHERE orderStatus = \''.mc_digitSan($_GET['del']).'\'')==0) {
  $cnt = $MCSYS->deleteStatus();
  $OK3 = true;
}

$pageTitle = mc_cleanDataEntVars($msg_javascript192) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/order-statuses.php');
include(PATH . 'templates/footer.php');

?>
