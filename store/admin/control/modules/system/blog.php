<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(PATH . 'control/classes/class.blog.php');
$BLOG           = new blog();
$BLOG->settings = $SETTINGS;

// Load language file(s)..
include(MCLANG . 'system/blog.php');

if (isset($_POST['process'])) {
  if ($_POST['title']) {
    $BLOG->add();
    $OK = true;
  }
}

if (isset($_POST['update'])) {
  if ($_POST['title']) {
    $BLOG->update();
    $OK2 = true;
  }
}

if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $BLOG->delete();
  $OK3 = true;
}

$pageTitle = mc_cleanDataEntVars($msg_admin3_0[56]) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/blog.php');
include(PATH . 'templates/footer.php');

?>