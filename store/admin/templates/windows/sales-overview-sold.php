<?php if (!defined('PARENT') || !isset($_GET['sold'])) { die('Permission Denied'); }
$ID = (int) $_GET['sold'];
$P  = mc_getTableData('products', 'id', $ID);
if (!isset($P->id)) {
  exit;
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
       SUM(`productQty`) AS `cnt`,
       SUM(`salePrice`) AS `s_price`
       FROM `" . DB_PREFIX . "purchases`
       LEFT JOIN `" . DB_PREFIX . "products`
       ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
       LEFT JOIN `" . DB_PREFIX . "sales`
       ON `" . DB_PREFIX . "purchases`.`saleID`    = `" . DB_PREFIX . "sales`.`id`
       WHERE `" . DB_PREFIX . "sales`.`saleConfirmation`   = 'yes'
       AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
       AND `" . DB_PREFIX . "purchases`.`productID` = '{$P->id}'
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$PURCHASE = mysqli_fetch_object($q_p);
// Total Cost..
$tCost = $PURCHASE->s_price;
$calculations = array(
  'profit' => array(0,0.00),
  'loss' => array(0,0.00)
);
?>
<div id="windowcontent">

  <div class="fieldHeadWrapper">
    <p>
      <i class="fa fa-money fa-fw"></i> <b><?php echo $msg_sales_product_overview[5]; ?></b>
      <hr style="margin:5px 0 5px 0">
      <span class="profit_product"><?php echo mc_safeHTML($P->pName); ?></span>
      <hr style="margin:5px 0 5px 0">
      <span class="profit_product"><?php echo $msg_sales_product_overview[6]; ?>: <b><?php echo mc_currencyFormat(mc_formatPrice($tCost),true); ?></b></span>
    </p>
  </div>

  <?php
  if ($tCost > 0) {
  // Calculations..
  $q_1 = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
         `" . DB_PREFIX . "purchases`.`productQty` AS `quantity`
         FROM `" . DB_PREFIX . "purchases`
         LEFT JOIN `" . DB_PREFIX . "products`
         ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
         LEFT JOIN `" . DB_PREFIX . "sales`
         ON `" . DB_PREFIX . "purchases`.`saleID`    = `" . DB_PREFIX . "sales`.`id`
         WHERE `" . DB_PREFIX . "sales`.`saleConfirmation`   = 'yes'
         AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
         AND `" . DB_PREFIX . "purchases`.`productID` = '{$P->id}'
         ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($P1 = mysqli_fetch_object($q_1)) {
    // Profit calculation..
    if ($PURCHASE->salePrice >= $PURCHASE->pPurPrice) {
      $t1   = mc_formatPrice($PURCHASE->pPurPrice * $PURCHASE->cnt);
      $t2   = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
      $diff = mc_formatPrice($t1 - $t2);
      $build[$PURCHASE->pr_id]['profit'] = mc_formatPrice(($build[$PURCHASE->pr_id]['profit'] + $diff));
    } else {
      $t1   = mc_formatPrice($PURCHASE->pPurPrice * $PURCHASE->cnt);
      $t2   = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
      $diff = mc_formatPrice($t2 - $t1);
      $build[$PURCHASE->pr_id]['loss'] = mc_formatPrice(($build[$PURCHASE->pr_id]['loss'] + $diff));
    }



    if ($P1->quantity > 1) {
      $sPrice = mc_formatPrice($P1->salePrice / $P1->quantity);
      $diff   = mc_formatPrice($sPrice - $P->pPurPrice);
    } else {
      $sPrice = $P1->salePrice;
      $diff   = mc_formatPrice($sPrice - $P->pPurPrice);
    }
    if ($sPrice >= $P->pPurPrice) {
      $calculations['profit'][0] = ($calculations['profit'][0] + $P1->quantity);
      $calculations['profit'][1] = mc_formatPrice($calculations['profit'][1] + $diff);
    } else {
      $calculations['loss'][0] = ($calculations['loss'][0] + $P1->quantity);
      $calculations['loss'][1] = mc_formatPrice($calculations['loss'][1] + $diff);
    }
  }
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
          <td><?php echo str_replace(array('{count}','{cost}'), array($calculations['profit'][0],mc_currencyFormat(mc_formatPrice($P->pPurPrice),true)), $msg_sales_product_overview[7]); ?></td>
          <td class="text-right"><?php echo ($calculations['profit'][1] > 0 ? mc_currencyFormat(mc_formatPrice($calculations['profit'][1]),true) : 'N/A'); ?></td>
        </tr>
        <tr>
          <td><?php echo str_replace(array('{count}','{cost}'), array($calculations['loss'][0],mc_currencyFormat(mc_formatPrice($P->pPurPrice),true)), $msg_sales_product_overview[8]); ?></td>
          <td class="text-right"><?php echo ($calculations['loss'][1] > 0 ? '-' . mc_currencyFormat(mc_formatPrice($calculations['loss'][1]),true) : 'N/A'); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
  $final = ($calculations['profit'][1] - $calculations['loss'][1]);
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