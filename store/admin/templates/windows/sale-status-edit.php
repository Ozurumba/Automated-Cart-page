<?php if (!defined('PARENT') || !isset($_GET['statnotes'])) { die('Permission Denied'); }
$ID          = (int) $_GET['statnotes'];
$ST          = mc_getTableData('statuses', 'id', $ID);
$SALE        = mc_getTableData('sales', 'id', $ST->saleID);
$payStatuses = mc_loadDefaultStatuses();
?>

<div id="windowcontent">

<div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
  <i class="fa fa-check fa-fw"></i> <?php echo $msg_salesupdate26; ?>
</div>



<div class="fieldHeadWrapper">
  <p><?php echo $msg_salesupdate33; ?></p>
</div>

<select name="status" id="stopt">
  <?php
  foreach ($payStatuses AS $key => $value) {
  ?>
  <option value="<?php echo $key; ?>"<?php echo ($ST->orderStatus==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
  <?php
  }
  // Get additional payment statuses..
  $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                 WHERE `pMethod` IN('all','".$SALE->paymentMethod."')
                 ORDER BY `pMethod`,`statname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_add_stats)>0) {
  ?>
  <option value="0" disabled="disabled">- - - - - - - - -</option>
  <?php
  }
  while ($STS = mysqli_fetch_object($q_add_stats)) {
  ?>
  <option value="<?php echo $STS->id; ?>"<?php echo ($ST->orderStatus==$STS->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($STS->statname); ?></option>
  <?php
  }
  ?>
</select>

<textarea style="margin-top:10px;height:300px" name="notes" rows="5" cols="20"><?php echo mc_safeHTML($ST->statusNotes); ?></textarea>

<label style="margin-top:10px"><?php echo $msg_order_status_update[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript131,'RIGHT'); ?></label>
<input tabindex="<?php echo (++$tabIndex); ?>" onclick="mc_setCheckStatus(this.checked,'up_chk_status_<?php echo $ID; ?>')" type="checkbox" name="visacc" value="yes"<?php echo ($ST->visacc=='yes' ? ' checked="checked"' : ''); ?>>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="up_chk_status_<?php echo $ID; ?>" value="<?php echo ($ST->visacc=='yes' ? 'yes' : 'no'); ?>">
 <input class="btn btn-primary" onclick="mc_updateStatus('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_salesupdate33); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesupdate33); ?>">
</p>

</div>