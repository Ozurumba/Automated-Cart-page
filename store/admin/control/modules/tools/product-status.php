<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/update-statuses.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-related.php');

if (isset($_POST['process'])) {
  if (!empty($_POST['range'])) {
    $MCPROD->updateProductStatuses();
    $OK = true;
  }
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript338) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/product-status.php');
include(PATH . 'templates/footer.php');

?>
