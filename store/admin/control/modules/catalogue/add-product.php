<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'sales/sales-update.php');

// Product expiry options
if (isset($_GET['prod_expiry'])) {
  if (isset($_POST['e_text'])) {
    $MCPROD->prodExpiryOpts();
    echo $JSON->encode(array(
      'OK'
    ));
    exit;
  }
  include(PATH . 'templates/windows/product-expiry-options.php');
  exit;
}

// Code check..
if (isset($_GET['codeCheck'])) {
  $CHECK = mc_getTableData('products', 'pCode', mc_safeSQL(trim($_GET['codeCheck'])), (isset($_GET['edit']) ? ' AND id != \'' . (int) $_GET['edit'] . '\'' : ''));
  echo $JSON->encode(array(
    (isset($CHECK->id) ? 'exists' : 'ok')
  ));
  exit;
}

// ISBN Lookup..
if (isset($_GET['isbnLookup'])) {
  $json = $ISBN->isbnLookup();
  echo $JSON->encode(array(
    'name' => $json[0],
    'short_desc' => $json[1],
    'full_desc' => $json[2],
    'text' => $json[3]
  ));
  exit;
}

// Batch update field selection..
if (isset($_GET['batchRoutines'])) {
  $data = explode('||', $_GET['batchRoutines']);
  if (!isset($_SESSION['batchFieldPrefs'])) {
    $_SESSION['batchFieldPrefs'] = array();
    if ($data[0] == 'exclude') {
      $_SESSION['batchFieldPrefs'][] = $data[2];
    }
  } else {
    switch($data[0]) {
      case 'include':
        if (in_array($data[2], $_SESSION['batchFieldPrefs'])) {
          // Flip array..
          $flip = array_flip($_SESSION['batchFieldPrefs']);
          // Remove key..
          unset($flip[$data[2]]);
          // Re-flip..
          $flip                        = array_flip($flip);
          // Update session array..
          $_SESSION['batchFieldPrefs'] = $flip;
        }
        break;
      case 'exclude':
        if (!in_array($data[2], $_SESSION['batchFieldPrefs'])) {
          $_SESSION['batchFieldPrefs'][] = $data[2];
        }
        break;
    }
  }
  echo $JSON->encode(array(
    $data[0]
  ));
  exit;
}

// Load local files..
if (isset($_GET['showLocalFiles'])) {
  $mCache = $MCCACHE->cache_options['cache_dir'] . '/ad-local-file-dir' . $MCCACHE->cache_options['cache_ext'];
  // If list is cached, return cache list..
  if ($MCCACHE->cache_options['cache_enable'] == 'yes' && @file_exists($mCache) && $MCCACHE->cache_exp($MCCACHE->cache_time($mCache)) == 'load') {
    echo $JSON->encode(array(
      mc_loadTemplateFile($mCache)
    ));
    exit;
  }
  $dString = '';
  if (@is_dir($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder)) {
    $options = mc_downloadDirScanner($SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder . '/', $SETTINGS->globalDownloadPath . '/' . $SETTINGS->downloadFolder);
    if ($options) {
      $MCCACHE->cache_file($mCache, '<option value="0">- - - - - - - - </option>' . mc_defineNewline() . $options);
      $dString = '<option value="0">- - - - - - - - </option>' . mc_defineNewline() . $options;
    } else {
      $dString = 'ERR';
    }
  } else {
    $dString = 'ERR';
  }
  echo $JSON->encode(array(
    $dString
  ));
  exit;
}

// Auto tags..
if (isset($_POST['create-tags'])) {
  // Generate tags..
  $tags = array();
  $txt  = explode(' ', $_POST['create-tags']);
  $txt  = array_map('trim', $txt);
  if (!empty($txt)) {
    foreach ($txt AS $words) {
      // Skip anything not containing letters..
      if (preg_match('%^[A-Za-z]+$%', $words)) {
        // Remove trailing commas/periods from words..
        if (substr($words, -1) == ',' || substr($words, -1) == '.') {
          $words = substr($words, 0, -1);
        }
        if (AUTO_TAGS_TEXT_LIMIT > 0) {
          if (strlen($words) >= AUTO_TAGS_TEXT_LIMIT) {
            $tags[] = (CAPITALISE_TAGS ? ucfirst(strtolower($words)) : strtolower($words));
          }
        } else {
          $tags[] = (CAPITALISE_TAGS ? ucfirst(strtolower($words)) : strtolower($words));
        }
      }
    }
  }
  // Remove duplicates..
  echo $JSON->encode(array(
    'message' => 'auto-create_tags',
    'text' => (!empty($tags) ? implode(', ', array_unique($tags)) : ' '),
    'field' => mc_safeHTML($_POST['field'])
  ));
  exit;
}

// Add product
if (isset($_POST['process'])) {
  if ($_POST['pName']) {
    $newProductID = $MCPROD->addNewProduct();
    $OK           = true;
  }
}

// Batch update products..
if (isset($_POST['productIDs']) && isset($_POST['productsUpdated']) && $_POST['productsUpdated'] > 0) {
  $fields = $MCPROD->batchUpdateProducts();
  $OK3    = true;
}

// Update product..
if (isset($_POST['update'])) {
  if ($_POST['pName']) {
    $MCPROD->updateProduct();
    $OK2 = true;
  }
}

// Batch delete
if (isset($_POST['delproducts']) && $uDel == 'yes') {
  foreach ($_POST['productIDs'] AS $sID) {
    $MCPROD->deleteProduct($sID);
  }
  header("Location: index.php?p=manage-products&deleted=" . count($_POST['productIDs']));
  exit;
}

$pageTitle    = (isset($_GET['edit']) ? $msg_productadd15 : mc_cleanDataEntVars($msg_javascript26)) . ': ' . $pageTitle;
$createFolder = true;
$loadiBox  = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-add.php');
include(PATH . 'templates/footer.php');

?>