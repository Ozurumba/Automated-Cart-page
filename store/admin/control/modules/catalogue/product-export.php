<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-add.php');
include(MCLANG . 'catalogue/product-export.php');

// Export products..
if (isset($_POST['process'])) {
  mc_memoryLimit();
  include(REL_PATH . 'control/classes/class.download.php');
  $DL         = new mcDownload();
  $MCPROD->dl = $DL;
  $return = $MCPROD->exportProductsToCSV();
}

$pageTitle   = mc_cleanDataEntVars($msg_productmanage59) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/'.$cmd.'.php');
include(PATH . 'templates/footer.php');

?>
