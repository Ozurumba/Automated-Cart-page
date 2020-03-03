<?php

if (!defined('PARENT') || $SETTINGS->en_wish == 'no' || defined('MC_TRADE_DISCOUNT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/wishlist.php');

// Export wish stats..
if (isset($_GET['export'])) {
  include(REL_PATH . 'control/classes/class.download.php');
  $DL        = new mcDownload();
  $MCACC->dl = $DL;
  $MCACC->exportWish();
}

$pageTitle     = mc_cleanDataEntVars($msg_admin3_0[49]) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/wishlist.php');
include(PATH . 'templates/footer.php');

?>
