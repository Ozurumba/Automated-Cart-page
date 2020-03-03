<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('giftcerts','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_giftcerts10);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_giftcerts11);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_giftcerts12);
}

?>

<form method="post" id="form" action="?p=gift<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" enctype="multipart/form-data">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_giftcerts9 : $msg_giftcerts2); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_giftcerts3; ?>: <?php echo mc_displayHelpTip($msg_javascript549,'RIGHT'); ?></label>
    <input type="text" name="name" tabindex="<?php echo (++$tabIndex); ?>" maxlength="250" value="<?php echo (isset($EDIT->name) ? mc_safeHTML($EDIT->name) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_giftcerts6; ?>: <?php echo mc_displayHelpTip($msg_javascript550); ?></label>
    <input type="file" name="image" tabindex="<?php echo (++$tabIndex); ?>" id="img">

    <label style="margin-top:10px"><?php echo $msg_giftcerts4; ?>: <?php echo mc_displayHelpTip($msg_javascript551); ?></label>
    <input type="text" name="value" tabindex="<?php echo (++$tabIndex); ?>" id="value" value="<?php echo (isset($EDIT->value) ? $EDIT->value : '1.00'); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_giftcerts5; ?>: <?php echo mc_displayHelpTip($msg_javascript552,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->enabled) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="yes">
 <?php
 // Existing image for edit..
 if (isset($EDIT->id)) {
 ?>
 <input type="hidden" name="curimage" value="<?php echo $EDIT->image; ?>">
 <?php
 }
 ?>
 <input class="btn btn-primary" type="submit" tabindex="10" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_giftcerts9 : $msg_giftcerts2)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_giftcerts9 : $msg_giftcerts2)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=gift\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<p id="loader" style="display:none"></p>
<?php
if (mc_rowCount('giftcerts')>0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=gift&order=yes&"+jQuery('#sortable').sortable('serialize'));
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

<div class="fieldHeadWrapper"  style="margin-top:20px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_giftcerts8; ?>:</p>
</div>

<div id="sortable">
<?php
$q_certs = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "giftcerts` ORDER BY `orderBy`")
           or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_certs)>0) {
  while ($GC = mysqli_fetch_object($q_certs)) {
  $img = $SETTINGS->ifolder.'/'.PRODUCTS_FOLDER.'/'.($GC->image ? $GC->image : '');
  $sum = mc_sumCount('giftcodes WHERE `giftID` = \''.$GC->id.'\' AND `active` = \'yes\'','value');
  ?>
  <div class="panel panel-default" id="gcode-<?php echo $GC->id; ?>" style="cursor:move" title="<?php echo mc_cleanDataEntVars($msg_giftcerts30); ?>">
    <div class="panel-heading">
     <?php echo mc_safeHTML($GC->name); ?>
    </div>
    <div class="panel-body">
      <?php
      if ($img) {
      ?>
      <img src="<?php echo $img; ?>" alt="<?php echo mc_safeHTML($GC->name); ?>" title="<?php echo mc_safeHTML($GC->name); ?>" class="img-responsive"><br>
      <?php
      }
      echo str_replace(array('{value}','{enabled}','{revenue}'),array(mc_currencyFormat($GC->value),($GC->enabled=='yes' ? $msg_script5 : $msg_script6),mc_currencyFormat(mc_formatPrice($sum,true))),$msg_giftcerts13); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=gift-report&amp;code=<?php echo $GC->id; ?>"><i class="fa fa-bar-chart fa-fw"></i></a>&nbsp;&nbsp;
      <a href="?p=gift&amp;edit=<?php echo $GC->id; ?>"><i class="fa fa-pencil fa-fw"></i></a>&nbsp;&nbsp;
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=gift&amp;del=<?php echo $GC->id; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_giftcerts14; ?></span>
<?php
}
?>
</div>


</div>
