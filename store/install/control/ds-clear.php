<?php

if (!defined('INSTALL_DIR')) {
  exit;
}

//=============================
// REMOVE DEMO IMAGES
//=============================

if (is_dir(INSTALL_DIR . PRODUCTS_FOLDER . '/demo')) {
  $dir = opendir(INSTALL_DIR . PRODUCTS_FOLDER . '/demo');
  while (false !== ($read = readdir($dir))) {
    if (substr($read,0,4) == 'img_') {
      @unlink(INSTALL_DIR . PRODUCTS_FOLDER . '/demo/' . $read);
    }
    if (substr($read,0,4) == 'tmb_') {
      @unlink(INSTALL_DIR . PRODUCTS_FOLDER . '/demo/' . $read);
    }
  }
  closedir($dir);
}

//=============================
// REMOVE ICONS / GIFT IMG
//=============================

if (is_dir(INSTALL_DIR . PRODUCTS_FOLDER)) {
  $dir = opendir(INSTALL_DIR . PRODUCTS_FOLDER);
  while (false !== ($read = readdir($dir))) {
    if (substr($read,0, 9) == 'demo_icon' || substr($read,0, 6) == 'demo_g') {
      @unlink(INSTALL_DIR . PRODUCTS_FOLDER . '/' . $read);
    }
  }
  closedir($dir);
}

//=============================
// ANYTHING ELSE?
//=============================

if (is_dir(INSTALL_DIR . PRODUCTS_FOLDER . '/demo')) {
  $dir = opendir(INSTALL_DIR . PRODUCTS_FOLDER . '/demo');
  while (false !== ($read = readdir($dir))) {
    if (is_file($read)) {
      @unlink(INSTALL_DIR . PRODUCTS_FOLDER . '/demo/' . $read);
    }
  }
  closedir($dir);
}

//=============================
// REMOVE DEMO DIRECTORY
//=============================

@rmdir(INSTALL_DIR . PRODUCTS_FOLDER . '/demo');

?>