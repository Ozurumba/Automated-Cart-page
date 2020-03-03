<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$updated,$msg_productprices27));
}

?>
<form method="post" action="?p=update-prices-csv" enctype="multipart/form-data" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript436); ?>')">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_admin3_0[5]; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productprices24; ?>: <?php echo mc_displayHelpTip($msg_javascript435,'RIGHT'); ?></label>
    <input type="file" name="file">

    <label style="margin-top:10px"><?php echo $msg_productprices22; ?>: <?php echo mc_displayHelpTip($msg_javascript176); ?></label>
    <input type="text" name="lines" value="5000" class="box">

    <label style="margin-top:10px"><?php echo $msg_productprices23; ?>: <?php echo mc_displayHelpTip($msg_javascript177,'LEFT'); ?></label>
    <input type="text" name="del" value="&#044;" class="box">
    <input style="margin-top:5px" type="text" name="enc" value="&quot;" class="box">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="import_from_csv" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productprices26); ?>" title="<?php echo mc_cleanDataEntVars($msg_productprices26); ?>">
</p>

</form>

</div>
