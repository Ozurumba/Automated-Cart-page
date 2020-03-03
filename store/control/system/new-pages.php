<?php

if (!defined('PARENT') || !isset($_GET['np'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Check digit..
mc_checkDigit($_GET['np']);

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');

// Get page data..
$NEW_PAGE = mc_getTableData('newpages', 'id', (int) $_GET['np'], ' AND `enabled` = \'yes\'');

if (!isset($NEW_PAGE->id)) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Check trade permissions..
if ($NEW_PAGE->trade == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Render new page data..
if (isset($NEW_PAGE->pageName)) {
  // Redirect external links..
  if ($NEW_PAGE->linkExternal == 'yes' && (substr($NEW_PAGE->pageText, 0, 5) == 'http:' OR substr($NEW_PAGE->pageText, 0, 6) == 'https:')) {
    header("Location: " . mc_cleanData($NEW_PAGE->pageText));
    exit;
  }

  $headerTitleText = mc_cleanData($NEW_PAGE->pageName . ': ' . $headerTitleText);

  // Overwrite meta data..
  if ($NEW_PAGE->pageKeys) {
    $overRideMetaKeys = mc_safeHTML($NEW_PAGE->pageKeys);
  }
  if ($NEW_PAGE->pageDesc) {
    $overRideMetaDesc = mc_safeHTML($NEW_PAGE->pageDesc);
  }

  // Breadcrumb..
  $breadcrumbs = array(
    mc_safeHTML($NEW_PAGE->pageName)
  );

  if ($NEW_PAGE->customTemplate) {
    // Load javascript..
    $loadJS['swipe']  = 'load';
  }

  // Load left menu?
  if ($NEW_PAGE->leftColumn == 'yes') {
    include(PATH . 'control/left-box-controller.php');
  }

  // Structured data..
  if ($NEW_PAGE->linkExternal == 'yes' && (substr(strtolower($NEW_PAGE->pageText), 0, 7) == 'http://' || substr(strtolower($NEW_PAGE->pageText), 0, 8) == 'https://')) {
    $url = trim($NEW_PAGE->pageText);
  } else {
    $url = $MCRWR->url(array(
      $MCRWR->config['slugs']['npg'] . '/' . $NEW_PAGE->id . '/' . ($NEW_PAGE->rwslug ? $NEW_PAGE->rwslug : $MCRWR->title($NEW_PAGE->pageName)),
      'np=' . $NEW_PAGE->id
    ));
  }
  $mc_structUrl   = $url;
  $mc_structTitle = $headerTitleText;
  $mc_structDesc  = (isset($overRideMetaDesc) ? $overRideMetaDesc : $SETTINGS->metaDesc);

  include(PATH . 'control/header.php');

  $tpl = mc_getSavant();
  $tpl->assign('PAGE', (array) $NEW_PAGE);
  $tpl->assign('DATA', mc_txtParsingEngine($NEW_PAGE->pageText));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/' . ($NEW_PAGE->customTemplate && file_exists(PATH . THEME_FOLDER . '/customTemplates/' . $NEW_PAGE->customTemplate) ? 'customTemplates/' . $NEW_PAGE->customTemplate : 'new-page.tpl.php'));

  include(PATH . 'control/footer.php');
} else {
  include(PATH . 'control/system/headers/404.php');
  exit;
}

?>