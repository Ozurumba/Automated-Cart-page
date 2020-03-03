<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

$baseP = (isset($SETTINGS->ifolder) ? $SETTINGS->ifolder : '');
if (function_exists('mc_detectSSLConnection') && isset($SETTINGS->ifolder)) {
  $ssl = mc_detectSSLConnection($SETTINGS);
  $baseP = ($ssl == 'yes' ? str_replace('http://', 'https://', $SETTINGS->ifolder) : $SETTINGS->ifolder);
}

$tpl->assign('DIR', (isset($mc_global[0]) ? $mc_global[0] : 'ltr'));
$tpl->assign('LANG', (isset($mc_global[1]) ? $mc_global[1] : 'en'));
$tpl->assign('CHARSET', (isset($charset) ? $charset : 'utf-8'));
$tpl->assign('SETTINGS', (isset($SETTINGS->id) ? (array) $SETTINGS : array()));
$tpl->assign('BASE_PATH', $baseP);
$tpl->assign('THEME_FOLDER', (defined('THEME_FOLDER') ? THEME_FOLDER : '_theme_default'));
$tpl->assign('SLIDE_PANEL', (isset($slidePanel) ? $slidePanel : ''));
$tpl->assign('LAYOUT', (defined('MC_CATVIEW') && MC_CATVIEW == 'grid' ? (isset($pCount) && $pCount > 0 ? ' layout_gridview' : 'layout_gridview') : ''));
$tpl->assign('P_COUNT', (isset($pCount) ? $pCount : '0'));
$tpl->assign('LEFT_MENU_BOXES', (isset($leftBoxDisplay) ? $MCMENUCLS->leftMenuWrapper($leftBoxDisplay) : ''));
$tpl->assign('SOCIAL_LINKS', (isset($MCSOCIAL) && method_exists($MCSOCIAL, 'links') ? $MCSOCIAL->links() : ''));
$tpl->assign('ACCOUNT', (!empty($loggedInUser) && isset($loggedInUser['id']) ? $loggedInUser : array()));
$tpl->assign('MBDX', (isset($MCPDTC) && method_exists($MCPDTC, 'isMobile') ? $MCPDTC : ''));

// If view is grid and there are no products, set class to blank to prevent html anomally..
if (defined('MC_CATVIEW') && MC_CATVIEW == 'grid' && isset($pCount) && $pCount == 0) {
  $tpl->assign('LAYOUT', '');
}

?>