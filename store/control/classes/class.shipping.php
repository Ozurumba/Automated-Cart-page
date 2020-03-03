<?php

class mcShipping {

  public $settings;
  public $products;
  public $cart;
  public $rwr;
  public $checkout;

  public function shipThreshold($cnty) {
    $str = 'Calculating free shipping threshold.' . mc_defineNewline();
    $t   = $this->cart->cartTotal();
    if ($this->settings->freeShipThreshold > 0 && $t > 0) {
      $str .= 'Free shipping threshold is: ' . $this->settings->freeShipThreshold . mc_defineNewline();
      $str .= 'Goods Total is: ' . $t . mc_defineNewline();
      $str .= 'Country Free Ship Settings: ' . $cnty;
      if ($this->settings->freeShipThreshold <= $t && $cnty == 'yes') {
        $str .= 'Free Shipping Threshold WILL be applied';
        $_SESSION['shipping-total'] = '0.00';
        return 'yes';
      }
      $str .= 'Free Shipping Threshold WILL NOT be applied';
      return 'no';
    }
    $str .= 'Free Shipping Threshold Ignored. Not set or tangible goods total is 0.00';
    mcShipping::log($str);
    return 'no';
  }

  public function setShipping($showCount = false, $pickup = 'no', $freeship = 'no') {
    global $msg_javascript120, $msg_script5, $msg_script6, $msg_javascript214, $msg_javascript215, $public_checkout95, $msg_javascript275, $msg_javascript276, $msg_javascript306, $msg_javascript307, $public_checkout94, $public_checkout138;
    mcShipping::log('Starting shipping check on ' . date('j F Y') . ' @ ' . date('H:i:A'));
    $string        = '';
    $count         = 0;
    $sWrap         = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping-wrapper.htm');
    $weight        = number_format($this->cart->cartWeight(), 2, '.', '');
    $sweight       = number_format($this->cart->cartWeightForShipping(), 2, '.', '');
    $prodShipCount = $this->cart->cartCountShippingItems();
    mcShipping::log('Weight is: ' . $weight);
    mcShipping::log('Shipping Weight is: ' . $sweight);
    mcShipping::log('Count of products to be shipped: ' . $prodShipCount);
    $only          = '';
    $cod           = 'yes';
    $shipCount     = 0;
    // Global switch kills all shipping..
    if (defined('KILL_CHECKOUT_SHIPPING')) {
      mcShipping::log('Global switch is set, NO shipping will be applied');
      if ($showCount) {
        return array(
          $showCount,
          'noshipping'
        );
      }
      return '';
    }
    // Switch off pickup if local not set but pickup is..
    if ($pickup == 'no') {
      mcShipping::log('Pickup is no, so enable pickup has been switched off');
      $this->settings->enablePickUp = 'no';
    }
    // Find shipping rate..
    if (mcShipping::shipThreshold($freeship) == 'no' && $prodShipCount > 0) {
      mcShipping::log('No free shipping threshold and products are to be shipped, so begin shipping rate check..');
      $ZONE        = mc_getTableData('zone_areas', 'id', (int) $_GET['a']);
      $getShipping = mcShipping::shipCalc($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
      if ($getShipping[0] != 'no-shipping-rates-set') {
        mcShipping::log('Shipping rate is loaded, info is: ' . print_r($getShipping, true));
        $string      = $getShipping[0];
        $only        = $getShipping[1];
        $cod         = $getShipping[2];
        $count       = $getShipping[3];
        $shipCount   = $getShipping[4];
      }
    } else {
      mcShipping::log('Either free shipping threshold has not been met, no products require shipping OR delivery country is restricted from free shipping threshold.');
      if (!$showCount) {
        $string = str_replace(array(
          '{text}',
          '{delivery_details}',
          '{price}',
          '{id}',
          '{checked}',
          '{cod}',
          '{theme_folder}'
        ), array(
          $msg_javascript306,
          $msg_javascript307,
          $this->cart->formatSystemCurrency('0.00'),
          'free-ship',
          (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == 'free-ship' ? ' checked="checked"' : ($this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
          'yes',
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping.htm'));
      }
      $only = 'free-ship';
      $cod  = 'yes';
      ++$count;
    }
    // Is free pickup enabled and set for selected country..
    if ($this->settings->enablePickUp == 'yes' && $shipCount > 0) {
      mcShipping::log('Free pickup is enabled for this country');
      if (!$showCount) {
        $string .= str_replace(array(
          '{text}',
          '{delivery_details}',
          '{price}',
          '{id}',
          '{checked}',
          '{cod}',
          '{theme_folder}'
        ), array(
          $msg_javascript214,
          $msg_javascript215,
          $this->cart->formatSystemCurrency('0.00'),
          'pickup',
          (isset($_SESSION['is-pick-up']) && $_SESSION['is-pick-up'] == 'yes' ? ' checked="checked"' : ($count == 0 ? ' checked="checked"' : '')),
          'yes',
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping.htm'));
      }
      if ($pickup == 'yes') {
        $only = 'pickup';
        $cod  = 'yes';
        ++$count;
      }
    } else {
      // If there are no shipping rules, well its free I guess..
      mcShipping::log('There are NO shipping rules so shipping is FREE');
      if ($shipCount == 0) {
        if (!$showCount) {
          $string = str_replace(array(
            '{text}',
            '{delivery_details}',
            '{price}',
            '{id}',
            '{checked}',
            '{cod}',
            '{theme_folder}'
          ), array(
            $msg_javascript275,
            $msg_javascript276,
            $this->cart->formatSystemCurrency('0.00'),
            'noshipping',
            ' checked="checked"',
            'yes',
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping.htm'));
        }
        ++$count;
        if ($showCount) {
          return array(
            $showCount,
            'noshipping',
            'yes'
          );
        }
        return ($string ? str_replace(array(
          '{text}',
          '{shipping_data}'
        ), array(
          'XX',
          trim($string)
        ), $sWrap) : '');
      }
    }
    if ($showCount) {
      return array(
        $count,
        $only,
        $cod
      );
    }
    return ($string ? str_replace(array(
      '{text}',
      '{shipping_data}'
    ), array(
      'XX',
      trim($string)
    ), $sWrap) : '');
  }

  public function shipCalc($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    $arr = ($this->settings->shipopts ? unserialize($this->settings->shipopts) : array('flatrate' => 'yes','itemrate' => 'yes','percent' => 'yes','qtyrates' => 'yes','rates' => 'yes'));
    $sp  = array('no-shipping-rates-set','','','','');
    if (!empty($arr)) {
      foreach ($arr AS $sOpts => $sOptsV) {
        if ($sOptsV == 'yes') {
          mcShipping::log($sOpts . ' is enabled..');
          switch($sOpts) {
            case 'flatrate':
              $sp = mcShipping::flatRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
              if (substr($sp[1],0,5) == 'flat-') {
                mcShipping::log('Flat rate has been found. Details: ' . print_r($sp, true));
                break 2;
              } else {
                mcShipping::log('Nothing found for flat rate shipping. Returned value is: ' . print_r($sp, true));
              }
              break;
            case 'itemrate':
              $sp = mcShipping::itemRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
              if (substr($sp[1],0,5) == 'pert-') {
                mcShipping::log('Per item rate has been found. Details: ' . print_r($sp, true));
                break 2;
              } else {
                mcShipping::log('Nothing found for per item rate shipping. Returned value is: ' . print_r($sp, true));
              }
              break;
            case 'percent':
              $sp = mcShipping::percentRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
              if (substr($sp[1],0,5) == 'perc-') {
                mcShipping::log('Percentage rate has been found. Details: ' . print_r($sp, true));
                break 2;
              } else {
                mcShipping::log('Nothing found for percentage rate shipping. Returned value is: ' . print_r($sp, true));
              }
              break;
            case 'qtyrates':
              $sp = mcShipping::qtyRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
              if (substr($sp[1],0,5) == 'qtyr-') {
                mcShipping::log('Quantity rate has been found. Details: ' . print_r($sp, true));
                break 2;
              } else {
                mcShipping::log('Nothing found for qty rate shipping. Returned value is: ' . print_r($sp, true));
              }
              break;
            case 'rates':
              $sp = mcShipping::weightRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount);
              if ($sp[1] != 'fail' && in_array($sp[2], array('yes','no'))) {
                mcShipping::log('Weight based rate has been found. Details: ' . print_r($sp, true));
                break 2;
              } else {
                mcShipping::log('Nothing found for weight based rates shipping. Returned value is: ' . print_r($sp, true));
              }
              break;
          }
        } else {
          mcShipping::log($sOpts . ' is NOT enabled..');
        }
      }
    }
    if ($sp[0] == 'no-shipping-rates-set') {
      mcShipping::log('No shipping rates have been found');
    }
    return $sp;
  }

  public function flatRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    global $public_checkout94;
    $ar = array('','fail','fail',0,0);
    mcShipping::log('Searching for flat rate for zone: ' . $ZONE->inZone);
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate` FROM `" . DB_PREFIX . "flat`
         WHERE `inZone` = '{$ZONE->inZone}'
         AND `enabled`  = 'yes'
         LIMIT 1
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $SR = mysqli_fetch_object($q);
    if (isset($SR->id)) {
      ++$count;
      ++$shipCount;
      if (!$showCount) {
        $ar[0] .= str_replace(array(
          '{text}',
          '{price}',
          '{id}',
          '{checked}',
          '{theme_folder}'
        ), array(
          $public_checkout94,
          $this->cart->formatSystemCurrency(mc_formatPrice($SR->rate)),
          $SR->id,
          (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == $SR->id ? ' checked="checked"' : (mysqli_num_rows($q) == 1 && $this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping-flat.htm'));
      }
      $ar[1] = 'flat-' . $SR->id;
      $ar[2] = 'yes';
    }
    $ar[3] = $count;
    $ar[4] = $shipCount;
    return $ar;
  }

  public function itemRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    global $public_checkout138;
    $ar = array('','fail','fail',0,0);
    mcShipping::log('Searching for per item rate for zone: ' . $ZONE->inZone);
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`item`,`rate` FROM `" . DB_PREFIX . "per`
         WHERE `inZone` = '{$ZONE->inZone}'
         AND `enabled`  = 'yes'
         LIMIT 1
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $SR = mysqli_fetch_object($q);
    if (isset($SR->id)) {
      // If one, then only apply first rate..
      // If more, apply additional rate to each product..
      if ($prodShipCount > 1) {
        $perItems     = mc_formatPrice($SR->item * ($prodShipCount - 1));
        $perItemTotal = mc_formatPrice($SR->rate + $perItems);
      } else {
        $perItemTotal = mc_formatPrice($SR->rate);
      }
      ++$count;
      ++$shipCount;
      if (!$showCount) {
        $ar[0] .= str_replace(array(
          '{text}',
          '{price}',
          '{id}',
          '{checked}',
          '{theme_folder}'
        ), array(
          str_replace(array(
            '{first}',
            '{item}'
          ), array(
            $this->cart->formatSystemCurrency($SR->rate),
            $this->cart->formatSystemCurrency($SR->item)
          ), $public_checkout138),
          $this->cart->formatSystemCurrency($perItemTotal),
          $SR->id,
          (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == $SR->id ? ' checked="checked"' : (mysqli_num_rows($q) == 1 && $this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping-per-item.htm'));
      }
      $ar[1] = 'pert-' . $SR->id;
      $ar[2] = 'yes';
    }
    $ar[3] = $count;
    $ar[4] = $shipCount;
    return $ar;
  }

  public function percentRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    global $public_checkout95;
    $ar = array('','fail','fail',0,0);
    $cartTotal     = $this->cart->cartTotal();
    $cartPercTotal = $this->cart->cartTotalPercRates();
    mcShipping::log('Searching for percent rate for zone ' . $ZONE->inZone . ' based on total of: ' . $cartPercTotal);
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`percentage` FROM `" . DB_PREFIX . "percent`
         WHERE `inZone`    = '{$ZONE->inZone}'
         AND `priceFrom`  <= $cartPercTotal
         AND `priceTo`    >= $cartPercTotal
         AND `enabled`     = 'yes'
         LIMIT 1
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $SR = mysqli_fetch_object($q);
    if (isset($SR->id) && $cartPercTotal > 0) {
      ++$count;
      ++$shipCount;
      if (!$showCount) {
        $perc  = number_format(($cartPercTotal * $SR->percentage) / 100, 2, '.', '');
        $ar[0] .= str_replace(array(
          '{text}',
          '{price}',
          '{id}',
          '{checked}',
          '{theme_folder}'
        ), array(
          str_replace('{percent}', $SR->percentage, $public_checkout95),
          $this->cart->formatSystemCurrency(mc_formatPrice($perc)),
          $SR->id,
          (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == $SR->id ? ' checked="checked"' : (mysqli_num_rows($q) == 1 && $this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping-percent.htm'));
      }
      $ar[1] = 'perc-' . $SR->id;
      $ar[2] = 'yes';
    }
    $ar[3] = $count;
    $ar[4] = $shipCount;
    return $ar;
  }

  public function weightRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    global $msg_javascript275, $msg_javascript276, $msg_javascript214, $msg_javascript215, $msg_script5, $msg_script6, $msg_script9, $msg_javascript120;
    $ar = array('','fail','fail',0,0);
    mcShipping::log('Searching for weight based rate for zone ' . $ZONE->inZone . ' based on weight of: ' . $sweight);
    if ($sweight > 0) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `rCost`,`sName`,`sEstimation`,`sSignature`,`enableCOD`,
           `" . DB_PREFIX . "services`.`id` AS `sid`,
           `" . DB_PREFIX . "rates`.`id` AS `rid`
           FROM `" . DB_PREFIX . "services`
           LEFT JOIN `" . DB_PREFIX . "rates`
           ON `" . DB_PREFIX . "services`.`id`  = `" . DB_PREFIX . "rates`.`rService`
           WHERE `rWeightFrom` <= $sweight
           AND `rWeightTo`     >= $sweight
           AND `inZone`         = '{$ZONE->inZone}'
           ORDER BY `sName`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($SR = mysqli_fetch_object($q)) {
        ++$count;
        ++$shipCount;
        // Tare weight..
        $tareCost = '0.00';
        $tare     = $this->cart->getTareWeight($sweight, $SR->sid);
        if (isset($tare[0]) && $tare[0] == 'yes') {
          switch(substr($tare[1], -1)) {
            case '%':
              $calc     = substr($tare[1], 0, -1);
              $tareCost = number_format(($SR->rCost * $calc) / 100, 2, '.', '');
              break;
            default:
              $tareCost = mc_formatPrice($tare[1]);
              break;
          }
        }
        if (!$showCount) {
          $ar[0] .= str_replace(array(
            '{text}',
            '{delivery_details}',
            '{price}',
            '{id}',
            '{checked}',
            '{cod}',
            '{theme_folder}'
          ), array(
            mc_cleanData($SR->sName),
            str_replace(array(
              '{time}',
              '{sig}'
            ), array(
              $SR->sEstimation,
              ($SR->sSignature == 'yes' ? $msg_script5 : $msg_script6)
            ), $msg_javascript120),
            $this->cart->formatSystemCurrency(mc_formatPrice($SR->rCost + $tareCost)),
            $SR->rid,
            (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == $SR->rid ? ' checked="checked"' : (mysqli_num_rows($q) == 1 && $this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
            $SR->enableCOD,
            THEME_FOLDER
          ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping.htm'));
        }
        $ar[1] = $SR->rid;
        $ar[2] = $SR->enableCOD;
      }
    }
    $ar[3] = $count;
    $ar[4] = $shipCount;
    return $ar;
  }

  public function qtyRate($prodShipCount, $showCount, $ZONE, $count, $sweight, $shipCount) {
    global $public_checkout95, $mc_checkout;
    $ar = array('','fail','fail',0,0);
    $cartPercTotal = $this->cart->cartTotalPercRates();
    mcShipping::log('Searching for quantity based rate for zone ' . $ZONE->inZone . ' based on shipping count of: ' . $prodShipCount);
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`rate` FROM `" . DB_PREFIX . "qtyrates`
         WHERE `inZone`   = '{$ZONE->inZone}'
         AND `qtyFrom`   <= $prodShipCount
         AND `qtyTo`     >= $prodShipCount
         AND `enabled`    = 'yes'
         LIMIT 1
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    $SR = mysqli_fetch_object($q);
    if (isset($SR->id)) {
      ++$count;
      ++$shipCount;
      if (!$showCount) {
        switch(substr($SR->rate,-1)) {
          case '%':
            $val = number_format(($cartPercTotal * substr($SR->rate,0,-1)) / 100, 2, '.', '');
            $txt = str_replace('{percent}', substr($SR->rate,0,-1), $mc_checkout[23]);
            break;
          default:
            $val = $SR->rate;
            $txt = $mc_checkout[22];
            break;
        }
        $ar[0] .= str_replace(array(
          '{text}',
          '{price}',
          '{id}',
          '{checked}',
          '{theme_folder}'
        ), array(
          $txt,
          $this->cart->formatSystemCurrency(mc_formatPrice($val)),
          $SR->id,
          (isset($_SESSION['shipping-rate']) && $_SESSION['shipping-rate'] == $SR->id ? ' checked="checked"' : (mysqli_num_rows($q) == 1 && $this->settings->enablePickUp == 'no' ? ' checked="checked"' : '')),
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-shipping-quantity.htm'));
      }
      $ar[1] = 'qtyr-' . $SR->id;
      $ar[2] = 'yes';
    }
    $ar[3] = $count;
    $ar[4] = $shipCount;
    return $ar;
  }

  public function setRegions($country = '') {
    global $public_checkout10;
    $string = '';
    if ($country == '' && $this->settings->shipCountry > 0) {
      $CT = mc_getTableData('countries', 'id', $this->settings->shipCountry);
      if (isset($CT->id)) {
        $country = $CT->id;
      }
    }
    if ($country) {
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zones`
               WHERE `zCountry` = '{$country}'
               ORDER BY `" . CHECKOUT_ZONE_ORDER_BY . "`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($query) > 0 && !isset($CT->id)) {
        $string = '<option value="0">- - - - - - - -</option>' . mc_defineNewline();
        while ($ZONE = mysqli_fetch_object($query)) {
          $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "zone_areas`
                WHERE `inZone`  = '{$ZONE->id}'
                AND `zCountry`  = '{$country}'
                ORDER BY `" . CHECKOUT_ZONE_AREA_ORDER_BY . "`
                ") or die(mc_MySQLError(__LINE__, __FILE__));
          if (mysqli_num_rows($q2) > 0) {
            $string .= '<optgroup label="' . $ZONE->zName . '">' . mc_defineNewline();
            while ($AREA = mysqli_fetch_object($q2)) {
              $string .= '<option value="' . $AREA->id . '"' . (isset($_SESSION['shipto'][0]) && $_SESSION['shipto'][0] == $AREA->id ? ' selected="selected"' : '') . '>' . $AREA->areaName . '</option>' . mc_defineNewline();
            }
            $string .= '</optgroup>' . mc_defineNewline();
          }
        }
      }
    }
    // Show message if no areas..
    return trim($string);
  }

  public function log($t) {
    if (SHIP_DEBUG_LOG && is_dir(GLOBAL_PATH . 'logs') && is_writeable(GLOBAL_PATH . 'logs') && function_exists('file_put_contents')) {
      $file = GLOBAL_PATH . 'logs/shipping-debug-log.log';
      file_put_contents($file, $t . mc_defineNewline() . '- - - - - - - - - - - - - - - - - - - - - - - - -' . mc_defineNewline(), FILE_APPEND);
    }
  }

}

?>