<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">
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

<?php

if (isset($OK) && $cnt>0) {
  echo mc_actionCompleted(str_replace('{count}',1,$msg_productmanage37));
}
if (isset($_GET['deleted'])) {
  echo mc_actionCompleted(str_replace('{count}',(int) $_GET['deleted'],$msg_productmanage37));
}

$SQL      = '';
$orderBy  = (isset($_GET['orderby']) && in_array($_GET['orderby'],array('name_asc','name_desc','id_asc','id_desc','price_asc','price_desc','stock_asc','stock_desc','hits_asc','hits_desc')) ? $_GET['orderby'] : 'name_asc');
switch($orderBy) {
  case 'name_asc':
    $orderBySQL = '`pName`';
    break;
  case 'name_desc':
    $orderBySQL = '`pName` DESC';
    break;
  case 'id_asc':
    $orderBySQL = '`pid`';
    break;
  case 'id_desc':
    $orderBySQL = '`pid` DESC';
    break;
  case 'price_asc':
    $orderBySQL = 'IF (`pOffer`>0,`pOffer`,`pPrice`)*100';
    break;
  case 'price_desc':
    $orderBySQL = 'IF (`pOffer`>0,`pOffer`,`pPrice`)*100 DESC';
    break;
  case 'stock_asc':
    $orderBySQL = '`pStock`';
    break;
  case 'stock_desc':
    $orderBySQL = '`pStock` DESC';
    break;
  case 'hits_asc':
    $orderBySQL = '`pVisits`';
    break;
  case 'hits_desc':
    $orderBySQL = '`pVisits` DESC';
    break;
}
if (isset($_GET['keys']) && $_GET['keys']) {
  $kys = mc_safeSQL($_GET['keys']);
  $SQL = 'WHERE `'.DB_PREFIX.'products`.`id` = \'' . (int) $kys . '\' OR `pName` LIKE \'%'.$kys.'%\' OR `pDescription` LIKE \'%'.$kys.'%\' OR `pNotes` LIKE \'%'.$kys.'%\' OR `pCode` LIKE \'%'.$kys.'%\'';
}
if (isset($_GET['cat'])) {
  $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `category` = \''.mc_digitSan($_GET['cat']).'\'';
}
if (isset($_GET['status'])) {
  switch($_GET['status']) {
    case 'yes':
    case 'no':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pEnable` = \''.$_GET['status'].'\'';
      break;
    case 'puryes':
    case 'purno':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pPurchase` = \''.substr($_GET['status'],3).'\'';
      break;
    case 'disyes':
    case 'disno':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `enDisqus` = \''.substr($_GET['status'],3).'\'';
      break;
    case 'down':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pDownload` = \'yes\'';
      break;
    case 'ship':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `freeShipping` = \'yes\'';
      break;
    case 'video':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' (`pVideo` NOT IN(\'\',\'0\') OR `pVideo2` NOT IN(\'\',\'0\') OR `pVideo3` NOT IN(\'\',\'0\'))';
      break;
    case 'offer':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pOffer` > \'0\'';
      break;
    case 'minqty':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `minPurchaseQty` > \'1\'';
      break;
    case 'restricted':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `countryRestrictions` != \'\'';
      break;
    case 'notes':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pNotes` != \'\'';
      break;
	  case 'cube':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pCube` > \'0\'';
      break;
    case 'guardian':
      $SQL .= ($SQL ? 'AND ' : 'WHERE ').' `pGuardian` > \'0\'';
      break;
  }
}

// Main query..
$q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT SQL_CALC_FOUND_ROWS *,
          DATE_FORMAT(`pDateAdded`,'" . $SETTINGS->mysqlDateFormat . "') AS `adate`,
          `" . DB_PREFIX . "products`.`id` AS `pid`
          FROM `" . DB_PREFIX . "products`
          LEFT JOIN `" . DB_PREFIX . "prod_category`
          ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
          $SQL
          GROUP BY `" . DB_PREFIX . "products`.`id`
          ORDER BY $orderBySQL
          LIMIT $limit,".PRODUCTS_PER_PAGE."
          ") or die(mc_MySQLError(__LINE__,__FILE__));
$c            = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT FOUND_ROWS() AS `rows`"));
$countedRows  = (isset($c->rows) ? number_format($c->rows,0,'.','') : '0');
?>

<div class="fieldHeadWrapper">
  <p>
  <?php
  if (mysqli_num_rows($q_prod)>0) {
  ?>
  <span style="float:right">
    <a href="#" onclick="jQuery('#filters').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a>
    <a href="#" onclick="jQuery('#filters2').slideToggle();return false"><i class="fa fa-search fa-fw"></i></a>
  </span>
  <?php
  } echo $msg_productmanage; ?> (<?php echo $countedRows; ?>):</p>
</div>
<?php
if (mysqli_num_rows($q_prod)>0) {
?>
<div class="formFieldWrapper" id="filters" style="display:none">
  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="?p=manage-products<?php echo (isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"><?php echo $msg_productmanage5; ?></option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="?p=manage-products&amp;cat=<?php echo $CATS->id; ?>&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="?p=manage-products&amp;cat=<?php echo $CHILDREN->id; ?>&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                 WHERE `catLevel` = '3'
                 AND `childOf`    = '{$CHILDREN->id}'
                 AND `enCat`      = 'yes'
                 ORDER BY `catname`
                 ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="?p=manage-products&amp;cat=<?php echo $INFANTS->id; ?>&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
  </select>

  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}" style="margin-top:10px">
    <option value="0"><?php echo $msg_productmanage34; ?></option>
    <option value="0">- - - - - - - - - -</option>
    <option value="?p=manage-products&amp;orderby=name_asc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage30; ?></option>
    <option value="?p=manage-products&amp;orderby=name_desc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='name_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage31; ?></option>
    <option value="?p=manage-products&amp;orderby=id_asc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='id_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage25; ?></option>
    <option value="?p=manage-products&amp;orderby=id_desc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='id_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage24; ?></option>
    <option value="?p=manage-products&amp;orderby=price_asc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage35; ?></option>
    <option value="?p=manage-products&amp;orderby=price_desc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='price_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage36; ?></option>
    <option value="?p=manage-products&amp;orderby=stock_asc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='stock_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage26; ?></option>
    <option value="?p=manage-products&amp;orderby=stock_desc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='stock_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage27; ?></option>
    <option value="?p=manage-products&amp;orderby=hits_asc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='hits_asc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage29; ?></option>
    <option value="?p=manage-products&amp;orderby=hits_desc&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['status']) ? '&amp;status='.$_GET['status'] : ''); ?>"<?php echo (isset($_GET['orderby']) && $_GET['orderby']=='hits_desc' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage28; ?></option>
  </select>

  <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}" style="margin-top:10px">
    <option value="0"><?php echo $msg_productmanage32; ?></option>
    <option value="0">- - - - - - - - - -</option>
    <option value="?p=manage-products<?php echo (isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"><?php echo $msg_productmanage33; ?></option>
    <option value="?p=manage-products&amp;status=yes&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='yes' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage48; ?></option>
    <option value="?p=manage-products&amp;status=no&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='no' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage49; ?></option>
    <option value="?p=manage-products&amp;status=puryes&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='puryes' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage44; ?></option>
    <option value="?p=manage-products&amp;status=purno&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='purno' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage43; ?></option>
    <option value="?p=manage-products&amp;status=disyes&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='disyes' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage45; ?></option>
    <option value="?p=manage-products&amp;status=disno&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='disno' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage46; ?></option>
    <option value="?p=manage-products&amp;status=down&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='down' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage41; ?></option>
    <option value="?p=manage-products&amp;status=ship&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='ship' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage42; ?></option>
    <option value="?p=manage-products&amp;status=video&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='video' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage47; ?></option>
    <option value="?p=manage-products&amp;status=minqty&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='minqty' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage51; ?></option>
    <option value="?p=manage-products&amp;status=restricted&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='restricted' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage56; ?></option>
    <option value="?p=manage-products&amp;status=notes&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='notes' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage65; ?></option>
    <?php
    // Show only if Maian Cube enabled..
    if ($SETTINGS->cubeAPI && $SETTINGS->cubeUrl) {
    ?>
    <option value="?p=manage-products&amp;status=cube&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='cube' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage68; ?></option>
    <?php
    }
    if ($SETTINGS->guardianAPI && $SETTINGS->guardianUrl) {
    ?>
    <option value="?p=manage-products&amp;status=guardian&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='guardian' ? ' selected="selected"' : ''); ?>><?php echo $msg_admin_product3_0[18]; ?></option>
    <?php
    }
    ?>
  	<option value="?p=manage-products&amp;status=offer&amp;page=<?php echo $page.(isset($_GET['keys']) ? '&amp;keys='.$_GET['keys'] : '').(isset($_GET['cat']) ? '&amp;cat='.$_GET['cat'] : '').(isset($_GET['orderby']) ? '&amp;orderby='.$_GET['orderby'] : ''); ?>"<?php echo (isset($_GET['status']) && $_GET['status']=='offer' ? ' selected="selected"' : ''); ?>><?php echo $msg_productmanage50; ?></option>
  </select>
</div>

<div class="formFieldWrapper" id="filters2" style="display:none">
  <form method="get" action="index.php">
  <p>
  <input type="hidden" name="p" value="manage-products"><input type="text" name="keys" class="box" placeholder="<?php echo mc_cleanDataEntVars($msg_productmanage4); ?>" value="<?php echo (isset($_GET['keys']) ? mc_safeHTML($_GET['keys']) : ''); ?>">
  <input style="margin-top:10px" type="submit" class="btn btn-primary" value="<?php echo mc_cleanDataEntVars($msg_productmanage10); ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage10); ?>">
  <input style="margin:10px 0 0 20px" type="button" onclick="window.location='?p=manage-products'" class="btn btn-success" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
  </p>
  </form>
</div>

<div id="formField">
<form method="post" action="?p=add-product&amp;edit=batch-mode">
<?php
while ($PRODUCTS = mysqli_fetch_object($q_prod)) {
$img  = mc_storeProductImg($PRODUCTS->pid,$PRODUCTS);
$atRs = mc_rowCount('attributes WHERE `productID` = \''.$PRODUCTS->pid.'\'');
?>
<div class="panel panel-default" id="prwrap_<?php echo $PRODUCTS->pid; ?>">
  <div class="panel-body">
    <span style="float:right" class="productimg hidden-xs"><?php echo $img; ?></span>
    <input type="checkbox" name="productIDs[]" onclick="mc_singleCheckBox(this.checked,'formField');mc_chkCnt('productIDs','counter','fmcnbutton');mc_chkCnt('productIDs','counter2','button2');" value="<?php echo $PRODUCTS->pid; ?>"> <b><?php echo mc_safeHTML($PRODUCTS->pName); ?></b><br><br>
    <?php echo $msg_productmanage18; ?>: <?php echo ($PRODUCTS->pCode ? $PRODUCTS->pCode : 'N/A'); ?><br>
    <?php echo $msg_productmanage13; ?>: <span id="stock_<?php echo $PRODUCTS->pid; ?>" onclick="mc_updateStock('<?php echo $PRODUCTS->pid; ?>','<?php echo ($PRODUCTS->pStock>0 ? number_format($PRODUCTS->pStock) : '0'); ?>','<?php echo mc_filterJS($msg_admin3_0[39]); ?>')" class="prodstockchange"><?php echo ($PRODUCTS->pStock>0 ? number_format($PRODUCTS->pStock) : '0'); ?></span><br>
    <div class="manageCost"><?php echo ($PRODUCTS->pOffer>0 ? '<del>'.mc_currencyFormat(mc_formatPrice($PRODUCTS->pPrice)).'</del> '.mc_currencyFormat(mc_formatPrice($PRODUCTS->pOffer)) : mc_currencyFormat(mc_formatPrice($PRODUCTS->pPrice))); ?></div>
    <div id="prd_<?php echo $PRODUCTS->pid; ?>" style="display:none">
    <hr>
    <?php echo $msg_productmanage11; ?>: <?php echo $PRODUCTS->adate; ?>, <?php echo $msg_productmanage21; ?>: <?php echo ($PRODUCTS->pVisits>0 ? number_format($PRODUCTS->pVisits) : '0'); ?>, <span onclick="mc_enableDisableProduct('<?php echo $PRODUCTS->pid; ?>')" id="endis_<?php echo $PRODUCTS->pid; ?>" style="cursor:pointer" title="<?php echo mc_safeHTML($PRODUCTS->pEnable=='yes' ? $msg_productmanage39 : $msg_productmanage38); ?>"><?php echo ($PRODUCTS->pEnable=='yes' ? $msg_productmanage22 : $msg_productmanage23); ?></span><br>
    <?php echo $msg_productmanage67 . ': ' . $PRODUCTS->pid; ?><br><br>
    <a href="?p=product-pictures&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage14); ?>"><i class="fa fa-camera fa-fw"></i> <?php echo $msg_productmanage14; ?></a> (<?php echo mc_rowCount('pictures WHERE `product_id` = \''.$PRODUCTS->pid.'\''); ?>)<br>
    <a href="?p=product-related&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage17); ?>"><i class="fa fa-exchange fa-fw"></i> <?php echo $msg_productmanage17; ?></a> (<?php echo mc_rowCount('prod_relation WHERE `product` = \''.$PRODUCTS->pid.'\''); ?>)<br>
    <?php
    if (PRODUCT_MP3_PREVIEWS) {
    ?>
    <a href="?p=product-mp3&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage19); ?>"><i class="fa fa-music fa-fw"></i> <?php echo $msg_productmanage19; ?></a> (<?php echo mc_rowCount('mp3 WHERE `product_id` = \''.$PRODUCTS->pid.'\''); ?>)<br>
    <?php
    }
    if ($PRODUCTS->pDownload=='no') {
    ?>
    <a href="?p=product-personalisation&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage20); ?>"><i class="fa fa-quote-left fa-fw"></i> <?php echo $msg_productmanage20; ?></a> (<?php echo mc_rowCount('personalisation WHERE `productID` = \''.$PRODUCTS->pid.'\''); ?>)<br>
    <a href="?p=product-attributes&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage7); ?>"><i class="fa fa-pencil-square-o fa-fw"></i> <?php echo $msg_productmanage7; ?></a> (<?php echo mc_rowCount('attributes WHERE `productID` = \''.$PRODUCTS->pid.'\''); ?>)<br>
    <?php
    if ($atRs > 0) {
    ?>
    <a href="?p=copy-attributes&amp;product=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_prodattributes24); ?>" ><i class="fa fa-clone fa-fw"></i> <?php echo $msg_prodattributes24; ?></a><br>
    <?php
    }
    }
    if (BUY_NOW_CODE_OPTION) {
    ?>
    <a href="?p=manage-products&amp;buynow=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_admin_manage_product3_0[0]); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_BUYNOW_HEIGHT; ?>','<?php echo DIVWIN_BUYNOW_WIDTH; ?>',this.title);return false;"><i class="fa fa-shopping-basket fa-fw"></i> <?php echo $msg_admin_manage_product3_0[0]; ?></a><br>
    <?php
    }
    if ($uDel=='yes') {
    ?>
    <a href="?p=manage-products&amp;delete=<?php echo $PRODUCTS->pid; ?>" onclick="return mc_confirmMessage('<?php echo str_replace('{product}', mc_filterJS($PRODUCTS->pName), $msg_javascript85); ?>')" title="<?php echo mc_cleanDataEntVars($msg_script10); ?>"><i class="fa fa-times fa-fw mc-red"></i> <?php echo $msg_script10; ?></a>
    <?php
    }
    ?>
    </div>
  </div>
  <div class="panel-footer">
    <a href="../?pd=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage8); ?>" onclick="window.open(this);return false"><i class="fa fa-desktop fa-fw"></i></a>
    <a href="?p=add-product&amp;edit=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_script9); ?>"><i class="fa fa-pencil fa-fw"></i></a>
    <a href="?p=add-product&amp;copyp=<?php echo $PRODUCTS->pid; ?>" title="<?php echo mc_cleanDataEntVars($msg_productmanage6); ?>"><i class="fa fa-copy fa-fw"></i></a>
    <a href="?p=manage-products&amp;notes=<?php echo $PRODUCTS->pid; ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_NOTES_HEIGHT; ?>','<?php echo DIVWIN_NOTES_WIDTH; ?>',this.title);return false;" title="<?php echo mc_cleanDataEntVars($msg_productmanage62); ?>"><i class="fa fa-file-text-o fa-fw"></i></a>
    &nbsp;&nbsp;&nbsp;<i class="fa fa-long-arrow-down fa-fw" style="cursor:pointer" title="<?php echo mc_cleanDataEntVars($msg_admin3_0[10]); ?>" onclick="mc_toggleMoreOptions(this,'<?php echo $PRODUCTS->pid; ?>')"></i>
  </div>
</div>
<?php
}
?>
<p style="padding:10px 0 0 10px">
  <input type="checkbox" name="all" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'formField');mc_chkCnt('productIDs','counter','fmcnbutton');mc_chkCnt('productIDs','counter2','button2');">&nbsp;&nbsp;&nbsp;
  <button type="submit" id="fmcnbutton" disabled="disabled" class="btn btn-primary"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_productmanage52); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-pencil fa-fw"></i></span> (<span class="counter">0</span>)</button>
  <?php
  if ($uDel == 'yes') {
  ?>
  <button type="submit" onclick="return confirmMessage_Add('<?php echo mc_filterJS($msg_javascript45); ?>')" name="delproducts" disabled="disabled" class="btn btn-danger" id="button2"><span class="hidden-xs"><?php echo mc_cleanDataEntVars($msg_admin3_0[38]); ?></span><span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-times fa-fw"></i></span> (<span class="counter2">0</span>)</button>
  <?php
  }
  ?>
</p>
<?php
define('PER_PAGE',PRODUCTS_PER_PAGE);
if ($countedRows>0 && $countedRows>PER_PAGE) {
  $PGS = new pagination(array($countedRows, $msg_script77, $page),'?p=' . $_GET['p'] . '&amp;next=', 'yes');
  echo $PGS->display();
}
} else {
?>
<span class="noData"><?php echo $msg_productmanage15; ?></span>
<?php
}
?>
</form>
</div>

</div>
