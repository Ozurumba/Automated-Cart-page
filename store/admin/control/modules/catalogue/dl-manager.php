<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/download-manager.php');

$pageTitle     = mc_cleanDataEntVars($msg_javascript398) . ': ' . $pageTitle;
$loadElFinder  =  true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/download-manager.php');
include(PATH . 'templates/footer.php');

?>
