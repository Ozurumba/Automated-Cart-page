<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-mp3.php');
include(MCLANG . 'catalogue/product-attributes.php');

// Re-order..
if (isset($_GET['order'])) {
  $MCPROD->reOrderMP3Files();
  exit;
}

// Add mp3s..
if (isset($_POST['process']) && !empty($_POST['mp3'])) {
  $run = $MCPROD->addMP3Files();
  if ($run>0) {
    $OK = true;
  }
}

// Update mp3..
if (isset($_POST['update'])) {
  $MCPROD->updateMP3File();
  $OK3 = true;
}

// Delete mp3..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCPROD->deleteMP3File();
  $OK2 = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_productmanage19) . ': ' . $pageTitle;
$soundManager  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-mp3.php');
include(PATH . 'templates/footer.php');

?>
