<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'shipping/shipping-rates.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'sales/sales-update.php');

// Create folder..
if (isset($_GET['newfldr'])) {
  // Clean folder name..
  $folder = preg_replace('/[^a-zA-Z0-9-_]+/', '', $_GET['newfldr']);
  // Create folder..
  if ($folder) {
    if ($folder == 'products') {
      $folder = 'products2';
    }
    $status = $MCCAT->createCategoryFolder($folder);
  }
  echo $JSON->encode(array(
    'message' => 'create-folder-category',
    'status' => $status,
    'error' => $msg_javascript201,
    'ok' => $msg_javascript154,
    'folder' => ($folder ? $folder : 'none')
  ));
  exit;
}

if (isset($_POST['process'])) {
  $run = $MCPROD->addProductPictures();
  if ($run > 0) {
    $OK = true;
  } else {
    header("Location: index.php?p=product-pictures&product=" . $_GET['product']);
    exit;
  }
}

if (isset($_GET['delete']) && $uDel == 'yes') {
  $cnt = $MCPROD->deleteProductPicture();
  $OK2 = true;
}

if (isset($_POST['process_pics'])) {
  $MCPROD->updatePictures();
  $OK3 = true;
}

$pageTitle    = mc_cleanDataEntVars($msg_javascript28) . ': ' . $pageTitle;
$createFolder = true;
$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-pictures.php');
include(PATH . 'templates/footer.php');

?>