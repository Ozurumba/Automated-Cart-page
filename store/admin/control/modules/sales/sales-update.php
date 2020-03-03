<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-update.php');
include(MCLANG_REL . 'emails.php');
include(MCLANG . 'catalogue/product-manage.php');

// Status search
if (isset($_GET['search'])) {
  $statuses = $MCSALE->searchStatuses();
  echo $JSON->encode($statuses);
  exit;
}

// Status text management..
if (isset($_GET['loadEditText']) && ctype_digit($_GET['loadEditText'])) {
  $S = mc_getTableData('statuses', 'id', $_GET['loadEditText']);
  echo $JSON->encode(array(
    mc_cleanData($S->statusNotes)
  ));
  exit;
}

if (isset($_GET['statnotes'])) {
  if (isset($_POST['notes'])) {
    $MCSALE->editSaleStatus($msg_salesupdate26);
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/sale-status-edit.php');
  exit;
}

// Sales status management..
if (isset($_GET['sstatus'])) {
  switch($_GET['sstatus']) {
    case 'load':
      $S = mc_getTableData('status_text', 'id', (isset($_GET['id']) ? (int) $_GET['id'] : '0'));
      echo $JSON->encode(array(
        'subject' => (isset($S->statTitle) ? mc_cleanData($S->statTitle) : ''),
        'text' => (isset($S->statText) ? mc_cleanData($S->statText) : '')
      ));
      break;
    case 'add':
      $s = $MCSALE->addStatusText();
      echo $JSON->encode(array(
        'message' => ($s != 'fail' && $s ? ($s == 'add' ? 'OK' : $msg_salesupdate26) : '')
      ));
      break;
  }
  exit;
}

if ($cmd == 'sales-statuses') {
  if (isset($_POST['process'])) {
    $MCSALE->updateStatusText();
    $OK = true;
  }

  if (isset($_GET['del'])) {
    $MCSALE->deleteStatusText();
    header("Location: ?p=sales-statuses&deldone=yes&sale=". ($_GET['sale'] == 'batch' ? 'batch' : (int) $_GET['sale']));
    exit;
  }
  $pageTitle    = $msg_salesupdate25 . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sales-statuses-edit.php');
  include(PATH . 'templates/footer.php');
  exit;
}

if (isset($_GET['print']) && ctype_digit($_GET['print'])) {
  include(PATH . 'templates/windows/statuses-print-friendly.php');
  exit;
}

if (isset($_POST['process'])) {
  include(GLOBAL_PATH . 'control/gateways/class.handler.php');
  include(GLOBAL_PATH . 'control/classes/class.rewrite.php');
  $sendM           = (isset($_POST['email']) && in_array($_POST['email'],array('yes','no')) ? $_POST['email'] : 'no');
  $MCRWR           = new mcRewrite();
  $GWY             = new paymentHandler();
  $GWY->settings   = $SETTINGS;
  $MCRWR->settings = $SETTINGS;
  // Create arrays of digits/letters..
  $a             = array_merge(range('a', 'z'), range(1, 9));
  shuffle($a);
  $append        = $a[4] . $a[23];
  // Find/replace..
  $find          = array(
    '{DOWNLOADS}',
    '{ORDER}'
  );
  $replace       = array(
    $MCRWR->url(array('account')),
    $GWY->buildProductOrder($_GET['sale'])
  );
  $_POST['text'] = str_replace($find, $replace, $_POST['text']);
  // Update order..
  $id            = $MCSALE->updateOrderStatus();
  // Add attachments..
  $files         = $MCSALE->addStatusAttachments($id);
  if ($sendM == 'yes') {
    include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
    // Add attachments..
    if (!empty($files)) {
      $MCMAIL->attachments = $files;
    }
    if ($_POST['buyer'] && $_POST['bill_2']) {
      $copy = array();
      if ($_POST['copy_email'] != '') {
        $copy = mc_getCopyAddresses($_POST['copy_email']);
      }
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $_POST['bill_2'],
        'to_name' => $_POST['buyer'],
        'subject' => $_POST['title'],
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $_POST['text'],
        'no-footer' => 'yes',
        'add-emails' => (!empty($copy) ? implode(',', $copy) : ''),
        'language' => $SETTINGS->languagePref
      ));
      $MCMAIL->smtpClose();
    }
  }
  // Clear attachments..
  if (isset($_POST['save']) && $_POST['save'] == 'no') {
    $MCSALE->clearAllAttachments($files);
  }
  $OK = true;
}

if (isset($_GET['delete']) && $uDel == 'yes') {
  $cnt = $MCSALE->deleteOrderStatus();
  $DEL = true;
}

// Create folder..
if (isset($_GET['newfldr'])) {
  // Clean folder name..
  $folder = preg_replace('/[^a-zA-Z0-9-_]+/', '', $_GET['newfldr']);
  // Create folder..
  if ($folder) {
    $status = $MCSALE->createAttachmentsFolder($folder);
  }
  echo $JSON->encode(array(
    'message' => 'create-folder',
    'status' => $status,
    'error' => $msg_javascript153,
    'ok' => $msg_javascript154,
    'folder' => ($folder ? $folder : 'none')
  ));
  exit;
}

$pageTitle    = $msg_salesupdate2 . ': ' . $pageTitle;
$loadiBox  = true;
$createFolder = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-update.php');
include(PATH . 'templates/footer.php');

?>