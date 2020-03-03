<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'oExpiry');
include(PATH.'templates/js-loader/date-picker.php');
if (isset($_GET['fcat'])) {
  $fCats = (int) $_GET['fcat'];
}
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_productoffers8);
}
if (isset($OK2)) {
  echo mc_actionCompleted($msg_productoffers9);
}
?>

<form method="post" id="form" action="?p=special-offers<?php echo (isset($EDIT->id) ? '&amp;edit='.$EDIT->id : ''); ?>">
<div class="fieldHeadWrapper">
  <p><?php echo (isset($EDIT->id) ? $msg_productoffers14 : $msg_productoffers5); ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productoffers2; ?>: <?php echo mc_displayHelpTip($msg_javascript87,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="oRate" value="0.00" class="box">

    <label style="margin-top:10px"><?php echo $msg_productoffers4; ?>: <?php echo mc_displayHelpTip($msg_javascript66,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="oExpiry" value="" class="box" id="oExpiry">

    <label  style="margin-top:10px"><?php echo $msg_productoffers26; ?>: <?php echo mc_displayHelpTip($msg_javascript489,'LEFT'); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="multiBuy" value="0" class="box" id="multiBuy">
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_productoffers19; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
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
    <input tabindex="<?php echo (++$tabIndex); ?>" onclick="mc_loadProducts('cat-<?php echo $CATS->id; ?>','0','offers')" type="radio" name="pCat" value="cat-<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    &nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="mc_loadProducts('child-<?php echo $CHILDREN->id; ?>','0','offers')" type="radio" name="pCat" value="child-<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" onclick="mc_loadProducts('infant-<?php echo $INFANTS->id; ?>','0','offers')" type="radio" name="pCat" value="child-<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    }
    }
    ?>
    </div>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <label><?php echo $msg_productoffers7; ?>: <?php echo mc_displayHelpTip($msg_javascript65); ?></label>
  <div class="categoryBoxes" id="products" style="margin-top:10px">
  <?php
  $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                FROM `" . DB_PREFIX . "products`
                LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                WHERE `category`  = '".(isset($cats[0]) ? $cats[0] : '0')."'
                AND `pEnable`     = 'yes'
                AND (`pOffer`     = '' OR `pOffer` <= 0)
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_products)>0) {
  ?>
  <input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_productoffers20; ?></b><br>
  <?php
  while ($PR = mysqli_fetch_object($q_products)) {
  ?>
  <input type="hidden" name="products[]" value="<?php echo $PR->pid; ?>">
  <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="product[]" value="<?php echo $PR->pid; ?>"> <?php echo mc_safeHTML($PR->pName).' - '.mc_currencyFormat(mc_formatPrice($PR->pPrice)); ?><br>
  <?php
  }
  } else {
  echo $msg_productoffers14;
  }
  ?>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productoffers5); ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers5); ?>">
</p>
</form><br>
<?php
$rIS = mc_rowCount('products WHERE `pEnable` = \'yes\' AND `pOffer` > 0');
?>
<div class="fieldHeadWrapper" style="margin-top:20px">
  <p><span style="float:right"><a href="#" onclick="jQuery('#filtercats').slideToggle();return false"><i class="fa fa-filter fa-fw"></i></a><?php echo ($uDel=='yes' && $rIS ? ' <a href="?p=special-offers&amp;clearall=yes" title="'.mc_cleanDataEntVars($msg_productoffers17).'" onclick="return mc_confirmMessage(\''.mc_filterJS($msg_javascript45).'\')"><i class="fa fa-times-circle fa-fw mc-red"></i></a>' : ''); ?></span><?php echo $msg_productoffers10; ?>:</p>
</div>

<div class="formFieldWrapper" id="filtercats" style="display:none">
    <select onchange="if(this.value!=0){location=this.options[this.selectedIndex].value}">
    <option value="0">- - - - - -</option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="?p=special-offers&amp;fcat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['fcat']) && $_GET['fcat'] == $CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `childOf`    = '{$CATS->id}'
                  AND `enCat`      = 'yes'
                  ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="?p=special-offers&amp;fcat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['fcat']) && $_GET['fcat'] == $CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ".(SHOW_DISABLED_CATS_ADD_PRODUCT ? 'AND `enCat` = \'yes\'' : '')."
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="?p=special-offers&amp;fcat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['fcat']) && $_GET['fcat'] == $INFANTS->id ? ' selected="selected"' : ''); ?>>- - <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </select>
</div>

<?php
$some = 0;
if (isset($fCats)) {
$q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
          WHERE `id` = '{$fCats}'
          ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
while ($CATS = mysqli_fetch_object($q_cats)) {
  $oc = mc_getCatOfferCount($CATS->id);
  if ($oc>0) {
  ++$some;
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <?php echo str_replace(array('{category}','{count}'),array(mc_safeHTML($CATS->catname),$oc),$msg_productoffers11); ?>
    </div>
    <?php
    if ($oc>0) {
    ?>
    <div class="panel-footer">
      <a href="?p=special-offers&amp;view=<?php echo $CATS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers12); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_HEIGHT; ?>','<?php echo DIVWIN_WIDTH; ?>',this.title);return false;"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=special-offers&amp;clear=<?php echo $CATS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers13); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
    <?php
    }
    ?>
  </div>
  <?php
  }
}
if ($some==0) {
?>
<span class="noData"><?php echo $msg_productoffers15; ?></span>
<?php
}
?>
<p><a href="?p=special-offers"><i class="fa fa-refresh fa-fw"></i></a></p>
<?php
} else {
if ($rIS>0) {
$q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
          WHERE `catLevel` = '1'
          AND `childOf`    = '0'
          AND `enCat`      = 'yes'
          " . (!empty($filterOfferCats) ? 'AND `id` IN(' . implode(',',$filterOfferCats) . ')' : '') . "
          ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
while ($CATS = mysqli_fetch_object($q_cats)) {
  $oc = mc_getCatOfferCount($CATS->id);
  if ($oc>0) {
  ++$some;
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      <?php echo str_replace(array('{category}','{count}'),array(mc_safeHTML($CATS->catname),$oc),$msg_productoffers11); ?>
    </div>
    <?php
    if ($oc>0) {
    ?>
    <div class="panel-footer">
      <a href="?p=special-offers&amp;view=<?php echo $CATS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers12); ?>"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=special-offers&amp;clear=<?php echo $CATS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers13); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
    <?php
    }
    ?>
  </div>
  <?php
  }
  $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                WHERE `catLevel` = '2'
                AND `enCat`      = 'yes'
                AND `childOf`    = '{$CATS->id}'
                " . (!empty($filterOfferCats) ? 'AND `id` IN(' . implode(',',$filterOfferCats) . ')' : '') . "
                ORDER BY `catname`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($CHILDREN = mysqli_fetch_object($q_children)) {
  $oc = mc_getCatOfferCount($CHILDREN->id);
  if ($oc>0) {
  ++$some;
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      &nbsp;&nbsp;&quot;<?php echo mc_safeHTML($CATS->catname) . '&quot; <i class="fa fa-angle-right fa-fw"></i> ' . str_replace(array('{category}','{count}'),array(mc_safeHTML($CHILDREN->catname),$oc),$msg_productoffers11); ?>
    </div>
    <?php
    if ($oc>0) {
    ?>
    <div class="panel-footer">
      <a href="?p=special-offers&amp;view=<?php echo $CHILDREN->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers12); ?>"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=special-offers&amp;clear=<?php echo $CHILDREN->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers13); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
    <?php
    }
    ?>
  </div>
  <?php
  }
  $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
               WHERE `catLevel` = '3'
               AND `enCat`      = 'yes'
               AND `childOf`    = '{$CHILDREN->id}'
               " . (!empty($filterOfferCats) ? 'AND `id` IN(' . implode(',',$filterOfferCats) . ')' : '') . "
               ORDER BY `catname`
               ") or die(mc_MySQLError(__LINE__,__FILE__));
  while ($INFANTS = mysqli_fetch_object($q_infants)) {
  $oc = mc_getCatOfferCount($INFANTS->id);
  if ($oc>0) {
  ++$some;
  ?>
  <div class="panel panel-default">
    <div class="panel-body">
      &nbsp;&nbsp;&quot;<?php echo mc_safeHTML($CHILDREN->catname) . '&quot; <i class="fa fa-angle-right fa-fw"></i> ' . mc_safeHTML($CATS->catname) . '&quot; <i class="fa fa-angle-right fa-fw"></i> ' . str_replace(array('{category}','{count}'),array(mc_safeHTML($INFANTS->catname),$oc),$msg_productoffers11); ?>
    </div>
    <?php
    if ($oc>0) {
    ?>
    <div class="panel-footer">
      <a href="?p=special-offers&amp;view=<?php echo $INFANTS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers12); ?>" onclick="mc_Window(this.href,'<?php echo DIVWIN_HEIGHT; ?>','<?php echo DIVWIN_WIDTH; ?>',this.title);return false;"><i class="fa fa-pencil fa-fw"></i></a>
      <?php
      if ($uDel=='yes') {
      ?>
      <a href="?p=special-offers&amp;clear=<?php echo $INFANTS->id; ?>" title="<?php echo mc_cleanDataEntVars($msg_productoffers13); ?>" onclick="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')"><i class="fa fa-times fa-fw mc-red"></i></a>
      <?php
      }
      ?>
    </div>
    <?php
    }
    ?>
  </div>
  <?php
  }
  }

  }
}
if ($some==0) {
?>
<span class="noData"><?php echo $msg_productoffers15; ?></span>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_productoffers15; ?></span>
<?php
}
}
?>


</div>
