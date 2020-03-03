<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

if (isset($_GET['prt']) && ctype_alnum($_GET['prt'])) {
  $u = mc_getTableData('accounts', 'system1', mc_safeSQL($_GET['prt']), ' AND `verified` = \'yes\'');
  if (isset($u->id)) {
    $tmp = 'account-newpass-reset.tpl.php';
  }
}

// Load language files..
include(MCLANG . 'accounts.php');

$headerTitleText = mc_cleanData($public_accounts_forgot[0] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs = array(
  $public_accounts_forgot[0]
);

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
$tpl->assign('TXT', $public_accounts_forgot);
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
$tpl->assign('TOKEN', (isset($tmp) ? $u->system1 : ''));
$tpl->assign('PASS_INSTRUCTION', str_replace('{chars}',$SETTINGS->minPassValue,($SETTINGS->forcePass == 'yes' ? $public_accounts_create[22] : $public_accounts_create[21])));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/' . (isset($tmp) ? $tmp : 'account-newpass.tpl.php'));

include(PATH . 'control/footer.php');

?>