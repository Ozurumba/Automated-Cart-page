<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/brands.php');

// Add brand..
if (isset($_POST['process']) && !empty($_POST['bCat'])) {
  $name  = $_FILES['file']['name'];
  $temp  = $_FILES['file']['tmp_name'];
  if ($name && $temp) {
    if (!file_exists($temp)) {
      die('Temp file "'.$temp.'" does not exist and could not be read. This is a server error, usually caused by permissions.<br><br>
           Check the system can access your tmp/ directory. Please revert to single entry if this persists.');
    }
    $count  = $MCCAT->addBatchBrands($name,$temp);
    $OKB    = true;
  } else {
    if ($_POST['name']) {
      $MCCAT->addBrand();
      $OK  = true;
    }
  }
}

// Update brand..
if (isset($_POST['update']) && $_POST['name'] && !empty($_POST['bCat'])) {
  $MCCAT->updateBrand();
  $OK2 = true;
}

// Delete brand..
if (isset($_POST['process_del']) && $uDel == 'yes') {
  $cnt = $MCCAT->deleteBrands();
  $OK3 = true;
}

$pageTitle   = $msg_header10 . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/brands.php');
include(PATH . 'templates/footer.php');

?>
