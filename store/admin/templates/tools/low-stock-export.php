<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($return) && $return=='none') {
?>
<div class="alert alert-warning alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-warning fa-fw"></i> <?php echo $msg_stockexport3; ?>
</div>
<?php
}
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript319; ?>:</p>
</div>

<form method="post" id="form" action="?p=low-stock-export">
<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
    <input type="checkbox" tabindex="<?php echo (++$tabIndex); ?>" name="log" value="all" onclick="mc_selectAll()"> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="range[]" value="<?php echo $CATS->id; ?>"<?php echo (isset($_POST['range']) && in_array($CATS->id,$_POST['range']) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="range[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (isset($_POST['range']) && in_array($CHILDREN->id,$_POST['range']) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo (++$tabIndex); ?>" type="checkbox" name="range[]" value="<?php echo $INFANTS->id; ?>"<?php echo (isset($_POST['range']) && in_array($INFANTS->id,$_POST['range']) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
    <?php
    }
    ?>
    </span>
    <?php
    }
    ?>
    </p>
    <?php
    }
    ?>
    </div>

    <label style="margin-top:10px"><?php echo $msg_stockexport5; ?>: <?php echo mc_displayHelpTip($msg_javascript321); ?></label>
    <input tabindex="<?php echo (++$tabIndex); ?>" type="text" name="from" value="<?php echo (isset($_POST['from']) ? mc_safeHTML($_POST['from']) : '0'); ?>" class="box">
    <input style="margin-top:5px" tabindex="<?php echo (++$tabIndex); ?>" type="text" name="to" value="<?php echo (isset($_POST['to']) ? mc_safeHTML($_POST['to']) : $SETTINGS->searchLowStockLimit); ?>" class="box">

    <label style="margin-top:10px"><?php echo $msg_stockexport7; ?>: <?php echo mc_displayHelpTip($msg_javascript322,'LEFT'); ?></label>
    <?php echo $msg_script5; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="disabled" value="yes"<?php echo (isset($_POST['disabled']) && $_POST['disabled']=='yes' ? ' checked="checked"' : (!isset($_POST['disabled']) ? ' checked="checked"' : '')); ?>> <?php echo $msg_script6; ?> <input tabindex="<?php echo (++$tabIndex); ?>" type="radio" name="disabled" value="no"<?php echo (isset($_POST['disabled']) && $_POST['disabled']=='no' ? ' checked="checked"' : ''); ?>>
  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_stockexport8); ?>" title="<?php echo mc_cleanDataEntVars($msg_stockexport8); ?>">
</p>
</form>

</div>
