<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('price_points','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_pricepoints8);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_pricepoints9);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_pricepoints10);
}
?>

<form method="post" action="?p=price-points<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_pricepoints6 : $msg_pricepoints5); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_pricepoints2; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="priceFrom" value="<?php echo (isset($EDIT->priceFrom) ? mc_cleanData($EDIT->priceFrom) : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_pricepoints3; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="priceTo" value="<?php echo (isset($EDIT->priceTo) ? mc_cleanData($EDIT->priceTo) : '0.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_pricepoints4; ?>: <?php echo mc_displayHelpTip($msg_javascript271,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="priceText" value="<?php echo (isset($EDIT->priceText) ? mc_safeHTML($EDIT->priceText) : ''); ?>" class="box" maxlength="200">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_pricepoints6 : $msg_pricepoints5)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_pricepoints6 : $msg_pricepoints5)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=price-points\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<?php
if (mc_rowCount('price_points')>0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=price-points&order=yes&"+jQuery('#sortable').sortable('serialize'));
      jQuery('#loader_msg').show('slow');
      jQuery('#loader_msg').html('<i class="fa fa-check fa-fw"></i>&nbsp;&nbsp;').fadeOut(6000);
    }
  });
});
//]]>
</script>
<?php
}
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_pricepoints7; ?>:</p>
</div>

<div id="sortable">
<?php
$q_pp = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "price_points`
        ORDER BY `orderBy`
        ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_pp)>0) {
  while ($POINTS = mysqli_fetch_object($q_pp)) {
  ?>
  <div class="panel panel-default" id="pp-<?php echo $POINTS->id; ?>" style="cursor:move" title="<?php echo mc_cleanDataEntVars($msg_cats20); ?>">
    <div class="panel-body">
      <?php echo ($POINTS->priceText ? mc_cleanData($POINTS->priceText) : mc_cleanData($POINTS->priceFrom).' - '.mc_cleanData($POINTS->priceTo)); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=price-points&amp;edit=<?php echo $POINTS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=price-points&amp;del='.$POINTS->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_pricepoints11; ?></span>
<?php
}
?>
</div>


</div>
