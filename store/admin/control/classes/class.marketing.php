<?php

class marketing {

  public $settings;

  public function resetTrackers() {
    $mt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `code` FROM `" . DB_PREFIX . "tracker`
          WHERE `id` IN(" . mc_safeSQL(implode(',',$_POST['reset'])) . ")
          ORDER BY `id`
          ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($TRACKER = mysqli_fetch_object($mt)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tracker_clicks`
      WHERE `code` = '" . mc_safeSQL($TRACKER->code) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `trackcode` = ''
      WHERE `trackcode` = '" . mc_safeSQL($TRACKER->code) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function addTracker() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "tracker` (
    `name`,
    `code`
    ) VALUES (
    '{$_POST['name']}',
    '{$_POST['code']}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateTracker() {
    $_POST = mc_safeImport($_POST);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "tracker` SET
    `name`  = '{$_POST['name']}',
    `code`  = '{$_POST['code']}'
    WHERE `id`   = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deleteTracker() {
    $TRK = mc_getTableData('tracker', 'id', mc_digitSan($_GET['del']));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tracker`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    // Remove clicks and reset sales..
    if (isset($TRK->code)) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "tracker_clicks`
      WHERE `code` = '" . mc_safeSQL($TRK->code) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
      `trackcode` = ''
      WHERE `trackcode` = '" . mc_safeSQL($TRK->code) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    mc_tableTruncationRoutine(array(
      'tracker',
      'tracker_clicks'
    ));
    return $rows;
  }

  public function trackCode() {
    $az = range('a', 'z');
    return $az[rand(0,13)] . $az[rand(14,24)] . time();
  }

}

?>