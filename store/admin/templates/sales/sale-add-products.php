<?php if (!defined('PARENT') || !isset($_GET['sale']) || !isset($_GET['type'])) { die('Permission Denied'); }
$SALE  = mc_getTableData('sales','id',mc_digitSan($_GET['sale']));
if (!isset($SALE->buyCode)) {
  exit;
}
if (!in_array($_GET['type'], array('physical','download'))) {
  exit;
}
define('SALES_ADD_PRODUCTS', 1);
?>

<form method="post" id="form" action="?p=add&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>&amp;type=<?php echo mc_safeHTML($_GET['type']); ?>" onsubmit="return confirmMessage_Add('<?php echo mc_cleanDataEntVars($msg_javascript45); ?>')">
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
    jQuery('#counter').val(count);
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanDataEntVars($msg_viewsale9)); ?> ('+count+')');
    jQuery('#button').prop('disabled', false);
  } else {
    jQuery('#button').prop('disabled', true);
    jQuery('#counter').val('0');
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanDataEntVars($msg_viewsale9)); ?> (0)');
  }
}
function confirmMessage_Add(txt) {
  if (jQuery('#counter').val()=='0' || jQuery('#counter').val()=='') {
    mc_alertBox('<?php echo mc_filterJS($msg_javascript325); ?>');
    return false;
  }
  var confirmSub = confirm(txt);
  if (confirmSub) {
    return true;
  } else {
    return false;
  }
}
//]]>
</script>

<?php
if (isset($OK)) {
$c  = (!empty($_POST['product']) ? count($_POST['product']) : '0');
?>
<div class="reloading">
  <?php echo str_replace('{count}', $c, $msg_viewsale12); ?><br><br>
  <img src="templates/images/doing-something.gif" alt="" title=""><br><br>
  <?php echo $msg_viewsale46; ?>
</div>
<?php
} else {
?>
<div class="alert alert-info">
  <?php
  $qLinksArr  = array('xxx');
  $qLinksIcon = 'cube';
  $saleID     = (int) $_GET['sale'];
  include(PATH . 'templates/sales/sales-quick-links.php');
  ?>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="0"><?php echo $msg_viewsale66; ?></option>
    <option value="0">- - - - - -</option>
    <?php
    $cats   = array();
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    $cats[] = $CATS->id;
    ?>
    <option value="?p=add&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>&amp;type=<?php echo $_GET['type']; ?>&amp;catid=<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 || isset($_GET['catid']) && $_GET['catid']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="?p=add&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>&amp;type=<?php echo $_GET['type']; ?>&amp;catid=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['catid']) && $_GET['catid']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;<?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 AND `enCat`      = 'yes'
                 ORDER BY `catname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="?p=add&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>&amp;type=<?php echo $_GET['type']; ?>&amp;catid=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['catid']) && $_GET['catid']==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </select>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <?php
  $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                FROM `" . DB_PREFIX . "products`
                LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                WHERE `category`                = '".(isset($_GET['catid']) ? mc_digitSan($_GET['catid']) : (isset($cats[0]) ? $cats[0] : '0'))."'
                AND `pEnable`                   = 'yes'
                ".(isset($_GET['type']) && $_GET['type']=='download' ? 'AND `pDownload` = \'yes\'' : 'AND `pDownload` = \'no\' AND `pStock` > 0')."
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_products)>0) {
  ?>
  <input type="checkbox" name="log" value="all" id="log" onclick="mc_changeButtonCount(this.form,'all')"> <b><?php echo $msg_prodrelated7; ?></b><br>
  <?php
  while ($PR = mysqli_fetch_object($q_products)) {
  ?>
  <input type="checkbox" name="product[]" value="<?php echo $PR->pid; ?>" onclick="mc_changeButtonCount(this.form,'single')"> <?php echo mc_safeHTML($PR->pName).' - <b>'.mc_currencyFormat(mc_formatPrice($PR->pOffer>0 ? $PR->pOffer : $PR->pPrice)); ?></b> <a href="?p=add-product&amp;edit=<?php echo $PR->pid; ?>" onclick="window.open(this);return false"><i class="fa fa-search fa-fw"></i></a><br>
  <?php
  }
  } else {
  echo ($_GET['type']=='download' ? $msg_productprices18 : $msg_productprices17);
  }
  ?>
</div>
<?php
if (mysqli_num_rows($q_products) > 0) {
?>
<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="counter" id="counter" value="0">
 <input type="hidden" name="process" value="yes">
 <input type="hidden" name="pCat" value="<?php echo (isset($_GET['catid']) ? mc_digitSan($_GET['catid']) : (isset($cats[0]) ? $cats[0] : '0')); ?>">
 <input type="hidden" name="buyCode" value="<?php echo $SALE->buyCode; ?>">
 <input type="hidden" name="purchaseDate" value="<?php echo $SALE->purchaseDate; ?>">
 <input type="hidden" name="purchaseTime" value="<?php echo $SALE->purchaseTime; ?>">
 <input type="hidden" name="weight" value="<?php echo $SALE->cartWeight; ?>">
 <input type="hidden" name="status" value="<?php echo $SALE->paymentStatus; ?>">
 <input type="hidden" name="type" value="<?php echo (in_array($_GET['type'],array('physical','download')) ? $_GET['type'] : 'physical'); ?>">
 <input class="btn btn-primary" disabled="disabled" type="submit" id="button" value="<?php echo mc_cleanDataEntVars($msg_viewsale9); ?> (0)" title="<?php echo mc_cleanDataEntVars($msg_viewsale9); ?>">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=sales-view&amp;sale=<?php echo mc_digitSan($_GET['sale']); ?>'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>
<?php
}
}
?>
</div>
</form>