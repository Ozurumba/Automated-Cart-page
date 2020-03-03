<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// SYSTEM DATA
//==========================

// New campaign columns..
if (mswCheckColumn('campaigns', 'categories') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "campaigns` add column `categories` text default null");
  mc_upgradeLog('Completed: categories column added to `' . DB_PREFIX . 'campaigns` table');
}

// Pay status/new page columns and updates..
if (mswCheckColumn('paystatuses', 'homepage') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "paystatuses` add column `homepage` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: homepage column added to `' . DB_PREFIX . 'paystatuses` table');
}

@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "paystatuses` set 'pMethod` = 'twocheckout' where `pMethod` = '2checkout'");
if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: 2checkout value updated to twocheckout in `' . DB_PREFIX . 'paystatuses` table');
}

if (mswCheckColumn('newpages', 'rwslug') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "newpages` add column `rwslug` varchar(250) not null default ''");
  mc_upgradeLog('Completed: rwslug column added to `' . DB_PREFIX . 'newpages` table');
}

if (mswCheckColumn('newpages', 'trade') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "newpages` add column `trade` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: trade column added to `' . DB_PREFIX . 'newpages` table');
}

// New banner columns..
if (mswCheckColumn('banners', 'bannerCats') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "banners` add column `bannerCats` text default null");
  mc_upgradeLog('Completed: bannerCats column added to `' . DB_PREFIX . 'banners` table');
}
if (mswCheckColumn('banners', 'bannerHome') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "banners` add column `bannerHome` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: bannerHome column added to `' . DB_PREFIX . 'banners` table');
}
if (mswCheckColumn('banners', 'bannerFrom') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "banners` add column `bannerFrom` date not null default '0000-00-00'");
  mc_upgradeLog('Completed: bannerFrom column added to `' . DB_PREFIX . 'banners` table');
}
if (mswCheckColumn('banners', 'bannerTo') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "banners` add column `bannerTo` date not null default '0000-00-00'");
  mc_upgradeLog('Completed: bannerTo column added to `' . DB_PREFIX . 'banners` table');
}
if (mswCheckColumn('banners', 'trade') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "banners` add column `trade` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: trade column added to `' . DB_PREFIX . 'banners` table');
}

?>