<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/news.php');

// Order news..
if (isset($_GET['order'])) {
  $MCSYS->reOrderNewsTicker();
  exit;
}

// Add news..
if (isset($_POST['process'])) {
  if ($_POST['newsText']) {
    $MCSYS->addNews();
    $OK  = true;
  }
}

// Update news..
if (isset($_POST['update']) && $_POST['newsText']) {
  $MCSYS->updateNews();
  $OK2 = true;
}

// Delete news..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deleteNews();
  $OK3 = true;
}

$pageTitle   = $msg_javascript421 . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/news-ticker.php');
include(PATH . 'templates/footer.php');

?>
