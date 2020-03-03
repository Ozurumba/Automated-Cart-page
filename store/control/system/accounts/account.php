<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

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

$headerTitleText = mc_cleanData($public_accounts[8] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs = array(
  $public_accounts[8]
);

// Load JS
$loadJS['mc-acc-ops']   = 'load';

// Has an account message expired?
if ($loggedInUser['message'] && $loggedInUser['messageexp'] != '0000-00-00' && $loggedInUser['messageexp'] <= date('Y-m-d')) {
  $MCACC->clearMsg($loggedInUser['id']);
  $loggedInUser['message'] = '';
}

// Left menu boxes..
// Set boxes to skip..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['popular'] = true;
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['rss']     = true;
$skipMenuBoxes['tweets']  = true;
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$showHis = (ACC_DASH_LATEST_COUNT > 0 ? ACC_DASH_LATEST_COUNT : 5);

$tpl = mc_getSavant();
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('TRADE', array(
  str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
  ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
));
$tpl->assign('WELCOME', str_replace(array('{name}','{count}'),array(mc_safeHTML($loggedInUser['name']),$showHis),$public_accounts_dashboard[0]));
$tpl->assign('TEXTD', $public_accounts_dashboard);
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
$tpl->assign('LATEST_ORDERS', $MCACC->history($loggedInUser['id'], $public_accounts_history, $mcSystemPaymentMethods, array('dash' => $showHis)));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-dashboard.tpl.php');

include(PATH . 'control/footer.php');

?>