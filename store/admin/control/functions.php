<?php

// Only for elfinder plugin..
function mc_elFinderAccessControl($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0 ? !($attr == 'read' || $attr == 'write') :  null;
}

// Can this user tweet?
function mc_tweetPerms($usr) {
  $p = 'no';
  if (isset($usr[5]) && in_array($usr[5], array('yes','no'))) {
    return $usr[5];
  }
  return $p;
}

// Format time..
function mc_formatTime($time) {
  if ($time == '') {
    return '00:00:00';
  }
  $t = explode(':', $time);
  if (isset($t[0],$t[1],$t[2])) {
    $h = ($t[0] > 24 ? 24 : ($t[0] < 10 && strlen($t[0]) == '1' ? '0' . $t[0] : $t[0]));
    $m = ($t[1] > 59 ? 59 : ($t[1] < 10 && strlen($t[1]) == '1' ? '0' . $t[1] : $t[1]));
    $s = ($t[2] > 59 ? 59 : ($t[2] < 10 && strlen($t[2]) == '1' ? '0' . $t[2] : $t[2]));
    return $h . ':' . $m . ':' . $s;
  }
  return '00:00:00';
}

// Hide/show fields for batch updating..
function mc_hideShowBatchOperation($identifier, $field, $omit = '') {
  global $msg_productmanage57, $msg_productmanage58;
  if (defined('BATCH_EDIT_MODE')) {
    return '<span style="float:right" class="fl_' . sha1($field) . '"><a class="include" onclick="mc_batchAddField(\'include\',\'' . $identifier . '\',\'' . $field . '\',\'' . sha1($field) . '\',\'' . $omit . '\');return false" href="#" title="' . mc_cleanDataEntVars($msg_productmanage57) . '"><i class="fa fa-check-square fa-fw"></i></a> <a class="exclude" href="#" onclick="mc_batchAddField(\'exclude\',\'' . $identifier . '\',\'' . $field . '\',\'' . sha1($field) . '\',\'' . $omit . '\');return false" title="' . mc_cleanDataEntVars($msg_productmanage58) . '"><i class="fa fa-minus-square fa-fw"></i></a></span>';
  }
  return '';
}

// Left box text..
function mc_leftBoxText($key) {
  global $msg_public_header8, $msg_public_header6, $msg_public_header13, $msg_public_header35, $msg_public_header3, $msg_public_header22, $msg_public_header15, $msg_public_header34;
  $arr = array(
    'recent' => $msg_public_header8,
    'links' => $msg_public_header6,
    'brands' => $msg_public_header13,
    'rss' => $msg_public_header35,
    'cat' => $msg_public_header3,
    'points' => $msg_public_header22,
    'popular' => $msg_public_header15,
    'tweets' => $msg_public_header34
  );
  return $arr[$key];
}

// Clear file cache..
function mc_clearFileCache() {
  global $cmd, $SETTINGS;
  if (file_exists(PATH . 'import/mc_FileCache.cache')) {
    @unlink(PATH . 'import/mc_FileCache.cache');
  }
  // Clear import cache..
  if ($cmd == 'main') {
    if (is_dir(PATH . 'import/')) {
      $dir = opendir(PATH . 'import/');
      while (false !== ($read = readdir($dir))) {
        if (substr(strtolower($read), -4) == '.txt') {
          $split = explode('-', $read);
          if (substr($split[1], 0, 8) < date('Ymd')) {
            @unlink(PATH . 'import/' . $read);
          }
        }
      }
      closedir($dir);
    }
  }
}

// Read download directory recursively..
function mc_downloadDirScanner($p, $root, $cur = '') {
  global $dString;
  $rootfiles = '';
  $exclude   = array(
    '.',
    '..',
    '.DS_Store',
    'Thumbs.db'
  );
  if (is_dir($p)) {
    $handle    = opendir($p);
    if ($handle) {
      while (false !== ($fn = readdir($handle))) {
        if (!in_array($fn, $exclude)) {
          if (!is_file($p . $fn) && is_dir($p . $fn . '/')) {
            if (strcmp($fn, '.') != 0 && strcmp($fn, '.') != 0) {
              if ($cur == '') {
                $f = substr($p . $fn, strlen($root) + 1);
              } else {
                $f = $cur;
              }
              if (is_dir($p . $fn)) {
                $dir = opendir($p . $fn);
                if ($dir) {
                  while (false !== ($read = readdir($dir))) {
                    if (!in_array($read, array(
                      '.',
                      '..'
                    )) && !is_dir($p . $fn . '/' . $read)) {
                      $dString .= '<option value="' . $f . '/' . $read . '">' . $f . '/' . $read . '</option>' . mc_defineNewline();
                    }
                  }
                  closedir($dir);
                }
              }
              mc_downloadDirScanner("$p$fn/", $root, $cur);
            }
          } else {
            $rootfiles .= '<option value="' . $fn . '">' . $fn . '</option>' . mc_defineNewline();
          }
        }
      }
      closedir($handle);
    }
  }
  return trim($dString . $rootfiles);
}

// Restriction limit redirect..
function mc_restrictionLimitRedirect() {
  header("Location: index.php?restriction=yes");
  exit;
}

// Get product display image..
function mc_storeProductImg($id, $PRODUCTS, $link = true, $giftImg = '',$alt = '') {
  global $SETTINGS;
  // Type won`t exist on add screen..
  if (isset($PRODUCTS->productType) && $PRODUCTS->productType == 'virtual') {
    $pic = '<img src="templates/images/no-product-image.gif" alt="' . mc_safeHTML($PRODUCTS->pName) . '" title="' . mc_safeHTML($PRODUCTS->pName) . '">';
    if ($giftImg && @file_exists($SETTINGS->serverPath . '/' . PRODUCTS_FOLDER . '/' . $giftImg)) {
      $pic = ($link ? '<a onclick="mc_Window(this.href,0,0,\'\');return false" href="' . $SETTINGS->ifolder . '/' . PRODUCTS_FOLDER . '/' . $giftImg . '">' : '') . ($alt ? $alt : '<img src="' . $SETTINGS->ifolder . '/' . PRODUCTS_FOLDER . '/' . $giftImg . '" alt="' . mc_safeHTML($PRODUCTS->pName) . '" title="' . mc_safeHTML($PRODUCTS->pName) . '" class="img-responsive">') . ($link ? '</a>' : '');
    }
  } else {
    $IMG = mc_getTableData('pictures', 'product_id', $id, 'ORDER BY displayImg,id');
    $pic = '<img src="templates/images/no-product-image.gif" alt="' . mc_safeHTML($PRODUCTS->pName) . '" title="' . mc_safeHTML($PRODUCTS->pName) . '">';
    if (isset($IMG->id)) {
      $ops = (isset($IMG->remoteServer) ? $IMG->remoteServer : 'no');
      switch($ops) {
        case 'yes':
          $pic = ($link ? '<a onclick="mc_Window(this.href,0,0,\'\');return false" href="' . $IMG->remoteImg . '" >' : '') . ($alt ? $alt : '<img src="' . ($IMG->remoteThumb ? $IMG->remoteThumb : $IMG->remoteImg) . '" alt="' . mc_safeHTML($PRODUCTS->pName) . '" title="' . mc_safeHTML($PRODUCTS->pName) . '" class="img-responsive">') . ($link ? '</a>' : '');
          break;
        default:
          if (@file_exists($SETTINGS->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->picture_path) && @file_exists($SETTINGS->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->thumb_path)) {
            $pic = ($link ? '<a onclick="mc_Window(this.href,0,0,\'\');return false" href="' . $SETTINGS->ifolder . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->picture_path . '" >' : '') . ($alt ? $alt : '<img src="' . $SETTINGS->ifolder . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->thumb_path . '" alt="' . mc_safeHTML($PRODUCTS->pName) . '" title="' . mc_safeHTML($PRODUCTS->pName) . '" class="img-responsive">') . ($link ? '</a>' : '');
          }
          break;
      }
    }
  }
  return $pic;
}

// Get tare weight..
function mc_getTareWeight($weight, $service, $multiple = array()) {
  if (isset($multiple[0], $multiple[1])) {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "tare`
             WHERE `rWeightFrom`             <= $multiple[0]
             AND `rWeightTo`                 >= $multiple[1]
             AND `rService`                   = '{$service}'
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
  } else {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "tare`
             WHERE `rWeightFrom`             <= $weight
             AND `rWeightTo`                 >= $weight
             AND `rService`                   = '{$service}'
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
  }
  $TARE = mysqli_fetch_object($query);
  if (isset($TARE->id)) {
    return array(
      'yes',
      $TARE->rCost
    );
  }
  return array(
    'no',
    '0.00'
  );
}

// Enter dates in box to correct format..
function mc_enterDatesBox($date) {
  global $SETTINGS;
  if ($date == '') {
    return '';
  }
  switch($SETTINGS->jsDateFormat) {
    case 'DD-MM-YYYY':
      $tdate = date('d-m-Y', strtotime($date));
      break;
    case 'DD/MM/YYYY':
      $tdate = date('d/m/Y', strtotime($date));
      break;
    case 'YYYY-MM-DD':
      $tdate = $date;
      break;
    case 'YYYY/MM/DD':
      $tdate = date('Y/m/d', strtotime($date));
      break;
    case 'MM-DD-YYYY':
      $tdate = date('m-d-Y', strtotime($date));
      break;
    case 'MM/DD/YYYY':
      $tdate = date('m/d/Y', strtotime($date));
      break;
  }
  return $tdate;
}

function mc_clearImportFolder() {
  if (is_dir(PATH . 'import')) {
    $dir = opendir(PATH . 'import');
    while (false !== ($read = readdir($dir))) {
      if (!in_array($read, array(
        '.',
        '..',
        'index.html',
        'index.htm',
        '.htaccess'
      ))) {
        @unlink(PATH . 'import/' . $read);
      }
    }
    closedir($dir);
  }
}

function mc_deletePermissions() {
  if (file_exists(PATH . 'control/access.php') && !in_array(PATH . 'control/access.php', get_included_files())) {
    include_once(PATH . 'control/access.php');
  }
  if (defined('USERNAME') && defined('PASSWORD') && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_global_user']) && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) && $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'] == USERNAME && $_SESSION[mc_encrypt(SECRET_KEY) . '_global_user'] == mc_encrypt(SECRET_KEY . mc_encrypt('gl0bal'))) {
    return 'yes';
  } else {
    if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv']) && in_array($_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv'], array(
      'yes',
      'no'
    ))) {
      return $_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv'];
    }
  }
  return 'no';
}

function mc_pagePermissions($pg, $header = true) {
  global $sysCartUser;
  if ($pg != 'logout') {
    if (isset($sysCartUser[1]) && $sysCartUser[1] == 'restricted') {
      if (!in_array($pg, $sysCartUser[3]) || $sysCartUser[3] == 'noaccess') {
        header("Location: index.php?perms=no");
        exit;
      }
    }
  }
  return true;
}

function mc_getUser($s) {
  if (file_exists(PATH . 'control/access.php') && !in_array(PATH . 'control/access.php', get_included_files())) {
    include_once(PATH . 'control/access.php');
  }
  $user = array();
  // Check cookie..
  if (isset($_COOKIE[mc_encrypt(SECRET_KEY . DB_NAME)]) && !isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'])) {
    $a = unserialize($_COOKIE[mc_encrypt(SECRET_KEY . DB_NAME)]);
    foreach ($a AS $k => $v) {
      $_SESSION[$k] = $v;
    }
    if (!isset($_SESSION['checkSysClean'])) {
      mc_systemCartCleanUp($s);
      $_SESSION['checkSysClean'] = true;
    }
  }
  if (defined('USERNAME') && defined('PASSWORD') && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) && $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'] == USERNAME && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_global_user']) && $_SESSION[mc_encrypt(SECRET_KEY) . '_global_user'] == mc_encrypt(SECRET_KEY . mc_encrypt('gl0bal'))) {
    $user[0] = USERNAME;
    $user[1] = 'global';
    $user[2] = '';
    $user[3] = array();
    $user[4] = '';
    $user[5] = TWEET_GLOBAL;
  } else {
    if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs']) && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_user_type']) && in_array($_SESSION[mc_encrypt(SECRET_KEY) . '_user_type'], array(
      'admin',
      'restricted'
    )) && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv']) && in_array($_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv'], array(
      'yes',
      'no'
    )) && isset($_SESSION[mc_encrypt(SECRET_KEY) . '_accessPages'])) {
      $user[0] = $_SESSION[mc_encrypt(SECRET_KEY) . '_loggedInAs'];
      $user[1] = $_SESSION[mc_encrypt(SECRET_KEY) . '_user_type'];
      $user[2] = $_SESSION[mc_encrypt(SECRET_KEY) . '_del_priv'];
      $user[3] = $_SESSION[mc_encrypt(SECRET_KEY) . '_accessPages'];
      $user[4] = $_SESSION[mc_encrypt(SECRET_KEY) . 'lastLoggedInTime'];
      $user[5] = $_SESSION[mc_encrypt(SECRET_KEY) . 'tweets'];
    }
  }
  return $user;
}

function mc_delPrivileges() {
  global $sysCartUser;
  if (isset($sysCartUser[1]) && $sysCartUser[1] == 'global') {
    return 'yes';
  }
  if (isset($sysCartUser[2]) && $sysCartUser[2] == 'yes') {
    return 'yes';
  }
  return 'no';
}

function mc_isWebmasterLoggedIn($user, $login = false) {
  if (!$login) {
    if (!isset($user[0], $user[1], $user[2], $user[3])) {
      header("Location: index.php?p=login");
      exit;
    }
  } else {
    if (isset($user[0], $user[1], $user[2], $user[3])) {
      header("Location: index.php");
      exit;
    }
  }
}

function mc_userManagementType($type) {
  global $msg_users7, $msg_users8;
  switch($type) {
    case 'admin':
      return $msg_users7;
      break;
    case 'restricted':
      return $msg_users8;
      break;
  }
}

function mc_getDiscountType($discount) {
  global $msg_coupons23, $msg_coupons24;
  switch($discount) {
    case 'freeshipping':
      return $msg_coupons23;
      break;
    case 'notax':
      return $msg_coupons24;
      break;
  }
}

// Get flat rate zones..
function mc_getFlatRateZones($table = 'flat') {
  $zones = array();
  $q_zon = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `inZone` FROM `" . DB_PREFIX . $table . "`") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($Z = mysqli_fetch_object($q_zon)) {
    $zones[] = $Z->inZone;
  }
  return $zones;
}

// Get offer count..
function mc_getCatOfferCount($id) {
  $count = 0;
  $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
                LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                WHERE `category`                          = '{$id}'
                AND `pEnable`                             = 'yes'
                AND `pOffer`                              > 0
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pName`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($P = mysqli_fetch_object($q_products)) {
    ++$count;
  }
  return $count;
}

// Get categories for product..full links or id numbers..
function mc_getProductCategories($id, $name = true) {
  $cats = array();
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `catname`,`" . DB_PREFIX . "categories`.`id` AS `cid` FROM `" . DB_PREFIX . "categories`
           LEFT JOIN `" . DB_PREFIX . "prod_category`
           ON `" . DB_PREFIX . "categories`.`id`            = `" . DB_PREFIX . "prod_category`.`category`
           WHERE `" . DB_PREFIX . "prod_category`.`product` = '{$id}'
           AND `enCat`                                  = 'yes'
           ORDER BY `" . DB_PREFIX . "categories`.`catname`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($C = mysqli_fetch_object($query)) {
    if ($name) {
      $cats[] = '<a href="?p=categories&amp;edit=' . $C->cid . '" title="' . mc_safeHTML($C->catname) . '">' . mc_cleanData($C->catname) . '</a>';
    } else {
      $cats[] = $C->cid;
    }
  }
  return (!empty($cats) ? ($name ? implode(', ', $cats) : $cats) : '');
}

// Get brands for product..full links or id numbers..
function mc_getProductBrands($id) {
  $brds = array();
  $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `brand` FROM `" . DB_PREFIX . "prod_brand`
           WHERE `product` = '{$id}'
           ORDER BY `id`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  while ($B = mysqli_fetch_object($q)) {
    $brds[] = $B->brand;
  }
  return $brds;
}

// Get product images folder..
function mc_getProductImagesFolder($product) {
  $PROD = mc_getTableData('products', 'id', $product);
  $FOLD = mc_getTableData('categories', 'id', $PROD->pCat);
  return (isset($FOLD->catFolder) && $FOLD->catFolder ? $FOLD->catFolder : 'products');
}

// Get stat counts..
function mc_getStatusStatCount($status, $sqlFD, $sqlTD) {
  $q_cnt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `p_count` FROM `" . DB_PREFIX . "sales`
           WHERE `paymentStatus`      = '{$status}'
           AND `purchaseDate`   BETWEEN '{$sqlFD}' AND '{$sqlTD}'
           AND `saleConfirmation`     = 'yes'
           ") or die(mc_MySQLError(__LINE__, __FILE__));
  $CNT = mysqli_fetch_object($q_cnt);
  return (isset($CNT->p_count) && $CNT->p_count > 0 ? number_format($CNT->p_count) : 0);
}

// Get category..
function mc_getCategoryName($id) {
  $S = mc_getTableData('categories', 'id', $id);
  return (isset($S->catname) ? mc_cleanData($S->catname) : '');
}

// Get shipping area..
function mc_getShippingArea($id) {
  $A = mc_getTableData('zone_areas', 'id', $id);
  return (isset($A->areaName) ? mc_cleanData($A->areaName) : '');
}

// Clean mp3 into readable text
function mc_mp3Clean($mp3) {
  if (strrpos($mp3, '.') !== false) {
    $mp3 = substr($mp3, 0, strrpos($mp3, '.'));
  }
  $mp3 = ucwords($mp3);
  return $mp3;
}

// Get physical/download goods count..
function mc_physDownCount($field) {
  if ($field == 'none') {
    return '0';
  } else {
    $sales = array_map('trim', explode(',', $field));
    return count($sales);
  }
}

// Get first product..
function mc_getFirstProduct() {
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "products` ORDER BY `id` LIMIT 1") or die(mc_MySQLError(__LINE__, __FILE__));
  $row = mysqli_fetch_object($query);
  return (isset($row->id) ? $row->id : 0);
}

// Clean prices..remove commas..
function mc_cleanInsertionPrice($price) {
  $price = str_replace(',', '', $price);
  return mc_formatPrice($price);
}

// Rate cleaner..strip percentage symbols..
function mc_rateCleaner($rate) {
  $rate = str_replace(array(
    '%'
  ), array(), $rate);
  return $rate;
}

// Server path to root folder..
function mc_uploadServerPath() {
  global $SETTINGS;
  if (!@is_dir($SETTINGS->serverPath)) {
    die('The following is <b>NOT</b> a valid server path for your installation: "' . $SETTINGS->serverPath . '". Please check and update your <a href="?p=settings">settings</a>.');
  }
  return $SETTINGS->serverPath . '/' . PRODUCTS_FOLDER . '/';
}

// Display box if action is done..
function mc_actionCompleted($text) {
  global $msg_script8;
  if (!ENABLE_SYSTEM_MESSAGES) {
    return '';
  }
  return '
  <div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <i class="fa fa-check fa-fw"></i> ' . $text . '
  </div>
  ';
}

// Display box if action is done..for errors..
function mc_actionCompletedError($text) {
  global $msg_script8;
  if (!ENABLE_SYSTEM_MESSAGES) {
    return '';
  }
  return '
  <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <i class="fa fa-warning fa-fw"></i> ' . $text . '
  </div>
  ';
}

// Display box if action is done for products..
function mc_actionCompletedProducts($text, $id) {
  global $msg_script8, $msg_productadd22, $msg_productadd23, $msg_productadd24, $msg_productadd52, $msg_productadd27, $msg_productadd28, $msg_productadd47, $msg_productadd48, $msg_productadd51;
  if (!ENABLE_SYSTEM_MESSAGES) {
    return '';
  }
  return '
  <div id="actionComplete">
    <p>' . $text . ' (<a href="#" onclick="closeThisDiv(\'actionComplete\');return false" title="' . mc_cleanDataEntVars($msg_script8) . '">' . $msg_script8 . '</a>)</p>
    <div class="menuBar">
      <ul>
        <li class="start">' . $msg_productadd52 . ':</li>
        <li><a href="?p=add-product&amp;edit=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd22) . '">' . $msg_productadd22 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=add-product&amp;copy=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd23) . '">' . $msg_productadd23 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=product-attributes&amp;product=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd28) . '">' . $msg_productadd28 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=product-pictures&amp;product=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd27) . '">' . $msg_productadd27 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=product-related&amp;product=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd47) . '">' . $msg_productadd47 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=product-mp3&amp;product=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd48) . '">' . $msg_productadd48 . '</a>&nbsp;&nbsp;&nbsp;/ </li>
        <li><a href="?p=product-personalisation&amp;product=' . $id . '" title="' . mc_cleanDataEntVars($msg_productadd51) . '">' . $msg_productadd51 . '</a></li>
      </ul>
    </div>
  </div>
  ';
}

?>