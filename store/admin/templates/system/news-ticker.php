<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT  = mc_getTableData('news_ticker','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_newsticker5);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_newsticker9);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_newsticker7);
}
?>

<form method="post" id="form" action="?p=news<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p>
  <?php
  echo (isset($EDIT->id) ? $msg_newsticker6 : $msg_newsticker); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_newsticker2; ?>: <?php echo mc_displayHelpTip($msg_javascript422,'RIGHT'); ?></label>
    <input type="text" name="newsText" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->newsText) ? mc_safeHTML($EDIT->newsText) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_newsticker8; ?>: <?php echo mc_displayHelpTip($msg_javascript423,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="yes"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='yes' ? ' checked="checked"' : (!isset($EDIT->id) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="no"<?php echo (isset($EDIT->enabled) && $EDIT->enabled=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_newsticker6 : $msg_newsticker)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_newsticker6 : $msg_newsticker)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=news\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>
<?php
if (mc_rowCount('news_ticker')>0) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  jQuery("#sortable").sortable({
    update : function (data) {
      jQuery("#loader").load("index.php?p=news&order=yes&"+jQuery('#sortable').sortable('serialize'));
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
<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><span class="float" id="loader"></span><span class="float" id="loader_msg" style="display:none" onclick="jQuery(this).hide()"></span><?php echo $msg_newsticker4; ?></p>
</div>

<div id="sortable">
<?php
$q_nt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "news_ticker`
        ORDER BY `orderBy`
        ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_nt)>0) {
  while ($TICKER = mysqli_fetch_object($q_nt)) {
  ?>
  <div class="panel panel-default" id="nt-<?php echo $TICKER->id; ?>" style="cursor:move" title="<?php echo mc_cleanDataEntVars($msg_newsticker10); ?>">
    <div class="panel-body">
      <?php echo mc_cleanData($TICKER->newsText); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=news&amp;edit=<?php echo $TICKER->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=news&amp;del='.$TICKER->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_newsticker3; ?></span>
<?php
}
?>
</div>


</div>
