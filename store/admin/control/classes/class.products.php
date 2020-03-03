<?php

class products {

  public $settings;
  public $dl;
  public $cache;

  public function prodExpiryOpts() {
    $ID = (int) $_GET['prod_expiry'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `exp_price`    = '" . (isset($_POST['e_price']) ? mc_safeSQL($_POST['e_price']) : '') . "',
    `exp_special`  = '" . (isset($_POST['e_special']) && in_array($_POST['e_special'], array('yes','no')) ? $_POST['e_special'] : 'no') . "',
    `exp_send`     = '" . (isset($_POST['e_send']) && in_array($_POST['e_send'], array('yes','no')) ? $_POST['e_send'] : 'no') . "',
    `exp_text`     = '" . (isset($_POST['e_text']) ? mc_safeSQL($_POST['e_text']) : '') . "'
    WHERE `id`     = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function exportStockOverviewToCSV() {
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import/export routines. Please update!');
    }
    $csvFile = PATH . 'import/stock-' . date('dmYHis') . '.csv';
    $sep     = ',';
    $build   = '';
    $SQL     = '';
    if (isset($_GET['export']) && $_GET['export'] > 0) {
      $SQL = 'AND `category` = \'' . mc_digitSan($_GET['export']) . '\'';
    }
    $q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
           `" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
           LEFT JOIN `" . DB_PREFIX . "prod_category`
           ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
           WHERE `pEnable` = 'yes'
           $SQL
           GROUP BY `" . DB_PREFIX . "products`.`id`
           ORDER BY `pStock` DESC,`pName`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PROD = mysqli_fetch_object($q_p)) {
      $build .= mc_cleanCSV(IN_STOCK_PREFIX_PRODUCTS . $PROD->pName, $sep) . $sep;
      $build .= mc_cleanCSV($PROD->pStock, $sep) . mc_defineNewline();
      $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
                    WHERE `productID` = '{$PROD->pid}'
                    ORDER BY `orderBy`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($AG = mysqli_fetch_object($q_products)) {
        $q_a = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
               WHERE `attrGroup` = '{$AG->id}'
               ORDER BY `orderBy`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($ATTR = mysqli_fetch_object($q_a)) {
          $build .= mc_cleanCSV(IN_STOCK_PREFIX_ATTRIBUTES . $AG->groupName . '/' . $ATTR->attrName, $sep) . $sep;
          $build .= mc_cleanCSV($ATTR->attrStock, $sep) . mc_defineNewline();
        }
      }
    }
    if ($build) {
      $this->dl->write($csvFile, trim($build));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    } else {
      header("Location: ?p=stock-overview");
    }
    exit;
  }

  public function updateSingleProductStock($id, $stock) {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pStock` = '{$stock}'
    WHERE `id` = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updateProductStock() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $stock = (int) $_POST['stock'];
    if (!empty($_POST['prod'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
      `pStock` = '{$stock}'
      WHERE `id` IN(" . implode(',', $_POST['prod']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    if (!empty($_POST['attr'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
      `attrStock` = '{$stock}'
      WHERE `id` IN(" . implode(',', $_POST['attr']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function batchUpdateProductsFromCSV($lines, $del, $enc) {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    // Upload CSV file..
    $fields = array(
      'codeID',
      'pName',
      'pMetaKeys',
      'pMetaDesc',
      'pTags',
      'pDescription',
      'pShortDescription',
      'pCode',
      'pDownload',
      'pDownloadPath',
      'pEnable',
      'pVideo',
      'pVideo2',
      'pVideo3',
      'pMultiBuy',
      'countryRestrictions',
      'freeShipping',
      'pPurchase',
      'pStock',
      'pWeight',
      'pPrice',
      'pPurPrice',
      'minPurchaseQty',
      'maxPurchaseQty',
      'pOffer',
      'pOfferExpiry',
      'rwslug',
      'expiry',
      'pCube',
      'pGuardian',
      'dropshipping'
    );
    $count  = 0;
    $head   = array();
    $point  = 0;
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
      $handle = fopen($_FILES['file']['tmp_name'], 'r');
      while (($CSV = fgetcsv($handle, $lines, $del, $enc)) !== FALSE) {
        ++$point;
        // For point 1, build header..
        if ($point == 1) {
          for ($i = 0; $i < count($CSV); $i++) {
            if (in_array($CSV[$i], $fields)) {
              $head[$CSV[$i]] = $i;
            }
          }
        } else {
          $sql = array();
          if (!empty($head)) {
            // Check whats being updated..
            $updateID = '';
            foreach ($fields AS $f) {
              $data = (isset($head[$f]) && isset($CSV[$head[$f]]) && $CSV[$head[$f]] ? trim($CSV[$head[$f]]) : '');
              switch($f) {
                case 'codeID':
                  $updateID = mc_safeSQL($CSV[$head['codeID']]);
                  break;
                case 'pStock':
                case 'minPurchaseQty':
                case 'maxPurchaseQty':
                case 'pMultiBuy':
                case 'pCube':
                case 'pGuardian':
                case 'dropshipping':
                  if ($data != '') {
                    $sql[] = "`{$f}` = '" . (int) $data . "'";
                  }
                  break;
                case 'countryRestrictions':
                  if ($data != '') {
                    $sz    = serialize(explode(',', preg_replace('/[^0-9,]/', '', $data)));
                    $sql[] = "`{$f}` = '" . mc_safeSQL($sz) . "'";
                  }
                  break;
                case 'pDownload':
                case 'pEnable':
                case 'freeShipping':
                case 'pPurchase':
                  if ($data != '') {
                    $vl = strtolower($data);
                    if (in_array($vl, array('yes','no'))) {
                      $sql[] = "`{$f}` = '{$vl}'";
                    }
                  }
                  break;
                case 'pOffer':
                case 'pPrice':
                case 'pPurPrice':
                  if ($data != '') {
                    $sql[] = "`{$f}` = '" . mc_cleanInsertionPrice($data) . "'";
                  }
                  break;
                case 'pOfferExpiry':
                case 'expiry':
                  if (strtotime($data) > 0) {
                    $sql[] = "`{$f}` = '" . mc_safeSQL($data) . "'";
                  }
                  break;
                default:
                  if ($data != '') {
                    $sql[] = "`{$f}` = '" . mc_safeSQL($data) . "'";
                  }
                  break;
              }
            }
            // Update product..
            if (!empty($sql) && $updateID) {
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
              " . implode(',', $sql) . "
              WHERE (`id` = '{$updateID}' OR `pCode` = '{$updateID}')
              ") or die(mc_MySQLError(__LINE__, __FILE__));
              // Was anything updated?
              if (mysqli_affected_rows($GLOBALS["___msw_sqli"]) > 0) {
                ++$count;
              }
            }
          } else {
            return '0';
          }
        }
      }
      fclose($handle);
    }
    // Remove temporary file..
    if (file_exists($_FILES['file']['tmp_name'])) {
      @unlink($_FILES['file']['tmp_name']);
    }
    return $count;
  }

  public function updateProductNotes() {
    $ID = (int) $_GET['notes'];
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pNotes`    = '" . mc_safeSQL($_POST['notes']) . "'
    WHERE `id`  = '{$ID}'
    ");
  }

  public function exportProductsToCSV() {
    global $msg_productadd4, $msg_productadd18, $msg_productadd19, $msg_productadd11, $msg_productadd6,
    $msg_productadd64, $msg_productadd7, $msg_productadd43, $msg_productadd42, $msg_productadd44,
    $msg_productadd39, $msg_productadd9, $msg_productadd84, $msg_productadd29, $msg_newpages31,
    $msg_productadd79, $msg_productadd88, $msg_productadd75, $msg_productadd32, $msg_admin_product3_0,
    $msg_productadd40, $msg_admin_product3_0;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import/export routines. Please update!');
    }
    $csvFile         = PATH . 'import/products-' . date('dmYHis') . '.csv';
    $build           = '';
    $header          = array();
    $sep             = ',';
    $_POST['fields'] = (empty($_POST['fields']) ? array() : $_POST['fields']);
    if (in_array('pName', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd4, $sep);
    }
    if (in_array('pTitle', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd75, $sep);
    }
    if (in_array('pBrands', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd32, $sep);
    }
    if (in_array('pMetaKeys', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd18, $sep);
    }
    if (in_array('pMetaDesc', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd19, $sep);
    }
    if (in_array('pTags', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd11, $sep);
    }
    if (in_array('rwslug', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_newpages31, $sep);
    }
    if (in_array('pDescription', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd6, $sep);
    }
    if (in_array('pShortDescription', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd64, $sep);
    }
    if (in_array('pCode', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd7, $sep);
    }
    if (in_array('pStock', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd43, $sep);
    }
    if (in_array('minPurchaseQty', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd79, $sep);
    }
    if (in_array('maxPurchaseQty', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd88, $sep);
    }
    if (in_array('pWeight', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd42, $sep);
    }
    if (in_array('pPrice', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd44, $sep);
    }
    if (in_array('pPurPrice', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[15], $sep);
    }
    if (in_array('pOffer', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd39, $sep);
    }
    if (in_array('pOfferExpiry', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd40, $sep);
    }
    if (in_array('pDownloadPath', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd9, $sep);
    }
    if (in_array('pVisits', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_productadd84, $sep);
    }
    if (in_array('pVideo', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[6], $sep);
    }
    if (in_array('pVideo2', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[7], $sep);
    }
    if (in_array('pVideo3', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[8], $sep);
    }
    if (in_array('expiry', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[0], $sep);
    }
    if (in_array('dropshipping', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[2], $sep);
    }
    if (in_array('pInsurance', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[9], $sep);
    }
    if (in_array('pDateAdded', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[10], $sep);
    }
    if (in_array('pCube', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[11], $sep);
    }
    if (in_array('pGuardian', $_POST['fields']) || empty($_POST['fields'])) {
      $header[] = mc_cleanCSV($msg_admin_product3_0[17], $sep);
    }
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
         LEFT JOIN `" . DB_PREFIX . "prod_category`
         ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
         " . (!empty($_POST['category']) ? 'AND `' . DB_PREFIX . 'prod_category`.`category` IN(' . mc_safeSQL(implode(',', $_POST['category'])) . ')' : '') . "
         GROUP BY `" . DB_PREFIX . "prod_category`.`product`
         ORDER BY `pName`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PROD = mysqli_fetch_object($q)) {
      $data = array();
      if (in_array('pName', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pName, $sep);
      }
      if (in_array('pTitle', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pTitle, $sep);
      }
      if (in_array('pBrands', $_POST['fields']) || empty($_POST['fields'])) {
        $cat = array();
        $qct = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `category` FROM `" . DB_PREFIX . "prod_category`
               WHERE `product` = '{$PROD->pid}'
               ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($CT = mysqli_fetch_object($qct)) {
          $cat[] = $CT->category;
        }
        if (!empty($cat)) {
          $brands = array();
          $qbr = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `name` FROM `" . DB_PREFIX . "brands`
                 WHERE (`bCat` = 'all'
                  OR `bCat` IN(" . mc_safeSQL(implode(',',$cat)) .")
                 )
                 ORDER BY `name`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
          while ($BD = mysqli_fetch_object($qbr)) {
            $brands[] = $BD->name;
          }
        }
        $data[] = mc_cleanCSV((!empty($brands) ? mc_cleanData(implode(', ', $brands)) : ''), $sep);
      }
      if (in_array('pMetaKeys', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pMetaKeys, $sep);
      }
      if (in_array('pMetaDesc', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pMetaDesc, $sep);
      }
      if (in_array('pTags', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pTags, $sep);
      }
      if (in_array('rwslug', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->rwslug, $sep);
      }
      if (in_array('pDescription', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pDescription, $sep);
      }
      if (in_array('pShortDescription', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pShortDescription, $sep);
      }
      if (in_array('pCode', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pCode, $sep);
      }
      if (in_array('pStock', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pStock, $sep);
      }
      if (in_array('minPurchaseQty', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->minPurchaseQty, $sep);
      }
      if (in_array('maxPurchaseQty', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->maxPurchaseQty, $sep);
      }
      if (in_array('pWeight', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pWeight, $sep);
      }
      if (in_array('pPrice', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pPrice, $sep);
      }
      if (in_array('pPurPrice', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pPurPrice, $sep);
      }
      if (in_array('pOffer', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pOffer, $sep);
      }
      if (in_array('pOfferExpiry', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pOfferExpiry, $sep);
      }
      if (in_array('pDownloadPath', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pDownloadPath, $sep);
      }
      if (in_array('pVisits', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pVisits, $sep);
      }
      if (in_array('pVideo', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pVideo, $sep);
      }
      if (in_array('pVideo2', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pVideo2, $sep);
      }
      if (in_array('pVideo3', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pVideo3, $sep);
      }
      if (in_array('expiry', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->expiry, $sep);
      }
      if (in_array('dropshipping', $_POST['fields']) || empty($_POST['fields'])) {
        $dship  = mc_getTableData('dropshippers','id',mc_digitSan($PROD->dropshipping));
        $data[] = mc_cleanCSV((isset($dship->name) ? mc_cleanData($dship->name) : ''), $sep);
      }
      if (in_array('pInsurance', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pInsurance, $sep);
      }
      if (in_array('pDateAdded', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pDateAdded, $sep);
      }
      if (in_array('pCube', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pCube, $sep);
      }
      if (in_array('pGuardian', $_POST['fields']) || empty($_POST['fields'])) {
        $data[] = mc_cleanCSV($PROD->pGuardian, $sep);
      }
      if (!empty($data)) {
        $build .= implode($sep, $data) . mc_defineNewline();
      }
    }
    if ($build) {
      $headerRow = implode($sep, $header) . mc_defineNewline();
      $this->dl->write($csvFile, trim($headerRow . $build));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    } else {
      return 'none';
    }
  }

  public function batchUpdateStockFromCSV() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $count = 0;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import/export routines. Please update!');
    }
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
      move_uploaded_file($_FILES['file']['tmp_name'], PATH . 'import/' . $_FILES['file']['name']);
      if (file_exists(PATH . 'import/' . $_FILES['file']['name'])) {
        $lines  = ($_POST['lines'] ? str_replace(array(
          '.',
          ','
        ), array(), mc_cleanData($_POST['lines'])) : '0');
        $del    = ($_POST['del'] ? mc_cleanData($_POST['del']) : ',');
        $enc    = ($_POST['enc'] ? mc_cleanData($_POST['enc']) : '"');
        $csv    = array();
        $handle = fopen(PATH . 'import/' . $_FILES['file']['name'], 'r');
        if ($handle) {
          while (($data = fgetcsv($handle, $lines, $del, $enc)) !== FALSE) {
            $code  = (isset($data[0]) ? $data[0] : '');
            $stock = (isset($data[1]) ? (int) $data[1] : '0');
            if ($code) {
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
              `pStock`       = '{$stock}'
              WHERE `pCode`  = '{$code}'
              ");
              $count = ($count + mysqli_affected_rows($GLOBALS["___msw_sqli"]));
            }
          }
        }
        fclose($handle);
      }
      @unlink(PATH . 'import/' . $_FILES['file']['name']);
      if (file_exists($_FILES['file']['tmp_name'])) {
        @unlink($_FILES['file']['tmp_name']);
      }
    }
    return $count;
  }

  public function batchUpdatePricesFromCSV() {
    $count = 0;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import/export routines. Please update!');
    }
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
      move_uploaded_file($_FILES['file']['tmp_name'], PATH . 'import/' . $_FILES['file']['name']);
      if (file_exists(PATH . 'import/' . $_FILES['file']['name'])) {
        $lines  = ($_POST['lines'] ? str_replace(array(
          '.',
          ','
        ), array(), mc_cleanData($_POST['lines'])) : '0');
        $del    = ($_POST['del'] ? mc_cleanData($_POST['del']) : ',');
        $enc    = ($_POST['enc'] ? mc_cleanData($_POST['enc']) : '"');
        $csv    = array();
        $handle = fopen(PATH . 'import/' . $_FILES['file']['name'], 'r');
        if ($handle) {
          while (($data = fgetcsv($handle, $lines, $del, $enc)) !== FALSE) {
            $code  = (isset($data[0]) ? mc_safeSQL($data[0]) : '');
            $offer = (isset($data[1]) ? mc_cleanInsertionPrice($data[1]) : '0.00');
            $price = (isset($data[2]) ? mc_cleanInsertionPrice($data[2]) : '0.00');
            $cost  = (isset($data[3]) ? mc_cleanInsertionPrice($data[3]) : '0.00');
            if ($code) {
              mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
              `pPrice`       = '{$price}',
              `pOffer`       = '{$offer}',
              `pPurPrice`    = '{$cost}'
              WHERE `pCode`  = '{$code}'
              ");
              $count = ($count + mysqli_affected_rows($GLOBALS["___msw_sqli"]));
            }
          }
        }
        fclose($handle);
      }
      @unlink(PATH . 'import/' . $_FILES['file']['name']);
      if (file_exists($_FILES['file']['tmp_name'])) {
        @unlink($_FILES['file']['tmp_name']);
      }
    }
    return $count;
  }

  public function changeProductStatus($status) {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pEnable`   = '{$status}'
    WHERE `id`  = '" . mc_digitSan($_GET['changeStatus']) . "'
    ");
  }

  public function updateProductStatuses() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    // Check vars..
    $action1 = (isset($_POST['action_1']) && in_array($_POST['action_1'], array(
      'enable',
      'disable'
    )) ? $_POST['action_1'] : 'enable');
    $action3 = (isset($_POST['action_3']) && in_array($_POST['action_3'], array(
      'enable',
      'disable'
    )) ? $_POST['action_3'] : 'enable');
    $action4 = (isset($_POST['action_4']) && in_array($_POST['action_4'], array(
      'enable',
      'disable'
    )) ? $_POST['action_4'] : 'enable');
    $action5 = (isset($_POST['action_5']) && in_array($_POST['action_5'], array(
      'enable',
      'disable'
    )) ? $_POST['action_5'] : 'enable');
    $action6 = (isset($_POST['action_6']) && in_array($_POST['action_6'], array(
      'enable',
      'disable'
    )) ? $_POST['action_6'] : 'disable');
    $action7 = (isset($_POST['action_6']) && in_array($_POST['action_7'], array(
      'enable',
      'disable'
    )) ? $_POST['action_7'] : 'enable');
    // Enable Products..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
    `pEnable` = '" . ($action1 == 'enable' ? 'yes' : 'no') . "'
    WHERE `" . DB_PREFIX . "products`.`id`            = `" . DB_PREFIX . "prod_category`.`product`
    AND `" . DB_PREFIX . "prod_category`.`category`  IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Categories..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
    `enCat` = '" . ($action3 == 'enable' ? 'yes' : 'no') . "'
    WHERE `id` IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Enable Brands..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "brands` SET
    `enBrand` = '" . ($action4 == 'enable' ? 'yes' : 'no') . "'
    WHERE `bCat` IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (isset($_POST['log'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "brands` SET
      `enBrand` = '" . ($action4 == 'enable' ? 'yes' : 'no') . "'
      WHERE `bCat` = 'all'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Disqus..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
    `enDisqus` = '" . ($action5 == 'enable' ? 'yes' : 'no') . "'
    WHERE `" . DB_PREFIX . "products`.`id`            = `" . DB_PREFIX . "prod_category`.`product`
    AND `" . DB_PREFIX . "prod_category`.`category`  IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
    `enDisqus` = '" . ($action5 == 'enable' ? 'yes' : 'no') . "'
    WHERE `id` IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Free shipping..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
    `freeShipping` = '" . ($action6 == 'enable' ? 'yes' : 'no') . "'
    WHERE `" . DB_PREFIX . "products`.`id`            = `" . DB_PREFIX . "prod_category`.`product`
    AND `" . DB_PREFIX . "prod_category`.`category`  IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "categories` SET
    `freeShipping` = '" . ($action6 == 'enable' ? 'yes' : 'no') . "'
    WHERE `id` IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Cart purchase..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
    `pPurchase` = '" . ($action7 == 'enable' ? 'yes' : 'no') . "'
    WHERE `" . DB_PREFIX . "products`.`id`            = `" . DB_PREFIX . "prod_category`.`product`
    AND `" . DB_PREFIX . "prod_category`.`category`  IN(" . implode(',', $_POST['range']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function reOrderPersonalisation() {
    if (!empty($_GET['pers']) && is_array($_GET['pers'])) {
      foreach ($_GET['pers'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "personalisation` SET
        `orderBy`        = '" . ($k + 1) . "'
        WHERE `id`       = '" . mc_digitSan($v) . "'
        AND `productID`  = '" . mc_digitSan($_GET['order']) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addPersonalisation() {
    // Check restriction limit for free version..
    $loop = 0;
    $org  = mc_rowCount('personalisation WHERE productID = \'' . (int) $_GET['product'] . '\'');
    if (LICENCE_VER == 'locked') {
      if (($org + 1) > RESTR_PERS) {
        mc_restrictionLimitRedirect();
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "personalisation` (
    `productID`,
    `persInstructions`,
    `persOptions`,
    `maxChars`,
    `persAddCost`,
    `enabled`,
    `boxType`,
    `reqField`,
    `orderBy`
    ) VALUES (
    '" . (int) $_GET['product'] . "',
    '" . mc_safeSQL($_POST['persInstructions']) . "',
    '" . mc_safeSQL(str_replace(mc_defineNewline(), '||', str_replace('||', '|', $_POST['persOptions']))) . "',
    '" . ($_POST['persOptions'] == '' ? (int) $_POST['maxChars'] : '0') . "',
    '" . mc_cleanInsertionPrice($_POST['persAddCost']) . "',
    '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
      'yes',
      'no'
    )) ? $_POST['enabled'] : 'no') . "',
    '" . (isset($_POST['boxType']) && in_array($_POST['boxType'], array(
      'input',
      'textarea'
    )) ? $_POST['boxType'] : 'no') . "',
    '" . (isset($_POST['reqField']) && in_array($_POST['reqField'], array(
      'yes',
      'no'
    )) ? $_POST['reqField'] : 'no') . "',
    '" . ($org > 0 ? (++$org) : (++$loop)) . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updatePersonalisation() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "personalisation` SET
    `persInstructions`  = '" . mc_safeSQL($_POST['persInstructions']) . "',
    `persOptions`       = '" . mc_safeSQL(str_replace(mc_defineNewline(), '||', str_replace('||', '|', $_POST['persOptions']))) . "',
    `maxChars`          = '" . ($_POST['persOptions'] == '' ? (int) $_POST['maxChars'] : '0') . "',
    `persAddCost`       = '" . mc_cleanInsertionPrice($_POST['persAddCost']) . "',
    `enabled`           = '" . (isset($_POST['enabled']) && in_array($_POST['enabled'], array(
      'yes',
      'no'
    )) ? $_POST['enabled'] : 'no') . "',
    `boxType`           = '" . (isset($_POST['boxType']) && in_array($_POST['boxType'], array(
      'input',
      'textarea'
    )) ? $_POST['boxType'] : 'no') . "',
    `reqField`          = '" . (isset($_POST['reqField']) && in_array($_POST['reqField'], array(
      'yes',
      'no'
    )) ? $_POST['reqField'] : 'no') . "'
    WHERE `id`            = '" . mc_digitSan($_POST['update']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function deletePersonalisation() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "personalisation` WHERE `id` = '" . mc_digitSan($_GET['del']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'personalisation'
    ));
    return $rows;
  }

  public function updateMP3File() {
    $ID = (int) $_GET['edit'];
    $fN = mc_safeSQL($_POST['fileName']);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "mp3` SET
    `fileName`  = '{$fN}'
    WHERE `id`  = '{$ID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function reOrderMP3Files() {
    if (!empty($_GET['mp3']) && is_array($_GET['mp3'])) {
      foreach ($_GET['mp3'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "mp3` SET
        `orderBy`   = '" . ($k + 1) . "'
        WHERE `id`  = '" . mc_digitSan($v) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addMP3Files() {
    $keys = array_keys($_POST['mp3']);
    $loop = 0;
    if (isset($_GET['product'])) {
      $org  = mc_rowCount('mp3 WHERE `product_id` = \'' . (int) $_GET['product'] . '\'');
      foreach ($keys AS $mp3Keys) {
        $fN  = mc_safeSQL($_POST['fileName'][$mp3Keys]);
        $mp3 = mc_safeSQL($_POST['mp3'][$mp3Keys]);
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "mp3` (
        `product_id`,`filePath`,`fileName`,`fileFolder`,`orderBy`
        ) VALUES (
        '" . mc_digitSan($_GET['product']) . "','{$mp3}','{$fN}','{$_POST['folder']}','" . ($org > 0 ? (++$org) : (++$loop)) . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    return (isset($_POST['mp3']) ? count($_POST['mp3']) : '0');
  }

  public function deleteMP3File() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "mp3`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'mp3'
    ));
    return $rows;
  }

  public function reloadOfferPrices() {
    if (!empty($_POST['products'])) {
      $multi = (int) $_POST['newBuy'];
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE IGNORE `" . DB_PREFIX . "products` SET
      `pOffer`        = '" . mc_cleanInsertionPrice($_POST['newPrice']) . "',
      `pMultiBuy`     = '{$multi}',
      `pOfferExpiry`  = '" . ($_POST['newExpiry'] ? mc_convertCalToSQLFormat($_POST['newExpiry'], $this->settings) : '0000-00-00') . "'
      WHERE `id`     IN(" . implode(',', $_POST['products']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function getPurchaseProducts($sale) {
    $prod = array();
    $q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purchases`
              WHERE `saleID`          = '{$sale}'
              AND `saleConfirmation`  = 'yes'
              ORDER BY `id`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($P = mysqli_fetch_object($q_prod)) {
      $prod[] = $P->productID;
    }
    return array_unique($prod);
  }

  public function deleteRelatedProduct() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_relation`
    WHERE `id` = '" . (int) $_GET['del'] . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'prod_relation'
    ));
    return $rows;
  }

  public function addRelatedProducts() {
    foreach ($_POST['product'] AS $id) {
      if (ctype_digit($id) && ctype_digit($_GET['product'])) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_relation` (
        `product`,`related`
        ) VALUES (
        '{$_GET['product']}','{$id}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
        // Mirror product relation across products..
        if (isset($_POST['mirror']) && $_POST['mirror'] == 'yes') {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_relation` (
          `product`,`related`
          ) VALUES (
          '{$id}','{$_GET['product']}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
  }

  public function updatePictures() {
    $prod = mc_digitSan($_GET['product']);
    if (isset($_POST['mainImg'])) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "pictures` SET
      `displayImg`        = 'yes'
      WHERE `product_id`  = '{$prod}'
      AND `id`            = '{$_POST['mainImg']}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "pictures` SET
      `displayImg`        = 'no'
      WHERE `product_id`  = '{$prod}'
      AND `id`           != '{$_POST['mainImg']}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    if (!empty($_POST['picIDs'])) {
      foreach ($_POST['picIDs'] AS $pcID) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "pictures` SET
        `pictitle`  = '" . mc_safeSQL($_POST['title'][$pcID]) . "',
        `picalt`    = '" . mc_safeSQL($_POST['alt'][$pcID]) . "'
        WHERE `product_id`  = '{$prod}'
        AND `id`            = '{$pcID}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function batchMoveProductsBetweenCategories() {
    // Update product categories..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "prod_category` SET
    `category`       = '" . mc_digitSan($_POST['destination']) . "'
    WHERE `product` IN(" . implode(',', $_POST['products']) . ")
    AND `category`   = '" . mc_digitSan($_GET['cat']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Update purchase product categories
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `categoryID`        = '{$_POST['destination']}'
    WHERE `productID`  IN(" . implode(',', $_POST['products']) . ")
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function getDiscount($price, $rate) {
    if (strpos($rate, '%') !== FALSE) {
      $decimal  = substr_replace($rate, '', -1) / 100 * $price;
      $discount = number_format($decimal, 2);
      return mc_formatPrice($discount);
    } else {
      return mc_formatPrice($rate);
    }
  }

  public function uploadImportFile($lines, $del, $enc) {
    $fields = array();
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import/export routines. Please update!');
    }
    mc_clearImportFolder();
    // Upload CSV file..
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
      $ext                       = strrchr(strtolower($_FILES['file']['name']), '.');
      $fl                        = 'import-' . date('Ymdhis') . $ext;
      $_SESSION['curImportFile'] = $fl;
      move_uploaded_file($_FILES['file']['tmp_name'], PATH . 'import/' . $fl);
      // If uploaded file exists, read CSV data and just get header column..
      if (file_exists(PATH . 'import/' . $fl)) {
        $handle = fopen(PATH . 'import/' . $fl, "r");
        while (($CSV = fgetcsv($handle, $lines, $del, $enc)) !== FALSE) {
          $CSV = array_map('trim', $CSV);
          return $CSV;
        }
        fclose($handle);
      }
    }
    return $fields;
  }

  public function batchImportAttributesFromCSV() {
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import routines. Please update!');
    }
    $count = 0;
    $ngrp = array();
    $orderCnt = 0;
    // Import..
    if ($_POST['category'] > 0) {
      // New attribute group..
      if ($_POST['attrGroup'] == '') {
        $_POST['attrGroup'] = $msg_import56;
      }
      if (!empty($_POST['product'])) {
        $loop = 0;
        for ($i=0; $i<count($_POST['product']); $i++) {
          // Check restriction limit for free version..
          $org = mc_rowCount('attr_groups WHERE `productID` = \'' . $_POST['product'][$i] . '\'');
          if (LICENCE_VER == 'locked') {
            if (($org + 1) > RESTR_ATTR_GRP) {
              mc_restrictionLimitRedirect();
            }
          }
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attr_groups` (
          `productID`,
          `groupName`,
          `orderBy`,
          `allowMultiple`,
          `isRequired`
          ) VALUES (
          '{$_POST['product'][$i]}',
          '" . mc_safeSQL($_POST['attrGroup']) . "',
          '" . ($org > 0 ? (++$org) : (++$loop)) . "',
          '" . (isset($_POST['allowMultiple']) && in_array($_POST['allowMultiple'], array(
            'yes',
            'no'
          )) ? $_POST['allowMultiple'] : 'no') . "',
          '" . (isset($_POST['isRequired']) && in_array($_POST['isRequired'], array(
            'yes',
            'no'
          )) ? $_POST['isRequired'] : 'no') . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          $newGroup = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
          $ngrp[$_POST['product'][$i]] = $newGroup;
        }
      }
      if (!empty($ngrp) && file_exists(PATH . 'import/' . $_POST['file'])) {
        $slot = 0;
        $handle  = fopen(PATH . 'import/' . $_POST['file'], "r");
        if ($handle) {
          while (($CSV = fgetcsv($handle, $_SESSION['mc_importPref']['lines'], $_SESSION['mc_importPref']['del'], $_SESSION['mc_importPref']['enc'])) !== FALSE) {
            ++$slot;
            if ($slot > 1) {
              // Clean array..
              $CSV          = array_map('trim', $CSV);
              $flip         = array_flip($_SESSION['mc_fieldMapping']);
              // Check incoming data and map fields..
              $i_attrName   = products::readImportProductValue('attrName', $flip, $CSV, 'string');
              $i_attrWeight = products::readImportProductValue('attrWeight', $flip, $CSV, 'decimal');
              $i_attrStock  = products::readImportProductValue('attrStock', $flip, $CSV, 'integer');
              $i_attrCost   = products::readImportProductValue('attrCost', $flip, $CSV, 'price');
              ++$orderCnt;
              foreach (array_keys($ngrp) AS $pID) {
                // Check restriction limit for free version..
                if (LICENCE_VER == 'locked') {
                  if (mc_rowCount('attributes WHERE `productID` = \'' . $pID . '\'') + 1 > RESTR_ATTR) {
                    mc_restrictionLimitRedirect();
                  }
                }
                mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attributes` (
                `productID`,
                `attrGroup`,
                `attrName`,
                `attrCost`,
                `attrStock`,
                `attrWeight`,
                `orderBy`
                ) VALUES (
                '{$pID}',
                '{$ngrp[$pID]}',
                '" . mc_safeSQL(mc_cleanData($i_attrName)) . "',
                '{$i_attrCost}',
                '{$i_attrStock}',
                '{$i_attrWeight}',
                '{$orderCnt}'
                )") or die(mc_MySQLError(__LINE__, __FILE__));
              }
              ++$count;
            }
          }
        }
        fclose($handle);
      }
    }
    mc_clearImportFolder();
    return array(
      $count,
      (!empty($_POST['product']) ? count($_POST['product']) : '0')
    );
  }

  public function batchImportProductsFromCSV() {
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for import routines. Please update!');
    }
    $count = 0;
    // Import..
    if (!empty($_POST['pCat'])) {
      // Read CSV file..
      $slot = 0;
      if (file_exists(PATH . 'import/' . $_POST['file'])) {
        $handle = fopen(PATH . 'import/' . $_POST['file'], "r");
        if ($handle) {
          while (($CSV = fgetcsv($handle, $_SESSION['mc_importPref']['lines'], $_SESSION['mc_importPref']['del'], $_SESSION['mc_importPref']['enc'])) !== FALSE) {
            ++$slot;
            // Skip first header row..
            if ($slot > 1) {
              // Clean array..
              $CSV              = array_map('trim', $CSV);
              $flip             = array_flip($_SESSION['mc_fieldMapping']);
              // Check incoming data..
              $i_pName          = products::readImportProductValue('pName', $flip, $CSV, 'string');
              $i_pTitle         = products::readImportProductValue('pTitle', $flip, $CSV, 'string');
              $i_pDescription   = products::readImportProductValue('pDescription', $flip, $CSV, 'string');
              $i_pSDescription  = products::readImportProductValue('pShortDescription', $flip, $CSV, 'string');
              $i_pMetaKeys      = products::readImportProductValue('pMetaKeys', $flip, $CSV, 'string');
              $i_pMetaDesc      = products::readImportProductValue('pMetaDesc', $flip, $CSV, 'string');
              $i_pTags          = products::readImportProductValue('pTags', $flip, $CSV, 'string');
              $i_rwslug         = products::readImportProductValue('rwslug', $flip, $CSV, 'string');
              $i_pDownload      = products::readImportProductValue('pDownload', $flip, $CSV, 'boolean');
              $i_pDownloadPath  = products::readImportProductValue('pDownloadPath', $flip, $CSV, 'string');
              $i_pDownloadLimit = products::readImportProductValue('pDownloadLimit', $flip, $CSV, 'integer');
              $i_pVideo         = products::readImportProductValue('pVideo', $flip, $CSV, 'string');
              $i_pVideo2        = products::readImportProductValue('pVideo2', $flip, $CSV, 'string');
              $i_pVideo3        = products::readImportProductValue('pVideo3', $flip, $CSV, 'string');
              $i_pStock         = products::readImportProductValue('pStock', $flip, $CSV, 'integer');
              $i_maxPurchaseQty = products::readImportProductValue('maxPurchaseQty', $flip, $CSV, 'integer');
              $i_minPurchaseQty = products::readImportProductValue('minPurchaseQty', $flip, $CSV, 'integer');
              $i_pStockNotify   = products::readImportProductValue('pStockNotify', $flip, $CSV, 'integer');
              $i_pVisits        = products::readImportProductValue('pVisits', $flip, $CSV, 'integer');
              $i_pCode          = products::readImportProductValue('pCode', $flip, $CSV, 'string');
              $i_pWeight        = products::readImportProductValue('pWeight', $flip, $CSV, 'decimal');
              $i_pPrice         = products::readImportProductValue('pPrice', $flip, $CSV, 'price');
              $i_pPurPrice      = products::readImportProductValue('pPurPrice', $flip, $CSV, 'price');
              $i_pOffer         = products::readImportProductValue('pOffer', $flip, $CSV, 'price');
              $i_pOfferExpiry   = products::readImportProductValue('pOfferExpiry', $flip, $CSV, 'date');
              $i_expiry         = products::readImportProductValue('expiry', $flip, $CSV, 'date');
              $i_dropshipping   = products::readImportProductValue('dropshipping', $flip, $CSV, 'integer');
              // Check restriction limit for free version..
              if (LICENCE_VER == 'locked') {
                if (mc_rowCount('products') + 1 > RESTR_PROD) {
                  mc_restrictionLimitRedirect();
                }
              }
              // Set free shipping as yes for downloadable items..
              if ($i_pDownload == 'yes') {
                $_POST['freeShipping'] = 'yes';
              }
              // Add to database..
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "products` (
              `pName`,
              `pTitle`,
              `pMetaKeys`,
              `pMetaDesc`,
              `pTags`,
              `pDescription`,
              `pShortDescription`,
              `pDownload`,
              `pDownloadPath`,
              `pVideo`,
              `pVideo2`,
              `pVideo3`,
              `pDownloadLimit`,
              `pStockNotify`,
              `pStock`,
              `pEnable`,
              `pDateAdded`,
              `pCode`,
              `pWeight`,
              `pPrice`,
              `pPurPrice`,
              `pOfferExpiry`,
              `pOffer`,
              `rssBuildDate`,
              `enDisqus`,
              `freeShipping`,
              `pPurchase`,
              `rwslug`,
              `minPurchaseQty`,
              `maxPurchaseQty`,
              `expiry`,
              `dropshipping`
              ) VALUES (
              '" . mc_safeSQL($i_pName) . "',
              '" . mc_safeSQL($i_pTitle) . "',
              '" . mc_safeSQL($i_pMetaKeys) . "',
              '" . mc_safeSQL($i_pMetaDesc) . "',
              '" . mc_safeSQL($i_pTags) . "',
              '" . mc_safeSQL(mc_cleanBBInput($i_pDescription)) . "',
              '" . mc_safeSQL(mc_cleanBBInput($i_pSDescription)) . "',
              '{$i_pDownload}',
              '" . mc_safeSQL($i_pDownloadPath) . "',
              '" . mc_safeSQL($i_pVideo) . "',
              '" . mc_safeSQL($i_pVideo2) . "',
              '" . mc_safeSQL($i_pVideo3) . "',
              '" . ($i_pDownloadLimit ? $i_pDownloadLimit : '1') . "',
              '" . ($i_pStockNotify ? $i_pStockNotify : '0') . "',
              '{$i_pStock}',
              '" . (isset($_POST['pEnable']) && in_array($_POST['pEnable'], array(
                'yes',
                'no'
              )) ? $_POST['pEnable'] : 'no') . "',
              '" . date("Y-m-d") . "',
              '" . mc_safeSQL($i_pCode) . "',
              '" . mc_safeSQL($i_pWeight) . "',
              '{$i_pPrice}',
              '{$i_pPurPrice}',
              '{$i_pOfferExpiry}',
              '{$i_pOffer}',
              '" . RSS_BUILD_DATE_FORMAT . "',
              '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
                'yes',
                'no'
              )) ? $_POST['enDisqus'] : 'no') . "',
              '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
                'yes',
                'no'
              )) ? $_POST['freeShipping'] : 'no') . "',
              '" . (isset($_POST['pPurchase']) && in_array($_POST['pPurchase'], array(
                'yes',
                'no'
              )) ? $_POST['pPurchase'] : 'no') . "',
              '" . mc_safeSQL($i_rwslug) . "',
              '{$i_minPurchaseQty}',
              '{$i_maxPurchaseQty}',
              '{$i_expiry}',
              '{$i_dropshipping}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              ++$count;
              $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
              // Add categories..
              if ($id > 0) {
                if (!empty($_POST['pCat'])) {
                  foreach ($_POST['pCat'] AS $c) {
                    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
                    `product`,`category`
                    ) VALUES (
                    '{$id}','{$c}'
                    )") or die(mc_MySQLError(__LINE__, __FILE__));
                  }
                }
              }
              // Add brands..
              if ($id > 0) {
                if (!empty($_POST['pBrand'])) {
                  foreach ($_POST['pBrand'] AS $b) {
                    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_brand` (
                    `product`,`brand`
                    ) VALUES (
                    '{$id}','{$b}'
                    )") or die(mc_MySQLError(__LINE__, __FILE__));
                  }
                }
              }
            }
          }
        }
        fclose($handle);
      }
    }
    mc_clearImportFolder();
    return $count;
  }

  public function checkImportProductType($value, $type) {
    switch($type) {
      case 'string':
        return $value;
        break;
      case 'integer':
        if ($value == '') {
          return '0';
        }
        return (int) $value;
        break;
      case 'boolean':
        if ($value == '') {
          return 'no';
        }
        return (in_array(strtolower($value), array(
          '1',
          'yes',
          true
        )) ? 'yes' : 'no');
        break;
      case 'decimal':
        if ($value == '') {
          return '0.00';
        }
        return $value;
        break;
      case 'price':
        if ($value == '') {
          return '0.00';
        }
        return mc_cleanInsertionPrice($value);
        break;
      case 'date':
        if ($value == '') {
          return '0000-00-00';
        }
        $ts = strtotime($value);
        if ($ts > 0) {
          return date('Y-m-d', $ts);
        } else {
          return '0000-00-00';
        }
        break;
    }
  }

  public function readImportProductValue($field, $flip, $CSV, $type) {
    if (isset($flip[$field])) {
      if (isset($_SESSION['mc_fieldMapping_alt'][$flip[$field]]) && trim($_SESSION['mc_fieldMapping_alt'][$flip[$field]]) != '') {
        return products::checkImportProductType($_SESSION['mc_fieldMapping_alt'][$flip[$field]], $type);
      } else {
        return products::checkImportProductType($CSV[$flip[$field]], $type);
      }
    }
    return products::checkImportProductType('', $type);
  }

  public function exportOverviewToCSV() {
    global $msg_slsproductoverview7;
    $separator = ',';
    $csvFile   = PATH . 'import/overview-' . date('dmYHis') . '.csv';
    $data      = $msg_slsproductoverview7 . mc_defineNewline();
    for ($i=0; $i<count($_POST['expcol1']); $i++) {
      $q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `pName` FROM `" . DB_PREFIX . "products`
             WHERE `id` = '" . (int) $_POST['expcol1'][$i] . "'
             ") or die(mc_MySQLError(__LINE__, __FILE__));
      $PRD = mysqli_fetch_object($q_p);
      $data .= mc_cleanCSV((isset($PRD->pName) ? $PRD->pName : 'N/A'), $separator) . $separator;
      $data .= $_POST['expcol2'][$i] . $separator;
      $data .= $_POST['expcol3'][$i] . $separator;
      $data .= $_POST['expcol4'][$i] . $separator;
      $data .= $_POST['expcol5'][$i] . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function exportHitsToCSV() {
    global $msg_hit_overview7;
    // Check writeable permissions..
    if (!is_writeable(PATH . 'import')) {
      die('Admin \'import\' folder is not writeable. This is required for export routines. Please update!');
    }
    $totalHits = mc_sumCount('products', 'pVisits');
    $SQL       = ($_GET['export'] != 'all' ? 'AND `category` = \'' . mc_digitSan($_GET['export']) . '\'' : '');
    $separator = ',';
    $csvFile   = PATH . 'import/hits-' . date('dmYHis') . '.csv';
    $data      = $msg_hit_overview7 . mc_defineNewline();
    $q_p      = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
                LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
                LEFT JOIN `" . DB_PREFIX . "categories`
                ON `" . DB_PREFIX . "categories`.`id` = `" . DB_PREFIX . "prod_category`.`category`
                WHERE `pEnable` = 'yes'
                $SQL
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pVisits` DESC,`pName`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PROD = mysqli_fetch_object($q_p)) {
      $perc = '0%';
      // Prevent division by zero errors..
      if ($PROD->pVisits > 0) {
        $perc = number_format($PROD->pVisits / $totalHits * 100, STATS_DECIMAL_PLACES) . '%';
      }
      $data .= mc_cleanCSV($PROD->pName, $separator) . $separator . mc_cleanCSV($PROD->catname, $separator) . $separator . $PROD->pVisits . $separator . $perc . mc_defineNewline();
    }
    if ($data) {
      $this->dl->write($csvFile, trim($data));
      $this->dl->dl($csvFile, 'application/force-download', 'yes');
    }
  }

  public function resetProductHits() {
    if ($_GET['reset'] == 'all') {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
      `pVisits` = '0'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
      `pVisits` = '0'
      WHERE `" . DB_PREFIX . "products`.`id`           = `" . DB_PREFIX . "prod_category`.`product`
      AND `" . DB_PREFIX . "prod_category`.`category`  = '" . mc_digitSan($_GET['reset']) . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function updateStockLevels() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $_POST            = mc_safeImport($_POST);
    $pfilter          = '';
    $vfilter          = '';
    $type             = (isset($_POST['type']) ? $_POST['type'] : 'incr');
    $_POST['product'] = (isset($_POST['product']) ? $_POST['product'] : array());
    $table            = (empty($_POST['table']) ? array(
      'products'
    ) : $_POST['table']);
    // Filter criteria..
    if ($_POST['min'] > 0 || $_POST['max'] > 0) {
      $pfilter .= 'AND `pStock` NOT BETWEEN \'' . ceil($_POST['min']) . '\' AND \'' . ceil($_POST['max']) . '\'';
      $vfilter .= 'AND `attrStock` NOT BETWEEN \'' . ceil($_POST['min']) . '\' AND \'' . ceil($_POST['max']) . '\'';
    }
    if (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['from'], $this->settings)) != '0000-00-00' && mc_checkValidDate(mc_convertCalToSQLFormat($_POST['to'], $this->settings)) != '0000-00-00') {
      $pfilter .= 'AND `pDateAdded` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
      $vfilter .= 'AND `pDateAdded` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
    }
    switch($type) {
      case 'fixed':
        $pr_stock_adj = '`pStock` = \'' . ceil($_POST['stock']) . '\'';
        $vr_stock_adj = '`attrStock` = \'' . ceil($_POST['stock']) . '\'';
        break;
      case 'incr':
        $pr_stock_adj = '`pStock` = (`pStock`+' . ceil($_POST['stock']) . ')';
        $vr_stock_adj = '`attrStock` = (`attrStock`+' . ceil($_POST['stock']) . ')';
        break;
      default:
        $pr_stock_adj = '`pStock` = (`pStock`-' . ceil($_POST['stock']) . ')';
        $vr_stock_adj = '`attrStock` = (`attrStock`-' . ceil($_POST['stock']) . ')';
        break;
    }
    //-------------------------------------------------------------------------------------------------------
    // Update products..
    //-------------------------------------------------------------------------------------------------------
    if (in_array('products', $table)) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
      $pr_stock_adj
      WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
      AND `" . DB_PREFIX . "products`.`id`              = `" . DB_PREFIX . "prod_category`.`product`
      " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'products`.`id` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
      $pfilter
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      // Fix minus values..
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET `pStock` = '0' WHERE `pStock` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    //-------------------------------------------------------------------------------------------------------
    // Update attributes..
    //-------------------------------------------------------------------------------------------------------
    if (in_array('attr', $table)) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "attributes`,`" . DB_PREFIX . "prod_category` SET
      $vr_stock_adj
      WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
      AND `" . DB_PREFIX . "attributes`.`productID`     = " . DB_PREFIX . "prod_category.product
      " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'attributes`.`productID` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
      $vfilter
      ") or die(mc_MySQLError(__LINE__, __FILE__));
      // Fix minus values..
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET `attrStock` = '0' WHERE `attrStock` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function updateProductPrices() {
    $_POST            = mc_safeImport($_POST);
    $pfilter          = '';
    $vfilter          = '';
    $type             = (isset($_POST['type']) ? $_POST['type'] : 'incr');
    $_POST['product'] = (isset($_POST['product']) ? $_POST['product'] : array());
    $rate             = (substr($_POST['price'], -1) == '%' ? 'perc' : 'price');
    $table            = (empty($_POST['table']) ? array(
      'products'
    ) : $_POST['table']);
    // Clear special offers..
    if (isset($_POST['clear']) && $_POST['clear'] == 'yes') {
      products::clearSpecialOffer('cat', $_POST['pCat']);
    }
    // Filter criteria..
    if ($_POST['min'] > 0 || $_POST['max'] > 0) {
      $pfilter .= 'AND `pPrice` NOT BETWEEN \'' . mc_cleanInsertionPrice($_POST['min']) . '\' AND \'' . mc_cleanInsertionPrice($_POST['max']) . '\'';
      $vfilter .= 'AND `attrCost` NOT BETWEEN \'' . mc_cleanInsertionPrice($_POST['min']) . '\' AND \'' . mc_cleanInsertionPrice($_POST['max']) . '\'';
    }
    if (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['from'], $this->settings)) != '0000-00-00' && mc_checkValidDate(mc_convertCalToSQLFormat($_POST['to'], $this->settings)) != '0000-00-00') {
      $pfilter .= 'AND `pDateAdded` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
      $vfilter .= 'AND `pDateAdded` BETWEEN \'' . mc_convertCalToSQLFormat($_POST['from'], $this->settings) . '\' AND \'' . mc_convertCalToSQLFormat($_POST['to'], $this->settings) . '\'';
    }
    switch($type) {
      case 'fixed':
        // This should not be percentage based. If it is, remove symbol..
        if ($rate == 'perc') {
          $_POST['price'] = @number_format(substr($_POST['price'], 0, -1), 2);
        }
        $pr_price_adj = '`pPrice` = \'' . mc_cleanInsertionPrice($_POST['price']) . '\'';
        $vr_price_adj = '`attrCost` = \'' . mc_cleanInsertionPrice($_POST['price']) . '\'';
        break;
      case 'incr':
        if ($rate == 'perc') {
          $pr_price_adj = '`pPrice` = ROUND(`pPrice`+`pPrice`/100*' . ceil(substr($_POST['price'], 0, -1)) . ',2)';
          $vr_price_adj = '`attrCost` = ROUND(`attrCost`+`attrCost`/100*' . ceil(substr($_POST['price'], 0, -1)) . ',2)';
        } else {
          $pr_price_adj = '`pPrice` = ROUND(`pPrice`+' . mc_cleanInsertionPrice($_POST['price']) . ',2)';
          $vr_price_adj = '`attrCost` = ROUND(`attrCost`+' . mc_cleanInsertionPrice($_POST['price']) . ',2)';
        }
        break;
      default:
        if ($rate == 'perc') {
          $pr_price_adj = '`pPrice` = ROUND(`pPrice`-`pPrice`/100*' . ceil(substr($_POST['price'], 0, -1)) . ',2)';
          $vr_price_adj = '`attrCost` = ROUND(`attrCost`-`attrCost`/100*' . ceil(substr($_POST['price'], 0, -1)) . ',2)';
        } else {
          $pr_price_adj = '`pPrice` = ROUND(`pPrice`-' . mc_cleanInsertionPrice($_POST['price']) . ',2)';
          $vr_price_adj = '`attrCost` = ROUND(`attrCost`-' . mc_cleanInsertionPrice($_POST['price']) . ',2)';
        }
        break;
    }
    switch($rate) {
      case 'perc':
        //-------------------------------------------------------------------------------------------------------
        // Update products..percentage..
        //-------------------------------------------------------------------------------------------------------
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
        $pr_price_adj
        WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
        AND `" . DB_PREFIX . "products`.`id`              = `" . DB_PREFIX . "prod_category`.`product`
        " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'products`.`id` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
        $pfilter
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        //-------------------------------------------------------------------------------------------------------
        // Update attributes..percentage..
        //-------------------------------------------------------------------------------------------------------
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes`,`" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
        $vr_price_adj
        WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
        AND `" . DB_PREFIX . "attributes`.`productID`     = `" . DB_PREFIX . "prod_category`.`product`
        " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'attributes`.`productID` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
        $vfilter
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      //-------------------------------------------------------------------------------------------------------
      // Update products..price..
      //-------------------------------------------------------------------------------------------------------
      case 'price':
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "prod_category` SET
        $pr_price_adj
        WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
        AND `" . DB_PREFIX . "products`.`id`              = `" . DB_PREFIX . "prod_category`.`product`
        " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'products`.`id` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
        $pfilter
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        //-------------------------------------------------------------------------------------------------------
        // Update attributes..price..
        //-------------------------------------------------------------------------------------------------------
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products`,`" . DB_PREFIX . "attributes`,`" . DB_PREFIX . "prod_category` SET
        $vr_price_adj
        WHERE `" . DB_PREFIX . "prod_category`.`category` = '{$_POST['pCat']}'
        AND `" . DB_PREFIX . "attributes`.`productID`     = `" . DB_PREFIX . "prod_category`.`product`
        " . (!empty($_POST['product']) ? 'AND `' . DB_PREFIX . 'attributes`.`productID` NOT IN(' . implode(',', $_POST['product']) . ')' : '') . "
        $vfilter
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
    // Fix minus values..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET `pPrice` = '0.00' WHERE `pPrice` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET `attrCost` = '0.00' WHERE `attrCost` < 0") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function clearSpecialOffer($which, $cat = 0) {
    switch($which) {
      case 'cat':
        if ($cat > 0) {
          $_GET['clear'] = $cat;
        }
        $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
                      LEFT JOIN `" . DB_PREFIX . "prod_category`
                      ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                      WHERE `category`                    = '{$_GET['clear']}'
                      GROUP BY `" . DB_PREFIX . "products`.`id`
                      ORDER BY `pName`
                      ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($P = mysqli_fetch_object($q_products)) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
          `pOffer`        = '',
          `pMultiBuy`     = '0',
          `pOfferExpiry`  = ''
          WHERE `id`      = '{$P->pid}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        break;
      case 'product':
        $prod = (int) $_GET['product'];
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
        `pOffer`        = '',
        `pMultiBuy`     = '0',
        `pOfferExpiry`  = ''
        WHERE `id`      = '{$prod}'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
    }
  }

  public function addSpecialOffer() {
    $rate             = (substr($_POST['oRate'], -1) == '%' ? 'perc' : 'price');
    $multi            = (int) $_POST['multiBuy'];
    $_POST['product'] = (isset($_POST['product']) ? $_POST['product'] : array());
    switch($rate) {
      case 'price':
        foreach ($_POST['products'] AS $id) {
          if (!in_array($id, $_POST['product'])) {
            mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
            `pOffer`        = `pPrice`-" . mc_cleanInsertionPrice($_POST['oRate']) . ",
            `pMultiBuy`     = '{$multi}',
            `pOfferExpiry`  = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['oExpiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['oExpiry'], $this->settings) : '0000-00-00') . "'
            WHERE `id`      = '{$id}'
            ") or die(mc_MySQLError(__LINE__, __FILE__));
          }
        }
        break;
      case 'perc':
        foreach ($_POST['products'] AS $id) {
          if (!in_array($id, $_POST['product'])) {
            mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
            `pOffer`        = ROUND(`pPrice`-`pPrice`/100*" . ceil(substr($_POST['oRate'], 0, -1)) . ",2),
            `pMultiBuy`     = '{$multi}',
            `pOfferExpiry`  = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['oExpiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['oExpiry'], $this->settings) : '0000-00-00') . "'
            WHERE `id`      = '{$id}'
            ") or die(mc_MySQLError(__LINE__, __FILE__));
          }
        }
        break;
    }
  }

  public function clearAllSpecialOffers() {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pOffer`        = '',
    `pOfferExpiry`  = ''
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function copyAttributes() {
    $groupIDs = array();
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT DISTINCT(`attrGroup`) FROM `" . DB_PREFIX . "attributes`
         WHERE `id` IN(" . mc_safeSQL(implode(',',$_POST['attr'])) . ")
         AND `productID` = '" . mc_digitSan($_GET['product']) . "'
         ORDER BY `attrGroup`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($AG = mysqli_fetch_object($q)) {
      $groupIDs[] = $AG->attrGroup;
    }
    if (!empty($groupIDs)) {
      foreach ($_POST['product'] AS $p) {
        // Groups..
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
             WHERE `productID` = '" . mc_digitSan($_GET['product']) . "'
             AND `id` IN(" . implode(',', $groupIDs) . ")
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($AG = mysqli_fetch_object($q)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attr_groups` (
          `productID`,`groupName`,`orderBy`,`allowMultiple`,`isRequired`
          ) VALUES (
          '{$p}',
          '" . mc_safeSQL(mc_cleanData($AG->groupName)) . "',
          '{$AG->orderBy}',
          '{$AG->allowMultiple}',
          '{$AG->isRequired}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
          // Attributes..
          $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
              WHERE `attrGroup` = '{$AG->id}'
              AND `id` IN(" . mc_safeSQL(implode(',',$_POST['attr'])) . ")
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
          while ($ATT = mysqli_fetch_object($q2)) {
            mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attributes` (
            `productID`,
            `attrGroup`,
            `attrName`,
            `attrCost`,
            `attrStock`,
            `attrWeight`,
            `orderBy`
            ) VALUES (
            '{$p}',
            '{$id}',
            '" . mc_safeSQL(mc_cleanData($ATT->attrName)) . "',
            '{$ATT->attrCost}',
            '{$ATT->attrStock}',
            '{$ATT->attrWeight}',
            '{$ATT->orderBy}'
            )") or die(mc_MySQLError(__LINE__, __FILE__));
          }
        }
      }
    }
  }

  public function updateAttributeGroups() {
    for ($i = 0; $i < count($_POST['groups']); $i++) {
      $allow = (isset($_POST['allowMultiple_' . $_POST['groups'][$i]]) ? $_POST['allowMultiple_' . $_POST['groups'][$i]] : 'no');
      $req   = (isset($_POST['isRequired_' . $_POST['groups'][$i]]) ? $_POST['isRequired_' . $_POST['groups'][$i]] : 'no');
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attr_groups` SET
      `groupName`      = '" . mc_safeSQL($_POST['groupName'][$i]) . "',
      `orderBy`        = '{$_POST['order'][$i]}',
      `allowMultiple`  = '{$allow}',
      `isRequired`     = '{$req}'
      WHERE `id`       = '{$_POST['groups'][$i]}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
  }

  public function addUpdateAttributes() {
    if (isset($_POST['process'])) {
      // Existing group or new group?
      if ($_POST['exgroup'] > 0) {
        $groupID = (int) $_POST['exgroup'];
      } else {
        $groupID = products::addAttributeGroup();
      }
      // Check restriction limit for free version..
      if (LICENCE_VER == 'locked') {
        if (mc_rowCount('attributes WHERE `productID` = \'' . mc_digitSan($_GET['product']) . '\' AND `attrGroup` = \'' . $groupID . '\'') + 1 > RESTR_ATTR) {
          mc_restrictionLimitRedirect();
        }
      }
    } else {
      // Existing group or new group?
      if ($_POST['exgroup'] > 0 && $_POST['exgroup'] == $_GET['edit']) {
        $groupID = (int) $_POST['exgroup'];
      } else {
        if ($_POST['exgroup'] > 0) {
          $groupID = (int) $_POST['exgroup'];
        } else {
          $groupID = products::addAttributeGroup();
        }
      }
      $allowMultiple = (in_array($_POST['allowMultiple'], array('yes','no')) ? $_POST['allowMultiple'] : '');
      $isRequired    = (in_array($_POST['isRequired'], array('yes','no')) ? $_POST['isRequired'] : '');
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attr_groups` SET
      `allowMultiple`  = '{$allowMultiple}',
      `isRequired`   = '{$isRequired}'
      WHERE `id`   = '" . (int) $_GET['edit'] . "'
      ") or die(mc_MySQLError(__LINE__, __FILE__));

    }
    $c = 0;
    if (isset($_POST['process'])) {
      for ($i = 0; $i < count($_POST['name']); $i++) {
        if ($_POST['name'][$i] && $groupID > 0) {
          ++$c;
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attributes` (
          `productID`,
          `attrGroup`,
          `attrName`,
          `attrCost`,
          `attrStock`,
          `attrWeight`,
          `orderBy`
          ) VALUES (
          '" . mc_digitSan($_GET['product']) . "',
          '{$groupID}',
          '" . mc_safeSQL($_POST['name'][$i]) . "',
          '" . mc_safeSQL($_POST['cost'][$i]) . "',
          '" . (int) $_POST['stock'][$i] . "',
          '" . mc_safeSQL($_POST['weight'][$i]) . "',
          '{$_POST['order'][$i]}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    } else {
      for ($i = 0; $i < count($_POST['attid']); $i++) {
        // New, added to existing group..
        if (substr($_POST['attid'][$i],0,2) == 'nb') {
          // Check restriction limit for free version..
          if (LICENCE_VER == 'locked') {
            if (mc_rowCount('attributes WHERE `productID` = \'' . mc_digitSan($_GET['product']) . '\' AND `attrGroup` = \'' . $groupID . '\'') + 1 > RESTR_ATTR) {
              mc_restrictionLimitRedirect();
            }
          }
          ++$c;
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attributes` (
          `productID`,
          `attrGroup`,
          `attrName`,
          `attrCost`,
          `attrStock`,
          `attrWeight`,
          `orderBy`
          ) VALUES (
          '" . mc_digitSan($_GET['product']) . "',
          '{$groupID}',
          '" . mc_safeSQL($_POST['name'][$i]) . "',
          '" . mc_safeSQL($_POST['cost'][$i]) . "',
          '" . (int) $_POST['stock'][$i] . "',
          '" . mc_safeSQL($_POST['weight'][$i]) . "',
          '{$_POST['order'][$i]}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          // If name empty, delete, else update
          if ($_POST['name'][$i]) {
            ++$c;
            mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attributes` SET
            `attrGroup`  = '{$groupID}',
            `attrName`   = '" . mc_safeSQL($_POST['name'][$i]) . "',
            `attrCost`   = '" . mc_safeSQL($_POST['cost'][$i]) . "',
            `attrStock`  = '" . (int) $_POST['stock'][$i] . "',
            `attrWeight` = '" . mc_safeSQL($_POST['weight'][$i]) . "',
            `orderBy`    = '{$_POST['order'][$i]}'
            WHERE `id`   = '{$_POST['attid'][$i]}'
            ") or die(mc_MySQLError(__LINE__, __FILE__));
          } else {
            products::deleteAttributes($_POST['attid'][$i]);
          }
        }
      }
    }
    return array($c, $groupID);
  }

  public function reOrderAttributes() {
    if (!empty($_GET['attr']) && is_array($_GET['attr'])) {
      foreach ($_GET['attr'] AS $k => $v) {
        mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attr_groups` SET
        `orderBy`        = '" . ($k + 1) . "'
        WHERE `id`       = '" . mc_digitSan($v) . "'
        AND `productID`  = '" . mc_digitSan($_GET['order']) . "'
        ") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  public function addAttributeGroup() {
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('attr_groups WHERE `productID` = \'' . mc_digitSan($_GET['product']) . '\'') + 1 > RESTR_ATTR_GRP) {
        mc_restrictionLimitRedirect();
      }
    }
    $allowMultiple = (in_array($_POST['allowMultiple'], array('yes','no')) ? $_POST['allowMultiple'] : '');
    $isRequired    = (in_array($_POST['isRequired'], array('yes','no')) ? $_POST['isRequired'] : '');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attr_groups` (
    `productID`,
    `groupName`,
    `orderBy`,
    `allowMultiple`,
    `isRequired`
    ) VALUES (
    '" . mc_digitSan($_GET['product']) . "',
    '" . mc_safeSQL($_POST['group']) . "',
    '0',
    '{$allowMultiple}',
    '{$isRequired}'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $gID = mysqli_insert_id($GLOBALS["___msw_sqli"]);
    // Adjust order..
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "attr_groups` SET
    `orderBy` = `id`
    WHERE `id`  = '{$gID}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return $gID;
  }

  public function deleteAttributeGroups() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attr_groups`
    WHERE `id` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attributes`
    WHERE `attrGroup` = '" . mc_digitSan($_GET['del']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    mc_tableTruncationRoutine(array(
      'attr_groups',
      'attributes'
    ));
    return $rows;
  }

  public function deleteAttributes($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attributes`
    WHERE `id` = '" . mc_digitSan($id) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'attributes'
    ));
    return $rows;
  }

  public function addAdditionalProductPictures($newProdID = 0) {
    $count   = 0;
    $allowed = array(
      '.jpg',
      '.jpeg',
      '.png',
      '.gif'
    );
    if ($_POST['folder'] == 'products') {
      $_POST['folder'] = '';
    }
    $_GET['product'] = $newProdID;
    for ($i = 0; $i < count($_FILES['addimg']['tmp_name']); $i++) {
      $temp = $_FILES['addimg']['tmp_name'][$i];
      $name = ($this->settings->renamePics == 'yes' ? strtolower($_FILES['addimg']['name'][$i]) : $_FILES['addimg']['name'][$i]);
      if ($temp && $name && is_uploaded_file($temp) && in_array(strrchr(strtolower($name), '.'), $allowed)) {
        // Check restriction limit for free version..
        if (LICENCE_VER == 'locked') {
          if (mc_rowCount('pictures WHERE `product_id` = \'' . (int) $_GET['product'] . '\'') + 1 > RESTR_PICS) {
            mc_restrictionLimitRedirect();
          }
        }
        $nextPic   = products::getNextPictureID($_GET['product']);
        $picPath   = ($this->settings->renamePics == 'yes' ? $this->settings->imgPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.') : $name);
        $thumbPath = $this->settings->tmbPrefix . ($this->settings->renamePics == 'yes' ? $_GET['product'] . '-' . $nextPic . strrchr($name, '.') : $name);
        // Check for duplicates if pictures aren`t being renamed..
        if ($this->settings->renamePics == 'no') {
          if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath)) {
            $picPath = $this->settings->imgPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.');
          }
          if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $thumbPath)) {
            $thumbPath = $this->settings->tmbPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.');
          }
        } else {
          // Check file with same name doesn`t exist..
          // As we are renaming this should never be the case, but..just in case..
          if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath)) {
            @unlink(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath);
          }
        }
        move_uploaded_file($temp, mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath);
        if (@file_exists(mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $picPath)) {
          // Only supported by some servers, so mask error..
          // This makes file removeable via FTP..
          @chmod(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath, AFTER_UPLOAD_CHMOD_VALUE);
          $dimensions = products::createThumbnail(mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $picPath, mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $thumbPath, mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']));
          if ($dimensions[2] == 'yes') {
            $sze = $dimensions[0] . ',' . $dimensions[1];
            mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "pictures` (
            `product_id`,`picture_path`,`thumb_path`,`folder`,`dimensions`
            ) VALUES (
            '{$_GET['product']}','{$picPath}','{$thumbPath}','{$_POST['folder']}','{$sze}'
            )") or die(mc_MySQLError(__LINE__, __FILE__));
            ++$count;
          }
        }
        if (file_exists($temp)) {
          @unlink($temp);
        }
      }
      unset($temp, $name);
    }
    return $count;
  }

  public function addProductPictures($newProdID = 0) {
    $count = 0;
    // Are we uploading remote images..
    if (isset($_FILES['remote']['tmp_name']) && $_FILES['remote']['name'] != '') {
      $remoteFile = file($_FILES['remote']['tmp_name']);
      for ($i = 0; $i < count($remoteFile); $i++) {
        $split = array_map('trim', explode(',', $remoteFile[$i]));
        $image = (isset($split[0]) ? str_replace('"', '', $split[0]) : '');
        $thumb = (isset($split[1]) ? str_replace('"', '', $split[1]) : '');
        $dsp   = (isset($split[2]) ? str_replace('"', '', strtolower($split[2])) : 'no');
        if ($image && substr(strtolower($image), 0, 4) == 'http') {
          // If display preference not yes or no, reset..
          if (!in_array($dsp, array(
            'yes',
            'no'
          ))) {
            $dsp = 'no';
          }
          // Check restriction limit for free version..
          if (LICENCE_VER == 'locked') {
            if (mc_rowCount('pictures WHERE `product_id` = \'' . (int) $_GET['product'] . '\'') + 1 > RESTR_PICS) {
              mc_restrictionLimitRedirect();
            }
          }
          // Add image..
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "pictures` (
          `product_id`,`picture_path`,`thumb_path`,`folder`,`dimensions`,`displayImg`,`remoteServer`,`remoteImg`,`remoteThumb`
          ) VALUES (
          '{$_GET['product']}','','','','0,0','{$dsp}','yes','{$image}','{$thumb}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          ++$count;
        }
      }
      return $count;
    } else {
      $allowed = array(
        '.jpg',
        '.jpeg',
        '.png',
        '.gif'
      );
      if ($_POST['folder'] == 'products') {
        $_POST['folder'] = '';
      }
      // If call is coming from add page, adjust for the single image..
      if ($newProdID > 0) {
        $_GET['product'] = $newProdID;
      }
      $_GET['product'] = mc_digitSan($_GET['product']);
      for ($i = 0; $i < count($_FILES['image']['tmp_name']); $i++) {
        $temp = $_FILES['image']['tmp_name'][$i];
        $name = ($this->settings->renamePics == 'yes' ? strtolower($_FILES['image']['name'][$i]) : $_FILES['image']['name'][$i]);
        if ($temp && $name && is_uploaded_file($temp) && in_array(strrchr(strtolower($name), '.'), $allowed)) {
          // Check restriction limit for free version..
          if (LICENCE_VER == 'locked') {
            if (mc_rowCount('pictures WHERE `product_id` = \'' . (int) $_GET['product'] . '\'') + 1 > RESTR_PICS) {
              mc_restrictionLimitRedirect();
            }
          }
          $nextPic   = products::getNextPictureID($_GET['product']);
          $picPath   = ($this->settings->renamePics == 'yes' ? $this->settings->imgPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.') : $name);
          $thumbPath = $this->settings->tmbPrefix . ($this->settings->renamePics == 'yes' ? $_GET['product'] . '-' . $nextPic . strrchr($name, '.') : $name);
          // Check for duplicates if pictures aren`t being renamed..
          if ($this->settings->renamePics == 'no') {
            if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath)) {
              $picPath = $this->settings->imgPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.');
            }
            if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $thumbPath)) {
              $thumbPath = $this->settings->tmbPrefix . $_GET['product'] . '-' . $nextPic . strrchr($name, '.');
            }
          } else {
            // Check file with same name doesn`t exist..
            // As we are renaming this should never be the case, but..just in case..
            if (@file_exists(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath)) {
              @unlink(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath);
            }
          }
          move_uploaded_file($temp, mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath);
          if (@file_exists(mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $picPath)) {
            // Only supported by some servers, so mask error..
            // This makes file removeable via FTP..
            @chmod(mc_uploadServerPath() . $_POST['folder'] . '/' . $picPath, AFTER_UPLOAD_CHMOD_VALUE);
            $dimensions = products::createThumbnail(mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $picPath, mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']) . '/' . $thumbPath, mc_uploadServerPath() . mc_imageDisplayPath($_POST['folder']));
            if ($dimensions[2] == 'yes') {
              $sze = $dimensions[0] . ',' . $dimensions[1];
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "pictures` (
              `product_id`,`picture_path`,`thumb_path`,`folder`,`dimensions`
              ) VALUES (
              '{$_GET['product']}','{$picPath}','{$thumbPath}','{$_POST['folder']}','{$sze}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              ++$count;
            }
          }
          if (file_exists($temp)) {
            @unlink($temp);
          }
        }
        unset($temp, $name);
      }
    }
    return $count;
  }

  public function createThumbnail($path, $tpath, $root) {
    if (!function_exists('imagecreatefromjpeg')) {
      die('The <a href="http://php.net/manual/en/book.image.php">GD library</a> is not enabled or installed on your server.
         Thumbnail creation cannot work until this is enabled! Contact your host to enable this or install manually.');
    }
    if (!is_dir($root) || !is_writeable($root)) {
      die('The following directory must exist and be writeable.<br><br><b>' . $root . '</b>');
    }
    $dimensions = $this->settings->thumbWidth . ',' . $this->settings->thumbHeight;
    $sizes      = getimagesize($path);
    $ext        = substr(strrchr(strtolower($path), '.'), 1);
    $thisWidth  = ($sizes[0] > $sizes[1] ? $this->settings->thumbWidth : $this->settings->thumbHeight);
    $thisHeight = ($sizes[0] > $sizes[1] ? $this->settings->thumbHeight : $this->settings->thumbWidth);
    $added      = 'no';
    if ($thisWidth > 0 && $thisHeight > 0) {
      // Process based on image type..
      switch($ext) {
        case 'jpg':
        case 'jpeg':
          // Get original image width and height..
          $img = imagecreatefromjpeg($path);
          if ($img && ($sizes[0] > $thisWidth || $sizes[1] > $thisHeight)) {
            // Are we maintaining aspect ratio?
            if ($this->settings->aspectRatio == 'yes') {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $scale    = min($thisWidth / $i_width, $thisHeight / $i_height);
              // For thumbnail, maintain aspect ratio of original image
              // If image is smaller or equal to new sizes, no resize is necessary
              if ($scale < 1) {
                $new_width  = floor($scale * $i_width);
                $new_height = floor($scale * $i_height);
                $tmp_img    = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                imagejpeg($tmp_img, $tpath, $this->settings->thumbQuality);
                imagedestroy($img);
                imagedestroy($tmp_img);
              } else {
                imagedestroy($img);
              }
            } else {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $tmp_img  = imagecreatetruecolor($this->settings->thumbWidth, $this->settings->thumbHeight);
              imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $this->settings->thumbWidth, $this->settings->thumbHeight, $i_width, $i_height);
              imagejpeg($tmp_img, $tpath, $this->settings->thumbQuality);
              imagedestroy($img);
              imagedestroy($tmp_img);
            }
            $added = 'yes';
          }
          break;
        case 'png':
          // Get original image width and height..
          $img = imagecreatefrompng($path);
          if ($img && ($sizes[0] > $thisWidth || $sizes[1] > $thisHeight)) {
            // Are we maintaining aspect ratio?
            if ($this->settings->aspectRatio == 'yes') {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $scale    = min($thisWidth / $i_width, $thisHeight / $i_height);
              // For thumbnail, maintain aspect ratio of original image
              // If image is smaller or equal to new sizes, no resize is necessary
              if ($scale < 1) {
                $new_width  = floor($scale * $i_width);
                $new_height = floor($scale * $i_height);
                $tmp_img    = imagecreatetruecolor($new_width, $new_height);
                $bk         = imagecolorallocate($tmp_img, 0, 0, 0);
                imagecolortransparent($tmp_img, $bk);
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                imagepng($tmp_img, $tpath, $this->settings->thumbQualityPNG);
                imagedestroy($img);
                imagedestroy($tmp_img);
              } else {
                imagedestroy($img);
              }
            } else {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $tmp_img  = imagecreatetruecolor($this->settings->thumbWidth, $this->settings->thumbHeight);
              $bk       = imagecolorallocate($tmp_img, 0, 0, 0);
              imagecolortransparent($tmp_img, $bk);
              imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $this->settings->thumbWidth, $this->settings->thumbHeight, $i_width, $i_height);
              imagepng($tmp_img, $tpath, $this->settings->thumbQualityPNG);
              imagedestroy($img);
              imagedestroy($tmp_img);
            }
            $added = 'yes';
          }
          break;
        case 'gif':
          // Get original image width and height..
          $img = imagecreatefromgif($path);
          if ($img && ($sizes[0] > $thisWidth || $sizes[1] > $thisHeight)) {
            // Are we maintaining aspect ratio?
            if ($this->settings->aspectRatio == 'yes') {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $scale    = min($thisWidth / $i_width, $thisHeight / $i_height);
              // For thumbnail, maintain aspect ratio of original image
              // If image is smaller or equal to new sizes, no resize is necessary
              if ($scale < 1) {
                $new_width  = floor($scale * $i_width);
                $new_height = floor($scale * $i_height);
                $tmp_img    = imagecreatetruecolor($new_width, $new_height);
                imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $i_width, $i_height);
                imagegif($tmp_img, $tpath, $this->settings->thumbQuality);
                imagedestroy($img);
                imagedestroy($tmp_img);
              } else {
                imagedestroy($img);
              }
            } else {
              $i_width  = imagesx($img);
              $i_height = imagesy($img);
              $tmp_img  = imagecreatetruecolor($this->settings->thumbWidth, $this->settings->thumbHeight);
              imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $this->settings->thumbWidth, $this->settings->thumbHeight, $i_width, $i_height);
              imagegif($tmp_img, $tpath, $this->settings->thumbQuality);
              imagedestroy($img);
              imagedestroy($tmp_img);
            }
            $added = 'yes';
          }
          break;
      }
    }
    // If image wasn`t created, copy the original?
    if ($added == 'no') {
      if (file_exists($tpath)) {
        @unlink($tpath);
      }
      if (@copy($path, $tpath)) {
        $sizes = getimagesize($path);
        $added = 'yes';
      }
    }
    return array(
      $sizes[0],
      $sizes[1],
      $added
    );
  }

  public function deleteProductPicture() {
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "pictures`
    WHERE `id` = '" . mc_digitSan($_GET['delete']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (@file_exists(mc_uploadServerPath() . $_GET['path'])) {
      @unlink(mc_uploadServerPath() . $_GET['path']);
    }
    if (@file_exists(mc_uploadServerPath() . $_GET['thumb'])) {
      @unlink(mc_uploadServerPath() . $_GET['thumb']);
    }
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'pictures'
    ));
    // If main display image is deleted, re-map it to next image..
    if (mc_rowCount('pictures') > 0 && isset($_GET['di']) && $_GET['di'] == 'yes') {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "pictures` ORDER BY `id` LIMIT 1") or die(mc_MySQLError(__LINE__, __FILE__));
      $P = mysqli_fetch_object($q);
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "pictures` SET `displayImg` = 'yes' WHERE `id` = '{$P->id}'") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    return $rows;
  }

  public function getNextPictureID($product) {
    $q_img = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "pictures`
             WHERE `product_id` = '{$product}'
             ORDER BY `id` DESC
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    $IMG = mysqli_fetch_object($q_img);
    return (mysqli_num_rows($q_img) == 0 ? '1' : ($IMG->id + 1));
  }

  public function addNewProduct() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $_POST = mc_safeImport($_POST);
    // Check restriction limit for free version..
    if (LICENCE_VER == 'locked') {
      if (mc_rowCount('products') + 1 > RESTR_PROD) {
        mc_restrictionLimitRedirect();
      }
    }
    // Set free shipping as yes for downloadable items..
    // If drop shipping is set, deactivate for downloads..
    if (isset($_POST['pDownload']) && $_POST['pDownload'] == 'yes') {
      $_POST['freeShipping'] = 'yes';
      if (isset($_POST['dropshipping'])) {
        $_POST['dropshipping'] = '0';
      }
    }
    $_POST['pDescription'] = mc_cleanBBInput($_POST['pDescription']);
    $isDown                = (isset($_POST['pDownload']) ? $_POST['pDownload'] : 'no');
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "products` (
    `pName`,
    `pTitle`,
    `pMetaKeys`,
    `pMetaDesc`,
    `pTags`,
    `pDescription`,
    `pShortDescription`,
    `pDownload`,
    `pDownloadPath`,
    `pVideo`,
    `pVideo2`,
    `pVideo3`,
    `pDownloadLimit`,
    `pCode`,
    `pStockNotify`,
    `pVisits`,
    `pEnable`,
    `pDateAdded`,
    `pWeight`,
    `pStock`,
    `pPrice`,
    `pOffer`,
    `pPurPrice`,
    `pOfferExpiry`,
    `rssBuildDate`,
    `enDisqus`,
    `freeShipping`,
    `pPurchase`,
    `minPurchaseQty`,
    `maxPurchaseQty`,
    `countryRestrictions`,
    `checkoutTextDisplay`,
    `rwslug`,
    `pAvailableText`,
    `pCube`,
    `pGuardian`,
    `dropshipping`,
    `expiry`
    ) VALUES (
    '{$_POST['pName']}',
    '{$_POST['pTitle']}',
    '{$_POST['pMetaKeys']}',
    '{$_POST['pMetaDesc']}',
    '{$_POST['pTags']}',
    '{$_POST['pDescription']}',
    '{$_POST['pShortDescription']}',
    '{$isDown}',
    '{$_POST['pDownloadPath']}',
    '{$_POST['pVideo']}',
    '{$_POST['pVideo2']}',
    '{$_POST['pVideo3']}',
    '" . (int) $_POST['pDownloadLimit'] . "',
    '{$_POST['pCode']}',
    '" . (int) $_POST['pStockNotify'] . "',
    '" . (int) $_POST['pVisits'] . "',
    '" . (isset($_POST['pEnable']) && in_array($_POST['pEnable'], array(
      'yes',
      'no'
    )) ? $_POST['pEnable'] : 'yes') . "',
    '" . date("Y-m-d") . "',
    '{$_POST['pWeight']}',
    '" . (int) $_POST['pStock'] . "',
    '" . mc_cleanInsertionPrice($_POST['pPrice']) . "',
    '" . ($_POST['pOffer'] > 0 ? mc_cleanInsertionPrice($_POST['pOffer']) : '') . "',
    '" . mc_cleanInsertionPrice($_POST['pPurPrice']) . "',
    '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings) : '0000-00-00') . "',
    '" . RSS_BUILD_DATE_FORMAT . "',
    '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
      'yes',
      'no'
    )) ? $_POST['enDisqus'] : 'no') . "',
    '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
      'yes',
      'no'
    )) ? $_POST['freeShipping'] : 'no') . "',
    '" . (isset($_POST['pPurchase']) && in_array($_POST['pPurchase'], array(
      'yes',
      'no'
    )) ? $_POST['pPurchase'] : 'yes') . "',
    '" . (int) $_POST['minPurchaseQty'] . "',
    '" . (int) $_POST['maxPurchaseQty'] . "',
    '" . (!empty($_POST['countryRestrictions']) ? serialize($_POST['countryRestrictions']) : '') . "',
    '{$_POST['checkoutTextDisplay']}',
    '{$_POST['rwslug']}',
    '{$_POST['pAvailableText']}',
    '" . ($_POST['pCube'] ? (int) $_POST['pCube'] : '0') . "',
    '" . ($_POST['pGuardian'] ? (int) $_POST['pGuardian'] : '0') . "',
    '" . (isset($_POST['dropshipping']) ? (int) $_POST['dropshipping'] : '0') . "',
    '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['expiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['expiry'], $this->settings) : '0000-00-00') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    // Add to categories...
    if (!empty($_POST['pCat'])) {
      foreach ($_POST['pCat'] AS $c) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
        `product`,`category`
        ) VALUES (
        '{$id}','{$c}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    } else {
      // If no categories were set, we add to all active parent categories..
      $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "categories`
             WHERE `catLevel` = '1'
             AND `childOf`    = '0'
             ORDER BY `id`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($PCATS = mysqli_fetch_object($q_c)) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
        `product`,`category`
        ) VALUES (
        '{$id}','{$PCATS->id}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    // Add to brands...
    if (!empty($_POST['pBrand'])) {
      foreach ($_POST['pBrand'] AS $b) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_brand` (
        `product`,`brand`
        ) VALUES (
        '{$id}','{$b}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    // Add main product image..
    if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] && $_FILES['image']['name']) {
      products::addProductPictures($id);
      define('MAIN_PIC_ADDED', 1);
    }
    // Additional images..
    if (!empty($_FILES['addimg']['tmp_name'])) {
      products::addAdditionalProductPictures($id);
    }
    // Are we copying product..
    // If yes, check for copying of pictures and attributes..
    if (isset($_GET['copyp'])) {
      // Copy attributes..
      if (isset($_POST['copyAttributes'])) {
        // Groups..
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
             WHERE `productID` = '" . mc_digitSan($_GET['copyp']) . "'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($AG = mysqli_fetch_object($q)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attr_groups` (
          `productID`,`groupName`,`orderBy`
          ) VALUES (
          '{$id}',
          '" . mc_safeSQL(mc_cleanData($AG->groupName)) . "',
          '{$AG->orderBy}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          $groupID = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
          // Attributes..
          $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
                WHERE `attrGroup` = '{$AG->id}'
                ORDER BY `orderBy`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
          while ($ATT = mysqli_fetch_object($q2)) {
            mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "attributes` (
            `productID`,
            `attrGroup`,
            `attrName`,
            `attrCost`,
            `attrStock`,
            `attrWeight`,
            `orderBy`
            ) VALUES (
            '{$id}',
            '{$groupID}',
            '" . mc_safeSQL(mc_cleanData($ATT->attrName)) . "',
            '{$ATT->attrCost}',
            '{$ATT->attrStock}',
            '{$ATT->attrWeight}',
            '{$ATT->orderBy}'
            )") or die(mc_MySQLError(__LINE__, __FILE__));
          }
        }
      }
      // Copy pictures..
      if (isset($_POST['copyPictures'])) {
        $q_pics = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "pictures`
                  WHERE `product_id` = '" . mc_digitSan($_GET['copyp']) . "'
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($PICS = mysqli_fetch_object($q_pics)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "pictures` (
          `product_id`,`picture_path`,`thumb_path`,`folder`,`dimensions`,`displayImg`
          ) VALUES (
          '{$id}','{$PICS->picture_path}','{$PICS->thumb_path}',
          '{$PICS->folder}','{$PICS->dimensions}','" . (defined('MAIN_PIC_ADDED') ? 'no' : $PICS->displayImg) . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      // Copy related products..
      if (isset($_POST['copyRelated'])) {
        $q_relt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "prod_relation`
                  WHERE `product` = '" . mc_digitSan($_GET['copyp']) . "'
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($REL = mysqli_fetch_object($q_relt)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_relation` (
          `product`,`related`
          ) VALUES (
          '{$id}','{$REL->related}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      // Copy MP3 previews..
      if (isset($_POST['copyMP3'])) {
        $q_mp3f = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "mp3`
                  WHERE `product_id` = '" . mc_digitSan($_GET['copyp']) . "'
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($MP3 = mysqli_fetch_object($q_mp3f)) {
          $MP3->fileName   = mc_safeSQL($MP3->fileName);
          $MP3->fileFolder = mc_safeSQL($MP3->fileFolder);
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "mp3` (
          `product_id`,`filePath`,`fileName`,`fileFolder`
          ) VALUES (
          '{$id}','{$MP3->filePath}','{$MP3->fileName}','{$MP3->fileFolder}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
      // Copy personalisation..
      if (isset($_POST['copyPersonalisation']) && $isDown == 'no') {
        $q_perf = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "personalisation`
                  WHERE `productID` = '" . mc_digitSan($_GET['copyp']) . "'
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($PERS = mysqli_fetch_object($q_perf)) {
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "personalisation` (
          `productID`,`persInstructions`,`persOptions`,`maxChars`,
          `persAddCost`,`enabled`,`boxType`,`reqField`
          ) VALUES (
          '{$id}','" . mc_safeSQL($PERS->persInstructions) . "','" . mc_safeSQL($PERS->persOptions) . "','{$PERS->maxChars}',
          '{$PERS->persAddCost}','{$PERS->enabled}','{$PERS->boxType}','{$PERS->reqField}'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    return $id;
  }

  public function batchUpdateProducts() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $_POST  = mc_safeImport($_POST);
    $SQL    = array();
    $count  = 0;
    // Update if update field array is blank..
    $fields = array(
      'pBrand',
      'pDownload',
      'pDownloadLimit',
      'pCode',
      'pStockNotify',
      'pVisits',
      'pEnable',
      'pWeight',
      'pStock',
      'pPrice',
      'pPurPrice',
      'pOffer',
      'pOfferExpiry',
      'enDisqus',
      'freeShipping',
      'pDateAdded',
      'pPurchase',
      'minPurchaseQty',
      'maxPurchaseQty',
      'countryRestrictions',
      'checkoutTextDisplay',
      'pCat',
      'pAvailableText',
      'pCube',
      'pGuardian',
      'expiry',
      'dropshipping'
    );
    if (empty($_SESSION['batchFieldPrefs'])) {
      $_SESSION['batchFieldPrefs'] = array();
    }
    // Loop through fields..
    foreach ($fields AS $f) {
      if (!in_array($f, $_SESSION['batchFieldPrefs'])) {
        ++$count;
        switch($f) {
          // Update categories...
          case 'pCat':
            if (!empty($_POST['pCat'])) {
              // Delete current categories..
              mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_category` WHERE `product` IN(" . mc_safeSQL($_POST['productIDs']) . ")") or die(mc_MySQLError(__LINE__, __FILE__));
              mc_tableTruncationRoutine(array(
                'prod_category'
              ));
              // Add new..
              foreach (explode(',', $_POST['productIDs']) AS $prID) {
                foreach ($_POST['pCat'] AS $c) {
                  mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
                  `product`,`category`
                  ) VALUES (
                  '{$prID}','{$c}'
                  )") or die(mc_MySQLError(__LINE__, __FILE__));
                }
              }
            }
            break;
          case 'pBrand':
            if (!empty($_POST['pBrand'])) {
              // Delete current brands..
              mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_brand` WHERE `product` IN(" . mc_safeSQL($_POST['productIDs']) . ")") or die(mc_MySQLError(__LINE__, __FILE__));
              mc_tableTruncationRoutine(array(
                'prod_brand'
              ));
              // Add new..
              foreach (explode(',', $_POST['productIDs']) AS $prID) {
                foreach ($_POST['pBrand'] AS $b) {
                  mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_brand` (
                  `product`,`brand`
                  ) VALUES (
                  '{$prID}','{$b}'
                  )") or die(mc_MySQLError(__LINE__, __FILE__));
                }
              }
            }
            break;
          case 'pDownload':
            $SQL[] = "`pDownload` = '" . (isset($_POST['pDownload']) && in_array($_POST['pDownload'], array(
              'yes',
              'no'
            )) ? $_POST['pDownload'] : 'no') . "'";
            break;
          case 'pDownloadLimit':
            $SQL[] = "`pDownloadLimit` = '" . (int) $_POST['pDownloadLimit'] . "'";
            break;
          case 'pCode':
            $SQL[] = "`pCode` = '{$_POST['pCode']}'";
            break;
          case 'pStockNotify':
            $SQL[] = "`pStockNotify` = '" . (int) $_POST['pStockNotify'] . "'";
            break;
          case 'pVisits':
            $SQL[] = "`pVisits` = '" . (int) $_POST['pVisits'] . "'";
            break;
          case 'pEnable':
            $SQL[] = "`pEnable` = '" . (isset($_POST['pEnable']) && in_array($_POST['pEnable'], array(
              'yes',
              'no'
            )) ? $_POST['pEnable'] : 'yes') . "'";
            break;
          case 'pWeight':
            $SQL[] = "`pWeight` = '" . (isset($_POST['pDownload']) && $_POST['pDownload'] == 'no' ? $_POST['pWeight'] : '0') . "'";
            break;
          case 'pStock':
            $SQL[] = "`pStock` = '" . (int) $_POST['pStock'] . "'";
            break;
          case 'pAvailableText':
            $SQL[] = "`pAvailableText` = '{$_POST['pAvailableText']}'";
            break;
          case 'pPrice':
            $SQL[] = "`pPrice` = '" . mc_cleanInsertionPrice($_POST['pPrice']) . "'";
            break;
          case 'pPurPrice':
            $SQL[] = "`pPurPrice` = '" . mc_cleanInsertionPrice($_POST['pPurPrice']) . "'";
            break;
          case 'pOffer':
            $SQL[] = "`pOffer` = '" . ($_POST['pOffer'] > 0 ? mc_cleanInsertionPrice($_POST['pOffer']) : '') . "'";
            break;
          case 'pOfferExpiry':
            $SQL[] = "`pOfferExpiry` = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings) : '0000-00-00') . "'";
            break;
          case 'pDateAdded':
            $SQL[] = "`pDateAdded` = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['pDateAdded'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['pDateAdded'], $this->settings) : '0000-00-00') . "'";
            break;
          case 'enDisqus':
            $SQL[] = "`enDisqus` = '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
              'yes',
              'no'
            )) ? $_POST['enDisqus'] : 'no') . "'";
            break;
          case 'freeShipping':
            if (isset($_POST['pDownload']) && $_POST['pDownload'] == 'yes') {
              $_POST['freeShipping'] = 'yes';
            }
            $SQL[] = "`freeShipping` = '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
              'yes',
              'no'
            )) ? $_POST['freeShipping'] : 'no') . "'";
            break;
          case 'pPurchase':
            $SQL[] = "`pPurchase` = '" . (isset($_POST['pPurchase']) && in_array($_POST['pPurchase'], array(
              'yes',
              'no'
            )) ? $_POST['pPurchase'] : 'yes') . "'";
            break;
          case 'minPurchaseQty':
            $SQL[] = "`minPurchaseQty` = '" . (int) $_POST['minPurchaseQty'] . "'";
            break;
          case 'maxPurchaseQty':
            $SQL[] = "`maxPurchaseQty` = '" . (int) $_POST['maxPurchaseQty'] . "'";
            break;
          case 'pCube':
            $SQL[] = "`pCube` = '" . (int) $_POST['pCube'] . "'";
            break;
          case 'pGuardian':
            $SQL[] = "`pGuardian` = '" . (int) $_POST['pGuardian'] . "'";
            break;
          case 'pAvailableText':
            $SQL[] = "`pAvailableText` = '{$_POST['pAvailableText']}'";
            break;
          case 'countryRestrictions':
            $SQL[] = "`countryRestrictions` = '" . (!empty($_POST['countryRestrictions']) ? serialize($_POST['countryRestrictions']) : '') . "'";
            break;
          case 'checkoutTextDisplay':
            $SQL[] = "`checkoutTextDisplay` = '{$_POST['checkoutTextDisplay']}'";
            break;
          case 'expiry':
            $SQL[] = "`expiry` = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['expiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['expiry'], $this->settings) : '0000-00-00') . "'";
            break;
          case 'dropshipping':
            $SQL[] = "`dropshipping` = '" . (int) $_POST['dropshipping'] . "'";
            break;
        }
      }
    }
    // Do we have something to update?
    if (!empty($SQL)) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
      " . implode(',', $SQL) . ",
      `rssBuildDate` = '" . RSS_BUILD_DATE_FORMAT . "'
      WHERE `id` IN(" . mc_safeSQL($_POST['productIDs']) . ")
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    return $count;
  }

  public function updateProduct() {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $_POST = mc_safeImport($_POST);
    // Set free shipping as yes for downloadable items..
    // If drop shipping is set, deactivate for downloads..
    if (isset($_POST['pDownload']) && $_POST['pDownload'] == 'yes') {
      $_POST['freeShipping'] = 'yes';
      if (isset($_POST['dropshipping'])) {
        $_POST['dropshipping'] = '0';
      }
    }
    $_POST['pDescription'] = mc_cleanBBInput($_POST['pDescription']);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pName`                = '{$_POST['pName']}',
    `pTitle`               = '{$_POST['pTitle']}',
    `pMetaKeys`            = '{$_POST['pMetaKeys']}',
    `pMetaDesc`            = '{$_POST['pMetaDesc']}',
    `pTags`                = '{$_POST['pTags']}',
    `pDescription`         = '{$_POST['pDescription']}',
    `pShortDescription`    = '{$_POST['pShortDescription']}',
    `pDownload`            = '" . (isset($_POST['pDownload']) && in_array($_POST['pDownload'], array(
      'yes',
      'no'
    )) ? $_POST['pDownload'] : 'no') . "',
    `pDownloadPath`        = '{$_POST['pDownloadPath']}',
    `pVideo`               = '{$_POST['pVideo']}',
    `pVideo2`              = '{$_POST['pVideo2']}',
    `pVideo3`              = '{$_POST['pVideo3']}',
    `pDownloadLimit`       = '" . (int) $_POST['pDownloadLimit'] . "',
    `pCode`                = '{$_POST['pCode']}',
    `pStockNotify`         = '" . (int) $_POST['pStockNotify'] . "',
    `pVisits`              = '" . (int) $_POST['pVisits'] . "',
    `pEnable`              = '" . (isset($_POST['pEnable']) && in_array($_POST['pEnable'], array(
      'yes',
      'no'
    )) ? $_POST['pEnable'] : 'yes') . "',
    `pWeight`              = '{$_POST['pWeight']}',
    `pStock`               = '" . (int) $_POST['pStock'] . "',
    `pPrice`               = '" . mc_cleanInsertionPrice($_POST['pPrice']) . "',
    `pPurPrice`            = '" . mc_cleanInsertionPrice($_POST['pPurPrice']) . "',
    `pOffer`               = '" . ($_POST['pOffer'] > 0 ? mc_cleanInsertionPrice($_POST['pOffer']) : '') . "',
    `pOfferExpiry`         = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['pOfferExpiry'], $this->settings) : '0000-00-00') . "',
    `enDisqus`             = '" . (isset($_POST['enDisqus']) && in_array($_POST['enDisqus'], array(
      'yes',
      'no'
    )) ? $_POST['enDisqus'] : 'no') . "',
    `freeShipping`         = '" . (isset($_POST['freeShipping']) && in_array($_POST['freeShipping'], array(
      'yes',
      'no'
    )) ? $_POST['freeShipping'] : 'no') . "',
    `rssBuildDate`         = '" . RSS_BUILD_DATE_FORMAT . "',
    `pPurchase`            = '" . (isset($_POST['pPurchase']) && in_array($_POST['pPurchase'], array(
      'yes',
      'no'
    )) ? $_POST['pPurchase'] : 'yes') . "',
    `minPurchaseQty`       = '" . (int) $_POST['minPurchaseQty'] . "',
    `maxPurchaseQty`       = '" . (int) $_POST['maxPurchaseQty'] . "',
    `countryRestrictions`  = '" . (!empty($_POST['countryRestrictions']) ? serialize($_POST['countryRestrictions']) : '') . "',
    `checkoutTextDisplay`  = '{$_POST['checkoutTextDisplay']}',
    `rwslug`               = '{$_POST['rwslug']}',
    `pAvailableText`       = '{$_POST['pAvailableText']}',
    `pCube`                = '" . ($_POST['pCube'] ? (int) $_POST['pCube'] : '0') . "',
    `pGuardian`            = '" . ($_POST['pGuardian'] ? (int) $_POST['pGuardian'] : '0') . "',
    `dropshipping`         = '" . (isset($_POST['dropshipping']) ? (int) $_POST['dropshipping'] : '0') . "',
    `expiry`               = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['expiry'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['expiry'], $this->settings) : '0000-00-00') . "',
    `pDateAdded`           = '" . (mc_checkValidDate(mc_convertCalToSQLFormat($_POST['pDateAdded'], $this->settings)) != '0000-00-00' ? mc_convertCalToSQLFormat($_POST['pDateAdded'], $this->settings) : '0000-00-00') . "'
    WHERE `id`             = '" . mc_digitSan($_GET['edit']) . "'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Update categories...
    if (!empty($_POST['pCat'])) {
      // Delete current categories..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_category` WHERE `product` = '" . mc_digitSan($_GET['edit']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
      mc_tableTruncationRoutine(array(
        'prod_category'
      ));
      // Add new..
      foreach ($_POST['pCat'] AS $c) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
        `product`,`category`
        ) VALUES (
        '" . mc_digitSan($_GET['edit']) . "','{$c}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    } else {
      // Delete current categories..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_category` WHERE `product` = '" . mc_digitSan($_GET['edit']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
      mc_tableTruncationRoutine(array(
        'prod_category'
      ));
      // If no categories were set, we add to all active parent categories..
      $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "categories`
             WHERE `catLevel` = '1'
             AND `childOf`    = '0'
             ORDER BY `id`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
      while ($PCATS = mysqli_fetch_object($q_c)) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_category` (
        `product`,`category`
        ) VALUES (
        '" . mc_digitSan($_GET['edit']) . "','{$PCATS->id}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    // Update brands...
    if (!empty($_POST['pBrand'])) {
      // Delete current brands..
      mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_brand` WHERE `product` = '" . mc_digitSan($_GET['edit']) . "'") or die(mc_MySQLError(__LINE__, __FILE__));
      mc_tableTruncationRoutine(array(
        'prod_brand'
      ));
      // Add new..
      foreach ($_POST['pBrand'] AS $b) {
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "prod_brand` (
        `product`,`brand`
        ) VALUES (
        '" . mc_digitSan($_GET['edit']) . "','{$b}'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
  }

  // Delete product..
  public function deleteProduct($id = 0) {
    // Cache clear..
    $this->cache->clear_cache_file(array(
      'categories',
      'homepage-cats',
      'sitemap-cats'
    ));
    $del  = (isset($_GET['delete']) && ctype_digit($_GET['delete']) ? $_GET['delete'] : '0');
    if ($id > 0) {
      $del = $id;
    }
    $P    = mc_getTableData('products', 'id', $del);
    $rows = 0;
    // For sales data, apply names to purchases table for deleted products..
    $qs = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purchases` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($SALE = mysqli_fetch_object($qs)) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
      `deletedProductName`  = '" . mc_safeSQL($P->pName) . "'
      WHERE `id`            = '{$SALE->id}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    // Delete related data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_relation` WHERE `product` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete mp3 data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "mp3` WHERE `product_id` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete personalisation data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "personalisation` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete picture data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "pictures` WHERE `product_id` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete attribute data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attributes` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "attr_groups` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete category data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "prod_category` WHERE `product` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete sale data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "purch_pers` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "comparisons` WHERE `thisProduct` = '{$del}' OR `thatProduct` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "activation_history` WHERE `products` = 'p" . $del . "'") or die(mc_MySQLError(__LINE__, __FILE__));
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "click_history` WHERE `productID` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Account related
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "accounts_wish` WHERE `product` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    // Delete product data..
    mysqli_query($GLOBALS["___msw_sqli"], "DELETE FROM `" . DB_PREFIX . "products` WHERE `id` = '{$del}'") or die(mc_MySQLError(__LINE__, __FILE__));
    $rows = mysqli_affected_rows($GLOBALS["___msw_sqli"]);
    mc_tableTruncationRoutine(array(
      'prod_relation',
      'mp3',
      'personalisation',
      'pictures',
      'attributes',
      'attr_groups',
      'prod_category',
      'purch_pers',
      'comparisons',
      'products',
      'click_history',
      'accounts_wish'
    ));
    return $rows;
  }

}

?>