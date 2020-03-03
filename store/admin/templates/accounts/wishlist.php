<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<div class="fieldHeadWrapper">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a> <a href="?p=wishlist&amp;export=<?php echo (isset($_GET['cat']) ? mc_digitSan($_GET['cat']) : 'all'); ?>" title="<?php echo mc_cleanDataEntVars($msg_accwishlist2); ?>"><i class="fa fa-save fa-fw"></i></a></span><?php echo mc_cleanData($msg_admin3_0[49]); ?>:</p>
</div>

<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
  <option value="?p=wishlist"><?php echo $msg_accwishlist5; ?></option>
  <?php
  $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
            WHERE `catLevel` = '1'
            AND `childOf`    = '0'
            AND `enCat`      = 'yes'
            ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CATS = mysqli_fetch_object($q_cats)) {
  ?>
  <option value="?p=wishlist&amp;cat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
  <?php
  $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                WHERE `catLevel` = '2'
                AND `enCat`      = 'yes'
                AND `childOf`    = '{$CATS->id}'
                ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CHILDREN = mysqli_fetch_object($q_children)) {
  ?>
  <option value="?p=wishlist&amp;cat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
  <?php
  $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
               WHERE `catLevel` = '3'
               AND `childOf`    = '{$CHILDREN->id}'
               AND `enCat`      = 'yes'
               ORDER BY `catname`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($INFANTS = mysqli_fetch_object($q_infants)) {
  ?>
  <option value="?p=wishlist&amp;cat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
  <?php
  }
  }
  }
  ?>
  </select>
</div>

<?php
$totalHits = mc_rowCount('accounts_wish');
$SQL       = '';
if (isset($_GET['cat'])) {
  $SQL = 'AND `category` = \'' . mc_digitSan($_GET['cat']) . '\'';
}
$q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
       (SELECT count(*) FROM `" . DB_PREFIX . "accounts_wish`
        WHERE `" . DB_PREFIX . "accounts_wish`.`product` = `" . DB_PREFIX . "products`.`id`
       ) AS `saveCnt`,
       `" . DB_PREFIX . "products`.`id` AS `pid`
       FROM `" . DB_PREFIX . "accounts_wish`
       LEFT JOIN `" . DB_PREFIX . "products`
       ON `" . DB_PREFIX . "accounts_wish`.`product`  = `" . DB_PREFIX . "products`.`id`
       LEFT JOIN `" . DB_PREFIX . "prod_category`
       ON `" . DB_PREFIX . "products`.`id`  = `" . DB_PREFIX . "prod_category`.`product`
       WHERE `pEnable` = 'yes'
       $SQL
       GROUP BY `" . DB_PREFIX . "accounts_wish`.`product`
       ORDER BY `saveCnt` DESC,`pName`
       LIMIT $limit,".PRODUCTS_PER_PAGE."
       ") or die(mc_MySQLError(__LINE__,__FILE__));
$c = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
if (mysqli_num_rows($q_p)>0) {
while ($PROD = mysqli_fetch_object($q_p)) {
$perc = 0;
// Prevent division by zero errors..
if ($PROD->saveCnt>0) {
  $perc = number_format($PROD->saveCnt/$totalHits*100,STATS_DECIMAL_PLACES);
}
?>
<div class="panel panel-default">
  <div class="panel-body">
    <a href="?p=add-product&amp;edit=<?php echo $PROD->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9).': '.mc_safeHTML($PROD->pName); ?>"><?php echo mc_safeHTML($PROD->pName); ?></a>
  </div>
  <div class="panel-footer">
    <?php echo number_format($PROD->saveCnt); ?> / <?php echo $perc; ?>%
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
<span class="noData"><?php echo $msg_accwishlist; ?></span>
<?php
}
?>
</div>
