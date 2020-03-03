<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// USERS
//==========================

if (mswCheckColumn('users', 'userEmail') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "users` add column `userEmail` text default null");
  mc_upgradeLog('Completed: userEmail column added to `' . DB_PREFIX . 'users` table');
}
if (mswCheckColumn('users', 'userNotify') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "users` add column `userNotify` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: userNotify column added to `' . DB_PREFIX . 'users` table');
}
if (mswCheckColumn('users', 'tweet') == 'no') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "users` add column `tweet` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: tweet column added to `' . DB_PREFIX . 'users` table');
}
if (mswCheckColumnType('users', 'userPass', '40') == 'yes') {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "users` change column `userPass` `userPass` varchar(250) not null default '' after `userName`");
  mc_upgradeLog('Completed: userPass column changed in `' . DB_PREFIX . 'users` table');
}

?>