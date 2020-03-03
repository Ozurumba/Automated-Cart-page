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

define('MC_ORDER_AJAX', 'search');
define('SEARCH_RESULTS_SCREEN', 1);

// Load language files..
include(MCLANG . 'search.php');
include(MCLANG . 'product.php');
include(MCLANG . 'category.php');

$orderByItems = array(
  'title-az' => $public_search17,
  'title-za' => $public_search19,
  'price-low' => $public_search18,
  'price-high' => $public_search20,
  'date-new' => $public_search21,
  'date-old' => $public_search22,
  'multi-buy' => $public_search25
);

// Add the view all option..
if (VIEW_ALL_SEARCH_DD == 'yes') {
  $orderByItems['all-items'] = $public_search24;
}

// If trade is logged in, multi buy and low stock are not applicable..
if (defined('MC_TRADE_DISCOUNT')) {
  unset($orderByItems['stock'],$orderByItems['multi-buy']);
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

// Set default for filter..
if (!isset($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)])) {
  $_GET['order'] = SEARCH_FILTER;
} else {
  if (!in_array($_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)], array_keys($orderByItems))) {
    $_GET['order'] = SEARCH_FILTER;
  } else {
    $_GET['order'] = $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)];
  }
}

// Initialise var/array..
$searchSQL    = '';

// Build search..
if (!isset($_GET['sk'])) {
  if (isset($_GET['q'])) {
    // Check for tampering..
    if (is_array($_GET['q'])) {
      $_GET['q'] = '';
    } else {
      $_GET['q'] = (string) $_GET['q'];
    }
    // Neutralise someone just hitting the search button on standard search..
    if ($_GET['q'] == '' && !isset($_GET['adv'])) {
      header("Location: " . $MCRWR->url(array('no-search')));
      exit;
    }
    // Filter out unecessary characters..
    //$_GET['q']  = preg_replace('/[^0-9a-zA-Z\-._\s]/', '', $_GET['q']);
    $splitWords = explode(' ', $_GET['q']);
    $words      = array();
    if (!empty($splitWords)) {
      foreach ($splitWords AS $w) {
        if (strlen($w) > 1) {
          $words[] = $w;
        }
      }
    }
    if ($words) {
      // Log search keywords in session..
      $_SESSION['store_SearchResults'] = $_GET['q'];
      if (isset($_SESSION['store_SearchResultsPricePoints'])) {
        unset($_SESSION['store_SearchResultsPricePoints']);
      }
      for ($i = 0; $i < count($words); $i++) {
        $sw = strtolower(mc_safeSQL($words[$i]));
        // Search only product tags..
        if ($SETTINGS->searchTagsOnly == 'yes') {
          $searchSQL .= mc_defineNewline() . ($i ? 'OR ' : 'AND (') . ' LOWER(`pTags`) LIKE \'%' . $sw . '%\' ';
        } else {
          $searchSQL .= mc_defineNewline() . ($i ? 'OR ' : 'AND (') . ' LOWER(`pName`) LIKE \'%' . $sw . '%\' OR LOWER(`pCode`) LIKE \'%' . $sw . '%\' OR LOWER(`pDescription`) LIKE \'%' . $sw . '%\' OR LOWER(`pTags`) LIKE \'%' . $sw . '%\' ';
        }
      }
      $searchSQL = $searchSQL . ')';
    }
  }

  // Advanced search options..
  $filterArr = array();
  if (!isset($_GET['sk'])) {
    if (isset($_GET['adv'])) {
      $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)][MC_ORDER_AJAX] = array();
      if (isset($_GET['from'], $_GET['to']) && mc_checkValidDate($_GET['from']) != '0000-00-00' && mc_checkValidDate($_GET['to']) != '0000-00-00') {
        $searchSQL .= mc_defineNewline() . 'AND `pDateAdded` BETWEEN \'' . mc_convertCalToSQLFormat($_GET['from'], $SETTINGS) . '\' AND \'' . mc_convertCalToSQLFormat($_GET['to'], $SETTINGS) . '\'';
      }
      if (isset($_GET['cat']) && (int) $_GET['cat'] > 0) {
        $searchSQL .= mc_defineNewline() . 'AND `category` = \'' . (int) $_GET['cat'] . '\'';
        $_SESSION['thisCat'] = (int) $_GET['cat'];
        $filterArr['cat']    = (int) $_GET['cat'];
      }
      if (isset($_GET['brand']) && (int) $_GET['brand'] > 0) {
        $searchSQL .= mc_defineNewline() . 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
        $filterArr['brand'] = (int) $_GET['brand'];
      }
      if ((int) $_GET['price1'] > 0 || (int) $_GET['price2'] > 0) {
        // Tidy up prices..
        $_GET['price1'] = @number_format(str_replace(array(
          ','
        ), array(
          ''
        ), $_GET['price1']), 2, '.', '');
        $_GET['price2'] = @number_format(str_replace(array(
          ','
        ), array(
          ''
        ), $_GET['price2']), 2, '.', '');
        $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)][MC_ORDER_AJAX]['price1'] = $_GET['price1'];
        $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)][MC_ORDER_AJAX]['price2'] = $_GET['price2'];
        // Multiply fields by 100 to remove decimal places..
        if (defined('MC_TRADE_DISCOUNT')) {
          $searchSQL .= mc_defineNewline() . 'AND `pPrice`*100 >= \'' . mc_safeSQL($_GET['price1'] * 100) . '\' AND `pPrice`*100 <= \'' . mc_safeSQL($_GET['price2'] * 100) . '\'';
        } else {
          $searchSQL .= mc_defineNewline() . 'AND IF(`pOffer`>0,`pOffer`,`pPrice`)*100 >= \'' . mc_safeSQL($_GET['price1'] * 100) . '\' AND IF(`pOffer`>0,`pOffer`,`pPrice`)*100 <= \'' . mc_safeSQL($_GET['price2'] * 100) . '\'';
        }
        // If keys are empty, have price points as the search term..
        if (trim($_GET['q']) == '') {
          $_SESSION['store_SearchResultsPricePoints'] = $_GET['price1'] . ' - ' . $_GET['price2'];
          // We can also skip the download filter..
          define('SKIP_DOWNLOADS_IN_SEARCH', 1);
        }
      }
      if (isset($_GET['download']) && !defined('SKIP_DOWNLOADS_IN_SEARCH')) {
        $searchSQL .= mc_defineNewline() . 'AND `pDownload` = \'' . (in_array($_GET['download'], array(
          'yes',
          'no'
        )) ? $_GET['download'] : 'no') . '\'';
      }
      if (!defined('MC_TRADE_DISCOUNT') && isset($_GET['stock']) && $_GET['stock'] == 'yes') {
        $searchSQL .= mc_defineNewline() . 'AND IF (`pDownload`=\'yes\',`pStock` >= \'0\',`pStock` >= \'' . ($SETTINGS->searchLowStockLimit > 0 ? $SETTINGS->searchLowStockLimit : '1') . '\')';
      }
      if (isset($_GET['specials']) && !defined('MC_TRADE_DISCOUNT')) {
        $searchSQL .= mc_defineNewline() . 'AND `pOffer` > \'0\'';
      }
      if (isset($_GET['sortby']) && in_array($_GET['sortby'], array_keys($orderByItems))) {
        $filterArr['sortby'] = $_GET['sortby'];
        $_SESSION['mc_sort_' . mc_encrypt(SECRET_KEY)] = $_GET['sortby'];
        $_SESSION['reset-next-links'] = 'yes';
      }
    }
  }
  // Lets log search results..
  if ($searchSQL) {
    $newSearchKey = rand(11, 9999) . '-' . substr(md5(uniqid(rand(), 1)), 3, 35);
    $MCPROD->buildSearchProducts($searchSQL, $newSearchKey, $filterArr);
  }
  header("Location: " . $MCRWR->url(array('no-search')));
  exit;
}

// Get search count for pagination..
$pCount          = $MCPROD->productList('search', array('count' => true));
$headerTitleText = mc_cleanData(str_replace('{count}', $pCount, $public_search23) . ': ' . $headerTitleText);
$pNumbers        = '';

if (isset($_GET['sk'])) {
  if (!mc_validateSearchKey($_GET['sk'])) {
    include(PATH . 'control/system/headers/403.php');
    exit;
  }
  // If all items are set for filter set products per page to a very high amount to display all..
  if ($_GET['order'] == 'all-items' && VIEW_ALL_SEARCH_DD == 'yes') {
    $SETTINGS->productsPerPage = '999999999999';
  } else {
    if ($SETTINGS->en_modr == 'yes') {
      $seolink = $MCRWR->url(array($MCRWR->config['slugs']['sch'] . '/' . $_GET['sk'] . '/{page}'));
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $seolink);
    } else {
      $pNumbers = mc_publicPageNumbers($pCount, $SETTINGS->productsPerPage, $SETTINGS->ifolder . '/?next=','cat,order');
    }
  }
}

// Load javascript..
$loadJS['swipe']      = 'load';
$loadJS['mc-acc-ops'] = 'load';
$stext                = mc_filterJS(str_replace('{website}', mc_cleanData($SETTINGS->website), $public_search16));

// If at least 1 mp3 exists, load sound manager..
if (mc_rowCount('mp3') > 0) {
  $loadJS['soundmanager'] = 'load';
}

// Save url..
if ($SETTINGS->en_modr == 'yes') {
  $surl = $MCRWR->url(array($MCRWR->config['slugs']['sch'] . '/' . $_GET['sk'] . '/1'));
  $_SESSION['store_saveSearchKey_' . mc_encrypt(SECRET_KEY)] = $_GET['sk'];
} else {
  $surl = $SETTINGS->ifolder . '/?sk=' . $_GET['sk'] . '&amp;next=1';
  $_SESSION['store_saveSearchKey_' . mc_encrypt(SECRET_KEY)] = $_GET['sk'];
}

// Breadcrumb..
$breadcrumbs = array(
  (isset($_GET['adv']) ? '<a href="' . $MCRWR->url(array('advanced-search')) . '">' . $msg_public_header5 . '</a>' : $mc_search[2]),
  str_replace('{count}', $pCount, $public_search23)
);

// Search text..
$txtSearchKeysText = (isset($_SESSION['store_SearchResults']) ? $_SESSION['store_SearchResults'] : '');
if (isset($_SESSION['store_SearchResultsPricePoints'])) {
  $txtSearchKeysText = $_SESSION['store_SearchResultsPricePoints'];
}

// For slider..
$slider = ($SETTINGS->searchSlider ? unserialize($SETTINGS->searchSlider) : array());

// Left menu boxes..
include(PATH . 'control/left-box-controller.php');

// Structured data..
$mc_structUrl   = $surl;
$mc_structTitle = $headerTitleText;

include(PATH . 'control/header.php');

$tpl = mc_getSavant();
$tpl->assign('TEXT', array(
  str_replace(array(
    '{count}',
    '{keys}'
  ), array(
    $pCount,
    $txtSearchKeysText
  ), ($txtSearchKeysText ? $public_search : $public_search23)),
  $public_search2,
  $public_search14,
  $public_search15,
  $stext,
  $msg_public_header5,
  array_map('mc_filterJS', $mc_search),
  $public_search12,
  $mc_category[1],
  $mc_leftmenu[1]
));
$tpl->assign('ERROR_JS', array_map('mc_filterJS', array(
  $msg_javascript406
)));
$tpl->assign('CATEGORIES', $MCMENUCLS->filter_cats($mc_leftmenu));
$tpl->assign('FILTER_OPTIONS', $MCSYS->orderBy($orderByItems));
$tpl->assign('SEARCH_RESULTS', $MCPROD->productList('search'));
$tpl->assign('PAGINATION', (isset($pNumbers) ? $pNumbers : ''));
$tpl->assign('ADVANCED_SEARCH_URL', $MCRWR->url(array('advanced-search')));
$tpl->assign('IS_LOGGED_IN', (!empty($loggedInUser) && isset($loggedInUser['id']) ? 'yes' : 'no'));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/search.tpl.php');

include(PATH . 'control/footer.php');

?>