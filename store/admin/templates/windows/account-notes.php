<?php if (!defined('PARENT') || !isset($_GET['notes'])) { die('Permission Denied'); }
$ID = (int) $_GET['notes'];
$AC = mc_getTableData('accounts', 'id', $ID);
if (!isset($AC->id)) {
  exit;
}
?>

<div id="windowcontent">

  <div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
    <i class="fa fa-check fa-fw"></i> <?php echo $msg_accounts9; ?>
  </div>

  <div class="fieldHeadWrapper">
    <p><?php echo mc_safeHTML($AC->name); ?></p>
  </div>

  <textarea name="nts" rows="5" cols="20" style="height:300px"><?php echo mc_safeHTML($AC->notes); ?></textarea>

  <p style="text-align:center;padding-top:20px">
   <input class="btn btn-primary" onclick="mc_updateAccountNotes('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_accounts10); ?>" title="<?php echo mc_cleanDataEntVars($msg_accounts10); ?>">
  </p>

</div>