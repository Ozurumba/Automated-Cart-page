<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/search-log.php');

// Reset hits..
if (isset($_GET['clear']) && $uDel == 'yes') {
  $cnt = $MCSYS->resetSearchLog();
  $OK  = true;
}

// Export hits..
if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCSYS->dl = $DL;
  $MCSYS->exportSearchLog($msg_searchlog8);
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript108) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/search-log.php');
include(PATH . 'templates/footer.php');

?>
