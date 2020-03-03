<?php
if (!defined('PARENT') || !in_array($_GET['conf'],array_keys($mcSystemPaymentMethods))) {
  die('Invalid parameter: "conf='.mc_safeHTML($_GET['conf']).'" is not supported');
}
$skipStatusDisplay = array('refunded','pending','cancelled');
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
<?php
if (function_exists('curl_init')) {
?>
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_paymethods2; ?>: <?php echo mc_displayHelpTip($msg_javascript536,'RIGHT'); ?></label>
    <input type="text" name="params[merchant-id]" value="<?php echo (isset($params['merchant-id']) ? mc_safeHTML($params['merchant-id']) : ''); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <div class="formRight">
    <label><?php echo $msg_settings235; ?>: <?php echo mc_displayHelpTip($msg_javascript457,'LEFT'); ?></label>
    <input type="text" name="redirect" class="box" value="<?php echo mc_safeHTML($PM->redirect); ?>" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
 <label><?php echo $msg_settings68; ?>: <?php echo mc_displayHelpTip($msg_javascript67,'RIGHT'); ?></label>
 <textarea rows="5" cols="30" name="info" id="info" class="textarea" tabindex="<?php echo ++$tabIndex; ?>"><?php echo mc_safeHTML($PM->info); ?></textarea>
</div>

<?php
// Payment statuses..
include(PATH.'templates/catalogue/payment-methods/statuses.php');
?>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_settings79; ?>: <?php echo mc_displayHelpTip($msg_javascript503,'RIGHT'); ?></label>
    <input type="text" name="liveserver" value="<?php echo mc_safeHTML($PM->liveserver); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
  </div>
  <div class="formRight">
    <label><?php echo $msg_settings80; ?>: <?php echo mc_displayHelpTip($msg_javascript504); ?></label>
    <input type="text" name="sandboxserver" value="<?php echo mc_safeHTML($PM->sandboxserver); ?>" class="box" tabindex="<?php echo ++$tabIndex; ?>">
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
<?php
} else {
?>
<div><span class="gatewayLoadErr"><?php echo $gateway_errors['curl']; ?><br><br>&#8226; <a href="http://php.net/manual/en/book.curl.php" onclick="window.open(this);return false">CURL</a><br><br><?php echo $gateway_errors['refresh']; ?></span></div>
<?php
}
?>