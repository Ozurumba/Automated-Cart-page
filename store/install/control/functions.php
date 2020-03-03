<?php

function mswCheckTable($table) {
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SHOW TABLES WHERE `Tables_in_" . DB_NAME . "` = '" . DB_PREFIX . $table . "'");
  $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
  $f = (isset($c->rows) ? $c->rows : '0');
  return ($f > 0 ? 'yes' : 'no');
}

function mswCheckColumn($table, $col) {
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `c` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . DB_NAME . "'
       AND `TABLE_NAME`  = '" . DB_PREFIX . $table . "'
       AND `COLUMN_NAME` = '{$col}'
       ");
  $R = mysqli_fetch_object($q);
  $f = (isset($R->c) ? $R->c : '0');
  return ($f > 0 ? 'yes' : 'no');
}

function mswCheckColumnType($table, $field, $string) {
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SHOW FIELDS FROM `" . DB_PREFIX . $table . "` WHERE `Field` = '{$field}'");
  $R = mysqli_fetch_object($q);
  $f = (isset($R->Type) ? strtolower($R->Type) : '');
  return (strpos($f, strtolower($string)) !== false ? 'yes' : 'no');
}

function mswCheckIndex($table, $index) {
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SHOW INDEX FROM " . DB_PREFIX . $table . " WHERE `Key_name` = '{$index}'");
  $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
  $f = (isset($c->rows) ? $c->rows : '0');
  return ($f > 0 ? 'yes' : 'no');
}

function mc_upgradeLog($text) {
  if (defined('MC_UPGRADE_LOG') && MC_UPGRADE_LOG && is_writeable(INSTALL_DIR . 'logs') && function_exists('file_put_contents')) {
    $header = '';
    if (!file_exists(INSTALL_DIR . 'logs/' . MC_UPGRADE_LOG_FILE)) {
      $query   = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT VERSION() AS `v`");
      $VERSION = @mysqli_fetch_object($query);
      $header  = 'Script: ' . SCRIPT_NAME . mc_defineNewline();
      $header .= 'Script Version: ' . SCRIPT_VERSION . mc_defineNewline();
      $header .= 'PHP Version: ' . phpVersion() . mc_defineNewline();
      $header .= 'MySQL Version: ' . (isset($VERSION->v) ? $VERSION->v : 'Unknown') . mc_defineNewline();
      if (isset($_SERVER['SERVER_SOFTWARE'])) {
        $header .= 'Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . mc_defineNewline();
      }
      if (isset($_SERVER["HTTP_USER_AGENT"])) {
        if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'win')) {
          $platform = 'Windows';
        } else if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac')) {
          $platform = 'Mac';
        } else {
          $platform = 'Other';
        }
        $header .= 'Platform: ' . $platform . mc_defineNewline();
      }
      $header .= '=================================================================================' . mc_defineNewline();
    }
    $string = date('d/m/Y') . ' @ ' . date('H:i:s') . ': ' . $text . mc_defineNewline();
    if (mysqli_error($GLOBALS["___msw_sqli"])) {
      $string .= date('d/m/Y') . ' @ ' . date('H:i:s') . ': [SQL ERROR] > ' . mysqli_error($GLOBALS["___msw_sqli"]) . mc_defineNewline();
    }
    $string .= '>>>> = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = >>>>' . mc_defineNewline();
    @file_put_contents(INSTALL_DIR . 'logs/' . MC_UPGRADE_LOG_FILE, $header . $string, FILE_APPEND);
  }
}

function mc_logDBError($table, $error, $code, $line, $file, $type = 'Create') {
  if (defined('MC_INSTALL_LOG') && MC_INSTALL_LOG && is_writeable(INSTALL_DIR . 'logs') && function_exists('file_put_contents')) {
    $header = '';
    if (!file_exists(INSTALL_DIR . 'logs/' . MC_INSTALL_LOG_FILE)) {
      $query   = @mysqli_query($GLOBALS["___msw_sqli"], "SELECT VERSION() AS `v`");
      $VERSION = @mysqli_fetch_object($query);
      $header  = 'Script: ' . SCRIPT_NAME . mc_defineNewline();
      $header .= 'Script Version: ' . SCRIPT_VERSION . mc_defineNewline();
      $header .= 'PHP Version: ' . phpVersion() . mc_defineNewline();
      $header .= 'MySQL Version: ' . (isset($VERSION->v) ? $VERSION->v : 'Unknown') . mc_defineNewline();
      if (isset($_SERVER['SERVER_SOFTWARE'])) {
        $header .= 'Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . mc_defineNewline();
      }
      if (isset($_SERVER["HTTP_USER_AGENT"])) {
        if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'win')) {
          $platform = 'Windows';
        } else if (strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac')) {
          $platform = 'Mac';
        } else {
          $platform = 'Other';
        }
        $header .= 'Platform: ' . $platform . mc_defineNewline();
      }
      $header .= '=================================================================================' . mc_defineNewline();
    }
    $string  = 'Date/Time: ' . date('d/m/Y') . ' @ ' . date('H:i:s') . mc_defineNewline();
    $string .= 'Table: ' . $table . mc_defineNewline();
    $string .= 'Operation: ' . $type . mc_defineNewline();
    $string .= 'Error Code: ' . $code . mc_defineNewline();
    $string .= 'Error Msg: ' . $error . mc_defineNewline();
    $string .= 'On Line: ' . $line . mc_defineNewline();
    $string .= 'In File: ' . $file . mc_defineNewline();
    $string .= '>>>> = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = >>>>' . mc_defineNewline();
    if (is_writeable(INSTALL_DIR . 'logs') && function_exists('file_put_contents')) {
      @file_put_contents(INSTALL_DIR . 'logs/' . MC_INSTALL_LOG_FILE, $header . $string, FILE_APPEND);
    }
  }
}

// Generates 60 character product key..
$_SERVER['HTTP_HOST']   = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : uniqid(rand(), 1));
$_SERVER['REMOTE_ADDR'] = (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : uniqid(rand(), 1));

if (function_exists('sha1')) {
  $c1      = sha1($_SERVER['HTTP_HOST'] . date('YmdHis') . $_SERVER['REMOTE_ADDR'] . time());
  $c2      = sha1(uniqid(rand(), 1) . time());
  $prodKey = substr($c1 . $c2, 0, 60);
} elseif (function_exists('md5')) {
  $c1      = md5($_SERVER['HTTP_POST'] . date('YmdHis') . $_SERVER['REMOTE_ADDR'] . time());
  $c2      = md5(uniqid(rand(), 1), time());
  $prodKey = substr($c1 . $c2, 0, 60);
} else {
  $c1      = str_replace('.', '', uniqid(rand(), 1));
  $c2      = str_replace('.', '', uniqid(rand(), 1));
  $c3      = str_replace('.', '', uniqid(rand(), 1));
  $prodKey = substr($c1 . $c2 . $c3, 0, 60);
}

$prodKey = strtoupper($prodKey);

?>