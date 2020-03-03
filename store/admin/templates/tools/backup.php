<?php if (!defined('PARENT')) { die('Permission Denied'); }
$totalBackup = 0;
$mcSPScheme  = mswDBSchemaArray();
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_backup15);
}

?>
<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanData($msg_backup2); ?>:</p>
</div>

<div class="table-responsive">
  <table class="table table-striped table-hover">
  <thead>
    <tr>
      <th><?php echo $msg_backup3; ?></th>
      <th><?php echo $msg_backup4; ?></th>
      <th><?php echo $msg_backup5; ?></th>
      <th><?php echo $msg_backup7; ?></th>
      <th><?php echo $msg_backup8; ?></th>
    </tr>
  </thead>
  <tbody>
  <?php
  $query = mysqli_query($GLOBALS["___msw_sqli"], "SHOW TABLE STATUS FROM `".DB_NAME."`");
  while ($SCHEMA = mysqli_fetch_assoc($query)) {
    if (in_array($SCHEMA['Name'],$mcSPScheme)) {
      $size   = ($SCHEMA['Rows']>0 ? $SCHEMA['Data_length'] + $SCHEMA['Index_length'] : '0');
      $utTS   = strtotime($SCHEMA['Update_time']);
      ?>
      <tr>
       <td><?php echo $SCHEMA['Name']; ?></td>
       <td><?php echo $SCHEMA['Rows']; ?></td>
       <td><?php echo ($SCHEMA['Rows']>0 ? mc_fileSizeConversion($size) : '0'); ?></td>
       <td><?php echo date(mc_backupDateFormat($SETTINGS).' H:iA',$utTS); ?></td>
       <td><?php echo $SCHEMA['Engine']; ?></td>
      </tr>
      <?php
      $totalBackup = ($totalBackup+$size);
    }
  }
  $tabIndex = 0;
  ?>
  </tbody>
  </table>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanData($msg_backup9); ?>:</p>
</div>

<form method="post" action="?p=backup" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript397); ?>')">
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_backup11; ?>: <?php echo mc_displayHelpTip($msg_javascript394,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo ++$tabIndex; ?>" name="download" value="yes" checked="checked"> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="download" value="no">

    <label style="margin-top:10px"><?php echo $msg_backup13; ?>: <?php echo mc_displayHelpTip($msg_javascript395); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo ++$tabIndex; ?>" name="compress" value="yes" checked="checked"> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="compress" value="no">

    <label style="margin-top:10px"><?php echo $msg_backup12; ?>: <?php echo mc_displayHelpTip($msg_javascript396,'LEFT'); ?></label>
    <input type="text" tabindex="<?php echo ++$tabIndex; ?>" name="emails" class="box" value="">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding:10px 0 20px 0">
 <input type="hidden" name="process" value="yes">
 <button class="btn btn-primary" type="submit"><?php echo mc_cleanData($msg_backup14); ?> (<?php echo $msg_backup10; ?>: <?php echo mc_fileSizeConversion($totalBackup); ?>)</button>
</p>
</form>

</div>
