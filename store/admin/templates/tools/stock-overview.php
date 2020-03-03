<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($stock_overview5);
}

?>
<div class="fieldHeadWrapper">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a href="?p=stock-overview&amp;export=<?php echo (isset($_GET['cat']) ? mc_digitSan($_GET['cat']) : 'all'); ?>" title="<?php echo mc_safeHTML($stock_overview3); ?>"><i class="fa fa-save fa-fw"></i></a></span><input type="checkbox" name="log" value="" onclick="mc_toggleCheckBoxes(this.checked,'formArea')"> <?php echo mc_cleanData($msg_header19); ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
  <option value="?p=stock-overview"><?php echo $msg_productmanage5; ?></option>
  <?php
  $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '1'
            AND `childOf`    = '0'
            AND `enCat`      = 'yes'
            ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CATS = mysqli_fetch_object($q_cats)) {
  ?>
  <option value="?p=stock-overview&amp;cat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
  <?php
  $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                WHERE `catLevel` = '2'
                AND `enCat`      = 'yes'
                AND `childOf`    = '{$CATS->id}'
                ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CHILDREN = mysqli_fetch_object($q_children)) {
  ?>
  <option value="?p=stock-overview&amp;cat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
  <?php
  $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
               WHERE `catLevel` = '3'
               AND `childOf`    = '{$CHILDREN->id}'
               AND `enCat`      = 'yes'
               ORDER BY `catname`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($INFANTS = mysqli_fetch_object($q_infants)) {
  ?>
  <option value="?p=stock-overview&amp;cat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
  <?php
  }
  }
  }
  ?>
  </select>
</div>

<div class="formArea">
<form method="post" id="form" action="?p=stock-overview<?php echo (isset($_GET['cat']) ? '&amp;cat='.mc_digitSan($_GET['cat']) : ''); ?>" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<?php
$SQL       = '';
if (isset($_GET['cat'])) {
  $SQL = 'AND `category` = \''.mc_digitSan($_GET['cat']).'\'';
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
       LEFT JOIN `" . DB_PREFIX . "prod_category`
       ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
       WHERE `pEnable`              = 'yes'
       $SQL
       GROUP BY `" . DB_PREFIX . "products`.`id`
       ORDER BY `pStock` DESC,`pName`
       LIMIT $limit,".PRODUCTS_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
if (mysqli_num_rows($q_p)>0) {
while ($PROD = mysqli_fetch_object($q_p)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <input type="checkbox" name="prod[]" value="<?php echo $PROD->pid; ?>"> <a href="?p=add-product&amp;edit=<?php echo $PROD->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($PROD->pName); ?>"><?php echo mc_safeHTML($PROD->pName); ?></a> (<?php echo $stock_overview4.': '.$PROD->pStock; ?>)
    <?php
    $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
                  WHERE `productID` = '{$PROD->pid}'
                  ORDER BY `orderBy`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($AG = mysqli_fetch_object($q_products)) {
    $q_a = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
           WHERE `attrGroup` = '{$AG->id}'
           ORDER BY `orderBy`
           ") or die(mc_MySQLError(__LINE__,__FILE__));
    if (mysqli_num_rows($q_products) > 0) {
    ?><hr><?php
    while ($ATTR = mysqli_fetch_object($q_a)) {
    ?>
    <input type="checkbox" name="attr[]" value="<?php echo $ATTR->id; ?>"> <a href="?p=product-attributes&amp;product=<?php echo $PROD->pid; ?>" title="<?php echo mc_safeHTML($AG->groupName); ?> / <?php echo mc_safeHTML($ATTR->attrName); ?>"><?php echo mc_safeHTML($AG->groupName); ?> / <?php echo mc_safeHTML($ATTR->attrName); ?></a> (<?php echo $stock_overview4.': '.$ATTR->attrStock; ?>)<br>
    <?php
    }
    }

    }
    ?>
  </div>
</div>
<?php
}
?>
<div class="fieldHeadWrapper">
  <p><?php echo $stock_overview6; ?>:</p>
</div>

<div class="formFieldWrapper">
  <input type="hidden" name="process" value="yes">
  <input type="text" name="stock" value="0" class="box">
  <p style="text-align:center;margin-top:20px">
    <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML($stock_overview7); ?>" title="<?php echo mc_safeHTML($stock_overview7); ?>">
  </p>
</div>

<?php
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($c->rows>0 && $c->rows>PER_PAGE) {
  $PGS = new pagination(array($c->rows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<span class="noData"><?php echo $stock_overview2; ?></span>
<?php
}
?>

</form>
</div>

</div>
