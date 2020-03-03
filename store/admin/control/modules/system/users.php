<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/users.php');

// Permissions..
if (!isset($sysCartUser[1]) || (isset($sysCartUser[1]) && $sysCartUser[1] == 'restricted')) {
  header('HTTP/1.0 403 Forbidden');
	header('Content-type: text/html; charset=utf-8');
  echo '<h1>Permission Denied</h1>';
  exit;
}

// Add user..
if (isset($_POST['process']) && $_POST['userName'] && $_POST['userPass']) {
  $MCUSR->addUser();
  $OK = true;
}

// Update user..
if (isset($_POST['update']) && $_POST['userName']) {
  $MCUSR->updateUser();
  $OK2 = true;
}

// Delete user..
if (isset($_GET['del']) && ctype_digit($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCUSR->deleteUser();
  $OK3 = true;
}

$pageTitle     = mc_cleanDataEntVars($msg_javascript254) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/users.php');
include(PATH . 'templates/footer.php');

?>
