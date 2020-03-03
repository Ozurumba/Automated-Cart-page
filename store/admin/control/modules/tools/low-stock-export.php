<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/low-stock-export.php');

// Export..
if (isset($_POST['process'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCSYS->dl = $DL;
  $return    = $MCSYS->exportLowStockItems();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript319) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/low-stock-export.php');
include(PATH . 'templates/footer.php');

?>
