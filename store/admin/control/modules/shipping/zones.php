<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-zones.php');

// Add zone..
if (isset($_POST['process']) && !empty($_POST['zCountry'])) {
  $ftemp = $_FILES['zones']['tmp_name'];
  $fname = $_FILES['zones']['name'];
  if (($ftemp && $fname) || $_POST['zones2']) {
    if (!file_exists($ftemp) && $fname) {
      die('Temp file "' . $ftemp . '" does not exist and could not be read. This is a server error, usually caused by permissions.<br><br>
           Check the system can access your tmp/ directory. Please revert to single entry if this persists.');
    }
    $MCSHIP->addNewZone($ftemp, $fname);
    $OK = true;
  } else {
    header("Location: index.php?p=zones");
    exit;
  }
}

// Update zone..
if (isset($_POST['update']) && $_POST['zCountry'] > 0) {
  $ftemp = $_FILES['zones']['tmp_name'];
  $fname = $_FILES['zones']['name'];
  if (($ftemp && $fname) || $_POST['zones2']) {
    if (!file_exists($ftemp) && $fname) {
      die('Temp file "' . $ftemp . '" does not exist and could not be read. This is a server error, usually caused by permissions.<br><br>
           Check the system can access your tmp/ directory. Please revert to single entry if this persists.');
    }
  }
  $MCSHIP->updateZone($ftemp, $fname);
  $OK2 = true;
}

// Delete zone..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSHIP->deleteZone();
  $OK3 = true;
}

$pageTitle = mc_cleanDataEntVars($msg_javascript32) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-zones.php');
include(PATH . 'templates/footer.php');

?>