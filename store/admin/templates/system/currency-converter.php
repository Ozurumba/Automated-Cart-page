<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">
<script>
//<![CDATA[
function mc_updateCurRates() {
  if (!jQuery('input[name="auto"]:checked').val()) {
    jQuery('#form').submit();
  } else {
    mc_ShowSpinner();
    jQuery(document).ready(function() {
     jQuery.ajax({
      type: 'POST',
	    url: 'index.php?p=currency-converter&processAuto=yes',
      data: jQuery("#content > form").serialize(),
      dataType: 'json',
      success: function (data) {
        mc_CloseSpinner();
        if (data[0] == 'OK') {
			    window.location = '?p=currency-converter&ok=yes';
			  } else {
			    window.location = '?p=currency-converter';
			  }
      }
     });
    });
    return false;
  }
}
//]]>
</script>
<?php
if (isset($OK) || isset($_GET['ok'])) {
  echo mc_actionCompleted($msg_currency5);
}
?>

<?php echo str_replace('{base}',$currencies[$SETTINGS->baseCurrency].' ('.$SETTINGS->baseCurrency.')',$msg_currency); ?><br><br>

<form method="post" id="form" action="?p=currency-converter">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_currency11; ?></p>
</div>

<?php
$r       = 0;
$q_cur   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "currencies`
           WHERE `curname` != ''
           AND `currency`  != '{$SETTINGS->baseCurrency}'
           AND `enableCur`  = 'yes'
           ORDER BY `curname`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
while ($CV = mysqli_fetch_object($q_cur)) {
?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <input type="hidden" name="cur[<?php echo $CV->currency; ?>]" value="yes"><input type="checkbox" name="iso[<?php echo $CV->currency; ?>]" value="<?php echo $CV->currency; ?>" checked="checked"> <?php echo mc_cleanData($CV->curname); ?>
    </div>
    <div class="panel-body">
      <label><?php echo $msg_currency6; ?>:</label>
      <input type="text" class="box" name="pref[<?php echo $CV->currency; ?>]" value="<?php echo str_replace(array('&'),array('&amp;'),$CV->currencyDisplayPref); ?>">
      <input style="margin-top:10px" type="text" class="box" name="rate[<?php echo $CV->currency; ?>]" value="<?php echo $CV->rate; ?>">
    </div>
    <div class="panel-footer">
      <?php echo ($CV->enableCur=='yes' ? '<b>1</b>'.$SETTINGS->baseCurrency.' = <b>'.$MCCRV->convert('1.00',$CV->currency,$CV->rate).'</b>'.$CV->currency : ''); ?>
    </div>
  </div>
<?php
}
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_currency12; ?></p>
</div>

<div class="formFieldWrapper">
  <?php
  $r       = 0;
  $q_cur   = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "currencies`
             WHERE `curname` != ''
             AND `currency`  != '{$SETTINGS->baseCurrency}'
             AND `enableCur`  = 'no'
             ORDER BY `curname`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CV = mysqli_fetch_object($q_cur)) {
  ?>
  <div><input type="hidden" name="cur[<?php echo $CV->currency; ?>]" value="yes"><input type="checkbox" name="iso[<?php echo $CV->currency; ?>]" value="<?php echo $CV->currency; ?>"> <?php echo mc_cleanData($CV->curname); ?></span></div>
  <?php
  }
  ?>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
  <input type="hidden" name="process" value="yes">
  <input type="checkbox" name="auto" value="yes" checked="checked"> <?php echo $msg_currency8; ?><br><br>
  <input class="btn btn-primary" type="button" onclick="mc_updateCurRates()" value="<?php echo mc_cleanDataEntVars($msg_currency4); ?>" title="<?php echo mc_cleanDataEntVars($msg_currency4); ?>">
</p>
</form>


</div>
