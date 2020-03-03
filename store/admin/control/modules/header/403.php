<?php

if (!defined('WINPARENT')) {
  define('WINPARENT', 1);
}
define('PATH_ERR', substr(dirname(__file__), 0, strpos(dirname(__file__), 'control')-1) . '/');

if (!isset($mc_global)) {
  $mc_global = array(
   'ltr',
   'en'
  );
}
$charset = 'utf-8';
$pageTitle = '403';

include(PATH_ERR . 'templates/windows/header.php');
include(PATH_ERR . 'templates/windows/403.php');
include(PATH_ERR . 'templates/windows/footer.php');
exit;

?>
