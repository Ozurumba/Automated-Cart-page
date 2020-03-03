<?php

if (!defined('PARENT')) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Load language files..
include(MCLANG . 'category.php');
include(MCLANG . 'product.php');

// Wish reload..
if ($SETTINGS->en_wish == 'yes' && isset($_GET['loadw'])) {
  $chop = explode('_', $_GET['loadw']);
  if (isset($chop[0], $chop[1])) {
    $W = mc_getTableData('accounts_wish', 'account', (int) $chop[1],' AND `product` = \'' . (int) $chop[0] . '\'');
    if (isset($W->id)) {
      $P = mc_getTableData('products', 'id', (int) $chop[0],' AND `pEnable` = \'yes\'');
      if (isset($P->id)) {
        $url = $MCRWR->url(array(
          $MCRWR->config['slugs']['prd'] . '/' . $P->id . '/' . ($P->rwslug ? $P->rwslug : $MCRWR->title($P->pName)),
          'pd=' . $P->id
        ));
        $_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)] = array($chop[0], $chop[1]);
        header("Location: " . $url);
        exit;
      }
    }
  }
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Enquiry..
if (isset($_GET['penq'])) {
  include(PATH . 'control/classes/mailer/global-mail-tags.php');
  include(PATH . 'control/classes/class.ajax.php');
  $MCOPS = new cartOps();
  $ret   = $MCOPS->enquiry((isset($loggedInUser['id']) ? $loggedInUser : array()));
  $arr   = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $msg_storeform[0]));
  if ($ret['msg'] != 'fail') {
    switch($ret['msg']) {
      case 'ok':
        if (isset($ret['form']['product']['pName'])) {
          $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails37);
          $msg   = MCLANG . 'email-templates/product-enquiry.txt';
          $MCMAIL->addTag('{NAME}', $ret['form']['name']);
          $MCMAIL->addTag('{EMAIL}', $ret['form']['email']);
          $MCMAIL->addTag('{COMMENTS}', $ret['form']['comments']);
          $MCMAIL->addTag('{PRODUCT}', $ret['form']['product']['pName']);
          $MCMAIL->addTag('{URL}', $MCRWR->url(array(
            $MCRWR->config['slugs']['prd'] . '/' . $ret['form']['product']['id'] . '/' . ($ret['form']['product']['rwslug'] ? $ret['form']['product']['rwslug'] : $MCRWR->title($ret['form']['product']['pName'])),
            'pd=' . $ret['form']['product']['id']
          )));
          $MCMAIL->sendMail(array(
            'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
            'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
            'to_email' => $SETTINGS->email,
            'to_name' => $SETTINGS->website,
            'subject' => $sbj,
            'replyto' => array(
              'name' => $ret['form']['name'],
              'email' => $ret['form']['email']
            ),
            'template' => $msg,
            'alive' => 'yes',
            'add-emails' => $SETTINGS->addEmails,
            'language' => $SETTINGS->languagePref
          ));
          if (PROD_ENQUIRY_AUTO_RESPONDER) {
            $sbj   = str_replace('{website}', $SETTINGS->website, $msg_emails38);
            $msg   = MCLANG . 'email-templates/product-enquiry-confirmation.txt';
            $MCMAIL->sendMail(array(
              'from_email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email),
              'from_name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
              'to_email' => $ret['form']['email'],
              'to_name' => $ret['form']['name'],
              'subject' => $sbj,
              'replyto' => array(
                'name' => ($SETTINGS->smtp_from ? $SETTINGS->smtp_from : $SETTINGS->website),
                'email' => ($SETTINGS->smtp_email ? $SETTINGS->smtp_email : $SETTINGS->email)
              ),
              'template' => $msg,
              'language' => (isset($loggedInUser['language']) ? $loggedInUser['language'] : $SETTINGS->languagePref)
            ));
          }
          $MCMAIL->smtpClose();
          $arr = array('msg' => 'ok', 'html' => '', 'text' => array($msg_storeform[1], $msg_storeform[2]));
          echo $MCJSON->encode($arr);
          exit;
        }
        break;
    }
  }
  echo $MCJSON->encode($arr);
  exit;
}

// View mp3..
if (isset($_GET['pMP3']) && ctype_digit($_GET['pMP3'])) {
  $arr = array('msg' => 'err', 'html' => '', 'text' => array($mc_global[2], $mc_product[16]));
  $cmp = mc_rowCount('mp3 WHERE `product_id` = \'' . (int) $_GET['pMP3'] . '\'');
  if ($cmp > 0) {
    $P = mc_getTableData('products', 'id', $_GET['pMP3']);
    if (!isset($P->id)) {
      echo $MCJSON->encode($arr);
      exit;
    }
    $tr  = $MCPROD->loadMP3();
    if ($tr) {
      $arr = array(
        'msg' => 'ok',
        'html' => $tr
      );
    }
  }
  echo $MCJSON->encode($arr);
  exit;
}

// Country restrictions..
if (isset($_GET['pCRes'])) {
  mc_checkDigit($_GET['pCRes']);
  $P = mc_getTableData('products', 'id', $_GET['pCRes']);
  if (!isset($P->id) || $P->countryRestrictions == '' || $P->countryRestrictions == null) {
    include(PATH . 'control/system/headers/403.php');
    exit;
  }
  $tpl = mc_getSavant();
  $tpl->assign('PDATA', (array) $P);
  $tpl->assign('TEXT', array(
    $mc_product
  ));
  $tpl->assign('COUNTRIES', $MCPROD->restrictedCountryList($P->countryRestrictions));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/product-country-restriction.tpl.php');
  exit;
}

// View product description..
if (isset($_GET['dsc'])) {
  mc_checkDigit($_GET['dsc']);
  $P = mc_getTableData('products', 'id', (int) $_GET['dsc']);
  if (!isset($P->id)) {
    include(PATH . 'control/system/headers/403.php');
    exit;
  }
  if ($P->pDescription == '') {
    $P->pDescription = $public_category14;
  }
  $tpl = mc_getSavant();
  $tpl->assign('PDATA', (array) $P);
  $tpl->assign('TEXT', array(
    mc_safeHTML($P->pName),
    mc_txtParsingEngine($P->pDescription),
    mc_safeHTML($P->pCode)
  ));

  // Global..
  include(PATH . 'control/system/global.php');

  $tpl->display(THEME_FOLDER . '/product-description.tpl.php');
  exit;
}

// Check query var digit..
if (!isset($_GET['pd'])) {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

mc_checkDigit($_GET['pd']);
$prdID = (int) $_GET['pd'];

// Add to basket..
if (isset($_POST['qty'])) {
  $MCAJAX->addItemToBasket();
  exit;
}

// Get product/cat data..
$PRODUCT = mc_getProductCatRelation($prdID, $SETTINGS);

// Trade stock override..
if (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0) {
  $PRODUCT->pStock = MC_TRADE_STOCK;
}

// Check product..
if (!isset($PRODUCT->pid)) {
  if ($PRODUCT == 'no-cat') {
    header("Location: " . $MCRWR->url(array('no-category-assigned')));
    exit;
  }
  include(PATH . 'control/system/headers/404.php');
  exit;
}

// Does visitor have permission to see this product?
if (isset($PRODUCT->vis) && mc_visProdPerms($PRODUCT->vis) == 'block') {
  include(PATH . 'control/system/headers/403.php');
  exit;
}

// Are out of stock products disabled..
if (in_array($SETTINGS->showOutofStock, array(
  'cat',
  'no'
))) {
  if ($PRODUCT->pStock == 0) {
    header("Location: " . $MCRWR->url(array('out-of-stock')));
    exit;
  }
}

// Trade stock adjustment..
if (defined('MC_TRADE_STOCK') && MC_TRADE_STOCK > 0) {
  $PRODUCT->pStock = MC_TRADE_STOCK;
}

$family = array();
// Is category parent/child or infant..
switch($PRODUCT->catLevel) {
  case '1':
    $family[$PRODUCT->prodCat] = array($PRODUCT->catname, $PRODUCT->rwslug);
    $_SESSION['thisCat']       = $PRODUCT->prodCat;
    break;
  case '2':
    $thisParent                = mc_getTableData('categories', 'id', $PRODUCT->childOf);
    $family[$thisParent->id]   = array($thisParent->catname, $thisParent->rwslug);
    $family[$PRODUCT->prodCat] = array($PRODUCT->catname, $PRODUCT->rwslug);
    $_SESSION['thisCat']       = $PRODUCT->childOf;
    break;
  case '3':
    $thisChild                 = mc_getTableData('categories', 'id', $PRODUCT->childOf);
    $thisParent                = mc_getTableData('categories', 'id', $thisChild->childOf);
    $family[$thisParent->id]   = array($thisParent->catname, $thisParent->rwslug);
    $family[$thisChild->id]    = array($thisChild->catname, $thisChild->rwslug);
    $family[$PRODUCT->prodCat] = array($PRODUCT->catname, $PRODUCT->rwslug);
    $_SESSION['thisCat']       = $thisChild->childOf;
    break;
}

// Load javascript..
$loadJS['swipe']   = 'load';

// Display for title bar..
$headerTitleText = str_replace(array(
  '{product}',
  '{cat}'
), array(
  mc_cleanData(($PRODUCT->pTitle ? $PRODUCT->pTitle : $PRODUCT->pName)),
  mc_cleanData($PRODUCT->catname)
), str_replace('{website}', mc_safeHTML($SETTINGS->website), ($PRODUCT->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? $public_product25 : $public_product17)));
$headerTitleText = mc_cleanData($headerTitleText);

// Overwrite meta data..
if ($PRODUCT->metaKeys || $PRODUCT->pMetaKeys) {
  $overRideMetaKeys = ($PRODUCT->pMetaKeys ? mc_safeHTML($PRODUCT->pMetaKeys) : mc_safeHTML($PRODUCT->metaKeys));
}
if ($PRODUCT->metaDesc || $PRODUCT->pMetaDesc) {
  $overRideMetaDesc = ($PRODUCT->pMetaDesc ? mc_safeHTML($PRODUCT->pMetaDesc) : mc_safeHTML($PRODUCT->metaDesc));
}

// Update product count..
if ($SETTINGS->hitCounter == 'yes') {
  $MCPROD->updateProductCount($PRODUCT->pid);
  $pProductHits = ($PRODUCT->pVisits + 1);
}

// Display brands..
$brandCatDisplay = $PRODUCT->category;

// Determine from/price text..
$from = $MCPROD->determinePriceFromText($PRODUCT->pid, ($PRODUCT->pStock > 0 ? ($PRODUCT->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? $PRODUCT->pOffer : $PRODUCT->pPrice) : '0.00'));

// Recently viewed array..
if ($SETTINGS->enableRecentView == 'yes') {
  if (empty($_SESSION['recentlyViewedItems'])) {
    $_SESSION['recentlyViewedItems'] = array();
  }
  if (!isset($_SESSION['recentlyViewedItems'][$PRODUCT->pid])) {
    $_SESSION['recentlyViewedItems'][$PRODUCT->pid] = '1';
  } else {
    $_SESSION['recentlyViewedItems'][$PRODUCT->pid] = ($_SESSION['recentlyViewedItems'][$PRODUCT->pid] + 1);
  }
}

// Breadcrumbs..
foreach ($family AS $k => $v) {
  $url = $MCRWR->url(array(
    $MCRWR->config['slugs']['cat'] . '/' . $k . '/1/' . ($v[1] ? $v[1] : $MCRWR->title($v[0])),
    'c=' . $k
  ));
  $breadcrumbs[]      = '<a href="' . $url . '" title="' . mc_safeHTML($v[0]) . '">' . $v[0] . '</a>';
  $menuLinksDisplay[] = $k;
}
$breadcrumbs[] = $public_product45;

// Structured meta data..
$mc_structUrl   = $MCRWR->url(array(
  $MCRWR->config['slugs']['prd'] . '/' . $PRODUCT->pid . '/' . ($PRODUCT->rwslug ? $PRODUCT->rwslug : $MCRWR->title($PRODUCT->pName)),
  'pd=' . $PRODUCT->pid
));
$mc_structTitle = $headerTitleText;
$mc_structDesc  = (isset($overRideMetaDesc) ? $overRideMetaDesc : $SETTINGS->metaDesc);
$displayThumb = $MCPROD->loadDisplayImage($PRODUCT->pid, true);
if ($displayThumb) {
  $mc_structImage = $SETTINGS->ifolder . '/' . $displayThumb;
  $mc_structImageRaw = PATH . $displayThumb;
}

// How many pictures does this product have?
$prodPicCount = mc_rowCount('pictures WHERE `product_id` = \'' . $PRODUCT->pid . '\'');

// For banner rotator..
define('SLIDER_CAT', $_SESSION['thisCat']);

// Left menu boxes..
$skipMenuBoxes['brands']  = true;
$skipMenuBoxes['points']  = true;
include(PATH . 'control/left-box-controller.php');

$perlson = '';
if ($PRODUCT->pDownload == 'no') {
  $perlson = $MCPROD->buildPersonalisationOptions($PRODUCT->pid);
}

include(PATH . 'control/header.php');

// Are any buy options required..
$qAGCount = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `attrReqCount` FROM `" . DB_PREFIX . "attr_groups`
             WHERE `productID` = '{$PRODUCT->pid}'
             AND `isRequired`  = 'yes'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
$AG_COUNT = mysqli_fetch_object($qAGCount);

// For building of qty drop down, check main product qty..
// If main product has no stock, find next product that has..
if ($PRODUCT->pStock == 0) {
  $qtyNext      = $MCPROD->buildBuyOptions($PRODUCT, $PRODUCT->pid, true);
  $qtyDropStock = $qtyNext;
} else {
  $qtyDropStock = $PRODUCT->pStock;
}

// For offer text..
$offerText = '';
if (!defined('MC_TRADE_DISCOUNT')) {
  if ($PRODUCT->pOfferExpiry != '0000-00-00' && $PRODUCT->pOfferExpiry >= date('Y-m-d') && $PRODUCT->pOffer > 0) {
    $offerText = str_replace('{date}', date($SETTINGS->systemDateFormat, strtotime($PRODUCT->pOfferExpiry)), $public_product50);
  } else {
    if ($PRODUCT->pOffer > 0) {
      $offerText = $public_product49;
    }
  }
}

// Quantity restrictions..
$qtyRestrictions = array();
// Trade min/max..
if (defined('MC_TRADE_MIN')) {
  if (MC_TRADE_MAX > 0) {
    $PRODUCT->maxPurchaseQty = MC_TRADE_MAX;
  }
  if (MC_TRADE_MIN > 0) {
    $PRODUCT->minPurchaseQty = MC_TRADE_MIN;
  }
}
if ($PRODUCT->minPurchaseQty > 0) {
  $qtyRestrictions[] = str_replace('{min}', $PRODUCT->minPurchaseQty, $public_product53);
}
if ($PRODUCT->maxPurchaseQty > 0) {
  $qtyRestrictions[] = str_replace('{max}', $PRODUCT->maxPurchaseQty, $public_product54);
}

// Does this product have mp3 previews?
$mp3PreviewCount = mc_rowCount('mp3 WHERE `product_id` = \'' . $PRODUCT->pid . '\'');
if ($mp3PreviewCount > 0) {
  $loadJS['soundmanager'] = 'load';
}

$otherDetails = array();
// Does this product expire?
if ($PRODUCT->expiry != '0000-00-00') {
  $otherDetails[] = array(
   'icon' => 'calendar',
   'text' => str_replace('{date}',date($SETTINGS->systemDateFormat,strtotime($PRODUCT->expiry)),$mc_product[12])
  );
}
// Min/max restrictions?
if (!empty($qtyRestrictions)) {
  $otherDetails[] = array(
   'icon' => 'warning',
   'text' => $public_product52 . ' ' . implode(', ', $qtyRestrictions)
  );
}
// Is this product on offer?
if ($PRODUCT->pOffer > 0 && !defined('MC_TRADE_DISCOUNT')) {
  $otherDetails[] = array(
    'icon' => 'check',
    'text' => $offerText
  );
}
// Is offer multi buy only?
if ($PRODUCT->pMultiBuy > 0) {
  $otherDetails[] = array(
    'icon' => 'cubes',
    'text' => str_replace('{items}', $PRODUCT->pMultiBuy, $public_product48)
  );
}
// Country restrictions?
if ($PRODUCT->countryRestrictions && $PRODUCT->countryRestrictions != null) {
  $otherDetails[] = array(
    'icon' => 'globe',
    'text' => str_replace('{id}', (int) $PRODUCT->pid,$mc_product[13])
  );
}

$tpl = mc_getSavant();
$tpl->assign('TXT', array(
  $public_product,
  $public_product2,
  $public_product3,
  $public_product4,
  $public_product5,
  $public_product6,
  (isset($AG_COUNT->attrReqCount) && $AG_COUNT->attrReqCount > 0 ? $public_product46 : $public_product10),
  $public_product15,
  $public_product16,
  $public_product21,
  $public_product22,
  $public_product20,
  $public_product12,
  str_replace(array(
    '{date}',
    '{hits}'
  ), array(
    $PRODUCT->adate,
    (isset($pProductHits) ? $pProductHits : 0)
  ), ($SETTINGS->hitCounter == 'yes' ? $public_product3 : $public_product37)),
  $public_product23,
  $public_product28,
  ($SETTINGS->en_wish == 'yes' && isset($_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)][1]) && $_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)][1] > 0 ? $mc_product[18] : $public_product20),
  $public_product40,
  $public_product47,
  $mc_product,
  $public_product38,
  $public_product24,
  $public_product10,
  $public_product36,
  $public_product19
));
$tpl->assign('AVAILABILITY', ($PRODUCT->pAvailableText ? mc_cleanData($PRODUCT->pAvailableText) : $MCPROD->displayInStockThreshold($PRODUCT->pStock, $PRODUCT->pDownload)));
$tpl->assign('PRODUCT_ID', $PRODUCT->pid);
$tpl->assign('PRODUCT_ADD_TEXT', mc_cleanData($SETTINGS->priceTextDisplay));
$tpl->assign('PRODUCT_STOCK', ($PRODUCT->maxPurchaseQty > 0 && $qtyDropStock > $PRODUCT->maxPurchaseQty ? $PRODUCT->maxPurchaseQty : $qtyDropStock));
$tpl->assign('NAME', $PRODUCT->pName);
$tpl->assign('DESC', mc_txtParsingEngine($PRODUCT->pDescription));
$tpl->assign('PICTURES', ($prodPicCount > 1 ? $MCPROD->buildProductImages($PRODUCT->pid, $PRODUCT->pName) : ''));
$tpl->assign('PICTURES_COUNT', $prodPicCount);
$tpl->assign('BUY_OPTIONS', $MCPROD->buildBuyOptions($PRODUCT));
$tpl->assign('TAGS', $MCPROD->buildProductTags($PRODUCT, $public_product11));
$tpl->assign('PERSONALISATION', (isset($perlson[0]) ? $perlson[0] : ''));
$tpl->assign('PROD_PRICE', ($PRODUCT->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? '<del>' . $MCPROD->formatSystemCurrency($PRODUCT->pPrice) . '</del> ' . $MCPROD->formatSystemCurrency($PRODUCT->pOffer) : $MCPROD->formatSystemCurrency($PRODUCT->pPrice, false, true)));
$tpl->assign('PRICE', ($PRODUCT->pOffer > 0 && !defined('MC_TRADE_DISCOUNT') ? '<del>' . $MCPROD->formatSystemCurrency($PRODUCT->pPrice) . '</del> ' . $MCPROD->formatSystemCurrency($PRODUCT->pOffer) : $MCPROD->formatSystemCurrency($PRODUCT->pPrice, false, true)));
$tpl->assign('IMG', (substr($displayThumb, 0, 5) == 'http:' || substr($displayThumb, 0, 6) == 'https:' ? $displayThumb : $SETTINGS->ifolder . '/' . $displayThumb));
$tpl->assign('IMG_URL', $MCPROD->loadDisplayImage($PRODUCT->pid, false));
$tpl->assign('SOCIAL', $MCSOCIAL->params('addthis'));
$tpl->assign('PRODUCT_VIDEOS', $MCPROD->videos($PRODUCT));
$tpl->assign('RELATED_PRODUCTS', $MCPROD->productList('related', array('rcat' => $PRODUCT->pid)));
$tpl->assign('SALE_COMPARISON', ($SETTINGS->saleComparisonItems > 0 ? $MCPROD->productList('comparison', array('rcat' => $PRODUCT->pid)) : ''));
$tpl->assign('MP3_PREVIEWS', $mp3PreviewCount);
$tpl->assign('PDATA', (array) $PRODUCT);
$tpl->assign('DISQUS', ($PRODUCT->pDISQ == 'yes' && $PRODUCT->cDISQ == 'yes' ? $MCSOCIAL->disqus($PRODUCT) : ''));
$tpl->assign('FLAGS', $MCPROD->details($otherDetails));
$tpl->assign('WISH_PURCHASE', ($SETTINGS->en_wish == 'yes' && isset($_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)]) ? $_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)] : array(0,0)));
$tpl->assign('HURRY_LIMITED', $MCPROD->hurrylimited($PRODUCT, 'products/product-limited-stock.htm', 1));

// Global..
include(PATH . 'control/system/global.php');

$tpl->display(THEME_FOLDER . '/product.tpl.php');

// Clear wish list session variable..
if (isset($_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)])) {
  unset($_SESSION['wish-list-' . mc_encrypt(SECRET_KEY)]);
}

include(PATH . 'control/footer.php');

?>