<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'system/new-pages.php');
include(MCLANG . 'catalogue/categories.php');

if (isset($_GET['urlSlug'])) {
  include(REL_PATH . 'control/classes/class.rewrite.php');
  $MCRWR           = new mcRewrite();
  $MCRWR->settings = $SETTINGS;
  $slug            = (isset($_POST['slug']) ? $_POST['slug'] : '');
  echo $JSON->encode(array(
    ($SETTINGS->en_modr == 'no' || !ENABLE_SLUG_SUGGESTION ? '' : substr($MCRWR->title(mc_cleanData($slug)), 0, 250))
  ));
  exit;
}

if (isset($_GET['changeStatus'])) {
  $PG     = mc_getTableData('newpages', 'id', (int) $_GET['changeStatus']);
  $status = (isset($PG->enabled) ? $PG->enabled : 'no');
  $name   = (isset($PG->pageName) ? '"' . $PG->pageName . '"' . mc_defineNewline() . mc_defineNewline() : '');
  $msg    = $MCSYS->enableDisablePages($status);
  switch($msg) {
    case 'yes':
      echo $JSON->encode(array(
        'status' => $name . $msg_javascript369,
        'id' => (int) $_GET['changeStatus'],
        'flag' => 'page_enabled.gif'
      ));
      break;
    case 'no':
      echo $JSON->encode(array(
        'status' => $name . $msg_javascript368,
        'id' => (int) $_GET['changeStatus'],
        'flag' => 'page_disabled.gif'
      ));
      break;
  }
  exit;
}

if (isset($_GET['order'])) {
  $MCSYS->reOrderNewPages();
  exit;
}

if (isset($_POST['process'])) {
  if ($_POST['pageName']) {
    $MCSYS->addNewWebPage();
    $OK = true;
  }
}

if (isset($_POST['update'])) {
  if ($_POST['pageName']) {
    $MCSYS->updateWebPage();
    $OK2 = true;
  }
}

if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deleteWebPage();
  $OK3 = true;
}

$pageTitle   = mc_cleanDataEntVars($msg_javascript198) . ': ' . $pageTitle;
$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/new-pages.php');
include(PATH . 'templates/footer.php');

?>