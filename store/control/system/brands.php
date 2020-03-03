<?php

if (!defined('PARENT') || !isset($_GET['pbnd'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Makes page load first page for filters..
if (isset($_SESSION['reset-next-links'])) {
  unset($_GET['next']);
  $page = 1;
  $limit = $page * $SETTINGS->productsPerPage - ($SETTINGS->productsPerPage);
}

define('LOAD_BRANDS', 1);

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');
include(MCLANG . 'brands.php');

// Order by options..
$orderByItems = array(
  'title-az' => $public_category6,
  'title-za' => $public_category25,
  'price-low' => $public_category8,
  'price-high' => $public_category26,
  'date-new' => $public_category7,
  'date-old' => $public_category27,
  'stock' => $public_category24,
  'multi-buy' => $public_search25
);

// Add the view all option..
if (VIEW_ALL_BRANDS_DD == 'yes') {
  $orderByItems['all-items'] = $public_category32;
}

// If trade is logged in, multi buy and low stock are not applicable..
if (defined('MC_TRADE_DISCOUNT')) {
  unset($orderByItems['stock'],$orderByItems['multi-buy']);
}

// Set default for filter..
if (!isset($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['order'] = BRANDS_FILTER;
} else {
  if (!in_array($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)], array_keys($orderByItems))) {
    $_GET['order'] = BRANDS_FILTER;
  } else {
    $_GET['order'] = $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)];
  }
}

// Does brand have multiples..
if (strpos($_GET['pbnd'], '_') !== false) {
  $chop           = explode('_', $_GET['pbnd']);
  $multiBuildUrl  = $_GET['pbnd'];
  $_GET['pbnd'] = $chop[0];
}

// Check query var digits..
mc_checkDigit($_GET['pbnd']);

// Get brand data..
$BRAND = mc_getTableData('brands', 'id', $_GET['pbnd']);

// Does cat exist..
if (!isset($BRAND->id)) {
  include(PATH . 'control/system/headers/404.php');
  exit;
}

// Is category enabled..
if ($BRAND->enBrand == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Page title..
$headerTitleText = mc_cleanData(str_replace(array(
  '{brand}',
  '{website}'
), array(
  $BRAND->name,
  $SETTINGS->website
), $public_brands));

// Determine brand IDs..
$brandIDs = (isset($multiBuildUrl) ? $multiBuildUrl : $_GET['pbnd']);

// Product count..
$pCount = $MCPROD->productList('category', array('count' => 'yes', 'brands' => $brandIDs));

// Determine pagination..
// If all items are set for filter set products per page to a very high amount to display all..
if ($_GET['order'] == 'all-items' && VIEW_ALL_BRANDS_DD == 'yes') {
  $SETTINGS->productsPerPage = '999999999999';
} else {
  if ($pCount > $SETTINGS->productsPerPage) {
    if ($SETTINGS->en_modr == 'yes') {
      $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['brs'] . '/' . $brandIDs . '/{page}/' . ($BRAND->rwslug ? $BRAND->rwslug : $MCRWR->title(mc_cleanData($BRAND->name)))));
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $seolink);
    } else {
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $SETTINGS->ifolder . '/?next=');
    }
  }
}

// Load javascript..
$loadJS['swipe']  = 'load';

// If at least 1 mp3 exists, load sound manager..
if (mc_rowCount('mp3') > 0) {
  $loadJS['soundmanager'] = 'load';
}

// Breadcrumb..
$breadcrumbs = array(
  $mc_brands[0],
  mc_safeHTML($BRAND->name)
);

// Data array..
$dArr = array(
  'rwslug' => $BRAND->rwslug,
  'name' => mc_cleanData($BRAND->name),
  'multi' => $brandIDs
);

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $MCRWR->url(array(
  $MCRWR->config['slugs']['brs'] . '/' . $brandIDs . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $MCRWR->title($BRAND->name)),
  'pbnd=' . $brandIDs
));
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('BRAND', array_map('mc_cleanData', (array) $BRAND));
$tpl->assign('BRANDNAME', mc_safeHTML($BRAND->name));
$tpl->assign('CATEGORIES', $MCMENUCLS->filter_cats($mc_leftmenu));
$tpl->assign('MESSAGE', $public_brands3);
$tpl->assign('TEXT', array(
  $public_category21,
  $public_category19,
  $public_brands2,
  $mc_category[1],
  $mc_leftmenu[1]
));
$tpl->assign('FEED_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['rsb'] . '/' . $brandIDs,
  'pbnd=' . $brandIDs
)));
$tpl->assign('PAGE_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['brs'] . '/' . $brandIDs . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $MCRWR->title($BRAND->name)),
  'pbnd=' . $brandIDs
)));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems));
$tpl->assign('PRODUCTS', $MCPROD->productList('category', array('brands' => $brandIDs)));
$tpl->assign('PAGINATION', ($pCount > 0 && isset($pNumbers) ? $pNumbers : ''));
$tpl->assign('BDATA', (array) $BRAND);

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/brands.tpl.php');

include(PATH . 'control/footer.php');

?>