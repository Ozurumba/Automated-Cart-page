<?php

if (!defined('PARENT')) {
  include(GLOBAL_PATH . 'control/system/headers/403.php');
  exit;
}

//=======================
// THEME SWITCHER
//=======================

define('SET_THEME_DEFAULT', (isset($_SESSION['mc_acc_type_' . mc_encrypt(mc_encrypt(SECRET_KEY))]) && $_SESSION['mc_acc_type_' . mc_encrypt(mc_encrypt(SECRET_KEY))] == 'trade' ? $SETTINGS->theme2 : $SETTINGS->theme));

// Firstly, check theme for trade accounts..
if (isset($_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
  $e = $_SESSION['mc_auth_' . mc_encrypt(mc_encrypt(SECRET_KEY))];
  if (mswIsValidEmail($e)) {
    $u = mc_getTableData('accounts', 'email', mc_safeSQL($e));
    if (isset($u->type) && $u->type == 'trade' && $SETTINGS->theme2 && is_dir(GLOBAL_PATH . 'content/' . $SETTINGS->theme2)) {
      define('THEME_FOLDER', 'content/' . $SETTINGS->theme2);
    }
  }
}

if (!defined('THEME_FOLDER')) {
  // Check to see if there are other themes before we do database lookups..
  $otherThemes = 0;
  $thisDate    = date('Y-m-d');
  if (is_dir(GLOBAL_PATH . 'content')) {
    $readThemes  = opendir(GLOBAL_PATH . 'content');
    while (false !== ($read = readdir($readThemes))) {
      if (is_dir(GLOBAL_PATH . 'content/' . $read) && substr(strtolower($read), 0, 6) == '_theme' && $read != SET_THEME_DEFAULT) {
        // Yep, something found, no further loops required..
        ++$otherThemes;
        break;
      }
    }
    closedir($readThemes);
  }

  // Did we find any themes..
  if ($otherThemes > 0) {
    // First check for category based theme..
    if (isset($_GET['c']) && $_GET['c'] > 0) {
      $themeCat = (int) $_GET['c'];
      $t = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `theme` FROM `" . DB_PREFIX . "categories`
           WHERE `id` = '{$themeCat}'
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $THM = mysqli_fetch_object($t);
    } else {
      $t = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `theme` FROM `" . DB_PREFIX . "themes`
           WHERE `from`  <= '{$thisDate}'
           AND `to`      >= '{$thisDate}'
           AND `enabled`  = 'yes'
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $THM = mysqli_fetch_object($t);
    }
    // Did the lookup find anything?
    if (isset($THM->theme) && $THM->theme && is_dir(GLOBAL_PATH . 'content/' . $THM->theme)) {
      // Check the theme folder exists, else load default..
      define('THEME_FOLDER', 'content/' . $THM->theme);
    } else {
      // Load default..
      define('THEME_FOLDER', 'content/' . (is_dir(GLOBAL_PATH . 'content/' . SET_THEME_DEFAULT) ? SET_THEME_DEFAULT : '_theme_default'));
    }
  } else {
    // Load default..
    define('THEME_FOLDER', 'content/' . (is_dir(GLOBAL_PATH . 'content/' . SET_THEME_DEFAULT) ? SET_THEME_DEFAULT : '_theme_default'));
  }
}

//=======================
// END SWITCHER..
//=======================

?>