<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'tools/hit-count-overview.php');
include(MCLANG . 'catalogue/product-manage.php');

// Reset hits..
if (isset($_GET['reset']) && $uDel == 'yes') {
  $MCPROD->resetProductHits();
  $OK = true;
}

// Export hits..
if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCPROD->dl = $DL;
  $MCPROD->exportHitsToCSV();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript164) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/hit-count-overview.php');
include(PATH . 'templates/footer.php');

?>
