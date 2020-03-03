<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'tools/entry-log.php');
include(MCLANG . 'accounts/add-account.php');

if (isset($_GET['reset']) && $uDel == 'yes') {
  $cnt = $MCSYS->clearEntryLog();
  $OK  = true;
}

if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCSYS->dl = $DL;
  $MCSYS->exportEntryLog();
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript99) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/tools/entry-log.php');
include(PATH . 'templates/footer.php');

?>
