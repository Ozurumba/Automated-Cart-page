<?php

if (!defined('PARENT') || !isset($_GET['c'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Makes page load first page for filters..
if (isset($_SESSION['reset-next-links'])) {
  unset($_GET['next']);
  $page = 1;
  $limit = $page * $SETTINGS->productsPerPage - ($SETTINGS->productsPerPage);
}

define('MC_ORDER_AJAX', 'category');

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');

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
if (VIEW_ALL_CATEGORY_DD == 'yes') {
  $orderByItems['all-items'] = $public_category32;
}

// If trade is logged in, multi buy and low stock are not applicable..
if (defined('MC_TRADE_DISCOUNT')) {
  unset($orderByItems['stock'],$orderByItems['multi-buy']);
}

// Check query var digits..
mc_checkDigit($_GET['c']);

// Get cat data..
$CAT = mc_getTableData('categories', 'id', (int) $_GET['c']);

// Does cat exist..
if (!isset($CAT->id)) {
  include(PATH . 'control/system/headers/404.php');
  exit;
}

// Is category enabled..
if ($CAT->enCat == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Is this category viewable to visitor?
if (mc_visCatPerms($CAT->vis) == 'block') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

$family = array();
$tree   = array();

// Is category parent/child or infant..
switch($CAT->catLevel) {
  case '1':
    $family[$CAT->id] = array(
      $CAT->catname,
      $CAT->rwslug
    );
    $tree['parent']        = ($CAT->titleBar ? $CAT->titleBar : $CAT->catname);
    $bFltrs                = $MCSYS->brandFilterCats(array($CAT->id));
    break;
  case '2':
    $thisParent              = mc_getTableData('categories', 'id', $CAT->childOf);
    $family[$thisParent->id] = array(
      $thisParent->catname,
      $thisParent->rwslug
    );
    $family[$CAT->id]   = array(
      $CAT->catname,
      $CAT->rwslug
    );
    $tree['parent']          = ($thisParent->titleBar ? $thisParent->titleBar : $thisParent->catname);
    $tree['child']           = ($CAT->titleBar ? $CAT->titleBar : $CAT->catname);
    if ($thisParent->enCat == 'no') {
      include(PATH . 'control/system/headers/403.php');
      exit;
    }
    $bFltrs                  = $MCSYS->brandFilterCats(array($CAT->id, $thisParent->id));
    break;
  case '3':
    $thisChild               = mc_getTableData('categories', 'id', $CAT->childOf);
    $thisParent              = mc_getTableData('categories', 'id', $thisChild->childOf);
    $family[$thisParent->id] = array(
      $thisParent->catname,
      $thisParent->rwslug
    );
    $family[$thisChild->id]  = array(
      $thisChild->catname,
      $thisChild->rwslug
    );
    $family[$CAT->id]   = array(
      $CAT->catname,
      $CAT->rwslug
    );
    $tree['parent']          = ($thisParent->titleBar ? $thisParent->titleBar : $thisParent->catname);
    $tree['child']           = ($thisChild->titleBar ? $thisChild->titleBar : $thisChild->catname);
    $tree['infant']          = ($CAT->titleBar ? $CAT->titleBar : $CAT->catname);
    if ($thisChild->enCat == 'no') {
      include(PATH . 'control/system/headers/403.php');
      exit;
    }
    $bFltrs                  = $MCSYS->brandFilterCats(array($CAT->id, $thisParent->id, $thisChild->id));
    break;
}

// Active filters..
if (isset($_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['brand'] = $_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)];
  // If this brand doesn`t exist for this category, clear it..
  if (strpos($_GET['brand'], ',') !== false) {
    $fBrands = explode(',', $_GET['brand']);
    $aIntS   = array_intersect($fBrands, $bFltrs);
    if (empty($aIntS)) {
      unset($_GET['brand'], $_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)]);
    }
  } else {
    if (!in_array($_GET['brand'], $bFltrs)) {
      unset($_GET['brand'], $_SESSION['mc_brands_filters_' . mc_encrypt(SECRET_KEY)]);
    }
  }
}

// Overwrite meta data..
if ($CAT->metaKeys) {
  $overRideMetaKeys = mc_safeHTML($CAT->metaKeys);
}
if ($CAT->metaDesc) {
  $overRideMetaDesc = mc_safeHTML($CAT->metaDesc);
}

// Page title..
if (isset($_GET['brand']) && $_GET['brand'] > 0) {
  mc_checkDigit($_GET['brand']);
  $B               = mc_getTableData('brands', 'id', $_GET['brand']);
  // Display based on whether cat is parent or child..
  $headerTitleText = str_replace(array(
    '{cat}',
    '{child}',
    '{infant}',
    '{brand}'
  ), array(
    mc_safeHTML($tree['parent']),
    (isset($tree['child']) ? mc_safeHTML($tree['child']) : ''),
    (isset($tree['infant']) ? mc_safeHTML($tree['infant']) : ''),
    mc_cleanData($B->name)
  ), str_replace('{website}', mc_safeHTML($SETTINGS->website), (!isset($tree['child']) ? $public_category30 : (isset($tree['infant']) ? $public_category22 : $public_category29))));
} else {
  // Display based on whether cat is parent or child..
  $headerTitleText = str_replace(array(
    '{cat}',
    '{child}',
    '{infant}'
  ), array(
    mc_safeHTML($tree['parent']),
    (isset($tree['child']) ? mc_safeHTML($tree['child']) : ''),
    (isset($tree['infant']) ? mc_safeHTML($tree['infant']) : '')
  ), str_replace('{website}', mc_safeHTML($SETTINGS->website), (!isset($tree['child']) ? $public_category : (isset($tree['infant']) ? $public_category23 : $public_category2))));
}

$headerTitleText = mc_cleanData($headerTitleText);

// Set default for filter..
if (!isset($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['order'] = CATEGORY_FILTER;
} else {
  if (!in_array($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)], array_keys($orderByItems))) {
    $_GET['order'] = CATEGORY_FILTER;
  } else {
    $_GET['order'] = $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)];
  }
}

// Product count..
$pCount = $MCPROD->productList('category', array('count' => 'yes'));

// Determine pagination..
// If all items are set for filter set products per page to a very high amount to display all..
if ($_GET['order'] == 'all-items' && VIEW_ALL_CATEGORY_DD == 'yes') {
  $SETTINGS->productsPerPage = '999999999999';
} else {
  if ($pCount > $SETTINGS->productsPerPage) {
    if ($SETTINGS->en_modr == 'yes') {
      $seolink  = $MCRWR->url(array($MCRWR->config['slugs']['cat'] . '/' . $CAT->id . '/{page}/' . ($CAT->rwslug ? $CAT->rwslug : $MCRWR->title(mc_cleanData($CAT->catname)))));
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $seolink);
    } else {
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $SETTINGS->ifolder . '/?next=','order');
    }
  }
}

// Load javascript..
$loadJS['swipe']  = 'load';

// If at least 1 mp3 exists, load sound manager..
if (mc_rowCount('mp3') > 0) {
  $loadJS['soundmanager'] = 'load';
}

// Display brands..
$brandCatDisplay = $_GET['c'];

// Category session var..
$_SESSION['thisCat'] = $_GET['c'];

// Breadcrumb..
foreach ($family AS $k => $v) {
  if (++$crumbcount != count($family)) {
    $url = $MCRWR->url(array(
      $MCRWR->config['slugs']['cat'] . '/' . $k . '/1/' . ($v[1] ? $v[1] : $MCRWR->title($v[0])),
      'c=' . $k
    ));
    $breadcrumbs[] = '<a href="' . $url . '" title="' . mc_safeHTML($v[0]) . '">' . $v[0] . '</a>';
  } else {
    $breadcrumbs[] = mc_safeHTML($v[0]);
  }
  $menuLinksDisplay[] = $k;
}

// Structured data..
$mc_structUrl   = $MCRWR->url(array(
  $MCRWR->config['slugs']['cat'] . '/' . $CAT->id . '/1/' . ($CAT->rwslug ? $CAT->rwslug : $MCRWR->title($CAT->catname)),
  'c=' . $CAT->id
));
$mc_structTitle = $headerTitleText;
$mc_structDesc  = (isset($overRideMetaDesc) ? $overRideMetaDesc : $SETTINGS->metaDesc);

// For banner rotator..
define('SLIDER_CAT', $CAT->id);

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('CATEGORIES', ($CAT->showRelated == 'yes' ? $MCMENUCLS->related() : ''));
$tpl->assign('MESSAGE', ($CAT->comments ? mc_txtParsingEngine($CAT->comments) : $public_category5));
$tpl->assign('TEXT', array(
  $public_category21,
  $public_category19,
  $public_category16,
  $mc_category[1]
));
$tpl->assign('FEED_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['rsc'] . '/' . $CAT->id,
  'crss=' . $CAT->id
)));
$tpl->assign('PAGE_URL', $MCRWR->url(array(
  $MCRWR->config['slugs']['cat'] . '/' . $CAT->id . '/1/' . ($CAT->rwslug ? $CAT->rwslug : $MCRWR->title($CAT->catname)),
  'c=' . $CAT->id
)));
$tpl->assign('LAYOUT', (defined('MC_CATVIEW') && MC_CATVIEW == 'grid' ? ' layout_gridview' : ''));
$tpl->assign('BRANDS', $MCPROD->displayBrandsList($brandCatDisplay, $MCRWR->title(mc_cleanData($CAT->catname)), (isset($thisParent->id) ? 'yes' : 'no'), 'a'));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems));
$tpl->assign('PRODUCTS', $MCPROD->productList('category'));
$tpl->assign('PAGINATION', ($pCount > 0 && isset($pNumbers) ? $pNumbers : ''));
$tpl->assign('CDATA', (array) $CAT);

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/category.tpl.php');

include(PATH . 'control/footer.php');

?>