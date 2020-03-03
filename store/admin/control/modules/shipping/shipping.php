<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'shipping/shipping.php');

if (isset($_POST['update'])) {
  $MCSYS->shipOptions();
  $OK = true;
}

$pageTitle = mc_cleanDataEntVars($msg_shipmanage) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/shipping/shipping-rates-manage.php');
include(PATH . 'templates/footer.php');

?>