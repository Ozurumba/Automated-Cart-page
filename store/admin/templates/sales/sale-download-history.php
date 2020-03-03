<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>

<div id="content">
<script>
//<![CDATA[
function mc_changeButtonCount(form,type) {
  var count = 0;
  if (type=='all') {
    mc_selectAll();
  }
  if (type=='single' && document.getElementById('log').checked==true) {
    document.getElementById('log').checked=false;
  }
  for (i = 0; i < form.elements.length; i++){
    var current = form.elements[i];
    if(current.name!='log' && current.type == 'checkbox' && current.checked){
      count++;
    }
  }
  if (count>0) {
    jQuery('#button').prop('disabled', false);
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanDataEntVars($msg_viewsale5)); ?> ('+count+')');
  } else {
    jQuery('#button').prop('disabled', true);
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanDataEntVars($msg_viewsale5)); ?> (0)');
  }
}
function mc_checkform(form) {
  <?php
  if ($SALE->bill_2=='') {
  ?>
  mc_alertBox('<?php echo mc_cleanDataEntVars($msg_javascript427); ?>');
  return false;
  <?php
  }
  ?>
  var message = '';
  var count   = 0;
  for (i = 0; i < form.elements.length; i++){
    var current = form.elements[i];
    if(current.type == 'checkbox' && current.checked){
      count++;
    }
  }
  if (count==0) {
    message +='- <?php echo mc_cleanDataEntVars($msg_javascript145); ?>';
  }
  if (message) {
    mc_alertBox(message);
    return false;
  } else {
    return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript142); ?>');
  }
}
//]]>
</script>

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',count($_POST['id']),$msg_viewsale36));
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_viewsale83);
  $SALE = mc_getTableData('sales','id', (int) $_GET['sale']);
}
if (isset($OK3)) {
  echo mc_actionCompleted($msg_viewsale84);
  $SALE = mc_getTableData('sales','id', (int) $_GET['sale']);
}
?>

<div class="alert alert-info">
  <?php
  $dPage      = true;
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['sale'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  $a      = array_merge(range('a','z'),range(1,9));
  shuffle($a);
  $append = $a[4].$a[23];
  ?>
</div>

<form method="post" id="form" action="?p=downloads&amp;sale=<?php echo (int) $_GET['sale']; ?>" onsubmit="return mc_checkform(this)">
<div class="fieldHeadWrapper">
  <p>
  <span style="float:right">
    <a href="index.php?p=downloads&amp;atoken=<?php echo (int) $_GET['sale'].'-'.$SALE->buyCode; ?>" title="<?php echo mc_cleanDataEntVars($msg_viewsale80); ?>" onclick="window.open(this);return false"><i class="fa fa-search fa-fw"></i></a>
    <a onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')" href="?p=downloads&amp;sale=<?php echo (int) $_GET['sale']; ?>&amp;action=<?php echo ($SALE->downloadLock=='yes' ? 'unlock' : 'lock'); ?>&amp;status=<?php echo $SALE->paymentStatus; ?>" title="<?php echo mc_safeHTML(($SALE->downloadLock=='yes' ? $msg_viewsale82 : $msg_viewsale81)); ?>"><?php echo ($SALE->downloadLock=='yes' ? '<i class="fa fa-unlock fa-fw"></i>' : '<i class="fa fa-lock fa-fw"></i>'); ?></a>
    <?php
	  if ($SETTINGS->downloadRestrictIP == 'yes') {
	  ?>
	  <a onclick="jQuery('#ipRestrictionBox').slideToggle();return false" href="#" title="<?php echo mc_cleanDataEntVars($msg_viewsale122); ?>"><i class="fa fa-globe fa-fw"></i></a>
	  <?php
	  }
	  ?>
  </span>
  <?php echo $msg_viewsale3; ?>:</p>
</div>

<div class="formFieldWrapper" id="ipRestrictionBox" style="display:none">
  <p><textarea name="ips" rows="3" cols="20" style="height:80px"><?php echo mc_safeHTML($SALE->ipAccess); ?></textarea>
   <span style="display:block;margin-top:5px">
    <?php echo str_replace('{sale}',(int) $_GET['sale'],$msg_viewsale123); ?>
   </span>
  </p>
</div>

<?php
$q_down = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,
          `" . DB_PREFIX . "products`.`id` AS `pid`,
          `" . DB_PREFIX . "purchases`.`id` AS `pur_id`
          FROM `" . DB_PREFIX . "purchases`
          LEFT JOIN `" . DB_PREFIX . "products`
          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
          WHERE `saleID`                        = '" . (int) $_GET['sale'] . "'
          AND `productType`                     = 'download'
          ORDER BY `" . DB_PREFIX . "purchases`.`id`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_down)>0) {
  while ($DOWN = mysqli_fetch_object($q_down)) {
  $details      = '';
  $code         = ($DOWN->pCode ? $DOWN->pCode : 'N/A');
  $weight       = ($DOWN->pWeight ? $DOWN->pWeight : 'N/A');
  $DOWN->pName  = ($DOWN->pName ? $DOWN->pName : $DOWN->deletedProductName);
  $isDel        = ($DOWN->deletedProductName ? '<span class="deletedItem">'.$msg_script53.'</span>' : '');
  $img          = mc_storeProductImg($DOWN->pid,$DOWN,true);
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <span style="float:right" class="productimg hidden-xs"><?php echo $img; ?></span>
      <?php
      if ($isDel=='') {
      ?>
      <input type="checkbox" name="id[]" value="<?php echo $DOWN->pur_id; ?>" onclick="mc_changeButtonCount(this.form,'single')">
      <?php
      }
      ?>
      <b><?php echo mc_safeHTML($DOWN->pName); ?></b> <?php echo ($isDel ? $isDel : '');
      echo ($code ? '<br><br>' . $code : '');
      if ($DOWN->wishpur > 0) {
      ?>
      <div class="alert alert-warning" style="margin:10px 0 0 0">
      <i class="fa fa-warning fa-fw"></i> <?php echo $msg_sales_screen[1]; ?> <b><?php echo mc_safeHTML($SALE->ship_1); ?></b>
      </div>
      <?php
      }
      ?>
    </div>
    <div class="panel-footer">
      <a onclick="mc_Window(this.href,'<?php echo DIVWIN_FIELD_INFO_HEIGHT; ?>','<?php echo DIVWIN_FIELD_INFO_WIDTH; ?>',this.title);return false" href="?p=downloads&amp;sale=<?php echo (int) $_GET['sale']; ?>&amp;ch=<?php echo $DOWN->pur_id; ?>" title="<?php echo mc_cleanDataEntVars($msg_viewsale69); ?>"><i class="fa fa-mouse-pointer fa-fw"></i> <?php echo $msg_viewsale69; ?></a>
    </div>
  </div>
  <?php
  }
  ?>
  <p style="text-align:left;margin-top:20px">
    <input type="hidden" name="process" value="yes">
    <input type="hidden" name="saleID" value="<?php echo (int) $_GET['sale']; ?>">
    <input type="checkbox" name="log" id="log" onclick="mc_changeButtonCount(this.form,'all')">&nbsp;&nbsp;&nbsp;
    <input class="btn btn-primary" disabled="disabled" id="button" type="submit" value="<?php echo mc_cleanDataEntVars($msg_viewsale5); ?> (0)" title="<?php echo mc_cleanDataEntVars($msg_viewsale5); ?>">
  </p>

  <div class="fieldHeadWrapper" style="margin-top:30px">
   <p><span style="float:right"><a href="javascript:window.print()"><i class="fa fa-print fa-fw"></i></a></span><?php echo (isset($_GET['ch']) ? $msg_viewsale69 : $msg_viewsale17); ?>:</p>
  </div>

  <?php
  $q_activation = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,DATE_FORMAT(`restoreDate`,'" . $SETTINGS->mysqlDateFormat . "') AS `rdate`
                  FROM `" . DB_PREFIX . "activation_history`
                  WHERE `saleID` = '" . (int) $_GET['sale'] . "'
                  ORDER BY `id` DESC
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_activation)>0) {
    while ($AH = mysqli_fetch_object($q_activation)) {
    if (isset($AH->products)) {
    ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <?php
        $P = array_map('trim',explode('|',$AH->products));
        foreach ($P AS $id) {
          $PRD  = mc_getTableData('products','id',substr($id,1));
          echo '<div>' . (isset($PRD->pName) ? mc_safeHTML($PRD->pName) : $msg_script53) . '</div>';
        }
        ?>
      </div>
      <div class="panel-footer">
        <i class="fa fa-clock-o fa-fw"></i> <?php echo str_replace(array('{user}','{time}','{date}'),array($AH->adminUser,$AH->restoreTime,$AH->rdate),$msg_viewsale67); ?>
      </div>
    </div>
    <?php
    }
    }
  } else {
  ?>
  <span class="noData"><?php echo $msg_viewsale18; ?></span>
  <?php
  }
} else {
?>
<span class="noData"><?php echo $msg_viewsale15; ?></span>
<?php
}
?>
</form>

</div>

