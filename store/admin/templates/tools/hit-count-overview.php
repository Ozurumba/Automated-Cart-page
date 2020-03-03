<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_hit_overview5);
}
?>

<div class="fieldHeadWrapper">
  <p><span style="float:right"><?php echo ($uDel=='yes' ? '<a href="?p=hit-count-overview&amp;reset='.(isset($_GET['cat']) ? mc_digitSan($_GET['cat']) : 'all').(isset($_GET['cat']) ? '&amp;cat='.mc_digitSan($_GET['cat']) : '').'" title="'.mc_cleanDataEntVars($msg_hit_overview3).'" onclick="return mc_confirmMessage(\''.(isset($_GET['cat']) ? mc_filterJS($msg_javascript166) : mc_filterJS($msg_javascript165)).'\')"><i class="fa fa-refresh fa-fw mc-red"></i></a> ' : ''); ?><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a href="?p=hit-count-overview&amp;export=<?php echo (isset($_GET['cat']) ? mc_digitSan($_GET['cat']) : 'all').(isset($_GET['from']) ? '&amp;from='.mc_checkValidDate($_GET['from']) : '').(isset($_GET['to']) ? '&amp;to='.mc_checkValidDate($_GET['to']) : ''); ?>" title="<?php echo mc_cleanDataEntVars($msg_hit_overview4); ?>"><i class="fa fa-save fa-fw"></i></a></span><?php echo mc_cleanData($msg_javascript164); ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=hit-count-overview"><?php echo $msg_productmanage5; ?></option>
  <?php
  $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '1'
            AND `childOf`    = '0'
            AND `enCat`      = 'yes'
            ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CATS = mysqli_fetch_object($q_cats)) {
  ?>
  <option value="?p=hit-count-overview&amp;cat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
  <?php
  $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                WHERE `catLevel` = '2'
                AND `enCat`      = 'yes'
                AND `childOf`    = '{$CATS->id}'
                ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CHILDREN = mysqli_fetch_object($q_children)) {
  ?>
  <option value="?p=hit-count-overview&amp;cat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
  <?php
  $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
               WHERE `catLevel` = '3'
               AND `childOf`    = '{$CHILDREN->id}'
               AND `enCat`      = 'yes'
               ORDER BY `catname`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($INFANTS = mysqli_fetch_object($q_infants)) {
  ?>
  <option value="?p=hit-count-overview&amp;cat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
  <?php
  }
  }
  }
  ?>
  </select>
</div>

<?php
$totalHits = mc_sumCount('products','pVisits');
$SQL       = '';
if (isset($_GET['cat'])) {
  $SQL = 'AND `category` = \''.mc_digitSan($_GET['cat']).'\'';
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
       LEFT JOIN `" . DB_PREFIX . "prod_category`
       ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
       WHERE `pEnable`                = 'yes'
       $SQL
       GROUP BY `" . DB_PREFIX . "products`.`id`
       ORDER BY `pVisits` DESC,`pName`
       LIMIT $limit,".PRODUCTS_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
if (mysqli_num_rows($q_p)>0) {
while ($PROD = mysqli_fetch_object($q_p)) {
$perc = 0;
// Prevent division by zero errors..
if ($PROD->pVisits>0) {
  $perc = number_format($PROD->pVisits/$totalHits*100,STATS_DECIMAL_PLACES);
}
?>
<div class="panel panel-default">
  <div class="panel-body">
    <a href="?p=add-product&amp;edit=<?php echo $PROD->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($PROD->pName); ?>"><?php echo mc_safeHTML($PROD->pName); ?></a>
  </div>
  <div class="panel-footer">
    <?php echo number_format($PROD->pVisits); ?> / <?php echo $perc; ?>%
  </div>
</div>
<?php
}
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($c->rows>0 && $c->rows>PER_PAGE) {
  $PGS = new pagination(array($c->rows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<span class="noData"><?php echo $msg_hit_overview2; ?></span>
<?php
}
?>
</div>
