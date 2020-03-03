<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/sales-revenue.php');
include(MCLANG . 'sales/sales-search.php');
include(MCLANG . 'catalogue/product-manage.php');

// Export hits..
if (isset($_GET['export']) && isset($_GET['from']) && isset($_GET['to'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCSALE->dl = $DL;
  $MCSALE->exportRevenue();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript425) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-revenue.php');
include(PATH . 'templates/footer.php');

?>
