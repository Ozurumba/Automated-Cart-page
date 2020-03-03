<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/sales-export.php');
include(MCLANG . 'sales/sales-search.php');
include(MCLANG . 'sales/view-sales.php');

if (isset($_POST['process'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCSALE->dl = $DL;
  $return     = $MCSALE->exportBuyersToCSV();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript134) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-export-buyers.php');
include(PATH . 'templates/footer.php');

?>
