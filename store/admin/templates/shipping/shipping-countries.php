<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">
<?php

if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('countries','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}

if (isset($OK)) {
  echo mc_actionCompleted($msg_countries5);
}
if (isset($OK2) && $count>0) {
  echo mc_actionCompleted(($count>1 ? str_replace('{count}',$count,$msg_countries20) : $msg_countries13));
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_countries14);
}
if (isset($OK4) && $count>0) {
  echo mc_actionCompleted(($count>1 ? str_replace('{count}',$count,$msg_countries16) : $msg_countries21));
}

$tabIndex = 0;

?>

<form method="post" action="?p=countries" id="form">
<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanData($msg_javascript31); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><input type="checkbox" id="log" name="log" onclick="mc_selectCountries()"> <b><?php echo $msg_settings204; ?></b></label>
    <div id="fromBoxWrapper">
     <?php
     $q_cnt = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
              ORDER BY `cName`
		          ") or die(mc_MySQLError(__LINE__, __FILE__));
     while ($COUNTRIES = mysqli_fetch_object($q_cnt)) {
     ?>
     <div><input type="checkbox" name="countries[]" value="<?php echo $COUNTRIES->id; ?>"<?php echo ($COUNTRIES->enCountry == 'yes' ? ' checked="checked"' : ''); ?>> <a href="?p=countries&amp;edit=<?php echo $COUNTRIES->id; ?>"><?php echo mc_safeHTML($COUNTRIES->cName); ?></a></div>
     <?php
     }
     ?>
    </div>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px" id="editcarea">
 <input type="hidden" name="endis" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_countries4); ?>" title="<?php echo mc_cleanDataEntVars($msg_countries4); ?>">
</p>

</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo (isset($EDIT->id) ? mc_cleanData($msg_countries12) : mc_cleanData($msg_countries11)); ?>:</p>
</div>

<form method="post" action="?p=countries<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>" id="form2">

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_countries8; ?>: <?php echo mc_displayHelpTip($msg_javascript390,'RIGHT'); ?></label>
    <input type="text" maxlength="250" name="cName" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cName) ? mc_safeHTML($EDIT->cName) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_countries9; ?>: <?php echo mc_displayHelpTip($msg_javascript391); ?></label>
    <input type="text" name="cISO" maxlength="3" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cISO) ? mc_cleanData($EDIT->cISO) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_countries23; ?>: <?php echo mc_displayHelpTip($msg_javascript391); ?></label>
    <input type="text" name="cISO_2" maxlength="2" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->cISO_2) ? mc_cleanData($EDIT->cISO_2) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_countries22; ?>: <?php echo mc_displayHelpTip($msg_javascript510,'LEFT'); ?></label>
    <input type="text" name="iso4217" maxlength="3" tabindex="<?php echo (++$tabIndex); ?>" value="<?php echo (isset($EDIT->iso4217) ? mc_cleanData($EDIT->iso4217) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_countries10; ?>: <?php echo mc_displayHelpTip($msg_javascript392,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enCountry" value="yes"<?php echo (isset($EDIT->enCountry) && $EDIT->enCountry=='yes' ? ' checked="checked"' : (!isset($EDIT->enCountry) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enCountry" value="no"<?php echo (isset($EDIT->enCountry) && $EDIT->enCountry=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update_country' : 'add_country'); ?>" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo (isset($EDIT->id) ? mc_cleanDataEntVars($msg_countries12) : mc_cleanDataEntVars($msg_countries11)); ?>" title="<?php echo (isset($EDIT->id) ? mc_cleanDataEntVars($msg_countries12) : mc_cleanDataEntVars($msg_countries11)); ?>">
 <?php
 if (isset($EDIT->id)) {
 ?>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <input class="btn btn-success" type="button" onclick="window.location='?p=countries'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <input class="btn btn-danger" type="button" onclick="mc_confirmMessageUrl('<?php echo mc_filterJS($msg_javascript45); ?>','index.php?p=countries&amp;delete=<?php echo $EDIT->id; ?>')" value="<?php echo mc_cleanDataEntVars($msg_script10); ?>" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>">
 <?php
 }
 ?>
</p>

</form>

<?php
if (isset($EDIT->id) && !isset($_POST['update_country'])) {
?>
<script>
//<![CDATA[
jQuery(document).ready(function() {
  mc_ScrollToArea('editcarea',0,0);
});
//]]>
</script>
<?php
}
?>


</div>
