<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>

<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_admin_viewsale3_0[15]);
}
if (isset($apisent)) {
  echo mc_actionCompleted($msg_admin_viewsale3_0[29]);
}
?>

<script>
//<![CDATA[
function confirmMessage_Add(txt) {
  var confirmSub = confirm(txt);
  if (confirmSub) {
    return true;
  } else {
    return false;
  }
}
//]]>
</script>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('xxx');
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['ordered'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>

<?php
// Any drop shippers?
if (mc_rowCount('dropshippers') > 0) {
  define('DROP_SHIPPERS_ACTIVE', 1);
}

?>
<div id="formField">
<form method="post" id="suform" action="index.php?p=sales&amp;ordered=<?php echo mc_digitSan($_GET['ordered']); ?>" onsubmit="return confirmMessage_Add('<?php echo mc_cleanDataEntVars($msg_javascript); ?>')">
<?php
// Check for incomplete sale..
$SALE = mc_getTableData('sales','id',
          mc_digitSan($_GET['ordered']),
          '',
          '*,DATE_FORMAT(`purchaseDate`,\''.$SETTINGS->mysqlDateFormat.'\') AS `pdate`'
        );
$q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`,
          `" . DB_PREFIX . "purchases`.`id` AS `pur_id` FROM `" . DB_PREFIX . "purchases`
          LEFT JOIN `" . DB_PREFIX . "products`
          ON `" . DB_PREFIX . "purchases`.`productID` = `" . DB_PREFIX . "products`.`id`
          WHERE `saleID`                          = '".mc_digitSan($_GET['ordered'])."'
          ".($SALE->saleConfirmation=='no' ? '' : 'AND `saleConfirmation` = \'yes\'')."
          ORDER BY `" . DB_PREFIX . "purchases`.`id`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_prod)>0) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_admin3_0[20]; ?> (<?php echo mysqli_num_rows($q_prod); ?>):</p>
</div>
<?php
$dropship_cnt = 0;
$api_cnt = array('cube' => 0, 'guardian' => 0);
while ($PRODUCT = mysqli_fetch_object($q_prod)) {
$link            = '';
$fLinks          = array();
$dropship        = 'no';
// Is this a virtual product purchase..
if ($PRODUCT->productType=='virtual' && $PRODUCT->giftID>0) {
  $GIFT            = mc_getTableData('giftcerts','id',$PRODUCT->giftID);
  $a_total         = $PRODUCT->attrPrice;
  $p_total         = $PRODUCT->persPrice;
  $code            = ($PRODUCT->pCode ? $PRODUCT->pCode : '&nbsp;');
  $weight          = 'N/A';
  $PRODUCT->pName  = (isset($GIFT->name) ? $GIFT->name : $PRODUCT->deletedProductName);
  $isDel           = ($PRODUCT->deletedProductName ? '<span class="deletedItem">('.$msg_script53.')</span>' : '');
  $img             = mc_storeProductImg($PRODUCT->pid,$PRODUCT,true,(isset($GIFT->image) ? $GIFT->image : ''));
  if ($isDel == '') {
    $fLinks[] = '<a href="?p=gift&amp;edit=' . $PRODUCT->giftID . '"><i class="fa fa-pencil fa-fw"></i> ' . $msg_admin3_0[22] . '</a>';
  }
  $dropship        = 'no';
} else {
  $a_total         = $PRODUCT->attrPrice;
  $p_total         = $PRODUCT->persPrice;
  $code            = ($PRODUCT->pCode ? $PRODUCT->pCode : 'N/A');
  $weight          = ($PRODUCT->pWeight ? $PRODUCT->pWeight : 'N/A');
  $PRODUCT->pName  = ($PRODUCT->pName ? $PRODUCT->pName : $PRODUCT->deletedProductName);
  $isDel           = ($PRODUCT->deletedProductName ? '<span class="deletedItem">('.$msg_script53.')</span>' : '');
  $img             = mc_storeProductImg($PRODUCT->pid,$PRODUCT,true,'');
  if ($isDel == '') {
    $fLinks[] = '<a href="?p=add-product&amp;edit=' . $PRODUCT->pid . '"><i class="fa fa-pencil fa-fw"></i> ' . $msg_admin3_0[22] . '</a>';
  }
  $dropship        = (defined('DROP_SHIPPERS_ACTIVE') && $PRODUCT->dropshipping > 0 ? 'yes' : 'no');
  if ($dropship == 'yes') {
    $DS       = mc_getTableData('dropshippers','id',$PRODUCT->dropshipping);
    if (isset($DS->id)) {
      ++$dropship_cnt;
      $fLinks[] = '<a href="?p=drop&amp;edit=' . $PRODUCT->dropshipping . '"><i class="fa fa-truck fa-fw"></i> ' . mc_cleanData($DS->name) . ' - ' . $msg_admin_viewsale3_0[13] . '</a>';
    } else {
      $dropship = 'no';
    }
  }
  if ($PRODUCT->pCube > 0 && $SETTINGS->cubeUrl && $SETTINGS->cubeAPI) {
    ++$api_cnt['cube'];
  }
  if ($PRODUCT->pGuardian > 0 && $SETTINGS->guardianUrl && $SETTINGS->guardianAPI) {
    ++$api_cnt['guardian'];
  }
}
// Downloadable?
if ($PRODUCT->productType=='download') {
  if ($PRODUCT->saleConfirmation=='yes') {
    $fLinks[] = str_replace('{sale}',mc_digitSan($_GET['ordered']),$msg_sales35);
  } else {
    $link = '';
  }
  $dropship = 'no';
}
// Gift cert..
if ($PRODUCT->productType=='virtual') {
  $fLinks[] = str_replace(array('{sale}','{purchase}'),array(mc_digitSan($_GET['ordered']),$PRODUCT->pur_id),$msg_sales46);
}
?>
<div class="panel panel-default">
  <div class="panel-body">
    <span style="float:right" class="productimg hidden-xs"><?php echo $img; ?></span>
    <?php
    if ($dropship == 'yes') {
    ?>
    <input type="checkbox" name="purchase[]" value="<?php echo $PRODUCT->pur_id; ?>" onclick="mc_chkCnt('purchase','counter','button')">
    <?php
    }
    ?>
    <b><?php echo mc_safeHTML($PRODUCT->pName); ?></b> <?php echo ($isDel ? $isDel : ''); ?>
    <?php
    echo ($code ? '<br><br>' . $code : '');
    echo ($link ? '<br><br>' . $link : '');
    //Attributes..
    if ($PRODUCT->productType == 'virtual') {
      $sgf = mc_saleGiftCerts(mc_digitSan($_GET['ordered']),$PRODUCT->pur_id);
      if (trim($sgf)) {
      ?>
      <hr>
      <?php
      echo $sgf;
      }
    } else {
      $sat = mc_saleAttributes(mc_digitSan($_GET['ordered']),$PRODUCT->pur_id,$PRODUCT->pid,false,$p_total);
      if (trim($sat)) {
      ?>
      <hr>
      <?php
      echo $sat;
      $fLinks[] = str_replace('{id}', $PRODUCT->pid, $msg_admin3_0[21]);
      }
    }
    // Personalised items..
    $q_ps = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "purch_pers`
            WHERE `purchaseID` = '{$PRODUCT->pur_id}'
            ORDER BY `id`
            ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_ps)>0) {
    ?>
    <hr>
    <?php
    while ($PS = mysqli_fetch_object($q_ps)) {
      $PERSONALISED  = mc_getTableData('personalisation','id',$PS->personalisationID);
      ?>
      <div>
      <?php
      if ($PS->visitorData && $PS->visitorData != 'no-option-selected') {
        echo '<i class="fa fa-angle-right"></i> ' . mc_persTextDisplay(mc_safeHTML($PERSONALISED->persInstructions),true).($PERSONALISED->persAddCost>0 ? ' (+'.mc_currencyFormat(mc_formatPrice($PERSONALISED->persAddCost)).')' : '').': ' . mc_safeHTML($PS->visitorData);
      }
      ?>
      </div>
      <?php

    }
    $fLinks[] = str_replace('{id}', mc_digitSan($_GET['ordered']), $msg_sales42);
    $fLinks[] = str_replace('{id}', $PRODUCT->pid, $msg_admin3_0[23]);
    }
    // Message about global discount being applied..
    if ($PRODUCT->globalDiscount > 0 && $PRODUCT->globalCost > 0) {
    ?>
    <div class="alert alert-info" style="margin:15px 0 0 0;padding:7px">
      <?php echo str_replace(array('{global}','{cost}'),array($PRODUCT->globalDiscount,mc_currencyFormat(mc_formatPrice($PRODUCT->globalCost))),($SALE->type == 'trade' ? $msg_admin_viewsale3_0[19] : $msg_viewsale85)).($a_total>0 ? ' + '.$a_total : '').($p_total>0 ? ' + '.$p_total : ''); ?></span>
    </div>
    <?php
    }
    if (!empty($fLinks)) {
    ?>
    <div id="prd_<?php echo $PRODUCT->pid; ?>-<?php echo $PRODUCT->pur_id; ?>" style="display:none">
    <hr>
    <?php
    echo implode('<br>', $fLinks);
    ?>
    </div>
    <?php
    }
    ?>
  </div>
  <div class="panel-footer">
   <?php echo str_replace(array('{price}','{qty}'),array(mc_currencyFormat(mc_formatPrice($PRODUCT->productQty*($PRODUCT->salePrice),true)),$PRODUCT->productQty),$msg_sales36).($a_total>0 ? ' + '.$a_total : '').($p_total>0 ? ' + '.$p_total : ''); ?>
   <?php
   if (!empty($fLinks)) {
   ?>
   &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $PRODUCT->pid; ?>-<?php echo $PRODUCT->pur_id; ?>')"></i>
   <?php
   }
   ?>
  </div>
</div>
<?php
}
}

if ($dropship_cnt > 0) {
?>
<input type="hidden" name="counter" id="counter" value="0">
<input type="hidden" name="process" value="yes">
<input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCnt('purchase','counter','button')">&nbsp;&nbsp;&nbsp;
<button type="submit" disabled="disabled" class="btn btn-primary" id="button"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin_viewsale3_0[14]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-envelope fa-fw"></i></span> (<span class="counter">0</span>)</button>
<?php
}
if (array_sum($api_cnt) > 0) {
?>
<div class="btn-group">
  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-file-pdf-o fa-fw"></i></span><span class="hidden-xs"><?php echo $msg_admin_viewsale3_0[26]; ?></span> <i class="fa fa-chevron-down fa-fw"></i>
  </button>
  <ul class="dropdown-menu">
    <?php
    if ($api_cnt['cube'] > 0) {
    ?>
    <li><a href="#" onclick="mc_sendToAPI('cube','<?php echo $saleID; ?>');return false"><?php echo $msg_admin_viewsale3_0[27]; ?></a></li>
    <?php
    }
    if ($api_cnt['guardian'] > 0) {
    ?>
    <li><a href="#" onclick="mc_sendToAPI('guardian','<?php echo $saleID; ?>');return false"><?php echo $msg_admin_viewsale3_0[28]; ?></a></li>
    <?php
    }
    ?>
  </ul>
</div>
<?php
}
?>
<input class="btn btn-success" type="button" onclick="window.location='?p=sales<?php echo (defined('INCPL_SALE') ? '-incomplete' : ''); ?>'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</form>
</div>

</div>
