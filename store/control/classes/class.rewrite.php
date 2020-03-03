<?php

class mcRewrite {

  public $settings;
  public $config = array(
    // Regex for auto title where slug isn`t set / available..
    'exp' => '`[^\w_-]`',

    // Default slug text..
    // Change value if required. DO NOT change key (value on left), have spaces OR forward slashes or else the system may fail.
    // If enabled, clear cache files after making changes here..
    'slugs' => array(
      'sof' => 'special-offers',
      'lpr' => 'latest-products',
      'ssc' => 'saved-searches',
      'wst' => 'wishlist',
      'his' => 'history',
      'prd' => 'product',
      'vpd' => 'product-desc',
      'ppi' => 'pay-info',
      'vdr' => 'view-order',
      'cat' => 'category',
      'brs' => 'brands',
      'rsc' => 'feed-cat',
      'rsb' => 'feed-brand',
      'rss' => 'feed-special',
      'rsl' => 'feed-latest',
      'pdl' => 'pdl',
      'zip' => 'zdl',
      'hlp' => 'help',
      'npg' => 'np',
      'edp' => 'edit-pp',
      'gft' => 'gift',
      'sch' => 'search',
      'wls' => 'wish',
      'tag' => 'tags',
      'tac' => 'terms',
      'ldw' => 'wishp'
    )
  );

  public function parser() {
    switch($this->settings->en_modr) {
      case 'yes':
        // These can be changed, buy you also need to find where they are called and change the text there too..
        // A good search utility is all you need. For example, for 'about-us' search for the following:
        // url(array('about-us'))
        $system = array(
          'about-us','advanced-search','create','logout','newpass','payment','checkpay','process','sitemap',
          'login','account','history','profile','close','order-invalid','status-err','free-restr',
          'acc-closed','gift','out-of-stock','no-search','code-error','dl-code-error','dl-expired','acc-exists',
          'dl-lock','opt-out','clearcart','code-help','gate1','gate2','no-category-assigned','no-wish-country'
        );
        if (isset($_GET['_mc_'])) {
          // Check .htaccess file exists..
          if (!file_exists(PATH . '.htaccess')) {
            die('
             [<b>ERROR</b>] Search engine friendly urls are enabled but the .htaccess file is <b>not</b> present.<br><br>
             Please rename "<b>htaccess_COPY.txt</b>" to "<b>.htaccess</b>" and reload page.
            ');
          }
          if (in_array($_GET['_mc_'], $system)) {
            switch($_GET['_mc_']) {
              case 'clearcart':
                $_GET['clearcart'] = 'yes';
                break;
              default:
                $_GET['p'] = $_GET['_mc_'];
                break;
            }
          } else {
            $chop = explode('/', $_GET['_mc_']);
            if (isset($chop[0])) {
              // Special offers..
              if ($chop[0] == $this->config['slugs']['sof']) {
                $_GET['p'] = 'special-offers';
                if (isset($chop[1])) {
                  $_GET['next'] = (int) $chop[1];
                }
              }
              // Latest products..
              else if ($chop[0] == $this->config['slugs']['lpr']) {
                $_GET['p'] = 'latest-products';
                if (isset($chop[1])) {
                  $_GET['next'] = (int) $chop[1];
                }
              }
              // Saved searches..
              else if ($chop[0] == $this->config['slugs']['ssc']) {
                $_GET['p'] = 'saved-searches';
                if (isset($chop[1])) {
                  $_GET['next'] = (int) $chop[1];
                }
              }
              // Wish list..
              else if ($chop[0] == $this->config['slugs']['wst']) {
                $_GET['p'] = 'wishlist';
                if (isset($chop[1])) {
                  $_GET['next'] = (int) $chop[1];
                }
              }
              // Load wish product info screen..
              else if ($chop[0] == $this->config['slugs']['ldw'] && isset($chop[1])) {
                $_GET['p'] = 'product';
                $_GET['loadw'] = $chop[1];
              }
              // History..
              else if ($chop[0] == $this->config['slugs']['his']) {
                $_GET['p'] = 'history';
                if (isset($chop[1])) {
                  $_GET['next'] = (int) $chop[1];
                }
              }
              // View product..
              else if ($chop[0] == $this->config['slugs']['prd'] && isset($chop[1])) {
                $_GET['pd'] = (int) $chop[1];
              }
              // View product description..
              else if ($chop[0] == $this->config['slugs']['vpd'] && isset($chop[1])) {
                $_GET['dsc'] = (int) $chop[1];
              }
              // View payment info..
              else if ($chop[0] == $this->config['slugs']['ppi'] && isset($chop[1]) && isset($chop[2]) && in_array($chop[2],array('cod','phone','bank','cheque','account'))) {
                $_GET['pinfo'] = $chop[1];
                $_GET['pm'] = $chop[2];
              }
              // View order..
              else if ($chop[0] == $this->config['slugs']['vdr'] && isset($chop[1])) {
                $_GET['vodr'] = (int) $chop[1];
              }
              // View category..
              else if ($chop[0] == $this->config['slugs']['cat'] && isset($chop[1])) {
                $_GET['c'] = $chop[1];
                if (isset($chop[2])) {
                  $_GET['next'] = (int) $chop[2];
                }
              }
              // View brands..
              else if ($chop[0] == $this->config['slugs']['brs'] && isset($chop[1])) {
                $_GET['pbnd'] = (int) $chop[1];
                if (isset($chop[2])) {
                  $_GET['next'] = (int) $chop[2];
                }
              }
              // View category feed..
              else if ($chop[0] == $this->config['slugs']['rsc'] && isset($chop[1])) {
                $_GET['crss'] = (int) $chop[1];
              }
              // View brand feed..
              else if ($chop[0] == $this->config['slugs']['rsb'] && isset($chop[1])) {
                $_GET['brss'] = (int) $chop[1];
              }
              // View special offer feed..
              else if ($chop[0] == $this->config['slugs']['rss']) {
                $_GET['rss'] = 'special' . (isset($chop[2]) ? '-' . (int) $chop[2] : '');
              }
              // View latest products feed..
              else if ($chop[0] == $this->config['slugs']['rsl']) {
                $_GET['rss'] = 'latest' . (isset($chop[2]) ? '-' . (int) $chop[2] : '');
              }
              // Product download..
              else if ($chop[0] == $this->config['slugs']['pdl'] && isset($chop[1])) {
                $_GET['pdl'] = preg_replace('/[^0-9a-zA-Z]/', '', $chop[1]);
              }
              // Zip download..
              else if ($chop[0] == $this->config['slugs']['zip'] && isset($chop[1])) {
                $_GET['zip'] = preg_replace('/[^0-9a-zA-Z]/', '', $chop[1]);
              }
              // Help file..
              else if ($chop[0] == $this->config['slugs']['hlp'] && isset($chop[1])) {
                $_GET['help'] = (in_array($chop[1], array('ins')) ? $chop[1] : $chop[1]);
              }
              // Terms..
              else if ($chop[0] == $this->config['slugs']['tac']) {
                $_GET['terms'] = 'yes';
              }
              // New page..
              else if ($chop[0] == $this->config['slugs']['npg'] && isset($chop[1])) {
                $_GET['np'] = (int) $chop[1];
              }
              //Edit personalisation..
              else if ($chop[0] == $this->config['slugs']['edp'] && isset($chop[1])) {
                $_GET['ppCE'] = $chop[1];
              }
              // Gift certs..
              else if ($chop[0] == $this->config['slugs']['gft'] && isset($chop[1])) {
                $_GET['gift'] = $chop[1];
              }
              // Search..
              else if ($chop[0] == $this->config['slugs']['sch'] && isset($chop[1])) {
                $_GET['sk'] = preg_replace('/[^0-9a-zA-Z-]/', '', $chop[1]);
                if (isset($chop[2])) {
                  $_GET['next'] = (int) $chop[2];
                }
              }
              // Public wish list..
              else if ($chop[0] == $this->config['slugs']['wls'] && isset($chop[1])) {
                $_GET['p'] = 'wish';
                if (isset($chop[1])) {
                  $_GET['wls'] = preg_replace('/[^0-9a-zA-Z-]/', '', $chop[1]);
                }
                if (isset($chop[2])) {
                  $_GET['next'] = (int) $chop[2];
                }
              }
              // Tags..
              else if ($chop[0] == $this->config['slugs']['tag'] && isset($chop[1])) {
                $_GET['q'] = $chop[1];
              }
              // Nothing..
              else {
                global $mc_global, $MCSOCIAL, $SETTINGS, $charset, $slidePanel, $leftBoxDisplay, $errorPages;
                if (!class_exists('Savant3_Filter')) {
                  include(PATH . 'control/engine/Savant3.php');
                }
                include(PATH . 'control/system/headers/404.php');
                exit;
              }
            } else {
              global $mc_global, $MCSOCIAL, $SETTINGS, $charset, $slidePanel, $leftBoxDisplay, $errorPages;
              if (!class_exists('Savant3_Filter')) {
                include(PATH . 'control/engine/Savant3.php');
              }
              include(PATH . 'control/system/headers/404.php');
              exit;
            }
          }
        }
        break;
      case 'no':
        // Var should never exist if rewrite rules are off..
        if (isset($_GET['_mc_'])) {
          header("Location: " . $this->settings->ifolder);
          exit;
        }
        break;
    }
  }

  public function url($data = array()) {
    if ($data[0] == 'base_href') {
      return $this->settings->ifolder;
    } else {
      switch($this->settings->en_modr) {
        case 'yes':
          return $this->settings->ifolder . '/' . mc_cleanData($data[0]);
          break;
        case 'no':
          return $this->settings->ifolder . '/' . (isset($data[1]) ? '?' . $data[1] : '?p=' . $data[0]);
          break;
      }
    }
  }

  public function title($title) {
    // Foreign character ascii conversions..
    // http://www.asciitable.com/
    $chars = array(
      chr(195) . chr(128) => 'A',
      chr(195) . chr(129) => 'A',
      chr(195) . chr(130) => 'A',
      chr(195) . chr(131) => 'A',
      chr(195) . chr(132) => 'A',
      chr(195) . chr(133) => 'A',
      chr(195) . chr(135) => 'C',
      chr(195) . chr(136) => 'E',
      chr(195) . chr(137) => 'E',
      chr(195) . chr(138) => 'E',
      chr(195) . chr(139) => 'E',
      chr(195) . chr(140) => 'I',
      chr(195) . chr(141) => 'I',
      chr(195) . chr(142) => 'I',
      chr(195) . chr(143) => 'I',
      chr(195) . chr(145) => 'N',
      chr(195) . chr(146) => 'O',
      chr(195) . chr(147) => 'O',
      chr(195) . chr(148) => 'O',
      chr(195) . chr(149) => 'O',
      chr(195) . chr(150) => 'O',
      chr(195) . chr(153) => 'U',
      chr(195) . chr(154) => 'U',
      chr(195) . chr(155) => 'U',
      chr(195) . chr(156) => 'U',
      chr(195) . chr(157) => 'Y',
      chr(195) . chr(159) => 's',
      chr(195) . chr(160) => 'a',
      chr(195) . chr(161) => 'a',
      chr(195) . chr(162) => 'a',
      chr(195) . chr(163) => 'a',
      chr(195) . chr(164) => 'a',
      chr(195) . chr(165) => 'a',
      chr(195) . chr(167) => 'c',
      chr(195) . chr(168) => 'e',
      chr(195) . chr(169) => 'e',
      chr(195) . chr(170) => 'e',
      chr(195) . chr(171) => 'e',
      chr(195) . chr(172) => 'i',
      chr(195) . chr(173) => 'i',
      chr(195) . chr(174) => 'i',
      chr(195) . chr(175) => 'i',
      chr(195) . chr(177) => 'n',
      chr(195) . chr(178) => 'o',
      chr(195) . chr(179) => 'o',
      chr(195) . chr(180) => 'o',
      chr(195) . chr(181) => 'o',
      chr(195) . chr(182) => 'o',
      chr(195) . chr(182) => 'o',
      chr(195) . chr(185) => 'u',
      chr(195) . chr(186) => 'u',
      chr(195) . chr(187) => 'u',
      chr(195) . chr(188) => 'u',
      chr(195) . chr(189) => 'y',
      chr(195) . chr(191) => 'y',
      chr(195) . chr(158) => 'S',
      chr(195) . chr(159) => 's',
      chr(195) . chr(166) => 'G',
      chr(195) . chr(167) => 'g',
      chr(195) . chr(152) => 'I',
      chr(195) . chr(141) => 'i',
      chr(195) . chr(154) => 'U',
      chr(195) . chr(129) => 'u',
      chr(195) . chr(153) => 'O',
      chr(195) . chr(148) => 'o',
      chr(195) . chr(128) => 'C',
      chr(195) . chr(135) => 'c'
    );

    // Convert foreign characters..
    $title = strtr($title, $chars);

    // Strip none alphabetic and none numeric chars..
    return str_replace(array(
      '-----',
      '----',
      '---',
      '--'
    ), '-', strtolower(preg_replace($this->config['exp'], '-', $title)));
  }

}

?>