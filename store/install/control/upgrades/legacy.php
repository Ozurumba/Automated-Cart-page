<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// LEGACY UPDATES
//==========================

// Version 2.0 / 2.01 Updates
if (mswCheckColumn('attr_groups', 'isRequired') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "attr_groups` add column `isRequired` enum('yes','no') not null default 'no'");
  mc_upgradeLog('v2.0/2.01 update completed. isRequired column added to `' . DB_PREFIX . 'attr_groups` table');
}

// Version 2.02 Updates
if (mswCheckColumn('pictures', 'remoteServer') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "pictures` add column `remoteServer` enum('yes','no') not null default 'no'");
  mc_upgradeLog('v2.02 update completed: remoteServer column added to `' . DB_PREFIX . 'pictures` table');
}
if (mswCheckColumn('pictures', 'remoteImg') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "pictures` add column `remoteImg` text default null");
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "pictures` add column `remoteThumb` text default null");
  mc_upgradeLog('v2.02 update completed: remoteImg column added to `' . DB_PREFIX . 'pictures` table');
}
if (mswCheckColumn('sales', 'shipType') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `shipType` varchar(20) not null default '' after `setShipRateID`");
  mc_upgradeLog('v2.02 update completed: shipType column added to `' . DB_PREFIX . 'sales` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "sales` set `shipType` = 'weight' where `shipType` = ''");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('v2.02 update completed: empty shipType columns updated to weight based in `' . DB_PREFIX . 'sales` table');
}

if (mswCheckColumn('products', 'pTitle') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pTitle` varchar(250) not null default '' after `pName`");
  mc_upgradeLog('v2.02 update completed: pTitle column added to `' . DB_PREFIX . 'products` table');
}

// Version 2.03 Updates
if (mswCheckColumn('categories', 'titleBar') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "categories` add column `titleBar` varchar(250) not null default '' after `catname`");
  mc_upgradeLog('v2.03 update completed: titleBar column added to `' . DB_PREFIX . 'categories` table');
}
if (mswCheckColumn('newpages', 'leftColumn') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "newpages` add column `leftColumn` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('v2.03 update completed: leftColumn column added to `' . DB_PREFIX . 'newpages` table');
}
if (mswCheckColumn('sales', 'insuranceTotal') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` add column `insuranceTotal` varchar(10) not null default '0' after `globalTotal`");
  mc_upgradeLog('v2.03 update completed: insuranceTotal column added to `' . DB_PREFIX . 'sales` table');
}
if (mswCheckColumn('products', 'pInsurance') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `pInsurance` varchar(10) not null default '0.00' after `pPrice`");
  mc_upgradeLog('v2.03 update completed: pInsurance column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('purchases', 'insPrice') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "purchases` add column `insPrice` varchar(10) not null default '0' after `attrPrice`");
  mc_upgradeLog('v2.03 update completed: insPrice column added to `' . DB_PREFIX . 'purchases` table');
}

// Version 2.05 Updates
if (mswCheckTable('paymentmethods') == 'yes') {
  if (mswCheckColumn('paymentmethods', 'redirectPaypal') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectPaypal` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectPhone') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectPhone` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectCheque') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectCheque` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectCash') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectCash` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectBank') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectBank` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectTwoCheckout') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectTwoCheckout` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectGoogleCheckout') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectGoogleCheckout` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectAlertPay') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectAlertPay` text default null");
  }
  if (mswCheckColumn('paymentmethods', 'redirectMoneyBookers') == 'no') {
    @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paymentmethods` add column `redirectMoneyBookers` text default null");
  }
  mc_upgradeLog('v2.05 update completed: new columns added to `' . DB_PREFIX . 'paymentmethods` table');
}
if (mswCheckColumn('products', 'minPurchaseQty') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `minPurchaseQty` int(10) not null default '0'");
  mc_upgradeLog('v2.05 update completed: minPurchaseQty column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'countryRestrictions') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `countryRestrictions` text default null");
  mc_upgradeLog('v2.05 update completed: countryRestrictions column added to `' . DB_PREFIX . 'products` table');
}
if (mswCheckColumn('products', 'checkoutTextDisplay') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "products` add column `checkoutTextDisplay` varchar(100) not null default ''");
  mc_upgradeLog('v2.05 update completed: checkoutTextDisplay column added to `' . DB_PREFIX . 'products` table');
}
if (!property_exists($SETTINGS, 'offerInsurance')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `offerInsurance` enum('yes','no') not null default 'no'");
  mc_upgradeLog('v2.05 update completed: offerInsurance column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'insuranceAmount')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `insuranceAmount` varchar(10) not null default ''");
  mc_upgradeLog('v2.05 update completed: insPrice column added to `' . DB_PREFIX . 'purchases` table');
}
if (!property_exists($SETTINGS, 'insuranceFilter')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `insuranceFilter` char(3) not null default ''");
  mc_upgradeLog('v2.05 update completed: insuranceFilter column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'excludeFreePop')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `excludeFreePop` enum('yes','no') not null default 'no'");
  mc_upgradeLog('v2.05 update completed: excludeFreePop column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'freeTextDisplay')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `freeTextDisplay` varchar(10) not null default 'FREE'");
  mc_upgradeLog('v2.05 update completed: freeTextDisplay column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'priceTextDisplay')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `priceTextDisplay` varchar(100) not null default ''");
  mc_upgradeLog('v2.05 update completed: priceTextDisplay column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'en_sitemap')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `en_sitemap` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('v2.05 update completed: en_sitemap column added to `' . DB_PREFIX . 'settings` table');
}

// Drop obsolete indexes from releases prior to 2.1
if (mswCheckIndex('sales', 'name_index') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` drop index `name_index`");
  mc_upgradeLog('Prior 2.1 update: altered table ' . DB_PREFIX . 'sales dropped index: name_index');
}
if (mswCheckIndex('sales', 'email_index') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "sales` drop index `email_index`");
  mc_upgradeLog('Prior 2.1 update: altered table ' . DB_PREFIX . 'sales dropped index: email_index');
}

?>