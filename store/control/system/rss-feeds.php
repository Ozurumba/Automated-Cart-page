<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Are feeds enabled..
if ($SETTINGS->en_rss == 'no') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

include(MCLANG . 'product.php');
include(MCLANG . 'brands.php');

include(PATH . 'control/classes/class.rss.php');

$rssFeed       = '';
$RSS           = new rssFeed();
$RSS->prefix   = DB_PREFIX;
$RSS->settings = $SETTINGS;
$RSS->rwr      = $MCRWR;

// Brand feed..
if (isset($_GET['brss'])) {
  // Does brand have multiples..
  if (strpos($_GET['brss'], '_') !== false) {
    $chop          = explode('_', $_GET['brss']);
    $multiBuildUrl = $_GET['brss'];
    $_GET['brss'] = $chop[0];
  } else {
    $multiBuildUrl = $_GET['brss'];
  }
  mc_checkDigit($_GET['brss']);
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['rsb'] . '/' . $_GET['brss'],
    'brss=' . $_GET['brss']
  ));
  $RSS->thisFeedUrl = $url;
  $BRAND            = mc_getTableData('brands', 'id', (int) $_GET['brss']);
  if (!isset($BRAND->name)) {
    include(PATH . 'control/system/headers/404.php');
    exit;
  }
  $rssFeed = $RSS->openChannel();
  $rssFeed .= $RSS->feedInfo(str_replace(array(
    '{website}',
    '{brand}'
  ), array(
    $SETTINGS->website,
    $BRAND->name
  ), $public_brands), $url, RSS_BUILD_DATE_FORMAT, str_replace("{website_name}", $SETTINGS->website, $msg_script24), $SETTINGS->website);
  $rssFeed .= $RSS->getBrands(RSS_BUILD_DATE_FORMAT, $multiBuildUrl);
  $rssFeed .= $RSS->closeChannel();
  header('Content-Type: text/xml');
  echo mc_cleanData(trim($rssFeed));
}

// Category feed..
if (isset($_GET['crss'])) {
  mc_checkDigit($_GET['crss']);
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['rsc'] . '/' . $_GET['crss'],
    'crss=' . $_GET['crss']
  ));
  $RSS->thisFeedUrl = $url;
  $CAT              = mc_getTableData('categories', 'id', (int) $_GET['crss']);
  if (!isset($CAT->catname)) {
    include(PATH . 'control/system/headers/404.php');
    exit;
  }
  $rssFeed = $RSS->openChannel();
  $rssFeed .= $RSS->feedInfo(str_replace(array(
    '{website_name}',
    '{category}'
  ), array(
    $SETTINGS->website,
    $CAT->catname
  ), $msg_script25), $url, RSS_BUILD_DATE_FORMAT, str_replace("{website_name}", $SETTINGS->website, $msg_script24), $SETTINGS->website);
  $rssFeed .= $RSS->getLatestCatProducts(RSS_BUILD_DATE_FORMAT, (int) $_GET['crss']);
  $rssFeed .= $RSS->closeChannel();
  header('Content-Type: text/xml');
  echo mc_cleanData(trim($rssFeed));
}

// Category/latest/specials feeds..
if (isset($_GET['rss'])) {
  $_GET['cat'] = (strpos($_GET['rss'], '-') !== FALSE ? substr($_GET['rss'], strpos($_GET['rss'], '-') + 1, strlen($_GET['rss'])) : '0');
  if ($_GET['cat'] > 0) {
    mc_checkDigit($_GET['cat']);
    $CAT = mc_getTableData('categories', 'id', (int) $_GET['cat']);
    if (!isset($CAT->catname)) {
      include(PATH . 'control/system/headers/404.php');
      exit;
    }
  }
  switch(substr($_GET['rss'], 0, 6)) {
    case 'latest':
      $url = $MCRWR->url(array(
        $MCRWR->config['slugs']['rsl'] . '/' . ($_GET['cat'] > 0 ? $_GET['cat'] : '0'),
        'rss=latest' . ($_GET['cat'] > 0 ? '-' . $_GET['cat'] : '0')
      ));
      $RSS->thisFeedUrl = $url;
      $rssFeed          = $RSS->openChannel();
      $rssFeed .= $RSS->feedInfo(str_replace(array(
        '{website_name}',
        '{category}'
      ), array(
        $SETTINGS->website,
        ''
      ), $msg_script31), $url, RSS_BUILD_DATE_FORMAT, str_replace("{website_name}", $SETTINGS->website, $msg_script24), $SETTINGS->website);
      $rssFeed .= $RSS->getLatestProducts(RSS_BUILD_DATE_FORMAT, $_GET['cat']);
      $rssFeed .= $RSS->closeChannel();
      header('Content-Type: text/xml');
      echo mc_cleanData(trim($rssFeed));
      break;
    case 'specia':
      $url = $MCRWR->url(array(
        $MCRWR->config['slugs']['rss'] . '/' . ($_GET['cat'] > 0 ? $_GET['cat'] : '0'),
        'rss=special' . ($_GET['cat'] > 0 ? '-' . $_GET['cat'] : '0')
      ));
      $RSS->thisFeedUrl = $url;
      $rssFeed          = $RSS->openChannel();
      $rssFeed .= $RSS->feedInfo(str_replace(array(
        '{website_name}',
        '{category}'
      ), array(
        $SETTINGS->website,
        ''
      ), $msg_script32), $url, RSS_BUILD_DATE_FORMAT, str_replace("{website_name}", $SETTINGS->website, $msg_script24), $SETTINGS->website);
      $rssFeed .= $RSS->getSpecialOfferProducts(RSS_BUILD_DATE_FORMAT, $_GET['cat']);
      $rssFeed .= $RSS->closeChannel();
      header('Content-Type: text/xml');
      echo mc_cleanData(trim($rssFeed));
      break;
  }
}

exit;

?>