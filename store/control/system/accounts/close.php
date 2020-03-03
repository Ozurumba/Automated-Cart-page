<?php

if (!defined('PARENT') || $SETTINGS->en_close == 'no') {
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

$headerTitleText = mc_cleanData($public_accounts[15] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('account')) . '">' . $public_accounts[8] . '</a>';
$breadcrumbs[] = $public_accounts[15];

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
$tpl->assign('TEXTC', $public_accounts_close);
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

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-close.tpl.php');

include(PATH . 'control/footer.php');

?>