<?php if (!defined('PARENT') || !isset($_GET['buynow'])) { die('Permission Denied'); }
$ID = (int) $_GET['buynow'];
?>
<div>

  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $msg_admin_manage_product3_0[1]; ?>
    </div>
    <div class="panel-body">
      <?php
      // HTML code example..
      echo str_replace('{url}',$SETTINGS->ifolder . '/?mcbn=' . $ID,mc_safeHTML($msg_admin_manage_product3_0[3]));
      ?>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading uppercase">
      <i class="fa fa-file-text-o fa-fw"></i> <?php echo $msg_admin_manage_product3_0[2]; ?>
    </div>
    <div class="panel-body">
      <?php
      // BB code example..
      echo str_replace('{url}',$SETTINGS->ifolder . '/?mcbn=' . $ID,mc_safeHTML($msg_admin_manage_product3_0[4]));
      ?>
    </div>
  </div>

</div>