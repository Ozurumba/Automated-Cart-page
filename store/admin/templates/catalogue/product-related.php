<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_prodrelated9);
}
if (isset($OK2) && $cnt>0) {
  echo mc_actionCompleted($msg_prodrelated10);
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('product-related');
  $qLinksIcon = 'exchange';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>

<form method="post" id="form" action="?p=product-related&amp;product=<?php echo mc_digitSan($_GET['product']); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_prodrelated2; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <?php
    $cats   = array();
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    $cats[] = $CATS->id;
    ?>
    <input onclick="mc_loadProducts('cat-<?php echo $CATS->id; ?>','<?php echo mc_digitSan($_GET['product']); ?>','related')" type="radio" name="pCat" value="cat-<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    &nbsp;&nbsp;<input onclick="mc_loadProducts('child-<?php echo $CHILDREN->id; ?>','<?php echo mc_digitSan($_GET['product']); ?>','related')" type="radio" name="pCat" value="child-<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input onclick="mc_loadProducts('infant-<?php echo $INFANTS->id; ?>','<?php echo mc_digitSan($_GET['product']); ?>','related')" type="radio" name="pCat" value="infant-<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    }
    }
    ?>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div id="products">
    <input type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_prodrelated7; ?></b><br>
    <?php
    $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                  FROM `" . DB_PREFIX . "products`
                  LEFT JOIN `" . DB_PREFIX . "prod_category`
                  ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                  WHERE `category`                          = '".$cats[0]."'
                  AND `pEnable`                             = 'yes'
                  AND `" . DB_PREFIX . "products`.`id` != '".mc_digitSan($_GET['product'])."'
                  GROUP BY `" . DB_PREFIX . "products`.`id`
                  ORDER BY `pName`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($PR = mysqli_fetch_object($q_products)) {
    ?>
    <input type="checkbox" name="product[]" value="<?php echo $PR->pid; ?>"> <?php echo mc_safeHTML($PR->pName); ?><br>
    <?php
    }
    ?>
    </div>
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_prodrelated12; ?>: <?php echo mc_displayHelpTip($msg_javascript381,'RIGHT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo (++$tabIndex); ?>" name="mirror" value="yes" checked="checked"> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="mirror" value="no">
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_prodrelated3); ?>" title="<?php echo mc_cleanDataEntVars($msg_prodrelated3); ?>">
</p>
</form>

<?php
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "prod_relation`.`id` AS `rid` FROM `" . DB_PREFIX . "prod_relation`
                    LEFT JOIN `" . DB_PREFIX . "products`
                    ON `" . DB_PREFIX . "prod_relation`.`related` = `" . DB_PREFIX . "products`.`id`
                    WHERE `product`  = '".mc_digitSan($_GET['product'])."'
                    ORDER BY `pName`
                    ") or die(mc_MySQLError(__LINE__,__FILE__));
if (mysqli_num_rows($q_p)>0) {
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo $msg_prodrelated4; ?> (<?php echo mysqli_num_rows($q_p); ?>):</p>
</div>
<?php
while ($PROD = mysqli_fetch_object($q_p)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <a href="?p=add-product&amp;edit=<?php echo $PROD->related; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9.': '.mc_cleanData($PROD->pName)); ?>"><?php echo mc_safeHTML($PROD->pName); ?></a>
  </div>
  <?php
  if ($uDel == 'yes') {
  ?>
  <div class="panel-footer">
    <a href="?p=product-related&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;del=<?php echo $PROD->rid; ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
  </div>
  <?php
  }
  ?>
</div>
<?php
}
} else {
?>
<div class="fieldHeadWrapper" style="margin-top:30px">
  <p><?php echo $msg_prodrelated4; ?> (0):</p>
</div>

<span class="noData"><?php echo $msg_prodrelated11; ?></span>
<?php
}
?>


</div>
