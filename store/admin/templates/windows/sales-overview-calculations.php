<?php if (!defined('PARENT') || !isset($_GET['profit'])) { die('Permission Denied'); }
$ID = (int) $_GET['profit'];
$P  = mc_getTableData('products', 'id', $ID);
if (!isset($P->id)) {
  exit;
}
$salePlatforms = mc_loadPlatforms($msg_platforms);
$SQL = '';
if (isset($_GET['cat']) && (int) $_GET['cat'] > 0) {
  $SQL = 'AND `categoryID` = \'' . mc_digitSan($_GET['cat']) . '\'';
}
if (isset($_GET['from']) && isset($_GET['to']) && mc_checkValidDate($_GET['from'])!='0000-00-00' && mc_checkValidDate($_GET['to'])!='0000-00-00') {
  $SQL .= mc_defineNewline() . 'AND `' . DB_PREFIX.'purchases`.`purchaseDate` BETWEEN \'' . mc_convertCalToSQLFormat($_GET['from'], $SETTINGS) . '\' AND \'' . mc_convertCalToSQLFormat($_GET['to'], $SETTINGS) . '\'';
}
if (isset($_GET['pfm']) && in_array($_GET['pfm'],array_keys($salePlatforms))) {
  $SQL .= mc_defineNewline() . 'AND `' . DB_PREFIX.'purchases`.`platform` = \'' . $_GET['pfm'] . '\'';
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
       SUM(`productQty`) AS `cnt`,
       `" . DB_PREFIX . "purchases`.`id` AS `pid`
       FROM `" . DB_PREFIX . "purchases`
       LEFT JOIN `" . DB_PREFIX . "products`
       ON `" . DB_PREFIX . "purchases`.`productID`     = `" . DB_PREFIX . "products`.`id`
       LEFT JOIN `" . DB_PREFIX . "sales`
       ON `" . DB_PREFIX . "purchases`.`saleID`        = `" . DB_PREFIX . "sales`.`id`
       WHERE `" . DB_PREFIX . "sales`.`saleConfirmation`   = 'yes'
       AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
       AND `" . DB_PREFIX . "products`.`id`                = '{$P->id}'
       $SQL
       GROUP BY `" . DB_PREFIX . "purchases`.`productID`,`" . DB_PREFIX . "purchases`.`salePrice`,`" . DB_PREFIX . "purchases`.`productQty`
       ORDER BY `cnt` DESC,`" . DB_PREFIX . "products`.`pName`
       ") or die(mc_MySQLError(__LINE__,__FILE__));
while ($PURCHASE = mysqli_fetch_object($q_p)) {
  if (!isset($build[$P->id])) {
    $build[$P->id]              = array();
    $build[$P->id]['sales']     = 0;
    $build[$P->id]['sell-cost'] = '0.00';
    $build[$P->id]['profit']    = '0.00';
    $build[$P->id]['loss']      = '0.00';
    $build[$P->id]['id']        = $P->id;
    $build[$P->id]['counts']    = array(0,0);
  }
  $thisTotal                  = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
  $build[$P->id]['name']      = $PURCHASE->pName;
  $build[$P->id]['sales']     = number_format(($build[$P->id]['sales'] + $PURCHASE->cnt));
  $build[$P->id]['sell-cost'] = mc_formatPrice(($build[$P->id]['sell-cost'] + $thisTotal));
  // Profit calculation..
  if ($PURCHASE->salePrice >= $PURCHASE->pPurPrice) {
    $t1   = mc_formatPrice($PURCHASE->pPurPrice * $PURCHASE->cnt);
    $t2   = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
    $diff = mc_formatPrice($t1 - $t2);
    $build[$P->id]['profit'] = mc_formatPrice(($build[$P->id]['profit'] + $diff));
    $build[$P->id]['counts'][0] = ($build[$P->id]['counts'][0] + $PURCHASE->cnt);
  } else {
    $t1   = mc_formatPrice($PURCHASE->pPurPrice * $PURCHASE->cnt);
    $t2   = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
    $diff = mc_formatPrice($t2 - $t1);
    $build[$P->id]['loss']      = mc_formatPrice(($build[$P->id]['loss'] + $diff));
    $build[$P->id]['counts'][1] = ($build[$P->id]['counts'][1] + $PURCHASE->cnt);
  }
}
?>
<div id="windowcontent">

  <div class="fieldHeadWrapper">
    <p>
      <i class="fa fa-money fa-fw"></i> <b><?php echo $msg_sales_product_overview[5]; ?></b>
      <hr style="margin:5px 0 5px 0">
      <span class="profit_product"><?php echo mc_safeHTML($P->pName); ?></span>
      <hr style="margin:5px 0 5px 0">
      <span class="profit_product"><?php echo $msg_sales_product_overview[6]; ?>: <b><?php echo (isset($build[$P->id]['sell-cost']) ? mc_currencyFormat(mc_formatPrice($build[$P->id]['sell-cost']), true) : mc_currencyFormat(mc_formatPrice('0.00'), true)); ?></b></span>
    </p>
  </div>

  <?php
  if (isset($build[$P->id]['sell-cost']) && $build[$P->id]['sell-cost'] > 0) {
  ?>
  <div class="table-responsive" style="margin-top:10px">
    <table class="table table-striped">
      <thead>
        <tr>
          <th><?php echo $msg_sales_product_overview[10]; ?></th>
          <th class="text-right"><?php echo $msg_sales_product_overview[11]; ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo str_replace(array('{count}','{cost}'), array($build[$P->id]['counts'][0],mc_currencyFormat(mc_formatPrice($P->pPurPrice),true)), $msg_sales_product_overview[7]); ?></td>
          <td class="text-right"><?php echo ($build[$P->id]['profit'] > 0 ? mc_currencyFormat(mc_formatPrice($build[$P->id]['profit']),true) : 'N/A'); ?></td>
        </tr>
        <tr>
          <td><?php echo str_replace(array('{count}','{cost}'), array($build[$P->id]['counts'][1],mc_currencyFormat(mc_formatPrice($P->pPurPrice),true)), $msg_sales_product_overview[8]); ?></td>
          <td class="text-right"><?php echo ($build[$P->id]['loss'] > 0 ? '-' . mc_currencyFormat(mc_formatPrice($build[$P->id]['loss']),true) : 'N/A'); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
  $final = mc_formatPrice($build[$P->id]['profit'] - $build[$P->id]['loss']);
  ?>
  <div class="table-responsive" style="margin-top:10px">
    <table class="table">
      <tbody>
        <tr>
          <td><?php echo $msg_sales_product_overview[9]; ?></td>
          <td class="text-right"><b><?php echo ($final > 0 ? '' : '-') . mc_currencyFormat(mc_formatPrice($final),true); ?></b></td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
  } else {
  ?>
  <hr>
  <p class="noData"><?php echo $msg_sales_product_overview[13]; ?></p>
  <?php
  }
  ?>

</div>