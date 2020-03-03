<?php
if (!isset($PM->defmeth)) {
  exit;
}
$viewtypes = explode(',', $PM->viewtype);
?>
<div class="formFieldWrapper">
 <div class="formLeft">
   <label><?php echo $msg_payment_methods[0]; ?>:</label>
   <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="defmeth" value="yes"<?php echo ($PM->defmeth=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="defmeth" value="no"<?php echo ($PM->defmeth=='no' ? ' checked="checked"' : ''); ?>>
 </div>
 <br class="clear">
</div>

<div class="formFieldWrapper">
 <div class="formLeft">
   <label><?php echo $msg_payment_methods[13]; ?>:</label>
   <div class="checkbox">
     <label><input type="checkbox" name="viewtype[]" value="g"<?php echo (in_array('a', $viewtypes) || in_array('g', $viewtypes) ? ' checked="checked"' : ''); ?>><?php echo $msg_payment_methods[14]; ?></label>
   </div>
   <div class="checkbox">
     <label><input type="checkbox" name="viewtype[]" value="p"<?php echo (in_array('a', $viewtypes) || in_array('p', $viewtypes) ? ' checked="checked"' : ''); ?>><?php echo $msg_payment_methods[15]; ?></label>
   </div>
   <div class="checkbox">
     <label><input type="checkbox" name="viewtype[]" value="t"<?php echo (in_array('a', $viewtypes) || in_array('t', $viewtypes) ? ' checked="checked"' : ''); ?>><?php echo $msg_payment_methods[16]; ?></label>
   </div>
 </div>
 <br class="clear">
</div>

<div class="formFieldWrapper">
 <div class="formLeft">
   <label><?php echo $msg_settings76.' '.$PM->display; ?>:</label>
   <?php echo $msg_script5; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="status" value="yes"<?php echo ($PM->status=='yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="status" value="no"<?php echo ($PM->status=='no' ? ' checked="checked"' : ''); ?>>
 </div>
 <br class="clear">
</div>