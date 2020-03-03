<?php

class gift {

  public $settings;
  public $dl;

  public function reOrderGiftCerts() {
    if (!empty($_GET['gcode']) && is_array($_GET['gcode'])) {
      foreach ($_GET['gcode'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcerts` SET
        `orderBy`  = '" . ($k + 1) . "'
        WHERE `id` = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function exportGiftOverviewToCSV() {
    global $msg_giftoverview12;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $separator = ',';
    $csvFile   = PATH . 'import/gift-certificates-' . date('d-m-Y-His') . '.csv';
    $data      = $msg_giftoverview12 . mc_defineNewline();
    $SQL       = 'WHERE `active` = \'yes\'';
    switch($_GET['export']) {
      case 'redeemed':
        $SQL = "WHERE `value` != `redeemed` AND `active` = 'yes'";
        break;
      case 'disabled':
        $SQL = "WHERE `enabled` = 'no' AND `active` = 'yes'";
        break;
    }
    if (isset($_GET['keys'])) {
      $sKeys  = '%' . mc_safeSQL($_GET['keys']) . '%';
      $sKeysD = mc_safeSQL($_GET['keys']);
      $SQL .= ($SQL ? 'AND ' : 'WHERE ') . "(`from_name` LIKE '{$sKeys}' OR `to_name` LIKE '{$sKeys}' OR `from_email` LIKE '{$sKeys}' OR `to_email` LIKE '{$sKeys}' OR `code` LIKE '{$sKeys}' OR `notes` LIKE '{$sKeys}' OR `dateAdded` = '{$sKeysD}')";
    }
    $q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes` $SQL ORDER BY `id` DESC") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($GIFT = mysqli_fetch_object($q_p)) {
      $data .= mc_cleanCSV($GIFT->from_name, $separator) . $separator . mc_cleanCSV($GIFT->from_email, $separator) . $separator . mc_cleanCSV($GIFT->to_name, $separator) . $separator . mc_cleanCSV($GIFT->to_email, $separator) . $separator . mc_cleanCSV(date($this->settings->systemDateFormat, strtotime($GIFT->dateAdded)), $separator) . $separator . mc_cleanCSV($GIFT->value, $separator) . $separator . mc_cleanCSV($GIFT->redeemed, $separator) . $separator . mc_cleanCSV($GIFT->code, $separator) . $separator . mc_cleanCSV($GIFT->notes, $separator) . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function deleteGiftCode() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "giftcodes`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'giftcodes'
    ));
    return $rows;
  }

  public function updateGiftCertInfo() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcodes` SET
    `from_name`  = '{$_POST['from_name']}',
    `to_name`    = '{$_POST['to_name']}',
    `from_email` = '{$_POST['from_email']}',
    `to_email`   = '{$_POST['to_email']}',
    `message`    = '{$_POST['message']}',
    `value`      = '" . mc_cleanInsertionPrice($_POST['value']) . "',
    `redeemed`   = '" . mc_cleanInsertionPrice($_POST['redeemed']) . "',
    `notes`      = '{$_POST['notes']}',
    `enabled`    = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
    )) ? $_POST['enabled'] : 'yes') . "'
    WHERE `id`   = '" . mc_digitSan($_POST['giftID']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function addGiftCertificate() {
    $_POST = mc_safeImport($_POST);
    $img   = '';
    $name  = (isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '');
    $temp  = (isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "giftcerts` (
    `name`,
    `value`,
    `image`,
    `enabled`,
    `orderBy`
    ) VALUES (
    '{$_POST['name']}',
    '" . mc_cleanInsertionPrice($_POST['value']) . "',
    '{$img}',
    '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
      'yes',
      'no'
    )) ? $_POST['enabled'] : 'yes') . "',
    '9999'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $ID = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Do we have an image?
    if ($name && $temp && is_uploaded_file($temp)) {
      $ext = substr(strrchr(strtolower($name), '.'), 1);
      $fl  = 'gift-' . $ID . '.' . $ext;
      move_uploaded_file($temp, mc_uploadServerPath() . $fl);
      if (@file_exists(mc_uploadServerPath() . $fl)) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcerts` SET
        `image`    = '{$fl}'
        WHERE `id` = '{$ID}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function updateGiftCertificate() {
    $_POST = mc_safeImport($_POST);
    $img   = $_POST['curimage'];
    $name  = (isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '');
    $temp  = (isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '');
    // Do we have an image?
    if ($name && $temp && is_uploaded_file($temp)) {
      // Delete existing..
      if ($_POST['curimage'] && @file_exists(mc_uploadServerPath() . $_POST['curimage'])) {
        @unlink(mc_uploadServerPath() . $_POST['curimage']);
      }
      $next = $_GET['edit'];
      $ext  = substr(strrchr(strtolower($name), '.'), 1);
      $fl   = 'gift-' . $next . '.' . $ext;
      move_uploaded_file($temp, mc_uploadServerPath() . $fl);
      if (@file_exists(mc_uploadServerPath() . $fl)) {
        $img = $fl;
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "giftcerts` SET
    `name`     = '{$_POST['name']}',
    `value`    = '" . mc_cleanInsertionPrice($_POST['value']) . "',
    `image`    = '{$img}',
    `enabled`  = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
        'yes',
        'no'
      )) ? $_POST['enabled'] : 'yes') . "'
    WHERE `id` = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteGiftCertificate() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "giftcerts`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'giftcerts'
    ));
    return $rows;
  }

}

?>