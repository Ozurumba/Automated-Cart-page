<?php if (!defined('PARENT') && !empty($_POST['batch'])) { die('Permission Denied'); }
// Nuke dupes..
$batchIDs = array_unique($_POST['batch']);
?>
<div id="content">
<script>
//<![CDATA[
function checkform() {
  var message = '';
  if (jQuery('#text').val()=='' || jQuery('#title').val()=='') {
    message = '- <?php echo mc_cleanDataEntVars($msg_javascript156); ?>';
  }
  if (message) {
    mc_alertBox(message);
    if (jQuery('#title').val()=='') {
      jQuery('#title').focus();
    } else {
      jQuery('#text').focus();
    }
    return false;
  } else {
    return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>');
  }
}
jQuery(document).ready(function() {
  jQuery('input[name="search_statuses"]').autocomplete({
	  source: 'index.php?p=sales-update&search=yes',
		minLength: 3,
		select: function(event, ui) {
      jQuery('#statusesShow').hide();
      if (ui.item.value > 0) {
        mc_ShowSpinner();
        mc_statusOption(ui.item.label, ui.item.text);
      }
		}
  });
});
//]]>
</script>

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',(!empty($orderArr) ? count($orderArr) : '0'),$msg_salesbatch10));
}
$payStatuses = mc_loadDefaultStatuses();
$find        = array('{WEBSITE_NAME}','{WEBSITE_URL}');
$replace     = array(mc_cleanData($SETTINGS->website),$SETTINGS->ifolder);
?>

<div id="form_field">
<form method="post" id="form" action="?p=sales-batch" onsubmit="return checkform()">
<div class="fieldHeadWrapper" id="mswhead">
  <p>
  <span class="float">
   <a href="#" onclick="if (jQuery('#text').val()!=''){mc_addNewStatus('<?php echo mc_filterJS($msg_javascript318); ?>');return false}else{mc_alertBox('<?php echo mc_filterJS($msg_javascript324); ?>');jQuery('#text').focus();return false}"><i class="fa fa-save fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate23); ?>"></i></a>
   <a href="#" onclick="jQuery('#statusesShow').slideToggle();return false"><i class="fa fa-search fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate24); ?>"></i></a>
   <a href="?p=sales-statuses&amp;sale=batch" title="<?php echo mc_cleanDataEntVars($msg_salesupdate25); ?>" onclick="window.open(this);return false"><i class="fa fa-pencil fa-fw" title="<?php echo mc_cleanDataEntVars($msg_salesupdate25); ?>"></i></a>
  </span>
  <?php echo $msg_salesbatch2; ?> (<?php echo count($batchIDs); ?>):</p>
</div>

<div class="formFieldWrapper" style="display:none" id="statusesShow">
  <input type="text" class="box" name="search_statuses" value="" placeholder="<?php echo mc_cleanDataEntVars($msg_order_status_update[1]); ?>">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_admin3_0[25]; ?></label>
    <input class="box" type="text" id="title" name="title" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo str_replace(array('{website}'),array(mc_cleanData($SETTINGS->website)),mc_cleanDataEntVars($msg_salesbatch3)); ?>">

    <label style="margin-top:10px"><?php echo $msg_admin3_0[26]; ?></label>
    <textarea rows="5" tabindex="<?php echo (++$tabIndex); ?>" cols="30" id="text" name="text"><?php echo mc_safeHTML(str_replace($find,$replace,file_get_contents(MCLANG_REL.'email-templates/admin/order-updated-batch.txt'))); ?></textarea>
    <span id="helpBlock" class="help-block"><?php echo $msg_salesbatch7; ?></span>

    <label style="margin-top:10px"><?php echo $msg_salesbatch8; ?></label>
    <?php echo $msg_script5; ?> <input onclick="if(this.checked){jQuery('#copym').slideDown()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="email" value="yes" checked="checked">
    <?php echo $msg_script6; ?> <input onclick="if(this.checked){jQuery('#copym').slideUp()}" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="email" value="no">

    <div id="copym">
      <label style="margin-top:10px"><?php echo $msg_salesupdate12; ?></label>
      <input type="text" tabindex="<?php echo (++$tabIndex); ?>" class="box" name="copy_email" value="">
    </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="LeftRight">
    <label><?php echo $msg_salesbatch11; ?>: <?php echo mc_displayHelpTip($msg_javascript538,'RIGHT'); ?></label>
    <select name="status" id="status" tabindex="<?php echo (++$tabIndex); ?>">
    <?php
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   WHERE `pMethod` IN('all')
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_add_stats)>0) {
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    }
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <option value="<?php echo $ST->id; ?>"><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_order_status_update[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript131,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="visacc" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="visacc" value="no" checked="checked">

  </div>
</div>

<div class="fieldHeadWrapper" style="margin-top:15px">
  <p><input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'form_field')" checked="checked">&nbsp;&nbsp;&nbsp;<?php echo $msg_salesbatch12; ?>:</p>
</div>

<?php
if (!empty($batchIDs)) {
foreach ($batchIDs AS $bK => $bV) {
$SALES  = mc_getTableData('sales','id',(int) $bV,'','*,DATE_FORMAT(`purchaseDate`,\''.$SETTINGS->mysqlDateFormat.'\') AS `sdate`');
?>
<div class="panel panel-default">
  <div class="panel-body">

    <div class="table-responsive hidden-xs">
      <table class="table salesitemtable" style="margin:0;padding:0">
      <tbody>
        <tr>
          <td><input onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch')" type="checkbox" name="batch[]" value="<?php echo $SALES->id; ?>" checked="checked"></td>
          <td><a href="?p=sales-search&amp;invoice=<?php echo $SALES->invoiceNo; ?>&amp;process=yes" onclick="window.open(this);return false"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></a></td>
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
    <input type="checkbox" onclick="mc_dualCheckStatus(this.checked,'<?php echo $SALES->id; ?>','batch')" name="batch[]" value="<?php echo $SALES->id; ?>" checked="checked"> <a href="?p=sales-search&amp;invoice=<?php echo $SALES->invoiceNo; ?>&amp;process=yes" onclick="window.open(this);return false"><b>#<?php echo mc_saleInvoiceNumber($SALES->invoiceNo, $SETTINGS); ?></b></a> - <i class="fa fa-user fa-fw<?php echo ($SALES->account > 0 ? ' mc-vis-sale' : ' mc-guest-sale'); ?>"></i> <?php echo ($SALES->bill_1 ? mc_safeHTML($SALES->bill_1) : $msg_admin3_0[19]); ?><br><br>

    <?php echo $SALES->sdate; ?><br>
    <?php echo mc_statusText($SALES->paymentStatus); ?><br>
    <?php echo mc_paymentMethodName($SALES->paymentMethod); ?>
    <div class="manageCost"><?php echo mc_currencyFormat(mc_formatPrice($SALES->grandTotal,true)); ?></div>
    </div>

  </div>
</div>
<?php
}
}
?>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_salesbatch2); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesbatch2); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=sales'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>

</form>
</div>
<p>&nbsp;</p>
</div>
