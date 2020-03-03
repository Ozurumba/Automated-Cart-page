<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">
<script>
//<![CDATA[
function changeButtonCount(form,type) {
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
    jQuery('#button').prop('disabled', false);
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanData($msg_productmove7)); ?> ('+count+')');
  } else {
    jQuery('#button').prop('disabled', true);
    jQuery('#button').val('<?php echo str_replace(array("'","&#039;"),array("\'","\'"),mc_cleanData($msg_productmove7)); ?> (0)');
  }
}
//]]>
</script>
<?php
if (isset($OK)) {
  echo mc_actionCompleted(str_replace('{count}',count($_POST['products']),$msg_productmove10));
}
?>

<form method="post" id="form" action="?p=batch-move<?php echo (isset($_GET['cat']) ? '&amp;cat='.mc_digitSan($_GET['cat']) : ''); ?>" onsubmit="return mc_confirmMessage('<?php echo mc_filterJS($msg_javascript45); ?>')">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript197; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">
    <label><?php echo $msg_productmove4; ?>: <?php echo mc_displayHelpTip($msg_javascript205,'RIGHT'); ?></label>
    <select name="source" onchange="if(this.value!= 0){location=this.options[this.selectedIndex].value}" tabindex="<?php echo (++$tabIndex); ?>">
    <option value="0">- - - - - -</option>
    <?php
    $getFirstCat     = 0;
    $getFirstCatMan  = '';
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="?p=batch-move&amp;cat=<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' selected="selected"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="?p=batch-move&amp;cat=<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' selected="selected"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="?p=batch-move&amp;cat=<?php echo $INFANTS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' selected="selected"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    </select>

    <label style="margin-top:10px"><?php echo $msg_productmove5; ?>: <?php echo mc_displayHelpTip($msg_javascript206); ?></label>
    <select name="destination" tabindex="<?php echo (++$tabIndex); ?>">
    <?php
    if (isset($_GET['cat'])) {
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="<?php echo $CATS->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CATS->id ? ' disabled="disabled"' : ''); ?>><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$CHILDREN->id ? ' disabled="disabled"' : ''); ?>>- <?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_GET['cat']) && mc_digitSan($_GET['cat'])==$INFANTS->id ? ' disabled="disabled"' : ''); ?>>&nbsp;&nbsp;- <?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    } else {
    ?>
    <option value="0"><?php echo mc_cleanDataEntVars($msg_productmove6); ?></option>
    <?php
    }
    ?>
  </select>
  </div>
  <br class="clear">
</div>

<?php
if (isset($_GET['cat'])) {
$q_prod = mysqli_query($GLOBALS["___msw_sqli"], "SELECT *,`" . DB_PREFIX . "products`.`id` AS `pid` FROM `" . DB_PREFIX . "products`
          LEFT JOIN `" . DB_PREFIX . "prod_category`
          ON `" . DB_PREFIX . "products`.`id` = `" . DB_PREFIX . "prod_category`.`product`
          WHERE `category` = '".mc_digitSan($_GET['cat'])."'
          ORDER BY `pName`
          ") or die(mc_MySQLError(__LINE__,__FILE__));
?>
<div class="fieldHeadWrapper">
  <p><input type="checkbox" id="log" name="log" onclick="changeButtonCount(this.form,'all')"> <?php echo $msg_productmove3; ?>:</p>
</div>
<?php
if (mysqli_num_rows($q_prod)>0) {
while ($PRODUCTS = mysqli_fetch_object($q_prod)) {
?>
<div class="panel panel-default">
  <div class="panel-body">
    <input type="checkbox" name="products[]" value="<?php echo $PRODUCTS->pid; ?>" onclick="changeButtonCount(this.form,'single')"> <?php echo mc_safeHTML($PRODUCTS->pName); ?>
  </div>
</div>
<?php
}
} else {
?>
<span class="noData"><?php echo $msg_productmove8; ?></span>
<?php
}

if (mysqli_num_rows($q_prod)>0) {
?>
<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="1">
 <input id="button" class="btn btn-primary" type="submit" disabled="disabled" value="<?php echo mc_cleanDataEntVars($msg_productmove7); ?> (0)" title="<?php echo mc_cleanDataEntVars($msg_productmove7); ?>">
</p>
<?php
}
}
?>
</form>


</div>
