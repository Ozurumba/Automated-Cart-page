<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">

<?php
if (isset($OK)) {
  echo mc_actionCompleted($msp_productstatus11);
}

if (isset($return) && $return=='none') {
?>
<div class="alert alert-warning alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <i class="fa fa-warning fa-fw"></i> <?php echo $msp_productstatus3; ?>
</div>
<?php
}
?>

<div class="fieldHeadWrapper">
  <p><?php echo $msg_javascript338; ?>:</p>
</div>

<form method="post" id="form" action="?p=product-status">
<div class="formFieldWrapper">
  <div class="formLeft">
    <div class="categoryBoxes">
    <input type="checkbox" tabindex="<?php echo ++$tabIndex; ?>" name="log" value="all" onclick="mc_toggleCheckBoxes(this.checked,'categoryBoxes')"> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="range[]" value="<?php echo $CATS->id; ?>"> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="range[]" value="<?php echo $CHILDREN->id; ?>"> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="range[]" value="<?php echo $INFANTS->id; ?>"> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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
  </div>
</div>

<div class="fieldHeadWrapper">
  <p><?php echo $msp_productstatus5; ?>:</p>
</div>

<div class="formFieldWrapper">
  <div class="formLeft">

    <select name="action_1">
      <option value="enable" selected="selected"><?php echo $msp_productstatus3; ?></option>
      <option value="disable"><?php echo $msp_productstatus6; ?></option>
    </select>

    <select name="action_3" style="margin-top:10px">
      <option value="enable" selected="selected"><?php echo $msp_productstatus12; ?></option>
      <option value="disable"><?php echo $msp_productstatus13; ?></option>
    </select>

    <select name="action_4" style="margin-top:10px">
      <option value="enable" selected="selected"><?php echo $msp_productstatus16; ?></option>
      <option value="disable"><?php echo $msp_productstatus17; ?></option>
    </select>

    <select name="action_5" style="margin-top:10px">
      <option value="enable" selected="selected"><?php echo $msp_productstatus9; ?></option>
      <option value="disable"><?php echo $msp_productstatus10; ?></option>
    </select>

    <select name="action_6" style="margin-top:10px">
      <option value="disable" selected="selected"><?php echo $msp_productstatus18; ?></option>
      <option value="enable"><?php echo $msp_productstatus19; ?></option>
    </select>

    <select name="action_7" style="margin-top:10px">
      <option value="enable" selected="selected"><?php echo $msp_productstatus14; ?></option>
      <option value="disable"><?php echo $msp_productstatus15; ?></option>
    </select>

  </div>
  <br class="clear">
</div>

<p style="text-align:center;padding:20px 0 20px 0">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_safeHTML($msp_productstatus8); ?>" title="<?php echo mc_safeHTML($msp_productstatus8); ?>">
</p>
</form>

</div>
