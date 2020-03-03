<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

include(MCLANG . 'catalogue/categories.php');
include(MCLANG_REL . 'header.php');

// Clear cache..
if (isset($_GET['clearcache'])) {
  $MCCACHE->clear_cache();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Auto fill..
if (isset($_GET['autoFillPath'])) {
  echo $JSON->encode(array(
    'OK',
    substr(GLOBAL_PATH, 0, -1)
  ));
  exit;
}

// Clear logo..
if (isset($_GET['removeLogo'])) {
  $MCSYS->resetStoreLogo();
  echo $JSON->encode(array(
    $msg_settings194,
    REL_HTTP_PATH . THEME_FOLDER . '/images/logo.png'
  ));
  exit;
}

// Check..
if (isset($_GET['s']) && !in_array($_GET['s'], range(2, 9))) {
  header("Location: index.php?p=settings");
  exit;
}

if (isset($_GET['s']) && $_GET['s'] == 4) {
  if (isset($_GET['reload']) && $_GET['reload'] == 'yes') {
    $id   = substr($_GET['pr'], strpos($_GET['pr'], '-') + 1, strlen($_GET['pr']));
    $html = '';
    $all  = '';
    $prod = array();
    $vars = array();
    $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                  FROM `" . DB_PREFIX . "products`
                  LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
                  WHERE `category`                 = '{$id}'
                  AND `pEnable`                    = 'yes'
                  GROUP BY `" . DB_PREFIX . "products`.`id`
                  ORDER BY `pName`
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PR = mysqli_fetch_object($q_products)) {
      $html .= '<b>' . $PR->pid . '</b> - ' . mc_cleanData($PR->pName) . '<br>';
    }
    echo $JSON->encode(array(
      ($html ? mc_cleanData($all . $html) : mc_cleanData($msg_settings210))
    ));
    exit;
  }
}

// Permissions..
if (isset($_GET['s']) && in_array($_GET['s'], range(1, 9))) {
  mc_pagePermissions('settings_' . $_GET['s']);
} else {
  mc_pagePermissions('settings_1');
}

// Re-Order Banners..
if (isset($_GET['s']) && $_GET['s'] == '9' && isset($_GET['order'])) {
  $MCSYS->reOrderBanners();
  exit;
}

// Add banners..
if (isset($_POST['process_banners'])) {
  $MCSYS->addBanners();
  $OK = true;
}

// Update banners..
if (isset($_POST['update_banners'])) {
  $MCSYS->updateBanners();
  $OK2 = true;
}

// Delete banner..
if (isset($_GET['del']) && ctype_digit($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCSYS->deleteBanner();
  $OK4 = true;
}

// Update settings..
if (isset($_POST['process'])) {
  $MCSYS->updateSettings();
  $OK = true;
}

$title = $msg_javascript106;

if (isset($_GET['s'])) {
  switch($_GET['s']) {
    case 3:
      $title = $msg_settings50;
      break;
    case 4:
      $title = $msg_settings54;
      break;
    case 5:
      $title = $msg_settings131;
      break;
    case 6:
      $title = $msg_settings25;
      break;
    case 7:
      $title = $msg_settings65;
      break;
    case 8:
      $title = $msg_settings84;
      break;
    case 9:
      $title = $msg_settings116;
      break;
  }
}

$pageTitle    = mc_cleanDataEntVars($title) . ': ' . $pageTitle;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/system/settings' . (isset($_GET['s']) ? $_GET['s'] : '') . '.php');
include(PATH . 'templates/footer.php');

?>