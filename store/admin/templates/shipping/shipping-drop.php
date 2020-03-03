<?php if (!defined('PARENT')) { die('Permission Denied'); }
if (isset($_GET['edit'])) {
  $EDIT = mc_getTableData('dropshippers','id',mc_digitSan($_GET['edit']));
  if (!isset($EDIT->id)) {
    exit;
  }
  $est   = ($EDIT->status ? explode(',',$EDIT->status) : array());
  $emd   = ($EDIT->method ? explode(',',$EDIT->method) : array());
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_dropship6);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_dropship5);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_dropship7);
}
$payStatuses = mc_loadDefaultStatuses();
?>

<form method="post" action="?p=drop<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_dropship2 : $msg_dropship); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_dropship3; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="name" value="<?php echo (isset($EDIT->name) ? mc_cleanData($EDIT->name) : ''); ?>" class="box" maxlength="100">

    <label style="margin-top:10px"><?php echo $msg_dropship4; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="email" value="<?php echo (isset($EDIT->emails) ? mc_cleanData($EDIT->emails) : ''); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_dropship11; ?>: <?php echo mc_displayHelpTip($msg_javascript264,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enable" value="yes"<?php echo (isset($EDIT->enable) && $EDIT->enable=='yes' ? ' checked="checked"' : (!isset($EDIT->enable) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="7" type="radio" name="enable" value="no"<?php echo (isset($EDIT->enable) && $EDIT->enable=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_dropship8; ?>: <?php echo mc_displayHelpTip($msg_javascript271,'LEFT'); ?></label>
    <?php
    // Get last status..
    foreach ($payStatuses AS $key => $value) {
    ?>
    <input type="checkbox" name="status[]" value="<?php echo $key; ?>"<?php echo (isset($EDIT->id) && in_array($key, $est) ? ' checked="checked"' : ''); ?>> <?php echo $value; ?><br>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   WHERE `pMethod` IN('all')
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <input type="checkbox" name="status[]" value="<?php echo $ST->id; ?>"<?php echo (isset($EDIT->id) && in_array($ST->id, $est) ? ' checked="checked"' : ''); ?>> <?php echo mc_cleanData($ST->statname); ?><br>
    <?php
    }
    ?>

    <label style="margin-top:10px"><?php echo $msg_dropship9; ?>: <?php echo mc_displayHelpTip($msg_javascript271,'LEFT'); ?></label>
    <?php
    foreach ($mcSystemPaymentMethods AS $key => $value) {
      if ($value['enable']=='yes') {
      ?>
      <input type="checkbox" name="method[]" value="<?php echo $key; ?>"<?php echo (isset($EDIT->id) && !empty($emd) && in_array($key, $emd) ? ' checked="checked"' : ''); ?>> <?php echo $value['lang']; ?><br>
      <?php
      }
    }
    ?>

    <label style="margin-top:10px"><?php echo $msg_dropship10; ?>: <?php echo mc_displayHelpTip($msg_javascript271,'LEFT'); ?></label>
    <select name="salestatus">
    <?php
    // Get last status..
    foreach ($payStatuses AS $key => $value) {
    ?>
    <option value="<?php echo $key; ?>"<?php echo (isset($EDIT->salestatus) && $EDIT->salestatus == $key ? ' selected="selected"' : ''); ?>><?php echo $value; ?></option>
    <?php
    }
    // Get additional payment statuses..
    $q_add_stats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
                   WHERE `pMethod` IN('all')
                   ORDER BY `pMethod`,`statname`
                   ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_add_stats)>0) {
    ?>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
    <?php
    }
    while ($ST = mysqli_fetch_object($q_add_stats)) {
    ?>
    <option value="<?php echo $ST->id; ?>"<?php echo (isset($EDIT->salestatus) && $EDIT->salestatus == $ST->id ? ' selected="selected"' : ''); ?>><?php echo mc_cleanData($ST->statname); ?></option>
    <?php
    }
    ?>
    </select>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="<?php echo (isset($EDIT->id) ? 'update' : 'process'); ?>" value="<?php echo (isset($EDIT->id) ? $EDIT->id : 'yes'); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_dropship2 : $msg_dropship)); ?>" title="<?php echo mc_safeHTML((isset($EDIT->id) ? $msg_dropship2 : $msg_dropship)); ?>"><?php echo (isset($EDIT->id) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location=\'?p=drop\'" value="'.mc_cleanDataEntVars($msg_script11).'" title="'.mc_cleanDataEntVars($msg_script11).'">' : ''); ?>
</p>
</form>

<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo $msg_dropship13; ?>:</p>
</div>

<div id="sortable">
<?php
$q_ds = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "dropshippers`
        ORDER BY `name`
        ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_ds)>0) {
  while ($DS = mysqli_fetch_object($q_ds)) {
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      [ID: <?php echo $DS->id; ?>] <b><?php echo mc_safeHTML($DS->name) . '</b><br>' . mc_safeHTML($DS->emails); ?><br><br>
      <?php echo ($DS->enable == 'yes' ? $msg_dropship14 : $msg_dropship15); ?>
    </div>
    <div class="panel-footer">
      <a href="?p=drop&amp;edit=<?php echo $DS->id; ?>"><i class="fa fa-pencil fa-fw"></i></a><?php echo ($uDel=='yes' ? ' <a href="?p=drop&amp;del='.$DS->id.'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times fa-fw mc-red"></i></a>' : ''); ?>
    </div>
  </div>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_dropship12; ?></span>
<?php
}
?>
</div>

</div>
