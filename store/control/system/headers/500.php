<?php

// Error Page, loads basic message if theme folder not found..

if (defined('PARENT') && defined('THEME_FOLDER')) {
  $tpl = mc_getSavant();

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->assign('TEXT', $errorPages);
  $tpl->display(THEME_FOLDER . '/error_pages/500.tpl.php');
} else {
  header('HTTP/1.0 500 Internal Server Error');
  header('Content-type: text/html; charset=utf-8');
  echo '<h1>Internal Server Error</h1>';
  echo '<a href="index.php">Return to Store</a>';
}

?>
