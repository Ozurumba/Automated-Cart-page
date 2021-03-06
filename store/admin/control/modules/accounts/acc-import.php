<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'accounts/accounts.php');
include(MCLANG . 'accounts/import.php');
include(MCLANG . 'catalogue/product-import.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(PATH . 'control/field-mapping.php');
include(MCLANG . 'accounts/add-account.php');

// Field information..
if (isset($_GET['field'])) {
  include(PATH . 'templates/windows/field-information.php');
  exit;
}

// Upload CSV file..
if (isset($_POST['process-upload-accounts'])) {
  // Refresh if no file was uploaded..
  if ($_FILES['file']['name'] == '') {
    header("Location: index.php?p=acc-import");
    exit;
  }
  $_SESSION['mc_importPref']          = array();
  // Set defaults..
  $lines                              = ($_POST['lines'] ? str_replace(array(
    '.',
    ','
  ), array(), mc_cleanData($_POST['lines'])) : '0');
  $del                                = ($_POST['del'] ? mc_cleanData($_POST['del']) : ',');
  $enc                                = ($_POST['enc'] ? mc_cleanData($_POST['enc']) : '"');
  $_SESSION['mc_importPref']['lines'] = $lines;
  $_SESSION['mc_importPref']['del']   = $del;
  $_SESSION['mc_importPref']['enc']   = $enc;
  $fields                             = $MCPROD->uploadImportFile($lines, $del, $enc);
  $FIELD_MAPPING                      = true;
}

// Field mapping process..
if (isset($_POST['process-accounts-mapping'])) {
  $_SESSION['mc_fieldMapping']     = array();
  $_SESSION['mc_fieldMapping_alt'] = array();
  for ($i = 0; $i < count($_POST['dbFields']); $i++) {
    if ($_POST['dbFields'][$i] != '0') {
      $_SESSION['mc_fieldMapping'][$i] = $_POST['dbFields'][$i];
    }
  }
  $ACC_OPTIONS = true;
}

// Add accounts to database..
if (isset($_POST['process-accounts'])) {
  $added = $MCACC->batchImportAccountsFromCSV();
  $OK    = true;
}

$pageTitle = mc_cleanDataEntVars($msg_admin3_0[59]) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/accounts/import.php');
include(PATH . 'templates/footer.php');

?>
