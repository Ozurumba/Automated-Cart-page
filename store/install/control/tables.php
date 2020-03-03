<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

$v      = (isset($_POST['mysql_version']) ? $_POST['mysql_version'] : 'MySQL4');
$c      = $_POST['charset'];
$tableD = array();

switch($v) {
  case 'MySQL4':
    if ($c) {
      $split     = explode('_', $c);
      $tableType = 'DEFAULT CHARACTER SET ' . $split[0] . PHP_EOL;
      $tableType .= 'COLLATE ' . $c . PHP_EOL;
    }
    $tableType .= 'TYPE = MyISAM';
    break;
  case 'MySQL5':
    if ($c) {
      $split     = explode('_', $c);
      $tableType = 'DEFAULT CHARACTER SET ' . $split[0] . PHP_EOL;
      $tableType .= 'COLLATE ' . $c . PHP_EOL;
    }
    $tableType .= 'ENGINE = MyISAM';
    break;
}

//============================================================
// INSTALL TABLE...ACCOUNTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "accounts`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "accounts` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `created` date NOT NULL DEFAULT '0000-00-00',
  `email` varchar(250) NOT NULL DEFAULT '',
  `pass` varchar(250) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  `verified` enum('yes','no') NOT NULL DEFAULT 'no',
  `timezone` varchar(50) NOT NULL DEFAULT '0',
  `ip` text default null,
  `notes` text default null,
  `reason` text default null,
  `system1` varchar(250) NOT NULL DEFAULT '',
  `system2` varchar(250) NOT NULL DEFAULT '',
  `language` varchar(100) NOT NULL DEFAULT '',
  `currency` varchar(100) NOT NULL DEFAULT '',
  `enablelog` enum('yes','no') NOT NULL DEFAULT 'yes',
  `newsletter` enum('yes','no') NOT NULL DEFAULT 'no',
  `message` text default null,
  `messageexp` date NOT NULL DEFAULT '0000-00-00',
  `type` enum('personal','trade') NOT NULL DEFAULT 'personal',
  `tradediscount` varchar(5) NOT NULL DEFAULT '',
  `minqty` varchar(10) NOT NULL DEFAULT '',
  `maxqty` varchar(10) NOT NULL DEFAULT '0',
  `stocklevel` varchar(10) NOT NULL DEFAULT '',
  `mincheckout` varchar(20) not null default '0.00',
  `trackcode` varchar(100) NOT NULL DEFAULT '',
  `params` text default null,
  `recent` text default null,
  `wishtext` text default null,
  PRIMARY KEY (`id`),
  KEY `em_index` (`email`),
  KEY `nm_index` (`name`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'accounts';
  mc_logDBError(DB_PREFIX . 'accounts', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ACCOUNTS_SEARCH..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "accounts_search`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "accounts_search` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` int(6) NOT NULL DEFAULT '0',
  `code` varchar(50) NOT NULL DEFAULT '',
  `saved` date NOT NULL DEFAULT '0000-00-00',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `code_index` (`code`)) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'accounts_search';
  mc_logDBError(DB_PREFIX . 'accounts_search', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ACCOUNTS_WISH..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "accounts_wish`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "accounts_wish` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` int(6) NOT NULL DEFAULT '0',
  `product` int(8) NOT NULL DEFAULT '0',
  `saved` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `account_index` (`account`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'accounts_wish';
  mc_logDBError(DB_PREFIX . 'accounts_wish', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ACTIVATION_HISTORY..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "activation_history`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "activation_history` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(7) NOT NULL DEFAULT '0',
  `products` text default null,
  `restoreDate` date NOT NULL DEFAULT '0000-00-00',
  `restoreTime` time NOT NULL DEFAULT '00:00:00',
  `adminUser` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'activation_history';
  mc_logDBError(DB_PREFIX . 'activation_history', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ADDRESSBOOK..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "addressbook`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "addressbook` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `account` int(8) NOT NULL DEFAULT '0',
  `nm` varchar(250) NOT NULL DEFAULT '',
  `em` varchar(250) NOT NULL DEFAULT '',
  `addr1` varchar(250) NOT NULL DEFAULT '',
  `addr2` varchar(250) NOT NULL DEFAULT '',
  `addr3` varchar(250) NOT NULL DEFAULT '',
  `addr4` varchar(250) NOT NULL DEFAULT '',
  `addr5` varchar(250) NOT NULL DEFAULT '',
  `addr6` varchar(250) NOT NULL DEFAULT '',
  `addr7` varchar(250) NOT NULL DEFAULT '',
  `addr8` varchar(250) NOT NULL DEFAULT '',
  `default` enum('yes','no') NOT NULL DEFAULT 'yes',
  `type` enum('bill','ship') NOT NULL DEFAULT 'bill',
  `zone` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ac_index` (`account`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'addressbook';
  mc_logDBError(DB_PREFIX . 'addressbook', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ATTACHMENTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "attachments`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(7) NOT NULL DEFAULT '0',
  `statusID` int(7) NOT NULL DEFAULT '0',
  `attachFolder` varchar(100) NOT NULL DEFAULT '',
  `fileName` varchar(100) NOT NULL DEFAULT '',
  `fileType` varchar(100) NOT NULL DEFAULT '',
  `fileSize` varchar(100) NOT NULL DEFAULT '',
  `isSaved` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `status_index` (`statusID`),
  KEY `sale_index` (`saleID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'attachments';
  mc_logDBError(DB_PREFIX . 'attachments', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ATTRIBUTES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "attributes`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "attributes` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `productID` int(10) NOT NULL DEFAULT '0',
  `attrGroup` int(10) NOT NULL DEFAULT '0',
  `attrName` varchar(100) NOT NULL DEFAULT '',
  `attrCost` varchar(50) NOT NULL DEFAULT '',
  `attrStock` int(10) NOT NULL DEFAULT '0',
  `attrWeight` varchar(50) NOT NULL DEFAULT '',
  `orderBy` int(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`productID`),
  KEY `group_index` (`attrGroup`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'attributes';
  mc_logDBError(DB_PREFIX . 'attributes', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ATTRIBUTE GROUPS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "attr_groups`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "attr_groups` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `productID` int(10) NOT NULL DEFAULT '0',
  `groupName` varchar(100) NOT NULL DEFAULT '',
  `orderBy` int(7) NOT NULL DEFAULT '0',
  `allowMultiple` enum('yes','no') NOT NULL DEFAULT 'no',
  `isRequired` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`productID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'attr_groups';
  mc_logDBError(DB_PREFIX . 'attr_groups', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...BANNERS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "banners`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "banners` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `bannerFile` varchar(250) NOT NULL DEFAULT '0',
  `bannerText` varchar(250) NOT NULL DEFAULT '0',
  `bannerUrl` varchar(250) NOT NULL DEFAULT '0',
  `bannerLive` enum('yes','no') NOT NULL DEFAULT 'yes',
  `bannerOrder` int(6) NOT NULL DEFAULT '0',
  `bannerCats` text default null,
  `bannerHome` enum('yes','no') NOT NULL DEFAULT 'no',
  `bannerFrom` date NOT NULL DEFAULT '0000-00-00',
  `bannerTo` date NOT NULL DEFAULT '0000-00-00',
  `trade` enum('yes','no') not null default 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'banners';
  mc_logDBError(DB_PREFIX . 'banners', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...BLOG..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "blog`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blog` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `title` text default null,
  `message` text default null,
  `created` int(13) NOT NULL DEFAULT '0',
  `published` int(13) NOT NULL DEFAULT '0',
  `autodelete` int(13) NOT NULL DEFAULT '0',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'blog';
  mc_logDBError(DB_PREFIX . 'blog', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...BOXES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "boxes`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "boxes` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `ident` varchar(250) NOT NULL DEFAULT '',
  `name` varchar(250) NOT NULL DEFAULT '',
  `status` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tmp` varchar(250) NOT NULL DEFAULT '',
  `orderby` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'boxes';
  mc_logDBError(DB_PREFIX . 'boxes', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...BRANDS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "brands`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `bCat` varchar(50) NOT NULL DEFAULT 'all',
  `enBrand` enum('yes','no') NOT NULL DEFAULT 'yes',
  `rwslug` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'brands';
  mc_logDBError(DB_PREFIX . 'brands', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...CAMPAIGNS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "campaigns`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "campaigns` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `cName` varchar(250) NOT NULL DEFAULT '',
  `cDiscountCode` varchar(50) NOT NULL DEFAULT '',
  `cMin` varchar(50) NOT NULL DEFAULT '0.00',
  `cUsage` int(5) NOT NULL DEFAULT '0',
  `cExpiry` date NOT NULL DEFAULT '0000-00-00',
  `cDiscount` varchar(20) NOT NULL DEFAULT '',
  `cAdded` date DEFAULT '0000-00-00',
  `cLive` enum('yes','no') NOT NULL DEFAULT 'yes',
  `categories` text default null,
  PRIMARY KEY (`id`),
  KEY `code_index` (`cDiscountCode`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'campaigns';
  mc_logDBError(DB_PREFIX . 'campaigns', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...CATEGORIES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "categories`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catname` varchar(250) NOT NULL,
  `titleBar` varchar(250) NOT NULL DEFAULT '',
  `comments` text default null,
  `catLevel` tinyint(1) NOT NULL DEFAULT '0',
  `childOf` int(6) NOT NULL DEFAULT '0',
  `metaDesc` text default null,
  `metaKeys` text default null,
  `enCat` enum('yes','no') NOT NULL DEFAULT 'yes',
  `orderBy` int(5) NOT NULL DEFAULT '0',
  `enDisqus` enum('yes','no') NOT NULL DEFAULT 'no',
  `freeShipping` enum('yes','no') NOT NULL DEFAULT 'no',
  `imgIcon` varchar(100) NOT NULL DEFAULT '',
  `showRelated` enum('yes','no') NOT NULL DEFAULT 'yes',
  `rwslug` varchar(250) NOT NULL DEFAULT '',
  `theme` varchar(200) NOT NULL DEFAULT '',
  `vis` varchar(30) not null default '',
  PRIMARY KEY (`id`),
  KEY `cat_index` (`catLevel`),
  KEY `child_index` (`childOf`),
  KEY `en_index` (`enCat`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'categories';
  mc_logDBError(DB_PREFIX . 'categories', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...CONFIG..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "cfg`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cfg` (
  `id` int(7) not null auto_increment,
	`op1` varchar(250) not null default '',
	`op2` varchar(250) not null default '',
	`op3` varchar(250) not null default '',
	`op4` varchar(250) not null default '',
	`op5` varchar(250) not null default '',
	`op6` text default null,
	`op7` text default null,
	`op8` text default null,
	`op9` text default null,
	`op10` text default null,
	primary key (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'cfg';
  mc_logDBError(DB_PREFIX . 'cfg', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...CLICK HISTORY..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "click_history`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "click_history` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(7) NOT NULL DEFAULT '0',
  `purchaseID` int(7) NOT NULL DEFAULT '0',
  `productID` int(7) NOT NULL DEFAULT '0',
  `clickDate` date NOT NULL DEFAULT '0000-00-00',
  `clickTime` time NOT NULL DEFAULT '00:00:00',
  `clickIP` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'click_history';
  mc_logDBError(DB_PREFIX . 'click_history', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...COMPARISONS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "comparisons`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "comparisons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(7) NOT NULL DEFAULT '0',
  `thisProduct` int(7) NOT NULL DEFAULT '0',
  `thatProduct` int(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sale_index` (`saleID`),
  KEY `this_index` (`thisProduct`),
  KEY `that_index` (`thatProduct`)) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'comparisons';
  mc_logDBError(DB_PREFIX . 'comparisons', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...COUNTRIES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "countries`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "countries` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `cName` varchar(250) NOT NULL DEFAULT '',
  `cISO` varchar(3) NOT NULL,
  `cISO_2` char(2) NOT NULL DEFAULT '',
  `iso4217` varchar(50) NOT NULL DEFAULT '0',
  `enCountry` enum('yes','no') NOT NULL DEFAULT 'no',
  `localPickup` enum('yes','no') NOT NULL DEFAULT 'no',
  `freeship` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'countries';
  mc_logDBError(DB_PREFIX . 'countries', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...COUPONS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "coupons`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "coupons` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `cCampaign` int(7) NOT NULL DEFAULT '0',
  `cDiscountCode` varchar(200) NOT NULL DEFAULT '',
  `cUseDate` date NOT NULL DEFAULT '0000-00-00',
  `saleID` mediumint(10) NOT NULL DEFAULT '0',
  `discountValue` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `code_index` (`cDiscountCode`),
  KEY `sale_index` (`saleID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'coupons';
  mc_logDBError(DB_PREFIX . 'coupons', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...CURRENCIES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "currencies`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "currencies` (
  `currency` char(3) NOT NULL DEFAULT '',
  `rate` varchar(20) NOT NULL DEFAULT '',
  `enableCur` enum('yes','no') DEFAULT 'no',
  `curname` varchar(30) NOT NULL,
  `currencyDisplayPref` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`currency`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'currencies';
  mc_logDBError(DB_PREFIX . 'currencies', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...DROPSHIPPERS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "dropshippers`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dropshippers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `emails` text default null,
  `status` text default null,
  `method` text default null,
  `salestatus` varchar(100) NOT NULL DEFAULT '',
  `enable` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'dropshippers';
  mc_logDBError(DB_PREFIX . 'dropshippers', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ENTRY_LOG..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "entry_log`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "entry_log` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(8) NOT NULL DEFAULT '0',
  `logdatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(250) NOT NULL DEFAULT '',
  `type` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_index` (`userid`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'entry_log';
  mc_logDBError(DB_PREFIX . 'entry_log', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...FLAT..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "flat`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "flat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inZone` int(8) NOT NULL DEFAULT '0',
  `rate` varchar(30) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'flat';
  mc_logDBError(DB_PREFIX . 'flat', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...GIFTCERTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "giftcerts`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "giftcerts` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `value` varchar(10) NOT NULL DEFAULT '',
  `image` varchar(250) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  `orderBy` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'giftcerts';
  mc_logDBError(DB_PREFIX . 'giftcerts', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...GIFTCODES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "giftcodes`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "giftcodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(10) NOT NULL DEFAULT '0',
  `purchaseID` int(11) NOT NULL DEFAULT '0',
  `giftID` int(10) NOT NULL DEFAULT '0',
  `code` varchar(200) NOT NULL DEFAULT '',
  `value` varchar(10) NOT NULL DEFAULT '',
  `redeemed` varchar(10) NOT NULL DEFAULT '',
  `from_name` varchar(100) NOT NULL DEFAULT '',
  `from_email` varchar(100) NOT NULL DEFAULT '',
  `to_name` varchar(100) NOT NULL DEFAULT '',
  `to_email` varchar(100) NOT NULL DEFAULT '',
  `message` text default null,
  `dateAdded` date NOT NULL DEFAULT '0000-00-00',
  `notes` text default null,
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  `active` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `gift_index` (`giftID`),
  KEY `sale_index` (`saleID`),
  KEY `code_index` (`code`),
  KEY `purc_index` (`purchaseID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'giftcodes';
  mc_logDBError(DB_PREFIX . 'giftcodes', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...METHODS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "methods`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "methods` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `orderby` int(3) NOT NULL DEFAULT '0',
  `method` varchar(100) NOT NULL DEFAULT '',
  `display` varchar(100) NOT NULL DEFAULT '',
  `status` enum('yes','no') NOT NULL DEFAULT 'yes',
  `defmeth` enum('yes','no') NOT NULL DEFAULT 'no',
  `liveserver` varchar(250) NOT NULL DEFAULT '',
  `sandboxserver` varchar(250) NOT NULL DEFAULT '',
  `plaintext` text default null,
  `htmltext` text default null,
  `info` text default null,
  `redirect` varchar(250) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `docs` varchar(100) NOT NULL DEFAULT '',
  `webpage` varchar(100) NOT NULL DEFAULT '',
  `statuses` text default null,
  `viewtype` varchar(20) not null default 'a',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'methods';
  mc_logDBError(DB_PREFIX . 'methods', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...METHODS-PARAMS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "methods_params`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "methods_params` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `method` varchar(200) NOT NULL DEFAULT '',
  `param` varchar(200) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mthd_index` (`method`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'methods_params';
  mc_logDBError(DB_PREFIX . 'methods_params', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...MP3..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "mp3`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mp3` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(7) NOT NULL DEFAULT '0',
  `filePath` varchar(250) NOT NULL,
  `fileName` varchar(250) NOT NULL DEFAULT '',
  `fileFolder` varchar(250) NOT NULL DEFAULT '',
  `orderBy` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`product_id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'mp3';
  mc_logDBError(DB_PREFIX . 'mp3', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...NEWPAGES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "newpages`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "newpages` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(250) NOT NULL DEFAULT '',
  `pageKeys` text default null,
  `pageDesc` text default null,
  `pageText` text default null,
  `orderBy` int(5) NOT NULL DEFAULT '0',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `linkPos` varchar(10) NOT NULL DEFAULT '1',
  `linkExternal` enum('yes','no') NOT NULL DEFAULT 'no',
  `customTemplate` varchar(250) NOT NULL DEFAULT '',
  `linkTarget` enum('same','new') NOT NULL DEFAULT 'new',
  `landingPage` enum('yes','no') NOT NULL DEFAULT 'no',
  `leftColumn` enum('yes','no') NOT NULL DEFAULT 'yes',
  `rwslug` varchar(250) NOT NULL DEFAULT '',
  `trade` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'newpages';
  mc_logDBError(DB_PREFIX . 'newpages', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...NEWSTEMPLATES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "newstemplates`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "newstemplates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `email` varchar(250) NOT NULL DEFAULT '',
  `subject` varchar(250) NOT NULL DEFAULT '',
  `html` text default null,
  `plain` text default null,
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'newstemplates';
  mc_logDBError(DB_PREFIX . 'newstemplates', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...NEWS TICKER..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "news_ticker`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "news_ticker` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `newsText` text default null,
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `orderBy` int(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'news_ticker';
  mc_logDBError(DB_PREFIX . 'news_ticker', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PAYSTATUSES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "paystatuses`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paystatuses` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `statname` varchar(200) NOT NULL DEFAULT '',
  `pMethod` varchar(15) NOT NULL DEFAULT 'all',
  `homepage` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `mthd_index` (`pMethod`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'paystatuses';
  mc_logDBError(DB_PREFIX . 'paystatuses', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PDF..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "pdf`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pdf` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `company` text default null,
  `address` varchar(250) NOT NULL DEFAULT '',
  `font` varchar(50) NOT NULL DEFAULT 'helvetica',
  `dir` enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  `meta` varchar(20) NOT NULL DEFAULT 'utf-8',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'pdf';
  mc_logDBError(DB_PREFIX . 'pdf', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PER..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "per`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "per` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inZone` int(8) NOT NULL DEFAULT '0',
  `rate` varchar(30) NOT NULL DEFAULT '',
  `item` varchar(30) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'percent';
  mc_logDBError(DB_PREFIX . 'percent', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PERCENT..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "percent`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "percent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inZone` int(8) NOT NULL DEFAULT '0',
  `priceFrom` varchar(30) NOT NULL DEFAULT '',
  `priceTo` varchar(30) NOT NULL DEFAULT '',
  `percentage` varchar(30) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`),
  KEY `from_index` (`priceFrom`),
  KEY `to_index` (`priceTo`),
  KEY `en_index` (`enabled`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'percent';
  mc_logDBError(DB_PREFIX . 'percent', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PERSONALISATION..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "personalisation`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "personalisation` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `productID` int(10) NOT NULL DEFAULT '0',
  `persInstructions` text default null,
  `persOptions` text default null,
  `maxChars` int(5) NOT NULL DEFAULT '0',
  `persAddCost` varchar(50) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `boxType` enum('input','textarea') NOT NULL DEFAULT 'input',
  `reqField` enum('yes','no') NOT NULL DEFAULT 'no',
  `orderBy` int(7) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_index` (`productID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'personalisation';
  mc_logDBError(DB_PREFIX . 'personalisation', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PICTURES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "pictures`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pictures` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(7) NOT NULL DEFAULT '0',
  `picture_path` varchar(250) NOT NULL DEFAULT '',
  `thumb_path` varchar(250) NOT NULL DEFAULT '',
  `folder` varchar(250) NOT NULL DEFAULT '',
  `dimensions` varchar(12) NOT NULL DEFAULT '',
  `displayImg` enum('yes','no') NOT NULL DEFAULT 'no',
  `remoteServer` enum('yes','no') NOT NULL DEFAULT 'no',
  `remoteImg` text default null,
  `remoteThumb` text default null,
  `pictitle` text default null,
  `picalt` text default null,
  PRIMARY KEY (`id`),
  KEY `product_index` (`product_id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'pictures';
  mc_logDBError(DB_PREFIX . 'pictures', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PRICE_POINTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "price_points`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "price_points` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priceFrom` varchar(30) NOT NULL DEFAULT '',
  `priceTo` varchar(30) NOT NULL DEFAULT '',
  `priceText` varchar(200) NOT NULL DEFAULT '',
  `orderBy` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from_index` (`priceFrom`),
  KEY `to_index` (`priceTo`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'price_points';
  mc_logDBError(DB_PREFIX . 'price_points', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PRODUCTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "products`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "products` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `pName` varchar(250) NOT NULL DEFAULT '',
  `pTitle` varchar(250) NOT NULL DEFAULT '',
  `pMetaKeys` text default null,
  `pMetaDesc` text default null,
  `pTags` text default null,
  `pDescription` text default null,
  `pShortDescription` text default null,
  `pDownload` enum('yes','no') NOT NULL DEFAULT 'no',
  `pDownloadPath` varchar(250) NOT NULL DEFAULT '',
  `pDownloadLimit` int(7) NOT NULL DEFAULT '0',
  `pCode` varchar(250) NOT NULL DEFAULT '',
  `pStockNotify` int(7) NOT NULL DEFAULT '0',
  `pStock` int(7) NOT NULL DEFAULT '0',
  `pEnable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `pDateAdded` date NOT NULL DEFAULT '0000-00-00',
  `pVisits` int(10) unsigned NOT NULL DEFAULT '0',
  `pVideo` varchar(250) NOT NULL DEFAULT '',
  `pVideo2` varchar(250) NOT NULL DEFAULT '',
  `pVideo3` varchar(250) NOT NULL DEFAULT '',
  `pWeight` varchar(50) NOT NULL DEFAULT '',
  `pPrice` varchar(20) NOT NULL DEFAULT '',
  `pPurPrice` varchar(20) not null default '0.00',
  `pInsurance` varchar(10) NOT NULL DEFAULT '0.00',
  `pOfferExpiry` date NOT NULL DEFAULT '0000-00-00',
  `pOffer` varchar(20) NOT NULL DEFAULT '',
  `pMultiBuy` int(10) NOT NULL DEFAULT '0',
  `rssBuildDate` varchar(35) NOT NULL DEFAULT '',
  `enDisqus` enum('yes','no') NOT NULL DEFAULT 'no',
  `freeShipping` enum('yes','no') NOT NULL DEFAULT 'no',
  `pPurchase` enum('yes','no') NOT NULL DEFAULT 'yes',
  `minPurchaseQty` int(10) NOT NULL DEFAULT '0',
  `maxPurchaseQty` int(10) NOT NULL DEFAULT '0',
  `countryRestrictions` text default null,
  `checkoutTextDisplay` varchar(100) NOT NULL DEFAULT '',
  `pNotes` text default null,
  `rwslug` varchar(250) NOT NULL DEFAULT '',
  `pAvailableText` varchar(250) NOT NULL DEFAULT '',
  `pCube` int(10) NOT NULL DEFAULT '0',
  `pGuardian` int(10) not null default '0',
  `dropshipping` int(8) NOT NULL DEFAULT '0',
  `expiry` date NOT NULL DEFAULT '0000-00-00',
  `exp_price` varchar(10) not null default '',
  `exp_special` enum('yes','no') not null default 'no',
  `exp_send` enum('yes','no') not null default 'no',
  `exp_text` text default null,
  PRIMARY KEY (`id`),
  KEY `pDownload` (`pDownload`),
  KEY `code_index` (`pCode`),
  KEY `name_index` (`pName`),
  KEY `stock_index` (`pStock`),
  KEY `price_index` (`pPrice`),
  KEY `cost_index` (`pPurPrice`),
  KEY `en_index` (`pEnable`),
  KEY `wght_index` (`pWeight`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'products';
  mc_logDBError(DB_PREFIX . 'products', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PROD_BRAND..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "prod_brand`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "prod_brand` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `product` int(8) NOT NULL DEFAULT '0',
  `brand` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`product`),
  KEY `brd_index` (`brand`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'prod_brand';
  mc_logDBError(DB_PREFIX . 'prod_brand', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PROD_CATEGORY..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "prod_category`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "prod_category` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `product` int(8) NOT NULL DEFAULT '0',
  `category` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`product`),
  KEY `cat_index` (`category`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'prod_category';
  mc_logDBError(DB_PREFIX . 'prod_category', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PROD_RELATION..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "prod_relation`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "prod_relation` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `product` int(8) NOT NULL DEFAULT '0',
  `related` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `prod_index` (`product`),
  KEY `rel_index` (`related`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'prod_relation';
  mc_logDBError(DB_PREFIX . 'prod_relation', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PURCHASES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "purchases`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchaseDate` date NOT NULL DEFAULT '0000-00-00',
  `purchaseTime` time NOT NULL DEFAULT '00:00:00',
  `saleID` int(11) NOT NULL DEFAULT '0',
  `productType` enum('download','physical','virtual') NOT NULL DEFAULT 'physical',
  `productID` int(7) NOT NULL DEFAULT '0',
  `giftID` int(7) NOT NULL DEFAULT '0',
  `categoryID` int(8) NOT NULL DEFAULT '0',
  `salePrice` varchar(20) NOT NULL DEFAULT '',
  `liveDownload` enum('yes','no') NOT NULL DEFAULT 'no',
  `persPrice` varchar(20) NOT NULL DEFAULT '',
  `attrPrice` varchar(20) NOT NULL DEFAULT '',
  `insPrice` varchar(10) NOT NULL DEFAULT '0.00',
  `globalDiscount` int(3) NOT NULL DEFAULT '0',
  `globalCost` varchar(20) NOT NULL DEFAULT '',
  `productQty` int(5) NOT NULL DEFAULT '0',
  `productWeight` varchar(20) NOT NULL DEFAULT '',
  `downloadAmount` int(7) NOT NULL DEFAULT '0',
  `downloadCode` char(50) NOT NULL DEFAULT '',
  `buyCode` varchar(50) NOT NULL DEFAULT '',
  `saleConfirmation` enum('yes','no') NOT NULL DEFAULT 'no',
  `deletedProductName` varchar(250) NOT NULL DEFAULT '',
  `freeShipping` enum('yes','no') NOT NULL DEFAULT 'no',
  `wishpur` int(6) NOT NULL DEFAULT '0',
  `platform` varchar(30) not null default 'desktop',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`),
  KEY `product_index` (`productID`),
  KEY `cat_index` (`categoryID`),
  KEY `conf_index` (`saleConfirmation`),
  KEY `dcode_index` (`downloadCode`),
  KEY `ld_index` (`liveDownload`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'purchases';
  mc_logDBError(DB_PREFIX . 'purchases', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PURCH_ATTS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "purch_atts`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purch_atts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(11) NOT NULL DEFAULT '0',
  `productID` int(11) NOT NULL DEFAULT '0',
  `purchaseID` int(11) NOT NULL DEFAULT '0',
  `attributeID` int(7) NOT NULL DEFAULT '0',
  `addCost` varchar(20) NOT NULL DEFAULT '',
  `attrName` varchar(100) NOT NULL DEFAULT '',
  `attrWeight` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`),
  KEY `prodid_index` (`productID`),
  KEY `purid_index` (`purchaseID`),
  KEY `attid_index` (`attributeID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'purch_atts';
  mc_logDBError(DB_PREFIX . 'purch_atts', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...PURCH_PERS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "purch_pers`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purch_pers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(11) NOT NULL DEFAULT '0',
  `productID` int(11) NOT NULL DEFAULT '0',
  `purchaseID` int(11) NOT NULL DEFAULT '0',
  `personalisationID` int(7) NOT NULL DEFAULT '0',
  `visitorData` text default null,
  `addCost` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`),
  KEY `prod_index` (`productID`),
  KEY `purc_index` (`purchaseID`),
  KEY `pers_index` (`personalisationID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'purch_pers';
  mc_logDBError(DB_PREFIX . 'purch_pers', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...QTYRATES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "qtyrates`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "qtyrates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inZone` int(8) NOT NULL DEFAULT '0',
  `qtyFrom` int(6) NOT NULL DEFAULT '0',
  `qtyTo` int(6) NOT NULL DEFAULT '0',
  `rate` varchar(30) NOT NULL DEFAULT '',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`),
  KEY `from_index` (`qtyFrom`),
  KEY `to_index` (`qtyTo`),
  KEY `en_index` (`enabled`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'qtyrates';
  mc_logDBError(DB_PREFIX . 'qtyrates', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...RATES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "rates`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "rates` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `rWeightFrom` varchar(50) NOT NULL DEFAULT '0',
  `rWeightTo` varchar(50) NOT NULL DEFAULT '0',
  `rCost` varchar(20) NOT NULL DEFAULT '',
  `rService` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from_index` (`rWeightFrom`),
  KEY `to_index` (`rWeightTo`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'rates';
  mc_logDBError(DB_PREFIX . 'rates', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SALES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "sales`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceNo` varchar(100) NOT NULL DEFAULT '',
  `account` int(8) NOT NULL DEFAULT '0',
  `saleNotes` text default null,
  `bill_1` varchar(250) NOT NULL DEFAULT '',
  `bill_2` varchar(250) NOT NULL DEFAULT '',
  `bill_3` varchar(250) NOT NULL DEFAULT '',
  `bill_4` varchar(250) NOT NULL DEFAULT '',
  `bill_5` varchar(250) NOT NULL DEFAULT '',
  `bill_6` varchar(250) NOT NULL DEFAULT '',
  `bill_7` varchar(250) NOT NULL DEFAULT '',
  `bill_8` varchar(250) NOT NULL DEFAULT '',
  `bill_9` int(5) NOT NULL DEFAULT '0',
  `ship_1` varchar(250) NOT NULL DEFAULT '',
  `ship_2` varchar(250) NOT NULL DEFAULT '',
  `ship_3` varchar(250) NOT NULL DEFAULT '',
  `ship_4` varchar(250) NOT NULL DEFAULT '',
  `ship_5` varchar(250) NOT NULL DEFAULT '',
  `ship_6` varchar(250) NOT NULL DEFAULT '',
  `ship_7` varchar(250) NOT NULL DEFAULT '',
  `ship_8` varchar(250) NOT NULL DEFAULT '',
  `buyerAddress` text default null,
  `paymentStatus` varchar(20) NOT NULL DEFAULT '',
  `gatewayID` varchar(250) NOT NULL DEFAULT '',
  `taxPaid` varchar(20) NOT NULL DEFAULT '',
  `taxRate` varchar(5) NOT NULL DEFAULT '',
  `couponCode` varchar(200) NOT NULL DEFAULT '',
  `couponTotal` varchar(100) NOT NULL DEFAULT '',
  `codeType` varchar(20) NOT NULL DEFAULT '',
  `subTotal` varchar(20) NOT NULL DEFAULT '',
  `grandTotal` varchar(20) NOT NULL DEFAULT '',
  `shipTotal` varchar(20) NOT NULL DEFAULT '',
  `globalTotal` varchar(20) NOT NULL DEFAULT '0',
  `insuranceTotal` varchar(10) NOT NULL DEFAULT '0.00',
  `chargeTotal` varchar(20) not null default '0.00',
  `globalDiscount` int(5) NOT NULL DEFAULT '0',
  `manualDiscount` varchar(20) NOT NULL DEFAULT '',
  `isPickup` enum('yes','no') NOT NULL DEFAULT 'no',
  `shipSetCountry` int(7) NOT NULL DEFAULT '0',
  `shipSetArea` int(7) NOT NULL DEFAULT '0',
  `setShipRateID` int(7) NOT NULL DEFAULT '0',
  `shipType` varchar(20) NOT NULL DEFAULT '',
  `cartWeight` varchar(20) NOT NULL DEFAULT '',
  `purchaseDate` date NOT NULL DEFAULT '0000-00-00',
  `purchaseTime` time NOT NULL DEFAULT '00:00:00',
  `buyCode` varchar(50) NOT NULL,
  `saleConfirmation` enum('yes','no') NOT NULL DEFAULT 'no',
  `paymentMethod` varchar(20) NOT NULL DEFAULT '',
  `ipAddress` text default null,
  `ipAccess` text default null,
  `restrictCount` int(7) NOT NULL DEFAULT '0',
  `orderCopyEmails` text default null,
  `zipLimit` int(5) NOT NULL DEFAULT '0',
  `downloadLock` enum('yes','no') NOT NULL DEFAULT 'no',
  `optInNewsletter` enum('yes','no') NOT NULL DEFAULT 'yes',
  `paypalErrorTrigger` tinyint(1) NOT NULL DEFAULT '0',
  `gateparams` text default null,
  `trackcode` varchar(100) NOT NULL DEFAULT '',
  `type` enum('personal','trade') NOT NULL DEFAULT 'personal',
  `wishlist` int(8) NOT NULL DEFAULT '0',
  `platform` varchar(30) not null default 'desktop',
  `affcode` varchar(200) not null default '',
  PRIMARY KEY (`id`),
  KEY `code_index` (`buyCode`),
  KEY `acc_index` (`account`),
  KEY `conf_index` (`saleConfirmation`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'sales';
  mc_logDBError(DB_PREFIX . 'sales', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SEARCH_INDEX..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "search_index`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "search_index` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `searchCode` varchar(50) NOT NULL DEFAULT '',
  `results` text default null,
  `searchDate` date NOT NULL DEFAULT '0000-00-00',
  `filters` text default null,
  PRIMARY KEY (`id`),
  KEY `code_index` (`searchCode`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'search_index';
  mc_logDBError(DB_PREFIX . 'search_index', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SEARCH_LOG..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "search_log`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "search_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` text default null,
  `results` int(7) NOT NULL DEFAULT '0',
  `searchDate` date NOT NULL DEFAULT '0000-00-00',
  `ip` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'search_log';
  mc_logDBError(DB_PREFIX . 'search_log', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SERVICES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "services`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "services` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `sName` varchar(250) NOT NULL DEFAULT '0',
  `sEstimation` varchar(250) NOT NULL DEFAULT '0',
  `sSignature` enum('yes','no') NOT NULL DEFAULT 'yes',
  `inZone` int(6) NOT NULL DEFAULT '0',
  `enableCOD` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'services';
  mc_logDBError(DB_PREFIX . 'services', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SETTINGS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "settings`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "settings` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `website` varchar(250) NOT NULL DEFAULT '',
  `theme` varchar(100) NOT NULL DEFAULT '_theme_default',
  `theme2` varchar(100) NOT NULL DEFAULT '_theme_default',
  `tradetheme` varchar(100) not null default '',
  `email` varchar(250) NOT NULL DEFAULT '',
  `addEmails` text default null,
  `serverPath` varchar(250) NOT NULL DEFAULT '',
  `languagePref` varchar(40) NOT NULL DEFAULT 'english.php',
  `logoName` varchar(50) NOT NULL DEFAULT '',
  `baseCurrency` char(3) NOT NULL DEFAULT 'GBP',
  `currencyDisplayPref` varchar(100) NOT NULL DEFAULT '',
  `logErrors` enum('yes','no') NOT NULL DEFAULT 'no',
  `gatewayMode` enum('test','live') NOT NULL DEFAULT 'test',
  `enableSSL` enum('yes','no') NOT NULL DEFAULT 'no',
  `enablePickUp` enum('yes','no') NOT NULL DEFAULT 'no',
  `shipCountry` varchar(10) NOT NULL DEFAULT '',
  `logFolderName` varchar(50) NOT NULL DEFAULT 'logs',
  `ifolder` varchar(250) NOT NULL DEFAULT '',
  `metaKeys` text default null,
  `metaDesc` text default null,
  `enableCart` enum('yes','no') NOT NULL DEFAULT 'yes',
  `offlineDate` date NOT NULL DEFAULT '0000-00-00',
  `offlineText` text default null,
  `offlineIP` text default null,
  `en_rss` enum('yes','no') NOT NULL DEFAULT 'yes',
  `rssScroller` enum('yes','no') NOT NULL DEFAULT 'no',
  `rssScrollerUrl` varchar(250) NOT NULL DEFAULT '',
  `rssScrollerLimit` int(3) NOT NULL DEFAULT '10',
  `en_modr` enum('yes','no') NOT NULL DEFAULT 'no',
  `cName` varchar(250) NOT NULL DEFAULT '',
  `cWebsite` varchar(250) NOT NULL DEFAULT '',
  `cTel` varchar(250) NOT NULL DEFAULT '',
  `cFax` varchar(250) NOT NULL DEFAULT '',
  `cAddress` text default null,
  `cOther` text default null,
  `cReturns` text default null,
  `smtp` enum('yes','no') NOT NULL DEFAULT 'no',
  `smtp_host` varchar(100) NOT NULL DEFAULT 'localhost',
  `smtp_user` varchar(100) NOT NULL DEFAULT '',
  `smtp_pass` varchar(100) NOT NULL DEFAULT '',
  `smtp_port` varchar(100) NOT NULL DEFAULT '25',
  `smtp_security` varchar(10) NOT NULL DEFAULT '',
  `smtp_from` varchar(250) NOT NULL DEFAULT '',
  `smtp_email` varchar(250) NOT NULL DEFAULT '',
  `smtp_debug` enum('yes','no') NOT NULL DEFAULT 'no',
  `smtp_insec` enum('yes','no') not null default 'no',
  `mailpref` enum('smtp','php') not null default 'smtp',
  `homeProdValue` int(3) NOT NULL DEFAULT '0',
  `homeProdType` varchar(10) NOT NULL DEFAULT 'latest',
  `homeProdCats` text default null,
  `homeProdIDs` text default null,
  `adminFooter` text default null,
  `publicFooter` text default null,
  `prodKey` char(60) NOT NULL DEFAULT '',
  `encoderVersion` varchar(5) NOT NULL DEFAULT '',
  `activateEmails` enum('yes','no') NOT NULL DEFAULT 'no',
  `saleComparisonItems` int(6) NOT NULL DEFAULT '0',
  `productsPerPage` int(4) NOT NULL DEFAULT '35',
  `mostPopProducts` int(5) NOT NULL DEFAULT '0',
  `mostPopPref` enum('sales','hits') NOT NULL DEFAULT 'sales',
  `latestProdLimit` int(5) NOT NULL DEFAULT '0',
  `latestProdDuration` enum('days','months','years') NOT NULL DEFAULT 'days',
  `searchLowStockLimit` int(5) NOT NULL DEFAULT '1',
  `enSearchLog` enum('yes','no') NOT NULL DEFAULT 'no',
  `savedSearches` int(6) NOT NULL DEFAULT '7',
  `searchSlider` text default null,
  `searchTagsOnly` enum('yes','no') NOT NULL DEFAULT 'no',
  `jsDateFormat` varchar(10) NOT NULL DEFAULT 'DD-MM-YYYY',
  `jsWeekStart` tinyint(1) NOT NULL DEFAULT '0',
  `timezone` varchar(50) NOT NULL DEFAULT 'Europe/London',
  `mysqlDateFormat` varchar(10) NOT NULL DEFAULT '',
  `systemDateFormat` varchar(30) NOT NULL DEFAULT 'j F Y',
  `rssFeedLimit` int(3) NOT NULL DEFAULT '50',
  `minInvoiceDigits` tinyint(2) NOT NULL DEFAULT '5',
  `invoiceNo` int(10) NOT NULL DEFAULT '1',
  `pendingAsComplete` enum('yes','no') NOT NULL DEFAULT 'no',
  `freeShipThreshold` varchar(10) NOT NULL DEFAULT '',
  `enableZip` enum('yes','no') NOT NULL DEFAULT 'no',
  `zipCreationLimit` varchar(100) NOT NULL DEFAULT '0',
  `zipLimit` int(3) NOT NULL DEFAULT '0',
  `zipTimeOut` int(6) NOT NULL DEFAULT '0',
  `zipMemoryLimit` int(5) NOT NULL DEFAULT '0',
  `zipAdditionalFolder` varchar(50) NOT NULL DEFAULT 'additional-zip',
  `enEntryLog` enum('yes','no') NOT NULL DEFAULT 'no',
  `softwareVersion` varchar(10) NOT NULL DEFAULT '',
  `smartQuotes` enum('yes','no') NOT NULL DEFAULT 'yes',
  `hitCounter` enum('yes','no') NOT NULL DEFAULT 'yes',
  `menuSubCats` enum('yes','no') NOT NULL DEFAULT 'yes',
  `adminFolderName` varchar(100) NOT NULL DEFAULT 'admin',
  `twitterLatest` enum('yes','no') NOT NULL DEFAULT 'no',
  `globalDiscount` varchar(20) NOT NULL DEFAULT '0',
  `globalDiscountExpiry` date NOT NULL DEFAULT '0000-00-00',
  `enableRecentView` enum('yes','no') NOT NULL DEFAULT 'yes',
  `freeDownloadRestriction` varchar(10) NOT NULL DEFAULT '0',
  `thumbWidth` int(4) NOT NULL DEFAULT '230',
  `thumbHeight` int(4) NOT NULL DEFAULT '200',
  `thumbQuality` int(3) NOT NULL DEFAULT '99',
  `thumbQualityPNG` tinyint(1) NOT NULL DEFAULT '9',
  `aspectRatio` enum('yes','no') NOT NULL DEFAULT 'yes',
  `renamePics` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tmbPrefix` varchar(100) NOT NULL DEFAULT 'tmb_',
  `imgPrefix` varchar(100) NOT NULL DEFAULT 'img_',
  `showOutofStock` enum('cat','yes','no') NOT NULL DEFAULT 'yes',
  `enableCheckout` enum('yes','no') NOT NULL DEFAULT 'yes',
  `globalDownloadPath` varchar(250) NOT NULL DEFAULT '',
  `maxProductChars` int(8) NOT NULL DEFAULT '200',
  `reduceDownloadStock` enum('yes','no') NOT NULL DEFAULT 'no',
  `enableBBCode` enum('yes','no') NOT NULL DEFAULT 'yes',
  `downloadFolder` varchar(100) NOT NULL DEFAULT '',
  `downloadRestrictIP` enum('yes','no') NOT NULL DEFAULT 'no',
  `downloadRestrictIPLog` enum('yes','no') NOT NULL DEFAULT 'no',
  `downloadRestrictIPCnt` int(7) NOT NULL DEFAULT '0',
  `downloadRestrictIPLock` int(7) NOT NULL DEFAULT '0',
  `downloadRestrictIPMail` enum('yes','no') NOT NULL DEFAULT 'no',
  `downloadRestrictIPGlobal` text default null,
  `parentCatHomeDisplay` enum('yes','no') NOT NULL DEFAULT 'no',
  `isbnAPI` varchar(50) NOT NULL DEFAULT '',
  `offerInsurance` enum('yes','no') NOT NULL DEFAULT 'no',
  `insuranceAmount` varchar(10) NOT NULL DEFAULT '',
  `insuranceFilter` char(3) NOT NULL DEFAULT '',
  `insuranceOptional` enum('yes','no') NOT NULL DEFAULT 'no',
  `insuranceValue` varchar(20) NOT NULL DEFAULT '',
  `insuranceInfo` text default null,
  `freeTextDisplay` varchar(10) NOT NULL DEFAULT '',
  `excludeFreePop` enum('yes','no') NOT NULL DEFAULT 'no',
  `priceTextDisplay` varchar(100) NOT NULL DEFAULT '',
  `en_sitemap` enum('yes','no') NOT NULL DEFAULT 'yes',
  `cubeUrl` varchar(250) NOT NULL DEFAULT '',
  `cubeAPI` varchar(250) NOT NULL DEFAULT '',
  `guardianUrl` varchar(250) not null default '',
  `guardianAPI` varchar(250) not null default '',
  `minCheckoutAmount` varchar(50) NOT NULL DEFAULT '',
  `showAttrStockLevel` enum('yes','no') NOT NULL DEFAULT 'no',
  `productStockThreshold` int(5) NOT NULL DEFAULT '30',
  `autoClear` int(3) NOT NULL DEFAULT '7',
  `batchMail` text default null,
  `freeAltRedirect` varchar(250) NOT NULL DEFAULT '',
  `menuCatCount` enum('yes','no') NOT NULL DEFAULT 'no',
  `menuBrandCount` enum('yes','no') NOT NULL DEFAULT 'no',
  `catGiftPos` varchar(10) NOT NULL DEFAULT 'end',
  `showBrands` enum('yes','no') NOT NULL DEFAULT 'yes',
  `minPassValue` int(5) NOT NULL DEFAULT '8',
  `en_wish` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tweetlimit` int(5) NOT NULL DEFAULT '10',
  `forcePass` enum('yes','no') NOT NULL DEFAULT 'yes',
  `en_create` enum('yes','no') NOT NULL DEFAULT 'yes',
  `en_create_mail` enum('yes','no') NOT NULL DEFAULT 'yes',
  `pdf` enum('yes','no') NOT NULL DEFAULT 'yes',
  `en_close` enum('yes','no') NOT NULL DEFAULT 'yes',
  `cache` enum('yes','no') NOT NULL DEFAULT 'yes',
  `cachetime` varchar(10) NOT NULL DEFAULT '30',
  `tweet` enum('yes','no') NOT NULL DEFAULT 'yes',
  `presalenotify` enum('yes','no') NOT NULL DEFAULT 'no',
  `presaleemail` text default null,
  `layout` enum('grid','list') NOT NULL DEFAULT 'list',
  `coupontax` enum('yes','no') NOT NULL DEFAULT 'yes',
  `shipopts` text default null,
  `tc` enum('yes','no') NOT NULL DEFAULT 'no',
  `tctext` text default null,
  `tradeship` enum('yes','no') not null default 'no',
  `salereorder` enum('yes','no') not null default 'yes',
  `hurrystock` int(7) not null default '0',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'settings';
  mc_logDBError(DB_PREFIX . 'settings', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...SOCIAL..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "social`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "social` (
  `id` int(5) not null auto_increment,
  `desc` varchar(50) not null default '',
  `param` text default null,
  `value` text default null,
  primary key (`id`),
  key `descK` (`desc`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'social';
  mc_logDBError(DB_PREFIX . 'social', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...STATUSES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "statuses`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saleID` int(7) NOT NULL DEFAULT '0',
  `statusNotes` text default null,
  `dateAdded` date NOT NULL DEFAULT '0000-00-00',
  `timeAdded` time NOT NULL DEFAULT '00:00:00',
  `orderStatus` varchar(20) NOT NULL DEFAULT '',
  `adminUser` varchar(100) NOT NULL DEFAULT '',
  `visacc` enum('yes','no') NOT NULL DEFAULT 'no',
  `account` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `saleid_index` (`saleID`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'statuses';
  mc_logDBError(DB_PREFIX . 'statuses', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...STATUS_TEXT..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "status_text`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "status_text` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `statTitle` varchar(250) NOT NULL DEFAULT '',
  `statText` text default null,
  `ref` varchar(250) not null default '',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'status_text';
  mc_logDBError(DB_PREFIX . 'status_text', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...TARE..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "tare`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tare` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `rWeightFrom` varchar(50) NOT NULL DEFAULT '0',
  `rWeightTo` varchar(50) NOT NULL DEFAULT '0',
  `rCost` varchar(20) NOT NULL DEFAULT '',
  `rService` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `from_index` (`rWeightFrom`),
  KEY `to_index` (`rWeightTo`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'tare';
  mc_logDBError(DB_PREFIX . 'tare', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...THEMES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "themes`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "themes` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `theme` varchar(200) NOT NULL DEFAULT '',
  `from` date NOT NULL DEFAULT '0000-00-00',
  `to` date NOT NULL DEFAULT '0000-00-00',
  `enabled` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `from_index` (`from`),
  KEY `to_index` (`to`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'themes';
  mc_logDBError(DB_PREFIX . 'themes', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...TRACKER..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "tracker`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tracker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `code` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'tracker';
  mc_logDBError(DB_PREFIX . 'tracker', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...TRACKER_CLICKS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "tracker_clicks`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tracker_clicks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '',
  `clicked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'tracker_clicks';
  mc_logDBError(DB_PREFIX . 'tracker_clicks', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...USERS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "users`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL DEFAULT '',
  `userPass` varchar(250) NOT NULL DEFAULT '',
  `userEmail` text default null,
  `userType` enum('admin','restricted') NOT NULL DEFAULT 'restricted',
  `userPriv` enum('yes','no') NOT NULL DEFAULT 'no',
  `accessPages` text default null,
  `enableUser` enum('yes','no') NOT NULL DEFAULT 'no',
  `lastLogin` varchar(250) NOT NULL DEFAULT '',
  `userNotify` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tweet` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'users';
  mc_logDBError(DB_PREFIX . 'users', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ZONES..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "zones`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "zones` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `zName` varchar(250) NOT NULL DEFAULT '',
  `zCountry` int(5) NOT NULL DEFAULT '0',
  `zRate` varchar(10) NOT NULL DEFAULT '',
  `zShipping` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `ctry_index` (`zCountry`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'zones';
  mc_logDBError(DB_PREFIX . 'zones', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

//============================================================
// INSTALL TABLE...ZONE_AREAS..
//============================================================

mysqli_query($GLOBALS["___msw_sqli"], "DROP TABLE IF EXISTS `" . DB_PREFIX . "zone_areas`");
$query = mysqli_query($GLOBALS["___msw_sqli"], "
CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "zone_areas` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `inZone` int(5) NOT NULL DEFAULT '0',
  `areaName` varchar(200) NOT NULL DEFAULT '',
  `zCountry` int(5) NOT NULL DEFAULT '0',
  `zRate` varchar(10) NOT NULL DEFAULT '',
  `zShipping` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  KEY `zone_index` (`inZone`),
  KEY `ctry_index` (`zCountry`)
) $tableType");

if (!$query) {
  $tableD[] = DB_PREFIX . 'zone_areas';
  mc_logDBError(DB_PREFIX . 'zone_areas', ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), __LINE__, __FILE__);
}

?>