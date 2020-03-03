<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

$data = array();

//=============================
// INSTALL DATA TO TABLES
//=============================

foreach (array('countries','currencies','newpages','boxes','methods','methods_params','social') AS $ins) {
  @mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . $ins . "`");
  $qT = mysqli_query($GLOBALS["___msw_sqli"], str_replace('{prefix}',DB_PREFIX,@file_get_contents(PATH . 'control/sql/' . $ins . '.sql')));
  if (!$qT) {
    $data[] = DB_PREFIX . $ins;
    mc_logDBError(DB_PREFIX . $ins,((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)),((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)),__LINE__,__FILE__,'Insert');
  }
}

//=========================
// INSTALL SETTINGS
//=========================

$storePath = 'http://www.example.com/cart';
if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
  $storePath   = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'install')-1);
}
$serverPath =  substr(PATH, 0, strpos(PATH,'install')-1);
$dataArr    =  array(
 'admin'    => 'Add your own footer in your admin control panel: System > Edit Footers',
 'public'   => 'Add your own footer in your admin control panel: System > Edit Footers',
 'slider'   => 'a:4:{s:3:"min";s:1:"0";s:3:"max";s:3:"300";s:5:"start";s:1:"5";s:3:"end";s:3:"100";}',
 'ship'     => '',
 'website'  => (isset($_SESSION['storePost']['website']) ? $_SESSION['storePost']['website'] : SCRIPT_NAME . ' E-Store'),
 'email'    => (isset($_SESSION['storePost']['email']) ? $_SESSION['storePost']['email'] : ''),
 'tmzne'    => (@date_default_timezone_get() ? date_default_timezone_get() : 'Europe/London')
);
@mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . "settings`");
$qT = mysqli_query($GLOBALS["___msw_sqli"], "INSERT IGNORE INTO `" . DB_PREFIX . "settings` (`website`, `theme`, `theme2`, `email`, `addEmails`, `serverPath`,
`languagePref`, `logoName`, `baseCurrency`, `currencyDisplayPref`, `logErrors`, `gatewayMode`, `enableSSL`, `enablePickUp`, `shipCountry`,
`logFolderName`, `ifolder`, `metaKeys`, `metaDesc`, `enableCart`, `offlineDate`, `offlineText`, `offlineIP`, `en_rss`, `rssScroller`, `rssScrollerUrl`,
`rssScrollerLimit`, `en_modr`, `cName`, `cWebsite`, `cTel`, `cFax`, `cAddress`, `cOther`, `cReturns`, `smtp`, `smtp_host`, `smtp_user`, `smtp_pass`,
`smtp_port`, `smtp_security`, `smtp_from`, `smtp_email`, `smtp_debug`, `homeProdValue`, `homeProdType`, `homeProdCats`, `homeProdIDs`, `adminFooter`,
`publicFooter`, `prodKey`, `encoderVersion`, `activateEmails`, `saleComparisonItems`, `productsPerPage`, `mostPopProducts`, `mostPopPref`, `latestProdLimit`,
`latestProdDuration`, `searchLowStockLimit`, `enSearchLog`, `savedSearches`, `searchSlider`, `searchTagsOnly`, `jsDateFormat`, `jsWeekStart`, `timezone`,
`mysqlDateFormat`, `systemDateFormat`, `rssFeedLimit`, `minInvoiceDigits`, `invoiceNo`, `pendingAsComplete`, `freeShipThreshold`, `enableZip`,
`zipCreationLimit`, `zipLimit`, `zipTimeOut`, `zipMemoryLimit`, `zipAdditionalFolder`, `enEntryLog`, `softwareVersion`, `smartQuotes`, `hitCounter`,
`menuSubCats`, `adminFolderName`, `twitterLatest`, `globalDiscount`, `globalDiscountExpiry`, `enableRecentView`, `freeDownloadRestriction`, `thumbWidth`,
`thumbHeight`, `thumbQuality`, `thumbQualityPNG`, `aspectRatio`, `renamePics`, `tmbPrefix`, `imgPrefix`, `showOutofStock`, `enableCheckout`,
`globalDownloadPath`, `maxProductChars`, `reduceDownloadStock`, `enableBBCode`, `downloadFolder`, `downloadRestrictIP`, `downloadRestrictIPLog`,
`downloadRestrictIPCnt`, `downloadRestrictIPLock`, `downloadRestrictIPMail`, `downloadRestrictIPGlobal`, `parentCatHomeDisplay`, `isbnAPI`, `offerInsurance`,
`insuranceAmount`, `insuranceFilter`, `insuranceOptional`, `insuranceValue`, `insuranceInfo`, `freeTextDisplay`, `excludeFreePop`, `priceTextDisplay`,
`en_sitemap`, `cubeUrl`, `cubeAPI`, `minCheckoutAmount`, `showAttrStockLevel`, `productStockThreshold`, `autoClear`, `batchMail`, `freeAltRedirect`,
`menuCatCount`, `menuBrandCount`, `catGiftPos`, `showBrands`, `minPassValue`, `en_wish`, `tweetlimit`, `forcePass`, `en_create`, `en_create_mail`, `pdf`,
`en_close`, `cache`, `cachetime`, `tweet`, `presalenotify`, `presaleemail`, `layout`, `coupontax`, `shipopts`, `tc`, `tctext`
) VALUES (
'" . mc_safeSQL($dataArr['website']) . "', '_theme_default', '_theme_default', '" . mc_safeSQL($dataArr['email']) . "', '', '" . mc_safeSQL($serverPath) . "', 'english', '', 'GBP', '&pound;{PRICE}',
'yes', 'test', 'no', 'yes', '0', 'logs', '" . mc_safeSQL($storePath) . "', '', '', 'yes', '0000-00-00', '', '', 'yes', 'no', '', 10, 'no', '" . mc_safeSQL($dataArr['website']) . "',
'" . mc_safeSQL($storePath) . "', '01234 456789', '01345 567890', '1 Company Street\r\nSomeplace\r\nSomewhere\r\nPost Code', '', 'Return info goes here..', 'yes', '', '', '', '587', '', '', '', 'no', 10, 'latest', '', '', '" . mc_safeSQL($dataArr['admin']) . "', '" . mc_safeSQL($dataArr['public']) . "', '" . mc_safeSQL($prodKey) . "', '1.0', 'yes', 10, 8, 10,
'sales', 36, 'months', 5, 'yes', 7, '" . mc_safeSQL($dataArr['slider']) . "', 'no', 'DD/MM/YYYY', 0, '" . mc_safeSQL($dataArr['tmzne']) . "', '%e %b %Y', 'F j, Y', 50, 5, 0, 'no', '0.00',
'yes', '0', 2, 0, 0, 'additional-zip', 'yes', '" . mc_safeSQL(SCRIPT_VERSION) . "', 'no', 'yes', 'yes', 'admin', 'no', '0', '0000-00-00', 'yes', '0', 230, 200, 96, 9, 'yes',
'yes', 'tmb_', 'img_', 'cat', 'yes', '" . mc_safeSQL($serverPath) . "', 300, 'no', 'yes', 'product-downloads', 'no', 'yes', 0, 5, 'yes', '', 'yes', '',
'yes', '10', 'op2', 'no', '0.00', '', 'FREE', 'yes', '', 'yes', '', '', '0.00', 'no', 30, 30, NULL, '', 'no', 'no', '16', 'no', 10, 'yes', 5, 'yes',
'yes', 'yes', 'yes', 'yes', 'no', '30', 'no', 'no', '', 'list', 'yes', '', 'no', '')");

if (!$qT) {
  $data[] = DB_PREFIX . 'settings';
  mc_logDBError(DB_PREFIX . 'settings',((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)),((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)),__LINE__,__FILE__,'Insert');
}

@mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . "pdf`");
$qT = mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "pdf` (`company`, `address`, `font`, `dir`, `meta`) VALUES ('', '', 'helvetica', 'ltr', 'utf-8')");

if (!$qT) {
  $data[] = DB_PREFIX . 'pdf';
  mc_logDBError(DB_PREFIX . 'pdf',((is_object($GLOBALS["___msw_sqli"])) ? mysqli_error($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)),((is_object($GLOBALS["___msw_sqli"])) ? mysqli_errno($GLOBALS["___msw_sqli"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)),__LINE__,__FILE__,'Insert');
}

?>