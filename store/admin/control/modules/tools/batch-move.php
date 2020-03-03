<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'tools/product-move.php');

if (isset($_POST['process']) && $_POST['destination']>0) {
  if (!empty($_POST['products'])) {
    $MCPROD->batchMoveProductsBetweenCategories();
    $_GET['cat'] = $_POST['destination'];
    $OK          = true;
  }
}

$pageTitle = mc_cleanDataEntVars($msg_javascript197) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/product-move.php');
include(PATH . 'templates/footer.php');

?>
