<?php if (!defined('PATH') || !isset($sysCartUser)) { exit; }

$slidePanelLeftMenu = array();
$footerSlideMenu    = '';

//--------------
// System
//--------------

$systemMenuArr = array(
  'settings_1','settings_5','settings_6','settings_7',
  'users','currency-converter','newpages','news','left-boxes'
);
$mR1         = array_intersect($systemMenuArr, $sysCartUser[3]);

if (!empty($mR1) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['system']          = array($msg_header, 'cog');
  $slidePanelLeftMenu['system']['links'] = array();

  // General settings..
  if (in_array('settings_1', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=settings',
      'name' => mc_cleanData($msg_javascript106)
    );
  }

  // Company information..
  if (in_array('settings_5', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=settings&amp;s=5',
      'name' => mc_cleanData($msg_settings131)
    );
  }

  // SMTP settings..
  if (in_array('settings_6', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=settings&amp;s=6',
      'name' => mc_cleanData($msg_settings25)
    );
  }

  // User management..
  if (in_array('users', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=users',
      'name' => mc_cleanData($msg_javascript254)
    );
  }

  // Currency converter..
  if (in_array('currency-converter', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=currency-converter',
      'name' => mc_cleanData($msg_javascript30)
    );
  }

  // New pages..
  if (in_array('newpages', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=newpages',
      'name' => mc_cleanData($msg_javascript198)
    );
  }

  // Left box controller..
  if (in_array('left-boxes', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=left-boxes',
      'name' => mc_cleanData($msg_settings208)
    );
  }

  // News ticker..
  if (in_array('news', $mR1) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['system']['links'][] = array(
      'url' => '?p=news',
      'name' => mc_cleanData($msg_javascript421)
    );
  }

  // Edit footers..
  if (LICENCE_VER=='unlocked') {
    if (in_array('settings_7', $mR1) || $sysCartUser[1] != 'restricted') {
      $slidePanelLeftMenu['system']['links'][] = array(
        'url' => '?p=settings&amp;s=7',
        'name' => mc_cleanData($msg_settings65)
      );
    }
  }

}

//-----------------
// Store settings
//-----------------

$storeSetMenuArr = array(
  'settings_2','settings_3','settings_4','settings_8','settings_9','themes','blog'
);
$mR1b         = array_intersect($storeSetMenuArr, $sysCartUser[3]);

if (!empty($mR1b) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['store']          = array($mc_admin[0], 'cogs');
  $slidePanelLeftMenu['store']['links'] = array();

  // Global store settings
  if (in_array('settings_3', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=settings&amp;s=3',
      'name' => mc_cleanData($msg_settings50)
    );
  }

  // Store online / offline..
  if (in_array('settings_8', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=settings&amp;s=8',
      'name' => mc_cleanData($msg_settings84)
    );
  }

  // Banners..
  if (in_array('settings_9', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=settings&amp;s=9',
      'name' => mc_cleanData($msg_settings116)
    );
  }

  // Homepage blog..
  if (in_array('blog', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=blog',
      'name' => mc_cleanData($msg_admin3_0[56])
    );
  }

  // Homepage product options..
  if (in_array('settings_4', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=settings&amp;s=4',
      'name' => mc_cleanData($msg_settings54)
    );
  }

  // Themes..
  if (in_array('themes', $mR1b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['store']['links'][] = array(
      'url' => '?p=themes',
      'name' => mc_cleanData($msg_header22)
    );
  }

}

//---------------
// Catalogue
//---------------

$catalogMenuArr = array(
  'add-product','manage-products','dl-manager','categories','brands',
  'price-points','product-import','product-batch-update','product-export','product-attributes-import'
);
$mR2            = array_intersect($catalogMenuArr, $sysCartUser[3]);

if (!empty($mR2) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['catalog']          = array($msg_header2, 'file-text-o');
  $slidePanelLeftMenu['catalog']['links'] = array();

  // Add product..
  if (in_array('add-product', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=add-product',
      'name' => mc_cleanData($msg_javascript26)
    );
  }

  // Batch import products from csv..
  if (in_array('product-import', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=product-import',
      'name' => mc_cleanData($msg_productadd3)
    );
  }

  // Manage products..
  if (in_array('manage-products', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=manage-products',
      'name' => mc_cleanData($msg_javascript27)
    );
  }

  // Download manager..
  if (in_array('dl-manager', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=dl-manager',
      'name' => mc_cleanData($msg_javascript398)
    );
  }

  // Categories..
  if (in_array('categories', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=categories',
      'name' => mc_cleanData($msg_javascript157)
    );
  }

  // Brands..
  if (in_array('brands', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=brands',
      'name' => mc_cleanData($msg_javascript158)
    );
  }

  // Price points..
  if (in_array('price-points', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=price-points',
      'name' => mc_cleanData($msg_javascript266)
    );
  }

  // Batch export products to csv..
  if (in_array('product-export', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=product-export',
      'name' => mc_cleanData($msg_productmanage59)
    );
  }

  // Batch update products from csv..
  if (in_array('product-batch-update', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=product-batch-update',
      'name' => mc_cleanData($msg_productmanage66)
    );
  }

  // Batch import attributes from CSV
  if (in_array('product-attributes-import', $mR2) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['catalog']['links'][] = array(
      'url' => '?p=product-attributes-import',
      'name' => mc_cleanData($msg_admin3_0[16])
    );
  }

}

//--------------------
// Discount options
//--------------------

$discountMenuArr = array(
  'gift','special-offers','discount-coupon','gift-overview'
);
$mR2b            = array_intersect($discountMenuArr, $sysCartUser[3]);

if (!empty($mR2b) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['discount']          = array($mc_admin[1], 'gift');
  $slidePanelLeftMenu['discount']['links'] = array();

  // Gift coupons..
  if (in_array('gift', $mR2b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['discount']['links'][] = array(
      'url' => '?p=gift',
      'name' => mc_cleanData($msg_header23)
    );
  }

  // Gift certificate overview..
  if (in_array('gift-overview', $mR2b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['discount']['links'][] = array(
      'url' => '?p=gift-overview',
      'name' => mc_cleanData($msg_header24)
    );
  }

  // Special offers..
  if (in_array('special-offers', $mR2b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['discount']['links'][] = array(
      'url' => '?p=special-offers',
      'name' => mc_cleanData($msg_javascript62)
    );
  }

  // Discount coupons..
  if (in_array('discount-coupons', $mR2b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['discount']['links'][] = array(
      'url' => '?p=discount-coupons',
      'name' => mc_cleanData($msg_javascript29)
    );
  }

}

//---------------
// Shipping
//---------------

$shipMenuArr = array('countries','zones','services','update-rates','update-tax','shipping','tare','drop');
$mR3         = array_intersect($shipMenuArr, $sysCartUser[3]);

if (!empty($mR3) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['shipping']          = array($msg_header4, 'truck');
  $slidePanelLeftMenu['shipping']['links'] = array();

  // Shipping countries..
  if (in_array('countries', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=countries',
      'name' => mc_cleanData($msg_javascript31)
    );
  }

  // Shipping zones..
  if (in_array('zones', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=zones',
      'name' => mc_cleanData($msg_javascript32)
    );
  }

  // Shipping services..
  if (in_array('services', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=services',
      'name' => mc_cleanData($msg_javascript100)
    );
  }

  // Shipping rates..
  if (in_array('shipping', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=shipping',
      'name' => mc_cleanData($msg_admin3_0[54])
    );
  }

  // Tare weight..
  if (in_array('tare', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=tare',
      'name' => mc_cleanData($msg_header20)
    );
  }

  // Drop shippers..
  if (in_array('drop', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=drop',
      'name' => mc_cleanData($msg_admin3_0[48])
    );
  }

  // Batch update rates..
  if (in_array('update-rates', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=update-rates',
      'name' => mc_cleanData($msg_javascript173)
    );
  }

  // Batch update tax..
  if (in_array('update-tax', $mR3) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['shipping']['links'][] = array(
      'url' => '?p=update-tax',
      'name' => mc_cleanData($msg_javascript283)
    );
  }

}

//--------------------
// Sales
//--------------------

$salesMenuArr = array(
  'sales','sales-incomplete','sales-add','order-statuses','sales-search','payment-methods',
  'sales-product-overview','sales-revenue','sales-trends','sales-export','sales-export-buyers'
);
$mR4          = array_intersect($salesMenuArr, $sysCartUser[3]);

if (!empty($mR4) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['sales']          = array($msg_header5, 'shopping-basket');
  $slidePanelLeftMenu['sales']['links'] = array();

  // Payment methods..
  if (in_array('payment-methods', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=payment-methods',
      'name' => mc_cleanData($msg_javascript168)
    );
  }

  // Sales..
  if (in_array('sales', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales',
      'name' => mc_cleanData($msg_javascript84)
    );
  }

  // Incomplete sales..
  if (in_array('sales-incomplete', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-incomplete',
      'name' => mc_cleanData($msg_header21)
    );
  }

  // Add sale..
  if (in_array('sales-add', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-add',
      'name' => mc_cleanData($msg_javascript355)
    );
  }

  // Sales search..
  if (in_array('sales-search', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-search',
      'name' => mc_cleanData($msg_javascript111)
    );
  }

  // Order statuses..
  if (in_array('order-statuses', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=order-statuses',
      'name' => mc_cleanData($msg_javascript192)
    );
  }

  // Sales overview..
  if (in_array('sales-product-overview', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-product-overview',
      'name' => mc_cleanData($msg_javascript163)
    );
  }

  // Sales revenue..
  if (in_array('sales-revenue', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-revenue',
      'name' => mc_cleanData($msg_javascript425)
    );
  }

  // Sales trends..
  if (in_array('sales-trends', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-trends',
      'name' => mc_cleanData($msg_stats21)
    );
  }

  // Export sales..
  if (in_array('sales-export', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-export',
      'name' => mc_cleanData($msg_javascript129)
    );
  }

  // Export buyers..
  if (in_array('sales-export-buyers', $mR4) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['sales']['links'][] = array(
      'url' => '?p=sales-export-buyers',
      'name' => mc_cleanData($msg_javascript134)
    );
  }

}

//--------------------
// Accounts
//--------------------

$accMenuArr = array(
  'accounts','mail-accounts','add-account','wishlist','taccounts','daccounts','acc-import'
);
$mR4ac          = array_intersect($accMenuArr, $sysCartUser[3]);

if (!empty($mR4ac) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['accounts']          = array($msg_admin3_0[40], 'users');
  $slidePanelLeftMenu['accounts']['links'] = array();

  // Add cccount..
  if (in_array('add-account', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=add-account',
      'name' => mc_cleanData($msg_admin3_0[41])
    );
  }

  // Accounts..
  if (in_array('accounts', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=accounts',
      'name' => mc_cleanData($msg_admin3_0[42])
    );
  }

  // Trade Accounts..
  if (in_array('taccounts', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=taccounts',
      'name' => mc_cleanData($msg_admin3_0[50])
    );
  }

  // Unverified / Disabled Accounts..
  if (in_array('daccounts', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=daccounts',
      'name' => mc_cleanData($msg_admin3_0[51])
    );
  }

  // Import accounts..
  if (in_array('acc-import', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=acc-import',
      'name' => mc_cleanData($msg_admin3_0[59])
    );
  }

  // Batch  accounts..
  if (in_array('mail-accounts', $mR4ac) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['accounts']['links'][] = array(
      'url' => '?p=mail-accounts',
      'name' => mc_cleanData($msg_admin3_0[43])
    );
  }

  // Wish list overview..
  if ($SETTINGS->en_wish == 'yes') {
    if (in_array('wishlist', $mR4ac) || $sysCartUser[1] != 'restricted') {
      $slidePanelLeftMenu['accounts']['links'][] = array(
        'url' => '?p=wishlist',
        'name' => mc_cleanData($msg_admin3_0[49])
      );
    }
  }

}

//-----------------
// Stock
//-----------------

$stockMenuArr = array(
  'update-stock','low-stock-export','stock-overview','update-stock-csv'
);
$mR4b          = array_intersect($stockMenuArr, $sysCartUser[3]);

if (!empty($mR4b) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['stock']          = array($msg_admin3_0[4], 'hourglass-3');
  $slidePanelLeftMenu['stock']['links'] = array();

  // Update stock levels..
  if (in_array('update-stock', $mR4b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['stock']['links'][] = array(
      'url' => '?p=update-stock',
      'name' => mc_cleanData($msg_javascript61)
    );
  }

  // Batch update stock from csv..
  if (in_array('update-stock-csv', $mR4b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['stock']['links'][] = array(
      'url' => '?p=update-stock-csv',
      'name' => mc_cleanData($msg_admin3_0[6])
    );
  }

  // Low stock export..
  if (in_array('low-stock-export', $mR4b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['stock']['links'][] = array(
      'url' => '?p=low-stock-export',
      'name' => mc_cleanData($msg_javascript319)
    );
  }

  // Stock overview..
  if (in_array('stock-overview', $mR4b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['stock']['links'][] = array(
      'url' => '?p=stock-overview',
      'name' => mc_cleanData($msg_header19)
    );
  }

}

//-----------------
// Tools
//-----------------

$toolsMenuArr = array(
  'update-prices','product-status','batch-move','backup','hit-count-overview',
  'newsletter','stats','marketing','update-prices-csv'
);
$mR5          = array_intersect($toolsMenuArr, $sysCartUser[3]);

if (!empty($mR5) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['tools']          = array($msg_header3, 'wrench');
  $slidePanelLeftMenu['tools']['links'] = array();

  // Update prices..
  if (in_array('update-prices', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=update-prices',
      'name' => mc_cleanData($msg_javascript60)
    );
  }

  // Batch update prices from csv..
  if (in_array('update-prices-csv', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=update-prices-csv',
      'name' => mc_cleanData($msg_admin3_0[5])
    );
  }

  // Batch enable / disable..
  if (in_array('product-status', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=product-status',
      'name' => mc_cleanData($msg_javascript338)
    );
  }

  // Move products..
  if (in_array('batch-move', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=batch-move',
      'name' => mc_cleanData($msg_javascript197)
    );
  }

  // Database backup..
  if (in_array('backup', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=backup',
      'name' => mc_cleanData($msg_javascript393)
    );
  }

  // Hit count overview..
  if (in_array('hit-count-overview', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=hit-count-overview',
      'name' => mc_cleanData($msg_javascript164)
    );
  }

  // Newsletter..
  if (in_array('newsletter', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=newsletter',
      'name' => mc_cleanData($msg_javascript309)
    );
  }

  // Stats..
  if (in_array('stats', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=stats',
      'name' => mc_cleanData($msg_javascript140)
    );
  }

  // Marketing Tracker..
  if (in_array('stats', $mR5) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['tools']['links'][] = array(
      'url' => '?p=marketing',
      'name' => mc_cleanData($msg_admin3_0[44])
    );
  }

}

//-----------------
// Logs
//-----------------

$logsMenuArr = array(
  'search-log','entry-log'
);
$mR5b          = array_intersect($logsMenuArr, $sysCartUser[3]);

if (!empty($mR5b) || $sysCartUser[1] != 'restricted') {

  $slidePanelLeftMenu['logs']          = array($mc_admin[2], 'pencil');
  $slidePanelLeftMenu['logs']['links'] = array();

  // Search log..
  if (in_array('search-log', $mR5b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['logs']['links'][] = array(
      'url' => '?p=search-log',
      'name' => mc_cleanData($msg_javascript108)
    );
  }

  // Entry log..
  if (in_array('entry-log', $mR5b) || $sysCartUser[1] != 'restricted') {
    $slidePanelLeftMenu['logs']['links'][] = array(
      'url' => '?p=entry-log',
      'name' => mc_cleanData($msg_javascript99)
    );
  }

}

?>