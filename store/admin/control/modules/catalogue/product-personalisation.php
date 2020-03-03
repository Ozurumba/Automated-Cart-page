<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-personalisation.php');
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'catalogue/categories.php');

// Adjust order..
if (isset($_GET['order'])) {
  $MCPROD->reOrderPersonalisation();
  exit;
}

if (isset($_POST['process']) && $_POST['persInstructions']) {
  $MCPROD->addPersonalisation();
  $OK = true;
}

if (isset($_POST['update']) && $_POST['persInstructions']) {
  $MCPROD->updatePersonalisation();
  $OK2 = true;
}

if (isset($_GET['del']) && ctype_digit($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCPROD->deletePersonalisation();
  $OK3 = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_personalisation4) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-personalisation.php');
include(PATH . 'templates/footer.php');

?>
