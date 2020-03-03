<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  unset($FIELD_MAPPING,$ACC_OPTIONS);
  echo mc_actionCompleted(str_replace('{count}',$added,$msg_accimport16));
}

if (isset($FIELD_MAPPING)) {
?>
<form method="post" action="?p=acc-import" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript302); ?>')">

<div class="fieldHeadWrapper">
  <p><span class="float"><a onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title);return false;" href="?p=acc-import&amp;field=account" title="<?php echo mc_cleanDataEntVars($msg_import27); ?>"><i class="fa fa-info-circle fa-fw"></i></a></span><?php echo str_replace('{count}',count($fields),$msg_import9); ?>:</p>
</div>

<?php
for ($i=0; $i<count($fields); $i++) {
?>
<div class="panel panel-default" id="imp_<?php echo $i; ?>">
  <div class="panel-heading">
    <?php echo (PROD_IMPORT_HEAD_TXT_LIMIT > 0 && strlen($fields[$i]) > PROD_IMPORT_HEAD_TXT_LIMIT ? substr(mc_safeHTML($fields[$i]), 0, PROD_IMPORT_HEAD_TXT_LIMIT) . '...' : ($fields[$i] ? mc_safeHTML($fields[$i]) : '- - -')); ?>
  </div>
  <div class="panel-body" style="overflow-y:auto">
    <select name="dbFields[]">
     <option value="0"><?php echo $msg_import55; ?></option>
     <option value="0"><?php echo $msg_import53; ?></option>
     <?php
     if (!empty($fieldMapping_accounts)) {
       foreach ($fieldMapping_accounts AS $k => $v) {
       ?>
       <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
       <?php
       }
     }
     ?>
    </select>
  </div>
</div>
<?php
}
?>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process-accounts-mapping" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_import7); ?>" title="<?php echo mc_cleanDataEntVars($msg_import7); ?>">
</p>
<?php
} elseif (isset($ACC_OPTIONS)) {
?>
<script>
//<![CDATA[
function mc_setAccType(valu) {
  switch(valu) {
    case 'personal':
      jQuery('#tradeopts').hide();
      break;
    case 'trade':
      jQuery('#tradeopts').slideDown();
      break;
  }
}
//]]>
</script>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_accimport12; ?>:</p>
</div>

<form method="post" action="?p=acc-import" id="form" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript180); ?>')">
<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts26; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="newsletter" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="newsletter" value="no" checked="checked">

    <label style="margin-top:10px"><?php echo $msg_addccts8; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enablelog" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enablelog" value="no" checked="checked">

  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_addccts31; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <?php echo $msg_addccts32; ?> <input onclick="mc_setAccType(this.value)" type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="type" value="personal" checked="checked"> <?php echo $msg_addccts33; ?> <input onclick="mc_setAccType(this.value)" tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="type" value="trade">
   </div>
  <br class="clear">
</div>

<div id="tradeopts" class="formFieldWrapper" style="display:none">
  <div class="formLeft">
    <label><?php echo $msg_addccts34; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <div class="form-group input-group">
      <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="tradediscount" value="" class="box addon-no-radius-right" maxlength="5">
      <span class="input-group-addon"><i class="fa fa-percent fa-fw"></i></span>
    </div>
    <label style="margin-top:10px"><?php echo $msg_addccts36; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="minqty" value="" class="box" maxlength="10">

    <label style="margin-top:10px"><?php echo $msg_addccts37; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="maxqty" value="" class="box" maxlength="10">

    <label style="margin-top:10px"><?php echo $msg_addccts47; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="mincheckout" value="" class="box" maxlength="20">

    <label style="margin-top:10px"><?php echo $msg_addccts38; ?>: <?php echo mc_displayHelpTip($msg_javascript269,'RIGHT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="stocklevel" value="" class="box" maxlength="10">
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_accimport13; ?>: <?php echo mc_displayHelpTip($msg_javascript270); ?></label>
    <select name="status">
      <option value="active" selected="selected"><?php echo $msg_accimport14; ?></option>
      <option value="unverified"><?php echo $msg_accimport15; ?></option>
    </select>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-accounts" value="yes">
 <input type="hidden" name="file" value="<?php echo (isset($_SESSION['curImportFile']) ? $_SESSION['curImportFile'] : ''); ?>">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_accimport11); ?>" title="<?php echo mc_cleanDataEntVars($msg_accimport11); ?>">
</p>
<?php
} else {
?>
<form method="post" action="?p=acc-import" enctype="multipart/form-data" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript180); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_admin3_0[59]; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_accimport; ?>: <?php echo mc_displayHelpTip($msg_javascript179,'RIGHT'); ?></label>
    <input type="file" name="file">

    <label style="margin-top:10px"><?php echo $msg_accimport2; ?>: <?php echo mc_displayHelpTip($msg_javascript176); ?></label>
    <input type="text" name="lines" value="5000" class="box">

    <label style="margin-top:10px"><?php echo $msg_accimport3; ?>: <?php echo mc_displayHelpTip($msg_javascript177,'LEFT'); ?></label>
    <input type="text" name="del" value="&#044;" class="box">
    <input  style="margin-top:5px" type="text" name="enc" value="&quot;" class="box">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process-upload-accounts" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_accimport4); ?>" title="<?php echo mc_cleanDataEntVars($msg_accimport4); ?>">
</p>
<?php
}
?>
</form>

</div>
