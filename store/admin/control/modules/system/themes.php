<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/themes.php');

if (isset($_POST['process'])) {
  if ($_POST['from'] && $_POST['to'] && $_POST['theme']) {
    $MCSYS->addThemeSwitch();
    $OK = true;
  } else {
    header("Location: ?p=themes");
	  exit;
  }
}

if (isset($_POST['update'])) {
  if ($_POST['from'] && $_POST['to'] && $_POST['theme']) {
    $MCSYS->updateThemeSwitch();
    $OK2 = true;
  } else {
    header("Location: ?p=themes");
	  exit;
  }
}

if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deleteThemeSwitch();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_header22) . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/theme-switcher.php');
include(PATH . 'templates/footer.php');

?>
