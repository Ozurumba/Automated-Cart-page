<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

define('CHECKOUT_LOADED', 1);

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');
include(MCLANG . 'payment.php');
include(MCLANG . 'accounts.php');

// If checkout is disabled, we shouldn`t be here..
if ($SETTINGS->enableCheckout == 'no') {
  header("Location: " . $MCRWR->url(array('base_href')));
  exit;
}

// HTML class..
include(PATH . 'control/classes/class.html.php');
$MCHTML           = new mcHtml();
$MCHTML->settings = $SETTINGS;

// Terms and conditions..
if (isset($_GET['terms']) && $SETTINGS->tc == 'yes') {
  $tpl = mc_getSavant();
  $tpl->assign('TEXT', array(
    $mc_checkout[25],
    mc_txtParsingEngine($SETTINGS->tctext)
  ));
  // Global..
  include(PATH . 'control/system/global.php');
  $tpl->display(THEME_FOLDER . '/checkout-terms-conditions.tpl.php');
  exit;
}

// Help files..
if (isset($_GET['help'])) {
  $tpl = mc_getSavant();
  // Check for window other than payment method..
  switch($_GET['help']) {
    case 'ins':
      $help = array(
        0 => $public_checkout120,
        1 => mc_txtParsingEngine($SETTINGS->insuranceInfo)
      );
      break;
    default:
      if (!in_array($_GET['help'],array_keys($mcSystemPaymentMethods))) {
        die('Permission Denied');
      }
      $help = mc_getHelp($_GET['help'], $mcSystemPaymentMethods);
      break;
  }
  $tpl->assign('TEXT', array(
    $help[0],
    $help[1]
  ));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/checkout-payment-help.tpl.php');
  exit;
}

// Edit gift..
if (isset($_GET['gift'])) {
  $slot = $MCCART->productSlotPosition($_GET['gift']);
  if (isset($_SESSION['giftAddr'][$slot])) {
    $tpl = mc_getSavant();
    $tpl->assign('TEXT', array(
      $public_checkout68,
      $public_checkout69,
      '',
      $gift_cert16,
      $gift_cert5,
      $gift_cert6,
      $gift_cert7,
      $gift_cert8,
      $gift_cert17
    ));
    $tpl->assign('URL', $MCRWR->url(array(
      $MCRWR->config['slugs']['gft'] . '/' . $_GET['gift'],
      'gift=' . $_GET['gift']
    )));
    $tpl->assign('GIFTID', $_GET['gift']);
    $tpl->assign('GIFT', $_SESSION['giftAddr'][$slot]);

    // Global..
    include(PATH . 'control/system/global.php');

    $tpl->display(THEME_FOLDER . '/checkout-edit-gift.tpl.php');
  }
  exit;
}

// Edit personalisation..
if (isset($_GET['ppCE'])) {
  $tpl = mc_getSavant();
  $tpl->assign('TEXT', array(
    $public_checkout68,
    $public_checkout69
  ));

  $tpl->assign('URL', $MCRWR->url(array(
    $MCRWR->config['slugs']['edp'] . '/' . $_GET['ppCE'],
    'ppCE=' . $_GET['ppCE']
  )));
  $tpl->assign('OPTIONS', $MCPROD->buildPersonalisationOptions(0, true));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/checkout-edit-personalisation.tpl.php');
  exit;
}

$headerTitleText = mc_cleanData($msg_public_header4 . ': ' . $headerTitleText);

// Load js..
$loadJS['swipe']     = 'load';
$loadJS['jquery-ui'] = 'load';
$loadJS['checkout']  = 'load';

// Breadcrumb..
$breadcrumbs = array(
  $msg_public_header4
);

$addr   = array(array(),array());
if (isset($loggedInUser['id'])) {
  $addr = $MCACC->getaddresses($loggedInUser['id']);
} else {
  $addr = $MCCKO->getaddresses();
}

// Account first name..
$firstN = '';
if (isset($loggedInUser['name'])) {
  $sfn = mc_splitAccName($loggedInUser['name']);
  $firstN = mc_safeHTML((isset($sfn[0]) ? $sfn[0] : $loggedInUser['name']));
}

// Left menu boxes..
$skipMenuBoxes['points']  = true;
$skipMenuBoxes['popular'] = true;
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['rss']     = true;
$skipMenuBoxes['tweets']  = true;
include(PATH . 'control/left-box-controller.php');

$wishCountry = '';
$isWish      = $MCCART->wishBasketCnt();

// Check address exists for wish list owner if shipping is required..
if ($isWish == 'yes') {
  if (!defined('KILL_CHECKOUT_SHIPPING')) {
    $wID = $MCCART->wishBasketCnt('yes');
    if ($wID > 0) {
      $addr = $MCACC->getaddresses($wID);
      if ($addr[1]['addr1'] > 0 && $addr[1]['zone'] > 0) {
        $CN = mc_getTableData('countries', 'id', $addr[1]['addr1']);
        $wishCountry = mc_cleanData($CN->cName);
      } else {
        header("Location: " . $MCRWR->url(array('no-wish-country')));
        exit;
      }
    } else {
      header("Location: " . $MCRWR->url(array('no-wish-country')));
      exit;
    }
  } else {
    $wID = $MCCART->wishBasketCnt('yes');
  }
}

include(PATH . 'control/header.php');

$tandcUrl = $MCRWR->url(array(
  $MCRWR->config['slugs']['tac'],
  'terms=yes'
));

$tpl = mc_getSavant();
$tpl->assign('TEXT', $public_accounts);
$tpl->assign('TXT', array(
  $mc_checkout,
  $public_checkout11,
  $public_checkout8,
  $public_checkout2,
  $public_checkout9,
  $public_checkout7,
  $chk_account,
  str_replace('{name}',$firstN,$chk_payment[19]),
  $public_checkout40,
  $public_checkout48,
  $public_checkout25,
  str_replace('{url}',$tandcUrl,$mc_checkout[24])
));
$tpl->assign('TEXT_JS', array_map('mc_filterJS', array(
  $msg_javascript122,
  $msg_javascript
)));
// Check user is logged in to display this info..
if (isset($loggedInUser['id'])) {
  $tpl->assign('TRADE', array(
    str_replace('{percent}', $loggedInUser['tradediscount'], $public_accounts[23]),
    ($loggedInUser['minqty'] > 0 ? str_replace('{count}', $loggedInUser['minqty'], $public_accounts[22]) : $public_accounts[24]),
    ($loggedInUser['maxqty'] > 0 ? str_replace('{count}', $loggedInUser['maxqty'], $public_accounts[22]) : $public_accounts[24]),
    ($loggedInUser['mincheckout'] > 0 ? $MCCART->formatSystemCurrency(mc_formatPrice($loggedInUser['mincheckout'])) : $public_accounts[24])
  ));
}
$tpl->assign('PTEXT', $chk_payment);
$tpl->assign('ADTXT', $public_accounts_create);
$tpl->assign('BASKET_TOTAL', $MCPROD->formatSystemCurrency($MCCART->cartTotal()));
$tpl->assign('BASKET_ITEMS', $MCCKO->buildBasketItems());

// Min checkout amount..
if (defined('MC_TRADE_MIN_CHECKOUT')) {
  $tpl->assign('MIN_CHECKOUT_RESTRICTION', (MC_TRADE_MIN_CHECKOUT > 0 ? ($MCCART->cartTotal() >= MC_TRADE_MIN_CHECKOUT ? 'no' : 'yes') : 'no'));
  $tpl->assign('MIN_CHECKOUT_AMNT', str_replace('{amount}',$MCPROD->formatSystemCurrency(MC_TRADE_MIN_CHECKOUT),$mc_checkout[35]));
} else {
  $tpl->assign('MIN_CHECKOUT_RESTRICTION', ($SETTINGS->minCheckoutAmount > 0 ? ($MCCART->cartTotal() >= $SETTINGS->minCheckoutAmount ? 'no' : 'yes') : 'no'));
  $tpl->assign('MIN_CHECKOUT_AMNT', str_replace('{amount}',$MCPROD->formatSystemCurrency($SETTINGS->minCheckoutAmount),$mc_checkout[35]));
}

$tpl->assign('CART_COUNT', $MCCART->cartCount());
$tpl->assign('COUNTRIES', array(
  'ship' => $MCHTML->loadShippingCountries('num',(isset($addr[1]['addr1']) ? $addr[1]['addr1'] : '0')),
  'bill' => $MCHTML->loadShippingCountries('num',(isset($addr[0]['addr1']) ? $addr[0]['addr1'] : '0'))
));
$tpl->assign('CSTAT', array(
  'count' => $MCCART->cartCount(),
  'total' => $MCPROD->formatSystemCurrency($MCCART->cartTotal())
));
$tpl->assign('ADDR', array(
  'ship' => $addr[1],
  'bill' => $addr[0]
));
$tpl->assign('PAYMENT_OPTIONS', $MCCKO->gateways($mcSystemPaymentMethods, '', 'no', (isset($loggedInUser['type']) ? $loggedInUser['type'] : 'guest')));
$first = $MCCKO->gateways($mcSystemPaymentMethods, '', 'yes', (isset($loggedInUser['type']) ? $loggedInUser['type'] : 'guest'));
$tpl->assign('STRIPE_EN', (isset($mcSystemPaymentMethods['stripe']['enable']) ? $mcSystemPaymentMethods['stripe']['enable'] : 'no'));
$tpl->assign('STRIPE_ID', (isset($mcSystemPaymentMethods['stripe']['ID']) ? $mcSystemPaymentMethods['stripe']['ID'] : '0'));
$tpl->assign('DEF_ICON', (isset($first[0]) ? THEME_FOLDER . '/images/gateways/' . $first[0] : ''));
if (isset($first[0])) {
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['hlp'] . '/' . $first[0],
    'help=' . $first[0]
  ));
}
$tpl->assign('GATE_URL', (isset($first[0]) ? $url : '#'));
$tpl->assign('TOTALS', $MCCART->buildBasketTotals());
$tpl->assign('WISH_PURCHASE', $isWish);
$curMsg = '';
if (isset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'])) {
  if ($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency'] != $SETTINGS->baseCurrency) {
    $joinC  = array_merge($other_currencies, $master_currencies);
    if (isset($joinC[$SETTINGS->baseCurrency])) {
      $curMsg = str_replace('{cur}', $joinC[$SETTINGS->baseCurrency], $chk_payment[21]);
    } else {
      unset($_SESSION[mc_encrypt(SECRET_KEY) . '_mc_currency']);
    }
  }
}

$tpl->assign('COUNTRY', (isset($addr[1]['addr1']) ? $addr[1]['addr1'] : '0'));
$tpl->assign('WISH_ID', (isset($wID) ? $wID : '0'));
$tpl->assign('WISH_MSG', ($isWish == 'yes' ? str_replace('{country}',$wishCountry,$mc_checkout[29]) : ''));
$tpl->assign('FREE_CART', ($MCCART->cartTotal() == 0 && $MCCART->allDownloadItemsInCart() == 0 && $MCCART->cartFreebies() > 0 ? 'yes' : 'no'));
$tpl->assign('SHOW_SHIPPING', (defined('KILL_CHECKOUT_SHIPPING') ? 'no' : 'yes'));
$tpl->assign('CUR_MESSAGE', $curMsg);
$tpl->assign('URL', array(
  $MCRWR->url(array('account')),
  $MCRWR->url(array('profile')),
  $MCRWR->url(array('history')),
  $MCRWR->url(array('wishlist')),
  $MCRWR->url(array('saved-searches')),
  $MCRWR->url(array('create')),
  $MCRWR->url(array('logout')),
  $MCRWR->url(array('close'))
));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/checkout.tpl.php');
include(PATH . 'control/footer.php');

?>