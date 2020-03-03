<?php
if (!defined('PARENT') || !in_array($_GET['conf'],array_keys($mcSystemPaymentMethods))) {
  die('Invalid parameter: "conf='.mc_safeHTML($_GET['conf']).'" is not supported');
}
$skipStatusDisplay = array('pending');
$PM = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "methods` WHERE `method` = '{$_GET['conf']}'"))
      or die(mc_MySQLError(__LINE__,__FILE__));
// Parameters..
$params = array();
$q      = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "methods_params` WHERE `method` = '{$_GET['conf']}'");
while ($M = mysqli_fetch_object($q)) {
  $params[$M->param] = $M->value;
}

// Global options..
include(PATH.'templates/catalogue/payment-methods/header.php');

?>
<div class="fieldHeadWrapper">
  <p>
  <?php
  if (DISPLAY_HELP_LINK) {
  ?>
  <span class="pull-right"><a href="../docs/<?php echo $PM->docs; ?>.html" onclick="window.open(this);return false"><i class="fa fa-book fa-fw"></i></a></span>
  <?php
  }
  ?>
  <?php echo $PM->display; ?></p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $mc_34_admin[0]; ?>: <?php echo mc_displayHelpTip($msg_javascript562,'RIGHT'); ?></label>
    <input type="text" name="params[perishable-key]" value="<?php echo (isset($params['perishable-key']) ? mc_safeHTML($params['perishable-key']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <div class="formRight">
    <label><?php echo $mc_34_admin[1]; ?>: <?php echo mc_displayHelpTip($msg_javascript563); ?></label>
    <input type="password" name="params[secret-key]" value="<?php echo (isset($params['secret-key']) ? mc_safeHTML($params['secret-key']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <div class="formRight">
    <label><?php echo $lng_37_gateways[0]; ?>: </label>
    <input type="password" name="params[endpoint-secret]" value="<?php echo (isset($params['endpoint-secret']) ? mc_safeHTML($params['endpoint-secret']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <div class="formRight">
    <label><?php echo $mc_34_admin[2]; ?>: <?php echo mc_displayHelpTip($msg_javascript563); ?></label>
    <input type="text" name="params[image]" value="<?php echo (isset($params['image']) ? mc_safeHTML($params['image']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
 <label><?php echo $msg_settings68; ?>: <?php echo mc_displayHelpTip($msg_javascript67,'RIGHT'); ?></label>
 <?php
 if ($SETTINGS->enableBBCode == 'yes') {
   define('BB_BOX', 'info');
   include(PATH . 'templates/bbcode-buttons.php');
 }
 ?>
 <textarea rows="5" cols="30" name="info" id="info" class="textarea" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($PM->info); ?></textarea>
</div>

<?php
// Payment statuses..
include(PATH.'templates/catalogue/payment-methods/statuses.php');
?>

<div class="formFieldWrapper">
  <div class="formLeft">
   <label><?php echo $msg_settings156; ?>: <?php echo mc_displayHelpTip($msg_javascript543); ?></label>
   <select name="params[locale]">
   <?php
   foreach ($stripeLocale AS $k => $v) {
   ?>
   <option value="<?php echo $k; ?>"<?php echo ($params['locale']==$k ? ' selected="selected"' : ''); ?>><?php echo $v; ?></option>
   <?php
   }
   ?>
   </select>
  </div>
  <br class="clear">
</div>

<?php
// Global options..
include(PATH.'templates/catalogue/payment-methods/global.php');
?>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process" value="yes">
 <input type="hidden" name="area" value="<?php echo $_GET['conf']; ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings42); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings42); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=payment-methods'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>

