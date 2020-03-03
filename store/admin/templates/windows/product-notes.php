<?php if (!defined('PARENT') || !isset($_GET['notes'])) { die('Permission Denied'); }
$ID = (int) $_GET['notes'];
$P  = mc_getTableData('products', 'id', $ID);
if (!isset($P->id)) {
  exit;
}
?>

<div id="windowcontent">

  <div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
    <i class="fa fa-check fa-fw"></i> <?php echo $msg_productmanage64; ?>
  </div>

  <div class="fieldHeadWrapper">
    <p><?php echo mc_safeHTML($P->pName); ?></p>
  </div>

  <textarea name="nts" rows="5" cols="20" style="height:300px"><?php echo mc_safeHTML($P->pNotes); ?></textarea>

  <p style="text-align:center;padding-top:20px">
   <input class="btn btn-primary" onclick="mc_updateProductNotes('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_productmanage63); ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage63); ?>">
  </p>

</div>