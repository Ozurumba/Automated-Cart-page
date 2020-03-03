<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// SOCIAL
//==========================

// Add row to new social table if blank..
if (mc_rowCount('social') == 0) {
  @mysqli_query($GLOBALS["___msw_sqli"], "insert into `" . DB_PREFIX . "social` (`desc`, `param`, `value`) values
  ('addthis', 'code', '" . (property_exists($SETTINGS, 'addThisModule') && strpos($SETTINGS->addThisModule, 'script') == false ? mc_safeSQL($SETTINGS->addThisModule) : '') . "'),
  ('disqus', 'disname', '" . (property_exists($SETTINGS, 'disqusShortName') ? mc_safeSQL($SETTINGS->disqusShortName) : '') . "'),
  ('disqus', 'discat', ''),
  ('pushover', 'pushuser', ''),
  ('pushover', 'pushtoken', ''),
  ('facebook', 'fbimage', ''),
  ('facebook', 'fbinsights', ''),
  ('twitter', 'conkey', ''),
  ('twitter', 'consecret', ''),
  ('twitter', 'token', ''),
  ('twitter', 'key', ''),
  ('twitter', 'username', '" . (property_exists($SETTINGS, 'twitterUser') ? mc_safeSQL($SETTINGS->twitterUser) : '') . "'),
  ('links', 'facebook', '" . (property_exists($SETTINGS, 'facebookLink') ? mc_safeSQL($SETTINGS->facebookLink) : '') . "'),
  ('links', 'twitter', '" . (property_exists($SETTINGS, 'twitterLink') ? mc_safeSQL($SETTINGS->twitterLink) : '') . "'),
  ('links', 'instagram', ''),
  ('links', 'youtube', ''),
  ('links', 'reddit', ''),
  ('links', 'pinterest', ''),
  ('links', 'flickr', ''),
  ('struct', 'twitter', 'yes'),
  ('struct', 'fb', 'yes'),
  ('struct', 'google', 'yes')");
}

if (property_exists($SETTINGS, 'addThisModule')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `addThisModule`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: addThisModule');
}
if (property_exists($SETTINGS, 'twitterUser')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `twitterUser`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: twitterUser');
}
if (property_exists($SETTINGS, 'disqusShortName')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `disqusShortName`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: disqusShortName');
}
if (property_exists($SETTINGS, 'disqusDevMode')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `disqusDevMode`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: disqusDevMode');
}
if (property_exists($SETTINGS, 'facebookLink')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `facebookLink`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: facebookLink');
}
if (property_exists($SETTINGS, 'twitterLink')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `twitterLink`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: twitterLink');
}

?>