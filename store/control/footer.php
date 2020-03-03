<?php

if (!defined('PARENT')) {
  include(PATH.'control/system/headers/403.php');
  exit;
}

// Copyright link..
if (LICENCE_VER == 'unlocked' && $SETTINGS->publicFooter) {
  $footer = $SETTINGS->publicFooter;
} else {
  $footer = '<b>' . $msg_script3 . '</b>: <a href="https://www.' . SCRIPT_URL . '" title="' . SCRIPT_NAME . '" onclick="window.open(this);return false">' . SCRIPT_NAME . '</a> ';
  $footer .= '&copy;2006-' . date('Y', time()) . ' <a href="https://www.maianscriptworld.co.uk" onclick="window.open(this);return false" title="Maian Script World">Maian Script World</a>. ' . $msg_script4 . '.';
}
$tpl = mc_getSavant();
// For relative loading..
if (defined('SAV_PATH')) {
  $tpl->addPath('template', SAV_PATH);
}
$tpl->assign('TXT', array(
  trim(str_replace('{store}', '', $public_checkout18))
));
$tpl->assign('FOOTER', trim($footer));
$tpl->assign('MODULES', $MCSYS->loadJSFunctions($loadJS, 'footer'));
$tpl->assign('TEXT',array($public_footer,$public_footer2,$public_footer3,$public_footer4,$public_footer5,$public_footer6,$mc_admin[3]));
$tpl->assign('LEFT_LINKS', $MCSYS->newPageLinksFooter());
$tpl->assign('MIDDLE_LINKS', $MCSYS->newPageLinksFooter('middle'));
$tpl->assign('CHECKOUT_URL', $MCRWR->url(array('checkpay')));
$tpl->assign('CART_COUNT', $MCCART->cartCount());
$tpl->assign('STRIPE_EN', (isset($mcSystemPaymentMethods['stripe']['enable']) ? $mcSystemPaymentMethods['stripe']['enable'] : 'no'));
$H = new paymentHandler();
$tpl->assign('STRIPE_PARAMS', (isset($mcSystemPaymentMethods['stripe']['enable']) && $mcSystemPaymentMethods['stripe']['enable'] == 'yes' ? $H->paymentParams('stripe') : array()));
$tpl->assign('ACC', array(
  'name' => (isset($loggedInUser['name']) ? $loggedInUser['name'] : ''),
  'email' => (isset($loggedInUser['email']) ? $loggedInUser['email'] : '')
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER.'/footer.tpl.php');

?>
