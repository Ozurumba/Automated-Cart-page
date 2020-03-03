<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('paystatuses','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    die('Error, invalid ID number');
  }
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_paymentstatuses8);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_paymentstatuses9);
}
if (isset($OK3) && $cnt>0) {
  echo mc_actionCompleted($msg_paymentstatuses10);
}
?>

<form method="post" action="?p=order-statuses<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->statname) ? $msg_paymentstatuses6 : $msg_paymentstatuses5); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_paymentstatuses2; ?>: <?php echo mc_displayHelpTip($msg_javascript193,'RIGHT'); ?></label>
    <input tabindex="1" type="text" name="statname" value="<?php echo (isset($EDIT->statname) ? mc_cleanData($EDIT->statname) : ''); ?>" class="box" maxlength="200">

    <label style="margin-top:10px"><?php echo $msg_paymentstatuses14; ?>: <?php echo mc_displayHelpTip($msg_javascript464,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="homepage" value="yes"<?php echo (isset($EDIT->homepage) && $EDIT->homepage=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="homepage" value="no"<?php echo (isset($EDIT->homepage) && $EDIT->homepage=='no' ? ' checked="checked"' : (!isset($EDIT->homepage) ? ' checked="checked"' : '')); ?>>

    <label style="margin-top:10px"><?php echo $msg_paymentstatuses3; ?>: <?php echo mc_displayHelpTip($msg_javascript194,'LEFT'); ?></label>
    <select tabindex="2" name="pMethod">
    <option value="all"><?php echo $msg_paymentstatuses4; ?></option>
    <?php
    if (!empty($mcSystemPaymentMethods)) {
    foreach ($mcSystemPaymentMethods AS $key => $value) {
	  if ($value['enable']=='yes') {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($EDIT->pMethod) && $EDIT->pMethod==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
    <?php
	  }
    }
    }
    ?>
    </select>
  </div>
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->statname) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->statname) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->statname) ? $msg_paymentstatuses6 : $msg_paymentstatuses5)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->statname) ? $msg_paymentstatuses6 : $msg_paymentstatuses5)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=order-statuses\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form><br>

<div class="fieldHeadWrapper" style="margin-top:20px">
  <p>
  <span class="pull-right">
   <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
  </span>
  <?php echo $msg_paymentstatuses7; ?>:</p>
</div>

<div class="formFieldWrapper" style="display:none" id="filters">
  <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
  <option value="?p=order-statuses"><?php echo $msg_paymentstatuses13; ?></option>
  <?php
  if (!empty($mcSystemPaymentMethods)) {
  foreach ($mcSystemPaymentMethods AS $key => $value) {
  ?>
  <option value="?p=order-statuses&amp;method=<?php echo $key; ?>"<?php echo (isset($_GET['method']) && $_GET['method']==$key ? ' selected="selected"' : ''); ?>><?php echo $value['lang']; ?></option>
  <?php
  }
  }
  ?>
  </select>
</div>

<?php
$q_statuses = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
              ".(isset($_GET['method']) && array_key_exists($_GET['method'],$mcSystemPaymentMethods) ? 'WHERE `pMethod` = \''.$_GET['method'].'\'' : '')."
              ORDER BY `pMethod`,`statname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_statuses)>0) {
  while ($STATUS = mysqli_fetch_object($q_statuses)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <b><?php echo mc_safeHTML($STATUS->statname); ?></b><br><br>
      <?php echo ($STATUS->pMethod=='all' ? $msg_paymentstatuses4 : mc_paymentMethodName($STATUS->pMethod)); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=order-statuses&amp;edit=<?php echo $STATUS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' && mc_rowCount('statuses WHERE `orderStatus` = \''.$STATUS->id.'\'')==0 ? ' <a href="?p=order-statuses&amp;del='.$STATUS->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_paymentstatuses11; ?></span>
<?php
}
?>


</div>
