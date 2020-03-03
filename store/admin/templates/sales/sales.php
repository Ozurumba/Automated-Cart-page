<?php if (!defined('PARENT')) { die('Permission Denied'); }
$payStatuses   = mc_loadDefaultStatuses();
$salePlatforms = mc_loadPlatforms($msg_platforms);
$aName         = '';
// Set filter here..
$sqlFilter  = (isset($_GET['filter']) && (array_key_exists($_GET['filter'],$payStatuses) || ctype_digit($_GET['filter'])) ? 'AND `paymentStatus` = \''.$_GET['filter'].'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['country']) && $_GET['country'] != 'all' ? 'AND `shipSetCountry` = \''.mc_digitSan($_GET['country']).'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['pm']) && in_array($_GET['pm'],array_keys($mcSystemPaymentMethods)) ? 'AND `paymentMethod` = \''.$_GET['pm'].'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['pfm']) && in_array($_GET['pfm'],array_keys($salePlatforms)) ? 'AND `platform` = \''.$_GET['pfm'].'\'' : '');
$sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').(isset($_GET['type']) && in_array($_GET['type'],array('personal','trade')) ? 'AND `type` = \''.$_GET['type'].'\'' : '');
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
if (isset($_GET['country']) && $_GET['country'] != 'all') {
  $_GET['country'] = mc_digitSan($_GET['country']);
}
// Filter by account?
if (isset($_GET['ahis'])) {
  $ACC        = mc_getTableData('accounts', 'id', mc_digitSan($_GET['ahis']));
  if (isset($ACC->id)) {
    $sqlFilter .= ($sqlFilter ? mc_defineNewline() : '').'AND `account` = \'' . mc_digitSan($_GET['ahis']) . '\'';
    $aName      = $ACC->name;
  }
}
// Get counts..
$q_cnts  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `scount`,SUM(`grandTotal`) AS `ptotal`
           FROM `" . DB_PREFIX . "sales`
           WHERE `saleConfirmation` = 'yes'
           $sqlFilter
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$SUMS    = mysqli_fetch_object($q_cnts);
?>
<div id="content">
<script>
//<![CDATA[
function confirmMessage_Add(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    return true;
  } else {
    return false;
  }
}
//]]>
</script>

<?php
if (isset($OK) && $cnt>0) {
  echo mc_actionCompleted(str_replace('{count}',1,$msg_sales40));
}
if (isset($_GET['deleted'])) {
  echo mc_actionCompleted(str_replace('{count}',(int) $_GET['deleted'],$msg_sales40));
}
?>

<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a></span><?php echo $msg_sales3.($aName ? ' - ' . mc_safeHTML($aName) : ''); ?> (<b><?php echo number_format($SUMS->scount); ?></b> / <b><?php echo str_replace('{total}',mc_currencyFormat(mc_formatPrice($SUMS->ptotal,true)),$msg_sales14); ?></b>):</p>
</div>

<form method="get" action="index.php">
<div class="formFieldWrapper" id="filters" style="display:none">
  <input type="hidden" name="p" value="sales">
  <?php
  if (isset($_GET['ahis'])) {
  ?>
  <input type="hidden" name="ahis" value="<?php echo mc_digitSan($_GET['ahis']); ?>">
  <?php
  }
  ?>
  <select name="filter">
    <option value="all"><?php echo $msg_sales11; ?></option>
    <?php
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_add_stats)>0) {
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    }
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <option value="<?php echo $ST->id; ?>"<?php echo (isset($_GET['filter']) && $_GET['filter']==$ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
  </select>

  <select name="orderby" style="margin-top:10px">
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

  <select style="margin-top:10px" name="type">
    <option value="all"><?php echo $msg_sales_export_buyers[1]; ?></option>
    <option value="personal"<?php echo (isset($_GET['type']) && $_GET['type']=='personal' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_cats3_0[3]; ?></option>
    <option value="trade"<?php echo (isset($_GET['type']) && $_GET['type']=='trade' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_cats3_0[4]; ?></option>
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
<form method="post" action="?p=sales-batch">
<?php
$query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
         FROM `" . DB_PREFIX . "sales`
         WHERE `saleConfirmation` = 'yes'
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
<div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
  <div class="panel-body">

    <div class="table-responsive hidden-xs">
      <table class="table salesitemtable" style="margin:0;padding:0">
      <tbody>
        <tr>
          <td><input onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch');mc_chkCntDiv('batch','counter','button');mc_chkCntDiv('batch','counter2','button2')" type="checkbox" name="batch[]" value="<?php echo $SALES->id; ?>"></td>
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
    <input onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch');mc_chkCntDiv('batch','counter','button');mc_chkCntDiv('batch','counter2','button2')" type="checkbox" name="batch[]" value="<?php echo $SALES->id; ?>"> <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

    <?php echo $SALES->sdate; ?><br>
    <?php echo mc_statusText($SALES->paymentStatus); ?><br>
    <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
    <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
    </div>

    <div id="prd_<?php echo $SALES->id; ?>" style="display:none">
    <hr>
    <?php
    echo str_replace(array('{id}','{count}'),array($SALES->id,mc_sumCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\'','productQty',true)),$msg_sales31);
    echo '<br>' . $msg_sales_view[1] . ': ' . mc_salePlatform($SALES->platform, $msg_platforms);
    if ($SALES->wishlist > 0) {
    ?><br><i class="fa fa-heart fa-fw"></i> <?php echo $msg_sales_screen[0]; ?>
    <?php
    }
    ?><br><br>
    <a href="?p=sales&amp;export=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales4); ?>"><i class="fa fa-save fa-fw"></i> <?php echo $msg_sales4; ?></a>
    <?php
    // Show personalisation link if there are personalised products..
    if (mc_rowCount('purch_pers WHERE `saleID` = \''.mc_digitSan($SALES->id).'\'')>0) {
    ?>
    <br><a href="?p=sales-view&amp;view-personalisation=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales37); ?>" onclick="window.open(this);return false"><i class="fa fa-print fa-fw"></i> <?php echo $msg_admin3_0[29]; ?></a>
    <?php
    }
    // Show downloads link if there are downloadable products..
    if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'download\'')>0) {
    ?>
    <br><a href="?p=downloads&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[7]); ?>"><i class="fa fa-download fa-fw"></i> <?php echo $msg_admin_viewsale3_0[7]; ?></a>
    <?php
    }
    if ($isShip == 'yes') {
    ?>
    <br><a href="?p=sales-view&amp;shipLabel=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales38); ?>" onclick="window.open(this);return false"><i class="fa fa-tags fa-fw"></i> <?php echo $msg_viewsale105; ?></a>
    <?php
    }
    if (!in_array($SALES->gateparams, array(null,''))) {
    ?>
    <br><a href="?p=sales-view&amp;gatewayParams=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_viewsale107); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_PERS_HEIGHT; ?>','<?php echo DIVWIN_PERS_WIDTH; ?>',this.title);return false;"><i class="fa fa-list-alt fa-fw"></i> <?php echo $msg_viewsale107; ?></a>
    <?php
    }
    ?>
    <br><a href="?p=sales-view&amp;stock_adj=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[24]); ?>"><i class="fa fa-bar-chart fa-fw"></i> <?php echo $msg_admin_viewsale3_0[24]; ?></a>
    <?php
    if ($uDel == 'yes') {
    ?>
    <br><a href="?p=sales&amp;delete=<?php echo $SALES->id; ?>" onclick="mc_confSaleDeletion('<?php echo mc_filterJS($msg_javascript45); ?>','<?php echo $SALES->id; ?>');return false" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>"><i class="fa fa-times fa-fw mc-red"></i> <?php echo $msg_script10; ?></a>
    <?php
    }
    ?>
    </div>
  </div>
  <div class="panel-footer">
    <span class="hidden-sm hidden-md hidden-lg"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span>
    <a href="?p=sales-view&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales13); ?>"><i class="fa fa-pencil fa-fw"></i></a>
    <a href="?p=invoice&amp;sale=<?php echo $SALES->id; ?>" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_sales5); ?>"><i class="fa fa-file-text-o fa-fw"></i></a>
    <?php
    // Show packing slip link if there are physical products..
    if ($isShip == 'yes') {
    ?>
    <a href="?p=packing-slip&amp;sale=<?php echo $SALES->id; ?>" onclick="window.open(this);return false" title="<?php echo mc_cleanDataEntVars($msg_sales6); ?>"><i class="fa fa-truck fa-fw"></i></a>
    <?php
    }
    ?>
    <a href="?p=sales-update&amp;sale=<?php echo $SALES->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_sales2); ?>"><i class="fa fa-history fa-fw"></i></a>
    &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $SALES->id; ?>')"></i>
  </div>
</div>
<?php
}
?>

<p style="padding:10px 0 10px 10px">
  <?php
  if (isset($_GET['ahis'])) {
  ?>
  <input type="hidden" name="ahis" value="<?php echo mc_digitSan($_GET['ahis']); ?>">
  <?php
  }
  ?>
  <input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCntDiv('batch','counter','button');mc_chkCntDiv('batch','counter2','button2')">&nbsp;&nbsp;&nbsp;
  <button type="submit" disabled="disabled" class="btn btn-primary" id="button"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_sales44); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-pencil fa-fw"></i></span> (<span class="counter">0</span>)</button>
  <?php
  if ($uDel == 'yes') {
  ?>
  <button type="submit" onclick="return confirmMessage_Add('<?php echo mc_filterJS($msg_javascript45); ?>')" name="delsales" disabled="disabled" class="btn btn-danger" id="button2"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[9]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-times fa-fw"></i></span> (<span class="counter2">0</span>)</button>
  <?php
  }
  echo (isset($_GET['ahis']) ? '<button class="btn btn-success" type="button" onclick="window.location=\'?p=accounts\'"><span class="hidden-xs">'.mc_cleanDataEntVars($msg_script11).'</span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-arrow-left fa-fw"></i></span></button>' : '');
  ?>
</p>

</form>
</div>
<?php
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($SUMS->scount > 0 && $SUMS->scount > PER_PAGE) {
  $PGS = new pagination(array($SUMS->scount, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<p class="noData"><?php echo $msg_sales17; ?></p>
</form>
</div>
<?php
}
?>

</div>
