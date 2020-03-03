<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Is sitemap enabled..
if ($SETTINGS->en_sitemap == 'no') {
  include(PATH . 'control/system/headers/404.php');
  exit;
}

$breadcrumbs = array(
  $msg_public_header33
);

$headerTitleText = $msg_public_header33 . ': ' . $headerTitleText;

include(PATH . 'control/classes/class.sitemap.php');
$SM           = new sitemap();
$SM->settings = $SETTINGS;
$SM->rwr      = $MCRWR;
$SM->cache    = $MCCACHE;

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $MCRWR->url(array('sitemap'));
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('TEXT', array(
  $msg_public_header33,
  $mc_sitemap
));
$tpl->assign('SITEMAP', $SM->catlist() . $SM->extras());

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/sitemap.tpl.php');

include(PATH . 'control/footer.php');

?>