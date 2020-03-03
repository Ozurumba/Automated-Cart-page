<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'tools/stock-overview.php');
include(MCLANG . 'catalogue/product-manage.php');

// Update stock..
if (isset($_POST['process'])) {
  $MCPROD->updateProductStock();
  $OK = true;
}

// Export stock..
if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCPROD->dl = $DL;
  $MCPROD->exportStockOverviewToCSV();
}

$pageTitle     = mc_cleanDataEntVars($msg_header19) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/stock-overview.php');
include(PATH . 'templates/footer.php');

?>
