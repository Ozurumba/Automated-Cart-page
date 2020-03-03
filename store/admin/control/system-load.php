<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// PERMISSION PROBLEM
if (isset($_GET['perms'])) {
  include(PATH . 'control/modules/system/access-denied.php');
  exit;
}

// AJAX PAGE DETECTION..
if (isset($_POST['create-tags'])) {
  $cmd = 'add-product';
}

// FOR SCRIPT CHECKS..
if (isset($_GET['restriction']) && LICENCE_VER == 'locked') {
  $cmd = 'main-stop';
}

// SET SALE VAR FOR LOGIN RELOAD..
if (isset($_GET['loadOrder']) && ctype_digit($_GET['loadOrder'])) {
  $_SESSION['loadOrder'] = $_GET['loadOrder'];
}

// PACKING SLIP PDF
if (isset($_GET['pdf-slip'])) {
  include(PATH . 'control/modules/system/pdf-packing-slip.php');
  exit;
}

// CLEAR FILE CACHE
if (!in_array($cmd, array(
  'add-product',
  'product-attributes'
))) {
  $MCCACHE->clear_cache_file('ad-local-file-dir');
}

// PERMISSIONS..
if (!in_array($cmd, array(
  'main',
  'users',
  'settings',
  'pages',
  'purchase',
  'main-stop'
))) {
  mc_pagePermissions($cmd);
}

// PURCHASE..
if (isset($_GET['purchaseRedir'])) {
  $cmd = 'purchase';
}

// DELETE PERMISSIONS..
$uDel = mc_deletePermissions();

// SYSTEM..
if (in_array($cmd, array(
  'main',
  'main-stop',
  'purchase',
  'settings',
  'newpages',
  'login',
  'logout',
  'users',
  'currency-converter',
  'news',
  'themes',
  'blog',
  'globsearch',
  'left-boxes'
))) {
  mc_isWebmasterLoggedIn($sysCartUser, $cmd == 'login' ? true : false);
  include(PATH . 'control/modules/system/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// SHIPPING..
if (in_array($cmd, array(
  'countries',
  'zones',
  'services',
  'rates',
  'update-rates',
  'update-tax',
  'flatrate',
  'percent',
  'tare',
  'itemrate',
  'drop',
  'shipping',
  'qtyrates'
))) {
  mc_isWebmasterLoggedIn($sysCartUser);
  include(PATH . 'control/modules/shipping/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// CATALOGUE..
if (in_array($cmd, array(
  'add-product',
  'product-csv',
  'discount-coupons',
  'coupon-report',
  'product-related',
  'order-statuses',
  'payment-methods',
  'load-related-products',
  'brands',
  'categories',
  'price-points',
  'product-attributes',
  'special-offers',
  'manage-products',
  'product-pictures',
  'product-mp3',
  'product-import',
  'product-attributes-import',
  'product-personalisation',
  'dl-manager',
  'copy-attributes',
  'product-export',
  'product-batch-update',
  'gift',
  'gift-report'
))) {
  mc_isWebmasterLoggedIn($sysCartUser);
  include(PATH . 'control/modules/catalogue/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// SALES..
if (in_array($cmd, array(
  'sales',
  'sales-incomplete',
  'sales-view',
  'downloads',
  'add',
  'add-manual',
  'sales-view-recal',
  'sales-search',
  'sales-update',
  'sales-export',
  'sales-add',
  'sales-export-buyers',
  'sales-product-overview',
  'invoice',
  'packing-slip',
  'sales-trends',
  'sales-statuses',
  'sales-revenue',
  'sales-batch',
  'gift-overview'
))) {
  mc_isWebmasterLoggedIn($sysCartUser);
  include(PATH . 'control/modules/sales/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// BUYER ACCOUNTS..
if (in_array($cmd, array(
  'accounts',
  'taccounts',
  'mail-accounts',
  'add-account',
  'acc-import',
  'wishlist',
  'daccounts'
))) {
  mc_isWebmasterLoggedIn($sysCartUser, $cmd == 'login' ? true : false);
  include(PATH . 'control/modules/accounts/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// TOOLS..
if (in_array($cmd, array(
  'update-prices',
  'update-prices-csv',
  'update-stock',
  'update-stock-csv',
  'batch-move',
  'hit-count-overview',
  'stats',
  'entry-log',
  'search-log',
  'newsletter',
  'newsletter-mail',
  'newsletter-templates',
  'low-stock-export',
  'product-status',
  'backup',
  'marketing',
  'stock-overview'
))) {
  mc_isWebmasterLoggedIn($sysCartUser);
  include(PATH . 'control/modules/tools/' . (isset($childController[$cmd]) ? $childController[$cmd] : $cmd) . '.php');
  exit;
}

// AJAX..
if (in_array($cmd, array(
  'ajax-ops',
  'manlist',
  'refresh-folders',
  'refresh-folders-cats'
))) {
  include(PATH . 'control/modules/ajax.php');
  exit;
}

include(PATH . 'control/modules/header/403.php');

?>