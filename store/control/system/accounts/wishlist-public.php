<?php

if (!defined('PARENT') || !isset($_GET['wls']) || !ctype_alnum($_GET['wls']) || $SETTINGS->en_wish == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('MC_ORDER_AJAX', 'wish');
define('MC_PUBLICWISH', 'yes');

// Load language files..
// Makes page load first page for filters..
if (isset($_SESSION['reset-next-links'])) {
  unset($_GET['next']);
  $page = 1;
  $limit = $page * $SETTINGS->productsPerPage - ($SETTINGS->productsPerPage);
}

include(MCLANG . 'accounts.php');
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');

$ACC = mc_getTableData('accounts', 'md5(concat(`id`,`email`))', mc_safeSQL($_GET['wls']), ' AND `verified` = \'yes\'');

if (!isset($ACC->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Is account disabed?
if ($ACC->enabled == 'no') {
  include(PATH . 'control/system/accounts/disabled.php');
  exit;
}

$headerTitleText = mc_cleanData(str_replace('{name}',mc_safeHTML($ACC->name),$public_accounts_wish_public[9]) . ': ' . $headerTitleText);

// Order by options..
$orderByItems = array(
  'title-az' => $public_accounts_wish_public[2],
  'title-za' => $public_accounts_wish_public[3],
  'price-low' => $public_accounts_wish_public[4],
  'price-high' => $public_accounts_wish_public[5],
  'date-new' => $public_accounts_wish_public[6],
  'date-old' => $public_accounts_wish_public[7],
  'stock' => $public_accounts_wish_public[8]
);

// Add the view all option..
if (VIEW_ALL_WISH_DD == 'yes') {
  $orderByItems['all-items'] = $public_accounts_wish_public[12];
}

// Set default for filter..
if (!isset($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['order'] = WISH_PRODUCTS_FILTER;
} else {
  if (!in_array($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)], array_keys($orderByItems))) {
    $_GET['order'] = WISH_PRODUCTS_FILTER;
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

$pCount = $MCPROD->productList('wishlist', array('count' => 'yes','accid' => $ACC->id));

// Determine pagination..
// If all items are set for filter set products per page to a very high amount to display all..
if ($_GET['order'] == 'all-items' && VIEW_ALL_WISH_DD == 'yes') {
  $SETTINGS->productsPerPage = '999999999999';
} else {
  if ($SETTINGS->en_modr == 'yes') {
    $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['wls'] . '/' . $_GET['wls'] . '/{page}'));
    $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $seolink);
  } else {
    $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $SETTINGS->ifolder . '/?wls=' . $_GET['wls'] . '&amp;next=','order,cat');
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
  str_replace('{name}',mc_safeHTML($ACC->name),$public_accounts_wish_public[10])
);

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $MCRWR->url(array(
  $MCRWR->config['slugs']['wls'] . '/' . $_GET['wls'] . '/1',
  'wls=' . $_GET['wls']
));
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('CATEGORIES', $MCMENUCLS->filter_cats($mc_leftmenu));
$tpl->assign('MESSAGE', ($ACC->wishtext ? mc_NL2BR($ACC->wishtext) : $public_accounts_wish_public[13]));
$tpl->assign('TEXT', array(
  $public_accounts_wish_public[0],
  $public_accounts_wish_public[1],
  $public_accounts_wish_public[11],
  str_replace('{name}',mc_safeHTML($ACC->name),$public_accounts_wish_public[9]),
  $mc_category[1],
  $mc_leftmenu[1],
  $public_accounts_wish_public[14]
));
$tpl->assign('PRODUCTS', $MCPROD->productList('wishlist', array('accid' => $ACC->id)));
$tpl->assign('PAGINATION', (isset($pNumbers) ? $pNumbers : ''));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems));
$tpl->assign('WISH_PERMS', (isset($loggedInUser['id']) && $loggedInUser['id'] == $ACC->id ? 'yes' : 'no'));
$tpl->assign('WISH_URL', $MCRWR->url(array($MCRWR->config['slugs']['wst'])));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/account-wishlist-public.tpl.php');

include(PATH . 'control/footer.php');

?>