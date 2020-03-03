<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',$count,$msg_batchupdate7));
}
?>

<form method="post" action="?p=product-batch-update" enctype="multipart/form-data" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript478); ?>')">

<div class="fieldHeadWrapper">
  <p><?php echo $msg_batchupdate2; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_batchupdate6; ?>: <?php echo mc_displayHelpTip($msg_javascript179,'RIGHT'); ?></label>
    <input type="file" name="file" id="file">

    <label style="margin-top:10px"><?php echo $msg_batchupdate4; ?>: <?php echo mc_displayHelpTip($msg_javascript176); ?></label>
    <input type="text" name="lines" value="5000" class="box">

    <label style="margin-top:10px"><?php echo $msg_batchupdate5; ?>: <?php echo mc_displayHelpTip($msg_javascript177,'LEFT'); ?></label>
    <input type="text" name="del" value="&#044;" class="box">
    <input  style="margin-top:5px" type="text" name="enc" value="&quot;" class="box">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_batchupdate3); ?>" title="<?php echo mc_cleanDataEntVars($msg_batchupdate3); ?>">
</p>

</form>

</div>
