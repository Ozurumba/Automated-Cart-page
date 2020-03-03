<?php

// Error Page, loads basic message if theme folder not found..

if (defined('PARENT') && defined('THEME_FOLDER')) {
  $tpl = mc_getSavant();

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->assign('TEXT', $errorPages);
  $tpl->display(THEME_FOLDER . '/error_pages/400.tpl.php');
} else {
  header('HTTP/1.0 400 Bad Request');
  header('Content-type: text/html; charset=utf-8');
  echo '<h1>Error - Bad Request</h1>';
  echo '<a href="index.php">Return to Store</a>';
}

?>
