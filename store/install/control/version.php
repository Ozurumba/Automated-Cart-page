<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

// Version update and path update..
if ($SETTINGS->serverPath == '' || $SETTINGS->globalDownloadPath == '' || $SETTINGS->ifolder == '') {
  $storePath = 'http://www.example.com/cart';
  if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
    $storePath = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'install') - 1);
  }
  $serverPath = substr(PATH, 0, strpos(PATH, 'install') - 1);
  @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
  `serverPath`          = '" . mc_safeSQL($serverPath) . "',
  `ifolder`             = '" . mc_safeSQL($storePath) . "',
  `globalDownloadPath`  = '" . mc_safeSQL($serverPath) . "',
  `encoderVersion`      = '1.0',
  `softwareVersion`     = '" . SCRIPT_VERSION . "'
  ");
} else {
  @mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
  `encoderVersion`   = '1.0',
  `softwareVersion`  = '" . SCRIPT_VERSION . "'
  ");
}

?>