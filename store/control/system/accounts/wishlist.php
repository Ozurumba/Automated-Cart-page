<?php

if (!defined('PARENT') || $SETTINGS->en_wish == 'no') {
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

// Is account disabed?
if ($ACC->enabled == 'no') {
  include(PATH . 'control/system/accounts/disabled.php');
  exit;
}

if ($_GET['p'] == 'code-help') {
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['wls'] . '/' . md5($ACC->id . $ACC->email) . '',
    'wls=' . md5($ACC->id . $ACC->email)
  ));
  $tpl = mc_getSavant();
  $tpl->assign('ADATA', (array) $ACC);
  $tpl->assign('TXT', array(
    $public_accounts_wish
  ));
  $tpl->assign('CODE', array(
    mc_safeHTML(str_replace(array('{text}','{url}'),array(str_replace('{website}',$SETTINGS->website,$public_accounts_wish[5]),$url),$public_accounts_wish[3])),
    mc_safeHTML(str_replace(array('{text}','{url}'),array(str_replace('{website}',$SETTINGS->website,$public_accounts_wish[5]),$url),$public_accounts_wish[4]))
  ));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/account-code-help.tpl.php');
  exit;
}

$headerTitleText = mc_cleanData($public_accounts[11] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('account')) . '">' . $public_accounts[8] . '</a>';
$breadcrumbs[] = $public_accounts[11];

// Load JS
$loadJS['mc-acc-ops']   = 'load';
$loadJS['swipe']        = 'load';

// Left menu boxes..
// Set boxes to skip..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['popular'] = true;
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['rss']     = true;
$skipMenuBoxes['tweets']  = true;
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$urls = array(
  $MCRWR->url(array('code-help')),
  $MCRWR->url(array(
    $MCRWR->config['slugs']['wls'] . '/' . md5($ACC->id . $ACC->email) . '',
    'wls=' . md5($ACC->id . $ACC->email)
  ))
);

// Pagination..
$pgn = array(
  'limit' => $page * WISHLIST_PER_PAGE - (WISHLIST_PER_PAGE),
  'per' => WISHLIST_PER_PAGE
);

$countRows = $MCACC->wishlist($loggedInUser['id'], '', $pgn, 'yes');
if ($countRows > $pgn['per']) {
  if ($SETTINGS->en_modr == 'yes') {
    $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['wst'] . '/{page}'));
    $pNumbers = mc_publicPageNumbers($countRows, $pgn['per'], $seolink);
  } else {
    $pNumbers = mc_publicPageNumbers($countRows, $pgn['per'], $SETTINGS->ifolder . '/?p=wishlist&amp;next=');
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
$tpl->assign('MESSAGE', str_replace(array('{url}','{hurl}'),array($urls[1],$urls[0]),$public_accounts_wish[0]));
$tpl->assign('TEXTW', $public_accounts_wish);
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
$tpl->assign('PRODUCTS', $MCACC->wishlist($loggedInUser['id'], $public_accounts_wish, $pgn));
$tpl->assign('PAGINATION', ($countRows > 0 && isset($pNumbers) ? $pNumbers : ''));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-wishlist.tpl.php');

include(PATH . 'control/footer.php');

?>