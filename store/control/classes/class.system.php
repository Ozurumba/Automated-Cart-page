<?php

class mcSystem {

  public $settings;
  public $cache;
  public $rwr;
  public $tweet_visibility = 3;

  public function blog($l) {
    $html = '';
    $tmp  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/home-blog.htm');
    $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "blog`
            WHERE `enabled` = 'yes'
            AND DATE(FROM_UNIXTIME(`published`)) <= '" . date('Y-m-d') . "'
			      ORDER BY `published` DESC
            ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($BLG = mysqli_fetch_object($q)) {
      $html .= str_replace(array(
        '{title}',
        '{message}',
        '{date}',
        '{theme_folder}'
      ), array(
        mc_cleanData($BLG->title),
        mc_txtParsingEngine($BLG->message),
        str_replace('{date}',mc_convertMySQLDate(date('Y-m-d',$BLG->published), $this->settings),$l[0]),
        THEME_FOLDER
      ), $tmp);
    }
    return $html;
  }

  public function headOptions($opt, $acc, $currencies, $lang = array()) {
    global $mc_header;
    switch($opt) {
      case 'currencies':
        $html = array();
        $set  = (isset($acc['currency']) && in_array($acc['currency'], array_keys($currencies)) ? $acc['currency'] : (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency']) && in_array($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'],array_keys($currencies)) ? $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'] : $this->settings->baseCurrency));
        $tmp  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/options-li.htm');
        $tmps = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/options-li-selected.htm');
        $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "currencies`
                WHERE (`enableCur`  = 'yes'
                  AND `rate`        > 0
                ) OR (
                  `currency` = '{$this->settings->baseCurrency}'
                )
                ORDER BY `curname`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($CV = mysqli_fetch_object($q)) {
          $html[] = str_replace(array(
            '{selected}',
            '{link}',
            '{text}',
            '{check}',
            '{js}'
          ), array (
            ($set == $CV->currency ? ' class="active"' : ''),
            ($set == $CV->currency ? '#' : $this->settings->ifolder . '/?cg_cur=' . $CV->currency),
            mc_safeHTML($CV->curname),
            ($set == $CV->currency ? $tmps : ''),
            ''
          ), $tmp);
        }
        break;
      case 'lang':
        $set  = (isset($acc['language']) && is_dir(PATH . 'content/language/' . $acc['language']) ? $acc['language'] : (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language']) && is_dir(PATH . 'content/language/' . $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language']) ? $_SESSION[mc_encrypt(SECRET_KEY) . '_mc_language'] : $this->settings->languagePref));
        $tmp  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/options-li.htm');
        $tmps = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/options-li-selected.htm');
        $html = array();
        if (!empty($lang)) {
          for ($i=0; $i<count($lang); $i++) {
            if (is_dir(PATH . 'content/language/' . $lang[$i][0])) {
              $html[] = str_replace(array(
                '{selected}',
                '{link}',
                '{text}',
                '{check}',
                '{js}'
              ), array (
                ($set == $lang[$i][0] ? ' class="active"' : ''),
                ($set == $lang[$i][0] ? '#' : $this->settings->ifolder . '/?cg_lang=' . $lang[$i][0]),
                mc_safeHTML($lang[$i][1]),
                ($set == $lang[$i][0] ? $tmps : ''),
                ''
              ), $tmp);
            }
          }
        }
        break;
    }
    return (!empty($html) ? implode(mc_defineNewline(), $html) : '');
  }

  public function brandFilterCats($arr = array()) {
    $bd = array();
    if (!empty($arr)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "brands`
           WHERE (`bCat`  IN(" . mc_safeSQL(implode(',',$arr)) . ") OR `bCat` = 'all')
           AND `enBrand`  = 'yes'
           ORDER BY `" . BRANDS_ORDER_BY . "`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($B = mysqli_fetch_object($q)) {
        $bd[] = $B->id;
      }
    }
    return $bd;
  }

  public function searchCatBrands($cat = 0) {
    global $public_search30;
    $html = '';
    $all  = '';
    if ($cat > 0) {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "brands`
               WHERE `bCat`  IN('{$cat}','all')
               AND `enBrand`  = 'yes'
               ORDER BY `" . BRANDS_ORDER_BY . "`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    } else {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,GROUP_CONCAT(`id`) AS `ids` FROM `" . DB_PREFIX . "brands`
               WHERE `enBrand`  = 'yes'
			         GROUP BY `name`
               ORDER BY `" . BRANDS_ORDER_BY . "`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
    }
    while ($BRAND = mysqli_fetch_object($query)) {
      $html .= str_replace(array(
        '{value}',
        '{selected}',
        '{name}',
        '{theme_folder}'
      ), array(
        (isset($BRAND->ids) ? $BRAND->ids : $BRAND->id),
        '',
        mc_safeHTML($BRAND->name),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-option-tags.htm'));
    }
    if ($cat > 0) {
      $all = str_replace(array(
        '{value}',
        '{selected}',
        '{name}',
        '{theme_folder}'
      ), array(
        '0',
        '',
        $public_search30,
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/html-option-tags.htm'));
    }
    return ($html ? $all . $html : '');
  }

  public function buildNewsTicker($cmd) {
    global $msg_public_header35;
    if (!isset($_GET['np'])) {
      if (NEWS_TICKER_DISPLAY_PREF && $cmd != 'home') {
        return '';
      }
    } else {
      if (NEWS_TICKER_DISPLAY_CUSTOM_PAGES == '' || NEWS_TICKER_DISPLAY_CUSTOM_PAGES == '0') {
        return '';
      } else {
        $boom = explode(',', NEWS_TICKER_DISPLAY_CUSTOM_PAGES);
        if (!in_array($_GET['np'], $boom)) {
          return '';
        }
      }
    }
    $html = array();
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "news_ticker` WHERE `enabled` = 'yes' ORDER BY `orderBy`") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($query) > 0) {
      while ($NEWS = mysqli_fetch_object($query)) {
        $html[] = str_replace(array(
          '{news_item}',
          '{theme_folder}'
        ), array(
          mc_cleanData($NEWS->newsText, THEME_FOLDER)
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/ticker-news-item.htm'));
      }
      return (!empty($html) ? str_replace(array(
        '{news}',
        '{text}',
        '{theme_folder}'
      ), array(
        implode(mc_defineNewline(), $html),
        $msg_public_header35,
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/ticker-wrapper.htm')) : '');
    }
  }

  public function breadcrumbs($breadcrumbs, $l) {
    if (!empty($breadcrumbs)) {
      $bc = array('<a href="' . $this->rwr->url(array('base_href')) . '">' . $l . '</a>');
      foreach ($breadcrumbs AS $bcl) {
        $bc[] = $bcl;
      }
      return implode(BREADCRUMBS_SEPARATOR, $bc);
    }
  }

  public function bannerSlider($cat = 0, $home = false, $type = '') {
    if (MC_MB_BANNERS && MC_PLATFORM_DETECTION == 'mobile') {
      return '';
    }
    $html         = array();
    $pages        = '';
    $cur          = date('Y-m-d');
    $count        = 0;
    $queryBuilder = array();
    if ($cat > 0) {
      $queryBuilder[] = "WHERE ((FIND_IN_SET('{$cat}',`bannerCats`) > 0 AND `bannerLive` = 'yes' AND `bannerHome` = 'no') OR (`bannerCats` = '' AND `bannerHome` = 'no' AND `bannerLive` = 'yes'))";
    }
    if ($home) {
      $queryBuilder[] = (empty($queryBuilder) ? 'WHERE (' : 'AND (') . "`bannerHome` IN('yes','no') AND `bannerLive` = 'yes')";
    } else {
      $queryBuilder[] = (empty($queryBuilder) ? 'WHERE (' : 'AND (') . "`bannerHome` IN('no') AND `bannerLive` = 'yes')";
    }
    if ($type == 'trade') {
      $queryBuilder[] = (empty($queryBuilder) ? 'WHERE (' : 'AND (') . "`trade` IN('yes','no'))";
    } else {
      $queryBuilder[] = (empty($queryBuilder) ? 'WHERE (' : 'AND (') . "`trade` = 'no')";
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "banners`
             " . implode(mc_defineNewline(), $queryBuilder) . "
             ORDER BY `bannerOrder`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($query) > 0) {
      while ($B = mysqli_fetch_object($query)) {
        if (file_exists(PATH . str_replace('{theme}', THEME_FOLDER, BANNER_FOLDER) . '/' . $B->bannerFile)) {
          $incBanner = 'yes';
          // Is this banner restricted to date range..
          if ($B->bannerFrom != '0000-00-00' && $B->bannerTo != '0000-00-00') {
            if ($B->bannerFrom <= $cur && $B->bannerTo >= $cur) {
              $incBanner = 'yes';
            } else {
              $incBanner = 'no';
            }
          }
          //Do we include banner?
          if ($incBanner == 'yes') {
            ++$count;
            $html[] = str_replace(array(
              '{url}',
              '{image}',
              '{name}',
              '{theme_folder}',
              '{banner_folder}',
              '{base}'
            ), array(
              ($B->bannerUrl ? $B->bannerUrl : '#'),
              $B->bannerFile,
              mc_safeHTML($B->bannerText),
              THEME_FOLDER,
              str_replace('{theme}','',BANNER_FOLDER),
              $this->settings->ifolder
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/header-slider-img.htm'));
          }
        }
      }
    }
    if (!empty($html)) {
      return str_replace(
        '{slides}',
        implode(mc_defineNewline(), $html),
        mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/header-slider.htm')
      );
    }
    return '';
  }

  public function orderBy($items, $type = 'options-li') {
    $string = '';
    $chk    = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/options-li-selected.htm');
    if (!empty($items)) {
      foreach ($items AS $k => $v) {
        $string .= str_replace(array(
          '{value}',
          '{link}',
          '{selected}',
          '{name}',
          '{theme_folder}',
          '{text}',
          '{check}',
          '{js}'
        ), array(
          $k,
          ($type == 'options-li' ? '#' : $k),
          (isset($_GET['order']) && $_GET['order'] == $k ? ' class="active"' : ''),
          $v,
          THEME_FOLDER,
          $v,
          (isset($_GET['order']) && $_GET['order'] == $k ? $chk : ''),
          ($type == 'options-li' ? ' onclick="mc_flor(\'sort\', this, \'' . $k . '\');return false"' : '')
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/' . $type . '.htm'));
      }
    }
    return trim($string);
  }

  public function loadShippingRates() {
    global $public_returns4, $public_returns5, $public_returns6, $public_returns7, $msg_script5, $msg_script6, $public_returns9, $public_returns10;
    $string = '';
    $q_zone = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`z`.`id` AS `zoneID` FROM `" . DB_PREFIX . "zones` AS `z`,`" . DB_PREFIX . "countries` AS `c`
              WHERE `c`.`id`  = `z`.`zCountry`
              AND `zCountry`  = '{$_GET['country']}'
              ORDER BY `zName`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($ZONE = mysqli_fetch_object($q_zone)) {
      $string .= str_replace(array(
        '{text1}',
        '{text2}',
        '{text3}',
        '{text4}',
        '{country}',
        '{zone}',
        '{theme_folder}'
      ), array(
        $public_returns4,
        $public_returns5,
        $public_returns6,
        $public_returns7,
        mc_safeHTML($ZONE->cName),
        mc_safeHTML($ZONE->zName),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/shipping-and-returns/shipping-zone-header.htm'));
      // Attach services to this zone..
      $q_services = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "services`
                    WHERE `inZone` = '{$ZONE->zoneID}'
                    ORDER BY `sName`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($SERVICES = mysqli_fetch_object($q_services)) {
        $q_rates = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "rates`
                   WHERE `rService` = '{$SERVICES->id}'
                   ORDER BY `id`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($RATES = mysqli_fetch_object($q_rates)) {
          $string .= str_replace(array(
            '{name}',
            '{estimation}',
            '{weight_from}',
            '{weight_to}',
            '{signature}',
            '{cost}',
            '{theme_folder}'
          ), array(
            (isset($curName) && $curName == mc_cleanData($SERVICES->sName) ? '&nbsp;' : mc_cleanData($SERVICES->sName)),
            (isset($curName) && $curName == mc_cleanData($SERVICES->sName) ? '&nbsp;' : str_replace('{estimation}', mc_cleanData($SERVICES->sEstimation), $public_returns9)),
            $RATES->rWeightFrom,
            $RATES->rWeightTo,
            ($SERVICES->sSignature == 'yes' ? $msg_script5 : $msg_script6),
            mc_currencyFormat($RATES->rCost),
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/shipping-and-returns/shipping-rate.htm'));
          $curName = mc_cleanData($SERVICES->sName);
        }
      }
      // Attach zone areas..
      $za = '';
      $q_zonea = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zone_areas`
                 WHERE `inZone` = '{$ZONE->zoneID}'
                 ORDER BY `areaName`
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($ZA = mysqli_fetch_object($q_zonea)) {
        $za .= mc_safeHTML($ZA->areaName) . ZONE_AREA_DELIMITER . ' ';
      }
      // Remove trailing delimiter..
      $za = substr_replace($za, '', -strlen(ZONE_AREA_DELIMITER . ' '));
      $string .= str_replace(array(
        '{text}',
        '{areas}',
        '{theme_folder}'
      ), array(
        $public_returns10,
        trim($za),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/shipping-and-returns/shipping-zone-areas.htm'));
    }
    return trim($string);
  }

  public function newPageLinksFooter($area = 'left') {
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/footer-links-' . $area . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    $link   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/footer-bar-link.htm');
    $string = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "newpages`
             WHERE `enabled`              = 'yes'
             AND FIND_IN_SET(" . ($area == 'left' ? '2' : '3') . ",`linkPos`) > 0
             AND `landingPage`            = 'no'
             " . (defined('MC_TRADE_DISCOUNT') ? 'AND `trade` IN(\'yes\',\'no\')' : 'AND `trade` IN(\'no\')') . "
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($LINKS = mysqli_fetch_object($query)) {
      if ($LINKS->linkExternal == 'yes' && (substr(strtolower($LINKS->pageText), 0, 7) == 'http://' || substr(strtolower($LINKS->pageText), 0, 8) == 'https://')) {
        $url    = trim($LINKS->pageText);
        $target = ($LINKS->linkTarget == 'new' ? ' onclick="window.open(this);return false"' : '');
      } else {
        $url    = $this->rwr->url(array(
          $this->rwr->config['slugs']['npg'] . '/' . $LINKS->id . '/' . ($LINKS->rwslug ? $LINKS->rwslug : $this->rwr->title($LINKS->pageName)),
          'np=' . $LINKS->id
        ));
        $target = '';
      }
      $string .= str_replace(array(
        '{url}',
        '{text}',
        '{target}'
      ), array(
        $url,
        mc_safeHTML($LINKS->pageName),
        $target
      ), $link);
    }
    // Update cache if enabled..
    $this->cache->cache_file($mCache, $string);
    return $string;
  }

  public function loadHomepageCategories() {
    global $public_home6, $msg_public_header36;
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/homepage-cats-' . MC_CACHE_FLAG . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    $cats  = '';
    $links = '';
    $tmp   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/home-categories-link.htm');
    // Gift cert link positioning..(at the end)
    if (defined('MC_TRADE_DISCOUNT')) {
      $hmGift = 0;
    } else {
      $hmGift = mc_rowCount('giftcerts WHERE `enabled` = \'yes\'');
    }
    if ($this->settings->catGiftPos == 'start' && $hmGift > 0) {
      $image = 'default_img.png';
      if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.jpg')) {
        $image = 'default_gift_icon.jpg';
      }
      if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.png')) {
        $image = 'default_gift_icon.png';
      }
      $links = str_replace(array(
        '{text}',
        '{url}',
        '{image}',
        '{theme_folder}',
        '{folder}',
        '{base}',
        '{add_class}'
      ), array(
        $msg_public_header36,
        $this->rwr->url(array('gift')),
        $image,
        THEME_FOLDER,
        PRODUCTS_FOLDER,
        $this->settings->ifolder,
        ($image == 'default_img.png' ? 'no-image' : 'img-responsive')
      ), $tmp);
    }
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `enCat`      = 'yes'
              AND " . MC_CATG_PMS_SQL . "
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    if (mysqli_num_rows($q_cats) > 0) {
      while ($CAT = mysqli_fetch_object($q_cats)) {
        $image = 'default_img.png';
        if ($CAT->imgIcon && file_exists(PATH . PRODUCTS_FOLDER . '/' . $CAT->imgIcon)) {
          $image = $CAT->imgIcon;
        }
        $url = $this->rwr->url(array(
          $this->rwr->config['slugs']['cat'] . '/' . $CAT->id . '/1/' . ($CAT->rwslug ? $CAT->rwslug : $this->rwr->title($CAT->catname)),
          'c=' . $CAT->id
        ));
        $links .= str_replace(array(
          '{text}',
          '{url}',
          '{image}',
          '{theme_folder}',
          '{folder}',
          '{base}',
          '{add_class}'
        ), array(
          mc_safeHTML($CAT->catname),
          $url,
          $image,
          THEME_FOLDER,
          PRODUCTS_FOLDER,
          $this->settings->ifolder,
          ($image == 'default_img.png' ? 'no-image' : 'img-responsive')
        ), $tmp);
        // Gift cert link positioning..(after parent)
        if ($this->settings->catGiftPos == $CAT->id && $hmGift > 0) {
          $image = 'default_img.png';
          if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.jpg')) {
            $image = 'default_gift_icon.jpg';
          }
          if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.png')) {
            $image = 'default_gift_icon.png';
          }
          $links .= str_replace(array(
            '{text}',
            '{url}',
            '{image}',
            '{theme_folder}',
            '{folder}',
            '{base}',
            '{add_class}'
          ), array(
            $msg_public_header36,
            $this->rwr->url(array('gift')),
            $image,
            THEME_FOLDER,
            PRODUCTS_FOLDER,
            $this->settings->ifolder,
            ($image == 'default_img.png' ? 'no-image' : 'img-responsive')
          ), $tmp);
        }
      }
      // Gift cert link positioning..(at the end)
      if ($this->settings->catGiftPos == 'end' && $hmGift > 0) {
        $image = 'default_img.png';
        if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.jpg')) {
          $image = 'default_gift_icon.jpg';
        }
        if (file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.png')) {
          $image = 'default_gift_icon.png';
        }
        $links .= str_replace(array(
          '{text}',
          '{url}',
          '{image}',
          '{theme_folder}',
          '{folder}',
          '{base}',
          '{add_class}'
        ), array(
          $msg_public_header36,
          $this->rwr->url(array('gift')),
          $image,
          THEME_FOLDER,
          PRODUCTS_FOLDER,
          $this->settings->ifolder,
          ($image == 'default_img.png' ? 'no-image' : 'img-responsive')
        ), $tmp);
      }
      $find    = array(
        '{text}',
        '{categories}',
        '{theme_folder}'
      );
      $replace = array(
        $public_home6,
        $links,
        THEME_FOLDER
      );
      $cats    = str_replace($find, $replace, mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/categories/home-categories.htm'));
    }
    if ($cats) {
      // Update cache if enabled..
      $this->cache->cache_file($mCache, $cats);
    }
    return trim($cats);
  }

  public function catCountDisplay($id, $giftcount = 0) {
    global $MCCART;
    if ($this->settings->menuCatCount == 'yes') {
      if ($giftcount > 0) {
        $c = $giftcount;
      } else {
        $pCount = $MCCART->productList('category', array('count' => 'yes', 'catover' => $id));
        $c      = $pCount;
      }
      return '<span class="menuProdCount">(' . ($c > 0 ? @number_format($c) : '0'). ')</span>';
    }
    return '';
  }

  public function loadCategories() {
    global $msg_public_header36;
    // Cached?
    $mCache = $this->cache->cache_options['cache_dir'] . '/categories-' . MC_CACHE_FLAG . $this->cache->cache_options['cache_ext'];
    if ($this->cache->cache_options['cache_enable'] == 'yes' && file_exists($mCache)) {
      if ($this->cache->cache_exp($this->cache->cache_time($mCache)) == 'load') {
        return mc_loadTemplateFile($mCache);
      }
    }
    $mcMenu       = array();
    $tmp_parent   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/categories.htm');
    // Legacy check..
    if (file_exists(PATH . THEME_FOLDER . '/html/left-menu/categories-no-subs.htm')) {
      $tmp_no_subs  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/categories-no-subs.htm');
    } else {
      $tmp_no_subs = $tmp_parent;
    }
    $tmp_child    = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/categories-children.htm');
    $tmp_infant   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/categories-infants.htm');
    $tmp_gift     = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/left-menu/categories-gift.htm');
    $defMenuPanel = DEF_OPEN_MENU_PANEL;
    if (isset($_SESSION[mc_encrypt('store_menu_panel' . SECRET_KEY)])) {
      $defMenuPanel = $_SESSION[mc_encrypt('store_menu_panel' . SECRET_KEY)];
    }
    if (defined('MC_TRADE_DISCOUNT')) {
      $hmGift = 0;
    } else {
      $hmGift     = mc_rowCount('giftcerts WHERE `enabled` = \'yes\'');
    }
    // Gift cert link positioning..(at the end)
    if ($this->settings->catGiftPos == 'start' && $hmGift > 0) {
      $mcMenu['gift'] = array(
        'url' => $this->rwr->url(array('gift')),
        'name' => $msg_public_header36,
        'icon' => 'default_gift_icon.png',
        'count' => mcSystem::catCountDisplay(0, $hmGift),
        'children' => array()
      );
    }
    // Parents..
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              AND " . MC_CATG_PMS_SQL . "
              ORDER BY `orderBy`
              ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['cat'] . '/' . $CATS->id . '/1/' . ($CATS->rwslug ? $CATS->rwslug : $this->rwr->title($CATS->catname)),
        'c=' . $CATS->id
      ));
      $mcMenu[$CATS->id] = array(
        'url' => $url,
        'name' => mc_safeHTML($CATS->catname),
        'icon' => $CATS->imgIcon,
        'children' => array(),
        'count' => mcSystem::catCountDisplay($CATS->id)
      );
      // Children..
      $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                    WHERE `catLevel` = '2'
                    AND `childOf`    = '{$CATS->id}'
                    AND `enCat`      = 'yes'
                    AND " . MC_CATG_PMS_SQL . "
                    ORDER BY `orderBy`
                    ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($q_children) > 0) {
        while ($CHILDREN = mysqli_fetch_object($q_children)) {
          $url = $this->rwr->url(array(
            $this->rwr->config['slugs']['cat'] . '/' . $CHILDREN->id . '/1/' . ($CHILDREN->rwslug ? $CHILDREN->rwslug : $this->rwr->title($CHILDREN->catname)),
            'c=' . $CHILDREN->id
          ));
          $mcMenu[$CATS->id]['children'][$CHILDREN->id] = array(
            'url' => $url,
            'name' => mc_safeHTML($CHILDREN->catname),
            'icon' => $CHILDREN->imgIcon,
            'infants' => array(),
            'count' => mcSystem::catCountDisplay($CHILDREN->id)
          );
          // Gift cert link positioning..(after child)
          if ($this->settings->catGiftPos == $CHILDREN->id && $hmGift > 0) {
            $mcMenu['gift'] = array(
              'url' => $this->rwr->url(array('gift')),
              'name' => $msg_public_header36,
              'icon' => 'default_gift_icon.png',
              'count' => mcSystem::catCountDisplay(0, $hmGift),
              'children' => array()
            );
          }
          // Infants..
          $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                       WHERE `catLevel` = '3'
                       AND `childOf`    = '{$CHILDREN->id}'
                       AND `enCat`      = 'yes'
                       AND " . MC_CATG_PMS_SQL . "
                       ORDER BY `orderBy`
                       ") or die(mc_MySQLError(__LINE__, __FILE__));
          if (mysqli_num_rows($q_infants) > 0) {
            while ($INFANTS = mysqli_fetch_object($q_infants)) {
              $url = $this->rwr->url(array(
                $this->rwr->config['slugs']['cat'] . '/' . $INFANTS->id . '/1/' . ($INFANTS->rwslug ? $INFANTS->rwslug : $this->rwr->title($INFANTS->catname)),
                'c=' . $INFANTS->id
              ));
              $mcMenu[$CATS->id]['children'][$CHILDREN->id]['infants'][] = array(
                'url' => $url,
                'name' => mc_safeHTML($INFANTS->catname),
                'icon' => $INFANTS->imgIcon,
                'count' => mcSystem::catCountDisplay($INFANTS->id)
              );
              // Gift cert link positioning..(after infant)
              if ($this->settings->catGiftPos == $INFANTS->id && $hmGift > 0) {
                $mcMenu['gift'] = array(
                  'url' => $this->rwr->url(array('gift')),
                  'name' => $msg_public_header36,
                  'icon' => 'default_gift_icon.png',
                  'count' => mcSystem::catCountDisplay(0, $hmGift),
                  'children' => array()
                );
              }
            }
          } else {
          }
        }
      } else {
      }
      // Gift cert link positioning..(after parent)
      if ($this->settings->catGiftPos == $CATS->id && $hmGift > 0) {
        $mcMenu['gift'] = array(
          'url' => $this->rwr->url(array('gift')),
          'name' => $msg_public_header36,
          'icon' => 'default_gift_icon.png',
          'count' => mcSystem::catCountDisplay(0, $hmGift),
          'children' => array()
        );
      }
    }
    // Gift cert link positioning..(at the end)
    if ($this->settings->catGiftPos == 'end' && $hmGift > 0) {
      $mcMenu['gift'] = array(
        'url' => $this->rwr->url(array('gift')),
        'name' => $msg_public_header36,
        'icon' => 'default_gift_icon.png',
        'count' => mcSystem::catCountDisplay(0, $hmGift),
        'children' => array()
      );
    }
    // Build menu..
    $mn = '';
    $mf = '';
    if (!empty($mcMenu)) {
      foreach (array_keys($mcMenu) AS $mAK) {
        switch($mAK) {
          case 'gift':
            $gImg = (file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['icon']) ? $mcMenu[$mAK]['icon'] : 'default_icon.png');
            if ($gImg == 'default_icon.png' && file_exists(PATH . PRODUCTS_FOLDER . '/default_gift_icon.jpg')) {
              $mcMenu[$mAK]['icon'] = 'default_gift_icon.jpg';
            }
            $mn .= str_replace(array(
              '{url}','{icon}','{count}','{text}','{theme_folder}','{base}','{folder}'
            ),
            array(
              $mcMenu[$mAK]['url'],
              ($mcMenu[$mAK]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['icon']) ? $mcMenu[$mAK]['icon'] : 'default_icon.png'),
              $mcMenu[$mAK]['count'],
              $mcMenu[$mAK]['name'],
              THEME_FOLDER,
              $this->settings->ifolder,
              PRODUCTS_FOLDER
            ),
            $tmp_gift);
            break;
          default:
            // Does category have children?
            if (!empty($mcMenu[$mAK]['children'])) {
              $defMenuCalc =
              $children = '';
              $defMCalc = '';
              $parAsChld = '';
              foreach (array_keys($mcMenu[$mAK]['children']) AS $mCD) {
                // Parent as child?
                if ($parAsChld == '' && defined('OFF_CANVAS_PARENT_AS_CHILD') && OFF_CANVAS_PARENT_AS_CHILD) {
                  $children .= str_replace(array(
                  '{url}','{icon}','{count}','{text}','{infants}','{theme_folder}','{base}','{folder}'
                  ),
                  array(
                    $mcMenu[$mAK]['url'],
                    ($mcMenu[$mAK]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['icon']) ? $mcMenu[$mAK]['icon'] : 'default_icon.png'),
                    $mcMenu[$mAK]['count'],
                    $mcMenu[$mAK]['name'],
                    '',
                    THEME_FOLDER,
                    $this->settings->ifolder,
                    PRODUCTS_FOLDER
                  ),
                  $tmp_child);
                  $parAsChld = 'yes';
                }
                $infants = '';
                // If default panel isn`t set, attempt to auto open first panel with children..
                if ($defMCalc == '' && $defMenuPanel == 0) {
                  $defMCalc = 'yes';
                  $defMenuPanel = $mAK;
                }
                // Do children have infants?
                if (!empty($mcMenu[$mAK]['children'][$mCD]['infants'])) {
                  foreach (array_keys($mcMenu[$mAK]['children'][$mCD]['infants']) AS $mIF) {
                    $infants .= str_replace(array(
                      '{url}','{icon}','{count}','{text}','{theme_folder}','{base}','{folder}'
                    ),
                    array(
                      $mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['url'],
                      ($mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['icon']) ? $mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['icon'] : 'default_icon.png'),
                      $mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['count'],
                      $mcMenu[$mAK]['children'][$mCD]['infants'][$mIF]['name'],
                      THEME_FOLDER,
                      $this->settings->ifolder,
                      PRODUCTS_FOLDER
                    ),
                    $tmp_infant);
                  }
                }
                $children .= str_replace(array(
                '{url}','{icon}','{count}','{text}','{infants}','{theme_folder}','{base}','{folder}'
                ),
                array(
                  $mcMenu[$mAK]['children'][$mCD]['url'],
                  ($mcMenu[$mAK]['children'][$mCD]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['children'][$mCD]['icon']) ? $mcMenu[$mAK]['children'][$mCD]['icon'] : 'default_icon.png'),
                  $mcMenu[$mAK]['children'][$mCD]['count'],
                  $mcMenu[$mAK]['children'][$mCD]['name'],
                  ($infants ? $infants : ''),
                  THEME_FOLDER,
                  $this->settings->ifolder,
                  PRODUCTS_FOLDER
                ),
                $tmp_child);
              }
              $mn .= str_replace(array(
                '{url}','{icon}','{count}','{text}','{children}','{theme_folder}',
                '{base}','{folder}','{id}','{collapsed}','{expanded}','{in}'
              ),
              array(
                $mcMenu[$mAK]['url'],
                ($mcMenu[$mAK]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['icon']) ? $mcMenu[$mAK]['icon'] : 'default_icon.png'),
                $mcMenu[$mAK]['count'],
                $mcMenu[$mAK]['name'],
                ($children ? $children : ''),
                THEME_FOLDER,
                $this->settings->ifolder,
                PRODUCTS_FOLDER,
                $mAK,
                ($mAK != $defMenuPanel ? ' class="collapsed" ' : ' '),
                ($mAK == $defMenuPanel ? 'true' : 'false'),
                ($mAK == $defMenuPanel ? ' in' : '')
              ),
              $tmp_parent);
            } else {
              $mn .= str_replace(array(
                '{url}','{icon}','{count}','{text}','{theme_folder}','{base}','{folder}'
              ),
              array(
                $mcMenu[$mAK]['url'],
                ($mcMenu[$mAK]['icon'] && file_exists(PATH . PRODUCTS_FOLDER . '/' . $mcMenu[$mAK]['icon']) ? $mcMenu[$mAK]['icon'] : 'default_icon.png'),
                $mcMenu[$mAK]['count'],
                $mcMenu[$mAK]['name'],
                THEME_FOLDER,
                $this->settings->ifolder,
                PRODUCTS_FOLDER
              ),
              $tmp_no_subs);
            }
            break;
        }
      }
    }
    if ($mn) {
      $mf = $mn;
    }
    // Update cache if enabled..
    $this->cache->cache_file($mCache, $mf);
    return $mf;
  }

  // Loads javascript/css code in header based on page load..
  // Not all files are required, so only load applicable ones..
  public function loadJSFunctions($load = array(), $loc = 'header') {
    $html = '';
    switch($loc) {
      case 'header':
        if (array_key_exists('swipe', $load)) {
          $html .= '<link rel="stylesheet" href="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/css/jquery.swipebox.css" type="text/css">' . mc_defineNewline();
        }
        if (array_key_exists('banners', $load)) {
          $html .= '<link rel="stylesheet" href="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/css/jquery.slippry.css" type="text/css">' . mc_defineNewline();
        }
        if (array_key_exists('ibox', $load)) {
          $html .= '<link rel="stylesheet" href="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/css/jquery.ibox.css" type="text/css">' . mc_defineNewline();
        }
        break;
      case 'footer':
        if (array_key_exists('rssNewsScroller', $load) && $this->settings->rssScroller == 'yes') {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.zrssfeed.js"></script>' . mc_defineNewline();
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.vticker.js"></script>' . mc_defineNewline();
          $html .= str_replace(array('{url}','{limit}'),array($this->settings->rssScrollerUrl, $this->settings->rssScrollerLimit),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/rss.htm'));
        }
        if (array_key_exists('swipe', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.swipebox.js"></script>' . mc_defineNewline();
          $html .= mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/swipebox.htm');
        }
        if (array_key_exists('ibox', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.ibox.js"></script>' . mc_defineNewline();
        }
        if (array_key_exists('ticker', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.pause.js"></script>' . mc_defineNewline();
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.marquee.js"></script>' . mc_defineNewline();
          $html .= mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/ticker.htm');
        }
        if (array_key_exists('banners', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.slippry.js"></script>' . mc_defineNewline();
          $html .= mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/banner-slider.htm');
        }
        if (array_key_exists('mc-acc-ops', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/mc-ac-ops.js"></script>' . mc_defineNewline();
        }
        if (array_key_exists('latestTweets', $load) && $this->settings->twitterLatest == 'yes') {
          $twl   = ($this->settings->tweetlimit > $this->tweet_visibility ? $this->tweet_visibility : $this->settings->tweetlimit);
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.tweetscroll.js"></script>' . mc_defineNewline();
          $html .= str_replace(array('{limit}','{visible}','{url}'),array($this->settings->tweetlimit,$twl,$this->settings->ifolder . '/?tweetscroller=yes'),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/tweetscroll.htm'));
        }
        if (array_key_exists('jquery-ui', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/jquery-ui.js"></script>' . mc_defineNewline();
        }
        if (array_key_exists('priceFormat', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.priceformat.js"></script>' . mc_defineNewline();
        }
        if (array_key_exists('checkout', $load)) {
          $fr    = array();
          $html .= strtr(mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/checkout.htm'), $fr);
        }
        if (array_key_exists('wish-zone', $load)) {
          $html .= str_replace('{id}',$load['params'][0],mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/wish-zone.htm'));
        }
        if (array_key_exists('states', $load)) {
          $html .= str_replace('{id}',$load['params'][0],mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/states.htm'));
        }
        if (array_key_exists('adv_search', $load)) {
          $html  .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/jquery.priceformat.js"></script>' . mc_defineNewline();
          $slider = ($this->settings->searchSlider ? unserialize($this->settings->searchSlider) : array());
          $fr     = array(
            '{calendar1}' => $load['params'][1],
            '{calendar2}' => $load['params'][0],
            '{calendar3}' => $this->settings->jsWeekStart,
            '{calendar4}' => mc_datePickerFormat($this->settings),
            '{slider_min}' => (isset($slider['min']) ? $slider['min'] : '0'),
            '{slider_max}' => (isset($slider['max']) ? $slider['max'] : '300'),
            '{slider_start}' => (isset($slider['start']) ? $slider['start'] : '5'),
            '{slider_end}' => (isset($slider['end']) ? $slider['end'] : '100')
          );
          $html .= strtr(mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/advanced-search.htm'), $fr);
        }
        if (array_key_exists('soundmanager', $load)) {
          $html .= '<script src="' . $this->settings->ifolder . '/' . THEME_FOLDER . '/js/plugins/soundmanager2.js"></script>' . mc_defineNewline();
          $html .= str_replace('{theme_folder}',THEME_FOLDER,mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/js/soundmanager.htm'));
        }
        break;
    }
    return ltrim($html);
  }

}

?>