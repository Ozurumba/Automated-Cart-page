<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
$salePlatforms = mc_loadPlatforms($msg_platforms);
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_hit_overview5);
}

$build = array();
$SQL   = '';
$httpq = array();
if (isset($_GET['cat']) && (int) $_GET['cat'] > 0) {
  $SQL          = 'AND `categoryID` = \'' . mc_digitSan($_GET['cat']) . '\'';
  $httpq['cat'] = $_GET['cat'];
}
if (isset($_GET['from']) && isset($_GET['to']) && mc_checkValidDate($_GET['from'])!='0000-00-00' && mc_checkValidDate($_GET['to'])!='0000-00-00') {
  $SQL          .= mc_defineNewline() . 'AND `' . DB_PREFIX.'purchases`.`purchaseDate` BETWEEN \'' . mc_convertCalToSQLFormat($_GET['from'], $SETTINGS) . '\' AND \'' . mc_convertCalToSQLFormat($_GET['to'], $SETTINGS) . '\'';
  $httpq['from'] = $_GET['from'];
  $httpq['to']   = $_GET['to'];
}
if (isset($_GET['pfm']) && in_array($_GET['pfm'],array_keys($salePlatforms))) {
  $SQL         .= mc_defineNewline() . 'AND `' . DB_PREFIX.'purchases`.`platform` = \'' . $_GET['pfm'] . '\'';
  $httpq['pfm'] = $_GET['pfm'];
}
$q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SUM(`productQty`) AS `cnt`
       FROM `" . DB_PREFIX . "purchases`
       WHERE `saleConfirmation` = 'yes'
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$tH         = mysqli_fetch_object($q_c);
$totalHits  = (isset($tH->cnt) ? $tH->cnt : 0);
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
       SUM(`productQty`) AS `cnt`,
       `" . DB_PREFIX . "purchases`.`id` AS `pid`,
       `" . DB_PREFIX . "products`.`id` AS `pr_id`
       FROM `" . DB_PREFIX . "purchases`
       LEFT JOIN `" . DB_PREFIX . "products`
       ON `" . DB_PREFIX . "purchases`.`productID`     = `" . DB_PREFIX . "products`.`id`
       LEFT JOIN `" . DB_PREFIX . "sales`
       ON `" . DB_PREFIX . "purchases`.`saleID`        = `" . DB_PREFIX . "sales`.`id`
       WHERE `" . DB_PREFIX . "sales`.`saleConfirmation`   = 'yes'
       AND `" . DB_PREFIX . "purchases`.`saleConfirmation` = 'yes'
       AND `" . DB_PREFIX . "products`.`id`                > 0
       $SQL
       GROUP BY `" . DB_PREFIX . "purchases`.`productID`,`" . DB_PREFIX . "purchases`.`salePrice`,`" . DB_PREFIX . "purchases`.`productQty`
       ORDER BY `cnt` DESC,`" . DB_PREFIX . "products`.`pName`
       LIMIT $limit," . SALES_OVERVIEW_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  =  (isset($c->rows) ? $c->rows : '0');
?>
<div class="fieldHeadWrapper" style="margin-top:10px">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a><?php echo (mysqli_num_rows($q_p)>0 ? ' <a class="export_product_overview" href="#" onclick="jQuery(\'#ov_form\').submit();return false" title="'.mc_cleanDataEntVars($msg_slsproductoverview2).'"><i class="fa fa-save fa-fw"></i></a>' : ''); ?></span><?php echo mc_cleanDataEntVars($msg_javascript163); ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <form method="get" action="index.php">
  <div>
  <select name="cat">
    <option value="all"><?php echo $msg_productmanage5; ?></option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 AND `enCat`      = 'yes'
                 ORDER BY `catname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
  </select>
  <select style="margin-top:10px" name="pfm">
    <option value="all"><?php echo $msg_sales_view[2]; ?></option>
    <?php
    if (!empty($salePlatforms)) {
    foreach ($salePlatforms AS $key => $value) {
	  ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['pfm']) && $_GET['pfm']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
	  }
    }
    ?>
  </select>
  <label style="margin-top:10px"><?php echo $msg_stats4; ?>:</label>
  <input type="hidden" name="p" value="sales-product-overview">
  <input type="text" name="from" value="<?php echo (isset($_GET['from']) && !in_array($_GET['from'],array('','0000-00-00')) ? mc_checkValidDate($_GET['from']) : ''); ?>" class="box" id="from">
  <input style="margin-top:5px" type="text" name="to" value="<?php echo (isset($_GET['to']) && !in_array($_GET['to'],array('','0000-00-00')) ? mc_checkValidDate($_GET['to']) : ''); ?>" class="box" id="to">
  <input style="margin-top:10px" type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_stats5); ?>" title="<?php echo mc_cleanDataEntVars($msg_stats5); ?>">
  </div>
  </form>
</div>

<?php
if (mysqli_num_rows($q_p)>0) {
?>
<form method="post" action="?p=sales-product-overview" id="ov_form">
<div class="table-responsive">
  <input type="hidden" name="exp" value="yes">
  <table class="table table-striped table-hover">
  <thead>
    <tr>
      <th><?php echo $msg_sales_product_overview[0]; ?></th>
      <th><?php echo $msg_sales_product_overview[1]; ?></th>
      <th><?php echo $msg_sales_product_overview[2]; ?></th>
      <th><?php echo $msg_sales_product_overview[3]; ?></th>
      <th><?php echo $msg_sales_product_overview[4]; ?></th>
    </tr>
  </thead>
  <tbody>
  <?php
  while ($PURCHASE = mysqli_fetch_object($q_p)) {
    if (!isset($build[$PURCHASE->pr_id])) {
      $build[$PURCHASE->pr_id]              = array();
      $build[$PURCHASE->pr_id]['sales']     = 0;
      $build[$PURCHASE->pr_id]['sell-cost'] = '0.00';
      $build[$PURCHASE->pr_id]['profit']    = '0.00';
      $build[$PURCHASE->pr_id]['loss']      = '0.00';
      $build[$PURCHASE->pr_id]['id']        = $PURCHASE->pr_id;
    }
    $thisTotal                            = mc_formatPrice($PURCHASE->salePrice * $PURCHASE->cnt);
    $build[$PURCHASE->pr_id]['name']      = $PURCHASE->pName;
    $build[$PURCHASE->pr_id]['sales']     = number_format(($build[$PURCHASE->pr_id]['sales'] + $PURCHASE->cnt));
    $build[$PURCHASE->pr_id]['sell-cost'] = mc_formatPrice(($build[$PURCHASE->pr_id]['sell-cost'] + $thisTotal));
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
  }
  if (!empty($build)) {
    array_multisort($build, SORT_DESC);
    foreach ($build AS $bK => $bV) {
    $perc  = 0;
    // Prevent division by zero errors..
    if ($bV['sales'] > 0) {
      $perc = @number_format(($bV['sales'] / $totalHits) * 100, STATS_DECIMAL_PLACES);
    }
    ?>
    <tr>
      <td><input type="hidden" name="expcol1[]" value="<?php echo $bV['id']; ?>"><a href="?p=add-product&amp;edit=<?php echo $bV['id']; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($bV['name']); ?>"><?php echo mc_safeHTML($bV['name']); ?></a></td>
      <td><input type="hidden" name="expcol2[]" value="<?php echo $bV['sales']; ?>"><?php echo $bV['sales']; ?></td>
      <td><input type="hidden" name="expcol3[]" value="<?php echo $perc; ?>%"><?php echo $perc; ?>%</td>
      <td><input type="hidden" name="expcol4[]" value="<?php echo mc_formatPrice($bV['sell-cost']); ?>"><?php echo mc_currencyFormat(mc_formatPrice($bV['sell-cost']), true); ?></td>
      <td><input type="hidden" name="expcol5[]" value="<?php echo mc_formatPrice($bV['profit'] - $bV['loss']); ?>"><a href="?p=sales-product-overview&amp;profit=<?php echo $bV['id'] . (!empty($httpq) ? '&amp;' . http_build_query($httpq,'&amp;') : ''); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_OVPROF_HEIGHT; ?>','<?php echo DIVWIN_OVPROF_WIDTH; ?>',this.title);return false;"><?php echo mc_currencyFormat(mc_formatPrice($bV['profit'] - $bV['loss']), true); ?></a></td>
    </tr>
    <?php
    }
  }
  ?>
  </tbody>
  </table>
</div>
</form>
<?php

define('PER_PAGE', SALES_OVERVIEW_PER_PAGE);
if ($countedRows>0 && $countedRows > PER_PAGE) {
  $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}

} else {
?>
<span class="noData"><?php echo ($SQL ? $msg_slsproductoverview5 : $msg_slsproductoverview6); ?></span>
<?php
}
?>
</div>
