<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/sales-overview.php');
include(MCLANG . 'sales/sales-search.php');
include(MCLANG . 'sales/sales-update.php');
include(MCLANG . 'sales/sales-export.php');
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'catalogue/product-manage.php');

$searchFilter = '';

if (isset($_GET['process'])) {
  $_GET = mc_safeImport($_GET);
  if (isset($_GET['invoice']) && $_GET['invoice'] && ctype_digit($_GET['invoice'])) {
    $searchFilter = 'AND `invoiceNo` = \'' . ltrim(mc_saleInvoiceNumber(strtolower($_GET['invoice']), $SETTINGS), '0') . '\' ';
  }
  if (isset($_GET['keys']) && $_GET['keys']) {
    $keyfilter     = 'AND (';
    $oFields       = array('bill_1','bill_2','bill_3','bill_4','bill_5','bill_6','bill_7','bill_8','saleNotes','shipType','trackcode','affcode');
    for ($i=0; $i<count($oFields); $i++) {
      $keyfilter .= ($i ? ' OR ' : '')."`" . $oFields[$i] . "` LIKE '%" . $_GET['keys'] . "%'";
    }
    $keyfilter    .= ')';
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . $keyfilter;
  }
  if (isset($_GET['code']) && $_GET['code']) {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND LOWER(`couponCode`) = \'' . strtolower($_GET['code']) . '\' OR LOWER(`gatewayID`) = \'' . strtolower($_GET['code']) . '\' ';
  }
  if (isset($_GET['from']) && isset($_GET['to'])) {
    if (mc_checkValidDate($_GET['from']) != '0000-00-00' && mc_checkValidDate($_GET['to']) != '0000-00-00') {
      $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND `purchaseDate` BETWEEN \'' . mc_convertCalToSQLFormat($_GET['from'], $SETTINGS) . '\' AND \'' . mc_convertCalToSQLFormat($_GET['to'], $SETTINGS) . '\' ';
    }
  }
  if (isset($_GET['status']) && $_GET['status'] != 'none') {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND LOWER(`paymentStatus`) = \'' . strtolower($_GET['status']) . '\' ';
  }
  if (isset($_GET['method']) && $_GET['method'] != 'none') {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND LOWER(`paymentMethod`) = \'' . strtolower($_GET['method']) . '\' ';
  }
  if (isset($_GET['country']) && (int) $_GET['country'] > 0) {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND `bill_9` = \'' . (int) $_GET['country'] . '\'';
  }
  if (isset($_GET['wish']) && in_array($_GET['wish'], array('yes','no'))) {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND `wishlist` > \'0\'';
  }
  if (isset($_GET['type']) && in_array($_GET['type'], array('personal','trade'))) {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND `type` = \'' . $_GET['type'] . '\'';
  }
  if (isset($_GET['pfm']) && in_array($_GET['pfm'], array('desktop','mobile','tablet'))) {
    $searchFilter .= ($searchFilter ? mc_defineNewline() : '') . 'AND `platform` = \'' . $_GET['pfm'] . '\'';
  }
  if ($searchFilter) {
    $SEARCH = true;
  } else {
    header("Location: index.php?p=sales-search");
    exit;
  }
}

$pageTitle    = mc_cleanDataEntVars($msg_javascript111) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-search.php');
include(PATH . 'templates/footer.php');

?>