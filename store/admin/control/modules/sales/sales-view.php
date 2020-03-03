<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-view.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-related.php');

// Stock adjustment.
if(isset($_GET['stock_adj'])) {
  if (isset($_POST['process'])) {
    $c1 = (!empty($_POST['p']) ? count($_POST['p']) : '0');
    $c2 = (!empty($_POST['a']) ? count($_POST['a']) : '0');
    $MCSALE->saleItemStockAdj();
    $OK = true;
  }
  $pageTitle  = $msg_admin_viewsale3_0[24] . ': ' . $pageTitle;
  $loadiBox   = true;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-products-stock-adjustment.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// View status history..
if (isset($_GET['status_view'])) {
  include(PATH . 'templates/windows/product-status-history.php');
  exit;
}

// Update IP addresses..
if (isset($_GET['saveIP'])) {
  $MCSALE->updateSaleIPAccess();
  echo $JSON->encode(array(
    'OK'
  ));
  exit;
}

// Resend gift certificates..
if (isset($_GET['resendGiftCert'])) {
  if (isset($_GET['ok'])) {
    $winMSG = $msg_admin3_0[24];
    include(PATH . 'templates/windows/ok.php');
  } else {
    include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
    include(REL_PATH . 'control/classes/class.products.php');
    include(REL_PATH . 'control/classes/class.cart.php');
    include(REL_PATH . 'control/classes/class.gift.php');
    $sale   = (int) $_GET['resendGiftCert'];
    $pur    = (int) $_GET['purID'];
    $gift   = (int) $_GET['gift'];
    $MCGIFT = new giftCertificate();
    $q_cert = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes`
              WHERE `saleID`   = '{$sale}'
              AND `purchaseID` = '{$pur}'
              AND `id`         = '{$gift}'
              ORDER BY `id`
              ");
    $GIFT_CERTS = mysqli_fetch_object($q_cert);
    if (isset($GIFT_CERTS->id)) {
      // Create code if it doesn`t exist..
      $giftCode = ($GIFT_CERTS->code ? $GIFT_CERTS->code : $MCGIFT->codeCreator($GIFT_CERTS->id));
      // Activate if not activated..
      $MCGIFT->activateCertificate($giftCode, $GIFT_CERTS->id);
      // Mail tags..
      $MCMAIL->addTag('{TO_NAME}', $GIFT_CERTS->to_name);
      $MCMAIL->addTag('{FROM_NAME}', $GIFT_CERTS->from_name);
      $MCMAIL->addTag('{CURRENCY}', $SETTINGS->baseCurrency);
      $MCMAIL->addTag('{VALUE}', $GIFT_CERTS->value);
      $MCMAIL->addTag('{GIFT_CODE}', $giftCode);
      $MCMAIL->addTag('{CUSTOM_MESSAGE}', ($GIFT_CERTS->message ? $GIFT_CERTS->message : $public_checkout137));
      // Send..
      $msg = LANG_PATH . 'gift-certificate.txt';
      $sbj = str_replace(array(
        '{website}',
        '{from_name}',
        '{to_name}'
      ), array(
        mc_cleanData($SETTINGS->website),
        mc_cleanData($GIFT_CERTS->from_name),
        mc_cleanData($GIFT_CERTS->to_name)
      ), $msg_emails23);
      $MCMAIL->sendMail(array(
        'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
        'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
        'to_email' => $GIFT_CERTS->to_email,
        'to_name' => $GIFT_CERTS->to_name,
        'subject' => $sbj,
        'replyto' => array(
          'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
        ),
        'template' => $msg,
        'language' => $SETTINGS->languagePref
      ));
      $MCMAIL->smtpClose();
    }
    echo $JSON->encode(array(
      'OK'
    ));
  }
  exit;
}

// View gateway parameters..
if (isset($_GET['gatewayParams'])) {
  include(PATH . 'templates/windows/gateway-params.php');
  exit;
}

// Tally up totals..
if (isset($_GET['loadBoxTotals'])) {
  echo $JSON->encode(array(
    'total' => ($_GET['loadBoxTotals'] ? mc_formatPrice($_GET['qty'] * array_sum(explode(',', $_GET['loadBoxTotals']))) : '0.00')
  ));
  exit;
}

// Sale weight..
if (isset($_GET['loadSaleWeight'])) {
  $weight = '0';
  $wID    = (isset($_GET['loadSaleWeight']) ? (int) $_GET['loadSaleWeight'] : '0');
  if (!empty($_POST['pid'])) {
    // Purchases..
    for ($i=0; $i<count($_POST['pid']); $i++) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `productWeight` FROM `" . DB_PREFIX . "purchases`
           WHERE `id`        = '{$_POST['pid'][$i]}'
           AND `productType` = 'physical'
           AND `saleID`      = '{$wID}'
           ") or die(mc_MySQLError(__LINE__, __FILE__));
      $W      = mysqli_fetch_object($q);
      $wght   = (isset($W->productWeight) ? @number_format(($W->productWeight * $_POST['qty'][$i]), 2, '.', '') : '0');
      if ($wght > 0) {
        $weight = @number_format(($weight + $wght), 2, '.', '');
      }
      // Attributes..
      if (!empty($_POST['attr'][$_POST['pid'][$i]])) {
        foreach ($_POST['attr'][$_POST['pid'][$i]] AS $vID) {
          $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `attrWeight` FROM `" . DB_PREFIX . "purch_atts`
               WHERE `attributeID` = '{$vID}'
               AND `purchaseID`    = '{$_POST['pid'][$i]}'
               AND `saleID`        = '{$wID}'
               ") or die(mc_MySQLError(__LINE__, __FILE__));
          $W      = mysqli_fetch_object($q);
          $wght   = (isset($W->attrWeight) ? @number_format(($W->attrWeight * $_POST['qty'][$i]), 2, '.', '') : '0');
          if ($wght > 0) {
            $weight = @number_format(($weight + $wght), 2, '.', '');
          }
        }
      }
    }
  } else {
    if ($_GET['loadSaleWeight'] > 0) {
      $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productWeight`) AS `w` FROM `" . DB_PREFIX . "purchases` WHERE `saleID` = '{$wID}'") or die(mc_MySQLError(__LINE__, __FILE__));
      $W    = mysqli_fetch_object($q);
      $wght = (isset($W->w) ? $W->w : '0');
      $q2 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`attrWeight`) AS `w` FROM `" . DB_PREFIX . "purch_atts` WHERE `saleID` = '{$wID}'") or die(mc_MySQLError(__LINE__, __FILE__));
      $W2     = mysqli_fetch_object($q2);
      $wght2  = (isset($W2->w) ? $W2->w : '0');
      $weight = @number_format($wght + $wght2, 2, '.', '');
    }
  }
  echo $JSON->encode(array(
    'total' => $weight
  ));
  exit;
}

// Next invoice number..
if (isset($_GET['nextInvoiceNo'])) {
  include_once(REL_PATH . 'control/gateways/class.handler.php');
  $GW           = new paymentHandler();
  $GW->settings = $SETTINGS;
  echo $JSON->encode(array(
    'OK',
    mc_saleInvoiceNumber($GW->getInvoiceNo(), $SETTINGS)
  ));
  exit;
}

// Add attribute..
if (isset($_GET['addAttribute'])) {
  $AT = mc_getTableData('attributes', 'id', (int) $_GET['addAttribute'], 'AND `productID` = \'' . (int) $_GET['product'] . '\'');
  echo $JSON->encode(array(
    'name' => mc_cleanData($AT->attrName),
    'cost' => $AT->attrCost,
    'qty' => ($AT->attrStock - 1),
    'weight' => $AT->attrWeight,
    'id' => $AT->id
  ));
  exit;
}

// Shipping Label..
if (isset($_GET['shipLabel'])) {
  define('WINPARENT', 1);
  $pageTitle  = $msg_viewsale105;
  include(PATH . 'templates/windows/header.php');
  include(PATH . 'templates/windows/shipping-label.php');
  include(PATH . 'templates/windows/footer.php');
  exit;
}

// Reset personalisation..
if (isset($_GET['ppreload'])) {
  $slot = (int) $_GET['ppreload'];
  $prices = array(
    'price' => '0.00',
    'attr' => '0.00',
    'pers' => '0.00',
    'total' => '0.00',
    'hlite' => mc_currencyFormat('0.00', true)
  );
  if (!empty($_POST['pid'])) {
    for ($i=0; $i<count($_POST['pid']); $i++) {
      if ($_POST['pid'][$i] == $slot) {
        $ID     = $_POST['pid'][$i];
        $qty    = $_POST['qty'][$i];
        $price  = mc_formatPrice($_POST['price'][$i]);
        $attr   = '0.00';
        $pers   = '0.00';
        if (!empty($_POST['attr_cost'][$ID])) {
          $attr = mc_formatPrice(array_sum($_POST['attr_cost'][$ID]));
        }
        if (!empty($_POST['pers_cost'][$ID])) {
          for ($p=0; $p<count($_POST['pers_cost'][$ID]); $p++) {
            if ($_POST['pvalue'][$ID][$p] != '' && $_POST['pvalue'][$ID][$p] != 'no-option-selected') {
              $pers = mc_formatPrice(($pers + $_POST['pers_cost'][$ID][$p]));
            }
          }
        }
        $prices['price'] = $price;
        $prices['attr']  = $attr;
        $prices['pers']  = $pers;
        $prices['total'] = mc_formatPrice(($price + $attr + $pers) * $qty);
        $prices['hlite'] = mc_currencyFormat($prices['total'], true);
        break;
      }
    }
  }
  echo $JSON->encode($prices);
  exit;
}

// Print personalisation window..
if (isset($_GET['print-personalisation']) || isset($_GET['view-personalisation'])) {
  $_GET['print-personalisation'] = (isset($_GET['view-personalisation']) ? mc_digitSan($_GET['view-personalisation']) : mc_digitSan($_GET['print-personalisation']));
  include(PATH . 'templates/windows/personalisation-print-friendly.php');
  exit;
}

// Refresh prices..
if (isset($_POST['process']) && $_POST['process'] == 'refresh-prices') {
  $ship   = mc_formatPrice($_POST['shipTotal']);
  $tax    = mc_formatPrice($_POST['taxRate']);
  $area   = (isset($_POST['shipSetArea']) ? mc_digitSan($_POST['shipSetArea']) : '0');
  $gift   = (isset($_POST['couponTotal']) ? mc_formatPrice($_POST['couponTotal']) : '0.00');
  $global = (isset($_POST['globalTotal']) ? mc_formatPrice($_POST['globalTotal']) : '0.00');
  $manual = (isset($_POST['manualDiscount']) ? mc_formatPrice($_POST['manualDiscount']) : '0.00');
  $ins    = (isset($_POST['insuranceTotal']) ? mc_formatPrice($_POST['insuranceTotal']) : '0.00');
  $addc   = (isset($_POST['chargeTotal']) ? mc_formatPrice($_POST['chargeTotal']) : '0.00');
  $price  = '0.00';
  $disc   = '0.00';
  $pers   = '0.00';
  $attr   = '0.00';
  if ($area > 0) {
    $A = mc_getTableData('zone_areas', 'id', $area, '', 'inZone');
    $Z = mc_getTableData('zones', 'id', $A->inZone, '', 'zShipping');
  }
  // Re-add prices..
  if (!empty($_POST['price'])) {
    for ($i = 0; $i < count($_POST['price']); $i++) {
      if ($_POST['price'][$i] > 0 && $_POST['qty'][$i] > 0) {
        $price = $price + mc_formatPrice($_POST['price'][$i] * $_POST['qty'][$i]);
      }
    }
  }
  // Attributes..
  if (!empty($_POST['pid'])) {
    for ($i = 0; $i < count($_POST['pid']); $i++) {
      $pID = $_POST['pid'][$i];
      if (!empty($_POST['attr_cost'][$pID])) {
        foreach ($_POST['attr_cost'][$pID] AS $aID => $aKey) {
          $attr = $attr + mc_formatPrice($_POST['attr_cost'][$pID][$aID] * $_POST['qty'][$i]);
        }
      }
    }
  }
  // Personalisation cost..
  if (!empty($_POST['pid'])) {
    for ($i = 0; $i < count($_POST['pid']); $i++) {
      $pID = $_POST['pid'][$i];
      if (!empty($_POST['pers_cost'][$pID])) {
        foreach ($_POST['pers_cost'][$pID] AS $prID => $prKey) {
          $attr = $attr + mc_formatPrice($_POST['pers_cost'][$pID][$prID] * $_POST['qty'][$i]);
        }
      }
    }
  }
  // Adjust price..
  $price = mc_formatPrice($price + $pers + $attr);
  // For global percentage, calculate based on price..
  if (isset($_POST['globalDiscount']) && $_POST['globalDiscount'] > 0) {
    $global = number_format(($price * $_POST['globalDiscount']) / 100, 2, '.', '');
  }
  // Discounts..
  if ($gift > 0 || $global > 0 || $manual > 0) {
    $disc = ($gift > 0 ? $gift : ($manual > 0 ? $manual : $global));
  }
  // Calculations..
  if (isset($Z->zShipping) && $Z->zShipping == 'yes') {
    $sprice = mc_formatPrice($price - $disc);
    $taxCal = ($tax > 0 ? number_format($tax * mc_formatPrice($sprice + $ship) / 100, 2, '.', '') : '0.00');
    $total  = mc_formatPrice($sprice + $ship + $taxCal + $ins);
  } else {
    $sprice = mc_formatPrice($price - $disc);
    $taxCal = ($tax > 0 ? number_format($tax * mc_formatPrice($sprice) / 100, 2, '.', '') : '0.00');
    $total  = mc_formatPrice($sprice + $taxCal + $ship + $ins);
  }
  $weight = 0;
  if (!empty($_POST['pid'])) {
    for ($i=0; $i<count($_POST['pid']); $i++) {
      $qty    = $_POST['qty'][$i];
      $wt     = mc_sumCount('purchases WHERE `id` = \'' . $_POST['pid'][$i] . '\'', 'productWeight');
      $wt2    = mc_sumCount('purch_atts WHERE `purchaseID` = \'' . $_POST['pid'][$i] . '\'', 'attrWeight');
      $weight = $weight + @number_format(($wt + $wt2) * $qty, 2, '.', '');
    }
  }
  echo $JSON->encode(array(
    'sub' => mc_formatPrice($price),
    'tax' => $taxCal,
    'grand' => mc_formatPrice($total + $addc),
    'global' => mc_formatPrice($global),
    'coupon' => mc_formatPrice($gift),
    'manual' => mc_formatPrice($manual),
    'weight' => ($weight > 0 ? @number_format($weight, 2, '.', '') : '0'),
    'text' => $msg_javascript248
  ));
  exit;
}

// Add product to sale..
if ($cmd == 'add') {
  $_GET['sale'] = mc_digitSan($_GET['sale']);
  if (isset($_POST['process'])) {
    if (!empty($_POST['product']) && $_GET['sale'] > 0) {
      $MCSALE->addProductToSale();
      $metaRefresh = array(
        'time' => 5,
        'url' => 'index.php?p=sales-view&amp;sale=' . $_GET['sale']
      );
      $OK = true;
    } else {
      header("Location: index.php?p=add&sale=" . (int) $_GET['sale'] . "&type=" . $_POST['type']);
      exit;
    }
  }
  $SALE         = mc_getTableData('sales', 'id', $_GET['sale'], '', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
  $pageTitle   = (isset($_GET['type']) && $_GET['type'] == 'physical' ? $msg_admin_viewsale3_0[3] : $msg_admin_viewsale3_0[4]) . ' (#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . '): ' . $pageTitle;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-add-products.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Reset downloads..
if ($cmd == 'downloads') {
  // Show order..
  if (isset($_GET['atoken'])) {
    $chop = explode('-', $_GET['atoken']);
    $_SESSION[mc_encrypt(SECRET_KEY) . '_saleToken'] = (int) $chop[0];
    header("Location: " . $SETTINGS->ifolder . "/?vOrder=" . $_GET['atoken'] . "&token=yes");
    exit;
  }
  $_GET['sale'] = mc_digitSan($_GET['sale']);
  $SALE         = mc_getTableData('sales', 'id', $_GET['sale'], '', '*,DATE_FORMAT(`purchaseDate`,\'' . $SETTINGS->mysqlDateFormat . '\') AS `pdate`');
  if (isset($_GET['ch'])) {
    include(PATH . 'templates/windows/sale-download-history-clicks.php');
    exit;
  }
  if (isset($_POST['process']) && $_GET['sale'] > 0) {
    include(REL_PATH . 'control/classes/class.rewrite.php');
    $MCRWR           = new mcRewrite();
    $MCRWR->settings = $SETTINGS;
    $productString   = '';
    $products        = array();
    if (!empty($_POST['id'])) {
      include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
      foreach ($_POST['id'] AS $id) {
        $PURCHASE   = mc_getTableData('purchases', 'id', $id);
        $PRD        = mc_getTableData('products', 'id', $PURCHASE->productID);
        // Assign order string and products array..
        $products[] = 'p' . $PURCHASE->productID;
        $productString .= mc_cleanData($PRD->pName);
        // Create unique download code..
        $code = mc_encrypt(uniqid(rand(), 1) . date('dmYhis') . $id);
        // Activate download..
        $MCSALE->activateDownloads($code, $id);
      }
      // Create arrays of digits/letters..
      $a = array_merge(range('a', 'z'), range(1, 9));
      shuffle($a);
      $append = $a[4] . $a[23];
      $dUrl = $SETTINGS->ifolder . '/?vOrder=' . $_GET['sale'] . '-' . $SALE->buyCode . $append;
      // Add activation log..
      $MCSALE->addActivationLog($_GET['sale'], implode('|', $products), count($products));
      // Send email to buyer..
      $SALE = mc_getTableData('sales', 'id', $_GET['sale']);
      // If wish list purchase use shipping fields..
      if ($SALE->wishlist > 0) {
        $SALE->bill_1 = $SALE->ship_1;
        $SALE->bill_2 = $SALE->ship_2;
        $dUrl = $MCRWR->url(array('account'));
      }
      if ($SALE->bill_2) {
        $MCMAIL->addTag('{PRODUCTS}', rtrim($productString));
        $MCMAIL->addTag('{D_URL}', ($SALE->account == '0' ? $dUrl : $MCRWR->url(array('account'))));
        $MCMAIL->addTag('{NAME}', $SALE->bill_1);
        $sbj = str_replace(array(
          '{website}',
          '{order}'
        ), array(
          mc_cleanData($SETTINGS->website),
          mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS)
        ), $msg_viewsale35);
        $msg = LANG_PATH . 'admin/download-reactivation.txt';
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $SALE->bill_2,
          'to_name' => $SALE->bill_1,
          'subject' => $sbj,
          'replyto' => array(
            'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
          ),
          'template' => $msg,
          'language' => $SETTINGS->languagePref
        ));
        $MCMAIL->smtpClose();
      }
      $OK = true;
    } else {
      header("Location: index.php?p=downloads&sale=" . $_GET['sale']);
      exit;
    }
  }
  // Lock/unlock download page..
  if (isset($_GET['action']) && in_array($_GET['action'], array(
    'lock',
    'unlock'
  ))) {
    switch($_GET['action']) {
      case 'lock':
        $MCSALE->downloadPageLock($_GET['sale'], $_GET['status'], $msg_viewsale83);
        $OK2 = true;
        break;
      case 'unlock':
        $MCSALE->downloadPageLock($_GET['sale'], $_GET['status'], $msg_viewsale84);
        $OK3 = true;
        break;
    }
  }
  $pageTitle   = $pageTitle = $msg_admin_viewsale3_0[7] . ' (#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . '): ' . $pageTitle;
  $loadiBox = true;
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-download-history.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Reload countries..
if (isset($_GET['c'])) {
  $_GET['c'] = mc_digitSan($_GET['c']);
  echo $JSON->encode(array(
    'areas' => $MCSALE->reloadCountries(),
    'services' => $MCSALE->reloadServices(),
    'taxrate' => $MCSALE->reloadServices(true)
  ));
  exit;
}

// Reload tax rate..
if (isset($_GET['z'])) {
  $_GET['z'] = mc_digitSan($_GET['z']);
  $ZONE      = mc_getTableData('zone_areas', 'id', $_GET['z']);
  echo $JSON->encode(array(
    'taxrate' => (isset($ZONE->zRate) && $ZONE->zRate != '' ? $ZONE->zRate : '0')
  ));
  exit;
}

// Edit sale..
if (isset($_POST['process']) && $_POST['process'] == 'yes') {
  $MCSALE->editSale();
  $OK = true;
}

// Load service price..
if (isset($_GET['service'])) {
  $C = '0.00';
  switch(substr($_GET['service'], 0, 4)) {
    case 'flat':
      $F = mc_getTableData('flat', 'id', (int) substr($_GET['service'], 4));
      $C = (isset($F->rate) ? $F->rate : '0.00');
      break;
    case 'pert':
      $F = mc_getTableData('per', 'id', (int) substr($_GET['service'], 4));
      $R = (isset($F->rate) ? $F->rate : '0.00');
      $E = (isset($F->item) ? $F->item : '0.00');
      $X = '';
      for ($i = 0; $i < count($_GET['pids']); $i++) {
        if ($i > 0) {
          $qty      = $_GET['qtys'][$i]['value'];
          $product  = $_GET['pids'][$i]['value'];
          $prodInfo = mc_getTableData('products', 'id', $product);
          // Check this product doesn`t have free shipping and also isn`t a download..
          if ($prodInfo->pDownload == 'no' && $prodInfo->freeShipping == 'no') {
            $X = ($X + ($E * $qty));
          }
        }
      }
      $C = number_format(($R + $X), 2, '.', '');
      break;
    case 'perc':
      $P = mc_getTableData('percent', 'id', (int) substr($_GET['service'], 4));
      $C = number_format(($_GET['price'] * $P->percentage) / 100, 2, '.', '');
      break;
    case 'qtyr':
      $P = mc_getTableData('qtyrates', 'id', (int) substr($_GET['service'], 4));
      if (isset($P->rate)) {
        switch(substr($P->rate, -1)) {
          case '%':
            $C = number_format(($_GET['price'] * substr($P->rate,0,-1)) / 100, 2, '.', '');
            break;
          default:
            $C = $P->rate;
            break;
        }
      }
      break;
    default:
      $S        = mc_getTableData('rates', 'id', (int) $_GET['service']);
      // Tare weight..
      $tareCost = '0.00';
      if ($_GET['weight'] > 0) {
        $tare = mc_getTareWeight($_GET['weight'], $S->rService);
        if ($tare[0] == 'yes') {
          switch(substr($tare[1], -1)) {
            case '%':
              $calc     = substr($tare[1], 0, -1);
              $tareCost = number_format(($S->rCost * $calc) / 100, 2, '.', '');
              break;
            default:
              $tareCost = mc_formatPrice($tare[1]);
              break;
          }
        }
      }
      $C = (isset($S->rCost) ? mc_formatPrice($S->rCost + $tareCost) : '0.00');
      break;
  }
  echo $JSON->encode(array(
    'price' => $C
  ));
  exit;
}

// Products added? Show message..
if (isset($_GET['products-added'])) {
  $OK2 = true;
}

// Get sale data..
if (isset($_GET['sale'])) {
  $SALE = mc_getTableData('sales', 'id', mc_digitSan($_GET['sale']), '', '*,DATE_FORMAT(purchaseDate,\'' . $SETTINGS->mysqlDateFormat . '\') AS pdate');
}

if (!isset($SALE->id)) {
  include(PATH . 'control/modules/header/403.php');
}

$pageTitle   = $msg_viewsale74 . ' (#' . mc_saleInvoiceNumber($SALE->invoiceNo, $SETTINGS) . '): ' . $pageTitle;
$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-view.php');
include(PATH . 'templates/footer.php');

?>