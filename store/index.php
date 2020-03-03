<?php

//------------------------------------------------------------------------------
// SCRIPT LOADER
// DO NOT change this file
//------------------------------------------------------------------------------

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header('Content-type: text/html; charset=utf-8');

@ini_set('session.cookie_httponly', 1);
@session_start();

// PREVENT DATE ERRORS
date_default_timezone_set('UTC');

define('PARENT', 1);
define('PATH', dirname(__file__) . '/');

include(PATH . 'control/connect.php');
include(PATH . 'control/functions.php');
include(PATH . 'control/system/init.php');

include(PATH . 'control/classes/class.errors.php');
if (ERR_HANDLER_ENABLED) {
  register_shutdown_function('mcFatalErr');
  set_error_handler('mcErrorhandler');
}

include(PATH . 'control/system-load.php');

?>