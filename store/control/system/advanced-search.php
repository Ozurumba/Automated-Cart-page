<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('MC_ORDER_AJAX', 'search');

// Load language files..
include(MCLANG . 'search.php');

// Load category brands..
if (isset($_GET['loadCatBrands'])) {
  $html = '';
  $cat  = (int) $_GET['loadCatBrands'];
  if ($cat > 0) {
    $html = $MCSYS->searchCatBrands($cat);
  }
  echo $MCJSON->encode(array(
    'brands' => $html
  ));
  exit;
}

$headerTitleText = mc_cleanData($msg_public_header5 . ': ' . $headerTitleText);

$orderByItems = array(
  'title-az' => $public_search17,
  'title-za' => $public_search19,
  'price-low' => $public_search18,
  'price-high' => $public_search20,
  'date-new' => $public_search21,
  'date-old' => $public_search22,
  'multi-buy' => $public_search25
);

// If trade is logged in, multi buy and low stock are not applicable..
if (defined('MC_TRADE_DISCOUNT')) {
  unset($orderByItems['multi-buy']);
}

// Load calendar..
$loadJS['adv_search']  = 'load';
$loadJS['priceFormat'] = 'load';
$loadJS['jquery-ui']   = 'load';
$loadJS['params']      = array(
  0 => trim($msg_cal3),
  1 => trim($msg_cal5)
);

// Breadcrumb..
$breadcrumbs = array(
  $msg_public_header5
);

// Left menu boxes..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['brands']  = true;
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('TEXT', array(
  $msg_public_header5,
  $public_search5,
  $public_search6,
  str_replace('{format}', $SETTINGS->jsDateFormat, $public_search7),
  $public_search8,
  $public_search9,
  str_replace('{currency}', $SETTINGS->baseCurrency, $public_search10),
  $public_search11,
  $public_search12,
  $msg_script5,
  $msg_script6,
  $public_search13,
  (isset($_SESSION['store_SearchResults']) ? $_SESSION['store_SearchResults'] : ''),
  $public_search26,
  $public_search27,
  $public_search28,
  $public_search29,
  $mc_leftmenu
));
$tpl->assign('URL', $MCRWR->url(array('base_href')));
$tpl->assign('CATEGORIES', $MCMENUCLS->filter_cats($mc_leftmenu, 'li'));
$tpl->assign('BRANDS', ($SETTINGS->showBrands == 'yes' ? $MCSYS->searchCatBrands() : ''));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems, 'html-option-tags'));
$tpl->assign('IS_DOWNLOADS', (mc_rowCount('products WHERE `pDownload` = \'yes\'') > 0 ? 'yes' : 'no'));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/advanced-search.tpl.php');

include(PATH . 'control/footer.php');

?>