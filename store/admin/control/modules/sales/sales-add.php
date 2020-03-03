<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'sales/view-sales.php');
include(MCLANG . 'sales/sales-add.php');
include(MCLANG . 'sales/sales-view.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-related.php');

// Refresh prices..
if (isset($_POST['process_add_sale']) && $_POST['process_add_sale'] == 'refresh-prices') {
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
  $aWeight = array();
  if (!empty($_POST['pd'])) {
    for ($i = 0; $i < count($_POST['pd']); $i++) {
      $pID = $_POST['pd'][$i];
      if (!empty($_POST['attr_cost'][$pID])) {
        foreach ($_POST['attr_cost'][$pID] AS $aID => $aKey) {
          $attr            = $attr + mc_formatPrice($_POST['attr_cost'][$pID][$aID] * $_POST['qty'][$i]);
          $aWeight[$pID][] = $_POST['attr_id'][$pID][$aID];
        }
      }
    }
  }
  // Personalisation cost..
  if (!empty($_POST['pd'])) {
    for ($i = 0; $i < count($_POST['pd']); $i++) {
      $pID = $_POST['pd'][$i];
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
  if (!empty($_POST['pd'])) {
    for ($i=0; $i<count($_POST['pd']); $i++) {
      $qty    = $_POST['qty'][$i];
      $chop   = explode('-', $_POST['pd'][$i]);
      $wt     = mc_sumCount('products WHERE `id` = \'' . $chop[0] . '\'', 'pWeight');
      $wt2    = 0;
      if (!empty($_POST['attr_cost'][$_POST['pd'][$i]])) {
        if (!empty($aWeight[$_POST['pd'][$i]])) {
          $wt2  = mc_sumCount('attributes WHERE `id` IN(\'' . implode(',',$aWeight[$_POST['pd'][$i]]) . '\')', 'attrWeight');
        }
      }
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
    'weight' => ($weight > 0 ? number_format($weight, 2, '.', '') : '0'),
    'text' => $msg_javascript248
  ));
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
  if (!empty($_POST['pd'])) {
    for ($i=0; $i<count($_POST['pd']); $i++) {
      if ($_POST['pd'][$i] == $slot) {
        $ID     = $_POST['pd'][$i];
        $qty    = $_POST['qty'][$i];
        $price  = mc_formatPrice($_POST['price'][$i]);
        $attr   = '0.00';
        $pers   = '0.00';
        if (!empty($_POST['attr_cost'][$ID])) {
          $attr = mc_formatPrice(array_sum($_POST['attr_cost'][$ID]));
        }
        if (!empty($_POST['pers_cost'][$ID])) {
          for ($p=0; $p<count($_POST['pers_cost'][$ID]); $p++) {
            if ($_POST['pnvalue'][$ID][$p] != '' && $_POST['pnvalue'][$ID][$p] != 'no-option-selected') {
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

// Load products..
if ($cmd == 'add-manual') {
  if (isset($_POST['process'])) {
    if (!empty($_POST['product'])) {
      switch($_POST['type']) {
        case 'physical':
          $cur = array();
          if (!empty($_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)])) {
            $cur = $_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)];
          }
          $_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)] = array_merge($_POST['product'], $cur);
          break;
        case 'download':
          $cur = array();
          if (!empty($_SESSION['add-down-' . mc_encrypt(SECRET_KEY)])) {
            $cur = $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)];
          }
          $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)] = array_merge($_POST['product'], $cur);
          break;
      }
      $metaRefresh = array(
        'time' => 5,
        'url' => 'index.php?p=sales-add'
      );
      $OK = true;
    } else {
      header("Location: index.php?p=add-manual&type=" . $_POST['type']);
      exit;
    }
  }
  $pageTitle   = mc_cleanDataEntVars($msg_javascript355) . ': ' . $pageTitle;

  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sale-add-products-manual.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Clear
if (isset($_GET['clear'])) {
  unset($_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)], $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)]);
  $_SESSION['add-phys-' . mc_encrypt(SECRET_KEY)] = array();
  $_SESSION['add-down-' . mc_encrypt(SECRET_KEY)] = array();
  header("Location: index.php?p=sales-add");
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

// Add sale and load edit page..
if (isset($_POST['process_add_sale']) && $_POST['process_add_sale'] == 'yes') {
  if ($_POST['acc_name'] && mswIsValidEmail($_POST['acc_email'])) {
    $MCSALE->account = $MCACC;
    $id = $MCSALE->addManualSale();
    if ($id[0] > 0) {
      // If account was created, do we send notification?
      if ($id[1] == 'yes' && isset($_POST['acc_send'])) {
        include(GLOBAL_PATH . 'control/classes/mailer/global-mail-tags.php');
        $sbj = str_replace('{website}', $SETTINGS->website, $msg_emails28);
        $msg = LANG_PATH . 'admin/new-account.txt';
        $MCMAIL->addTag('{NAME}', $_POST['name']);
        $MCMAIL->addTag('{EMAIL}', $_POST['email']);
        $MCMAIL->addTag('{PASS}', $_POST['pass']);
        $MCMAIL->addTag('{ACC_TYPE}', $msg_addccts32);
        $MCMAIL->sendMail(array(
          'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
          'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
          'to_email' => $_POST['email'],
          'to_name' => $_POST['name'],
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
      header("Location: index.php?p=sales-view&sale=" . $id[0] . "&newacc=yes");
      exit;
    }
  }
  include(PATH . 'templates/header.php');
  include(PATH . 'templates/sales/sales-view.php');
  include(PATH . 'templates/footer.php');
  exit;
}

// Load service price..
if (isset($_GET['service'])) {
  $C = '0.00';
  switch(substr($_GET['service'], 0, 4)) {
    case 'flat':
      $F = mc_getTableData('flat', 'id', (int) substr($_GET['service'], 4));
      $C = (isset($F->rate) ? $F->rate : '0.00');
      break;
    case 'perc':
      $P = mc_getTableData('percent', 'id', (int) substr($_GET['service'], 4));
      $C = number_format(($_GET['price'] * $P->percentage) / 100, 2, '.', '');
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

$pageTitle   = $pageTitle = mc_cleanDataEntVars($msg_javascript355) . ': ' . $pageTitle;
$loadiBox = true;

include(PATH . 'templates/header.php');
include(PATH . 'templates/sales/sales-add.php');
include(PATH . 'templates/footer.php');

?>