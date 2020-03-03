<?php

if (!defined('PARENT')) {echo 'hi';
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('VIEW_GUEST', 1);

// Load language files..
include(MCLANG . 'view-order.php');
include(MCLANG . 'accounts.php');

// Check order code..
$split = (isset($_GET['vOrder']) ? explode('-', $_GET['vOrder']) : array());
$id    = (isset($split[0]) && (int) $split[0] > 0 ? $split[0] : 'inv');
$code  = (isset($split[1]) && ctype_alnum($split[1]) ? $split[1] : 'inv');

// If either 0, invalid..
if ($id == 'inv' || $code == '0') {
  if (isset($_GET['pdl'])) {
    $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));
    echo $MCJSON->encode($arr);
    exit;
  }
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Get order data..
// Use substring for legacy versions of Maian Cart..
$ORDER = mc_getTableData('sales', 'SUBSTRING(`buyCode`, 1, 20)', substr($code, 0, 20), 'AND `id` = \'' . $id . '\'', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');

// Is this page valid..
if (!isset($ORDER->id) || (isset($ORDER->saleConfirmation) && $ORDER->saleConfirmation == 'no')) {
  if (isset($_GET['pdl'])) {
    $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_global[3]));
    echo $MCJSON->encode($arr);
    exit;
  }
  header("Location: " . $MCRWR->url(array('order-invalid')));
  exit;
}

// Is this being viewed via admin?
if (isset($_GET['token'])) {
  if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_saleToken']) && $_SESSION[mc_encrypt(SECRET_KEY) . '_saleToken'] == $ORDER->id) {
    define('ADMIN_ACCESS_ALLOWED', 1);
  }
}


// Product download..
if (isset($_GET['pdl'])) {
  define('GUEST_OPS', 1);
  include(PATH . 'control/system/guest-ops.php');
  exit;
}

// PDF..
if (isset($_GET['pdfshow'])) {
  $arr = array(
    'msg' => 'ok',
    'rdr' => 'index.php?pdfg=' . $ORDER->id . '-' . $ORDER->buyCode
  );
  echo $MCJSON->encode($arr);
  exit;
}

include(MCLANG . 'admin/sales/invoice-packingslip.php');
include(MCLANG . 'admin/versions/2.1.php');
include(MCLANG . 'admin/versions/3.0.php');
include(PATH . 'control/classes/class.order.php');
$MCORDER                 = new mcOrder();
$MCORDER->settings       = $SETTINGS;
$MCORDER->order          = $ORDER;
$MCORDER->products       = $MCPROD;
$MCORDER->perms['guest'] = 'yes';
$MCORDER->rwr            = $MCRWR;

// If order has a none gateway payment option and is pending, do not load screen..
// If order is cancelled or refunded, prevent access too..
if ((in_array($ORDER->paymentMethod, array(
  'cheque',
  'phone',
  'cod',
  'bank'
)) && $ORDER->paymentStatus == 'pending') || $ORDER->paymentStatus == 'cancelled' || $ORDER->paymentStatus == 'refunded') {
  header("Location: " . $MCRWR->url(array('status-err')));
  exit;
}

// Check to see if buyer has account or are already logged in..
if (!defined('ADMIN_ACCESS_ALLOWED')) {
  if ($ORDER->account > 0 || isset($loggedInUser['id'])) {

    $ACC = mc_getTableData('accounts', 'id', (isset($loggedInUser['id']) ? $loggedInUser['id'] : $ORDER->account));

    if (isset($ACC->id) && $ACC->verified == 'yes' && $ACC->enabled == 'yes') {

      // If already logged in we can direct to account order page..
      if (isset($loggedInUser['id'])) {

        $url = $MCRWR->url(array(
          $MCRWR->config['slugs']['vdr'] . '/' . $ORDER->id,
          'vodr=' . $ORDER->id
        ));
        header("Location: " . $url);

      // If not logged in, just show message instructing buyer to log in..
      } else {

        $_SESSION['login-redirect'] = $MCRWR->url(array(
          $MCRWR->config['slugs']['vdr'] . '/' . $ORDER->id,
          'vodr=' . $ORDER->id
        ));
        header("Location: " . $MCRWR->url(array('acc-exists')));

      }
      exit;

    }

  }
}

$headerTitleText = mc_cleanData($msg_public_view16 . ': ' . $headerTitleText);
$breadcrumbs[]   = $msg_view_order[1];
$breadcrumbs[]   = $msg_public_view21;
$breadcrumbs[]   = '#' . mc_saleInvoiceNumber($ORDER->invoiceNo, $SETTINGS);

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
$tpl->assign('TEXTV', $public_accounts_view_order);
$tpl->assign('URL', array(
  $MCRWR->url(array('account')),
  $MCRWR->url(array('profile')),
  $MCRWR->url(array('history')),
  $MCRWR->url(array('wishlist')),
  $MCRWR->url(array('saved-searches')),
  $MCRWR->url(array('create')),
  $MCRWR->url(array('logout'))
));

// Viewing address permissions..
$showPDF = 'yes';
if ($SETTINGS->en_wish == 'yes' && $ORDER->wishlist > 0) {
  $billAddr = $MCORDER->address('bill');
  $shipAddr = $msg_view_order[3];
  $showPDF = 'no';
  $MCORDER->perms['show-dl'] = 'no';
} else {
  $billAddr = $MCORDER->address('bill');
  $shipAddr = $MCORDER->address('ship');
}

$tpl->assign('SHOW_PDF', $showPDF);
$tpl->assign('ORDER', (array) $ORDER);
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

$tpl->display(THEME_FOLDER . '/guest-view-order.tpl.php');

include(PATH . 'control/footer.php');

?>