<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-batch.php');
include(MCLANG . 'sales/sales-update.php');
include(MCLANG_REL . 'emails.php');
include(MCLANG . 'catalogue/product-manage.php');

if (isset($_POST['exportSales'])) {
  if (!empty($_POST['batch'])) {
    include(REL_PATH . 'control/classes/class.download.php');
    $DL         = new mcDownload();
    $MCSALE->dl = $DL;
    $MCSALE->exportSalesToCSV(0,$_POST['batch']);
  }
  header("Location: index.php?p=search-sales");
  exit;
}

if (isset($_POST['process']) && !empty($_POST['batch'])) {
  include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
  include(GLOBAL_PATH . 'control/gateways/class.handler.php');
  $sendM         = (isset($_POST['email']) && in_array($_POST['email'],array('yes','no')) ? $_POST['email'] : 'no');
  $GWY           = new paymentHandler();
  $GWY->settings = $SETTINGS;
  if (!empty($_POST['batch'])) {
    mc_memoryLimit();
    $orderArr = array_unique($_POST['batch']);
    include(REL_PATH . 'control/classes/class.rewrite.php');
    $MCRWR           = new mcRewrite();
    $MCRWR->settings = $SETTINGS;
    foreach ($orderArr AS $orderID) {
      // Get order info..
      $ORDER = mc_getTableData('sales', 'id', $orderID);
      // Create arrays of digits/letters..
      $a     = array_merge(range('a', 'z'), range(1, 9));
      shuffle($a);
      $append   = $a[4] . $a[23];
      // Find/replace..
      $find     = array(
        '{ORDER-NO}',
        '{DATE}',
        '{NAME}',
        '{DOWNLOADS}',
        '{ORDER}'
      );
      $replace  = array(
        mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS),
        date($SETTINGS->systemDateFormat, strtotime($ORDER->purchaseDate)),
        $ORDER->bill_1,
        ($ORDER->account > 0 ? $MCRWR->url(array('account')) : $SETTINGS->ifolder . '/?vOrder=' . $orderID . '-' . $ORDER->buyCode . $append),
        $GWY->buildProductOrder($orderID)
      );
      $textData = str_replace($find, $replace, $_POST['text']);
      // Update order..
      $id       = $MCSALE->batchUpdateOrderStatus($orderID);
      if ($sendM == 'yes') {
        $em  = array_map('trim', explode(',', $SETTINGS->addEmails));
        $ot  = array();
        if (count($em) > 1) {
          $ot = $em;
          unset($ot[0]);
        }
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $ORDER->bill_2,
          'to_name' => $ORDER->bill_1,
          'subject' => str_replace($find, $replace, $_POST['title']),
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => $textData,
          'no-footer' => 'yes',
          'add-emails' => (!empty($ot) ? implode(',',$ot) : ''),
          'language' => $SETTINGS->languagePref
        ));
      }
    }
    $MCMAIL->smtpClose();
  }
  $OK = true;
}

if (isset($_POST['delsales']) && $uDel == 'yes') {
  foreach ($_POST['batch'] AS $sID) {
    $MCSALE->deleteOrderSale($sID);
  }
  header("Location: index.php?p=sales" . (isset($_POST['ahis']) ? '&ahis=' . (int) $_POST['ahis'] : '') . "&deleted=" . count($_POST['batch']));
  exit;
}

if (isset($_POST['delsearchsales']) && $uDel == 'yes') {
  foreach ($_POST['batch'] AS $sID) {
    $MCSALE->deleteOrderSale($sID);
  }
  header("Location: index.php?p=sales-search&deleted=" . count($_POST['batch']));
  exit;
}

if (empty($_POST['batch'])) {
  header("Location: index.php?p=sales" . (isset($_POST['ahis']) ? '&ahis=' . (int) $_POST['ahis'] : ''));
  exit;
}

$pageTitle    = $msg_salesbatch2 . ': ' . $pageTitle;
$loadiBox  = true;
$createFolder = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-batch.php');
include(PATH . 'templates/footer.php');

?>