<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/login.php');

// Logout..
if ($cmd == 'logout') {
  session_unset();
  session_destroy();
  unset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'], $_SESSION[mc_encrypt(SECRET_KEY) . '_global_user'], $_SESSION[mc_encrypt(SECRET_KEY) . '_accessPages'], $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'], $_SESSION[mc_encrypt(SECRET_KEY) . '_user_type'], $_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv']);
  $sysCartUser = array();
  if (isset($_COOKIE[mc_encrypt(SECRET_KEY . DB_NAME)])) {
    $_COOKIE[mc_encrypt(SECRET_KEY . DB_NAME)] = '';
    unset($_COOKIE[mc_encrypt(SECRET_KEY . DB_NAME)]);
    @setcookie(mc_encrypt(SECRET_KEY . DB_NAME), '');
  }
  header("Location: index.php?p=login");
  exit;
}

// Login..
if (isset($_POST['process'])) {
  $_POST = array_map('trim', $_POST);
  if ($_POST['user'] == '') {
    $U_ERROR = $msg_login5;
  }
  if ($_POST['pass'] == '') {
    $P_ERROR = $msg_login6;
  }
  if (defined('RESTRICT_BY_IP') && RESTRICT_BY_IP) {
    $allowed = array_map('trim', explode(',', RESTRICT_BY_IP));
    $current = mc_getRealIPAddr(true);
    if (isset($current[0]) && !in_array($current[0], $allowed)) {
      $U_ERROR = $msg_admin_login[0];
    }
  }
  if (!isset($U_ERROR) && !isset($P_ERROR)) {
    // Check global login first..
    if (file_exists(PATH . 'control/access.php') && !in_array(PATH . 'control/access.php', get_included_files())) {
      include_once(PATH . 'control/access.php');
    }
    if (defined('USERNAME') && defined('PASSWORD') && $_POST['user'] == USERNAME && mc_PassHash(array('type' => 'calc', 'val' => $_POST['pass'], 'hash' => mc_PassHash(array('type' => 'add', 'pass' => PASSWORD))))) {
      $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']  = USERNAME;
      $_SESSION[mc_encrypt(SECRET_KEY) . '_global_user'] = mc_encrypt(SECRET_KEY . mc_encrypt('gl0bal'));
      // Log if enabled..
      if ($SETTINGS->enEntryLog == 'yes') {
        $MCSYS->addEntryLog('global','0');
      }
      // Set cookie..
      if (ENABLE_LOGIN_COOKIE && isset($_POST['rm'])) {
        @setcookie(mc_encrypt(SECRET_KEY . DB_NAME), serialize($_SESSION), time() + 60 * 60 * 24 * LOGIN_COOKIE_DURATION);
      }
      // Redirect to order if coming from order link..
      if (isset($_SESSION['loadOrder']) && ctype_digit($_SESSION['loadOrder']) && ORDER_REDIRECT) {
        header("Location: index.php?p=sales-view&sale=" . (int) $_SESSION['loadOrder']);
        unset($_SESSION['loadOrder']);
      } else {
        header("Location: index.php");
      }
      exit;
    } else {
      $_POST = mc_safeImport($_POST);
      $U     = mc_getTableData('users', 'userName', $_POST['user'], ' AND `enableUser` = \'yes\'');
      if (!isset($U->userName) || !mc_PassHash(array('type' => 'calc', 'val' => $_POST['pass'], 'hash' => $U->userPass))) {
        $P_ERROR = $msg_login7;
      } else {
        if ($U->accessPages == 'noaccess' && $U->userType == 'restricted') {
          $P_ERROR = $msg_login8;
        } else {
          $_SESSION[mc_encrypt(SECRET_KEY) . '_accessPages']     = explode('|', $U->accessPages);
          $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']      = $_POST['user'];
          $_SESSION[mc_encrypt(SECRET_KEY) . '_user_type']       = $U->userType;
          $_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv']        = $U->userPriv;
          $_SESSION[mc_encrypt(SECRET_KEY) . 'lastLoggedInTime'] = ($U->lastLogin ? $U->lastLogin : $msg_login9);
          $_SESSION[mc_encrypt(SECRET_KEY) . 'tweets']           = $U->tweet;
          // Log if enabled..
          if ($SETTINGS->enEntryLog == 'yes') {
            $MCSYS->addEntryLog('user',$U);
          }
          // Set cookie..
          if (ENABLE_LOGIN_COOKIE && isset($_POST['rm'])) {
            @setcookie(mc_encrypt(SECRET_KEY . DB_NAME), serialize($_SESSION), time() + 60 * 60 * 24 * LOGIN_COOKIE_DURATION);
          }
          // Update user last logged in time..
          $MCUSR->updateUserLogTime($U->id, $msg_login9);
          // Redirect to order if coming from order link..
          // Check permissions first..
          if (in_array('sales-view', $_SESSION[mc_encrypt(SECRET_KEY) . '_accessPages']) || $U->userType == 'admin') {
            $perms = true;
          }
          if (isset($_SESSION['loadOrder']) && ctype_digit($_SESSION['loadOrder']) && isset($perms) && ORDER_REDIRECT) {
            header("Location: index.php?p=sales-view&sale=" . (int) $_SESSION['loadOrder']);
            unset($_SESSION['loadOrder']);
          } else {
            header("Location: index.php");
          }
          exit;
        }
      }
    }
  }
}

$pageTitle = mc_cleanDataEntVars($msg_login11);

include(PATH . 'templates/system/portal.php');

?>