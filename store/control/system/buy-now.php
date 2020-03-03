<?php

if (!defined('PARENT') || !isset($_GET['mcbn']) || !BUY_NOW_CODE_OPTION) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

$tpl = mc_getSavant();
$tpl->assign('TITLE', mc_safeHTML($msg_buynow[0]));
$tpl->assign('ID', (int) $_GET['mcbn']);
$tpl->assign('TXT', array(
  $msg_buynow[0],
  $msg_buynow[1]
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/buy-now.tpl.php');

?>