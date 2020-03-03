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

include(PATH . 'control/config.php');
include(INSTALL_DIR . 'control/system/constants.php');
include(INSTALL_DIR . 'control/defined.php');
include(INSTALL_DIR . 'control/connect.php');
include(INSTALL_DIR . 'control/functions.php');

mc_dbConnector();

include(PATH . 'control/functions.php');

$cmd        = (isset($_GET['s']) ? $_GET['s'] : '1');
$title      = '';
$stages     = 6;
$perc_width = ($cmd > 1 ? ceil(($cmd - 1) * (100 / $stages)) : '0');
$progress   = ($cmd > 1 ? ceil(($cmd - 1) * (100 / $stages)) : '0');

if (isset($_GET['connectionTest'])) {
  $cmd = 'test';
}

// Check if PHP version is too old..
if (phpVersion() < MSW_PHP_MIN_VER || !function_exists('file_get_contents')) {
  $cmd  = 'e';
  $code = 'old';
  $type = 'FATAL ERROR';
}

/*$QST = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT @@sql_mode AS `mode`");
$ST  = @mysqli_fetch_object($QST);
if (isset($ST->mode) && strpos(strtolower($ST->mode), 'strict_trans_tables') !== false) {
  $cmd  = 'e';
  $code = 'strict';
  $type = 'FATAL ERROR';
}*/

switch($cmd) {
  case '1':
    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/1.php');
    include(PATH . 'content/footer.php');
    break;

  case '2':
    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/2.php');
    include(PATH . 'content/footer.php');
    break;

  case '3':
    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/3.php');
    include(PATH . 'content/footer.php');
    break;

  case '4':

    if (isset($_POST['tables'])) {
      include(PATH . 'control/tables.php');
      header("Location: index.php?s=" . (empty($tableD) ? '5' : 'e&msg=tables'));
      exit;
    }

    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'control/controller.php');
    include(PATH . 'content/header.php');
    include(PATH . 'content/4.php');
    include(PATH . 'content/footer.php');
    break;

  case '5':

    if (isset($_POST['storeInfo'])) {
      include(PATH . 'control/storedata.php');
      header("Location: index.php?s=" . (empty($storedata) ? '6' : 'e&msg=sdata'));
      exit;
    }

    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/5.php');
    include(PATH . 'content/footer.php');
    break;

  case '6':

    if (isset($_POST['finish'])) {
      include(PATH . 'control/data.php');
      if (empty($data)) {
        if (isset($_POST['demo'])) {
          include(PATH . 'control/ds.php');
        } else {
          include(PATH . 'control/ds-clear.php');
        }
      }
      header("Location: index.php?s=" . (empty($data) ? '7' : 'e&msg=data'));
      exit;
    }

    $title = 'Step ' . $cmd . ': ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/6.php');
    include(PATH . 'content/footer.php');
    break;

  case '7':
    $title = 'Completed: ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/7.php');
    include(PATH . 'content/footer.php');
    break;

  case 'e':

    if (isset($_GET['msg'])) {
      switch($_GET['msg']) {
        case 'tables':
          $cmd  = 'e';
          $code = 'tables';
          $type = 'DB ERROR';
          break;
        case 'sdata':
          $cmd  = 'e';
          $code = 'sdata';
          $type = 'DB ERROR';
          break;
        case 'data':
          $cmd  = 'e';
          $code = 'tables';
          $type = 'DB ERROR';
          break;
      }
    }

    $title = 'Error: ';
    include(PATH . 'content/header.php');
    include(PATH . 'content/error.php');
    include(PATH . 'content/footer.php');
    break;
}

?>