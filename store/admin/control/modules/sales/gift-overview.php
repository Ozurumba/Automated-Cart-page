<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/gift-overview.php');

// Gift class
include(PATH . 'control/classes/class.gift.php');
$MCGIFT            = new gift();
$MCGIFT->settings  = $SETTINGS;

// Export hits..
if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCGIFT->dl = $DL;
  $MCGIFT->exportGiftOverviewToCSV();
}

if (isset($_GET['del'])) {
  $cnt = $MCGIFT->deleteGiftCode();
  $OK  = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_header24) . ': ' . $pageTitle;
$loadiBox   = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/gift-overview.php');
include(PATH . 'templates/footer.php');

?>
