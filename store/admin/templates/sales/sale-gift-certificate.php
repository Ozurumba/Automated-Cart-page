<?php if (!defined('PARENT')) { die('Permission Denied'); }
$_GET['viewSaleGift'] = (isset($_GET['viewSaleGift']) ? mc_digitSan($_GET['viewSaleGift']) : '0');
$_GET['purID']        = (isset($_GET['purID']) ? mc_digitSan($_GET['purID']) : '0');
if ($_GET['viewSaleGift']==0 || $_GET['purID']==0) {
  exit;
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_giftcerts28);
}

?>
<div class="alert alert-info">
  <?php
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['viewSaleGift'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>
<?php

$q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcodes`
     WHERE `saleID` = '{$_GET['viewSaleGift']}'
     AND `purchaseID` = '{$_GET['purID']}'
     ORDER BY `id`")
     or die(mc_MySQLError(__LINE__,__FILE__));
while ($GIFT = mysqli_fetch_object($q)) {
?>
<form method="post" action="?p=gift&amp;viewSaleGift=<?php echo mc_digitSan($_GET['viewSaleGift']); ?>&amp;purID=<?php echo $_GET['purID']; ?>">
<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanDataEntVars($msg_giftcerts20); ?> (<b><?php echo ($GIFT->code ? $GIFT->code : $msg_giftcerts31); ?></b>):</p>
</div>


<div class="formFieldWrapper">
  <div class="formLeft">
  <label><?php echo $msg_giftcerts21; ?>:</label>
  <input type="text" class="box" name="from_name" value="<?php echo mc_safeHTML($GIFT->from_name); ?>"></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts23; ?>:</label>
  <input type="text" class="box" name="from_email" value="<?php echo mc_safeHTML($GIFT->from_email); ?>"></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts22; ?>:</label>
  <input type="text" class="box" name="to_name" value="<?php echo mc_safeHTML($GIFT->to_name); ?>"></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts24; ?>:</label>
  <input type="text" class="box" name="to_email" value="<?php echo mc_safeHTML($GIFT->to_email); ?>"></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts32; ?>:</label>
  <textarea name="message" rows="5" cols="20"><?php echo mc_safeHTML($GIFT->message); ?></textarea></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts26; ?> / <?php echo $msg_giftcerts27; ?>:</label>
  <input type="text" class="box" name="value" value="<?php echo mc_safeHTML($GIFT->value); ?>">
  <input style="margin-top:5px" type="text" class="box" name="redeemed" value="<?php echo mc_safeHTML($GIFT->redeemed); ?>"></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts25; ?>:</label>
  <textarea name="notes" rows="5" cols="20" ><?php echo mc_safeHTML($GIFT->notes); ?></textarea></p>

  <label style="margin-top:10px"><?php echo $msg_giftcerts5; ?>:</label>
  <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo ($GIFT->enabled=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="no"<?php echo ($GIFT->enabled=='no' ? ' checked="checked"' : ''); ?>></p>
  </div>
</div>

<p style="margin:20px 0 20px 0;text-align:center">
  <input type="hidden" name="process" value="yes">
  <input type="hidden" name="giftID" value="<?php echo $GIFT->id; ?>">
  <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_giftcerts7); ?>" title="<?php echo mc_cleanDataEntVars($msg_giftcerts7); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <button class="btn btn-default" type="button" onclick="mc_resendGiftCert('<?php echo mc_digitSan($_GET['viewSaleGift']); ?>','<?php echo (int) $_GET['purID']; ?>','<?php echo $GIFT->id; ?>','<?php echo mc_filterJS($msg_javascript45); ?>',200,150);return false"><i class="fa fa-envelope fa-fw"></i><span class="hidden-xs"> <?php echo mc_cleanDataEntVars($msg_viewsale120); ?></span></button>
</p>

<hr>

</div>
</form>
<?php
}
?>
</div>