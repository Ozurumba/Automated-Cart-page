<?php if (!defined('PARENT')) { die('Permission Denied'); } ?>
<div id="content">
<script>
//<![CDATA[
function mc_catSlctr(valu) {
  if (valu != '0') {
    switch(valu) {
      case 'close':
        jQuery('#sel1').show();
        jQuery('#sel2').hide();
        jQuery('#prodList').hide();
        break;
      case 'box':
        jQuery('#sel1').show();
        jQuery('#sel2').hide();
        break;
      case 'list':
        jQuery('#prodList').hide();
        break;
      default:
        mc_loadHomeProducts(valu);
        break;
    }
  }
}
//]]>
</script>

<?php
$tabIndex = 0;
if (isset($OK)) {
  echo mc_actionCompleted($msg_settings31);
  //Reload..
  $SETTINGS = mysqli_fetch_object(mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "settings`"))
              or die(mc_MySQLError(__LINE__,__FILE__));
}
$pIDs   = explode(',',$SETTINGS->homeProdCats);
?>

<form method="post" id="form" action="?p=settings&amp;s=4">
<div class="fieldHeadWrapper">
  <p><?php echo $msg_settings54; ?></p>
</div>

<div class="formFieldWrapper" id="topData">
  <div class="formLeft">
    <label><?php echo $msg_settings55; ?>: <?php echo mc_displayHelpTip($msg_javascript189,'RIGHT'); ?></label>
    <input type="text" name="homeProdValue" value="<?php echo $SETTINGS->homeProdValue; ?>" maxlength="3" class="box">
    <select name="homeProdType" tabindex="<?php echo ++$tabIndex; ?>" style="margin-top:5px">
    <option value="latest"<?php echo ($SETTINGS->homeProdType=='latest' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings56; ?></option>
    <option value="random"<?php echo ($SETTINGS->homeProdType=='random' ? ' selected="selected"' : ''); ?>><?php echo $msg_settings57; ?></option>
    </select>

    <label style="margin-top:10px"><?php echo $msg_settings58; ?>: <?php echo mc_displayHelpTip($msg_javascript190,'LEFT'); ?></label>
    <div class="categoryBoxes" id="homeProdCatsArea">
    <input type="checkbox" name="homeProdCats[]" value="all" onclick="mc_toggleCheckBoxesID(this.checked,'homeProdCatsArea')"<?php echo (in_array('all',$pIDs) ? ' checked="checked"' : ''); ?>> <b><?php echo $msg_productadd35; ?></b><br>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <p id="cat_<?php echo $CATS->id; ?>"><input onclick="if(this.checked){mc_selectChildren('cat_<?php echo $CATS->id; ?>','on')}else{mc_selectChildren('cat_<?php echo $CATS->id; ?>','off')}" tabindex="<?php echo ++$tabIndex; ?>" type="checkbox" name="homeProdCats[]" value="<?php echo $CATS->id; ?>"<?php echo (in_array($CATS->id,$pIDs) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CATS->catname); ?><br>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <span id="child_<?php echo $CHILDREN->id; ?>">
    &nbsp;&nbsp;<input onclick="if(this.checked){mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','on')}else{mc_selectChildren('child_<?php echo $CHILDREN->id; ?>','off')}" type="checkbox" tabindex="<?php echo ++$tabIndex; ?>" name="homeProdCats[]" value="<?php echo $CHILDREN->id; ?>"<?php echo (in_array($CHILDREN->id,$pIDs) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($CHILDREN->catname); ?><br>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT * FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" tabindex="<?php echo ++$tabIndex; ?>" name="homeProdCats[]" value="<?php echo $INFANTS->id; ?>"<?php echo (in_array($INFANTS->id,$pIDs) ? ' checked="checked"' : ''); ?>> <?php echo mc_safeHTML($INFANTS->catname); ?><br>
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
  <br class="clear">
</div>

<div class="formFieldWrapper">
  <div class="form-group" id="sel1">
    <label><?php echo $msg_settings59; ?>: <?php echo mc_displayHelpTip($msg_javascript191,'RIGHT'); ?></label>
    <div class="form-group input-group">
     <span class="input-group-addon"><a href="#"<?php echo (mc_rowCount('products WHERE `pEnable` = \'yes\'') == 0 ? ' onclick="mc_alertBox(\''.mc_filterJS($msg_javascript286).'\');return false" ' : ' onclick="jQuery(\'#sel2\').show();jQuery(\'#sel1\').hide();return false" '); ?>title="<?php echo mc_cleanDataEntVars($msg_settings147); ?>"><i class="fa fa-search fa-fw"></i></a></span>
     <input type="text" name="homeProdIDs" value="<?php echo mc_cleanData($SETTINGS->homeProdIDs); ?>" class="box addon-no-radius">
    </div>
  </div>
  <div class="form-group" id="sel2" style="display:none">
  <label><?php echo $msg_admin_settings3_0[40]; ?>: <?php echo mc_displayHelpTip($msg_javascript191,'RIGHT'); ?></label>
  <select name="pids" onchange="mc_catSlctr(this.value)" class="form-control">
    <option value="0">- - - - - - - - -</option>
    <?php
    $q_cats = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
              WHERE `catLevel` = '1'
              AND `childOf`    = '0'
              AND `enCat`      = 'yes'
              ORDER BY `catname`
              ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CATS = mysqli_fetch_object($q_cats)) {
    ?>
    <option value="cat-<?php echo $CATS->id; ?>"><?php echo mc_safeHTML($CATS->catname); ?></option>
    <?php
    $q_children = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '2'
                  AND `enCat`      = 'yes'
                  AND `childOf`    = '{$CATS->id}'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($CHILDREN = mysqli_fetch_object($q_children)) {
    ?>
    <option value="child-<?php echo $CHILDREN->id; ?>">&nbsp;&nbsp;<?php echo mc_safeHTML($CHILDREN->catname); ?></option>
    <?php
    $q_infants = mysqli_query($GLOBALS["___msw_sqli"], "SELECT `id`,`catname` FROM `" . DB_PREFIX . "categories`
                  WHERE `catLevel` = '3'
                  AND `childOf`    = '{$CHILDREN->id}'
                  AND `enCat`      = 'yes'
                  ORDER BY `catname`
                  ") or die(mc_MySQLError(__LINE__,__FILE__));
    while ($INFANTS = mysqli_fetch_object($q_infants)) {
    ?>
    <option value="infant-<?php echo $INFANTS->id; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo mc_safeHTML($INFANTS->catname); ?></option>
    <?php
    }
    }
    }
    ?>
    <option value="0">- - - - - - - - - -</option>
    <option value="close"><?php echo $msg_admin_settings3_0[39]; ?></option>
  </select>
  </div>
  <br class="clear">
</div>

<div class="formFieldWrapper" id="prodList" style="display:none;line-height:25px">
</div>

<p style="text-align:center;padding-top:20px">
 <input type="hidden" name="process" value="yes">
 <input class="btn btn-primary" type="submit" value="<?php echo mc_cleanDataEntVars($msg_settings8); ?>" title="<?php echo mc_cleanDataEntVars($msg_settings8); ?>">
</p>
</form>


</div>
