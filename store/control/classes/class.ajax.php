<?php

class cartOps extends shoppingCart {

  public $settings;
  public $json;
  public $checkout;
  public $products;
  public $mail;
  public $rwr;
  public $shipping;

  public function enquiry($u) {
    $form  = array(
      'blank' => (!isset($_POST['bk']) || $_POST['bk'] ? 'spam' : 'not-spam'),
      'name' => (isset($_POST['nm']) && $_POST['nm'] ? $_POST['nm'] : (isset($u['name']) ? $u['name'] : '')),
      'email' => (isset($_POST['em']) && $_POST['em'] && mswIsValidEmail($_POST['em']) ? $_POST['em'] : (isset($u['email']) ? $u['email'] : '')),
      'comments' => (isset($_POST['msg']) ? $_POST['msg'] : ''),
      'id' => (isset($_GET['penq']) ? (int) $_GET['penq'] : '0')
    );
    if ($form['blank'] == 'not-spam') {
      if ($form['name'] && $form['email'] && $form['comments'] && $form['id'] > 0) {
        $P               = mc_getTableData('products', 'id', $form['id']);
        $form['product'] = (isset($P->id) ? (array) $P : array());
        $arr = array(
          'msg' => 'ok',
          'form' => $form
        );
      } else {
        $arr = array('msg' => 'err');
      }
    } else {
      $arr = array('msg' => 'fail');
    }
    return $arr;
  }

  public function add() {
    return cartOps::addToBasket();
  }

  public function hidden() {
    $json = cartOps::buildBasketTotals(true, true);
    $html = array();
    $fields = array(
      't-tax' => (isset($json['cart-total-t-tax']) ? $json['cart-total-t-tax'] : '0.00'),
      't-tax-rate' => (isset($json['cart-total-t-tax-rate']) ? $json['cart-total-t-tax-rate'] : '0'),
      't-coupon' => (isset($json['cart-total-t-coupon']) ? $json['cart-total-t-coupon'] : '0.00'),
      't-sub' => (isset($json['cart-total-t-sub']) ? $json['cart-total-t-sub'] : '0.00'),
      't-total' => (isset($json['cart-raw-amount']) ? $json['cart-raw-amount'] : '0.00'),
      't-insurance' => (isset($json['cart-total-t-insurance']) ? $json['cart-total-t-insurance'] : '0.00'),
      't-shipping' => (isset($json['cart-total-t-shipping']) ? $json['cart-total-t-shipping'] : '0.00'),
      't-global' => (isset($json['cart-total-t-global']) ? $json['cart-total-t-global'] : '0.00')
    );
    foreach ($fields AS $k => $v) {
      $html[] = '<input type="hidden" name="' . mc_safeHTML($k) . '" value="' . mc_safeHTML($v) . '">';
    }
    $cHid = implode(mc_defineNewline(), $html);
    cartOps::log('Hidden Vars for Checkout: ' . $cHid);
    return $cHid;
  }

  public function insurance() {
    global $public_checkout99,$public_checkout100;
    $html = array();
    $status = (isset($_GET['st']) && in_array($_GET['st'], array(
      'fa fa-check fa-fw',
      'fa fa-times fa-fw mc-red'
    )) ? $_GET['st'] : 'fa fa-check fa-fw');
    switch($status) {
      case 'fa fa-check fa-fw':
        // Make sure we clear mask if set..
        if (isset($_SESSION['insurance-mask'])) {
          unset($_SESSION['insurance-mask']);
        }
        $html = array(
          'class' => 'fa fa-times fa-fw mc-red',
          'html' => $public_checkout100
        );
        break;
      case 'fa fa-times fa-fw mc-red':
        $html = array(
          'class' => 'fa fa-check fa-fw',
          'html' => $public_checkout99
        );
        define('MASK_INSURANCE', 1);
        break;
    }
    return $html;
  }

  public function coupon($cpID) {
    global $MCPROD, $msg_javascript125, $msg_javascript126, $msg_javascript127, $msg_javascript128, $msg_javascript333,
    $public_checkout17, $msg_javascript213, $msg_javascript358, $msg_javascript359, $msg_javascript360, $msg_javascript361,
    $msg_javascript364, $msg_jscript, $msg_jscript2, $public_checkout129, $errorPages;
    if ($cpID) {
      $_SESSION['couponCode'] = array();
      $COUPON                 = $this->products->getCouponDiscount($cpID);
      if (isset($COUPON[0])) {
        switch($COUPON[0]) {
          case 'trade-discount':
          case 'global-discount':
            $msg = ($COUPON[0] == 'global-discount' ? $msg_javascript333 : $errorPages['403']);
            break;
          case 'invalid':
            $msg = $msg_javascript126;
            break;
          case 'redeemed':
            $msg = $public_checkout129;
            break;
          case 'free-cart':
            $msg = $msg_javascript364;
            break;
          case 'min-amount':
            $msg = str_replace('{amount}', cartOps::formatSystemCurrency(mc_formatPrice($COUPON[1])), $msg_javascript127);
            break;
          case 'min-amount-cats':
            $msg = str_replace('{amount}', cartOps::formatSystemCurrency(mc_formatPrice($COUPON[1])), $msg_jscript);
            break;
          case 'low-total':
            $msg = $msg_javascript128;
            break;
          case 'low-total-cats':
            $msg = $msg_jscript2;
            break;
          case 'ok':
            $msg                    = 'ok';
            $_SESSION['couponCode'] = $COUPON;
            break;
        }
      } else {
        $msg = $msg_javascript126;
      }
    } else {
      $msg = $msg_javascript126;
    }
    $discountString = '0.00';
    if (isset($COUPON[1])) {
      switch($COUPON[1]) {
        case 'notax':
          $discountString = $msg_javascript360;
          break;
        case 'freeshipping':
          $discountString = $msg_javascript361;
          break;
        default:
          $discountString = '-' . cartOps::formatSystemCurrency(mc_formatPrice($COUPON[1]));
          break;
      }
    }
    $cpn = array(
      'msg' => $msg,
      'discount' => $discountString,
      'coupon' => (isset($COUPON[0]) ? $COUPON : '')
    );
    cartOps::log('Coupon Array: ' . print_r($cpn, true));
    return $cpn;
  }

  public function regions($country) {
    global $public_checkout101;
    $html = '';
    $CT    = mc_getTableData('countries', 'id', $country);
    if (isset($CT->id)) {
      $A     = mc_rowCount('zone_areas WHERE `zCountry` = \'' . $country . '\'');
      $restr = $this->checkout->checkCountryRestriction($country);
      if (!empty($restr[0])) {
        $resProd = $this->checkout->productList($restr[1]);
        cartOps::log('Checkout Restriction Found: ' . implode('#####', $restr[0]));
        return array(
          0 => 'cty-restr',
          1 => str_replace(array('{country}','{products}'), array($CT->cName, $resProd), $public_checkout101),
          2 => implode('#####', $restr[0])
        );
      } else {
        return array(
          0 => 'ok',
          1 => $this->shipping->setRegions($country)
        );
      }
    }
    return array(
      0 => '',
      1 => ''
    );
  }

  public function setShipping($id) {
    // Check for flat/percentage or per item rates..
    if (substr($id, 0, 4) == 'flat') {
      $type = 'flat';
      $R = mc_getTableData('flat', 'id', mc_digitSan(substr($id, 5)));
      if (isset($R->rate)) {
        $shipCost = $R->rate;
        $shipID   = $R->id;
      }
    } elseif (substr($id, 0, 4) == 'pert') {
      $type = 'pert';
      $R = mc_getTableData('per', 'id', mc_digitSan(substr($id, 5)));
      if (isset($R->item)) {
        // How many items should have shipping applied..
        $prodShipCount = cartOps::cartCountShippingItems();
        // If one, then only apply first rate..
        // If more, apply additional rate to each product..
        if ($prodShipCount > 1) {
          $perItems     = mc_formatPrice($R->item * ($prodShipCount - 1));
          $perItemTotal = mc_formatPrice($R->rate + $perItems);
        } else {
          $perItemTotal = mc_formatPrice($R->rate);
        }
        $shipCost = $perItemTotal;
        $shipID   = $R->id;
      }
    } elseif (substr($id, 0, 4) == 'perc') {
      $type     = 'percent';
      $R        = mc_getTableData('percent', 'id', mc_digitSan(substr($id, 5)));
      if (isset($R->percentage)) {
        $perc     = number_format((cartOps::cartTotalPercRates() * $R->percentage) / 100, 2, '.', '');
        $shipCost = $perc;
        $shipID   = $R->id;
      }
    } elseif (substr($id, 0, 4) == 'qtyr') {
      $type     = 'qtyr';
      $R        = mc_getTableData('qtyrates', 'id', mc_digitSan(substr($id, 5)));
      if (isset($R->rate)) {
        switch(substr($R->rate, -1)) {
          case '%':
            $val = number_format((cartOps::cartTotalPercRates() * $R->rate) / 100, 2, '.', '');
            break;
          default:
            $val = $R->rate;
            break;
        }
        $shipCost = $val;
        $shipID   = $R->id;
      }
    } else {
      $type     = 'weight';
      $R        = mc_getTableData('rates', 'id', mc_digitSan($id));
      if (isset($R->rService)) {
        // Tare weight..
        $tareCost = '0.00';
        $sweight  = number_format(shoppingCart::cartWeightForShipping(), 2, '.', '');
        if ($sweight > 0 && isset($R->rService)) {
          $tare = shoppingCart::getTareWeight($sweight, $R->rService);
          if ($tare[0] == 'yes') {
            switch(substr($tare[1], -1)) {
              case '%':
                $calc     = substr($tare[1], 0, -1);
                $tareCost = number_format(($R->rCost * $calc) / 100, 2, '.', '');
                break;
              default:
                $tareCost = mc_formatPrice($tare[1]);
                break;
            }
          }
        }
        $shipCost = (isset($R->rCost) ? mc_formatPrice($R->rCost + $tareCost) : '0.00');
        $shipID   = (isset($R->id) ? $R->id : '0');
      }
    }
    // Update shipping..
    if ((isset($shipCost) && $shipCost) || in_array($id, array(
      'pickup',
      'noshipping',
      'free-ship'
    ))) {
      $_SESSION['shipping-total'] = mc_formatPrice((in_array($id, array(
        'pickup',
        'noshipping',
        'free-ship'
      )) ? '0.00' : $shipCost));
      $_SESSION['is-pick-up']     = ($id == 'pickup' ? 'yes' : 'no');
      $_SESSION['shipping-rate']  = (isset($shipID) ? $shipID : '0');
      $_SESSION['shipping-type']  = $type;
    }
  }

  public function shipping($country, $area) {
    $html = array('','','');
    $CT   = mc_getTableData('countries', 'id', $country);
    if (isset($CT->id)) {
      $ZN = mc_getTableData('zone_areas', 'id', $area);
      if (isset($ZN->areaName)) {
        $html = array(
          $this->shipping->setShipping(false, $CT->localPickup, $CT->freeship),
          $ZN->areaName,
          $CT->cName
        );
      }
    }
    cartOps::log('Shipping Function: ' . implode(mc_defineNewline(), $html));
    return $html;
  }

  public function update() {
    global $msg_javascript415, $msg_javascript455, $msg_jscript3, $mc_checkout;
    if (isset($_SESSION['shipping-rate'])) {
      unset($_SESSION['shipping-type'], $_SESSION['shipping-rate'], $_SESSION['shipping-total']);
    }
    if (isset($_GET['id']) && isset($_GET['qty']) && isset($_GET['act'])) {
      $_GET['qty'] = ceil($_GET['qty']);
      switch($_GET['act']) {
        case 'plus':
          $_GET['qty'] = (int) ($_GET['qty'] + 1);
          break;
        default:
          $_GET['qty'] = (int) ($_GET['qty'] - 1);
          break;
      }
      $t           = explode('-', $_GET['id']);
      // Get slot for this code number..
      $thisSlot    = cartOps::productSlotPosition($t[2]);
      switch($t[0]) {
        // GIFT CERTIFICATE..
        case 'gift':
          $G      = mc_getTableData('giftcerts', 'id', mc_digitSan($t[1]));
          $price  = mc_formatPrice($G->value);
          $offer  = '0.00';
          $name   = $G->name;
          $slevel = '9999';
          break;
        // PRODUCT..
        default:
          $P     = mc_getTableData('products', 'id', mc_digitSan($t[1]));
          // Trade min/max..
          if (defined('MC_TRADE_MIN')) {
            if (MC_TRADE_MAX > 0) {
              $P->maxPurchaseQty = MC_TRADE_MAX;
            }
            if (MC_TRADE_MIN > 0) {
              $P->minPurchaseQty = MC_TRADE_MIN;
            }
          }
          $price = mc_formatPrice($P->pPrice);
          $offer = '0.00';
          // Special offer..
          // Disable for trade..
          if (defined('MC_TRADE_DISCOUNT')) {
            $P->pOffer = 0;
          }
          if ($P->pOffer > 0) {
            // Is it multi buy?
            if ($P->pMultiBuy > 0) {
              if ($_GET['qty'] >= $P->pMultiBuy) {
                $offer = mc_formatPrice($P->pOffer);
              }
            } else {
              $offer = mc_formatPrice($P->pOffer);
            }
          }
          $name   = $P->pName;
          $slevel = (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0 ? MC_TRADE_STOCK : $P->pStock);
          break;
      }
      $addCost = '0.00';
      $attSlot = 'attr-' . substr($_GET['id'], 8);
      // Min purchase qty..
      if ($t[0] != 'gift') {
        if ($_GET['qty'] > 0 && $P->minPurchaseQty > 0) {
          if ($_GET['qty'] < $P->minPurchaseQty) {
            return array(
              0 => 'err',
              1 => str_replace('{min}', $P->minPurchaseQty, $mc_checkout[19])
            );
          }
        }
        // Check quantity hasn`t exceeded stock..
        if ($_GET['qty'] > $slevel) {
          return array(
            0 => 'err',
            1 => str_replace('{count}', $slevel, $mc_checkout[17])
          );
        }
        // Check max purchase qty..
        if ($_GET['qty'] > 0 && $P->maxPurchaseQty > 0) {
          if ($_GET['qty'] > $P->maxPurchaseQty) {
            return array(
              0 => 'err',
              1 => str_replace('{max}', $P->maxPurchaseQty, $msg_jscript3)
            );
          }
          $P = mc_getTableData('products', 'id', mc_digitSan($t[1]));
          $price = mc_formatPrice($P->pPrice);
          $offer = '0.00';
          // Special offer..
          // Disable for trade..
          if (defined('MC_TRADE_DISCOUNT')) {
            $P->pOffer = 0;
          }
          if ($P->pOffer > 0) {
            // Is it multi buy?
            if ($P->pMultiBuy > 0) {
              if ($_GET['qty'] >= $P->pMultiBuy) {
                $offer = mc_formatPrice($P->pOffer);
              }
            } else {
              $offer = mc_formatPrice($P->pOffer);
            }
          }
          $name   = $P->pName;
          $slevel = (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0 ? MC_TRADE_STOCK : $P->pStock);
        }
      }
      // Personalisation..
      if (!empty($_SESSION[$_GET['id']]) && $t[0] != 'gift') {
        foreach ($_SESSION[$_GET['id']] AS $v) {
          $split   = explode('|-<>-|', $v);
          $PER     = mc_getTableData('personalisation', 'id', $split[0]);
          $vData   = $split[1];
          $price   = mc_formatPrice($price + $PER->persAddCost);
          $addCost = mc_formatPrice($addCost + $PER->persAddCost);
        }
      }
      // Attributes..
      if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot]) && $t[0] != 'gift') {
        $a_price = array();
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,SUM(`attrCost`) AS `sum` FROM `" . DB_PREFIX . "attributes`
             WHERE `id` IN(" . implode(',', $_SESSION[$attSlot]) . ")
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
        while ($A = mysqli_fetch_object($q)) {
          $array[] = $A->sum;
          // Check stock hasn`t exceeded limits..
          if ($_GET['qty'] > $A->attrStock) {
            return array(
              0 => 'err',
              1 => str_replace(array(
                '{count}',
                '{attribute}'
              ), array(
                $A->attrStock,
                mc_cleanData($A->attrName)
              ), $msg_javascript415)
            );
          }
        }
        $price   = mc_formatPrice($price + $array[0]);
        $addCost = mc_formatPrice($addCost + $array[0]);
      }
      // Update..
      cartOps::updateCartOption($_GET['id'], $_GET['qty'], ($slevel < $_GET['qty'] ? $slevel : $_GET['qty']), ($offer > 0 ? $offer : $price));
      $grand = cartOps::buildBasketTotals(true);
      $itemr = $this->checkout->buildBasketItems($_GET['id']);
      return array(
        0 => 'ok',
        1 => $itemr,
        2 => $_GET['qty'],
        3 => ($this->settings->minCheckoutAmount > 0 ? ($grand >= $this->settings->minCheckoutAmount ? 'no' : 'yes') : 'no')
      );
    }
  }

  public function delete() {
    cartOps::deleteCartItemFromMenuBasket((isset($_GET['code']) ? $_GET['code'] : 'xxxxx'));
    return cartOps::formatSystemCurrency(cartOps::buildBasketTotals(true));
  }

  public function deleteItem() {
    cartOps::deleteCartItem($_GET['id']);
  }

  public function log($t) {
    if (CHECKOUT_DEBUG_LOG && is_dir(GLOBAL_PATH . 'logs') && is_writeable(GLOBAL_PATH . 'logs') && function_exists('file_put_contents')) {
      $file = GLOBAL_PATH . 'logs/checkout-debug-log.log';
      file_put_contents($file, $t . mc_defineNewline() . '- - - - - - - - - - - - - - - - - - - - - - - - -' . mc_defineNewline(), FILE_APPEND);
    }
  }

}

?>