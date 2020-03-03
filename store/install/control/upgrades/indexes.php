<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// MYSQL INDEXES
//==========================

$mysqlIndexes = array(

  // Table: accounts
  'accounts' => array(
    'em_index' => 'email',
    'nm_index' => 'name'
  ),

  // Table: accounts_search
  'accounts_search' => array(
    'code_index' => 'code'
  ),

  // Table: accounts_wish
  'accounts_wish' => array(
    'account_index' => 'account'
  ),

  // Table: activation_history
  'activation_history' => array(
    'saleid_index' => 'saleID'
  ),

  // Table: addressbook
  'addressbook' => array(
    'ac_index' => 'account'
  ),

  // Table: attachments
  'attachments' => array(
    'status_index' => 'statusID',
    'sale_index' => 'saleID'
  ),

  // Table: attributes
  'attributes' => array(
    'prod_index' => 'productID',
    'group_index' => 'attrGroup'
  ),

  // Table: attr_groups
  'attr_groups' => array(
    'prod_index' => 'productID'
  ),

  // Table: campaigns
  'campaigns' => array(
    'code_index' => 'cDiscountCode'
  ),

  // Table: categories
  'categories' => array(
    'cat_index' => 'catLevel',
    'child_index' => 'childOf',
    'en_index' => 'enCat'
  ),

  // Table: click_history
  'click_history' => array(
    'saleid_index' => 'saleID'
  ),

  // Table: comparisons
  'comparisons' => array(
    'sale_index' => 'saleID',
    'this_index' => 'thisProduct',
    'that_index' => 'thatProduct'
  ),

  // Table: coupons
  'coupons' => array(
    'code_index' => 'cDiscountCode',
    'sale_index' => 'saleID'
  ),

  // Table: entry_log
  'entry_log' => array(
    'id_index' => 'userid'
  ),

  // Table: flat
  'flat' => array(
    'zone_index' => 'inZone'
  ),

  // Table: giftcodes
  'giftcodes' => array(
    'gift_index' => 'giftID',
    'sale_index' => 'saleID',
    'code_index' => 'code',
    'purc_index' => 'purchaseID'
  ),

  // Table: methods_params
  'methods_params' => array(
    'mthd_index' => 'method'
  ),

  // Table: mp3
  'mp3' => array(
    'prod_index' => 'product_id'
  ),

  // Table: paystatuses
  'paystatuses' => array(
    'mthd_index' => 'pMethod'
  ),

  // Table: per
  'per' => array(
    'zone_index' => 'inZone'
  ),

  // Table: percent
  'percent' => array(
    'zone_index' => 'inZone',
    'from_index' => 'priceFrom',
    'to_index' => 'priceTo',
    'en_index' => 'enabled'
  ),

  // Table: personalisation
  'personalisation' => array(
    'product_index' => 'productID'
  ),

  // Table: pictures
  'pictures' => array(
    'product_index' => 'product_id'
  ),

  // Table: price_points
  'price_points' => array(
    'from_index' => 'priceFrom',
    'to_index' => 'priceTo'
  ),

  // Table: products
  'products' => array(
    'pDownload' => 'pDownload',
    'code_index' => 'pCode',
    'name_index' => 'pName',
    'stock_index' => 'pStock',
    'en_index' => 'pEnable',
    'wght_index' => 'pWeight',
    'price_index' => 'pPrice',
    'cost_index' => 'pPurPrice'
  ),

  // Table: prod_category
  'prod_category' => array(
    'prod_index' => 'product',
    'cat_index' => 'category'
  ),

  // Table: prod_relation
  'prod_relation' => array(
    'prod_index' => 'product',
    'rel_index' => 'related'
  ),

  // Table: purchases
  'purchases' => array(
    'saleid_index' => 'saleID',
    'product_index' => 'productID',
    'cat_index' => 'categoryID',
    'conf_index' => 'saleConfirmation',
    'dcode_index' => 'downloadCode',
    'ld_index' => 'liveDownload'
  ),

  // Table: purch_atts
  'purch_atts' => array(
    'saleid_index' => 'saleID',
    'prodid_index' => 'productID',
    'purid_index' => 'purchaseID',
    'attid_index' => 'attributeID'
  ),

  // Table: purch_pers
  'purch_pers' => array(
    'saleid_index' => 'saleID',
    'prod_index' => 'productID',
    'purc_index' => 'purchaseID',
    'pers_index' => 'personalisationID'
  ),

  // Table: rates
  'rates' => array(
    'from_index' => 'rWeightFrom',
    'to_index' => 'rWeightTo'
  ),

  // Table: sales
  'sales' => array(
    'code_index' => 'buyCode',
    'conf_index' => 'saleConfirmation',
    'acc_index' => 'account'
  ),

  // Table: search_index
  'search_index' => array(
    'code_index' => 'searchCode'
  ),

  // Table: services
  'services' => array(
    'zone_index' => 'inZone'
  ),

  // Table: social
  'social' => array(
    'descK' => 'desc'
  ),

  // Table: statuses
  'statuses' => array(
    'saleid_index' => 'saleID'
  ),

  // Table: tare
  'tare' => array(
    'from_index' => 'rWeightFrom',
    'to_index' => 'rWeightTo'
  ),

  // Table: themes
  'themes' => array(
    'from_index' => 'from',
    'to_index' => 'to'
  ),

  // Table: tracker
  'tracker' => array(
    'code' => 'code'
  ),

  // Table: tracker_clicks
  'tracker_clicks' => array(
    'code' => 'code'
  ),

  // Table: zones
  'zones' => array(
    'ctry_index' => 'zCountry'
  ),

  // Table: zone_areas
  'zone_areas' => array(
    'zone_index' => 'inZone',
    'ctry_index' => 'zCountry'
  )

);

foreach ($mysqlIndexes AS $table => $data) {
  foreach ($data AS $name => $col) {
    if (mswCheckIndex($table, $name) == 'no') {
      @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . $table . "` add index `" . $name . "` (`" . $col . "`)");
      mc_upgradeLog('Completed: altered table `' . DB_PREFIX . $table . '` added index: ' . $name);
    }
  }
}

?>