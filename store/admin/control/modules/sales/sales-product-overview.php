<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/sales-overview.php');
include(MCLANG . 'sales/sales-search.php');
include(MCLANG . 'catalogue/product-manage.php');

// View profit calculation..
if (isset($_GET['profit'])) {
  include(PATH . 'templates/windows/sales-overview-calculations.php');
  exit;
}

// Export hits..
if (isset($_POST['exp'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCPROD->dl = $DL;
  $MCPROD->exportOverviewToCSV();
}

$pageTitle = mc_cleanDataEntVars($msg_javascript163) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-product-overview.php');
include(PATH . 'templates/footer.php');

?>
