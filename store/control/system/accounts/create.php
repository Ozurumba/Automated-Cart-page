<?php

if (!defined('PARENT') || $SETTINGS->en_create == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Set session var for CleanTalk..
$_SESSION[mc_encrypt(SECRET_KEY) . '_stime'] = time();

// Load language files..
include(MCLANG . 'accounts.php');

$headerTitleText = mc_cleanData($public_accounts[13] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs = array(
  $public_accounts[13]
);

// HTML class..
include(PATH . 'control/classes/class.html.php');
$MCHTML           = new mcHtml();
$MCHTML->settings = $SETTINGS;

// Load JS
$loadJS['mc-acc-ops']   = 'load';
$loadJS['states'] = 'load';
$loadJS['params'] = array(
  '0'
);

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
$tpl->assign('TEXTP', $public_accounts_create);
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
$tpl->assign('COUNTRIES', array(
  'ship' => $MCHTML->loadShippingCountries('num'),
  'bill' => $MCHTML->loadShippingCountries('num')
));
$tpl->assign('PASS_INSTRUCTION', str_replace('{chars}',$SETTINGS->minPassValue,($SETTINGS->forcePass == 'yes' ? $public_accounts_create[22] : $public_accounts_create[21])));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-create.tpl.php');

include(PATH . 'control/footer.php');

?>