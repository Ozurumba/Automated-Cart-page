<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// TABLES
//==========================

if (mswCheckTable('flat') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "flat` (
  `id` int(10) unsigned not null auto_increment,
  `inZone` int(8) not null default '0',
  `rate` varchar(30) not null default '',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`),
  index `zone_index` (`inZone`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'flat` table created');
}
if (mswCheckTable('percent') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "percent` (
  `id` int(10) unsigned not null auto_increment,
  `inZone` int(8) not null default '0',
  `priceFrom` varchar(30) not null default '',
  `priceTo` varchar(30) not null default '',
  `percentage` varchar(30) not null default '',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`),
  index `zone_index` (`inZone`),
  index `from_index` (`priceFrom`),
  index `to_index` (`priceTo`),
  index `en_index` (`enabled`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'percent` table created');
}
if (mswCheckTable('newstemplates') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "newstemplates` (
  `id` int(10) unsigned not null auto_increment,
  `name` varchar(250) not null default '',
  `email` varchar(250) not null default '',
  `subject` varchar(250) not null default '',
  `html` text default null,
  `plain` text default null,
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'newstemplates` table created');
}
if (mswCheckTable('methods') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "methods` (
  `id` int(3) not null auto_increment,
  `orderby` int(3) not null default '0',
  `method` varchar(100) not null default '',
  `display` varchar(100) not null default '',
  `status` enum('yes','no') not null default 'yes',
  `defmeth` enum('yes','no') not null default 'no',
  `liveserver` varchar(250) not null default '',
  `sandboxserver` varchar(250) not null default '',
  `plaintext` text default null,
  `htmltext` text default null,
  `info` text default null,
  `redirect` varchar(250) not null default '',
  `image` varchar(100) not null default '',
  `docs` varchar(100) not null default '',
  `webpage` varchar(100) not null default '',
  `statuses` text default null,
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'methods` table created');
}
if (mswCheckTable('methods_params') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "methods_params` (
  `id` int(3) not null auto_increment,
  `method` varchar(200) not null default '',
  `param` varchar(200) not null default '',
  `value` varchar(250) not null default '',
  primary key (`id`),
  index `mthd_index` (`method`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'methods_params` table created');
}
if (mswCheckTable('tare') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "tare` (
  `id` int(4) unsigned not null auto_increment,
  `rWeightFrom` varchar(50) not null default '0',
  `rWeightTo` varchar(50) not null default '0',
  `rCost` varchar(20) not null default '',
  `rService` int(6) not null default '0',
  primary key (`id`),
  index `from_index` (`rWeightFrom`),
  index `to_index` (`rWeightTo`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'tare` table created');
}
if (mswCheckTable('themes') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "themes` (
  `id` int(7) unsigned not null auto_increment,
  `theme` varchar(200) not null default '',
  `from` date not null default '0000-00-00',
  `to` date not null default '0000-00-00',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`),
  index `from_index` (`from`),
  index `to_index` (`to`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'themes` table created');
}
if (mswCheckTable('giftcerts') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "giftcerts` (
  `id` mediumint(10) unsigned not null auto_increment,
  `name` varchar(250) not null default '',
  `value` varchar(10) not null default '',
  `image` varchar(250) not null default '',
  `orderBy` int(5) not null default '0',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`)
) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'giftcerts` table created');
}
if (mswCheckTable('giftcodes') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "giftcodes` (
  `id` int(10) unsigned not null auto_increment,
  `saleID` int(10) not null default '0',
  `purchaseID` int(11) not null default '0',
  `giftID` int(10) not null default '0',
  `code` varchar(200) not null default '',
  `value` varchar(10) not null default '',
  `redeemed` varchar(10) not null default '',
  `from_name` varchar(100) not null default '',
  `from_email` varchar(100) not null default '',
  `to_name` varchar(100) not null default '',
  `to_email` varchar(100) not null default '',
  `message` text default null,
  `dateAdded` date not null default '0000-00-00',
  `notes` text default null,
  `enabled` enum('yes','no') not null default 'yes',
  `active` enum('yes','no') not null default 'no',
  primary key (`id`),
  index `gift_index` (`giftID`),
  index `sale_index` (`saleID`),
  index `code_index` (`code`),
  index `purc_index` (`purchaseID`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'giftcodes` table created');
}
if (mswCheckTable('per') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "per` (
  `id` int(10) unsigned not null auto_increment,
  `inZone` int(8) not null default '0',
  `rate` varchar(30) not null default '',
  `item` varchar(30) not null default '',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`),
  index `zone_index` (`inZone`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'per` table created');
}
if (mswCheckTable('tracker') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "tracker` (
  `id` int(10) unsigned not null auto_increment,
  `name` varchar(250) not null default '',
  `code` varchar(100) not null default '',
  primary key (`id`),
  index `code` (`code`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'tracker` table created');
}
if (mswCheckTable('tracker_clicks') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "tracker_clicks` (
  `id` int(10) unsigned not null auto_increment,
  `code` varchar(100) not null default '',
  `clicked` datetime not null default '0000-00-00 00:00:00',
  `ip` varchar(250) not null default '',
  primary key (`id`),
  index `code` (`code`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'tracker_clicks` table created');
}
if (mswCheckTable('accounts') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "accounts` (
  `id` int(8) not null auto_increment,
  `name` varchar(200) not null default '',
  `created` date not null default '0000-00-00',
  `email` varchar(250) not null default '',
  `pass` varchar(40) not null default '',
  `enabled` enum('yes','no') not null default 'yes',
  `verified` enum('yes','no') not null default 'no',
  `timezone` varchar(50) not null default '0',
  `ip` text default null,
  `notes` text default null,
  `reason` text default null,
  `system1` varchar(250) not null default '',
  `system2` varchar(250) not null default '',
  `language` varchar(100) not null default 'english',
  `currency` varchar(100) not null default '',
  `enablelog` enum('yes','no') not null default 'yes',
  `newsletter` enum('yes','no') NOT NULL DEFAULT 'no',
  `message` text default null,
  `messageexp` date not null default '0000-00-00',
  `type` enum('personal','trade') not null default 'personal',
  `tradediscount` varchar(5) not null default '',
  `minqty` varchar(10) not null default '',
  `maxqty` varchar(10) not null default '0',
  `stocklevel` varchar(10) not null default '',
  `mincheckout` varchar(20) not null default '0.00',
  `trackcode` varchar(100) not null default '',
  `params` text default null,
  `recent` text default null,
  `wishtext` text default null,
  primary key (`id`),
  index `em_index` (`email`),
  index `nm_index` (`name`),
  index `ps_index` (`pass`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'accounts` table created');
}
if (mswCheckTable('accounts_search') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "accounts_search` (
  `id` int(11) unsigned not null auto_increment,
  `account` int(6) not null default '0',
  `code` varchar(50) not null default '',
  `saved` date not null default '0000-00-00',
  `name` varchar(50) not null default '',
  primary key (`id`),
  index `code_index` (`code`),
  index `acc_index` (`account`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'accounts_search` table created');
}
if (mswCheckTable('accounts_wish') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "accounts_wish` (
  `id` int(11) unsigned not null auto_increment,
  `account` int(6) not null default '0',
  `product` int(8) not null default '0',
  `saved` date not null default '0000-00-00',
  primary key (`id`),
  index `account_index` (`account`),
  index `prod_index` (`product`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'accounts_wish` table created');
}
if (mswCheckTable('addressbook') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "addressbook` (
  `id` int(5) not null auto_increment,
  `account` int(8) not null default '0',
  `nm` varchar(250) not null default '',
  `em` varchar(250) not null default '',
  `addr1` varchar(250) not null default '',
  `addr2` varchar(250) not null default '',
  `addr3` varchar(250) not null default '',
  `addr4` varchar(250) not null default '',
  `addr5` varchar(250) not null default '',
  `addr6` varchar(250) not null default '',
  `addr7` varchar(250) not null default '',
  `addr8` varchar(250) not null default '',
  `default` enum('yes','no') not null default 'yes',
  `type` enum('bill','ship') not null default 'bill',
  `zone` int(8) not null default '0',
  primary key (`id`),
  index `ac_index` (`account`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'addressbook` table created');
}
if (mswCheckTable('dropshippers') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "dropshippers` (
  `id` int(10) unsigned not null auto_increment,
  `name` varchar(100) not null default '',
  `emails` text default null,
  `status` text default null,
  `method` text default null,
  `salestatus` varchar(100) not null default '',
  `enable` enum('yes','no') not null default 'no',
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'dropshippers` table created');
}
if (mswCheckTable('pdf') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "pdf` (
  `id` tinyint(1) not null auto_increment,
  `company` text default null,
  `address` varchar(250) not null default '',
  `font` varchar(50) not null default 'helvetica',
  `dir` enum('ltr','rtl') not null default 'ltr',
  `meta` varchar(20) not null default 'utf-8',
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'pdf` table created');
}
if (mswCheckTable('prod_brand') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "prod_brand` (
  `id` int(4) unsigned not null auto_increment,
  `product` int(8) not null default '0',
  `brand` int(8) not null default '0',
  primary key (`id`),
  index `prod_index` (`product`),
  index `brd_index` (`brand`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'prod_brand` table created');
}
if (mswCheckTable('qtyrates') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "qtyrates` (
  `id` int(10) unsigned not null auto_increment,
  `inZone` int(8) not null default '0',
  `qtyFrom` int(6) not null default '0',
  `qtyTo` int(6) not null default '0',
  `rate` varchar(30) not null default '',
  `enabled` enum('yes','no') not null default 'yes',
  primary key (`id`),
  index `zone_index` (`inzone`),
  index `from_index` (`qtyFrom`),
  index `to_index` (`qtyTo`),
  index `en_index` (`enabled`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'qtyrates` table created');
}
if (mswCheckTable('blog') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "blog` (
  `id` int(7) not null auto_increment,
  `title` text default null,
  `message` text default null,
  `created` int(13) not null default '0',
  `published` int(13) not null default '0',
  `autodelete` int(13) not null default '0',
  `enabled` enum('yes','no') not null default 'no',
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'blog` table created');
}
if (mswCheckTable('boxes') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "boxes` (
  `id` tinyint(1) not null auto_increment,
  `ident` varchar(250) not null default '',
  `name` varchar(250) not null default '',
  `status` enum('yes','no') not null default 'yes',
  `tmp` varchar(250) not null default '',
  `orderby` int(8) not null default '0',
  primary key (`id`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'boxes` table created');
}
if (mswCheckColumn('entry_log', 'userName') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "drop table `" . DB_PREFIX . "entry_log`");
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "entry_log` (
  `id` int(7) unsigned not null auto_increment,
  `userid` int(8) not null default '0',
  `logdatetime` datetime not null default '0000-00-00 00:00:00',
  `ip` varchar(250) not null default '',
  `type` varchar(250) not null default '',
  PRIMARY KEY (`id`),
  KEY `id_index` (`userid`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'entry_log` table dropped and re-created');
}
if (mswCheckTable('social') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "social` (
  `id` int(5) not null auto_increment,
  `desc` varchar(50) not null default '',
  `param` text default null,
  `value` text default null,
  primary key (`id`),
  key `descK` (`desc`)
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'social` table created');
}
if (mswCheckTable('cfg') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "create table `" . DB_PREFIX . "cfg` (
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
  ) " . $tableType);
  mc_upgradeLog('Completed: `' . DB_PREFIX . 'cfg` table created');
}

?>