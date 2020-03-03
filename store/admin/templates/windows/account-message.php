<?php if (!defined('PARENT') || !isset($_GET['message'])) { die('Permission Denied'); }
$ID = (int) $_GET['message'];
$AC = mc_getTableData('accounts', 'id', $ID);
if (!isset($AC->id)) {
  exit;
}
?>

<div id="windowcontent">

  <div class="alert alert-warning" style="display:none" onclick="jQuery(this).slideUp()">
    <i class="fa fa-check fa-fw"></i> <?php echo $msg_accounts32; ?>
  </div>

  <div class="fieldHeadWrapper">
    <p><?php echo mc_safeHTML($AC->name); ?></p>
  </div>

  <textarea name="msg" rows="5" cols="20" style="height:250px"><?php echo mc_safeHTML($AC->message); ?></textarea>

  <div class="form-group" style="margin-top:10px">
    <div class="form-group input-group">
     <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
     <input type="text" class="form-control" name="exp" placeholder="<?php echo mc_safeHTML($msg_accounts31); ?>" value="<?php echo ($AC->messageexp != '0000-00-00' ? $AC->messageexp : ''); ?>">
    </div>
  </div>

  <p style="text-align:center;padding-top:20px">
   <input class="btn btn-primary" onclick="mc_updateAccountMessage('<?php echo $ID; ?>')" type="button" value="<?php echo mc_cleanDataEntVars($msg_accounts30); ?>" title="<?php echo mc_cleanDataEntVars($msg_accounts30); ?>">
  </p>

</div>