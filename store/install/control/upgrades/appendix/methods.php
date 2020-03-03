<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//=============================
// PAYMENT METHODS/GATEWAYS
//=============================

$serialized = 'a:5:{s:9:"completed";s:8:"shipping";s:8:"download";s:9:"completed";s:7:"virtual";s:9:"completed";s:7:"pending";s:7:"pending";s:8:"refunded";s:6:"refund";}';

@mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . "methods`");
@mysqli_query($GLOBALS["___msw_sqli"], str_replace('{prefix}',DB_PREFIX,@file_get_contents(PATH . 'control/sql/methods.sql')));

mc_upgradeLog('Added new payment gateway information');

?>