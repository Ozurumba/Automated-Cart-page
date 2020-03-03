<?php

class rssFeed {

  public $xmlVersion = '1.0';
  public $encoding = 'utf-8';
  public $rssVersion = '2.0';
  public $language = 'en-us';
  public $settings;
  public $thisFeedUrl;
  public $rwr;

  // Get special offers..
  public function getSpecialOfferProducts($build_date, $cat) {
    $string = '';
    if (defined('MC_TRADE_DISCOUNT')) {
      return $string;
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
             `" . DB_PREFIX . "products`.`id` AS `pid`
             FROM `" . DB_PREFIX . "products`
             LEFT JOIN `" . DB_PREFIX . "prod_category`
             ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
             WHERE `pEnable`                 = 'yes'
             AND `pOffer`                    > 0
             " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
             " . ($cat > 0 ? 'AND `category`     = \'' . $cat . '\'' : '') . "
             GROUP BY `" . DB_PREFIX . "products`.`id`
             ORDER BY `" . DB_PREFIX . "products`.`" . ORDER_SPECIAL_OFFERS . "`
             LIMIT " . $this->settings->rssFeedLimit . "
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($RSS = mysqli_fetch_object($query)) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $RSS->pid . '/' . ($RSS->rwslug ? $RSS->rwslug : $this->rwr->title($RSS->pName)),
        'pd=' . $RSS->pid
      ));
      $string .= rssFeed::addItem($RSS->pName, $url, ($RSS->rssBuildDate ? $RSS->rssBuildDate : $build_date), mc_txtParsingEngine($RSS->pDescription), $RSS->pid, ($RSS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? mc_currencyFormat($RSS->pOffer) . ' <del>' . mc_currencyFormat($RSS->pPrice) . '</del>' : mc_currencyFormat($RSS->pPrice, '', true)));
    }
    return trim($string);
  }

  // Get brand products..
  public function getBrands($build_date, $brands) {
    $string = '';
    $bnds   = '';
    if (strpos($brands, '_') !== false) {
      $chop = explode('_', $brands);
      for ($i=0; $i<count($chop); $i++) {
        $bnds .= ($i > 0 ? ' OR (' : 'AND (') . '`brand` = \'' . (int) $chop[$i] . '\')' . mc_defineNewline();
      }
    } else {
      $bnds = 'AND `brand` = \'' . (int) $brands . '\'';
    }
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
             `" . DB_PREFIX . "products`.`id` AS `pid`
             FROM `" . DB_PREFIX . "products`
             LEFT JOIN `" . DB_PREFIX . "prod_category`
             ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
             LEFT JOIN `" . DB_PREFIX . "prod_brand`
             ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_brand`.`product`
             WHERE `pEnable`  = 'yes'
             " . ($bnds ? rtrim($bnds) : '') . "
             GROUP BY `" . DB_PREFIX . "products`.`id`
             ORDER BY `pid` DESC
             LIMIT " . $this->settings->rssFeedLimit . "
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($RSS = mysqli_fetch_object($query)) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $RSS->pid . '/' . ($RSS->rwslug ? $RSS->rwslug : $this->rwr->title($RSS->pName)),
        'pd=' . $RSS->pid
      ));
      $string .= rssFeed::addItem($RSS->pName, $url, ($RSS->rssBuildDate ? $RSS->rssBuildDate : $build_date), mc_txtParsingEngine($RSS->pDescription), $RSS->pid, ($RSS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? mc_currencyFormat($RSS->pOffer) . ' <del>' . mc_currencyFormat($RSS->pPrice) . '</del>' : mc_currencyFormat($RSS->pPrice, '', true)));
    }
    return trim($string);
  }

  // Get latest products..
  public function getLatestProducts($build_date, $cat) {
    $string = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
             `" . DB_PREFIX . "products`.`id` AS `pid`
             FROM `" . DB_PREFIX . "products`
             LEFT JOIN `" . DB_PREFIX . "prod_category`
             ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
             WHERE `pEnable`                 = 'yes'
             " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
             " . ($cat > 0 ? 'AND `category`     = \'' . $cat . '\'' : '') . "
             GROUP BY `" . DB_PREFIX . "products`.`id`
             ORDER BY `pid` DESC
             LIMIT " . $this->settings->rssFeedLimit . "
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($RSS = mysqli_fetch_object($query)) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $RSS->pid . '/' . ($RSS->rwslug ? $RSS->rwslug : $this->rwr->title($RSS->pName)),
        'pd=' . $RSS->pid
      ));
      $string .= rssFeed::addItem($RSS->pName, $url, ($RSS->rssBuildDate ? $RSS->rssBuildDate : $build_date), mc_txtParsingEngine($RSS->pDescription), $RSS->pid, ($RSS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? mc_currencyFormat($RSS->pOffer) . ' <del>' . mc_currencyFormat($RSS->pPrice) . '</del>' : mc_currencyFormat($RSS->pPrice, '', true)));
    }
    return trim($string);
  }

  // Get latest category products..
  public function getLatestCatProducts($build_date, $cat) {
    $string = '';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`pDateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `a_date`,
             `" . DB_PREFIX . "products`.`id` AS `pid`
             FROM `" . DB_PREFIX . "products`
             LEFT JOIN `" . DB_PREFIX . "prod_category`
             ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
             WHERE `category`                = '{$cat}'
             AND `pEnable`                   = 'yes'
             " . ($this->settings->showOutofStock == 'no' ? 'AND `pStock` > 0' : '') . "
             GROUP BY `" . DB_PREFIX . "products`.`id`
             ORDER BY `pid` DESC
             LIMIT " . $this->settings->rssFeedLimit . "
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($RSS = mysqli_fetch_object($query)) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['prd'] . '/' . $RSS->pid . '/' . ($RSS->rwslug ? $RSS->rwslug : $this->rwr->title($RSS->pName)),
        'pd=' . $RSS->pid
      ));
      $string .= rssFeed::addItem($RSS->pName, $url, ($RSS->rssBuildDate ? $RSS->rssBuildDate : $build_date), mc_txtParsingEngine($RSS->pDescription), $RSS->pid, ($RSS->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? mc_currencyFormat($RSS->pOffer) . ' <del>' . mc_currencyFormat($RSS->pPrice) . '</del>' : mc_currencyFormat($RSS->pPrice, '', true)));
    }
    return trim($string);
  }

  // Starts RSS Channel..
  public function openChannel() {
    $xml   = '<rss version="' . $this->rssVersion . '" xmlns:atom="http://www.w3.org/2005/Atom">' . mc_defineNewline() . '<channel>';
    $xml2  = '<?xml version="' . $this->xmlVersion . '" encoding="' . $this->encoding . '" ?>' . mc_defineNewline();
    return trim($xml2 . $xml);
  }

  // Loads data into Feed..
  public function addItem($title = '', $link = '', $date = '', $desc = '', $id = '', $price = '') {
    global $public_product18;
    return mc_defineNewline() . '<item>
     <title>' . rssFeed::render($title) . '</title>
     <link>' . $link . '</link>
     <pubDate>' . $date . '</pubDate>
     <guid>' . $link . '</guid>
     <description><![CDATA[' . rssFeed::itemImage($id) . rssFeed::removeTags($desc) . '<br><br><b>' . str_replace('{price}', $price, $public_product18) . '</b>]]></description>
    </item>';
  }

  // Get image..
  public function itemImage($id) {
    return '<img src="' . $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/' . rssFeed::loadProductImage($id) . '" width="150" title="" alt=""><br><br>';
  }

  // Load product image..
  public function loadProductImage($id) {
    $image = 'no-product-image.gif';
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "pictures`
             WHERE `product_id` = '{$id}'
             ORDER BY `displayImg`,`id`
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    $IMG = mysqli_fetch_object($query);
    return (isset($IMG->thumb_path) ? mc_imageDisplayPath($IMG->folder) . '/' . $IMG->thumb_path : $image);
  }

  // Loads Feed Info..
  public function feedInfo($title = '', $link = '', $date = '', $desc = '', $site = '') {
    return mc_defineNewline() . '<title>' . rssFeed::render($title) . '</title>
    <link>' . $link . '</link>
    <description>' . rssFeed::render($desc) . '</description>
    <lastBuildDate>' . $date . '</lastBuildDate>
    <language>' . $this->language . '</language>
    <generator>' . rssFeed::render($site) . '</generator>
    <atom:link href="' . $this->thisFeedUrl . '" rel="self" type="application/rss+xml" />';
  }

  // Closes RSS Channel..
  public function closeChannel() {
    return mc_defineNewline() . '</channel></rss>';
  }

  // Renders Feed Data..
  public function render($data, $clean_tags = false) {
    if ($clean_tags) {
      $data = rssFeed::removeTags($data);
    }
    return '<![CDATA[' . mc_cleanData($data) . ']]>';
  }

  // Removes certain tags from feed..
  public function removeTags($data) {
    // Clean foreign characters..
    return $data;
  }

}

?>