<?php if (!defined('PARENT')) { die('Permission Denied'); }
$S = mc_getTableData('sales','id',(int) $_GET['gatewayParams']);
if (!isset($S->gateparams)) {
  exit;
}
$params = ($S->gateparams ? explode('<-->',$S->gateparams) : array());
?>
<div id="windowcontent">

<div class="fieldHeadWrapper">
  <p><?php echo mc_cleanData($msg_viewsale107); ?></p>
</div>

<?php
if (!empty($params)) {
?>
<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th><?php echo $msg_admin3_0[33]; ?></th>
        <th><?php echo $msg_admin3_0[34]; ?></th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($params AS $gp) {
    $chop = explode('=>',$gp);
    if (isset($chop[0],$chop[1])) {
    ?>
    <tr>
      <td><?php echo mc_safeHTML($chop[0]); ?></td>
      <td><?php echo mc_safeHTML($chop[1]); ?></td>
    </tr>
    <?php
    }
    }
    ?>
    </tbody>
  </table>
</div>
<div class="alert alert-success">
  <p><?php echo str_replace('{gateway}',$mcSystemPaymentMethods[$S->paymentMethod]['lang'],mc_cleanData($msg_viewsale108)); ?></p>
</div>
<?php
} else {
?>
<p class="noParams"><?php echo $msg_viewsale109; ?></p>
<?php
}
?>

</div>