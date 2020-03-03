<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>

<div id="content">
<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_productoffers9);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_productoffers25);
}
$CAT = mc_getTableData('categories','id', mc_digitSan($_GET['view']));
define('CALBOX', 'newExpiry');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div class="fieldHeadWrapper">
  <p><?php echo str_replace('{cat}',(isset($CAT->catname) ? mc_safeHTML($CAT->catname) : ''),$msg_productoffers16); ?>:</p>
</div>

<form method="post" id="form" action="?p=special-offers&amp;view=<?php echo mc_digitSan($_GET['view']); ?>" onsubmit="if(jQuery('#newPrice').val()=='0.00'){mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript490); ?>');return false;}else{return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')}">
<div class="formFieldWrapper">
  <div>
  <input type="hidden" name="processUpdateOffers" value="yes">
  <label><?php echo $msg_productoffers22; ?>:</label>
  <input class="box" type="text" name="newPrice" id="newPrice" value="0.00">
  <label style="margin-top:10px"><?php echo $msg_productoffers23; ?>:</label>
  <input class="box" type="text" name="newExpiry" id="newExpiry">
  <label style="margin-top:10px"><?php echo $msg_productoffers27; ?>:</label>
  <input class="box" type="text" name="newBuy" id="newBuy" value="0">
  <div style="margin-top:10px">
  <input type="submit" value="<?php echo mc_cleanDataEntVars($msg_productoffers24); ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers24); ?>" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=special-offers'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
  </div>
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'panel')"></p>
</div>

<?php
$q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,
              DATE_FORMAT(`pOfferExpiry`,'" . $SETTINGS->mysqlDateFormat . "') AS `pEDate`
              FROM `" . DB_PREFIX . "products`
              LEFT JOIN `" . DB_PREFIX . "prod_category`
              ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
              WHERE `category`                = '".mc_digitSan($_GET['view'])."'
              AND `pEnable`                   = 'yes'
              AND `pOffer`                    > 0
              GROUP BY `" . DB_PREFIX . "products`.`id`
              ORDER BY `pName`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
while ($P = mysqli_fetch_object($q_products)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <input type="checkbox" name="products[]" value="<?php echo $P->pid; ?>"> <b><?php echo mc_safeHTML($P->pName); ?></b><br><br><span class="offerExpiry"><?php echo $msg_productoffers21; ?>: <?php echo ($P->pEDate>0 ? $P->pEDate : 'N/A').' / '.$msg_productoffers28.': '.$P->pMultiBuy; ?></span><br>
    <span class="strike"><?php echo mc_currencyFormat(mc_formatPrice($P->pPrice)); ?></span> / <?php echo mc_currencyFormat(mc_formatPrice($P->pOffer)); ?>
  </div>
  <?php
  if ($uDel=='yes') {
  ?>
  <div class="panel-footer">
    <a href="?p=special-offers&amp;view=<?php echo mc_digitSan($_GET['view']); ?>&amp;product=<?php echo $P->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers13); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
  </div>
  <?php
  }
  ?>
</div>
<?php
}
?>
</form>
</div>
