<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//==========================
// SETTINGS
//==========================

if (!property_exists($SETTINGS, 'theme')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `theme` varchar(100) not null default '_theme_default' after `website`");
  mc_upgradeLog('Completed: theme column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'theme2')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `theme2` varchar(100) not null default '_theme_default' after `theme`");
  mc_upgradeLog('Completed: theme2 column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tradetheme')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tradetheme` varchar(100) not null default '' after `theme2`");
  mc_upgradeLog('Completed: tradetheme column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'offlineIP')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `offlineIP` text default null after `offlineText`");
  mc_upgradeLog('Completed: offlineIP column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'cubeUrl')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `cubeUrl` varchar(250) not null default ''");
  mc_upgradeLog('Completed: cubeUrl column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'cubeAPI')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `cubeAPI` varchar(250) not null default ''");
  mc_upgradeLog('Completed: cubeAPI column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'insuranceOptional')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `insuranceOptional` enum('yes','no') not null default 'no' after `insuranceFilter`");
  mc_upgradeLog('Completed: insuranceOptional column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'insuranceValue')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `insuranceValue` varchar(20) not null default '' after `insuranceOptional`");
  mc_upgradeLog('Completed: insuranceValue column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'insuranceInfo')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `insuranceInfo` text default null after `insuranceValue`");
  mc_upgradeLog('Completed: insuranceInfo column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'twitterUser')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `twitterUser` varchar(50) not null default '' after `twitterLink`");
  mc_upgradeLog('Completed: twitterUser column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'twitterLatest')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `twitterLatest` enum('yes','no') not null default 'no' after `twitterUser`");
  mc_upgradeLog('Completed: twitterLatest column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'cReturns')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `cReturns` text default null after `cOther`");
  mc_upgradeLog('Completed: cReturns column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'searchSlider')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `searchSlider` text default null after `savedSearches`");
  mc_upgradeLog('Completed: searchSlider column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'searchTagsOnly')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `searchTagsOnly` enum('yes','no') not null default 'no' after `searchSlider`");
  mc_upgradeLog('Completed: searchTagsOnly column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'minCheckoutAmount')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `minCheckoutAmount` varchar(50) not null default ''");
  mc_upgradeLog('Completed: minCheckoutAmount column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'showAttrStockLevel')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `showAttrStockLevel` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: showAttrStockLevel column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'qtyStockThreshold')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `qtyStockThreshold` int(5) not null default '50'");
  mc_upgradeLog('Completed: qtyStockThreshold column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'productStockThreshold')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `productStockThreshold` int(5) not null default '30'");
  mc_upgradeLog('Completed: productStockThreshold column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'rssScroller')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `rssScroller` enum('yes','no') not null default 'no' after `en_rss`");
  mc_upgradeLog('Completed: rssScroller column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'rssScrollerUrl')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `rssScrollerUrl` varchar(250) not null default '' after `rssScroller`");
  mc_upgradeLog('Completed: rssScrollerUrl column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'rssScrollerLimit')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `rssScrollerLimit` int(3) not null default '10' after `rssScrollerUrl`");
  mc_upgradeLog('Completed: rssScrollerLimit column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'autoClear')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `autoClear` int(3) not null default '7'");
  mc_upgradeLog('Completed: autoClear column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'batchMail')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `batchMail` text default null");
  mc_upgradeLog('Completed: batchMail column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'freeAltRedirect')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `freeAltRedirect` varchar(250) not null default ''");
  mc_upgradeLog('Completed: freeAltRedirect column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'aspectRatio')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `aspectRatio` enum('yes','no') not null default 'yes' after `thumbQualityPNG`");
  mc_upgradeLog('Completed: aspectRatio column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'renamePics')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `renamePics` enum('yes','no') not null default 'yes' after `aspectRatio`");
  mc_upgradeLog('Completed: renamePics column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tmbPrefix')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tmbPrefix` varchar(100) not null default 'tmb_' after `renamePics`");
  mc_upgradeLog('Completed: tmbPrefix column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'imgPrefix')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `imgPrefix` varchar(100) not null default 'img_' after `tmbPrefix`");
  mc_upgradeLog('Completed: imgPrefix column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'leftBoxOrder')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` change `leftBoxOrder` `leftBoxOrder` text default null");
  mc_upgradeLog('Completed: leftBoxOrder column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'leftBoxCustom')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `leftBoxCustom` text default null after `leftBoxOrder`");
  mc_upgradeLog('Completed: leftBoxCustom column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'menuCatCount')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `menuCatCount` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: menuCatCount column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'menuBrandCount')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `menuBrandCount` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: menuBrandCount column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'catGiftPos')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `catGiftPos` varchar(10) not null default 'end'");
  mc_upgradeLog('Completed: catGiftPos column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'invoiceNo')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `invoiceNo` int(10) not null default '1' after `minInvoiceDigits`");
  mc_upgradeLog('Completed: invoiceNo column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'showBrands')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `showBrands` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: showBrands column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIP')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIP` enum('yes','no') not null default 'no' after `downloadFolder`");
  mc_upgradeLog('Completed: downloadRestrictIP column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIPLog')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIPLog` enum('yes','no') not null default 'no' after `downloadRestrictIP`");
  mc_upgradeLog('Completed: downloadRestrictIPLog column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIPCnt')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIPCnt` int(7) not null default '0' after `downloadRestrictIPLog`");
  mc_upgradeLog('Completed: downloadRestrictIPCnt column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIPLock')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIPLock` int(7) not null default '0' after `downloadRestrictIPCnt`");
  mc_upgradeLog('Completed: downloadRestrictIPLock column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIPMail')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIPMail` enum('yes','no') not null default 'no' after `downloadRestrictIPLock`");
  mc_upgradeLog('Completed: downloadRestrictIPMail column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'downloadRestrictIPGlobal')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `downloadRestrictIPGlobal` text default null after `downloadRestrictIPMail`");
  mc_upgradeLog('Completed: downloadRestrictIPGlobal column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'smtp_security')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `smtp_security` varchar(10) not null default '' after `smtp_port`");
  mc_upgradeLog('Completed: smtp_security column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'smtp_from')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `smtp_from` varchar(250) not null default '' after `smtp_security`");
  mc_upgradeLog('Completed: smtp_from column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'smtp_email')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `smtp_email` varchar(250) not null default '' after `smtp_from`");
  mc_upgradeLog('Completed: smtp_email column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'smtp_debug')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `smtp_debug` enum('yes','no') not null default 'no' after `smtp_email`");
  mc_upgradeLog('Completed: smtp_debug column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'minPassValue')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `minPassValue` int(5) not null default '8'");
  mc_upgradeLog('Completed: minPassValue column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'en_wish')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `en_wish` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: en_wish column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tweetlimit')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tweetlimit` int(5) not null default '10'");
  mc_upgradeLog('Completed: tweetlimit column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'forcePass')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `forcePass` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: forcePass column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'en_create')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `en_create` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: en_create column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'en_create_mail')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `en_create_mail` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: en_create_mail column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'pdf')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `pdf` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: pdf column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'cache')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `cache` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: cache column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'cachetime')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `cachetime` varchar(10) not null default '30'");
  mc_upgradeLog('Completed: cachetime column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tweet')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tweet` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: tweet column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'presalenotify')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `presalenotify` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: presalenotify column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'presaleemail')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `presaleemail` text default null");
  mc_upgradeLog('Completed: presaleemail column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'layout')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `layout` enum('grid','list') not null default 'list'");
  mc_upgradeLog('Completed: layout column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'coupontax')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `coupontax` enum('yes','no') not null default 'yes'");
  mc_upgradeLog('Completed: coupontax column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'shipopts')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `shipopts` text default null");
  mc_upgradeLog('Completed: shipopts column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tc')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tc` enum('yes','no') not null default 'no'");
  mc_upgradeLog('Completed: tc column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tctext')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tctext` text default null");
  mc_upgradeLog('Completed: tctext column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'en_close')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `en_close` enum('yes','no') NOT NULL DEFAULT 'yes'");
  mc_upgradeLog('Completed: en_close column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'tradeship')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `tradeship` enum('yes','no') NOT NULL DEFAULT 'no'");
  mc_upgradeLog('Completed: tradeship column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'salereorder')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `salereorder` enum('yes','no') NOT NULL DEFAULT 'yes'");
  mc_upgradeLog('Completed: salereorder column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'hurrystock')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `hurrystock` int(7) not null default '0'");
  mc_upgradeLog('Completed: hurrystock column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'guardianUrl')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `guardianUrl` varchar(250) not null default ''");
  mc_upgradeLog('Completed: guardianUrl column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'guardianAPI')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `guardianAPI` varchar(250) not null default ''");
  mc_upgradeLog('Completed: guardianAPI column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'timezone')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `timezone` varchar(50) not null default ''");
  mc_upgradeLog('Completed: timezone column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'mailpref')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `mailpref` enum('smtp','php') not null default 'smtp' after `smtp_debug");
  mc_upgradeLog('Completed: mailpref column added to `' . DB_PREFIX . 'settings` table');
}
if (!property_exists($SETTINGS, 'smtp_insec')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` add column `smtp_insec` enum('yes','no') not null default 'no' after `mailpref`");
  mc_upgradeLog('Completed: smtp_insec column added to `' . DB_PREFIX . 'settings` table');
}

// Update new search slider field..
if ($SETTINGS->searchSlider == '') {
  $slider = 'a:4:{s:3:"min";s:1:"0";s:3:"max";s:3:"300";s:5:"start";s:1:"5";s:3:"end";s:3:"100";}';
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "settings` set `searchSlider` = '" . mc_safeSQL($slider) . "'");
  mc_upgradeLog('Completed: alter table ' . DB_PREFIX . 'settings with search slider settings: ' . $slider);
}

// Update new timezone field..
@mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "settings` set `timezone` = '" . (@date_default_timezone_get() ? date_default_timezone_get() : 'Europe/London') . "'");

mc_upgradeLog('Completed: altered table ' . DB_PREFIX . 'settings. Timezone set to: ' . (@date_default_timezone_get() ? date_default_timezone_get() : 'Europe/London'));

// Update new invoice field..
if (!property_exists($SETTINGS, 'invoiceNo') || $SETTINGS->invoiceNo == '1') {
  $S       = @mysqli_fetch_object(@mysqli_query($GLOBALS["___msw_sqli"], "select `invoiceNo` from `" . DB_PREFIX . "sales` order by `invoiceNo` desc limit 1"));
  $invoice = (property_exists($S, 'invoiceNo') ? ($S->invoiceNo + 1) : '1');
  @mysqli_query($GLOBALS["___msw_sqli"], "update `" . DB_PREFIX . "settings` set `invoiceNo` = '{$invoice}'");
  mc_upgradeLog('Completed: altered table ' . DB_PREFIX . 'settings. Invoice no set to: ' . $invoice);
}

// Drop unused columns..
if (property_exists($SETTINGS, 'freeLogging')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `freeLogging`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: freeLogging');
}
if (property_exists($SETTINGS, 'serverTimeAdjustment')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `serverTimeAdjustment`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: serverTimeAdjustment');
}
if (property_exists($SETTINGS, 'isoCurrencyPosition')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `isoCurrencyPosition`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: isoCurrencyPosition');
}
if (property_exists($SETTINGS, 'appendIndex')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `appendIndex`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: appendIndex');
}
if (property_exists($SETTINGS, 'helpTips')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `helpTips`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: helpTips');
}
if (property_exists($SETTINGS, 'sitemapPref')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `sitemappref`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: sitemappref');
}
if (property_exists($SETTINGS, 'flashVideoWidth')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `flashvideowidth`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: flashvideowidth');
}
if (property_exists($SETTINGS, 'flashVideoHeight')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `flashvideoheight`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: flashvideoheight');
}
if (property_exists($SETTINGS, 'contactDisplay')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `contactdisplay`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: contactdisplay');
}
if (property_exists($SETTINGS, 'qtyStockThreshold')) {
  @mysqli_query($GLOBALS["___msw_sqli"], "alter table `" . DB_PREFIX . "settings` drop column `qtyStockThreshold`");
  mc_upgradeLog('Completed: altered table `' . DB_PREFIX . 'settings` dropped column: qtyStockThreshold');
}

?>