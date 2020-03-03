<?php

// Database connection..
function mc_dbConnector() {
  if (function_exists('mysqli_connect')) {
    $connect = @($GLOBALS["___msw_sqli"] = mysqli_connect(trim(DB_HOST), trim(DB_USER), trim(DB_PASS)));
    if (!$connect) {
      die(mc_MySQLError(__LINE__, __FILE__));
    }
    if ($connect && !((bool) mysqli_query($connect, 'USE `' . DB_NAME . '`'))) {
      die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      if (!mysqli_ping($GLOBALS["___msw_sqli"])) {
        die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    if ($connect) {
      // Character set..
      if (DB_CHAR_SET) {
        if (strtolower(DB_CHAR_SET) == 'utf-8') {
          $change = str_replace('-', '', DB_CHAR_SET);
        }
        @mysqli_query($GLOBALS["___msw_sqli"], "SET CHARACTER SET '" . (isset($change) ? $change : DB_CHAR_SET) . "'");
        @mysqli_query($GLOBALS["___msw_sqli"], "SET NAMES '" . (isset($change) ? $change : DB_CHAR_SET) . "'");
        if (defined('DB_TIMEZONE') && DB_TIMEZONE != '') {
          @mysqli_query($GLOBALS["___msw_sqli"], "SET `time_zone` = '" . DB_TIMEZONE . "'");
        }
      }
      // Locale..
      if (defined('DB_LOCALE')) {
        if (DB_CHAR_SET && DB_LOCALE) {
          @mysqli_query($GLOBALS["___msw_sqli"], "SET `lc_time_names` = '" . DB_LOCALE . "'");
        }
      }
      @mysqli_query($GLOBALS["___msw_sqli"], "SET `sql_mode` = 'IGNORE_SPACE,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
    }
  } else {
    die('[FATAL ERROR] mysqli functions not enabled on server, load aborted');
  }
}

function mc_getCFG($flag = '') {
  $arr = array();
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "cfg` WHERE `op1` = '{$flag}'");
  while ($cfg = mysqli_fetch_object($q)) {
    $arr[$cfg->op1][$cfg->op2] = mc_cleanData($cfg->op6);
  }
  return $arr;
}

function mc_cleanCustomTags($data, $tags) {
  $keys = array_fill_keys(array_keys($tags), '');
  return strtr($data, $keys);
}

function mc_categoryPermissions($q = 'AND', $pfx = '') {
  if (defined('MC_CATG_PMS')) {
    $p = explode(',', MC_CATG_PMS);
    // If public exists, available everywhere by default..
    if (in_array('1', $p)) {
      return $q . ' FIND_IN_SET(\'1\',' . $pfx . '`vis`) > 0';
    } else {
      // Available for both types of account?
      if (in_array('2', $p) && in_array('3', $p)) {
        return $q . ' (FIND_IN_SET(\'2\',' . $pfx . '`vis`) > 0 OR FIND_IN_SET(\'3\',' . $pfx . '`vis`) > 0)';
      } else {
        // Just for standard..
        if (in_array('2', $p)) {
          return $q . ' FIND_IN_SET(\'2\',' . $pfx . '`vis`) > 0';
        }
        // Just for trade..
        if (in_array('3', $p)) {
          return $q . ' FIND_IN_SET(\'3\',' . $pfx . '`vis`) > 0';
        }
      }
    }
  }
  return '';
}

function mc_catViewPref($s, $usr = array()) {
  // For mobiles, always show list..
  // This in itself adapts to mobiles via responsiveness
  // Grid view doesn`t adapt correctly..
  if (MC_PLATFORM_DETECTION == 'mobile') {
    return 'list';
  }
  // User pref..
  if (isset($usr['params'])) {
    $pms = ($usr['params'] ? unserialize($usr['params']) : array());
    if (isset($pms['layout']) && in_array($pms['layout'], array('grid','list'))) {
      return $pms['layout'];
    }
  }
  // Session reload from icon click..
  if (isset($_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)])) {
    // If same as default, clear session var..
    if (isset($s->layout) && $_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)] == $s->layout) {
      unset($_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)]);
      return $s->layout;
    }
    if (in_array($_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)], array('grid','list'))) {
      return $_SESSION['mc_layout_' . mc_encrypt(SECRET_KEY)];
    }
  }
  // Default..
  return (isset($s->layout) && in_array($s->layout, array('grid','list')) ? $s->layout : 'list');
}

function mc_marketingTracker($s) {
  if (defined('TRACKING_CODE_PREFIX') && TRACKING_CODE_PREFIX && isset($_GET[TRACKING_CODE_PREFIX])) {
    if (!isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_mark_tracker'])) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "tracker`
           WHERE `code` = '" . mc_safeSQL($_GET[TRACKING_CODE_PREFIX]) . "'
           LIMIT 1
           ");
      $TC = mysqli_fetch_object($q);
      if (isset($TC->id)) {
        $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_mark_tracker'] = $_GET[TRACKING_CODE_PREFIX];
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "tracker_clicks` (
        `code`,
        `clicked`,
        `ip`
        ) VALUES (
        '" . mc_safeSQL($_GET[TRACKING_CODE_PREFIX]) . "',
        '" . date('Y-m-d H:i:s') . "',
        '" . mc_safeSQL(mc_getRealIPAddr()) . "'
        )");
      }
    }
    header("Location: " . $s->ifolder);
    exit;
  }
}

function mc_memoryLimit($memory = '100', $timeout = '0') {
  @ini_set('memory_limit', $memory . 'M');
  @set_time_limit($timeout);
}

function mc_loadTemplateFile($file) {
  return (file_exists($file) ? file_get_contents($file) : die('<span style="color:red">[TEMPLATE LOAD ERROR]</span><br><br>The following template is missing:<br><br><b>' . $file . '</b>'));
}

function mc_scriptCheckVersion($s) {
  if ($s->softwareVersion == '') {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
    `softwareVersion` = '" . SCRIPT_VERSION . "'
    ");
  }
}

// Check product permissions..
function mc_visProdPerms($vis) {
  $chop  = explode(',', $vis);
  $allow = explode(',', MC_CATG_PMS);
  foreach ($chop AS $pm) {
    if (in_array($pm, $allow)) {
      return 'ok';
    }
  }
  return 'block';
}

// Check category permissions..
function mc_visCatPerms($vis) {
  $chop  = explode(',', $vis);
  $allow = explode(',', MC_CATG_PMS);
  foreach ($chop AS $pm) {
    if (in_array($pm, $allow)) {
      return 'ok';
    }
  }
  return 'block';
}

// Get shipping zone and area..
function mc_getShippingZoneArea($id) {
  $S = mc_getTableData('zone_areas', 'id', (int) $id);
  $z = '';
  if (isset($S->id)) {
    $ZN = mc_getTableData('zones', 'id', $S->inZone);
    if (isset($ZN->zName)) {
      $z = mc_cleanData($ZN->zName);
    }
  }
  return (isset($S->areaName) ? ($z ? $z . ' / ' : '') . mc_cleanData($S->areaName) : 'N/A');
}

// Get shipping service..
function mc_getShippingService($id) {
  $S = mc_getTableData('services', 'id', (int) $id);
  return (isset($S->sName) ? mc_cleanData($S->sName) : 'N/A');
}

// Get shipping country..
function mc_getShippingCountry($id, $all = false) {
  $C = mc_getTableData('countries', 'id', (int) $id);
  return (isset($C->cName) ? ($all ? $C : mc_cleanData($C->cName)) : 'N/A');
}

// Get service id from rate..
function mc_getShippingServiceFromRate($id) {
  $R = mc_getTableData('rates', 'id', (int) $id);
  return (isset($R->rService) ? $R->rService : 'N/A');
}

// DB Schema..
function mswDBSchemaArray() {
  $tbl = array();
  if (strlen(DB_PREFIX) > 0) {
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SHOW TABLES WHERE SUBSTRING(`Tables_in_" . DB_NAME . "`,1," . strlen(DB_PREFIX) . ") = '" . DB_PREFIX . "'");
  } else {
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SHOW TABLES");
  }
  while ($TABLES = mysqli_fetch_object($q)) {
    $field = 'Tables_in_' . DB_NAME;
    $tbl[] = $TABLES->{$field};
  }
  return $tbl;
}

// Fixes settings fields if manual schema was run..
function mc_manualSchemaFix($s) {
  if ($s->ifolder == '' || $s->serverPath == '' || $s->timezone == '') {
    $storePath = 'http://www.example.com/cart';
    if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
      $storePath   = 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'index.php')-1);
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
    `serverPath`          = '" . mc_safeSQL(substr(PATH, 0, -1), true) . "',
    `ifolder`             = '{$storePath}',
    `cWebsite`            = '{$storePath}',
    `prodKey`             = '" . mc_generateProductKey() . "',
	  `timezone`            = '" . (@date_default_timezone_get() ? date_default_timezone_get() : 'Europe/London') . "',
    `encoderVersion`      = '1.0',
    `softwareVersion`     = '" . SCRIPT_VERSION . "',
    `globalDownloadPath`  = '" . mc_safeSQL(substr(PATH, 0, -1), true) . "',
    `downloadFolder`      = 'product-downloads'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Page reload..
    header("Location: index.php");
    exit;
  }
}

// Load language file..
function mc_loadLangFile($s) {
  $base = PATH . 'content/language/';
  return $base . (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language']) && is_dir($base . $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language']) ? $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language'] : $s->languagePref) . '/';
}

// Detect date timezone..
function mc_dateTimeDetect($SETTINGS) {
  date_default_timezone_set(($SETTINGS->timezone ? $SETTINGS->timezone : @date_default_timezone_get()));
}

function mc_queryString($flag = array()) {
  $qstring = array();
  if (!empty($_GET)) {
    foreach ($_GET AS $k => $v) {
      if (is_array($v)) {
        foreach ($v AS $v2) {
          $qstring[] = $k . '[]=' . urlencode($v2);
        }
      } else {
        $merge = array_merge($flag, array(
          'p',
          'next'
        ));
        if (!in_array($k, $merge)) {
          $qstring[] = $k . '=' . urlencode($v);
        }
      }
    }
  }
  return (!empty($qstring) ? '&amp;' . implode('&amp;', $qstring) : '');
}

// Generates 60 character product key..
function mc_generateProductKey() {
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
  return strtoupper($prodKey);
}

// Catch MySQL errors..
function mc_MySQLError($file, $line) {
  global $msg_script61;
  if (!ENABLE_MYSQL_ERRORS) {
    echo MYSQL_DEFAULT_ERROR;
    exit;
  }
  echo '<p style="color:#555;border:2px solid #ff9999;padding:10px;font-size:12px;line-height:20px;background:#fff">';
  echo '<span style="float:right;font-size:40px;color:red;display:block;padding:10px 10px 0 0">X</span>';
  echo '<b style="color:black">[ <span style="text-decoration:underline">MYSQL DATABASE ERROR</span> ]</b><br><br>';
  if (isset($GLOBALS["___msw_sqli"]) && mysqli_errno($GLOBALS["___msw_sqli"])) {
    echo '<b>' . (isset($msg_script61[0]) ? $msg_script61[0] : 'Code') . '</b>: ' . mysqli_errno($GLOBALS["___msw_sqli"]) . '<br>';
  }
  if (isset($GLOBALS["___msw_sqli"]) && mysqli_error($GLOBALS["___msw_sqli"])) {
    echo '<b>' . (isset($msg_script61[1]) ? $msg_script61[1] : 'Error') . '</b>: ' . mysqli_error($GLOBALS["___msw_sqli"]) . '<br>';
  }
  echo '<b>' . (isset($msg_script61[2]) ? $msg_script61[2] : 'File') . '</b>: ' . $line . '<br>';
  echo '<b>' . (isset($msg_script61[3]) ? $msg_script61[3] : 'Line') . '</b>: ' . $file . '<br><br>';
  echo '<b style="color:black">[ <span style="text-decoration:underline">HELP</span> ]</b><br><br>';
  echo 'For "Unknown column" errors or missing tables, check you are running the latest version and the latest version has all the required tables. The following schema can be used for reference:<br><br>';
  echo '<b>docs/schematic/maian_cart_schema.sql</b><br><br>';
  echo 'If you have upgraded from a previous version, try running the upgrade routine again. Note that your database user MUST have FULL privileges, including DROP privileges for upgrade routines.<br><br>';
  echo 'If this persists and you don`t understand whats gone wrong, <a href="https://www.maianscriptworld.co.uk/contact/">contact</a> me asap.';
  echo '</p>';
  exit;
}

// Reset global expiry..
function mc_resetGlobalExpiryDiscount() {
  if (!defined('MC_TRADE_DISCOUNT')) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "settings` SET
    `globalDiscount`        = '0',
    `globalDiscountExpiry`  = '0000-00-00'
    ");
  }
}

// Table truncation routine..
function mc_tableTruncationRoutine($tables = array()) {
  if (!empty($tables)) {
    foreach ($tables AS $t) {
      if (mc_rowCount($t) == 0) {
        mysqli_query($GLOBALS["___msw_sqli"], "TRUNCATE TABLE `" . DB_PREFIX . $t . "`") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }
}

// Clear auto delete blog entries..
function mc_clearBlogAutoDelete() {
  mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "blog`
  WHERE `autodelete` > 0
  AND DATE(FROM_UNIXTIME(`autodelete`)) <= '" . date('Y-m-d') . "'
  ");
}

// Clear product offers..
function mc_clearProductOffers() {
  mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "products` SET
  `pOffer`              = '',
  `pMultiBuy`           = '0',
  `pOfferExpiry`        = '0000-00-00'
  WHERE `pOfferExpiry` <= '" . date("Y-m-d") . "'
  AND `pOfferExpiry`   != '0000-00-00'
  ");
}

// Log searches..
function mc_logSearchResults($results, $count, $s) {
  if ($results) {
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "search_log` (
    `keyword`,`results`,`searchDate`,`ip`
    ) VALUES (
    '" . mc_safeSQL($results) . "','{$count}','" . date("Y-m-d") . "','" . mc_safeSQL(mc_getRealIPAddr()) . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }
}

// Clear session vars..
function mc_clearSessVars($arr = array()) {
  if (!empty($arr)) {
    foreach ($arr AS $s) {
      if (isset($_SESSION[$s])) {
        $_SESSION[$s] = '';
        unset($_SESSION[$s]);
      }
    }
  }
}

// Sale gift certs..
function mc_saleGiftCerts($sale, $purchase) {
  global $msg_invoice37;
  $att = '';
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes`
           WHERE `saleID`    = '{$sale}'
           AND `purchaseID`  = '{$purchase}'
           ORDER BY `id`
		       LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  if (mysqli_num_rows($query) > 0) {
    $att = '<span class="saleAttributes">' . mc_defineNewline();
    $GC  = mysqli_fetch_object($query);
    $att .= '<span class="attribute">' . str_replace(array(
      '{name}',
      '{to_name}'
    ), array(
      mc_safeHTML($GC->from_name),
      mc_safeHTML($GC->to_name)
    ), $msg_invoice37) . '</span>' . mc_defineNewline();
    $att .= '</span>' . mc_defineNewline();
  }
  return mc_defineNewline() . trim($att);
}

// Sale attribute purchases..
function mc_saleAttributes($sale, $purchase, $product, $slip = false, $p_total = 0, $plain = false, $plainprice = false) {
  global $msg_viewsale87, $msg_sales42;
  $att = '';
  $pl  = array();
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_atts`
           LEFT JOIN `" . DB_PREFIX . "attributes`
           ON `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
           LEFT JOIN `" . DB_PREFIX . "attr_groups`
           ON `" . DB_PREFIX . "attributes`.`attrGroup` = `" . DB_PREFIX . "attr_groups`.`id`
           WHERE `" . DB_PREFIX . "purch_atts`.`saleID`    = '{$sale}'
           AND `" . DB_PREFIX . "purch_atts`.`purchaseID`  = '{$purchase}'
           AND `" . DB_PREFIX . "purch_atts`.`productID`   = '{$product}'
           ORDER BY `" . DB_PREFIX . "purch_atts`.`id`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  if (mysqli_num_rows($query) > 0) {
    while ($ATTRIBUTES = mysqli_fetch_object($query)) {
      if ($plain) {
        $pl[] = $ATTRIBUTES->groupName . ': ' . $ATTRIBUTES->attrName . ($plainprice && $ATTRIBUTES->addCost > 0 ? ' (+' . mc_currencyFormat(mc_formatPrice($ATTRIBUTES->addCost)) . ')' : '');
      } else {
        if (!$slip) {
          $att .= '<div><i class="fa fa-angle-right"></i> ' . mc_safeHTML($ATTRIBUTES->groupName) . ': ' . mc_safeHTML($ATTRIBUTES->attrName) . ($ATTRIBUTES->addCost > 0 ? ' (+' . mc_currencyFormat(mc_formatPrice($ATTRIBUTES->addCost)) . ')' : '') . '</div>' . mc_defineNewline();
        } else {
          $att .= '<div>' . mc_safeHTML($ATTRIBUTES->groupName) . ': ' . mc_safeHTML($ATTRIBUTES->attrName) . '</div>' . mc_defineNewline();
        }
      }
    }
  }
  return ($plain ? $pl : mc_defineNewline() . trim($att));
}

// Trade restrictions
function mc_tradeCatRestr($cats = array()) {
  if (!empty($cats)) {
    $string = array();
    $q      = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `id` IN(" . implode(',', $cats) . ")
              AND `enCat` = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($C = mysqli_fetch_object($q)) {
      switch($C->catLevel) {
        case '1':
          $string[] = mc_cleanData($C->catname);
          break;
        case '2':
          $P = mc_getTableData('categories', 'id', $C->childOf);
          $string[] = mc_cleanData($P->catname) . ' > ' . mc_cleanData($C->catname);
          break;
        case '3':
          $S = mc_getTableData('categories', 'id', $C->childOf);
          $P = mc_getTableData('categories', 'id', $S->childOf);
          $string[] = mc_cleanData($P->catname) . ' > ' . mc_cleanData($S->catname) . ' > ' . mc_cleanData($C->catname);
          break;
      }
    }
    return implode(mc_defineNewline(), $string);
  }
  return 'N/A';
}

// Re-activate cart..
function mc_autoActivateCart($rw) {
  mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "settings` SET
  `enableCart`   = 'yes',
  `offlineDate`  = '0000-00-00'
  LIMIT 1
  ") or die(mc_MySQLError(__LINE__, __FILE__));
  header("Location: " . $rw->url(array('base_href')));
  exit;
}

// Get savant..
function mc_getSavant() {
  $tpl = new Savant3();
  return $tpl;
}

// Get personalisation text..
function mc_persTextDisplay($text, $alt = false) {
  if (strpos($text, '|') === FALSE) {
    return $text;
  }
  $split = explode('|', $text);
  return ($alt && isset($split[1]) && $split[1] ? $split[1] : $split[0]);
}

// Display text based on whats enabled..
function mc_txtParsingEngine($text) {
  global $MCBB, $SETTINGS;
  return ($SETTINGS->enableBBCode == 'yes' ? $MCBB->bbParser($text) : (AUTO_PARSE_LINE_BREAKS ? mc_NL2BR(mc_cleanData($text)) : mc_cleanData($text)));
}

// New line to break..
function mc_NL2BR($text) {
  // Second param added in 5.3.0, else its not available..
  if (version_compare(phpversion(), '5.3.0', '<')) {
    return str_replace(mc_defineNewline(), '<br>', $text);
  }
  return nl2br($text, false);
}

// Clean up..
function mc_systemCartCleanUp($stgs) {
  $ts = strtotime(date("Y-m-d"));
  // Clear sales..
  if ($stgs->autoClear > 0) {
    $pend = array();
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "sales`
         WHERE `purchaseDate`   <= '" . date("Y-m-d", strtotime('-' . $stgs->autoClear . ' days', $ts)) . "'
         AND `saleConfirmation`  = 'no'
         AND `paymentStatus`     = 'pending'
         ");
    while ($PN = mysqli_fetch_object($q)) {
      $pend[] = $PN->id;
    }
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "sales`
    WHERE `purchaseDate`   <= '" . date("Y-m-d", strtotime('-' . $stgs->autoClear . ' days', $ts)) . "'
    AND `saleConfirmation`  = 'no'
    AND `id`           NOT IN (" . (!empty($pend) ? implode(',',$pend) : '0') . ")
    ");
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`saleID` FROM `" . DB_PREFIX . "purchases`
             WHERE `purchaseDate`   <= '" . date("Y-m-d", strtotime('-' . $stgs->autoClear . ' days', $ts)) . "'
             AND `saleConfirmation`  = 'no'
             AND `saleID`       NOT IN (" . (!empty($pend) ? implode(',',$pend) : '0') . ")
             ");
    while ($D = mysqli_fetch_object($query)) {
      // Clear personalisation..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers`
      WHERE `purchaseID`  = '{$D->id}'
      ");
      // Clear attributes..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_atts`
      WHERE `purchaseID`  = '{$D->id}'
      ");
      // Clear gift codes..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "giftcodes`
      WHERE `saleID`  = '{$D->saleID}'
      ");
    }
    // Clear purchases..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purchases`
    WHERE `purchaseDate`   <= '" . date("Y-m-d", strtotime('-' . $stgs->autoClear . ' days', $ts)) . "'
    AND `saleConfirmation`  = 'no'
    ");
  }
  // Clear searches..
  if ($stgs->savedSearches > 0) {
    $sc   = array();
    $q_ss = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `searchCode` FROM `" . DB_PREFIX . "search_index`
            WHERE `searchDate` <= '" . date("Y-m-d", strtotime('-' . $stgs->savedSearches . ' days', $ts)) . "'
            ");
    while ($SS = mysqli_fetch_object($q_ss)) {
      $sc[] = "'" . $SS->searchCode . "'";
    }
    if (!empty($sc)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "search_index`
      WHERE `searchCode` IN(" . implode(',', $sc) . ")
      ");
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_search`
      WHERE `code` IN(" . implode(',', $sc) . ")
      ");
    }
  }
}

// Memory allocation override..
function mc_memAllocation($memo = '100M', $limit = 0) {
  @ini_set('memory_limit', $memo);
  @set_time_limit($limit);
}

// Get help text..
function mc_getHelp($help, $methods) {
  global $public_checkout63;
  if (isset($methods[$help]['ID'])) {
    $which = $methods[$help]['lang'];
    $msg   = mc_txtParsingEngine($methods[$help]['info']);
  } else {
    die('Payment option not supported or enabled');
  }
  return array(
    $which,
    ($msg ? $msg : $public_checkout63)
  );
}

// Convert us date to specified date..
function mc_convertMySQLDate($date, $s) {
  if ($date == '') {
    return '0000-00-00';
  }
  $split = array_map('trim', explode('-', $date));
  switch($s->jsDateFormat) {
    case 'DD-MM-YYYY':
      return $split[2] . '-' . $split[1] . '-' . $split[0];
      break;
    case 'DD/MM/YYYY':
      return $split[2] . '/' . $split[1] . '/' . $split[0];
      break;
    case 'YYYY-MM-DD':
      return $split[0] . '-' . $split[1] . '-' . $split[2];
      break;
    case 'YYYY/MM/DD':
      return $split[0] . '/' . $split[1] . '/' . $split[2];
      break;
    case 'MM-DD-YYYY':
      return $split[2] . '-' . $split[0] . '-' . $split[1];
      break;
    case 'MM/DD/YYYY':
      return $split[2] . '/' . $split[0] . '/' . $split[1];
      break;
  }
}

// Get backup format..
function mc_backupDateFormat($s, $skipslash = false) {
  switch($s->jsDateFormat) {
    case 'DD-MM-YYYY':
    case 'DD/MM/YYYY':
      return ($skipslash ? 'd-m-Y' : 'd/m/Y');
      break;
    case 'YYYY-MM-DD':
    case 'YYYY/MM/DD':
      return 'Y-m-d';
      break;
    case 'MM-DD-YYYY':
    case 'MM/DD/YYYY':
      return ($skipslash ? 'm-d-Y' : 'm/d/Y');
      break;
    default:
      return ($skipslash ? 'd-m-Y' : 'd/m/Y');
      break;
  }
}

// Convert date display in form field boxes..
function mc_convertBoxedDate($date, $s) {
  if ($date == '') {
    return '';
  }
  // Is the date a correct timestamp?
  if (strtotime($date) > 0) {
    return date('Y-m-d', strtotime($date));
  }
  if (strpos($date, '-') !== false) {
  $split = array_map('trim', explode('-', $date));
  } else {
    $split = array_map('trim', explode('/', $date));
  }
  switch($s->jsDateFormat) {
    case 'DD-MM-YYYY':
    case 'DD/MM/YYYY':
      return $split[2] . '-' . $split[1] . '-' . $split[0];
      break;
    case 'YYYY-MM-DD':
    case 'YYYY/MM/DD':
      return $split[0] . '-' . $split[1] . '-' . $split[2];
      break;
    case 'MM-DD-YYYY':
    case 'MM/DD/YYYY':
      return $split[1] . '-' . $split[2] . '-' . $split[0];
      break;
    default:
      return date('Y-m-d');
      break;
  }
}

// Get mysql date format..
function mc_convertCalToSQLFormat($date, $s) {
  if ($date == '') {
    return '0000-00-00';
  }
  if (strpos($date, '-') !== false) {
    $split = array_map('trim', explode('-', $date));
  } else {
    $split = array_map('trim', explode('/', $date));
  }
  if (isset($split[0],$split[1],$split[2])) {
    switch($s->jsDateFormat) {
      case 'DD-MM-YYYY':
      case 'DD/MM/YYYY':
        return $split[2] . '-' . $split[1] . '-' . $split[0];
        break;
      case 'YYYY-MM-DD':
      case 'YYYY/MM/DD':
        return $split[0] . '-' . $split[1] . '-' . $split[2];
        break;
      case 'MM-DD-YYYY':
      case 'MM/DD/YYYY':
        return $split[2] . '-' . $split[0] . '-' . $split[1];
        break;
      default:
        return date('Y-m-d');
        break;
    }
  } else {
    return date('Y-m-d');
  }
}

// Cleans CSV..adds quotes if data contains delimiter..
function mc_cleanCSV($data, $del) {
  $data = str_replace('"', '', $data);
  return '"' . mc_cleanData($data) . '"';
}

// Platform..
function mc_salePlatform($flag, $l) {
  switch($flag) {
    case 'desktop':
    case 'pc':
      return '<i class="fa fa-desktop fa-fw"></i> ' . $l[0];
      break;
    case 'tablet':
      return '<i class="fa fa-tablet fa-fw"></i> ' . $l[1];
      break;
    case 'mobile':
      return '<i class="fa fa-mobile fa-fw"></i> ' . $l[2];
      break;
  }
}

// Payment method name..
function mc_paymentMethodName($method) {
  global $msg_admin_global, $mcSystemPaymentMethods, $msg_admin_global2;
  switch($method) {
    case 'free':
      return $msg_admin_global2;
      break;
    default:
      return (isset($mcSystemPaymentMethods[$method]) ? $mcSystemPaymentMethods[$method]['lang'] : $msg_admin_global);
      break;
  }
}

function mc_skipLogUsers() {
  $skip = array();
  if (ENTRY_LOG_SKIP_USERS) {
    if (strpos(ENTRY_LOG_SKIP_USERS, ',') !== FALSE) {
      $skip = array_map('trim', explode(',', strtolower(ENTRY_LOG_SKIP_USERS)));
    } else {
      $skip[] = strtolower(ENTRY_LOG_SKIP_USERS);
    }
  }
  return $skip;
}

// Copy addresses..
function mc_getCopyAddresses($emails) {
  if (strpos($emails, ',') !== FALSE) {
    $e = explode(',', $emails);
  } else {
    $e   = array();
    $e[] = $emails;
  }
  return array_map('trim', $e);
}

// Brand counts..
function brandCountDisplay($id, $settings) {
  if ($settings->menuBrandCount == 'yes') {
    if (strpos($id, ',') !== false) {
      $b = array();
      // For multiple, we need only count the product once..
      foreach (explode(',', $id) AS $i) {
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `" . DB_PREFIX . "prod_brand`.`product` AS `pid`
             FROM `" . DB_PREFIX . "prod_brand`
             LEFT JOIN `" . DB_PREFIX . "products`
             ON `" . DB_PREFIX . "prod_brand`.`product` = `" . DB_PREFIX . "products`.`id`
             WHERE `brand` = '{$i}'
             AND `pEnable` = 'yes'
             ");
        while ($P = mysqli_fetch_object($q)) {
          if (!in_array($P->pid, $b)) {
            $b[] = $P->pid;
          }
        }
      }
      $bCount = @number_format(count($b));
    } else {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `count`
           FROM `" . DB_PREFIX . "prod_brand`
           LEFT JOIN `" . DB_PREFIX . "products`
           ON `" . DB_PREFIX . "prod_brand`.`product` = `" . DB_PREFIX . "products`.`id`
           WHERE `brand` = '{$id}'
           AND `pEnable` = 'yes'
           ");
      $CN = mysqli_fetch_object($q);
      $bCount = (isset($CN->count) ? @number_format($CN->count) : '0');

    }
    return ' <span class="menuProdCount">(' . $bCount . ')</span>';
  }
  return '';
}

function mc_getProductCatRelation($id, $s) {
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
       DATE_FORMAT(`pDateAdded`,'" . $s->mysqlDateFormat . "') AS `adate`,
       `" . DB_PREFIX . "products`.`id` AS `pid`,
       `" . DB_PREFIX . "products`.`enDisqus` AS `pDISQ`,`" . DB_PREFIX . "categories`.`enDisqus` AS `cDISQ`,
       `" . DB_PREFIX . "products`.`rwslug` AS `pSlug`,`" . DB_PREFIX . "categories`.`rwslug` AS `cSlug`,
       `" . DB_PREFIX . "prod_category`.`category` AS `prodCat`
       FROM `" . DB_PREFIX . "products`
       LEFT JOIN `" . DB_PREFIX . "prod_category`
        ON `" . DB_PREFIX . "products`.`id`        = `" . DB_PREFIX . "prod_category`.`product`
       LEFT JOIN `" . DB_PREFIX . "categories`
        ON `" . DB_PREFIX . "categories`.`id`      = `" . DB_PREFIX . "prod_category`.`category`
       WHERE `" . DB_PREFIX . "products`.`id`     = '{$id}'
       AND `" . DB_PREFIX . "products`.`pEnable`  = 'yes'
       AND `" . DB_PREFIX . "categories`.`enCat`  = 'yes'
       GROUP BY `" . DB_PREFIX . "products`.`id`
       LIMIT 1
       ") or die(mc_MySQLError(__LINE__, __FILE__));
  $p = mysqli_fetch_object($q);
  if (isset($p->pid)) {
    return $p;
  } else {
    // Does product exist with no categories?
    $p = mc_getTableData('products', 'id', (int) $id, ' AND `pEnable` = \'yes\'');
    if (isset($p->id)) {
      return 'no-cat';
    }
  }
  return '';
}

function mc_callBackUrls($cmd) {
  if (isset($_GET['q'])) {
    return 'search';
  } elseif (isset($_GET['pdl']) || isset($_GET['zip']) || isset($_GET['pinfo']) || isset($_GET['vOrder'])) {
    return 'view-order';
  } elseif (isset($_GET['pd']) || isset($_GET['dsc']) || isset($_GET['pCRes']) || isset($_GET['pMP3'])) {
    return 'product';
  } elseif (isset($_GET['c'])) {
    return 'category';
  } elseif (isset($_GET['pbnd'])) {
    return 'brands';
  } elseif (isset($_GET['sk'])) {
    return 'search';
  } elseif (isset($_GET['np'])) {
    return 'np';
  } elseif (isset($_GET['crss']) || isset($_GET['rss']) || isset($_GET['brss'])) {
    return 'feed';
  } elseif (isset($_GET['help']) || isset($_GET['clearcart']) || isset($_GET['ppCE']) || isset($_GET['gift']) || isset($_GET['ppRebuild'])) {
    return 'checkpay';
  } elseif (isset($_GET['clearDiscountCoupon']) || isset($_GET['reloadTotalCoupons'])) {
    return 'checkpay';
  } else {
    return $cmd;
  }
}

function mc_detectSSLConnection($s) {
  return ($s->enableSSL == 'yes' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'yes' : 'no');
}

// Image display path..
function mc_imageDisplayPath($folder) {
  return $folder;
}

// Calculate invoice number based on digits..
function mc_saleInvoiceNumber($num, $s) {
  $zeros = '';
  if ($s->minInvoiceDigits > 0 && $s->minInvoiceDigits > strlen($num)) {
    for ($i = 0; $i < $s->minInvoiceDigits - strlen($num); $i++) {
      $zeros .= 0;
    }
  }
  return ($zeros . $num);
}

// Adjust text for sale status..
function mc_statusText($status) {
  global $msg_script26, $msg_script27, $msg_script28, $msg_script63, $msg_script64, $msg_script74;
  if (ctype_digit($status)) {
    $STAT = mc_getTableData('paystatuses', 'id', $status);
    return (isset($STAT->statname) ? mc_cleanData($STAT->statname) : $msg_script29);
  } else {
    switch($status) {
      case 'completed':
        return $msg_script26;
        break;
      case 'refund':
        return $msg_script28;
        break;
      case 'pending':
        return $msg_script27;
        break;
      case 'cancelled':
        return $msg_script63;
        break;
      case 'despatched':
        return $msg_script64;
        break;
      case 'shipping':
        return $msg_script74;
        break;
      default:
        return 'N/A';
        break;
    }
  }
}

// Platforms..
function mc_loadPlatforms($l) {
  return array(
    'desktop' => $l[0],
    'tablet' => $l[1],
    'mobile' => $l[2]
  );
}

// Default statuses..
function mc_loadDefaultStatuses() {
  global $msg_script26, $msg_script27, $msg_script28, $msg_script63, $msg_script64, $msg_script74;
  return array(
    'completed' => $msg_script26,
    'pending' => $msg_script27,
    'refund' => $msg_script28,
    'cancelled' => $msg_script63,
    'despatched' => $msg_script64,
    'shipping' => $msg_script74
  );
}

// Check valid email..
function mswIsValidEmail($em) {
  if (function_exists('filter_var') && filter_var($em, FILTER_VALIDATE_EMAIL)) {
    return true;
  }
  if (preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
    '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD', $em)) {
    return true;
  }
  return false;
}

// Check search key..
function mc_validateSearchKey($key) {
  if (strpos($key, '-') !== false) {
    $split = explode('-', $key);
    if (isset($split[0]) && ctype_digit($split[0]) && isset($split[1]) && ctype_alnum($split[1])) {
      return true;
    }
  }
  return false;
}

// Check digit var..
function mc_checkDigit($id) {
  if (!preg_match('/^[0-9]+$/i', $id)) {
    global $mc_global, $MCSOCIAL, $SETTINGS, $charset, $slidePanel, $leftBoxDisplay, $errorPages;
    include(PATH . 'control/system/headers/403.php');
    exit;
  }
}

function mc_splitAccName($name) {
  return explode(' ', $name);
}

function mc_digitSan($no) {
  return (preg_match('/^[0-9]+$/i', $no) ? $no : '0');
}

function mc_checkValidDate($str) {
  if ($str == '') {
    return '0000-00-00';
  }
  $stamp = strtotime($str);
  if ($stamp < 0) {
    return '0000-00-00';
  }
  $month = date('m', $stamp);
  $day   = date('d', $stamp);
  $year  = date('Y', $stamp);
  if (checkdate($month, $day, $year)) {
    return $str;
  }
  return '0000-00-00';
}

// Date picker event..
function mc_datePickerFormat($settings) {
  // Convert into js format dates..
  switch($settings->jsDateFormat) {
    case 'DD-MM-YYYY':
      $formatJS = 'dd-mm-yy';
      break;
    case 'DD/MM/YYYY':
      $formatJS = 'dd/mm/yy';
      break;
    case 'YYYY-MM-DD':
      $formatJS = 'yy-mm-dd';
      break;
    case 'YYYY/MM/DD':
      $formatJS = 'yy/mm/dd';
      break;
    case 'MM-DD-YYYY':
      $formatJS = 'mm-dd-yy';
      break;
    case 'MM/DD/YYYY':
      $formatJS = 'mm/dd/yy';
      break;
  }
  return $formatJS;
}

// If there is no data for a specific area, load default..
function mc_nothingToShow($msg, $tags = false, $tmp = '') {
  return str_replace(array(
    '{text}'
  ), array(
    $msg
  ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/' . ($tmp ? $tmp : 'nothing-to-show.htm')));
}

// Filter for some javascript routines
function mc_filterJS($data) {
  return str_replace(array("'",'"'), array("\'"), $data);
}

// Public pagination..
function mc_publicPageNumbers($count, $limit, $seo, $skip = '') {
  global $msg_script77, $page, $SETTINGS;
  if (!defined('PER_PAGE')) {
    define('PER_PAGE', $limit);
  }
  $PTION = new pagination(array(
    $count,
    $msg_script77,
    $page,
    $skip
  ), $seo, (defined('ADMIN_PANEL') ? 'yes' : 'no'), $SETTINGS);
  $pages = $PTION->display();
  if ($pages) {
    return str_replace('{pages}', $pages, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/page-numbers.htm'));
  }
  return '';
}

// Format price..
function mc_formatPrice($price, $comma = false) {
  $sep = '';
  if (PRICE_THOUSANDS_SEPARATORS && $comma) {
    $sep = PRICE_THOUSANDS_SEPARATORS;
  }
  $price = @number_format($price, 2, '.', '');
  $price = preg_replace("/[^0-9\.]/", "", str_replace(',', '.', $price));
  if (substr($price, -3, 1) == '.') {
    $pennies = '.' . substr($price, -2);
    $price   = substr($price, 0, strlen($price) - 3);
  } elseif (substr($price, -2, 1) == '.') {
    $pennies = '.' . substr($price, -1);
    $price   = substr($price, 0, strlen($price) - 2);
  } else {
    $pennies = '.00';
  }
  $price = preg_replace("/[^0-9]/", "", $price);
  // Prevent formatting errors during imports..
  if ($price == '') {
    $price = '0';
  }
  if (rtrim($pennies, '.') == '') {
    $pennies = '.00';
  }
  return ($price . $pennies > 0 ? @number_format($price . $pennies, PRICE_FORMAT_DECIMAL_PLACES, '.', ($sep ? $sep : '')) : '0.00');
}

// Help tip..
function mc_displayHelpTip($text) {
  return '';
}

// Cleans output data..
function mc_cleanData($data) {
  if (version_compare(PHP_VERSION, '5.4', '<') && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    $sybase = strtolower(@ini_get('magic_quotes_sybase'));
    if (empty($sybase) || $sybase == 'off') {
      // Fixes issue of new line chars not parsing between single quotes..
      $data = str_replace('\n', '\\\n', $data);
      return stripslashes($data);
    }
  }
  return $data;
}

// Cleans output data with character entities..
function mc_safeHTML($data) {
  $data = htmlspecialchars($data);
  $data = str_replace('&amp;#', '&#', $data);
  $data = str_replace('&amp;amp;', '&amp;', $data);
  return mc_cleanData($data);
}

// Cleans variables..
function mc_cleanDataEntVars($data) {
  $data = str_replace(array(
    '"'
  ), array(
    '&quot;'
  ), $data);
  return $data;
}

// Cleans output data without amendments..
function mc_cleanRawData($data) {
  if (version_compare(PHP_VERSION, '5.4', '<') && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    $sybase = strtolower(@ini_get('magic_quotes_sybase'));
    if (empty($sybase) || $sybase == 'off') {
      // Fixes issue of new line chars not parsing between single quotes..
      $data = str_replace('\n', '\\\n', $data);
      return stripslashes($data);
    }
  }
  return $data;
}

// Gets sum count..
function mc_sumCount($table, $rowname = '', $format = true) {
  $rowname = (substr($rowname, 0, 1) == '`' ? $rowname : '`' . $rowname . '`');
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT sum($rowname) AS `s_count` FROM " . DB_PREFIX . $table) or die(mc_MySQLError(__LINE__, __FILE__));
  $row = mysqli_fetch_object($query);
  if ($format) {
    return number_format($row->s_count, 0, '.', '');
  } else {
    return $row->s_count;
  }
}

// Gets row count..
function mc_rowCount($table, $where = '', $format = true) {
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `r_count` FROM " . DB_PREFIX . $table . $where) or die(mc_MySQLError(__LINE__, __FILE__));
  $row = mysqli_fetch_object($query);
  if ($format) {
    return number_format($row->r_count);
  } else {
    return $row->r_count;
  }
}

// Currency formatting..
function mc_currencyFormat($price = '', $currency = '', $alttext = false) {
  global $SETTINGS;
  // Alternative text for free items..
  if ($alttext && $SETTINGS->freeTextDisplay) {
    if (in_array($price, array(
      '0.00',
      '0',
      ''
    ))) {
      return mc_safeHTML($SETTINGS->freeTextDisplay);
    }
  }
  if (defined('PDF_SHOW_CURRENCY_SYMBOLS') && !PDF_SHOW_CURRENCY_SYMBOLS) {
    return $price;
  }
  return str_replace('{PRICE}', $price, $SETTINGS->currencyDisplayPref);
}

// ISO4217 conversion..
function mc_iso4217_conversion($cur) {
  global $iso4217_conversion;
  return (isset($iso4217_conversion[$cur]) ? $iso4217_conversion[$cur] : '000');
}

// Clean form input..
function mc_cleanStripTags($data) {
  return strip_tags(trim($data));
}

// Gets data based on param criteria..
function mc_getTableData($table, $row, $id, $and = '', $params = '*') {
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT $params FROM `" . DB_PREFIX . $table . "`
           WHERE $row  = '{$id}'
           $and
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  return mysqli_fetch_object($query);
}

// File size..
function mc_fileSizeConversion($size = 0, $precision = 2) {
  if ($size > 0) {
    $base     = log($size) / log(1024);
    $suffixes = array(
      'Bytes',
      'KB',
      'MB',
      'GB',
      'TB'
    );
    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
  } else {
    return '0Bytes';
  }
}

// Gets data based on param criteria of 2 joined tables..
function mc_getJoinedTableData($table, $table2, $join, $join2, $row, $id, $date = '', $and = '') {
  global $SETTINGS;
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . $table . "`.`id` AS `id1`,
           `" . DB_PREFIX . $table2 . "`.`id` AS `id2`
           " . ($date ? ",DATE_FORMAT(`" . DB_PREFIX . $table . "`.`" . $date . "`,'" . $SETTINGS->mysqlDateFormat . "') AS `a_date`" : "") . "
           FROM `" . DB_PREFIX . $table . "`
           LEFT JOIN `" . DB_PREFIX . $table2 . "`
           ON `" . DB_PREFIX . $table . "`" . $join . "`    = `" . DB_PREFIX . $table2 . "`" . $join2 . "`
           WHERE `" . DB_PREFIX . $table . "`" . $row . "`  = '{$id}'
           $and
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  return mysqli_fetch_object($query);
}

// Define new line per op system..
function mc_defineNewline() {
  if (defined('PHP_EOL')) {
    return PHP_EOL;
  }
  $nl = "\r\n";
  if (isset($_SERVER["HTTP_USER_AGENT"]) && strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
    $nl = "\r\n";
  } else if (isset($_SERVER["HTTP_USER_AGENT"]) && strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
    $nl = "\r";
  } else {
    $nl = "\n";
  }
  return $nl;
}

// Gets visitor IP address..
function mc_getRealIPAddr($arr = false) {
  $ips = array();
  $types = array(
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'REMOTE_ADDR'
  );
  foreach ($types AS $key) {
    if (array_key_exists($key, $_SERVER) === true) {
      foreach (array_map('trim', explode(',', $_SERVER[$key])) AS $ipA) {
        if (!in_array($ipA, $ips) && filter_var($ipA, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
          $ips[] = $ipA;
        } else {
          // Double check for localhost..
          if (!in_array($ipA, $ips) && in_array($ipA, array('::1','127.0.0.1'))) {
            $ips[] = $ipA;
          }
        }
      }
    }
  }
  if ($arr) {
    return $ips;
  }
  return (!empty($ips) ? implode(',', $ips) : '');
}

// Get browser type..
function mc_getBrowserType() {
  $agent = 'IE';
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    if (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'OPERA') !== FALSE) {
      $agent = 'OPERA';
    } elseif (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'MSIE') !== FALSE) {
      $agent = 'IE';
    } elseif (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'OMNIWEB') !== FALSE) {
      $agent = 'OMNIWEB';
    } elseif (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'MOZILLA') !== FALSE) {
      $agent = 'MOZILLA';
    } elseif (strpos(strtoupper($_SERVER['HTTP_USER_AGENT']), 'KONQUEROR') !== FALSE) {
      $agent = 'KONQUEROR';
    } else {
      $agent = 'OTHER';
    }
  }
  return $agent;
}

// Get mime type..
function mc_getMimeType($file) {
  global $mc_mimeTypes;
  $e = substr(strrchr(strtolower($file), '.'), +1);
  $a = mc_getBrowserType();
  // Check for PECL extension..
  if (function_exists('finfo_file')) {
    $info = finfo_open(FILEINFO_MIME_TYPE);
    $type = finfo_file($info, $file);
    // Check mime array..
  } else if (isset($mc_mimeTypes[$e])) {
    $type = $mc_mimeTypes[$e];
    // Fallback..
  } else {
    $type = (in_array($a, array(
      'IE',
      'OPERA'
    )) ? 'application/octetstream' : 'application/octet-stream');
  }
  return $type;
}

// Returns encrypted data..
function mc_encrypt($data) {
  return (function_exists('sha1') ? sha1($data) : md5($data));
}

// Safe mysql import..
function mc_safeImport($data) {
  global $SETTINGS;
  if (version_compare(PHP_VERSION, '5.4', '<') && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    $sybase = strtolower(@ini_get('magic_quotes_sybase'));
    if (empty($sybase) || $sybase == 'off') {
      $data = mc_multiDimensionalArrayMap('stripslashes', $data);
    } else {
      $data = mc_multiDimensionalArrayMap('mc_removeDoubleApostrophes', $data);
    }
  }
  if ($SETTINGS->smartQuotes == 'yes') {
    $data = mc_multiDimensionalArrayMap('mc_convertSmartQuotes', $data);
  }
  $data = mc_multiDimensionalArrayMap('mysqli_real_escape_string', $data);
  return $data;
}

// Parse url for query string params..
function mswQueryParams($skip = array(), $start = 'no') {
  $s = '';
  if (!empty($_GET)) {
    foreach ($_GET AS $gK => $gV) {
      // Check for array elements in query string..
      if (is_array($gV)) {
        foreach ($gV AS $gKA => $gVA) {
          if (!in_array($gK, $skip)) {
            $s .= '&amp;' . $gK . '[]=' . mc_safeHTML($gVA);
          }
        }
      } else {
        if (!in_array($gK, $skip)) {
          $s .= '&amp;' . $gK . '=' . mc_safeHTML($gV);
        }
      }
    }
  }
  return ($start == 'yes' ? substr($s, 5) : $s);
}

// Fix Microsoft Word smart quotes..
function mc_convertSmartQuotes($string) {
  if (mb_check_encoding($string, 'UTF-8')) {
    return $string;
  }
  $search  = array(
    chr(145),
    chr(146),
    chr(147),
    chr(148),
    chr(151)
  );
  $replace = array(
    "'",
    "'",
    '"',
    '"',
    '-'
  );
  return str_replace($search, $replace, $string);
}

// Password hash..
function mc_PassHash($d = array()) {
  switch($d['type']) {
    case 'add':
      return password_hash($d['pass'], PASSWORD_BCRYPT);
      break;
    case 'calc':
      return password_verify($d['val'], $d['hash']);
      break;
  }
}

// Safe mysql import..none array..
function mc_safeSQL($data) {
  global $SETTINGS;
  if (version_compare(PHP_VERSION, '5.4', '<') && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    $sybase = strtolower(@ini_get('magic_quotes_sybase'));
    if (empty($sybase) || $sybase == 'off') {
      $data = stripslashes($data);
    } else {
      $data = mc_removeDoubleApostrophes($data);
    }
  }
  if (!defined('INSTALL_DIR') && isset($SETTINGS->smartQuotes)) {
    if ($SETTINGS->smartQuotes == 'yes') {
      $data = mc_convertSmartQuotes($data);
    }
  }
  $data = ((isset($GLOBALS["___msw_sqli"]) && is_object($GLOBALS["___msw_sqli"])) ? mysqli_real_escape_string($GLOBALS["___msw_sqli"], $data) : ((trigger_error("Fix the mysqli_real_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
  return $data;
}

// Quote/apostrophe stripper..
function mc_quoteStripper($data) {
  return str_replace(array(
    '"',
    "'"
  ), array(), $data);
}

// Recursive way of handling multi dimensional arrays..
function mc_multiDimensionalArrayMap($func, $arr) {
  $newArr = array();
  if (!empty($arr)) {
    foreach ($arr AS $key => $value) {
      if ($func == 'mysqli_real_escape_string') {
        $newArr[$key] = (is_array($value) ? mc_multiDimensionalArrayMap($func, $value) : $func($GLOBALS["___msw_sqli"], $value));
      } else {
        $newArr[$key] = (is_array($value) ? mc_multiDimensionalArrayMap($func, $value) : $func($value));
      }
    }
  }
  return $newArr;
}

// Remove double apostrophes via magic quotes setting..
function mc_removeDoubleApostrophes($data) {
  return str_replace("''", "'", $data);
}

// Clean BB Input..
// Strip whitespace from tags to prevent html warnings about malformed urls..
function mc_cleanBBInput($data) {
  global $SETTINGS;
  if ($SETTINGS->enableBBCode == 'yes') {
    $find = array(
      '[img] ',
      ' [/img]',
      '[url] ',
      ' [/url]',
      '[email] ',
      ' [/email]',
      '[IMG] ',
      ' [/IMG]',
      '[URL] ',
      ' [/URL]',
      '[EMAIL] ',
      ' [/EMAIL]'
    );
    $repl = array(
      '[img]',
      '[/img]',
      '[url]',
      '[/url]',
      '[email]',
      '[/email]',
      '[IMG]',
      '[/IMG]',
      '[URL]',
      '[/URL]',
      '[EMAIL]',
      '[/EMAIL]'
    );
    $data = str_replace($find, $repl, $data);
  }
  return $data;
}

// Language file reload..
if (isset($_GET['lang'])) {
  if (is_dir(PATH . 'content/language/' . $_GET['lang'])) {
    @setcookie("langswitch_" . mc_encrypt(SECRET_KEY), $_GET['lang'], time() + 60 * 60 * 24 * 30);
    header("Location: index.php");
  } else {
    include(PATH . 'control/system/headers/403.php');
  }
  exit;
}

// Check language cookie..
if (defined('SECRET_KEY')) {
  if (isset($_COOKIE['langswitch_' . mc_encrypt(SECRET_KEY)]) && !is_dir(PATH . 'content/language/' . $_COOKIE['langswitch_' . mc_encrypt(SECRET_KEY)])) {
    setcookie('langswitch_' . mc_encrypt(SECRET_KEY), '');
    unset($_COOKIE['langswitch_' . mc_encrypt(SECRET_KEY)]);
  }
}

// Controller..
function mc_fileController() {
  if (!file_exists(GLOBAL_PATH . 'control/system/core/sys-controller.php')) {
    die('[FATAL ERROR] The "control/system/core/sys-controller.php" file does NOT exist in your installation. It may have been auto deleted by your anti virus software. If
    this is the case, this is a false positive. Please add the file to your anti virus whitelist, re-add and refresh page.');
  }
}

// Global filtering on post and get input..
$GCLN  = $_GET;
$_GET  = mc_multiDimensionalArrayMap('mc_cleanStripTags', $_GET);
$_GET  = mc_multiDimensionalArrayMap('mc_quoteStripper', $_GET);
$_GET  = mc_multiDimensionalArrayMap('htmlspecialchars', $_GET);
$_POST = mc_multiDimensionalArrayMap('trim', $_POST);

?>