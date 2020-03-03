<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-search.php');

if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCSALE->dl = $DL;
  $MCSALE->exportStatsToCSV();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript140) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/stats.php');
include(PATH . 'templates/footer.php');

?>
