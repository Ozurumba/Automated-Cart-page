<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
$payStatuses = mc_loadDefaultStatuses();
?>
<div id="content">

<?php
if (isset($return) && $return=='none') {
?>
<div class="alert alert-warning alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-warning fa-fw"></i> <?php echo $msg_salesexport15; ?>
</div>
<?php
}
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_salesexport2; ?>:</p>
</div>

<form method="post" id="form" action="?p=sales-export">
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_salesexport10; ?>: <?php echo mc_displayHelpTip($msg_javascript135,'RIGHT'); ?></label>
    <select name="range" tabindex="<?php echo (++$tabIndex); ?>">
    <option value="0">- - - - - -</option>
    <?php
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_POST['range']) && $_POST['range']==$key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_add_stats)>0) {
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    }
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <option value="<?php echo $ST->id; ?>"<?php echo (isset($_POST['range']) && $_POST['range']==$ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_salessearch4; ?>: <?php echo mc_displayHelpTip($msg_javascript136); ?></label>
    <input type="text" tabindex="<?php echo (++$tabIndex); ?>" name="from" value="<?php echo (isset($_POST['from']) ? mc_safeHTML($_POST['from']) : ''); ?>" class="box" id="from">
    <input style="margin-top:5px" type="text" tabindex="<?php echo (++$tabIndex); ?>" name="to" value="<?php echo (isset($_POST['to']) ? mc_safeHTML($_POST['to']) : ''); ?>" class="box" id="to">

    <label style="margin-top:10px"><?php echo $msg_salesexport6; ?>: <?php echo mc_displayHelpTip($msg_javascript148,'RIGHT'); ?></label>
    <select name="method" tabindex="<?php echo (++$tabIndex); ?>">
    <option value="0">- - - - - -</option>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && !in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_POST['method']) && $_POST['method']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes' && in_array($key, $noneGateway)) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($_POST['method']) && $_POST['method']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    ?>
    </select>

  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_sales18; ?>: <?php echo mc_displayHelpTip($msg_javascript149); ?></label>
    <select name="country" tabindex="<?php echo (++$tabIndex); ?>">
    <option value="0">- - - - - -</option>
    <?php
    $q_c = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "countries`
           WHERE `enCountry` = 'yes'
           ORDER BY `cName`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($C = mysqli_fetch_object($q_c)) {
    ?>
    <option value="<?php echo $C->id; ?>"<?php echo (isset($_POST['country']) && $_POST['country']==$C->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($C->cName); ?></option>
    <?php
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_sales_export_buyers[0]; ?></label>
    <select name="type">
      <option value="0">- - - - - -</option>
      <option value="all"<?php echo (isset($_POST['type']) && $_POST['type'] == 'all' ? ' selected="selected"' : ''); ?>>- - - - - -</option>
      <option value="guest"<?php echo (isset($_POST['type']) && $_POST['type'] == 'guest' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales_export_buyers[2]; ?></option>
      <option value="personal"<?php echo (isset($_POST['type']) && $_POST['type'] == 'standard' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales_export_buyers[3]; ?></option>
      <option value="trade"<?php echo (isset($_POST['type']) && $_POST['type'] == 'trade' ? ' selected="selected"' : ''); ?>><?php echo $msg_sales_export_buyers[4]; ?></option>
    </select>
  </div>
</div>

<p style="text-align:center;padding:20px 0 30px 0">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_salesexport2); ?>" title="<?php echo mc_cleanDataEntVars($msg_salesexport2); ?>">
 <?php echo (isset($return) && $return=='none' ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=sales-export\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

</div>
