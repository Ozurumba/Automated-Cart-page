<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_productprices12);
}

?>

<form method="post" action="?p=update-prices" id="form" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript60; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productprices3; ?>: <?php echo mc_displayHelpTip($msg_javascript87,'RIGHT'); ?></label>
    <input type="text" name="price" value="" class="box" tabindex="<?php echo ++$tabIndex; ?>">

    <label style="margin-top:10px"><?php echo $msg_productprices4; ?>: <?php echo mc_displayHelpTip($msg_javascript88,'RIGHT'); ?></label>
    <?php echo $msg_productprices6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="type" value="incr" checked="checked"> <?php echo $msg_productprices7; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="type" value="decr"> <?php echo $msg_productprices19; ?> <input type="radio" name="type" value="fixed">

    <label style="margin-top:10px"><?php echo $msg_productstock17; ?>: <?php echo mc_displayHelpTip($msg_javascript414,'RIGHT'); ?></label>
    <?php echo $msg_productstock15; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="table[]" value="products" checked="checked"> <?php echo $msg_productstock16; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="table[]" value="attr">

    <label style="margin-top:10px"><?php echo $msg_productprices11; ?>: <?php echo mc_displayHelpTip($msg_javascript91,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input type="radio" tabindex="<?php echo ++$tabIndex; ?>" name="clear" value="yes"> <?php echo $msg_script6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="clear" value="no" checked="checked">
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productprices13; ?> (<?php echo $msg_productprices14; ?> / <?php echo $msg_productprices15; ?>): <?php echo mc_displayHelpTip($msg_javascript92,'LEFT'); ?></label>
    <input type="text" tabindex="<?php echo ++$tabIndex; ?>" name="min" class="box" value="0.00">
    <input style="margin-top:5px" type="text" tabindex="<?php echo ++$tabIndex; ?>" name="max" class="box" value="0.00">

    <label style="margin-top:10px"><?php echo $msg_productprices9; ?>: <?php echo mc_displayHelpTip($msg_javascript90,'LEFT'); ?></label>
    <input type="text" name="from" class="box" tabindex="<?php echo ++$tabIndex; ?>" id="from">
    <input style="margin-top:5px" type="text" tabindex="<?php echo ++$tabIndex; ?>" name="to" class="box" id="to">

  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productprices8; ?>: <?php echo mc_displayHelpTip($msg_javascript89,'RIGHT'); ?></label>
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
    <input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('cat-<?php echo $CATS->id; ?>','0','prices')" type="radio" name="pCat" value="<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    &nbsp;&nbsp;<input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('child-<?php echo $CHILDREN->id; ?>','0','prices')" type="radio" name="pCat" value="<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('infant-<?php echo $INFANTS->id; ?>','0','prices')" type="radio" name="pCat" value="<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    }
    }
    ?>
    </div>
  <label style="margin-top:10px"><?php echo $msg_productprices16; ?>: <?php echo mc_displayHelpTip($msg_javascript218,'LEFT'); ?></label>
  <div class="categoryBoxes" id="products">
  <?php
  $q_products = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid`
                FROM `" . DB_PREFIX . "products`
                LEFT JOIN `" . DB_PREFIX . "prod_category`
                ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
                WHERE `category`                = '".(isset($cats[0]) ? (int) $cats[0] : '0')."'
                AND `pEnable`                   = 'yes'
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_products)>0) {
  ?>
  <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_prodrelated7; ?></b><br>
  <?php
  while ($PR = mysqli_fetch_object($q_products)) {
  ?>
  <input type="hidden" name="products[]" value="<?php echo $PR->pid; ?>">
  <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="product[]" value="<?php echo $PR->pid; ?>"> <?php echo mc_safeHTML($PR->pName).' - '.mc_currencyFormat(mc_formatPrice($PR->pPrice)); ?><br>
  <?php
  }
  } else {
  echo $msg_productprices17;
  }
  ?>
  </div>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productprices5); ?>" title="<?php echo mc_cleanDataEntVars($msg_productprices5); ?>">
</p>
</form>


</div>
