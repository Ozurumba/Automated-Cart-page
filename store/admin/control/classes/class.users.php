<?php

class users {

  public $settings;

  public function updateUserLogTime($id, $time) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "users` SET
    `lastLogin`  = '{$time}'
    WHERE `id`   = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Add User..
  public function addUser() {
    $_POST = mc_safeImport($_POST);
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('users') + 1 > RESTR_USERS) {
        mc_restrictionLimitRedirect();
      }
    }
    $restricted = (!empty($_POST['pages']) ? $_POST['pages'] : array(
      'noaccess'
    ));
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "users` (
    `userName`,
    `userPass`,
    `userEmail`,
    `userType`,
    `userPriv`,
    `accessPages`,
    `enableUser`,
    `userNotify`,
    `tweet`
    ) VALUES (
    '{$_POST['userName']}',
    '" . mc_PassHash(array('type' => 'add', 'pass' => $_POST['userPass'])) . "',
    '{$_POST['userEmail']}',
    '" . (isset($_POST['userType']) && in_array($_POST['userType'], array(
        'admin',
        'restricted'
      )) ? $_POST['userType'] : 'restricted') . "',
    '" . (isset($_POST['userPriv']) && in_array($_POST['userPriv'], array(
        'yes',
        'no'
      )) ? $_POST['userPriv'] : 'no') . "',
    '" . (isset($_POST['userType']) && $_POST['userType'] == 'restricted' ? implode('|', $restricted) : '') . "',
    '" . (isset($_POST['enableUser']) && in_array($_POST['enableUser'], array(
        'yes',
        'no'
      )) ? $_POST['enableUser'] : 'no') . "',
    '" . (isset($_POST['userNotify']) && in_array($_POST['userNotify'], array(
        'yes',
        'no'
      )) ? $_POST['userNotify'] : 'no') . "',
    '" . (isset($_POST['tweet']) && in_array($_POST['tweet'], array(
        'yes',
        'no'
      )) ? $_POST['tweet'] : 'no') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Update user..
  public function updateUser() {
    $_POST      = mc_safeImport($_POST);
    $restricted = (!empty($_POST['pages']) ? $_POST['pages'] : array(
      'noaccess'
    ));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "users` SET
    `userName`     = '{$_POST['userName']}',
    `userPass`     = '" . ($_POST['userPass'] ? mc_PassHash(array('type' => 'add', 'pass' => $_POST['userPass'])) : $_POST['userPass2']) . "',
    `userEmail`    = '{$_POST['userEmail']}',
    `userType`     = '" . (isset($_POST['userType']) && in_array($_POST['userType'], array(
        'admin',
        'restricted'
      )) ? $_POST['userType'] : 'restricted') . "',
    `userPriv`     = '" . (isset($_POST['userPriv']) && in_array($_POST['userPriv'], array(
        'yes',
        'no'
      )) ? $_POST['userPriv'] : 'no') . "',
    `accessPages`  = '" . (isset($_POST['userType']) && $_POST['userType'] == 'restricted' ? implode('|', $restricted) : '') . "',
    `enableUser`   = '" . (isset($_POST['enableUser']) && in_array($_POST['enableUser'], array(
        'yes',
        'no'
      )) ? $_POST['enableUser'] : 'no') . "',
    `userNotify`   = '" . (isset($_POST['userNotify']) && in_array($_POST['userNotify'], array(
        'yes',
        'no'
      )) ? $_POST['userNotify'] : 'no') . "',
    `tweet`   = '" . (isset($_POST['tweet']) && in_array($_POST['tweet'], array(
        'yes',
        'no'
      )) ? $_POST['tweet'] : 'no') . "'
    WHERE id       = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  // Delete user..
  public function deleteUser() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "users` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'users'
    ));
    return $rows;
  }

}

?>