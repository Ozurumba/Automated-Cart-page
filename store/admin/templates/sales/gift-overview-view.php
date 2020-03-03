<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>

<form method="post" action="?p=gift&amp;viewGift=<?php echo $_GET['viewGift']; ?>">
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_giftcerts28);
}
?>

<div class="fieldHeadWrapper">
  <p><span class="float"><b><?php echo ($GIFT->code ? $GIFT->code : $msg_giftcerts31); ?></b></span><?php echo mc_cleanDataEntVars($msg_giftcerts20); ?>:</p>
</div>
<?php
if (isset($GIFT->id)) {
?>
<div class="formFieldWrapper">

  <div class="formLeft">

    <label><?php echo $msg_giftcerts21; ?>:</label>
    <input type="text" class="box" name="from_name" value="<?php echo mc_safeHTML($GIFT->from_name); ?>">

    <label style="margin-top:10px"><?php echo $msg_giftcerts22; ?>:</label>
    <input type="text" class="box" name="to_name" value="<?php echo mc_safeHTML($GIFT->to_name); ?>">

    <label style="margin-top:10px"><?php echo $msg_giftcerts32; ?>:</label>
    <textarea name="message" rows="5" cols="20"><?php echo mc_safeHTML($GIFT->message); ?></textarea>

    <label style="margin-top:10px"><?php echo $msg_giftcerts26; ?> / <?php echo $msg_giftcerts27; ?>:</label>
    <input type="text" class="box" name="value" value="<?php echo mc_safeHTML($GIFT->value); ?>">
    <input  style="margin-top:5px" type="text" class="box" name="redeemed" value="<?php echo mc_safeHTML($GIFT->redeemed); ?>">

    <label style="margin-top:10px"><?php echo $msg_giftcerts23; ?>:</label>
    <input type="text" class="box" name="from_email" value="<?php echo mc_safeHTML($GIFT->from_email); ?>">

    <label style="margin-top:10px"><?php echo $msg_giftcerts24; ?>:</label>
    <input type="text" class="box" name="to_email" value="<?php echo mc_safeHTML($GIFT->to_email); ?>">

    <label style="margin-top:10px"><?php echo $msg_giftcerts25; ?>:</label>
    <textarea name="notes" rows="5" cols="20"><?php echo mc_safeHTML($GIFT->notes); ?></textarea>

    <label style="margin-top:10px"><?php echo $msg_giftcerts5; ?>:</label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="enabled" value="yes"<?php echo ($GIFT->enabled=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="enabled" value="no"<?php echo ($GIFT->enabled=='no' ? ' checked="checked"' : ''); ?>>

  </div>

  <br class="clear">

  <p style="margin:20px 0 10px 0">
    <input type="hidden" name="process" value="yes">
    <input type="hidden" name="giftID" value="<?php echo $GIFT->id; ?>">
    <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_giftcerts7); ?>" title="<?php echo mc_cleanDataEntVars($msg_giftcerts7); ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=gift-overview'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
  </p>

</div>
<?php
} else {
?>
<span class="noData"><?php echo $msg_giftcerts29; ?></span>
<?php
}
?>
</div>
</form>
