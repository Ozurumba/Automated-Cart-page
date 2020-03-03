<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_updatetax6);
}
?>

<form method="post" id="form" action="?p=update-tax" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript283; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_updatetax2; ?> (%): <?php echo mc_displayHelpTip($msg_javascript107,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="price" value="" class="box">

    <label style="margin-top:10px"><?php echo $msg_productprices4; ?>: <?php echo mc_displayHelpTip($msg_javascript88); ?></label>
    <?php echo $msg_productprices6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="incr" checked="checked"> <?php echo $msg_productprices7; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="decr"> <?php echo $msg_productprices19; ?> <input type="radio" name="type" value="fixed">
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_updatetax4; ?></b><br>
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry` = 'yes'
               ORDER BY `cName`,`zName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="zones[]" value="<?php echo $ZONES->zid; ?>"> <?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_cleanData($ZONES->zName); ?> (<?php echo mc_cleanData($ZONES->zRate); ?>%)<br>
    <?php
    }
    ?>
    </div>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_updatetax5); ?>" title="<?php echo mc_cleanDataEntVars($msg_updatetax5); ?>">
</p>
</form>


</div>
