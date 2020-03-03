<?php

/* ERROR HANDLER
-----------------------------------------------------*/

define('ERR_HANDLER_PATH', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control') - 1) . '/'); // DO NOT change!!
define('ERR_HANDLER_LOG_FOLDER', 'logs'); // Name of logs folder..
define('ERR_HANDLER_ENABLED', 1); // Enable custom error handler?
define('ERR_HANDLER_DISPLAY', 1); // Display a message on screen?
define('ERR_APPEND_RAND_STRING', 0); // Adds random string to file name for security. Prevents someone attempting browser access.
define('MASK_FILE_PATH', 0); // Hide file path if error occurs..
define('FILE_ERR_LOG_FILE', 'errors.log'); // File name of error log
define('FILE_FATAL_ERR_LOG_FILE', 'fatal_errors.log'); // File name of fatal error log

class mcErrs {

  public function generalErr($error) {
    mcErrs::log($error, FILE_ERR_LOG_FILE);
  }

  public function mailErr($error) {
    mcErrs::log($error, FILE_ERR_LOG_FILE);
  }

  public function fatalErr($error) {
    mcErrs::log($error, FILE_FATAL_ERR_LOG_FILE);
  }

  public function log($error, $file) {
    if (is_dir(ERR_HANDLER_PATH . ERR_HANDLER_LOG_FOLDER)) {
      file_put_contents(ERR_HANDLER_PATH . ERR_HANDLER_LOG_FOLDER . '/' . mcErrs::raStr() . $file, trim($error) . PHP_EOL . '- - - - - - - - - - - - - - - - - - -' . PHP_EOL, FILE_APPEND);
    }
  }

  public function raStr() {
    return (ERR_APPEND_RAND_STRING ? substr(md5(uniqid(rand(),1)), 3, 30) . '-' : '');
  }

}

// Initiate the class..
$MCEH = new mcErrs();

if (ERR_HANDLER_ENABLED) {
  // Switch off display errors..
  @ini_set('display_errors', 0);
  // Set error reporting level..
  error_reporting(E_ALL);
}

function mcFatalErr() {
  global $MCEH;
  $error = error_get_last();
  if (isset($error['type'])) {
    if ((defined('E_ERROR') && $error['type'] == E_ERROR) || $error['type'] == 4) {
      $string = '[Error Code: ' . $error['type'] . '] ' . $error['message'] . linending();
      $string .= '[Date/Time: ' . date('j F Y @ H:iA') . ']' . linending();
      $string .= '[Fatal error on line ' . $error['line'] . ' in file ' . $error['file'] . ']';
      if (ERR_HANDLER_DISPLAY) {
        echo '<div style="background:#ff9999"><p style="padding:10px;color:#fff">A fatal error has occurred. For more details please view "' . ERR_HANDLER_LOG_FOLDER . '/' . FILE_FATAL_ERR_LOG_FILE . '".</div>';
      }
      $MSEH->fatalErr($string);
    }
  }
}

function mcErrorhandler($errno, $errstr, $errfile, $errline) {
  global $MCEH;
  if (!(error_reporting() & $errno)) {
    return;
  }
  if (!method_exists($MCEH,'generalErr') || !method_exists($MCEH,'fatalErr')) {
    return;
  }
  switch($errno) {
    case E_USER_ERROR:
      $string = '[Error Code: ' . $errno . '] ' . $errstr . PHP_EOL;
      $string .= '[Date/Time: ' . date('j F Y @ H:iA') . ']' . PHP_EOL;
      $string .= '[Error on line ' . $errline . ' in file ' . $errfile . ']';
      if (ERR_HANDLER_DISPLAY) {
        echo '<div style="background:#ff9999"><p style="padding:10px;color:#fff">A fatal error has occurred. For more details please view "' . ERR_HANDLER_LOG_FOLDER . '/' . FILE_FATAL_ERR_LOG_FILE . '".</div>';
      }
      $MCEH->fatalErr($string);
      exit;
      break;

    case E_USER_WARNING:
      $string = '[Error Code: ' . $errno . '] ' . $errstr;
      $string .= '[Date/Time: ' . date('j F Y @ H:iA') . ']' . PHP_EOL;
      $string .= '[Error on line ' . $errline . ' in file ' . $errfile . ']';
      if (ERR_HANDLER_DISPLAY) {
        echo '<div style="background:#ff9999"><p style="padding:10px;color:#fff">An error has occurred. For more details please view "' . ERR_HANDLER_LOG_FOLDER . '/' . FILE_ERR_LOG_FILE . '".</div>';
      }
      $MCEH->generalErr($string);
      break;

    case E_USER_NOTICE:
      $string = '[Error Code: ' . $errno . '] ' . $errstr . PHP_EOL;
      $string .= '[Date/Time: ' . date('j F Y @ H:iA') . ']' . PHP_EOL;
      $string .= '[Error on line ' . $errline . ' in file ' . $errfile . ']';
      if (ERR_HANDLER_DISPLAY) {
        echo '<div style="background:#ff9999"><p style="padding:10px;color:#fff">An error has occurred. For more details please view "' . ERR_HANDLER_LOG_FOLDER . '/' . FILE_ERR_LOG_FILE . '".</div>';
      }
      $MCEH->generalErr($string);
      break;

    default:
      $string = '[Error Code: ' . $errno . '] ' . $errstr . PHP_EOL;
      $string .= '[Date/Time: ' . date('j F Y @ H:iA') . ']' . PHP_EOL;
      $string .= '[Error on line ' . $errline . ' in file ' . $errfile . ']';
      if (ERR_HANDLER_DISPLAY) {
        echo '<div style="background:#ff9999"><p style="padding:10px;color:#fff">An error has occurred. For more details please view "' . ERR_HANDLER_LOG_FOLDER . '/' . FILE_ERR_LOG_FILE . '".</div>';
      }
      $MCEH->generalErr($string);
      break;
  }
  return true;
}

?>