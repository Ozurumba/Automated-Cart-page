<?php

class cats {

  public $settings;
  public $cache;

  // Re-order..
  public function reOrderCategories() {
    if (!empty($_POST['p'])) {
      for ($i = 0; $i < count($_POST['p']); $i++) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
        `orderBy`     = '{$_POST['parentOrder'][$i]}'
        WHERE `id`    = '{$_POST['p'][$i]}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    if (!empty($_POST['c'])) {
      for ($i = 0; $i < count($_POST['c']); $i++) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
        `orderBy`     = '{$_POST['childOrder'][$i]}'
        WHERE `id`    = '{$_POST['c'][$i]}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    if (!empty($_POST['i'])) {
      for ($i = 0; $i < count($_POST['i']); $i++) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
        `orderBy`     = '{$_POST['infantOrder'][$i]}'
        WHERE `id`    = '{$_POST['i'][$i]}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
  }

  // Create picture folder..
  public function createCategoryFolder($folder) {
    $chmod  = (CHMOD_VALUE ? CHMOD_VALUE : 0777);
    $status = 'error';
    if (@is_dir($this->settings->serverPath . '/' . PRODUCTS_FOLDER) && @is_writeable($this->settings->serverPath . '/' . PRODUCTS_FOLDER)) {
      if (!@is_dir($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $folder)) {
        $oldumask = @umask(0);
        @mkdir($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $folder, $chmod);
        @umask($oldumask);
        if (@is_dir($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $folder)) {
          return 'ok';
        }
      }
    }
    return $status;
  }

  // Batch Add brands..
  public function addBatchBrands($name, $temp) {
    $_POST = mc_safeImport($_POST);
    $F     = array_map('trim', file($temp));
    foreach ($F AS $brandName) {
      if (in_array('all', $_POST['bCat'])) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "brands` (
        `name`,
        `bCat`,
        `enBrand`
        ) VALUES (
        '" . mc_safeSQL(substr($brandName, 0, 250)) . "',
        'all',
        '" . (isset($_POST['enBrand']) && in_array($_POST['enBrand'], array(
          'yes',
          'no'
        )) ? $_POST['enBrand'] : 'yes') . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      } else {
        for ($i = 0; $i < count($_POST['bCat']); $i++) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "brands` (
          `name`,
          `bCat`,
          `enBrand`
          ) VALUES (
          '" . mc_safeSQL(substr($brandName, 0, 250)) . "',
          '{$_POST['bCat'][$i]}',
          '" . (isset($_POST['enBrand']) && in_array($_POST['enBrand'], array(
            'yes',
            'no'
          )) ? $_POST['enBrand'] : 'yes') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    @unlink($temp);
    return count($F);
  }

  // Add brands..
  public function addBrand() {
    $_POST = mc_safeImport($_POST);
    if (in_array('all', $_POST['bCat'])) {
      if ($_POST['name'] != '') {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "brands` (
        `name`,
        `bCat`,
        `enBrand`
        ) VALUES (
        '{$_POST['name']}',
        'all',
        '" . (isset($_POST['enBrand']) && in_array($_POST['enBrand'], array(
          'yes',
          'no'
        )) ? $_POST['enBrand'] : 'yes') . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    } else {
      for ($i = 0; $i < count($_POST['bCat']); $i++) {
        if ($_POST['name'] != '') {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "brands` (
          `name`,
          `bCat`,
          `enBrand`
          ) VALUES (
          '{$_POST['name']}',
          '{$_POST['bCat'][$i]}',
          '" . (isset($_POST['enBrand']) && in_array($_POST['enBrand'], array(
            'yes',
            'no'
          )) ? $_POST['enBrand'] : 'yes') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
  }

  // Update brand..
  public function updateBrand() {
    $_POST = mc_safeImport($_POST);
    if ($_POST['name']) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "brands` SET
      `name`      = '{$_POST['name']}',
      `bCat`      = '{$_POST['bCat'][0]}',
      `enBrand`   = '" . (isset($_POST['enBrand']) && in_array($_POST['enBrand'], array(
        'yes',
        'no'
      )) ? $_POST['enBrand'] : 'yes') . "'
      WHERE `id`  = '" . mc_digitSan($_POST['update']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  // Delete brand..
  public function deleteBrands() {
    $rows  = 0;
    $rows2 = 0;
    if (!empty($_POST['delcats'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "brands`
      WHERE `bCat` IN(" . implode(',', $_POST['delcats']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    }
    if (!empty($_POST['delete'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "brands`
      WHERE `id` IN(" . implode(',', $_POST['delete']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      $rows2 = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    }
    mc_tableTruncationRoutine(array(
      'brands'
    ));
    return ($rows + $rows2);
  }

  // Add category..
  public function addCat() {
    $_POST = mc_safeImport($_POST);
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('categories') + 1 > RESTR_CATS) {
        mc_restrictionLimitRedirect();
      }
    }
    $_POST['comments'] = mc_cleanBBInput($_POST['comments']);
    $vis               = (!empty($_POST['vis']) ? mc_safeSQL(implode(',', $_POST['vis'])) : 'public');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "categories` (
    `catname`,
    `titleBar`,
    `comments`,
    `catLevel`,
    `childOf`,
    `metaDesc`,
    `metaKeys`,
    `enCat`,
    `orderBy`,
    `enDisqus`,
    `freeShipping`,
    `showRelated`,
    `rwslug`,
    `theme`,
    `vis`
    ) VALUES (
    '{$_POST['catname']}',
    '{$_POST['titleBar']}',
    '{$_POST['comments']}',
    '" . ($_POST['type'] == 'new' ? '1' : (substr($_POST['type'], 0, 5) == 'child' ? '3' : '2')) . "',
    '" . ($_POST['type'] == 'new' ? '0' : (substr($_POST['type'], 0, 5) == 'child' ? substr($_POST['type'], 6) : $_POST['type'])) . "',
    '{$_POST['metaDesc']}',
    '{$_POST['metaKeys']}',
    '" . (isset($_POST['enCat']) && in_array($_POST['enCat'], array(
      'yes',
      'no'
    )) ? $_POST['enCat'] : 'yes') . "',
    '" . ($_POST['type'] == 'new' ? '9999' : '0') . "',
    '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
      'yes',
      'no'
    )) ? $_POST['enDisqus'] : 'no') . "',
    '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
      'yes',
      'no'
    )) ? $_POST['freeShipping'] : 'no') . "',
    '" . (isset($_POST['showRelated']) && in_array($_POST['showRelated'], array(
      'yes',
      'no'
    )) ? $_POST['showRelated'] : 'yes') . "',
    '{$_POST['rwslug']}',
    '{$_POST['theme']}',
    '{$vis}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $id  = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Is there an icon..
    $img = $_FILES['icon']['name'];
    $tmp = $_FILES['icon']['tmp_name'];
    if ($img && $tmp) {
      $ext = substr(strrchr(strtolower($img), '.'), 1);
      $ico = 'icon_' . $id . '.' . $ext;
      if (is_uploaded_file($tmp)) {
        move_uploaded_file($tmp, $this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $ico);
        if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $ico)) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
          `imgIcon`   = '{$ico}'
          WHERE `id`  = '{$id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    // If 'all' is selected form homepage cats, add it on the end..
    if (substr($this->settings->homeProdCats, 0, 3) == 'all') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "settings` SET
      `homeProdCats` = CONCAT(`homeProdCats`,',{$id}')
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
  }

  // Reset Icon..
  public function resetCategoryIcon() {
    if ($_GET['icon'] && @file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_GET['icon'])) {
      @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_GET['icon']);
    }
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
    `imgIcon`   = ''
    WHERE `id`  = '" . mc_digitSan($_GET['removeIcon']) . "'
    ");
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
  }

  // Update category..
  public function updateCat() {
    $_POST = mc_safeImport($_POST);
    $cIcon = $_POST['icon'];
    // Is there an icon..
    $img   = $_FILES['icon']['name'];
    $tmp   = $_FILES['icon']['tmp_name'];
    if ($img && $tmp) {
      $ext = substr(strrchr(strtolower($img), '.'), 1);
      $ico = 'icon_' . $_POST['update'] . '.' . $ext;
      if (is_uploaded_file($tmp)) {
        // Clear old..
        if ($_POST['icon'] && @file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_POST['icon'])) {
          @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $_POST['icon']);
        }
        move_uploaded_file($tmp, $this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $ico);
        if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $ico)) {
          $cIcon = $ico;
        } else {
          $cIcon = '';
        }
      }
    }
    $_POST['comments'] = mc_cleanBBInput($_POST['comments']);
    $newLevel          = ($_POST['type'] == 'new' ? '1' : (substr($_POST['type'], 0, 5) == 'child' ? '3' : '2'));
    $newChild          = ($_POST['type'] == 'new' ? '0' : (substr($_POST['type'], 0, 5) == 'child' ? substr($_POST['type'], 6) : $_POST['type']));
    $vis               = (!empty($_POST['vis']) ? mc_safeSQL(implode(',', $_POST['vis'])) : 'public');
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
    `catname`       = '{$_POST['catname']}',
    `titleBar`      = '{$_POST['titleBar']}',
    `comments`      = '{$_POST['comments']}',
    `catLevel`      = '" . ($_POST['type'] == 'new' ? '1' : (substr($_POST['type'], 0, 5) == 'child' ? '3' : '2')) . "',
    `childOf`       = '" . ($_POST['type'] == 'new' ? '0' : (substr($_POST['type'], 0, 5) == 'child' ? substr($_POST['type'], 6) : $_POST['type'])) . "',
    `metaDesc`      = '{$_POST['metaDesc']}',
    `metaKeys`      = '{$_POST['metaKeys']}',
    `enCat`         = '" . (isset($_POST['enCat']) && in_array($_POST['enCat'], array(
      'yes',
      'no'
    )) ? $_POST['enCat'] : 'yes') . "',
    `enDisqus`      = '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
      'yes',
      'no'
    )) ? $_POST['enDisqus'] : 'no') . "',
    `freeShipping`  = '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
      'yes',
      'no'
    )) ? $_POST['freeShipping'] : 'no') . "',
    `imgIcon`       = '{$cIcon}',
    `showRelated`   = '" . (isset($_POST['showRelated']) && in_array($_POST['showRelated'], array(
      'yes',
      'no'
    )) ? $_POST['showRelated'] : 'yes') . "',
    `rwslug`        = '{$_POST['rwslug']}',
    `theme`         = '{$_POST['theme']}',
    `vis`           = '{$vis}'
    WHERE `id`      = '" . mc_digitSan($_POST['update']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Adjust subs..
    if ($newLevel != $_POST['level']) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
           WHERE `childOf`  = '" . mc_digitSan($_POST['update']) . "'
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($C = mysqli_fetch_object($q)) {
        $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `childOf`  = '{$C->id}'
              ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($C2 = mysqli_fetch_object($q2)) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
          `catLevel`  = " . ($newLevel > $_POST['level'] ? ($newLevel == '3' ? '3' : '(`catLevel`+1)') : '(`catLevel`-1)') . ",
          `childOf`   = '" . ($newLevel == '3' ? ($_POST['type'] == 'new' ? '0' : (substr($_POST['type'], 0, 5) == 'child' ? substr($_POST['type'], 6) : $_POST['type'])) : $C->id) . "'
          WHERE `id`  = '{$C2->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
        `catLevel`  = " . ($newLevel > $_POST['level'] ? ($newLevel == '3' ? '3' : '(`catLevel`+1)') : '(`catLevel`-1)') . ",
        `childOf`   = '" . ($newLevel == '3' ? ($_POST['type'] == 'new' ? '0' : (substr($_POST['type'], 0, 5) == 'child' ? substr($_POST['type'], 6) : $_POST['type'])) : $C->childOf) . "'
        WHERE `id`  = '{$C->id}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
  }

  // Delete category..
  public function deleteCat() {
    $loopCats = array();
    $icons    = array();
    if ($_GET['icon']) {
      $icons[] = $_GET['icon'];
    }
    $_GET['del'] = mc_digitSan($_GET['del']);
    if (isset($_GET['parent']) && $_GET['parent'] == 'yes') {
      // Get children..
      $q_chi = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`imgIcon` FROM `" . DB_PREFIX . "categories`
               WHERE `childOf` = '" . mc_digitSan($_GET['del']) . "'
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($CH = mysqli_fetch_object($q_chi)) {
        $loopCats[] = $CH->id;
        if ($CH->imgIcon) {
          $icons[] = $CH->imgIcon;
        }
      }
      $loopCats[] = mc_digitSan($_GET['del']);
    } else {
      $loopCats[] = mc_digitSan($_GET['del']);
    }
    // Remove category..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "categories`
    WHERE `id`    IN(" . implode(',', $loopCats) . ")
    OR `childOf`  IN(" . implode(',', $loopCats) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $afrows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    // Remove icons..
    if (!empty($icons)) {
      foreach ($icons AS $iconImage) {
        if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $iconImage) && @is_writeable($this->settings->serverPath . '/' . PRODUCTS_FOLDER)) {
          @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . $iconImage);
        }
      }
    }
    // Remove brands..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "brands`
    WHERE `bCat` IN(" . implode(',', $loopCats) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Loop through products and remove all data..
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
    LEFT JOIN `" . DB_PREFIX . "prod_category`
    ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
    WHERE `" . DB_PREFIX . "prod_category`.`category`  IN(" . implode(',', $loopCats) . ")
    GROUP BY `" . DB_PREFIX . "prod_category`.`product`
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PROD = mysqli_fetch_object($query)) {
      // Remove product images..
      $q_pic = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "pictures`
               WHERE `product_id` = '{$PROD->pid}'
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_pic) > 0) {
        while ($IMG = mysqli_fetch_object($q_pic)) {
          if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->picture_path)) {
            @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->picture_path);
          }
          if (@file_exists($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->thumb_path)) {
            @unlink($this->settings->serverPath . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->thumb_path);
          }
          mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "pictures`
          WHERE `id` = '{$IMG->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      // Remove mp3 data..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "mp3` WHERE `product_id` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      // Remove personalisation..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "personalisation` WHERE `productID` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers` WHERE `productID` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      // Remove relation..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_relation` WHERE `product` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      // Remove attributes..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attributes` WHERE `productID` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attr_groups` WHERE `productID` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
      // Remove product info..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "products` WHERE `id` = '{$PROD->pid}'") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Remove product category info..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_category` WHERE `category`  IN(" . implode(',', $loopCats) . ")") or die(mc_MySQLError(__LINE__, __FILE__));
    // Truncate if tables are empty..
    mc_tableTruncationRoutine(array(
      'categories',
      'brands',
      'products',
      'prod_category',
      'pictures',
      'mp3',
      'personalisation',
      'purch_pers',
      'prod_relation',
      'attributes',
      'attr_groups'
    ));
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    return $afrows;
  }

}

?>