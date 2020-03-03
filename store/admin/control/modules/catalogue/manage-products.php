<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-attributes.php');

// Buy now code..
if (isset($_GET['buynow'])) {
  include(PATH . 'templates/windows/buy-now-code.php');
  exit;
}

// Update stock..
if (isset($_GET['stockUpdate'])) {
  $stock = (int) $_GET['stock'];
  $id    = (int) $_GET['stockUpdate'];
  $MCPROD->updateSingleProductStock($id, $stock);
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Notes..
if (isset($_GET['notes'])) {
  // Update notes..
  if (isset($_POST['notes'])) {
    $MCPROD->updateProductNotes();
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/product-notes.php');
  exit;
}

// Enable/disable..
if (isset($_GET['changeStatus']) && ctype_digit($_GET['changeStatus'])) {
  $P = mc_getTableData('products', 'id', $_GET['changeStatus']);
  switch($P->pEnable) {
    case 'yes':
      $MCPROD->changeProductStatus('no');
      echo $JSON->encode(array(
        'status' => $msg_productmanage23,
        'newstatus' => $msg_productmanage40 . ' ' . $msg_productmanage23
      ));
      break;
    case 'no':
      $MCPROD->changeProductStatus('yes');
      echo $JSON->encode(array(
        'status' => $msg_productmanage22,
        'newstatus' => $msg_productmanage40 . ' ' . $msg_productmanage22
      ));
      break;
  }
  exit;
}

if (isset($_GET['delete'])) {
  $cnt = $MCPROD->deleteProduct();
  $OK  = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript27) . ': ' . $pageTitle;
$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-manage.php');
include(PATH . 'templates/footer.php');

?>