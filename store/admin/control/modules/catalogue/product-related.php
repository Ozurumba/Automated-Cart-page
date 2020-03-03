<?php

if (!defined('PARENT')) {
  include(PATH . 'control/modules/header/403.php');
}

// Load language file(s)..
include(MCLANG . 'catalogue/product-pictures.php');
include(MCLANG . 'catalogue/product-manage.php');
include(MCLANG . 'catalogue/product-related.php');
include(MCLANG . 'catalogue/product-attributes.php');
include(MCLANG . 'tools/update-prices.php');
include(MCLANG . 'catalogue/product-offers.php');

if ($cmd == 'load-related-products') {
  $id   = substr($_GET['pr'], strpos($_GET['pr'], '-') + 1, strlen($_GET['pr']));
  $sale = mc_digitSan($_GET['sale']);
  if (ctype_digit($id) && $id > 0 && ctype_digit($_GET['cur'])) {
    $html = '';
    $all  = '';
    $prod = array();
    // If sale is higher than 0, get purchase products and attributes..
    if ($sale > 0) {
      $a    = $MCPROD->getPurchaseProducts($sale);
      $prod = (!empty($a[0]) ? $a[0] : array());
    }
    $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                  FROM `" . DB_PREFIX . "products`
                  LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                  WHERE `category`  = '{$id}'
                  AND `pEnable`     = 'yes'
                  " . ($_GET['cur'] > 0 ? 'AND `' . DB_PREFIX . 'products`.`id` != \'' . mc_digitSan($_GET['cur']) . '\'' : '') . "
                  " . ($_GET['cur'] == 0 && !in_array($_GET['pg'], array(
                  'prices',
                  'stock',
                  'sale',
                  'saled'
                  )) ? 'AND (`' . DB_PREFIX . 'products`.`pOffer` = \'\' OR `' . DB_PREFIX . 'products`.`pOffer` <= \'0\')' : '') . "
                    " . ($sale > 0 && in_array($_GET['pg'], array(
                    'sale',
                    'saled'
                  )) && is_array($prod) && !empty($prod) ? 'AND `' . DB_PREFIX . 'products`.`id` NOT IN(' . implode(',', $prod) . ')' : '') . "
                  " . (isset($_GET['dl']) && $_GET['dl'] == 'yes' ? 'AND `pDownload` = \'yes\'' : '') . "
                  GROUP BY `" . DB_PREFIX . "products`.`id`
                  ORDER BY `pName`
                  ") or die(mc_MySQLError(__LINE__, __FILE__));
    while ($PR = mysqli_fetch_object($q_products)) {
      if (in_array($_GET['pg'], array(
        'stock'
      ))) {
        $html .= (mc_digitSan($_GET['cur']) == 0 ? '<input type="hidden" name="products[]" value="' . $PR->pid . '">' : '') . '<input type="checkbox" name="product[]" value="' . $PR->pid . '"> ' . mc_cleanData($PR->pName) . (mc_digitSan($_GET['cur']) == 0 ? ' - (' . $PR->pStock . ')' : '') . '<br>';
      } else {
        if (in_array($_GET['pg'], array(
          'sale',
          'saled'
        ))) {
          $html .= '<input onclick="changeButtonCount(this.form,\'single\')" type="checkbox" name="product[]" value="' . $PR->pid . '"> ' . mc_cleanData($PR->pName) . ' - ' . mc_currencyFormat(mc_formatPrice($PR->pOffer > 0 ? $PR->pOffer : $PR->pPrice)) . '<br>';
        } else {
          $html .= (mc_digitSan($_GET['cur']) == 0 ? '<input type="hidden" name="products[]" value="' . $PR->pid . '">' : '') . '<input type="checkbox" name="product[]" value="' . $PR->pid . '"> ' . mc_cleanData($PR->pName) . (mc_digitSan($_GET['cur']) == 0 ? ' - ' . mc_currencyFormat(mc_formatPrice($PR->pPrice)) : '') . '<br>';
        }
      }
    }
    if ($html) {
      if (in_array($_GET['pg'], array(
        'sale',
        'saled'
      ))) {
        $all = '<input type="checkbox" name="log" value="all" id="log" onclick="changeButtonCount(this.form,\'all\')"> <b>' . $msg_prodrelated7 . '</b><br>';
      } else {
        $all = '<input type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b>' . $msg_prodrelated7 . '</b><br>';
      }
    }
    if (in_array($_GET['pg'], array(
      'prices',
      'stock'
    ))) {
      $msg_productoffers14 = $msg_productprices17;
    }
    if (in_array($_GET['pg'], array(
      'saled'
    ))) {
      $msg_prodrelated8 = $msg_productprices18;
    }
    $jcode = ($html ? mc_cleanData($all . $html) : mc_cleanData((mc_digitSan($_GET['cur']) > 0 ? $msg_prodrelated8 : $msg_productoffers14)));
    echo $JSON->encode(array(
      $jcode
    ));
    exit;
  }
}

if (isset($_POST['process'])) {
  if (!empty($_POST['product'])) {
    $MCPROD->addRelatedProducts();
    $OK = true;
  } else {
    header("Location: index.php?p=product-related&product=" . mc_digitSan($_GET['product']));
    exit;
  }
}

if (isset($_GET['del']) && ctype_digit($_GET['del']) && $uDel == 'yes') {
  $cnt = $MCPROD->deleteRelatedProduct();
  $OK2 = true;
}

$pageTitle  = $msg_productmanage17 . ': ' . $pageTitle;

include(PATH . 'templates/header.php');
include(PATH . 'templates/catalogue/product-related.php');
include(PATH . 'templates/footer.php');

?>