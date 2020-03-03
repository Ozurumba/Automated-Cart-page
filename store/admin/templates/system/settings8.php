<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'offlineDate');
include(PATH . 'templates/js-loader/date-picker.php');
?>
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

<form method="post" id="form" action="?p=settings&amp;s=8">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings84; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings11; ?>: <?php echo mc_displayHelpTip($msg_javascript12,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableCart" value="yes"<?php echo ($SETTINGS->enableCart=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="enableCart" value="no"<?php echo ($SETTINGS->enableCart=='no' ? ' checked="checked"' : ''); ?>>

    <label style="margin-top:10px"><?php echo $msg_settings85; ?>: <?php echo mc_displayHelpTip($msg_javascript228,'RIGHT'); ?></label>
    <input type="text" name="offlineDate" value="<?php echo ($SETTINGS->offlineDate!='0000-00-00' ? mc_convertMySQLDate($SETTINGS->offlineDate, $SETTINGS) : ''); ?>" class="box" id="offlineDate" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings243; ?>: <?php echo mc_displayHelpTip($msg_javascript463,'RIGHT'); ?></label>
    <input type="text" name="offlineIP" value="<?php echo $SETTINGS->offlineIP; ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_settings86; ?>: <?php echo mc_displayHelpTip($msg_javascript229,'LEFT'); ?></label>
    <?php
    if ($SETTINGS->enableBBCode == 'yes') {
      define('BB_BOX', 'offlineText');
      include(PATH . 'templates/bbcode-buttons.php');
    }
    ?>
    <textarea rows="5" cols="30" class="tarea" name="offlineText" id="offlineText" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($SETTINGS->offlineText); ?></textarea>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
</p>
</form>


</div>
