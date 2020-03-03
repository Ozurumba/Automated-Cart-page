<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// GLOBAL SYSTEM CONSTANTS
include(PATH . 'control/system/constants.php');

// DB CONNECTION
mc_dbConnector();

// LOAD SETTINGS DATA
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));

// CHECK INSTALLER
if (!isset($SETTINGS->languagePref)) {
  header("Location: install/index.php");
  exit;
}

// THEME/LANGUAGE LOADERS
include(PATH . 'control/theme-loader.php');
define('MCLANG', mc_loadLangFile($SETTINGS));

// CACHE CONTROLLER
include(PATH . 'control/classes/class.cache.php');
$MCCACHE = new mcCache($SETTINGS);

// LOAD BASKET PAYMENT METHODS
include(PATH . 'control/gateways/system-payment-methods.php');

// DETECT TIMEZONE
mc_dateTimeDetect($SETTINGS);

// TIMEZONE
include(PATH . 'control/timezones.php');

// MANUAL SCHEMA FIX
mc_manualSchemaFix($SETTINGS);

// CHECK VERSION
mc_scriptCheckVersion($SETTINGS);

// LOAD GLOBAL AND HEADER LANGUAGE FILES
include(MCLANG . 'global.php');
include(MCLANG . 'header.php');
include(MCLANG . 'footer.php');
include(MCLANG . 'version2.1.php');
include(MCLANG . 'version3.0.php');
include(MCLANG . 'version3.4.php');
include(MCLANG . 'version3.7.php');
include(MCLANG . 'checkout.php');
include(MCLANG . 'emails.php');
include(MCLANG . 'gift.php');

// REWRITE RULE PARSER
include(PATH . 'control/classes/class.rewrite.php');
$MCRWR             = new mcRewrite();
$MCRWR->settings   = $SETTINGS;
$MCRWR->parser();

// DECLARE VARS
$cmd              = (isset($_GET['p']) ? $_GET['p'] : 'home');
$page             = (isset($_GET['next']) && $_GET['next'] > 0 ? (int) $_GET['next'] : '1');
$limit            = $page * $SETTINGS->productsPerPage - ($SETTINGS->productsPerPage);
$count            = 0;
$loadJS           = array();
$formErrors       = array();
$eImgError        = array();
$breadcrumbs      = array();
$crumbcount       = 0;
$menuLinksDisplay = array();
$childrenDisplay  = array();
$skipMenuBoxes    = array();
$systemLang       = array(array($SETTINGS->languagePref,$mc_header[9]));
$cfg              = mc_getCFG('affiliate');

// FOR CALLBACKS
$cmd = mc_callBackUrls($cmd);

// SWITCH FOR CHECKOUT LOAD. CHECKOUT ONLY
if ((isset($_GET['p']) && $_GET['p'] == 'checkpay') || ($cmd == 'checkpay')) {
  if (!defined('KILL_CHECKOUT_SHIPPING') && mc_rowCount('zones') == 0) {
    define('KILL_CHECKOUT_SHIPPING', 1);
  }
  // CLEAR INSURANCE MASK ON INITIAL LOAD
  if (isset($_SESSION['insurance-mask'])) {
    unset($_SESSION['insurance-mask']);
  }
}

// LOAD USER DEFINED VARS
include(PATH . 'control/defined.php');

// MARKETING TRACKER
mc_marketingTracker($SETTINGS);

// IS TICKER ENABLED?
if (mc_rowCount('news_ticker WHERE `enabled` = \'yes\'') > 0) {
  $loadJS['ticker'] = 'load';
}

// NEWS TICKER PREF
if (NEWS_TICKER_DISPLAY_PREF && $cmd != 'home') {
  unset($loadJS['ticker']);
}

// IS SLIDER ENABLED?
if (mc_rowCount('banners WHERE `bannerLive` = \'yes\'') > 0) {
  $loadJS['banners'] = 'load';
}

// IS RSS NEWS SCROLLER ENABLED?
if ($SETTINGS->rssScroller == 'yes' && $SETTINGS->rssScrollerUrl && $SETTINGS->rssScrollerLimit > 0) {
  $loadJS['rssNewsScroller'] = 'load';
}

// LOAD CONTROLLERS..
include(PATH . 'control/system-controllers.php');

// MAIAN AFFILIATE LOADER
if (isset($cfg['affiliate']['en'], $cfg['affiliate']['param']) && 
  $cfg['affiliate']['en'] == 'yes' && isset($_GET[$cfg['affiliate']['param']])
) {
  define('AFF_SYS_LOADER', 1);
  include(PATH . 'control/classes/class.affiliate.php');
  $AFF = new affiliate();
  $AFF->cfg = $cfg;
  $AFF->json = $MCJSON;
  include(PATH . 'control/affiliate.php');
}

// ARE LATEST TWEETS ENABLED?
if ($SETTINGS->twitterLatest == 'yes') {
  $twPar = $MCSOCIAL->params('twitter');
  if ($twPar['twitter']['username'] == '' || $twPar['twitter']['conkey'] == '' ||
      $twPar['twitter']['consecret'] == '' || $twPar['twitter']['token'] = '' || $twPar['twitter']['key'] == '') {
    die('You have enabled the latest tweets in the settings, but you have NOT set the authentication parameters. Please update:<br><br>
    System > General Settings > Settings Menu > Social Network Settings');
  }
  $loadJS['latestTweets'] = 'load';
}

// IS ANYONE LOGGED IN
$loggedInUser = $MCACC->access();

// LOAD USER PARAMS AND REPLACE DEFAULTS
if (isset($loggedInUser['id'])) {
  include(PATH . 'control/system/accounts/user-params.php');
} else {
  $mc_catPermissions = '1';
  $mc_cacheFlag      = 'guest';
  $mc_catSQL         = "LOCATE('1', `vis`) > 0";
}

// TRADE ONLY
if (isset($loggedInUser['type']) && $loggedInUser['type'] == 'trade') {
  include(PATH . 'control/system/accounts/trade.php');
  // TRADE KILL SHIPPING
  if (!defined('KILL_CHECKOUT_SHIPPING') && $SETTINGS->tradeship == 'yes') {
    define('KILL_CHECKOUT_SHIPPING', 1);
  }
}

// CATEGORY VIEW PREFERENCE
define('MC_CACHE_FLAG', $mc_cacheFlag);
define('MC_CATG_PMS', $mc_catPermissions);
define('MC_CATG_PMS_SQL', $mc_catSQL);
define('MC_PLATFORM_DETECTION', ($MCPDTC->isMobile() ? ($MCPDTC->isTablet() ? 'tablet' : 'mobile') : 'pc'));
define('MC_CATVIEW', mc_catViewPref($SETTINGS, (isset($loggedInUser['id']) ? $loggedInUser : array())));

// MENU PANEL
$slidePanel = $MCSYS->loadCategories();

// REWRITE CHECK..
if ($SETTINGS->en_modr == 'yes') {
  if (!file_exists(PATH . '.htaccess')) {
    die('You have enabled search engine friendly urls, but the .htaccess file has not been enabled:<br><br>
    Please rename "<b>htaccess_COPY.txt</b>" to "<b>.htaccess</b>" and <a href="index.php">reload</a> page.');
  }
}

?>