<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings111);
}

?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_paymethods36.' ('.(count($mcSystemPaymentMethods) - count($noneGateway)).')'; ?>:</p>
</div>

<?php
if (!empty($mcSystemPaymentMethods)) {
foreach ($mcSystemPaymentMethods AS $k => $v) {
if (!in_array($k,$noneGateway)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="methodimg hidden-xs hidden-sm">
      <?php
      if ($v['web']) {
      ?>
      <a href="<?php echo $v['web']; ?>" onclick="window.open(this);return false"><img src="templates/images/gateways/<?php echo $v['img']; ?>" alt="<?php echo mc_safeHTML($v['lang']); ?>" title="<?php echo mc_safeHTML($v['lang']); ?>"></a>
      <?php
      } else {
      ?>
      <img src="templates/images/gateways/<?php echo $v['img']; ?>" alt="<?php echo mc_safeHTML($v['lang']); ?>" title="<?php echo mc_safeHTML($v['lang']); ?>">
      <?php
      }
      ?>
    </div>
    <b><?php echo mc_safeHTML($v['lang']); ?></b><br><br>
    <?php echo ($v['enable']=='yes' ? '<i class="fa fa-check fa-fw"></i> '.$msg_settings71 : '<i class="fa fa-times fa-fw"></i> '.$msg_settings72) . ($v['default'] == 'yes' ? ' (' . $msg_payment_methods[1] . ')' : ''); ?>
  </div>
  <div class="panel-footer">
    <a href="<?php echo $v['web']; ?>" onclick="window.open(this);return false"><i class="fa fa-desktop fa-fw"></i></a>
    <?php
    if (DISPLAY_HELP_LINK) {
    ?>
    <a href="../docs/<?php echo $v['docs']; ?>.html" onclick="window.open(this);return false"><i class="fa fa-book fa-fw"></i></a>
    <?php
    }
    ?>
    <a href="?p=payment-methods&amp;conf=<?php echo $k; ?>"><i class="fa fa-cog fa-fw"></i></a>
  </div>
</div>
<?php
}
}
}
?>

<div class="fieldHeadWrapper" style="margin:10px 0 20px 0">
  <p><?php echo $msg_paymethods37.' ('.count($noneGateway).')'; ?>:</p>
</div>

<?php
if (!empty($mcSystemPaymentMethods)) {
foreach ($mcSystemPaymentMethods AS $k => $v) {
if (in_array($k,$noneGateway)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="methodimg hidden-xs hidden-sm">
      <img src="templates/images/gateways/<?php echo $v['img']; ?>" alt="<?php echo mc_safeHTML($v['lang']); ?>" title="<?php echo mc_safeHTML($v['lang']); ?>">
    </div>
    <b><?php echo mc_safeHTML($v['lang']); ?></b><br><br>
    <?php echo ($v['enable']=='yes' ? '<i class="fa fa-check fa-fw"></i> '.$msg_settings71 : '<i class="fa fa-times fa-fw"></i> '.$msg_settings72); ?>
  </div>
  <div class="panel-footer">
    <?php
    if (DISPLAY_HELP_LINK) {
    ?>
    <a href="../docs/<?php echo $v['docs']; ?>.html" onclick="window.open(this);return false"><i class="fa fa-book fa-fw"></i></a>
    <?php
    }
    ?>
    <a href="?p=payment-methods&amp;conf=<?php echo $k; ?>"><i class="fa fa-cog fa-fw"></i></a>
  </div>
</div>
<?php
}
}
}
?>


</div>
