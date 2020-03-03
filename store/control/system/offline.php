<?php

if (!defined('PARENT')) {
  include(PATH.'control/system/headers/403.php');
  exit;
}

// Re-activation date..
if ($SETTINGS->offlineDate == date('Y-m-d')) {
  mc_autoActivateCart($MCRWR);
}

$tpl = mc_getSavant();
$tpl->assign('TEXT', array(mc_txtParsingEngine($SETTINGS->offlineText),$msg_script42));
$tpl->assign('TITLE', mc_cleanData($msg_script42 . $headerTitleText));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/system-offline.tpl.php');

?>
