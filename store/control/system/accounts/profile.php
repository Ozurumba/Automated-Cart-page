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

$headerTitleText = mc_cleanData($public_accounts[9] . ': ' . $headerTitleText);

// HTML class..
include(PATH . 'control/classes/class.html.php');
$MCHTML           = new mcHtml();
$MCHTML->settings = $SETTINGS;

// Breadcrumb..
$breadcrumbs[] = '<a href="' . $MCRWR->url(array('account')) . '">' . $public_accounts[8] . '</a>';
$breadcrumbs[] = $public_accounts[9];

// Get addresses
$addr = $MCACC->getaddresses($loggedInUser['id']);

// Load JS
$loadJS['mc-acc-ops']   = 'load';
if ($SETTINGS->en_wish == 'yes') {
  $loadJS['wish-zone'] = 'load';
  $loadJS['params']    = array(
    $loggedInUser['id']
  );
} else {
  $loadJS['states'] = 'load';
  $loadJS['params']    = array(
    $loggedInUser['id']
  );
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

$tpl = mc_getSavant();
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('TRADE', array(
  str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
  ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
  ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
));
$tpl->assign('TEXTP', $public_accounts_profile);
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
  'ship' => $MCHTML->loadShippingCountries('num',$addr[1]['addr1']),
  'bill' => $MCHTML->loadShippingCountries('num',$addr[0]['addr1'])
));
$tpl->assign('ZONES', '');
$tpl->assign('BILL_SHIP_FLDS', array(
  'bill' => $addr[0],
  'ship' => $addr[1]
));
$tpl->assign('PASS_INSTRUCTION', str_replace('{chars}',$SETTINGS->minPassValue,($SETTINGS->forcePass == 'yes' ? $public_accounts_create[22] : $public_accounts_create[21])));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-profile.tpl.php');

include(PATH . 'control/footer.php');

?>