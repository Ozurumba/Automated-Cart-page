<?php if (!defined('PARENT')) { die('Permission Denied'); }
define('CALBOX', 'from|to');
include(PATH.'templates/js-loader/date-picker.php');
?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msg_productstock12);
}

?>

<form method="post" id="form" action="?p=update-stock" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript61; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productstock3; ?>: <?php echo mc_displayHelpTip($msg_javascript93,'RIGHT'); ?></label>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="text" name="stock" value="" class="box">

    <label style="margin-top:10px"><?php echo $msg_productstock4; ?>: <?php echo mc_displayHelpTip($msg_javascript94,'RIGHT'); ?></label>
    <?php echo $msg_productstock6; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="type" value="incr" checked="checked"> <?php echo $msg_productstock7; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="radio" name="type" value="decr"> <?php echo $msg_productprices19; ?> <input type="radio" name="type" value="fixed">

    <label style="margin-top:10px"><?php echo $msg_productstock17; ?>: <?php echo mc_displayHelpTip($msg_javascript414,'RIGHT'); ?></label>
    <?php echo $msg_productstock15; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="table[]" value="products" checked="checked"> <?php echo $msg_productstock16; ?> <input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="table[]" value="attr">
  </div>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productstock13; ?> (<?php echo $msg_productstock14; ?>/<?php echo $msg_productstock11; ?>): <?php echo mc_displayHelpTip($msg_javascript96,'LEFT'); ?></label>
    <input tabindex="<?php echo ++$tabIndex; ?>" type="text" name="min" class="box" value="0">
    <input style="margin-top:5px" tabindex="<?php echo ++$tabIndex; ?>" type="text" value="0" name="max" class="box">

    <label style="margin-top:10px"><?php echo $msg_productstock9; ?>: <?php echo mc_displayHelpTip($msg_javascript90,'LEFT'); ?></label>
    <input type="text" name="from" tabindex="<?php echo ++$tabIndex; ?>" class="box" id="from">
    <input style="margin-top:5px" tabindex="<?php echo ++$tabIndex; ?>" type="text" name="to" class="box" id="to">
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
    <input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('cat-<?php echo $CATS->id; ?>','0','stock')" type="radio" name="pCat" value="<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    &nbsp;&nbsp;<input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('child-<?php echo $CHILDREN->id; ?>','0','stock')" type="radio" name="pCat" value="<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo ++$tabIndex; ?>" onclick="mc_loadProducts('infant-<?php echo $INFANTS->id; ?>','0','stock')" type="radio" name="pCat" value="<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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
                WHERE `category`                = '".(isset($cats[0]) ? $cats[0] : '0')."'
                AND `pEnable`                   = 'yes'
                GROUP BY `" . DB_PREFIX . "products`.`id`
                ORDER BY `pName`
                ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_products)>0) {
  ?>
  <input type="checkbox" tabindex="<?php echo ++$tabIndex; ?>" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_prodrelated7; ?></b><br>
  <?php
  while ($PR = mysqli_fetch_object($q_products)) {
  ?>
  <input type="hidden" name="products[]" value="<?php echo $PR->pid; ?>">
  <input type="checkbox" tabindex="<?php echo ++$tabIndex; ?>" name="product[]" value="<?php echo $PR->pid; ?>"> <?php echo mc_safeHTML($PR->pName).' - ('.$PR->pStock; ?>)<br>
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
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_productstock5); ?>" title="<?php echo mc_cleanDataEntVars($msg_productstock5); ?>">
</p>
</form>


</div>
