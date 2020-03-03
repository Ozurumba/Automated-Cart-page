<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping-countries.php');

// Add new countries..
if (isset($_POST['add_country'])) {
  $count = $MCSYS->addNewCountries();
  $OK2   = true;
}

// Edit country..
if (isset($_POST['update_country'])) {
  $MCSYS->updateCountry();
  $OK3   = true;
}

// Delete selected..
if (isset($_GET['delete']) && $uDel == 'yes') {
  $count = $MCSYS->deleteCountry();
  $OK4   = true;
}

// Add countries..
if (isset($_POST['endis'])) {
  $MCSYS->updateCountries();
  $OK = true;
}

$pageTitle = mc_cleanDataEntVars($msg_javascript31) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-countries.php');
include(PATH . 'templates/footer.php');

?>
