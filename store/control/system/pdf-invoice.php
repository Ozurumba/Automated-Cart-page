<?php

/* PDF INVOICE CREATION
----------------------------------------*/

define('MC_PDF', 1);
define('BATCH_FILE_NME', 'invoice-pdfs.zip');

include(MCLANG . 'pdf.php');

if ($SETTINGS->pdf == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

$PDF_CFG = mc_getTableData('pdf', 'id', '1');

include(GLOBAL_PATH . 'control/tcpdf/tcpdf_config.php');
include(GLOBAL_PATH . 'control/tcpdf/tcpdf.php');
include(GLOBAL_PATH . 'control/tcpdf/tcpdf_custom.php');

include(MCLANG . 'admin/sales/invoice-packingslip.php');
include(MCLANG . 'admin/versions/2.1.php');
include(MCLANG . 'admin/versions/3.0.php');
include(PATH . 'control/classes/class.order.php');

$MCORDER           = new mcOrder();
$MCORDER->settings = $SETTINGS;
$MCORDER->products = $MCPROD;

global $l;
$l = array(
  'a_meta_charset' => $PDF_CFG->meta,
  'a_meta_dir' => $PDF_CFG->dir,
  'a_meta_language' => 'en'
);

$CD = (isset($_GET['pdfg']) ? explode('-', $_GET['pdfg']) : array('fail','fail'));

// Permissions..
if (!isset($_GET['admin-pdf-loader'])) {
  if (isset($_GET['pdf'])) {
    if (!isset($loggedInUser['id'])) {
      header("Location: " . $MCRWR->url(array('account')));
      exit;
    }
    $SALE = mc_getTableData('sales', 'id', (int) $_GET['pdf'], ' AND `account` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
    if (!isset($SALE->id) && $SETTINGS->en_wish == 'yes') {
      $SALE = mc_getTableData('sales', 'id', (int) $_GET['pdf'], ' AND `wishlist` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
    }
  } else if (isset($CD[0],$CD[1]) && (int) $CD[0] > 0 && ctype_alnum($CD[1]) && $CD[1] != 'fail') {
    $ID      = (int) $CD[0];
    $buyCode = $CD[1];
    $SALE    = mc_getTableData('sales', 'id', $ID, ' AND `buyCode` = \'' . mc_safeSQL($buyCode) . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
  } else {
    header("Location: " . $MCRWR->url(array('account')));
    exit;
  }
} else {
  switch($_GET['pdf']) {
    case 'batch':
      if (!isset($_SESSION['batchPDFIDs'])) {
        include(PATH . 'control/system/headers/403.php');
      }
      define('PDF_BATCH_OPS', 1);
      break;
    default:
      $CD      = (isset($_GET['pdf']) ? explode('-', $_GET['pdf']) : array('fail','fail'));
      $ID      = (int) $CD[0];
      $buyCode = (isset($CD[1]) && ctype_alnum($CD[1]) ? $CD[1] : 'fail');
      $SALE    = mc_getTableData('sales', 'id', (int) $ID, 'AND `buyCode` = \'' . $buyCode . '\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
      break;
  }
}

// Processor for single PDF..
if (!defined('PDF_BATCH_OPS')) {

  if (!isset($SALE->id)) {
    include(PATH . 'control/system/headers/403.php');
    exit;
  }

  $MCORDER->order = $SALE;
  $MCORDER->incsale = (in_array($SALE->paymentStatus, array('','pending')) && isset($_GET['admin-pdf-loader']) ? 'yes' : 'no');

  // Create PDF..
  $PDF = new mcTCPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, $PDF_CFG->meta, false);

  $PDF->SetCreator(PDF_CREATOR);
  $PDF->SetAuthor(PDF_AUTHOR);
  $PDF->SetTitle($SETTINGS->website . ' - ' . $invoice_pdf_opts[0]);
  $PDF->SetSubject($invoice_pdf_opts[0] . '#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS));

  $PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $PDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
  $PDF->SetFooterMargin(PDF_MARGIN_FOOTER);
  $PDF->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);

  $PDF->setLanguageArray($l);
  $PDF->setFontSubsetting(true);
  $PDF->SetFont($PDF_CFG->font, '', PDF_FONT_SIZE, '', true);
  $PDF->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $PDF->setPrintHeader(false);

  $PDF->AddPage();

  if (DRAW_OUTSIDE_BORDER) {
    $PDF->SetLineStyle(($SALE->type == 'trade' ? $drawOutsideBorderTradeCfg : $drawOutsideBorderCfg));
    $PDF->Line(0,0,$PDF->getPageWidth(),0);
    $PDF->Line($PDF->getPageWidth(),0,$PDF->getPageWidth(),$PDF->getPageHeight());
    $PDF->Line(0,$PDF->getPageHeight(),$PDF->getPageWidth(),$PDF->getPageHeight());
    $PDF->Line(0,0,0,$PDF->getPageHeight());
  }

  // Viewing address permissions..
  if (!isset($_GET['admin-pdf-loader']) && $SETTINGS->en_wish == 'yes' && $SALE->wishlist > 0) {
    if (isset($loggedInUser['id']) && $SALE->wishlist ==  $loggedInUser['id']) {
      $billAddr = $msg_view_order[2];
      $shipAddr = $MCORDER->address('ship');
    } else {
      $billAddr = $MCORDER->address('bill');
      $shipAddr = $msg_view_order[3];
    }
  } else {
    $billAddr = $MCORDER->address('bill');
    $shipAddr = $MCORDER->address('ship');
  }

  $totals = $MCORDER->totals('pdf-invoice/' . $SALE->type . '/total.htm', array('subtotal'));
  $datalang = array(
    '{COMPANY_NAME}' => ($PDF_CFG->company ? mc_cleanData($PDF_CFG->company) : $SETTINGS->website),
    '{COMPANY_ADDRESS}' => mc_NL2BR(mc_cleanData($PDF_CFG->address)),
    '{TXT_0}' => $invoice_pdf[0],
    '{TXT_1}' => $invoice_pdf[1],
    '{DATE}' => $SALE->pdate,
    '{TXT_2}' => $invoice_pdf[2],
    '{INV_NO}' => mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS),
    '{TXT_3}' => $invoice_pdf[3],
    '{GATEWAY}' => mc_paymentMethodName($SALE->paymentMethod),
    '{TXT_4}' => $invoice_pdf[4],
    '{BILL_ADDR}' => $billAddr,
    '{TXT_5}' => $invoice_pdf[5],
    '{TXT_6}' => $invoice_pdf[6],
    '{SHIP_ADDR}' => $shipAddr,
    '{TXT_7}' => $invoice_pdf[7],
    '{TXT_8}' => $invoice_pdf[8],
    '{TXT_9}' => $invoice_pdf[9],
    '{TXT_10}' => $invoice_pdf[10],
    '{ITEMS}' => $MCORDER->shipped(array(
        'pdf-invoice/' . $SALE->type . '/item.htm',
        'pdf-invoice/' . $SALE->type . '/attributes.htm',
        'pdf-invoice/' . $SALE->type . '/personalised.htm'
      ), '<br>', true) . $MCORDER->downloads(array(
        'pdf-invoice/' . $SALE->type . '/item.htm',
      )) . $MCORDER->gift(array(
        'pdf-invoice/' . $SALE->type . '/item.htm'
      )
    ),
    '{TXT_11}' => $invoice_pdf[11],
    '{ROWSPAN}' => ($totals[1] + 1),
    '{SHIP_METHOD}' => ($SALE->setShipRateID > 0 ? mc_getShippingService(mc_getShippingServiceFromRate($SALE->setShipRateID)) : 'N/A'),
    '{TXT_12}' => $invoice_pdf[12],
    '{SUB_TOTAL}' => mc_currencyFormat(mc_formatPrice($SALE->subTotal,true)),
    '{TOTALS}' => $totals[0],
    '{TXT_13}' => $invoice_pdf[13],
    '{NOTES}' => mc_NL2BR($SALE->saleNotes),
    '{TXT_14}' => ($SALE->paymentStatus == 'pending' ? $invoice_pdf[16] : $invoice_pdf[14]),
    '{TXT_15}' => $invoice_pdf[15] . $SETTINGS->baseCurrency
  );

  $PDF->writeHTML(
    strtr(file_get_contents(THEME_FOLDER . '/html/pdf-invoice/' . $SALE->type . '/wrapper.htm'), $datalang),
    true,
    0,
    true,
    0
  );

  $PDF->Output('invoice_' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . (PURCHASE_DATE_IN_FILE_NAME ? '_' . $SALE->purchaseDate : '') . '.pdf', 'D');

} else {

  // No timeout..
  mc_memAllocation();

  // Batch / Admin ONLY..
  if (!isset($_SESSION['batchPDFIDs'])) {
    include(REL_PATH . 'control/modules/header/403.php');
  }
  if (!is_writeable(PATH . $SETTINGS->adminFolderName . '/import')) {
    die('Admin import folder must exist and be writeable for batch PDFs');
  }
  if (file_exists(PATH . $SETTINGS->adminFolderName . '/import/' . BATCH_FILE_NME)) {
    @unlink(PATH . $SETTINGS->adminFolderName . '/import/' . BATCH_FILE_NME);
  }
  if (class_exists('ZipArchive')) {
    $del    = array();
    $ZIP    = new ZipArchive();
    $ZFILE  = $ZIP->open(PATH . $SETTINGS->adminFolderName . '/import/' . BATCH_FILE_NME, ZipArchive::CREATE);
    if ($ZFILE) {
      $q_sl = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `pdate`
              FROM `" . DB_PREFIX . "sales`
              WHERE `id` IN(" . mc_safeSQL(implode(',',$_SESSION['batchPDFIDs'])) . ")
              ORDER BY `id`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($SALE = mysqli_fetch_object($q_sl)) {
        // Pass sale to order builder..
        $MCORDER->order   = $SALE;
        $MCORDER->incsale = (in_array($SALE->paymentStatus, array('','pending')) ? 'yes' : 'no');

        // Create PDF..
        $PDF = new mcTCPPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, $PDF_CFG->meta, false);

        $PDF->SetCreator(PDF_CREATOR);
        $PDF->SetAuthor(PDF_AUTHOR);
        $PDF->SetTitle($SETTINGS->website . ' - ' . $invoice_pdf_opts[0]);
        $PDF->SetSubject($invoice_pdf_opts[0] . '#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS));

        $PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $PDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $PDF->SetHeaderMargin(PDF_MARGIN_HEADER);
        $PDF->SetFooterMargin(PDF_MARGIN_FOOTER);
        $PDF->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $PDF->setLanguageArray($l);
        $PDF->setFontSubsetting(true);
        $PDF->SetFont($PDF_CFG->font, '', PDF_FONT_SIZE, '', true);
        $PDF->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $PDF->setPrintHeader(false);

        $PDF->AddPage();

        if (DRAW_OUTSIDE_BORDER) {
          $PDF->SetLineStyle(($SALE->type == 'trade' ? $drawOutsideBorderTradeCfg : $drawOutsideBorderCfg));
          $PDF->Line(0,0,$PDF->getPageWidth(),0);
          $PDF->Line($PDF->getPageWidth(),0,$PDF->getPageWidth(),$PDF->getPageHeight());
          $PDF->Line(0,$PDF->getPageHeight(),$PDF->getPageWidth(),$PDF->getPageHeight());
          $PDF->Line(0,0,0,$PDF->getPageHeight());
        }

        $totals = $MCORDER->totals('pdf-invoice/' . $SALE->type . '/total.htm', array('subtotal'));
        $datalang = array(
          '{COMPANY_NAME}' => ($PDF_CFG->company ? mc_cleanData($PDF_CFG->company) : $SETTINGS->website),
          '{COMPANY_ADDRESS}' => mc_NL2BR(mc_cleanData($PDF_CFG->address)),
          '{TXT_0}' => $invoice_pdf[0],
          '{TXT_1}' => $invoice_pdf[1],
          '{DATE}' => $SALE->pdate,
          '{TXT_2}' => $invoice_pdf[2],
          '{INV_NO}' => mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS),
          '{TXT_3}' => $invoice_pdf[3],
          '{GATEWAY}' => mc_paymentMethodName($SALE->paymentMethod),
          '{TXT_4}' => $invoice_pdf[4],
          '{BILL_ADDR}' => $MCORDER->address('bill'),
          '{TXT_5}' => $invoice_pdf[5],
          '{TXT_6}' => $invoice_pdf[6],
          '{SHIP_ADDR}' => $MCORDER->address('ship'),
          '{TXT_7}' => $invoice_pdf[7],
          '{TXT_8}' => $invoice_pdf[8],
          '{TXT_9}' => $invoice_pdf[9],
          '{TXT_10}' => $invoice_pdf[10],
          '{ITEMS}' => $MCORDER->shipped(array(
              'pdf-invoice/' . $SALE->type . '/item.htm',
              'pdf-invoice/' . $SALE->type . '/attributes.htm',
              'pdf-invoice/' . $SALE->type . '/personalised.htm'
            ), '<br>', true) . $MCORDER->downloads(array(
              'pdf-invoice/' . $SALE->type . '/item.htm',
            )) . $MCORDER->gift(array(
              'pdf-invoice/' . $SALE->type . '/item.htm'
            )
          ),
          '{TXT_11}' => $invoice_pdf[11],
          '{ROWSPAN}' => ($totals[1] + 1),
          '{SHIP_METHOD}' => ($SALE->setShipRateID > 0 ? mc_getShippingService(mc_getShippingServiceFromRate($SALE->setShipRateID)) : 'N/A'),
          '{TXT_12}' => $invoice_pdf[12],
          '{SUB_TOTAL}' => mc_currencyFormat(mc_formatPrice($SALE->subTotal,true)),
          '{TOTALS}' => $totals[0],
          '{TXT_13}' => $invoice_pdf[13],
          '{NOTES}' => mc_NL2BR($SALE->saleNotes),
          '{TXT_14}' => $invoice_pdf[14],
          '{TXT_15}' => $invoice_pdf[15] . $SETTINGS->baseCurrency
        );

        $PDF->writeHTML(
          strtr(file_get_contents(THEME_FOLDER . '/html/pdf-invoice/' . $SALE->type . '/wrapper.htm'), $datalang),
          true,
          0,
          true,
          0
        );

        $fn = PATH . $SETTINGS->adminFolderName . '/import/invoice_' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . (PURCHASE_DATE_IN_FILE_NAME ? '_' . $SALE->purchaseDate : '') . '.pdf';
        $PDF->Output($fn, 'F');
        if (file_exists($fn)) {
          $ZIP->addFile($fn, basename($fn));
          $del[] = $fn;
        }
      }
      $ZIP->close();
      if (!empty($del)) {
        foreach ($del AS $d) {
          @unlink($d);
        }
      }
      include(PATH . 'control/classes/class.download.php');
      $DL = new mcDownload();
      $DL->dl(PATH . $SETTINGS->adminFolderName . '/import/' . BATCH_FILE_NME, 'application/zip', 'yes');
    } else {
      die('Zip file could not be created, check server error log!');
    }
  } else {
    die('<a href="http://php.net/manual/en/class.ziparchive.php">ZipArchive</a> PHP class not available on server. Install, reboot server and try again.');
  }

}

?>