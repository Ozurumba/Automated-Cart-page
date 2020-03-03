<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// LAYOUT
//==========================

// Add row to new boxes table if blank..
if (mc_rowCount('boxes') == 0) {
  @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "boxes` (`ident`, `name`, `status`, `tmp`, `orderby`) values
  ('points', '', 'yes', '', 1),
  ('popular', '', 'yes', '', 3),
  ('tweets', '', 'yes', '', 5),
  ('recent', '', 'yes', '', 4),
  ('links', '', 'yes', '', 8),
  ('brands', '', 'yes', '', 2),
  ('rss', '', 'yes', '', 6)");
  mc_upgradeLog('Completed: default row added to `' . DB_PREFIX . 'boxes` table');
}

if (property_exists($SETTINGS, 'leftBoxOrder')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `leftBoxOrder`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: leftBoxOrder');
}
if (property_exists($SETTINGS, 'leftBoxCustom')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `leftBoxCustom`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: leftBoxCustom');
}

?>