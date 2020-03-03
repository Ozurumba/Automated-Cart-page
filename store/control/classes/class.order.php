<?php

class mcOrder {

  public $settings;
  public $order;
  public $products;
  public $rwr;
  public $perms = array(
    'guest' => 'no',
    'show-dl' => 'yes',
    'account' => 0
  );
  public $incsale = 'no';
  public $htmltags = array();

  public function shipped($tmpl = array('view-order/shipped.htm','view-order/attributes.htm','view-order/personalised.htm'), $lb = '<br>', $iprice = false) {
    $html  = array();
    if (defined('ADMIN_PANEL')) {
      $tmp   = mc_loadTemplateFile($tmpl[0]);
      $tmpa  = mc_loadTemplateFile($tmpl[1]);
      $tmpp  = mc_loadTemplateFile($tmpl[2]);
    } else {
      $tmp   = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl[0]);
      $tmpa  = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl[1]);
      $tmpp  = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl[2]);
    }
    if (isset($_GET['incl-sale']) || $this->incsale == 'yes') {
      $saleConf = 'AND (`saleConfirmation` = \'no\' OR `saleConfirmation` = \'yes\')';
    } else {
      $saleConf = 'AND `saleConfirmation` = \'yes\'';
    }
    $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
            `" . DB_PREFIX . "products`.`id` AS `pid`,
            `" . DB_PREFIX . "purchases`.`id` AS `pcid`
            FROM `" . DB_PREFIX . "purchases`
            LEFT JOIN `" . DB_PREFIX . "products`
            ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
            WHERE `saleID`                        = '{$this->order->id}'
            AND `productType`                     = 'physical'
            $saleConf
            ORDER BY `" . DB_PREFIX . "purchases`.`id`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q) > 0) {
      while ($PHYS = mysqli_fetch_object($q)) {
        $code         = ($PHYS->pCode ? $PHYS->pCode : 'N/A');
        $PHYS->pName  = ($PHYS->pName ? $PHYS->pName : $PHYS->deletedProductName);
        $pers_Price   = '0.00';
        $attr         = mc_saleAttributes(mc_digitSan($this->order->id),$PHYS->pcid,$PHYS->pid,false,0,true,true);
        $psld         = array();
        $url          = '';
        if (defined('VIEW_ACC')) {
          $url          = $this->rwr->url(array(
            $this->rwr->config['slugs']['prd'] . '/' . $PHYS->pid . '/' . ($PHYS->rwslug ? $PHYS->rwslug : $this->rwr->title($PHYS->pName)),
            'pd=' . $PHYS->pid
          ));
        }
        if (!empty($attr)) {
          $item  = ($code ? '[' . $code . '] ' : '') . (defined('VIEW_ACC') ? '<a href="' . $url . '" onclick="window.open(this);return false">' : '') . mc_safeHTML($PHYS->pName) . (defined('VIEW_ACC') ? '</a>' : '') . ($iprice ? ' = ' . mc_currencyFormat(mc_formatPrice($PHYS->salePrice,true)) : '');
          $item .= str_replace('{attributes}', implode($lb, $attr), $tmpa);
        } else {
          $item  = ($code ? '[' . $code . '] ' : '') . (defined('VIEW_ACC') ? '<a href="' . $url . '" onclick="window.open(this);return false">' : '') . mc_safeHTML($PHYS->pName) . (defined('VIEW_ACC') ? '</a>' : '');
        }
        // Personalised items..
        $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
                WHERE `purchaseID` = '{$PHYS->pcid}'
                ORDER BY `id`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
        if (mysqli_num_rows($q_ps)>0) {
          while ($PS = mysqli_fetch_object($q_ps)) {
            $PERSONALISED  = mc_getTableData('personalisation','id',$PS->personalisationID);
            if ($PS->visitorData && $PS->visitorData != 'no-option-selected') {
              $psld[] = '<i class="fa fa-angle-right fa-fw"></i> ' . mc_persTextDisplay(mc_safeHTML($PERSONALISED->persInstructions),true).($PERSONALISED->persAddCost>0 ? ' (+'.mc_currencyFormat(mc_formatPrice($PERSONALISED->persAddCost)).')' : '').': ' . mc_safeHTML($PS->visitorData);
            }
          }
          if (!empty($psld)) {
            $item .= str_replace('{personalised}', implode($lb, $psld), $tmpp);
          }
          $pers_Price = ($PHYS->persPrice>0 ? mc_formatPrice($PHYS->persPrice) : '0.00');
        }
        $html[] = str_replace(
          array('{item}','{qty}','{calc}','{total}','{unit}'),
          array(
            $item,
            $PHYS->productQty,
            mc_currencyFormat(mc_formatPrice($PHYS->salePrice,true)) . ($pers_Price > 0 || $PHYS->attrPrice > 0 ? ' + ' . mc_currencyFormat(mc_formatPrice($pers_Price + $PHYS->attrPrice,true)) : ''),
            mc_currencyFormat(mc_formatPrice(($PHYS->productQty*$PHYS->salePrice) + ($pers_Price + $PHYS->attrPrice),true)),
            mc_currencyFormat(mc_formatPrice($PHYS->salePrice + $pers_Price + $PHYS->attrPrice,true))
          ),
          $tmp
        );
      }
    }
    return (!empty($html) ? implode(mc_defineNewline(), $html) : '');
  }

  public function gift($tmpl = array('view-order/gift-certs.htm')) {
    $html = array();
    if (defined('ADMIN_PANEL')) {
      $tmp  = mc_loadTemplateFile($tmpl[0]);
    } else {
      $tmp  = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl[0]);
    }
    if (isset($_GET['incl-sale']) || $this->incsale == 'yes') {
      $saleConf = 'AND (`saleConfirmation` = \'no\' OR `saleConfirmation` = \'yes\')';
    } else {
      $saleConf = 'AND `saleConfirmation` = \'yes\'';
    }
    $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
            `" . DB_PREFIX . "products`.`id` AS `pid`,
            `" . DB_PREFIX . "purchases`.`id` AS `pcid`
            FROM `" . DB_PREFIX . "purchases`
            LEFT JOIN `" . DB_PREFIX . "products`
            ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
            WHERE `saleID`                         = '{$this->order->id}'
            AND `productType`                      = 'virtual'
            $saleConf
            ORDER BY `" . DB_PREFIX . "purchases`.`id`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q) > 0) {
      while ($VUTL = mysqli_fetch_object($q)) {
        $GIFT  = mc_getTableData('giftcerts','id',$VUTL->giftID);
        $url   = '';
        if (defined('VIEW_ACC')) {
          $url   = $this->rwr->url(array('gift'));
        }
        $item  = (defined('VIEW_ACC') ? '<a href="' . $url . '" onclick="window.open(this);return false">' : '') . (isset($GIFT->name) ? $GIFT->name : $VUTL->deletedProductName) . (defined('VIEW_ACC') ? '</a>' : '');
        $html[] = str_replace(
          array('{item}','{qty}','{calc}','{total}','{gift}','{unit}'),
          array(
            $item,
            $VUTL->productQty,
            mc_currencyFormat(mc_formatPrice($VUTL->salePrice,true)),
            mc_currencyFormat(mc_formatPrice($VUTL->productQty * $VUTL->salePrice,true)),
            mc_saleGiftCerts(mc_digitSan($this->order->id),$VUTL->pcid),
            mc_currencyFormat(mc_formatPrice($VUTL->salePrice,true))
          ),
          $tmp
        );
      }
    }
    return (!empty($html) ? implode(mc_defineNewline(), $html) : '');
  }

  public function downloads($tmpl = array('view-order/downloads.htm','view-order/guest-downloads.htm')) {
    $html = array();
    if ($this->perms['show-dl'] == 'no') {
      $tmpl = array('view-order/no-wish-download.htm','view-order/no-wish-download.htm');
    }
    if (in_array($this->order->paymentMethod, array('bank','cod','cheque','phone')) && $this->order->paymentStatus == 'pending') {
      $tmpl = array('view-order/pending-download.htm','view-order/pending-download.htm');
    }
    if (defined('ADMIN_PANEL')) {
      $tmp = mc_loadTemplateFile($tmpl[0]);
    } else {
      $tmp = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . ($this->perms['guest'] == 'yes' ? $tmpl[1] : $tmpl[0]));
    }
    if (isset($_GET['incl-sale']) || $this->incsale == 'yes') {
      $saleConf = 'AND (`saleConfirmation` = \'no\' OR `saleConfirmation` = \'yes\')';
    } else {
      $saleConf = 'AND `saleConfirmation` = \'yes\'';
    }
    $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
            `" . DB_PREFIX . "products`.`id` AS `pid`,
            `" . DB_PREFIX . "purchases`.`id` AS `pcid`
            FROM `" . DB_PREFIX . "purchases`
            LEFT JOIN `" . DB_PREFIX . "products`
            ON `" . DB_PREFIX . "purchases`.`productID`  = `" . DB_PREFIX . "products`.`id`
            WHERE `saleID`                        = '{$this->order->id}'
            AND `productType`                     = 'download'
            $saleConf
            ORDER BY `" . DB_PREFIX . "purchases`.`id`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q) > 0) {
      while ($DOWN = mysqli_fetch_object($q)) {
        $code         = ($DOWN->pCode ? $DOWN->pCode : 'N/A');
        $DOWN->pName  = ($DOWN->pName ? $DOWN->pName : $DOWN->deletedProductName);
        $url          = '';
        if (defined('VIEW_ACC')) {
          $url          = $this->rwr->url(array(
            $this->rwr->config['slugs']['prd'] . '/' . $DOWN->pid . '/' . ($DOWN->rwslug ? $DOWN->rwslug : $this->rwr->title($DOWN->pName)),
            'pd=' . $DOWN->pid
          ));
        }
        $item         = ($code ? '[' . $code . '] ' : '') . (defined('VIEW_ACC') ? '<a href="' . $url . '" onclick="window.open(this);return false">' : '') . mc_safeHTML($DOWN->pName) . (defined('VIEW_ACC') ? '</a>' : '');
        $html[] = str_replace(
          array('{item}','{qty}','{calc}','{total}','{id}','{code}','{sid}','{unit}'),
          array(
            $item,
            $DOWN->productQty,
            mc_currencyFormat(mc_formatPrice($DOWN->salePrice,true)),
            mc_currencyFormat(mc_formatPrice($DOWN->productQty * $DOWN->salePrice,true)),
            $DOWN->pcid,
            $this->order->buyCode,
            $this->order->id,
            mc_currencyFormat(mc_formatPrice($DOWN->salePrice,true))
          ),
          $tmp
        );
      }
    }
    return (!empty($html) ? implode(mc_defineNewline(), $html) : '');
  }

  public function address($type, $lb = '<br>') {
    global $public_accounts_view_order;
    $html = array();
    switch($type) {
      case 'bill':
        foreach (array(1,3,4,5,6,7,9,2) AS $b) {
          $f = 'bill_' . $b;
          if (isset($this->order->{$f}) && $this->order->{$f}) {
            $html[] = ($b == 9 ? mc_getShippingCountry($this->order->{$f}) : ($b == 2 ? $lb . $public_accounts_view_order[9] : '') . mc_safeHTML($this->order->{$f}));
          }
        }
        break;
      case 'ship':
        foreach (array(1,3,4,5,6,7,9,2,8) AS $s) {
          switch($s) {
            case 9:
              if ($this->order->shipSetCountry > 0) {
                $html[] = mc_getShippingCountry($this->order->shipSetCountry);
              }
              break;
            default:
              $f = 'ship_' . $s;
              if (isset($this->order->{$f}) && $this->order->{$f}) {
                $html[] = ($s == 2 ? $lb . $public_accounts_view_order[9] : '') . ($s == 8 ? $public_accounts_view_order[10] : '') . mc_safeHTML($this->order->{$f});
              }
              break;
          }
        }
        // If count is less than 3, this order has no shipping details (possibly download only order), so reset
        if (count($html) < 3) {
          $html = array();
        }
        break;
    }
    return (!empty($html) ? implode($lb, $html) : '');
  }

  public function info($lb = '<br>', $tmpl = 'view-order/info-item.htm') {
    global $msg_invoice3, $msg_invoice4, $msg_invoice30, $msg_invoice19, $msg_invoice33, $mc_global, $msg_javascript214, $msg_script5,
           $msg_script6, $public_accounts_history;
    $html   = array();
    if (defined('ADMIN_PANEL')) {
      $tmp    = mc_loadTemplateFile($tmpl);
    } else {
      $tmp    = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl);
    }
    $html[] = str_replace(array('{info}','{value}'),array($msg_invoice3, $this->order->pdate), $tmp);
    if (!defined('ADMIN_PANEL') && in_array($this->order->paymentMethod, array('bank','cod','cheque','phone')) && in_array($this->order->paymentStatus, array('pending','shipping'))) {
      $url = $this->rwr->url(array(
        $this->rwr->config['slugs']['ppi'] . '/' . $this->order->id . '/' . $this->order->paymentMethod,
        'pinfo=' . $this->order->id . '&pm=' . $this->order->paymentMethod
      ));
      if (defined('VIEW_GUEST') || (defined('VIEW_ACC') && $this->order->wishlist == 0)) {
        $html[] = str_replace(array('{info}','{value}'),array($msg_invoice4, mc_paymentMethodName($this->order->paymentMethod) . ' <a href="' . $url . '" onclick="mc_Window(this.href, 450, 500, \'\');return false"><i class="fa fa-info-circle fa-fw"></i></a>'), $tmp);
      } else {
        if (defined('VIEW_ACC') && $this->order->wishlist > 0) {
          $html[] = str_replace(array('{info}','{value}'),array($msg_invoice4, $public_accounts_history[7]), $tmp);
        }
      }
    } else {
      if (defined('VIEW_GUEST') || (defined('VIEW_ACC') && $this->order->wishlist == 0)) {
        $html[] = str_replace(array('{info}','{value}'),array($msg_invoice4, mc_paymentMethodName($this->order->paymentMethod)), $tmp);
      } else {
        if (defined('VIEW_ACC') && $this->order->wishlist > 0) {
          $html[] = str_replace(array('{info}','{value}'),array($msg_invoice4, $public_accounts_history[7]), $tmp);
        }
      }
    }
    if ($this->order->setShipRateID > 0 && in_array($this->order->shipType,array('weight'))) {
      $html[] = str_replace(array('{info}','{value}'),array($msg_invoice30, mc_getShippingService(mc_getShippingServiceFromRate($this->order->setShipRateID))), $tmp);
    }
    if ($this->order->couponCode) {
      $html[] = str_replace(array('{info}','{value}'),array($msg_invoice19, $msg_script5), $tmp);
    }
    if ($this->order->insuranceTotal > 0) {
      $html[] = str_replace(array('{info}','{value}'),array($msg_invoice33, $msg_script5), $tmp);
    }
    if ($this->order->isPickup == 'yes') {
      $html[] = str_replace(array('{info}','{value}'),array($msg_javascript214, $msg_script5), $tmp);
    }
    if ($this->order->ipAddress) {
      $html[] = str_replace(array('{info}','{value}'),array($mc_global[4], $this->order->ipAddress), $tmp);
    }
    return implode($lb, $html);
  }

  public function totals($tmpl = 'view-order/total.htm', $skip = array()) {
    global $msg_invoice28, $msg_invoice27, $msg_invoice17, $msg_invoice19, $msg_invoice36, $msg_view_order,
    $msg_invoice20, $msg_invoice21, $msg_invoice35, $msg_invoice33, $msg_invoice22, $msg_admin_invoice3_0;
    $html = array();
    if (defined('ADMIN_PANEL')) {
      $tmp  = mc_loadTemplateFile($tmpl);
    } else {
      $tmp  = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl);
    }
    $tot  = array();
    if (!in_array('subtotal', $skip)) {
      $tot[] = array(
        'text' => $msg_invoice17,
        'cost' => mc_currencyFormat(mc_formatPrice($this->order->subTotal,true))
      );
    }
    // Gift / discount total..
    if ($this->order->globalDiscount > 0) {
      $tot[] = array(
        'text' => str_replace('{percentage}',$this->order->globalDiscount,($this->order->type == 'trade' ? $msg_admin_invoice3_0[7] : $msg_invoice27)),
        'cost' => '- ' . mc_currencyFormat(mc_formatPrice($this->order->globalTotal,true))
      );
    } elseif ($this->order->manualDiscount > 0) {
      $tot[] = array(
        'text' => $msg_invoice28,
        'cost' => '- ' . mc_currencyFormat(mc_formatPrice($this->order->manualDiscount,true))
      );
    } else {
      // Discount Coupon...
      if ($this->order->couponTotal > 0 && $this->order->codeType == 'discount') {
        $COUPON  = mc_getTableData('coupons','cDiscountCode',$this->order->couponCode);
        if (isset($COUPON->cCampaign)) {
          $CAMP  = mc_getTableData('campaigns','id',$COUPON->cCampaign);
          $tot[] = array(
            'text' => $msg_invoice19,
            'cost' => '- ' . mc_currencyFormat($this->products->getDiscount(mc_formatPrice($this->order->subTotal),$CAMP->cDiscount))
          );
        }
      }
      // Gift Certificate...
      if ($this->order->couponTotal > 0 && $this->order->codeType == 'gift') {
        $GIFT = mc_getTableData('giftcodes','code',$this->order->couponCode);
        if (isset($GIFT->id)) {
          $tot[] = array(
            'text' => $msg_invoice36,
            'cost' => '- ' . mc_currencyFormat($this->products->getDiscount(mc_formatPrice($this->order->subTotal),$this->order->couponTotal))
          );
        }
      }
    }
    // Shipping and tax..
    if ($this->order->shipSetArea > 0) {
      // If tax isn`t applied to shipping, show tax before shipping..
      $A  = mc_getTableData('zone_areas','id',$this->order->shipSetArea,'','inZone');
      if (isset($A->inZone)) {
        $Z  = mc_getTableData('zones','id',$A->inZone,'','zShipping');
      }
      // Tax on shipping as well as total..
      if (isset($Z->zShipping) && $Z->zShipping == 'yes') {
        if ($this->order->shipTotal>0) {
          $tot[] = array(
            'text' => $msg_invoice20,
            'cost' => ($this->order->shipTotal > 0 ? mc_currencyFormat(mc_formatPrice($this->order->shipTotal,true)) : $msg_invoice35)
          );
        }
        if ($this->order->taxPaid > 0) {
          $tot[] = array(
            'text' => str_replace('{rate}',$this->order->taxRate . '%',$msg_invoice21),
            'cost' => mc_currencyFormat(mc_formatPrice($this->order->taxPaid,true))
          );
        }
      } else {
        // Tax on total, not shipping..
        if ($this->order->taxPaid > 0) {
          $tot[] = array(
            'text' => str_replace('{rate}',$this->order->taxRate.'%',$msg_invoice21),
            'cost' => mc_currencyFormat(mc_formatPrice($this->order->taxPaid,true))
          );
        }
        if ($this->order->shipTotal > 0) {
          $tot[] = array(
            'text' => $msg_invoice20,
            'cost' => mc_currencyFormat(mc_formatPrice($this->order->shipTotal,true))
          );
        }
      }
    } else {
      // If no areas are on display, but shipping does exist, show it..
      if ($this->order->shipTotal > 0) {
        $tot[] = array(
          'text' => $msg_invoice20,
          'cost' => mc_currencyFormat(mc_formatPrice($this->order->shipTotal,true))
        );
      }
    }
    // Insurance..
    if ($this->order->insuranceTotal > 0) {
      $tot[] = array(
        'text' => $msg_invoice33,
        'cost' => mc_currencyFormat(mc_formatPrice($this->order->insuranceTotal,true))
      );
    }
    // Additional charges..
    if ($this->order->chargeTotal > 0) {
      $tot[] = array(
        'text' => $msg_view_order[4],
        'cost' => mc_currencyFormat(mc_formatPrice($this->order->chargeTotal,true))
      );
    }
    $tot[] = array(
      'text' => $msg_invoice22,
      'cost' => mc_currencyFormat(mc_formatPrice($this->order->grandTotal,true))
    );
    // Loop and return..
    for ($i=0; $i<count($tot); $i++) {
      $html[] = str_replace(array('{text}','{total}'),array($tot[$i]['text'],$tot[$i]['cost']),$tmp);
    }
    return array(
      implode(mc_defineNewline(), $html),
      count($tot)
    );
  }

  public function statuses($tmpl = 'view-order/status.htm') {
    $html = array();
    $sql  = '';
    if (defined('ADMIN_PANEL')) {
      $tmp  = mc_loadTemplateFile($tmpl);
    } else {
      $tmp  = mc_loadTemplateFile(GLOBAL_PATH . THEME_FOLDER . '/html/' . $tmpl);
      $sql  = 'AND `account` = \'' . $this->perms['account'] . '\'';
    }
    $q    = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`dateAdded`,'" . $this->settings->mysqlDateFormat . "') AS `sdate`
            FROM `" . DB_PREFIX . "statuses`
            WHERE `saleID` = '{$this->order->id}'
            AND `visacc`   = 'yes'
            $sql
            ORDER BY `id` DESC
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q) > 0) {
      while ($ST = mysqli_fetch_object($q)) {
        $html[] = str_replace(
          array('{status}','{date}','{time}','{text}'),
          array(
            mc_statusText($ST->orderStatus),
            $ST->sdate,
            $ST->timeAdded,
            mc_NL2BR(mc_cleanCustomTags(mc_safeHTML($ST->statusNotes), $this->htmltags))
          ),
          $tmp
        );
      }
    }
    return (!empty($html) ? implode(mc_defineNewline(), $html) : '');
  }

}

?>