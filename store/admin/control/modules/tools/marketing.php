<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'tools/marketing.php');

// Marketing class
include(PATH . 'control/classes/class.marketing.php');
$MCMK            = new marketing();
$MCMK->settings  = $SETTINGS;

// Generate tracker..
if (isset($_GET['gentrack'])) {
  echo $JSON->encode(array(
    'OK',
    $MCMK->trackCode()
  ));
  exit;
}

// Reset trackers..
if (!empty($_POST['reset'])) {
  $MCMK->resetTrackers();
  $OK4 = true;
}

// Add tracker..
if (isset($_POST['process'])) {
  $MCMK->addTracker();
  $OK = true;
}

// Update tracker..
if (isset($_POST['update'])) {
  $MCMK->updateTracker();
  $OK2 = true;
}

// Delete tracker..
if (isset($_GET['del']) && $uDel == 'yes') {
  $rows = $MCMK->deleteTracker();
  $OK3 = true;
}

$pageTitle     = mc_cleanDataEntVars((isset($_GET['edit']) ? $msg_marketing2 : $msg_marketing)) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/marketing-tracker.php');
include(PATH . 'templates/footer.php');

?>
