<?php
if (!isset($PM->defmeth)) {
  exit;
}
?>
<div class="formFieldGatewayTop">
 <div class="btn-group">
   <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     <?php echo $msg_payment_methods[17]; ?> <i class="fa fa-chevron-down fa-fw"></i>
   </button>
   <ul class="dropdown-menu dropdown-menu-right">
     <?php
     if (!empty($mcSystemPaymentMethods)) {
       foreach ($mcSystemPaymentMethods AS $k => $v) {
         if (!in_array($k,$noneGateway)) {
         ?>
         <li<?php echo (isset($_GET['conf']) && $_GET['conf'] == $k? ' class="active"': ''); ?>><a href="?p=payment-methods&amp;conf=<?php echo $k; ?>"><?php echo mc_safeHTML($v['lang']); ?></a></li>
         <?php
         }
       }
     }
     ?>
     <li role="separator" class="divider"></li>
     <?php
     if (!empty($mcSystemPaymentMethods)) {
       foreach ($mcSystemPaymentMethods AS $k => $v) {
         if (in_array($k,$noneGateway)) {
         ?>
         <li<?php echo (isset($_GET['conf']) && $_GET['conf'] == $k? ' class="active"': ''); ?>><a href="?p=payment-methods&amp;conf=<?php echo $k; ?>"><?php echo mc_safeHTML($v['lang']); ?></a></li>
         <?php
         }
       }
     }
     ?>
   </ul>
 </div>
</div>