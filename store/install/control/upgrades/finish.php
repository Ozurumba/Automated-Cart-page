<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// FINISH
//==========================

// Brands..
if (mswCheckColumn('brands', 'rwslug') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "brands` add column `rwslug` varchar(250) not null default ''");
  mc_upgradeLog('Completed: rwslug column added to `' . DB_PREFIX . 'brands` table');
}
if (mswCheckColumnType('brands', 'bCat', 'varchar') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "brands` change column `bCat` `bCat` varchar(50) not null default 'all' after `name`");
  mc_upgradeLog('Completed: bCat column changed in `' . DB_PREFIX . 'brands` table');
}

// Search index..
if (mswCheckColumn('search_index', 'filters') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "search_index` add column `filters` text default null");
  mc_upgradeLog('Completed: filters column added to `' . DB_PREFIX . 'search_index` table');
}

// Update mp3 paths that start "templates/"
@mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "mp3` SET
`fileFolder` = CONCAT('content/', SUBSTRING(`fileFolder`,11))
WHERE SUBSTRING(`fileFolder`, 1, 9) = 'templates'
");

if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: Updated existing mp3 paths that had old templates path');
}

// Clear old contact page..
@mysqli_query($GLOBALS["___msw_sqli"], "delete from `" . DB_PREFIX . "newpages` where `id` = 1");

if (@mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
  mc_upgradeLog('Completed: Old contact page has been deleted');
}

// Default row for pdf table..
if (mc_rowCount('pdf') == 0) {
  @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "pdf` values (1, null, '', 'helvetica', 'ltr', 'utf-8')");
  mc_upgradeLog('Completed: added default data to `' . DB_PREFIX . 'pdf` table');
}

// Add new columns to search log
if (mswCheckColumn('search_log', 'ip') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "search_log` add column `ip` varchar(250) not null default ''");
  mc_upgradeLog('Completed: ip column added to `' . DB_PREFIX . 'search_log` table');
}

?>