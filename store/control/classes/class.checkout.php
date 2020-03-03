<?php

class mcCheckout {

  public $settings;
  public $products;
  public $cart;
  public $rwr;
  public $account;
  public $gwmethod;

  public function rebuildAccountOrder($id, $acc, $sale) {
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
    // Check purchases..
    $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
         `" . DB_PREFIX . "purchases`.`productID` AS `purProdID`,
         `" . DB_PREFIX . "purchases`.`id` AS `purID`
         FROM `" . DB_PREFIX . "purchases`
         LEFT JOIN `" . DB_PREFIX . "sales`
          ON `" . DB_PREFIX . "sales`.`id` = `" . DB_PREFIX . "purchases`.`saleID`
         WHERE `" . DB_PREFIX . "sales`.`saleConfirmation`   = 'yes'
         AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
         AND `" . DB_PREFIX . "purchases`.`saleID` = '{$id}'
         AND `" . DB_PREFIX . "sales`.`account`     = '{$acc}'
         ORDER BY `" . DB_PREFIX . "purchases`.`id`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($PURCHASES = mysqli_fetch_object($q)) {
      $skipProduct = 'no';
      switch ($PURCHASES->productType) {
        case 'virtual':
          $GFTCT = mc_getTableData('giftcerts', 'id', $PURCHASES->giftID,' AND `enabled` = \'yes\'');
          if (!isset($GFTCT->id)) {
            $skipProduct = 'yes';
          }
          break;
        default:
          $PRODUCT = mc_getTableData('products', 'id', $PURCHASES->productID,' AND `pEnable` = \'yes\'');
          if (!isset($PRODUCT->id)) {
            $skipProduct = 'yes';
          }
          break;
      }
      if ($skipProduct == 'no') {
        // Adjust stock max for trade..
        if ($PURCHASES->giftID == 0) {
          if (defined('MC_TRADE_DISCOUNT')) {
            if (MC_TRADE_STOCK > 0) {
              $PRODUCT->pStock = MC_TRADE_STOCK;
            }
          }
        }
        $qty = $PURCHASES->productQty;
        $serialize = $PURCHASES->purProdID;
        // Check min purchase qty..
        // Trade min/max..
        if ($PURCHASES->giftID == 0) {
          if (defined('MC_TRADE_MIN')) {
            if (MC_TRADE_MAX > 0) {
              $PRODUCT->maxPurchaseQty = MC_TRADE_MAX;
            }
            if (MC_TRADE_MIN > 0) {
              $PRODUCT->minPurchaseQty = MC_TRADE_MIN;
            }
          }
          if ($qty > 0 && $PRODUCT->minPurchaseQty > 0) {
            if ($qty < $PRODUCT->minPurchaseQty) {
              $qty = $PRODUCT->minPurchaseQty;
            }
          }
          // Check max purchase qty..
          if ($qty > 0 && $PRODUCT->maxPurchaseQty > 0) {
            if ($qty > $PRODUCT->maxPurchaseQty) {
              $qty = $PRODUCT->maxPurchaseQty;
            }
          }
        }
        if ((isset($PRODUCT->pStock) && $qty <= $PRODUCT->pStock) || $PURCHASES->giftID > 0) {
          $attributes      = array();
          $personalisation = array();
          $attrStock       = 'ok';
          $price           = (isset($PRODUCT->pPrice) ? $PRODUCT->pPrice : '0.00');
          $global          = '0.00';
          $tradeprice      = '0.00';
          $offerSet        = 'no';
          $exCostg         = array('0.00','0.00');
          $giftData        = array();
          $giftCheck       = 'ok';
          $sum             = array(
            0,
            0
          );
          $type            = ($PURCHASES->giftID == 0 ? 'product' : 'gift');
          $code            = $this->cart->genID();
          // Special offer..
          // Disable for trade..
          if ($PURCHASES->giftID == 0) {
            if (defined('MC_TRADE_DISCOUNT')) {
              $PRODUCT->pOffer = 0;
            }
            if ($PRODUCT->pOffer > 0) {
              // Is it multi buy?
              if ($PRODUCT->pMultiBuy > 0) {
                if ($qty >= $PRODUCT->pMultiBuy) {
                  $offerSet = 'yes';
                  $price = $PRODUCT->pOffer;
                }
              } else {
                $offerSet = 'yes';
                $price = $PRODUCT->pOffer;
              }
            }
          }
          // Is this a gift cert?
          if ($PURCHASES->giftID > 0) {
            $GFT   = mc_getTableData('giftcodes', 'id', $PURCHASES->giftID,' AND `saleID` = \'' . $id . '\' AND `purchaseID` = \'' . $PURCHASES->purID . '\' AND `enabled` = \'yes\'');
            if (isset($GFT->from_name)) {
              // Is price the same?
              if ($price != $GFTCT->value) {
                $price = $GFTCT->value;
              }
              $giftData = array(
                'from_name' => mc_cleanData($GFT->from_name),
                'from_email' => mc_cleanData($GFT->from_email),
                'to_name' => mc_cleanData($GFT->to_name),
                'to_email' => mc_cleanData($GFT->to_email),
                'message' => mc_cleanData($GFT->message)
              );
            } else {
              $giftCheck = 'fail';
            }
          } else {
            // Does this product have attributes?
            $_SESSION['attr-' . $PURCHASES->purProdID . '-' . $code] = array();
            $aExtraCost = array();
            $a = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
                 `" . DB_PREFIX . "attributes`.`id` AS `attrID`,
                 `" . DB_PREFIX . "purch_atts`.`id` AS `attrPurID`
                 FROM `" . DB_PREFIX . "purch_atts`
                 LEFT JOIN `" . DB_PREFIX . "attributes`
                  ON `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
                 WHERE `" . DB_PREFIX . "purch_atts`.`saleID` = '{$id}'
                 AND `" . DB_PREFIX . "purch_atts`.`purchaseID` = '{$PURCHASES->purID}'
                 AND `" . DB_PREFIX . "purch_atts`.`productID` = '{$PURCHASES->purProdID}'
                 ORDER BY `" . DB_PREFIX . "purch_atts`.`id`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
            while ($ATTR = mysqli_fetch_object($a)) {
              if ($attrStock == 'ok' && $ATTR->attrStock > 0 && $qty > 0) {
                $aExtraCost[] = $ATTR->attrCost;
                $_SESSION['attr-' . $PURCHASES->purProdID . '-' . $code][] = $ATTR->attrID;
                $serialize .= $ATTR->attrID;
                ++$sum[1];
              } else {
                $attrStock = 'fail';
              }
            }
            // If no attribute data, remove slot..
            if ($sum[1] == 0 || $attrStock == 'fail') {
              unset($_SESSION['attr-' . $PURCHASES->purProdID . '-' . $code]);
            } else {
              $exCostg[1] = (!empty($aExtraCost) ? mc_formatPrice(array_sum($aExtraCost)) : '0.00');
            }
          }
          if ($attrStock == 'ok' && $giftCheck != 'fail') {
            if ($PURCHASES->giftID == 0) {
              $_SESSION[$type . '-' . $PURCHASES->purProdID . '-' . $code] = array();
              $pExtraCost = array();
              // Does this product have personalisation..
              $p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                   LEFT JOIN `" . DB_PREFIX . "personalisation`
                    ON `" . DB_PREFIX . "purch_pers`.`personalisationID` = `" . DB_PREFIX . "personalisation`.`id`
                   WHERE `" . DB_PREFIX . "purch_pers`.`saleID` = '{$id}'
                   AND `" . DB_PREFIX . "purch_pers`.`purchaseID` = '{$PURCHASES->purID}'
                   AND `" . DB_PREFIX . "purch_pers`.`productID` = '{$PURCHASES->purProdID}'
                   AND `" . DB_PREFIX . "personalisation`.`enabled` = 'yes'
                   ORDER BY `" . DB_PREFIX . "purch_pers`.`id`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
              while ($PER = mysqli_fetch_object($p)) {
                $_SESSION[$type . '-' . $PURCHASES->purProdID . '-' . $code][] = $PER->personalisationID . '|-<>-|' . str_replace('|-<>-|', '', mc_cleanData($PER->visitorData));
                $serialize .= $PER->visitorData;
                ++$sum[0];
                $pExtraCost[] = $PER->persAddCost;
              }
              // If no personalisation remove array slot..
              // Else, total up sum of extra cost..
              if ($sum[0] == 0) {
                unset($_SESSION[$type . '-' . $PURCHASES->purProdID . '-' . $code]);
              } else {
                $exCostg[0] = (!empty($pExtraCost) ? mc_formatPrice(array_sum($pExtraCost)) : '0.00');
              }
            }
            // Session cart vars..
            $_SESSION['cart_count']  = $_SESSION['cart_count'] + $qty;
            $_SESSION['product'][]   = $type . '-' . ($PURCHASES->giftID > 0 ? $PURCHASES->giftID : $PURCHASES->purProdID) . '-' . $code;
            $_SESSION['cost'][]      = mc_formatPrice($price);
            // Set current prices..
            if ($PURCHASES->giftID == 0) {
              if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0) {
                $global      = ($PRODUCT->pPrice > 0 ? $this->cart->getDiscount($PRODUCT->pPrice, MC_TRADE_DISCOUNT . '%') : '0.00');
                $tradeprice  = ($PRODUCT->pPrice > 0 ? $this->cart->getDiscount($PRODUCT->pPrice, MC_TRADE_DISCOUNT . '%') : '0.00');
              } else {
                $global  = ($PRODUCT->pPrice > 0 && $offerSet == 'no' && $this->settings->globalDiscount > 0 ? $this->cart->getDiscount($PRODUCT->pPrice, $this->settings->globalDiscount . '%') : '0.00');
              }
            }
            $_SESSION['trade'][]     = mc_formatPrice($tradeprice);
            $_SESSION['quantity'][]  = $qty;
            $_SESSION['freebies'][]  = ($price == 0 && isset($PRODUCT->pDownload) && $PRODUCT->pDownload == 'yes' ? 'yes' : 'no');
            $_SESSION['download'][]  = (isset($PRODUCT->pDownload) && $PRODUCT->pDownload == 'yes' ? 'yes' : 'no');
            $_SESSION['global'][]    = $global;
            $_SESSION['category'][]  = $PURCHASES->categoryID;
            // Get category data..
            if ($PURCHASES->categoryID > 0) {
              $CATEGORY = mc_getTableData('categories', 'id', $PURCHASES->categoryID);
            }
            $_SESSION['shipping'][]  = ($PURCHASES->giftID == 0 ? (isset($CATEGORY->freeShipping) && $CATEGORY->freeShipping == 'yes' ? 'yes' : $PRODUCT->freeShipping) : 'no');
            $_SESSION['killzone'][]  = ($PURCHASES->giftID == 0 && $PRODUCT->countryRestrictions ? $PRODUCT->countryRestrictions : 'none');
            $_SESSION['giftAddr'][]  = $giftData;
            $_SESSION['extraCost'][] = $exCostg;
            $_SESSION['exists'][]    = ($PURCHASES->giftID > 0 ? '0' : mc_encrypt($serialize));
            $_SESSION['wishlist'][]  = '0';
          }
        }
      }
    }
    return $_SESSION['cart_count'];
  }

  public function buildBasketItems($rebuild = '') {
    global $public_checkout3, $public_checkout12, $public_checkout13, $mc_checkout, $public_checkout14, $public_checkout15,
    $msg_javascript121, $msg_javascript123, $public_product21, $msg_script9, $msg_javascript196, $public_category13,
    $public_checkout64, $msg_javascript45, $public_checkout67, $public_checkout71, $public_product30, $public_checkout72,
    $public_checkout86, $msg_shop_basket;
    $html  = '';
    $slot  = 0;
    $oftmp = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-item-offer.htm');
    if (!empty($_SESSION['product'])) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        $block = '';
        $wishItem = '';
        if ($_SESSION['product'][$i] != 'void' && strpos($_SESSION['product'][$i], '-') !== FALSE) {
          ++$slot;
          $t       = explode('-', $_SESSION['product'][$i]);
          $attSlot = 'attr-' . substr($_SESSION['product'][$i], 8);
          if (isset($t[0]) || isset($t[1])) {
            switch($t[0]) {
              // GIFT CERTIFICATE..
              case 'gift':
                $G      = mc_getTableData('giftcerts', 'id', $t[1]);
                $price  = mc_formatPrice($G->value);
                $offer  = '0.00';
                $name   = mc_cleanData($G->name);
                $pUrl   = $this->rwr->url(array('gift'));
                $pdID   = $G->id;
                $textD  = '';
                $stockL = '9999';
                $code   = '';
                break;
              // PRODUCT..
              default:
                $P     = mc_getTableData('products', 'id', $t[1]);
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
                    if ($_SESSION['quantity'][$i] >= $P->pMultiBuy) {
                      $offer = mc_formatPrice($P->pOffer);
                    }
                  } else {
                    $offer = mc_formatPrice($P->pOffer);
                  }
                }
                $name   = mc_cleanData($P->pName);
                $pUrl = $this->rwr->url(array(
                  $this->rwr->config['slugs']['prd'] . '/' . $P->id . '/' . ($P->rwslug ? $P->rwslug : $this->rwr->title($P->pName)),
                  'pd=' . $P->id
                ));
                $pdID   = $P->id;
                $textD  = $P->checkoutTextDisplay;
                $stockL = (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0 ? MC_TRADE_STOCK : $P->pStock);
                $code   = ($P->pCode ? '[' . $P->pCode . '] ' : '');
                break;
            }
            $marker        = 'p' . $t[1];
            $addCost       = '0.00';
            // Does this item have any personalisation options..
            $psData  = '';
            if (!empty($_SESSION[$_SESSION['product'][$i]]) && $t[0] != 'gift') {
              $li = '';
              foreach ($_SESSION[$_SESSION['product'][$i]] AS $v) {
                $split = explode('|-<>-|', $v);
                $PER   = mc_getTableData('personalisation', 'id', $split[0]);
                $vData = $split[1];
                if (isset($PER->persInstructions)) {
                  if (PERSONALISATION_TEXT_RESTRICTION > 0) {
                    $vData = trim($vData);
                    $vData = (strlen($vData) > PERSONALISATION_TEXT_RESTRICTION ? substr($vData, 0, PERSONALISATION_TEXT_RESTRICTION) . $public_checkout72 : $vData);
                  }
                  if ($vData && $vData != 'no-option-selected') {
                    $li .= str_replace(array(
                      '{option_text}',
                      '{visitor_data}',
                      '{extra_cost}',
                      '{theme_folder}'
                    ), array(
                      mc_persTextDisplay(mc_safeHTML($PER->persInstructions), true),
                      mc_NL2BR(mc_safeHTML($vData)),
                      ($PER->persAddCost > 0 ? str_replace('{extra_cost}', $this->products->formatSystemCurrency(mc_formatPrice($PER->persAddCost)), $public_checkout71) : $public_product30),
                      THEME_FOLDER
                    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-personalisation-option.htm'));
                    if ($PER->persAddCost > 0) {
                      //$price   = mc_formatPrice($price + $PER->persAddCost);
                      //$addCost = mc_formatPrice($addCost + $PER->persAddCost);
                    }
                  }
                }
              }
              // Show personalisation..
              if ($li) {
                $psData = str_replace(array(
                  '{items}',
                  '{edit}',
                  '{url}',
                  '{id}',
                  '{ship_tax_off}',
                  '{price-box}',
                  '{theme_folder}'
                ), array(
                  trim($li),
                  $public_checkout67,
                  $this->rwr->url(array(
                    $this->rwr->config['slugs']['edp'] . '/' . $_SESSION['product'][$i],
                    'ppCE=' . $_SESSION['product'][$i]
                  )),
                  $_SESSION['product'][$i],
                  (defined('KILL_CHECKOUT_SHIPPING') ? 'off' : 'on'),
                  'pb-product-' . substr($marker, 1) . '-' . $t[2],
                  THEME_FOLDER
                ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-personalisation-wrapper.htm'));
              }
            }
            // Attributes..
            $attributes = '';
            if (isset($_SESSION[$attSlot])) {
              $attributes = $this->cart->getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], true, false, false, $i, $psData);
            }
            $totalItemCost = '0.00';
            $sum           = array('0.00');
            if (isset($_SESSION['extraCost'][$i][0])) {
              $sum[] = mc_formatPrice($_SESSION['extraCost'][$i][0]);
            }
            if (isset($_SESSION['extraCost'][$i][1])) {
              $sum[] = mc_formatPrice($_SESSION['extraCost'][$i][1]);
            }
            // For gift certificate, show to and from..
            if ($t[0] == 'gift') {
              $attributes = $this->cart->mc_GiftFromTo($i, true, $_SESSION['product'][$i]);
            }
            // Price display..
            if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0 && isset($_SESSION['trade'][$i])) {
              $displayPrice  = $this->cart->formatSystemCurrency(mc_formatPrice(($price - $_SESSION['trade'][$i]))) . str_replace('{price}',$this->cart->formatSystemCurrency(mc_formatPrice($price)),$oftmp);
              $totalItemCost = ($price - $_SESSION['trade'][$i]);
            } else {
              if ($offer > 0) {
                $displayPrice  = $this->cart->formatSystemCurrency(mc_formatPrice($offer)) . str_replace('{price}',$this->cart->formatSystemCurrency(mc_formatPrice($price)),$oftmp);
                $totalItemCost = $offer;
              } else {
                $displayPrice  = $this->cart->formatSystemCurrency(mc_formatPrice($price));
                $totalItemCost = $price;
              }
            }
            // Is this a wish list item?
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
            $block = str_replace(array(
              '{url}',
              '{item}',
              '{cart_slot_id}',
              '{id}',
              '{max}',
              '{qty}',
              '{price}',
              '{delete}',
              '{text}',
              '{view_product}',
              '{text2}',
              '{no_manual}',
              '{extras}',
              '{image}',
              '{personalisation_details}',
              '{ship_tax_off}',
              '{additional_text}',
              '{theme_folder}',
              '{qty_text}',
              '{code}',
              '{total}',
              '{wish_list_item}'
            ), array(
              $pUrl,
              mc_safeHTML($name),
              $_SESSION['product'][$i],
              $pdID,
              $stockL,
              $_SESSION['quantity'][$i],
              $displayPrice,
              $public_checkout14,
              $msg_javascript121,
              $public_category13,
              $msg_javascript123,
              $msg_javascript196,
              $attributes,
              $this->cart->loadDisplayImage($pdID, true, false, $t[0]),
              trim($psData),
              (defined('KILL_CHECKOUT_SHIPPING') ? 'off' : 'on'),
              mcCheckout::additionalCheckoutText($textD),
              THEME_FOLDER,
              $mc_checkout[9],
              $code,
              $this->cart->formatSystemCurrency(mc_formatPrice(($_SESSION['quantity'][$i] * (array_sum($sum) + $totalItemCost)))),
              $wishItem
            ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-item.htm'));
          }
        }
        // For personlisation update, we only need updated product, so break from loop and finish..
        if ($rebuild) {
          if ($rebuild == $_SESSION['product'][$i]) {
            $html = $block;
            continue; // break if
            continue; // break if
            continue; // break for
          } else {
            $block = '';
          }
        }
        // Other products, ie, on checkout screen load..
        $html .= $block;
      }
    }
    return ($html ? str_replace(array(
      '{text}',
      '{cart_items}',
      '{url}',
      '{clear}',
      '{clear_text}',
      '{theme_folder}'
    ), array(
      str_replace('{count}', $this->cart->cartCount(), $public_checkout3),
      $html,
      $this->rwr->url(array('clearcart')),
      $public_checkout64,
      $msg_javascript45,
      THEME_FOLDER
    ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-wrapper.htm')) : '');
  }

  public function additionalCheckoutText($text) {
    if ($text) {
      return str_replace(array(
        '{text}',
        '{theme_folder}'
      ), array(
        mc_cleanData($text),
        THEME_FOLDER
      ), mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-item-add-text.htm'));
    }
    return $text;
  }

  public function addOrderToDatabase($table, $code, $gateway = true, $method = '', $invoice = '', $form) {
    global $GATEWAY;
    $weight = $this->cart->cartWeight();
    $ip     = mc_getRealIPAddr();
    $add    = array();
    $type   = 'personal';
    $tCode  = (isset($_SESSION[sha1(SECRET_KEY) . '_mc_mark_tracker']) ? mc_safeSQL($_SESSION[sha1(SECRET_KEY) . '_mc_mark_tracker']) : '');
    // If buyer has account, check type..
    if (isset($form['bill']['em']) && mswIsValidEmail($form['bill']['em'])) {
      $ACC = mc_getTableData('accounts', 'email', mc_safeSQL($form['bill']['em']), ' AND `enabled` = \'yes\'');
      if (isset($ACC->type) && in_array($ACC->type, array('personal','trade'))) {
        $type = $ACC->type;
        // Check track code..
        if ($tCode == '' && $ACC->trackcode) {
          $tCode = mc_safeSQL($ACC->trackcode);
        }
        $form['accountID'] = $ACC->id;
      }
    }
    // Current time..
    $currentTime = date('H:i:s');
    // Check for trade discount and override..
    if (defined('MC_TRADE_DISCOUNT') && MC_TRADE_DISCOUNT > 0) {
      $this->settings->globalDiscount = MC_TRADE_DISCOUNT;
    }
    // If wish list purchase, get recipient shipping information..
    if (!defined('MC_TRADE_DISCOUNT') && isset($form['wish']) && $form['wish'] > 0) {
      $RCPA = mc_getTableData('accounts', 'id', (int) $form['wish']);
      if (isset($RCPA->id)) {
        $addr = $this->account->getaddresses($form['wish']);
        $form['ship']  = array(
          'nm' => mc_cleanData($RCPA->name),
          'em' => mc_cleanData($RCPA->email),
          '1' => $form['ship'][1],
          '2' => (isset($addr[1]['addr2']) ? $addr[1]['addr2'] : ''),
          '3' => (isset($addr[1]['addr3']) ? $addr[1]['addr3'] : ''),
          '4' => (isset($addr[1]['addr4']) ? $addr[1]['addr4'] : ''),
          '5' => (isset($addr[1]['addr5']) ? $addr[1]['addr5'] : ''),
          '6' => (isset($addr[1]['addr6']) ? $addr[1]['addr6'] : ''),
          '7' => (isset($addr[1]['addr7']) ? $addr[1]['addr7'] : '')
        );
      }
    }
    mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . $table . "` (
    `invoiceNo`,
    `account`,
    `saleNotes`,
    `bill_1`,
    `bill_2`,
    `bill_3`,
    `bill_4`,
    `bill_5`,
    `bill_6`,
    `bill_7`,
    `bill_8`,
    `bill_9`,
    `ship_1`,
    `ship_2`,
    `ship_3`,
    `ship_4`,
    `ship_5`,
    `ship_6`,
    `ship_7`,
    `ship_8`,
    `buyerAddress`,
    `paymentStatus`,
    `gatewayID`,
    `taxPaid`,
    `taxRate`,
    `couponCode`,
    `couponTotal`,
    `codeType`,
    `subTotal`,
    `grandTotal`,
    `shipTotal`,
    `globalTotal`,
    `insuranceTotal`,
    `globalDiscount`,
    `manualDiscount`,
    `isPickup`,
    `shipSetCountry`,
    `shipSetArea`,
    `setShipRateID`,
    `shipType`,
    `cartWeight`,
    `purchaseDate`,
    `purchaseTime`,
    `buyCode`,
    `saleConfirmation`,
    `paymentMethod`,
    `ipAddress`,
    `trackcode`,
    `type`,
    `wishlist`,
    `platform`,
    `affcode`
    ) VALUES (
    '" . ($invoice ? $invoice : (!$gateway ? $this->gwmethod->getInvoiceNo() : '')) . "',
    '" . (isset($form['accountID']) ? (int) $form['accountID'] : '0') . "',
    '" . (isset($form['notes']) ? $form['notes'] : '') . "',
    '" . (isset($form['bill']['nm']) ? mc_safeSQL($form['bill']['nm']) : '') . "',
    '" . (isset($form['bill']['em']) ? mc_safeSQL($form['bill']['em']) : '') . "',
    '" . (isset($form['bill'][2]) ? mc_safeSQL($form['bill'][2]) : '') . "',
    '" . (isset($form['bill'][3]) ? mc_safeSQL($form['bill'][3]) : '') . "',
    '" . (isset($form['bill'][4]) ? mc_safeSQL($form['bill'][4]) : '') . "',
    '" . (isset($form['bill'][5]) ? mc_safeSQL($form['bill'][5]) : '') . "',
    '" . (isset($form['bill'][6]) ? mc_safeSQL($form['bill'][6]) : '') . "',
    '',
    '" . (isset($form['bill'][1]) ? (int) $form['bill'][1] : '0') . "',
    '" . (isset($form['ship']['nm']) ? mc_safeSQL($form['ship']['nm']) : '') . "',
    '" . (isset($form['ship']['em']) ? mc_safeSQL($form['ship']['em']) : '') . "',
    '" . (isset($form['ship'][2]) ? mc_safeSQL($form['ship'][2]) : '') . "',
    '" . (isset($form['ship'][3]) ? mc_safeSQL($form['ship'][3]) : '') . "',
    '" . (isset($form['ship'][4]) ? mc_safeSQL($form['ship'][4]) : '') . "',
    '" . (isset($form['ship'][5]) ? mc_safeSQL($form['ship'][5]) : '') . "',
    '" . (isset($form['ship'][6]) ? mc_safeSQL($form['ship'][6]) : '') . "',
    '" . (isset($form['ship'][7]) ? mc_safeSQL($form['ship'][7]) : '') . "',
    '',
    '" . ($method == 'free' ? 'completed' : (!$gateway ? 'pending' : '')) . "',
    '" . (isset($form['account']) && $form['account'] == 'yes' ? 'create-account' : '') . "',
    '" . (isset($_POST['t-tax']) ? mc_safeSQL($_POST['t-tax']) : '0.00') . "',
    '" . (isset($_POST['t-tax-rate']) ? mc_safeSQL($_POST['t-tax-rate']) : '0') . "',
    '" . (isset($form['coupon']) ? mc_safeSQL($form['coupon']) : '') . "',
    '" . (isset($_POST['t-coupon']) ? mc_safeSQL($_POST['t-coupon']) : '0.00') . "',
    '" . (isset($_SESSION['couponCode'][5]) ? mc_safeSQL($_SESSION['couponCode'][5]) : '') . "',
    '" . (isset($_POST['t-sub']) ? mc_safeSQL($_POST['t-sub']) : '0.00') . "',
    '" . (isset($_POST['t-total']) ? mc_safeSQL($_POST['t-total']) : '0.00') . "',
    '" . (isset($_POST['t-shipping']) ? mc_safeSQL($_POST['t-shipping']) : '0.00') . "',
    '" . (isset($_POST['t-global']) ? mc_safeSQL($_POST['t-global']) : '0.00') . "',
    '" . (isset($_POST['t-insurance']) ? mc_safeSQL($_POST['t-insurance']) : '0.00') . "',
    '" . $this->settings->globalDiscount . "',
    '0.00',
    '" . (isset($_SESSION['is-pick-up']) ? mc_safeSQL($_SESSION['is-pick-up']) : 'no') . "',
    '" . (isset($form['ship'][1]) ? (int) $form['ship'][1] : '0') . "',
    '" . (isset($_SESSION['shipto'][0]) ? mc_safeSQL($_SESSION['shipto'][0]) : '0') . "',
    '" . (isset($_SESSION['shipping-rate']) ? mc_safeSQL($_SESSION['shipping-rate']) : '0') . "',
    '" . (isset($_SESSION['shipping-type']) ? mc_safeSQL($_SESSION['shipping-type']) : 'weight') . "',
    '{$weight}',
    '" . date("Y-m-d") . "',
    '{$currentTime}',
    '{$code}',
    '" . (!$gateway ? 'yes' : 'no') . "',
    '{$method}',
    '{$ip}',
    '{$tCode}',
    '{$type}',
    '" . (!defined('MC_TRADE_DISCOUNT') && isset($form['wish']) ? (int) $form['wish'] : '0') . "',
    '" . mc_safeSQL((MC_PLATFORM_DETECTION == 'pc' ? 'desktop' : MC_PLATFORM_DETECTION)) . "',
    '" . (defined('MSW_AFF_SYS_KEY') ? mc_safeSQL((isset($_COOKIE[mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY)]) ? $_COOKIE[mc_encrypt(SECRET_KEY . MSW_AFF_SYS_KEY)] : '')) : '') . "'
    )") or die(mc_MySQLError(__LINE__, __FILE__));
    $id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
    //---------------------------
    // Add purchases..
    //---------------------------
    if (!isset($_SESSION['product'])) {
      return 0;
    }
    for ($i = 0; $i < count($_SESSION['product']); $i++) {
      if ($_SESSION['product'][$i] != 'void') {
        $t       = explode('-', $_SESSION['product'][$i]);
        $attSlot = 'attr-' . substr($_SESSION['product'][$i], 8);
        $pArray  = array();
        $aArray  = array();
        if (isset($t[0]) && isset($t[1])) {
          $addW = 0;
          // Did attributes adjust the weight?
          if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot])) {
            $addW = $this->cart->getAttributeData($_SESSION[$attSlot], $_SESSION['product'][$i], false, false, true);
          }
          switch($t[0]) {
            // GIFT CERTIFICATE..
            case 'gift':
              $G      = mc_getTableData('giftcerts', 'id', mc_digitSan($t[1]));
              $price  = mc_formatPrice($G->value);
              $weight = (0 + $addW);
              $marker = 'p' . mc_digitSan($t[1]);
              $iFS    = 'no';
              $global = '0.00';
              $d1     = $G->id . time() . rand(1111, 9999);
              $d2     = $G->id . date('dmYHis') . uniqid(rand(), 1);
              $pType  = 'virtual';
              $dCode  = '';
              $isDL   = 'no';
              $pdID   = '0';
              $gfID   = $G->id;
              break;
            // PRODUCT..
            default:
              $P      = mc_getTableData('products', 'id', mc_digitSan($t[1]));
              $price  = mc_formatPrice(($P->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? $P->pOffer : $P->pPrice));
              $weight = ($P->pWeight + $addW);
              $marker = 'p' . mc_digitSan($t[1]);
              $iFS    = $P->freeShipping;
              $global = ($_SESSION['global'][$i] > 0 ? $_SESSION['global'][$i] : '0.00');
              $d1     = $P->id . time() . rand(1111, 9999);
              $d2     = $P->id . date('dmYHis') . uniqid(rand(), 1);
              $pType  = ($P->pDownload == 'yes' ? 'download' : 'physical');
              $dCode  = ($P->pDownload == 'yes' ? $this->cart->generateDownloadCode($d1, $d2) : '');
              $isDL   = $P->pDownload;
              $pdID   = $P->id;
              $gfID   = '0';
              break;
          }
          mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purchases` (
          `purchaseDate`,
          `purchaseTime`,
          `saleID`,
          `productType`,
          `productID`,
          `giftID`,
          `categoryID`,
          `salePrice`,
          `productQty`,
          `persPrice`,
          `attrPrice`,
          `globalDiscount`,
          `globalCost`,
          `productWeight`,
          `liveDownload`,
          `downloadAmount`,
          `downloadCode`,
          `buyCode`,
          `saleConfirmation`,
          `freeShipping`,
          `wishpur`,
          `platform`
          ) VALUES (
          '" . date("Y-m-d") . "',
          '{$currentTime}',
          '{$id}',
          '{$pType}',
          '{$pdID}',
          '{$gfID}',
          '{$_SESSION['category'][$i]}',
          '{$price}',
          '{$_SESSION['quantity'][$i]}',
          '0.00',
          '0.00',
          '{$this->settings->globalDiscount}',
          '{$global}',
          '{$weight}',
          '{$isDL}',
          '0',
          '{$dCode}',
          '{$code}',
          '" . (!$gateway ? 'yes' : 'no') . "',
          '{$iFS}',
          '{$_SESSION['wishlist'][$i]}',
          '" . mc_safeSQL((MC_PLATFORM_DETECTION == 'pc' ? 'desktop' : MC_PLATFORM_DETECTION)) . "'
          )") or die(mc_MySQLError(__LINE__, __FILE__));
          $lastPurchaseID = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
          // Add personalisation..
          if (isset($_SESSION[$_SESSION['product'][$i]]) && $t[0] != 'gift') {
            foreach ($_SESSION[$_SESSION['product'][$i]] AS $k => $v) {
              $split = explode('|-<>-|', $v);
              $PER   = mc_getTableData('personalisation', 'id', $split[0]);
              $ac    = (isset($PER->persAddCost) && $PER->persAddCost > 0 ? mc_formatPrice($PER->persAddCost) : '0.00');
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_pers` (
              `saleID`,
              `productID`,
              `purchaseID`,
              `personalisationID`,
              `visitorData`,
              `addCost`
              ) VALUES (
              '{$id}',
              '{$P->id}',
              '{$lastPurchaseID}',
              '{$split[0]}',
              '" . mc_safeSQL($split[1]) . "',
              '{$ac}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              $pArray[] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
            }
          }
          // Add attributes..
          if (isset($_SESSION[$attSlot]) && !empty($_SESSION[$attSlot]) && $t[0] != 'gift') {
            foreach ($_SESSION[$attSlot] AS $v) {
              $ATT = mc_getTableData('attributes', 'id', $v);
              $ac  = (isset($ATT->attrCost) && $ATT->attrCost > 0 ? mc_formatPrice($ATT->attrCost) : '0.00');
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "purch_atts` (
              `saleID`,
              `productID`,
              `purchaseID`,
              `attributeID`,
              `addCost`,
              `attrName`,
              `attrWeight`
              ) VALUES (
              '{$id}',
              '{$P->id}',
              '{$lastPurchaseID}',
              '{$v}',
              '{$ac}',
              '" . mc_safeSQL($ATT->attrName) . "',
              '{$ATT->attrWeight}'
              )") or die(mc_MySQLError(__LINE__, __FILE__));
              $aArray[] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___msw_sqli"]))) ? false : $___mysqli_res);
            }
          }
          // Add gift certificates to database..
          if ($t[0] == 'gift') {
            for ($c = 0; $c < $_SESSION['quantity'][$i]; $c++) {
              mysqli_query($GLOBALS["___msw_sqli"], "INSERT INTO `" . DB_PREFIX . "giftcodes` (
              `saleID`,
              `purchaseID`,
              `giftID`,
              `code`,
              `value`,
              `redeemed`,
              `from_name`,
              `from_email`,
              `to_name`,
              `to_email`,
              `message`,
              `dateAdded`,
              `notes`,
              `enabled`,
              `active`
              ) VALUES (
              '{$id}',
              '{$lastPurchaseID}',
              '{$gfID}',
              '',
              '{$price}',
              '0.00',
              '" . (isset($_SESSION['giftAddr'][$i]['from_name']) ? mc_safeSQL($_SESSION['giftAddr'][$i]['from_name']) : '') . "',
              '" . (isset($_SESSION['giftAddr'][$i]['from_email']) ? mc_safeSQL($_SESSION['giftAddr'][$i]['from_email']) : '') . "',
              '" . (isset($_SESSION['giftAddr'][$i]['to_name']) ? mc_safeSQL($_SESSION['giftAddr'][$i]['to_name']) : '') . "',
              '" . (isset($_SESSION['giftAddr'][$i]['to_email']) ? mc_safeSQL($_SESSION['giftAddr'][$i]['to_email']) : '') . "',
              '" . (isset($_SESSION['giftAddr'][$i]['message']) ? mc_safeSQL($_SESSION['giftAddr'][$i]['message']) : '') . "',
              '" . date("Y-m-d") . "',
              '',
              'no',
              'no'
		          )") or die(mc_MySQLError(__LINE__, __FILE__));
            }
          }
          // Update personalisation and attribute prices in purchases table..
          $sums = array(
            '0.00',
            '0.00'
          );
          if (!empty($pArray)) {
            $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`addCost`) AS `sum` FROM `" . DB_PREFIX . "purch_pers`
                 WHERE `id` IN(" . mc_safeSQL(implode(',', $pArray)) . ")
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
            $A       = mysqli_fetch_object($q);
            $sums[0] = (isset($A->sum) ? $A->sum : '0.00');
          }
          if (!empty($aArray)) {
            $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`addCost`) AS `sum` FROM `" . DB_PREFIX . "purch_atts`
                 WHERE `id` IN(" . mc_safeSQL(implode(',', $aArray)) . ")
                 ") or die(mc_MySQLError(__LINE__, __FILE__));
            $B       = mysqli_fetch_object($q);
            $sums[1] = (isset($B->sum) ? $B->sum : '0.00');
          }
          mysqli_query($GLOBALS["___msw_sqli"], "UPDATE `" . DB_PREFIX . "purchases` SET
          `persPrice`  = '" . mc_formatPrice($sums[0] * $_SESSION['quantity'][$i]) . "',
          `attrPrice`  = '" . mc_formatPrice($sums[1] * $_SESSION['quantity'][$i]) . "'
          WHERE `id`   = '{$lastPurchaseID}'
          ") or die(mc_MySQLError(__LINE__, __FILE__));
        }
      }
    }
    return $id;
  }

  public function gateways($arr, $selected = '', $raw = 'no', $type = 'all') {
    $html   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-payment-methods.htm');
    $wrap   = mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-payment-methods-wrapper.htm');
    $blank  = str_replace(array('{type}','{selected}','{name}'),array(0,' disabled="disabled"','- - - - - - -'),mc_loadTemplateFile(PATH . THEME_FOLDER . '/html/basket-checkout/basket-payment-methods.htm'));
    $build  = '';
    $build2 = '';
    $first  = 0;
    $data   = array();
    if (!empty($arr) && is_array($arr)) {
      foreach ($arr AS $k => $v) {
        $vtyp = explode(',', $v['viewtype']);
        if ($v['enable'] == 'yes' && ($v['viewtype'] == 'a' || in_array(substr($type, 0, 1), $vtyp))) {
          if ($v['default'] == 'yes') {
            $selected = $k;
            // For raw data, we can finish here..
            if ($raw == 'yes') {
              return array($k);
            }
          }
          if (in_array($k, array('bank','cheque','cod','phone','account'))) {
            $build2 .= str_replace(array(
              '{image}',
              '{name}',
              '{url}',
              '{type}',
              '{selected}'
            ), array(
              THEME_FOLDER . '/images/methods/' . $v['img'],
              $v['lang'],
              $this->rwr->url(array(
                $this->rwr->config['slugs']['hlp'] . '/' . $k,
                'help=' . $k
              )),
              $k,
              ($selected && $selected == $k ? ' selected="selected"' : (++$first == 1 ? ' selected="selected"' : ''))
            ), $html);
          } else {
            $build .= str_replace(array(
              '{image}',
              '{name}',
              '{url}',
              '{type}',
              '{selected}'
            ), array(
              THEME_FOLDER . '/images/methods/' . $v['img'],
              $v['lang'],
              $this->rwr->url(array(
                $this->rwr->config['slugs']['hlp'] . '/' . $k,
                'help=' . $k
              )),
              $k,
              ($selected && $selected == $k ? ' selected="selected"' : (++$first == 1 ? ' selected="selected"' : ''))
            ), $html);
          }
          $data[] = $k;
        }
      }
    }
    if ($raw == 'yes') {
      return $data;
    }
    return ($build || $build2 ? str_replace('{methods}', $build . ($build2 ? ($build ? $blank : '') . $build2 : ''), $wrap) : '');
  }

  public function checkCountryRestriction($country) {
    $rest   = array();
    $restID = array();
    if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0) {
      for ($i = 0; $i < count($_SESSION['product']); $i++) {
        if ($_SESSION['product'][$i] != 'void' && $_SESSION['killzone'][$i] != 'none') {
          $pcRes = unserialize($_SESSION['killzone'][$i]);
          if (is_array($pcRes) && in_array($country, $pcRes)) {
            $chop     = explode('-', $_SESSION['product'][$i]);
            $rest[]   = 'bsk-' . $_SESSION['product'][$i];
            $restID[] = $chop[1];
          }
        }
      }
    }
    return array(
      $rest,
      $restID
    );
  }

  public function productList($arr) {
    $html = array();
    if (!empty($arr)) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `pName`
           FROM `" . DB_PREFIX . "products`
           WHERE `id` IN(" . mc_safeSQL(implode(',', $arr)) . ")
           ORDER BY `pName`
           ");
      while ($P = mysqli_fetch_object($q)) {
        $html[] = '<i class="fa fa-warning fa-fw"></i> ' . mc_safeHTML($P->pName);
      }
    }
    return (!empty($html) ? implode('<br>', $html) : '');
  }

  public function defaults() {
  }

  public function getaddresses() {
    $addr = array(
      'bill' => array(
        'addr1' => (isset($_SESSION['mc_checkout']['bill'][1]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][1]) : $this->settings->shipCountry),
        'addr2' => (isset($_SESSION['mc_checkout']['bill'][2]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][2]) : ''),
        'addr3' => (isset($_SESSION['mc_checkout']['bill'][3]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][3]) : ''),
        'addr4' => (isset($_SESSION['mc_checkout']['bill'][4]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][4]) : ''),
        'addr5' => (isset($_SESSION['mc_checkout']['bill'][5]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][5]) : ''),
        'addr6' => (isset($_SESSION['mc_checkout']['bill'][6]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][6]) : ''),
        'addr7' => (isset($_SESSION['mc_checkout']['bill'][7]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][7]) : ''),
        'addr8' => (isset($_SESSION['mc_checkout']['bill'][8]) ? mc_safeHTML($_SESSION['mc_checkout']['bill'][8]) : ''),
        'nm'    => (isset($_SESSION['mc_checkout']['bill']['nm']) ? mc_safeHTML($_SESSION['mc_checkout']['bill']['nm']) : ''),
        'em'    => (isset($_SESSION['mc_checkout']['bill']['em']) ? mc_safeHTML($_SESSION['mc_checkout']['bill']['em']) : '')
      ),
      'ship' => array(
        'addr1' => (isset($_SESSION['mc_checkout']['ship'][1]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][1]) : $this->settings->shipCountry),
        'addr2' => (isset($_SESSION['mc_checkout']['ship'][2]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][2]) : ''),
        'addr3' => (isset($_SESSION['mc_checkout']['ship'][3]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][3]) : ''),
        'addr4' => (isset($_SESSION['mc_checkout']['ship'][4]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][4]) : ''),
        'addr5' => (isset($_SESSION['mc_checkout']['ship'][5]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][5]) : ''),
        'addr6' => (isset($_SESSION['mc_checkout']['ship'][6]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][6]) : ''),
        'addr7' => (isset($_SESSION['mc_checkout']['ship'][7]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][7]) : ''),
        'addr8' => (isset($_SESSION['mc_checkout']['ship'][8]) ? mc_safeHTML($_SESSION['mc_checkout']['ship'][8]) : ''),
        'nm'    => (isset($_SESSION['mc_checkout']['ship']['nm']) ? mc_safeHTML($_SESSION['mc_checkout']['ship']['nm']) : ''),
        'em'    => (isset($_SESSION['mc_checkout']['ship']['em']) ? mc_safeHTML($_SESSION['mc_checkout']['ship']['em']) : '')
      )
    );
    // Try and get billing name / email from first gift cert if no-one is logged in, but gift certs exist..
    if ($addr['bill']['nm'] == '') {
      if (isset($_SESSION['giftAddr'][0]['from_name']) && $_SESSION['product'][0] != 'void') {
        $addr['bill']['nm'] = mc_safeHTML($_SESSION['giftAddr'][0]['from_name']);
        $addr['bill']['em'] = mc_safeHTML($_SESSION['giftAddr'][0]['from_email']);
      }
    }
    return array(
      $addr['bill'],
      $addr['ship']
    );
  }

}

?>