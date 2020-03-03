<?php

class shoppingCart extends mcProducts {

  public $settings;
  public $unicode = 10;
  public $rwr;

  public function addDownloadToken($id) {
    $tk = shoppingCart::generateDownloadCode(shoppingCart::generateUniCode(32), $id);
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `downloadCode` = '{$tk}'
    WHERE `id`     = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
    return $tk;
  }

  public function resetDownloadToken($id) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
    `downloadCode` = ''
    WHERE `id`     = '{$id}'
    ") or die(mc_MySQLError(__LINE__, __FILE__));
  }

  public function updatePaypalErrorTrigger($data) {
    mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "sales` SET
    `paypalErrorTrigger` = (`paypalErrorTrigger` + 1)
    WHERE `id`           = '{$data[1]}'
    AND `buyCode`        = '" . mc_safeSQL($data[0]) . "'
    AND `paymentMethod`  = 'paypal'
    ");
  }

  public function addClickHistory($sale, $id, $product) {
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "click_history` (
    `saleID`,`purchaseID`,`productID`,`clickDate`,`clickTime`,`clickIP`
    ) VALUES (
    '{$sale}','{$id}','{$product}',
    '" . date("Y-m-d") . "',
    '" . date("H:i:s") . "',
    '" . mc_safeSQL(mc_getRealIPAddr()) . "'
    )");
  }

  public function getProductPrice() {
    $slotprice = '0.00';
    $split     = explode('-', $_GET['ppRebuild']);
    if (!empty($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && strpos($_SESSION['product'][$i], '-') !== FALSE) {
          $t       = explode('-', $_SESSION['product'][$i]);
          $attSlot = 'attr-' . substr($_SESSION['product'][$i], 8);
          if (isset($t[0]) || isset($t[1])) {
            if ($t[0] == $split[1] && $t[1] == $split[2]) {
              $P       = mc_getTableData('products', 'id', $t[1]);
              $price   = mc_formatPrice($P->pPrice);
              // Disable for trade..
              if (defined('MC_TRADE_DISCOUNT')) {
                $P->pOffer = 0;
              }
              $offer   = mc_formatPrice($P->pOffer);
              $addCost = '0.00';
              // Personalisation..
              if (!empty($_SESSION[$_SESSION['product'][$i]])) {
                foreach ($_SESSION[$_SESSION['product'][$i]] AS $v) {
                  $split   = explode('|-<>-|', $v);
                  $PER     = mc_getTableData('personalisation', 'id', $split[0]);
                  $vData   = $split[1];
                  $price   = mc_formatPrice($price + $PER->persAddCost);
                  $addCost = mc_formatPrice($addCost + $PER->persAddCost);
                }
              }
              // Attributes..
              if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot])) {
                $a_price = shoppingCart::getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], false, true);
                $price   = mc_formatPrice($price + $a_price);
                $addCost = mc_formatPrice($addCost + $a_price);
              }
              if ($offer > 0) {
                return shoppingCart::formatSystemCurrency(mc_formatPrice($offer + $addCost) * $_SESSION['quantity'][$i]) . '<del class="prevprice" id="pbv-' . $t[0] . '-' . $t[1] . '-' . $t[2] . '">' . shoppingCart::formatSystemCurrency(mc_formatPrice($price)) . '</del>';
              } else {
                return shoppingCart::formatSystemCurrency(mc_formatPrice($price) * $_SESSION['quantity'][$i]);
              }
            }
          }
        }
      }
    }
    return $slotprice;
  }

  public function updatePersonalisation() {
    global $msg_javascript297;
    $_SESSION[$_GET['prd']] = array();
    $recal = array();
    if (!empty($_POST['personalisation'])) {
      foreach ($_POST['personalisation'] AS $k => $v) {
        $PER = mc_getTableData('personalisation', 'id', $k);
        if (isset($PER->reqField) && $PER->reqField == 'yes' && ((trim($v) == '' || $v == 'no-option-selected'))) {
          return array(
            'err',
            $msg_javascript297,
            'personalisation_' . $k
          );
        }
        if ($v && $v != 'no-option-selected') {
          $_SESSION[$_GET['prd']][] = $k . '|-<>-|' . str_replace('|-<>-|', '', $v);
          $recal[] = $k;
        }
      }
    }
    // Recalculate
    if (!empty($recal)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`persAddCost`) AS `pt` FROM `" . DB_PREFIX . "personalisation`
           WHERE `id` IN(" . mc_safeSQL(implode(',', array_unique($recal))) . ")
           AND `persAddCost`  > 0
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $PL = mysqli_fetch_object($q);
      if (isset($PL->pt)) {
        $slot = shoppingCart::productSlotPosition($_GET['prd']);
        if (!isset($_SESSION['extraCost'])) {
          $_SESSION['extraCost'] = array();
          $_SESSION['extraCost'][$slot][0] = '0.00';
        }
        $_SESSION['extraCost'][$slot][0] = mc_formatPrice($PL->pt);
      }
    }
    return array('ok', '');
  }

  public function getAttributeIDs($id) {
    $ar = array();
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id` FROM `" . DB_PREFIX . "attributes`
         WHERE  `attrGroup` = '{$id}'
         ORDER BY `orderBy`
         ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($A = mysqli_fetch_object($q)) {
      $ar[] = $A->id;
    }
    return $ar;
  }

  public function getAttributeData($array, $product, $data = false, $price = false, $weight = false, $slot = 0, $explus = '') {
    global $public_checkout90, $public_checkout91, $public_checkout92, $public_checkout71, $mc_checkout;
    $html = '';
    $sum  = array('0.00');
    $ext  = array();
    if (!empty($array)) {
      if ($price) {
        $SQL = 'SUM(`attrCost`) AS `sum`';
      } elseif ($weight) {
        $SQL = 'SUM(`attrWeight`) AS `weight`';
      } else {
        $SQL = '*';
      }
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT $SQL FROM `" . DB_PREFIX . "attributes`
           LEFT JOIN `" . DB_PREFIX . "attr_groups`
           ON `" . DB_PREFIX . "attributes`.`attrGroup` = `" . DB_PREFIX . "attr_groups`.`id`
           WHERE `" . DB_PREFIX . "attributes`.`id` IN(" . mc_safeSQL(implode(',', $array)) . ")
           ORDER BY `" . DB_PREFIX . "attributes`.`orderBy`
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($A = mysqli_fetch_object($q)) {
        // If all we want is the sum, return it..
        if ($price) {
          return $A->sum;
        } elseif ($weight) {
          return $A->weight;
        } else {
          $sum[] = mc_formatPrice($A->attrCost);
          $ext[] = '<i class="fa fa-plus"></i> ' . mc_safeHTML($A->groupName) . ': ' . mc_safeHTML($A->attrName) . ($A->attrCost > 0 ? ' (+' . shoppingCart::formatSystemCurrency(mc_formatPrice($A->attrCost)) . ')' : '');
        }
      }
    }
    if (isset($_SESSION[$product]) && !empty($_SESSION[$product])) {
      if (isset($_SESSION['extraCost'][$slot][0])) {
        $ext[] = ($data ? $public_checkout92 : $public_checkout91 . ($_SESSION['extraCost'][$slot][0] > 0 ? ' (+' . shoppingCart::formatSystemCurrency(mc_formatPrice($_SESSION['extraCost'][$slot][0])) . ')' : ''));
        $sum[] = mc_formatPrice($_SESSION['extraCost'][$slot][0]);
      } else {
        $ext[] = ($data ? $public_checkout92 : $public_checkout91);
      }
    }
    if (!empty($ext)) {
      $html .= str_replace(array(
        '{extras}',
        '{extras_cost}',
        '{theme_folder}'
      ), array(
        implode('<br>', $ext) . $explus,
        $mc_checkout[10] . shoppingCart::formatSystemCurrency(mc_formatPrice(array_sum($sum))),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/extras.htm'));
    }
    return trim($html);
  }

  public function buildDialogBasket() {
    global $msg_public_header16, $msg_public_header17, $msg_public_header18, $msg_public_header19,
    $msg_public_header20, $msg_public_header21, $public_category13, $msg_shop_basket;
    $html   = str_replace(array(
      '{text}',
      '{theme_folder}'
    ), array(
      $msg_public_header16,
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/empty.htm'));
    $items  = '';
    $latest = '';
    $oftmp  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/offer.htm');
    if (shoppingCart::cartCount() > 0 && !empty($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        $wishItem = '';
        $_SESSION['quantity'][$i] = ceil($_SESSION['quantity'][$i]);
        $attSlot                  = 'attr-' . substr($_SESSION['product'][$i], 8);
        if ($_SESSION['product'][$i] != 'void' && strpos($_SESSION['product'][$i], '-') !== FALSE) {
          $attributes = '';
          if (isset($_SESSION[$attSlot]) || isset($_SESSION[$_SESSION['product'][$i]])) {
            if (!isset($_SESSION[$attSlot]) || empty($_SESSION[$attSlot])) {
              $_SESSION[$attSlot] = array();
            }
            $attributes = shoppingCart::getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], false, false, false, $i);
          }
          $t = explode('-', $_SESSION['product'][$i]);
          if (isset($t[0]) || isset($t[1])) {
            switch($t[0]) {
              // GIFT CERTIFICATE..
              case 'gift':
                $G          = mc_getTableData('giftcerts', 'id', $t[1]);
                $price      = mc_formatPrice($G->value);
                $offer      = '0.00';
                $name       = mc_safeHTML($G->name);
                $attributes = shoppingCart::mc_GiftFromTo($i);
                $pUrl       = $this->rwr->url(array('gift'));
                if ($G->image && file_exists(PATH . PRODUCTS_FOLDER . '/' . $G->image)) {
                  $pImg       = $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/' . $G->image;
                } else {
                  $pImg       = $this->settings->ifolder . '/' . PRODUCTS_FOLDER . '/default_gift.png';
                }
                break;
              // PRODUCTS..
              default:
                $P     = mc_getTableData('products', 'id', $t[1]);
                $price = mc_formatPrice($P->pPrice);
                $imgs  = $this->loadProductImage($t[1]);
                $pImg  = $imgs[0];
                $offer = '0.00';
                // Special offer..
                // Disable for trade..
                if (defined('MC_TRADE_DISCOUNT')) {
                  $P->pOffer = 0;
                }
                if ($P->pOffer > 0) {
                  // Is it multi buy?
                  if ($P->pMultiBuy > 0) {
                    if ($_SESSION['quantity'][$i] >= $P->pMultiBuy) {
                      $offer = mc_formatPrice($P->pOffer);
                    }
                  } else {
                    $offer = mc_formatPrice($P->pOffer);
                  }
                }
                $name = mc_safeHTML($P->pName);
                $pUrl = $this->rwr->url(array(
                  $this->rwr->config['slugs']['prd'] . '/' . $P->id . '/' . ($P->rwslug ? $P->rwslug : $this->rwr->title($P->pName)),
                  'pd=' . $P->id
                ));
                break;
            }
            $totalItemCost = '0.00';
            $sum           = array('0.00');
            if (isset($_SESSION['extraCost'][$i][0])) {
              $sum[] = mc_formatPrice($_SESSION['extraCost'][$i][0]);
            }
            if (isset($_SESSION['extraCost'][$i][1])) {
              $sum[] = mc_formatPrice($_SESSION['extraCost'][$i][1]);
            }
            $marker = 'p' . $t[1];
            // Price display..
            if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0 && isset($_SESSION['trade'][$i])) {
              $displayPrice  = shoppingCart::formatSystemCurrency(mc_formatPrice(($price - $_SESSION['trade'][$i]))) . str_replace('{price}',shoppingCart::formatSystemCurrency(mc_formatPrice($price)),$oftmp);
              $totalItemCost = ($price - $_SESSION['trade'][$i]);
            } else {
              if ($offer > 0) {
                $displayPrice  = shoppingCart::formatSystemCurrency(mc_formatPrice($offer)) . str_replace('{price}',shoppingCart::formatSystemCurrency(mc_formatPrice($price)),$oftmp);
                $totalItemCost = $offer;
              } else {
                $displayPrice  = shoppingCart::formatSystemCurrency(mc_formatPrice($price));
                $totalItemCost = $price;
              }
            }
            // Is this a wish list item?
            if (isset($_SESSION['wishlist'][$i])) {
              if ($this->settings->en_wish == 'yes' && $_SESSION['wishlist'][$i] > 0) {
                $ACC = mc_getTableData('accounts', 'id', (int) $_SESSION['wishlist'][$i]);
                $url = $this->rwr->url(array(
                  $this->rwr->config['slugs']['wls'] . '/' . md5($ACC->id . $ACC->email) . '',
                  'wls=' . md5($ACC->id . $ACC->email)
                ));
                if (isset($ACC->name)) {
                  $wishItem = str_replace(array(
                    '{text}',
                    '{url}'
                  ),array(
                    str_replace('{name}',mc_safeHTML($ACC->name),$msg_shop_basket[3]),
                    $url
                  ),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/wish-list-item.htm'));
                }
              }
            }
            $items .= str_replace(array(
              '{type}',
              '{image}',
              '{id}',
              '{code}',
              '{delete}',
              '{product}',
              '{price}',
              '{qty}',
              '{qty_text}',
              '{apply}',
              '{product_url}',
              '{view_product}',
              '{product_alt}',
              '{extras}',
              '{theme_folder}',
              '{each}',
              '{total}',
              '{wish_list_item}'
            ), array(
              $t[0],
              $pImg,
              $t[1],
              $t[2],
              $msg_public_header17,
              shoppingCart::mc_productName($name),
              $displayPrice,
              $_SESSION['quantity'][$i],
              $msg_public_header19,
              $msg_public_header20,
              $pUrl,
              $public_category13,
              $name,
              $attributes,
              THEME_FOLDER,
              $msg_shop_basket[1],
              shoppingCart::formatSystemCurrency(mc_formatPrice(($_SESSION['quantity'][$i] * (array_sum($sum) + $totalItemCost)))),
              $wishItem
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/item.htm'));
          }
        }
      }
      if ($items) {
        $html = str_replace(array(
          '{basket_items}',
          '{total}',
          '{url}',
          '{checkout_and_pay}',
          '{theme_folder}'
        ), array(
          $items,
          shoppingCart::formatSystemCurrency(shoppingCart::cartTotal()),
          $this->rwr->url(array('checkpay')),
          $msg_public_header18,
          THEME_FOLDER
        ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/wrapper.htm'));
      }
    }
    return trim($html);
  }

  public function mc_GiftFromTo($slot, $basket = false, $prod_slot = '') {
    global $gift_cert11, $gift_cert12, $gift_cert13, $gift_cert14, $gift_cert15, $gift_cert16, $gift_cert18;
    $from = str_replace('{name}', $_SESSION['giftAddr'][$slot]['from_name'], ($basket ? $gift_cert13 : $gift_cert11));
    $to   = str_replace('{name}', $_SESSION['giftAddr'][$slot]['to_name'], ($basket ? $gift_cert14 : $gift_cert12));
    $from = str_replace('{email}', $_SESSION['giftAddr'][$slot]['from_email'], $from);
    $to   = str_replace('{email}', $_SESSION['giftAddr'][$slot]['to_email'], $to);
    if ($basket) {
      $wrap = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-gift-info.htm');
      $cus  = ($_SESSION['giftAddr'][$slot]['message'] ? $gift_cert18 : '');
      $html = str_replace(array(
        '{id}',
        '{from}',
        '{from_name}',
        '{from_email}',
        '{to}',
        '{to_name}',
        '{to_email}',
        '{url}',
        '{edit}',
        '{custom_message}'
      ), array(
        '',
        $gift_cert13,
        mc_safeHTML($_SESSION['giftAddr'][$slot]['from_name']),
        mc_safeHTML($_SESSION['giftAddr'][$slot]['from_email']),
        $gift_cert14,
        mc_safeHTML($_SESSION['giftAddr'][$slot]['to_name']),
        mc_safeHTML($_SESSION['giftAddr'][$slot]['to_email']),
        $this->rwr->url(array(
          $this->rwr->config['slugs']['gft'] . '/' . $prod_slot,
          'gift=' . $prod_slot
        )),
        $gift_cert15,
        $cus
      ), $wrap);
      return str_replace(array(
        '{extras}',
        '{extras_cost}',
        '{theme_folder}'
      ), array(
        $html,
        '--',
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/extras.htm'));
    } else {
      return str_replace(array(
        '{extras}',
        '{extras_cost}'
      ), array(
        $from . '<br>' . $to,
        '- -'
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-dialog/extras.htm'));
    }
  }

  public function mc_productName($name) {
    if (LEFT_MENU_BASKET_CHAR_LIMIT > 0) {
      if (strlen($name) > LEFT_MENU_BASKET_CHAR_LIMIT) {
        return substr($name, 0, LEFT_MENU_BASKET_CHAR_LIMIT) . '..';
      } else {
        return $name;
      }
    }
    return $name;
  }

  public function incrementProductDownload($dl, $pr) {
    // Has this product reached its limit..
    if (isset($pr->pDownloadLimit) && $pr->pDownloadLimit > 0) {
      if (isset($_GET['pdl'])) {
        if (($dl->downloadAmount + 1) > $pr->pDownloadLimit) {
          $random = shoppingCart::generateUniCode(32);
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `liveDownload`        = 'no',
          `buyCode`             = '{$random}'
          WHERE `downloadCode`  = '" . mc_safeSQL($_GET['pdl']) . "'
          AND `id`              = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          return 'expired';
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `downloadAmount`      = (`downloadAmount`+1)
          WHERE `downloadCode`  = '" . mc_safeSQL($_GET['pdl']) . "'
          AND `id`              = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      } else {
        if (($dl->downloadAmount + 1) > $pr->pDownloadLimit) {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `liveDownload`  = 'no'
          WHERE `id`      = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          return 'expired';
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `downloadAmount` = (`downloadAmount`+1)
          WHERE `id`       = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    } else {
      if (isset($pr->vDownloadLimit) && $pr->vDownloadLimit > 0) {
        if (($dl->downloadAmount + 1) > $pr->vDownloadLimit) {
          $random = shoppingCart::generateUniCode(32);
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `liveDownload`        = 'no',
          `buyCode`             = '{$random}'
          WHERE `downloadCode`  = '{$_GET['pdl']}'
          AND `id`              = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
          return 'expired';
        } else {
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `downloadAmount`      = (`downloadAmount`+1)
          WHERE `downloadCode`  = '{$_GET['pdl']}'
          AND `id`              = '{$dl->id}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    return 'ok';
  }

  public function generateDownloadCode($data, $data2) {
    $f = mc_encrypt($data);
    $f .= mc_encrypt($data2);
    return rtrim(substr($f, 0, 50));
  }

  public function rebuildDiscountCoupon() {
    global $public_checkout36, $public_checkout130;
    $totals = '';
    $field  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-option-total.htm');
    if (isset($_SESSION['couponCode'][1])) {
      // If there is no shipping cost anyway, pointless adding this coupon info to totals..
      if (isset($_SESSION['shipping-total']) && $_SESSION['shipping-total'] == 0 && $_SESSION['couponCode'][1] == 'freeshipping') {
        return 'none';
      }
      // If there is no tax cost anyway, pointless adding this coupon info to totals..
      elseif (isset($_SESSION['tax-total']) && $_SESSION['tax-total'] == 0 && $_SESSION['couponCode'][1] == 'notax') {
        return 'none';
      }
      $totals .= str_replace(array(
        '{amount}',
        '{text}',
        '{id}',
        '{price}',
        '{theme_folder}'
      ), array(
        '-' . shoppingCart::formatSystemCurrency($_SESSION['couponCode'][1]),
        ($_SESSION['couponCode'][5] == 'gift' ? $public_checkout130 : $public_checkout36),
        't-coupon',
        mc_formatPrice($_SESSION['couponCode'][1]),
        THEME_FOLDER
      ), $field);
    }
    return trim($totals);
  }

  public function buildGrandTotal($grand) {
    global $public_checkout39;
    $field = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-option-total.htm');
    return str_replace(array(
      '{amount}',
      '{text}',
      '{price}',
      '{theme_folder}'
    ), array(
      shoppingCart::formatSystemCurrency($grand),
      $public_checkout39,
      mc_formatPrice($grand),
      THEME_FOLDER
    ), $field);
  }

  public function buildBasketTotals($rawamount = false, $jsonresponse = false) {
    global $public_checkout35, $public_checkout36, $public_checkout37, $public_checkout38, $public_checkout73,
    $public_checkout97, $public_checkout98, $public_checkout119, $public_checkout130, $mc_checkout;
    $totals                   = '';
    $amount                   = shoppingCart::cartTotal(false,false,true);
    $sub_amount               = shoppingCart::cartTotal(false,false,true);
    $json                     = array();
    $global                   = shoppingCart::cartGlobalDiscount(true);
    $field                    = '';
    if (!$rawamount) {
      $field                  = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-option-total.htm');
    }
    //----------------------------------------
    // Basket sub total..
    //----------------------------------------
    $totals                   = str_replace(array(
      '{amount}',
      '{text}',
      '{id}',
      '{price}'
    ), array(
      shoppingCart::formatSystemCurrency(shoppingCart::cartTotal(false,false,true)),
      $public_checkout35,
      't-sub',
      $amount
    ), $field);
    // Json..
    $json['cart-total-sub']   = shoppingCart::formatSystemCurrency($amount);
    $json['cart-total-t-sub'] = $amount;
    //----------------------------------------
    // Global discount...
    //----------------------------------------
    if ($global > 0 && !defined('NO_GLOBAL_DISCOUNT_MENU')) {
      $globalDisc = shoppingCart::cartGlobalDiscount();
      $amount     = mc_formatPrice($amount - $globalDisc);
      $totals .= str_replace(array(
        '{amount}',
        '{text}',
        '{id}',
        '{price}'
      ), array(
        '-' . shoppingCart::formatSystemCurrency($globalDisc),
        str_replace(array(
          '{percentage}',
          '{count}'
        ), array(
          (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0 ? MC_TRADE_DISCOUNT : $this->settings->globalDiscount),
          $global
        ), (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0 ? $mc_checkout[18] : $public_checkout73)),
        't-global',
        mc_formatPrice($globalDisc)
      ), $field);
      // Json..
      $json['cart-total-global']   = shoppingCart::formatSystemCurrency($globalDisc);
      $json['cart-total-t-global'] = $globalDisc;
    }
    //----------------------------------------
    // Load tax calculation here..
    //----------------------------------------
    shoppingCart::cartTax($amount);
    //----------------------------------------
    // Discount coupon savings..
    //----------------------------------------
    if (isset($_SESSION['couponCode'][1]) && ($_SESSION['couponCode'][1] != '' || in_array($_SESSION['couponCode'][1], array(
      'freeshipping',
      'notax'
    )))) {
      // Free shipping coupon: If there is no shipping cost anyway, pointless adding this coupon..
      if (isset($_SESSION['shipping-total']) && $_SESSION['shipping-total'] == 0 && $_SESSION['couponCode'][1] == 'freeshipping' && !defined('KILL_CHECKOUT_SHIPPING')) {
        $amount = mc_formatPrice($amount + 0);
      }
      // Free tax coupon: If there is no tax cost anyway, pointless adding this coupon..
      elseif (isset($_SESSION['tax-total']) && $_SESSION['tax-total'] == 0 && $_SESSION['couponCode'][1] == 'notax' && !defined('KILL_CHECKOUT_SHIPPING')) {
        $amount = mc_formatPrice($amount + 0);
      }
      // With actual discount, thats ok..
      if ($_SESSION['couponCode'][1] != '') {
        if ($_SESSION['couponCode'][1] > 0) {
          $totals .= str_replace(array(
            '{amount}',
            '{text}',
            '{id}',
            '{price}'
          ), array(
            '-' . shoppingCart::formatSystemCurrency($_SESSION['couponCode'][1]),
            ($_SESSION['couponCode'][5] == 'gift' ? $public_checkout130 : $public_checkout36),
            't-coupon',
            mc_formatPrice($_SESSION['couponCode'][1])
          ), $field);
        }
        $amount                      = mc_formatPrice($amount - $_SESSION['couponCode'][1]);
        // Json..
        $json['cart-total-coupon']   = shoppingCart::formatSystemCurrency(mc_formatPrice($_SESSION['couponCode'][1]));
        $json['cart-total-t-coupon'] = mc_formatPrice($_SESSION['couponCode'][1]);
      }
    }
    //--------------------------------------------------------
    // If tax is only on product total, add tax here..
    // Global switch kills all tax..
    //--------------------------------------------------------
    if (isset($_SESSION['tax-total']) && isset($_SESSION['tax-rate']) && $_SESSION['tax-total'] > 0 && $_SESSION['is-tax-rate'] == 'no' && !defined('KILL_CHECKOUT_SHIPPING')) {
      $totals .= str_replace(array(
        '{amount}',
        '{text}',
        '{id}',
        '{price}'
      ), array(
        shoppingCart::formatSystemCurrency($_SESSION['tax-total']),
        str_replace('{tax}', $_SESSION['tax-rate'], $public_checkout37),
        't-tax',
        (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax' ? '0.00' : mc_formatPrice($_SESSION['tax-total']))
      ), $field);
      if (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax') {
        $_SESSION['tax-total'] = '0.00';
        $amount                = mc_formatPrice($amount + 0);
      } else {
        $amount = mc_formatPrice($amount + $_SESSION['tax-total']);
      }
      // Json..
      $json['cart-total-tax']   = shoppingCart::formatSystemCurrency((isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax' ? '0.00' : mc_formatPrice($_SESSION['tax-total'])));
      $json['cart-total-t-tax'] = (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax' ? '0.00' : mc_formatPrice($_SESSION['tax-total']));
      $json['cart-total-t-tax-rate'] = ($json['cart-total-t-tax'] > 0 && $_SESSION['tax-rate'] > 0 ? $_SESSION['tax-rate'] : '0');
    }
    //----------------------------------------
    // Shipping..
    //----------------------------------------
    if ((isset($_SESSION['shipping-total']) && $_SESSION['shipping-total'] == 0) || (!isset($_SESSION['shipping-total']))) {
      $_SESSION['shipping-total'] = '0.00';
    }
    //----------------------------------------
    // No shipping if cart weight 0..
    // Also, nothing for global switch
    //----------------------------------------
    if (shoppingCart::cartWeight() == 0 && mc_rowCount('flat') == 0 && mc_rowCount('per') == 0 && mc_rowCount('percent') == 0) {
      $_SESSION['shipping-total'] = '0.00';
    }
    if (defined('KILL_CHECKOUT_SHIPPING')) {
      $_SESSION['shipping-total'] = '0.00';
    }
    // Hide shipping if switch is on..
    if (!defined('KILL_CHECKOUT_SHIPPING')) {
      $totals .= str_replace(array(
        '{amount}',
        '{text}',
        '{id}',
        '{price}'
      ), array(
        shoppingCart::formatSystemCurrency($_SESSION['shipping-total']),
        $public_checkout38,
        't-shipping',
        (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'freeshipping' ? '0.00' : mc_formatPrice($_SESSION['shipping-total']))
      ), $field);
    }
    if (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'freeshipping' && !defined('KILL_CHECKOUT_SHIPPING')) {
      $_SESSION['shipping-total'] = '0.00';
      $amount                     = mc_formatPrice($amount + 0);
    } else {
      $amount = mc_formatPrice($amount + $_SESSION['shipping-total']);
    }
    if (defined('KILL_CHECKOUT_SHIPPING')) {
      // Json..
      $json['cart-total-shipping']   = shoppingCart::formatSystemCurrency('0.00');
      $json['cart-total-t-shipping'] = '0.00';
    } else {
      // Json..
      $json['cart-total-shipping']   = shoppingCart::formatSystemCurrency((isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'freeshipping' ? '0.00' : mc_formatPrice($_SESSION['shipping-total'])));
      $json['cart-total-t-shipping'] = (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'freeshipping' ? '0.00' : mc_formatPrice($_SESSION['shipping-total']));
    }
    //------------------------------------------------------
    // If tax also includes shipping, enter tax here..
    //------------------------------------------------------
    if (isset($_SESSION['tax-total']) && isset($_SESSION['tax-rate']) && $_SESSION['tax-total'] > 0 && $_SESSION['is-tax-rate'] == 'yes' && !defined('KILL_CHECKOUT_SHIPPING')) {
      $totals .= str_replace(array(
        '{amount}',
        '{text}',
        '{id}',
        '{price}'
      ), array(
        shoppingCart::formatSystemCurrency($_SESSION['tax-total']),
        str_replace('{tax}', $_SESSION['tax-rate'], $public_checkout37),
        't-tax',
        mc_formatPrice($_SESSION['tax-total'])
      ), $field);
      if (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax') {
        $_SESSION['tax-total'] = '0.00';
        $amount                = mc_formatPrice($amount + 0);
      } else {
        $amount = mc_formatPrice($amount + $_SESSION['tax-total']);
      }
      // Json..
      $json['cart-total-tax']   = shoppingCart::formatSystemCurrency(mc_formatPrice($_SESSION['tax-total']));
      $json['cart-total-t-tax'] = mc_formatPrice($_SESSION['tax-total']);
      $json['cart-total-t-tax-rate'] = ($json['cart-total-t-tax'] > 0 && $_SESSION['tax-rate'] > 0 ? $_SESSION['tax-rate'] : '0');
    }
    //----------------------------
    // Insurance charges..
    //----------------------------
    if ($this->settings->offerInsurance == 'yes' && $this->settings->insuranceAmount > 0 && !defined('NO_GLOBAL_DISCOUNT_MENU')) {
      // Check filter. Should be populated, but in case its not set default..
      $this->settings->insuranceFilter = (in_array($this->settings->insuranceFilter, array(
        'op1',
        'op2',
        'op3',
        'op4',
        'op5',
        'op6',
        'op7',
        'op8'
      )) ? $this->settings->insuranceFilter : 'op1');
      $insValue                        = 0;
      $_SESSION['insurance-total']     = '0.00';
      switch($this->settings->insuranceFilter) {
        // Fixed Amount (Optional/Required)
        case 'op1':
        case 'op5':
          $insValue = number_format($this->settings->insuranceAmount, 2, '.', '');
          break;
        // % of Cart Total (Optional/Required)
        case 'op2':
        case 'op6':
          $insValue = number_format(($this->settings->insuranceAmount * mc_formatPrice($amount)) / 100, 2, '.', '');
          break;
        // % of Shipping Cost (Optional/Required)
        case 'op3':
        case 'op7':
          if ($_SESSION['shipping-total'] > 0) {
            $insValue = number_format(($this->settings->insuranceAmount * $_SESSION['shipping-total']) / 100, 2, '.', '');
          }
          break;
        // % of Goods Total (Optional/Required)
        case 'op4':
        case 'op8':
          if ($sub_amount > 0) {
            // Is there a limit on what products have insurance applied?
            if ($this->settings->insuranceValue > 0) {
              $sub_amount = shoppingCart::cartTotal(false, true);
              $insValue   = number_format(($this->settings->insuranceAmount * $sub_amount) / 100, 2, '.', '');
            } else {
              $insValue = number_format(($this->settings->insuranceAmount * $sub_amount) / 100, 2, '.', '');
            }
          }
          break;
      }
      // Only apply if greater than 0..
      if ($insValue > 0) {
        // If the remove option was used, we just mask the insurance..
        if (defined('MASK_INSURANCE') || isset($_SESSION['insurance-mask'])) {
          $insValue                   = '0.00';
          $_SESSION['insurance-mask'] = '0.00';
        }
        // If not set by default and action not present, mask..
        if (!in_array($this->settings->insuranceFilter, array('op5','op6','op7','op8'))) {
          if ($this->settings->insuranceOptional == 'no' && !isset($_GET['st'])) {
            $insValue                   = '0.00';
            $_SESSION['insurance-mask'] = '0.00';
          }
        }
        $displayTxt = (in_array($this->settings->insuranceFilter, array(
          'op1',
          'op2',
          'op3',
          'op4'
        )) ? ($this->settings->insuranceOptional == 'no' ? $public_checkout119 : $public_checkout98) : $public_checkout97);
        $insUrl = $this->rwr->url(array(
          $this->rwr->config['slugs']['hlp'] . '/ins',
          'help=ins'
        ));
        $totals .= str_replace(array(
          '{amount}',
          '{text}',
          '{id}',
          '{price}'
        ), array(
          shoppingCart::formatSystemCurrency($insValue),
          str_replace('{url}', $insUrl, $displayTxt),
          't-insurance',
          mc_formatPrice($insValue)
        ), $field);
        // Json..
        $json['cart-total-insurance']   = shoppingCart::formatSystemCurrency($insValue);
        $json['cart-total-t-insurance'] = $insValue;
        $amount                         = mc_formatPrice($amount + $insValue);
        $_SESSION['insurance-total']    = $insValue;
      } else {
        // If previously set, remove..
        if (isset($_SESSION['insurance-total'])) {
          unset($_SESSION['insurance-total']);
        }
      }
    }
    //----------------------------------------
    // Return json array or html..
    //----------------------------------------
    if ($jsonresponse) {
      $json['cart-raw-amount'] = $amount;
      return $json;
    }
    return ($rawamount ? $amount : trim($totals));
  }

  public function getTareWeight($weight, $service) {
    $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "tare`
             WHERE `rWeightFrom`             <= $weight
             AND `rWeightTo`                 >= $weight
             AND `rService`                   = '{$service}'
             LIMIT 1
             ") or die(mc_MySQLError(__LINE__, __FILE__));
    $TARE = mysqli_fetch_object($query);
    if (isset($TARE->id)) {
      return array(
        'yes',
        $TARE->rCost
      );
    }
    return array(
      'no',
      '0.00'
    );
  }

  public function buildUSStates($mc_usStates) {
    $string = '';
    if (!empty($mc_usStates)) {
      foreach ($mc_usStates AS $stK => $stV) {
        $string .= '<option value="' . $stK . '">' . mc_cleanData($stV) . '</option>' . mc_defineNewline();
      }
    }
    return $string;
  }

  public function buildCanStates($mc_canStates) {
    $string = '';
    if (!empty($mc_canStates)) {
      foreach ($mc_canStates AS $stK => $stV) {
        $string .= '<option value="' . $stK . '">' . mc_cleanData($stV) . '</option>' . mc_defineNewline();
      }
    }
    return $string;
  }

  public function buildDiscountCouponOption() {
    global $public_checkout6, $public_checkout7, $public_checkout12, $public_checkout16, $public_checkout17;
    if (shoppingCart::cartCount() > 0) {
      return str_replace(array(
        '{text}',
        '{text2}',
        '{text3}',
        '{text4}',
        '{price}',
        '{image}',
        '{remove_link}',
        '{show_for_no_shipping}',
        '{theme_folder}'
      ), array(
        $public_checkout6,
        $public_checkout7,
        $public_checkout16,
        $public_checkout12,
        (isset($_SESSION['couponCode'][1]) ? '-' . shoppingCart::formatSystemCurrency($_SESSION['couponCode'][1]) : shoppingCart::formatSystemCurrency('0.00')),
        (isset($_SESSION['couponCode'][1]) ? 'discount-ok.png' : 'discount-blank.gif'),
        (isset($_SESSION['couponCode'][1]) ? '<span class="remove" id="remove"><a href="#" onclick="deleteDiscountCoupon();return false" title="' . $public_checkout17 . '">' . $public_checkout17 . '</a></span>' : ''),
        (defined('KILL_CHECKOUT_SHIPPING') ? ' style="display:block"' : ''),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-discount-coupon.htm'));
    }
  }

  public function addToBasket() {
    global $msg_javascript296, $msg_javascript297, $msg_javascript415, $msg_javascript437, $msg_javascript455, $msg_jscript3;
    $loc = (isset($_GET['loc']) && in_array($_GET['loc'], array('product','category','buynow')) ? $_GET['loc'] : 'category');
    // Only one wish list item is supported..
    if ($this->settings->en_wish == 'yes') {
      if (isset($_POST['wish']['account']) && $_POST['wish']['account'] > 0 && shoppingCart::wishBasketCnt() == 'yes') {
        return array(
          'status' => 'wishrestr'
        );
      } else {
        if (shoppingCart::wishBasketCnt() == 'yes') {
          return array(
            'status' => 'wishcheck'
          );
        }
      }
    }
    // If the area is not the product page, prevent POST trigger from anywhere else..
    if ($loc == 'category') {
      $_POST = array();
    }
    // If buy now, clear basket first..
    if ($loc == 'buynow') {
      shoppingCart::clearCart();
    }
    $qty        = (isset($_POST['qty']) ? (int) $_POST['qty'] : '1');
    $id         = (isset($_GET['id']) ? (int) $_GET['id'] : '0');
    $tradeprice = '0.00';
    if ($id == 0) {
      return array(
        'status' => 'failed'
      );
    }
    $cat = (isset($_SESSION['thisCat']) ? $_SESSION['thisCat'] : 0);
    if ($qty == 0) {
      return array(
        'status' => 'nothing'
      );
    }
    $type     = 'product';
    $marker   = 'p' . $id;
    $P        = mc_getTableData('products', 'id', $id);
    // Is ID valid?
    if (!isset($P->id)) {
      return array(
        'status' => 'inv-product'
      );
    }
    if (defined('MC_TRADE_DISCOUNT')) {
      if (MC_TRADE_STOCK > 0) {
        $P->pStock = MC_TRADE_STOCK;
      }
      if (!isset($_POST['qty']) && MC_TRADE_MIN > 1) {
        $qty = MC_TRADE_MIN;
      }
    } else {
      if (!isset($_POST['qty']) && $P->minPurchaseQty > 1) {
        $qty = $P->minPurchaseQty;
      }
    }
    // If this is being added from none product page and other options are required, fail..
    if ((!isset($_POST['qty']) && in_array($loc, array('category','buynow'))) || $loc == 'buynow') {
      $countPers = mc_rowCount('personalisation WHERE `productID`  = \'' . $id . '\' AND `enabled` = \'yes\' AND `reqField` = \'yes\'');
      if ($countPers > 0) {
        return array(
          'status' => 'force-product',
          'product' => (array) $P
        );
      }
      $countAttrGrps = mc_rowCount('attr_groups WHERE `productID`  = \'' . $id . '\' AND `isRequired` = \'yes\'');
      $countAttrItms = mc_rowCount('attributes WHERE `productID`  = \'' . $id . '\'');
      if ($countAttrGrps > 0 && $countAttrItms > 0) {
        return array(
          'status' => 'force-product',
          'product' => (array) $P
        );
      }
    }
    $quantity = ($P->pStock < $qty ? $P->pStock : $qty);
    $price    = $P->pPrice;
    // Special offer..
    // Disable for trade..
    if (defined('MC_TRADE_DISCOUNT')) {
      $P->pOffer = 0;
    }
    if ($P->pOffer > 0) {
      // Is it multi buy?
      if ($P->pMultiBuy > 0) {
        if ($qty >= $P->pMultiBuy) {
          define('OFFER_SET', 1);
          $price = $P->pOffer;
        }
      } else {
        define('OFFER_SET', 1);
        $price = $P->pOffer;
      }
    }
    if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT >0 && MC_TRADE_DISCOUNT > 0) {
      $global      = ($P->pPrice > 0 ? shoppingCart::getDiscount($P->pPrice, MC_TRADE_DISCOUNT . '%') : '0.00');
      $tradeprice  = ($P->pPrice > 0 ? shoppingCart::getDiscount($P->pPrice, MC_TRADE_DISCOUNT . '%') : '0.00');
    } else {
      $global  = ($P->pPrice > 0 && !defined('OFFER_SET') && $this->settings->globalDiscount > 0 ? shoppingCart::getDiscount($P->pPrice, $this->settings->globalDiscount . '%') : '0.00');
    }
    $stock   = $P->pStock;
    $iFS     = $P->freeShipping;
    $freebie = ($price == 0 && $P->pDownload == 'yes' ? 'yes' : 'no');
    $isDownL = ($P->pDownload == 'yes' ? 'yes' : 'no');
    $psItems = array();
    $atItems = array();
    $exCostg = array('0.00','0.00');
    $killZ   = $P->countryRestrictions;
    // Adjust cat if blank..
    if ($cat == 0) {
      $C   = mc_getTableData('prod_category', 'product', $P->id, 'ORDER BY `id` DESC');
      $cat = (isset($C->category) ? $C->category : 0);
    }
    // Get category data..
    if ($cat > 0) {
      $CATEGORY = mc_getTableData('categories', 'id', $cat);
    }
    // Check we have a category or that the kill switch isn`t enabled..
    if (!isset($CATEGORY->freeShipping) || defined('KILL_CHECKOUT_SHIPPING')) {
      $CATEGORY->freeShipping = 'no';
    }
    // Display message if out of stock..
    if ($stock == 0) {
      return array(
        'status' => 'no-stock'
      );
    }
    // Check min purchase qty..
    // Trade min/max..
    if (defined('MC_TRADE_MIN')) {
      if (MC_TRADE_MAX > 0) {
        $P->maxPurchaseQty = MC_TRADE_MAX;
      }
      if (MC_TRADE_MIN > 0) {
        $P->minPurchaseQty = MC_TRADE_MIN;
      }
    }
    if ($qty > 0 && $P->minPurchaseQty > 0) {
      if ($qty < $P->minPurchaseQty) {
        // For trade, don`t fail, just auto update..
        if (defined('MC_TRADE_DISCOUNT')) {
          $qty = $P->minPurchaseQty;
        } else {
          return array(
            'status' => 'min-fail',
            'min' => $P->minPurchaseQty,
            'product' => (array) $P
          );
        }
      }
    }
    // Check max purchase qty..
    if ($qty > 0 && $P->maxPurchaseQty > 0) {
      if ($qty > $P->maxPurchaseQty) {
        // For trade, don`t fail, just auto update..
        if (defined('MC_TRADE_DISCOUNT')) {
          $qty = $P->maxPurchaseQty;
        } else {
          return array(
            'status' => 'max-fail',
            'max' => $P->maxPurchaseQty
          );
        }
      }
    }
    // Check attributes if any are required..
    if (!empty($_POST['reqAttr'])) {
      foreach ($_POST['reqAttr'] AS $key => $value) {
        if ($value == 'yes') {
          $missAttr = 0;
          $attrGrab = shoppingCart::getAttributeIDs($key);
          if (!empty($attrGrab)) {
            foreach ($attrGrab AS $checkAttrID) {
              if (in_array($checkAttrID, $_POST['attr'])) {
                ++$missAttr;
              }
            }
            if ($missAttr == 0) {
              return array(
                'status' => 'attr-required',
                'group' => 'attr_group_' . $key
              );
            }
          }
        }
      }
    }
    // If any personalisation options are present, check required fields..
    if (!empty($_POST['personalisation'])) {
      foreach ($_POST['personalisation'] AS $k => $v) {
        $PER = mc_getTableData('personalisation', 'id', $k);
        if (isset($PER->reqField) && $PER->reqField == 'yes' && (trim($v) == '' || $v == 'no-option-selected')) {
          return array(
            'status' => 'pers-required',
            'field' => 'personalisation_' . $k
          );
        } else {
          if (trim($v) != '' && $v != 'no-option-selected') {
            $psItems[] = $k;
          }
        }
      }
    }
    // Get sum of personalised items..
    if (!empty($psItems)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`persAddCost`) AS `pt` FROM `" . DB_PREFIX . "personalisation`
           WHERE `id` IN(" . mc_safeSQL(implode(',', array_unique($psItems))) . ")
           AND `persAddCost`  > 0
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $PL = mysqli_fetch_object($q);
      if (isset($PL->pt)) {
        //$price      = mc_formatPrice($price + $PL->pt);
        $exCostg[0] = mc_formatPrice($PL->pt);
      }
    }
    // Check that attributes don`t exceed stock level..
    if (!empty($_POST['attr'])) {
      foreach ($_POST['attr'] AS $k => $v) {
        if ($v > 0) {
          $ATT = mc_getTableData('attributes', 'id', $v);
          if (isset($ATT->attrStock) && $qty > $ATT->attrStock) {
            return array(
              'status' => 'attr-exceed',
              'stock' => $ATT->attrStock,
              'name' => mc_cleanData($ATT->attrName)
            );
          }
          $atItems[] = $v;
        }
      }
    }
    // Get sum of attributes..
    if (!empty($atItems)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`attrCost`) AS `at` FROM `" . DB_PREFIX . "attributes`
           WHERE `id` IN(" . mc_safeSQL(implode(',', array_unique($atItems))) . ")
           AND `attrCost`  > 0
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $A = mysqli_fetch_object($q);
      if (isset($A->at)) {
        //$price      = mc_formatPrice($price + $A->at);
        $exCostg[1] = mc_formatPrice($A->at);
      }
    }
    // Assign session vars if cart is empty..
    if (!isset($_SESSION['cart_count']) || (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] == 0)) {
      if (isset($_SESSION['cart_count'])) {
        shoppingCart::clearCart();
      }
      $_SESSION['cart_count'] = '0';
      $_SESSION['cost']       = array();
      $_SESSION['trade']      = array();
      $_SESSION['product']    = array();
      $_SESSION['quantity']   = array();
      $_SESSION['global']     = array();
      $_SESSION['freebies']   = array();
      $_SESSION['download']   = array();
      $_SESSION['category']   = array();
      $_SESSION['shipping']   = array();
      $_SESSION['exists']     = array();
      $_SESSION['killzone']   = array();
      $_SESSION['giftAddr']   = array();
      $_SESSION['extraCost']  = array();
      $_SESSION['wishlist']   = array();
    }
    $code      = shoppingCart::genID();
    $serialize = $id;
    $sum       = array(
      0,
      0
    );
    // Personalisation array..won`t be available for all products so check status..
    if (!empty($_POST['personalisation'])) {
      $_SESSION[$type . '-' . $id . '-' . $code] = array();
      foreach ($_POST['personalisation'] AS $k => $v) {
        if ($v && $v != 'no-option-selected') {
          $_SESSION[$type . '-' . $id . '-' . $code][] = $k . '|-<>-|' . str_replace('|-<>-|', '', $v);
          $serialize .= $v;
          ++$sum[0];
        }
      }
      // If no personalisation remove array slot..
      if ($sum[0] == 0) {
        unset($_SESSION[$type . '-' . $id . '-' . $code]);
      }
    }
    // Attributes array..won`t be available for all products so check status..
    if (!empty($_POST['attr'])) {
      $_SESSION['attr-' . $id . '-' . $code] = array();
      foreach ($_POST['attr'] AS $k => $v) {
        if ($v > 0) {
          $_SESSION['attr-' . $id . '-' . $code][] = $v;
          $serialize .= $v;
          ++$sum[1];
        }
      }
      // If no attribute data, remove slot..
      if ($sum[1] == 0) {
        unset($_SESSION['attr-' . $id . '-' . $code]);
      }
    }
    // Does this already exist in the cart array?
    if (in_array(mc_encrypt($serialize), $_SESSION['exists'])) {
      $ncode = shoppingCart::codeIdentification(mc_encrypt($serialize));
      if ($ncode != 'none') {
        shoppingCart::updateCartOption($ncode,$quantity,$stock,mc_formatPrice($price));
        return array(
          'status' => 'exists',
          'code' => $ncode,
          'qty' => $quantity
        );
      }
    }
    $_SESSION['cart_count']  = $_SESSION['cart_count'] + $quantity;
    $_SESSION['product'][]   = $type . '-' . $id . '-' . $code;
    $_SESSION['cost'][]      = mc_formatPrice($price);
    $_SESSION['trade'][]     = mc_formatPrice($tradeprice);
    $_SESSION['quantity'][]  = $quantity;
    $_SESSION['freebies'][]  = $freebie;
    $_SESSION['download'][]  = $isDownL;
    $_SESSION['global'][]    = $global;
    $_SESSION['category'][]  = $cat;
    $_SESSION['shipping'][]  = ($CATEGORY->freeShipping == 'yes' ? 'yes' : $iFS);
    $_SESSION['exists'][]    = mc_encrypt($serialize);
    $_SESSION['killzone'][]  = ($killZ ? $killZ : 'none');
    $_SESSION['giftAddr'][]  = array();
    $_SESSION['extraCost'][] = $exCostg;
    $_SESSION['wishlist'][]  = ($this->settings->en_wish == 'yes' && isset($_POST['wish']['account']) ? (int) $_POST['wish']['account'] : '0');
    return array(
      'status' => 'ok',
      'code' => $code,
      'qty' => $quantity,
      'wish' => (isset($_POST['wish']['account']) && $_POST['wish']['account'] > 0 ? 'yes' : 'no')
    );
  }

  // How many items in cart should have shipping applied
  // For per item rates..
  public function cartCountShippingItems() {
    $count = 0;
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && substr($_SESSION['product'][$i], 0, 4) != 'gift' && $_SESSION['download'][$i] == 'no' && $_SESSION['shipping'][$i] == 'no') {
          $count += $_SESSION['quantity'][$i];
        }
      }
    }
    return $count;
  }

  // How many items in cart are actually pay items
  public function cartCountPaidItems() {
    $count = 0;
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['freebies'][$i] == 'no' && $_SESSION['cost'][$i] > 0) {
          $count += $_SESSION['quantity'][$i];
        }
      }
    }
    return $count;
  }

  // How many freebies in cart..
  public function cartFreebies() {
    $count = 0;
    if (!isset($_SESSION['product'])) {
      return $count;
    }
    for ($i = 0; $i < count($_SESSION['product']); $i++) {
      if ($_SESSION['product'][$i] != 'void' && $_SESSION['freebies'][$i] == 'yes') {
        ++$count;
      }
      return $count;
    }
    return $count;
  }

  // Does cart only contain downloads..
  public function doesCartContainOnlyDownloads() {
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['download'][$i] == 'no') {
          return 'no';
        }
      }
      return 'yes';
    }
    return 'no';
  }

  // Are all virtual products (ie, gift certs)..
  public function allVirtualProducts() {
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && substr($_SESSION['product'][$i], 0, 4) != 'gift') {
          return 'no';
        }
      }
      return 'yes';
    }
    return 'no';
  }

  // Does basket contain wish list items..
  public function wishBasketCnt($id = 'no') {
    $count = 0;
    if (!isset($_SESSION['product'])) {
      return 'no';
    }
    for ($i = 0; $i < count($_SESSION['product']); $i++) {
      if ($_SESSION['product'][$i] != 'void' && $_SESSION['wishlist'][$i] > 0) {
        return ($id == 'yes' ? $_SESSION['wishlist'][$i] : 'yes');
      }
    }
    return ($id == 'yes' ? '0' : 'no');
  }

  // How many download items in cart..
  public function allDownloadItemsInCart() {
    $count = 0;
    if (!isset($_SESSION['product'])) {
      return $count;
    }
    for ($i = 0; $i < count($_SESSION['product']); $i++) {
      if ($_SESSION['product'][$i] != 'void' && $_SESSION['download'][$i] == 'no') {
        ++$count;
      }
      return $count;
    }
    return $count;
  }

  // Get slot position from code..
  public function productSlotPosition($code) {
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] == $code) {
          return $i;
        }
      }
    }
    return 'fail';
  }

  // Get code from product array..
  public function codeIdentification($slot) {
    if (isset($_SESSION['exists'])) {
      for ($i = 0; $i < count($_SESSION['exists']); $i++) {
        if ($_SESSION['exists'][$i] != '0') {
          if ($slot == $_SESSION['exists'][$i]) {
            return $_SESSION['product'][$i];
          }
        }
      }
    }
    return 'none';
  }

  // Random identifier..
  public function genID() {
    $o = shoppingCart::generateUniCode(ceil($this->unicode / 2));
    $t = shoppingCart::generateUniCode(ceil($this->unicode / 2));
    return strtoupper($o . $t);
  }

  // Get global discount...
  public function cartGlobalDiscount($count = false) {
    $cost = '0.00';
    $cnt  = 0;
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['global'][$i] > 0) {
          ++$cnt;
          if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0) {
            $disc = shoppingCart::getDiscount($_SESSION['cost'][$i], MC_TRADE_DISCOUNT . '%');
          } else {
            $disc = shoppingCart::getDiscount($_SESSION['cost'][$i], $this->settings->globalDiscount . '%');
          }
          $_SESSION['global'][$i] = mc_formatPrice($disc * $_SESSION['quantity'][$i]);
          $cost                   = $cost + mc_formatPrice($disc * $_SESSION['quantity'][$i]);
        }
      }
    }
    return ($count ? $cnt : mc_formatPrice($cost));
  }

  // Gets total weight of items in cart...
  public function cartWeight() {
    $weight = '0';
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void') {
          $t       = explode('-', $_SESSION['product'][$i]);
          $attSlot = 'attr-' . substr($_SESSION['product'][$i], 8);
          if (isset($t[0]) && isset($t[1])) {
            $P        = mc_getTableData('products', 'id', mc_digitSan($t[1]));
            $a_weight = 0;
            // Weight for attributes..
            if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot])) {
              $a_weight = shoppingCart::getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], false, false, true);
            }
            $incr   = ($P->pWeight + $a_weight);
            $weight = $weight + ($incr * $_SESSION['quantity'][$i]);
          }
        }
      }
    }
    return ($weight > 0 ? number_format($weight, 2, '.', '') : '0');
  }

  // Gets total weight of items in cart that have shipping applied...
  public function cartWeightForShipping() {
    $weight = '0';
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['shipping'][$i] == 'no') {
          $t       = explode('-', $_SESSION['product'][$i]);
          $attSlot = 'attr-' . substr($_SESSION['product'][$i], 8);
          if (isset($t[0]) && isset($t[1])) {
            $P        = mc_getTableData('products', 'id', mc_digitSan($t[1]));
            $a_weight = 0;
            // Weight for attributes..
            if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot])) {
              $a_weight = shoppingCart::getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], false, false, true);
            }
            $incr   = ($P->pWeight + $a_weight);
            $weight = $weight + ($incr * $_SESSION['quantity'][$i]);
          }
        }
      }
    }
    return ($weight > 0 ? number_format($weight, 2, '.', '') : '0');
  }

  // Get tax amount..
  public function cartTax($sub) {
    $tax = array();
    // No shipping for free shipping coupons..
    if (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'freeshipping') {
      $_SESSION['shipping-total'] = '0.00';
    }
    // No tax for free tax coupons..
    if (isset($_SESSION['couponCode'][1]) && $_SESSION['couponCode'][1] == 'notax') {
      $_SESSION['tax-total']   = 0;
      $_SESSION['is-tax-rate'] = 'no';
      $_SESSION['tax-rate']    = 0;
      return;
    }
    $discountCouponOff = 0;
    // Coupon discount?
    if (isset($_SESSION['couponCode'][1]) && $this->settings->coupontax == 'yes') {
      $discountCouponOff = $_SESSION['couponCode'][1];
    }
    $shipping = (isset($_SESSION['shipping-total']) ? mc_formatPrice($_SESSION['shipping-total']) : '0.00');
    if (isset($_SESSION['shipto'][0])) {
      // Load zone area..
      $Z_A  = mc_getTableData('zone_areas', 'id', mc_digitSan($_SESSION['shipto'][0]));
      // Load zone..
      $ZONE = mc_getTableData('zones', 'id', (isset($Z_A->inZone) ? $Z_A->inZone : '0'));
      if (isset($ZONE->zRate) && $ZONE->zRate > 0) {
        switch($ZONE->zShipping) {
          case 'yes':
            $calc = number_format($ZONE->zRate * mc_formatPrice($sub + $shipping - $discountCouponOff) / 100, 2, '.', '');
            $tax  = array(
              $calc,
              'yes',
              $ZONE->zRate
            );
            break;
          case 'no':
            $calc = number_format($ZONE->zRate * mc_formatPrice($sub - $discountCouponOff) / 100, 2, '.', '');
            $tax  = array(
              $calc,
              'no',
              $ZONE->zRate
            );
            break;
        }
        $_SESSION['tax-total']   = (isset($tax[0]) ? $tax[0] : 0);
        $_SESSION['is-tax-rate'] = (isset($tax[1]) ? $tax[1] : 'no');
        $_SESSION['tax-rate']    = (isset($tax[2]) ? $tax[2] : 0);
        return;
      }
    }
    $_SESSION['tax-total']   = 0;
    $_SESSION['is-tax-rate'] = 'no';
    $_SESSION['tax-rate']    = 0;
    return;
  }

  // Gets cart total for coupon restricted categories...
  public function cartTotalCouponCatRestriction($cats) {
    $categories = explode(',', $cats);
    $cost       = '0.00';
    if (empty($categories)) {
      return $cost;
    }
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void') {
          $chop = explode('-', $_SESSION['product'][$i]);
          $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `category` FROM `" . DB_PREFIX . "prod_category`
                   WHERE `product` = '{$chop[1]}'
				           ORDER BY `category`
                   ") or die(mc_MySQLError(__LINE__, __FILE__));
          while ($CATS = mysqli_fetch_object($query)) {
            if (in_array($CATS->category, $categories)) {
              $item = ($_SESSION['cost'][$i]) * $_SESSION['quantity'][$i];
              $cost = mc_formatPrice($cost + $item);
              break;
            }
          }
        }
      }
    }
    return mc_formatPrice($cost);
  }

  // Gets cart total...
  public function cartTotal($with_coupon = false, $with_ins = false, $skip_trade = false) {
    $cost = '0.00';
    $ins  = '0.00';
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void') {
          // Adjustment for trade..
          if (!$skip_trade && defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0 && isset($_SESSION['trade'][$i])) {
            $item = mc_formatPrice($_SESSION['cost'][$i] - $_SESSION['trade'][$i]) * $_SESSION['quantity'][$i];
          } else {
            $item = ($_SESSION['cost'][$i]) * $_SESSION['quantity'][$i];
          }
          $pers = (isset($_SESSION['extraCost'][$i][0]) && $_SESSION['extraCost'][$i][0] > 0 ? mc_formatPrice($_SESSION['extraCost'][$i][0] * $_SESSION['quantity'][$i]) : '0.00');
          $atts = (isset($_SESSION['extraCost'][$i][1]) && $_SESSION['extraCost'][$i][1] > 0 ? mc_formatPrice($_SESSION['extraCost'][$i][1] * $_SESSION['quantity'][$i]) : '0.00');
          $cost = mc_formatPrice($cost + ($item + $pers + $atts));
          if ($_SESSION['cost'][$i] >= $this->settings->insuranceValue) {
            $ins = mc_formatPrice($ins + ($item + $pers + $atts));
          }
        }
      }
    }
    // Is discount coupon present..
    if ($cost > 0 && isset($_SESSION['couponCode'][1]) && $with_coupon) {
      $newCost = mc_formatPrice($cost - $_SESSION['couponCode'][1]);
      if ($newCost > 0) {
        $cost = $newCost;
      }
    }
    // Do we just want amount applicable for insurance?
    if ($with_ins) {
      return mc_formatPrice($ins);
    }
    return mc_formatPrice($cost);
  }

  // Cart total for percentage based rates..
  public function cartTotalPercRates($with_coupon = false) {
    $cost = '0.00';
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['shipping'][$i] == 'no') {
          $item = ($_SESSION['cost'][$i]) * $_SESSION['quantity'][$i];
          $cost = mc_formatPrice($cost + $item);
        }
      }
    }
    // Is discount coupon present..
    if ($cost > 0 && isset($_SESSION['couponCode'][1]) && $with_coupon) {
      $newCost = mc_formatPrice($cost - $_SESSION['couponCode'][1]);
      if ($newCost > 0) {
        $cost = $newCost;
      }
    }
    return mc_formatPrice($cost);
  }

  // Updates option in cart array..
  public function updateCartOption($prod, $qty, $stock, $price) {
    $_SESSION['cart_count'] = 0;
    if (!empty($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] == $prod) {
          $_SESSION['quantity'][$i] = ($_SESSION['quantity'][$i] + $qty);
          $_SESSION['cost'][$i]     = $price;
          // If stock level has exceeded total stock, adjust stock..
          if ($_SESSION['quantity'][$i] > $stock) {
            $_SESSION['quantity'][$i] = $stock;
          }
        }
        $_SESSION['cart_count'] = $_SESSION['cart_count'] + $_SESSION['quantity'][$i];
      }
    }
  }

  // Delete cart item from menu basket..
  public function deleteCartItemFromMenuBasket($id) {
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($id == $_SESSION['product'][$i]) {
          $attr                               = 'attr-' . substr($_SESSION['product'][$i], 8);
          $_SESSION['cost'][$i]               = 0;
          $_SESSION[$_SESSION['product'][$i]] = array();
          $_SESSION[$attr]                    = array();
          $_SESSION['product'][$i]            = 'void';
          $_SESSION['cart_count']             = $_SESSION['cart_count'] - $_SESSION['quantity'][$i];
          $_SESSION['quantity'][$i]           = 0;
          $_SESSION['global'][$i]             = 0;
          $_SESSION['freebies'][$i]           = 'no';
          $_SESSION['download'][$i]           = 0;
          $_SESSION['shipping'][$i]           = 0;
          $_SESSION['exists'][$i]             = 0;
          $_SESSION['killzone'][$i]           = 'none';
          $_SESSION['giftAddr'][$i]           = array();
        }
      }
      // If cart is now empty, clear vars..
      if ($_SESSION['cart_count'] == 0) {
        shoppingCart::clearCart();
      }
    }
  }

  // Delete cart item..
  public function deleteCartItem($id) {
    $wasDownload = 'no';
    if (isset($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($id == $_SESSION['product'][$i]) {
          $attr                               = 'attr-' . substr($_SESSION['product'][$i], 8);
          $wasDownload                        = $_SESSION['download'][$i];
          $_SESSION[$_SESSION['product'][$i]] = array();
          $_SESSION[$attr]                    = array();
          $_SESSION['cost'][$i]               = 0;
          $_SESSION['product'][$i]            = 'void';
          $_SESSION['cart_count']             = $_SESSION['cart_count'] - $_SESSION['quantity'][$i];
          $_SESSION['quantity'][$i]           = 0;
          $_SESSION['download'][$i]           = 0;
          $_SESSION['freebies'][$i]           = 'no';
          $_SESSION['shipping'][$i]           = 0;
          $_SESSION['exists'][$i]             = 0;
          $_SESSION['killzone'][$i]           = 'none';
          $_SESSION['giftAddr'][$i]           = array();
        }
      }
      // If cart is now empty, clear vars..
      if ($_SESSION['cart_count'] == 0) {
        shoppingCart::clearCart();
      }
    }
    return $wasDownload;
  }

  // Get area/country we are shipping to..
  public function getShippingDestination() {
    $string = '';
    if (isset($_SESSION['shipto'][0]) && isset($_SESSION['shipto'][1])) {
      $CN = mc_getTableData('countries', 'id', mc_digitSan($_SESSION['shipto'][1]));
      $AR = mc_getTableData('zone_areas', 'id', mc_digitSan($_SESSION['shipto'][0]));
      return str_replace(array(
        '{country}',
        '{area}'
      ), array(
        mc_cleanData($CN->cName),
        mc_cleanData($AR->areaName)
      ), $public_shipping19);
    }
    return $string;
  }

  // Build hidden fields..
  public function hiddenFields($grand) {
    global $build;
    $hiddenFields = '';
    foreach ($build AS $key => $value) {
      $hiddenFields .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . mc_defineNewline();
    }
    // Only extract certain session vars for the hidden fields..
    foreach ($_SESSION AS $key => $value) {
      if (in_array($key, array(
        'shippingSetCountry',
        'shippingSetArea',
        'setShippingRateID',
        'setShippingRateService'
      ))) {
        $hiddenFields .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . mc_defineNewline();
      }
    }
    $hiddenFields .= '<input type="hidden" name="grand-total" value="' . $grand . '">';
    return trim($hiddenFields);
  }

  // Gets count of items in cart..
  public function cartCount() {
    return (isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : '0');
  }

  // Clears all cart items..
  public function clearCart() {
    $tmp = array();
    // Keep account / admin vars to prevent being logged out after cart is cleared..
    foreach (array('mc_auth_','mc_acc_type_','mc_checkrdr_') AS $a) {
      if (isset($_SESSION[$a . mc_encrypt(mc_encrypt(SECRET_KEY))])) {
        $tmp[$a . mc_encrypt(mc_encrypt(SECRET_KEY))] = $_SESSION[$a . mc_encrypt(mc_encrypt(SECRET_KEY))];
      }
    }
    foreach (array('_mc_currency','_loggedInAs','_global_user','_accessPages','_loggedInAs','_user_type','_del_priv') AS $b) {
      if (isset($_SESSION[mc_encrypt(SECRET_KEY) . $b])) {
        $tmp[mc_encrypt(SECRET_KEY) . $b] = $_SESSION[mc_encrypt(SECRET_KEY) . $b];
      }
    }
    // Clear..
    $_SESSION = array();
    // Restore kept vars..
    if (!empty($tmp)) {
      $_SESSION = $tmp;
    }
  }

  // Generate buy code for sales..
  public function generateUniCode($num = 20, $email = '', $name = '') {
    $f = mc_encrypt(date('Ymdhis') . $email . $name . uniqid(rand(), 1));
    $f .= mc_encrypt(time() . $email . uniqid(rand(), 1));
    return substr($f, 0, $num);
  }

}

?>