<?php

class mcLeftMenu {

  public $system;
  public $products;
  public $settings;
  public $rwr;
  public $cache;
  public $account;

  public function leftMenuWrapper($menu) {
    return str_replace('{boxes}', $menu, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/menu-wrapper.htm'));
  }

  public function related() {
    global $CAT, $public_category3, $public_category4, $thisParent, $thisChild;
    $children = '';
    $relatedC = array();
    $links    = '';
    // Get all children ids..
    switch($CAT->catLevel) {
      case '1':
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
             WHERE `catLevel`  = '2'
             AND `childOf`     = '" . (int) $_GET['c'] . "'
             AND `enCat`       = 'yes'
             AND " . MC_CATG_PMS_SQL
             ) or die(mc_MySQLError(__LINE__, __FILE__));
        while ($C = mysqli_fetch_object($q)) {
          $relatedC[] = $C->id;
        }
        break;
      case '2':
        $relatedC[] = $thisParent->id;
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
             WHERE `catLevel`  = '3'
             AND `childOf`     = '" . (int) $_GET['c'] . "'
             AND `enCat`       = 'yes'
             AND " . MC_CATG_PMS_SQL
             ) or die(mc_MySQLError(__LINE__, __FILE__));
        while ($C = mysqli_fetch_object($q)) {
          $relatedC[] = $C->id;
        }
        break;
      case '3':
        $relatedC[] = $thisParent->id;
        $relatedC[] = $thisChild->id;
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
             WHERE `catLevel`  = '3'
             AND `childOf`     = '{$thisChild->id}'
             AND `id`         != '" . (int) $_GET['c'] . "'
             AND `enCat`       = 'yes'
             AND " . MC_CATG_PMS_SQL
             ) or die(mc_MySQLError(__LINE__, __FILE__));
        while ($C = mysqli_fetch_object($q)) {
          $relatedC[] = $C->id;
        }
        break;
    }
    if (!empty($relatedC)) {
      $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                    WHERE `id`    IN(" . mc_safeSQL(implode(',', $relatedC)) . ")
                    AND `enCat`    = 'yes'
                    AND " . MC_CATG_PMS_SQL . "
                    ORDER BY `catLevel`,`orderBy`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_children) > 0) {
        while ($CHILDREN = mysqli_fetch_object($q_children)) {
          $image = 'default_img.png';
          if ($CHILDREN->imgIcon && file_exists(PATH . PRODUCTS_FOLDER . '/' . $CHILDREN->imgIcon)) {
            $image = $CHILDREN->imgIcon;
          }
          $url = $this->rwr->url(array(
            $this->rwr->config['slugs']['cat'] . '/' . $CHILDREN->id . '/1/' . ($CHILDREN->rwslug ? $CHILDREN->rwslug : $this->rwr->title($CHILDREN->catname)),
            'c=' . $CHILDREN->id
          ));
          $links .= str_replace(array(
            '{text}',
            '{url}',
            '{image}',
            '{theme_folder}'
          ), array(
            mc_safeHTML($CHILDREN->catname),
            $url,
            $image,
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/related-categories-link.htm'));
        }
        $thisCount = mysqli_num_rows($q_children);
        $find      = array(
          '{text}',
          '{categories}',
          '{theme_folder}'
        );
        $replace   = array(
          str_replace('{count}', $thisCount, ($thisCount == 1 ? $public_category3 : $public_category4)),
          $links,
          THEME_FOLDER
        );
        $children  = str_replace($find, $replace, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/related-categories.htm'));
      }
    }
    return $children;
  }

  public function filter_cats($l, $tags = '') {
    switch($tags) {
      case 'li':
        $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-option-tags.htm');
        break;
      default:
        $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/cat-filter-link.htm');
        break;
    }
    $string = '';
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              AND " . MC_CATG_PMS_SQL . "
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
      $selected = '';
      $icon     = 'caret-right';
      if (isset($_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)]) && $_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)] == $CATS->id) {
        $selected = ($tags == 'li' ? ' selected="selected"' : ' class="point_selector"');
        $icon     = 'check';
      }
      $string .= str_replace(array(
        '{value}',
        ($tags == 'li' ? '{name}' : '{category}'),
        '{selected}',
        '{theme_folder}',
        '{spaces}',
        '{icon}'
      ), array(
        $CATS->id,
        mc_safeHTML($CATS->catname),
        $selected,
        THEME_FOLDER,
        '',
        $icon
      ), $link);
      $qC = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '2'
            AND `enCat`      = 'yes'
            AND `childOf`    = '{$CATS->id}'
            AND " . MC_CATG_PMS_SQL . "
            ORDER BY `orderBy`
            ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($CHILDREN = mysqli_fetch_object($qC)) {
        $selected = '';
        $icon     = 'caret-right';
        if (isset($_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)]) && $_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)] == $CHILDREN->id) {
          $selected = ($tags == 'li' ? ' selected="selected"' : ' class="point_selector"');
          $icon     = 'check';
        }
        $string .= str_replace(array(
          '{value}',
          '{selected}',
          ($tags == 'li' ? '{name}' : '{category}'),
          '{theme_folder}',
          '{spaces}',
          '{icon}'
        ), array(
          $CHILDREN->id,
          $selected,
          ($tags == 'li'? '&nbsp;&nbsp;' : '') . mc_safeHTML($CHILDREN->catname),
          THEME_FOLDER,
          '&nbsp;&nbsp;',
          $icon
        ), $link);
        // Infants..
        $qI = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '3'
              AND `enCat`      = 'yes'
              AND `childOf`    = '{$CHILDREN->id}'
              AND " . MC_CATG_PMS_SQL . "
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($INFANTS = mysqli_fetch_object($qI)) {
          $selected = '';
          $icon     = 'caret-right';
          if (isset($_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)]) && $_SESSION['mc_cat_filters_' . mc_encrypt(SECRET_KEY)] == $INFANTS->id) {
            $selected = ($tags == 'li' ? ' selected="selected"' : ' class="point_selector"');
            $icon     = 'check';
          }
          $string .= str_replace(array(
            '{value}',
            '{selected}',
            ($tags == 'li' ? '{name}' : '{category}'),
            '{theme_folder}',
            '{spaces}',
            '{icon}'
          ), array(
            $INFANTS->id,
            $selected,
            ($tags == 'li'? '&nbsp;&nbsp;&nbsp;&nbsp;' : '') . mc_safeHTML($INFANTS->catname),
            THEME_FOLDER,
            '&nbsp;&nbsp;&nbsp;&nbsp;',
            $icon
          ), $link);
        }
      }
    }
    if ($tags == 'li') {
      return trim($string);
    }
    return ($string ? str_replace(array(
      '{text}',
      '{all_text}',
      '{categories}',
      '{theme_folder}'
    ), array(
      $l[5],
      $l[1],
      $string,
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/cat-filter-wrapper.htm')) : '');
  }

  public function rss($l) {
    if ($this->settings->rssScroller == 'no' || $this->settings->rssScrollerLimit <= 0) {
      return '';
    }
    $html = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/rss-scroller.htm');
    return str_replace(array(
      '{text}',
      '{url}',
      '{limit}'
    ), array(
      $l,
      $this->settings->rssScrollerUrl,
      $this->settings->rssScrollerLimit
    ), $html);
  }

  public function tweets($param = array(), $l) {
    if ($this->settings->twitterLatest == 'no' || empty($param)) {
      return '';
    }
    $html = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/latest-tweets.htm');
    return str_replace(array(
      '{text}',
      '{username}'
    ), array(
      $l,
      $param['twitter']['username']
    ), $html);
  }

  public function all_brands($l) {
    $html = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,GROUP_CONCAT(`id`) AS `ids` FROM `" . DB_PREFIX . "brands`
             WHERE `enBrand` = 'yes'
			       GROUP BY `name`
             ORDER BY `" . BRANDS_ORDER_BY . "`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($BRAND = mysqli_fetch_object($query)) {
      if (defined('MC_ORDER_AJAX')) {
        $url = $BRAND->ids;
      } else {
        $url = $this->rwr->url(array(
          $this->rwr->config['slugs']['brs'] . '/' . str_replace(',','_',$BRAND->ids) . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $this->rwr->title($BRAND->name)),
          'pbnd=' . str_replace(',','_',$BRAND->ids)
        ));
      }
      $selected = '';
      $icon     = 'caret-right';
      if (isset($_GET['pbnd'])) {
        if (strpos($_GET['pbnd'], '_') !== false) {
          $chop = explode('_', $_GET['pbnd']);
          if ($chop[0] == $BRAND->id) {
            $selected = ' class="brand_selector"';
            $icon     = 'check';
          }
        } else {
          if ($_GET['pbnd'] == $BRAND->id) {
            $selected = ' class="brand_selector"';
            $icon     = 'check';
          }
        }
      }
      if (isset($_GET['brand']) && (int) $_GET['brand'] == $BRAND->id) {
        $selected = ' class="brand_selector"';
        $icon     = 'check';
      }
      $html .= str_replace(array(
        '{id}',
        '{brand}',
        '{theme_folder}',
        '{count}',
        '{selected}',
        '{icon}'
      ), array(
        $url,
        mc_safeHTML($BRAND->name),
        THEME_FOLDER,
        brandCountDisplay($BRAND->ids, $this->settings),
        $selected,
        $icon
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/brands-link.htm'));
    }
    return ($html ? str_replace(array(
      '{text}',
      '{all_text}',
      '{brands}',
      '{theme_folder}',
      '{class}'
    ), array(
      (defined('MC_ORDER_AJAX') ? $l[1][3] : $l[0]),
      $l[1][0],
      $html,
      THEME_FOLDER,
      'allbrands'
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/brands-wrapper.htm')) : '');
  }

  public function new_pages($l) {
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/left-menu-links' . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/new-page-links.htm');
    $string = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newpages`
             WHERE `enabled`              = 'yes'
             AND FIND_IN_SET('1',`linkPos`) > 0
             AND `landingPage`            = 'no'
             " . (defined('MC_TRADE_DISCOUNT') ? 'AND `trade` IN(\'yes\',\'no\')' : 'AND `trade` IN(\'no\')') . "
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($LINKS = mysqli_fetch_object($query)) {
      if ($LINKS->linkExternal == 'yes' && (substr(strtolower($LINKS->pageText), 0, 7) == 'http://' || substr(strtolower($LINKS->pageText), 0, 8) == 'https://')) {
        $url    = trim($LINKS->pageText);
        $target = ($LINKS->linkTarget == 'new' ? ' onclick="window.open(this);return false"' : '');
      } else {
        $url = $this->rwr->url(array(
          $this->rwr->config['slugs']['npg'] . '/' . $LINKS->id . '/' . ($LINKS->rwslug ? $LINKS->rwslug : $this->rwr->title($LINKS->pageName)),
          'np=' . $LINKS->id
        ));
        $target = '';
      }
      $string .= str_replace(array(
        '{url}',
        '{text}',
        '{target}',
        '{theme_folder}'
      ), array(
        $url,
        mc_safeHTML($LINKS->pageName),
        $target,
        THEME_FOLDER
        ), $link);
    }
    $lnk = ($string ? str_replace(array(
      '{text}',
      '{pages}',
      '{theme_folder}'
    ), array(
      $l,
      trim($string),
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/new-pages-wrapper.htm')) : '');
    // Update cache if enabled..
    $this->cache->cache_file($mCache, $lnk);
    return $lnk;
  }

  public function recently_viewed($l) {
    if ($this->settings->enableRecentView == 'no' || empty($_SESSION['recentlyViewedItems'])) {
      return '';
    }
    $string = '';
    $wish   = '';
    $cart   = '';
    $ids    = array();
    if ($this->settings->en_wish == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
      $wish   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/addtowishlist-button.htm');
    }
    if ($this->settings->enableCheckout == 'yes') {
      $cart   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/addtobasket-button.htm');
    }
    $items  = $_SESSION['recentlyViewedItems'];
    arsort($items);
    $items = mcLeftMenu::sortRecentArray($items);
    $link  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/most-recent-links.htm');
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "products`
             WHERE `id` IN(" . mc_safeSQL(implode(',', $items)) . ")
             AND `pEnable` = 'yes'
             ORDER BY FIELD(`id`," . mc_safeSQL(implode(',', $items)) . ")
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PRODUCTS = mysqli_fetch_object($query)) {
      $images = $this->products->loadProductImage($PRODUCTS->id);
      $string .= str_replace(array(
        '{url}',
        '{product}',
        '{image}',
        '{price}',
        '{theme_folder}',
        '{wishlist_button}',
        '{addtobasket_button}'
      ), array(
        $this->rwr->url(array(
          $this->rwr->config['slugs']['prd'] . '/' . $PRODUCTS->id . '/' . ($PRODUCTS->rwslug ? $PRODUCTS->rwslug : $this->rwr->title($PRODUCTS->pName)),
          'pd=' . $PRODUCTS->id
        )),
        mc_safeHTML($PRODUCTS->pName),
        $images[0],
        $PRODUCTS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? '<del>' . $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pPrice)) . '</del> ' . $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pOffer)) : $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pPrice), false, true),
        THEME_FOLDER,
        str_replace('{id}', $PRODUCTS->id, $wish),
        str_replace('{id}', $PRODUCTS->id, $cart)
      ), $link);
    }
    if (isset($l[3]['id'])) {
      $this->account->updateRecent($l[3]['id'], serialize($_SESSION['recentlyViewedItems']));
    }
    return ($string ? str_replace(array(
      '{text}',
      '{links}',
      '{clear_confirm}',
      '{clear}',
      '{theme_folder}'
    ), array(
      $l[0],
      $string,
      mc_filterJS($l[1]),
      mc_filterJS($l[2]),
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/most-recent-wrapper.htm')) : '');
  }

  public function popular_products($l) {
    if ($this->settings->mostPopProducts == 0 || $this->settings->mostPopProducts == '') {
      return '';
    }
    $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/most-popular-links.htm');
    $string = '';
    $wish   = '';
    $cart   = '';
    if ($this->settings->en_wish == 'yes' && !defined('MC_TRADE_DISCOUNT')) {
      $wish   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/addtowishlist-button.htm');
    }
    if ($this->settings->enableCheckout == 'yes') {
      $cart   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/addtobasket-button.htm');
    }
    $cat    = (isset($_GET['c']) ? (int) $_GET['c'] : '0');
    if ($this->settings->mostPopPref == 'sales') {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,SUM(`productQty`) AS `pcnt`,
               `" . DB_PREFIX . "products`.`id` AS `pid`
               FROM `" . DB_PREFIX . "purchases`
               LEFT JOIN `" . DB_PREFIX . "products`
                ON `" . DB_PREFIX . "purchases`.`productID`           = `" . DB_PREFIX . "products`.`id`
               LEFT JOIN `" . DB_PREFIX . "categories`
                ON `" . DB_PREFIX . "purchases`.`categoryID`          = `" . DB_PREFIX . "categories`.`id`
               WHERE `" . DB_PREFIX . "purchases`.`saleConfirmation`  = 'yes'
               AND `" . DB_PREFIX . "purchases`.`deletedProductName`  = ''
               AND `" . DB_PREFIX . "products`.`pEnable`              = 'yes'
               AND `" . DB_PREFIX . "products`.`id`                   > 0
               " . ($cat > 0 ? 'AND `categoryID` = \'' . $cat . '\'' : '') . "
               " . ($this->settings->excludeFreePop == 'yes' ? 'AND `' . DB_PREFIX . 'products`.`pPrice` > 0' : '') . "
               AND " . MC_CATG_PMS_SQL . "
               GROUP BY `" . DB_PREFIX . "purchases`.`productID`
               ORDER BY `pcnt` DESC,`pName`
               LIMIT " . $this->settings->mostPopProducts . "
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
               LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
               LEFT JOIN `" . DB_PREFIX . "categories`
                ON `" . DB_PREFIX . "prod_category`.`category`  = `" . DB_PREFIX . "categories`.`id`
               WHERE `pEnable` = 'yes'
               " . ($cat > 0 ? 'AND `' . DB_PREFIX . 'prod_category`.`category` = \'' . $cat . '\'' : '') . "
               AND " . MC_CATG_PMS_SQL . "
               GROUP BY `" . DB_PREFIX . "prod_category`.`product`
               ORDER BY `pVisits` DESC
               LIMIT " . $this->settings->mostPopProducts . "
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    while ($PRODUCTS = mysqli_fetch_object($query)) {
      $details = '';
      $images  = $this->products->loadProductImage($PRODUCTS->pid);
      $string .= str_replace(array(
        '{url}',
        '{product}',
        '{image}',
        '{price}',
        '{theme_folder}',
        '{wishlist_button}',
        '{addtobasket_button}'
      ), array(
        $this->rwr->url(array(
          $this->rwr->config['slugs']['prd'] . '/' . $PRODUCTS->pid . '/' . ($PRODUCTS->rwslug ? $PRODUCTS->rwslug : $this->rwr->title($PRODUCTS->pName)),
          'pd=' . $PRODUCTS->pid
        )),
        mc_safeHTML($PRODUCTS->pName),
        $images[0],
        $PRODUCTS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? '<del>' . $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pPrice)) . '</del> ' . $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pOffer)) : $this->products->formatSystemCurrency(mc_formatPrice($PRODUCTS->pPrice), false, true),
        THEME_FOLDER,
        str_replace('{id}', $PRODUCTS->pid, $wish),
        str_replace('{id}', $PRODUCTS->pid, $cart)
      ), $link);
    }
    return ($string ? str_replace(array(
      '{text}',
      '{links}',
      '{theme_folder}'
    ), array(
      $l,
      trim($string),
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/most-popular-wrapper.htm')) : '');
  }

  public function brands($cat, $catname = '', $sub = 'no', $l) {
    $html = '';
    // For sub cats, just get relevant brands..
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
             WHERE `bCat`  IN('{$cat}','all')
             AND `enBrand`  = 'yes'
             ORDER BY `" . BRANDS_ORDER_BY . "`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($BRAND = mysqli_fetch_object($query)) {
      $selected = '';
      $icon     = 'caret-right';
      if (defined('MC_ORDER_AJAX')) {
        $url = $BRAND->id;
      } else {
        $url = $this->rwr->url(array(
          $this->rwr->config['slugs']['brs'] . '/' . $BRAND->id . '/1/' . ($BRAND->rwslug ? $BRAND->rwslug : $this->rwr->title($BRAND->name)),
          'pbnd=' . $BRAND->id
        ));
      }
      if (isset($_GET['brand']) && (int) $_GET['brand'] == $BRAND->id) {
        $selected = ' class="brand_selector"';
        $icon     = 'check';
      }
      $html .= str_replace(array(
        '{id}',
        '{brand}',
        '{theme_folder}',
        '{count}',
        '{selected}',
        '{icon}'
      ), array(
        $url,
        mc_safeHTML($BRAND->name),
        THEME_FOLDER,
        brandCountDisplay($BRAND->id, $this->settings),
        $selected,
        $icon
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/brands-link.htm'));
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
          $kids[$CHILDREN->id] = array(
            $CHILDREN->catname,
            $CHILDREN->rwslug
          );
        }
      }
      // Now display ALL brands for all sub cats..
      if (!empty($kids)) {
        $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
                 WHERE `bCat`  IN(" . mc_safeSQL(implode(',', array_keys($kids))) . ")
                 AND `enBrand`  = 'yes'
                 ORDER BY `bCat`,`" . BRANDS_ORDER_BY . "`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($BRAND = mysqli_fetch_object($query)) {
          $selected = '';
          $icon     = 'caret-right';
          if (isset($_GET['brand']) && (int) $_GET['brand'] == $BRAND->id) {
            $selected = ' class="brand_selector"';
            $icon     = 'check';
          }
          $html .= str_replace(array(
            '{id}',
            '{brand}',
            '{theme_folder}',
            '{selected}',
            '{icon}'
          ), array(
            $BRAND->id,
            mc_cleanData($kids[$BRAND->bCat]) . ' - ' . mc_safeHTML($BRAND->name),
            THEME_FOLDER,
            $selected,
            $icon
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/brands-link.htm'));
        }
      }
    }
    return ($html ? str_replace(array(
      '{text}',
      '{all_text}',
      '{brands}',
      '{theme_folder}'
    ), array(
      (defined('MC_ORDER_AJAX') ? $l[1][3] : $l[0]),
      $l[1][0],
      $html,
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/brands-wrapper.htm')) : '');
  }

  public function price_points($l) {
    $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/price-points-link.htm');
    $string = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "price_points`
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PP = mysqli_fetch_object($query)) {
      if (defined('MC_ORDER_AJAX')) {
        $url = $PP->id;
      } else {
        if (defined('LOAD_BRANDS')) {
          $url = $PP->id;
        } else {
          $url = $this->settings->ifolder . '/?q=&amp;price1=' . $PP->priceFrom . '&amp;price2=' . $PP->priceTo . '&amp;adv=1';
        }
      }
      $selected = '';
      $icon     = 'caret-right';
      if (isset($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']['price1']) && isset($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']['price2'])) {
        if ($_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']['price1'] == $PP->priceFrom && $_SESSION['mcGP_' . mc_encrypt(SECRET_KEY)]['search']['price2'] == $PP->priceTo) {
          $selected = ' class="point_selector"';
          $icon     = 'check';
        }
      } else {
        if (isset($_SESSION['mc_points_filters_' . mc_encrypt(SECRET_KEY)][2]) && $_SESSION['mc_points_filters_' . mc_encrypt(SECRET_KEY)][2] == $PP->id) {
          $selected = ' class="point_selector"';
          $icon     = 'check';
        }
      }
      $string .= str_replace(array(
        '{id}',
        '{price_range}',
        '{theme_folder}',
        '{selected}',
        '{icon}'
      ), array(
        $url,
        ($PP->priceText ? mc_cleanData($PP->priceText) : $this->products->formatSystemCurrency(mc_formatPrice($PP->priceFrom)) . ' - ' . $this->products->formatSystemCurrency(mc_formatPrice($PP->priceTo))),
        THEME_FOLDER,
        $selected,
        $icon
      ), $link);
    }
    return ($string ? str_replace(array(
      '{text}',
      '{all_text}',
      '{price_ranges}',
      '{theme_folder}'
    ), array(
      (defined('SEARCH_RESULTS_SCREEN') || defined('LOAD_BRANDS') || defined('MC_ORDER_AJAX') ? $l[1][0] : $l[0]),
      $l[2][2],
      $string,
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/price-points-wrapper.htm')) : '');
  }

  private function sortRecentArray($a) {
    $sort = array();
    foreach ($a AS $k => $v) {
      $sort[] = $k;
    }
    return $sort;
  }

  public function menu_data() {
    $arr = array();
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `ident`,`name`,`tmp` FROM `" . DB_PREFIX . "boxes`
         WHERE `status` = 'yes'
         ORDER BY `orderby`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($BX = mysqli_fetch_object($q)) {
      $arr[] = array(
        0 => $BX->ident,
        1 => mc_cleanData($BX->name),
        2 => $BX->tmp
      );
    }
    return $arr;
  }

}

?>