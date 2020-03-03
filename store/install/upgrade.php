<?php

@session_start();

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header('Content-type: text/html; charset=utf-8');

date_default_timezone_set('UTC');

define('PATH', dirname(__file__) . '/');
define('INSTALL_DIR', substr(dirname(__file__), 0, strpos(dirname(__file__), 'install') - 1) . '/');

if (!function_exists('mysqli_connect')) {
  die('!!! <b>The mysqli functions are not enabled on your server. Your must enable these functions before you can continue.</b><br><br>
  <a href="http://php.net/manual/en/book.mysqli.php">http://php.net/manual/en/book.mysqli.php</a>');
}

include(INSTALL_DIR . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

define('UPGRADE_ROUTINE', 'yes');

include(PATH . 'control/config.php');
include(INSTALL_DIR . 'control/system/constants.php');
include(INSTALL_DIR . 'control/defined.php');
include(INSTALL_DIR . 'control/connect.php');
include(INSTALL_DIR . 'control/functions.php');
include(PATH . 'control/functions.php');
include(INSTALL_DIR . 'control/classes/class.json.php');

mc_dbConnector();

$title     = SCRIPT_NAME . ': Upgrade Routine';
$tableType = 'ENGINE = MyISAM';
$ops       = array();
$MCJSON    = new jsonHandler();

// LOAD SETTINGS DATA..
// We can mask the error thrown here and redirect index file to installer..
$SETTINGS = @mysqli_fetch_object(@mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings` LIMIT 1"));

// CHECK INSTALLER..
if (!isset($SETTINGS->id)) {
  header("Location: index.php");
  exit;
}

if (phpVersion() < MSW_PHP_MIN_VER) {
  die('Your PHP version is too old. Min required: <b>PHP' . MSW_PHP_MIN_VER . '</b>, Your version: <b>PHP' . phpVersion() . '</b>');
}

// Table and collation..
$qCSVer = @mysqli_query($GLOBALS["___msw_sqli"], "SHOW VARIABLES");
$VARS   = @mysqli_fetch_object($qCSVer);
if (is_object($VARS)) {
  $VARS = (array) $VARS;
  if (isset($VARS['character_set_database'])) {
    $tableType = 'DEFAULT CHARACTER SET ' . $VARS['character_set_database'] . PHP_EOL;
    $tableType .= 'COLLATE ' . $VARS['collation_database'] . PHP_EOL;
  }
  if (isset($VARS['version'])) {
    if ($VARS['version'] < 5) {
      $tableType .= 'TYPE = MyISAM';
    } else {
      $tableType .= 'ENGINE = MyISAM';
    }
  } else {
    $tableType = 'ENGINE = MyISAM';
  }
}

// Legacy version..
if (!isset($SETTINGS->encoderVersion)) {
  die('Version appears to be older than 2.0. Upgrade not possible, sorry.');
}

// v2.0..
if (!isset($SETTINGS->softwareVersion)) {
  $SETTINGS->softwareVersion = '2.0';
}

if (isset($_GET['upgrade'])) {
  include(PATH . 'control/upgrade-routine.php');
}

if (isset($_GET['completed'])) {
  include(PATH . 'content/header.php');
  include(PATH . 'content/upgrade-completed.php');
  include(PATH . 'content/footer.php');
  exit;
}

include(PATH . 'content/header.php');
include(PATH . 'content/upgrade.php');
include(PATH . 'content/footer.php');

?>