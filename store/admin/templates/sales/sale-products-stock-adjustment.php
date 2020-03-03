<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>

<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace(array('{count1}','{count2}'),array($c1,$c2),$msg_admin_viewsale3_0[25]));
}
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('xxx');
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['stock_adj'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>

<div id="formField">
<form method="post" action="index.php?p=sales-view&amp;stock_adj=<?php echo mc_digitSan($_GET['stock_adj']); ?>">
<?php
// Check for incomplete sale..
$SALE = mc_getTableData('sales','id',
          mc_digitSan($_GET['stock_adj']),
          '',
          '*,DATE_FORMAT(`purchaseDate`,\''.$SETTINGS->mysqlDateFormat.'\') AS `pdate`'
        );
$q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,
          `" . DB_PREFIX . "purchases`.`id` AS `pur_id` FROM `" . DB_PREFIX . "purchases`
          LEFT JOIN `" . DB_PREFIX . "products`
          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
          WHERE `saleID`                          = '".mc_digitSan($_GET['stock_adj'])."'
          ".($SALE->saleConfirmation=='no' ? '' : 'AND `saleConfirmation` = \'yes\'')."
          ORDER BY `" . DB_PREFIX . "purchases`.`id`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_prod)>0) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_admin3_0[20]; ?> (<?php echo mysqli_num_rows($q_prod); ?>):</p>
</div>
<div class="table-responsive">
  <table class="table table-striped table-hover">
    <tbody>
    <?php
    while ($PRODUCT = mysqli_fetch_object($q_prod)) {
      $isDel = ($PRODUCT->deletedProductName ? '<span class="deletedItem">('.$msg_script53.')</span>' : '');
      ?>
      <tr>
        <td style="vertical-align:middle"><b><?php echo mc_safeHTML($PRODUCT->pName); ?></b> <?php echo ($isDel ? $isDel : ''); ?></td>
        <td style="vertical-align:middle"><input type="text" name="p[<?php echo $PRODUCT->pid; ?>]" value="<?php echo $PRODUCT->pStock; ?>" class="form-control"></td>
      </tr>
      <?php
      $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
               `" . DB_PREFIX . "attributes`.`id` AS `attributeID`
               FROM `" . DB_PREFIX . "purch_atts`
               LEFT JOIN `" . DB_PREFIX . "attributes`
               ON `" . DB_PREFIX . "purch_atts`.`attributeID` = `" . DB_PREFIX . "attributes`.`id`
               LEFT JOIN `" . DB_PREFIX . "attr_groups`
               ON `" . DB_PREFIX . "attributes`.`attrGroup` = `" . DB_PREFIX . "attr_groups`.`id`
               WHERE `" . DB_PREFIX . "purch_atts`.`saleID`    = '".mc_digitSan($_GET['stock_adj'])."'
               AND `" . DB_PREFIX . "purch_atts`.`purchaseID`  = '{$PRODUCT->pur_id}'
               AND `" . DB_PREFIX . "purch_atts`.`productID`   = '{$PRODUCT->pid}'
               ORDER BY `" . DB_PREFIX . "purch_atts`.`id`
               ") or die(mc_MySQLError(__LINE__, __FILE__));
      if (mysqli_num_rows($query) > 0) {
        while ($ATTRIBUTES = mysqli_fetch_object($query)) {
        ?>
        <tr>
          <td style="vertical-align:middle"><i class="fa fa-caret-right fa-fw"></i><?php echo mc_safeHTML($ATTRIBUTES->groupName . ': ' . $ATTRIBUTES->attrName); ?></td>
          <td style="vertical-align:middle"><input type="text" name="a[<?php echo $ATTRIBUTES->attributeID; ?>]" value="<?php echo $ATTRIBUTES->attrStock; ?>" class="form-control"></td>
        </tr>
        <?php
        }
      }
    }
    ?>
    </tbody>
  </table>
</div>
<?php
}

?>
<hr>
<div style="text-align:center">
  <input type="hidden" name="process" value="yes">
  <button type="submit" class="btn btn-primary" id="button" style="margin-right:20px"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin_viewstockadjust3_0[0]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-check fa-fw"></i></span></button>
  <input class="btn btn-success" type="button" onclick="window.location='?p=sales<?php echo (defined('INCPL_SALE') ? '-incomplete' : ''); ?>'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</div>
</form>
</div>

</div>
