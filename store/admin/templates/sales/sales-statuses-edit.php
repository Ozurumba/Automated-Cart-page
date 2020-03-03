<?php if (!defined('PARENT')) { die('Permission Denied'); }
$saleID = ($_GET['sale'] == 'batch' ? 'batch' : (int) $_GET['sale']);
?>

<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_salesupdate29);
}
if (isset($_GET['deldone'])) {
  echo mc_actionCompleted($msg_salesupdate30);
}
if (isset($_GET['error'])) {
  echo mc_actionCompletedError(str_replace('{ref}', mc_safeHTML(urldecode($_GET['error'])), $msg_order_status_update[3]));
}
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_salesupdate25; ?> (<?php echo mc_rowCount('status_text'); ?>):</p>
</div>

<form method="post" action="?p=sales-statuses<?php echo (isset($_GET['id']) ? '&amp;id='.mc_digitSan($_GET['id']) : ''); ?>&amp;sale=<?php echo $saleID; ?>">

<div class="formFieldWrapper">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="0">- - - - - -</option>
  <?php
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "status_text` ORDER BY `statTitle`")
           or die(mc_MySQLError(__LINE__,__FILE__));
  while ($STATUS = mysqli_fetch_object($query)) {
  ?>
  <option value="?p=sales-statuses&amp;id=<?php echo $STATUS->id; ?>&amp;sale=<?php echo $saleID; ?>"<?php echo (isset($_GET['id']) && $_GET['id']==$STATUS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($STATUS->ref); ?></option>
  <?php
  }
  ?>
  </select>
</div>
<?php
if (isset($_GET['id'])) {
$EDIT = mc_getTableData('status_text','id',mc_digitSan($_GET['id']));
?>
<div class="formFieldWrapper">
  <label><?php echo $msg_order_status_update[2]; ?>:</label>
  <input type="text" name="ref" value="<?php echo mc_safeHTML($EDIT->ref); ?>" class="box" maxlength="250" tabindex="<?php echo (++$tabIndex); ?>">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_salesupdate31; ?>:</label>
  <input type="text" name="statTitle" value="<?php echo mc_safeHTML($EDIT->statTitle); ?>" class="box" tabindex="<?php echo (++$tabIndex); ?>">
</div>

<div class="formFieldWrapper">
 <label><?php echo $msg_salesupdate32; ?>:</label>
 <textarea rows="5" cols="30" name="statText" class="textarea" tabindex="<?php echo (++$tabIndex); ?>"><?php echo ($EDIT->statText && $EDIT->statText!=null ? mc_safeHTML($EDIT->statText) : ''); ?></textarea>
</div>

<p style="text-align:center;padding:10px 0 0 0">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" name="update" value="<?php echo mc_cleanDataEntVars($msg_salesupdate33); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesupdate33); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <?php
 if ($saleID > 0) {
 ?>
 <button class="btn btn-success" type="button" onclick="window.location='?p=sales-update&amp;sale=<?php echo $saleID; ?>'"><?php echo $msg_script11; ?></button>
 <?php
 }
 if (isset($_GET['id']) && $uDel == 'yes') {
 ?>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <button class="btn btn-danger" type="button" onclick="return mc_confirmMessageUrl('<?php echo mc_filterJS($msg_javascript45); ?>','?p=sales-statuses&amp;del=<?php echo mc_digitSan($_GET['id']); ?>&amp;sale=<?php echo $saleID; ?>')"><?php echo $msg_salesupdate34; ?></button>
 <?php
 }
 ?>
</p>
<?php
} else {
?>
<button class="btn btn-success" type="button" onclick="window.location='?p=sales-update&amp;sale=<?php echo $saleID; ?>'"><?php echo $msg_script11; ?></button>
<?php
}
?>
</form>


</div>