<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings31);
  //Reload..
  $SETTINGS = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"))
              or die(mc_MySQLError(__LINE__,__FILE__));
}
?>

<form method="post" id="form" action="?p=settings&amp;s=5">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings131; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings20; ?>: <?php echo mc_displayHelpTip($msg_javascript17,'RIGHT'); ?></label>
    <input type="text" name="cName" value="<?php echo mc_safeHTML($SETTINGS->cName); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings21; ?>: <?php echo mc_displayHelpTip($msg_javascript18,'LEFT'); ?></label>
    <input type="text" name="cWebsite" value="<?php echo mc_safeHTML($SETTINGS->cWebsite); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings22; ?>: <?php echo mc_displayHelpTip($msg_javascript19,'RIGHT'); ?></label>
    <input type="text" name="cTel" value="<?php echo mc_safeHTML($SETTINGS->cTel); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings23; ?>: <?php echo mc_displayHelpTip($msg_javascript20,'LEFT'); ?></label>
    <input type="text" name="cFax" value="<?php echo mc_safeHTML($SETTINGS->cFax); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings24; ?>: <?php echo mc_displayHelpTip($msg_javascript21,'RIGHT'); ?></label>
    <textarea rows="5" cols="30" name="cAddress" style="height:100px" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->cAddress); ?></textarea><br>

    <label style="margin-top:10px"><?php echo $msg_settings267; ?>: <?php echo mc_displayHelpTip($msg_javascript487,'RIGHT'); ?></label>
    <textarea rows="5" cols="30" name="cReturns" style="height:100px" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->cReturns); ?></textarea>

    <label style="margin-top:10px"><?php echo $msg_settings32; ?>: <?php echo mc_displayHelpTip($msg_javascript98,'LEFT'); ?></label>
    <textarea rows="5" cols="30" name="cOther" style="height:250px" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->cOther); ?></textarea>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
</p>
</form>


</div>
