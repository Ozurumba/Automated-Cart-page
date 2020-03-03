<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/categories.php');

// Remove icon..
if (isset($_GET['removeIcon']) && ctype_digit($_GET['removeIcon'])) {
  $MCCAT->resetCategoryIcon();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Adjust order..
if (isset($_POST['process_order'])) {
  $MCCAT->reOrderCategories();
  $OK4 = true;
}

// Add category..
if (isset($_POST['process']) && $_POST['catname']) {
  $MCCAT->addCat();
  $OK = true;
}

// Update category..
if (isset($_POST['update']) && $_POST['catname']) {
  $MCCAT->updateCat();
  $OK2 = true;
}

// Delete category..
if (isset($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCCAT->deleteCat();
  $OK3 = true;
}

$pageTitle     = $msg_header2 . ': ' . $pageTitle;
$loadiBox   = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/categories.php');
include(PATH . 'templates/footer.php');

?>
