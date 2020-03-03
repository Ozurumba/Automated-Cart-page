<?php

if (!defined('PARENT') || (!isset($_GET['vodr']) && !isset($_GET['pinfo']))) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('VIEW_ACC', 1);

// Not logged in, go to log in screen..
if (empty($loggedInUser) || !isset($loggedInUser['id'])) {
  header("Location: " . $MCRWR->url(array('login')));
  exit;
}

// Load language files..
include(MCLANG . 'accounts.php');

$ACC = mc_getTableData('accounts', 'id', $loggedInUser['id'], ' AND `verified` = \'yes\'');

if (!isset($ACC->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Is account disabled?
if ($ACC->enabled == 'no') {
  include(PATH . 'control/system/accounts/disabled.php');
  exit;
}

// Payment information.
if (isset($_GET['pinfo']) && isset($_GET['pm']) && in_array($_GET['pm'], array('bank','cod','cheque','phone','account'))) {
  $SALE = mc_getTableData('sales', 'id', (int) $_GET['pinfo'], ' AND `account` = \'' . $loggedInUser['id'] . '\' AND `paymentMethod` = \'' . mc_safeSQL($_GET['pm']) . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
  if (isset($SALE->id)) {
    include(MCLANG . 'admin/sales/invoice-packingslip.php');
    $tpl = mc_getSavant();
    $tpl->assign('TEXT', array(
      $msg_invoice4,
      mc_paymentMethodName($SALE->paymentMethod),
      str_replace('{INVOICE_NO}',mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS),mc_txtParsingEngine($mcSystemPaymentMethods[$SALE->paymentMethod]['html']))
    ));
    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-payment-info.tpl.php');
    exit;
  }
  echo $errorPages['403'];
  exit;
} else {
  $SALE = mc_getTableData('sales', 'id', (int) $_GET['vodr'], ' AND `account` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
}

if (!isset($SALE->id)) {
  // Is this a wish list purchase?
  if ($SETTINGS->en_wish == 'yes') {
    $SALE = mc_getTableData('sales', 'id', (int) $_GET['vodr'], ' AND `wishlist` = \'' . $loggedInUser['id'] . '\' AND `saleConfirmation` = \'yes\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
    if (!isset($SALE->id)) {
      include(PATH . 'control/system/headers/403.php');
      exit;
    }
  } else {
    include(PATH . 'control/system/headers/403.php');
    exit;
  }
}

include(MCLANG . 'admin/sales/invoice-packingslip.php');
include(MCLANG . 'admin/versions/2.1.php');
include(MCLANG . 'admin/versions/3.0.php');
include(PATH . 'control/classes/class.order.php');
$MCORDER           = new mcOrder();
$MCORDER->settings = $SETTINGS;
$MCORDER->order    = $SALE;
$MCORDER->products = $MCPROD;
$MCORDER->rwr      = $MCRWR;
$MCORDER->htmltags = $mc_mailHTMLTags;

$headerTitleText = mc_cleanData($public_accounts_view_order[0] . ' #' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('account')) . '">' . $public_accounts[8] . '</a>';
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('history')) . '">' . $public_accounts[10] . '</a>';
$breadcrumbs[] = $public_accounts_view_order[0];
$breadcrumbs[] = '#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS);

// Load JS
$loadJS['mc-acc-ops']   = 'load';

// Left menu boxes..
// Set boxes to skip..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['popular'] = true;
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['rss']     = true;
$skipMenuBoxes['tweets']  = true;
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('TRADE', array(
  str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
  ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
));
$tpl->assign('TEXTV', $public_accounts_view_order);
$tpl->assign('URL', array(
  $MCRWR->url(array('account')),
  $MCRWR->url(array('profile')),
  $MCRWR->url(array('history')),
  $MCRWR->url(array($MCRWR->config['slugs']['wst'])),
  $MCRWR->url(array($MCRWR->config['slugs']['ssc'])),
  $MCRWR->url(array('create')),
  $MCRWR->url(array('logout')),
  $MCRWR->url(array('close'))
));

// Viewing address permissions..
$showPDF = 'yes';
if ($SETTINGS->en_wish == 'yes' && $SALE->wishlist > 0) {
  if ($SALE->wishlist ==  $loggedInUser['id']) {
    $billAddr = $msg_view_order[2];
    $shipAddr = $MCORDER->address('ship');
    $showPDF = 'no';
  } else {
    $billAddr = $MCORDER->address('bill');
    $shipAddr = $msg_view_order[3];
    $MCORDER->perms['show-dl'] = 'no';
  }
} else {
  $billAddr = $MCORDER->address('bill');
  $shipAddr = $MCORDER->address('ship');
}

// Status permissions account..
$MCORDER->perms['account'] = $loggedInUser['id'];

$tpl->assign('SHOW_PDF', $showPDF);
$tpl->assign('IS_WISH', ($SETTINGS->en_wish == 'yes' && $SALE->wishlist > 0 ? 'yes' : 'no'));
$tpl->assign('ORDER', (array) $SALE);
$totals = $MCORDER->totals();
$tpl->assign('BUILD', array(
  'bill-address' => $billAddr,
  'ship-address' => $shipAddr,
  'shipped' => $MCORDER->shipped(),
  'downloads' => $MCORDER->downloads(),
  'gift-certs' => $MCORDER->gift(),
  'info' => $MCORDER->info(),
  'totals' => $totals[0],
  'statuses' => $MCORDER->statuses()
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-view-order.tpl.php');

include(PATH . 'control/footer.php');

?>