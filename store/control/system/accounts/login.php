<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Logout..
if ($_GET['p'] == 'logout') {
  if (isset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
    $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))] = '';
    unset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))]);
  }
  if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language'])) {
    unset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language']);
  }
  if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'])) {
    unset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency']);
  }
  if (isset($_SESSION['mc_acc_type_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
    unset($_SESSION['mc_acc_type_' . mc_encrypt(mc_encrypt(SECRET_KEY))]);
  }
  header("Location: " . $MCRWR->url(array('account')));
  exit;
}

// Load language files..
include(MCLANG . 'accounts.php');

$headerTitleText = mc_cleanData($public_accounts[0] . ': ' . $headerTitleText);

// Breadcrumb..
$breadcrumbs = array(
  $public_accounts[0]
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
$tpl->assign('SS', array(
  'email' => (isset($_SESSION['ss_mail_' . mc_encrypt(SECRET_KEY)]) ? $_SESSION['ss_mail_' . mc_encrypt(SECRET_KEY)] : '')
));
$tpl->assign('URL', array(
  $MCRWR->url(array('account')),
  $MCRWR->url(array('profile')),
  $MCRWR->url(array('history')),
  $MCRWR->url(array($MCRWR->config['slugs']['wst'])),
  $MCRWR->url(array($MCRWR->config['slugs']['ssc'])),
  $MCRWR->url(array('create')),
  $MCRWR->url(array('logout')),
  $MCRWR->url(array('newpass')),
  $MCRWR->url(array('close'))
));

// Clear session var after verification..
if (isset($_SESSION['ss_mail_' . mc_encrypt(SECRET_KEY)])) {
  unset($_SESSION['ss_mail_' . mc_encrypt(SECRET_KEY)]);
}

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-login.tpl.php');

include(PATH . 'control/footer.php');

?>