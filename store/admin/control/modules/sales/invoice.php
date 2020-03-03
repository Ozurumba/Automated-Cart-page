<?php

if (!defined('PARENT') || !isset($_GET['sale'])) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/invoice-packingslip.php');

$SALE = mc_getTableData('sales', 'id', mc_digitSan($_GET['sale']), '', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
if ($cmd == 'invoice' && isset($SALE->id)) {
  $ZONE  = mc_getTableData('zone_areas', 'id', $SALE->shipSetArea);
  $pageTitle = $msg_invoice . ': #' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . ': ' . $pageTitle;
  include(PATH . 'templates/windows/sale-invoice.php');
  exit;
}
if ($cmd == 'packing-slip' && isset($SALE->id)) {
  $ZONE  = mc_getTableData('zone_areas', 'id', $SALE->shipSetArea);
  $pageTitle = $msg_invoice15 . ': #' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . ': ' . $pageTitle;
  include(PATH . 'templates/windows/sale-packing-slip.php');
  exit;
}

echo $msg_sales12;

?>