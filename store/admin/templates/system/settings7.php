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

<form method="post" id="form" action="?p=settings&amp;s=7">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings65; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings66; ?>: <?php echo mc_displayHelpTip($msg_javascript211,'RIGHT'); ?></label>
    <textarea rows="5" cols="30" name="publicFooter" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->publicFooter); ?></textarea>
  </div>
  <div class="formRight">
    <label><?php echo $msg_settings67; ?>: <?php echo mc_displayHelpTip($msg_javascript210,'LEFT'); ?></label>
    <textarea rows="5" cols="30" name="adminFooter" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->adminFooter); ?></textarea>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
</p>
</form>


</div>
