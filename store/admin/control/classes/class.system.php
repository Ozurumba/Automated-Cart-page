<?php

class systemEngine {

  public $settings;
  public $dl;
  public $cache;

  public function shipOptions() {
    $arr = array();
    foreach ($_POST['sp'] AS $k) {
      $arr[$k] = (isset($_POST['ship'][$k]) ? 'yes' : 'no');
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
    `shipopts` = '" . mc_safeSQL(serialize($arr)) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function addThemeSwitch() {
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT IGNORE INTO `" . DB_PREFIX . "themes` (
    `theme`,
    `from`,
    `to`,
    `enabled`
    ) VALUES (
    '" . mc_safeSQL($_POST['theme']) . "',
    '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['from'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['from'], $this->settings) : '0000-00-00') . "',
    '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['to'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['to'], $this->settings) : '0000-00-00') . "',
    '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateThemeSwitch() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "themes` SET
    `theme`    = '" . mc_safeSQL($_POST['theme']) . "',
    `from`     = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['from'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['from'], $this->settings) : '0000-00-00') . "',
    `to`       = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['to'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['to'], $this->settings) : '0000-00-00') . "',
    `enabled`  = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "'
    WHERE `id` = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteThemeSwitch() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "themes` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'themes'
    ));
    return $rows;
  }

  public function reOrderNewsTicker() {
    if (!empty($_GET['nt']) && is_array($_GET['nt'])) {
      foreach ($_GET['nt'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "news_ticker` SET
        `orderBy`   = '" . ($k + 1) . "'
        WHERE `id`  = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addNews() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "news_ticker` (
    `newsText`,
    `enabled`,
    `orderBy`
    ) VALUES (
    '{$_POST['newsText']}',
    '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "',
    '99999'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateNews() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "news_ticker` SET
    `newsText`  = '{$_POST['newsText']}',
    `enabled`   = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
      'yes',
      'no'
    )) ? $_POST['enabled'] : 'yes') . "'
    WHERE `id`  = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteNews() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "news_ticker` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'news_ticker'
    ));
    return $rows;
  }

  public function exportLowStockItems() {
    global $msg_stockexport9;
    if (!empty($_POST['range'])) {
      $separator = ',';
      $csvFile   = PATH . 'import/low-stock-items-' . date('d-m-Y-His') . '.csv';
      $data      = $msg_stockexport9 . mc_defineNewline();
      $from      = (int) $_POST['from'];
      $to        = (int) $_POST['to'];
      $disabled  = (isset($_POST['disabled']) && $_POST['disabled'] == 'no' ? 'AND `pEnable` = \'yes\'' : '');
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
               LEFT JOIN `" . DB_PREFIX . "prod_category`
               ON `" . DB_PREFIX . "prod_category`.`product` = `" . DB_PREFIX . "products`.`id`
               WHERE `category` IN(" . implode(',', $_POST['range']) . ")
               AND `pStock` BETWEEN '{$from}' AND '{$to}'
               $disabled
               GROUP BY `product`
               ORDER BY `pName`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($PRODUCT = mysqli_fetch_object($query)) {
        $cats = '';
        $qCat = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "prod_category`
                LEFT JOIN `" . DB_PREFIX . "categories`
                ON `" . DB_PREFIX . "prod_category`.`category`    = `" . DB_PREFIX . "categories`.`id`
                WHERE `" . DB_PREFIX . "prod_category`.`product`  = '{$PRODUCT->pid}'
                ORDER BY `catname`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($CATS = mysqli_fetch_object($qCat)) {
          $cats .= mc_cleanData($CATS->catname) . $separator;
        }
        $data .= mc_cleanCSV($PRODUCT->pName, $separator) . $separator;
        $data .= '' . $separator;
        $data .= mc_cleanCSV(substr($cats, 0, -1), $separator) . $separator;
        $data .= mc_cleanCSV($PRODUCT->pStock, $separator) . $separator . mc_defineNewline();
        $qAtt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
                WHERE `productID` = '{$PRODUCT->pid}'
                AND `attrStock` BETWEEN '{$from}' AND '{$to}'
                ORDER BY `orderBy`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (mysqli_num_rows($qAtt) > 0) {
          while ($ATTRIBUTES = mysqli_fetch_object($qAtt)) {
            $data .= mc_cleanCSV($PRODUCT->pName, $separator) . $separator;
            $data .= mc_cleanCSV($ATTRIBUTES->attrName, $separator) . $separator;
            $data .= mc_cleanCSV(substr($cats, 0, -1), $separator) . $separator;
            $data .= mc_cleanCSV($ATTRIBUTES->attrStock, $separator) . $separator . mc_defineNewline();
          }
        }
      }
      if ($data) {
        $this->dl->write($csvFile, trim($data));
        $this->dl->dl($csvFile, 'application/force-download', 'yes');
      }
    }
    return 'none';
  }

  public function resetSearchLog() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "search_log`") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'search_log'
    ));
    return $rows;
  }

  public function exportSearchLog($lng) {
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/search-log-' . date('d-m-Y-His') . '.csv';
    $data      = $lng . mc_defineNewline();
    $totalLogs = mc_rowCount('search_log', '', false);
    $q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
           count(*) AS `sr`,
           ROUND((count(*) / " . $totalLogs . ") * 100, " . STATS_DECIMAL_PLACES . ") AS `perc`
           FROM `" . DB_PREFIX . "search_log`
           " . (isset($_GET['zero']) ? 'WHERE `results` = \'0\'' : '') . "
           GROUP BY `keyword`
           ORDER BY `perc` DESC
           ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($LOG = mysqli_fetch_object($q_l)) {
      $data .= mc_cleanCSV($LOG->keyword, $separator) . $separator . mc_cleanCSV($LOG->sr, $separator) . $separator . mc_cleanCSV($LOG->results, $separator) . $separator . mc_cleanCSV($LOG->perc . '%', $separator) . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function reOrderBanners() {
    if (!empty($_GET['ban']) && is_array($_GET['ban'])) {
      foreach ($_GET['ban'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "banners` SET
        `bannerOrder` = '" . ($k + 1) . "'
        WHERE `id`    = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function reOrderPricePoints() {
    if (!empty($_GET['pp']) && is_array($_GET['pp'])) {
      foreach ($_GET['pp'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "price_points` SET
        `orderBy`   = '" . ($k + 1) . "'
        WHERE `id`  = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addPricePoint() {
    $_POST = mc_safeImport($_POST);
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('price_points') + 1 > RESTR_POINTS) {
        mc_restrictionLimitRedirect();
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "price_points` (
    `priceFrom`,
	  `priceTo`,
	  `priceText`,
    `orderBy`
    ) VALUES (
    '{$_POST['priceFrom']}',
    '{$_POST['priceTo']}',
    '{$_POST['priceText']}',
    '99999'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updatePricePoint() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "price_points` SET
    `priceFrom`  = '{$_POST['priceFrom']}',
    `priceTo`    = '{$_POST['priceTo']}',
    `priceText`  = '{$_POST['priceText']}'
    WHERE id     = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deletePricePoint() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "price_points` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'price_points'
    ));
    return $rows;
  }

  // Export log..
  public function exportEntryLog() {
    global $msg_entrylog7;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/entry-log-' . date('d-m-Y-His') . '.csv';
    $data      = $msg_entrylog7 . mc_defineNewline();
    $SQL       = '';
    $type      = (isset($_GET['export']) && in_array($_GET['export'],array('admin','personal','trade')) ? $_GET['export'] : 'all');
    if (isset($_GET['keys']) && $_GET['keys']) {
      if (strtolower($_GET['keys']) == strtolower(USERNAME)) {
        $SQL = 'WHERE `el`.`userid` = \'0\'';
      } else {
        $SQL = 'WHERE (`acc`.`name` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\') OR (`usr`.`userName` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\')';
      }
    }
    $q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `el`.*,
           `acc`.`name` AS `accName`,
           `usr`.`userName` AS `usrName`,
           DATE_FORMAT(DATE(`el`.`logdatetime`),'" . $this->settings->mysqlDateFormat . "') AS `ldate`,
           TIME(`el`.`logdatetime`) AS `ltime`,
           `el`.`ip` AS `logIP`,
           `el`.`type` AS `logType`
           FROM `" . DB_PREFIX . "entry_log` AS `el`
           LEFT JOIN `" . DB_PREFIX . "accounts` `acc` ON `acc`.`id` = `el`.`userid` AND `el`.`type` IN('personal','trade')
           LEFT JOIN `" . DB_PREFIX . "users` `usr` ON `usr`.`id` = `el`.`userid` AND `el`.`type` IN('admin')
           " . ($SQL == '' && $type != 'all' ? 'WHERE `el`.`type` = \'' . mc_safeSQL($type) . '\'' : ''). "
           $SQL
           ORDER BY `el`.`id` DESC
           ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($LOG = mysqli_fetch_object($q_l)) {
      switch($LOG->userid) {
        case '0':
          $nm = mc_safeHTML(USERNAME);
          break;
        default:
          switch($LOG->logType) {
            case 'admin':
              $nm = mc_safeHTML($LOG->usrName);
              break;
            default:
              $nm = mc_safeHTML($LOG->accName);
              break;
          }
          break;
      }
      $data .= mc_cleanCSV($nm, $separator) . $separator . mc_cleanCSV($LOG->ldate, $separator) . $separator . $LOG->ltime . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  // Clear log..
  public function clearEntryLog() {
    $SQL       = '';
    $rows      = 0;
    $type      = (isset($_GET['reset']) && in_array($_GET['reset'],array('admin','personal','trade')) ? $_GET['reset'] : 'all');
    $del       = array();
    if (isset($_GET['keys']) && $_GET['keys']) {
      if (strtolower($_GET['keys']) == strtolower(USERNAME)) {
        $SQL = 'WHERE `el`.`userid` = \'0\'';
      } else {
        $SQL = 'WHERE (`acc`.`name` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\') OR (`usr`.`userName` LIKE \'%' . mc_safeSQL($_GET['keys']) . '%\')';
      }
    }
    $q_l = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `el`.`id` AS `logID`
           FROM `" . DB_PREFIX . "entry_log` AS `el`
           LEFT JOIN `" . DB_PREFIX . "accounts` `acc` ON `acc`.`id` = `el`.`userid` AND `el`.`type` IN('personal','trade')
           LEFT JOIN `" . DB_PREFIX . "users` `usr` ON `usr`.`id` = `el`.`userid` AND `el`.`type` IN('admin')
           " . ($SQL == '' && $type != 'all' ? 'WHERE `el`.`type` = \'' . mc_safeSQL($type) . '\'' : ''). "
           $SQL
           ORDER BY `el`.`id` DESC
           ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($LOG = mysqli_fetch_object($q_l)) {
      $del[] = $LOG->logID;
    }
    if (!empty($del)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "entry_log` WHERE `id` IN(" . implode(',', $del) . ")");
    }
    mc_tableTruncationRoutine(array(
      'entry_log'
    ));
    return count($del);
  }

  // Add entry log..
  public function addEntryLog($type,$user = '') {
    $skip = mc_skipLogUsers();
    switch($type) {
      case 'global':
        if (!empty($skip) && in_array(strtolower('global_no'), $skip)) {
          return;
        }
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "entry_log` (
        `userid`,`logdatetime`,`ip`,`type`
        ) VALUES (
        '0','" . date("Y-m-d H:i:s") . "','" . mc_safeSQL(mc_getRealIPAddr()) . "','admin'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      default:
        if (!empty($skip) && in_array(strtolower($user->userEmail), $skip)) {
          return;
        }
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "entry_log` (
        `userid`,`logdatetime`,`ip`,`type`
        ) VALUES (
        '{$user->id}','" . date("Y-m-d H:i:s") . "','" . mc_safeSQL(mc_getRealIPAddr()) . "','admin'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
  }

  // Add banners..
  public function addBanners() {
    $_POST  = mc_safeImport($_POST);
    if (isset($_POST['trade']) && $_POST['trade'] == 'yes') {
      $folder = str_replace('{theme}', TRADE_THEME_FOLDER, BANNER_FOLDER);
    } else {
      $folder = str_replace('{theme}', THEME_FOLDER, BANNER_FOLDER);
    }
    $temp   = $_FILES['image']['tmp_name'];
    $name   = $_FILES['image']['name'];
    $size   = $_FILES['image']['size'];
    // Get last image..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "banners` ORDER BY `id` DESC LIMIT 1") or die(mc_MySQLError(__LINE__, __FILE__));
    $L = mysqli_fetch_object($q);
    if ($temp && $name && $size > 0) {
      if (is_uploaded_file($temp)) {
        if (RENAME_BANNERS) {
          $ext  = strrchr(strtolower($name), '.');
          $file = BANNER_PREFIX . (isset($L->id) ? ($L->id + 1) : '1') . $ext;
        } else {
          $file = $name;
        }
        // If banner with same name exists, remove it..
        if (@file_exists($this->settings->serverPath . '/' . $folder . '/' . $file)) {
          @unlink($this->settings->serverPath . '/' . $folder . '/' . $file);
        }
        move_uploaded_file($temp, $this->settings->serverPath . '/' . $folder . '/' . $file);
        @chmod($this->settings->serverPath . '/' . $folder . '/' . $file, AFTER_UPLOAD_CHMOD_VALUE);
        if (@file_exists($this->settings->serverPath . '/' . $folder . '/' . $file)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT IGNORE INTO `" . DB_PREFIX . "banners` (
          `bannerFile`,`bannerText`,`BannerUrl`,`bannerLive`,
          `bannerCats`,`bannerHome`,`bannerFrom`,`bannerTo`,`trade`
          ) VALUES (
          '{$file}',
          '{$_POST['text']}',
          '{$_POST['url']}',
          '" . (isset($_POST['bannerLive']) && in_array($_POST['bannerLive'], array(
              'yes',
              'no'
            )) ? $_POST['bannerLive'] : 'no') . "',
          '" . (!empty($_POST['bannerCats']) ? implode(',', $_POST['bannerCats']) : '') . "',
          '" . (isset($_POST['bannerHome']) && in_array($_POST['bannerHome'], array(
              'yes',
              'no'
            )) ? $_POST['bannerHome'] : 'no') . "',
          '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['bannerFrom'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['bannerFrom'], $this->settings) : '0000-00-00') . "',
          '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['bannerTo'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['bannerTo'], $this->settings) : '0000-00-00') . "',
          '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
              'yes',
              'no'
            )) ? $_POST['trade'] : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          if (file_exists($temp)) {
            @unlink($temp);
          }
        }
      }
    }
  }

  // Update banners..
  public function updateBanners() {
    $_POST  = mc_safeImport($_POST);
    if (isset($_POST['trade']) && $_POST['trade'] == 'yes') {
      $folder = str_replace('{theme}', TRADE_THEME_FOLDER, BANNER_FOLDER);
    } else {
      $folder = str_replace('{theme}', THEME_FOLDER, BANNER_FOLDER);
    }
    $temp   = $_FILES['image']['tmp_name'];
    $name   = $_FILES['image']['name'];
    $size   = $_FILES['image']['size'];
    $file   = '';
    // Get last image..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "banners` ORDER BY `id` DESC LIMIT 1") or die(mc_MySQLError(__LINE__, __FILE__));
    $L = mysqli_fetch_object($q);
    if ($temp && $name && $size > 0) {
      if (is_uploaded_file($temp)) {
        if (RENAME_BANNERS) {
          $ext  = strrchr(strtolower($name), '.');
          $file = BANNER_PREFIX . (isset($L->id) ? ($L->id + 1) : '1') . $ext;
        } else {
          $file = $name;
        }
        // If banner with same name exists, remove it..
        if (@file_exists($this->settings->serverPath . '/' . $folder . '/' . $file)) {
          @unlink($this->settings->serverPath . '/' . $folder . '/' . $file);
        }
        // If old file was different, remove it..
        if ($_POST['old_img'] && @file_exists($this->settings->serverPath . '/' . $folder . '/' . $_POST['old_img'])) {
          @unlink($this->settings->serverPath . '/' . $folder . '/' . $_POST['old_img']);
        }
        move_uploaded_file($temp, $this->settings->serverPath . '/' . $folder . '/' . $file);
        @chmod($this->settings->serverPath . '/' . $folder . '/' . $file, AFTER_UPLOAD_CHMOD_VALUE);
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "banners` SET
    `bannerFile` = '" . ($file ? $file : $_POST['old_img']) . "',
    `bannerText` = '{$_POST['text']}',
    `BannerUrl`  = '{$_POST['url']}',
    `bannerLive` = '" . (isset($_POST['bannerLive']) && in_array($_POST['bannerLive'], array(
        'yes',
        'no'
      )) ? $_POST['bannerLive'] : 'no') . "',
    `bannerCats` = '" . (!empty($_POST['bannerCats']) ? implode(',', $_POST['bannerCats']) : '') . "',
    `bannerHome` = '" . (isset($_POST['bannerHome']) && in_array($_POST['bannerHome'], array(
        'yes',
        'no'
      )) ? $_POST['bannerHome'] : 'no') . "',
    `bannerFrom` = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['bannerFrom'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['bannerFrom'], $this->settings) : '0000-00-00') . "',
    `bannerTo`   = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['bannerTo'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['bannerTo'], $this->settings) : '0000-00-00') . "',
    `trade`      = '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
        'yes',
        'no'
      )) ? $_POST['trade'] : 'no') . "'
    WHERE `id`   = '" . mc_digitSan($_POST['update_banners']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Delete banner..
  public function deleteBanner() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "banners` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    if (@file_exists($this->settings->serverPath . '/' . $_GET['file'])) {
      @unlink($this->settings->serverPath . '/' . $_GET['file']);
    }
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'banners'
    ));
    return $rows;
  }

  public function enableDisablePages($status) {
    // Clear link cache..
    $this->cache->clear_cache_file(array(
      'left-menu-links',
      'footer-links-left',
      'footer-links-middle',
      'sitemap-extra'
    ));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newpages` SET
    `enabled`   = '" . ($status == 'yes' ? 'no' : 'yes') . "'
    WHERE `id`  = '" . mc_digitSan($_GET['changeStatus']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return ($status == 'yes' ? 'no' : 'yes');
  }

  public function reOrderNewPages() {
    // Clear link cache..
    $this->cache->clear_cache_file(array(
      'left-menu-links',
      'footer-links-left',
      'footer-links-middle',
      'sitemap-extra'
    ));
    if (!empty($_GET['pg']) && is_array($_GET['pg'])) {
      foreach ($_GET['pg'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newpages` SET
        `orderBy`     = '" . ($k + 1) . "'
        WHERE `id`    = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addNewWebPage() {
    // Clear link cache..
    $this->cache->clear_cache_file(array(
      'left-menu-links',
      'footer-links-left',
      'footer-links-middle',
      'sitemap-extra'
    ));
    $_POST             = mc_safeImport($_POST);
    $_POST['pageText'] = mc_cleanBBInput($_POST['pageText']);
    if (!isset($_POST['customTemplate'])) {
      $_POST['customTemplate'] = '';
    }
    if (isset($_POST['linkExternal']) && $_POST['linkExternal'] == 'yes') {
      $_POST['landingPage'] = 'no';
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "newpages` (
    `pageName`,
    `pageKeys`,
    `pageDesc`,
    `pageText`,
    `orderBy`,
    `enabled`,
    `linkPos`,
    `linkExternal`,
    `customTemplate`,
    `linkTarget`,
    `landingPage`,
    `leftColumn`,
    `rwslug`,
    `trade`
    ) VALUES (
    '{$_POST['pageName']}',
    '{$_POST['pageKeys']}',
    '{$_POST['pageDesc']}',
    '{$_POST['pageText']}',
    '99999',
    '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "',
    '" . (!empty($_POST['linkPos']) ? implode(',', $_POST['linkPos']) : '1') . "',
    '" . (isset($_POST['linkExternal']) && in_array($_POST['linkExternal'], array(
        'yes',
        'no'
      )) ? $_POST['linkExternal'] : 'no') . "',
    '{$_POST['customTemplate']}',
    '" . (isset($_POST['linkTarget']) && in_array($_POST['linkTarget'], array(
        'new',
        'same'
      )) ? $_POST['linkTarget'] : 'new') . "',
    '" . (isset($_POST['landingPage']) && in_array($_POST['landingPage'], array(
        'yes',
        'no'
      )) ? $_POST['landingPage'] : 'no') . "',
    '" . (isset($_POST['leftColumn']) && in_array($_POST['leftColumn'], array(
        'yes',
        'no'
      )) ? $_POST['leftColumn'] : 'no') . "',
    '{$_POST['rwslug']}',
    '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
        'yes',
        'no'
      )) ? $_POST['trade'] : 'no') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Remove any other landing pages..
    if (isset($_POST['landingPage']) && $_POST['landingPage'] == 'yes') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newpages` SET
      `landingPage` = 'no'
      WHERE `id`   != '{$id}'
      AND `trade`   = '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
        'yes',
        'no'
      )) ? $_POST['trade'] : 'no') . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function updateWebPage() {
    // Clear link cache..
    $this->cache->clear_cache_file(array(
      'left-menu-links',
      'footer-links-left',
      'footer-links-middle',
      'sitemap-extra'
    ));
    $_POST             = mc_safeImport($_POST);
    $_POST['pageText'] = mc_cleanBBInput($_POST['pageText']);
    if (!isset($_POST['customTemplate'])) {
      $_POST['customTemplate'] = '';
    }
    if (isset($_POST['linkExternal']) && $_POST['linkExternal'] == 'yes') {
      $_POST['landingPage'] = 'no';
    }
    if ($_GET['edit'] == '1') {
      $_POST['leftColumn'] = 'yes';
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newpages` SET
    `pageName`        = '{$_POST['pageName']}',
    `pageKeys`        = '{$_POST['pageKeys']}',
    `pageDesc`        = '{$_POST['pageDesc']}',
    `pageText`        = '{$_POST['pageText']}',
    `enabled`         = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "',
    `linkPos`         = '" . (!empty($_POST['linkPos']) ? implode(',', $_POST['linkPos']) : '1') . "',
    `linkExternal`    = '" . (isset($_POST['linkExternal']) && in_array($_POST['linkExternal'], array(
        'yes',
        'no'
      )) ? $_POST['linkExternal'] : 'no') . "',
    `customTemplate`  = '{$_POST['customTemplate']}',
    `linkTarget`      = '" . (isset($_POST['linkTarget']) && in_array($_POST['linkTarget'], array(
        'new',
        'same'
      )) ? $_POST['linkTarget'] : 'new') . "',
    `landingPage`     = '" . (isset($_POST['landingPage']) && in_array($_POST['landingPage'], array(
        'yes',
        'no'
      )) ? $_POST['landingPage'] : 'no') . "',
    `leftColumn`      = '" . (isset($_POST['leftColumn']) && in_array($_POST['leftColumn'], array(
        'yes',
        'no'
      )) ? $_POST['leftColumn'] : 'no') . "',
    `rwslug`          = '{$_POST['rwslug']}',
    `trade`           = '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
        'yes',
        'no'
      )) ? $_POST['trade'] : 'no') . "'
    WHERE `id`        = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Remove any other landing pages..
    if (isset($_POST['landingPage']) && $_POST['landingPage'] == 'yes') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "newpages` SET
      `landingPage` = 'no'
      WHERE `id`   != '" . mc_digitSan($_GET['edit']) . "'
      AND `trade`   = '" . (isset($_POST['trade']) && in_array($_POST['trade'], array(
        'yes',
        'no'
      )) ? $_POST['trade'] : 'no') . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function deleteWebPage() {
    // Clear link cache..
    $this->cache->clear_cache_file(array(
      'left-menu-links',
      'footer-links-left',
      'footer-links-middle',
      'sitemap-extra'
    ));
    if ($_GET['del'] > 1) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "newpages`
      WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      return mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    }
    return 0;
  }

  public function addStatus() {
    $_POST = mc_safeImport($_POST);
    $hs    = (isset($_POST['homepage']) && in_array($_POST['homepage'], array(
      'yes',
      'no'
    )) ? $_POST['homepage'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "paystatuses` (
    `statname`,
    `pMethod`,
    `homepage`
    ) VALUES (
    '{$_POST['statname']}',
    '{$_POST['pMethod']}',
    '{$hs}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateStatus() {
    $_POST = mc_safeImport($_POST);
    $hs    = (isset($_POST['homepage']) && in_array($_POST['homepage'], array(
      'yes',
      'no'
    )) ? $_POST['homepage'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "paystatuses` SET
    `statname` = '{$_POST['statname']}',
    `pMethod`  = '{$_POST['pMethod']}',
    `homepage` = '{$hs}'
    WHERE `id` = '{$_POST['update']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteStatus() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "paystatuses`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'paystatuses'
    ));
    return $rows;
  }

  public function batchUpdateTaxRates() {
    $_POST = mc_safeImport($_POST);
    $count = 0;
    if (!empty($_POST['zones'])) {
      $perc = (substr($_POST['price'], -1) == '%' ? substr($_POST['price'], 0, -1) : $_POST['price']);
      $type = (isset($_POST['type']) ? $_POST['type'] : 'incr');
      switch($type) {
        case 'fixed':
          $rates = '`zRate` = \'' . $perc . '\'';
          break;
        case 'incr':
          $rates = '`zRate` = (`zRate`+' . $perc . ')';
          break;
        default:
          $rates = '`zRate` = (`zRate`-' . $perc . ')';
          break;
      }
      // Update tax rates..
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "zones` SET $rates WHERE `id` IN(" . implode(',', $_POST['zones']) . ")") or die(mc_MySQLError(__LINE__, __FILE__));
      $count = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
      // Update tax rates..
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "zone_areas` SET $rates WHERE `inZone` IN(" . implode(',', $_POST['zones']) . ")") or die(mc_MySQLError(__LINE__, __FILE__));
      $count = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
      // Fix minus values..
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "zones` SET `zRate` = '0' WHERE `zRate` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
      return $count;
    }
    return '0';
  }

  public function updatePage() {
    $_POST = mc_safeImport($_POST);
    $row   = $_POST['process'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
    $row = '{$_POST['text']}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function addDiscountCoupon() {
    $_POST = mc_safeImport($_POST);
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('campaigns') + 1 > RESTR_COUPONS) {
        mc_restrictionLimitRedirect();
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT IGNORE INTO `" . DB_PREFIX . "campaigns` (
    `cName`,
    `cDiscountCode`,
    `cMin`,
    `cUsage`,
    `cExpiry`,
    `cDiscount`,
    `cAdded`,
    `cLive`,
    `categories`
    ) VALUES (
    '{$_POST['cName']}',
    '{$_POST['cDiscountCode']}',
    '" . ($_POST['cMin'] > 0 ? mc_cleanInsertionPrice($_POST['cMin']) : '0') . "',
    '{$_POST['cUsage']}',
    '" . (mc_checkValidDate($_POST['cExpiry']) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['cExpiry'], $this->settings) : '0000-00-00') . "',
    '" . systemEngine::calculateDiscountType($_POST['cDiscount']) . "',
    '" . date("Y-m-d") . "',
    '" . (isset($_POST['cLive']) && in_array($_POST['cLive'], array(
        'yes',
        'no'
      )) ? $_POST['cLive'] : 'yes') . "',
    '" . (!empty($_POST['cat']) ? implode(',', $_POST['cat']) : '') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function calculateDiscountType($discount) {
    if (strpos(strtolower($discount), 'free') !== FALSE) {
      return 'freeshipping';
    }
    if (strpos(strtolower($discount), 'tax') !== FALSE) {
      return 'notax';
    }
    return $discount;
  }

  public function updateDiscountCoupon() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "campaigns` SET
    `cName`          = '{$_POST['cName']}',
    `cDiscountCode`  = '{$_POST['cDiscountCode']}',
    `cMin`           = '" . ($_POST['cMin'] > 0 ? mc_cleanInsertionPrice($_POST['cMin']) : '0') . "',
    `cUsage`         = '{$_POST['cUsage']}',
    `cExpiry`        = '" . (mc_checkValidDate($_POST['cExpiry']) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['cExpiry'], $this->settings) : '0000-00-00') . "',
    `cDiscount`      = '" . systemEngine::calculateDiscountType($_POST['cDiscount']) . "',
    `cLive`          = '" . (isset($_POST['cLive']) && in_array($_POST['cLive'], array(
        'yes',
        'no'
      )) ? $_POST['cLive'] : 'yes') . "',
    `categories`     = '" . (!empty($_POST['cat']) ? implode(',', $_POST['cat']) : '') . "'
    WHERE `id`       = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteDiscountCoupon() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "campaigns`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "coupons`
    WHERE `cCampaign` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'campaigns',
      'coupons'
    ));
    return $rows;
  }

  public function updateCurrencyConverter() {
    $keys = array_keys($_POST['cur']);
    for ($i = 0; $i < count($keys); $i++) {
      $switch = (isset($_POST['iso'][$keys[$i]]) ? 'yes' : 'no');
      switch($switch) {
        // Enabled currency..
        case 'yes':
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "currencies` SET
          `enableCur`           = 'yes',
          `rate`                = '" . (isset($_POST['rate'][$keys[$i]]) ? mc_safeSQL($_POST['rate'][$keys[$i]]) : '') . "',
          `currencyDisplayPref` = '" . (isset($_POST['pref'][$keys[$i]]) ? mc_safeSQL(str_replace('&amp;','&',$_POST['pref'][$keys[$i]])) : '') . "'
          WHERE `currency`      = '{$keys[$i]}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          break;
        // Disabled currency..
        case 'no':
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "currencies` SET
          `enableCur`           = 'no',
	        `rate`                = '0',
          `currencyDisplayPref` = ''
          WHERE `currency`      = '{$keys[$i]}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          break;
      }
    }
  }

  public function addNewCountries() {
    $c     = 0;
    if ($_POST['cName'] != '' && $_POST['cISO'] != '' && $_POST['cISO_2'] != '' && $_POST['iso4217'] != '') {
      mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "countries` (
      `cName`,
      `cISO`,
      `cISO_2`,
      `iso4217`,
      `enCountry`
      ) VALUES (
      '". mc_safeSQL($_POST['cName']) . "',
      '". mc_safeSQL($_POST['cISO']) . "',
      '". mc_safeSQL($_POST['cISO_2']) . "',
      '". mc_safeSQL($_POST['iso4217']) . "',
      '" . (in_array($_POST['enCountry'], array(
        'yes',
        'no'
        )) ? $_POST['enCountry'] : 'no') . "'
      )") or die(mc_MySQLError(__LINE__, __FILE__));
      ++$c;
    }
    return $c;
  }

  public function updateCountry() {
    if ($_POST['cName'] != '' && $_POST['cISO'] != '' && $_POST['cISO_2'] != '' && $_POST['iso4217'] != '') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
      `cName`      = '". mc_safeSQL($_POST['cName']) . "',
      `cISO`       = '". mc_safeSQL($_POST['cISO']) . "',
      `cISO_2`     = '". mc_safeSQL($_POST['cISO_2']) . "',
      `iso4217`    = '". mc_safeSQL($_POST['iso4217']) . "',
      `enCountry`  = '" . (in_array($_POST['enCountry'], array(
        'yes',
        'no'
        )) ? $_POST['enCountry'] : 'no') . "'
      WHERE `id`   = '" . mc_digitSan($_GET['edit']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function updateCountries() {
    if (empty($_POST['countries'])) {
      $_POST['countries'] = array(0);
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
    `enCountry`  = 'yes'
    WHERE `id` IN(" . implode(',', $_POST['countries']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
    `enCountry` = 'no'
    WHERE `id` NOT IN(" . implode(',', $_POST['countries']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteCountry() {
    $ID = (int) $_GET['delete'];
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "countries` WHERE `id` = '{$ID}'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'countries'
    ));
    return $rows;
  }

  public function resetStoreLogo() {
    if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_GET['removeLogo'])) {
      @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_GET['removeLogo']);
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET `logoName` = ''");
  }

  public function uploadWebLogo($name, $temp) {
    $logo = '';
    if (@is_dir($this->settings->serverPath . '/' . PRODUCTS_FOLDER) && @is_writeable($this->settings->serverPath . '/' . PRODUCTS_FOLDER)) {
      if (is_uploaded_file($temp)) {
        $ext  = strrchr($name, '.');
        $file = 'logo-' . date('Ymdhis') . strtolower($ext);
        move_uploaded_file($temp, $this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $file);
        if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $file)) {
          // Some servers require permission changes..
          @chmod($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $file, AFTER_UPLOAD_CHMOD_VALUE);
          return $file;
        }
      }
    }
    return $logo;
  }

  public function updateSettings() {
    $_POST = mc_safeImport($_POST);
    $area  = (isset($_GET['s']) ? $_GET['s'] : '1');
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    switch($area) {
      case '1':
        // Clear link cache..
        if (isset($_POST['en_modr']) && $_POST['en_modr'] != $this->settings->en_modr) {
          $this->cache->clear_cache();
        } else {
          $this->cache->clear_cache_file(array(
            'left-menu-links',
            'footer-links-left',
            'footer-links-middle',
            'sitemap-extra'
          ));
        }
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
        `website`               = '{$_POST['website']}',
        `theme`                 = '{$_POST['theme']}',
        `theme2`                = '{$_POST['theme2']}',
        `tradetheme`            = '{$_POST['tradetheme']}',
        `email`                 = '{$_POST['email']}',
        `addEmails`             = '{$_POST['addEmails']}',
        `serverPath`            = '" . systemEngine::filterInstallationPaths($_POST['serverPath']) . "',
        `languagePref`          = '{$_POST['languagePref']}',
        `ifolder`               = '" . systemEngine::filterInstallationPaths($_POST['ifolder']) . "',
        `metaKeys`              = '{$_POST['metaKeys']}',
        `metaDesc`              = '{$_POST['metaDesc']}',
        `en_rss`                = '" . (isset($_POST['en_rss']) && in_array($_POST['en_rss'], array(
              'yes',
              'no'
            )) ? $_POST['en_rss'] : 'yes') . "',
        `rssScroller`           = '" . (isset($_POST['rssScroller']) && in_array($_POST['rssScroller'], array(
              'yes',
              'no'
            )) ? $_POST['rssScroller'] : 'yes') . "',
        `rssScrollerUrl`        = '{$_POST['rssScrollerUrl']}',
        `rssScrollerLimit`      = '" . mc_digitSan($_POST['rssScrollerLimit']) . "',
        `en_modr`               = '" . (isset($_POST['en_modr']) && in_array($_POST['en_modr'], array(
              'yes',
              'no'
            )) ? $_POST['en_modr'] : 'no') . "',
        `activateEmails`        = '" . (isset($_POST['activateEmails']) && in_array($_POST['activateEmails'], array(
              'yes',
              'no'
            )) ? $_POST['activateEmails'] : 'no') . "',
        `enableBBCode`          = '" . (isset($_POST['enableBBCode']) && in_array($_POST['enableBBCode'], array(
              'yes',
              'no'
            )) ? $_POST['enableBBCode'] : 'no') . "',
        `productsPerPage`       = '" . mc_digitSan($_POST['productsPerPage']) . "',
        `systemDateFormat`      = '{$_POST['systemDateFormat']}',
        `mysqlDateFormat`       = '{$_POST['mysqlDateFormat']}',
        `jsDateFormat`          = '{$_POST['jsDateFormat']}',
        `jsWeekStart`           = '" . mc_digitSan($_POST['jsWeekStart']) . "',
        `timezone`              = '{$_POST['timezone']}',
        `rssFeedLimit`          = '" . mc_digitSan($_POST['rssFeedLimit']) . "',
        `saleComparisonItems`   = '" . mc_digitSan($_POST['saleComparisonItems']) . "',
        `searchLowStockLimit`   = '" . mc_digitSan($_POST['searchLowStockLimit']) . "',
        `mostPopProducts`       = '" . mc_digitSan($_POST['mostPopProducts']) . "',
        `mostPopPref`           = '" . (isset($_POST['mostPopPref']) && in_array($_POST['mostPopPref'], array(
              'hits',
              'sales'
            )) ? $_POST['mostPopPref'] : 'sales') . "',
        `latestProdLimit`       = '" . mc_digitSan($_POST['latestProdLimit']) . "',
        `latestProdDuration`    = '" . (isset($_POST['latestProdDuration']) && in_array($_POST['latestProdDuration'], array(
              'days',
              'months',
              'years'
            )) ? $_POST['latestProdDuration'] : 'days') . "',
        `enableZip`             = '" . (isset($_POST['enableZip']) && in_array($_POST['enableZip'], array(
              'yes',
              'no'
            )) ? $_POST['enableZip'] : 'no') . "',
        `minInvoiceDigits`      = '" . mc_digitSan($_POST['minInvoiceDigits']) . "',
        `invoiceNo`             = '" . mc_digitSan($_POST['invoiceNo']) . "',
        `zipCreationLimit`      = '" . ($_POST['zipCreationLimit'] ? mc_digitSan($_POST['zipCreationLimit']) : '0') . "',
        `zipLimit`              = '" . ($_POST['zipLimit'] ? mc_digitSan($_POST['zipLimit']) : '0') . "',
        `zipTimeOut`            = '" . ($_POST['zipTimeOut'] ? mc_digitSan($_POST['zipTimeOut']) : '0') . "',
        `zipMemoryLimit`        = '" . ($_POST['zipMemoryLimit'] ? mc_digitSan($_POST['zipMemoryLimit']) : '0') . "',
        `zipAdditionalFolder`   = '" . ($_POST['zipAdditionalFolder'] ? $_POST['zipAdditionalFolder'] : 'additional-zip') . "',
        `enEntryLog`            = '" . (isset($_POST['enEntryLog']) && in_array($_POST['enEntryLog'], array(
              'yes',
              'no'
            )) ? $_POST['enEntryLog'] : 'no') . "',
        `enSearchLog`           = '" . (isset($_POST['enSearchLog']) && in_array($_POST['enSearchLog'], array(
              'yes',
              'no'
            )) ? $_POST['enSearchLog'] : 'no') . "',
        `smartQuotes`           = '" . (isset($_POST['smartQuotes']) && in_array($_POST['smartQuotes'], array(
              'yes',
              'no'
            )) ? $_POST['smartQuotes'] : 'no') . "',
        `hitCounter`            = '" . (isset($_POST['hitCounter']) && in_array($_POST['hitCounter'], array(
              'yes',
              'no'
            )) ? $_POST['hitCounter'] : 'no') . "',
        `adminFolderName`       = '{$_POST['adminFolderName']}',
        `twitterLatest`         = '" . (isset($_POST['twitterLatest']) && in_array($_POST['twitterLatest'], array(
              'yes',
              'no'
            )) ? $_POST['twitterLatest'] : 'no') . "',
        `enableRecentView`      = '" . (isset($_POST['enableRecentView']) && in_array($_POST['enableRecentView'], array(
              'yes',
              'no'
            )) ? $_POST['enableRecentView'] : 'yes') . "',
        `savedSearches`         = '" . mc_digitSan($_POST['savedSearches']) . "',
        `searchSlider`          = '" . serialize($_POST['searchSlider']) . "',
        `searchTagsOnly`        = '" . (isset($_POST['searchTagsOnly']) && in_array($_POST['searchTagsOnly'], array(
              'yes',
              'no'
            )) ? $_POST['searchTagsOnly'] : 'no') . "',
        `thumbWidth`            = '" . mc_digitSan($_POST['thumbWidth']) . "',
        `thumbHeight`           = '" . mc_digitSan($_POST['thumbHeight']) . "',
        `thumbQuality`          = '" . mc_digitSan($_POST['thumbQuality']) . "',
        `thumbQualityPNG`       = '" . mc_digitSan($_POST['thumbQualityPNG']) . "',
        `aspectRatio`           = '" . (isset($_POST['aspectRatio']) && in_array($_POST['aspectRatio'], array(
              'yes',
              'no'
            )) ? $_POST['aspectRatio'] : 'yes') . "',
        `renamePics`            = '" . (isset($_POST['renamePics']) && in_array($_POST['renamePics'], array(
              'yes',
              'no'
            )) ? $_POST['renamePics'] : 'yes') . "',
        `tmbPrefix`             = '{$_POST['tmbPrefix']}',
        `imgPrefix`             = '{$_POST['imgPrefix']}',
        `maxProductChars`       = '" . mc_digitSan($_POST['maxProductChars']) . "',
        `parentCatHomeDisplay`  = '" . (isset($_POST['parentCatHomeDisplay']) && in_array($_POST['parentCatHomeDisplay'], array(
              'yes',
              'no'
            )) ? $_POST['parentCatHomeDisplay'] : 'no') . "',
        `isbnAPI`               = '{$_POST['isbnAPI']}',
        `freeTextDisplay`       = '{$_POST['freeTextDisplay']}',
        `excludeFreePop`        = '" . (isset($_POST['excludeFreePop']) && in_array($_POST['excludeFreePop'], array(
              'yes',
              'no'
            )) ? $_POST['excludeFreePop'] : 'no') . "',
        `priceTextDisplay`      = '{$_POST['priceTextDisplay']}',
        `en_sitemap`            = '" . (isset($_POST['en_sitemap']) && in_array($_POST['en_sitemap'], array(
              'yes',
              'no'
            )) ? $_POST['en_sitemap'] : 'yes') . "',
        `cubeUrl`               = '{$_POST['cubeUrl']}',
        `cubeAPI`               = '{$_POST['cubeAPI']}',
        `guardianUrl`           = '{$_POST['guardianUrl']}',
        `guardianAPI`           = '{$_POST['guardianAPI']}',
        `menuCatCount`          = '" . (isset($_POST['menuCatCount']) && in_array($_POST['menuCatCount'], array(
              'yes',
              'no'
            )) ? $_POST['menuCatCount'] : 'no') . "',
        `menuBrandCount`        = '" . (isset($_POST['menuBrandCount']) && in_array($_POST['menuBrandCount'], array(
              'yes',
              'no'
            )) ? $_POST['menuBrandCount'] : 'no') . "',
        `catGiftPos`            = '{$_POST['catGiftPos']}',
        `showBrands`            = '" . (isset($_POST['showBrands']) && in_array($_POST['showBrands'], array(
              'yes',
              'no'
            )) ? $_POST['showBrands'] : 'no') . "',
        `minPassValue`          = '" . mc_digitSan($_POST['minPassValue']) . "',
        `tweetlimit`            = '" . mc_digitSan($_POST['tweetlimit']) . "',
        `en_wish`               = '" . (isset($_POST['en_wish']) && in_array($_POST['en_wish'], array(
              'yes',
              'no'
            )) ? $_POST['en_wish'] : 'yes') . "',
        `forcePass`             = '" . (isset($_POST['forcePass']) && in_array($_POST['forcePass'], array(
              'yes',
              'no'
            )) ? $_POST['forcePass'] : 'yes') . "',
        `en_create`             = '" . (isset($_POST['en_create']) && in_array($_POST['en_create'], array(
              'yes',
              'no'
            )) ? $_POST['en_create'] : 'yes') . "',
        `en_create_mail`        = '" . (isset($_POST['en_create_mail']) && in_array($_POST['en_create_mail'], array(
              'yes',
              'no'
            )) ? $_POST['en_create_mail'] : 'yes') . "',
        `en_close`              = '" . (isset($_POST['en_close']) && in_array($_POST['en_close'], array(
              'yes',
              'no'
            )) ? $_POST['en_close'] : 'yes') . "',
        `cache`                 = '" . (isset($_POST['cache']) && in_array($_POST['cache'], array(
              'yes',
              'no'
            )) ? $_POST['cache'] : 'yes') . "',
        `cachetime`             = '" . mc_digitSan($_POST['cachetime']) . "',
        `layout`                = '" . (isset($_POST['layout']) && in_array($_POST['layout'], array(
              'list',
              'grid'
            )) ? $_POST['layout'] : 'list') . "',
        `tradeship`             = '" . (isset($_POST['tradeship']) && in_array($_POST['tradeship'], array(
              'yes',
              'no'
            )) ? $_POST['tradeship'] : 'no') . "',
        `salereorder`           = '" . (isset($_POST['salereorder']) && in_array($_POST['salereorder'], array(
              'yes',
              'no'
            )) ? $_POST['salereorder'] : 'yes') . "',
        `hurrystock`           = '" . mc_digitSan($_POST['hurrystock']) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        // API fields..
        if (!empty($_POST['api'])) {
          foreach (array_keys($_POST['api']) AS $k) {
            foreach ($_POST['api'][$k] AS $apiK => $apiV) {
              $QP     = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`
                        FROM `" . DB_PREFIX . "social`
                        WHERE `desc` = '{$k}'
                        AND `param` = '{$apiK}'
                        LIMIT 1
                        ");
              $PAR = mysqli_fetch_object($QP);
              if (isset($PAR->id)) {
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "social` SET
                `value`    = '" . mc_safeSQL($apiV) . "'
                WHERE `id` = '{$PAR->id}'
                ");
              } else {
                mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "social` (
                `desc`,
                `param`,
                `value`
                ) VALUES (
                '" . mc_safeSQL($k) . "',
                '" . mc_safeSQL($apiK) . "',
                '" . mc_safeSQL($apiV) . "'
                )");
              }
            }
          }
        }
        // Affiliate
        if (!empty($_POST['cfg'])) {
          $cfg_keys = array_keys($_POST['cfg']);
          foreach ($cfg_keys AS $kys) {
            if (!empty($_POST['cfg'][$kys])) {
              foreach ($_POST['cfg'][$kys] AS $k => $v) {
                // Filtering of certain values
                switch($k) {
                  case 'product':
                    $v = (int) $v;
                    break;
                  case 'commission':
                    $v = (substr($v, -1) == '%' ? substr($v, 0, -1) : $v);
                    break;
                }
                $Q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "cfg`
                     WHERE `op1` = '" . mc_safeSQL($kys) . "'
                     AND `op2` = '" . mc_safeSQL($k) . "'
                     ");
                $P = mysqli_fetch_object($Q);
                if (isset($P->id)) {
                  mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "cfg` SET
                  `op6` = '" . mc_safeSQL($v) . "'
                  WHERE `op1` = '" . mc_safeSQL($kys) . "'
                  AND `op2` = '" . mc_safeSQL($k) . "'
                  ");
                } else {
                  mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "cfg` (
                  `op1`,
                  `op2`,
                  `op6`
                  ) VALUES (
                  '" . mc_safeSQL($kys) . "',
                  '" . mc_safeSQL($k) . "',
                  '" . mc_safeSQL($v) . "'
                  )");
                }
              }
            }
          }
        }
        // Reset..
        if (isset($_POST['forcePassReset'])) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "accounts` SET
          `pass` = sha1(concat(`id`,curtime(),`email`,rand(5)))
          WHERE `system1` = ''
          ");
        }
        break;
      case '2':
        break;
      case '3':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "settings` SET
        `baseCurrency`             = '" . substr($_POST['baseCurrency'], 0, 3) . "',
        `currencyDisplayPref`      = '" . str_replace('&amp;','&',$_POST['currencyDisplayPref']) . "',
        `gatewayMode`              = '" . (isset($_POST['gatewayMode']) && in_array($_POST['gatewayMode'], array(
              'live',
              'test'
            )) ? $_POST['gatewayMode'] : 'test') . "',
        `logErrors`                = '" . (isset($_POST['logErrors']) && in_array($_POST['logErrors'], array(
              'yes',
              'no'
            )) ? $_POST['logErrors'] : 'yes') . "',
        `logFolderName`            = '{$_POST['logFolderName']}',
        `enablePickUp`             = '" . (isset($_POST['enablePickUp']) && in_array($_POST['enablePickUp'], array(
              'yes',
              'no'
            )) ? $_POST['enablePickUp'] : 'no') . "',
        `shipCountry`              = '" . mc_digitSan($_POST['shipCountry']) . "',
        `enableSSL`                = '" . (isset($_POST['enableSSL']) && in_array($_POST['enableSSL'], array(
              'yes',
              'no'
            )) ? $_POST['enableSSL'] : 'no') . "',
        `pendingAsComplete`        = '" . (isset($_POST['pendingAsComplete']) && in_array($_POST['pendingAsComplete'], array(
              'yes',
              'no'
            )) ? $_POST['pendingAsComplete'] : 'no') . "',
        `freeShipThreshold`        = '" . mc_cleanInsertionPrice($_POST['freeShipThreshold']) . "',
        `downloadFolder`           = '{$_POST['downloadFolder']}',
        `downloadRestrictIP`       = '" . (isset($_POST['downloadRestrictIP']) && in_array($_POST['downloadRestrictIP'], array(
              'yes',
              'no'
            )) ? $_POST['downloadRestrictIP'] : 'no') . "',
        `downloadRestrictIPLog`    = '" . (isset($_POST['downloadRestrictIPLog']) && in_array($_POST['downloadRestrictIPLog'], array(
              'yes',
              'no'
            )) ? $_POST['downloadRestrictIPLog'] : 'no') . "',
        `downloadRestrictIPLock`   = '" . mc_digitSan($_POST['downloadRestrictIPLock']) . "',
        `downloadRestrictIPMail`   = '" . (isset($_POST['downloadRestrictIPMail']) ? 'yes' : 'no') . "',
        `downloadRestrictIPGlobal` = '{$_POST['downloadRestrictIPGlobal']}',
        `globalDownloadPath`       = '" . systemEngine::filterInstallationPaths($_POST['globalDownloadPath']) . "',
        `globalDiscount`           = '" . mc_digitSan($_POST['globalDiscount']) . "',
        `globalDiscountExpiry`     = '" . (mc_checkValidDate($_POST['globalDiscountExpiry']) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['globalDiscountExpiry'], $this->settings) : '0000-00-00') . "',
        `freeDownloadRestriction`  = '" . mc_digitSan($_POST['freeDownloadRestriction']) . "',
        `enableCheckout`           = '" . (isset($_POST['enableCheckout']) && in_array($_POST['enableCheckout'], array(
              'yes',
              'no'
            )) ? $_POST['enableCheckout'] : 'yes') . "',
        `showOutofStock`           = '" . (isset($_POST['showOutofStock']) && in_array($_POST['showOutofStock'], array(
              'cat',
              'yes',
              'no'
            )) ? $_POST['showOutofStock'] : 'yes') . "',
        `reduceDownloadStock`      = '" . (isset($_POST['reduceDownloadStock']) && in_array($_POST['reduceDownloadStock'], array(
              'yes',
              'no'
            )) ? $_POST['reduceDownloadStock'] : 'no') . "',
        `offerInsurance`           = '" . (isset($_POST['offerInsurance']) && in_array($_POST['offerInsurance'], array(
              'yes',
              'no'
            )) ? $_POST['offerInsurance'] : 'yes') . "',
        `insuranceAmount`          = '" . (substr($_POST['insuranceAmount'], -1) == '%' ? substr($_POST['insuranceAmount'], 0, -1) : $_POST['insuranceAmount']) . "',
        `insuranceFilter`          = '{$_POST['insuranceFilter']}',
        `insuranceOptional`        = '" . (isset($_POST['insuranceOptional']) && in_array($_POST['insuranceOptional'], array(
              'yes',
              'no'
            )) ? $_POST['insuranceOptional'] : 'no') . "',
        `insuranceValue`           = '{$_POST['insuranceValue']}',
        `insuranceInfo`            = '{$_POST['insuranceInfo']}',
        `minCheckoutAmount`        = '{$_POST['minCheckoutAmount']}',
        `productStockThreshold`    = '" . mc_digitSan($_POST['productStockThreshold']) . "',
        `showAttrStockLevel`       = '" . (isset($_POST['showAttrStockLevel']) && in_array($_POST['showAttrStockLevel'], array(
              'yes',
              'no'
            )) ? $_POST['showAttrStockLevel'] : 'no') . "',
        `autoClear`                = '" . mc_digitSan($_POST['autoClear']) . "',
        `freeAltRedirect`          = '{$_POST['freeAltRedirect']}',
        `pdf`                      = '" . (isset($_POST['pdf']['en']) && in_array($_POST['pdf']['en'], array(
              'yes',
              'no'
            )) ? $_POST['pdf']['en'] : 'yes') . "',
        `presalenotify`            = '" . (isset($_POST['presalenotify']) && in_array($_POST['presalenotify'], array(
              'yes',
              'no'
            )) ? $_POST['presalenotify'] : 'no') . "',
        `presaleemail`             = '{$_POST['presaleemail']}',
        `coupontax`                = '" . (isset($_POST['coupontax']) && in_array($_POST['coupontax'], array(
              'yes',
              'no'
            )) ? $_POST['coupontax'] : 'yes') . "',
        `tc`                       = '" . (isset($_POST['tc']) && in_array($_POST['tc'], array(
              'yes',
              'no'
            )) ? $_POST['tc'] : 'no') . "',
        `tctext`                   = '{$_POST['tctext']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        // Clear special offers if global discount is set..
        if ($_POST['globalDiscount'] > 0) {
          if (isset($_POST['clearSpecialOffers']) && $_POST['clearSpecialOffers'] == 'yes') {
            mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
            `pOffer`        = '',
            `pOfferExpiry`  = ''
            ") or die(mc_MySQLError(__LINE__, __FILE__));
          }
        }
        // Update local countries..
        if (!empty($_POST['pickup'])) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `localPickup` = 'yes'
          WHERE `id` IN(" . mc_safeSQL(implode(',', $_POST['pickup'])) . ")
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `localPickup` = 'no'
          WHERE `id` NOT IN(" . mc_safeSQL(implode(',', $_POST['pickup'])) . ")
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `localPickup` = 'no'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        // Update free shipping threshold countries..
        if (!empty($_POST['freeship'])) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `freeship` = 'yes'
          WHERE `id` IN(" . mc_safeSQL(implode(',', $_POST['freeship'])) . ")
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `freeship` = 'no'
          WHERE `id` NOT IN(" . mc_safeSQL(implode(',', $_POST['freeship'])) . ")
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "countries` SET
          `freeship` = 'no'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        // Update PDF info..
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "pdf` SET
        `company` = '{$_POST['pdf']['company']}',
        `address` = '{$_POST['pdf']['address']}',
        `font` = '" . (isset($_POST['pdf']['font']) && in_array($_POST['pdf']['font'], array(
          'helvetica',
          'dejavusans'
        )) ? $_POST['pdf']['font'] : 'helvetica') . "',
        `dir` = '" . (isset($_POST['pdf']['dir']) && in_array($_POST['pdf']['dir'], array(
          'ltr',
          'rtl'
        )) ? $_POST['pdf']['dir'] : 'ltr') . "',
        `meta` = '{$_POST['pdf']['meta']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case '4':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
        `homeProdValue`  = '" . mc_digitSan($_POST['homeProdValue']) . "',
        `homeProdType`   = '" . (isset($_POST['homeProdType']) && in_array($_POST['homeProdType'], array(
              'random',
              'latest'
            )) ? $_POST['homeProdType'] : 'latest') . "',
        `homeProdCats`   = '" . (!empty($_POST['homeProdCats']) ? mc_safeSQL(implode(',', $_POST['homeProdCats'])) : '') . "',
        `homeProdIDs`    = '" . (substr($_POST['homeProdIDs'], -1) == ',' ? substr($_POST['homeProdIDs'], 0, -1) : $_POST['homeProdIDs']) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case '5':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
        `cName`           = '{$_POST['cName']}',
        `cWebsite`        = '{$_POST['cWebsite']}',
        `cTel`            = '{$_POST['cTel']}',
        `cFax`            = '{$_POST['cFax']}',
        `cAddress`        = '{$_POST['cAddress']}',
        `cOther`          = '{$_POST['cOther']}',
        `cReturns`        = '{$_POST['cReturns']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case '6':
        $_POST['smtp_security'] = (in_array($_POST['smtp_security'], array('','tls','ssl')) ? $_POST['smtp_security'] : '');
        $_POST['smtp_debug']    = (in_array($_POST['smtp_debug'], array('yes','no')) ? $_POST['smtp_debug'] : 'no');
        $_POST['mailpref']      = (in_array($_POST['mailpref'], array('smtp','php')) ? $_POST['mailpref'] : 'smtp');
        $_POST['smtp_insec']    = (in_array($_POST['smtp_insec'], array('yes','no')) ? $_POST['smtp_insec'] : 'no');
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
        `smtp`           = 'yes',
        `smtp_host`      = '{$_POST['smtp_host']}',
        `smtp_user`      = '{$_POST['smtp_user']}',
        `smtp_pass`      = '{$_POST['smtp_pass']}',
        `smtp_port`      = '{$_POST['smtp_port']}',
        `smtp_security`  = '{$_POST['smtp_security']}',
        `smtp_from`      = '{$_POST['smtp_from']}',
        `smtp_email`     = '{$_POST['smtp_email']}',
        `smtp_debug`     = '{$_POST['smtp_debug']}',
        `mailpref`       = '{$_POST['mailpref']}',
        `smtp_insec`     = '{$_POST['smtp_insec']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        // API fields..
        if (!empty($_POST['api'])) {
          foreach (array_keys($_POST['api']) AS $k) {
            foreach ($_POST['api'][$k] AS $apiK => $apiV) {
              $QP     = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`
                        FROM `" . DB_PREFIX . "social`
                        WHERE `desc` = '{$k}'
                        AND `param` = '{$apiK}'
                        LIMIT 1
                        ");
              $PAR = mysqli_fetch_object($QP);
              if (isset($PAR->id)) {
                mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "social` SET
                `value`    = '" . mc_safeSQL($apiV) . "'
                WHERE `id` = '{$PAR->id}'
                ");
              } else {
                mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "social` (
                `desc`,
                `param`,
                `value`
                ) VALUES (
                '" . mc_safeSQL($k) . "',
                '" . mc_safeSQL($apiK) . "',
                '" . mc_safeSQL($apiV) . "'
                )");
              }
            }
          }
        }
        break;
      case '7':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
        `adminFooter`    = '{$_POST['adminFooter']}',
        `publicFooter`   = '{$_POST['publicFooter']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case '8':
        $_POST['offlineText'] = mc_cleanBBInput($_POST['offlineText']);
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "settings` SET
        `enableCart`   = '" . (isset($_POST['enableCart']) && in_array($_POST['enableCart'], array(
              'yes',
              'no'
            )) ? $_POST['enableCart'] : 'yes') . "',
        `offlineDate`  = '" . (mc_checkValidDate($_POST['offlineDate']) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['offlineDate'], $this->settings) : '0000-00-00') . "',
        `offlineText`  = '{$_POST['offlineText']}',
        `offlineIP`    = '{$_POST['offlineIP']}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case '9':
        break;
    }
  }

  public function filterInstallationPaths($path) {
    if (substr($path, -1) == '/') {
      $path = substr_replace($path, '', -1);
    }
    if (substr($path, -1) == '\\') {
      $path = substr_replace($path, '', -2);
    }
    return $path;
  }

  // Check for new version..
  public function mswSoftwareVersionCheck() {
    $url = 'https://www.maianscriptworld.co.uk/version-check.php?id=' . SCRIPT_ID;
    $str = '';
    if (function_exists('curl_init')) {
      $ch = @curl_init();
      @curl_setopt($ch, CURLOPT_URL, $url);
      @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $result = @curl_exec($ch);
      @curl_close($ch);
      if ($result) {
        if ($result != $this->settings->softwareVersion) {
          $str = 'Installed Version: ' . $this->settings->softwareVersion . mc_defineNewline();
          $str .= 'Current Version: ' . $result . mc_defineNewline() . mc_defineNewline();
          $str .= '<i class="fa fa-times fa-fw"></i> Your version is out of date.' . mc_defineNewline() . mc_defineNewline();
          $str .= 'Download new version at:' . mc_defineNewline();
          $str .= '<a href="https://www.' . SCRIPT_URL . '/download.html" onclick="window.open(this);return false">www.' . SCRIPT_URL . '</a>';
        } else {
          $str = 'Current Version: ' . $this->settings->softwareVersion . mc_defineNewline() . mc_defineNewline() . '<i class="fa fa-check fa-fw"></i> You are currently using the latest version';
        }
      }
    } else {
      if (@ini_get('allow_url_fopen') == '1') {
        $result = @file_get_contents($url);
        if ($result) {
          if ($result != $this->settings->softwareVersion) {
            $str = 'Installed Version: ' . $this->settings->softwareVersion . mc_defineNewline();
            $str .= 'Current Version: ' . $result . mc_defineNewline() . mc_defineNewline();
            $str .= '<i class="fa fa-times fa-fw"></i> Your version is out of date.' . mc_defineNewline() . mc_defineNewline();
            $str .= 'Download new version at:' . mc_defineNewline();
            $str .= '<a href="https://www.' . SCRIPT_URL . '/download.html" onclick="window.open(this);return false">www.' . SCRIPT_URL . '</a>';
          } else {
            $str = 'Current Version: ' . $this->settings->softwareVersion . mc_defineNewline() . mc_defineNewline() . '<i class="fa fa-check fa-fw"></i> You are currently using the latest version';
          }
        }
      }
    }
    // Nothing?
    if ($str == '') {
      $str = 'Server check functions not available.' . mc_defineNewline() . mc_defineNewline();
      $str .= 'Please visit <a href="https://www.' . SCRIPT_URL . '/download.html" onclick="window.open(this);return false">www.' . SCRIPT_URL . '</a> to check for updates';
    }
    return $str;
  }

}

?>