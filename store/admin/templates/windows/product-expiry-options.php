<?php if (!defined('PARENT') || !isset($_GET['prod_expiry'])) { die('Permission Denied'); }
$ID = (int) $_GET['prod_expiry'];
$P  = mc_getTableData('products', 'id', $ID);
if (!isset($P->id)) {
  exit;
}
?>

<div id="windowcontent">

  <div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
    <i class="fa fa-check fa-fw"></i> <?php echo $msg_product_expiry_options[5]; ?>
  </div>

  <div class="form-group">
    <label><?php echo $msg_product_expiry_options[1]; ?></label>
    <input type="text" class="form-control" name="exp[price]" value="<?php echo mc_safeHTML($P->exp_price); ?>">
  </div>

  <div class="form-group">
    <label><?php echo $msg_product_expiry_options[2]; ?></label>
    <div class="checkbox">
      <label><input type="checkbox" name="exp[special]" onclick="mc_setCheckStatus(this.checked,'special_<?php echo $ID; ?>')" value="yes"<?php echo ($P->exp_special == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script5; ?></label>
    </div>
  </div>

  <div class="form-group">
    <label><?php echo $msg_product_expiry_options[3]; ?></label>
    <div class="checkbox">
      <label><input type="checkbox" name="exp[send]" onclick="mc_setCheckStatus(this.checked,'send_<?php echo $ID; ?>')" value="yes"<?php echo ($P->exp_send == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_script5; ?></label>
    </div>
  </div>

  <div class="form-group">
    <label><?php echo $msg_product_expiry_options[4]; ?></label>
    <textarea name="exp[text]" rows="5" cols="20" style="height:100px"><?php echo mc_safeHTML($P->exp_text); ?></textarea>
  </div>

  <p style="text-align:center;padding-top:20px">
   <input type="hidden" name="special_<?php echo $ID; ?>" value="<?php echo $P->exp_special; ?>">
   <input type="hidden" name="send_<?php echo $ID; ?>" value="<?php echo $P->exp_send; ?>">
   <input class="btn btn-primary" onclick="mc_updateProdExp('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_product_expiry_options[0]); ?>" title="<?php echo mc_cleanDataEntVars($msg_product_expiry_options[0]); ?>">
  </p>

</div>