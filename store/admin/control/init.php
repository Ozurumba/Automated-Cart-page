<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// CONNECTION..
include(PATH . 'control/defined.php');
include(PATH . 'control/defined2.php');
include(REL_PATH . 'control/connect.php');
include(REL_PATH . 'control/functions.php');
include(PATH . 'control/functions.php');
include(REL_PATH . 'control/system/constants.php');

// DATABASE CONNECTOR..
mc_dbConnector();

// LOAD SETTINGS..
$SETTINGS = @mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"));

if (!isset($SETTINGS->languagePref)) {
  header("Location: ../install/index.php");
  exit;
}

// LOGGED IN USER..
$sysCartUser = mc_getUser($SETTINGS);

// Cache Controller..
include(REL_PATH . 'control/classes/class.cache.php');
$MCCACHE = new mcCache($SETTINGS);

// LOAD USER DEFINED VARS..
include(REL_PATH . 'control/defined.php');

// DEFINE LANGUAGE PATHS..
define('THEME_FOLDER', 'content/' . ($SETTINGS->theme && is_dir(REL_PATH . 'content/' . $SETTINGS->theme) ? $SETTINGS->theme : '_theme_default'));
define('TRADE_THEME_FOLDER', 'content/' . ($SETTINGS->theme2 && is_dir(REL_PATH . 'content/' . $SETTINGS->theme2) ? $SETTINGS->theme2 : '_theme_default'));
define('MCLANG', REL_PATH . 'content/language/' . $SETTINGS->languagePref . '/admin/');
define('MCLANG_REL', REL_PATH . 'content/language/' . $SETTINGS->languagePref . '/');
define('LANG_PATH', GLOBAL_PATH . 'content/language/' . $SETTINGS->languagePref . '/email-templates/');
define('LANG_BASE_PATH', GLOBAL_PATH . 'content/language/');
define('ADMIN_LOADER', 1);

// Load basket payment methods..
include(REL_PATH . 'control/gateways/system-payment-methods.php');

// LOAD LANGUAGE FILES..
include(MCLANG_REL . 'global.php');
include(MCLANG_REL . 'version3.0.php');
include(MCLANG_REL . 'version3.4.php');
include(MCLANG_REL . 'version3.7.php');
include(MCLANG . 'system/settings.php');
include(MCLANG . 'tools/stats.php');
include(MCLANG . 'system/header.php');
include(MCLANG . 'catalogue/product-add.php');
include(MCLANG . 'system/main.php');
include(MCLANG . 'versions/2.1.php');
include(MCLANG . 'versions/3.0.php');
include(MCLANG_REL . 'version2.1.php');

// INCLUDE FILES..
mc_fileController();
include(PATH . 'control/arrays.php');
include(REL_PATH . 'control/classes/class.json.php');
include(REL_PATH . 'control/system/core/sys-controller.php');
include(PATH . 'control/classes/class.system.php');
include(REL_PATH . 'control/classes/class.mobile-detection.php');
include(PATH . 'control/classes/class.users.php');
include(PATH . 'control/classes/class.gateways.php');
include(PATH . 'control/classes/class.categories.php');
include(PATH . 'control/classes/class.accounts.php');
include(PATH . 'control/classes/class.products.php');
include(PATH . 'control/classes/class.shipping.php');
include(REL_PATH . 'control/classes/class.page.php');
include(PATH . 'control/classes/class.sales.php');
include(REL_PATH . 'control/classes/class.bbCode.php');
include(REL_PATH . 'control/classes/class.parser.php');
include(REL_PATH . 'control/classes/mailer/class.send.php');
include(REL_PATH . 'control/classes/class.currencies.php');
include(PATH . 'control/classes/class.isbn.php');
include(REL_PATH . 'control/currencies.php');

// DETECT TIMEZONE (PHP5+)..
mc_dateTimeDetect($SETTINGS);

// CHECK VERSION..
mc_scriptCheckVersion($SETTINGS);

// DEFAULT VARS..
$cmd             = isset($_GET['p']) ? $_GET['p'] : 'main';
$page            = (isset($_GET['next']) && $_GET['next'] > 0 ? (int) $_GET['next'] : '1');
$limit           = $page * PRODUCTS_PER_PAGE - (PRODUCTS_PER_PAGE);
$pageTitle       = mc_safeHTML($SETTINGS->website);
$count           = 0;
$tabIndex        = 0;
$textareaFullScr = true;
$noneGateway     = array('bank','cod','phone','cheque','account');

// CREATE CLASS OBJECTS..
$MCSYS             = new systemEngine();
$MCUSR             = new users();
$MCPDTC            = new Mobile_Detect();
$MCBB              = new bbCode_Parser();
$MCPARSER          = new mcDataParser();
$MCMAIL            = new mcMail();
$MCGWY             = new gateways();
$MCCAT             = new cats();
$MCACC             = new accounts();
$MCPROD            = new products();
$MCSHIP            = new shipping();
$MCSALE            = new sales();
$MCCRV             = new curConverter();
$ISBN              = new isbn();
$JSON              = new jsonHandler();
$MCPARSER->bbCode  = $MCBB;
$MCUSR->settings   = $SETTINGS;
$MCMAIL->parser    = $MCPARSER;
$MCCRV->settings   = $SETTINGS;
$MCSYS->settings   = $SETTINGS;
$MCSYS->cache      = $MCCACHE;
$MCCAT->settings   = $SETTINGS;
$MCCAT->cache      = $MCCACHE;
$MCPROD->settings  = $SETTINGS;
$MCPROD->cache     = $MCCACHE;
$MCSHIP->settings  = $SETTINGS;
$MCSALE->settings  = $SETTINGS;
$ISBN->settings    = $SETTINGS;
$MCACC->settings   = $SETTINGS;
$MCACC->account    = $MCACC;
$MCGWY->cache      = $MCCACHE;
$MCBB->settings    = $SETTINGS;

define('MC_PLATFORM_DETECTION', ($MCPDTC->isMobile() ? ($MCPDTC->isTablet() ? 'tablet' : 'mobile') : 'pc'));

?>