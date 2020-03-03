<?php if (!defined('PARENT')) { die('You do not have permission to view this file!!'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',count($_POST['product']),$msg_prodattributes27));
}

$P = mc_getTableData('products','id',mc_digitSan($_GET['product']));
$thisProductID = mc_digitSan($_GET['product']);
?>

<div class="alert alert-info">
  <?php
  $qLinksArr  = array('copy-attributes');
  $qLinksIcon = 'pencil-square-o';
  include(PATH . 'templates/catalogue/product-quick-links.php');
  ?>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_prodattributes25; ?>:</p>
</div>

<div id="formField">
<form method="post" id="form" action="?p=copy-attributes&amp;product=<?php echo mc_digitSan($_GET['product']); ?>" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">

<div class="formFieldWrapper">
  <div class="table-responsive attributearea">
    <table class="table table-striped table-hover" style="padding-bottom:0;margin-bottom:0">
    <thead>
      <tr>
        <th><input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'attributearea')"checked="checked" ></th>
        <th><?php echo $msg_prodattributes9; ?></th>
        <th><?php echo $msg_prodattributes10; ?></th>
        <th><?php echo $msg_prodattributes15; ?></th>
        <th><?php echo $msg_prodattributes5; ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $qG = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attr_groups`
             WHERE `productID` = '" . mc_digitSan($_GET['product']) . "'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__, __FILE__));
      while ($ATTG = mysqli_fetch_object($qG)) {
        $q = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "attributes`
             WHERE `productID` = '".mc_digitSan($_GET['product'])."'
             AND `attrGroup` = '{$ATTG->id}'
             ORDER BY `orderBy`
             ") or die(mc_MySQLError(__LINE__,__FILE__));
        while ($ATT = mysqli_fetch_object($q)) {
        ?>
        <tr>
          <td><input type="checkbox" name="attr[]" value="<?php echo $ATT->id; ?>" checked="checked"></td>
          <td><?php echo mc_safeHTML($ATT->attrName); ?><span class="copyAttrGrp"><i class="fa fa-angle-right"></i> <?php echo mc_safeHTML($ATTG->groupName); ?></span></td>
          <td><?php echo mc_currencyFormat(mc_formatPrice($ATT->attrCost)); ?></td>
          <td><?php echo $ATT->attrWeight; ?></td>
          <td><?php echo $ATT->attrStock; ?></td>
          </td>
        </tr>
        <?php
        }
      }
      ?>
    </tbody>
    </table>
  </div>
</div>

<?php
if (mysqli_num_rows($qG) > 0) {
?>
<div class="fieldHeadWrapper">
  <p><?php echo $msg_product_attributes[1]; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <select onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}">
    <option value="0"><?php echo $msg_viewsale66; ?></option>
    <option value="0" disabled="disabled">- - - - - - - - -</option>
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
    <option value="?p=copy-attributes&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;catid=<?php echo $CATS->id; ?>"<?php echo (count($cats)==1 || isset($_GET['catid']) && $_GET['catid']==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '".$CATS->id."'
                  ORDER BY `catname`
				  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="?p=copy-attributes&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;&amp;catid=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['catid']) && $_GET['catid']==$CHILDREN->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;<?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="?p=copy-attributes&amp;product=<?php echo mc_digitSan($_GET['product']); ?>&amp;&amp;catid=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['catid']) && $_GET['catid']==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </select>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper prodcheck">
  <?php
  $q_p = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `pName`,`" . DB_PREFIX . "products`.`id` AS `pid`
         FROM `" . DB_PREFIX . "products`
         LEFT JOIN `" . DB_PREFIX . "prod_category`
         ON `" . DB_PREFIX . "products`.`id`   = `" . DB_PREFIX . "prod_category`.`product`
         WHERE `category`                  = '".(isset($_GET['catid']) ? mc_digitSan($_GET['catid']) : (isset($cats[0]) ? $cats[0] : '0'))."'
         AND `" . DB_PREFIX . "products`.`id` != '".mc_digitSan($_GET['product'])."'
         AND `pDownload`                   = 'no'
         GROUP BY `" . DB_PREFIX . "products`.`id`
         ORDER BY `pName`
         ") or die(mc_MySQLError(__LINE__,__FILE__));
  if (mysqli_num_rows($q_p)>0) {
    ?>
    <div class="checkbox">
      <label><input type="checkbox" name="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'prodcheck');mc_chkCnt('product','butcnt','fmcnbutton')"> <b><?php echo $msg_prodrelated7; ?></b></label>
    </div>
    <?php
    while ($PR = mysqli_fetch_object($q_p)) {
    ?>
    <div class="checkbox">
      <label><input type="checkbox" name="product[]" value="<?php echo $PR->pid; ?>" onclick="mc_singleCheckBox(this.checked,'formField');mc_chkCnt('product','butcnt','fmcnbutton')"> <?php echo mc_safeHTML($PR->pName); ?></label>
    </div>
    <?php
    }
  } else {
    echo $msg_prodattributes26;
  }
  ?>
</div>
<?php
if (mysqli_num_rows($q_p)>0) {
?>
<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <button class="btn btn-primary" type="submit" disabled="disabled" id="fmcnbutton" title="<?php echo mc_safeHTML($msg_prodattributes25); ?>"><?php echo $msg_prodattributes24; ?> (<span class="butcnt">0</span>)</button>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="button" onclick="window.location='?p=manage-products'" value="<?php echo mc_cleanDataEntVars($msg_script11); ?>" title="<?php echo mc_cleanDataEntVars($msg_script11); ?>">
</p>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_prodattributes12; ?></span>
<?php
}
?>
</form>
</div>

</div>