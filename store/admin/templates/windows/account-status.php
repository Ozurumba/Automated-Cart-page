<?php if (!defined('PARENT') || !isset($_GET['accstatus'])) { die('Permission Denied'); }
$ID = (int) $_GET['accstatus'];
$AC = mc_getTableData('accounts', 'id', $ID);
if (!isset($AC->id)) {
  exit;
}
?>

<div id="windowcontent">

  <div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
    <i class="fa fa-check fa-fw"></i> <?php echo $msg_accounts16; ?>
  </div>

  <div class="fieldHeadWrapper">
    <p><?php echo mc_safeHTML($AC->name); ?></p>
  </div>

  <textarea name="reason" rows="5" cols="20" style="height:230px" placeholder="<?php echo mc_safeHTML($msg_accounts18); ?>"><?php echo mc_safeHTML($AC->reason); ?></textarea><br>

  &nbsp;&nbsp;<input type="checkbox" name="status" onclick="mc_setCheckStatus(this.checked,'up_accchk_status_<?php echo $ID; ?>')" value="yes"<?php echo ($AC->enabled == 'yes' ? ' checked="checked"' : ''); ?>> <?php echo $msg_accounts13; ?>

  <p style="text-align:center;padding-top:20px">
   <input type="hidden" name="up_accchk_status_<?php echo $ID; ?>" value="<?php echo ($AC->enabled == 'yes' ? 'yes' : 'no'); ?>">
   <input class="btn btn-primary" onclick="mc_updateAccountStatus('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_accounts17); ?>" title="<?php echo mc_cleanDataEntVars($msg_accounts17); ?>">
  </p>

</div>