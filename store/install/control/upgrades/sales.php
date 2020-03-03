<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// SALES / PURCHASE DATA
//==========================

if (mswCheckColumn('sales', 'bill_1') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_1` varchar(250) not null default '' after `saleBuyerName`");
  mc_upgradeLog('Completed: bill_1 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_2') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_2` varchar(250) not null default '' after `bill_1`");
  mc_upgradeLog('Completed: bill_2 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_3') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_3` varchar(250) not null default '' after `bill_2`");
  mc_upgradeLog('Completed: bill_3 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_4') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_4` varchar(250) not null default '' after `bill_3`");
  mc_upgradeLog('Completed: bill_4 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_5') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_5` varchar(250) not null default '' after `bill_4`");
  mc_upgradeLog('Completed: bill_5 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_6') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_6` varchar(250) not null default '' after `bill_5`");
  mc_upgradeLog('Completed: bill_6 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_7') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_7` varchar(250) not null default '' after `bill_6`");
  mc_upgradeLog('Completed: bill_7 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_8') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_8` varchar(250) not null default '' after `bill_7`");
  mc_upgradeLog('Completed: bill_8 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'bill_9') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `bill_9` int(5) not null default '0' after `bill_8`");
  mc_upgradeLog('Completed: bill_9 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_1') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_1` varchar(250) not null default '' after `bill_9`");
  mc_upgradeLog('Completed: ship_1 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_2') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_2` varchar(250) not null default '' after `ship_1`");
  mc_upgradeLog('Completed: ship_2 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_3') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_3` varchar(250) not null default '' after `ship_2`");
  mc_upgradeLog('Completed: ship_3 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_4') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_4` varchar(250) not null default '' after `ship_3`");
  mc_upgradeLog('Completed: ship_4 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_5') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_5` varchar(250) not null default '' after `ship_4`");
  mc_upgradeLog('Completed: ship_5 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_6') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_6` varchar(250) not null default '' after `ship_5`");
  mc_upgradeLog('Completed: ship_6 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_7') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_7` varchar(250) not null default '' after `ship_6`");
  mc_upgradeLog('Completed: ship_7 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ship_8') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ship_8` varchar(250) not null default '' after `ship_7`");
  mc_upgradeLog('Completed: ship_8 column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'gateparams') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `gateparams` text default null");
  mc_upgradeLog('Completed: gateparams column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'codeType') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `codeType` varchar(20) not null default '' after `couponTotal`");
  mc_upgradeLog('Completed: codeType column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'ipAccess') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `ipAccess` text default null after `ipAddress`");
  mc_upgradeLog('Completed: ipAccess column added to `' . DB_PREFIX . 'sales` table');

  // Copy IP addresses to new access field..
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `ipAccess` = `ipAddress`");
  if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
    mc_upgradeLog('Completed: ipAccess fields updated with ipAddress data in `' . DB_PREFIX . 'sales` table');
  }
}
if (mswCheckColumn('sales', 'restrictCount') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `restrictCount` int(7) not null default '0' after `ipAccess`");
  mc_upgradeLog('Completed: restrictCount column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'chargeTotal') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `chargeTotal` varchar(20) not null default '0.00' after `insuranceTotal`");
  mc_upgradeLog('Completed: chargeTotal column added to `' . DB_PREFIX . 'sales` table');
}

// Add new purchase columns..
if (mswCheckColumn('purchases', 'giftID') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purchases` add column `giftID` int(7) not null default '0' after `productID`");
  mc_upgradeLog('Completed: giftID column added to `' . DB_PREFIX . 'purchases` table');
}
if (mswCheckColumnType('purchases', 'productType', 'virtual') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purchases` change `productType` `productType` enum('download','physical','virtual') not null default 'physical' after `saleID`");
  mc_upgradeLog('Completed: productType column changed in `' . DB_PREFIX . 'purchases` table');
}
if (mswCheckColumn('purchases', 'wishpur') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purchases` add column `wishpur` int(6) not null default '0'");
  mc_upgradeLog('Completed: wishpur column added to `' . DB_PREFIX . 'purchases` table');
}
if (mswCheckColumn('purchases', 'platform') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purchases` add column `platform` varchar(30) not null default 'desktop'");
  mc_upgradeLog('Completed: platform column added to `' . DB_PREFIX . 'purchases` table');
}

if (mswCheckColumn('purch_atts', 'attrName') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purch_atts` add column `attrName` varchar(100) not null default ''");
  mc_upgradeLog('Completed: attrName column added to `' . DB_PREFIX . 'purch_atts` table');
}
if (mswCheckColumn('purch_atts', 'attrWeight') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purch_atts` add column `attrWeight` varchar(50) not null default '0'");
  mc_upgradeLog('Completed: attrWeight column added to `' . DB_PREFIX . 'purch_atts` table');
}

// Update current sales data..
@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `codeType` = 'discount' where `couponCode` != '' and `codeType` = ''");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: codeType column updated where codetype is currently blank `' . DB_PREFIX . 'sales` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set 'paymentMethod` = 'twocheckout' where `paymentMethod` = '2checkout'");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: paymentMethod column updated to twocheckout from 2checkout in `' . DB_PREFIX . 'sales` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set 'paymentMethod` = 'skrill' where `paymentMethod` = 'moneybookers'");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: paymentMethod column updated to skrill from moneybookers in `' . DB_PREFIX . 'sales` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set 'paymentMethod` = 'payvector' where `paymentMethod` = 'iridium'");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: paymentMethod column updated to payvector from iridium in `' . DB_PREFIX . 'sales` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set 'paymentMethod` = 'paytrail' where `paymentMethod` = 'suvtoy'");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: paymentMethod column updated to paytrail from suvtoy in `' . DB_PREFIX . 'sales` table');
}

// Update sale buyer info into new address fields..
if (mswCheckColumn('sales', 'saleBuyerName') == 'yes') {

  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `ship_1` = `saleBuyerName`");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `bill_1` = `saleBuyerName`");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `ship_2` = `buyerEmail`");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `bill_2` = `buyerEmail`");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `ship_8` = `phoneNo`");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `bill_9` = `shipSetCountry`");

  // Now drop unused columns..
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` drop `phoneNo`,drop `buyerEmail`,drop `saleBuyerName`");

  mc_upgradeLog('Completed: Ported old sale buyer information to new shipping/billing fields');

}

if (mswCheckColumn('sales', 'trackcode') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `trackcode` varchar(100) not null default ''");
  mc_upgradeLog('Completed: trackcode column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'account') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `account` int(8) not null default '0' after `invoiceno`");
  mc_upgradeLog('Completed: account column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'type') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `type` enum('personal','trade') not null default 'personal'");
  mc_upgradeLog('Completed: type column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'wishlist') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `wishlist` int(8) not null default '0'");
  mc_upgradeLog('Completed: wishlist column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'platform') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `platform` varchar(30) not null default 'desktop'");
  mc_upgradeLog('Completed: platform column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('sales', 'affcode') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `affcode` varchar(200) not null default ''");
  mc_upgradeLog('Completed: affcode column added to `' . DB_PREFIX . 'sales` table');
}

// Update purchase attributes name and weight that are blank..
@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "purch_atts`,`" . DB_PREFIX . "attributes` set
`" . DB_PREFIX . "purch_atts`.`attrName`          = `" . DB_PREFIX . "attributes`.`attrName`
where `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
and `" . DB_PREFIX . "purch_atts`.`attrName`      = ''
");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: attrName column updated where value was currently blank in `' . DB_PREFIX . 'purch_atts` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "purch_atts`,`" . DB_PREFIX . "attributes` set
`" . DB_PREFIX . "purch_atts`.`attrWeight`        = `" . DB_PREFIX . "attributes`.`attrWeight`
where `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
and `" . DB_PREFIX . "purch_atts`.`attrWeight`    = ''
");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: attrWeight column updated where value was currently blank in `' . DB_PREFIX . 'purch_atts` table');
}

// Status updates
if (mswCheckColumn('statuses', 'visacc') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "statuses` add column `visacc` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: visacc column added to `' . DB_PREFIX . 'statuses` table');
}
if (mswCheckColumn('statuses', 'account') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "statuses` add column `account` int(8) not null default '0'");
  mc_upgradeLog('Completed: account column added to `' . DB_PREFIX . 'statuses` table');

  // Update new account column..
  @mysqli_query($GLOBALS["___msw_sqli"], "delete from `" . DB_PREFIX . "statuses` where (select `account` from `" . DB_PREFIX . "sales` where `" . DB_PREFIX . "sales`.`id` = `" . DB_PREFIX . "statuses`.`saleID`) IS NULL");
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "statuses` set `account` = (select `account` from `" . DB_PREFIX . "sales` where `" . DB_PREFIX . "sales`.`id` = `" . DB_PREFIX . "statuses`.`saleID`)");

  mc_upgradeLog('Completed: account column data ported from sales into `' . DB_PREFIX . 'statuses` table');
}

// Status text updates..
if (mswCheckColumn('status_text', 'ref') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "status_text` add column `ref` varchar(250) not null default ''");
  mc_upgradeLog('Completed: ref column added to `' . DB_PREFIX . 'status_text` table');
  // Port title to ref and clear title for legacy..
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "status_text` set `ref` = `statTitle` WHERE `ref` = ''");
  if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
    mc_upgradeLog('Completed: ref field updated with statTitle data in `' . DB_PREFIX . 'status_text` table for blank ref values');
  }
}

?>