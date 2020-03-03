<?php

class mcProducts {

  public $settings;
  public $rates;
  public $cache;
  public $system;
  public $social;
  public $rwr;
  private $predesctag = '<hr>';

  public function productList($screen, $config = array()) {
    global $public_category11, $public_category10, $public_category12, $public_category20,
    $public_category9, $public_category10, $public_category13, $public_category14, $public_category17,
    $public_category18, $public_category15, $public_category18, $public_product9, $public_category28,
    $public_product42, $public_search3, $mc_product, $limit, $CAT;
    $cloop      = 0;
    $products   = '';
    $additional = '';
    $points     = '';
    // Are price point filters set?
    if (!empty($_SESSION['mc_points_filters_' . mc_encrypt(SECRET_KEY)])) {
      $p1 = $_SESSION['mc_points_filters_' . mc_encrypt(SECRET_KEY)][0];
      $p2 = $_SESSION['mc_points_filters_' . mc_encrypt(SECRET_KEY)][1];
      if (defined('MC_TRADE_DISCOUNT')) {
        $points = 'AND `pPrice`*100 >= \'' . mc_safeSQL($p1 * 100) . '\' AND `pPrice`*100 <= \'' . mc_safeSQL($p2 * 100) . '\'';
      } else {
        $points = 'AND IF(`pOffer`>0,`pOffer`,`pPrice`)*100 >= \'' . mc_safeSQL($p1 * 100) . '\' AND IF(`pOffer`>0,`pOffer`,`pPrice`)*100 <= \'' . mc_safeSQL($p2 * 100) . '\'';
      }
    }
    switch($screen) {
      case 'home':
        $sql        = '';
        $orderBy    = '';
        $loadTmp    = 'categories/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        $limit      = ($this->settings->homeProdValue && $this->settings->homeProdValue > 0 ? $this->settings->homeProdValue : HOMEPAGE_PRODUCTS_LIMIT);
        if ($this->settings->homeProdIDs) {
          // Check IDs..
          $hpIDs = array();
          foreach(explode(',', $this->settings->homeProdIDs) AS $homeProdIDs) {
            if ($homeProdIDs) {
              $hpIDs[] = (int) $homeProdIDs;
            }
          }
          $sql     = 'WHERE `' . DB_PREFIX . 'products`.`id` IN(' . mc_safeSQL(implode(',', $hpIDs)) . ')';
          $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id`';
        } else {
          // Was 'all' checked? If so, remove it..
          if (substr($this->settings->homeProdCats, 0, 3) == 'all') {
            $this->settings->homeProdCats = substr($this->settings->homeProdCats, 4);
          }
          if ($this->settings->homeProdCats) {
            // Check IDs..
            $hcIDs = array();
            foreach(explode(',', $this->settings->homeProdCats) AS $homeCatIDs) {
              if ($homeCatIDs) {
                $hcIDs[] = (int) $homeCatIDs;
              }
            }
            $sql = 'WHERE `category` IN(' . mc_safeSQL(implode(',', $hcIDs)) . ')';
          } else {
            $sql = 'WHERE `category` > 0';
          }
          switch($this->settings->homeProdType) {
            case 'latest':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
              break;
            case 'random':
              $orderBy = 'ORDER BY rand()';
              break;
          }
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 $sql
                 AND `pEnable` = 'yes'
                 $additional
                 " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 $orderBy
                 LIMIT $limit
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      case 'category':
        $loadTmp    = 'categories/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        $orderBy    = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
        if (isset($_GET['category'])) {
          $_GET['c'] = (int) $_GET['category'];
        }
        if (isset($_GET['order'])) {
          switch($_GET['order']) {
            case 'price-low':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100';
              }
              break;
            case 'price-high':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100 DESC';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100 DESC';
              }
              break;
            case 'title-az':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              break;
            case 'title-za':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName` DESC';
              break;
            case 'date-new':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded` DESC';
              break;
            case 'date-old':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded`';
              break;
            case 'stock':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pStock`';
              break;
            case 'multi-buy':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pMultiBuy` DESC';
              break;
            case 'all-items':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              $limit   = 0;
              break;
            default:
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
              break;
          }
        } else {
          $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
        }
        // Are brand filters set?
        if (isset($_GET['brand']) && (int) $_GET['brand'] > 0) {
          $additional = 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
        }
        if (isset($config['brands']) && $config['brands']) {
          if (isset($_GET['cat']) && (int) $_GET['cat'] > 0) {
            $additional = "AND `category` = '" . (int) $_GET['cat'] . "'";
          }
          $brandSQL = '';
          if (strpos($config['brands'], '_') !== false) {
            $chop = explode('_', $config['brands']);
            for ($i=0; $i<count($chop); $i++) {
              $brandSQL .= ($i > 0 ? ' OR ' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\'';
            }
            $brandSQL .= ')' . mc_defineNewline();
          } else {
            $brandSQL = 'AND `brand` = \'' . (int) $config['brands'] . '\'' . mc_defineNewline();
          }
          $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                   `" . DB_PREFIX . "products`.`id` AS `pid`
                   FROM `" . DB_PREFIX . "products`
                   LEFT JOIN `" . DB_PREFIX . "prod_category`
                    ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
                   LEFT JOIN `" . DB_PREFIX . "categories`
                    ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                   LEFT JOIN `" . DB_PREFIX . "prod_brand`
                    ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_brand`.`product`
                   WHERE `pEnable`                 = 'yes'
                   $brandSQL
                   $additional
                   $points
                   AND " . MC_CATG_PMS_SQL . "
                   GROUP BY `" . DB_PREFIX . "products`.`id`
                   $orderBy
                   LIMIT $limit," . $this->settings->productsPerPage . "
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
        } else {
          $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                   `" . DB_PREFIX . "products`.`id` AS `pid`
                   FROM `" . DB_PREFIX . "products`
                   LEFT JOIN `" . DB_PREFIX . "prod_category`
                    ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
                   LEFT JOIN `" . DB_PREFIX . "categories`
                    ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                   LEFT JOIN `" . DB_PREFIX . "prod_brand`
                    ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_brand`.`product`
                   WHERE `category`                = '" . (isset($config['catover']) && $config['catover'] > 0 ? $config['catover'] : $_GET['c']) . "'
                   AND `pEnable`                   = 'yes'
                   $additional
                   $points
                   " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                   AND " . MC_CATG_PMS_SQL . "
                   GROUP BY `" . DB_PREFIX . "products`.`id`
                   $orderBy
                   LIMIT $limit," . $this->settings->productsPerPage . "
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      case 'specials':
        $loadTmp    = 'categories/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        $orderBy    = 'ORDER BY `' . DB_PREFIX . 'products`.`' . ORDER_SPECIAL_OFFERS . '`';
        if (isset($_GET['order'])) {
          switch($_GET['order']) {
            case 'price-low':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100';
              }
              break;
            case 'price-high':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100 DESC';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100 DESC';
              }
              break;
            case 'title-az':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              break;
            case 'title-za':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName` DESC';
              break;
            case 'date-new':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded` DESC';
              break;
            case 'date-old':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded`';
              break;
            case 'stock':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pStock`';
              break;
            case 'multi-buy':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pMultiBuy` DESC';
              break;
            case 'all-items':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              $limit   = 0;
              break;
            default:
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`' . ORDER_SPECIAL_OFFERS . '`';
              break;
          }
        }
        // Are brand filters set?
        if (isset($_GET['brand'])) {
          if (strpos($_GET['brand'],',') !== false) {
            $chop = explode(',', $_GET['brand']);
            for ($i=0; $i<count($chop); $i++) {
              $additional .= mc_defineNewline() . ($i > 0 ? ' OR ' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\'';
            }
            $additional .= ')' . mc_defineNewline();
          } else {
            $additional = mc_defineNewline() . 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
          }
        }
        if (isset($_GET['cat']) && $_GET['cat'] > 0) {
          $additional .= mc_defineNewline() . 'AND `category` = \'' . (int) $_GET['cat'] . '\'';
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 LEFT JOIN `" . DB_PREFIX . "prod_brand`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_brand`.`product`
                 WHERE `pOffer`                  > 0
                 AND `pEnable`                   = 'yes'
                 $additional
                 $points
                 " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 $orderBy
                 LIMIT $limit," . $this->settings->productsPerPage . "
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      case 'latest':
        $loadTmp    = 'categories/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        $orderBy    = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
        if (isset($_GET['order'])) {
          switch($_GET['order']) {
            case 'price-low':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100';
              }
              break;
            case 'price-high':
              if (defined('MC_TRADE_DISCOUNT')) {
                $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100 DESC';
              } else {
                $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100 DESC';
              }
              break;
            case 'title-az':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              break;
            case 'title-za':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName` DESC';
              break;
            case 'date-new':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded` DESC';
              break;
            case 'date-old':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded`';
              break;
            case 'stock':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pStock`';
              break;
            case 'multi-buy':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pMultiBuy` DESC';
              break;
            default:
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
              break;
          }
        }
        // Are brand filters set?
        if (isset($_GET['brand'])) {
          if (strpos($_GET['brand'],',') !== false) {
            $chop = explode(',', $_GET['brand']);
            for ($i=0; $i<count($chop); $i++) {
              $additional .= mc_defineNewline() . ($i > 0 ? ' OR ' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\'';
            }
            $additional .= ')' . mc_defineNewline();
          } else {
            $additional = mc_defineNewline() . 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
          }
        }
        if (isset($_GET['cat']) && $_GET['cat'] > 0) {
          $additional .= mc_defineNewline() . 'AND `category` = \'' . (int) $_GET['cat'] . '\'';
        }
        switch($this->settings->latestProdDuration) {
          case 'days':
            $interval = 'INTERVAL ' . (int) $this->settings->latestProdLimit . ' DAY';
            break;
          case 'months':
            $interval = 'INTERVAL ' . (int) $this->settings->latestProdLimit . ' MONTH';
            break;
          case 'years':
            $interval = 'INTERVAL ' . (int) $this->settings->latestProdLimit . ' YEAR';
            break;
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 LEFT JOIN `" . DB_PREFIX . "prod_brand`
                  ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_brand`.`product`
                 WHERE `pEnable`                = 'yes'
                 " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                 $additional
                 $points
                 AND (`pDateAdded`              BETWEEN DATE_SUB('" . date("Y-m-d") . "',$interval) AND '" . date("Y-m-d") . "')
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 $orderBy
                 LIMIT $limit," . $this->settings->productsPerPage . "
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      case 'related':
        $loadTmp  = 'products/product-related-item.htm';
        $html     = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-related-wrapper.htm');
        $query    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                    `" . DB_PREFIX . "products`.`id` AS `pid`
                    FROM `" . DB_PREFIX . "prod_relation`
                    LEFT JOIN `" . DB_PREFIX . "products`
                     ON `" . DB_PREFIX . "products`.`id`           = `" . DB_PREFIX . "prod_relation`.`related`
                    LEFT JOIN `" . DB_PREFIX . "prod_category`
                     ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                    LEFT JOIN `" . DB_PREFIX . "categories`
                     ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                    WHERE `" . DB_PREFIX . "prod_relation`.`product`  = '{$config['rcat']}'
                    AND `pEnable`                               = 'yes'
                    " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                    AND " . MC_CATG_PMS_SQL . "
                    GROUP BY `" . DB_PREFIX . "products`.`id`
                    ORDER BY `" . RELATED_ORDER_BY . "`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case 'comparison':
        $matchIDs = '';
        $loadTmp    = 'products/product-comparison-item.htm';
        $html     = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-comparison-wrapper.htm');
        // Get product array..
        $qcp = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `saleID`,GROUP_CONCAT(DISTINCT(`productID`) ORDER BY `productID`) AS `purchaseList` FROM `" . DB_PREFIX . "purchases`
               WHERE `saleConfirmation` = 'yes'
               GROUP BY `saleID`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($C = mysqli_fetch_object($qcp)) {
          $boom = explode(',', $C->purchaseList);
          if (in_array($config['rcat'], $boom) && $C->purchaseList) {
            $matchIDs .= ',' . $C->purchaseList;
          }
        }
        // If no match string, display nothing..
        if (!$matchIDs) {
          return '';
        } else {
          // Explode into array, removing first comma..
          $loopIDs = explode(',', substr($matchIDs, 1));
          // Take out duplicates..
          $loopIDs = array_unique($loopIDs);
        }
        // Empty array returns nothing..
        if (empty($loopIDs)) {
          return '';
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 WHERE `" . DB_PREFIX . "products`.`id` IN(" . mc_safeSQL(implode(',', $loopIDs)) . ")
                 AND `" . DB_PREFIX . "products`.`id`   != '{$config['rcat']}'
                 AND `pEnable`   = 'yes'
                 " . ($this->settings->showOutofStock == 'no' ? 'AND pStock > 0' : '') . "
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 ORDER BY `" . COMPARISON_ORDER_BY . "`
                 LIMIT " . $this->settings->saleComparisonItems . "
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        break;
      case 'search':
        $loadTmp = 'categories/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        if (!isset($_GET['sk'])) {
          return mc_nothingToShow($public_search3, false, 'nothing-to-show-search.htm');
        }
        $searchProds = mc_getTableData('search_index', 'searchCode', $_GET['sk']);
        if (!isset($searchProds->results) || $searchProds->results == '') {
          return (isset($config['count']) ? 0 : mc_nothingToShow($public_search3, false, 'nothing-to-show-search.htm'));
        }
        // Ordering and filtering..
        $cat     = '';
        $orderBy = 'ORDER BY `' . SEARCH_ORDER_BY . '`';
        switch($_GET['order']) {
          case 'price-low':
            if (defined('MC_TRADE_DISCOUNT')) {
              $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100';
            } else {
              $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100';
            }
            break;
          case 'price-high':
            if (defined('MC_TRADE_DISCOUNT')) {
              $orderBy = 'ORDER BY REPLACE(`' . DB_PREFIX . 'products`.`pPrice`,\',\',\'\')*100 DESC';
            } else {
              $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100 DESC';
            }
            break;
          case 'title-az':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
            break;
          case 'title-za':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName` DESC';
            break;
          case 'date-new':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded` DESC';
            break;
          case 'date-old':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded`';
            break;
          case 'all-items':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
            $limit   = 0;
            break;
          case 'stock':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pStock`';
            break;
          case 'multi-buy':
            $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pMultiBuy` DESC';
            break;
        }
        // Are brand filters set?
        if (isset($_GET['brand'])) {
          if (strpos($_GET['brand'],',') !== false) {
            $chop = explode(',', $_GET['brand']);
            for ($i=0; $i<count($chop); $i++) {
              $additional .= mc_defineNewline() . ($i > 0 ? ' OR ' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\'';
            }
            $additional .= ')' . mc_defineNewline();
          } else {
            $additional = mc_defineNewline() . 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
          }
        }
        if (isset($_GET['cat']) && $_GET['cat'] > 0) {
          $additional .= mc_defineNewline() . 'AND `' . DB_PREFIX . 'prod_category`.`category` = \'' . (int) $_GET['cat'] . '\'';
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 LEFT JOIN `" . DB_PREFIX . "prod_brand`
                  ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_brand`.`product`
                 WHERE `pEnable` = 'yes'
                 AND `" . DB_PREFIX . "products`.`id` IN(" . unserialize($searchProds->results) . ")
                 $additional
                 $points
                 " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 $orderBy
                 LIMIT $limit," . $this->settings->productsPerPage . "
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      case 'wishlist':
        $loadTmp    = 'categories/wish-list/category-product-' . (defined('MC_CATVIEW') ? MC_CATVIEW : 'list') . '-view.htm';
        $orderBy    = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
        if (isset($_GET['order'])) {
          switch($_GET['order']) {
            case 'price-low':
              $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100';
              break;
            case 'price-high':
              $orderBy = 'ORDER BY REPLACE(IF(`' . DB_PREFIX . 'products`.`pOffer`>0,`' . DB_PREFIX . 'products`.`pOffer`,`' . DB_PREFIX . 'products`.`pPrice`),\',\',\'\')*100 DESC';
              break;
            case 'title-az':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName`';
              break;
            case 'title-za':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pName` DESC';
              break;
            case 'date-new':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded` DESC';
              break;
            case 'date-old':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pDateAdded`';
              break;
            case 'stock':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pStock`';
              break;
            case 'multi-buy':
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`pMultiBuy` DESC';
              break;
            default:
              $orderBy = 'ORDER BY `' . DB_PREFIX . 'products`.`id` DESC';
              break;
          }
        }
        // Are brand filters set?
        if (isset($_GET['brand'])) {
          if (strpos($_GET['brand'],',') !== false) {
            $chop = explode(',', $_GET['brand']);
            for ($i=0; $i<count($chop); $i++) {
              $additional .= mc_defineNewline() . ($i > 0 ? ' OR ' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\'';
            }
            $additional .= ')' . mc_defineNewline();
          } else {
            $additional = mc_defineNewline() . 'AND `brand` = \'' . (int) $_GET['brand'] . '\'';
          }
        }
        if (isset($_GET['cat']) && $_GET['cat'] > 0) {
          $additional .= mc_defineNewline() . 'AND `category` = \'' . (int) $_GET['cat'] . '\'';
        }
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
                 `" . DB_PREFIX . "products`.`id` AS `pid`,
                 `" . DB_PREFIX . "accounts_wish`.`id` AS `wlid`
                 FROM `" . DB_PREFIX . "products`
                 LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
                 LEFT JOIN `" . DB_PREFIX . "categories`
                  ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
                 LEFT JOIN `" . DB_PREFIX . "prod_brand`
                  ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_brand`.`product`
                 LEFT JOIN `" . DB_PREFIX . "accounts_wish`
                  ON `" . DB_PREFIX . "accounts_wish`.`product`  = `" . DB_PREFIX . "products`.`id`
                 WHERE `" . DB_PREFIX . "products`.`pEnable` = 'yes'
                 AND `" . DB_PREFIX . "accounts_wish`.`account` = '{$config['accid']}'
                 " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
                 $additional
                 $points
                 AND " . MC_CATG_PMS_SQL . "
                 GROUP BY `" . DB_PREFIX . "products`.`id`
                 $orderBy
                 LIMIT $limit," . $this->settings->productsPerPage . "
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        if (isset($config['count'])) {
          $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
          return (isset($c->rows) ? $c->rows : '0');
        }
        break;
      default:
        return '<div class="alert alert-danger"><i class="fa fa-warning fa-fw"></i> [Fatal Error] Nothing is configured for the "<b>' . mc_safeHTML($screen) . '</b>" parameter.</div>';
        break;
    }
    while ($PR = mysqli_fetch_object($query)) {
      ++$cloop;
      $PR->pStock = (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0 ? MC_TRADE_STOCK : $PR->pStock);;
      $noStock    = false;
      $sGif       = '';
      $images     = mcProducts::loadProductImage($PR->pid);
      $oimages    = mcProducts::buildProductImages($PR->pid, $PR->pName, 'no', (isset($images[2]) ? $images[2] : '0'));
      $atc        = '';
      $plink      = str_replace(array(
        '{product_url}',
        '{view_product}',
        '{theme_folder}'
      ), array(
        $this->rwr->url(array(
          $this->rwr->config['slugs']['prd'] . '/' . $PR->pid . '/' . ($PR->rwslug ? $PR->rwslug : $this->rwr->title($PR->pName)),
          'pd=' . $PR->pid
        )),
        mc_filterJS($public_category13),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-product-link2.htm'));
      $link    = str_replace(array(
        '{product_url}',
        '{product_title}',
        '{theme_folder}'
      ), array(
        $this->rwr->url(array(
          $this->rwr->config['slugs']['prd'] . '/' . $PR->pid . '/' . ($PR->rwslug ? $PR->rwslug : $this->rwr->title($PR->pName)),
          'pd=' . $PR->pid
        )),
        $public_category18,
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-product-link.htm'));
      // Show out of stock button..
      if (in_array($this->settings->showOutofStock, array(
        'cat',
        'yes'
      ))) {
        if ($PR->pStock == 0) {
          if ($this->settings->showOutofStock == 'cat') {
            $link = str_replace(array(
              '{product_url}',
              '{product_title}',
              '{theme_folder}'
            ), array(
              $this->rwr->url(array(
                $this->rwr->config['slugs']['prd'] . '/' . $PR->pid . '/' . ($PR->rwslug ? $PR->rwslug : $this->rwr->title($PR->pName)),
                'pd=' . $PR->pid
              )),
              $mc_product[0],
              THEME_FOLDER
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-outofstock-link.htm'));
          }
          $noStock = true;
        }
      }
      // Show wishlist button?
      $enCheck = '';
      $enWish  = '';
      if (!$noStock && $this->settings->enableCheckout == 'yes') {
        $enCheck = str_replace(array(
          '{text}',
          '{id}',
          '{theme_folder}'
        ), array(
          $mc_product[3],
          $PR->pid,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-addtobasket-button.htm'));
      }
      // Show add to cart button if checkout enabled..
      if (!$noStock && $this->settings->en_wish == 'yes' && !defined('MC_TRADE_DISCOUNT') && !defined('MC_PUBLICWISH')) {
        $enWish = str_replace(array(
          '{text}',
          '{id}',
          '{theme_folder}'
        ), array(
          $mc_product[4],
          $PR->pid,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-addtowishlist-button.htm'));
      }
      $jsbox = '';
      // Show wishlist button?
      $enCheck = '';
      $enWish  = '';
      if (!$noStock && $this->settings->enableCheckout == 'yes') {
        $wlUrl = '';
        if (isset($config['accid']) && $screen == 'wishlist') {
          $wlUrl = $this->rwr->url(array(
            $this->rwr->config['slugs']['ldw'] . '/' . $PR->pid . '_' . $config['accid'],
            'loadw=' . $PR->pid . '_' . $config['accid']
          ));
        }
        $enCheck = str_replace(array(
          '{url}',
          '{text}',
          '{id}',
          '{theme_folder}'
        ), array(
          $wlUrl,
          ($screen == 'wishlist' ? $mc_product[17] : $mc_product[3]),
          $PR->pid,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/' . ($screen == 'wishlist' ? 'wish-list/' : '') . 'category-addtobasket-button.htm'));
      }
      // Show add to cart button if checkout enabled..
      if (!$noStock && $screen != 'wishlist' && $this->settings->en_wish == 'yes' && !defined('MC_TRADE_DISCOUNT') && !defined('MC_PUBLICWISH')) {
        $enWish = str_replace(array(
          '{text}',
          '{id}',
          '{theme_folder}'
        ), array(
          $mc_product[4],
          $PR->pid,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-addtowishlist-button.htm'));
      }
      // Show sale button..
      if ($PR->pOffer > 0 && !defined('MC_TRADE_DISCOUNT')) {
        if (!$noStock) {
          $pUrl = $this->rwr->url(array(
            $this->rwr->config['slugs']['prd'] . '/' . $PR->pid . '/' . ($PR->rwslug ? $PR->rwslug : $this->rwr->title($PR->pName)),
            'pd=' . $PR->pid
          ));
          $link = str_replace(array(
            '{product_url}',
            '{product_title}',
            '{a}',
            '{/a}',
            '{theme_folder}'
          ), array(
            $pUrl,
            $public_category18,
            '<a href="' . $pUrl . '">',
            '</a>',
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-sale-link.htm'));
        }
        $sGif = str_replace(array('{theme_folder}','{text}'), array(THEME_FOLDER,$public_category28), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-sale-gif.htm'));
      }
      $rwurl = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $PR->pid . '/' . ($PR->rwslug ? $PR->rwslug : $this->rwr->title($PR->pName)),
        'pd=' . $PR->pid
      ));
      $find    = array(
        '{image}',
        '{product_title}',
        '{img_title}',
        '{img_alt}',
        '{more_info}',
        '{product_url}',
        '{date_added}',
        '{add_to_cart}',
        '{saleItem}',
        '{views}',
        '{view_product}',
        '{description}',
        '{price}',
        '{enlarge}',
        '{desc}',
        '{big_image_url}',
        '{desc_url}',
        '{id}',
        '{product_link}',
        '{a}',
        '{/a}',
        '{product_view_link}',
        '{out_of_stock}',
        '{sale}',
        '{colorbox}',
        '{offer_expiry}',
        '{product_expiry}',
        '{pcode}',
        '{theme_folder}',
        '{multi_buy}',
        '{zoom_class}',
        '{stock_level}',
        '{other_images}',
        '{en_basket}',
        '{en_wishlist}',
        '{mp3_preview}',
        '{hurry_limited_stock_text}'
      );
      $replace = array(
        $images[0],
        mc_safeHTML($PR->pName),
        mc_safeHTML(($images[4] ? $images[4] : $PR->pName)),
        mc_safeHTML(($images[5] ? $images[5] : $PR->pName)),
        $public_category15,
        $rwurl,
        str_replace('{date}', $PR->a_date, $public_category11),
        $atc,
        ($PR->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? ' class="saleItem"' : ''),
        str_replace('{views}', $PR->pVisits, $public_category12),
        mc_filterJS($public_category13),
        ($PR->pShortDescription ? $this->predesctag . mcProducts::mc_descriptionMoreLink(mcProducts::mc_descriptionParser($PR->pShortDescription), $PR->pid) : ''),
        ($PR->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? '<del>' . mcProducts::formatSystemCurrency(mc_formatPrice($PR->pPrice)) . '</del> ' . mcProducts::formatSystemCurrency(mc_formatPrice($PR->pOffer)) : mcProducts::formatSystemCurrency(mc_formatPrice($PR->pPrice), false, true)),
        $public_category9,
        $public_category10,
        $images[1],
        $this->rwr->url(array($this->rwr->config['slugs']['vpd'] . '/' . $PR->pid,'dsc=' . $PR->pid)),
        $PR->pid,
        $link,
        (!$noStock ? '<a href="' . $rwurl . '">' : ''),
        (!$noStock ? '</a>' : ''),
        (!$noStock ? $plink : ''),
        ($noStock ? $public_product42 : ''),
        $sGif,
        $jsbox,
        mcProducts::productExpiryDate($PR->pOfferExpiry),
        mcProducts::productExpiryDate($PR->expiry, 'product'),
        $PR->pCode,
        THEME_FOLDER,
        mcProducts::productMultiBuy($PR->pOffer, $PR->pMultiBuy),
        (basename($images[0]) == 'default_img.png' ? 'zoom_hide' : 'zoom'),
        $PR->pStock,
        $oimages,
        $enCheck,
        $enWish,
        mcProducts::productMP3Link($PR->pid),
        mcProducts::hurrylimited($PR)
      );
      $products .= str_replace($find, $replace, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/' . $loadTmp));
    }
    return ($products ? $products : mc_nothingToShow($public_category20));
  }

  public function hurrylimited($pr, $tmp = 'categories/category-limited-stock.htm', $slot = 0) {
    global $msg_hurry_limited_stock_text;
    $hurry = '';
    if ($pr->pDownload == 'no') {
      if ($this->settings->hurrystock > 0 && $pr->pStock <= $this->settings->hurrystock) {
        $count = ($this->settings->hurrystock >= $pr->pStock ? $this->settings->hurrystock : $pr->pStock);
        $hurry = str_replace(array('{theme_folder}','{text}'),array(THEME_FOLDER,str_replace('{stock}',$count,$msg_hurry_limited_stock_text[$slot])),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/' . $tmp));
      }
    }
    return $hurry;
  }

  public function details($arr) {
    $html = array();
    if (!empty($arr)) {
      $wrap = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-flags-wrapper.htm');
      $detl = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-flags-detail.htm');
      foreach ($arr AS $dtl) {
        $html[] = str_replace(array('{icon}','{flag}'),array($dtl['icon'],$dtl['text']),$detl);
      }
    }
    return (!empty($html) ? str_replace('{flags}',implode(mc_defineNewline(), $html),$wrap) : '');
  }

  public function videos($product) {
    $html = '';
    if ($product->pVideo) {
      $iframe = str_replace('{CODE}', $product->pVideo, YOU_TUBE_EMBED_CODE);
      $html  .= str_replace('{iframe}', $iframe, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/audio-video/youtube.htm'));
    }
    if ($product->pVideo2) {
      $iframe = str_replace('{CODE}', $product->pVideo2, VIMEO_EMBED_CODE);
      $html  .= str_replace('{iframe}', $iframe, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/audio-video/vimeo.htm'));
    }
    if ($product->pVideo3) {
      $iframe = str_replace('{CODE}', $product->pVideo3, DAILY_MOTION_EMBED_CODE);
      $html  .= str_replace('{iframe}', $iframe, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/audio-video/daily-motion.htm'));
    }
    return $html;
  }

  public function restrictedCountryList($countries) {
    $html = array();
    $arr  = (!empty($countries) ? unserialize($countries) : array());
    $res  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-country-restriction.htm');
    if ($countries && is_array($arr)  && !empty($arr)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `cName` FROM `" . DB_PREFIX . "countries`
           WHERE `id` IN(" . mc_safeSQL(implode(',', $arr)) . ")
           AND `enCountry` = 'yes'
           GROUP BY `cName`
           ORDER BY `cName`") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($C = mysqli_fetch_object($q)) {
        $html[] = str_replace('{country}', mc_safeHTML($C->cName), $res);
      }
    }
    return (!empty($html) ? implode('<br>', $html) : '');
  }

  public function determineDownloadPath($path) {
    if (substr(strtolower($path), 0, 6) == 'ftp://' || substr(strtolower($path), 0, 7) == 'sftp://') {
      return $path;
    } else {
      return $this->settings->globalDownloadPath . '/' . $this->settings->downloadFolder . '/' . $path;
    }
  }

  public function stockOnSelectedItems($id, $stock, $download) {
    global $public_product9, $public_product34, $public_product35, $public_product7;
    if ($download == 'yes' && $stock > 0) {
      return $public_product7;
    }
    $items = array();
    $none  = array();
    if ($stock > 0) {
      $items[] = $stock;
    } else {
      $none[] = $stock;
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
             WHERE `productID` = '{$id}'
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($ATT = mysqli_fetch_object($query)) {
      if ($ATT->attrStock > 0) {
        $items[] = $ATT->attrStock;
      } else {
        $none[] = $ATT->attrStock;
      }
    }
    return (count($none) > 0 ? $public_product34 : (empty($items) ? $public_product9 : $public_product35));
  }

  public function determinePriceFromText($id, $main) {
    $prices = array();
    if ($main > 0) {
      $prices[] = $main * 100;
    }
    return $prices;
  }

  public function loadMP3() {
    $mp3 = '';
    $wrp = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-mp3-wrapper.htm');
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "mp3`
             WHERE `product_id` = '{$_GET['pMP3']}'
             GROUP BY CONCAT(`fileFolder`,`filePath`)
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($MP3 = mysqli_fetch_object($query)) {
      $mp3 .= str_replace(array(
        '{track}',
        '{mp3}',
        '{id}',
        '{theme_folder}'
      ),
      // Escape mp3 apostrophes if present in file name..
        array(
        mc_cleanData($MP3->fileName),
        $MP3->fileFolder . '/' . mc_filterJS($MP3->filePath),
        $MP3->id,
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-mp3-track.htm'));
    }
    return ($mp3 ? str_replace('{tracks}', $mp3, $wrp) : '');
  }

  public function productExpiryDate($expiry, $type='offer') {
    global $public_category31, $mc_category;
    if ($expiry == '0000-00-00' || defined('MC_TRADE_DISCOUNT')) {
      return '';
    }
    return str_replace(array(
      '{expiry_date}',
      '{theme_folder}'
    ), array(
      str_replace('{date}', date($this->settings->systemDateFormat, strtotime($expiry)), ($type == 'offer' ? $public_category31 : $mc_category[0])),
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-offer-expiry.htm'));
  }

  public function productMultiBuy($offer, $multi) {
    global $public_category33;
    if ($offer > 0 && $multi > 0 && !defined('MC_TRADE_DISCOUNT')) {
      return str_replace(array(
        '{text}',
        '{theme_folder}'
      ), array(
        str_replace('{items}', $multi, $public_category33),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-multi-buy.htm'));
    }
  }

  public function productMP3Link($id) {
    global $public_product23;
    $link = '';
    $mp3s = mc_rowCount('mp3 WHERE `product_id` = \'' . $id . '\'');
    if ($mp3s > 0) {
      return str_replace(array(
        '{text}',
        '{theme_folder}',
        '{id}'
      ), array(
        $public_product23,
        THEME_FOLDER,
        $id
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-mp3' . (defined('MC_CATVIEW') && MC_CATVIEW == 'grid' ? '-gridview' : '') . '.htm'));
    }
    return $link;
  }

  public function loadDisplayImage($id, $thumb = false, $url = false, $type = 'product') {
    switch($type) {
      //GIFT CERTIFICATE..
      case 'gift':
        $def = PRODUCTS_FOLDER . '/default_img.png';
        $IMG = mc_getTableData('giftcerts', 'id', $id);
        if ($IMG->image) {
          return PRODUCTS_FOLDER . '/' . $IMG->image;
        }
        break;
      // PRODUCT..
      default:
        $def = PRODUCTS_FOLDER . '/default_img.png';
        $IMG = mc_getTableData('pictures', 'product_id', $id, 'ORDER BY `displayImg`,`id`');
        if (isset($IMG->picture_path)) {
          switch($IMG->remoteServer) {
            case 'yes':
              return ($IMG->remoteThumb ? $IMG->remoteThumb : $IMG->remoteImg);
              break;
            case 'no':
              return ($url ? $IMG->id : PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . ($thumb ? $IMG->thumb_path : $IMG->picture_path));
              break;
          }
        }
        break;
    }
    return $def;
  }

  public function buildProductSaleDownloads($order, $zip = '') {
    global $msg_public_view7, $msg_public_view8, $msg_public_view11, $msg_public_view12, $msg_public_view13, $msg_public_view27;
    $string = '';
    $total  = 0;
    $runner = 0;
    $remote = 0;
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purchases`
             LEFT JOIN `" . DB_PREFIX . "products`
              ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
             WHERE `saleID`          = '{$order->id}'
             AND `liveDownload`      = 'yes'
             AND `saleConfirmation`  = 'yes'
             AND `productType`       = 'download'
             ORDER BY `" . DOWNLOADS_ORDER_BY . "`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($DL = mysqli_fetch_object($query)) {
      $isAvailable = false;
      if (substr(strtolower($DL->pDownloadPath), 0, 7) == 'http://' || substr(strtolower($DL->pDownloadPath), 0, 8) == 'https://') {
        $bts = mcProducts::mc_getRemoteFileSize($DL->pDownloadPath);
      } else {
        $bts = (file_exists(mcProducts::determineDownloadPath($DL->pDownloadPath)) ? filesize(mcProducts::determineDownloadPath($DL->pDownloadPath)) : '0');
      }
      $fs = mc_fileSizeConversion($bts);
      $pD = mc_safeHTML($DL->pName);
      $ft = substr(strrchr($DL->pDownloadPath, '.'), 1);
      $AM = $DL->pDownloadLimit;
      if (substr(strtolower($DL->pDownloadPath), 0, 7) == 'http://' || substr(strtolower($DL->pDownloadPath), 0, 8) == 'https://') {
        ++$remote;
      }
      if (substr(strtolower($DL->pDownloadPath), 0, 6) == 'ftp://' || substr(strtolower($DL->pDownloadPath), 0, 7) == 'sftp://') {
        ++$remote;
      }
      // Is this download available..
      if ($AM == 0) {
        $isAvailable = true;
        $total       = ($total + $bts);
        ++$runner;
      } else {
        if ($AM > $DL->downloadAmount) {
          $isAvailable = true;
          $total       = ($total + $bts);
          ++$runner;
        }
      }
      // Only build zip string if showing product downloads..
      if ($zip == '' && $isAvailable) {
        $dUrl = $this->rwr->url(array($this->rwr->config['slugs']['pdl'] . '/' . $DL->downloadCode,'pdl=' . $DL->downloadCode));
        $string .= str_replace(array(
          '{url}',
          '{text}',
          '{product}',
          '{estimated_file_size}',
          '{filesize}',
          '{filetype}',
          '{click_to_download}',
          '{theme_folder}',
          '{base}'
        ), array(
          $dUrl,
          $msg_public_view8,
          $pD,
          $msg_public_view11,
          $fs,
          $ft,
          $msg_public_view8,
          THEME_FOLDER,
          $this->settings->ifolder
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-downloads.htm'));
      }
    }
    // Check size of zip..
    if ($this->settings->zipCreationLimit > 0) {
      if ($total > $this->settings->zipCreationLimit) {
        $nozip = true;
      }
    }
    // If zip version, return zip link..
    if ($zip == 'zip' && $this->settings->enableZip == 'yes') {
      if (isset($nozip) || $runner == 0 || $remote > 0) {
        return '';
      }
      $chop = explode('-', $_GET['vOrder']);
      $zUrl = $this->rwr->url(array($this->rwr->config['slugs']['zip'] . '/' . $_GET['vOrder'],'zdl=' . $_GET['vOrder']));
      return str_replace(array(
        '{url}',
        '{text}',
        '{title}',
        '{theme_folder}',
        '{additional}'
      ), array(
        $zUrl,
        str_replace('{filesize}', mc_fileSizeConversion($total), $msg_public_view12),
        $msg_public_view13,
        THEME_FOLDER,
        ($this->zipAdditional() > 0 ? '<span class="additional">' . $msg_public_view27 . '</span>' : '')
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-downloads-zip.htm'));
    }
    return ($string ? $string : mc_nothingToShow($msg_public_view7));
  }

  // Not used..
  public function loadLatestProductsIntoArray() {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT GROUP_CONCAT(DISTINCT(`id`) order by `id` desc) AS `list`
             FROM `" . DB_PREFIX . "products`
             WHERE `pEnable` = 'yes'
             ORDER BY `id` DESC
             LIMIT " . $this->settings->latestProdLimit . "
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    $row = mysqli_fetch_object($query);
    return (isset($row->list) ? $row->list : '0');
  }

  public function displayBrandsList($cat, $catname = '', $sub = 'no', $tagtype = 'option') {
    global $page;
    $html = '';
    // For sub cats, just get relevant brands..
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
             WHERE `bCat`  IN('{$cat}','all')
             AND `enBrand`  = 'yes'
             ORDER BY `" . BRANDS_ORDER_BY . "`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($BRAND = mysqli_fetch_object($query)) {
      $html .= str_replace(array(
        '{value}',
        '{selected}',
        '{name}',
        '{theme_folder}'
      ), array(
        $this->rwr->url(array(
          $this->rwr->config['slugs']['brs'] . '/' . $BRAND->id . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $this->rwr->title($BRAND->name)),
          'pbnd=' . $BRAND->id
        )),
        (isset($_GET['brand']) && $_GET['brand'] == $BRAND->id ? ' selected="selected"' : ''),
        mc_safeHTML($BRAND->name),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-' . $tagtype . '-tags.htm'));
    }
    // If category is a parent and the parent has no brands,
    // display (if any) sub category brands on parent page..
    if ($sub == 'no' && $html == '') {
      $kids = array();
      $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                    WHERE `childOf`  = '{$cat}'
                    AND `catLevel`   = '1'
                    AND `enCat`      = 'yes'
                    AND " . MC_CATG_PMS_SQL . "
                    ORDER BY `orderBy`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_children) > 0) {
        while ($CHILDREN = mysqli_fetch_object($q_children)) {
          $kids[$CHILDREN->id] = $CHILDREN->catname;
        }
      }
      // Now display ALL brands for all sub cats.. Appears on parent page only..
      if (!empty($kids)) {
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
                 WHERE `bCat` IN(" . mc_safeSQL(implode(',', array_keys($kids))) . ")
                 AND `enBrand`  = 'yes'
                 ORDER BY `bCat`,`" . BRANDS_ORDER_BY . "`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($BRAND = mysqli_fetch_object($query)) {
          $html .= str_replace(array(
            '{value}',
            '{selected}',
            '{name}',
            '{theme_folder}'
          ), array(
            $this->rwr->url(array(
              $this->rwr->config['slugs']['brs'] . '/' . $BRAND->id . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $this->rwr->title($BRAND->name)),
              'pbnd=' . $BRAND->id
            )),
            (isset($_GET['brand']) && $_GET['brand'] == $BRAND->id ? ' selected="selected"' : ''),
            mc_cleanData($kids[$BRAND->bCat]) . ' - ' . mc_safeHTML($BRAND->name),
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-' . $tagtype . '-tags.htm'));
        }
      }
    }
    return ($html ? $html : '');
  }

  public function getCouponDiscount($code) {
    global $MCCART;
    $cRespCode = array();
    $cartTotal = $MCCART->cartTotal();
    // Check if code is gift certificate..
    $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes`
           WHERE `code`  = '" . mc_safeSQL($code) . "'
           AND `enabled` = 'yes'
				   AND `active`  = 'yes'
           LIMIT 1
           ") or die(mc_MySQLError(__LINE__, __FILE__));
    $GIFT = mysqli_fetch_object($q_c);
    // If global discount is set, discount coupons cannot be used..
    // Doesn`t apply to gift certificates..
    if (!isset($GIFT->id) && $this->settings->globalDiscount > 0) {
      $cRespCode[0] = 'global-discount';
      return $cRespCode;
    }
    // Should never load for trade, but anyway..just in case..
    if (defined('MC_TRADE_DISCOUNT')) {
      $cRespCode[0] = 'trade-discount';
      return $cRespCode;
    }
    // If cart total 0 (ie free items), coupons are pointless..
    if ($cartTotal == 0 && $MCCART->allDownloadItemsInCart() == 0 && $MCCART->cartFreebies() > 0) {
      $cRespCode[0] = 'free-cart';
      return $cRespCode;
    }
    // Was gift certificate found?
    if (isset($GIFT->id)) {
      // How much can be redeemed?
      $redeem = @number_format($GIFT->value - $GIFT->redeemed, 2, '.', '');
      // Is redeemable value..
      // A => full redeemed
      // B => greater than cart amount?
      // C => same as cart amount
      // D => less than cart amount..
      if (in_array($redeem, array(
        '0',
        '0.00'
      ))) {
        $cRespCode[0] = 'redeemed';
      } else {
        if ($redeem > $cartTotal) {
          $reedemValue = @number_format($cartTotal, 2, '.', '');
        } elseif ($redeem == $cartTotal) {
          $reedemValue = @number_format($cartTotal, 2, '.', '');
        } else {
          $reedemValue = @number_format($redeem, 2, '.', '');
        }
        $cRespCode[0] = 'ok';
        $cRespCode[1] = $reedemValue;
        $cRespCode[2] = $GIFT->code;
        $cRespCode[4] = $GIFT->id;
        $cRespCode[5] = 'gift';
      }
    } else {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "campaigns`
               WHERE `cDiscountCode`  = '" . mc_safeSQL($code) . "'
               AND `cLive`            = 'yes'
               LIMIT 1
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      $DCODE = mysqli_fetch_object($query);
      if (isset($DCODE->cDiscountCode)) {
        $cartCatTotal = $MCCART->cartTotalCouponCatRestriction($DCODE->categories);
        // First check usage..
        $usage        = (mc_rowCount('coupons WHERE `cDiscountCode` = \'' . mc_safeSQL($code) . '\'') + 1);
        if ($DCODE->cUsage > 0 && $usage > $DCODE->cUsage) {
          $cRespCode[0] = 'invalid';
          // Next, has this code expired..
        } else if ($DCODE->cExpiry != '0000-00-00' && $DCODE->cExpiry < date("Y-m-d")) {
          $cRespCode[0] = 'invalid';
        } else {
          if ($DCODE->cMin > 0) {
            // For min, check standard min value and also category restriction value..
            if ($cartTotal < mc_formatPrice($DCODE->cMin)) {
              $cRespCode[0] = 'min-amount';
              $cRespCode[1] = mc_formatPrice($DCODE->cMin);
            } else {
              // Check minimum is met for category restriction..
              if ($DCODE->categories) {
                if ($cartCatTotal < mc_formatPrice($DCODE->cMin)) {
                  $cRespCode[0] = 'min-amount-cats';
                  $cRespCode[1] = mc_formatPrice($DCODE->cMin);
                }
              }
            }
          }
          // Is this a free shipping or free tax discount..
          if (in_array($DCODE->cDiscount, array(
            'freeshipping',
            'notax'
          ))) {
            $cRespCode[0] = 'ok';
            $cRespCode[1] = $DCODE->cDiscount;
            $cRespCode[2] = $DCODE->cDiscountCode;
            $cRespCode[4] = $DCODE->id;
            $cRespCode[5] = 'discount';
          } else {
            // Check category restrictions..
            if ($DCODE->categories) {
              // Fixed or percentage discount..
              $DISC = mcProducts::getDiscount($cartCatTotal, $DCODE->cDiscount);
              // Lets make sure that the amount minus the discount isn`t 0..
              if ($cartCatTotal - $DISC <= 0 || $cartCatTotal == 0) {
                $cRespCode[0] = 'low-total-cats';
              }
            } else {
              // Fixed or percentage discount..
              $DISC = mcProducts::getDiscount($cartTotal, $DCODE->cDiscount);
              // Lets make sure that the amount minus the discount isn`t 0..
              if ($cartTotal - $DISC <= 0) {
                $cRespCode[0] = 'low-total';
              }
            }
            // If we are this far and everything is ok, discount coupon is fine..
            if (!in_array('min-amount', $cRespCode) && !in_array('low-total', $cRespCode) && !in_array('low-total-cats', $cRespCode) && !in_array('min-amount-cats', $cRespCode)) {
              $cRespCode[0] = 'ok';
              $cRespCode[1] = $DISC;
              $cRespCode[2] = $DCODE->cDiscountCode;
              $cRespCode[4] = $DCODE->id;
              $cRespCode[5] = 'discount';
            }
          }
        }
      } else {
        $cRespCode[0] = 'invalid';
      }
    }
    return $cRespCode;
  }

  public function buildProductImages($id, $name = '', $arr = 'no', $skip = 0) {
    global $mc_category;
    $pics    = '';
    $images  = array();
    $none    = str_replace('{text}',mc_safeHTML($mc_category[2]),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-pictures-img-none.htm'));
    if ($arr == 'no') {
      $wrapper = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-pictures.htm');
    }
    $query   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "pictures`
               WHERE `product_id` = '{$id}'
               " . ($skip > 0 ? 'AND `id` != \'' . $skip . '\'' : '') . "
               ORDER BY `displayImg`,`id`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($IMG = mysqli_fetch_object($query)) {
      $tmb = '';
      $img = '';
      switch($IMG->remoteServer) {
        case 'yes':
          if ($IMG->remoteImg) {
            $tmb = ($IMG->remoteThumb ? $IMG->remoteThumb : $IMG->remoteImg);
            $img = $IMG->remoteImg;
          }
          break;
        case 'no':
          // Do local images exist?
          if (file_exists(PATH . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->thumb_path) &&
              file_exists(PATH . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->picture_path)) {
            $tmb = $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->thumb_path;
            $img = $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/' . ($IMG->folder ? $IMG->folder . '/' : '') . $IMG->picture_path;
          }
          break;
      }
      if ($tmb && $img) {
        if ($arr == 'yes') {
          $images[] = array(
            'tmb' => $tmb,
            'img' => $img,
            'display' => $IMG->displayImg,
            'theme' => THEME_FOLDER,
            'title' => mc_cleanData($IMG->pictitle),
            'alt' => mc_cleanData($IMG->picalt)
          );
        } else {
          $pics .= str_replace(array(
            '{thumb}',
            '{display_img}',
            '{big_image}',
            '{theme_folder}',
            '{id}',
            '{img_title}',
            '{img_alt}'
          ), array(
            $tmb,
            ($IMG->displayImg == 'yes' ? '' : ''),
            $img,
            THEME_FOLDER,
            $id,
            mc_safeHTML(($IMG->pictitle ? mc_cleanData($IMG->pictitle) : $name)),
            mc_safeHTML(($IMG->picalt ? mc_cleanData($IMG->picalt) : $name))
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-pictures-img.htm'));
        }
      }
    }
    if ($arr == 'yes') {
      return $images;
    }
    // For grid view add camera icon depicting no additional images
    // Keeps layout tidy..
    if ($pics == '' && defined('MC_CATVIEW') && MC_CATVIEW == 'grid') {
      $pics = $none;
    }
    return ($pics ? str_replace(array(
      '{pictures}',
      '{theme_folder}'
    ), array(
      trim($pics),
      THEME_FOLDER
    ), $wrapper) : '');
  }

  public function getPersonalisedVisData($slot) {
    $data = array();
    if (!empty($_SESSION[$slot])) {
      foreach ($_SESSION[$slot] AS $s) {
        $split           = explode('|-<>-|', $s);
        $data[$split[0]] = $split[1];
      }
    }
    return $data;
  }

  public function buildPersonalisationOptions($id, $edit = false) {
    global $public_product24, $public_product30, $public_product29, $public_product32, $public_product43, $public_product44;
    $html     = '';
    $req      = 0;
    $limitIDs = array();
    if ($edit) {
      $split   = explode('-', $_GET['ppCE']);
      $id      = $split[1];
      $visData = mcProducts::getPersonalisedVisData($_GET['ppCE']);
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "personalisation`
             WHERE `productID`  = '{$id}'
             AND `enabled`      = 'yes'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($P = mysqli_fetch_object($query)) {
      $select = '';
      $value  = '';
      // In edit mode, load value if exists..
      if ($edit && isset($_SESSION[$_GET['ppCE']])) {
        $value = (isset($visData[$P->id]) ? $visData[$P->id] : '');
      }
      // Check if at least 1 entry is required..
      if ($P->reqField == 'yes') {
        ++$req;
      }
      if ($P->persOptions) {
        $options = array_map('trim', explode('||', $P->persOptions));
        if (!empty($options)) {
          if (AUTO_SORT_PERSONALISATION_OPTIONS) {
            sort($options);
          }
          foreach ($options AS $op) {
            $select .= str_replace(array(
              '{value}',
              '{selected}',
              '{theme_folder}'
            ), array(
              mc_cleanData($op),
              ($value == $op ? ' selected="selected"' : ''),
              THEME_FOLDER
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-personalisation-select-option.htm'));
          }
          if ($select) {
            $html .= str_replace(array(
              '{text}',
              '{id}',
              '{options}',
              '{extra_cost}',
              '{theme_folder}',
              '{required}'
            ), array(
              mc_persTextDisplay(mc_cleanData($P->persInstructions)) . ($P->reqField == 'yes' ? $public_product43 : ''),
              $P->id,
              trim($select),
              ($P->persAddCost > 0 ? str_replace('{extra_cost}', mcProducts::formatSystemCurrency(mc_formatPrice($P->persAddCost)), $public_product32) : $public_product30),
              THEME_FOLDER,
              ''
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-personalisation-select.htm'));
          }
        }
      } else {
        // Is max characters imposed..
        $script = '';
        if ($P->maxChars > 0) {
          $limitIDs[] = array($P->id, $P->maxChars);
        }
        $html .= str_replace(array(
          '{start}',
          '{text}',
          '{id}',
          '{max}',
          '{maxlength}',
          '{extra_cost}',
          '{value}',
          '{required}',
          '{theme_folder}'
        ), array(
          ($value ? strlen($value) : '0'),
          mc_persTextDisplay(mc_cleanData($P->persInstructions)) . ($P->reqField == 'yes' ? $public_product43 : ''),
          $P->id,
          $P->maxChars,
          ($P->maxChars > 0 ? ' maxlength="' . $P->maxChars . '"' : ''),
          ($P->persAddCost > 0 ? str_replace('{extra_cost}', mcProducts::formatSystemCurrency(mc_formatPrice($P->persAddCost)), $public_product32) : $public_product30),
          mc_safeHTML($value),
          ($P->reqField == 'yes' ? $public_product43 : ''),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-personalisation-' . (in_array($P->boxType, array(
          'input',
          'textarea'
        )) ? $P->boxType : 'input') . ($P->maxChars > 0 ? '-restricted' : '') . '.htm'));
      }
    }
    $data = ($html ? str_replace(array(
      '{text}',
      '{options}',
      '{theme_folder}'
    ), array(
      ($req > 0 ? $public_product44 : $public_product24),
      trim($html),
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-personalisation-wrapper.htm')) : '');
    return array($data, $limitIDs);
  }

  public function buildBuyOptions($P) {
    global $public_product32, $public_product9, $public_product43;
    $html  = '';
    $gWrap = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-attribute-wrapper.htm');
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
             WHERE `productID` = '{$P->pid}'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($AG = mysqli_fetch_object($query)) {
      $attr = '';
      if ($AG->allowMultiple == 'no') {
        $attr .= str_replace(array(
          '{value}',
          '{disabled}',
          '{text}',
          '{cost}',
          '{theme_folder}'
        ), array(
          '0',
          '',
          '- - - - - - -',
          '',
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-attribute.htm'));
      }
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
           WHERE `attrGroup` = '{$AG->id}'
           AND `productID`   = '{$P->pid}'
           ORDER BY `orderBy`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($ATTRIBUTES = mysqli_fetch_object($q)) {
        $attr .= str_replace(array(
          '{value}',
          '{disabled}',
          '{text}',
          '{cost}',
          '{theme_folder}'
        ), array(
          $ATTRIBUTES->id,
          ($ATTRIBUTES->attrStock == 0 ? ' disabled="disabled"' : ''),
          mc_safeHTML($ATTRIBUTES->attrName) . ($ATTRIBUTES->attrStock == 0 ? ' - ' . $public_product9 : ($this->settings->showAttrStockLevel == 'yes' ? mcProducts::attributeStockLevel($ATTRIBUTES->attrStock) : '')),
          ($ATTRIBUTES->attrCost > 0 && $ATTRIBUTES->attrStock > 0 ? str_replace('{extra_cost}', mcProducts::formatSystemCurrency(mc_formatPrice($ATTRIBUTES->attrCost)), ' ' . $public_product32) : ''),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-attribute.htm'));
      }
      if ($attr) {
        $html .= str_replace(array(
          '{attributes}',
          '{name}',
          '{multiple}',
          '{id}',
          '{isRequired}',
          '{theme_folder}'
        ), array(
          $attr,
          mc_safeHTML($AG->groupName) . ($AG->isRequired == 'yes' ? $public_product43 : ''),
          ($AG->allowMultiple == 'yes' ? 'multiple="multiple"' : ''),
          $AG->id,
          $AG->isRequired,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-attributes.htm'));
      }
    }
    return ($html ? str_replace(array(
      '{attr_groups}',
      '{theme_folder}'
    ), array(
      trim($html),
      THEME_FOLDER
    ), $gWrap) : '');
  }

  public function updateProductCount($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
    `pVisits`   = (`pVisits`+1)
    WHERE `id`  = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function buildProductTags($prod, $lang) {
    $tagData = '';
    $tags    = '';
    if ($prod->pTags) {
      $tagData = '';
      $tCount  = 0;
      if (strpos($prod->pTags, ',') !== FALSE) {
        $split = array_map('trim', explode(',', mc_cleanData($prod->pTags)));
        foreach ($split AS $pTag) {
          $tagData .= '<a href="' . $this->rwr->url(array($this->rwr->config['slugs']['tag'] . '/' . urlencode($pTag),'q=' . urlencode($pTag))) . '">' . $pTag . '</a>';
          if (++$tCount != count($split)) {
            $tagData .= TAG_SEPARATOR;
          }
        }
      } else {
        $tagData = '<a href="' . $this->rwr->url(array($this->rwr->config['slugs']['tag'] . '/' . urlencode(mc_cleanData($prod->pTags)),'q=' . urlencode(mc_cleanData($prod->pTags)))) . '">' . mc_cleanData($prod->pTags) . '</a>';
      }
      $find    = array(
        '{tags_text}',
        '{tags}',
        '{theme_folder}'
      );
      $replace = array(
        str_replace('{count}', (isset($split) ? count($split) : 1), $lang),
        $tagData,
        THEME_FOLDER
      );
      $tags    = str_replace($find, $replace, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/products/product-tags.htm'));
    }
    return $tags;
  }

  public function getDiscount($price, $rate) {
    if (strpos($rate, '%') !== FALSE) {
      $decimal  = substr($rate, 0, -1) / 100 * $price;
      $discount = number_format($decimal, 2, '.', '');
      return mc_formatPrice($discount);
    } else {
      return mc_formatPrice($rate);
    }
  }

  public function attributeStockLevel($stock) {
    global $public_product51;
    if ($this->settings->productStockThreshold > 0) {
      if ($stock > $this->settings->productStockThreshold) {
        return '';
      }
      return str_replace('{attr_stock}', ($stock < $this->settings->productStockThreshold ? $stock : $this->settings->productStockThreshold), $public_product51);
    } else {
      return str_replace('{attr_stock}', $stock, $public_product51);
    }
  }

  public function displayInStockThreshold($stock, $download = 'no') {
    global $public_product8, $public_product33, $public_product7, $public_product9, $mc_product;
    if ($download == 'yes' && $stock > 0) {
      return $public_product7;
    }
    if ($stock == 0) {
      return $public_product9;
    }
    if ($this->settings->productStockThreshold > 0) {
      return ($stock > $this->settings->productStockThreshold ? $public_product8 : str_replace('{count}', $stock, $public_product33));
    } else {
      return str_replace('{count}', $stock, $public_product33);
    }
  }

  public function buildSearchProducts($search, $key, $filters = array()) {
    global $limit, $public_category11, $public_category10, $public_category12, $public_search3, $public_category9,
    $page, $public_category13, $public_category14, $public_category17, $public_category18, $public_category15,
    $public_category18, $public_product9, $public_category28, $public_product42;
    if ($search == '') {
      header("Location: " . $this->rwr->url(array('no-search')));
      exit;
    }
    $products = array();
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
             DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
             `" . DB_PREFIX . "products`.`id` AS `pid`
             FROM `" . DB_PREFIX . "products`
             LEFT JOIN `" . DB_PREFIX . "prod_category`
              ON `" . DB_PREFIX . "prod_category`.`product` = `" . DB_PREFIX . "products`.`id`
             LEFT JOIN `" . DB_PREFIX . "categories`
              ON `" . DB_PREFIX . "prod_category`.`category` = `" . DB_PREFIX . "categories`.`id`
             LEFT JOIN `" . DB_PREFIX . "prod_brand`
              ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_brand`.`product`
             WHERE `pEnable` = 'yes'
             " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
             $search
             AND " . MC_CATG_PMS_SQL . "
             GROUP BY `" . DB_PREFIX . "products`.`id`
             ORDER BY `" . DB_PREFIX . "products`.`id`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    // Log search..only log on page 1 of search page.
    if ($search && $this->settings->enSearchLog == 'yes' && trim($_GET['q']) != '' && $page == 1) {
      $c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
    }
    while ($PR = mysqli_fetch_object($query)) {
      $products[] = $PR->pid;
    }
    mc_logSearchResults($_GET['q'], (isset($c->rows) ? $c->rows : '0'), $this->settings);
    if (!empty($products)) {
      $ids       = serialize(implode(',', array_unique($products)));
      // Check to see if search already exists..
      // If it does, just return key..
      $oldSearch = mc_getTableData('search_index', '`results`', $ids);
      if (isset($oldSearch->id)) {
        $key = $oldSearch->searchCode;
      } else {
        // Log key info..
        mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "search_index` (
        `searchCode`,`results`,`searchDate`,`filters`
        ) VALUES (
        '{$key}','{$ids}','" . date("Y-m-d") . "','" . (!empty($filters) ? mc_safeSQL(serialize($filters)) : '') . "'
        )") or die(mc_MySQLError(__LINE__, __FILE__));
      }
    }
    $url = $this->rwr->url(array($this->rwr->config['slugs']['sch'] . '/' . $key . '/1','sk=' . $key));
    header("Location: " . $url);
    exit;
  }

  public function mc_descriptionParser($desc) {
    if ($this->settings->maxProductChars > 0) {
      return mc_NL2BR(mc_safeHTML(trim(substr($desc, 0, $this->settings->maxProductChars))));
    }
    return mc_NL2BR(mc_safeHTML($desc));
  }

  public function mc_descriptionMoreLink($text, $id) {
    global $public_category10;
    $html = str_replace(
      array('{text}', '{id}'),
      array($public_category10, $id),
      mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/category-full-desc-link.htm')
    );
    if ($text) {
      return $text . $html;
    }
    return '';
  }

  public function checkProductStock($id, $update = false) {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
             WHERE `attrStock`  > 0
             AND `productID`    = '{$id}'
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($query) == 0 && $update) {
      mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "products` SET
      `pStock`    = 'no'
      WHERE `id`  = '{$id}'
      ") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      if (!$update) {
        return (mysqli_num_rows($query) > 0 ? true : false);
      }
    }
  }

  public function formatSystemCurrency($price, $symbol = false, $alttext = false) {
    global $MCCRV, $public_category16, $cur_sym_display;
    $conversion = '';
    if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'])) {
      $altCurrency = substr($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'], 0, 3);
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "currencies`
               WHERE `currency`  = '{$altCurrency}'
               AND `enableCur`   = 'yes'
               LIMIT 1
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      $CUR = mysqli_fetch_object($query);
      if (isset($CUR->currency)) {
        $conversion = $MCCRV->convert($price, $altCurrency, $CUR->rate);
      }
    }
    // Alternative text..
    if ($alttext && $this->settings->freeTextDisplay) {
      if (in_array($price, array(
        '0.00',
        '0',
        ''
      ))) {
        return mc_safeHTML($this->settings->freeTextDisplay);
      }
    }
    // If conversion is blank, no currency is set..
    if ($conversion == '') {
      // Check if cookie existed previously to prevent display error..
      // Legacy only..
      if (isset($_COOKIE[mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency'])) {
        setcookie(mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency', '');
        unset($_COOKIE[mc_encrypt(SECRET_KEY . $_SERVER['HTTP_HOST']) . '_mc_currency']);
      }
      return mc_currencyFormat(mc_formatPrice($price, true));
    }
    // Check display preference isn`t blank..
    // If its blank, nothing will show, so default to basic display using currency code..
    if ($CUR->currencyDisplayPref == '') {
      $CUR->currencyDisplayPref = $altCurrency . '{PRICE}';
    }
    return str_replace('{PRICE}', mc_formatPrice($conversion), $CUR->currencyDisplayPref);
  }

  public function loadProductImage($id) {
    $image = 'default_img.png';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "pictures`
             WHERE `product_id` = '{$id}'
             ORDER BY `displayImg`,`id`
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    $IMG = mysqli_fetch_object($query);
    if (isset($IMG->thumb_path)) {
      switch($IMG->remoteServer) {
        case 'yes':
          $tmb = ($IMG->remoteThumb ? $IMG->remoteThumb : $IMG->remoteImg);
          $img = $IMG->remoteImg;
          break;
        case 'no':
          if (file_exists(PATH . PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->thumb_path)) {
            $tmb = PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->thumb_path;
          } else {
            $tmb = PRODUCTS_FOLDER . '/' . $image;
          }
          $img = PRODUCTS_FOLDER . '/' . ($IMG->folder ? mc_imageDisplayPath($IMG->folder) . '/' : '') . $IMG->picture_path;
          break;
      }
      return array(
        $tmb,
        $img,
        $IMG->id,
        $IMG->remoteServer,
        mc_cleanData($IMG->pictitle),
        mc_cleanData($IMG->picalt)
      );
    } else {
      return array(
        PRODUCTS_FOLDER . '/' . $image,
        PRODUCTS_FOLDER . '/' . $image,
        0,
        'no',
        '',
        ''
      );
    }
  }

}

?>