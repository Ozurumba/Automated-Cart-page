<?php

//================================
// HELP PAGE LINK CONVERTERS
//================================

$helpPages = array(
  'main' => 'admin_index',
  // SYSTEM..
  'settings' => 'system-1',
  'settings2' => 'settings-3',
  'settings3' => 'settings-1',
  'settings4' => 'settings-5',
  'settings5' => 'settings-6',
  'settings6' => 'settings-7',
  'settings7' => 'settings-8',
  'settings8' => 'settings-2',
  'settings9' => 'settings-4',
  'currency-converter' => 'system-3',
  'newpages' => 'system-4',
  'users' => 'system-2',
  'news' => 'system-5',
  'themes' => 'system-6',
  'globsearch' => 'system-7',
  'blog' => 'system-8',
  'left-boxes' => 'system-9',
  // CATALOGUE..
  'add-product' => 'catalogue-1',
  'manage-products' => 'catalogue-2',
  'gift' => 'catalogue-17',
  'gift-report' => 'catalogue-19',
  'dl-manager' => 'catalogue-12',
  'product-attributes' => 'product-info2',
  'copy-attributes' => 'product-info3',
  'product-pictures' => 'product-info1',
  'product-mp3' => 'product-info5',
  'product-related' => 'product-info4',
  'product-import' => 'catalogue-10',
  'product-batch-update' => 'catalogue-16',
  'product-attributes-import' => 'catalogue-10',
  'categories' => 'catalogue-3',
  'brands' => 'catalogue-4',
  'price-points' => 'catalogue-5',
  'payment-methods' => 'payment-options',
  'order-statuses' => 'catalogue-7',
  'special-offers' => 'catalogue-8',
  'discount-coupons' => 'catalogue-9',
  'coupon-report' => 'catalogue-9',
  'product-personalisation' => 'product-info6',
  'product-export' => 'catalogue-15',
  // SHIPPING..
  'countries' => 'shipping-1',
  'zones' => 'shipping-2',
  'services' => 'shipping-3',
  'shipping' => 'shipping',
  'rates' => 'shipping-4',
  'tare' => 'shipping-10',
  'update-rates' => 'shipping-5',
  'update-tax' => 'shipping-6',
  'flatrate' => 'shipping-8',
  'itemrate' => 'shipping-11',
  'percent' => 'shipping-9',
  'qtyrates' => 'shipping-12',
  'drop' => 'dropship',
  // SALES..
  'sales' => 'sales-1',
  'downloads' => 'sales-10',
  'sales-incomplete' => 'sales-15',
  'sales-add' => 'sales-18',
  'sales-product-overview' => 'sales-2',
  'gift-overview' => 'sales-12',
  'sales-search' => 'sales-3',
  'sales-trends' => 'sales-6',
  'sales-export' => 'sales-17',
  'sales-export-buyers' => 'sales-5',
  'sales-update' => 'sales-7',
  'sales-batch' => 'sales-16',
  'sales-view' => 'sales-8',
  'sales-revenue' => 'sales-11',
  'sales-statuses' => 'sales-14',
  // ACCOUNTS..
  'add-account' => 'account-1',
  'accounts' => 'account-2',
  'taccounts' => 'account-3',
  'daccounts' => 'account-4',
  'mail-accounts' => 'account-5',
  'wishlist' => 'account-6',
  'acc-import' => 'account-7',
  // TOOLS..
  'update-prices' => 'tools-1',
  'update-prices-csv' => 'tools-14',
  'update-stock' => 'tools-2',
  'update-stock-csv' => 'tools-13',
  'low-stock-export' => 'tools-9',
  'product-status' => 'tools-10',
  'batch-move' => 'tools-3',
  'hit-count-overview' => 'tools-4',
  'stock-overview' => 'tools-12',
  'newsletter' => 'tools-8',
  'newsletter-mail' => 'tools-15',
  'newsletter-templates' => 'tools-16',
  'stats' => 'tools-5',
  'entry-log' => 'tools-6',
  'search-log' => 'tools-7',
  'backup' => 'tools-11',
  'marketing' => 'marketing'
);

// HELP PAGE - OTHER..
if (isset($_GET['p']) && $_GET['p'] == 'special-offers' && isset($_GET['view'])) {
  $helpPages['special-offers'] = 'catalogue-20';
}
if (isset($_GET['p']) && $_GET['p'] == 'coupon-report' && isset($_GET['code'])) {
  $helpPages['coupon-report'] = 'catalogue-21';
}
if (isset($_GET['p']) && $_GET['p'] == 'add' && isset($_GET['sale']) && isset($_GET['type'])) {
  $helpPages['add'] = 'sales-4';
}
if (isset($_GET['p']) && $_GET['p'] == 'gift' && isset($_GET['purID']) && isset($_GET['viewSaleGift'])) {
  $helpPages['gift'] = 'sales-13';
}
if (isset($_GET['p']) && $_GET['p'] == 'sales' && isset($_GET['ordered'])) {
  $helpPages['sales'] = 'sales-9';
}
if (isset($_GET['p']) && $_GET['p'] == 'sales-view' && isset($_GET['stock_adj'])) {
  $helpPages['sales-view'] = 'sales-19';
}
if (isset($_GET['p']) && $_GET['p'] == 'add-product' && isset($_GET['edit']) && $_GET['edit'] == 'batch-mode') {
  $helpPages['add-product'] = 'catalogue-6';
}
if (isset($_GET['p']) && $_GET['p'] == 'payment-methods' && isset($_GET['conf'])) {
  if (in_array($_GET['conf'], array_keys($mcSystemPaymentMethods))) {
    $helpPages['payment-methods'] = $mcSystemPaymentMethods[$_GET['conf']]['docs'];
  }
}
if (isset($_GET['bbCode'])) {
  $helpPages['main'] = 'features-6';
}

if (isset($_GET['versionCheck'])) {
  $helpPages['main'] = 'vc';
}

//============================
// CHILDREN OF MAIN PAGES
//============================

$childController = array(
  'load-related-products' => 'product-related',
  'coupon-report' => 'discount-coupons',
  'downloads' => 'sales-view',
  'add' => 'sales-view',
  'add-manual' => 'sales-add',
  'sales-view-recal' => 'sales-view',
  'gift' => 'gift-certificates',
  'gift-report' => 'gift-certificates',
  'login' => 'portal',
  'logout' => 'portal',
  'product-attributes-import' => 'product-import',
  'copy-attributes' => 'product-attributes',
  'packing-slip' => 'invoice',
  'sales-statuses' => 'sales-update',
  'main-stop' => 'main',
  'newsletter-mail' => 'newsletter',
  'newsletter-templates' => 'newsletter',
  'update-prices-csv' => 'update-prices',
  'update-stock-csv' => 'update-stock'
);

//======================================
// SKRILL - SUPPORTED LANGUAGES
//======================================

$skrillLanguages = array(
  'CN' => 'Chinese',
  'CZ' => 'Czech',
  'DA' => 'Danish',
  'NL' => 'Dutch',
  'EN' => 'English',
  'FI' => 'Finnish',
  'FR' => 'French',
  'DE' => 'German',
  'GR' => 'Greek',
  'IT' => 'Italian',
  'PL' => 'Polish',
  'RO' => 'Romanian',
  'RU' => 'Russian',
  'ES' => 'Spanish',
  'SV' => 'Swedish',
  'TR' => 'Turkish'
);

//===================================
// 2CHECKOUT - SUPPORTED LANGUAGES
//===================================

$twoCheckoutLanguages = array(
  'EN' => 'English',
  'ZH' => 'Chinese',
  'DA' => 'Danish',
  'NL' => 'Dutch',
  'FR' => 'French',
  'GR' => 'German',
  'EL' => 'Greek',
  'IT' => 'Italian',
  'JP' => 'Japanese',
  'NO' => 'Norwegian',
  'PT' => 'Portuguese',
  'SL' => 'Slovenian',
  'SP' => 'Spanish'
);

//===================================
// STRIPE - SUPPORTED LOCALE
//===================================

$stripeLocale = array(
  'auto' => 'Auto Detection (Recommended)',
  'da' => 'Danish',
  'nl' => 'Dutch',
  'en' => 'English',
  'fi' => 'Finnish',
  'fr' => 'French',
  'de' => 'German',
  'it' => 'Italian',
  'ja' => 'Japanese',
  'no' => 'Norwegian',
  'zh' => 'Simplified Chinese',
  'es' => 'Spanish',
  'sv' => 'Swedish'
);

//===================================
// ICEPAY - SUPPORTED LANGUAGES
//===================================

$icePayLanguages = array(
  'EN' => 'English',
  'FR' => 'French',
  'DE' => 'German',
  'ES' => 'Spanish',
  'NL' => 'Dutch'
);

//===================================
// CCNOW - SUPPORTED LANGUAGES
//===================================

$ccNowLanguages = array(
  'en' => 'English',
  'fr' => 'French',
  'de' => 'German',
  'it' => 'Italian',
  'es' => 'Spanish'
);

//============================================
// PAYTRAIL - SUPPORTED LANGUAGES
//============================================

$payTrailLanguages = array(
  'en_US' => 'English',
  'fi_FI' => 'Finnish',
  'sv_SE' => 'Swedish'
);

//========================================
// SOCIAL
// Font awesome icon, name, website
//========================================

$socialSites = array(
  array('facebook','Facebook','https://www.facebook.com'),
  array('twitter','Twitter','https://www.twitter.com'),
  array('instagram','Instagram','https://www.instagram.com'),
  array('youtube','YouTube','https://www.youtube.com'),
  array('reddit','Reddit','https://www.reddit.com'),
  array('pinterest','Pinterest','https://www.pinterest.com'),
  array('flickr','Flickr','https://www.flickr.com')
);

?>