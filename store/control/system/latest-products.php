<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Makes page load first page for filters..
if (isset($_SESSION['reset-next-links'])) {
  unset($_GET['next']);
  $page = 1;
  $limit = $page * $SETTINGS->productsPerPage - ($SETTINGS->productsPerPage);
}

define('MC_ORDER_AJAX', 'latest');

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'latest-products.php');
include(MCLANG . 'product.php');
include(MCLANG . 'special-offers.php');

$headerTitleText = mc_cleanData($public_latest12 . ': ' . $headerTitleText);

// Order by options..
$orderByItems = array(
  'title-az' => $public_latest5,
  'title-za' => $public_latest8,
  'price-low' => $public_latest7,
  'price-high' => $public_latest9,
  'date-new' => $public_latest10,
  'date-old' => $public_latest11,
  'stock' => $public_latest6,
  'multi-buy' => $public_search25
);

// If trade is logged in, multi buy and low stock are not applicable..
if (defined('MC_TRADE_DISCOUNT')) {
  unset($orderByItems['stock'],$orderByItems['multi-buy']);
}

// Add the view all option..
if (VIEW_ALL_LATEST_DD == 'yes') {
  $orderByItems['all-items'] = $public_special12;
}

// Set default for filter..
if (!isset($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['order'] = LATEST_PRODUCTS_FILTER;
} else {
  if (!in_array($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)], array_keys($orderByItems))) {
    $_GET['order'] = LATEST_PRODUCTS_FILTER;
  } else {
    $_GET['order'] = $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)];
  }
}

// Active filters..
if (isset($_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['brand'] = $_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)];
}
if (isset($_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['cat'] = (int) $_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)];
} else {
  $_GET['cat'] = '0';
}

$pCount = $MCPROD->productList('latest', array('count' => 'yes'));

// Determine pagination..
// If all items are set for filter set products per page to a very high amount to display all..
if ($_GET['order'] == 'all-items' && VIEW_ALL_LATEST_DD == 'yes') {
  $SETTINGS->productsPerPage = '999999999999';
} else {
  if ($SETTINGS->en_modr == 'yes') {
    $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['lpr'] . '/{page}'));
    $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $seolink);
  } else {
    $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $SETTINGS->ifolder . '/?p=latest-products&amp;next=','order,cat');
  }
}

// Category session var..
$_SESSION['thisCat'] = $_GET['cat'];

// Load javascript..
$loadJS['swipe']  = 'load';

// If at least 1 mp3 exists, load sound manager..
if (mc_rowCount('mp3') > 0) {
  $loadJS['soundmanager'] = 'load';
}

// Breadcrumb..
$breadcrumbs = array(
  $public_latest12
);

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $MCRWR->url(array(
  $MCRWR->config['slugs']['lpr'] . '/1',
  'p=latest-products'
));
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('CATEGORIES', $MCMENUCLS->filter_cats($mc_leftmenu));
$tpl->assign('MESSAGE', str_replace('{count}', $SETTINGS->latestProdLimit, $public_latest));
$tpl->assign('TEXT', array(
  $public_latest12,
  $public_latest2,
  $public_latest3,
  $public_latest4,
  $mc_category[1],
  $mc_leftmenu[1]
));
$tpl->assign('FEED_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['rsl'] . ($_GET['cat'] > 0 ? '/' . (int) $_GET['cat'] : ''),
  'rss=latest' . ($_GET['cat'] > 0 ? '-' . (int) $_GET['cat'] : '')
)));
$tpl->assign('PRODUCTS', $MCPROD->productList('latest'));
$tpl->assign('PAGINATION', (isset($pNumbers) ? $pNumbers : ''));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/latest-products.tpl.php');

include(PATH . 'control/footer.php');

?>