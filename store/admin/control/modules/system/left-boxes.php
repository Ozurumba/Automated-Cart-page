<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'system/left-boxes.php');
include(MCLANG_REL . 'header.php');

include(PATH . 'control/classes/class.leftboxes.php');
$BOX           = new mcBoxContr();
$BOX->settings = $SETTINGS;

// Flag..
if (isset($_GET['flag'])) {
  $BOX->flag();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Add box..
if (isset($_GET['abox'])) {
  $BOX->add();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Update settings..
if (isset($_POST['process'])) {
  $BOX->update();
  $OK = true;
}

$pageTitle  = mc_cleanDataEntVars($msg_settings208) . ': ' . $pageTitle;
$loadiBox   = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/left-boxes.php');
include(PATH . 'templates/footer.php');

?>