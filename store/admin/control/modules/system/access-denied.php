<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/access-denied.php');

// Display message if restriction is reached..
$pageTitle = $msg_acsden . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/access-denied.php');
include(PATH . 'templates/footer.php');

?>