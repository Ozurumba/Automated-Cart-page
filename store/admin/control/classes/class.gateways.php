<?php

class gateways {

  public $cache;

  public function updatePaymentMethods() {
    $viewtype = (empty($_POST['viewtype']) ? 'a' : implode(',',$_POST['viewtype']));
    $defstt   = 'a:3:{s:9:"completed";s:8:"shipping";s:8:"download";s:9:"completed";s:7:"virtual";s:9:"completed";}';
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "methods` SET
    `status`        = '" . (isset($_POST['status']) && in_array($_POST['status'], array(
      'yes',
      'no'
    )) ? $_POST['status'] : 'yes') . "',
    `defmeth`       = '" . (isset($_POST['defmeth']) && in_array($_POST['defmeth'], array(
      'yes',
      'no'
    )) ? $_POST['defmeth'] : 'no') . "',
    `liveserver`    = '" . (isset($_POST['liveserver']) ? mc_safeSQL($_POST['liveserver']) : '') . "',
    `sandboxserver` = '" . (isset($_POST['sandboxserver']) ? mc_safeSQL($_POST['sandboxserver']) : '') . "',
    `plaintext`     = '" . (isset($_POST['plaintext']) ? mc_safeSQL($_POST['plaintext']) : '') . "',
    `htmltext`      = '" . (isset($_POST['htmltext']) ? mc_safeSQL($_POST['htmltext']) : '') . "',
    `info`          = '" . (isset($_POST['info']) ? mc_safeSQL(mc_cleanBBInput($_POST['info'])) : '') . "',
    `redirect`      = '" . (isset($_POST['redirect']) ? mc_safeSQL($_POST['redirect']) : '') . "',
    `statuses`      = '" . (!empty($_POST['orderStatus']) ? serialize($_POST['orderStatus']) : $defstt) . "',
    `viewtype`      = '" . mc_safeSQL($viewtype) . "'
    WHERE `method`  = '" . mc_safeSQL($_POST['area']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Was default set?
    if (isset($_POST['defmeth']) && $_POST['defmeth'] == 'yes') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "methods` SET
      `defmeth`       = 'no'
      WHERE `method` != '" . mc_safeSQL($_POST['area']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Parameters. If it doesn`t exist, insert it..
    if (!empty($_POST['params'])) {
      foreach ($_POST['params'] AS $k => $v) {
        if (mc_rowCount('methods_params WHERE `method` = \'' . mc_safeSQL($_POST['area']) . '\' AND `param` = \'' . $k . '\'') > 0) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "methods_params` SET
          `value`         = '" . mc_safeSQL($v) . "'
          WHERE `method`  = '" . mc_safeSQL($_POST['area']) . "'
          AND `param`     = '{$k}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "methods_params` (
          `method`,`value`,`param`
          ) VALUES (
          '" . mc_safeSQL($_POST['area']) . "','" . mc_safeSQL($v) . "','{$k}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    $this->cache->clear_cache_file('payment-methods');
  }

  public function enableDisablePaymentMethods() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "methods` SET
    `status` = '" . (isset($_GET['enable']) ? 'yes' : 'no') . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $this->cache->clear_cache_file('payment-methods');
  }

}

?>