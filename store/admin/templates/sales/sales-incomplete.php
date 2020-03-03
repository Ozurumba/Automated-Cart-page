<?php if (!defined('PARENT')) { die('Permission Denied'); }
$payStatuses   = mc_loadDefaultStatuses();
$salePlatforms = mc_loadPlatforms($msg_platforms);
// Set filter here..
$sqlFilter  = (isset($_GET['filter']) && (array_key_exists($_GET['filter'],$payStatuses) || ctype_digit($_GET['filter'])) ? 'AND `paymentStatus` = \''.$_GET['filter'].'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['country']) && $_GET['country'] != 'all' ? 'AND `shipSetCountry` = \''.mc_digitSan($_GET['country']).'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['pm']) && in_array($_GET['pm'],array_keys($mcSystemPaymentMethods)) ? 'AND `paymentMethod` = \''.$_GET['pm'].'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['pfm']) && in_array($_GET['pfm'],array_keys($salePlatforms)) ? 'AND `platform` = \''.$_GET['pfm'].'\'' : '');
$sqlOrder   = '`id` DESC';
if (isset($_GET['orderby'])) {
  switch($_GET['orderby']) {
    case 'date_asc':   $sqlOrder   = '`purchaseDate`';         break;
    case 'date_desc':  $sqlOrder   = '`purchaseDate` DESC';    break;
    case 'price_asc':  $sqlOrder   = '`grandTotal`*1000';      break;
    case 'price_desc': $sqlOrder   = '`grandTotal`*1000 DESC'; break;
    case 'name_asc':   $sqlOrder   = '`bill_1`';               break;
    case 'name_desc':  $sqlOrder   = '`bill_1` DESC';          break;
  }
}
if (isset($_GET['country'])) {
  $_GET['country'] = mc_digitSan($_GET['country']);
}
// Get counts..
$q_cnts  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `scount`,SUM(`grandTotal`) AS `ptotal`
           FROM `" . DB_PREFIX . "sales`
           WHERE `saleConfirmation` = 'no'
           AND `paymentStatus`     IN('','pending')
           $sqlFilter
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$SUMS    = mysqli_fetch_object($q_cnts);
?>
<div id="content">

<?php
if (isset($OK) && count($_POST['del'])>0) {
  echo mc_actionCompleted(str_replace('{count}', count($_POST['del']), $msg_admin3_0[32]));
}

?>

<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_sales3; ?> (<b><?php echo number_format($SUMS->scount); ?></b> / <b><?php echo str_replace('{total}',mc_currencyFormat(mc_formatPrice($SUMS->ptotal,true)),$msg_sales14); ?></b>):</p>
</div>

<form method="get" action="index.php">
<div class="formFieldWrapper" id="filters" style="display:none">
  <input type="hidden" name="p" value="sales-incomplete">
  <select name="orderby">
    <option value="0"><?php echo $msg_sales19; ?></option>
    <option value="date_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales20; ?></option>
    <option value="date_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales21; ?></option>
    <option value="price_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales22; ?></option>
    <option value="price_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales23; ?></option>
    <option value="name_asc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales24; ?></option>
    <option value="name_desc"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales25; ?></option>
  </select>

  <select style="margin-top:10px" name="country">
    <option value="all"><?php echo $msg_sales29; ?></option>
    <?php
    $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
           WHERE `enCountry` = 'yes'
           ORDER BY `cName`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($C = mysqli_fetch_object($q_c)) {
    ?>
    <option value="<?php echo $C->id; ?>"<?php echo (isset($_GET['country']) && $_GET['country']==$C->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($C->cName); ?></option>
    <?php
    }
    ?>
  </select>

  <select style="margin-top:10px" name="pm">
    <option value="all"><?php echo $msg_sales28; ?></option>
    <?php
    if (!empty($mcSystemPaymentMethods)) {
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && !in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['pm']) && $_GET['pm']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['pm']) && $_GET['pm']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
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
  </select><br>
  <input type="submit" value="<?php echo mc_cleanDataEntVars($msg_admin3_0[18]); ?>" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[18]); ?>" class="btn btn-primary">
</div>
</form>

<div id="formField">
<form method="post" action="?p=sales-incomplete" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<?php
$delPerms = 0;
$query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
         FROM `" . DB_PREFIX . "sales`
         WHERE `saleConfirmation` = 'no'
         AND `paymentStatus`     IN('','pending')
         $sqlFilter
         ORDER BY $sqlOrder
         LIMIT $limit,".PRODUCTS_PER_PAGE."
         ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($query)>0) {
while ($SALES = mysqli_fetch_object($query)) {
$isShip = 'no';
if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
  $isShip = 'yes';
}
?>
<div class="panel panel-default">
  <div class="panel-body">

    <div class="table-responsive hidden-xs">
      <table class="table salesitemtable" style="margin:0;padding:0">
      <tbody>
        <tr>
          <?php
          if ($uDel=='yes' && $SALES->paymentStatus!='pending') {
          ++$delPerms;
          ?>
          <td><input type="checkbox" name="del[]" onclick="mc_chkCnt('del','counter','button')" value="<?php echo $SALES->id; ?>"></td>
          <?php
          } else {
          ?>
          <td><i class="fa fa-warning fa-fw"></i></td>
          <?php
          }
          ?>
          <td><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></td>
          <td><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?></td>
          <td><?php echo $SALES->sdate; ?></td>
          <td><?php echo mc_paymentMethodName($SALES->paymentMethod); ?></td>
          <td><?php echo mc_statusText($SALES->paymentStatus); ?></td>
          <td><b><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></b></td>
        </tr>
      </tbody>
      </table>
    </div>

    <div class="hidden-sm hidden-md hidden-lg">
    <?php
    if ($uDel=='yes') {
    ?><input type="checkbox" name="del[]" onclick="mc_chkCnt('del','counter','button')" value="<?php echo $SALES->id; ?>">
    <?php
    }
    ?>
    <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

    <?php echo $SALES->sdate; ?><br>
    <?php echo mc_statusText($SALES->paymentStatus); ?><br>
    <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
    <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
    </div>

  </div>
  <div class="panel-footer">
    <span class="hidden-sm hidden-md hidden-lg"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span>
    <?php
    if ($SALES->paymentStatus == 'pending') {
    ?>
    <span class="mc-red"><a href="?p=sales-view&amp;sale=<?php echo $SALES->id; ?>" class="mc-red"><i class="fa fa-warning fa-fw"></i> <?php echo $msg_salesincomplete3; ?></a></span>
    <?php
    } else {
    ?>
    <a href="?p=sales-view&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales13); ?>"><i class="fa fa-pencil fa-fw"></i></a>
    <?php
    }
    ?>
  </div>
</div>
<?php
}

if ($uDel=='yes' && $delPerms > 0) {
?>
<p style="padding:10px 0 10px 10px">
<input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCnt('del','counter','button');">&nbsp;&nbsp;&nbsp;
<button type="submit" disabled="disabled" id="button" class="btn btn-danger"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin3_0[31]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-times fa-fw"></i></span> (<span class="counter">0</span>)</button>
</p>
<?php
}
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($SUMS->scount>0 && $SUMS->scount>PER_PAGE) {
  $PGS = new pagination(array($SUMS->scount, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<p class="noData"><?php echo $msg_sales17; ?></p>
<?php
}
?>
</div>
</form>

</div>
