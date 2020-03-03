<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

if (isset($_SESSION[mc_encrypt('adm_menu_panel' . SECRET_KEY)])) {
  unset($_SESSION[mc_encrypt('adm_menu_panel' . SECRET_KEY)]);
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');

// Tweet..
if (mc_tweetPerms($sysCartUser) == 'yes' && isset($_GET['tweet']) && $SETTINGS->tweet == 'yes') {
  include(PATH . 'templates/windows/post-tweet.php');
  exit;
}

// Test Mails.
if (isset($_GET['tmail'])) {
  include(PATH . 'templates/windows/test-mail.php');
  exit;
}

// Preview text..
if (isset($_GET['prevTxtBox'])) {
  if (isset($_POST['boxdata'])) {
    $_SESSION['previewBoxText'] = mc_cleanData($_POST['boxdata']);
    echo $JSON->encode(array(
      'resp' => 'OK'
    ));
  } else {
    echo (isset($_SESSION['previewBoxText']) ? $_SESSION['previewBoxText'] : $msg_admin3_0[7]);
  }
  exit;
}

// BBCode window..
if (isset($_GET['bbCode'])) {
  $pageTitle = $msg_script66 . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/bbcode.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Version check..
if (isset($_GET['versionCheck'])) {
  if (isset($_GET['ck'])) {
    echo $JSON->encode(array(
      'html' => mc_NL2BR($MCSYS->mswSoftwareVersionCheck())
    ));
    exit;
  }
  $pageTitle = $msg_header17 . ': ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/system/version-check.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Display message if restriction is reached..
if ($cmd == 'main-stop' && LICENCE_VER == 'locked') {
  $pageTitle = 'Free Version Restriction: ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'control/modules/system/controller.php');
  include(PATH . 'templates/footer.php');
  exit;
}

$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/main.php');
include(PATH . 'templates/footer.php');

?>