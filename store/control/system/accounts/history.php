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

$headerTitleText = mc_cleanData($public_accounts[10] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('account')) . '">' . $public_accounts[8] . '</a>';
$breadcrumbs[] = $public_accounts[10];

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

// Pagination..
$pgn = array(
  'limit' => $page * HISTORY_PER_PAGE - (HISTORY_PER_PAGE),
  'per' => HISTORY_PER_PAGE
);

$countRows = $MCACC->history($loggedInUser['id'], '', array(), $pgn, 'yes');
if ($countRows > $pgn['per']) {
  if ($SETTINGS->en_modr == 'yes') {
    $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['his'] . '/{page}'));
    $pNumbers = mc_publicPageNumbers($countRows, $pgn['per'], $seolink);
  } else {
    $pNumbers = mc_publicPageNumbers($countRows, $pgn['per'], $SETTINGS->ifolder . '/?p=history&amp;next=');
  }
}

$tpl = mc_getSavant();
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('TRADE', array(
  str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
  ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
));
$tpl->assign('TEXTH', $public_accounts_history);
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
$tpl->assign('HISTORY', $MCACC->history($loggedInUser['id'], $public_accounts_history, $mcSystemPaymentMethods, $pgn));
$tpl->assign('PAGINATION', ($countRows > 0 && isset($pNumbers) ? $pNumbers : ''));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-history.tpl.php');

include(PATH . 'control/footer.php');

?>