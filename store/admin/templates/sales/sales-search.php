<?php if (!defined('PARENT')) { die('Permission Denied'); }
$payStatuses   = mc_loadDefaultStatuses();
$salePlatforms = mc_loadPlatforms($msg_platforms);
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
if (isset($_GET['deleted'])) {
  echo mc_actionCompleted(str_replace('{count}',(int) $_GET['deleted'],$msg_sales40));
}
?>

<script>
//<![CDATA[
function mc_searchFieldReset() {
  jQuery('.searchAreaFields input[type="text"]').val('');
  sflds = ['status','method','country'];
  for (var i=0; i<sflds.length; i++) {
    jQuery('select[name="' + sflds[i] + '"] option:selected').prop('selected', false);
    jQuery('select[name="' + sflds[i] + '"] option:first').prop('selected', 'selected');
  }
}
//]]>
</script>

<div id="searchinputarea"<?php echo (isset($SEARCH) ? ' style="display:none"' : ''); ?>>
<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="mc_searchFieldReset();return false"><i class="fa fa-refresh fa-fw"></i></a></span><?php echo $msg_salessearch2; ?>:</p>
</div>

<form method="get" action="index.php">
<div class="formFieldWrapper searchAreaFields">
  <div class="formLeft">
    <label><?php echo $msg_salessearch9; ?>:</label>
    <input type="text" name="keys" value="<?php echo (isset($_GET['keys']) ? mc_safeHTML($_GET['keys']) : ''); ?>" class="box" autofocus>

    <label style="margin-top:10px"><?php echo $msg_salessearch3; ?>: <?php echo mc_displayHelpTip($msg_javascript326,'RIGHT'); ?></label>
    <input type="text" name="invoice" value="<?php echo (isset($_GET['invoice']) ? mc_safeHTML($_GET['invoice']) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_salessearch8; ?>: <?php echo mc_displayHelpTip($msg_javascript553,'RIGHT'); ?></label>
    <input type="text" name="code" value="<?php echo (isset($_GET['code']) ? mc_safeHTML($_GET['code']) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_salessearch4; ?>:</label>
    <input id="from" type="text" name="from" value="<?php echo (isset($_GET['from']) ? mc_safeHTML($_GET['from']) : ''); ?>" class="box">
    <input style="margin-top:5px" id="to" type="text" name="to" value="<?php echo (isset($_GET['to']) ? mc_safeHTML($_GET['to']) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_salesupdate5; ?>: <?php echo mc_displayHelpTip($msg_javascript131,'RIGHT'); ?></label>
    <select name="status">
    <option value="none">- - - - - -</option>
    <?php
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['status']) && $_GET['status']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
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
    <option value="<?php echo $ST->id; ?>"<?php echo (isset($_GET['status']) && $_GET['status']==$ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_salesexport6; ?>: <?php echo mc_displayHelpTip($msg_javascript148,'RIGHT'); ?></label>
    <select name="method">
    <option value="none">- - - - - -</option>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && !in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['method']) && $_GET['method']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_GET['method']) && $_GET['method']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_sales18; ?>: <?php echo mc_displayHelpTip($msg_javascript149); ?></label>
    <select name="country">
    <option value="0">- - - - - -</option>
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

    <label style="margin-top:10px"><?php echo $msg_sales_view[2]; ?>: <?php echo mc_displayHelpTip($msg_javascript148,'RIGHT'); ?></label>
    <select name="pfm">
    <option value="none">- - - - - -</option>
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

    <?php
    if ($SETTINGS->en_wish == 'yes') {
    ?>
    <label style="margin-top:10px"><?php echo $msg_sales_search[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript149); ?></label>
    <select name="wish">
    <option value="0">- - - - - -</option>
    <option value="yes"<?php echo (isset($_GET['wish']) && $_GET['wish']=='yes' ? ' selected="selected"' : ''); ?>><?php echo $msg_script5; ?></option>
    <option value="no"<?php echo (isset($_GET['wish']) && $_GET['wish']=='no' ? ' selected="selected"' : ''); ?>><?php echo $msg_script6; ?></option>
    </select>
    <?php
    }

    if (mc_rowCount('sales', ' WHERE `type` = \'trade\'') > 0) {
    ?>
    <label style="margin-top:10px"><?php echo $msg_sales_search[1]; ?>: <?php echo mc_displayHelpTip($msg_javascript149); ?></label>
    <select name="type">
    <option value="0">- - - - - -</option>
    <option value="personal"<?php echo (isset($_GET['type']) && $_GET['type']=='personal' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_cats3_0[3]; ?></option>
    <option value="trade"<?php echo (isset($_GET['type']) && $_GET['type']=='trade' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_cats3_0[4]; ?></option>
    </select>
    <?php
    }
    ?>
  </div>
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="p" value="sales-search">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_salessearch2); ?>" title="<?php echo mc_cleanDataEntVars($msg_salessearch2); ?>">
</p>
</form>
</div>

<?php
if (isset($SEARCH)) {
?>
<div id="searchresultsarea">
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
<div id="formField">
<form method="post" action="?p=sales-batch">
<?php
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
$q_cnts  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT count(*) AS `scount`,SUM(`grandTotal`) AS `ptotal`
           FROM `" . DB_PREFIX . "sales`
           WHERE `saleConfirmation` = 'yes'
           $searchFilter
           ") or die(mc_MySQLError(__LINE__,__FILE__));
$SUMS    = mysqli_fetch_object($q_cnts);
?>
<div class="fieldHeadWrapper">
  <p><span class="float"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a href="#" onclick="jQuery('#searchresultsarea').hide();jQuery('#searchinputarea').show();return false"><i class="fa fa-search fa-fw"></i></a></span><?php echo $msg_sales3; ?> (<?php echo number_format($SUMS->scount); ?>):</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select name="orderby" onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="0"><?php echo $msg_sales19; ?></option>
    <option value="0">- - - - - -</option>
    <option value="?p=sales-search&amp;orderby=date_desc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales20; ?></option>
    <option value="?p=sales-search&amp;orderby=date_asc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='date_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales21; ?></option>
    <option value="?p=sales-search&amp;orderby=price_desc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales22; ?></option>
    <option value="?p=sales-search&amp;orderby=price_asc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales23; ?></option>
    <option value="?p=sales-search&amp;orderby=name_asc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales24; ?></option>
    <option value="?p=sales-search&amp;orderby=name_desc<?php echo mc_queryString(array('orderby')); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales25; ?></option>
  </select>
</div>
<?php
$isShipCount = 0;
$query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`purchaseDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `sdate`
         FROM `" . DB_PREFIX . "sales`
         WHERE `saleConfirmation` = 'yes'
         $searchFilter
         ORDER BY $sqlOrder
         LIMIT $limit,".PRODUCTS_PER_PAGE."
         ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($query)>0) {
while ($SALES = mysqli_fetch_object($query)) {
$isShip = 'no';
if (mc_rowCount('purchases WHERE `saleID` = \''.$SALES->id.'\' AND `saleConfirmation` = \'yes\' AND `productType` = \'physical\'')>0) {
  $isShip = 'yes';
  ++$isShipCount;
}
?>
<div class="panel panel-default" id="salearea_<?php echo $SALES->id; ?>">
  <div class="panel-body">

    <div class="table-responsive hidden-xs">
      <table class="table salesitemtable" style="margin:0;padding:0">
      <tbody>
        <tr>
          <td><input type="checkbox" name="batch[]" onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch');mc_chkCntDiv('batch','counter','button');mc_chkCntDiv('batch','counter2','button2');mc_chkCntDiv('batch','counter3','button3');mc_chkCntDiv('batch','counter4','button4')" value="<?php echo $SALES->id; ?>"></td>
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
    <input type="checkbox" name="batch[]" onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch');mc_chkCntDiv('batch','counter','button');mc_chkCntDiv('batch','counter2','button2');mc_chkCntDiv('batch','counter3','button3');mc_chkCntDiv('batch','counter4','button4')" value="<?php echo $SALES->id; ?>"> <span class="hidden-xs"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b> - </span><i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

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
    if ($uDel=='yes') {
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

<div style="padding:10px 0 10px 10px">
  <input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'searchresultsarea');mc_chkCntDiv('batch','counter','button','searchresultsarea');mc_chkCntDiv('batch','counter2','button2','searchresultsarea');mc_chkCntDiv('batch','counter3','button3','searchresultsarea');mc_chkCntDiv('batch','counter4','button4','searchresultsarea')">&nbsp;&nbsp;&nbsp;
  <button type="submit" disabled="disabled" class="btn btn-primary" id="button"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_sales44); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-pencil fa-fw"></i></span> (<span class="counter">0</span>)</button>
  <button type="submit" name="exportSales" class="btn btn-success" id="button2" disabled="disabled"><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-save fa-fw"></i></span><span class="hidden-xs"><?php echo $msg_admin3_0[30]; ?></span> (<span class="counter2">0</span>)</button>
  <?php
  // Are PDFs enabled?
  if ($SETTINGS->pdf == 'yes') {
  ?>
  <div class="btn-group">
    <button type="button" id="button4" disabled="disabled" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-file-pdf-o fa-fw"></i></span><span class="hidden-xs"><?php echo $msg_admin_viewsale3_0[16]; ?></span> (<span class="counter4">0</span>) <i class="fa fa-chevron-down fa-fw"></i>
    </button>
    <ul class="dropdown-menu">
      <li><a href="#" onclick="mc_loadPDF('pdf-inv-batch','formField');return false"><?php echo $msg_admin_viewsale3_0[17]; ?></a></li>
      <?php
      // If nothing is being shipped, packing slips are pointless..
      if ($isShipCount > 0) {
      ?>
      <li><a href="#" onclick="mc_loadPDF('pdf-slip-batch','formField');return false"><?php echo $msg_admin_viewsale3_0[18]; ?></a></li>
      <?php
      }
      ?>
    </ul>
  </div>
  <?php
  }
  if ($uDel == 'yes') {
  ?>
  <button type="submit" onclick="return confirmMessage_Add('<?php echo mc_filterJS($msg_javascript45); ?>')" name="delsearchsales" disabled="disabled" class="btn btn-danger" id="button3"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[9]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-times fa-fw"></i></span> (<span class="counter3">0</span>)</button>
  <?php
  }
  ?>
</div>

<?php
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($SUMS->scount>0 && $SUMS->scount>PER_PAGE) {
  $PGS = new pagination(array($SUMS->scount, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<p class="noData"><?php echo $msg_sales26; ?></p>
<?php
}
?>
</form>
</div>
</div>
<?php
}
?>

</div>
