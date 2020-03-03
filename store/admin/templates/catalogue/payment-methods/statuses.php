<?php
$ops          = array(
 'completed'  => $callBack['completed'],
 'download'   => $callBack['download'],
 'virtual'    => $callBack['virtual'],
 'pending'    => $callBack['pending'],
 'cancelled'  => $callBack['cancelled'],
 'refunded'   => $callBack['refunded']
);
$defaults     = array(
 'completed'  => 'shipping',
 'download'   => 'completed',
 'virtual'    => 'completed',
 'pending'    => 'pending',
 'cancelled'  => 'cancelled',
 'refunded'   => 'refund'
);
$pS  = mc_loadDefaultStatuses();
$cS  = array();
$cuR = ($PM->statuses ? unserialize($PM->statuses) : $defaults);
// Get additional payment statuses..
$qS  = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "paystatuses`
       WHERE `pMethod` IN('all','{$_GET['conf']}')
       ORDER BY `pMethod`,`statname`
       ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($qS)>0) {
  while ($ST = mysqli_fetch_object($qS)) {
    $cS[$ST->id] = mc_safeHTML($ST->statname);
  }
}
// What doesn`t apply to this gateway...
?>
<div class="formFieldWrapper">
 <?php
 foreach ($ops AS $sK => $V) {
   if (!in_array($sK,$skipStatusDisplay)) {
   ?>
   <label<?php echo ($sK!='completed' ? ' style="margin-top:10px"' : ''); ?>><?php echo $V; ?></label>
   <select name="orderStatus[<?php echo $sK; ?>]">
   <?php
   foreach ($pS AS $k => $v) {
   ?>
   <option value="<?php echo $k; ?>"<?php echo ($k!='none' && isset($cuR[$sK]) && $cuR[$sK]==$k ? ' selected="selected"' : ''); ?>><?php echo $v; ?></option>
   <?php
   }
   if (!empty($cS)){
   ?>
   <option value="none">- - - - - - - -</option>
   <?php
   foreach ($cS AS $k => $v) {
   ?>
   <option value="<?php echo $k; ?>"<?php echo ($k!='none' && isset($cuR[$sK]) && $cuR[$sK]==$k ? ' selected="selected"' : ''); ?>><?php echo $v; ?></option>
   <?php
   }
   }
   ?>
   </select>
   <?php
   }
 }
 ?>
</div>