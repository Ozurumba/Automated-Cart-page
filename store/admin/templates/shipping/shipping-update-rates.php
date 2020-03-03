<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_updaterates6);
}
?>

<form method="post" id="form" action="?p=update-rates" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript173; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_updaterates2; ?>: <?php echo mc_displayHelpTip($msg_javascript174,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="price" value="" class="box">

    <label style="margin-top:10px"><?php echo $msg_updaterates10; ?>: <?php echo mc_displayHelpTip($msg_javascript447,'RIGHT'); ?></label>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="rpref[]" value="flat"> <?php echo $msg_updaterates7; ?><br>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="rpref[]" value="peri" onclick="if(this.checked){jQuery('#perItemAdd').show()}else{jQuery('#perItemAdd').hide()}"> <?php echo $msg_javascript577; ?><br>
	  <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="rpref[]" value="perc"> <?php echo $msg_updaterates8; ?><br>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="rpref[]" value="weight"> <?php echo $msg_updaterates9; ?><br>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="rpref[]" value="tare"> <?php echo $msg_updaterates11; ?>

    <div id="perItemAdd" style="display:none">
	    <label style="margin-top:10px"><?php echo $msg_updaterates12; ?> (<?php echo $msg_javascript577; ?>): <?php echo mc_displayHelpTip($msg_javascript585); ?></label>
      <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="add" value="" class="box">
	  </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productprices4; ?>: <?php echo mc_displayHelpTip($msg_javascript88); ?></label>
    <?php echo $msg_productprices6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="incr" checked="checked"> <?php echo $msg_productprices7; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="decr"> <?php echo $msg_productprices19; ?> <input type="radio" name="type" value="fixed">
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
    <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_updaterates4; ?></b><br>
    <?php
    $q_zones = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "zones`.`id` AS `zid` FROM `" . DB_PREFIX . "zones`
               LEFT JOIN `" . DB_PREFIX . "countries`
               ON `" . DB_PREFIX . "zones`.`zCountry` = `" . DB_PREFIX . "countries`.`id`
               WHERE `enCountry` = 'yes'
               ORDER BY `cName`,`zName`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ZONES = mysqli_fetch_object($q_zones)) {
    ?>
    <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="zones[]" value="<?php echo $ZONES->zid; ?>"> <?php echo mc_cleanData($ZONES->cName); ?> - <?php echo mc_cleanData($ZONES->zName); ?><br>
    <?php
    }
    ?>
    </div>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_updaterates5); ?>" title="<?php echo mc_cleanDataEntVars($msg_updaterates5); ?>">
</p>
</form>


</div>
